<?php
include "config.php";

if (isset($_POST['submit'])) {

    $nama        = $_POST['nama'];
    $kegiatan    = $_POST['pelatihan'];
    $periode_awal  = $_POST['periode_awal'];  
    $periode_akhir = $_POST['periode_akhir']; 
    $issued_date  = $_POST['issued_date'];    
    $status       = $_POST['status'];
    $template_id  = $_POST['template_id'];

    // simpan ke database (tanpa format periode)
    $stmt = $conn->prepare("
        INSERT INTO sertifikat 
        (nama, pelatihan, periode_awal, periode_akhir, issued_date, status, template_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("ssssssi", $nama, $kegiatan, $periode_awal, $periode_akhir, $issued_date, $status, $template_id);

    session_start();

    if ($stmt->execute()) {
        $last_id = $conn->insert_id;

        $_SESSION['success'] = "Data sertifikat berhasil ditambahkan dan tersimpan!";

        header("Location: data_sertifikat_lo.php?id=$last_id");
        exit;
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat menyimpan data. Silakan ulangi kembali!";

        header("Location: data_sertifikat_lo.php");
        exit;
    }
}
