<?php
$allowed_roles = ["admin"];
require_once __DIR__ . "/../../bootstrap.php";
require_once BASE_PATH . "/auth/cek_login.php";
require_once BASE_PATH . "/config/config.php";

$id = $_POST['id'];
$password = $_POST['password'];

if (!ctype_digit($id)) die("ID tidak valid");

/* hash password */
$password_hash = password_hash($password, PASSWORD_DEFAULT);

/* update password (AMAN) */
$stmt = mysqli_prepare($conn, "UPDATE users SET password=? WHERE id=?");
mysqli_stmt_bind_param($stmt, "si", $password_hash, $id);
mysqli_stmt_execute($stmt);

/* redirect */
header("Location:" . BASE_URL . "admin/user/index.php");
exit;