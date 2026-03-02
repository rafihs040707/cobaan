<?php
$allowed_roles = ["admin"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/admin/header.php';
require_once BASE_PATH . '/config/config.php';

$cari = isset($_GET['cari']) ? $_GET['cari'] : "";

// pagination
$batas = 5;
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

// query utama
if ($cari != "") {
    $query = "SELECT * FROM template WHERE nama_template LIKE '%$cari%' OR LOWER(penyelenggara) LIKE '%$cari%'";
} else {
    $query = "SELECT * FROM template";
}

// hitung jumlah data
$data_all = mysqli_query($conn, $query);
$jumlah_data = mysqli_num_rows($data_all);
$total_halaman = ceil($jumlah_data / $batas);

// query data + pagination
$query_limit = $query . " LIMIT $batas OFFSET $halaman_awal";
$data_template = mysqli_query($conn, $query_limit);

$nomor = $halaman_awal + 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Data Template</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>vendor/bs.min.css">
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>image/logo.png">
</head>

<div>

    <div class="container mt-4">
        <h2 class="my-2 ms-3">Data Template</h2>
        <!-- FORM SEARCH -->
        <form method="GET" action="<?= BASE_URL ?>admin/template/cari.php" class="col-sm-4 mb-3 ms-4 mt-4">
            <label for="cari" class="ms-3">Masukkan Kata Kunci:</label>
            <div class="d-inline-flex ms-2 w-100">
                <input class="form-control form-control-ms" type="text" id="cari" name="cari" placeholder="Cari"
                    value="<?= htmlspecialchars($cari); ?>">
                <button type="submit" class="btn btn-secondary ms-3">Cari</button>
            </div>
            <a href="<?= BASE_URL ?>admin/template/index.php"
                class="btn btn-sm btn-primary text-decoration-none text-white mt-4 ms-3 mb-2">
                Kembali Ke Data Template
            </a>
        </form>

        <!-- PESAN JIKA DATA TIDAK DITEMUKAN -->
        <?php if ($cari != "" && $jumlah_data == 0) { ?>
            <div class="alert alert-info">
                Tidak ada data yang sesuai dengan pencarian.
            </div>
        <?php } ?>

        <div class="table-responsive">
            <table class="table table-sm table-bordered border-primary table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Template</th>
                        <th>Penyelenggara</th>
                        <th>Tampak Depan</th>
                        <th>Tampak Belakang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($jumlah_data > 0) { ?>
                        <?php while ($template = mysqli_fetch_assoc($data_template)) { ?>
                            <tr>
                                <td><?= $nomor++; ?></td>
                                <td><?= $template['nama_template']; ?></td>
                                <td><?= $template['penyelenggara']; ?></td>
                                <td>
                                    <img src="<?= BASE_URL ?>uploads/template/<?= $template['tampak_depan']; ?>" width="80">
                                </td>
                                <td>
                                    <?php if (empty($template['tampak_belakang'])) { ?>
                                        <span class="badge bg-secondary">Tidak ada Tampak Belakang</span>
                                    <?php } else { ?>
                                        <img src="uploads/template/<?php echo $template['tampak_belakang']; ?>" width="80">
                                    <?php } ?>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-info text-black"
                                        href="<?= BASE_URL ?>admin/template/edit.php?id=<?= $template['id']; ?>">Edit</a>
                                    <button class="btn btn-sm btn-secondary text-white" data-bs-toggle="modal"
                                        data-bs-target="#modalGambar<?= $template['id']; ?>">Preview</button>
                                    <a class="btn btn-sm btn-danger text-white"
                                        href="<?= BASE_URL ?>admin/template/hapus.php?id=<?= $template['id']; ?>"
                                        onclick="return confirm('Apakah yakin data template ini akan dihapus?');">Hapus</a>
                                </td>
                            </tr>
                            <div class="modal fade" id="modalGambar<?= $template['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detail Template</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <p class="fs-5"><strong>Nama Template:</strong> <?= $template['nama_template']; ?>
                                            </p>
                                            <p class="fs-5"><strong>Penyelenggara:</strong> <?= $template['penyelenggara']; ?>
                                            </p>
                                            <p class="fs-5"><strong>Instruktur:</strong> <?= $template['instruktur']; ?></p>

                                            <div class="row">
                                                <div class="col-md-6 text-center">
                                                    <p><strong>Tampak Depan</strong></p>
                                                    <img src="<?= BASE_URL ?>uploads/template/<?= $template['tampak_depan']; ?>"
                                                        class="img-fluid rounded border" alt="Tampak Depan">
                                                </div>
                                                <div class="col-md-6 text-center">
                                                    <p><strong>Tampak Belakang</strong></p>
                                                    <img src="<?= BASE_URL ?>uploads/template/<?= $template['tampak_belakang']; ?>"
                                                        class="img-fluid rounded border" alt="Tampak Belakang">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5">Tidak ada data</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <?php if ($jumlah_data > $batas) { ?>
            <nav>
                <ul class="pagination justify-content-end">
                    <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?halaman=<?= $halaman - 1; ?>&cari=<?= $cari; ?>">Previous</a>
                    </li>

                    <?php for ($x = 1; $x <= $total_halaman; $x++) { ?>
                        <li class="page-item <?= ($halaman == $x) ? 'active' : ''; ?>">
                            <a class="page-link" href="?halaman=<?= $x; ?>&cari=<?= $cari; ?>"><?= $x; ?></a>
                        </li>
                    <?php } ?>

                    <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?halaman=<?= $halaman + 1; ?>&cari=<?= $cari; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php } ?>

    </div>
</div>
</div>
</div>
<script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
<script src="<?= BASE_URL ?>vendor/sidebar.js"></script>
</body>

</html>