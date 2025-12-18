<?php
// views/admin/home/hero_form.php
$baseUrl   = $vars['baseUrl'] ?? '/eventprint/public';
$baseUrl   = rtrim($baseUrl, '/');
$mode      = $vars['mode'] ?? 'create'; // create|edit
$item      = $vars['item'] ?? [];
$csrfToken = $vars['csrfToken'] ?? '';

$id = (int)($item['id'] ?? 0);

$action = ($mode === 'edit')
  ? $baseUrl . '/admin/home/hero/update/' . $id
  : $baseUrl . '/admin/home/hero/store';

$currentImage = (string)($item['image'] ?? ''); // path relatif di DB
?>

<h1 class="h3 mb-3"><?= $mode === 'edit' ? 'Edit Slide' : 'Tambah Slide' ?></h1>

<div class="card">
  <div class="card-body">

    <form method="post" action="<?= htmlspecialchars($action) ?>" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="old_image" value="<?= htmlspecialchars($currentImage, ENT_QUOTES, 'UTF-8') ?>">

      <div class="mb-3">
        <label class="form-label">Judul</label>
        <input class="form-control" name="title" required
               value="<?= htmlspecialchars($item['title'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Subtitle</label>
        <textarea class="form-control" name="subtitle" rows="3"><?= htmlspecialchars($item['subtitle'] ?? '') ?></textarea>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Badge</label>
          <input class="form-control" name="badge" value="<?= htmlspecialchars($item['badge'] ?? '') ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Upload Image (JPG/PNG/WebP)</label>
          <input class="form-control" type="file" name="image_file" accept="image/jpeg,image/png,image/webp">
          <div class="form-text">Kalau upload, gambar lama (jika ada) akan diganti.</div>

          <?php if ($currentImage): ?>
            <div class="mt-2">
              <div class="small text-muted mb-1">Preview:</div>
              <img
                src="<?= htmlspecialchars($baseUrl . '/' . ltrim($currentImage, '/')) ?>"
                alt="Preview"
                style="max-width: 240px; border-radius: 10px; border: 1px solid #e5e7eb;"
              >
            </div>
          <?php endif; ?>
        </div>

        <div class="col-md-6">
          <label class="form-label">CTA Text</label>
          <input class="form-control" name="cta_text" value="<?= htmlspecialchars($item['cta_text'] ?? '') ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">CTA Link</label>
          <input class="form-control" name="cta_link"
                 value="<?= htmlspecialchars($item['cta_link'] ?? '') ?>"
                 placeholder="/products">
        </div>

        <div class="col-md-3">
          <label class="form-label">Position</label>
          <input type="number" class="form-control" name="position" min="1"
                 value="<?= (int)($item['position'] ?? 1) ?>">
        </div>

        <div class="col-md-3 d-flex align-items-end">
          <div class="form-check">
            <?php $checked = ((int)($item['is_active'] ?? 1) === 1) ? 'checked' : ''; ?>
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= $checked ?>>
            <label class="form-check-label" for="is_active">Aktif</label>
          </div>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-secondary" href="<?= $baseUrl ?>/admin/home/hero">Kembali</a>
      </div>
    </form>

  </div>
</div>
