<?php
/**
 * Homepage View (1:1 Parity with Reference)
 * Reference: frontend/public/views/home.html
 */
?>

<!-- Hero Section: 3 Banner Layout (Opsi B: Only Banner 1 Carousel) -->
<section class="hero-section-3banner">
    <div class="container">
        <div class="hero-grid-3">
            <!-- BANNER 1: Large Promotion Banner (LEFT) - CAROUSEL -->
            <div class="hero-banner hero-banner-large">
                <div class="hero-carousel-wrapper">
                    <div class="hero-carousel" id="heroBanner1">
                        <?php if (!empty($heroSlides)): ?>
                            <?php foreach ($heroSlides as $i => $slide): ?>
                                <div class="hero-slide <?= $i === 0 ? 'active' : '' ?>"
                                    style="padding: 0; background: #eee; display: <?= $i === 0 ? 'flex' : 'none' ?>;">
                                    <?php $link = !empty($slide['cta_link']) ? $slide['cta_link'] : '#'; ?>

                                    <?php if ($link !== '#'): ?>
                                        <a href="<?= e($link) ?>" style="display:block; width:100%; height:100%;">
                                            <img src="<?= uploadUrl($slide['image']) ?>" alt="<?= e($slide['title']) ?>"
                                                style="width:100%; height:100%; object-fit: cover; display:block;"
                                                onerror="this.parentElement.style.display='none';" />
                                        </a>
                                    <?php else: ?>
                                        <img src="<?= uploadUrl($slide['image']) ?>" alt="<?= e($slide['title']) ?>"
                                            style="width:100%; height:100%; object-fit: cover; display:block;"
                                            onerror="this.style.display='none';" />
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback Slide 1 (Full Image) -->
                            <div class="hero-slide active" style="padding: 0; background: #dae0e5;">
                                <a href="/products" style="display:block; width:100%; height:100%;">
                                    <img src="<?= assetUrl('frontend/images/hero-woman-laptop.jpg') ?>" alt="Hero 1"
                                        style="width:100%; height:100%; object-fit: cover; display:block;" />
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Carousel Navigation -->
                    <button type="button" class="hero-carousel-btn hero-prev" id="heroPrev" aria-label="Previous">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="hero-carousel-btn hero-next" id="heroNext" aria-label="Next">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <!-- Carousel Dots -->
                    <div class="hero-carousel-dots" id="heroCarouselDots">
                        <?php if (!empty($heroSlides)): ?>
                            <?php foreach ($heroSlides as $i => $slide): ?>
                                <div class="hero-carousel-dot <?= $i === 0 ? 'active' : '' ?>" data-slide="<?= $i ?>"></div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="hero-carousel-dot active" data-slide="0"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- BANNER 2: Product Banner (TOP RIGHT) - CAROUSEL -->
            <div class="hero-banner hero-banner-small hero-banner-top">
                <div class="hero-carousel-wrapper">
                    <div class="hero-carousel" id="heroBanner2">
                        <?php if (!empty($heroSlidesRightTop)): ?>
                            <?php foreach ($heroSlidesRightTop as $i => $slide): ?>
                                <div class="hero-slide <?= $i === 0 ? 'active' : '' ?>"
                                    style="padding:0; display: <?= $i === 0 ? 'flex' : 'none' ?>;">
                                    <?php $link = !empty($slide['cta_link']) ? $slide['cta_link'] : '#'; ?>
                                    <a href="<?= e($link) ?>" style="display:block; width:100%; height:100%;">
                                        <img src="<?= uploadUrl($slide['image']) ?>" alt="<?= e($slide['title']) ?>"
                                            style="width:100%; height:100%; object-fit: cover; display:block;" />
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback Slide -->
                            <div class="hero-slide active"
                                style="background: linear-gradient(135deg, #ffc0cb 0%, #ffb6c1 100%);">
                                <div class="hero-promo-badge">30% Off</div>
                                <div class="hero-content-compact">
                                    <p class="hero-label">PILIHAN</p>
                                    <h3 class="hero-title-compact">TERBAIK</h3>
                                    <p class="hero-desc">Untuk Kebutuhan Souvenir Anda</p>
                                    <a href="/products" class="hero-btn">Shop Now</a>
                                </div>
                                <div class="hero-image-compact">
                                    <img src="/assets/frontend/images/hero-products.png" alt="Products"
                                        onerror="this.style.display='none';" />
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Controls -->
                    <button class="hero-carousel-btn hero-prev"><i class="fas fa-chevron-left"></i></button>
                    <button class="hero-carousel-btn hero-next"><i class="fas fa-chevron-right"></i></button>
                    <!-- Dots -->
                    <div class="hero-carousel-dots">
                        <?php if (!empty($heroSlidesRightTop)): ?>
                            <?php foreach ($heroSlidesRightTop as $i => $slide): ?>
                                <div class="hero-carousel-dot <?= $i === 0 ? 'active' : '' ?>" data-slide="<?= $i ?>"></div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="hero-carousel-dot active" data-slide="0"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- BANNER 3: Product Banner (BOTTOM RIGHT) - CAROUSEL -->
            <div class="hero-banner hero-banner-small hero-banner-bottom">
                <div class="hero-carousel-wrapper">
                    <div class="hero-carousel" id="heroBanner3">
                        <?php if (!empty($heroSlidesRightBottom)): ?>
                            <?php foreach ($heroSlidesRightBottom as $i => $slide): ?>
                                <div class="hero-slide <?= $i === 0 ? 'active' : '' ?>"
                                    style="padding:0; display: <?= $i === 0 ? 'flex' : 'none' ?>;">
                                    <?php $link = !empty($slide['cta_link']) ? $slide['cta_link'] : '#'; ?>
                                    <a href="<?= e($link) ?>" style="display:block; width:100%; height:100%;">
                                        <img src="<?= uploadUrl($slide['image']) ?>" alt="<?= e($slide['title']) ?>"
                                            style="width:100%; height:100%; object-fit: cover; display:block;" />
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback Slide -->
                            <div class="hero-slide active"
                                style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
                                <div class="hero-content-compact">
                                    <p class="hero-label">Order Cetak</p>
                                    <h3 class="hero-title-compact">Semudah Chat WA</h3>
                                    <a href="https://wa.me/628123681590" class="hero-wa-btn">Hubungi WA</a>
                                </div>
                                <div class="hero-image-compact">
                                    <img src="/assets/frontend/images/hero-woman-phone.png" alt="Woman"
                                        onerror="this.style.display='none';" />
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Controls -->
                    <button class="hero-carousel-btn hero-prev"><i class="fas fa-chevron-left"></i></button>
                    <button class="hero-carousel-btn hero-next"><i class="fas fa-chevron-right"></i></button>
                    <!-- Dots -->
                    <div class="hero-carousel-dots">
                        <?php if (!empty($heroSlidesRightBottom)): ?>
                            <?php foreach ($heroSlidesRightBottom as $i => $slide): ?>
                                <div class="hero-carousel-dot <?= $i === 0 ? 'active' : '' ?>" data-slide="<?= $i ?>"></div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="hero-carousel-dot active" data-slide="0"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
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

<!-- Kata Mereka (Testimonials) - REFERENCE STRUCTURE -->
<section class="section ep-testimonials">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Kata Mereka</h2>
            <p class="section-subtitle">Apa kata pelanggan tentang layanan kami</p>
        </div>
        <div class="ep-testimonials-wrapper">
            <div id="testimonialsContainer" class="ep-testimonials-track">
                <!-- Rendered by JS -->
            </div>
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