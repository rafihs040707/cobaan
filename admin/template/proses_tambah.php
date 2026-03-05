<?php
$allowed_roles = ["admin"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/config/config.php';

if (isset($_POST['submit'])) {

    $nama_template = mysqli_real_escape_string($conn, $_POST['nama_template']);
    $penyelenggara = mysqli_real_escape_string($conn, $_POST['penyelenggara']);
    $targetDir = BASE_PATH . "/uploads/template/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    function uploadFile($fieldName, $targetDir)
    {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] != 0) {
            return null;
        }

        $file = $_FILES[$fieldName];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png'];
        if (!in_array($ext, $allowed)) {
            die("Format file tidak valid! (jpg, jpeg, png)");
        }

        $hashName = md5(uniqid()) . '.' . $ext;
        $targetFile = $targetDir . $hashName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $hashName;
        }
        return null;
    }

    $tampak_depan = uploadFile('tampak_depan', $targetDir);
    $tampak_belakang = uploadFile('tampak_belakang', $targetDir);

    $stmt = $conn->prepare("INSERT INTO template (nama_template, penyelenggara, tampak_depan, tampak_belakang) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama_template, $penyelenggara, $tampak_depan, $tampak_belakang);
    


    if ($stmt->execute()) {
        $_SESSION['success'] = "Data template berhasil ditambahkan dan tersimpan!";
        header("Location:" . BASE_URL . "admin/template/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat menyimpan data. Silakan ulangi kembali!";
        header("Location:" . BASE_URL . "admin/template/index.php");
        exit;
    }
} else {
    die("Akses dilarang...");
}
