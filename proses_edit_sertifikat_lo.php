<?php
include("config.php");

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
            pelatihan = ?, 
            periode_awal = ?, 
            periode_akhir = ?, 
            issued_date = ?, 
            status = ?, 
            template_id = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "sssssiii",
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
        header("Location: data_sertifikat_lo.php");
        exit;
    } else {
        $_SESSION['error'] = "Data gagal diperbarui. Silakan coba lagi!";
        header("Location: data_sertifikat_lo.php");
        exit;
    }
} else {
    die("Akses dilarang...");
}
