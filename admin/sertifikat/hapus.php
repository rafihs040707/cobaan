<?php
$allowed_roles = ["admin"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
session_start();
require_once BASE_PATH . '/config/config.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID sertifikat tidak ditemukan.";
    header("Location: <?= BASE_URL ?>index.php");
    exit;
}

// ambil data dulu (untuk nama file pdf & qr)
$query = mysqli_query($conn, "SELECT file_sertifikat, qr_image FROM sertifikat WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    $_SESSION['error'] = "Data sertifikat tidak ditemukan.";
    header("Location:" . BASE_URL . "admin/sertifikat/index.php");
    exit;
}

// path file pdf & qr
$pdfPath = BASE_PATH . "/uploads/sertifikat/" . $data['file_sertifikat'];
$qrPath  = BASE_PATH . "/uploads/qrcode/" . $data['qr_image'];

// hapus file pdf jika ada
if (!empty($data['file_sertifikat']) && file_exists($pdfPath)) {
    unlink($pdfPath);
}

// hapus file qr jika ada
if (!empty($data['qr_image']) && file_exists($qrPath)) {
    unlink($qrPath);
}

// hapus data dari database
$delete = mysqli_query($conn, "DELETE FROM sertifikat WHERE id='$id'");

if ($delete) {
    $_SESSION['success'] = "Data sertifikat berhasil dihapus.";
} else {
    $_SESSION['error'] = "Data sertifikat gagal dihapus.";
}

header("Location:" . BASE_URL . "admin/sertifikat/index.php");
exit;
?>
