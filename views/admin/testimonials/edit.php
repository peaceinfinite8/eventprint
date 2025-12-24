<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$item = $item ?? null;
$errors = $errors ?? [];
$old = $old ?? [];
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');

if (!$item) {
    echo "<p>Testimonial tidak ditemukan.</p>";
    return;
}
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Edit Testimonial</h1>
        <p class="text-muted small mb-0">Perbarui ulasan pelanggan</p>
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
    <form method="post" action="<?php echo $baseUrl; ?>/admin/testimonials/update/<?php echo (int)$item['id']; ?>" enctype="multipart/form-data">
      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

       <div class="row g-4">
        <div class="col-lg-8">
            <h5 class="fw-bold text-primary mb-3"><i class="fas fa-comment-alt me-2"></i>Konten Testimonial</h5>
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="dash-form-label">NAMA PELANGGAN <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required
                           value="<?php echo htmlspecialchars($old['name'] ?? $item['name'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="dash-form-label">POSISI / JABATAN</label>
                    <input type="text" name="position" class="form-control"
                           value="<?php echo htmlspecialchars($old['position'] ?? ($item['position'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="dash-form-label">PESAN / TESTIMONIAL <span class="text-danger">*</span></label>
                <textarea name="message" class="form-control" rows="5" required><?php echo htmlspecialchars($old['message'] ?? $item['message'], ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            
             <div class="row g-3">
                 <div class="col-md-6">
                     <label class="dash-form-label">RATING</label>
                     <select name="rating" class="form-select">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>" <?= ($old['rating'] ?? $item['rating']) == $i ? 'selected' : '' ?>><?= $i ?> Bintang (<?= str_repeat('⭐', $i) ?>)</option>
                        <?php endfor; ?>
                     </select>
                 </div>
                 <div class="col-md-6">
                    <label class="dash-form-label">URUTAN TAMPIL</label>
                    <input type="number" name="sort_order" class="form-control" min="0"
                           value="<?php echo htmlspecialchars($old['sort_order'] ?? $item['sort_order'], ENT_QUOTES, 'UTF-8'); ?>">
                 </div>
            </div>
        </div>

        <div class="col-lg-4">
             <div class="card border border-light bg-light mt-0">
                 <div class="card-body">
                    <label class="dash-form-label mb-2">FOTO PROFIL</label>
                    
                     <?php if (!empty($item['photo'])): ?>
                      <div class="mb-3 text-center p-3 bg-white rounded border">
                        <img src="<?php echo $baseUrl . '/' . htmlspecialchars($item['photo'], ENT_QUOTES, 'UTF-8'); ?>"
                             alt="Photo"
                             class="rounded-circle shadow-sm object-fit-cover"
                             style="width: 100px; height: 100px;">
                        <input type="hidden" name="old_photo" value="<?php echo htmlspecialchars($item['photo'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="small text-muted mt-2">Foto Saat Ini</div>
                      </div>
                    <?php else: ?>
                        <div class="mb-3 text-center p-4 border border-dashed rounded bg-white">
                            <i class="fas fa-user-circle fa-3x text-muted opacity-25"></i>
                            <div class="small text-muted mt-2">Belum ada foto</div>
                        </div>
                    <?php endif; ?>

                    <label class="form-label small text-muted">Ganti Foto (Opsional)</label>
                    <input type="file" name="photo" class="form-control text-sm" accept="image/*">
                    <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah.</small>

                    <div class="form-check form-switch mt-4">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1"
                               <?php echo ($old['is_active'] ?? $item['is_active']) ? 'checked' : ''; ?>>
                        <label class="form-check-label fw-medium" for="is_active">Status Aktif</label>
                    </div>
                 </div>
             </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-5 pt-3 border-top">
        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
        <a href="<?php echo $baseUrl; ?>/admin/testimonials" class="btn btn-outline-secondary px-4"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
      </div>
    </form>
  </div>
</div>
