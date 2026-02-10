<?php
session_start();

// batas waktu 5 menit
$timeout = 300; // 5 menit

// jika belum login
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit;
}

// cek aktivitas terakhir
if (isset($_SESSION['last_activity'])) {
    if ((time() - $_SESSION['last_activity']) > $timeout) {
        session_unset();
        session_destroy();
        header("Location: index.php?pesan=timeout");
        exit;
    }
}

// update waktu aktivitas terakhir
$_SESSION['last_activity'] = time();
?>
