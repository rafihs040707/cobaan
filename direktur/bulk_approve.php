<?php
$allowed_roles = ["direktur"];
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/config/config.php';

if (!empty($_POST['ids'])) {

    $ids = $_POST['ids'];
    $user_id = $_SESSION['user_id']; // ambil direktur yang approve

    // buat placeholder (?, ?, ?)
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // parameter: user_id + semua id
    $params = array_merge([$user_id], $ids);

    $stmt = $conn->prepare("
    UPDATE sertifikat 
    SET 
        status = 'approved',
        validated_by = ?,
        validated_at = NOW(),
        issued_date = IFNULL(issued_date, NOW())
    WHERE status = 'pending'
    AND id IN ($placeholders)
");

    $stmt->execute($params);
}

header("Location:" . BASE_URL . "direktur/sertifikat.php");
exit;