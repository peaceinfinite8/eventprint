<?php
// views/admin/our_store/create.php

$baseUrl = $baseUrl ?? '/eventprint/public';
$errors  = $errors ?? [];
$old     = $old ?? ($_SESSION['old_input'] ?? []);
unset($_SESSION['old_input']);

$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');

$val = function(string $key, $default = '') use ($old) {
    return htmlspecialchars((string)($old[$key] ?? $default), ENT_QUOTES, 'UTF-8');
};

$checked = function(string $key, bool $default = false) use ($old) {
    $v = $old[$key] ?? null;
    if ($v === null) return $default ? 'checked' : '';
    return ($v == 1 || $v === 'on') ? 'checked' : '';
};

$sel = function(string $key, string $value, string $default = '') use ($old) {
    $current = (string)($old[$key] ?? $default);
    return $current === $value ? 'selected' : '';
};
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Tambah Store</h1>
        <p class="text-muted small mb-0">Tambahkan lokasi toko atau kantor baru</p>
    </div>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger border-0 shadow-sm mb-4">
    <div class="d-flex align-items-start">
        <i class="fas fa-exclamation-circle me-2 mt-1"></i>
        <ul class="mb-0 ps-3">
        <?php foreach ($errors as $fieldErrors): ?>
            <?php foreach ((array)$fieldErrors as $msg): ?>
            <li><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </ul>
    </div>
  </div>
<?php endif; ?>

<div class="dash-container-card fade-in delay-1">
  <div class="p-4">
    <form method="post"
          action="<?php echo $baseUrl; ?>/admin/our-home/stores/store"
          enctype="multipart/form-data">

      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

      <div class="row g-4">
        <!-- Main Info -->
        <div class="col-lg-8">
            <h5 class="fw-bold text-primary mb-3"><i class="fas fa-store me-2"></i>Informasi Utama</h5>
            
            <div class="mb-3">
                <label class="dash-form-label">NAMA STORE <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required
                        placeholder="Contoh: Kantor Pusat"
                        value="<?php echo $val('name'); ?>">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="dash-form-label">SLUG (OPSIONAL)</label>
                    <input type="text" name="slug" class="form-control"
                            placeholder="auto-generate-jika-kosong"
                            value="<?php echo $val('slug'); ?>">
                    <div class="form-text small">Huruf kecil, angka, dan strip.</div>
                </div>
                <div class="col-md-6">
                    <label class="dash-form-label">TIPE KANTOR <span class="text-danger">*</span></label>
                    <select name="office_type" class="form-select" required>
                        <option value="hq" <?php echo $sel('office_type', 'hq', 'branch'); ?>>HQ (Kantor Pusat)</option>
                        <option value="branch" <?php echo $sel('office_type', 'branch', 'branch'); ?>>Branch (Cabang)</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="dash-form-label">ALAMAT LENGKAP <span class="text-danger">*</span></label>
                 <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="fas fa-map-marker-alt"></i></span>
                    <textarea name="address" class="form-control" rows="3" required
                          placeholder="Jalan, Nomor, Kelurahan, Kecamatan..."><?php echo $val('address'); ?></textarea>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                     <label class="dash-form-label">KOTA <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control" required
                            placeholder="Contoh: Jakarta Pusat"
                            value="<?php echo $val('city'); ?>">
                </div>
                <div class="col-md-6">
                     <label class="dash-form-label">GOOGLE MAPS URL</label>
                    <input type="text" name="gmaps_url" class="form-control"
                            placeholder="https://maps.google.com/..."
                            value="<?php echo $val('gmaps_url'); ?>">
                </div>
            </div>
            
             <h5 class="fw-bold text-primary mb-3 mt-4"><i class="fas fa-address-book me-2"></i>Kontak & Jam Operasional</h5>

             <div class="row g-3 mb-3">
                <div class="col-md-6">
                     <label class="dash-form-label">TELEPON</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-phone"></i></span>
                        <input type="text" name="phone" class="form-control"
                                placeholder="021-xxxxxxx"
                                value="<?php echo $val('phone'); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                     <label class="dash-form-label">WHATSAPP</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fab fa-whatsapp"></i></span>
                        <input type="text" name="whatsapp" class="form-control"
                                placeholder="62812xxxxxxx"
                                value="<?php echo $val('whatsapp'); ?>">
                    </div>
                </div>
             </div>

             <div class="mb-3">
                 <label class="dash-form-label">JAM OPERASIONAL (JSON)</label>
                 <textarea name="hours" class="form-control font-monospace text-muted" rows="4"
                           placeholder='["Senin – Jumat : 09.00 – 18.00","Sabtu : 08.00 – 18.00","Minggu : Libur"]'><?php echo $val('hours'); ?></textarea>
                 <div class="form-text small">
                    Masukkan format Array JSON. Contoh: <code>["Senin-Jumat: 09.00-17.00", "Sabtu: 09.00-14.00"]</code>
                 </div>
             </div>

        </div>

        <!-- Sidebar / Settings -->
        <div class="col-lg-4">
             <div class="card border border-light bg-light">
                <div class="card-body">
                    <label class="dash-form-label mb-2">PENGATURAN</label>
                    
                    <div class="mb-3">
                         <label class="form-label small text-muted">Urutan Tampil</label>
                         <input type="number" name="sort_order" class="form-control" min="1"
                             value="<?php echo $val('sort_order', $nextSortOrder ?? 1); ?>">
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                   <?php echo $checked('is_active', true); ?>>
                            <label class="form-check-label fw-medium" for="is_active">Status Aktif</label>
                        </div>
                    </div>
                </div>
             </div>

             <div class="card border border-light bg-light mt-3">
                 <div class="card-body">
                    <label class="dash-form-label mb-2">FOTO / THUMBNAIL</label>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                    <div class="form-text small mt-2">Format: JPG/PNG/WEBP. Ukuran disarankan: 800x600px.</div>
                 </div>
             </div>
        </div>

      </div>

      <div class="d-flex gap-2 mt-5 pt-3 border-top">
        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>Simpan</button>
        <a href="<?php echo $baseUrl; ?>/admin/our-home/stores" class="btn btn-outline-secondary px-4"><i class="fas fa-arrow-left me-2"></i>Batal</a>
      </div>

    </form>
  </div>
</div>
