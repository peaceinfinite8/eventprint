<?php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$posts   = $posts ?? [];
?>

<section class="ep-section py-5">
  <div class="container-fluid px-4">
    <div class="ep-section-head d-flex align-items-end justify-content-between gap-3 flex-wrap mb-4">
      <div>
        <div class="ep-eyebrow-sm">Artikel</div>
        <h1 class="ep-title-sm">Tips & Panduan Cetak</h1>
      </div>
      <a class="btn btn-outline-secondary" href="<?= $baseUrl ?>/products">Lihat Produk</a>
    </div>

    <?php if (empty($posts)): ?>
      <div class="alert alert-info">Belum ada artikel yang dipublish.</div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($posts as $p): ?>
          <?php
            $img = !empty($p['thumbnail'])
              ? $baseUrl . '/' . ltrim($p['thumbnail'], '/')
              : $baseUrl . '/assets/admin/img/photos/unsplash-3.jpg';
          ?>
          <div class="col-12 col-md-6 col-lg-4">
            <a class="card h-100 shadow-sm border-0 text-decoration-none text-dark" href="<?= $baseUrl ?>/articles/<?= urlencode($p['slug'] ?? '') ?>">
              <div style="height:200px; overflow:hidden; border-top-left-radius: .75rem; border-top-right-radius: .75rem;">
                <img src="<?= htmlspecialchars($img) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
              </div>
              <div class="card-body">
                <div class="fw-bold mb-2"><?= htmlspecialchars($p['title'] ?? '-') ?></div>
                <div class="text-muted small">
                  <?= htmlspecialchars($p['excerpt'] ?? 'Baca selengkapnya...') ?>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
