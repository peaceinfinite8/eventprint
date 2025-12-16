<?php
// admin/home/hero_form.php
$baseUrl   = $baseUrl ?? '/eventprint/public';
$mode      = $mode ?? 'create'; // create|edit
$item      = $item ?? [];
$csrfToken = $csrfToken ?? Security::csrfToken();

$key = (string)($item['item_key'] ?? '');

$action = ($mode === 'edit')
  ? $baseUrl . '/admin/home/hero/update/' . urlencode($key)
  : $baseUrl . '/admin/home/hero/store';
?>

<h1 class="h3 mb-3"><?= $mode === 'edit' ? 'Edit Slide' : 'Tambah Slide' ?></h1>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?= htmlspecialchars($action) ?>">
      <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">

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
          <input class="form-control" name="badge"
                 value="<?= htmlspecialchars($item['badge'] ?? '') ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Image (path/url)</label>
          <input class="form-control" name="image"
                 value="<?= htmlspecialchars($item['image'] ?? '') ?>"
                 placeholder="/uploads/settings/banner.jpg">
        </div>

        <div class="col-md-6">
          <label class="form-label">CTA Text</label>
          <input class="form-control" name="cta_text"
                 value="<?= htmlspecialchars($item['cta_text'] ?? '') ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">CTA Link</label>
          <input class="form-control" name="cta_link"
                 value="<?= htmlspecialchars($item['cta_link'] ?? '') ?>"
                 placeholder="/contact#order">
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
        <button class="btn btn-primary">Simpan</button>
        <a class="btn btn-secondary" href="<?= $baseUrl ?>/admin/home/hero">Kembali</a>
      </div>
    </form>
  </div>
</div>
