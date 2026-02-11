<?php
session_start();
include "config.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID template tidak ditemukan.";
    header("Location: data_template.php");
    exit;
}

// ambil data template dulu untuk dapat nama file gambarnya
$query = mysqli_query($conn, "SELECT tampak_depan FROM template WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    $_SESSION['error'] = "Template tidak ditemukan.";
    header("Location: data_template.php");
    exit;
}

// path file gambar
$filePath = "uploads/template/" . $data['tampak_depan'];

// hapus data dari database
$delete = mysqli_query($conn, "DELETE FROM template WHERE id='$id'");

if ($delete) {

    // hapus file jika ada
    if (!empty($data['tampak_depan']) && file_exists($filePath)) {
        unlink($filePath);
    }

    $_SESSION['success'] = "Template berhasil dihapus.";
    header("Location: data_template.php");
    exit;

} else {
    $_SESSION['error'] = "Template gagal dihapus.";
    header("Location: data_template.php");
    exit;
}
?>
