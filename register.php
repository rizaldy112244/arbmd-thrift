<?php
session_start();
require_once 'config/koneksi.php';

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        $check = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username' OR email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Username atau Email sudah terpakai!";
        } else {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (nama, username, email, password, role) 
                      VALUES ('$nama', '$username', '$email', '$hashed_pass', 'user')";
            
            if (mysqli_query($koneksi, $query)) {
                $success = "Akun berhasil dibuat! Silakan <a href='login.php' class='text-white text-decoration-underline'>Login</a>.";
            } else {
                $error = "Gagal mendaftar, coba lagi!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — ARBMD THRIFT</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0a0a0a; color: #ffffff; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .register-card { background: #111111; border: 1px solid #222222; border-radius: 12px; padding: 32px; }
        .brand-title { font-weight: 800; font-size: 1.8rem; color: #ffffff; }
        .form-control { background-color: #1a1a1a; border: 1px solid #333333; color: #ffffff; padding: 10px 14px; }
        .form-control:focus { background-color: #1a1a1a; border-color: #ffffff; color: #ffffff; box-shadow: none; }
        .btn-register { background: #ffffff; color: #000000; font-weight: 700; border: none; padding: 10px; }
        .btn-register:hover { background: #cccccc; color: #000000; }
        .btn-back { color: #888888; text-decoration: none; font-size: 0.85rem; }
        .btn-back:hover { color: #ffffff; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            
            <div class="mb-3 text-center">
                <a href="index.php" class="btn-back"><i class="bi bi-arrow-left me-1"></i> Kembali ke Catalog</a>
            </div>

            <div class="register-card">
                <div class="text-center mb-4">
                    <span class="brand-title">ARBMD</span>
                    <p class="text-secondary small mt-1 mb-0">Create an account to join the squad</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2 small bg-dark text-danger border-danger mb-3"><?= $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success py-2 small bg-dark text-success border-success mb-3"><?= $success; ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">NAMA LENGKAP</label>
                        <input type="text" name="nama" class="form-control" placeholder="Alex Turner" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">USERNAME</label>
                        <input type="text" name="username" class="form-control" placeholder="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">EMAIL</label>
                        <input type="email" name="email" class="form-control" placeholder="email@domain.com" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">PASSWORD</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">KONFIRMASI</label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>
                    <button type="submit" name="register" class="btn btn-register w-100 rounded-2 mb-3">DAFTAR AKUN</button>
                </form>

                <hr class="border-secondary border-opacity-25 my-3">
                <p class="text-center small text-secondary mb-0">Sudah punya akun? <a href="login.php" class="text-white text-decoration-underline">Masuk</a></p>
            </div>

        </div>
    </div>
</div>

</body>
</html>