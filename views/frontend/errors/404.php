<?php
// views/frontend/errors/404.php

$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$baseUrl = rtrim($baseUrl, '/');
if ($baseUrl === '') $baseUrl = '/eventprint/public';

$title = $vars['title'] ?? 'Halaman Tidak Ditemukan';
$message = $vars['message'] ?? 'Halaman yang kamu cari tidak tersedia atau sudah dipindahkan.';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="p-4 border rounded-3 bg-white shadow-sm">
        <h1 class="mb-2" style="font-size:2rem;">404</h1>
        <h2 class="h4 mb-3"><?= htmlspecialchars($title) ?></h2>
        <p class="text-muted mb-4"><?= htmlspecialchars($message) ?></p>

        <div class="d-flex gap-2 flex-wrap">
          <a href="<?= $baseUrl ?>/" class="btn btn-primary">Kembali ke Home</a>
          <a href="<?= $baseUrl ?>/products" class="btn btn-outline-primary">Lihat Produk</a>
        </div>
      </div>
    </div>
  </div>
</div>
