<?php
ob_start();
$allowed_roles = ["admin","lo"];
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

error_reporting(0);
ini_set('display_errors', 0);

$id = $_GET['id'] ?? null;
if (!$id) die("ID tidak ditemukan");

// ======================
// AMBIL DATA SERTIFIKAT + TEMPLATE
// ======================
$q = mysqli_query($conn, "
    SELECT 
        s.*, 
        t.tampak_depan,
        p.nama_pelatihan
    FROM sertifikat s
    JOIN template t ON s.template_id = t.id
    LEFT JOIN pelatihan p ON s.pelatihan_id = p.id
    WHERE s.id = '$id'
");


$data = mysqli_fetch_assoc($q);
if (!$data) die("Data tidak ditemukan");

// ======================
// GENERATE NOMOR SERTIFIKAT
// ======================
if (empty($data['nomor_sertifikat'])) {
    $tahun = date('Y');
    $bulan = date('m');

    $q2 = mysqli_query($conn, "
        SELECT MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(nomor_sertifikat, '/', -1), '-', 1) AS UNSIGNED)) AS last_no
        FROM sertifikat
        WHERE nomor_sertifikat IS NOT NULL
        AND nomor_sertifikat LIKE 'CERT/$tahun/$bulan/%'
    ");

    $row = mysqli_fetch_assoc($q2);

    $urut = ($row['last_no'] ?? 0) + 1;
    $nomor = str_pad($urut, 4, '0', STR_PAD_LEFT);

    $uuid  = substr(bin2hex(random_bytes(4)), 0, 8);

    $nomor_sertifikat = "CERT/$tahun/$bulan/$nomor-$uuid";

    mysqli_query($conn, "
        UPDATE sertifikat 
        SET nomor_sertifikat = '$nomor_sertifikat'
        WHERE id = '$id'
    ");

    $data['nomor_sertifikat'] = $nomor_sertifikat;
}

// ======================
// FORMAT TANGGAL
// ======================
$awal  = strtotime($data['periode_awal']);
$akhir = strtotime($data['periode_akhir']);

if (date('F Y', $awal) == date('F Y', $akhir)) {
    $periode = date('F d', $awal) . " - " . date('d, Y', $akhir);
} else {
    $periode = date('F d', $awal) . " - " . date('F d, Y', $akhir);
}

$issued = date('F d, Y', strtotime($data['issued_date']));

// jika ada penyelenggara di tabel sertifikat
    if (!empty($data['penyelenggara'])) {
        $penyelenggara = $data['penyelenggara'];
    }

// ======================
// QR TEXT (LINK VERIFIKASI)
// ======================
$parts = explode("-", $data['nomor_sertifikat']);
$uuid = end($parts);

$qrText = "http://localhost/sertifikat/verify/verify.php?uuid=" . $uuid;


// ======================
// GENERATE QR CODE + SIMPAN FILE
// ======================
$qrFolder = BASE_PATH . "/uploads/qrcode/";
if (!is_dir($qrFolder)) {
    mkdir($qrFolder, 0777, true);
}

$qrFilename = "qr_" . $id . ".png";
$qrPath = $qrFolder . $qrFilename;

$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qrText);
$qrContent = @file_get_contents($qrUrl);

if (!$qrContent) {
    die("Gagal generate QR Code. Pastikan internet aktif atau allow_url_fopen ON.");
}

file_put_contents($qrPath, $qrContent);
$qrUrlPath = BASE_URL . "uploads/qrcode/" . $qrFilename;

// ======================
// TEMPLATE PATH
// ======================

$templatePath = BASE_URL . "uploads/template/" . $data['tampak_depan'];

// ======================
// HTML PDF (POSISI SUDAH DISESUAIKAN TEMPLATE)
// ======================
$html = "
<!DOCTYPE html>
<html>
<head>
<style>
@page { margin: 0; }

body {
    margin: 0;
    padding: 0;
    font-family: 'Times New Roman', serif;
}

/* background template */
.bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 1123px;
    height: 794px;
    z-index: -1;
}

/* NAMA PESERTA */
.nama {
    position: absolute;
    top: 300px;
    left: 53px;
    width: 100%;
    text-align: center;
    font-size: 45px;
    font-weight: bold;
    color: #cfa34a;
}

/* PELATIHAN */
.pelatihan {
    position: absolute;
    top: 445px;
    left: 60px;
    width: 100%;
    text-align: center;
    font-size: 26px;
    font-weight: bold;
    color: #cfa34a;
}

/* PERIODE */
.periode {
    position: absolute;
    top: 500px;
    left: 50px;
    width: 100%;
    text-align: center;
    font-size: 20px;
    font-weight: normal;
    color: black;
}

/* ISSUED DATE kiri bawah */
.issued {
    position: absolute;
    bottom: 27px;
    left: 60px;
    font-size: 15px;
    color: black;
}

/* NOMOR kanan bawah */
.nomor {
    position: absolute;
    bottom: 143px;
    right: 5px;
    font-size: 11px;
    color: black;
}

/* QR kanan bawah */
.qr {
    position: absolute;
    bottom: 18px;
    right: 13px;
}
</style>
</head> 
<body>

<img class='bg' src='{$templatePath}'>

<div class='nama'>{$data['nama']}</div>
<div class='pelatihan'>{$data['nama_pelatihan']}</div>
<div class='periode'>Periode: {$periode}</div>

<div class='issued'>Issued Date: {$issued}</div>
<div class='nomor'>{$data['nomor_sertifikat']}</div>

<div class='qr'>
    <img src='{$qrUrlPath}' width='120'>
</div>  

</body>
</html>
";

// ======================
// DOMPDF SETTINGS
// ======================
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();


// ======================
// SIMPAN PDF
// ======================
$pdfFolder = BASE_PATH . "/uploads/sertifikat/";
if (!is_dir($pdfFolder)) {
    mkdir($pdfFolder, 0777, true);
}

$filename = "sertifikat_" . preg_replace('/[^A-Za-z0-9_\-]/', '_', $data['nama']) . ".pdf";
$pdfPath = $pdfFolder . $filename;

file_put_contents($pdfPath, $dompdf->output());

// ======================
// UPDATE DATABASE
// ======================
mysqli_query($conn, "
    UPDATE sertifikat
    SET qr_code = '$qrText',
        qr_image = '$qrFilename',
        file_sertifikat = '$filename'
    WHERE id = '$id'
");

// ======================
// OUTPUT PDF
// ======================
ob_end_clean();

$preview = isset($_GET['preview']);

// pastikan file sudah tersimpan
if (!file_exists($pdfPath)) {
    die("File PDF tidak ditemukan.");
}

// URL file PDF yang sudah dibuat
$fileUrl = BASE_URL . "uploads/sertifikat/" . $filename;

if ($preview) {
    header("Location: " . $fileUrl);
    exit;
}

// kalau mau force download
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
readfile($pdfPath);
exit;


?>
