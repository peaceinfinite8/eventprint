<?php
$baseUrl = $baseUrl ?? ($vars['baseUrl'] ?? '/eventprint/public');
$p = $product ?? [];
$imgs = $images ?? [];

$mainImg = $imgs[0] ?? ($p['thumbnail'] ?? '');
$mainImgUrl = $mainImg ? ($baseUrl . '/uploads/product/' . $mainImg) : '';
?>

<main class="py-5">
  <div class="container-fluid px-4">
    <div class="row g-4">

      <!-- GALLERY -->
      <div class="col-lg-6">
        <div class="pd-gallery">
          <div class="pd-main">
            <?php if ($mainImgUrl): ?>
              <img id="pdMainImg" src="<?= htmlspecialchars($mainImgUrl) ?>" alt="<?= htmlspecialchars($p['name'] ?? 'Produk') ?>">
            <?php else: ?>
              <div class="p-5 text-center text-muted">No Image</div>
            <?php endif; ?>
          </div>

          <?php if (!empty($imgs)): ?>
            <div class="pd-thumbs">
              <?php foreach ($imgs as $i => $img): 
                $u = $baseUrl . '/uploads/product/' . $img;
              ?>
                <button type="button" class="pd-thumb <?= $i === 0 ? 'active' : '' ?>" data-img="<?= htmlspecialchars($u) ?>">
                  <img src="<?= htmlspecialchars($u) ?>" alt="thumb">
                </button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- INFO -->
      <div class="col-lg-6">
        <h1 class="ep-title"><?= htmlspecialchars($p['name'] ?? '-') ?></h1>
        <p class="ep-subtitle"><?= htmlspecialchars($p['description'] ?? '') ?></p>

        <!-- MARKETPLACE BADGE (optional) -->
        <div class="d-flex gap-2 flex-wrap mt-3">
          <a class="ep-market-btn" href="#" target="_blank" rel="noopener">
            <span class="pd-m shopee">S</span> Shopee
          </a>
          <a class="ep-market-btn" href="#" target="_blank" rel="noopener">
            <span class="pd-m tokopedia">T</span> Tokopedia
          </a>
        </div>

        <div class="mt-4">
          <a class="btn btn-primary" href="<?= $baseUrl ?>/contact#order">
            <i class="bi bi-lightning-charge-fill me-2"></i>Order Sekarang
          </a>
        </div>
      </div>

    </div>
  </div>
</main>

<script>
  // thumb switcher
  document.querySelectorAll('.pd-thumb').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.pd-thumb').forEach(x => x.classList.remove('active'));
      btn.classList.add('active');
      const img = btn.getAttribute('data-img');
      const main = document.getElementById('pdMainImg');
      if (main && img) main.src = img;
    });
  });
</script>
