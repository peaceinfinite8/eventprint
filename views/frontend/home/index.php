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

    <div class="services-wrapper">
      <button class="services-nav prev" id="servPrev" aria-label="Previous">❮</button>
      <div id="categories" class="category-list services-track" data-services-track>
        <!-- Rendered by JS -->
      </div>
      <button class="services-nav next" id="servNext" aria-label="Next">❯</button>
    </div>
  </div>
</section>

<!-- Produk Unggulan Kami -->
<section class="section section-highlight">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Produk Unggulan Kami</h2>
    </div>
    <div id="featuredProducts" class="grid grid-4">
      <!-- Rendered by JS -->
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

<!-- Why Choose + Mini Banner -->
<section class="why-choose-section">
  <div class="container" id="whyChooseSection">
    <!-- Rendered by JS -->
  </div>
</section>
