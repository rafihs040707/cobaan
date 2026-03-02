<?php
$allowed_roles = ["admin"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/admin/header.php';
require_once BASE_PATH . '/config/config.php';

$cari = strtolower(trim($_GET['cari'] ?? ''));

// ======================
// PAGINATION
// ======================
$batas = 5;
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

// ======================
// BUILD WHERE DINAMIS
// ======================
$where = "";
$params = [];
$types = "";

if ($cari !== "") {
    $where = "WHERE nama LIKE ? OR LOWER(email) LIKE ? OR LOWER(role) LIKE ?";
    $like = "%$cari%";
    $params = [$like, $like, $like];
    $types .= "sss";
}


// ======================
// HITUNG TOTAL DATA
// ======================
$sql_count = "SELECT COUNT(*) as total FROM users $where";
$stmt_count = mysqli_prepare($conn, $sql_count);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt_count, $types, ...$params);
}

mysqli_stmt_execute($stmt_count);
$result_count = mysqli_stmt_get_result($stmt_count);
$row_count = mysqli_fetch_assoc($result_count);

$jumlah_data = $row_count['total'];
$total_halaman = ceil($jumlah_data / $batas);

// ======================
// QUERY DATA + PAGINATION
// ======================
$sql_data = "SELECT * FROM users $where LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql_data);

// gabungkan parameter
$params_data = $params;
$params_data[] = $batas;
$params_data[] = $halaman_awal;

$types_data = $types . "ii";

mysqli_stmt_bind_param($stmt, $types_data, ...$params_data);
mysqli_stmt_execute($stmt);
$data_user = mysqli_stmt_get_result($stmt);

$nomor = $halaman_awal + 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Data user</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>vendor/bs.min.css">
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>image/logo.png">
</head>

<div>

    <div class="container mt-4">
        <h2 class="my-2 ms-3">Data User</h2>
        <!-- FORM SEARCH -->
        <form method="GET" action="<?= BASE_URL ?>admin/user/cari.php" class="col-sm-4 mb-3 ms-4 mt-4">
            <label for="cari" class="ms-3">Masukkan Kata Kunci:</label>
            <div class="d-inline-flex ms-2 w-100">
                <input class="form-control form-control-ms" type="text" id="cari" name="cari" placeholder="Cari"
                    value="<?= htmlspecialchars($cari); ?>">
                <button type="submit" class="btn btn-secondary ms-3">Cari</button>
            </div>
            <a href="<?= BASE_URL ?>admin/user/index.php"
                class="btn btn-sm btn-primary text-decoration-none text-white mt-4 ms-3 mb-2">
                Kembali Ke Data user
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
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($jumlah_data > 0) { ?>
                        <?php while ($user = mysqli_fetch_assoc($data_user)) { ?>
                            <tr>
                                <td><?= $nomor++; ?></td>
                                <td><?= $user['nama']; ?></td>
                                <td><?= $user['email']; ?></td>
                                <td><?= $user['role']; ?></td>
                                <td>
                                    <a class="btn btn-sm btn-info text-black"
                                        href="<?= BASE_URL ?>admin/user/edit.php?id=<?= $user['id']; ?>">Edit</a>
                                    <a class="btn btn-sm btn-danger text-black"
                                        href="<?= BASE_URL ?>admin/user/hapus.php?id=<?= $user['id']; ?>">Hapus</a>
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

</div>
</div>
</div>
<script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
<script src="<?= BASE_URL ?>vendor/sidebar.js"></script>
</body>

</html>