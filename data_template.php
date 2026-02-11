<?php
include 'header_admin.php';
include 'config.php';
?>

<head>
    <title>Data Template</title>
</head>

<?php if (isset($_SESSION['success'])) { ?>
    <div id="successAlert" class="alert alert-success fade show text-center" role="alert">
        <?= $_SESSION['success']; ?>
    </div>

    <script>
        setTimeout(() => {
            let alertBox = document.getElementById("successAlert");
            if (alertBox) {
                alertBox.classList.remove("show"); // fade out
            }
        }, 3000);

        setTimeout(() => {
            let alertBox = document.getElementById("successAlert");
            if (alertBox) {
                alertBox.remove(); // hapus element setelah fade selesai
            }
        }, 4000);
    </script>
<?php unset($_SESSION['success']); } ?>

<?php if (isset($_SESSION['error'])) { ?>
    <div id="errorAlert" class="alert alert-danger fade show text-center" role="alert">
        <?= $_SESSION['error']; ?>
    </div>

    <script>
        setTimeout(() => {
            let alertBox = document.getElementById("errorAlert");
            if (alertBox) {
                alertBox.classList.remove("show");
            }
        }, 3000);

        setTimeout(() => {
            let alertBox = document.getElementById("errorAlert");
            if (alertBox) {
                alertBox.remove();
            }
        }, 4000);
    </script>
<?php unset($_SESSION['error']); } ?>

<div class="container">
    <h2 class="my-2 ms-3">Data Template</h2>
    <form action="cari_template.php" method="GET" class="col-sm-3 mb-3 ms-4 mt-4">
        <label for="cari" class="ms-3">Masukkan Kata Kunci:</label>
        <div class="d-inline-flex ms-2">
            <input class="form-control form-control-ms" type="text" id="cari" name="cari" placeholder="Cari"
                required>
            <button type="submit" class="btn btn-secondary ms-3">Cari</button>
        </div>
    </form>
    <a href="./tambah_template.php"
        class="btn btn-primary btn-sm text-decoration-none text-white ms-4 mt-2 mb-4">Tambah Data Template</a>
</div>

<div class="container">
    <div class="table-responsive">
        <table class="table table-sm table-bordered border-primary table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Template</th>
                    <th>Penyelenggara</th>
                    <th>Tampak Depan</th>
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

                $data = mysqli_query($conn, "select * from template");
                $jumlah_data = mysqli_num_rows($data);
                $total_halaman = ceil($jumlah_data / $batas);

                $data_template = mysqli_query($conn, "select * from template limit $batas OFFSET $halaman_awal");
                $nomor = $halaman_awal + 1;
                while ($template = mysqli_fetch_array($data_template)) {
                ?>
                    <tr>
                        <th><?php echo $nomor++; ?></th>
                        <td><?php echo $template['nama_template']; ?></td>
                        <td><?php echo $template['penyelenggara']; ?></td>
                        <td><img src="uploads/template/<?php echo $template['tampak_depan']; ?>" width="80"></td>
                        <td>
                            <a href="edit_template.php?id=<?= $template['id']; ?>" class="btn btn-sm btn-info text-black mt-2">Edit</a>
                            <a href="hapus_template.php?id=<?= $template['id']; ?>" class="btn btn-sm btn-danger text-white mt-2" onclick="return confirm('Apakah yakin data template ini akan dihapus?');">Hapus</a>
                            <button class="btn btn-sm btn-secondary text-white mt-2" data-bs-toggle="modal" data-bs-target="#modalGambar<?= $template['id']; ?>">Preview</button>
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
                                    <p class="fs-5"><strong>Nama Template:</strong> <?= $template['nama_template']; ?></p>
                                    <p class="fs-5"><strong>Penyelenggara:</strong> <?= $template['penyelenggara']; ?></p>

                                    <div class="row">
                                        <div class="col-md-6 text-center">
                                            <p><strong>Tampak Depan</strong></p>
                                            <img src="uploads/template/<?= $template['tampak_depan']; ?>"
                                                class="img-fluid rounded border"
                                                alt="Tampak Depan">
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
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






<script src="./vendor/bs.bundle.min.js"></script>
</body>

</html>