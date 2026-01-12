<?php
// views/admin/blog/edit.php

$baseUrl = $baseUrl ?? '/eventprint';
$post = $post ?? null;

if (!$post) {
  echo "<p>Artikel tidak ditemukan.</p>";
  return;
}

// Ambil error & old input (kalau sebelumnya validasi gagal)
$errors = class_exists('Validation') ? Validation::errors() : [];
$old = $_SESSION['old_input'] ?? [];
if (class_exists('Validation')) {
  Validation::clear();
}

// CSRF token di–inject dari layout (main.php)
// fallback kalau nggak ada
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">Edit Artikel</h1>
    <p class="text-muted small mb-0">Perbarui konten artikel atau berita</p>
  </div>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger border-0 shadow-sm mb-4">
    <div class="d-flex align-items-start">
      <i class="fas fa-exclamation-circle me-2 mt-1"></i>
      <ul class="mb-0 ps-3">
        <?php foreach ($errors as $fieldErrors): ?>
          <?php foreach ((array) $fieldErrors as $msg): ?>
            <li><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></li>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
<?php endif; ?>

<div class="dash-container-card fade-in delay-1">
  <div class="p-4">
    <form method="post" action="<?php echo $baseUrl; ?>/admin/blog/update/<?php echo (int) $post['id']; ?>"
      enctype="multipart/form-data">

      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

      <div class="row g-4">
        <!-- Left: Main Content -->
        <div class="col-lg-8">
          <h5 class="fw-bold text-primary mb-3"><i class="fas fa-edit me-2"></i>Konten Artikel</h5>

          <div class="mb-3">
            <label class="dash-form-label">JUDUL ARTIKEL <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" required placeholder="Masukkan judul menarik..."
              value="<?php echo htmlspecialchars($old['title'] ?? $post['title'], ENT_QUOTES, 'UTF-8'); ?>">
          </div>

          <div class="mb-3">
            <label class="dash-form-label">EXCERPT (INGKASAN)</label>
            <textarea name="excerpt" rows="3" class="form-control" placeholder="Ringkasan singkat..."><?php
            echo htmlspecialchars($old['excerpt'] ?? ($post['excerpt'] ?? ''), ENT_QUOTES, 'UTF-8');
            ?></textarea>
            <div class="form-text small">Opsional.</div>
          </div>

          <div class="mb-3">
            <label class="dash-form-label">KONTEN LENGKAP <span class="text-danger">*</span></label>
            <textarea name="content" rows="12" class="form-control" required placeholder="Tulis konten..."><?php
            echo htmlspecialchars($old['content'] ?? ($post['content'] ?? ''), ENT_QUOTES, 'UTF-8');
            ?></textarea>
          </div>

          <hr class="border-light my-4">

          <h5 class="fw-bold text-primary mb-3"><i class="fas fa-link me-2"></i>Link Eksternal (Opsional)</h5>
          <div class="row g-3">
            <div class="col-md-8">
              <label class="dash-form-label">EXTERNAL URL</label>
              <input type="url" name="external_url" class="form-control" placeholder="https://..."
                value="<?php echo htmlspecialchars($old['external_url'] ?? ($post['external_url'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-4">
              <label class="dash-form-label">LINK TARGET</label>
              <select name="link_target" class="form-select">
                <?php $target = $old['link_target'] ?? ($post['link_target'] ?? '_self'); ?>
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
                  <?php $selected_cat = $old['post_category'] ?? ($post['post_category'] ?? ''); ?>
                  <option value="">-- Pilih Kategori --</option>
                  <option value="featured" <?php echo $selected_cat === 'featured' ? 'selected' : ''; ?>>Featured</option>
                  <option value="unggulan" <?php echo $selected_cat === 'unggulan' ? 'selected' : ''; ?>>Unggulan</option>
                  <option value="tren" <?php echo $selected_cat === 'tren' ? 'selected' : ''; ?>>Tren</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="dash-form-label">TIPE TAMPILAN</label>
                <select name="post_type" class="form-select">
                  <?php $selected_type = $old['post_type'] ?? ($post['post_type'] ?? 'normal'); ?>
                  <option value="normal" <?php echo $selected_type === 'normal' ? 'selected' : ''; ?>>Normal (Default)
                  </option>
                  <option value="large" <?php echo $selected_type === 'large' ? 'selected' : ''; ?>>Large (Grid Besar)
                  </option>
                  <option value="small" <?php echo $selected_type === 'small' ? 'selected' : ''; ?>>Small (Compact)
                  </option>
                </select>
              </div>

              <div class="form-check form-switch mb-2">
                <input type="checkbox" name="is_featured" class="form-check-input" id="featuredCheck" <?php
                $featured = isset($old['is_featured'])
                  ? (bool) $old['is_featured']
                  : !empty($post['is_featured']);
                echo $featured ? 'checked' : '';
                ?>>
                <label class="form-check-label" for="featuredCheck">Featured (Homepage)</label>
              </div>

              <div class="form-check form-switch">
                <input type="checkbox" name="is_published" class="form-check-input" id="publishedCheck" <?php
                // pakai old kalau ada; kalau tidak pakai nilai dari DB
                $published = isset($old['is_published'])
                  ? (bool) $old['is_published']
                  : !empty($post['is_published']);
                echo $published ? 'checked' : '';
                ?>>
                <label class="form-check-label fw-bold text-success" for="publishedCheck">Publish</label>
              </div>
            </div>
          </div>

          <div class="card border border-light bg-light mt-0 mb-3">
            <div class="card-body">
              <h6 class="fw-bold mb-3">Media & Tampilan</h6>

              <div class="mb-3">
                <label class="dash-form-label mb-2">THUMBNAIL IMAGE</label>

                <?php if (!empty($post['thumbnail'])): ?>
                  <div class="mb-2 position-relative">
                    <img src="<?php echo $baseUrl . '/' . htmlspecialchars($post['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>"
                      alt="" class="img-fluid rounded border shadow-sm w-100 object-fit-cover" style="max-height: 180px;">
                    <div
                      class="position-absolute bottom-0 start-0 w-100 bg-dark bg-opacity-50 text-white text-center py-1 rounded-bottom small">
                      Saat Ini
                    </div>
                  </div>
                <?php else: ?>
                  <div class="text-center p-3 border-2 border-dashed rounded bg-white mb-2">
                    <i class="fas fa-image fa-2x text-muted opacity-25"></i>
                    <div class="small text-muted mt-1">Belum ada gambar</div>
                  </div>
                <?php endif; ?>

                <input type="file" name="thumbnail" class="form-control text-sm mt-2" accept="image/*">
                <small class="text-muted d-block mt-1">Kosongkan jika tidak ingin mengubah.</small>
              </div>

              <div class="mb-3">
                <label class="dash-form-label">BACKGROUND COLOR</label>
                <div class="input-group">
                  <span class="input-group-text bg-white"><i class="fas fa-palette"></i></span>
                  <input type="text" name="bg_color" class="form-control" placeholder="#HEX atau color name"
                    value="<?php echo htmlspecialchars($old['bg_color'] ?? ($post['bg_color'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-5 pt-3 border-top">
        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i> Simpan Perubahan</button>
        <a href="<?php echo $baseUrl; ?>/admin/blog" class="btn btn-outline-secondary px-4"><i
            class="fas fa-arrow-left me-2"></i> Kembali</a>
      </div>

    </form>
  </div>
</div>