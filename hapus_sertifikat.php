<?php
session_start();
include "config.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID sertifikat tidak ditemukan.";
    header("Location: data_sertifikat.php");
    exit;
}

// ambil data dulu (untuk nama file pdf & qr)
$query = mysqli_query($conn, "SELECT file_sertifikat, qr_image FROM sertifikat WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    $_SESSION['error'] = "Data sertifikat tidak ditemukan.";
    header("Location: data_sertifikat.php");
    exit;
}

// path file pdf & qr
$pdfPath = "uploads/sertifikat/" . $data['file_sertifikat'];
$qrPath  = "uploads/qrcode/" . $data['qr_image'];

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

header("Location: data_sertifikat.php");
exit;
?>
