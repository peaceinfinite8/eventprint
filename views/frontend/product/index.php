<?php
$baseUrl  = $vars['baseUrl'] ?? '/eventprint/public';
$products = $vars['products'] ?? [];  // <- WAJIB dari $vars
?>

<section class="py-5">
  <div class="container-fluid px-4">
    <div class="d-flex align-items-end justify-content-between gap-3 flex-wrap mb-4">
      <div>
        <div class="text-primary fw-semibold" style="letter-spacing:.12em;font-size:.85rem;">PRODUK &amp; LAYANAN</div>
        <h1 class="fw-bold" style="font-size:clamp(1.6rem,2.6vw,2.4rem)">Semua Produk</h1>
        <p class="text-muted mb-0">Pilih produk, lihat detail, dan hitung harga otomatis.</p>
      </div>
      <a class="btn btn-outline-secondary" href="<?= $baseUrl ?>/contact#order">Order / Quotation</a>
    </div>

    <?php if (empty($products)): ?>
      <div class="alert alert-info">Belum ada produk aktif.</div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($products as $p): ?>
          <?php
            $img = !empty($p['thumbnail'])
              ? $baseUrl . '/' . ltrim($p['thumbnail'], '/')
              : $baseUrl . '/assets/admin/img/photos/unsplash-1.jpg';

            $price = (float)($p['base_price'] ?? 0);
          ?>
          <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
              <div style="height:190px; overflow:hidden; border-top-left-radius: .75rem; border-top-right-radius: .75rem;">
                <img src="<?= htmlspecialchars($img) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
              </div>
              <div class="card-body">
                <div class="fw-semibold mb-1"><?= htmlspecialchars($p['name'] ?? '-') ?></div>
                <?php if (!empty($p['short_description'])): ?>
                  <div class="text-muted small mb-2" style="min-height:2.4em;">
                    <?= htmlspecialchars($p['short_description']) ?>
                  </div>
                <?php else: ?>
                  <div class="text-muted small mb-2" style="min-height:2.4em;">&nbsp;</div>
                <?php endif; ?>

                <div class="fw-bold mb-3">Rp <?= number_format($price, 0, ',', '.') ?></div>

                <a class="btn btn-primary w-100"
                   href="<?= $baseUrl ?>/products/<?= (int)$p['id'] ?>">
                   Lihat Detail & Hitung Harga
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
