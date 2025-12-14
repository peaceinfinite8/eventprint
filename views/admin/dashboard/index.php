<?php
$baseUrl = $baseUrl ?? '/eventprint/public';

$latestProducts   = $latestProducts ?? [];
$latestProductsPg = $latestProductsPg ?? ['total'=>0,'page'=>1,'per_page'=>10];

$prodTotal   = (int)($latestProductsPg['total'] ?? 0);
$prodPage    = max(1, (int)($latestProductsPg['page'] ?? 1));
$prodPerPage = max(1, (int)($latestProductsPg['per_page'] ?? 10));
$prodPages   = (int)ceil($prodTotal / $prodPerPage);

if (!function_exists('ep_rupiah')) {
  function ep_rupiah($n) {
    return 'Rp ' . number_format((float)$n, 0, ',', '.');
  }
}

?>

<h1 class="h3 mb-3">Dashboard</h1>

<!-- ✅ ONBOARDING (buat orang awam) -->
<div class="card ep-welcome mb-4 border-0">
  <div class="card-body p-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
    <div>
      <div class="ep-welcome-title">Selamat datang di Panel Admin EventPrint</div>
      <div class="ep-welcome-sub">
        Panel ini tempat kamu mengelola isi website: <b>Produk</b>, <b>Diskon</b>, <b>Lokasi/Store</b>, <b>Artikel</b>, dan <b>Pesan Kontak</b>.
        Kalau ini pertama kali, mulai dari tombol “Tambah Produk”.
      </div>

      <div class="ep-quicksteps mt-3">
        <div class="ep-step"><span class="ep-step-n">1</span> Tambah Produk</div>
        <div class="ep-step"><span class="ep-step-n">2</span> Atur Harga (Opsi)</div>
        <div class="ep-step"><span class="ep-step-n">3</span> Buat Diskon</div>
        <div class="ep-step"><span class="ep-step-n">4</span> Cek Pesan Masuk</div>
      </div>
    </div>

    <div class="text-lg-end">
      <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
        <span class="badge bg-light text-dark">Total Produk: <?php echo (int)$stats['products']; ?></span>
        <span class="badge bg-warning text-dark">Pesan Baru: <?php echo (int)$stats['contact_unread']; ?></span>
      </div>

      <div class="mt-3 d-grid gap-2 d-lg-flex justify-content-lg-end">
        <a href="<?php echo $baseUrl; ?>/admin/products/create" class="btn btn-light text-primary fw-bold">
          + Tambah Produk
        </a>
        <a href="<?php echo $baseUrl; ?>/admin/discounts" class="btn btn-outline-light">
          Kelola Diskon
        </a>
        <a href="<?php echo $baseUrl; ?>/admin/contact" class="btn btn-outline-light">
          Lihat Pesan
        </a>
      </div>
    </div>
  </div>
</div>

<!-- ✅ STAT CARDS -->
<div class="row g-3 mb-4">
  <div class="col-12 col-md-3">
    <a class="card ep-stat text-decoration-none" href="<?php echo $baseUrl; ?>/admin/products">
      <div class="card-body">
        <div class="ep-stat-k">Produk</div>
        <div class="ep-stat-v"><?php echo (int)$stats['products']; ?></div>
        <div class="ep-stat-h">Kelola katalog produk</div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-3">
    <a class="card ep-stat text-decoration-none" href="<?php echo $baseUrl; ?>/admin/our-store">
      <div class="card-body">
        <div class="ep-stat-k">Our Store</div>
        <div class="ep-stat-v"><?php echo (int)$stats['stores']; ?></div>
        <div class="ep-stat-h">Kelola cabang / lokasi</div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-3">
    <a class="card ep-stat text-decoration-none" href="<?php echo $baseUrl; ?>/admin/blog">
      <div class="card-body">
        <div class="ep-stat-k">Artikel</div>
        <div class="ep-stat-v"><?php echo (int)$stats['blog']; ?></div>
        <div class="ep-stat-h">Kelola konten blog</div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-3">
    <a class="card ep-stat text-decoration-none" href="<?php echo $baseUrl; ?>/admin/contact">
      <div class="card-body">
        <div class="ep-stat-k">Pesan Baru</div>
        <div class="ep-stat-v"><?php echo (int)$stats['contact_unread']; ?></div>
        <div class="ep-stat-h">Cek pesan masuk</div>
      </div>
    </a>
  </div>
</div>


<div class="d-flex justify-content-between align-items-center mt-4 mb-2" id="latest-products">
  <h5 class="mb-0">Produk Terbaru</h5>
  <a class="small" href="<?= $baseUrl ?>/admin/products">Lihat semua</a>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">

    <?php if (empty($latestProducts)): ?>
      <div class="text-muted">Belum ada produk.</div>
    <?php else: ?>
      <div class="row g-3">
        <?php foreach ($latestProducts as $p): ?>
          <?php
            $img = !empty($p['thumbnail'])
              ? $baseUrl . '/' . ltrim($p['thumbnail'], '/')
              : $baseUrl . '/assets/admin/img/photos/unsplash-1.jpg';

            $basePrice = (float)($p['base_price'] ?? 0);

            $hasDiscount = !empty($p['discount_type']) && (float)$p['discount_value'] > 0;

            $badgeText  = '';
            $finalPrice = $basePrice;

            if ($hasDiscount) {
              $dtype = strtolower(trim((string)$p['discount_type']));
              $dval  = (float)$p['discount_value'];

              if ($dtype === 'percent') {
                $badgeText  = 'Diskon ' . rtrim(rtrim(number_format($dval,2,'.',''), '0'), '.') . '%';
                $finalPrice = max(0, $basePrice - ($basePrice * ($dval/100)));
              } else {
                $badgeText  = 'Diskon ' . ep_rupiah($dval);
                $finalPrice = max(0, $basePrice - $dval);
              }
            }

            $pid = (int)($p['id'] ?? 0);
            $isFeatured = !empty($p['is_featured']) && (int)$p['is_featured'] === 1;

          ?>

          <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="ep-prod-card card h-100 border-0">
              <div class="ep-prod-thumb">
                <img src="<?= htmlspecialchars($img) ?>" alt="" class="ep-prod-img">

                <?php if ($hasDiscount): ?>
                  <span class="ep-discount-badge"><?= htmlspecialchars($badgeText) ?></span>
                <?php endif; ?>

                <?php if ($isFeatured): ?>
                  <span class="ep-featured-badge"><i class="bi bi-star-fill me-1"></i>Unggulan</span>
                <?php endif; ?>
              </div>

              <div class="card-body">
                <div class="ep-prod-title"><?= htmlspecialchars($p['name'] ?? '-') ?></div>

                <?php if (!empty($p['short_description'])): ?>
                  <div class="ep-prod-desc"><?= htmlspecialchars($p['short_description']) ?></div>
                <?php else: ?>
                  <div class="ep-prod-desc text-muted">—</div>
                <?php endif; ?>

                <div class="mt-2">
                  <?php if ($hasDiscount): ?>
                    <div class="ep-price-old"><?= ep_rupiah($basePrice) ?></div>
                    <div class="ep-price"><?= ep_rupiah($finalPrice) ?></div>
                  <?php else: ?>
                    <div class="ep-price"><?= ep_rupiah($basePrice) ?></div>
                  <?php endif; ?>
                </div>

                <div class="d-flex gap-2 mt-3">
                  <a class="btn btn-outline-primary btn-sm"
                     href="<?= $baseUrl ?>/admin/products/edit/<?= $pid ?>">
                     Ubah
                  </a>

                  <a class="btn btn-warning btn-sm text-white"
                     href="<?= $baseUrl ?>/admin/products/<?= $pid ?>/options">
                     Atur Harga
                  </a>
                </div>
              </div>
            </div>
          </div>

        <?php endforeach; ?>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="small text-muted">
          Menampilkan <?= count($latestProducts) ?> produk (Hal <?= $prodPage ?> dari <?= max(1,$prodPages) ?>).
        </div>

        <?php if ($prodPages > 1): ?>
          <?php $qs = $_GET ?? []; ?>
          <ul class="pagination mb-0">
            <?php
              $prev = $prodPage - 1;
              $next = $prodPage + 1;

              $qsPrev = $qs; $qsPrev['prod_page'] = max(1, $prev);
              $qsNext = $qs; $qsNext['prod_page'] = min($prodPages, $next);

              $urlPrev = $baseUrl . '/admin/dashboard?' . http_build_query($qsPrev) . '#latest-products';
              $urlNext = $baseUrl . '/admin/dashboard?' . http_build_query($qsNext) . '#latest-products';
            ?>

            <li class="page-item <?= ($prodPage <= 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= htmlspecialchars($urlPrev) ?>">‹</a>
            </li>

            <?php
              $start = max(1, $prodPage - 2);
              $end   = min($prodPages, $prodPage + 2);
              for ($i=$start; $i<=$end; $i++):
                $qsI = $qs; $qsI['prod_page'] = $i;
                $urlI = $baseUrl . '/admin/dashboard?' . http_build_query($qsI) . '#latest-products';
            ?>
              <li class="page-item <?= ($i === $prodPage) ? 'active' : '' ?>">
                <a class="page-link" href="<?= htmlspecialchars($urlI) ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>

            <li class="page-item <?= ($prodPage >= $prodPages) ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= htmlspecialchars($urlNext) ?>">›</a>
            </li>
          </ul>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </div>
</div>


<!-- ✅ CABANG / LOKASI TERBARU -->
<div class="d-flex justify-content-between align-items-center mb-2">
  <h2 class="h5 mb-0">Cabang / Lokasi Terbaru</h2>
  <a href="<?php echo $baseUrl; ?>/admin/our-store" class="text-muted small">Kelola lokasi</a>
</div>

<?php if (!empty($latestStores)): ?>
  <?php
    $st = $latestStores[0];

    // ✅ fallback field biar gak “hilang” kalau struktur kolom beda
    $title   = $st['title'] ?? $st['name'] ?? $st['store_name'] ?? $st['client_name'] ?? 'Our Store';
    $desc    = $st['short_description'] ?? $st['description'] ?? '';
    $thumb   = $st['thumbnail'] ?? '';
    $address = $st['address'] ?? '';
    $phone   = $st['phone'] ?? '';
    $gmapsEmbed = $st['gmaps_embed'] ?? '';
    $lat = $st['latitude'] ?? '';
    $lng = $st['longitude'] ?? '';
  ?>

  <div class="row g-3 mb-4">
    <div class="col-12 col-lg-7">
      <div class="card store-preview h-100 shadow-sm border-0">
        <div class="store-preview-thumb">
          <?php if (!empty($thumb)): ?>
            <img src="<?php echo $baseUrl . '/' . htmlspecialchars($thumb); ?>" alt="" class="store-preview-img">
          <?php else: ?>
            <div class="store-preview-placeholder"></div>
          <?php endif; ?>
        </div>

        <div class="card-body">
          <h5 class="store-preview-title mb-1"><?php echo htmlspecialchars($title); ?></h5>
          <p class="store-preview-desc mb-0">
            <?php echo $desc ? htmlspecialchars($desc) : '<span class="text-muted">(Tidak ada deskripsi)</span>'; ?>
          </p>
          <?php if ($address): ?>
            <div class="mt-2 small text-muted">
              <b>Alamat:</b> <?php echo htmlspecialchars($address); ?>
            </div>
          <?php endif; ?>
          <?php if ($phone): ?>
            <div class="small text-muted">
              <b>Telepon:</b> <?php echo htmlspecialchars($phone); ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-5">
      <div class="card store-side h-100 shadow-sm border-0">
        <div class="store-side-top">
          <div class="store-side-header">Peta Lokasi</div>

          <div class="store-map-wrap">
            <?php if (!empty($gmapsEmbed)): ?>
              <iframe src="<?php echo htmlspecialchars($gmapsEmbed); ?>" class="store-map" allowfullscreen="" loading="lazy"></iframe>
            <?php elseif (!empty($lat) && !empty($lng)): ?>
              <iframe src="https://www.google.com/maps?q=<?php echo urlencode($lat . ',' . $lng); ?>&output=embed" class="store-map" allowfullscreen="" loading="lazy"></iframe>
            <?php elseif (!empty($address)): ?>
              <iframe src="https://www.google.com/maps?q=<?php echo urlencode($address); ?>&output=embed" class="store-map" allowfullscreen="" loading="lazy"></iframe>
            <?php else: ?>
              <div class="store-map-empty">Belum ada data lokasi (gmaps / koordinat / alamat).</div>
            <?php endif; ?>
          </div>
        </div>

        <div class="store-side-bottom">
          <div class="store-side-header">Aksi Cepat</div>
          <div class="p-3 d-grid gap-2">
            <a class="btn btn-outline-primary" href="<?php echo $baseUrl; ?>/admin/our-store">Kelola Lokasi</a>
            <a class="btn btn-primary" href="<?php echo $baseUrl; ?>/admin/our-store/create">Tambah Lokasi</a>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php else: ?>
  <div class="card mb-4">
    <div class="card-body text-muted">
      Belum ada data lokasi. <a href="<?php echo $baseUrl; ?>/admin/our-store/create">Tambah lokasi pertama</a>.
    </div>
  </div>
<?php endif; ?>


<!-- ✅ SCRIPT carousel -->
<script>
(function(){
  const track = document.getElementById('epProductTrack');
  if(!track) return;

  const btns = document.querySelectorAll('.ep-carousel-btn');
  btns.forEach(btn => {
    btn.addEventListener('click', () => {
      const dir = parseInt(btn.getAttribute('data-dir') || '1', 10);
      const card = track.querySelector('.ep-product-card');
      const step = card ? (card.getBoundingClientRect().width + 16) : 320;
      track.scrollBy({ left: dir * step * 2, behavior: 'smooth' });
    });
  });
})();
</script>
