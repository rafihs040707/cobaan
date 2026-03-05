<?php
$allowed_roles = ["admin"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/config/config.php';

if (isset($_POST['update'])) {

    $id                 = $_POST['id'];
    $nama_pelatihan     = $_POST['nama_pelatihan'];
    $instruktur         = $_POST['instruktur'];
    $deskripsi          = $_POST['deskripsi'];

    $stmt = $conn->prepare("
        UPDATE pelatihan 
        SET nama_pelatihan = ?, 
            instruktur = ?,
            deskripsi = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "sssi",
        $nama_pelatihan,
        $instruktur,
        $deskripsi,
        $id
    );

    session_start();

    if ($stmt->execute()) {
        $_SESSION['success'] = "Data berhasil diperbarui!";
        header("Location: " . BASE_URL . "/admin/pelatihan/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Data gagal diperbarui. Silakan coba lagi!";
        header("Location: " . BASE_URL . "/admin/pelatihan/index.php");
        exit;
    }
} else {
    die("Akses dilarang...");
}
