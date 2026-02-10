<?php 
include 'header_admin.php'; 
include 'config.php';
?>

<head>
    <title>Dashboard Admin</title>
</head>

<?php
$data_template = mysqli_query($conn, "SELECT * FROM template");
$jumlah_template = mysqli_num_rows($data_template);

$data_sertifikat = mysqli_query($conn, "SELECT * FROM sertifikat");
$jumlah_sertifikat = mysqli_num_rows($data_sertifikat);
?>


<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-3 mt-4">
                    <div class="card">
                        <div class="card-body text-center" style="background-color: #F5CF24;">
                            <img src="./image/iconmedali.png" class="ukuran my-3">
                            <h3 class="my-2"><?php echo $jumlah_sertifikat ?></h3>
                            <h5 class="my-3">Sertifikat</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 mt-4">
                    <div class="card">
                        <div class="card-body text-center" style="background-color: #30D1FF;">
                            <img src="./image/icontemplate.png" class="ukuran my-3">
                            <h3 class="my-2"><?php echo $jumlah_template ?></h3>
                            <h5 class="my-3">Template</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<script src="./vendor/bs.bundle.min.js"></script>
</body>

</html>