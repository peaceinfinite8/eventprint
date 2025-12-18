<?php
/**
 * Product Detail Page
 * Display product information, gallery, options, and pricing
 */

$finalPrice = $product['base_price'];
if ($discount) {
    if ($discount['discount_type'] === 'percent') {
        $finalPrice = $product['base_price'] * (1 - $discount['discount_value'] / 100);
    } else {
        $finalPrice = $product['base_price'] - $discount['discount_value'];
    }
}
?>

<div class="product-detail-page">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="<?= baseUrl('/') ?>">Home</a>
            <span class="separator">›</span>
            <a href="<?= baseUrl('/products') ?>">Products</a>
            <?php if (!empty($product['category_name'])): ?>
                <span class="separator">›</span>
                <a href="<?= baseUrl('/products?category=' . e($product['category_slug'])) ?>"><?= e($product['category_name']) ?></a>
            <?php endif; ?>
            <span class="separator">›</span>
            <span class="current"><?= e($product['name']) ?></span>
        </nav>
        
        <div class="product-detail-grid">
            <!-- Product Images -->
            <div class="product-images">
                <div class="main-image">
                    <img src="<?= imageUrl($product['thumbnail'], 'frontend/images/product-placeholder.jpg') ?>" 
                         alt="<?= e($product['name']) ?>" 
                         id="mainImage">
                </div>
                
                <?php if (!empty($gallery)): ?>
                    <div class="image-gallery">
                        <img src="<?= imageUrl($product['thumbnail']) ?>" 
                             alt="<?= e($product['name']) ?>" 
                             class="gallery-thumb active"
                             onclick="changeImage(this.src)">
                        <?php foreach ($gallery as $img): ?>
                            <img src="<?= imageUrl($img) ?>" 
                                 alt="<?= e($product['name']) ?>"
                                 class="gallery-thumb"
                                 onclick="changeImage(this.src)">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Product Info -->
            <div class="product-info-section">
                <?php if (!empty($product['category_name'])): ?>
                    <span class="product-category-badge"><?= e($product['category_name']) ?></span>
                <?php endif; ?>
                
                <h1 class="product-title"><?= e($product['name']) ?></h1>
                
                <?php if (!empty($product['short_description'])): ?>
                    <p class="product-excerpt"><?= e($product['short_description']) ?></p>
                <?php endif; ?>
                
                <!-- Price -->
                <div class="product-pricing">
                    <?php if ($discount): ?>
                        <div class="discount-badge">
                            <?php if ($discount['discount_type'] === 'percent'): ?>
                                DISKON <?= number_format($discount['discount_value'], 0) ?>%
                            <?php else: ?>
                                DISKON <?= formatPrice($discount['discount_value']) ?>
                            <?php endif; ?>
                        </div>
                        <div class="price-wrapper">
                            <span class="original-price"><?= formatPrice($product['base_price']) ?></span>
                            <span class="final-price"><?= formatPrice($finalPrice) ?></span>
                        </div>
                    <?php else: ?>
                        <span class="final-price"><?= formatPrice($product['base_price']) ?></span>
                    <?php endif; ?>
                </div>
                
                <!-- Product Options -->
                <?php if (!empty($optionGroups)): ?>
                    <form class="product-options-form" id="productForm">
                        <?php foreach ($optionGroups as $group): ?>
                            <div class="option-group">
                                <label class="option-label">
                                    <?= e($group['name']) ?>
                                    <?php if ($group['is_required']): ?>
                                        <span class="required">*</span>
                                    <?php endif; ?>
                                </label>
                                
                                <?php if ($group['input_type'] === 'select'): ?>
                                    <select name="option_<?= $group['id'] ?>" 
                                            class="option-select" 
                                            <?= $group['is_required'] ? 'required' : '' ?>
                                            onchange="calculatePrice()">
                                        <option value="">Pilih <?= e($group['name']) ?></option>
                                        <?php foreach ($group['values'] as $value): ?>
                                            <option value="<?= $value['id'] ?>" 
                                                    data-price-type="<?= $value['price_type'] ?>"
                                                    data-price-value="<?= $value['price_value'] ?>">
                                                <?= e($value['label']) ?>
                                                <?php if ($value['price_value'] > 0): ?>
                                                    (+<?= formatPrice($value['price_value']) ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                <?php elseif ($group['input_type'] === 'radio'): ?>
                                    <div class="option-radio-group">
                                        <?php foreach ($group['values'] as $value): ?>
                                            <label class="radio-option">
                                                <input type="radio" 
                                                       name="option_<?= $group['id'] ?>" 
                                                       value="<?= $value['id'] ?>"
                                                       data-price-type="<?= $value['price_type'] ?>"
                                                       data-price-value="<?= $value['price_value'] ?>"
                                                       <?= $group['is_required'] ? 'required' : '' ?>
                                                       onchange="calculatePrice()">
                                                <span class="radio-label">
                                                    <?= e($value['label']) ?>
                                                    <?php if ($value['price_value'] > 0): ?>
                                                        <span class="option-price">(+<?= formatPrice($value['price_value']) ?>)</span>
                                                    <?php endif; ?>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                <?php elseif ($group['input_type'] === 'checkbox'): ?>
                                    <div class="option-checkbox-group">
                                        <?php foreach ($group['values'] as $value): ?>
                                            <label class="checkbox-option">
                                                <input type="checkbox" 
                                                       name="option_<?= $group['id'] ?>[]" 
                                                       value="<?= $value['id'] ?>"
                                                       data-price-type="<?= $value['price_type'] ?>"
                                                       data-price-value="<?= $value['price_value'] ?>"
                                                       onchange="calculatePrice()">
                                                <span class="checkbox-label">
                                                    <?= e($value['label']) ?>
                                                    <?php if ($value['price_value'] > 0): ?>
                                                        <span class="option-price">(+<?= formatPrice($value['price_value']) ?>)</span>
                                                    <?php endif; ?>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="calculated-price">
                            <strong>Total Harga:</strong>
                            <span class="total-price" id="totalPrice"><?= formatPrice($finalPrice) ?></span>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-large">
                            Hubungi Kami untuk Order
                        </button>
                    </form>
                <?php else: ?>
                    <a href="<?= baseUrl('/contact') ?>" class="btn btn-primary btn-large">
                        Hubungi Kami untuk Order
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Description -->
        <?php if (!empty($product['description'])): ?>
            <div class="product-description">
                <h2>Deskripsi Produk</h2>
                <div class="description-content">
                    <?= nl2br(e($product['description'])) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Gallery image change
function changeImage(src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.gallery-thumb').forEach(thumb => {
        thumb.classList.remove('active');
    });
    event.target.classList.add('active');
}

// Price calculation
const basePrice = <?= $finalPrice ?>;

function calculatePrice() {
    let total = basePrice;
    
    // Get all selected options
    document.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked, select').forEach(input => {
        if (input.tagName === 'SELECT') {
            const selected = input.options[input.selectedIndex];
            if (selected && selected.value) {
                const priceType = selected.dataset.priceType;
                const priceValue = parseFloat(selected.dataset.priceValue || 0);
                if (priceType === 'fixed') {
                    total += priceValue;
                } else if (priceType === 'percent') {
                    total += (basePrice * priceValue / 100);
                }
            }
        } else if (input.value) {
            const priceType = input.dataset.priceType;
            const priceValue = parseFloat(input.dataset.priceValue || 0);
            if (priceType === 'fixed') {
                total += priceValue;
            } else if (priceType === 'percent') {
                total += (basePrice * priceValue / 100);
            }
        }
    });
    
    // Update display
    document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

// Form submission
document.getElementById('productForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Redirect to contact page
    const productName = <?= json_encode($product['name']) ?>;
    const message = `Saya tertarik dengan produk: ${productName}`;
    window.location.href = <?= json_encode(baseUrl('/contact')) ?> + '?product=' + encodeURIComponent(productName);
});
</script>
