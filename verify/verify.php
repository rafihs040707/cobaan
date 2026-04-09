<?php
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/config/config.php';

$nomor = $_GET['kode'] ?? null;

// ======================
// DEFAULT INVALID
// ======================
$statusValid = false;

$nama = "Tidak Ditemukan";
$pelatihan = "Tidak Ditemukan";
$periode = "Tidak Ditemukan";
$issued = "Tidak Ditemukan";
$nomor_sertifikat = "Tidak Ditemukan";
$penyelenggara = "";

$qrImg = "";
$pdfFile = "";

// ======================
// CEK NOMOR SERTIFIKAT
// ======================
if ($nomor) {

    $nomor = trim($nomor);

    $stmt = mysqli_prepare($conn, "
        SELECT 
            s.*, 
            t.nama_template, 
            t.penyelenggara,
            p.nama_pelatihan
        FROM sertifikat s
        JOIN template t ON s.template_id = t.id
        LEFT JOIN pelatihan p ON s.pelatihan_id = p.id
        WHERE LOWER(TRIM(s.nomor_sertifikat)) = LOWER(TRIM(?))
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmt, "s", $nomor);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);


    // ======================
    // JIKA DATA VALID DITEMUKAN
    // ======================
    if ($data) {


        $statusValid = true;

        $nama = $data['nama'];
        $pelatihan = $data['nama_pelatihan'];
        $nomor_sertifikat = $data['nomor_sertifikat'];

        // jika ada kolom penyelenggara
        if (!empty($data['penyelenggara'])) {
            $penyelenggara = $data['penyelenggara'];
        }

        // format periode
        $awal = strtotime($data['periode_awal']);
        $akhir = strtotime($data['periode_akhir']);

        if (date('F Y', $awal) == date('F Y', $akhir)) {
            $periode = date('F d', $awal) . " - " . date('d, Y', $akhir);
        } else {
            $periode = date('F d', $awal) . " - " . date('F d, Y', $akhir);
        }

        $issued = date('F d, Y', strtotime($data['issued_date']));

        // ======================
        // QR Image
        // ======================
        $qrPath = "";
        $qrUrl = "";

        if (!empty($data['qr_image'])) {
            $qrPath = BASE_PATH . "/uploads/qrcode/" . $data['qr_image']; // untuk file_exists
            $qrUrl = BASE_URL . "uploads/qrcode/" . $data['qr_image'];  // untuk <img>
        }

        // ======================
        // PDF Sertifikat
        // ======================
        $pdfPath = "";
        $pdfUrl = "";

        if (!empty($data['file_sertifikat'])) {
            $pdfPath = BASE_PATH . "/uploads/sertifikat/" . $data['file_sertifikat']; // untuk file_exists
            $pdfUrl = BASE_URL . "uploads/sertifikat/" . $data['file_sertifikat'];  // untuk href
        }
    }
}

// ======================
// STYLE VALID / INVALID
// ======================
$color = $statusValid ? "#329F4A" : "#E63946";
$icon = $statusValid ? BASE_URL . "image/centang.png" : BASE_URL . "image/silang.png";
$title = $statusValid ? "Sertifikat Tervalidasi dan Terdaftar" : "Sertifikat tidak Terdaftar";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>vendor/bs.min.css">
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>image/logo.png">
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

                                <?php
                                $nomor_tmp = $data['nomor_sertifikat'] ?? '';
                                $nomor_tampil = $nomor_tmp !== ''
                                    ? explode("/", $nomor_tmp)[0]
                                    : '';
                                ?>
                                <tr>
                                    <td>Nomor Sertifikat</td>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($nomor_tampil); ?></td>
                                </tr>

                                <tr>
                                    <td>Penyelenggara</td>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($penyelenggara); ?></td>
                                </tr>
                            </table>
                        </td>
                        <td class="text-center">
                            <?php if ($statusValid && !empty($qrPath) && file_exists($qrPath)) { ?>
                                <img src="<?= $qrUrl; ?>" width="160">
                                <h6 class="mt-3">Scan QR Code untuk Verifikasi Sertifikat</h6>
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
                        <td>No. Sertifikat</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($nomor_tampil); ?></td>
                    </tr>

                    <tr>
                        <td>Penyelenggara</td>
                        <td>:</td>
                        <td><?= htmlspecialchars($penyelenggara); ?></td>
                    </tr>

                    <tr>
                        <th colspan="3" class="pt-4 text-center">QR Code</th>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center">
                            <?php if ($statusValid && !empty($qrPath) && file_exists($qrPath)) { ?>
                                <img src="<?= $qrUrl; ?>" width="160">
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
        <?php if ($statusValid && !empty($pdfPath) && file_exists($pdfPath)) { ?>
            <div class="d-flex justify-content-center align-items-center my-4 flex-wrap gap-2">

                <a href="<?= $pdfUrl; ?>" class="btn btn-success px-4" download>
                    <img src="<?= BASE_URL ?>image/download.png" style="width: 25px; height: 25px;">
                    Download Sertifikat
                </a>
                <button onclick="printPDF()" class="btn btn-info text-black px-4">
                    <img src="<?= BASE_URL ?>image/print.png" style="width: 25px; height: 25px;">
                    Print Sertifikat
                </button>
            </div>
        <?php } ?>
    </div>

    <iframe id="printFrame" style="display:none;"></iframe>

    <script src="<?= BASE_URL ?>vendor/bs.bundle.min.js"></script>
    <script>
        function printPDF() {
            const frame = document.getElementById('printFrame');
            frame.src = "<?= $pdfUrl ?>";

            frame.onload = function () {
                frame.contentWindow.focus();
                frame.contentWindow.print();
            };
        }
    </script>
</body>

</html>