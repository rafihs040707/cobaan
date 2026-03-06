<?php
$allowed_roles = ["admin"];
require_once __DIR__ . "/../../bootstrap.php";
require_once BASE_PATH . "/auth/cek_login.php";
require_once BASE_PATH . "/admin/header.php";
require_once BASE_PATH . "/config/config.php";

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    die("User tidak ditemukan");
}

?>


<head>
    <title>Reset Password</title>
</head>

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body">
                <h4>Reset Password User</h4>
                <hr>
                <p><b>Nama:</b> <?= $user['nama'] ?></p>
                <p><b>Email:</b> <?= $user['email'] ?></p>
                <p><b>Role:</b> <?= $user['role'] ?></p>
                <form method="POST" action="proses_reset.php">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <div class="mb-3">
                        <label>Password Baru</label>
                        <input type="password" name="password" class="form-control" minlength="8" required>
                    </div>
                    <button class="btn btn-danger">
                        Reset Password
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        Batal
                    </a>
                </form>
            </div>
        </div>
    </div>


    <script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
</body>

</html>