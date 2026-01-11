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
                        <!-- Slide 1 -->
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
                        <!-- Slide 2 -->
                        <div class="hero-slide"
                            style="background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%); display:none;">
                            <div class="hero-promo-badge">New</div>
                            <div class="hero-content-compact">
                                <p class="hero-label">MERCH</p>
                                <h3 class="hero-title-compact">KANTOR</h3>
                                <p class="hero-desc">Cetak Logo Perusahaan</p>
                                <a href="/products" class="hero-btn">Lihat</a>
                            </div>
                        </div>
                        <!-- Slide 3 -->
                        <div class="hero-slide"
                            style="background: linear-gradient(135deg, #fca5a5 0%, #ef4444 100%); display:none;">
                            <div class="hero-content-compact">
                                <h3 class="hero-title-compact">DISKON</h3>
                                <p class="hero-desc">Hingga 50% Hari Ini</p>
                                <a href="/products" class="hero-btn">Cek Promo</a>
                            </div>
                        </div>
                    </div>
                    <!-- Controls -->
                    <button class="hero-carousel-btn hero-prev"><i class="fas fa-chevron-left"></i></button>
                    <button class="hero-carousel-btn hero-next"><i class="fas fa-chevron-right"></i></button>
                    <!-- Dots -->
                    <div class="hero-carousel-dots">
                        <div class="hero-carousel-dot active" data-slide="0"></div>
                        <div class="hero-carousel-dot" data-slide="1"></div>
                        <div class="hero-carousel-dot" data-slide="2"></div>
                    </div>
                </div>
            </div>

            <!-- BANNER 3: Product Banner (BOTTOM RIGHT) - CAROUSEL -->
            <div class="hero-banner hero-banner-small hero-banner-bottom">
                <div class="hero-carousel-wrapper">
                    <div class="hero-carousel" id="heroBanner3">
                        <!-- Slide 1 -->
                        <div class="hero-slide active"
                            style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
                            <div class="hero-content-compact">
                                <p class="hero-label">Order Cetak</p>
                                <h3 class="hero-title-compact">Semudah Chat WA</h3>
                                <a href="https://wa.me/628123681590" class="hero-wa-btn">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                    </svg>
                                    08123681590
                                </a>
                                <div class="hero-features-list">
                                    <div class="hero-feature-item">
                                        <i class="fas fa-star"></i> <span>XY Standar</span>
                                    </div>
                                    <div class="hero-feature-item">
                                        <i class="fas fa-palette"></i> <span>CMYK</span>
                                    </div>
                                    <div class="hero-feature-item">
                                        <i class="fas fa-box"></i> <span>Packaging</span>
                                    </div>
                                    <div class="hero-feature-item">
                                        <i class="fas fa-shield-alt"></i> <span>QC Ketat</span>
                                    </div>
                                </div>
                            </div>
                            <div class="hero-image-compact">
                                <img src="/assets/frontend/images/hero-woman-phone.png" alt="Woman with phone"
                                    onerror="this.style.display='none';" />
                            </div>
                        </div>
                        <!-- Slide 2 -->
                        <div class="hero-slide"
                            style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); display:none;">
                            <div class="hero-content-compact">
                                <p class="hero-label">LAYANAN</p>
                                <h3 class="hero-title-compact">EKSPRESS</h3>
                                <p class="hero-desc">Cetak Ditunggu, Hasil Kilat</p>
                                <a href="/contact" class="hero-wa-btn">Hubungi Kami</a>
                            </div>
                        </div>
                        <!-- Slide 3 -->
                        <div class="hero-slide"
                            style="background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%); display:none;">
                            <div class="hero-content-compact">
                                <h3 class="hero-title-compact">GARANSI</h3>
                                <p class="hero-desc">Kepuasan Pelanggan Prioritas</p>
                                <div class="hero-features-list">
                                    <div class="hero-feature-item">
                                        <i class="fas fa-check-circle"></i> <span>Revisi Gratis</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Controls -->
                    <button class="hero-carousel-btn hero-prev"><i class="fas fa-chevron-left"></i></button>
                    <button class="hero-carousel-btn hero-next"><i class="fas fa-chevron-right"></i></button>
                    <!-- Dots -->
                    <div class="hero-carousel-dots">
                        <div class="hero-carousel-dot active" data-slide="0"></div>
                        <div class="hero-carousel-dot" data-slide="1"></div>
                        <div class="hero-carousel-dot" data-slide="2"></div>
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