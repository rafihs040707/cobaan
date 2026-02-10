<?php
include "config.php";

$uuid = $_GET['uuid'] ?? null;

// ======================
// DEFAULT INVALID
// ======================
$statusValid = false;

$nama = "Tidak Ditemukan";
$pelatihan = "Tidak Ditemukan";
$periode = "Tidak Ditemukan";
$issued = "Tidak Ditemukan";
$nomor_sertifikat = "Tidak Ditemukan";
$penyelenggara = "Tidak Ditemukan";

$qrImg = "";
$pdfFile = "";

// ======================
// CEK UUID
// ======================
if ($uuid) {

    $uuid_safe = mysqli_real_escape_string($conn, $uuid);

    // Ambil data hanya jika status = 1 (VALID)
    $query = mysqli_query($conn, "
        SELECT s.*, t.nama_template
        FROM sertifikat s
        JOIN template t ON s.template_id = t.id
        WHERE s.nomor_sertifikat LIKE '%-$uuid_safe'
        AND s.status = 1
        LIMIT 1
    ");

    $data = mysqli_fetch_assoc($query);

    // ======================
    // JIKA DATA VALID DITEMUKAN
    // ======================
    if ($data) {

        $statusValid = true;

        $nama = $data['nama'];
        $pelatihan = $data['pelatihan'];
        $nomor_sertifikat = $data['nomor_sertifikat'];

        // jika ada kolom penyelenggara
        if (!empty($data['penyelenggara'])) {
            $penyelenggara = $data['penyelenggara'];
        }

        // format periode
        $awal  = strtotime($data['periode_awal']);
        $akhir = strtotime($data['periode_akhir']);

        if (date('F Y', $awal) == date('F Y', $akhir)) {
            $periode = date('F d', $awal) . " - " . date('d, Y', $akhir);
        } else {
            $periode = date('F d', $awal) . " - " . date('F d, Y', $akhir);
        }

        $issued = date('F d, Y', strtotime($data['issued_date']));

        // QR Image
        if (!empty($data['qr_image'])) {
            $qrImg = "uploads/qrcode/" . $data['qr_image'];
        }

        // PDF Sertifikat
        if (!empty($data['file_sertifikat'])) {
            $pdfFile = "uploads/sertifikat/" . $data['file_sertifikat'];
        }
    }
}

// ======================
// STYLE VALID / INVALID
// ======================
$color = $statusValid ? "#329F4A" : "#E63946";
$icon  = $statusValid ? "./image/centang.png" : "./image/silang.png";
$title = $statusValid ? "Sertifikat Valid dan Terdaftar" : "Sertifikat Tidak Valid atau tidak Terdaftar";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./vendor/bs.min.css">
    <title>Halaman Verifikasi</title>
</head>

<body>

<div class="container mt-5">

    <!-- HEADER ICON -->
    <div class="d-flex align-items-center justify-content-center flex-wrap text-center">
        <div class="rounded-circle d-flex align-items-center justify-content-center"
            style="width: 90px; height: 90px; background-color: <?= $color; ?>;">
            <img src="<?= $icon; ?>" style="width:55px;">
        </div>

        <h4 class="ms-3 mb-0 fw-bold mt-3 mt-md-0" style="color: <?= $color; ?>;">
            <?= $title; ?>
        </h4>
    </div>

    <!-- DESKTOP VIEW -->
    <div class="table-responsive d-none d-md-block mt-5">
        <table class="table table-borderless align-middle">
            <tbody>
                <tr>
                    <th style="width:70%">Data Sertifikat</th>
                    <th class="text-center" style="width:30%">QR Code</th>
                </tr>

                <tr>
                    <td>
                        <table class="table table-borderless">
                            <tr>
                                <td style="width:200px;">Nama Peserta</td>
                                <td style="width:20px;">:</td>
                                <td><?= htmlspecialchars($nama); ?></td>
                            </tr>

                            <tr>
                                <td>Judul Pelatihan</td>
                                <td>:</td>
                                <td><?= htmlspecialchars($pelatihan); ?></td>
                            </tr>

                            <tr>
                                <td>Periode</td>
                                <td>:</td>
                                <td><?= htmlspecialchars($periode); ?></td>
                            </tr>

                            <tr>
                                <td>Issued Date</td>
                                <td>:</td>
                                <td><?= htmlspecialchars($issued); ?></td>
                            </tr>

                            <tr>
                                <td>Nomor Sertifikat</td>
                                <td>:</td>
                                <td><?= htmlspecialchars($nomor_sertifikat); ?></td>
                            </tr>

                            <tr>
                                <td>Penyelenggara</td>
                                <td>:</td>
                                <td><?= htmlspecialchars($penyelenggara); ?></td>
                            </tr>

                            <tr>
                                <td>Status</td>
                                <td>:</td>
                                <td>
                                    <?php if ($statusValid) { ?>
                                        <span class="badge bg-success px-3 py-2">Valid</span>
                                    <?php } else { ?>
                                        <span class="badge bg-danger px-3 py-2">Tidak Valid</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td class="text-center">
                        <?php if ($statusValid && !empty($qrImg) && file_exists($qrImg)) { ?>
                            <img src="<?= $qrImg; ?>" width="160">
                            <h6 class="mt-3">Scan untuk Verifikasi</h6>
                        <?php } else { ?>
                            <p class="text-muted">QR Code tidak tersedia</p>
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <!-- MOBILE VIEW -->
    <div class="d-block d-md-none mt-5">
        <table class="table table-borderless">
            <tbody>
                <tr>
                    <th colspan="3">Data Sertifikat</th>
                </tr>

                <tr>
                    <td>Nama Peserta</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($nama); ?></td>
                </tr>

                <tr>
                    <td>Judul Pelatihan</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($pelatihan); ?></td>
                </tr>

                <tr>
                    <td>Periode</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($periode); ?></td>
                </tr>

                <tr>
                    <td>Issued Date</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($issued); ?></td>
                </tr>

                <tr>
                    <td>Nomor Sertifikat</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($nomor_sertifikat); ?></td>
                </tr>

                <tr>
                    <td>Penyelenggara</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($penyelenggara); ?></td>
                </tr>

                <tr>
                    <td>Status</td>
                    <td>:</td>
                    <td>
                        <?php if ($statusValid) { ?>
                            <span class="badge bg-success px-3 py-2">Valid</span>
                        <?php } else { ?>
                            <span class="badge bg-danger px-3 py-2">Tidak Valid</span>
                        <?php } ?>
                    </td>
                </tr>

                <tr>
                    <th colspan="3" class="pt-4 text-center">QR Code</th>
                </tr>

                <tr>
                    <td colspan="3" class="text-center">
                        <?php if ($statusValid && !empty($qrImg) && file_exists($qrImg)) { ?>
                            <img src="<?= $qrImg; ?>" width="160">
                            <h6 class="mt-3">Scan untuk Verifikasi</h6>
                        <?php } else { ?>
                            <p class="text-muted">QR Code tidak tersedia</p>
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <!-- BUTTON DOWNLOAD + PRINT (hanya jika valid) -->
    <?php if ($statusValid && !empty($pdfFile) && file_exists($pdfFile)) { ?>
        <div class="d-flex justify-content-center align-items-center my-4 flex-wrap gap-2">

            <a href="<?= $pdfFile; ?>" class="btn btn-primary px-4" download>
                <img src="./image/download.png" style="width: 25px; height: 25px;">
                Download Sertifikat
            </a>

            <a href="<?= $pdfFile; ?>" target="_blank" class="btn btn-secondary px-4">
                <img src="./image/print.png" style="width: 25px; height: 25px;">
                Print Sertifikat
            </a>

        </div>
    <?php } ?>

</div>

<script src="./vendor/bs.bundle.min.js"></script>
</body>
</html>
