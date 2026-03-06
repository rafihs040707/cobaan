<?php
$allowed_roles = ["admin"];
require_once __DIR__ . "/../../bootstrap.php";
require_once BASE_PATH . "/auth/cek_login.php";
require_once BASE_PATH . "/config/config.php";

$id = $_POST['id'];

$password = $_POST['password'];

/* hash password */

$password_hash = password_hash($password, PASSWORD_DEFAULT);

/* update password */

mysqli_query($conn,"UPDATE users SET password='$password_hash' WHERE id='$id'");

/* redirect */

header("Location:" . BASE_URL . "admin/user/index.php");
exit;