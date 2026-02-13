<?php
$allowed_roles = ["admin"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/admin/header.php';
require_once BASE_PATH . '/config/config.php';

$id = $_GET['id'];
$data_sertifikat = mysqli_query($conn, "SELECT * FROM sertifikat WHERE id='$id'");
$sertifikat = mysqli_fetch_assoc($data_sertifikat);
?>

<head>
    <title>Edit Data Sertifikat</title>
</head>

<h2 class="ms-5 my-4">Edit Data Sertifikat</h2>

<form action="<?= BASE_URL ?>admin/sertifikat/proses_edit.php" method="POST" class="mx-4">

    <input type="hidden" name="id" value="<?= $sertifikat['id']; ?>">

    <div class="mb-4">
        <label class="form-label ms-3">Nama:</label>
        <input type="text" name="nama" value="<?= $sertifikat['nama']; ?>" class="form-control" maxlength="64" required>
    </div>

    <div class="mb-4">
        <label class="form-label ms-3">Pelatihan:</label>
        <select class="form-select form-select-sm" name="pelatihan" required>
            <option disabled>Pilih Pelatihan</option>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM pelatihan");
            while ($p = mysqli_fetch_assoc($q)) {
                echo "<option value='" . $p['id'] . "'>" . $p['nama_pelatihan'] . "</option>";
            }
            ?>
        </select>
    </div>

    <div class="mb-4">
        <label class="form-label ms-3">Periode Awal:</label>
        <input type="date" name="periode_awal" value="<?= $sertifikat['periode_awal']; ?>" class="form-control" required onfocus="this.showPicker()">
    </div>

    <div class="mb-4">
        <label class="form-label ms-3">Periode Akhir:</label>
        <input type="date" name="periode_akhir" value="<?= $sertifikat['periode_akhir']; ?>" class="form-control" required onfocus="this.showPicker()">
    </div>

    <div class="mb-4">
        <label class="form-label ms-3">Issued Date:</label>
        <input type="date" name="issued_date" value="<?= $sertifikat['issued_date']; ?>" class="form-control" required onfocus="this.showPicker()">
    </div>

    <div class="mb-4">
        <label class="form-label ms-3">Status:</label>
        <select class="form-select form-select-sm" name="status" required>
            <option disabled>Pilih Status</option>
            <option value="1" <?= ($sertifikat['status'] == 1) ? 'selected' : ''; ?>>Valid</option>
            <option value="0" <?= ($sertifikat['status'] == 0) ? 'selected' : ''; ?>>Tidak Valid</option>
        </select>
    </div>

    <div class="mb-4">
        <label class="form-label ms-3">Template Sertifikat:</label>
        <select class="form-select form-select-sm" name="template_id" required>
            <option disabled>Pilih Template Sertifikat</option>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM template");
            while ($t = mysqli_fetch_assoc($q)) {
                echo "<option value='" . $t['id'] . "'>" . $t['nama_template'] . "</option>";
            }
            ?>
        </select>
    </div>

    <div class="d-grid gap-2 d-flex justify-content-center mt-3 pb-5">
        <button type="submit" name="submit" class="btn btn-primary ms-2 col-3">Update</button>
        <a href="<?= BASE_URL ?>admin/sertifikat/index.php" style="background-color: #6C7301;" class="btn text-decoration-none text-white">
            Kembali Ke Halaman Sertifikat
        </a>
    </div>

</form>

<script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
</body>

</html>