<?php
// views/frontend/home/index.php
$baseUrl = $baseUrl ?? '/eventprint/public';
?>

<!-- Banner Carousel -->
<div id="bannerCarousel" class="banner-carousel hero--fullbleed">
  <!-- Rendered by JS -->
</div>

<!-- Category Icon Bar (Moved & Full Bleed) -->
<section class="category-bar--fullbleed">
  <div class="container catbar__inner">
    <h2 class="catbar__label">SERVICES</h2>

    <div id="categories" class="category-list services-track" data-services-track>
      <!-- Rendered by JS -->
    </div>
  </div>
</section>

<!-- Produk Unggulan Kami -->
<section class="section section-highlight">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Produk Unggulan Kami</h2>
    </div>
    <div class="featured-carousel-wrapper">
      <button class="carousel-btn prev" id="featuredPrev" aria-label="Previous">❮</button>
      <div class="carousel-viewport" id="featuredViewport">
        <div id="featuredProducts" class="carousel-track">
          <!-- Rendered by JS -->
        </div>
      </div>
      <button class="carousel-btn next" id="featuredNext" aria-label="Next">❯</button>
    </div>
  </div>
</section>

<!-- Print Warna & Hitam Putih -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Print Warna & Hitam Putih</h2>
    </div>
    <div id="printProducts" class="grid grid-4">
      <!-- Rendered by JS -->
    </div>
  </div>
</section>

<!-- Cetak Media Promosi -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Cetak Media Promosi</h2>
    </div>
    <div id="mediaProducts" class="grid grid-4">
      <!-- Rendered by JS -->
    </div>
  </div>
</section>

<!-- Merchandise & Souvenir -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Merchandise & Souvenir</h2>
    </div>
    <div id="merchProducts" class="grid grid-4">
      <!-- Rendered by JS -->
    </div>
  </div>
</section>


<!-- Kata Mereka (Testimonials) -->
<section class="section ep-testimonials">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Kata Mereka</h2>
      <p class="section-subtitle">Apa kata pelanggan tentang layanan kami</p>
    </div>
    <div class="ep-testimonials-wrapper">
      <button class="ep-nav-btn prev" id="testiPrev" aria-label="Previous">❮</button>
      <div id="testimonialsContainer" class="ep-testimonials-track">
        <!-- Rendered by JS -->
      </div>
      <button class="ep-nav-btn next" id="testiNext" aria-label="Next">❯</button>
    </div>
  </div>
</section>

<!-- Why Choose + Mini Banner -->
<section class="why-choose-section">
  <div class="container" id="whyChooseSection">
    <!-- Rendered by JS -->
  </div>
</section>

<!-- Initialize Homepage -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (typeof initHomePage === 'function') {
      initHomePage();
    }
  });
</script>