<?php
include "config.php";

$cari = isset($_GET['cari']) ? $_GET['cari'] : "";

// pagination
$batas = 5;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

// query utama
if ($cari != "") {
    $query = "SELECT * FROM template WHERE nama_template LIKE '%$cari%'";
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
    <link rel="stylesheet" href="./vendor/bs.min.css">
</head>

<body>

    <div class="container mt-4">
        <div class="d-flex align-items-center gap-2">
            <!-- FORM SEARCH -->
            <form method="GET" class="col-sm-3 mb-3 ms-4 mt-4">
                <label for="cari" class="ms-3">Masukkan Kata Kunci:</label>
                <div class="d-inline-flex ms-2">
                    <input class="form-control form-control-ms" type="text" id="cari" name="cari" placeholder="Cari"
                        value="<?= htmlspecialchars($cari); ?>">
                    <button type="submit" class="btn btn-secondary ms-3">Cari</button>
                </div>
            </form>
            <a href="data_template.php" class="btn btn-secondary text-decoration-none text-white mt-4">Kembali Ke Data
                Template</a>
        </div>

        <!-- PESAN JIKA DATA TIDAK DITEMUKAN -->
        <?php if ($cari != "" && $jumlah_data == 0) { ?>
            <div class="alert alert-danger">
                Data yang dicari tidak ditemukan!
            </div>
        <?php } ?>

        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Template</th>
                        <th>Tampak Depan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($jumlah_data > 0) { ?>
                        <?php while ($template = mysqli_fetch_assoc($data_template)) { ?>
                            <tr>
                                <td><?= $nomor++; ?></td>
                                <td><?= $template['nama']; ?></td>
                                <td>
                                    <img src="uploads/template/<?= $template['tampak_depan']; ?>" width="80">
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-info text-white" href="edit_template.php?id=<?= $template['id']; ?>">Edit</a>
                                    <a class="btn btn-sm btn-danger text-white" href="hapus_template.php?id=<?= $template['id']; ?>">Hapus</a>
                                    <a class="btn btn-sm btn-secondary text-white" href="#">Preview</a>
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

    <script src="./vendor/bs.bundle.min.js"></script>
</body>

</html>