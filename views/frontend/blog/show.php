<?php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$post    = $post ?? null;

if (!$post) {
  echo "<div class='container-fluid px-4 py-5'><div class='alert alert-danger'>Artikel tidak ditemukan.</div></div>";
  return;
}

$img = !empty($post['thumbnail'])
  ? $baseUrl . '/' . ltrim($post['thumbnail'], '/')
  : $baseUrl . '/assets/admin/img/photos/unsplash-3.jpg';
?>

<section class="ep-section py-5">
  <div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
      <a class="btn btn-sm btn-outline-secondary" href="<?= $baseUrl ?>/articles"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
      <a class="btn btn-sm btn-outline-primary" href="<?= $baseUrl ?>/contact#order"><i class="bi bi-bag-check-fill me-2"></i>Order</a>
    </div>

    <div class="card shadow-sm border-0">
      <img src="<?= htmlspecialchars($img) ?>" alt="" style="width:100%;height:360px;object-fit:cover;border-top-left-radius:12px;border-top-right-radius:12px;">
      <div class="card-body">
        <h1 class="h4 fw-bold mb-2"><?= htmlspecialchars($post['title'] ?? '-') ?></h1>
        <div class="text-muted small mb-3"><?= htmlspecialchars($post['published_at'] ?? '') ?></div>

        <div class="content">
          <?= $post['content'] ?? '' ?>
        </div>
      </div>
    </div>
  </div>
</section>
