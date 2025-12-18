<?php
/**
 * 404 Error Page
 * Display when page not found
 */
?>

<div class="error-page">
  <div class="container">
    <div class="error-content">
      <h1 class="error-code">404</h1>
      <h2 class="error-title">Halaman Tidak Ditemukan</h2>
      <p class="error-message">
        Maaf, halaman yang Anda cari tidak dapat ditemukan.
      </p>
      <div class="error-actions">
        <a href="<?= baseUrl('/') ?>" class="btn btn-primary">
          Kembali ke Homepage
        </a>
        <a href="<?= baseUrl('/products') ?>" class="btn btn-outline">
          Lihat Produk
        </a>
      </div>
    </div>
  </div>
</div>

<style>
  .error-page {
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 0;
  }

  .error-content {
    text-align: center;
    max-width: 600px;
  }

  .error-code {
    font-size: 120px;
    font-weight: bold;
    color: #00AEEF;
    margin: 0;
    line-height: 1;
  }

  .error-title {
    font-size: 32px;
    margin: 20px 0;
    color: #333;
  }

  .error-message {
    font-size: 18px;
    color: #666;
    margin-bottom: 30px;
  }

  .error-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
  }
</style>