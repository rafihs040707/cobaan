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

<form action="<?= BASE_URL ?>admin/pelatihan/proses_tambah.php" method="POST" class="mx-4" enctype="multipart/form-data">

    <div class="mb-2">
        <label for="nama_pelatihan" class="form-label ms-3">Nama Pelatihan: </label>
        <input type="text" name="nama_pelatihan" placeholder="Example: Front-End" class="form-control"
            maxlength="100" required autocomplete="off"><br>
    </div>

    <div class="mb-2">
        <label for="instruktur" class="form-label ms-3">Instruktur: </label>
        <input type="text" name="instruktur" placeholder="Example: Rafi" class="form-control"
            maxlength="100" required autocomplete="off"><br>
    </div>

    <div class="mb-2">
        <label for="deskripsi" class="form-label ms-3">Deskripsi: </label>
        <input type="text" name="deskripsi" placeholder="Example: Belajar membuat tampilan menarik" class="form-control"
            maxlength="255" autocomplete="off"><br>
    </div>

    <div class="d-grid gap-2 d-flex justify-content-center mt-3">
        <button type="submit" name="submit" class="btn btn-primary ms-2 col-3">Submit</button>
        <button type="reset" class="btn btn-warning ms-2 col-3">Reset Form</button>
        <a href="<?= BASE_URL ?>admin/pelatihan/index.php" style="background-color: #6C7301;"
            class="btn text-decoration-none text-white">Kembali Ke Halaman
            Pelatihan</a>
    </div>
</form>





</div>
</div>
</div>

<script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
<script src="<?= BASE_URL ?>vendor/sidebar.js"></script>

</body>

</html>