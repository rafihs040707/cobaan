<?php
$allowed_roles = ["admin"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/admin/header.php';
require_once BASE_PATH . '/config/config.php';
?>

<head>
    <title>Tambah Data Sertifikat</title>
</head>

<h2 class="ms-5 my-4">Tambah Data Sertifikat</h2>

<form action="<?= BASE_URL ?>/admin/sertifikat/proses_tambah.php" method="POST" class="mx-4">

    <div class="mb-4">
        <label for="nama" class="form-label ms-3">Nama: </label>
        <input type="text" name="nama" placeholder="Example: Arkan Beckham" class="form-control" maxlength="64"
            required>
    </div>

    <div class="mb-4">
        <label for="pelatihan" class="form-label ms-3">Pelatihan: </label>
        <select class="form-select form-select-sm" name="pelatihan" required>
            <option disabled selected>Pilih Pelatihan</option>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM pelatihan");
            while ($p = mysqli_fetch_assoc($q)) {
                echo "<option value='" . $p['id'] . "'>" . $p['nama_pelatihan'] . "</option>";
            }
            ?>
        </select>
    </div>

    <div class="mb-4">
        <label for="periode_awal" class="form-label ms-3">Periode Awal: </label>
        <input type="date" name="periode_awal" class="form-control" required onfocus="this.showPicker()">
    </div>

    <div class="mb-4">
        <label for="periode_akhir" class="form-label ms-3">Periode Akhir: </label>
        <input type="date" name="periode_akhir" class="form-control" required onfocus="this.showPicker()">
    </div>

    <div class="mb-4">
        <label for="issued_date" class="form-label ms-3">Issued Date: </label>
        <input type="date" name="issued_date" class="form-control" required onfocus="this.showPicker()">
    </div>

    <div class="mb-4">
        <label for="status" class="form-label ms-3">Status: </label>
        <select class="form-select form-select-sm" aria-label=".form-select-sm example" name="status" required>
            <option selected disabled>Pilih Status</option>
            <option value="1">Valid</option>
            <option value="0">Tidak Valid</option>
        </select>
    </div>

    <div class="mb-4">
        <label for="template_id" class="form-label ms-3">Template Sertifikat: </label>
        <select class="form-select form-select-sm" aria-label=".form-select-sm example" name="template_id" required>
            <option selected disabled>Pilih Template Sertifikat</option>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM template");
            while ($t = mysqli_fetch_assoc($q)) {
                echo "<option value='" . $t['id'] . "'>" . $t['nama_template'] . "</option>";
            }
            ?>
        </select>
    </div>

    <div class="d-grid gap-2 d-flex justify-content-center mt-3 pb-5">
        <button type="submit" name="submit" class="btn btn-primary ms-2 col-3">Submit</button>
        <button type="reset" class="btn btn-warning ms-2 col-3">Reset Form</button>
        <a href="<?= BASE_URL ?>admin/sertifikat/index.php" style="background-color: #6C7301;"
            class="btn text-decoration-none text-white">Kembali Ke Halaman
            Sertifikat</a>
    </div>
</form>





<script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
</body>

</html>