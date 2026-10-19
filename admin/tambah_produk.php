<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
require_once '../config/koneksi.php';

$pesan = '';

// Ambil opsi kategori dari DB
$kategori_query = mysqli_query($koneksi, "SELECT * FROM kategori");

if (isset($_POST['simpan'])) {
    $id_kategori  = $_POST['id_kategori'];
    $nama_produk  = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $brand        = mysqli_real_escape_string($koneksi, $_POST['brand']);
    $ukuran       = mysqli_real_escape_string($koneksi, $_POST['ukuran']);
    $harga        = intval($_POST['harga']);
    $kondisi      = mysqli_real_escape_string($koneksi, $_POST['kondisi']);
    $deskripsi    = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $status       = $_POST['status'];

    // Pengolahan Upload Gambar
    $foto_nama    = $_FILES['gambar']['name'];
    $foto_tmp     = $_FILES['gambar']['tmp_name'];
    $foto_ext     = strtolower(pathinfo($foto_nama, PATHINFO_EXTENSION));
    
    // Rename nama gambar agar unik
    $nama_baru    = time() . '_' . rand(100, 999) . '.' . $foto_ext;
    $target_dir   = "../assets/uploads/" . $nama_baru;

    $ekstensi_diperbolehkan = array('jpg', 'jpeg', 'png', 'webp');

    if (in_array($foto_ext, $ekstensi_diperbolehkan)) {
        if (move_uploaded_file($foto_tmp, $target_dir)) {
            $sql = "INSERT INTO produk (id_kategori, nama_produk, brand, ukuran, harga, kondisi, deskripsi, gambar, status) 
                    VALUES ('$id_kategori', '$nama_produk', '$brand', '$ukuran', '$harga', '$kondisi', '$deskripsi', '$nama_baru', '$status')";
            
            if (mysqli_query($koneksi, $sql)) {
                header("Location: produk.php");
                exit;
            } else {
                $pesan = "<div class='alert alert-danger'>Gagal menyimpan data ke database.</div>";
            }
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal mengunggah foto produk.</div>";
        }
    } else {
        $pesan = "<div class='alert alert-warning'>Format file harus JPG, JPEG, PNG, atau WEBP.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Baju Thrift - ARBMD Admin</title>
    <link href="[https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css](https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css)" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">Form Tambah Item Thrift Baru</h5>
                </div>
                <div class="card-body p-4">
                    <?= $pesan; ?>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Item / Produk</label>
                                <input type="text" name="nama_produk" class="form-control" placeholder="Contoh: Crewneck Nike Vintage" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="id_kategori" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php while ($kat = mysqli_fetch_assoc($kategori_query)): ?>
                                        <option value="<?= $kat['id_kategori']; ?>"><?= $kat['nama_kategori']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Brand / Merk</label>
                                <input type="text" name="brand" class="form-control" placeholder="Nike, Adidas, Carhartt" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ukuran (Panjang x Lebar)</label>
                                <input type="text" name="ukuran" class="form-control" placeholder="L (P 70 x L 55)" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Harga (Rp)</label>
                                <input type="number" name="harga" class="form-control" placeholder="150000" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kondisi Barang / Rating</label>
                                <input type="text" name="kondisi" class="form-control" placeholder="9.5/10 - Mulus No Minus" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Barang</label>
                                <select name="status" class="form-select">
                                    <option value="Ready">Ready</option>
                                    <option value="Sold Out">Sold Out</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi Tambahan</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Informasi tag, warna asli, detail karet, dll."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Upload Foto Pakaian</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="produk.php" class="btn btn-outline-secondary">Kembali</a>
                            <button type="submit" name="simpan" class="btn btn-dark">Simpan Produk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>