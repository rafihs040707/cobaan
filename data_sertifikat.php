<?php
include 'header_admin.php';
include 'config.php';
?>

<head>
    <title>Data Sertifikat</title>
</head>

<div class="container">
    <h2 class="my-2 ms-3">Data Sertifikat</h2>
    <form action="cari_sertifikat.php" method="GET" class="col-sm-3 mb-3 ms-4 mt-4">
        <label for="cari" class="ms-3">Masukkan Kata Kunci:</label>
        <div class="d-inline-flex ms-2">
            <input class="form-control form-control-ms" type="text" id="cari" name="cari" placeholder="Cari">
            <button type="submit" class="btn btn-secondary ms-3">Cari</button>
        </div>
    </form>
    <a href="./tambah_sertifikat.php" class="btn btn-primary btn-sm text-decoration-none text-white ms-4 mt-2 mb-4">Tambah Data Sertifikat</a>
</div>

<div class="container-fluid">

    <!-- TABEL (DESKTOP & TABLET) -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-sm table-bordered border-primary table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Pelatihan</th>
                    <th>Periode</th>
                    <th>Issued Date</th>
                    <th>Status</th>
                    <th>nomor_sertifikat</th>
                    <th>Template Yang Digunakan</th>
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
                $data = mysqli_query($conn, "select * from sertifikat");
                $jumlah_data = mysqli_num_rows($data);
                $total_halaman = ceil($jumlah_data / $batas);

                $data_sertifikat = mysqli_query($conn, "SELECT s.*, t.nama_template FROM sertifikat s JOIN template t ON s.template_id = t.id LIMIT $batas OFFSET $halaman_awal");
                $nomor = $halaman_awal + 1;
                while ($sertifikat = mysqli_fetch_array($data_sertifikat)) {
                    $awal  = strtotime($sertifikat['periode_awal']);
                    $akhir = strtotime($sertifikat['periode_akhir']);
                    $tanggal_terbit = strtotime($sertifikat['issued_date']);

                    if (date('F Y', $awal) == date('F Y', $akhir)) {
                        $periode = date('F d', $awal) . " - " . date('d, Y', $akhir);
                    } else {
                        $periode = date('F d', $awal) . " - " . date('F d, Y', $akhir);
                    }
                    $terbit = date('F d, Y', strtotime($sertifikat['issued_date']));
                ?>
                    <tr>
                        <th><?php echo $nomor++; ?>.</th>
                        <td><?php echo $sertifikat['nama']; ?></td>
                        <td><?php echo $sertifikat['pelatihan']; ?></td>
                        <td><?php echo $periode ?></td>
                        <td><?php echo $terbit ?></td>
                        <td>
                            <?php if ($sertifikat['status'] == 0) { ?>
                                <span class="badge bg-danger">Tidak Valid</span>
                            <?php } else { ?>
                                <span class="badge bg-success">Valid</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (empty($sertifikat['nomor_sertifikat'])) { ?>
                                <span class="badge bg-warning">Belum Generate</span>
                            <?php } else { ?>
                                <?= $sertifikat['nomor_sertifikat']; ?>
                            <?php } ?>
                        </td>
                        <td><?php echo $sertifikat['nama_template']; ?></td>
                        <td>
                            <a href="edit_sertifikat.php?id=<?= $sertifikat['id']; ?>" class="btn btn-sm btn-warning text-white mt-1">Edit</a>
                            <a href="hapus_sertifikat.php?id=<?= $sertifikat['id']; ?>" class="btn btn-sm btn-danger text-white mt-1" onclick="return confirm('Apakah yakin data sertifikat ini akan dihapus?');">Hapus</a>
                            <a href="generate_pdf_sertifikat.php?id=<?= $sertifikat['id']; ?>&preview=1" class="btn btn-sm btn-info text-white mt-1" target="_blank">Preview</a>
                            <a href="generate_pdf_sertifikat.php?id=<?= $sertifikat['id']; ?>" class="btn btn-sm btn-primary text-white mt-1">Generate</a>
                            <?php
                            $filePdf = "uploads/sertifikat/" . $sertifikat['file_sertifikat'];
                            if (!empty($sertifikat['file_sertifikat']) && file_exists($filePdf)) {
                                $link = "download.php?id=" . $sertifikat['id'];
                            } else {
                                $link = "generate_sertifikat.php?id=" . $sertifikat['id'];
                            }
                            ?>
                            <a href="<?= $link; ?>" class="btn btn-sm btn-success text-white mt-1">Download PDF</a>
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

<div class="container-fluid d-block d-md-none">
    <?php
    $batas = 5;
    $halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
    $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

    $data_sertifikat = mysqli_query($conn, "SELECT s.*, t.nama_template FROM sertifikat s JOIN template t ON s.template_id = t.id LIMIT $batas OFFSET $halaman_awal");

    $nomor = $halaman_awal + 1;

    while ($sertifikat = mysqli_fetch_array($data_sertifikat)) {

        $awal  = strtotime($sertifikat['periode_awal']);
        $akhir = strtotime($sertifikat['periode_akhir']);

        if (date('F Y', $awal) == date('F Y', $akhir)) {
            $periode = date('F d', $awal) . " - " . date('d, Y', $akhir);
        } else {
            $periode = date('F d', $awal) . " - " . date('F d, Y', $akhir);
        }

        $terbit = date('F-d-Y', strtotime($sertifikat['issued_date']));
    ?>
        <div class="d-block d-md-none">
            <div class="card mb-2 border-primary shadow-sm">
                <div class="card-body p-2">

                    <!-- Header -->
                    <div class="d-flex justify-content-between">
                        <div class="fw-bold">
                            <?= $nomor++; ?>. <?= $sertifikat['nama']; ?>
                        </div>
                        <?php if ($sertifikat['status'] == 0) { ?>
                            <span class="badge bg-danger">Tidak Valid</span>
                        <?php } else { ?>
                            <span class="badge bg-success">Valid</span>
                        <?php } ?>
                    </div>

                    <div class="text-muted small">
                        Pelatihan: <?= $sertifikat['pelatihan']; ?>
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
                        <a href="edit_sertifikat.php?id=<?= $sertifikat['id']; ?>" class="btn btn-sm btn-warning text-white w-100">Edit</a>
                        <a href="hapus_sertifikat.php?id=<?= $sertifikat['id']; ?>" class="btn btn-sm btn-danger text-white w-100" onclick="return confirm('Apakah yakin data sertifikat ini akan dihapus?');">Hapus</a>
                        <a href="generate_pdf_sertifikat.php?id=<?= $sertifikat['id']; ?>&preview=1" class="btn btn-sm btn-info text-white w-100" target="_blank">Preview</a>
                        <a href="generate_pdf_sertifikat.php?id=<?= $sertifikat['id']; ?>" class="btn btn-sm btn-primary text-white w-100">Generate</a>
                        <?php
                        $filePdf = "uploads/sertifikat/" . $sertifikat['file_sertifikat'];
                        if (!empty($sertifikat['file_sertifikat']) && file_exists($filePdf)) {
                            $link = "download.php?id=" . $sertifikat['id'];
                        } else {
                            $link = "generate_sertifikat.php?id=" . $sertifikat['id'];
                        }
                        ?>
                        <a href="<?= $link; ?>" class="btn btn-sm btn-success text-white w-100">Download PDF</a>
                    </div>

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





<script src="./vendor/bs.bundle.min.js"></script>
</body>

</html>