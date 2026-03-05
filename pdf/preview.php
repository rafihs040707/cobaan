<?php
$allowed_roles = ["admin", "lo"];
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/config/config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID tidak ditemukan");
}

$q = mysqli_query($conn,"SELECT file_sertifikat FROM sertifikat WHERE id='$id'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    die("Data tidak ditemukan");
}

$filename = $data['file_sertifikat'];
$pdfPath  = BASE_PATH . "/uploads/sertifikat/" . $filename;

if (!file_exists($pdfPath)) {
    die("File belum digenerate.");
}

// redirect ke file PDF asli
$pdfUrl = BASE_URL . "uploads/sertifikat/" . $filename;

header("Location: $pdfUrl");
exit;