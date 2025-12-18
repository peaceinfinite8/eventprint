<?php
$baseUrl     = $baseUrl ?? ($vars['baseUrl'] ?? '/eventprint/public');
$categories  = $categories ?? ($vars['categories'] ?? []);
$homeContent = $homeContent ?? ($vars['homeContent'] ?? []);
$csrfToken   = $csrfToken ?? ($vars['csrfToken'] ?? '');
$stats       = $stats ?? ($vars['stats'] ?? []);

$heroUrl    = $baseUrl . '/admin/home/hero';
$contentUrl = $baseUrl . '/admin/home/content';
$previewUrl = $baseUrl . '/';

$heroTotal  = (int)($stats['hero_total'] ?? 0);
$heroActive = (int)($stats['hero_active'] ?? 0);
$contactPct = (int)($stats['contact_pct'] ?? 0);
$mappingPct = (int)($stats['mapping_pct'] ?? 0);

$printId    = (int)($stats['print_id'] ?? 0);
$mediaId    = (int)($stats['media_id'] ?? 0);
$printName  = (string)($stats['print_name'] ?? '');
$mediaName  = (string)($stats['media_name'] ?? '');

$printCount = (int)($stats['print_prod_count'] ?? 0);
$mediaCount = (int)($stats['media_prod_count'] ?? 0);
$featCount  = (int)($stats['featured_count'] ?? 0);
?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h3 mb-0">Konten Beranda</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-primary" href="<?= htmlspecialchars($previewUrl) ?>" target="_blank" rel="noopener">
      <i class="align-middle me-1" data-feather="external-link"></i> Preview Home
    </a>
    <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($baseUrl . '/admin/dashboard') ?>">
      Kembali
    </a>
  </div>
</div>

<?php if ($heroActive <= 0): ?>
  <div class="alert alert-warning d-flex align-items-start gap-2">
    <i data-feather="alert-triangle"></i>
    <div>
      <b>Hero Slides tidak ada yang aktif.</b> Banner di frontend akan kosong.
      <a href="<?= htmlspecialchars($heroUrl) ?>" class="ms-1">Kelola Hero</a>.
    </div>
  </div>
<?php endif; ?>

<?php if ($mappingPct < 100): ?>
  <div class="alert alert-info d-flex align-items-start gap-2">
    <i data-feather="info"></i>
    <div>
      <b>Mapping kategori belum lengkap.</b> Section Print/Media bisa kosong.
      <a href="#mapping" class="ms-1">Isi mapping</a>.
    </div>
  </div>
<?php endif; ?>

<?php if ($printId > 0 && $printCount <= 0): ?>
  <div class="alert alert-danger d-flex align-items-start gap-2">
    <i data-feather="x-circle"></i>
    <div>
      <b>Mapping “Print” sudah dipilih tapi produknya 0.</b>
      Frontend section Print akan kosong. Solusi: aktifkan produk di kategori itu atau ganti mapping.
    </div>
  </div>
<?php endif; ?>

<?php if ($mediaId > 0 && $mediaCount <= 0): ?>
  <div class="alert alert-danger d-flex align-items-start gap-2">
    <i data-feather="x-circle"></i>
    <div>
      <b>Mapping “Media Promosi” sudah dipilih tapi produknya 0.</b>
      Frontend section Media akan kosong. Solusi: aktifkan produk di kategori itu atau ganti mapping.
    </div>
  </div>
<?php endif; ?>

<div class="row g-3 mb-4">
  <div class="col-12 col-lg-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-3">
          <div>
            <h5 class="mb-1">Hero Slides</h5>
            <div class="text-muted small">Banner carousel (judul/subtitle/tombol/gambar/urutan).</div>
          </div>
          <i class="align-middle" data-feather="image"></i>
        </div>

        <div class="mt-3 d-flex flex-wrap gap-2">
          <span class="badge bg-light text-dark">Total: <?= $heroTotal ?></span>
          <span class="badge <?= $heroActive > 0 ? 'bg-success' : 'bg-danger' ?>">Aktif: <?= $heroActive ?></span>
        </div>

        <div class="mt-3">
          <a class="btn btn-primary" href="<?= htmlspecialchars($heroUrl) ?>">Kelola Hero</a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-3">
          <div>
            <h5 class="mb-1">Kontak & CTA</h5>
            <div class="text-muted small">Alamat, email, WA + CTA bar.</div>
          </div>
          <i class="align-middle" data-feather="edit-3"></i>
        </div>

        <div class="mt-3">
          <div class="small text-muted mb-1">Kelengkapan</div>
          <div class="progress" style="height:8px;">
            <div class="progress-bar" role="progressbar" style="width: <?= max(0,min(100,$contactPct)) ?>%"></div>
          </div>
          <div class="small text-muted mt-2"><?= $contactPct ?>%</div>
        </div>

        <div class="mt-3">
          <a class="btn btn-outline-primary" href="<?= htmlspecialchars($contentUrl) ?>">Edit Konten</a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-3">
          <div>
            <h5 class="mb-1">Mapping Kategori</h5>
            <div class="text-muted small">Menentukan produk yang tampil di Home.</div>
          </div>
          <i class="align-middle" data-feather="hash"></i>
        </div>

        <div class="mt-3">
          <div class="small text-muted mb-1">Status mapping</div>
          <div class="progress" style="height:8px;">
            <div class="progress-bar" role="progressbar" style="width: <?= max(0,min(100,$mappingPct)) ?>%"></div>
          </div>
          <div class="small text-muted mt-2"><?= $mappingPct ?>%</div>

          <div class="mt-2 small">
            <div><b>Print:</b> <?= $printName !== '' ? htmlspecialchars($printName) : '<span class="text-danger">Belum dipilih</span>' ?>
              <span class="text-muted">(<?= $printCount ?> produk)</span>
            </div>
            <div><b>Media:</b> <?= $mediaName !== '' ? htmlspecialchars($mediaName) : '<span class="text-danger">Belum dipilih</span>' ?>
              <span class="text-muted">(<?= $mediaCount ?> produk)</span>
            </div>
            <div class="mt-1"><b>Featured:</b> <span class="text-muted"><?= $featCount ?> produk unggulan</span></div>
          </div>
        </div>

        <div class="mt-3">
          <a class="btn btn-outline-secondary" href="#mapping">Atur Mapping</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm" id="mapping">
  <div class="card-body">
    <h5 class="mb-2">Mapping Kategori untuk Home</h5>
    <div class="text-muted small mb-3">
      Tentukan kategori untuk section <b>Print Warna & Hitam Putih</b> dan <b>Cetak Media Promosi</b>.
    </div>

    <form method="post" action="<?= $baseUrl ?>/admin/home/category-map" class="row g-3">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

      <div class="col-12 col-lg-6">
        <label class="form-label">Kategori untuk "Print"</label>
        <select class="form-select" name="home_print_category_id">
          <option value="0">— Pilih Kategori —</option>
          <?php foreach ($categories as $c): ?>
            <?php $id = (int)($c['id'] ?? 0); ?>
            <option value="<?= $id ?>" <?= ((string)$id === (string)($homeContent['home_print_category_id'] ?? '')) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['name'] ?? '') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label">Kategori untuk "Media Promosi"</label>
        <select class="form-select" name="home_media_category_id">
          <option value="0">— Pilih Kategori —</option>
          <?php foreach ($categories as $c): ?>
            <?php $id = (int)($c['id'] ?? 0); ?>
            <option value="<?= $id ?>" <?= ((string)$id === (string)($homeContent['home_media_category_id'] ?? '')) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['name'] ?? '') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12">
        <button class="btn btn-primary" type="submit">Simpan Mapping</button>
      </div>
    </form>
  </div>
</div>
