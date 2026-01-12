<?php
// views/frontend/blog/index.php
$baseUrl = $baseUrl ?? '/eventprint';
?>

<style>
  /* Blog-specific styles */
  .blog-hero {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 16px;
    margin-bottom: 40px;
  }

  .blog-hero-main {
    border-radius: var(--radius-medium);
    overflow: hidden;
    box-shadow: var(--shadow-card);
  }

  .blog-hero-main-image {
    width: 100%;
    height: 320px;
    background: var(--gray-200);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-600);
  }

  .blog-hero-main-content {
    padding: 20px;
    background: var(--white);
  }

  .blog-hero-aside {
    display: grid;
    gap: 12px;
  }

  .blog-hero-small {
    border-radius: var(--radius-small);
    overflow: hidden;
    box-shadow: var(--shadow-card);
    background: var(--white);
    min-height: 100px;
  }

  .blog-hero-small-image {
    width: 100%;
    height: 80px;
    background: var(--gray-200);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    color: var(--gray-600);
  }

  .blog-hero-small-content {
    padding: 12px;
  }

  .blog-hero-small-content p {
    font-size: 0.85rem;
    margin: 0;
  }

  .blog-carousel-container {
    overflow-x: auto;
    margin-bottom: 16px;
  }

  .blog-carousel-scroll {
    display: flex;
    gap: 16px;
    padding-bottom: 8px;
  }

  @media (max-width: 768px) {
    .blog-hero {
      grid-template-columns: 1fr;
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