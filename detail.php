<?php
// Contoh format teks pesan ke WhatsApp
$nomor_wa = "628xxxxxxxxxx"; // Ganti dengan nomor WA admin
$pesan = "Halo Admin ARBMD Thrift, saya ingin membeli produk ini:\n\n"
       . "*Nama Produk:* " . $data['nama_produk'] . "\n"
       . "*Brand:* " . $data['brand'] . "\n"
       . "*Ukuran:* " . $data['ukuran'] . "\n"
       . "*Harga:* Rp " . number_format($data['harga'], 0, ',', '.') . "\n\n"
       . "Apakah barang ini masih ready?";

$link_wa = "https://api.whatsapp.com/send?phone=" . $nomor_wa . "&text=" . urlencode($pesan);
?>

<a href="<?= $link_wa; ?>" target="_blank" class="btn btn-success">
   <i class="bi bi-whatsapp"></i> Beli via WhatsApp
</a>