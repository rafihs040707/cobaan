<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', serif;
        }

        .halaman {
            position: fixed;
            top: 0;
            left: 0;
            width: 1123px;
            height: 794px;
            z-index: -1;
        }

        .nama {
            position: absolute;
            top: 300px;
            left: 53px;
            width: 100%;
            text-align: center;
            font-size: 45px;
            font-weight: bold;
            color: #cfa34a;
        }

        .pelatihan {
            position: absolute;
            top: 445px;
            left: 60px;
            width: 100%;
            text-align: center;
            font-size: 26px;
            font-weight: bold;
            color: #cfa34a;
        }

        .periode {
            position: absolute;
            top: 505px;
            left: 50px;
            width: 100%;
            text-align: center;
            font-size: 20px;
            color: black;
        }

        .issued {
            position: absolute;
            bottom: 27px;
            left: 50px;
            font-size: 15px;
        }

        .nama_ceo {
            position: absolute;
            bottom: 55px;
            left: 50px;
            width: 100%;
            text-align: center;
            font-size: 18px;
        }

        .ceo {
            position: absolute;
            bottom: 32px;
            left: 50px;
            width: 100%;
            text-align: center;
            font-size: 16px;
        }

        .ttd_direktur {
            position: absolute;
            bottom: 40px;
            left: 510px;
            width: 100%;
        }

        .nomor {
            position: absolute;
            bottom: 145px;
            right: 35px;
            font-size: 15px;
        }

        .qr {
            position: absolute;
            bottom: 26px;
            right: 22px;
        }

        .depan {
            background-image: url("<?= BASE_URL ?>uploads/template/<?= $bg_depan ?>");
            background-size: cover;
            background-position: center;
        }

    </style>
</head>

<body>
    <div class="halaman depan">

    <div class="nama">
        <?= $data['nama'] ?>
    </div>

    <div class="pelatihan">
        <?= $data['nama_pelatihan'] ?>
    </div>

    <div class="periode">
        Periode: <?= $periode ?>
    </div>

    <div class="issued">
        Issued Date: <?= $issued ?>
    </div>

    <div class="nama_ceo">
        <u>Endra Prasetya Rudiyanto</u>
    </div>

    <div class="ceo">
        Chief Executive Officer
    </div>

    <div class="ttd_direktur">
        <?= $ttdDirektur ?>
    </div>

    <div class="nomor">
        <?= $nomor_tampil ?>
    </div>

    <div class="qr">
        <img src="<?= $qrUrlPath ?>" width="120">
    </div>
    </div>

</body>

</html>