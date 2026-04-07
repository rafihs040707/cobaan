<?php
$allowed_roles = ["lo"];
require_once __DIR__ . '/../../bootstrap.php';
require_once BASE_PATH . '/auth/cek_login.php';
require_once BASE_PATH . '/config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_POST['submit'])) {
    die("Akses dilarang...");
}

// ======================
// AMBIL + SANITIZE
// ======================
$id           = (int)($_POST['id'] ?? 0);
$nama         = trim($_POST['nama'] ?? '');
$pelatihan    = (int)($_POST['pelatihan'] ?? 0);
$periode_awal = trim($_POST['periode_awal'] ?? '');
$periode_akhir= trim($_POST['periode_akhir'] ?? '');
$template_id  = (int)($_POST['template_id'] ?? 0);

// ======================
// VALIDASI WAJIB
// ======================
if (
    !$id ||
    $nama === '' ||
    !$pelatihan ||
    $periode_awal === '' ||
    $periode_akhir === '' ||
    !$template_id
) {
    $_SESSION['error'] = "Semua field wajib diisi.";
    header("Location: " . BASE_URL . "lo/sertifikat/index.php");
    exit;
}

// validasi tanggal
if ($periode_akhir < $periode_awal) {
    $_SESSION['error'] = "Periode akhir tidak boleh lebih kecil dari periode awal.";
    header("Location: " . BASE_URL . "lo/sertifikat/index.php");
    exit;
}

// ======================
// PREPARE UPDATE
// ======================
$stmt = $conn->prepare("
    UPDATE sertifikat 
    SET nama = ?, 
        pelatihan_id = ?, 
        periode_awal = ?, 
        periode_akhir = ?, 
        template_id = ?
    WHERE id = ?
");

if (!$stmt) {
    $_SESSION['error'] = "Prepare statement gagal.";
    header("Location: " . BASE_URL . "lo/sertifikat/index.php");
    exit;
}

$stmt->bind_param(
    "sissii",
    $nama,
    $pelatihan,
    $periode_awal,
    $periode_akhir,
    $template_id,
    $id
);

// ======================
// EKSEKUSI
// ======================
$conn->begin_transaction();

try {

    // ======================
    // UPDATE SERTIFIKAT
    // ======================
    if (!$stmt->execute()) {
        throw new Exception("Update sertifikat gagal");
    }

    // ======================
    // HAPUS DATA LAMA
    // ======================
    mysqli_query($conn, "
        DELETE FROM sertifikat_materi 
        WHERE sertifikat_id = '$id'
    ");

    // ======================
    // INSERT ULANG
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

        foreach ($materi_list as $i => $materi) {

            $materi = trim($materi);
            $durasi  = trim($durasi_list[$i] ?? '');

            if ($materi === '' && $durasi === '') continue;

            // cek materi
            $stmt_cek->bind_param("s", $materi);
            $stmt_cek->execute();
            $result = $stmt_cek->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $materi_id = $row['id'];
            } else {
                $stmt_insert_materi->bind_param("s", $materi);
                $stmt_insert_materi->execute();
                $materi_id = $conn->insert_id;
            }

            // insert relasi
            $urutan = $i + 1;

            $stmt_insert_relasi->bind_param(
                "iisi",
                $id,
                $materi_id,
                $durasi,
                $urutan
            );

            $stmt_insert_relasi->execute();
        }
    }

    $conn->commit();

    $_SESSION['success'] = "Data sertifikat & materi berhasil diupdate.";

} catch (Exception $e) {

    $conn->rollback();

    $_SESSION['error'] = "Gagal update: " . $e->getMessage();
}

header("Location: " . BASE_URL . "lo/sertifikat/index.php");
exit;