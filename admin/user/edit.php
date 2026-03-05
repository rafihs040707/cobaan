<?php
$allowed_roles = ["admin"]; // superadmin otomatis lolos
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/admin/header.php';
require_once BASE_PATH . '/config/config.php';

$id = $_GET['id'];
$data_user = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
$user = mysqli_fetch_assoc($data_user);
?>

<h2 class="ms-5 my-4">Edit Data User</h2>

<form action="<?= BASE_URL ?>admin/user/proses_edit.php" method="POST" class="mx-4" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $user['id']; ?>">

    <div class="mb-2">
        <label class="form-label ms-3">Nama:</label>
        <input type="text" name="nama" value="<?= $user['nama']; ?>" class="form-control" required>
    </div>

    <div class="mb-2">
        <label class="form-label ms-3">Email:</label>
        <input type="email" name="email" value="<?= $user['email']; ?>" class="form-control" required>
    </div>

    <div class="mb-4">
        <label class="form-label ms-3">Role:</label>
        <select class="form-select form-select-sm" name="role" required>
            <option disabled>Pilih Status</option>
            <option value="admin" <?= ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
            <option value="lo" <?= ($user['role'] == 'lo') ? 'selected' : ''; ?>>LO</option>
            <option value="direktur" <?= ($user['role'] == 'direktur') ? 'selected' : ''; ?>>Direktur</option>
        </select>
    </div>

    <div class="d-flex justify-content-center mt-3">
        <button type="submit" name="update" class="btn btn-primary col-3">Update</button>
        <a href="<?= BASE_URL ?>admin/user/index.php" class="btn btn-secondary ms-2">Kembali</a>
    </div>
</form>

</div>
</div>
</div>

<script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
<script src="<?= BASE_URL ?>vendor/sidebar.js"></script>

</body>

</html>