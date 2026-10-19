<?php
session_start();

// Handling Path Koneksi
if (file_exists(__DIR__ . '/../config/koneksi.php')) {
    require_once __DIR__ . '/../config/koneksi.php';
} elseif (file_exists(__DIR__ . '/config/koneksi.php')) {
    require_once __DIR__ . '/config/koneksi.php';
} else {
    die("File koneksi.php tidak ditemukan!");
}

// Proteksi Akses Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// 1. INISIALISASI VARIABEL UTAMA (Agar tidak undefined / merah)
$aksi  = isset($_GET['aksi']) ? $_GET['aksi'] : 'tambah';
$error = '';

// 2. INISIALISASI DATA EDIT DEFAULT
$data_edit = [
    'id_produk'   => '',
    'nama_produk' => '',
    'id_kategori' => '',
    'brand'       => '',
    'ukuran'      => '',
    'kondisi'     => 'Like New (9.5/10)',
    'harga'       => '',
    'status'      => 'Ready',
    'gambar'      => 'default.jpg'
];

// --- AKSI HAPUS ---
if ($aksi === 'hapus' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $q_foto = mysqli_query($koneksi, "SELECT gambar FROM produk WHERE id_produk = $id");
    if ($dt = mysqli_fetch_assoc($q_foto)) {
        if ($dt['gambar'] != 'default.jpg' && file_exists("../assets/uploads/" . $dt['gambar'])) {
            unlink("../assets/uploads/" . $dt['gambar']);
        }
    }
    mysqli_query($koneksi, "DELETE FROM produk WHERE id_produk = $id");
    header("Location: index.php");
    exit;
}

// --- JIKA MODE EDIT, AMBIL DATA DARI DB ---
if ($aksi === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = $id");
    if (mysqli_num_rows($res) > 0) {
        $data_edit = mysqli_fetch_assoc($res);
    }
}

// --- AKSI SIMPAN (TAMBAH / EDIT) ---
if (isset($_POST['simpan'])) {
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $id_kategori = intval($_POST['id_kategori']);
    $brand       = mysqli_real_escape_string($koneksi, $_POST['brand']);
    $ukuran      = mysqli_real_escape_string($koneksi, $_POST['ukuran']);
    $kondisi     = mysqli_real_escape_string($koneksi, $_POST['kondisi']);
    $harga       = intval($_POST['harga']);
    $status      = mysqli_real_escape_string($koneksi, $_POST['status']);

    $gambar = $_POST['gambar_lama'] ?? 'default.jpg';
    if (isset($_FILES['gambar']['name']) && $_FILES['gambar']['name'] != '') {
        $filename   = $_FILES['gambar']['name'];
        $tmp_name   = $_FILES['gambar']['tmp_name'];
        $ext        = pathinfo($filename, PATHINFO_EXTENSION);
        $new_name   = time() . '_' . rand(100, 999) . '.' . $ext;
        $target_dir = "../assets/uploads/";

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($tmp_name, $target_dir . $new_name)) {
            if (isset($_POST['gambar_lama']) && $_POST['gambar_lama'] != 'default.jpg' && file_exists($target_dir . $_POST['gambar_lama'])) {
                unlink($target_dir . $_POST['gambar_lama']);
            }
            $gambar = $new_name;
        }
    }

    if ($aksi === 'tambah') {
        $query = "INSERT INTO produk (id_kategori, nama_produk, brand, ukuran, kondisi, harga, status, gambar) 
                  VALUES ($id_kategori, '$nama_produk', '$brand', '$ukuran', '$kondisi', $harga, '$status', '$gambar')";
        if (mysqli_query($koneksi, $query)) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal menambah produk!";
        }
    } elseif ($aksi === 'edit' && isset($_POST['id_produk'])) {
        $id_produk = intval($_POST['id_produk']);
        $query = "UPDATE produk SET 
                  id_kategori = $id_kategori,
                  nama_produk = '$nama_produk',
                  brand       = '$brand',
                  ukuran      = '$ukuran',
                  kondisi     = '$kondisi',
                  harga       = $harga,
                  status      = '$status',
                  gambar      = '$gambar'
                  WHERE id_produk = $id_produk";
        if (mysqli_query($koneksi, $query)) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal mengupdate produk!";
        }
    }
}

$res_kat = mysqli_query($koneksi, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($aksi); ?> Item — ARBMD THRIFT</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0a0a0a; color: #ffffff; }
        .form-card { background: #111111; border: 1px solid #222222; border-radius: 12px; padding: 28px; }
        .form-control, .form-select { background-color: #1a1a1a; border: 1px solid #333333; color: #ffffff; }
        .form-control:focus, .form-select:focus { background-color: #1a1a1a; border-color: #ffffff; color: #ffffff; box-shadow: none; }
        .btn-submit { background: #ffffff; color: #000000; font-weight: 700; border: none; padding: 10px; }
        .btn-submit:hover { background: #cccccc; color: #000000; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-white mb-0"><?= $aksi === 'edit' ? 'Edit Item Thrift' : 'Tambah Drop Baru'; ?></h4>
                <a href="index.php" class="btn btn-outline-light btn-sm">Kembali</a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2 small bg-dark text-danger border-danger mb-3"><?= $error; ?></div>
            <?php endif; ?>

            <div class="form-card">
                <form action="produk.php?aksi=<?= $aksi; ?>" method="POST" enctype="multipart/form-data">
                    
                    <?php if ($aksi === 'edit'): ?>
                        <input type="hidden" name="id_produk" value="<?= $data_edit['id_produk']; ?>">
                        <input type="hidden" name="gambar_lama" value="<?= $data_edit['gambar']; ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">NAMA BARANG / ITEM</label>
                        <input type="text" name="nama_produk" class="form-control" placeholder="Contoh: WORK JACKET CAMO" value="<?= htmlspecialchars($data_edit['nama_produk']); ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">KATEGORI</label>
                            <select name="id_kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php while ($k = mysqli_fetch_assoc($res_kat)): ?>
                                    <option value="<?= $k['id_kategori']; ?>" <?= $data_edit['id_kategori'] == $k['id_kategori'] ? 'selected' : ''; ?>>
                                        <?= strtoupper($k['nama_kategori']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">BRAND</label>
                            <input type="text" name="brand" class="form-control" placeholder="Contoh: Calle, Carhartt, Stussy" value="<?= htmlspecialchars($data_edit['brand']); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold text-secondary">SIZE</label>
                            <input type="text" name="ukuran" class="form-control" placeholder="L" value="<?= htmlspecialchars($data_edit['ukuran']); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold text-secondary">HARGA (RP)</label>
                            <input type="number" name="harga" class="form-control" placeholder="638000" value="<?= $data_edit['harga']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold text-secondary">STATUS</label>
                            <select name="status" class="form-select">
                                <option value="Ready" <?= $data_edit['status'] === 'Ready' ? 'selected' : ''; ?>>Ready</option>
                                <option value="Sold Out" <?= $data_edit['status'] === 'Sold Out' ? 'selected' : ''; ?>>Sold Out</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">KONDISI BARANG</label>
                        <input type="text" name="kondisi" class="form-control" placeholder="Contoh: 9.5/10 - Mulus No Minus" value="<?= htmlspecialchars($data_edit['kondisi']); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary">FOTO PRODUK</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                    </div>

                    <button type="submit" name="simpan" class="btn btn-submit w-100 rounded-2">
                        <?= $aksi === 'edit' ? 'UPDATE ITEM' : 'SIMPAN DROP BARU'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>