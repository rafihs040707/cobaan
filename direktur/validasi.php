<?php
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/auth/cek_login.php';

if ($_SESSION['role'] !== 'direktur') {
    die("Akses ditolak");
}

$id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if ($id) {

    $stmt = $conn->prepare("
        UPDATE sertifikat 
        SET 
            status = 'approved',
            validated_by = ?,
            validated_at = NOW(),
            issued_date = IFNULL(issued_date, NOW())
        WHERE id = ?
    ");

    $stmt->execute([$user_id, $id]);
}

header("Location:" . BASE_URL . "direktur/sertifikat.php");
exit;