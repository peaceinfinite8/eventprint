<?php
/**
 * Products List Page
 * Display products with category filter and pagination
 */

// Ensure variables are defined
$currentCategory = $currentCategory ?? null;
?>

<div class="products-page">
    <div class="container products-container">
        <!-- Sidebar Filter -->
        <aside class="products-sidebar">
            <h3 class="sidebar-title">Kategori</h3>
            <ul class="category-filter">
                <li>
                    <a href="<?= baseUrl('/products') ?>"
                        class="category-link <?= empty($currentCategory) ? 'active' : '' ?>">
                        Semua Produk
                        <span class="count">(<?= $totalProducts ?>)</span>
                    </a>
                </li>
                <?php foreach ($categories as $cat): ?>
                    <li>
                        <a href="<?= baseUrl('/products?category=' . e($cat['slug'])) ?>"
                            class="category-link <?= $currentCategory === $cat['slug'] ? 'active' : '' ?>">
                            <?= e($cat['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <!-- Products Grid -->
        <div class="products-main">
            <?php if (!empty($products)): ?>
                <div class="products-header">
                    <h2 class="page-title">
                        <?php if ($currentCategory): ?>
                            <?php
                            $currentCat = array_filter($categories, fn($c) => $c['slug'] === $currentCategory);
                            $currentCat = reset($currentCat);
                            echo e($currentCat['name'] ?? 'Produk');
                            ?>
                        <?php else: ?>
                            Semua Produk
                        <?php endif; ?>
                    </h2>
                    <p class="products-count">Menampilkan <?= count($products) ?> dari <?= $totalProducts ?> produk</p>
                </div>

                <div class="grid grid-3">
                    <?php foreach ($products as $product): ?>
                        <a href="<?= baseUrl('/products/' . e($product['slug'])) ?>" class="product-card">
                            <div class="product-image">
                                <img src="<?= imageUrl($product['thumbnail'], 'frontend/images/product-placeholder.jpg') ?>"
                                    alt="<?= e($product['name']) ?>" loading="lazy">
                            </div>
                            <div class="product-info">
                                <?php if (!empty($product['category_name'])): ?>
                                    <span class="product-category"><?= e($product['category_name']) ?></span>
                                <?php endif; ?>
                                <h3 class="product-name"><?= e($product['name']) ?></h3>
                                <p class="product-price"><?= formatPrice($product['base_price']) ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= baseUrl('/products?page=' . ($currentPage - 1) . ($currentCategory ? '&category=' . $currentCategory : '')) ?>"
                                class="pagination-btn">← Previous</a>
                        <?php endif; ?>

                        <div class="pagination-numbers">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i === $currentPage): ?>
                                    <span class="pagination-number active"><?= $i ?></span>
                                <?php elseif ($i === 1 || $i === $totalPages || abs($i - $currentPage) <= 2): ?>
                                    <a href="<?= baseUrl('/products?page=' . $i . ($currentCategory ? '&category=' . $currentCategory : '')) ?>"
                                        class="pagination-number"><?= $i ?></a>
                                <?php elseif (abs($i - $currentPage) === 3): ?>
                                    <span class="pagination-ellipsis">...</span>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?= baseUrl('/products?page=' . ($currentPage + 1) . ($currentCategory ? '&category=' . $currentCategory : '')) ?>"
                                class="pagination-btn">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-icon">📦</div>
                    <h3>Belum Ada Produk</h3>
                    <p>Tidak ada produk di kategori ini.</p>
                    <a href="<?= baseUrl('/products') ?>" class="btn btn-primary">Lihat Semua Produk</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>