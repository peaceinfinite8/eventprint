<?php
// views/frontend/product/detail.php
// Product Detail Page - STRICT FRONTEND-FIRST APPROACH
// Reference: frontend/public/views/product-detail.html
header('X-EP-View: views/frontend/product/detail.php');
echo "<!-- EP_VIEW_USED: " . __FILE__ . " -->";
?>

<style>
    /* Product Detail Specific Styles */
    .product-detail-container {
        display: grid;
        grid-template-columns: 1fr 1.2fr 0.8fr;
        gap: 24px;
        margin-bottom: 40px;
    }

    /* Gallery Column */
    .gallery-section {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .main-image {
        width: 100%;
        height: 400px;
        background: var(--gray-200);
        border-radius: var(--radius-medium);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray-600);
        font-size: 1.1rem;
        overflow: hidden;
    }

    .main-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .thumbnail-list {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }

    .thumbnail {
        height: 100px;
        background: var(--gray-200);
        border-radius: var(--radius-small);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray-600);
        font-size: 0.8rem;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .thumbnail:hover {
        border-color: var(--primary-cyan);
    }

    .thumbnail.active {
        border-color: var(--primary-cyan);
        background: var(--cyan-50);
    }

    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Options Column */
    .options-section h1 {
        font-size: 1.75rem;
        margin-bottom: 8px;
    }

    .price-display {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-cyan);
        margin-bottom: 16px;
    }

    /* Marketplace CTAs */
    .marketplace-ctas {
        display: flex;
        gap: 10px;
        margin-bottom: 24px;
    }

    .marketplace-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: var(--radius-pill);
        border: none;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .marketplace-btn svg {
        width: 16px;
        height: 16px;
    }

    .marketplace-btn.shopee {
        background: #EE4D2D;
        color: white;
    }

    .marketplace-btn.shopee:hover {
        background: #D73211;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(238, 77, 45, 0.3);
    }

    .marketplace-btn.tokopedia {
        background: #42B549;
        color: white;
    }

    .marketplace-btn.tokopedia:hover {
        background: #2E9738;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(66, 181, 73, 0.3);
    }

    .option-group {
        margin-bottom: 24px;
    }

    .option-label {
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 10px;
        display: block;
        color: var(--gray-900);
    }

    .chips-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .chip {
        padding: 6px 20px;
        height: 32px;
        border-radius: var(--radius-pill);
        border: 1.5px solid var(--cyan-200);
        background: var(--white);
        color: var(--gray-700);
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
    }

    .chip:hover {
        border-color: var(--primary-cyan);
        background: var(--cyan-50);
    }

    .chip.active {
        background: var(--primary-cyan);
        border-color: var(--primary-cyan);
        color: var(--white);
    }

    .note-textarea {
        width: 100%;
        min-height: 150px;
        padding: 12px 16px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-input);
        font-family: var(--font-body);
        font-size: 0.9rem;
        resize: vertical;
        transition: border 0.3s ease;
    }

    .note-textarea:focus {
        outline: none;
        border-color: var(--primary-cyan);
    }

    .file-upload-wrapper {
        position: relative;
    }

    .file-upload-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        background: var(--gray-100);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-input);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .file-upload-btn:hover {
        background: var(--gray-200);
    }

    .file-upload-btn input[type="file"] {
        display: none;
    }

    .file-name {
        font-size: 0.85rem;
        color: var(--gray-600);
        margin-top: 8px;
    }

    .file-error {
        font-size: 0.85rem;
        color: #DC2626;
        margin-top: 8px;
    }

    /* Checkout Box */
    .checkout-box {
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-medium);
        padding: 20px;
        box-shadow: var(--shadow-card);
        position: sticky;
        top: calc(var(--navbar-height) + 20px);
    }

    .checkout-title {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .quantity-stepper {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .quantity-label {
        font-size: 0.9rem;
        font-weight: 600;
        min-width: 70px;
    }

    .stepper-controls {
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--gray-100);
        padding: 6px 12px;
        border-radius: var(--radius-pill);
    }

    .stepper-btn {
        width: 28px;
        height: 28px;
        border: none;
        background: var(--white);
        border-radius: 50%;
        color: var(--gray-700);
        font-size: 1.2rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .stepper-btn:hover:not(:disabled) {
        background: var(--primary-cyan);
        color: var(--white);
    }

    .stepper-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .quantity-value {
        min-width: 40px;
        text-align: center;
        font-weight: 600;
        font-size: 1rem;
    }

    .subtotal-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-top: 1px solid var(--gray-200);
        margin-bottom: 16px;
    }

    .subtotal-label {
        font-size: 0.95rem;
        font-weight: 600;
    }

    .subtotal-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-cyan);
    }

    .checkout-btn {
        width: 100%;
        height: 48px;
        background: var(--primary-cyan);
        color: var(--white);
        border: none;
        border-radius: var(--radius-pill);
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .checkout-btn:hover {
        background: var(--cyan-600);
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
    }

    /* Info Sections */
    .info-sections {
        margin-top: 40px;
        display: grid;
        gap: 32px;
    }

    .info-section {
        background: var(--white);
        padding: 24px;
        border-radius: var(--radius-medium);
        box-shadow: var(--shadow-card);
    }

    .info-section h3 {
        font-size: 1.1rem;
        margin-bottom: 12px;
        color: var(--gray-900);
    }

    .info-section ul {
        list-style: none;
        padding: 0;
    }

    .info-section ul li {
        margin-bottom: 8px;
        color: var(--gray-700);
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .info-section ul li::before {
        content: "• ";
        color: var(--primary-cyan);
        font-weight: bold;
        margin-right: 8px;
    }

    .info-section p {
        color: var(--gray-700);
        line-height: 1.6;
        margin: 0;
    }

    /* Toast Notification */
    .toast {
        position: fixed;
        top: 100px;
        right: 24px;
        background: var(--gray-900);
        color: var(--white);
        padding: 16px 24px;
        border-radius: var(--radius-small);
        box-shadow: var(--shadow-hover);
        z-index: 1000;
        animation: slideIn 0.3s ease;
        display: none;
    }

    .toast.show {
        display: block;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Empty State */
    .product-not-found {
        text-align: center;
        padding: 80px 24px;
    }

    .product-not-found h2 {
        margin-bottom: 16px;
    }

    .product-not-found p {
        color: var(--gray-600);
        margin-bottom: 24px;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .product-detail-container {
            grid-template-columns: 1fr 1fr;
        }

        .checkout-box {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .product-detail-container {
            grid-template-columns: 1fr;
        }

        .main-image {
            height: 300px;
        }
    }
</style>

<!-- Toast Notification -->
<div id="toast" class="toast"></div>

<!-- Product Detail Content -->
<section class="section">
    <div class="container">
        <div id="productDetailContent">
            <!-- Rendered by JS -->
        </div>
    </div>
</section>