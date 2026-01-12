<?php
/**
 * Homepage View (1:1 Parity with Reference)
 * Reference: frontend/public/views/home.html
 */
?>

<!-- Banner Carousel (REFERENCE: id="bannerCarousel") -->
<div id="bannerCarousel" class="banner-carousel hero--fullbleed">
    <!-- Rendered by JS in renderHome.js -->
</div>

<!-- Category Icon Bar (REFERENCE: category-bar--fullbleed) -->
<section class="category-bar--fullbleed">
    <div class="container catbar__inner">
        <h2 class="catbar__label">SERVICES</h2>
        <!-- Live Search Hint (hidden by default, shown when searching) -->
        <p id="servicesSearchHint" class="services-search-hint" hidden></p>

        <div class="services-wrapper">
            <button type="button" class="services-nav prev" id="servPrev" aria-label="Previous">‹</button>
            <div id="categories" class="category-list services-track" data-services-track>
                <!-- Rendered by JS -->
            </div>
            <button type="button" class="services-nav next" id="servNext" aria-label="Next">›</button>
        </div>
    </div>
</section>

<!-- Produk Unggulan Kami (REFERENCE: with carousel wrapper) -->
<section class="section section-highlight">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Produk Unggulan Kami</h2>
        </div>
        <div class="featured-carousel-wrapper">
            <button type="button" class="carousel-btn prev" id="featuredPrev" aria-label="Previous">❮</button>
            <div class="carousel-viewport" id="featuredViewport">
                <div id="featuredProducts" class="carousel-track">
                    <!-- Rendered by JS -->
                </div>
            </div>
            <button type="button" class="carousel-btn next" id="featuredNext" aria-label="Next">❯</button>
        </div>
    </div>
</section>

<!-- Dynamic Sections (Rendered from Admin Config) -->
<?php if (!empty($dynamicSections)): ?>
    <?php foreach ($dynamicSections as $sec): ?>
        <?php
        $themeClass = 'theme-' . ($sec['theme'] ?? 'red');
        $layoutClass = ($sec['layout'] ?? 'standard') === 'reverse' ? 'row-reverse' : '';
        $products = $sec['products'] ?? [];
        ?>
        <section class="section section-highlight <?= $themeClass ?>">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title"><?= htmlspecialchars($sec['category_name'] ?? 'Section') ?></h2>
                    <?php if (!empty($sec['category_slug'])): ?>
                        <a href="<?= $baseUrl ?>/products?category=<?= $sec['category_slug'] ?>"
                            class="btn btn-sm btn-outline-light rounded-pill px-4">
                            Lihat Semua <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Server-Side Rendered Grid -->
                <div class="grid grid-4 g-4">
                    <?php if (empty($products)): ?>
                        <div class="col-12 text-center text-white py-5">
                            <p class="mb-0">Belum ada produk di kategori ini.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <?php
                            // Prepare Data
                            $pId = $p['id'];
                            $pSlug = $p['slug'];
                            $pName = htmlspecialchars($p['name']);
                            $pImg = $p['thumbnail'] ?? ($p['image'] ?? '');
                            $pPrice = (float) ($p['base_price'] ?? 0);
                            $pStock = (int) ($p['stock'] ?? 0);
                            $isOOS = $pStock <= 0;

                            // Discount Logic
                            $discType = $p['discount_type'] ?? 'none';
                            $discVal = (float) ($p['discount_value'] ?? 0);
                            $finalPrice = $pPrice;
                            $hasDisc = false;
                            $badgeHtml = '';

                            if (!$isOOS && $discVal > 0) {
                                if ($discType === 'percent' || $discType === 'percentage') {
                                    $discAmount = ($pPrice * $discVal) / 100;
                                    $finalPrice = $pPrice - $discAmount;
                                    $hasDisc = true;
                                    $badgeHtml = '<div style="position: absolute; top: 10px; right: 10px; background: #ef4444; color: white; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 2;">Hemat ' . round($discVal) . '%</div>';
                                } elseif ($discType === 'fixed') {
                                    $finalPrice = $pPrice - $discVal;
                                    $hasDisc = true;
                                    // Optional: Fixed badge
                                }
                            }
                            ?>
                            <a href="<?= $baseUrl ?>/products/<?= $pSlug ?>"
                                class="product-card-link <?= $isOOS ? 'out-of-stock' : '' ?>">
                                <div class="product-card <?= $isOOS ? 'out-of-stock' : '' ?>" style="position: relative;">
                                    <?= $badgeHtml ?>
                                    <div class="product-card-image">
                                        <?php if ($pImg): ?>
                                            <img src="<?= $pImg ?>" alt="<?= $pName ?>" loading="lazy">
                                        <?php else: ?>
                                            <span>Gambar Produk</span>
                                        <?php endif; ?>
                                        <?php if ($isOOS): ?>
                                            <div class="out-of-stock-overlay">Stok Habis</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-card-info">
                                        <h3 class="product-card-name"><?= $pName ?></h3>
                                        <?php if ($isOOS): ?>
                                            <p class="product-card-price out-of-stock">
                                                <span class="strikethrough">Rp <?= number_format($pPrice, 0, ',', '.') ?></span>
                                                <span class="stock-label">Stok Habis</span>
                                            </p>
                                        <?php elseif ($hasDisc): ?>
                                            <p class="product-card-price">
                                                <span
                                                    style="text-decoration: line-through; color: #9ca3af; font-size: 0.875rem; margin-right: 4px;">Rp
                                                    <?= number_format($pPrice, 0, ',', '.') ?></span>
                                                <span style="color: #ef4444; font-weight: bold;">Rp
                                                    <?= number_format($finalPrice, 0, ',', '.') ?></span>
                                            </p>
                                        <?php else: ?>
                                            <p class="product-card-price">Rp <?= number_format($pPrice, 0, ',', '.') ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endforeach; ?>
<?php else: ?>
    <!-- Fallback if no dynamic sections mapped -->
    <section class="section">
        <div class="container text-center py-5">
            <p class="text-muted">Kategori belum dikonfigurasi di Admin Panel.</p>
        </div>
    </section>
<?php endif; ?>

<!-- Kata Mereka (Testimonials) - REFERENCE STRUCTURE -->
<section class="section ep-testimonials">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Kata Mereka</h2>
            <p class="section-subtitle">Apa kata pelanggan tentang layanan kami</p>
        </div>
        <div class="ep-testimonials-wrapper">
            <button type="button" class="ep-nav-btn prev" id="testiPrev" aria-label="Previous">❮</button>
            <div id="testimonialsContainer" class="ep-testimonials-track">
                <!-- Rendered by JS -->
            </div>
            <button type="button" class="ep-nav-btn next" id="testiNext" aria-label="Next">❯</button>
        </div>
    </div>
</section>

<!-- Why Choose + Mini Banner -->
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