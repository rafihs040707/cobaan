<?php
$allowed_roles = ["admin"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/config/config.php';

$id = $_GET['id'] ?? null;

if (!$id || !ctype_digit($id)) {
    $_SESSION['error'] = "ID template tidak valid.";
    header("Location:" . BASE_URL . "admin/template/index.php");
    exit;
}

$conn->begin_transaction();

try {

    // 🔒 ambil + lock row
    $stmt = $conn->prepare("
        SELECT tampak_depan, tampak_belakang 
        FROM template 
        WHERE id=? 
        FOR UPDATE
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if (!$data) {
        throw new Exception("Template tidak ditemukan.");
    }

    // path file
    $fileDepan = BASE_PATH . "/uploads/template/" . $data['tampak_depan'];
    $fileBelakang = BASE_PATH . "/uploads/template/" . $data['tampak_belakang'];

    // 🧹 hapus file dulu
    if (!empty($data['tampak_depan']) && file_exists($fileDepan)) {
        if (!unlink($fileDepan)) {
            throw new Exception("Gagal menghapus file depan.");
        }
    }

    if (!empty($data['tampak_belakang']) && file_exists($fileBelakang)) {
        if (!unlink($fileBelakang)) {
            throw new Exception("Gagal menghapus file belakang.");
        }
    }

    // 🗑️ hapus DB
    $stmtDel = $conn->prepare("DELETE FROM template WHERE id=?");
    $stmtDel->bind_param("i", $id);
    $stmtDel->execute();

    // ✅ commit
    $conn->commit();

    $_SESSION['success'] = "Template berhasil dihapus.";

} catch (Exception $e) {

    // ❌ rollback
    $conn->rollback();

    $_SESSION['error'] = $e->getMessage();
}

header("Location:" . BASE_URL . "admin/template/index.php");
exit;