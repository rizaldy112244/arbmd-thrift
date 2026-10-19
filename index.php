<?php
session_start();
require_once 'config/koneksi.php';

// Inisialisasi Filter
$where_clauses = array();

// 1. Filter Kategori
if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $id_kat = intval($_GET['kategori']);
    $where_clauses[] = "produk.id_kategori = $id_kat";
}

// 2. Filter Search
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($koneksi, $_GET['search']);
    $where_clauses[] = "(produk.nama_produk LIKE '%$search%' OR produk.brand LIKE '%$search%')";
}

// 3. Filter Status (Availability)
if (isset($_GET['availability'])) {
    if ($_GET['availability'] === 'instock') {
        $where_clauses[] = "produk.status = 'Ready'";
    } elseif ($_GET['availability'] === 'sold') {
        $where_clauses[] = "produk.status = 'Sold Out'";
    }
}

// 4. Filter Ukuran
if (isset($_GET['size']) && !empty($_GET['size'])) {
    $size = mysqli_real_escape_string($koneksi, $_GET['size']);
    $where_clauses[] = "produk.ukuran LIKE '%$size%'";
}

// Combining SQL WHERE
$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Query Produk
$query_produk = "SELECT produk.*, kategori.nama_kategori 
                FROM produk 
                LEFT JOIN kategori ON produk.id_kategori = kategori.id_kategori 
                $where_sql 
                ORDER BY produk.id_produk DESC";
$res_produk = mysqli_query($koneksi, $query_produk);
$res_kat    = mysqli_query($koneksi, "SELECT * FROM kategori");

$nomor_wa = "6281234567890"; // Ganti dengan nomor WhatsApp Admin kamu
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARBMD THRIFT — Catalog</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0a0a0a;
            color: #ffffff;
        }

        .navbar-minimal {
            background: #0a0a0a;
            border-bottom: 1px solid #222222;
            padding: 18px 0;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 1.6rem;
            letter-spacing: -0.5px;
            color: #ffffff;
            text-decoration: none;
        }

        .nav-link-custom {
            color: #ffffff;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-decoration: none;
            transition: 0.2s;
        }

        .nav-link-custom:hover {
            color: #888888;
        }

        .filter-sidebar {
            border: 1px solid #222222;
            border-radius: 12px;
            padding: 20px;
            background: #111111;
        }

        .filter-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 12px;
            color: #888888;
        }

        .filter-group {
            border-bottom: 1px solid #222222;
            padding-bottom: 16px;
            margin-bottom: 16px;
        }

        .filter-group:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .form-check-label {
            font-size: 0.85rem;
            color: #cccccc;
            cursor: pointer;
        }

        .form-check-input {
            background-color: #1a1a1a;
            border-color: #333333;
        }

        .form-check-input:checked {
            background-color: #ffffff;
            border-color: #ffffff;
        }

        .form-control {
            background-color: #1a1a1a;
            border: 1px solid #333333;
            color: #ffffff;
        }

        .form-control:focus {
            background-color: #1a1a1a;
            border-color: #ffffff;
            color: #ffffff;
            box-shadow: none;
        }

        .btn-size {
            border: 1px solid #333333;
            background: #1a1a1a;
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 600;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.2s;
        }

        .btn-size:hover, .btn-size.active {
            background: #ffffff;
            color: #000000;
            border-color: #ffffff;
        }

        .product-card {
            border: none;
            background: transparent;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .product-img-wrapper {
            background: #141414;
            border: 1px solid #222222;
            border-radius: 8px;
            overflow: hidden;
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            position: relative;
        }

        .product-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 12px;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-img-wrapper img {
            transform: scale(1.05);
        }

        .product-name {
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 4px;
            color: #ffffff;
        }

        .product-price {
            font-size: 0.85rem;
            font-weight: 600;
            color: #aaaaaa;
        }

        .badge-status {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .table-size-chart th {
            background: #1a1a1a;
            color: #ffffff;
            border-color: #333333;
        }
        .table-size-chart td {
            background: #111111;
            color: #cccccc;
            border-color: #222222;
        }
    </style>
</head>
<body>

<!-- Navbar Minimalis Hitam -->
<nav class="navbar-minimal sticky-top">
  <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
    <a href="index.php" class="brand-logo">ARBMD</a>
    
    <!-- Menu Utama -->
    <div class="d-none d-md-flex gap-4">
        <a href="index.php" class="nav-link-custom">CATALOG</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#modalSizeChart" class="nav-link-custom">SIZE CHART</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#modalOrder" class="nav-link-custom"><i class="bi bi-whatsapp text-success me-1"></i> HOW TO ORDER LEWAT WA</a>
    </div>

    <!-- Tombol Akses Dashboard / Login / Keluar -->
    <div class="d-flex align-items-center gap-2">
        <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin/index.php" class="btn btn-light btn-sm font-bold text-dark fw-bold px-3">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard Admin
                </a>
            <?php else: ?>
                <span class="small font-bold text-light me-2"><i class="bi bi-person me-1"></i> <?= htmlspecialchars($_SESSION['nama']); ?></span>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-outline-danger btn-sm px-3" onclick="return confirm('Yakin ingin keluar / quit, dawg?')">
                <i class="bi bi-box-arrow-right me-1"></i> Keluar
            </a>
        <?php else: ?>
            <a href="login.php" class="btn btn-outline-light btn-sm px-3 fw-bold">
                <i class="bi bi-person me-1"></i> Login
            </a>
        <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Main Container -->
<div class="container-fluid px-4 py-4">
    <div class="row g-4">
        
        <!-- Sidebar Filter (Kiri) -->
        <div class="col-lg-3 col-md-4">
            <div class="filter-sidebar">
                <form action="index.php" method="GET">
                    
                    <!-- Search Input -->
                    <div class="filter-group">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari baju/brand..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button type="submit" class="btn btn-light btn-sm"><i class="bi bi-search"></i></button>
                        </div>
                    </div>

                    <!-- Filter Kategori -->
                    <div class="filter-group">
                        <div class="filter-title">Product Type</div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="kategori" id="kat_all" value="" <?= !isset($_GET['kategori']) || $_GET['kategori'] == '' ? 'checked' : ''; ?> onchange="this.form.submit()">
                            <label class="form-check-label" for="kat_all">All Products</label>
                        </div>
                        <?php mysqli_data_seek($res_kat, 0); while ($k = mysqli_fetch_assoc($res_kat)): ?>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="radio" name="kategori" id="kat_<?= $k['id_kategori']; ?>" value="<?= $k['id_kategori']; ?>" <?= isset($_GET['kategori']) && $_GET['kategori'] == $k['id_kategori'] ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <label class="form-check-label" for="kat_<?= $k['id_kategori']; ?>"><?= htmlspecialchars($k['nama_kategori']); ?></label>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Filter Availability -->
                    <div class="filter-group">
                        <div class="filter-title">Availability</div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="availability" id="avail_all" value="all" <?= !isset($_GET['availability']) || $_GET['availability'] == 'all' ? 'checked' : ''; ?> onchange="this.form.submit()">
                            <label class="form-check-label" for="avail_all">All</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="availability" id="avail_in" value="instock" <?= isset($_GET['availability']) && $_GET['availability'] == 'instock' ? 'checked' : ''; ?> onchange="this.form.submit()">
                            <label class="form-check-label" for="avail_in">In Stock</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="availability" id="avail_sold" value="sold" <?= isset($_GET['availability']) && $_GET['availability'] == 'sold' ? 'checked' : ''; ?> onchange="this.form.submit()">
                            <label class="form-check-label" for="avail_sold">Sold Out</label>
                        </div>
                    </div>

                    <!-- Filter Size -->
                    <div class="filter-group">
                        <div class="filter-title">Size</div>
                        <div class="d-flex flex-wrap gap-2">
                            <?php 
                            $sizes = array('S', 'M', 'L', 'XL', 'XXL');
                            foreach ($sizes as $sz): 
                                $active = (isset($_GET['size']) && $_GET['size'] == $sz) ? 'active' : '';
                            ?>
                                <a href="index.php?size=<?= $sz; ?>" class="btn-size <?= $active; ?>"><?= $sz; ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <a href="index.php" class="btn btn-outline-light btn-sm w-100 mt-2">Reset Filter</a>
                </form>
            </div>
        </div>

        <!-- Catalog Grid (Kanan) -->
        <div class="col-lg-9 col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0 text-white" style="font-size: 1.2rem;">All Products</h4>
                <small class="text-secondary"><?= mysqli_num_rows($res_produk); ?> Items</small>
            </div>

            <div class="row g-4">
                <?php if (mysqli_num_rows($res_produk) > 0): ?>
                    <?php while ($p = mysqli_fetch_assoc($res_produk)): ?>
                        <?php 
                        $pesan_wa = "Halo Admin ARBMD Thrift, saya mau tanya/order item ini:\n\n"
                                  . "📌 *Item:* " . $p['nama_produk'] . "\n"
                                  . "🏷️ *Brand:* " . $p['brand'] . "\n"
                                  . "📏 *Size:* " . $p['ukuran'] . "\n"
                                  . "💰 *Harga:* Rp " . number_format($p['harga'], 0, ',', '.');
                        $link_wa = "https://api.whatsapp.com/send?phone=" . $nomor_wa . "&text=" . urlencode($pesan_wa);
                        ?>
                        <div class="col-lg-4 col-md-6 col-6">
                            <a href="<?= $link_wa; ?>" target="_blank" class="product-card">
                                <div class="product-img-wrapper">
                                    <img src="assets/uploads/<?= $p['gambar']; ?>" alt="<?= htmlspecialchars($p['nama_produk']); ?>">
                                    <?php if ($p['status'] === 'Ready'): ?>
                                        <span class="badge bg-light text-dark badge-status">IN STOCK</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary badge-status">SOLD OUT</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-name"><?= htmlspecialchars($p['nama_produk']); ?></div>
                                <div class="product-price">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-secondary">Tidak ada produk yang cocok dengan filter kamu.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<!-- Modal Size Chart -->
<div class="modal fade" id="modalSizeChart" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-white border-secondary">
      <div class="modal-header border-secondary">
        <h5 class="modal-title font-bold">SIZE CHART GUIDE (PANJANG x LEBAR)</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="small text-secondary mb-3">*Ukur pakaian favoritmu dari ketiak ke ketiak (Lebar) dan dari bahu ke bawah (Panjang) dalam satuan Centimeter (cm).</p>
        
        <div class="table-responsive">
            <table class="table table-bordered table-size-chart text-center align-middle mb-0">
                <thead>
                    <tr>
                        <th>SIZE</th>
                        <th>PANJANG (P)</th>
                        <th>LEBAR (L)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="fw-bold">S</td><td>66 - 68 cm</td><td>48 - 50 cm</td></tr>
                    <tr><td class="fw-bold">M</td><td>69 - 71 cm</td><td>51 - 53 cm</td></tr>
                    <tr><td class="fw-bold">L</td><td>72 - 74 cm</td><td>54 - 57 cm</td></tr>
                    <tr><td class="fw-bold">XL</td><td>75 - 77 cm</td><td>58 - 61 cm</td></tr>
                    <tr><td class="fw-bold">XXL</td><td>78 - 80 cm</td><td>62 - 65 cm</td></tr>
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal How To Order Lewat WA -->
<div class="modal fade" id="modalOrder" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-white border-secondary">
      <div class="modal-header border-secondary">
        <h5 class="modal-title font-bold"><i class="bi bi-whatsapp text-success me-2"></i>CARA PEMESANAN VIA WHATSAPP</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body small">
        <ol class="ps-3 mb-0">
          <li class="mb-2">Cari dan pilih item barang thrift yang kamu suka di katalog.</li>
          <li class="mb-2">Cek detail ukuran ($P \times L$) dengan membuka menu **SIZE CHART**.</li>
          <li class="mb-2">Klik foto/kotak produk yang kamu pilih. Kamu akan otomatis diarahkan ke chat WhatsApp Admin ARBMD Thrift.</li>
          <li class="mb-2">Kirim format pesan otomatis yang sudah terisi untuk konfirmasi ketersediaan stok.</li>
          <li>Lakukan transfer pembayaran & kirimkan bukti transfer. Barang impianmu siap dikirim!</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>