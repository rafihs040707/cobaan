<?php
$allowed_roles = ["direktur"];
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/direktur/header.php';

/* =============================
    FILTER
============================= */

$status = $_GET['status'] ?? '';
$bulan = $_GET['bulan'] ?? '';

/* =============================
    PAGINATION
============================= */

$batas = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;

if ($halaman < 1) {
    $halaman = 1;
}

$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

/* =============================
    WHERE QUERY
============================= */

$where = "WHERE 1=1";

if ($status != '') {
    $status_safe = mysqli_real_escape_string($conn, $status);
    $where .= " AND s.status='$status_safe'";
}

if ($bulan != '') {
    $start = $bulan . "-01";
    $end = date('Y-m-d', strtotime("$start +1 month"));
    $where .= " AND s.issued_date >= '$start' AND s.issued_date < '$end'";
}

/* =============================
    TOTAL DATA
============================= */

$data_total = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total
FROM sertifikat s
$where"
);

$row_total = mysqli_fetch_assoc($data_total);
$jumlah_data = $row_total['total'];

$total_halaman = ceil($jumlah_data / $batas);

$previous = $halaman - 1;
$next = $halaman + 1;

$start_info = $jumlah_data > 0 ? $halaman_awal + 1 : 0;
$end_info = min($halaman_awal + $batas, $jumlah_data);

/* =============================
    QUERY DATA
============================= */

$query = mysqli_query(
    $conn,
    "SELECT s.*, t.nama_template, t.locale, p.nama_pelatihan
FROM sertifikat s
JOIN template t ON s.template_id = t.id
LEFT JOIN pelatihan p ON s.pelatihan_id = p.id
$where
LIMIT $batas OFFSET $halaman_awal"
);

$list_sertifikat = [];

while ($row = mysqli_fetch_assoc($query)) {
    $list_sertifikat[] = $row;
}
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
            if (alertBox) { alertBox.classList.remove("show"); }
        }, 5000);
        setTimeout(() => {
            let alertBox = document.getElementById("successAlert");
            if (alertBox) { alertBox.remove(); }
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
            if (alertBox) { alertBox.classList.remove("show"); }
        }, 5000);
        setTimeout(() => {
            let alertBox = document.getElementById("errorAlert");
            if (alertBox) { alertBox.remove(); }
        }, 6000);
    </script>
    <?php unset($_SESSION['error']);
} ?>


<div class="container">
    <h2 class="my-2 ms-3">Data Sertifikat</h2>
</div>


<div class="container mb-3 mt-5">
    <div class="row">
        <div class="col-md-2">
            <label class="form-label">Filter Validasi</label>
            <select id="filterStatus" class="form-select">
                <option value="">Semua</option>
                <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>
                    Pending
                </option>
                <option value="approved" <?= $status == 'approved' ? 'selected' : '' ?>>
                    Approved
                </option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Filter Bulan</label>
            <input type="month" id="filterBulan" class="form-control" value="<?= $bulan ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label">Tampilkan</label>
            <div class="d-flex align-items-center gap-2">
                <form method="GET">
                    <input type="hidden" name="status" value="<?= $status ?>">
                    <input type="hidden" name="bulan" value="<?= $bulan ?>">
                    <select name="limit" class="form-select" onchange="this.form.submit()">
                        <option value="5" <?= $batas == 5 ? 'selected' : '' ?>>5</option>
                        <option value="10" <?= $batas == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= $batas == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $batas == 50 ? 'selected' : '' ?>>50</option>
                    </select>
                </form>
                <span class="small">
                    <?= $start_info ?>–<?= $end_info ?> dari <?= $jumlah_data ?> data
                </span>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid">
    <div id="tableContainer">

        <!-- Dekstop -->
        <div class="table-responsive d-none d-md-block">
            <form method="POST" action="bulk_approve.php">
                <button type="submit" class="btn btn-sm btn-success mb-3 ms-3">
                    Validasi
                </button>
                <table class="table table-sm table-bordered border-primary table-hover text-center align-middle">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll" class="form-check-input"></th>
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
                        $nomor = $halaman_awal + 1;
                        foreach ($list_sertifikat as $sertifikat) {

                            $locale = $sertifikat['locale'] ?? 'en';

                            $awal = strtotime($sertifikat['periode_awal']);
                            $akhir = strtotime($sertifikat['periode_akhir']);

                            $d1 = date('d', $awal);
                            $d2 = date('d', $akhir);

                            $m1 = (int) date('n', $awal);
                            $m2 = (int) date('n', $akhir);

                            $y1 = date('Y', $awal);
                            $y2 = date('Y', $akhir);

                            $bulan_id = [
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember'
                            ];

                            if ($locale === 'id') {

                                if ($m1 === $m2 && $y1 === $y2) {
                                    $periode = "{$d1} - {$d2} {$bulan_id[$m2]} {$y2}";
                                } elseif ($y1 === $y2) {
                                    $periode = "{$d1} {$bulan_id[$m1]} - {$d2} {$bulan_id[$m2]} {$y2}";
                                } else {
                                    $periode = "{$d1} {$bulan_id[$m1]} {$y1} - {$d2} {$bulan_id[$m2]} {$y2}";
                                }

                            } else {

                                if (date('F Y', $awal) == date('F Y', $akhir)) {
                                    $periode = date('F d', $awal) . " - " . date('d, Y', $akhir);
                                } elseif ($y1 === $y2) {
                                    $periode = date('F d', $awal) . " - " . date('F d, Y', $akhir);
                                } else {
                                    $periode = date('F d, Y', $awal) . " - " . date('F d, Y', $akhir);
                                }
                            }


                            // ===== ISSUED DATE =====
                            if (!empty($sertifikat['issued_date'])) {

                                $ts = strtotime($sertifikat['issued_date']);

                                if ($locale === 'id') {
                                    $d = date('d', $ts);
                                    $m = (int) date('n', $ts);
                                    $y = date('Y', $ts);

                                    $terbit = "{$d} {$bulan_id[$m]} {$y}";
                                } else {
                                    $terbit = date('F d, Y', $ts);
                                }

                            } else {
                                $terbit = '<span class="badge bg-warning text-dark">Belum divalidasi</span>';
                            }
                            ?>
                            <tr>
                                <td>
                                    <?php if ($sertifikat['status'] === 'pending'): ?>
                                        <input type="checkbox" name="ids[]" value="<?= $sertifikat['id'] ?>"
                                            class="form-check-input row-check">
                                    <?php endif; ?>
                                </td>
                                <th><?= $nomor++; ?>.</th>
                                <td><?= $sertifikat['nama']; ?></td>
                                <td><?= $sertifikat['nama_pelatihan']; ?></td>
                                <td><?= $periode ?></td>
                                <td><?= $terbit ?></td>
                                <td>
                                    <?php if (empty($sertifikat['nomor_sertifikat'])) { ?>
                                        <span class="badge bg-warning text-dark">Belum Generate</span>
                                    <?php } else { ?>
                                        <?= $sertifikat['nomor_sertifikat']; ?>
                                    <?php } ?>
                                </td>
                                <td><?= $sertifikat['nama_template']; ?></td>
                                <td>
                                    <?php if ($sertifikat['status'] == 'pending'): ?>
                                        <a href="validasi.php?id=<?= $sertifikat['id'] ?>"
                                            class="btn btn-success btn-sm">Validasi</a>
                                    <?php else: ?>
                                        <span class="badge bg-success">Sudah divalidasi</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination justify-content-end">
                        <li class="page-item <?= ($halaman <= 1 ? 'disabled' : '') ?>">
                            <a class="page-link"
                                href="?halaman=<?= $previous ?>&limit=<?= $batas ?>&status=<?= $status ?>&bulan=<?= $bulan ?>">
                                Previous
                            </a>
                        </li>
                        <?php for ($x = 1; $x <= $total_halaman; $x++): ?>
                            <li class="page-item <?= ($x == $halaman ? 'active' : '') ?>">
                                <a class="page-link"
                                    href="?halaman=<?= $x ?>&limit=<?= $batas ?>&status=<?= $status ?>&bulan=<?= $bulan ?>">
                                    <?= $x ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($halaman >= $total_halaman ? 'disabled' : '') ?>">
                            <a class="page-link"
                                href="?halaman=<?= $next ?>&limit=<?= $batas ?>&status=<?= $status ?>&bulan=<?= $bulan ?>">
                                Next
                            </a>
                        </li>
                    </ul>
                </nav>
            </form>
        </div>

        <!-- Mobile -->
        <div class="container-fluid d-block d-md-none">
            <?php
            $nomor = $halaman_awal + 1;
            foreach ($list_sertifikat as $sertifikat) {
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
                <div class="card mb-2 border-primary shadow-sm">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between">
                            <div class="fw-bold">
                                <?= $nomor++; ?>. <?= $sertifikat['nama']; ?>
                            </div>
                        </div>
                        <div class="text-muted small">
                            Pelatihan: <?= $sertifikat['nama_pelatihan']; ?>
                        </div>
                        <hr class="my-2">
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
                            <div><strong>Template Yang Digunakan:</strong>
                                <?= $sertifikat['nama_template']; ?>
                            </div>
                        </div>
                        <div class="d-flex gap-1 mt-2 flex-wrap">
                            <?php if ($sertifikat['status'] == 'pending'): ?>
                                <a href="validasi.php?id=<?= $sertifikat['id'] ?>"
                                    class="btn btn-success w-100 btn-sm">Approve</a>
                            <?php else: ?>
                                <span class="badge bg-success w-100">Sudah di-Approve</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <nav>
                <ul class="pagination justify-content-end">
                    <li class="page-item <?= ($halaman <= 1 ? 'disabled' : '') ?>">
                        <a class="page-link"
                            href="?halaman=<?= $previous ?>&limit=<?= $batas ?>&status=<?= $status ?>&bulan=<?= $bulan ?>">
                            Previous
                        </a>
                    </li>
                    <?php for ($x = 1; $x <= $total_halaman; $x++): ?>
                        <li class="page-item <?= ($x == $halaman ? 'active' : '') ?>">
                            <a class="page-link"
                                href="?halaman=<?= $x ?>&limit=<?= $batas ?>&status=<?= $status ?>&bulan=<?= $bulan ?>">
                                <?= $x ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($halaman >= $total_halaman ? 'disabled' : '') ?>">
                        <a class="page-link"
                            href="?halaman=<?= $next ?>&limit=<?= $batas ?>&status=<?= $status ?>&bulan=<?= $bulan ?>">
                            Next
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
</div>

<script>
    document.addEventListener("change", function (e) {
        if (e.target.id === "filterStatus" || e.target.id === "filterBulan") {
            const status = document.getElementById("filterStatus").value
            const bulan = document.getElementById("filterBulan").value
            window.location = `?status=${status}&bulan=${bulan}`
        }
    })
    document.addEventListener("change", function (e) {
        if (e.target.id === "checkAll") {
            document.querySelectorAll(".row-check").forEach(cb => {
                cb.checked = e.target.checked
            })
        }
        if (e.target.classList.contains("row-check")) {
            const all = document.querySelectorAll(".row-check")
            const checked = document.querySelectorAll(".row-check:checked")
            const checkAll = document.getElementById("checkAll")
            if (checkAll) {
                checkAll.checked = all.length === checked.length
            }
        }
    })
</script>

<script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>

</body>

</html>