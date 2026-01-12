<?php
/**
 * Blog List Page
 * 100% Parity with frontend/public/views/blog.html
 */
?>

<style>
    /* Blog-specific styles (from reference blog.html) */
    /* Blog-specific styles (Refined) */
    .blog-hero {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 24px;
        /* Increased gap */
        margin-bottom: 40px;
    }

    .blog-hero-main {
        border-radius: var(--radius-medium);
        overflow: hidden;
        box-shadow: var(--shadow-card);
        background: var(--white);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .blog-hero-main-image {
        width: 100%;
        height: 350px;
        background: var(--gray-200);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray-600);
        overflow: hidden;
        position: relative;
    }

    .blog-hero-main-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .blog-hero-main:hover .blog-hero-main-image img {
        transform: scale(1.05);
    }

    .blog-hero-main-content {
        padding: 24px;
        background: var(--white);
        flex-grow: 1;
        /* Ensure content fills space */
    }

    .blog-hero-main-content h3 {
        font-size: 1.5rem;
        margin-bottom: 12px;
        line-height: 1.3;
    }

    .blog-hero-main-content h3 a {
        text-decoration: none;
        color: var(--gray-900);
        transition: color 0.2s;
    }

    .blog-hero-main-content h3 a:hover {
        color: var(--primary-cyan);
    }

    .blog-hero-aside {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .blog-hero-small {
        display: flex;
        border-radius: var(--radius-small);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        /* Lighter shadow */
        background: var(--white);
        min-height: 100px;
        transition: transform 0.2s ease;
    }

    .blog-hero-small:hover {
        transform: translateX(4px);
    }

    .blog-hero-small-image {
        width: 120px;
        min-width: 120px;
        /* Fixed width */
        height: auto;
        background: var(--gray-200);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        color: var(--gray-600);
    }

    .blog-hero-small-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .blog-hero-small-content {
        padding: 12px 16px;
        display: flex;
        align-items: center;
    }

    .blog-hero-small-content p {
        font-size: 0.95rem;
        margin: 0;
        font-weight: 600;
        line-height: 1.4;
    }

    .blog-hero-small-content p a {
        text-decoration: none;
        color: var(--gray-800);
    }

    .blog-hero-small-content p a:hover {
        color: var(--primary-cyan);
    }

    /* Carousel */
    .blog-carousel-container {
        overflow-x: auto;
        margin-bottom: 32px;
        padding-bottom: 16px;
    }

    .blog-carousel-scroll {
        display: flex;
        gap: 20px;
    }

    .blog-carousel-card {
        min-width: 280px;
        width: 280px;
        padding: 20px;
        border-radius: var(--radius-medium);
        box-shadow: var(--shadow-card);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.3s ease;
        background: var(--white);
        /* Default fallback */
    }

    .blog-carousel-card:hover {
        transform: translateY(-5px);
    }

    /* Dynamic Colors from JS need CSS classes */
    .bg-blue {
        background: #E0F2FE;
        color: #0284C7;
    }

    .bg-green {
        background: #DCFCE7;
        color: #16A34A;
    }

    .bg-purple {
        background: #F3E8FF;
        color: #9333EA;
    }

    .bg-orange {
        background: #FFEDD5;
        color: #EA580C;
    }

    .blog-carousel-title {
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 12px;
        line-height: 1.4;
    }

    .blog-carousel-excerpt {
        font-size: 0.85rem;
        opacity: 0.8;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Trending Grid */
    .blog-card {
        background: var(--white);
        border-radius: var(--radius-medium);
        overflow: hidden;
        box-shadow: var(--shadow-card);
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease;
    }

    .blog-card:hover {
        transform: translateY(-5px);
    }

    .blog-card-image {
        height: 200px;
        background: var(--gray-200);
        overflow: hidden;
    }

    .blog-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .blog-card-content {
        padding: 20px;
        flex-grow: 1;
    }

    .blog-card-title {
        font-size: 1.25rem;
        margin-bottom: 10px;
        line-height: 1.4;
    }

    .blog-card-title a {
        text-decoration: none;
        color: var(--gray-900);
    }

    .blog-card-title a:hover {
        color: var(--primary-cyan);
    }

    .blog-card-excerpt {
        color: var(--gray-600);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .blog-hero {
            grid-template-columns: 1fr;
        }

        .blog-hero-main-image {
            height: 250px;
        }

        .grid-2 {
            grid-template-columns: 1fr !important;
            /* Force single column */
        }
    }
</style>

<!-- Blog Content -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Blog</h2>
        </div>

        <!-- Hero Mosaic -->
        <div id="blogHero" class="blog-hero">
            <!-- Rendered by JS -->
        </div>

        <!-- Postingan Unggulan -->
        <div class="section-header">
            <h3 class="section-title">Postingan Unggulan</h3>
        </div>
        <div class="blog-carousel-container">
            <div id="unggulanCarousel" class="blog-carousel-scroll">
                <!-- Rendered by JS -->
            </div>
        </div>

        <!-- Sedang Tren -->
        <div class="section-header mt-4">
            <h3 class="section-title">Sedang Tren</h3>
        </div>
        <div id="trenGrid" class="grid grid-2">
            <!-- Rendered by JS -->
        </div>
    </div>
</section>

<!-- Page-Specific Scripts -->
<script src="<?= assetUrl('frontend/js/render/renderBlog.js') ?>"></script>
<script>
    // Initialize blog page on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBlogPage);
    } else {
        initBlogPage();
    }
</script>