<?php
$baseUrl   = $baseUrl ?? '/eventprint';
$settings  = $settings ?? [];
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');

require_once __DIR__ . '/../../../app/helpers/settings_helpers.php';
$currentLogo = buildLogoPreviewUrl((string)$baseUrl, (array)$settings);
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

<link rel="stylesheet" href="<?= htmlspecialchars(rtrim($baseUrl, '/') . '/assets/admin/css/settings.css') ?>">
<script defer src="<?= htmlspecialchars(rtrim($baseUrl, '/') . '/assets/admin/js/settings.js') ?>"></script>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">Website & Business Settings</h1>
    <p class="text-muted small mb-0">Pusat pengaturan brand, kontak, dan mesin penjualan EventPrint</p>
  </div>
</div>

<div class="dash-container-card fade-in delay-1">
  <div class="p-4">
    <form id="settingsForm"
          action="<?= htmlspecialchars($baseUrl) ?>/admin/settings/update"
          method="post"
          enctype="multipart/form-data"
          novalidate>

      <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">

      <div class="row g-5">
        <div class="col-lg-6">
          <h5 class="fw-bold text-primary mb-4"><i class="fas fa-info-circle me-2"></i>Informasi Website</h5>

          <div class="mb-3">
            <label class="dash-form-label">Nama Website</label>
            <input type="text" name="site_name" class="form-control" placeholder="EventPrint"
                   value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>">
          </div>

          <div class="mb-3">
            <label class="dash-form-label">Tagline</label>
            <input type="text" name="site_tagline" class="form-control" placeholder="Layanan cetak event, pameran, dan promosi brand"
                   value="<?= htmlspecialchars($settings['site_tagline'] ?? '') ?>">
          </div>

          <div class="mb-4">
            <label class="dash-form-label">Logo Website</label>

            <div class="d-flex align-items-start gap-3">
              <div id="logoBox" class="logo-box rounded border d-flex align-items-center justify-content-center position-relative overflow-hidden" title="Klik untuk ganti logo">
                <?php if ($currentLogo): ?>
                  <img id="logoPreview" src="<?= htmlspecialchars($currentLogo) ?>" alt="Logo">
                  <div id="logoEmpty" class="d-none text-muted">
                    <i class="fas fa-image fa-2x opacity-25"></i>
                  </div>
                <?php else: ?>
                  <div id="logoEmpty" class="text-muted">
                    <i class="fas fa-image fa-2x opacity-25"></i>
                  </div>
                  <img id="logoPreview" src="" alt="Logo" class="d-none">
                <?php endif; ?>
                <div class="logo-hint position-absolute bottom-0 start-0 end-0 text-center py-1">Klik untuk ganti</div>
              </div>

              <div class="flex-grow-1">
                <input id="logoInput" type="file" name="logo" class="form-control form-control-sm" accept="image/png,image/jpeg,image/webp">
                <div class="small text-muted mt-1">PNG/JPG/WEBP, maks 2MB.</div>
                <div id="logoError" class="small text-danger d-none mt-1"></div>

                <div id="logoActions" class="d-none mt-2 d-flex gap-2">
                  <button type="button" id="logoChangeBtn" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-sync-alt me-1"></i> Ganti
                  </button>
                  <button type="button" id="logoClearBtn" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash me-1"></i> Reset
                  </button>
                </div>
              </div>
            </div>
          </div>

          <hr class="border-light my-4">

          <div class="d-flex align-items-start justify-content-between mb-3">
            <div>
              <h5 class="fw-bold text-primary mb-1">
                <i class="fas fa-address-book me-2"></i>Kontak & Lokasi Bisnis
              </h5>
              <p class="text-muted small mb-0">Data ini tampil di website (footer, halaman kontak, tombol WhatsApp).</p>
            </div>
          </div>

          <div class="mb-3">
            <label class="dash-form-label">Jam Operasional</label>
            <input type="text" name="operating_hours" class="form-control"
                   placeholder="Senin–Sabtu • 08.00–19.00"
                   value="<?= htmlspecialchars($settings['operating_hours'] ?? '') ?>">
            <div class="small text-muted mt-1">Ditampilkan di header & halaman kontak.</div>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="dash-form-label">Telepon Kantor</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-phone"></i></span>
                <input type="text" name="phone" class="form-control" placeholder="0815xxxxxxx"
                       value="<?= htmlspecialchars($settings['phone'] ?? '') ?>">
              </div>
              <div class="small text-muted mt-1">Untuk panggilan. Bisa berbeda dari WhatsApp.</div>
            </div>

            <div class="col-md-6">
              <label class="dash-form-label">WhatsApp Utama</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fab fa-whatsapp"></i></span>
                <input type="text" name="whatsapp" class="form-control" placeholder="62812xxxxxxx"
                       value="<?= htmlspecialchars($settings['whatsapp'] ?? '') ?>">
              </div>
              <div class="small text-muted mt-1">Gunakan format 62 tanpa spasi. Contoh: 6281234567890</div>
            </div>
          </div>

          <div class="mb-3 mt-3">
            <label class="dash-form-label">Email Bisnis</label>
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="fas fa-envelope"></i></span>
              <input type="email" name="email" class="form-control" placeholder="cs@eventprint.id"
                     value="<?= htmlspecialchars($settings['email'] ?? '') ?>">
            </div>
            <div class="small text-muted mt-1">Untuk permintaan penawaran & administrasi.</div>
          </div>

          <div class="mb-3">
            <label class="dash-form-label">Alamat</label>
            <textarea name="address" rows="3" class="form-control"
                      placeholder="Tulis alamat lengkap (jalan, kota, provinsi)."><?= htmlspecialchars($settings['address'] ?? '') ?></textarea>
            <div class="small text-muted mt-1">Tampil di footer & halaman kontak.</div>
          </div>

          <div class="mb-3">
            <label class="dash-form-label">Link Google Maps</label>
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt"></i></span>
              <input id="mapsLinkInput" type="text" name="maps_link" class="form-control" placeholder="https://maps.app.goo.gl/..."
                     value="<?= htmlspecialchars($settings['maps_link'] ?? '') ?>">
            </div>
            <div class="small text-muted mt-1">Gunakan link share dari Google Maps.</div>
          </div>

          <div class="mb-3">
            <label class="dash-form-label">Embed Maps (Iframe)</label>
            <textarea id="gmapsEmbedInput" name="gmaps_embed" rows="4" class="form-control font-monospace small text-muted"
                      placeholder='<iframe src="https://www.google.com/maps/embed?pb=..." loading="lazy"></iframe>'><?= htmlspecialchars($settings['gmaps_embed'] ?? '') ?></textarea>
            <div class="small text-muted mt-1">Opsional. Tempel kode iframe dari Google Maps.</div>
            <div id="gmapsEmbedError" class="small text-danger d-none mt-2"></div>
          </div>
        </div>

        <div class="col-lg-6">
          <?php include __DIR__ . '/_social_links.php'; ?>

          <hr class="border-light my-4">

          <?php include __DIR__ . '/_contacts_block.php'; ?>

          <div class="mt-4">
            <div class="ep-preview-card">
              <div class="ep-preview-head">
                <div class="fw-semibold small text-muted">
                  <i class="fas fa-map-location-dot me-2"></i>Preview Lokasi
                </div>
                <a id="gmapsOpenNewTab" class="small text-decoration-none d-none" href="#" target="_blank" rel="noopener">
                  Buka di Google Maps <i class="fas fa-arrow-up-right-from-square ms-1"></i>
                </a>
              </div>

              <div class="p-2">
                <div class="ep-ratio-16x9 rounded overflow-hidden border">
                  <iframe
                    id="gmapsPreviewFrame"
                    src=""
                    style="border:0;"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    allowfullscreen
                  ></iframe>
                </div>

                <div id="gmapsPreviewEmpty" class="text-muted small mt-2 px-1">
                  Tempel embed Google Maps untuk melihat preview.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-end mt-5 pt-3 border-top">
        <button id="saveBtn" type="submit" class="btn btn-primary px-5 py-2 shadow-sm">
          <span class="btn-text"><i class="fas fa-save me-2"></i> Simpan Pengaturan</span>
        </button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/_logo_crop_modal.php'; ?>

<script>
  window.__SETTINGS_PAGE__ = {
    initialLogoSrc: <?= json_encode($currentLogo ? $currentLogo : '') ?>
  };
</script>
