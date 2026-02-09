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

    if ($stmt->execute()) {
        header("Location: data_sertifikat.php");
        exit;
    } else {
        echo "Gagal update data: " . $stmt->error;
    }
} else {
    die("Akses dilarang...");
}
