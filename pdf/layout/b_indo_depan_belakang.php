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
            position: relative;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
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

        .belakang {
            background-image: url("<?= BASE_URL ?>uploads/template/<?= $bg_belakang ?>");
            background-size: cover;
            background-position: center;
        }

        .judul_belakang {
            position: relative;
            top: 155px;
            width: 100%;
            text-align: center;
            font-size: 17px;
            font-weight: bold;
        }

        .info_peserta {
            position: relative;
            top: 205px;
            width: 550px;
            margin: auto;
            font-size: 16px;
            border-collapse: collapse;
        }

        .tabel_materi {
            position: absolute;
            top: 336px;
            left: 280px;
            width: 560px;
            border-collapse: collapse;
            font-size: 15px;
        }

        .tabel_materi th {
            border: 1px solid #444;
            background: #e5e5e5;
        }

        .tabel_materi td {
            border: 1px solid #444;
        }

        .total td {
            font-weight: bold;
            text-align: center;
        }

        table {
            page-break-inside: avoid;
        }

        .text-center {
            text-align: center;
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
            Dikeluarkan Tanggal: <?= $issued ?>
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

    <div class="halaman belakang">

        <div class="judul_belakang">
            <?= $data['nama_pelatihan'] ?>
        </div>

        <table class="info_peserta">
            <tr>
                <td width="85">Nama</td>
                <td>: <?= $data['nama'] ?></td>
            </tr>
        </table>

        <table class="info_peserta">
            <tr>
                <td width="85">Nomor Sertifikat</td>
                <td>: <?= $data['nomor_sertifikat'] ?></td>
            </tr>
        </table>

        <table class="tabel_materi">
            <thead>
                <tr>
                    <th width="15%">SESI</th>
                    <th>TOPIK</th>
                    <th width="22%">DURASI / JAM</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($materiList)): ?>
                    <?php foreach ($materiList as $i => $m): ?>
                        <tr>
                            <td class="text-center"><?= $m['urutan'] ?></td>
                            <td><?= htmlspecialchars($m['nama_materi']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($m['durasi']) ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <tr class="total">
                        <td colspan="2">Total Durasi</td>
                        <td><?= $totalDurasi ?></td>
                    </tr>

                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center;">Tidak ada data materi</td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>

    </div>
</body>

</html>