-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Okt 2026 pada 05.32
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `arbmd_thrift`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Jaket'),
(2, 'Hoodie'),
(3, 'Crewneck'),
(4, 'T-Shirt'),
(5, 'Celana');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `brand` varchar(50) DEFAULT 'Unbranded',
  `ukuran` varchar(30) NOT NULL,
  `harga` int(11) NOT NULL,
  `kondisi` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) NOT NULL DEFAULT 'default.jpg',
  `status` enum('Ready','Sold Out') DEFAULT 'Ready',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id_produk`, `id_kategori`, `nama_produk`, `brand`, `ukuran`, `harga`, `kondisi`, `deskripsi`, `gambar`, `status`, `created_at`) VALUES
(1, 1, 'CARHARTT DETROIT JACKET VINTAGE DUCK CANVAS', 'Carhartt', 'L (P72 x L58)', 1250000, '9/10 Mulus Tag Lengkap', 'Canvas tebal, warna fading cakep vintage style', 'default.jpg', 'Ready', '2026-10-19 03:16:32'),
(2, 2, 'STONE ISLAND GARMENT DYED HOODIE BLACK', 'Stone Island', 'M (P68 x L54)', 1850000, '9.5/10 Like New + Patch', 'Kain tebal, patch lengan komplit, garansi authentic', 'default.jpg', 'Ready', '2026-10-19 03:16:32'),
(3, 3, 'NIKE VINTAGE CENTER SWOOSH CREWNECK', 'Nike', 'XL (P74 x L62)', 650000, '9/10 No Minus Noda', 'Bordir Swoosh tengah, warna navy pekat', 'default.jpg', 'Ready', '2026-10-19 03:16:32'),
(4, 5, 'DICKIES 874 WORK PANTS KHAKI ORIGINAL', 'Dickies', 'L (Waist 32 P102)', 450000, '9.5/10 Mulus Ban Keras', 'Celana kerja klasik Dickies 874, bahan tebal tegak', 'default.jpg', 'Ready', '2026-10-19 03:16:32'),
(5, 1, 'CARHARTT WIP WORK IN PROGRESS PARKA', 'Carhartt', 'XL (P76 x L60)', 980000, '9/10 Mulus Waterproof', 'Bahan tebal anti angin, banyak kantong tactical', 'default.jpg', 'Ready', '2026-10-19 03:16:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `username`, `password`, `no_hp`, `alamat`, `role`, `created_at`) VALUES
(1, 'Admin ARBMD', 'admin@arbmd.com', 'admin', '$2y$10$w0fP8402A6O0zH5D2p8Z0.G.6uU2Bv7zO6K2Q1h1mY3M4Q1Z1a2b3c', NULL, NULL, 'admin', '2026-10-19 01:45:52'),
(2, 'Pembeli Pertama', 'user@gmail.com', 'pembeli', '$2y$10$w0fP8402A6O0zH5D2p8Z0.G.6uU2Bv7zO6K2Q1h1mY3M4Q1Z1a2b3c', NULL, NULL, 'user', '2026-10-19 01:45:52');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
