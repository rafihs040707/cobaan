<?php
$allowed_roles = ["direktur"];
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/direktur/header.php';
?>

<head>
    <title>Data Sertifikat</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>vendor/bs.min.css">
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
    <?php unset($_SESSION['success']);
} ?>

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
    <?php unset($_SESSION['error']);
} ?>


<div class="container">
    <h2 class="my-2 ms-3">Data Sertifikat</h2>
    <form action="<?= BASE_URL ?>admin/sertifikat/cari.php" method="GET" class="col-sm-3 mb-3 ms-4 mt-4">
        <label for="cari" class="ms-3">Masukkan Kata Kunci:</label>
        <div class="d-inline-flex ms-2">
            <input class="form-control form-control-ms" type="text" id="cari" name="cari" placeholder="Cari" required>
            <button type="submit" class="btn btn-secondary ms-3">Cari</button>
        </div>
    </form>
</div>

<div class="container mb-3">
    <div class="row">
        <div class="col-md-2">
            <label class="form-label">Filter Validasi</label>
            <select id="filterStatus" class="form-select">
                <option value="">Semua</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Filter Bulan</label>
            <input type="month" id="filterBulan" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Tampilkan</label>
            <form method="GET" id="limitForm">
                <select name="limit" class="form-select" onchange="document.getElementById('limitForm').submit()">
                    <?php $limit = $_GET['limit'] ?? 5; ?>
                    <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5</option>
                    <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                </select>
            </form>
        </div>
    </div>
</div>

<div class="container-fluid">

    <!-- TABEL (DESKTOP & TABLET) -->
    <div id="tableContainer">
        <div class="table-responsive d-none d-md-block">
            <form method="POST" action="bulk_approve.php">
                <button type="submit" class="btn btn-sm btn-success mb-3 ms-3">
                    Validasi
                </button>
                <table class="table table-sm table-bordered border-primary table-hover text-center align-middle">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="checkAll" class="form-check-input">
                            </th>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Pelatihan</th>
                            <th>Periode</th>
                            <th>Issued Date</th>
                            <th>nomor sertifikat</th>
                            <th>Template Used</th>
                            <th>Validasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $batas = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
                        $halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
                        $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

                        $previous = $halaman - 1;
                        $next = $halaman + 1;
                        $data = mysqli_query($conn, "select * from sertifikat");
                        $jumlah_data = mysqli_num_rows($data);
                        $total_halaman = ceil($jumlah_data / $batas);

                        $data_sertifikat = mysqli_query($conn, "SELECT s.*, t.nama_template, p.nama_pelatihan FROM sertifikat s JOIN template t ON s.template_id = t.id LEFT JOIN pelatihan p ON s.pelatihan_id = p.id LIMIT $batas OFFSET $halaman_awal");
                        $nomor = $halaman_awal + 1;
                        while ($sertifikat = mysqli_fetch_array($data_sertifikat)) {
                            $awal = strtotime($sertifikat['periode_awal']);
                            $akhir = strtotime($sertifikat['periode_akhir']);

                            if (date('F Y', $awal) == date('F Y', $akhir)) {
                                $periode = date('F d', $awal) . " - " . date('d, Y', $akhir);
                            } else {
                                $periode = date('F d', $awal) . " - " . date('F d, Y', $akhir);
                            }
                            $terbit = !empty($sertifikat['issued_date'])
                                ? date('F d, Y', strtotime($sertifikat['issued_date']))
                                : '-';
                            ?>
                            <tr>
                                <td>
                                    <?php if ($sertifikat['status'] === 'pending'): ?>
                                        <!-- Checkbox hanya muncul jika pending -->
                                        <input type="checkbox" name="ids[]" value="<?= $sertifikat['id'] ?>"
                                            class="form-check-input row-check">
                                    <?php endif; ?>
                                </td>
                                <th><?php echo $nomor++; ?>.</th>
                                <td><?php echo $sertifikat['nama']; ?></td>
                                <td><?php echo $sertifikat['nama_pelatihan']; ?></td>
                                <td><?php echo $periode ?></td>
                                <td><?php echo $terbit ?></td>
                                <td>
                                    <?php if (empty($sertifikat['nomor_sertifikat'])) { ?>
                                        <span class="badge bg-warning">Belum Generate</span>
                                    <?php } else { ?>
                                        <?= $sertifikat['nomor_sertifikat']; ?>
                                    <?php } ?>
                                </td>
                                <td><?php echo $sertifikat['nama_template']; ?></td>
                                <td>
                                    <?php if ($sertifikat['status'] == 'pending'): ?>
                                        <a href="validasi.php?id=<?= $sertifikat['id'] ?>" class="btn btn-success">Approve</a>
                                    <?php else: ?>
                                        <span class="badge bg-success">Sudah di-Approve</span>
                                    <?php endif; ?>
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
                                echo "href=href='?halaman=$previous&limit=$batas'";
                            } ?>>Previous</a>
                        </li>
                        <?php
                        for ($x = 1; $x <= $total_halaman; $x++) {
                            ?>
                            <li class="page-item"><a class="page-link"
                                    href="?halaman=<?php echo $x ?>&limit=<?php echo $batas ?>"><?php echo $x; ?></a>
                            </li>
                            <?php
                        }
                        ?>
                        <li class="page-item">
                            <a class="page-link" <?php if ($halaman < $total_halaman) {
                                echo "href='?halaman=$next&limit=$batas'";
                            } ?>>Next</a>
                        </li>
                    </ul>
                </nav>
            </form>
        </div>
    </div>
</div>


<div class="container-fluid d-block d-md-none">
    <?php
    $batas = 5;
    $halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
    $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

    $data_sertifikat = mysqli_query($conn, "SELECT s.*, t.nama_template, p.nama_pelatihan FROM sertifikat s JOIN template t ON s.template_id = t.id LEFT JOIN pelatihan p ON s.pelatihan_id = p.id LIMIT $batas OFFSET $halaman_awal");
    $nomor = $halaman_awal + 1;

    while ($sertifikat = mysqli_fetch_array($data_sertifikat)) {

        $awal = strtotime($sertifikat['periode_awal']);
        $akhir = strtotime($sertifikat['periode_akhir']);

        if (date('F Y', $awal) == date('F Y', $akhir)) {
            $periode = date('F d', $awal) . " - " . date('d, Y', $akhir);
        } else {
            $periode = date('F d', $awal) . " - " . date('F d, Y', $akhir);
        }

        $terbit = !empty($sertifikat['issued_date'])
            ? date('F d, Y', strtotime($sertifikat['issued_date']))
            : '-';
        ?>
        <div class="d-block d-md-none">
            <div class="card mb-2 border-primary shadow-sm">
                <div class="card-body p-2">

                    <!-- Header -->
                    <div class="d-flex justify-content-between">
                        <div class="fw-bold">
                            <?= $nomor++; ?>. <?= $sertifikat['nama']; ?>
                        </div>
                    </div>

                    <div class="text-muted small">
                        Pelatihan: <?= $sertifikat['nama_pelatihan']; ?>
                    </div>
                    <hr class="my-2">

                    <!-- Detail -->
                    <div class="small">
                        <div><strong>Periode:</strong> <?= $periode; ?></div>
                        <div><strong>Issued Date:</strong> <?= $terbit; ?></div>
                        <div><strong>No Sertifikat:</strong>
                            <?php if (empty($sertifikat['nomor_sertifikat'])) { ?>
                                <span class="badge bg-warning">Belum Generate</span>
                            <?php } else { ?>
                                <?= $sertifikat['nomor_sertifikat']; ?>
                            <?php } ?>
                        </div>
                        <div><strong>Template Yang Digunakan:</strong> <?= $sertifikat['nama_template']; ?></div>
                    </div>

                    <!-- Action -->
                    <div class="d-flex gap-1 mt-2 flex-wrap">
                        <td>
                            <?php if ($sertifikat['status'] == 'pending'): ?>
                                <a href="validasi.php?id=<?= $sertifikat['id'] ?>" class="btn btn-success w-100">Approve</a>
                            <?php else: ?>
                                <span class="badge bg-success w-100">Sudah di-Approve</span>
                            <?php endif; ?>
                        </td>

                    </div>
                </div>
            </div>
        <?php } ?>
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
</div>
</div>

<script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
<script src="<?= BASE_URL ?>vendor/sidebar.js"></script>
<script src="<?= BASE_URL ?>direktur/ajax/ajax.js"></script>

</body>

</html>