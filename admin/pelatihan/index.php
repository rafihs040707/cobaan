<?php
$allowed_roles = ["admin"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/admin/header.php';
require_once BASE_PATH . '/config/config.php';
?>

<head>
    <title>Data Pelatihan</title>
</head>

<?php if (isset($_SESSION['success'])) { ?>
    <div id="successAlert" class="alert alert-success fade show d-flex position-absolute w-100" role="alert">
        <?= $_SESSION['success']; ?>
    </div>

    <script>
        setTimeout(() => {
            let alertBox = document.getElementById("successAlert");
            if (alertBox) {
                alertBox.classList.remove("show"); // fade out
            }
        }, 5000);

        setTimeout(() => {
            let alertBox = document.getElementById("successAlert");
            if (alertBox) {
                alertBox.remove(); // hapus element setelah fade selesai
            }
        }, 6000);
    </script>
<?php unset($_SESSION['success']); } ?>

<?php if (isset($_SESSION['error'])) { ?>
    <div id="errorAlert" class="alert alert-danger fade show d-flex position-absolute w-100" role="alert">
        <?= $_SESSION['error']; ?>
    </div>

    <script>
        setTimeout(() => {
            let alertBox = document.getElementById("errorAlert");
            if (alertBox) {
                alertBox.classList.remove("show");
            }
        }, 5000);

        setTimeout(() => {
            let alertBox = document.getElementById("errorAlert");
            if (alertBox) {
                alertBox.remove();
            }
        }, 6000);
    </script>
<?php unset($_SESSION['error']); } ?>

<div class="container">
    <h2 class="my-2 ms-3">Data Pelatihan</h2>
    <form action="<?= BASE_URL ?>admin/pelatihan/cari.php" method="GET" class="col-sm-3 mb-3 ms-4 mt-4">
        <label for="cari" class="ms-3">Masukkan Kata Kunci:</label>
        <div class="d-inline-flex ms-2">
            <input class="form-control form-control-ms" type="text" id="cari" name="cari" placeholder="Cari"
                required>
            <button type="submit" class="btn btn-secondary ms-3">Cari</button>
        </div>
    </form>
    <a href="<?= BASE_URL ?>admin/pelatihan/tambah.php"
        class="btn btn-primary btn-sm text-decoration-none text-white ms-4 mt-2 mb-4">Tambah Data Pelatihan</a>
</div>

<div class="container">
    <div class="table-responsive">
        <table class="table table-sm table-bordered border-primary table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelatihan</th>
                    <th>Deskripsi</th>
                    <th>status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $batas = 5;
                $halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
                $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

                $previous = $halaman - 1;
                $next = $halaman + 1;

                $data = mysqli_query($conn, "select * from pelatihan");
                $jumlah_data = mysqli_num_rows($data);
                $total_halaman = ceil($jumlah_data / $batas);

                $data_pelatihan = mysqli_query($conn, "select * from pelatihan limit $batas OFFSET $halaman_awal");
                $nomor = $halaman_awal + 1;
                while ($pelatihan = mysqli_fetch_array($data_pelatihan)) {
                ?>
                    <tr>
                        <th><?php echo $nomor++; ?></th>
                        <td><?php echo $pelatihan['nama_pelatihan']; ?></td>
                        <td><?php echo $pelatihan['deskripsi']; ?></td>
                        <td>
                            <?php if ($pelatihan['status'] == 0) { ?>
                                <span class="badge bg-danger">Nonaktif</span>
                            <?php } else { ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php } ?>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>admin/pelatihan/edit.php?id=<?= $pelatihan['id']; ?>" class="btn btn-sm btn-info text-black mt-2">Edit</a>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <nav>
            <ul class="pagination justify-content-end">
                <li class="page-item">
                    <a class="page-link" <?php if ($halaman > 1) {
                                                echo "href='?halaman=$previous'";
                                            } ?>>Previous</a>
                </li>
                <?php
                for ($x = 1; $x <= $total_halaman; $x++) {
                ?>
                    <li class="page-item"><a class="page-link" href="?halaman=<?php echo $x ?>"><?php echo $x; ?></a></li>
                <?php
                }
                ?>
                <li class="page-item">
                    <a class="page-link" <?php if ($halaman < $total_halaman) {
                                                echo "href='?halaman=$next'";
                                            } ?>>Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>






<script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
</body>

</html>