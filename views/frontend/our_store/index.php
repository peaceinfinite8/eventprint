<?php
$baseUrl   = $vars['baseUrl'] ?? '/eventprint/public';
$stores    = $stores ?? ($vars['stores'] ?? []);
$storeMain = $storeMain ?? ($vars['storeMain'] ?? null);

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function labelOfficeType($t){
  return ($t === 'hq') ? 'Head Office' : 'Branch';
}

// Kalau gmaps_url bukan embed link, kita tampilkan tombol "Buka Maps"
function isEmbedUrl($url){
  $url = (string)$url;
  return $url !== '' && (strpos($url, 'google.com/maps/embed') !== false || strpos($url, '/maps/embed') !== false);
}
?>

<main class="ep-section py-5 ep-bg-soft">
  <div class="container-fluid px-4">

    <?php if (!$storeMain): ?>
      <div class="alert alert-warning mb-0">
        Data toko belum tersedia. Pastikan ada record di tabel <b>our_store</b> dengan <b>is_active = 1</b>.
      </div>
    <?php else: ?>

      <div class="row g-4 align-items-center">
        <div class="col-lg-6">
          <div class="ep-eyebrow">Our Home</div>
          <h1 class="ep-title" style="font-size:clamp(1.6rem,2.6vw,2.4rem)">Alamat toko & jam operasional</h1>
          <p class="ep-subtitle mb-0">
            Data diambil dari database (<code>our_store</code>).
          </p>

          <div class="ep-store-card p-4 mt-3">
            <div class="d-flex align-items-start gap-3">
              <div class="ep-store-icon"><i class="bi bi-shop-window"></i></div>

              <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                  <div class="fw-semibold" id="epStoreName"><?= e($storeMain['name'] ?? '-') ?></div>
                  <span class="badge bg-primary">
                    <?= e(labelOfficeType($storeMain['office_type'] ?? 'branch')) ?>
                  </span>
                </div>

                <div class="text-muted" id="epStoreAddress">
                  <?= e($storeMain['address'] ?? '-') ?>, <?= e($storeMain['city'] ?? '-') ?>
                </div>

                <div class="row g-3 mt-3">
                  <div class="col-6">
                    <div class="small text-muted">Telepon</div>
                    <div class="fw-semibold" id="epStorePhone"><?= e($storeMain['phone'] ?? '-') ?></div>
                  </div>
                  <div class="col-6">
                    <div class="small text-muted">WhatsApp</div>
                    <div class="fw-semibold"><?= e($storeMain['whatsapp'] ?? '-') ?></div>
                  </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-3" id="epStoreBadges">
                  <?php if (!empty($storeMain['city'])): ?>
                    <span class="badge bg-light text-dark border"><i class="bi bi-geo-alt me-1"></i><?= e($storeMain['city']) ?></span>
                  <?php endif; ?>
                  <?php if (!empty($storeMain['phone'])): ?>
                    <span class="badge bg-light text-dark border"><i class="bi bi-telephone me-1"></i><?= e($storeMain['phone']) ?></span>
                  <?php endif; ?>
                  <?php if (!empty($storeMain['whatsapp'])): ?>
                    <span class="badge bg-success"><i class="bi bi-whatsapp me-1"></i><?= e($storeMain['whatsapp']) ?></span>
                  <?php endif; ?>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-3">
                  <?php if (!empty($storeMain['whatsapp'])):
                    $wa = preg_replace('/\D+/', '', (string)$storeMain['whatsapp']);
                    // kalau user input 08..., ubah ke 62...
                    if (strpos($wa, '0') === 0) $wa = '62' . substr($wa, 1);
                  ?>
                    <a class="btn btn-success" target="_blank" href="https://wa.me/<?= e($wa) ?>">
                      <i class="bi bi-whatsapp me-2"></i>Hubungi via WhatsApp
                    </a>
                  <?php endif; ?>

                  <?php if (!empty($storeMain['gmaps_url'])): ?>
                    <a class="btn btn-outline-primary" target="_blank" href="<?= e($storeMain['gmaps_url']) ?>">
                      <i class="bi bi-map me-2"></i>Buka Google Maps
                    </a>
                  <?php endif; ?>
                </div>

              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="ep-map-card">
            <?php if (!empty($storeMain['gmaps_url']) && isEmbedUrl($storeMain['gmaps_url'])): ?>
              <iframe
                class="store-map"
                src="<?= e($storeMain['gmaps_url']) ?>"
                width="100%"
                height="420"
                style="border:0;border-radius:14px;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
            <?php else: ?>
              <div class="ep-map-placeholder" style="border-radius:14px;min-height:420px;">
                <div class="ep-map-badge">
                  <i class="bi bi-geo-alt-fill me-2"></i>Map belum di-embed
                </div>
                <div class="text-white-50 small mt-2">
                  Isi kolom <code>gmaps_url</code> dengan link embed (google.com/maps/embed...) supaya map tampil.
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <?php if (count($stores) > 1): ?>
        <hr class="my-5">

        <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-3">
          <div>
            <div class="ep-eyebrow-sm">Cabang</div>
            <h2 class="ep-title-sm mb-0">Daftar Toko / Workshop</h2>
          </div>
        </div>

        <div class="row g-4">
          <?php foreach ($stores as $s): ?>
            <div class="col-12 col-md-6 col-xl-4">
              <div class="card border-0 shadow-sm h-100" style="border-radius:14px; overflow:hidden;">
                <?php
                  $img = !empty($s['thumbnail'])
                    ? $baseUrl . '/' . ltrim($s['thumbnail'], '/')
                    : $baseUrl . '/assets/admin/img/photos/unsplash-3.jpg';
                ?>
                <div style="height:170px;background:#f1f5f9;">
                  <img src="<?= e($img) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="fw-semibold"><?= e($s['name'] ?? '-') ?></div>
                    <span class="badge bg-primary"><?= e(labelOfficeType($s['office_type'] ?? 'branch')) ?></span>
                  </div>
                  <div class="text-muted small mt-1">
                    <?= e($s['address'] ?? '-') ?>, <?= e($s['city'] ?? '-') ?>
                  </div>

                  <div class="d-flex flex-wrap gap-2 mt-3">
                    <?php if (!empty($s['whatsapp'])):
                      $wa = preg_replace('/\D+/', '', (string)$s['whatsapp']);
                      if (strpos($wa, '0') === 0) $wa = '62' . substr($wa, 1);
                    ?>
                      <a class="btn btn-sm btn-success" target="_blank" href="https://wa.me/<?= e($wa) ?>">
                        <i class="bi bi-whatsapp me-1"></i>WA
                      </a>
                    <?php endif; ?>

                    <?php if (!empty($s['gmaps_url'])): ?>
                      <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?= e($s['gmaps_url']) ?>">
                        <i class="bi bi-map me-1"></i>Maps
                      </a>
                    <?php endif; ?>

                    <?php if (!empty($s['phone'])): ?>
                      <span class="btn btn-sm btn-outline-secondary disabled">
                        <i class="bi bi-telephone me-1"></i><?= e($s['phone']) ?>
                      </span>
                    <?php endif; ?>
                  </div>

                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    <?php endif; ?>

  </div>
</main>
