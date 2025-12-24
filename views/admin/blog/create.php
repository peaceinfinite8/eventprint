<?php
// views/admin/blog/create.php

$baseUrl = $baseUrl ?? '/eventprint/public';

// dari controller (opsional, kalau nggak ada tetap aman)
$errors = $errors ?? [];
$old    = $old ?? [];
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Tambah Artikel</h1>
        <p class="text-muted small mb-0">Tulis artikel atau berita baru</p>
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
    <form method="post" action="<?php echo $baseUrl; ?>/admin/blog/store" enctype="multipart/form-data">
      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

      <div class="row g-4">
        <!-- Left: Main Content -->
        <div class="col-lg-8">
            <h5 class="fw-bold text-primary mb-3"><i class="fas fa-edit me-2"></i>Konten Artikel</h5>
            
            <div class="mb-3">
                <label class="dash-form-label">JUDUL ARTIKEL <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" required
                       placeholder="Masukkan judul menarik..."
                       value="<?php echo htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="mb-3">
                <label class="dash-form-label">EXCERPT (INGKASAN)</label>
                <textarea name="excerpt" rows="3" class="form-control" placeholder="Ringkasan singkat untuk tampilan list..."><?php echo htmlspecialchars($old['excerpt'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <div class="form-text small">Opsional, jika kosong akan diambil dari awal konten.</div>
            </div>

            <div class="mb-3">
                <label class="dash-form-label">KONTEN LENGKAP <span class="text-danger">*</span></label>
                <textarea name="content" rows="12" class="form-control" required placeholder="Tulis konten lengkap artikel di sini..."><?php echo htmlspecialchars($old['content'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            
            <hr class="border-light my-4">
            
            <h5 class="fw-bold text-primary mb-3"><i class="fas fa-link me-2"></i>Link Eksternal (Opsional)</h5>
            <div class="row g-3">
                 <div class="col-md-8">
                    <label class="dash-form-label">EXTERNAL URL</label>
                    <input type="url" name="external_url" class="form-control" 
                           placeholder="https://blog.example.com/my-post"
                           value="<?php echo htmlspecialchars($old['external_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <small class="text-muted d-block mt-1">Jika diisi, klik pada artikel akan redirect ke URL ini.</small>
                 </div>
                 <div class="col-md-4">
                    <label class="dash-form-label">LINK TARGET</label>
                    <select name="link_target" class="form-select">
                      <?php $target = $old['link_target'] ?? '_self'; ?>
                      <option value="_self" <?php echo $target === '_self' ? 'selected' : ''; ?>>Tab Sama (_self)</option>
                      <option value="_blank" <?php echo $target === '_blank' ? 'selected' : ''; ?>>Tab Baru (_blank)</option>
                    </select>
                 </div>
            </div>
        </div>

        <!-- Right: Settings & Meta -->
        <div class="col-lg-4">
             <div class="card border border-light bg-light mt-0 mb-3">
                 <div class="card-body">
                    <h6 class="fw-bold mb-3">Pengaturan Publikasi</h6>
                    
                    <div class="mb-3">
                        <label class="dash-form-label">KATEGORI</label>
                        <select name="post_category" class="form-select">
                          <?php $selected_cat = $old['post_category'] ?? ''; ?>
                          <option value="">-- Pilih Kategori --</option>
                          <option value="featured" <?php echo $selected_cat === 'featured' ? 'selected' : ''; ?>>Featured</option>
                          <option value="unggulan" <?php echo $selected_cat === 'unggulan' ? 'selected' : ''; ?>>Unggulan</option>
                          <option value="tren" <?php echo $selected_cat === 'tren' ? 'selected' : ''; ?>>Tren</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">TIPE TAMPILAN</label>
                        <select name="post_type" class="form-select">
                          <?php $selected_type = $old['post_type'] ?? 'normal'; ?>
                          <option value="normal" <?php echo $selected_type === 'normal' ? 'selected' : ''; ?>>Normal (Default)</option>
                          <option value="large" <?php echo $selected_type === 'large' ? 'selected' : ''; ?>>Large (Grid Besar)</option>
                          <option value="small" <?php echo $selected_type === 'small' ? 'selected' : ''; ?>>Small (Compact)</option>
                        </select>
                    </div>
                    
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="is_featured" class="form-check-input" id="featuredCheck"
                               <?php echo !empty($old['is_featured']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="featuredCheck">Featured (Homepage)</label>
                    </div>
                    
                    <div class="form-check form-switch">
                        <input type="checkbox" name="is_published" class="form-check-input" id="publishedCheck"
                               <?php echo !empty($old['is_published']) ? 'checked' : ''; ?>>
                        <label class="form-check-label fw-bold text-success" for="publishedCheck">Publish Sekarang</label>
                    </div>
                 </div>
             </div>

             <div class="card border border-light bg-light mt-0 mb-3">
                 <div class="card-body">
                    <h6 class="fw-bold mb-3">Media & Tampilan</h6>
                    
                    <div class="mb-3">
                        <label class="dash-form-label mb-2">THUMBNAIL IMAGE</label>
                        <div class="text-center p-3 border-2 border-dashed rounded bg-white mb-2">
                            <i class="fas fa-image fa-2x text-muted mb-2"></i>
                            <input type="file" name="thumbnail" class="form-control text-sm" accept="image/*">
                        </div>
                        <small class="text-muted">Format: JPG, PNG, WEBP.</small>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">BACKGROUND COLOR</label>
                        <div class="input-group">
                             <span class="input-group-text bg-white"><i class="fas fa-palette"></i></span>
                             <input type="text" name="bg_color" class="form-control" 
                               placeholder="#00AEEF atau blue-1"
                               value="<?php echo htmlspecialchars($old['bg_color'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <small class="text-muted d-block mt-1">Opsional (untuk carousel).</small>
                    </div>
                 </div>
             </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-5 pt-3 border-top">
        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i> Simpan Artikel</button>
        <a href="<?php echo $baseUrl; ?>/admin/blog" class="btn btn-outline-secondary px-4"><i class="fas fa-arrow-left me-2"></i> Batal</a>
      </div>

    </form>
  </div>
</div>
