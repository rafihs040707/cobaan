<?php
ob_start();

$allowed_roles = ["admin", "lo"];

require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\ErrorCorrectionLevel;

ini_set('display_errors', 1);
error_reporting(E_ALL);

$id = $_GET['id'] ?? null;
$mode = $_GET['mode'] ?? 'generate';
$isPreview = ($mode === 'preview');

if (!$id) {
    die("ID tidak ditemukan");
}

function randomLetters($length, $chars)
{
    $result = '';
    $max = strlen($chars) - 1;
    $bytes = random_bytes($length);

    for ($i = 0; $i < $length; $i++) {
        $result .= $chars[ord($bytes[$i]) % ($max + 1)];
    }
    return $result;
}

function formatPeriode($awal, $akhir)
{
    if (date('F Y', $awal) == date('F Y', $akhir)) {
        return date('F d', $awal) . " - " . date('d, Y', $akhir);
    }
    return date('F d', $awal) . " - " . date('F d, Y', $akhir);
}

$q = mysqli_query($conn, "
SELECT 
    s.*, 
    t.tampak_depan,
    t.tampak_belakang,
    t.file_layout,
    p.nama_pelatihan
FROM sertifikat s
JOIN template t ON s.template_id = t.id
LEFT JOIN pelatihan p ON s.pelatihan_id = p.id
WHERE s.id = '$id'
");

$data = mysqli_fetch_assoc($q);

$bg_depan = $data['tampak_depan'];
$bg_belakang = $data['tampak_belakang'];

if (!$data) {
    die("Data tidak ditemukan");
}

$approved = ($data['status'] === 'approved');

if (!$isPreview && !$approved) {
    die("Sertifikat belum divalidasi direktur.");
}

if (!$isPreview && empty($data['nomor_sertifikat'])) {

    $tahun = date('Y');
    $bulan = date('m');
    $kategori = str_pad($data['pelatihan_id'] ?? '00', 2, '0', STR_PAD_LEFT);

    $namaLengkap = trim($data['nama'] ?? '');

    if ($namaLengkap === '') {
        $inisialBelakang = 'NA';
    } else {
        $parts = preg_split('/\s+/u', $namaLengkap);
        $namaBelakang = end($parts);
        $namaBelakang = strtoupper(preg_replace('/[^A-Z]/i', '', $namaBelakang));

        if ($namaBelakang === '') {
            $inisialBelakang = 'NA';
        } else {
            $inisialBelakang = substr($namaBelakang, -2);
            $inisialBelakang = str_pad($inisialBelakang, 2, 'X', STR_PAD_LEFT);
        }
    }

    mysqli_begin_transaction($conn);

    try {

        $prefix = "$tahun$bulan$kategori";

        $q2 = mysqli_query($conn, "
        SELECT MAX(
            CAST(SUBSTRING(nomor_sertifikat,9,4) AS UNSIGNED)
        ) AS last_no
        FROM sertifikat
        WHERE nomor_sertifikat LIKE '$prefix%'
        FOR UPDATE
        ");

        $row = mysqli_fetch_assoc($q2);

        $urut = ($row['last_no'] ?? 0) + 1;
        $nomorUrut = str_pad($urut, 4, '0', STR_PAD_LEFT);

        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $unique6 =
            randomLetters(2, $lower) .
            randomLetters(2, $upper) .
            randomLetters(2, $lower);

        $nomor_sertifikat = "{$tahun}{$bulan}{$kategori}{$nomorUrut}/{$unique6}{$inisialBelakang}";

        mysqli_query($conn, "
        UPDATE sertifikat 
        SET nomor_sertifikat='$nomor_sertifikat'
        WHERE id='$id'
        ");

        mysqli_commit($conn);

        $data['nomor_sertifikat'] = $nomor_sertifikat;

    } catch (Exception $e) {

        mysqli_rollback($conn);
        die("Terjadi kesalahan saat generate nomor sertifikat.");

    }
}

$periode = formatPeriode(
    strtotime($data['periode_awal']),
    strtotime($data['periode_akhir'])
);

$issued = !empty($data['issued_date'])
    ? date('F d, Y', strtotime($data['issued_date']))
    : '-';

$nomorFull = $data['nomor_sertifikat'] ?? '';
$pos = strpos($nomorFull, '/');
$nomor_tampil = ($pos !== false) ? substr($nomorFull, 0, $pos) : $nomorFull;

$kode_unik = $data['nomor_sertifikat'] ?? '';
$qrText = BASE_URL . "verify/verify.php?kode=" . $kode_unik;

$qrFolder = BASE_PATH . "/uploads/qrcode/";

if (!is_dir($qrFolder)) {
    mkdir($qrFolder, 0777, true);
}

$kodeUtama = explode('/', $kode_unik)[0];
$safeKode  = preg_replace('/[^A-Za-z0-9]/', '', $kodeUtama);
$qrFilename = "qr_" . $safeKode . ".png";
$qrPath = $qrFolder . $qrFilename;
$qrUrlPath = BASE_URL . "uploads/qrcode/" . $qrFilename;

if (!$isPreview && !file_exists($qrPath)) {

    $qrCode = new QrCode(
        data: $qrText,
        size: 400,
        margin: 10,
        errorCorrectionLevel: ErrorCorrectionLevel::High
    );

    $logo = new Logo(
        path: BASE_PATH . '/image/logo_putih.png',
        resizeToWidth: 150
    );

    $writer = new PngWriter();
    $result = $writer->write($qrCode, $logo);

    file_put_contents($qrPath, $result->getString());
}

$ttdDirektur = '';
$ttdPath = BASE_URL . '/image/ttd.png';

if ($approved) {
    $ttdDirektur = "<img src='{$ttdPath}' width='200'>";
}

$qMateri = mysqli_query($conn, "
    SELECT 
        sm.urutan,
        sm.durasi,
        mm.nama_materi
    FROM sertifikat_materi sm
    JOIN materi_master mm ON sm.materi_id = mm.id
    WHERE sm.sertifikat_id = '$id'
    ORDER BY sm.urutan ASC
");

$materiList = [];
$totalDurasi = 0;

while ($row = mysqli_fetch_assoc($qMateri)) {
    $materiList[] = $row;

    // kalau isinya angka → hitung total
    if (is_numeric($row['durasi'])) {
        $totalDurasi += (int)$row['durasi'];
    }
}



// Mengambil tampilan html dari folder pdf/layout/
$templateFile = BASE_PATH . "/pdf/layout/" . $data['file_layout'];
if (!$data['file_layout'] || !file_exists($templateFile)) {
    die("Template tidak ditemukan");
}
/* menangkap HTML dari layout */
ob_start();
include $templateFile;
$html = ob_get_clean();



$options = new Options();
$options->set('isRemoteEnabled  ', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

if ($isPreview) {

    header("Content-Type: application/pdf");
    echo $dompdf->output();
    exit;

}

$pdfFolder = BASE_PATH . "/uploads/sertifikat/";

if (!is_dir($pdfFolder)) {
    mkdir($pdfFolder, 0777, true);
}

$filename = $nomor_tampil . ".pdf";
$pdfPath = $pdfFolder . $filename;

if (file_exists($pdfPath)) {
    unlink($pdfPath);
}

file_put_contents($pdfPath, $dompdf->output());

mysqli_query($conn, "
UPDATE sertifikat
SET 
qr_code='$qrText',
qr_image='$qrFilename',
file_sertifikat='$filename'
WHERE id='$id'
");

$role = $_SESSION['role'] ?? '';

$redirect = BASE_URL;

if ($role === 'admin') {
    $redirect .= "admin/sertifikat/index.php";
} elseif ($role === 'lo') {
    $redirect .= "lo/sertifikat/index.php";
}

header("Location: $redirect");
exit;