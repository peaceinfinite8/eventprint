<?php
$baseUrl       = $baseUrl ?? '/eventprint';
$nextSortOrder = $nextSortOrder ?? 1;
$errors = $errors ?? [];
$old    = $old ?? [];
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Tambah Laminasi Baru</h1>
        <p class="text-muted small mb-0">Tambahkan opsi laminasi atau finishing baru</p>
    </div>
</div>

<div class="dash-container-card fade-in delay-1">
  <div class="p-4">
    <form method="post" action="<?php echo $baseUrl; ?>/admin/laminations/store" enctype="multipart/form-data">
      <input type="hidden" name="_token"
             value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger dash-alert mb-4">
          <ul class="mb-0">
            <?php foreach ($errors as $fieldErrors): ?>
              <?php foreach ($fieldErrors as $msg): ?>
                <li><?php echo htmlspecialchars($msg); ?></li>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="row g-4">
          <div class="col-md-7">
              <div class="mb-3">
                <label class="dash-form-label">NAMA LAMINASI <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-layer-group"></i></span>
                    <input type="text" name="name" class="form-control" required
                       placeholder="Contoh: Doff (Matte)"
                       value="<?php echo htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
              </div>

              <div class="mb-3">
                <label class="dash-form-label">DELTA HARGA (RP)</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">Rp</span>
                    <input type="number" name="price_delta" class="form-control" step="500" min="0" placeholder="0"
                       value="<?php echo htmlspecialchars($old['price_delta'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-text small text-muted">
                    <i class="fas fa-info-circle me-1"></i> Tambahan harga per unit jika memilih laminasi ini.
                </div>
              </div>
          </div>

          <div class="col-md-5">
              <div class="p-4 bg-light rounded border border-light h-100">
                  <h6 class="fw-bold text-primary mb-3">Pengaturan Tampilan</h6>
                  
                  <div class="mb-3">
                    <label class="dash-form-label">GAMBAR ILUSTRASI</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <div class="form-text small">Opsional. Muncul saat laminasi dipilih.</div>
                  </div>

                  <div class="mb-3">
                    <label class="dash-form-label">URUTAN TAMPIL</label>
                    <input type="number" name="sort_order" class="form-control"
                       value="<?php echo htmlspecialchars($old['sort_order'] ?? $nextSortOrder, ENT_QUOTES, 'UTF-8'); ?>">
                     <div class="form-text small">Semakin kecil angkanya, semakin awal munculnya.</div>
                  </div>

                  <div class="form-check form-switch pt-2">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="activeCheck"
                       <?php echo (!isset($old['is_active']) || $old['is_active']) ? 'checked' : ''; ?>>
                    <label class="form-check-label fw-medium text-dark" for="activeCheck">
                      Aktifkan Laminasi Ini
                    </label>
                  </div>
              </div>
          </div>
      </div>

      <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
        <a href="<?php echo $baseUrl; ?>/admin/laminations" class="btn btn-outline-secondary px-4">Batal</a>
        <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-save me-2"></i> Simpan Laminasi</button>
      </div>

    </form>
  </div>
</div>
