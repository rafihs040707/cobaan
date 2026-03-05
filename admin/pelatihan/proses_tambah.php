<?php
$allowed_roles = ["admin"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/config/config.php';

if (isset($_POST['submit'])) {

    $nama_pelatihan     = $_POST['nama_pelatihan'];
    $instruktur         = $_POST['instruktur'];
    $deskripsi          = $_POST['deskripsi'];

    // simpan ke database (tanpa format periode)
    $stmt = $conn->prepare("
        INSERT INTO pelatihan 
        (nama_pelatihan, instruktur, deskripsi)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("sss", $nama_pelatihan, $instruktur, $deskripsi);


    if ($stmt->execute()) {
        $last_id = $conn->insert_id;

        $_SESSION['success'] = "Data pelatihan berhasil ditambahkan dan tersimpan!";

        header("Location:" . BASE_URL . "admin/pelatihan/index.php?id=$last_id");
        exit;
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat menyimpan data. Silakan ulangi kembali!";

        header("Location:" . BASE_URL . "admin/pelatihan/index.php");
        exit;
    }
}
