<?php
// views/frontend/partials/product_card.php
// $p is the product array passed from the loop
$img = $p['thumbnail'] ?? $p['image'] ?? '';
// Basic fallback if empty
if (empty($img)) {
    $img = $baseUrl . '/assets/frontend/images/product-placeholder.jpg';
}
// Ensure full URL
if (!preg_match('#^https?://#i', $img)) {
    // If it's already a full relative path from safePublicImage, fine.
    // If just filename, handle it (though controller usually does this).
    // Assuming controller passed a valid URL or path relative to public.
}

$price = (float) ($p['base_price'] ?? 0);
$displayPrice = 'Rp ' . number_format($price, 0, ',', '.');
// FIX: Use plural 'products' to match router and All Products page behavior
$link = $baseUrl . '/products/' . ($p['slug'] ?? '#');
$name = htmlspecialchars($p['name'] ?? 'Product Name');

// Check stock (optional parity)
$isOutOfStock = isset($p['stock']) && (int) $p['stock'] <= 0;
?>

<a href="<?= $link ?>" class="product-card <?= $isOutOfStock ? 'out-of-stock' : '' ?>">
    <div class="product-card-image">
        <img src="<?= $img ?>" alt="<?= $name ?>" loading="lazy">
        <?php if ($isOutOfStock): ?>
            <div class="out-of-stock-overlay">Habis</div>
        <?php endif; ?>

    </div>
    <div class="product-card-info">
        <h4 class="product-card-name">
            <?= $name ?>
        </h4>
        <div class="product-card-price <?= $isOutOfStock ? 'out-of-stock' : '' ?>">
            <?php if ($isOutOfStock): ?>
                <span class="strikethrough">
                    <?= $displayPrice ?>
                </span>
                <span class="stock-label">Stok Habis</span>
            <?php else: ?>
                <?= $displayPrice ?>
            <?php endif; ?>
        </div>
    </div>
</a>