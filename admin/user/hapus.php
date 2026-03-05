<?php
$allowed_roles = ["admin"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/config/config.php';

$id = $_GET['id'] ?? null;

// ✅ validasi ketat
if (!$id || !ctype_digit($id)) {
    $_SESSION['error'] = "ID user tidak valid.";
    header("Location:" . BASE_URL . "admin/user/index.php");
    exit;
}

// 🛡️ optional: cegah hapus diri sendiri
if ($id == $_SESSION['user_id']) {
    $_SESSION['error'] = "Tidak bisa menghapus akun sendiri.";
    header("Location:" . BASE_URL . "admin/user/index.php");
    exit;
}

// 🔍 cek user ada
$stmtCheck = $conn->prepare("SELECT id FROM users WHERE id=?");
$stmtCheck->bind_param("i", $id);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "User tidak ditemukan.";
    header("Location:" . BASE_URL . "admin/user/index.php");
    exit;
}

// 🗑️ delete aman
$stmtDel = $conn->prepare("DELETE FROM users WHERE id=?");
$stmtDel->bind_param("i", $id);
$delete = $stmtDel->execute();

if ($delete) {
    $_SESSION['success'] = "Data user berhasil dihapus.";
} else {
    $_SESSION['error'] = "Data user gagal dihapus.";
}

header("Location:" . BASE_URL . "admin/user/index.php");
exit;