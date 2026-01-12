<?php
$baseUrl = $baseUrl ?? '/eventprint';
$category = $category ?? null;

if (!$category) {
  echo "<p>Kategori tidak ditemukan.</p>";
  return;
}

// errors & old dari controller (kalau ada)
$errors = $errors ?? [];
$old = $old ?? [];

// kalau ada old input (habis gagal submit), override nilai category
if (!empty($old)) {
  $category['name'] = $old['name'] ?? $category['name'];
  $category['slug'] = $old['slug'] ?? $category['slug'];
  $category['description'] = $old['description'] ?? $category['description'];
  $category['sort_order'] = $old['sort_order'] ?? $category['sort_order'];
  if (array_key_exists('is_active', $old)) {
    $category['is_active'] = (int) $old['is_active'];
  }
}
?>


<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3 mb-0">Edit Category</h1>
  <a href="<?php echo $baseUrl; ?>/admin/product-categories" class="btn btn-secondary">
    <i class="fa-solid fa-arrow-left me-2"></i> Back to List
  </a>
</div>

<div class="dash-container-card">
  <div class="dash-header">
    <h5 class="dash-title">Category Details</h5>
  </div>
  <div class="dash-body">

    <form method="post"
      action="<?php echo $baseUrl; ?>/admin/product-categories/update/<?php echo (int) $category['id']; ?>"
      enctype="multipart/form-data">

      <input type="hidden" name="_token"
        value="<?php echo htmlspecialchars($csrfToken ?? Security::csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mb-4">
          <ul class="mb-0 ps-3">
            <?php foreach ($errors as $fieldErrors): ?>
              <?php foreach ($fieldErrors as $msg): ?>
                <li><?php echo htmlspecialchars($msg); ?></li>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="row g-3">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label fw-bold text-muted small text-uppercase">Category Name</label>
            <input type="text" name="name" class="form-control" required
              value="<?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label fw-bold text-muted small text-uppercase">Slug <small
                class="fw-normal text-muted">(Optional)</small></label>
            <input type="text" name="slug" class="form-control"
              value="<?php echo htmlspecialchars($old['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <div class="form-text">Current:
              <code><?php echo htmlspecialchars($category['slug'], ENT_QUOTES, 'UTF-8'); ?></code>
            </div>
          </div>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold text-muted small text-uppercase">Description</label>
        <textarea name="description" rows="3" class="form-control"><?php
        echo htmlspecialchars($category['description'] ?? '', ENT_QUOTES, 'UTF-8');
        ?></textarea>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label fw-bold text-muted small text-uppercase">Sort Order</label>
            <input type="number" name="sort_order" class="form-control"
              value="<?php echo htmlspecialchars((int) $category['sort_order'], ENT_QUOTES, 'UTF-8'); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label fw-bold text-muted small text-uppercase">WhatsApp Redirect</label>
            <input type="text" name="whatsapp_number" class="form-control" placeholder="e.g. 6281xxxx"
              value="<?php echo htmlspecialchars($category['whatsapp_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          </div>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold text-muted small text-uppercase">Service Icon (Optional)</label>

        <?php if (!empty($category['icon'])): ?>
          <div class="mb-2 p-2 border rounded bg-light d-inline-block text-center">
            <?php if (preg_match('/^bi-/', $category['icon'])): ?>
              <!-- Text Icon (Legacy) -->
              <i class="<?php echo htmlspecialchars($category['icon']); ?> fs-2 text-primary"></i>
              <div class="small text-muted mt-1">Icon Bootstrap:
                <code><?php echo htmlspecialchars($category['icon']); ?></code>
              </div>
            <?php else: ?>
              <!-- Image Icon -->
              <img src="<?php echo $baseUrl . '/' . htmlspecialchars($category['icon']); ?>" alt="Icon"
                style="width: 64px; height: 64px; object-fit: contain;">
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <input type="file" name="icon" class="form-control" accept="image/jpeg,image/png,image/webp,image/svg+xml"
          data-cropper="true" data-aspect-ratio="1">
        <div class="form-text small">
          <i class="fas fa-info-circle me-1"></i> Rasio 1:1. Ukuran disarankan 96x96px. Format: JPG, PNG, WebP, SVG.
        </div>

        <!-- Live Preview -->
        <div id="imgPreviewContainer" class="mt-3" style="display:none">
          <label class="form-label small text-muted text-uppercase fw-bold">Preview (Will be saved)</label>
          <div class="p-2 border rounded bg-light d-inline-block">
            <img id="imgPreview" style="max-width: 150px; max-height: 150px; object-fit: contain;">
          </div>
        </div>
      </div>

      <div class="form-check form-switch mb-3">
        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="activeCheck" <?php echo !empty($category['is_active']) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="activeCheck">Active Status</label>
      </div>

      <div class="mt-4 pt-4 border-top">
        <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-save me-2"></i> Update
          Category</button>
      </div>

    </form>

  </div>
</div>

<!-- Include Cropper Modal & Handler -->