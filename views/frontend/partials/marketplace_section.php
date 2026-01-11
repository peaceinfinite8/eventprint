<?php
/**
 * Marketplace Section Partial
 * 
 * Vars:
 * $sectionTitle  (string)  - Large Heading
 * $ctaText       (string)  - Text for link "Belanja ..."
 * $bannerPos     (string)  - 'left' or 'right'
 * $theme         (string)  - 'red', 'blue', 'custom'
 * $categorySlug  (string)  - For link
 * $products      (array)   - List of products (max 6)
 * $bannerImage   (string)  - Optional image URL
 */

$posClass = ($bannerPos === 'right') ? 'banner-right' : '';
$themeClass = 'theme-' . ($theme ?? 'red');
$validProducts = array_slice($products ?? [], 0, 6);
$hasProducts = !empty($validProducts);
?>

<?php if ($hasProducts): ?>
    <section class="marketplace-container">
        <div class="marketplace-section <?= $posClass ?>">

            <!-- Banner Wrapper -->
            <div class="mp-banner-wrapper">
                <a href="<?= $baseUrl ?>/products?category=<?= $categorySlug ?>" class="mp-banner-card <?= $themeClass ?>">

                    <div class="mp-banner-content">
                        <h2 class="mp-banner-title">
                            <?= htmlspecialchars($sectionTitle) ?>
                        </h2>
                        <div class="mp-banner-subtitle">
                            <?= htmlspecialchars($ctaText) ?> <i class="fa-solid fa-arrow-right"></i>
                        </div>
                    </div>

                    <!-- Wave Decoration -->
                    <div class="mp-banner-wave"></div>

                    <!-- Optional Image -->
                    <?php if (!empty($bannerImage)): ?>
                        <img src="<?= $bannerImage ?>" alt="<?= htmlspecialchars($sectionTitle) ?>" class="mp-banner-img"
                            loading="lazy">
                    <?php else: ?>
                        <!-- Placeholder SVG or Icon if no image -->
                        <div class="mp-banner-img" style="opacity:0.2; transform: scale(1.5);">
                            <i class="fas fa-print fa-10x"></i>
                        </div>
                    <?php endif; ?>
                </a>
            </div>

            <!-- Product Grid -->
            <div class="mp-product-grid">
                <?php foreach ($validProducts as $p): ?>
                    <a href="<?= $baseUrl ?>/product/<?= $p['slug'] ?>" class="mp-product-card">
                        <div class="mp-pc-thumb">
                            <img src="<?= $p['main_image'] ?? $baseUrl . '/assets/images/no-image.png' ?>"
                                alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                        </div>
                        <div class="mp-pc-body">
                            <div class="mp-pc-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <span class="mp-pc-sold">Active</span>
                            </div>
                            <h4 class="mp-pc-title">
                                <?= htmlspecialchars($p['name']) ?>
                            </h4>
                            <div class="mp-pc-price">Rp
                                <?= number_format($p['price'], 0, ',', '.') ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

        </div>
    </section>
<?php endif; ?>