<?php
session_start();

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == "admin") {
        header("Location: dashboard_admin.php");
        exit;
    } elseif ($_SESSION['role'] == "lo") {
        header("Location: dashboard_lo.php");
        exit;
    }
}

// ALERT LOGIN SALAH
if (isset($_SESSION['login_error'])) {
    echo "<script>alert('{$_SESSION['login_error']}');</script>";
    unset($_SESSION['login_error']); // hapus supaya tidak muncul lagi
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="vendor/bs.min.css">
    <title>Login</title>
</head>

<body style="background-image: url('image/watermark.png'); background-size: cover; background-position: center; height: 100vh; background-repeat: no-repeat;">

    <?php
    if (isset($_GET['logout']) && $_GET['logout'] == 'berhasil') {
        $durasi = 4000; // durasi dalam ms (4 detik)
    ?>
        <div id="logoutAlert" class="alert alert-primary d-flex position-absolute w-100 align-items-center fade show" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
            </svg>
            <div>
                Anda telah berhasil keluar dari aplikasi E-Sertifikat
            </div>
        </div>

        <script>
            setTimeout(() => {
                const alertEl = document.getElementById('logoutAlert');
                if (alertEl) {
                    const bsAlert = new bootstrap.Alert(alertEl);
                    bsAlert.close();
                }
            }, <?= $durasi ?>);

            window.history.replaceState({}, document.title, 'index.php');
        </script>
    <?php
    }
    ?>

    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-4 text-center">
                <h3 class="mb-3 fw-bold text-white">E-Sertifikat</h3>
                <div class="card shadow">
                    <div class="card-body" style="background-color: #848484;">
                        <h4 class="text-center mb-3">Sign in to start your session </h4>
                        <form action="proses_login.php" method="POST">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" name="email" id="floatingInput" placeholder="email address">
                                <label for="floatingInput">Email address</label>
                            </div>
                            <div class="form-floating">
                                <input type="password" class="form-control" name="password" id="floatingPassword" placeholder="password">
                                <label for="floatingPassword">Password</label>
                            </div>
                            <div class="text-center mt-3">
                                <button type="submit" name="submit" class="btn btn-primary">Sign in</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="vendor/bs.bundle.min.js"></script>
</body>

</html>