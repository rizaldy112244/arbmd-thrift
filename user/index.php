<?php
session_start();

// Cek apakah user sudah login dan role-nya adalah user biasa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Area Pembeli - ARBMD Thrift</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">ARBMD Thrift</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-link text-light">Halo, <?= $_SESSION['nama']; ?></span>
                <a class="nav-link" href="../index.php">Lihat Katalog</a>
                <a class="nav-link text-danger" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3>Dashboard Pembeli</h3>
        <p>Selamat berbelanja item thrift impianmu!</p>
    </div>
</body>
</html>
