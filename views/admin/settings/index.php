<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$settings = $settings ?? [];
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">General Settings</h1>
    <p class="text-muted small mb-0">Pengaturan utama informasi website & kontak</p>
  </div>
</div>

<div class="dash-container-card fade-in delay-1">
  <div class="p-4">
    <form action="<?php echo $baseUrl; ?>/admin/settings/update" method="post" enctype="multipart/form-data">
      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

      <div class="row g-5">
        <!-- Left Column: Site Info & Contact -->
        <div class="col-lg-6">
          <h5 class="fw-bold text-primary mb-4"><i class="fas fa-info-circle me-2"></i>Informasi Website</h5>

          <div class="mb-3">
            <label class="dash-form-label">NAMA WEBSITE</label>
            <input type="text" name="site_name" class="form-control" placeholder="EventPrint"
              value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>">
          </div>

          <div class="mb-3">
            <label class="dash-form-label">TAGLINE</label>
            <input type="text" name="site_tagline" class="form-control" placeholder="Your Trusted Printing Partner"
              value="<?php echo htmlspecialchars($settings['site_tagline'] ?? ''); ?>">
          </div>

          <div class="mb-4">
            <label class="dash-form-label">LOGO WEBSITE</label>
            <div class="d-flex align-items-center gap-3">
              <?php if (!empty($settings['logo'])): ?>
                <div class="bg-white p-2 rounded border text-center"
                  style="width: 100px; height: 100px; border-style: dashed !important;">
                  <img src="<?php echo $baseUrl . '/' . htmlspecialchars($settings['logo']); ?>" alt="Logo"
                    class="h-100 w-100 object-fit-contain">
                </div>
              <?php else: ?>
                <div class="bg-light p-2 rounded border d-flex align-items-center justify-content-center text-muted"
                  style="width: 100px; height: 100px;">
                  <i class="fas fa-image fa-2x opacity-25"></i>
                </div>
              <?php endif; ?>
              <div class="flex-grow-1">
                <input type="file" name="logo" class="form-control text-sm" accept="image/*">
                <small class="text-muted d-block mt-1">Format: PNG/JPG. Kosongkan jika tidak ingin mengubah.</small>
              </div>
            </div>
          </div>

          <hr class="border-light my-4">

          <h5 class="fw-bold text-primary mb-4"><i class="fas fa-address-book me-2"></i>Informasi Kontak</h5>

          <div class="mb-3">
            <label class="dash-form-label">JAM OPERASIONAL (TOPBAR)</label>
            <input type="text" name="operating_hours" class="form-control"
              placeholder="Contoh: Senin – Sabtu: 08:00 – 17:00"
              value="<?php echo htmlspecialchars($settings['operating_hours'] ?? ''); ?>">
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="dash-form-label">NO. TELEPON</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-phone"></i></span>
                <input type="text" name="phone" class="form-control"
                  value="<?php echo htmlspecialchars($settings['phone'] ?? ''); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <label class="dash-form-label">WHATSAPP</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fab fa-whatsapp"></i></span>
                <input type="text" name="whatsapp" class="form-control" placeholder="628..."
                  value="<?php echo htmlspecialchars($settings['whatsapp'] ?? ''); ?>">
              </div>
            </div>
          </div>

          <div class="mb-3 mt-3">
            <label class="dash-form-label">EMAIL UTAMA</label>
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="fas fa-envelope"></i></span>
              <input type="email" name="email" class="form-control"
                value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>">
            </div>
          </div>

          <div class="mb-3">
            <label class="dash-form-label">ALAMAT LENGKAP</label>
            <textarea name="address" rows="3" class="form-control"><?php
            echo htmlspecialchars($settings['address'] ?? '');
            ?></textarea>
          </div>

          <div class="mb-3">
            <label class="dash-form-label">LINK GOOGLE MAPS</label>
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt"></i></span>
              <input type="text" name="maps_link" class="form-control" placeholder="https://maps.app.goo.gl/..."
                value="<?php echo htmlspecialchars($settings['maps_link'] ?? ''); ?>">
            </div>
          </div>

          <div class="mb-3">
            <label class="dash-form-label">GOOGLE MAPS EMBED (IFRAME HTML)</label>
            <textarea name="gmaps_embed" rows="3" class="form-control font-monospace small text-muted"
              placeholder='<iframe src="...'> <?php
              echo htmlspecialchars($settings['gmaps_embed'] ?? '');
              ?></textarea>
          </div>

        </div>

        <!-- Right Column: Social Media & Extra -->
        <div class="col-lg-6">
          <h5 class="fw-bold text-primary mb-4"><i class="fas fa-share-alt me-2"></i>Social Media Links</h5>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="dash-form-label">FACEBOOK</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fab fa-facebook-f"></i></span>
                <input type="text" name="facebook" class="form-control"
                  value="<?php echo htmlspecialchars($settings['facebook'] ?? ''); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <label class="dash-form-label">TWITTER / X</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fab fa-twitter"></i></span>
                <input type="text" name="twitter" class="form-control"
                  value="<?php echo htmlspecialchars($settings['twitter'] ?? ''); ?>">
              </div>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="dash-form-label">INSTAGRAM</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fab fa-instagram"></i></span>
                <input type="text" name="instagram" class="form-control"
                  value="<?php echo htmlspecialchars($settings['instagram'] ?? ''); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <label class="dash-form-label">TIKTOK</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fab fa-tiktok"></i></span>
                <input type="text" name="tiktok" class="form-control"
                  value="<?php echo htmlspecialchars($settings['tiktok'] ?? ''); ?>">
              </div>
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="dash-form-label">YOUTUBE</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fab fa-youtube"></i></span>
                <input type="text" name="youtube" class="form-control"
                  value="<?php echo htmlspecialchars($settings['youtube'] ?? ''); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <label class="dash-form-label">LINKEDIN</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fab fa-linkedin-in"></i></span>
                <input type="text" name="linkedin" class="form-control"
                  value="<?php echo htmlspecialchars($settings['linkedin'] ?? ''); ?>">
              </div>
            </div>
          </div>

          <hr class="border-light my-4">

          <h5 class="fw-bold text-primary mb-3"><i class="fas fa-headset me-2"></i>Kontak Sales Tambahan</h5>
          <div class="p-3 bg-light rounded border border-light">
            <div id="contacts-container">
              <?php
              $contacts = json_decode($settings['sales_contacts'] ?? '[]', true);
              if (!is_array($contacts))
                $contacts = [];
              foreach ($contacts as $c):
                ?>
                <div class="row g-2 mb-2 contact-row">
                  <div class="col-5">
                    <input type="text" name="sales_contacts[name][]" class="form-control form-control-sm"
                      placeholder="Nama / Label" value="<?= htmlspecialchars($c['name'] ?? '') ?>">
                  </div>
                  <div class="col-5">
                    <input type="text" name="sales_contacts[number][]" class="form-control form-control-sm"
                      placeholder="Nomor WA (e.g 6281...)" value="<?= htmlspecialchars($c['number'] ?? '') ?>">
                  </div>
                  <div class="col-2">
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-contact"><i
                        class="fas fa-times"></i></button>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary mt-2 w-100 border-dashed" id="add-contact">
              <i class="fas fa-plus me-1"></i> Tambah Kontak Sales
            </button>
          </div>

        </div>
      </div>

      <div class="d-flex justify-content-end mt-5 pt-3 border-top">
        <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm">
          <i class="fas fa-save me-2"></i> Simpan Pengaturan
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('contacts-container');
    const addBtn = document.getElementById('add-contact');

    addBtn.addEventListener('click', function () {
      const div = document.createElement('div');
      div.className = 'row g-2 mb-2 contact-row fade-in';
      div.innerHTML = `
      <div class="col-5">
        <input type="text" name="sales_contacts[name][]" class="form-control form-control-sm" placeholder="Nama / Label">
      </div>
      <div class="col-5">
        <input type="text" name="sales_contacts[number][]" class="form-control form-control-sm" placeholder="Nomor WA (e.g 6281...)">
      </div>
      <div class="col-2">
        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-contact"><i class="fas fa-times"></i></button>
      </div>
    `;
      container.appendChild(div);
    });

    container.addEventListener('click', function (e) {
      if (e.target.closest('.remove-contact')) {
        e.target.closest('.contact-row').remove();
      }
    });
  });
</script>