<?php
$allowed_roles = ["admin"];
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/admin/header.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/config/config.php';
?>

<head>
    <title>Dashboard Admin</title>
</head>

<?php
$data_template = mysqli_query($conn, "SELECT * FROM template");
$jumlah_template = mysqli_num_rows($data_template);

$data_sertifikat = mysqli_query($conn, "SELECT * FROM sertifikat");
$jumlah_sertifikat = mysqli_num_rows($data_sertifikat);

$data_pelatihan = mysqli_query($conn, "SELECT * FROM pelatihan");
$jumlah_pelatihan = mysqli_num_rows($data_pelatihan);
?>

<div class="container mt-3">
    <div class="jumbotron">
        <h1 class="text-center">Selamat, Anda Berhasil Login.! <br> Sebagai <?php $p = $_SESSION['role'];
                                                                    echo $p; ?></h1>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-3 mt-4">
                    <div class="card">
                        <div class="card-body text-center" style="background-color: #F5CF24;">
                            <img src="<?= BASE_URL ?>image/iconmedali.png" class="ukuran my-3">
                            <h3 class="my-2"><?php echo $jumlah_sertifikat ?></h3>
                            <h5 class="my-3">Sertifikat</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 mt-4">
                    <div class="card">
                        <div class="card-body text-center" style="background-color: #30D1FF;">
                            <img src="<?= BASE_URL ?>image/icon_template.png" class="ukuran my-3">
                            <h3 class="my-2"><?php echo $jumlah_template ?></h3>
                            <h5 class="my-3">Template</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 mt-4">
                    <div class="card">
                        <div class="card-body text-center text-white" style="background-color: #848484;">
                            <img src="<?= BASE_URL ?>image/training.png" class="ukuran my-3">
                            <h3 class="my-2"><?php echo $jumlah_pelatihan ?></h3>
                            <h5 class="my-3">Pelatihan</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
</body>

</html>