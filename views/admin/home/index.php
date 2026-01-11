<?php
$baseUrl = $baseUrl ?? ($vars['baseUrl'] ?? '/eventprint/public');
$categories = $categories ?? ($vars['categories'] ?? []);
$homeContent = $homeContent ?? ($vars['homeContent'] ?? []);
$csrfToken = $csrfToken ?? ($vars['csrfToken'] ?? '');
$stats = $stats ?? ($vars['stats'] ?? []);

$heroUrl = $baseUrl . '/admin/home/hero';
$contentUrl = $baseUrl . '/admin/home/content';
$previewUrl = $baseUrl . '/';

$heroTotal = (int) ($stats['hero_total'] ?? 0);
$heroActive = (int) ($stats['hero_active'] ?? 0);
$contactPct = (int) ($stats['contact_pct'] ?? 0);
$mappingPct = (int) ($stats['mapping_pct'] ?? 0);

$printId = (int) ($stats['print_id'] ?? 0);
$mediaId = (int) ($stats['media_id'] ?? 0);
$printName = (string) ($stats['print_name'] ?? '');
$mediaName = (string) ($stats['media_name'] ?? '');

$printCount = (int) ($stats['print_prod_count'] ?? 0);
$mediaCount = (int) ($stats['media_prod_count'] ?? 0);
$featCount = (int) ($stats['featured_count'] ?? 0);

$smallBannerTotal = (int) ($stats['small_banner_total'] ?? 0);
$smallBannerActive = (int) ($stats['small_banner_active'] ?? 0);

$testimonialTotal = (int) ($stats['testimonial_total'] ?? 0);
$testimonialActive = (int) ($stats['testimonial_active'] ?? 0);
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">Konten Beranda</h1>
    <p class="text-muted small mb-0">Kelola semua elemen visual di halaman depan website</p>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-primary bg-white shadow-sm" href="<?= htmlspecialchars($previewUrl) ?>" target="_blank"
      rel="noopener">
      <i class="fas fa-external-link-alt me-2"></i>Live Preview
    </a>
  </div>
</div>

<?php if ($heroActive <= 0): ?>
  <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4">
    <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
    <div>
      <strong>Hero Slides kosong atau tidak aktif!</strong> Pengunjung akan melihat area kosong di bagian atas.
      <a href="<?= htmlspecialchars($heroUrl) ?>" class="alert-link text-decoration-none ms-1">Fix Now</a>
    </div>
  </div>
<?php endif; ?>

<div class="row g-4 mb-4">
  <!-- Hero Slides Card -->
  <div class="col-12 col-lg-4">
    <div class="dash-container-card h-100 p-4 position-relative overflow-hidden">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div class="icon-circle bg-primary bg-opacity-10 text-primary mb-3">
          <i class="fas fa-images"></i>
        </div>
        <span class="badge bg-light text-dark border">Total: <?= $heroTotal ?></span>
      </div>
      <h5 class="fw-bold mb-2">Hero Slides</h5>
      <p class="text-muted small mb-4">Banner utama (carousel) di bagian paling atas halaman depan.</p>

      <div class="d-flex align-items-center justify-content-between mt-auto">
        <div class="small text-muted">Active: <span
            class="fw-bold text-<?= $heroActive > 0 ? 'success' : 'danger' ?>"><?= $heroActive ?></span></div>
        <a href="<?= htmlspecialchars($heroUrl) ?>" class="btn btn-sm btn-primary rounded-pill px-3">
          Kelola <i class="fas fa-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Mapping Access Card -->
  <div class="col-12 col-lg-4">
    <div class="dash-container-card h-100 p-4 position-relative overflow-hidden">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div class="icon-circle bg-info bg-opacity-10 text-info mb-3">
          <i class="fas fa-sitemap"></i>
        </div>
        <span class="badge bg-light text-dark border"><?= $mappingPct ?>% Setup</span>
      </div>
      <h5 class="fw-bold mb-2">Category Mapping</h5>
      <p class="text-muted small mb-4">Tentukan kategori produk untuk section Print & Media.</p>

      <div class="d-flex align-items-center justify-content-between mt-auto">
        <div class="small text-muted">
          Print: <strong><?= $printCount ?></strong> | Media: <strong><?= $mediaCount ?></strong>
        </div>
        <a href="#mapping-section" class="btn btn-sm btn-info text-white rounded-pill px-3">
          Atur <i class="fas fa-arrow-down ms-1"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Testimonials Card -->
  <div class="col-12 col-lg-4">
    <div class="dash-container-card h-100 p-4 position-relative overflow-hidden">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div class="icon-circle bg-warning bg-opacity-10 text-warning mb-3">
          <i class="fas fa-comment-alt"></i>
        </div>
        <span class="badge bg-light text-dark border">Total: <?= $testimonialTotal ?></span>
      </div>
      <h5 class="fw-bold mb-2">Testimonials</h5>
      <p class="text-muted small mb-4">Ulasan pelanggan untuk membangun kepercayaan.</p>

      <div class="d-flex align-items-center justify-content-between mt-auto">
        <div class="small text-muted">Active: <span
            class="fw-bold text-<?= $testimonialActive > 0 ? 'success' : 'danger' ?>"><?= $testimonialActive ?></span>
        </div>
        <a href="<?= $baseUrl ?>/admin/testimonials" class="btn btn-sm btn-warning text-dark rounded-pill px-3">
          Kelola <i class="fas fa-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-5">
  <!-- Why Choose Us -->
  <div class="col-md-6">
    <div class="dash-container-card p-3 d-flex align-items-center gap-3 h-100">
      <div class="icon-square bg-success bg-opacity-10 text-success rounded-3 p-3">
        <i class="fas fa-check-circle fa-lg"></i>
      </div>
      <div class="flex-grow-1">
        <h6 class="fw-bold mb-1">Why Choose Us</h6>
        <p class="text-muted small mb-0">Judul, deskripsi & ilustrasi keunggulan.</p>
      </div>
      <a href="<?= $baseUrl ?>/admin/home/why-choose" class="btn btn-icon btn-light text-primary">
        <i class="fas fa-pencil-alt"></i>
      </a>
    </div>
  </div>

  <!-- Small Banners -->
  <div class="col-md-6">
    <div class="dash-container-card p-3 d-flex align-items-center gap-3 h-100">
      <div class="icon-square bg-danger bg-opacity-10 text-danger rounded-3 p-3">
        <i class="fas fa-ad fa-lg"></i>
      </div>
      <div class="flex-grow-1">
        <h6 class="fw-bold mb-1">Small Banners (Promo)</h6>
        <p class="text-muted small mb-0"><?= $smallBannerActive ?> Banner Aktif</p>
      </div>
      <a href="<?= $baseUrl ?>/admin/home/small-banner" class="btn btn-icon btn-light text-primary">
        <i class="fas fa-pencil-alt"></i>
      </a>
    </div>
  </div>

  <!-- Store Info Update -->
  <div class="col-md-6">
    <div class="dash-container-card p-3 d-flex align-items-center gap-3 h-100">
      <div class="icon-square bg-secondary bg-opacity-10 text-secondary rounded-3 p-3">
        <i class="fas fa-info-circle fa-lg"></i>
      </div>
      <div class="flex-grow-1">
        <h6 class="fw-bold mb-1">Info Kontak & Store</h6>
        <p class="text-muted small mb-0">Alamat, Email, WhatsApp di Footer/Home.</p>
      </div>
      <a href="<?= $baseUrl ?>/admin/home/content" class="btn btn-icon btn-light text-primary">
        <i class="fas fa-pencil-alt"></i>
      </a>
    </div>
  </div>
</div>

<!-- Section Mapping Kategori -->
<div id="mapping-section" class="dash-container-card fade-in">
  <div class="card-header bg-white border-bottom p-4">
    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-sitemap me-2"></i>Mapping Kategori Homepage</h5>
  </div>
  <div class="p-4">
    <p class="text-muted mb-4">
      Pilih kategori produk yang akan ditampilkan secara otomatis pada section <b>"Print Warna & Hitam Putih"</b> dan
      <b>"Cetak Media Promosi"</b> di halaman depan.
    </p>

    <form method="post" action="<?= $baseUrl ?>/admin/home/category-map">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

      <div class="row g-4">
        <div class="col-md-4">
          <div class="bg-light p-3 rounded border">
            <label class="dash-form-label mb-2">SECTION "PRINT"</label>
            <select class="form-select" name="home_print_category_id">
              <option value="0">— Pilih Kategori —</option>
              <?php foreach ($categories as $c): ?>
                <?php $id = (int) ($c['id'] ?? 0); ?>
                <option value="<?= $id ?>" <?= ((string) $id === (string) ($homeContent['home_print_category_id'] ?? '')) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c['name'] ?? '') ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="small mt-2 text-muted">
              Terpilih: <strong><?= $printName ?: '-' ?></strong> (<?= $printCount ?> produk)
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="bg-light p-3 rounded border">
            <label class="dash-form-label mb-2">SECTION "MEDIA PROMOSI"</label>
            <select class="form-select" name="home_media_category_id">
              <option value="0">— Pilih Kategori —</option>
              <?php foreach ($categories as $c): ?>
                <?php $id = (int) ($c['id'] ?? 0); ?>
                <option value="<?= $id ?>" <?= ((string) $id === (string) ($homeContent['home_media_category_id'] ?? '')) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c['name'] ?? '') ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="small mt-2 text-muted">
              Terpilih: <strong><?= $mediaName ?: '-' ?></strong> (<?= $mediaCount ?> produk)
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="bg-light p-3 rounded border">
            <label class="dash-form-label mb-2">SECTION "MERCHANDISE"</label>
            <select class="form-select" name="home_merch_category_id">
              <option value="0">— Pilih Kategori —</option>
              <?php foreach ($categories as $c): ?>
                <?php $id = (int) ($c['id'] ?? 0); ?>
                <option value="<?= $id ?>" <?= ((string) $id === (string) ($homeContent['home_merch_category_id'] ?? '')) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c['name'] ?? '') ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="small mt-2 text-muted">
              Terpilih: <strong><?= $stats['merch_name'] ?: '-' ?></strong> (<?= $stats['merch_prod_count'] ?> produk)
            </div>
          </div>
        </div>
      </div>

      <div class="mt-4 pt-3 border-top text-end">
        <button class="btn btn-primary px-4" type="submit">
          <i class="fas fa-save me-2"></i>Simpan Mapping
        </button>
      </div>
    </form>
  </div>
</div>