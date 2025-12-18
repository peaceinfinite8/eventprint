<?php
/**
 * Blog Detail Page
 * Display single blog post with content and related posts
 */
?>

<article class="blog-detail-page">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="<?= baseUrl('/') ?>">Home</a>
            <span class="separator">›</span>
            <a href="<?= baseUrl('/blog') ?>">Blog</a>
            <span class="separator">›</span>
            <span class="current"><?= e($post['title']) ?></span>
        </nav>

        <!-- Post Header -->
        <header class="post-header">
            <h1 class="post-title"><?= e($post['title']) ?></h1>
            <div class="post-meta">
                <span class="post-date">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path
                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
                    </svg>
                    <?= formatDate($post['published_at'], 'd M Y') ?>
                </span>
            </div>
        </header>

        <!-- Featured Image -->
        <?php if (!empty($post['thumbnail'])): ?>
            <div class="post-featured-image">
                <img src="<?= imageUrl($post['thumbnail'], 'frontend/images/blog-placeholder.jpg') ?>"
                    alt="<?= e($post['title']) ?>">
            </div>
        <?php endif; ?>

        <!-- Post Content -->
        <div class="post-content">
            <?= nl2br(e($post['content'])) ?>
        </div>

        <!-- Social Share -->
        <div class="post-share">
            <h4>Bagikan Artikel:</h4>
            <div class="share-buttons">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(baseUrl('/blog/' . $post['slug'])) ?>"
                    target="_blank" class="share-btn facebook" aria-label="Share on Facebook">
                    Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode(baseUrl('/blog/' . $post['slug'])) ?>&text=<?= urlencode($post['title']) ?>"
                    target="_blank" class="share-btn twitter" aria-label="Share on Twitter">
                    Twitter
                </a>
                <a href="https://api.whatsapp.com/send?text=<?= urlencode($post['title'] . ' ' . baseUrl('/blog/' . $post['slug'])) ?>"
                    target="_blank" class="share-btn whatsapp" aria-label="Share on WhatsApp">
                    WhatsApp
                </a>
            </div>
        </div>

        <!-- Related Posts -->
        <?php if (!empty($relatedPosts)): ?>
            <section class="related-posts">
                <h3 class="section-title">Artikel Terkait</h3>
                <div class="related-grid">
                    <?php foreach ($relatedPosts as $related): ?>
                        <a href="<?= baseUrl('/blog/' . e($related['slug'])) ?>" class="related-card">
                            <div class="related-image">
                                <img src="<?= imageUrl($related['thumbnail'], 'frontend/images/blog-placeholder.jpg') ?>"
                                    alt="<?= e($related['title']) ?>" loading="lazy">
                            </div>
                            <div class="related-content">
                                <span class="post-date"><?= formatDate($related['published_at']) ?></span>
                                <h4 class="related-title"><?= e($related['title']) ?></h4>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Back to Blog -->
        <div class="post-navigation">
            <a href="<?= baseUrl('/blog') ?>" class="btn btn-outline">
                ← Kembali ke Blog
            </a>
        </div>
    </div>
</article>