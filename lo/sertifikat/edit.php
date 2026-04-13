<?php
$allowed_roles = ["lo"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/lo/header.php';
require_once BASE_PATH . '/config/config.php';

$id = $_GET['id'];
$qMateri = mysqli_query($conn, "
    SELECT sm.*, mm.nama_materi 
    FROM sertifikat_materi sm
    JOIN materi_master mm ON sm.materi_id = mm.id
    WHERE sm.sertifikat_id = '$id'
    ORDER BY sm.urutan ASC
");
$data_sertifikat = mysqli_query($conn, "SELECT * FROM sertifikat WHERE id='$id'");
$sertifikat = mysqli_fetch_assoc($data_sertifikat);
?>

<head>
    <title>Edit Data Sertifikat</title>
</head>

<h2 class="ms-5 my-4">Edit Data Sertifikat</h2>

<form action="<?= BASE_URL ?>lo/sertifikat/proses_edit.php" method="POST" class="mx-4">

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
            $q = mysqli_query($conn, "SELECT id, nama_pelatihan FROM pelatihan ORDER BY nama_pelatihan ASC");
            while ($p = mysqli_fetch_assoc($q)) {
                $selected = ($p['id'] == $sertifikat['pelatihan_id']) ? 'selected' : '';
                echo "<option value='" . $p['id'] . "' $selected>" . $p['nama_pelatihan'] . "</option>";
            }
            ?>
        </select>
    </div>

    <div class="mb-4">
        <label class="form-label ms-3">Periode Awal:</label>
        <input type="date" name="periode_awal" value="<?= $sertifikat['periode_awal']; ?>" class="form-control" required
            onfocus="this.showPicker()">
    </div>

    <div class="mb-4">
        <label class="form-label ms-3">Periode Akhir:</label>
        <input type="date" name="periode_akhir" value="<?= $sertifikat['periode_akhir']; ?>" class="form-control"
            required onfocus="this.showPicker()">
    </div>

    <div class="mb-4">
        <label class="form-label ms-3">Template Sertifikat:</label>
        <select class="form-select form-select-sm" name="template_id" id="file_layout" required>
            <option disabled>Pilih Template Sertifikat</option>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM template");
            while ($t = mysqli_fetch_assoc($q)) {
                $selected = ($t['id'] == $sertifikat['template_id']) ? 'selected' : '';
                echo "<option value='" . $t['id'] . "' $selected>" . $t['nama_template'] . "</option>";
            }
            ?>
        </select>
    </div>

    <div class="mb-4" id="materi-section">
        <div class="d-flex">
            <div class="col-md-4">
                <label class="form-label ms-3">Materi</label>
            </div>
            <div class="col-md-4">
                <label class="form-label ms-3">Durasi</label>
            </div>
        </div>

        <div id="materi-wrapper">

            <?php if (mysqli_num_rows($qMateri) > 0): ?>
                <?php while ($m = mysqli_fetch_assoc($qMateri)): ?>
                    <div class="row materi-item mb-3">

                        <div class="col-md-4 mt-2">
                            <input type="text" name="materi[]" value="<?= $m['nama_materi'] ?>"
                                class="form-control materi-input">
                        </div>

                        <div class="col-md-4 mt-2">
                            <input type="text" name="durasi[]" value="<?= $m['durasi'] ?>" class="form-control">
                        </div>

                        <div class="col-md-2 mt-2">
                            <button type="button" class="btn btn-danger hapus">Hapus</button>
                        </div>

                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- fallback kalau belum ada -->
                <div class="row materi-item mb-3">
                    <div class="col-md-4">
                        <input type="text" name="materi[]" class="form-control materi-input">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="durasi[]" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger hapus">Hapus</button>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <button type="button" id="tambah" class="btn btn-primary mt-2">
            + Tambah Materi
        </button>
    </div>

    <div class="d-grid gap-2 d-flex justify-content-center mt-3 pb-5">
        <button type="submit" name="submit" class="btn btn-primary ms-2 col-3">Update</button>
        <a href="<?= BASE_URL ?>lo/sertifikat/index.php" style="background-color: #6C7301;"
            class="btn text-decoration-none text-white">
            Kembali Ke Halaman Sertifikat
        </a>
    </div>

</form>

<script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>vendor/autocomplete.js"></script>
</body>

</html>