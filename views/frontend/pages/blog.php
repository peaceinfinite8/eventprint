<?php
/**
 * Blog List Page
 * Display blog posts with featured hero mosaic and pagination
 */
?>

<div class="blog-page">
    <div class="container">
        <!-- Hero Mosaic (Featured Posts) -->
        <?php if (!empty($featuredPosts)): ?>
            <section class="blog-hero-mosaic">
                <h2 class="section-title">Artikel Unggulan</h2>
                <div class="mosaic-grid">
                    <?php foreach ($featuredPosts as $index => $post): ?>
                        <a href="<?= baseUrl('/blog/' . e($post['slug'])) ?>"
                            class="mosaic-card <?= $index === 0 ? 'featured-large' : '' ?>">
                            <div class="mosaic-image">
                                <img src="<?= imageUrl($post['thumbnail'], 'frontend/images/blog-placeholder.jpg') ?>"
                                    alt="<?= e($post['title']) ?>" loading="lazy">
                            </div>
                            <div class="mosaic-content">
                                <span class="post-date"><?= formatDate($post['published_at']) ?></span>
                                <h3 class="post-title"><?= e($post['title']) ?></h3>
                                <?php if (!empty($post['excerpt'])): ?>
                                    <p class="post-excerpt"><?= e($post['excerpt']) ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Latest Posts -->
        <section class="blog-list-section">
            <h2 class="section-title">Postingan Terbaru</h2>

            <?php if (!empty($posts)): ?>
                <div class="blog-grid">
                    <?php foreach ($posts as $post): ?>
                        <article class="blog-card">
                            <a href="<?= baseUrl('/blog/' . e($post['slug'])) ?>" class="blog-card-link">
                                <div class="blog-image">
                                    <img src="<?= imageUrl($post['thumbnail'], 'frontend/images/blog-placeholder.jpg') ?>"
                                        alt="<?= e($post['title']) ?>" loading="lazy">
                                </div>
                                <div class="blog-content">
                                    <span class="post-date"><?= formatDate($post['published_at']) ?></span>
                                    <h3 class="blog-title"><?= e($post['title']) ?></h3>
                                    <?php if (!empty($post['excerpt'])): ?>
                                        <p class="blog-excerpt"><?= e($post['excerpt']) ?></p>
                                    <?php endif; ?>
                                    <span class="read-more">Baca Selengkapnya →</span>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= baseUrl('/blog?page=' . ($currentPage - 1)) ?>" class="pagination-btn">← Previous</a>
                        <?php endif; ?>

                        <div class="pagination-numbers">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i === $currentPage): ?>
                                    <span class="pagination-number active"><?= $i ?></span>
                                <?php elseif ($i === 1 || $i === $totalPages || abs($i - $currentPage) <= 2): ?>
                                    <a href="<?= baseUrl('/blog?page=' . $i) ?>" class="pagination-number"><?= $i ?></a>
                                <?php elseif (abs($i - $currentPage) === 3): ?>
                                    <span class="pagination-ellipsis">...</span>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?= baseUrl('/blog?page=' . ($currentPage + 1)) ?>" class="pagination-btn">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-icon">📝</div>
                    <h3>Belum Ada Artikel</h3>
                    <p>Artikel akan segera ditambahkan. Stay tuned!</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>