<?php
$services  = $vars['services'] ?? [];
$uploadDir = $vars['productUploadDir'] ?? 'product';
?>

<div class="row g-4 mt-2">
  <?php if (!empty($services)): ?>
    <?php foreach ($services as $p): ?>
      <?php
        $name  = $p['name'] ?? '-';
        $slug  = $p['slug'] ?? '';
        $desc  = $p['short_description'] ?? '';
        $price = (float)($p['base_price'] ?? 0);

        $thumb = $p['thumbnail'] ?? null;
        $img   = $thumb
          ? ($baseUrl . '/uploads/' . $uploadDir . '/' . rawurlencode($thumb))
          : ($baseUrl . '/assets/frontend/img/placeholder.png');
      ?>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100 shadow-sm border-0">
          <div class="ratio ratio-4x3 bg-light">
            <img
              src="<?= $img ?>"
              class="w-100 h-100 object-fit-cover"
              alt="<?= htmlspecialchars($name) ?>"
              loading="lazy"
            >
          </div>

          <div class="card-body">
            <div class="fw-semibold"><?= htmlspecialchars($name) ?></div>

            <?php if ($desc !== ''): ?>
              <div class="text-muted small mt-1">
                <?= htmlspecialchars($desc) ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($p['category_name'])): ?>
              <div class="small mt-2">
                <span class="badge text-bg-light border">
                  <?= htmlspecialchars($p['category_name']) ?>
                </span>
              </div>
            <?php endif; ?>
          </div>

          <div class="card-footer bg-white border-0 pt-0">
            <div class="d-flex justify-content-between align-items-center">
              <div class="fw-semibold">
                <?php if ($price > 0): ?>
                  Rp <?= number_format($price, 0, ',', '.') ?>
                <?php else: ?>
                  <span class="text-muted small">Hubungi CS</span>
                <?php endif; ?>
              </div>

              <a class="btn btn-sm btn-primary"
                 href="<?= $baseUrl ?>/product/<?= rawurlencode($slug) ?>">
                Detail
              </a>
            </div>
          </div>
        </div>
      </div>

    <?php endforeach; ?>
  <?php else: ?>
    <div class="col-12">
      <div class="alert alert-light border mb-0">Belum ada produk aktif.</div>
    </div>
  <?php endif; ?>
</div>
