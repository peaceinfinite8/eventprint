<?php
// views/frontend/home/index.php
$baseUrl = $baseUrl ?? '/eventprint/public';
?>

<!-- Banner Carousel -->
<section class="section">
  <div class="container">
    <div id="bannerCarousel" class="banner-carousel"><!-- rendered by JS --></div>
  </div>
</section>

<!-- Kata Mereka -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Kata Mereka</h2>
    </div>
    <div id="testimonials" class="grid grid-4"><!-- rendered by JS --></div>
  </div>
</section>

<!-- Category Icon Row -->
<section class="category-bar">
  <div id="categories" class="category-list"><!-- rendered by JS --></div>
</section>

<!-- Produk Unggulan Kami -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Produk Unggulan Kami</h2>
    </div>
    <div id="featuredProducts" class="grid grid-4"><!-- rendered by JS --></div>
  </div>
</section>

<!-- Print -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Print Warna & Hitam Putih</h2>
    </div>
    <div id="printProducts" class="grid grid-4"><!-- rendered by JS --></div>
  </div>
</section>

<!-- Media Promosi -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Cetak Media Promosi</h2>
    </div>
    <div id="mediaProducts" class="grid grid-4"><!-- rendered by JS --></div>
  </div>
</section>

<!-- Kontak Kami -->
<section class="section">
  <div class="container">
    <div class="contact-row">
      <div class="contact-box" id="homeMap">
        <span>Lokasi GMaps</span>
      </div>


      <div class="contact-info">
        <h3>Kontak Kami</h3>
        <div id="contactInfo"><!-- rendered by JS --></div>

        <a href="<?= $baseUrl ?>/contact" class="btn btn-primary mt-3">Hubungi kami</a>
      </div>
    </div>
  </div>
</section>

<!-- CTA Bar -->
<section>
  <div class="container">
    <div class="cta-row">
      <a id="ctaLeft"  href="#" class="cta-block">Baca Artikel!</a>
      <a id="ctaRight" href="#" class="cta-block">Kenapa Pilih Kami?</a>
    </div>
  </div>
</section>

