<?php
$allowed_roles = ["lo"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/lo/header.php';

$cari = isset($_GET['cari']) ? trim($_GET['cari']) : "";

/* =========================
    PAGINATION
========================= */
$batas = 5;
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
if ($halaman < 1)
    $halaman = 1;

$halaman_awal = ($halaman - 1) * $batas;

$where = "";

if (!empty($cari)) {
    $cari_safe = mysqli_real_escape_string($conn, strtolower($cari));
    $where = "WHERE 
        LOWER(s.nama) LIKE '%$cari_safe%' OR
        LOWER(s.nomor_sertifikat) LIKE '%$cari_safe%' OR
        LOWER(p.nama_pelatihan) LIKE '%$cari_safe%'";
}

/* =========================
    COUNT TOTAL DATA
========================= */
$query_count = "SELECT COUNT(*) as total
                FROM sertifikat s
                JOIN template t ON s.template_id = t.id
                LEFT JOIN pelatihan p ON s.pelatihan_id = p.id
                $where";

$result_count = mysqli_query($conn, $query_count);
$row_count = mysqli_fetch_assoc($result_count);

$jumlah_data = $row_count['total'] ?? 0;
$total_halaman = ceil($jumlah_data / $batas);

/* =========================
    AMBIL DATA + LIMIT
========================= */
$query_data = "SELECT 
                    s.*, 
                    t.nama_template,
                    p.nama_pelatihan
                FROM sertifikat s
                JOIN template t ON s.template_id = t.id
                LEFT JOIN pelatihan p ON s.pelatihan_id = p.id
                $where
                ORDER BY s.id DESC
                LIMIT $batas OFFSET $halaman_awal";

$data_sertifikat = mysqli_query($conn, $query_data);

$nomor = $halaman_awal + 1;
?>



<head>
    <title>Pencarian Sertifikat</title>
</head>

<div class="container">
    <h2 class="my-2 ms-3">Data Sertifikat</h2>
    <!-- FORM SEARCH -->
    <form action="<?= BASE_URL ?>lo/sertifikat/cari.php" method="GET" class="col-sm-3 mb-3 ms-4 mt-4">
        <label for="cari" class="ms-3">Masukkan Kata Kunci:</label>
        <div class="d-inline-flex ms-2">
            <input class="form-control form-control-ms" type="text" id="cari" name="cari" placeholder="Cari"
                value="<?= htmlspecialchars($cari); ?>" required autocomplete="off">
            <button type="submit" class="btn btn-secondary ms-3">Cari</button>
        </div>
    </form>
    <a href="<?= BASE_URL ?>lo/sertifikat/index.php"
        class="btn btn-primary btn-sm text-decoration-none text-white ms-4 mt-2 mb-4">
        Kembali Ke Data Sertifikat
    </a>
</div>

<div class="container-fluid">
    <!-- PESAN JIKA DATA TIDAK DITEMUKAN -->
    <?php if ($cari != "" && $jumlah_data == 0) { ?>
        <div class="alert alert-info">
            Tidak ada data yang sesuai dengan pencarian.
        </div>
    <?php } ?>

    <!-- ======================= -->
    <!-- TABEL (DESKTOP & TABLET) -->
    <!-- ======================= -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-sm table-bordered border-primary table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Pelatihan</th>
                    <th>Periode</th>
                    <th>Issued Date</th>
                    <th>No Sertifikat</th>
                    <th>Template Yang Digunakan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($jumlah_data > 0) { ?>
                    <?php while ($sertifikat = mysqli_fetch_array($data_sertifikat)) {

                        $awal = strtotime($sertifikat['periode_awal']);
                        $akhir = strtotime($sertifikat['periode_akhir']);

                        if (date('F Y', $awal) == date('F Y', $akhir)) {
                            $periode = date('F d', $awal) . " - " . date('d, Y', $akhir);
                        } else {
                            $periode = date('F d', $awal) . " - " . date('F d, Y', $akhir);
                        }

                        $terbit = !empty($sertifikat['issued_date'])
                            ? date('F d, Y', strtotime($sertifikat['issued_date']))
                            : '<span class="badge bg-warning text-dark">Belum divalidasi</span>';
                        ?>
                        <tr>
                            <th><?= $nomor++; ?>.</th>
                            <td><?= $sertifikat['nama']; ?></td>
                            <td><?= $sertifikat['nama_pelatihan']; ?></td>
                            <td><?= $periode ?></td>
                            <td><?= $terbit ?></td>

                            <td>
                                <?php if (empty($sertifikat['nomor_sertifikat'])) { ?>
                                    <span class="badge bg-warning">Belum Generate</span>
                                <?php } else { ?>
                                    <?= $sertifikat['nomor_sertifikat']; ?>
                                <?php } ?>
                            </td>

                            <td><?= $sertifikat['nama_template']; ?></td>

                            <td style="white-space: nowrap; min-width: 250px;">
                                <div class="d-flex flex-nowrap gap-1">
                                    <!-- Edit -->
                                    <div>
                                        <a href="<?= BASE_URL ?>lo/sertifikat/edit.php?id=<?= $sertifikat['id']; ?>"
                                            class="btn btn-sm btn-warning text-black">Edit</a>
                                    </div>
                                    <!-- Preview -->
                                    <div>
                                        <a href="<?= BASE_URL ?>pdf/preview.php?id=<?= $sertifikat['id']; ?>"
                                            class="btn btn-sm btn-info text-black" target="_blank">Preview</a>
                                    </div>
                                    <!-- Generate -->
                                    <div style="min-width: 110px;">
                                        <?php if ($sertifikat['status'] === 'approved'): ?>
                                            <a href="<?= BASE_URL ?>pdf/generate.php?id=<?= $sertifikat['id']; ?>"
                                                class="btn btn-sm btn-primary w-100 text-white">Generate</a>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Belum divalidasi</span>
                                        <?php endif; ?>
                                    </div>
                                    <!-- Download -->
                                    <div style="min-width: 90px;">
                                        <?php if (!empty($sertifikat['file_sertifikat'])): ?>
                                            <a href="<?= BASE_URL ?>pdf/download.php?id=<?= $sertifikat['id'] ?>"
                                                class="btn btn-success btn-sm w-100">Download</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="9">Tidak ada data</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- ======================= -->
        <!-- PAGINATION -->
        <!-- ======================= -->
        <?php if ($jumlah_data > $batas) { ?>
            <nav>
                <ul class="pagination justify-content-end">
                    <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?halaman=<?= $halaman - 1; ?>&cari=<?= urlencode($cari); ?>">Previous</a>
                    </li>

                    <?php for ($x = 1; $x <= $total_halaman; $x++) { ?>
                        <li class="page-item <?= ($halaman == $x) ? 'active' : ''; ?>">
                            <a class="page-link" href="?halaman=<?= $x; ?>&cari=<?= urlencode($cari); ?>"><?= $x; ?></a>
                        </li>
                    <?php } ?>

                    <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?halaman=<?= $halaman + 1; ?>&cari=<?= urlencode($cari); ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php } ?>
    </div>

    <!-- ======================= -->
    <!-- CARD (MOBILE) -->
    <!-- ======================= -->
    <div class="container mt-3 d-block d-md-none">
        <?php
        // Query ulang khusus mobile (karena data desktop sudah habis dipakai while)
        $data_mobile = mysqli_query($conn, $query_data);
        $nomor_mobile = $halaman_awal + 1;

        while ($sertifikat = mysqli_fetch_array($data_mobile)) {

            $awal = strtotime($sertifikat['periode_awal']);
            $akhir = strtotime($sertifikat['periode_akhir']);

            if (date('F Y', $awal) == date('F Y', $akhir)) {
                $periode = date('F d', $awal) . " - " . date('d, Y', $akhir);
            } else {
                $periode = date('F d', $awal) . " - " . date('F d, Y', $akhir);
            }

            $terbit = !empty($sertifikat['issued_date'])
                ? date('F d, Y', strtotime($sertifikat['issued_date']))
                : '<span class="badge bg-warning text-dark">Belum divalidasi</span>';
            ?>
            <div class="d-block d-md-none">
                <div class="card mb-2 border-primary shadow-sm">
                    <div class="card-body p-2">

                        <!-- Header -->
                        <div class="d-flex justify-content-between">
                            <div class="fw-bold">
                                <?= $nomor_mobile++; ?>. <?= $sertifikat['nama']; ?>
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
                                    <span class="badge bg-warning text-dark">Belum Generate</span>
                                <?php } else { ?>
                                    <?= $sertifikat['nomor_sertifikat']; ?>
                                <?php } ?>
                            </div>
                            <div><strong>Template Yang Digunakan:</strong> <?= $sertifikat['nama_template']; ?></div>
                        </div>

                        <!-- Action -->
                        <div class="d-flex gap-1 mt-2 flex-wrap">
                            <a href="<?= BASE_URL ?>lo/sertifikat/edit.php?id=<?= $sertifikat['id']; ?>"
                                class="btn btn-sm btn-warning text-white w-100">Edit</a>

                            <a href="<?= BASE_URL ?>pdf/generate_pdf_sertifikat.php?id=<?= $sertifikat['id']; ?>&preview=1"
                                class="btn btn-sm btn-info text-white w-100" target="_blank">Preview</a>

                            <a href="<?= BASE_URL ?>pdf/generate_pdf_sertifikat.php?id=<?= $sertifikat['id']; ?>"
                                class="btn btn-sm btn-primary text-white w-100">Generate</a>

                            <?php
                            $filePdf = BASE_PATH . "uploads/sertifikat/" . $sertifikat['file_sertifikat'];
                            if (!empty($sertifikat['file_sertifikat']) && file_exists($filePdf)) {
                                $link = "<?= BASE_URL ?>pdf/download.php?id=" . $sertifikat['id'];
                            } else {
                                $link = "<?= BASE_URL ?>pdf/generate_pdf_sertifikat.php?id=" . $sertifikat['id'];
                            }
                            ?>

                            <a href="<?= $link; ?>" class="btn btn-sm btn-success text-white w-100">Download PDF</a>
                        </div>

                    </div>
                </div>
            </div>
        <?php } ?>
        <!-- ======================= -->
        <!-- PAGINATION -->
        <!-- ======================= -->
        <?php if ($jumlah_data > $batas) { ?>
            <nav>
                <ul class="pagination justify-content-end">
                    <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?halaman=<?= $halaman - 1; ?>&cari=<?= urlencode($cari); ?>">Previous</a>
                    </li>

                    <?php for ($x = 1; $x <= $total_halaman; $x++) { ?>
                        <li class="page-item <?= ($halaman == $x) ? 'active' : ''; ?>">
                            <a class="page-link" href="?halaman=<?= $x; ?>&cari=<?= urlencode($cari); ?>"><?= $x; ?></a>
                        </li>
                    <?php } ?>

                    <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?halaman=<?= $halaman + 1; ?>&cari=<?= urlencode($cari); ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php } ?>
    </div>
</div>

<script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
</body>

</html>