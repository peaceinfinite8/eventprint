<?php
/**
 * Homepage View
 * Displays hero carousel, categories, featured products, testimonials
 */
?>

<!-- Hero Carousel -->
<?php if (!empty($heroSlides)): ?>
    <section class="banner-carousel hero--fullbleed">
        <div class="carousel-container">
            <?php foreach ($heroSlides as $index => $slide): ?>
                <div class="carousel-slide <?= $index === 0 ? 'active' : '' ?>">
                    <?php if (!empty($slide['image'])): ?>
                        <img src="<?= imageUrl($slide['image']) ?>" alt="<?= e($slide['title']) ?>" class="slide-bg">
                    <?php endif; ?>
                    <div class="container slide-content">
                        <?php if (!empty($slide['badge'])): ?>
                            <span class="slide-badge"><?= e($slide['badge']) ?></span>
                        <?php endif; ?>
                        <h1 class="slide-title"><?= e($slide['title']) ?></h1>
                        <?php if (!empty($slide['subtitle'])): ?>
                            <p class="slide-subtitle"><?= e($slide['subtitle']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($slide['cta_text']) && !empty($slide['cta_link'])): ?>
                            <?php
                            // Check if CTA link is absolute URL or relative path
                            $ctaUrl = $slide['cta_link'];
                            if (!preg_match('/^https?:\/\//i', $ctaUrl)) {
                                // Relative path - prepend baseUrl
                                $ctaUrl = baseUrl($ctaUrl);
                            }
                            // If it's already a full URL, use it as-is
                            ?>
                            <a href="<?= e($ctaUrl) ?>" class="btn btn-primary btn-lg">
                                <?= e($slide['cta_text']) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (count($heroSlides) > 1): ?>
                <!-- Carousel Controls -->
                <button class="carousel-control prev" aria-label="Previous slide">❮</button>
                <button class="carousel-control next" aria-label="Next slide">❯</button>

                <!-- Carousel Indicators -->
                <div class="carousel-indicators">
                    <?php foreach ($heroSlides as $index => $slide): ?>
                        <button class="indicator-dot <?= $index === 0 ? 'active' : '' ?>" data-slide="<?= $index ?>"
                            aria-label="Go to slide <?= $index + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<!-- Category Icon Bar -->
<?php if (!empty($categories)): ?>
    <section class="category-bar--fullbleed">
        <div class="container catbar__inner">
            <h2 class="catbar__label">SERVICES</h2>

            <div class="services-wrapper">
                <button class="services-nav prev" id="servPrev" aria-label="Previous">❮</button>
                <div class="category-list services-track" data-services-track>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?= baseUrl('/products?category=' . e($cat['slug'])) ?>" class="category-item">
                            <span class="category-icon">
                                <?php
                                $icon = $cat['icon'] ?? '🖨️';
                                // Check if icon is a FontAwesome class (starts with 'fa')
                                if (strpos($icon, 'fa-') === 0 || strpos($icon, 'fa ') === 0) {
                                    echo '<i class="fas ' . e($icon) . '"></i>';
                                } else {
                                    // Treat as emoji or plain text
                                    echo e($icon);
                                }
                                ?>
                            </span>
                            <span class="category-name"><?= e($cat['name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <button class="services-nav next" id="servNext" aria-label="Next">❯</button>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Print Warna & Hitam Putih -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Print Warna & Hitam Putih</h2>
        </div>
        <div id="printProducts" class="grid grid-4">
            <?php
            // Display up to 4 products (filter by category or show featured)
            $displayProducts = array_slice($featuredProducts ?? [], 0, 4);
            foreach ($displayProducts as $product):
                ?>
                <div class="product-card">
                    <div class="product-card-image">
                        <a href="<?= baseUrl('/products/' . e($product['slug'])) ?>">
                            <img src="<?= imageUrl($product['thumbnail'] ?? '') ?>" alt="<?= e($product['name']) ?>">
                        </a>
                    </div>
                    <div class="product-card-info">
                        <h3 class="product-card-title">
                            <a href="<?= baseUrl('/products/' . e($product['slug'])) ?>">
                                <?= e($product['name']) ?>
                            </a>
                        </h3>
                        <?php if (isset($product['base_price'])): ?>
                            <p class="product-card-price"><?= formatPrice($product['base_price']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
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
            <?php
            // Display next 4 products
            $displayProducts = array_slice($featuredProducts ?? [], 4, 4);
            foreach ($displayProducts as $product):
                ?>
                <div class="product-card">
                    <div class="product-card-image">
                        <a href="<?= baseUrl('/products/' . e($product['slug'])) ?>">
                            <img src="<?= imageUrl($product['thumbnail'] ?? '') ?>" alt="<?= e($product['name']) ?>">
                        </a>
                    </div>
                    <div class="product-card-info">
                        <h3 class="product-card-title">
                            <a href="<?= baseUrl('/products/' . e($product['slug'])) ?>">
                                <?= e($product['name']) ?>
                            </a>
                        </h3>
                        <?php if (isset($product['base_price'])): ?>
                            <p class="product-card-price"><?= formatPrice($product['base_price']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonials (Kata Mereka) -->
<?php if (!empty($testimonials)): ?>
    <section class="section testimonials-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Kata Mereka</h2>
            </div>
            <div class="testimonials-grid">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="testimonial-card">
                        <?php if (!empty($testimonial['photo'])): ?>
                            <img src="<?= imageUrl($testimonial['photo'], 'frontend/images/avatar-placeholder.jpg') ?>"
                                alt="<?= e($testimonial['name']) ?>" class="testimonial-photo">
                        <?php endif; ?>
                        <div class="testimonial-rating">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <span class="star <?= $i < ($testimonial['rating'] ?? 5) ? 'filled' : '' ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <p class="testimonial-message"><?= e($testimonial['message']) ?></p>
                        <div class="testimonial-author">
                            <strong><?= e($testimonial['name']) ?></strong>
                            <?php if (!empty($testimonial['position'])): ?>
                                <span class="testimonial-position"><?= e($testimonial['position']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Produk Unggulan Kami -->
<?php if (!empty($featuredProducts)): ?>
    <section class="section section-highlight">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Produk Unggulan Kami</h2>
            </div>
            <div class="grid grid-4">
                <?php foreach ($featuredProducts as $product): ?>
                    <a href="<?= baseUrl('/products/' . e($product['slug'])) ?>" class="product-card">
                        <div class="product-image">
                            <img src="<?= imageUrl($product['thumbnail'], 'frontend/images/product-placeholder.jpg') ?>"
                                alt="<?= e($product['name']) ?>" loading="lazy">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= e($product['name']) ?></h3>
                            <p class="product-price"><?= formatPrice($product['base_price']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Why Choose + Mini Banner -->
<section class="why-choose-section">
    <div class="container">
        <div class="why-grid">
            <div class="why-col">
                <h3>Kenapa Pilih EventPrint?</h3>
                <ul class="why-list">
                    <li>✓ Kualitas cetak terjamin</li>
                    <li>✓ Harga kompetitif</li>
                    <li>✓ Pengerjaan cepat</li>
                    <li>✓ Gratis konsultasi desain</li>
                </ul>
            </div>
            <div class="why-col cta-col">
                <a href="<?= baseUrl($cta['left_link'] ?? 'blog') ?>"
                    class="btn btn-outline"><?= e($cta['left_text'] ?? 'Baca Artikel!') ?></a>
                <a href="<?= baseUrl($cta['right_link'] ?? 'our-home') ?>"
                    class="btn btn-primary"><?= e($cta['right_text'] ?? 'Kenapa Pilih Kami?') ?></a>
            </div>
        </div>
    </div>
</section>

<!-- Add carousel functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const carousel = document.querySelector('.carousel-container');
        if (!carousel) return;

        const slides = carousel.querySelectorAll('.carousel-slide');
        const dots = carousel.querySelectorAll('.indicator-dot');
        const prevBtn = carousel.querySelector('.prev');
        const nextBtn = carousel.querySelector('.next');

        let currentSlide = 0;
        const totalSlides = slides.length;

        if (totalSlides <= 1) return;

        function showSlide(n) {
            slides.forEach(s => s.classList.remove('active'));
            dots.forEach(d => d.classList.remove('active'));

            currentSlide = (n + totalSlides) % totalSlides;
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        function prevSlide() {
            showSlide(currentSlide - 1);
        }

        // Event listeners
        if (prevBtn) prevBtn.addEventListener('click', prevSlide);
        if (nextBtn) nextBtn.addEventListener('click', nextSlide);

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => showSlide(index));
        });

        // Auto-advance every 5 seconds
        setInterval(nextSlide, 5000);
    });
</script>