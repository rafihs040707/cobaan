<?php
include 'config.php';

if (isset($_POST['update'])) {

    $id   = $_POST['id'];
    $nama = $_POST['nama'];

    $tampak_depan_lama    = $_POST['tampak_depan_lama'];
    $tampak_belakang_lama = $_POST['tampak_belakang_lama'];

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
                nama='$nama',
                tampak_depan='$tampak_depan'
                WHERE id='$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: data_template.php?status=success");
    } else {
        echo "Gagal update data: " . mysqli_error($conn);
    }
}
