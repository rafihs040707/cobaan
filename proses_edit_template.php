<?php
include 'config.php';

if (isset($_POST['update'])) {

    $id   = $_POST['id'];
    $nama_template = $_POST['nama_template'];
    $penyelenggara = $_POST['penyelenggara'];

    $tampak_depan_lama    = $_POST['tampak_depan_lama'];

    // folder upload
    $folder = "uploads/template/";

    // === PROSES GAMBAR DEPAN ===
    if ($_FILES['tampak_depan']['name'] != "") {
        $tampak_depan = time() . "_" . $_FILES['tampak_depan']['name'];
        move_uploaded_file($_FILES['tampak_depan']['tmp_name'], $folder . $tampak_depan);

        // hapus file lama
        if ($tampak_depan_lama != "" && file_exists($folder . $tampak_depan_lama)) {
            unlink($folder . $tampak_depan_lama);
        }
    } else {
        $tampak_depan = $tampak_depan_lama;
    }

    // === UPDATE DATABASE ===
    $query = "UPDATE template SET 
                nama_template='$nama_template',
                penyelenggara='$penyelenggara',
                tampak_depan='$tampak_depan'
                WHERE id='$id'";

    session_start();

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Data template berhasil diperbarui.";
        header("Location: data_template.php");
        exit;
    } else {
        $_SESSION['error'] = "Data template gagal diperbarui. Silakan coba lagi.";
        header("Location: data_template.php");
        exit;
    }
}
