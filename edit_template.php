<?php
include 'header_admin.php';
include 'config.php';

$id = $_GET['id'];
$data_template = mysqli_query($conn, "SELECT * FROM template WHERE id='$id'");
$template = mysqli_fetch_assoc($data_template);
?>

<h2 class="ms-5 my-4">Edit Data Template</h2>

<form action="proses_edit_template.php" method="POST" class="mx-4" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $template['id']; ?>">
    <input type="hidden" name="tampak_depan_lama" value="<?= $template['tampak_depan']; ?>">

    <div class="mb-2">
        <label class="form-label ms-3">Nama Template:</label>
        <input type="text" name="nama_template" value="<?= $template['nama_template']; ?>" class="form-control" required>
    </div>

    <div class="mb-2">
        <p class="ms-2">Gambar tampak depan lama:</p>
        <img src="uploads/template/<?= $template['tampak_depan']; ?>" width="100px" class="ms-5"><br>
        <label class="form-label ms-3">Tampak Depan:</label>
        <input type="file" name="tampak_depan" class="form-control" accept="image/*">
    </div>

    <div class="d-flex justify-content-center mt-3">
        <button type="submit" name="update" class="btn btn-primary col-3">Update</button>
        <a href="data_template.php" class="btn btn-secondary ms-2">Kembali</a>
    </div>
</form>
