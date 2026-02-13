<?php
$allowed_roles = ["admin"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/config/config.php';

$cari = isset($_GET['cari']) ? $_GET['cari'] : "";

// pagination
$batas = 5;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

// query utama
if ($cari != "") {
    $query = "SELECT * FROM pelatihan WHERE nama_pelatihan LIKE '%$cari%' OR LOWER(deskripsi) LIKE '%$cari%'";
} else {
    $query = "SELECT * FROM pelatihan";
}

// hitung jumlah data
$data_all = mysqli_query($conn, $query);
$jumlah_data = mysqli_num_rows($data_all);
$total_halaman = ceil($jumlah_data / $batas);

// query data + pagination
$query_limit = $query . " LIMIT $batas OFFSET $halaman_awal";
$data_pelatihan = mysqli_query($conn, $query_limit);

$nomor = $halaman_awal + 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Data Pelatihan</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>vendor/bs.min.css">
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>image/logo.png">
</head>

<body>

    <div class="container mt-4">
        <!-- FORM SEARCH -->
        <form method="GET" action="<?= BASE_URL ?>admin/pelatihan/cari.php" class="col-sm-4 mb-3 ms-4 mt-4">
            <label for="cari" class="ms-3">Masukkan Kata Kunci:</label>
            <div class="d-inline-flex ms-2 w-100">
                <input class="form-control form-control-ms" type="text" id="cari" name="cari" placeholder="Cari"
                    value="<?= htmlspecialchars($cari); ?>">
                <button type="submit" class="btn btn-secondary ms-3">Cari</button>
            </div>
            <a href="<?= BASE_URL ?>admin/pelatihan/index.php" class="btn btn-secondary text-decoration-none text-white mt-4 ms-3 mb-2">
                Kembali Ke Data pelatihan
            </a>
        </form>

        <!-- PESAN JIKA DATA TIDAK DITEMUKAN -->
        <?php if ($cari != "" && $jumlah_data == 0) { ?>
            <div class="alert alert-danger">
                Data yang dicari tidak ditemukan!
            </div>
        <?php } ?>

        <div class="table-responsive">
            <table class="table table-sm table-bordered border-primary table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelatihan</th>
                        <th>deskripsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($jumlah_data > 0) { ?>
                        <?php while ($pelatihan = mysqli_fetch_assoc($data_pelatihan)) { ?>
                            <tr>
                                <td><?= $nomor++; ?></td>
                                <td><?= $pelatihan['nama_pelatihan']; ?></td>
                                <td><?= $pelatihan['deskripsi']; ?></td>
                                <td>
                                    <?php if ($pelatihan['status'] == 0) { ?>
                                        <span class="badge bg-danger">Tidak Valid</span>
                                    <?php } else { ?>
                                        <span class="badge bg-success">Valid</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-info text-black" href="<?= BASE_URL ?>admin/pelatihan/edit.php?id=<?= $pelatihan['id']; ?>">Edit</a>
                                </td>
                            </tr>
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

    <script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
</body>

</html>