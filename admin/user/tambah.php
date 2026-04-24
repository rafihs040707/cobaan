<?php
$allowed_roles = ["admin"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/admin/header.php';
?>

<head>
    <title>Tambah Data Pelatihan</title>
</head>

<h2 class="ms-5 my-4">Tambah Data pelatihan</h2>

<form action="<?= BASE_URL ?>admin/user/proses_tambah.php" method="POST" class="mx-4" enctype="multipart/form-data">

    <div class="mb-2">
        <label for="nama" class="form-label ms-3">Nama: </label>
        <input type="text" name="nama" placeholder="Example: Rafi" class="form-control" maxlength="100"
            required autocomplete="off"><br>
    </div>

    <div class="mb-2">
        <label for="email" class="form-label ms-3">Email: </label>
        <input type="email" name="email" placeholder="Example@gmail.com" class="form-control" maxlength="64" autocomplete="off" required><br>
    </div>

    <div class="mb-2">
        <label for="password" class="form-label ms-3">Password: </label>
        <input type="password" name="password" placeholder="12345678" class="form-control" minlength="8"
            maxlength="64" required><br>
    </div>

    <div class="mb-4">
        <label for="role" class="form-label ms-3">Role: </label>
        <select class="form-select form-select-sm" aria-label=".form-select-sm example" name="role" required>
            <option value="" selected disabled>Pilih Role</option>
            <option value="admin">Admin</option>
            <option value="lo">LO</option>
            <option value="direktur">Direktur</option>
        </select>
    </div>

    <div class="d-grid gap-2 d-flex justify-content-center mt-3">
        <button type="submit" name="submit" class="btn btn-primary ms-2 col-3">Submit</button>
        <button type="reset" class="btn btn-warning ms-2 col-3">Reset Form</button>
        <a href="<?= BASE_URL ?>admin/user/index.php" style="background-color: #6C7301;"
            class="btn text-decoration-none text-white">Kembali Ke Halaman
            user</a>
    </div>
</form>





</div>
</div>
</div>

<script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
<script src="<?= BASE_URL ?>vendor/sidebar.js"></script>

</body>

</html>