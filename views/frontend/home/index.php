<?php
// views/frontend/home/index.php
$baseUrl = $baseUrl ?? '/eventprint';
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

<!-- Custom CSS for Marketplace Layout -->
<link rel="stylesheet" href="<?= $baseUrl ?>/assets/frontend/css/home-marketplace.css?v=<?= time() ?>">

<!-- ==============================================
     DYNAMIC SECTIONS LOOP
     ============================================== -->
<?php if (!empty($dynamicSections)): ?>
  <?php foreach ($dynamicSections as $sec): ?>
    <?php
    $reverseClass = ($sec['layout'] === 'reverse') ? 'reverse' : '';

    // Theme Logic: Support Hex or Legacy Class
    $themeValue = $sec['theme'] ?? 'red';
    $themeClass = '';
    $customGradientStyle = '';

    if (strpos($themeValue, '#') === 0) {
      // It is a Hex Color (Custom)
      // Generate a simple gradient: Color -> Darker Variant
      // Simple darker color calculation for PHP 7 lines
      // (We assume valid 6-char hex for simplicity or fallback)
      $hex = ltrim($themeValue, '#');
      if (strlen($hex) == 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
      }
      $r = hexdec(substr($hex, 0, 2));
      $g = hexdec(substr($hex, 2, 2));
      $b = hexdec(substr($hex, 4, 2));

      // Darken by 20%
      $r2 = max(0, $r - 50);
      $g2 = max(0, $g - 50);
      $b2 = max(0, $b - 50);

      $color2 = sprintf("#%02x%02x%02x", $r2, $g2, $b2);

      $customGradientStyle = "background: linear-gradient(135deg, $themeValue, $color2) !important;";
    } else {
      // Legacy Class
      $themeClass = 'theme-' . $themeValue;
    }

    $products = array_slice($sec['products'], 0, 6);

    // Custom Text Logic (No Fallbacks for Desc/Button as per request)
    $bannerTitle = !empty($sec['custom_title']) ? $sec['custom_title'] : $sec['category_name'];
    $bannerDesc = $sec['custom_description'] ?? '';
    $bannerBtn = $sec['custom_button_text'] ?? '';
    ?>
    <section class="section">
      <div class="container">
        <div class="section-split-layout <?= $reverseClass ?>">

          <!-- Banner Card -->
          <?php
          $bgStyle = '';
          if (!empty($sec['image'])) {
            // Determine full URL for image
            $imgPath = $baseUrl . '/' . ltrim($sec['image'], '/');
            $overlayStyle = $sec['overlay_style'] ?? 'dark';

            if ($overlayStyle === 'light') {
              // No dark overlay ("Bright" / "Asli")
              $bgStyle = 'style="background: url(\'' . $imgPath . '\') center/cover no-repeat !important;"';
            } else {
              // Default Dark Overlay (for contrast)
              $bgStyle = 'style="background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.6) 100%), url(\'' . $imgPath . '\') center/cover no-repeat !important;"';
            }
          } elseif ($customGradientStyle) {
            // Use Custom Color Gradient if no image is present
            $bgStyle = 'style="' . $customGradientStyle . '"';
          }
          ?>
          <div class="section-banner-card <?= $themeClass ?>" <?= $bgStyle ?>>
            <!-- Click Overlay (Partial Height to exclude bottom curve) -->
            <a href="<?= $baseUrl ?>/products?category=<?= $sec['category_slug'] ?>" class="banner-click-overlay"
              style="position: absolute; top: 0; left: 0; width: 100%; height: calc(100% - 140px); z-index: 5;"
              aria-label="Belanja <?= htmlspecialchars($sec['category_name']) ?>"></a>

            <h3><?= htmlspecialchars($bannerTitle) ?></h3>

            <?php if (!empty($bannerDesc)): ?>
              <p class="mb-4 text-white opacity-75"><?= htmlspecialchars($bannerDesc) ?></p>
            <?php endif; ?>

            <?php if (!empty($bannerBtn)): ?>
              <span class="banner-link">
                <?= htmlspecialchars($bannerBtn) ?> <i class="fa-solid fa-arrow-right"></i>
              </span>
            <?php endif; ?>
          </div>

          <!-- Product Grid -->
          <div class="section-product-grid">
            <?php foreach ($products as $p): ?>
              <?php include __DIR__ . '/../partials/product_card.php'; ?>
            <?php endforeach; ?>
          </div>

        </div>
      </div>
    </section>
  <?php endforeach; ?>
<?php endif; ?>


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