<?php
// app/views/frontend/home/index.php
$baseUrl  = $baseUrl ?? '/eventprint/public';
?><!-- Banner Carousel -->
<section class="section">
  <div class="container">
    <div id="bannerCarousel" class="banner-carousel"></div>
  </div>
</section>

<!-- Kata Mereka (Testimonials) -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Kata Mereka</h2>
    </div>
    <div id="testimonials" class="grid grid-4"></div>
  </div>
</section>

<!-- Category Icon Row -->
<section class="category-bar">
  <div id="categories" class="category-list"></div>
</section>

<!-- Produk Unggulan Kami -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Produk Unggulan Kami</h2>
    </div>
    <div id="featuredProducts" class="grid grid-4"></div>
  </div>
</section>

<!-- Print Warna & Hitam Putih -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Print Warna & Hitam Putih</h2>
    </div>
    <div id="printProducts" class="grid grid-4"></div>
  </div>
</section>

<!-- Cetak Media Promosi -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Cetak Media Promosi</h2>
    </div>
    <div id="mediaProducts" class="grid grid-4"></div>
  </div>
</section>

<!-- Kontak Kami + Lokasi GMaps -->
<section class="section">
  <div class="container">
    <div class="contact-row">
      <div class="contact-box">
        <span>Lokasi GMaps</span>
      </div>
      <div class="contact-info">
        <h3>Kontak Kami</h3>
        <div id="contactInfo"></div>
        <!-- ganti link .html ke route PHP -->
        <a href="<?= rtrim($vars['baseUrl'] ?? '/eventprint/public','/') ?>/contact" class="btn btn-primary mt-3">Hubungi kami</a>
      </div>
    </div>
  </div>
</section>

<!-- CTA Bar -->
<section>
  <div class="container">
    <div class="cta-row">
      <a href="<?= rtrim($vars['baseUrl'] ?? '/eventprint/public','/') ?>/blog" class="cta-block">Baca Artikel!</a>
      <a href="<?= rtrim($vars['baseUrl'] ?? '/eventprint/public','/') ?>/our-home" class="cta-block">Kenapa Pilih Kami?</a>
    </div>
  </div>
</section>
