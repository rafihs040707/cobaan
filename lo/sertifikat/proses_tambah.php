<?php
$allowed_roles = ["lo"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/config/config.php';

if (isset($_POST['submit'])) {

    // ======================
    // AMBIL DATA
    // ======================
    $nama = preg_replace('/\s+/', ' ', trim($_POST['nama'] ?? ''));
    $pelatihan_id = trim($_POST['pelatihan'] ?? '');
    $periode_awal = trim($_POST['periode_awal'] ?? '');
    $periode_akhir = trim($_POST['periode_akhir'] ?? '');
    $issued_date = trim($_POST['issued_date'] ?? '');
    $template_id = trim($_POST['template_id'] ?? '');

    // ======================
    // VALIDASI
    // ======================
    if (
        $nama === '' ||
        $pelatihan_id === '' ||
        $periode_awal === '' ||
        $periode_akhir === '' ||
        $template_id === ''
    ) {
        $_SESSION['error'] = "Semua field wajib diisi!";
        header("Location:" . BASE_URL . "admin/sertifikat/index.php");
        exit;
    }

    if ($periode_akhir < $periode_awal) {
        $_SESSION['error'] = "Periode akhir tidak boleh lebih kecil dari periode awal!";
        header("Location:" . BASE_URL . "admin/sertifikat/index.php");
        exit;
    }

    // ======================
    // MULAI TRANSAKSI 🔥
    // ======================
    $conn->begin_transaction();

    try {

        // ======================
        // INSERT SERTIFIKAT
        // ======================
        $stmt = $conn->prepare("
            INSERT INTO sertifikat 
            (nama, pelatihan_id, periode_awal, periode_akhir, template_id)
            VALUES (?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            throw new Exception("Prepare sertifikat gagal");
        }

        $stmt->bind_param(
            "sissi",
            $nama,
            $pelatihan_id,
            $periode_awal,
            $periode_akhir,
            $template_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Insert sertifikat gagal");
        }

        $last_id = $conn->insert_id;

        // ======================
        // INSERT MATERI
        // ======================
        $materi_list = $_POST['materi'] ?? [];
        $durasi_list  = $_POST['durasi'] ?? [];

        if (!empty($materi_list)) {

            $stmt_cek = $conn->prepare("SELECT id FROM materi_master WHERE nama_materi = ?");
            $stmt_insert_materi = $conn->prepare("INSERT INTO materi_master (nama_materi) VALUES (?)");
            $stmt_insert_relasi = $conn->prepare("
                INSERT INTO sertifikat_materi (sertifikat_id, materi_id, durasi, urutan)
                VALUES (?, ?, ?, ?)
            ");

            if (!$stmt_cek || !$stmt_insert_materi || !$stmt_insert_relasi) {
                throw new Exception("Prepare statement materi gagal");
            }

            foreach ($materi_list as $i => $materi) {

                $materi = trim($materi);
                $durasi  = trim($durasi_list[$i] ?? '');

                if ($materi === '' && $durasi === '') {
                    continue;
                }

                // CEK materi
                $stmt_cek->bind_param("s", $materi);
                if (!$stmt_cek->execute()) {
                    throw new Exception("Cek materi gagal");
                }

                $result = $stmt_cek->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $materi_id = $row['id'];
                } else {
                    $stmt_insert_materi->bind_param("s", $materi);
                    if (!$stmt_insert_materi->execute()) {
                        throw new Exception("Insert materi gagal");
                    }
                    $materi_id = $conn->insert_id;
                }

                // INSERT RELASI
                $urutan = $i + 1;

                $stmt_insert_relasi->bind_param(
                    "iisi",
                    $last_id,
                    $materi_id,
                    $durasi,
                    $urutan
                );

                if (!$stmt_insert_relasi->execute()) {
                    throw new Exception("Insert relasi gagal");
                }
            }
        }

        // ======================
        // COMMIT ✅
        // ======================
        $conn->commit();

        $_SESSION['success'] = "Data sertifikat & materi berhasil disimpan!";
        header("Location:" . BASE_URL . "lo/sertifikat/index.php?id=$last_id");
        exit;

    } catch (Exception $e) {

        // ======================
        // ROLLBACK ❌
        // ======================
        $conn->rollback();

        $_SESSION['error'] = "Gagal menyimpan data: " . $e->getMessage();
        header("Location:" . BASE_URL . "lo/sertifikat/index.php");
        exit;
    }
}