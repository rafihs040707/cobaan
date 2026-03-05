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
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id = $_GET['id'] ?? null;      
if (!$id)
    die("ID tidak ditemukan");
$q3 = mysqli_query($conn,"SELECT status FROM sertifikat WHERE id = '$id'");
$row = mysqli_fetch_assoc($q3);
if ($row['status'] !== 'approved') {
    die("Sertifikat belum divalidasi direktur.");
}
$approved = ($row['status'] === 'approved');
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
if (!$data)
    die("Data tidak ditemukan");

// ======================
// GENERATE NOMOR SERTIFIKAT
// ======================
if (empty($data['nomor_sertifikat'])) {

    $tahun = date('Y');
    $bulan = date('m');

    // 🔹 kategori dari tabel pelatihan (misal: 01, 02, dst)
    $kategori = str_pad($data['pelatihan_id'] ?? '00', 2, '0', STR_PAD_LEFT);

    // ===============================
    // 🔹 ambil 2 huruf terakhir nama belakang
    // ===============================
    $namaLengkap = trim($data['nama'] ?? '');

    if ($namaLengkap === '') {
        $inisialBelakang = 'NA';
    } else {
        // pecah nama
        $parts = preg_split('/\s+/u', $namaLengkap);

        // ambil kata terakhir (kalau cuma 1 kata tetap aman)
        $namaBelakang = end($parts);

        // bersihkan huruf saja
        $namaBelakang = strtoupper(preg_replace('/[^A-Z]/i', '', $namaBelakang));

        if ($namaBelakang === '') {
            $inisialBelakang = 'NA';
        } else {
            // ambil 2 huruf dari belakang
            $inisialBelakang = substr($namaBelakang, -2);

            // kalau kurang dari 2 huruf → pad kiri
            $inisialBelakang = str_pad($inisialBelakang, 2, 'X', STR_PAD_LEFT);
        }
    }

    mysqli_begin_transaction($conn);

    try {

        // ======================================
        // 🔒 LOCK untuk nomor urut per bulan
        // ======================================
        $prefix = "$tahun$kategori$bulan";

        $q2 = mysqli_query($conn, "
            SELECT MAX(
                CAST(SUBSTRING(nomor_sertifikat, 9, 4) AS UNSIGNED)
            ) AS last_no
            FROM sertifikat
            WHERE nomor_sertifikat LIKE '$prefix%'
            FOR UPDATE
        ");

        $row = mysqli_fetch_assoc($q2);

        $urut = ($row['last_no'] ?? 0) + 1;
        $nomorUrut = str_pad($urut, 4, '0', STR_PAD_LEFT);

        // ======================================
        // 🔐 unique6 pola: 2 kecil, 2 besar, 2 kecil
        // ======================================
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

        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $unique6 =
            randomLetters(2, $lower) .
            randomLetters(2, $upper) .
            randomLetters(2, $lower);

        // ======================================
        // 🧾 final format
        // ======================================
        $nomor_sertifikat = "{$tahun}{$kategori}{$bulan}{$nomorUrut}/{$unique6}{$inisialBelakang}";

        $update = mysqli_query($conn, "
            UPDATE sertifikat 
            SET nomor_sertifikat = '$nomor_sertifikat'
            WHERE id = '$id'
        ");

        if (!$update) {
            throw new Exception("Gagal update nomor sertifikat");
        }

        mysqli_commit($conn);

        $data['nomor_sertifikat'] = $nomor_sertifikat;

    } catch (Exception $e) {

        mysqli_rollback($conn);
        die("Terjadi kesalahan saat generate nomor sertifikat.");
    }
}


// ======================
// FORMAT TANGGAL
// ======================
$awal = strtotime($data['periode_awal']);
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
// POTONG NOMOR UNTUK TAMPILAN (SETELAH GENERATE FIX)
// ======================
$nomorFull = $data['nomor_sertifikat'] ?? '';

if ($nomorFull !== '') {
    $pos = strpos($nomorFull, '/');
    $nomor_tampil = ($pos !== false) ? substr($nomorFull, 0, $pos) : $nomorFull;
} else {
    $nomor_tampil = '';
}

// ======================
// GENERATE QR CODE + LOGO (ENDROID v6)
// ======================
$kode_unik = $data['nomor_sertifikat'] ?? '';
$qrText = BASE_URL . "verify/verify.php?kode=" . $kode_unik;

$qrFolder = BASE_PATH . "/uploads/qrcode/";
if (!is_dir($qrFolder)) {
    mkdir($qrFolder, 0777, true);
}

$safeKode = preg_replace('/[^A-Za-z0-9]/', '_', $kode_unik);
$qrFilename = "qr_" . $safeKode . ".png";
$qrPath = $qrFolder . $qrFilename;
$qrUrlPath = BASE_URL . "uploads/qrcode/" . $qrFilename;

// 🔥 hanya generate jika belum ada & bukan preview
if (!file_exists($qrPath)) {

    // QR
    $qrCode = new QrCode(
        data: $qrText,
        size: 400,
        margin: 10,
        errorCorrectionLevel: ErrorCorrectionLevel::High
    );

    // LOGO (PASTIKAN PATH BENAR)
    $logo = new Logo(
        path: BASE_PATH . '/image/logo_putih.png',
        resizeToWidth: 150
    );

    $writer = new PngWriter();
    $result = $writer->write($qrCode, $logo);

    file_put_contents($qrPath, $result->getString());
}
    
// $ttdDirektur = '';
// $ttdPath = BASE_PATH . '/image/ttd_direktur.png';

// if ($approved) {
//     $ttdDirektur = "<img src='{$ttdPath}' width='200'>";
// }

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
    top: 505px;
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
    left: 50px;
    font-size: 15px;
    color: black;
}

/* CEO tengah bawah */
.nama_ceo {
    position: absolute;
    bottom: 55px;
    left: 50px;
    width: 100%;
    text-align: center;
    font-size: 18px;
    font-weight: normal;
    color: black;
}

/* CEO tengah bawah */
.ceo {
    position: absolute;
    bottom: 30px;
    left: 50px;
    width: 100%;
    text-align: center;
    font-size: 16px;
    font-weight: normal;
    color: black;
}

/* NOMOR kanan bawah */
.nomor {
    position: absolute;
    bottom: 145px;
    right: 35px;
    font-size: 15px;
    color: black;
}

/* QR kanan bawah */
.qr {
    position: absolute;
    bottom: 26px;
    right: 22px;
}
</style>
</head> 
<body>

<img class='bg' src='{$templatePath}'>

<div class='nama'>{$data['nama']}</div>
<div class='pelatihan'><b>{$data['nama_pelatihan']}</b></div>
<div class='periode'>Periode: {$periode}</div>

<div class='issued'>Issued Date: {$issued}</div>
<div class='nama_ceo'><u>Endra Prasetya Rudiyanto</u></div>
<div class='ceo'>Chief Executive Officer</div>
<!-- <div class='ttd_direktur'>{$ttdDirektur}</div> -->
<div class='nomor'>" . $nomor_tampil . "</div>

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
// ======================
// SIMPAN PDF
// ======================

$pdfFolder = BASE_PATH . "/uploads/sertifikat/";
if (!is_dir($pdfFolder)) {
    mkdir($pdfFolder, 0777, true);
}

$cleanNomor = $nomor_tampil;
$filename = $cleanNomor . ".pdf";
$pdfPath = $pdfFolder . $filename;

if (file_exists($pdfPath)) {
    unlink($pdfPath);
}

file_put_contents($pdfPath, $dompdf->output());

mysqli_query($conn, "
    UPDATE sertifikat
    SET qr_code = '$qrText',
        qr_image = '$qrFilename',
        file_sertifikat = '$filename'
    WHERE id = '$id'
");

// 🔵 GENERATE MODE — tidak tampil, langsung balik ke index
$role = $_SESSION['role'] ?? '';

$redirect = BASE_URL;

if ($role === 'admin') {
    $redirect .= "admin/sertifikat/index.php";
} elseif ($role === 'lo') {
    $redirect .= "lo/sertifikat/index.php";
}

header("Location: $redirect");
exit;
