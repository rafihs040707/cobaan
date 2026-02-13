<?php
$allowed_roles = ["lo"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/auth/cek_login.php';

if (isset($_POST['submit'])) {

    $id           = $_POST['id'];
    $nama         = $_POST['nama'];
    $pelatihan    = $_POST['pelatihan'];
    $periode_awal = $_POST['periode_awal'];
    $periode_akhir = $_POST['periode_akhir'];
    $issued_date  = $_POST['issued_date'];
    $status       = $_POST['status'];
    $template_id  = $_POST['template_id'];

    $stmt = $conn->prepare("
    UPDATE sertifikat 
    SET nama = ?, 
        pelatihan_id = ?, 
        periode_awal = ?, 
        periode_akhir = ?, 
        issued_date = ?, 
        status = ?, 
        template_id = ?
    WHERE id = ?
");


    $stmt->bind_param(
        "sisssiii",
        $nama,
        $pelatihan,
        $periode_awal,
        $periode_akhir,
        $issued_date,
        $status,
        $template_id,
        $id
    );

    session_start();

    if ($stmt->execute()) {
        $_SESSION['success'] = "Data berhasil diperbarui!";
        header("Location:" . BASE_URL . "lo/sertifikat/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Data gagal diperbarui. Silakan coba lagi!";
        header("Location:" . BASE_URL . "lo/sertifikat/index.php");
        exit;
    }
} else {
    die("Akses dilarang...");
}
