<?php
$categories = $categories ?? [];

$errors = Validation::errors();
$old = $_SESSION['old_input'] ?? [];
Validation::clear();

$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>


<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3 mb-0">Create Product</h1>
  <a href="<?php echo $baseUrl; ?>/admin/products" class="btn btn-secondary">
    <i class="fa-solid fa-arrow-left me-2"></i> Back to List
  </a>
</div>

<div class="dash-container-card">
  <div class="dash-header">
    <h5 class="dash-title">Product Details</h5>
  </div>
  <div class="dash-body">

    <form method="post" action="<?php echo $baseUrl; ?>/admin/products/store" enctype="multipart/form-data">
      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mb-4">
          <ul class="mb-0 ps-3">
            <?php foreach ($errors as $fieldErrors): ?>
              <?php foreach ((array) $fieldErrors as $msg): ?>
                <li><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></li>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="row g-4">
        <!-- Left Column: Main Info -->
        <div class="col-lg-8">
          <div class="mb-3">
            <label class="form-label fw-bold text-muted small text-uppercase">Product Name *</label>
            <input type="text" name="name" class="form-control" required
              value="<?php echo htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold text-muted small text-uppercase">Short Description</label>
            <textarea name="short_description" class="form-control" rows="3"><?php
            echo htmlspecialchars($old['short_description'] ?? '', ENT_QUOTES, 'UTF-8');
            ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold text-muted small text-uppercase">Full Description</label>
            <textarea name="description" class="form-control" rows="6"><?php
            echo htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8');
            ?></textarea>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold text-muted small text-uppercase">Shopee URL</label>
              <input type="url" name="shopee_url" class="form-control" placeholder="https://shopee.co.id/..."
                value="<?php echo htmlspecialchars($old['shopee_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold text-muted small text-uppercase">Tokopedia URL</label>
              <input type="url" name="tokopedia_url" class="form-control" placeholder="https://tokopedia.com/..."
                value="<?php echo htmlspecialchars($old['tokopedia_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label fw-bold text-muted small text-uppercase">Work Time</label>
              <textarea name="work_time" rows="3" class="form-control"
                placeholder="Order 1-10 unit: 1-2 days..."><?php echo htmlspecialchars($old['work_time'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold text-muted small text-uppercase">Product Notes</label>
              <textarea name="product_notes" rows="3" class="form-control"
                placeholder="Prices include frame..."><?php echo htmlspecialchars($old['product_notes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label fw-bold text-muted small text-uppercase">Specifications</label>
              <textarea name="specs" rows="3" class="form-control"
                placeholder="Size: 80x200cm..."><?php echo htmlspecialchars($old['specs'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold text-muted small text-uppercase">Upload Rules</label>
              <textarea name="upload_rules" rows="3" class="form-control"
                placeholder="Accepted files: .pdf, .ai..."><?php echo htmlspecialchars($old['upload_rules'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
          </div>
        </div>

        <!-- Right Column: Settings & Price -->
        <div class="col-lg-4">
          <div class="p-3 bg-light rounded border mb-4">
            <h6 class="fw-bold mb-3">Settings</h6>
            <div class="mb-3">
              <label class="form-label fw-bold text-muted small text-uppercase">Category</label>
              <select name="category_id" class="form-select">
                <option value="">— Select Category —</option>
                <?php foreach ($categories as $cat): ?>
                  <?php
                  $selectedId = $old['category_id'] ?? '';
                  $isSelected = ((string) $selectedId === (string) $cat['id']);
                  ?>
                  <option value="<?php echo (int) $cat['id']; ?>" <?php echo $isSelected ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold text-muted small text-uppercase">Base Price (Rp)</label>
              <input type="number" name="base_price" class="form-control fw-bold" min="0" step="0.01"
                value="<?php echo htmlspecialchars($old['base_price'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold text-muted small text-uppercase">Stock *</label>
              <input type="number" name="stock" class="form-control" min="0" required
                value="<?php echo htmlspecialchars($old['stock'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold text-muted small text-uppercase">Thumbnail</label>
              <input type="file" name="thumbnail" class="form-control">
            </div>

            <hr>

            <div class="form-check form-switch mb-2">
              <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="feat" <?php echo !empty($old['is_featured']) ? 'checked' : ''; ?>>
              <label for="feat" class="form-check-label">Featured Product</label>
            </div>
            <div class="form-check form-switch">
              <input type="checkbox" name="is_active" value="1" class="form-check-input" id="active" <?php echo empty($old) ? 'checked' : (!empty($old['is_active']) ? 'checked' : ''); ?>>
              <label for="active" class="form-check-label">Active Status</label>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-4 pt-4 border-top">
        <button class="btn btn-primary px-4"><i class="fa-solid fa-save me-2"></i> Save Product</button>
      </div>

    </form>

  </div>
</div>