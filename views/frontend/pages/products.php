<?php
/**
 * Products Listing Page (1:1 with Frontend Reference)
 * Includes: Breadcrumbs, Sidebar, Product Grid
 */
?>

<!-- Breadcrumbs -->
<div class="breadcrumbs" id="breadcrumbs">
    <div class="container">
        <a href="<?= baseUrl('/') ?>" class="breadcrumb-link">Home</a>
        <span class="breadcrumb-separator">›</span>
        <span class="breadcrumb-current">All Products</span>
    </div>
</div>

<!-- Products Layout -->
<div class="products-layout">
    <div class="container">
        <div class="products-container">
            <!-- Sidebar -->
            <aside class="products-sidebar">
                <div class="sidebar-group">
                    <div class="sidebar-head">
                        <h3 class="sidebar-title">Categories</h3>
                    </div>
                    <div class="sidebar-body">
                        <ul class="sidebar-list">
                            <li class="sidebar-item <?= empty($currentCategory) ? 'active' : '' ?>">
                                <a href="<?= baseUrl('/products') ?>" class="sidebar-link">
                                    All Products
                                </a>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                                <li class="sidebar-item <?= ($currentCategory === $cat['slug']) ? 'active' : '' ?>">
                                    <a href="<?= baseUrl('/products?category=' . e($cat['slug'])) ?>" class="sidebar-link">
                                        <?= e($cat['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="products-main">
                <div class="products-header">
                    <h1 class="products-title">
                        <?php if ($currentCategory): ?>
                            <?php
                            $catName = 'Products';
                            foreach ($categories as $cat) {
                                if ($cat['slug'] === $currentCategory) {
                                    $catName = $cat['name'];
                                    break;
                                }
                            }
                            echo e($catName);
                            ?>
                        <?php else: ?>
                            All Products
                        <?php endif; ?>
                    </h1>
                    <p class="products-count"><?= $totalProducts ?> products found</p>
                </div>

                <?php if (!empty($products)): ?>
                    <div class="grid grid-4">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="product-card-image">
                                    <a href="<?= baseUrl('/products/' . e($product['slug'])) ?>">
                                        <img src="<?= imageUrl($product['thumbnail'] ?? '', 'frontend/images/product-placeholder.jpg') ?>"
                                            alt="<?= e($product['name']) ?>" loading="lazy">
                                    </a>
                                </div>
                                <div class="product-card-info">
                                    <h3 class="product-card-title">
                                        <a href="<?= baseUrl('/products/' . e($product['slug'])) ?>">
                                            <?= e($product['name']) ?>
                                        </a>
                                    </h3>
                                    <?php if (isset($product['category_name'])): ?>
                                        <p class="product-card-category"><?= e($product['category_name']) ?></p>
                                    <?php endif; ?>
                                    <?php if (isset($product['base_price'])): ?>
                                        <p class="product-card-price"><?= formatPrice($product['base_price']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php
                                $pageUrl = baseUrl('/products');
                                if ($currentCategory) {
                                    $pageUrl .= '?category=' . urlencode($currentCategory) . '&page=' . $i;
                                } else {
                                    $pageUrl .= '?page=' . $i;
                                }
                                ?>
                                <a href="<?= $pageUrl ?>" class="pagination-link <?= $i === $currentPage ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No products found in this category.</p>
                        <a href="<?= baseUrl('/products') ?>" class="btn btn-primary">View All Products</a>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</div>