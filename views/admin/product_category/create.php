<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$nextSortOrder = $nextSortOrder ?? 0;

// dari controller (kalau ada error / old input)
$errors = $errors ?? [];
$old = $old ?? [];
?>


<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3 mb-0">Create Category</h1>
  <a href="<?php echo $baseUrl; ?>/admin/product-categories" class="btn btn-secondary">
    <i class="fa-solid fa-arrow-left me-2"></i> Back to List
  </a>
</div>

<div class="dash-container-card">
  <div class="dash-header">
    <h5 class="dash-title">Category Details</h5>
  </div>
  <div class="dash-body">

    <form method="post" action="<?php echo $baseUrl; ?>/admin/product-categories/store">

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
              value="<?php echo htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label fw-bold text-muted small text-uppercase">Slug <small
                class="fw-normal text-muted">(Optional)</small></label>
            <input type="text" name="slug" class="form-control" placeholder="auto-generated-if-empty"
              value="<?php echo htmlspecialchars($old['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          </div>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold text-muted small text-uppercase">Description</label>
        <textarea name="description" rows="3" class="form-control"><?php
        echo htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8');
        ?></textarea>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label fw-bold text-muted small text-uppercase">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="<?php
            echo htmlspecialchars(
              $old['sort_order'] ?? (int) $nextSortOrder,
              ENT_QUOTES,
              'UTF-8'
            );
            ?>">
            <div class="form-text">Lower numbers appear first.</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label fw-bold text-muted small text-uppercase">WhatsApp Redirect (Opt)</label>
            <input type="text" name="whatsapp_number" class="form-control" placeholder="e.g. 6281xxxx"
              value="<?php echo htmlspecialchars($old['whatsapp_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          </div>
        </div>
      </div>

      <?php
      $isActiveChecked = 'checked';
      if (array_key_exists('is_active', $old)) {
        $isActiveChecked = ((string) $old['is_active'] === '1') ? 'checked' : '';
      }
      ?>
      <div class="form-check form-switch mb-3">
        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="activeCheck" <?php echo $isActiveChecked; ?>>
        <label class="form-check-label" for="activeCheck">Active Status</label>
      </div>

      <div class="mt-4 pt-4 border-top">
        <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-save me-2"></i> Create
          Category</button>
      </div>

    </form>

  </div>
</div>