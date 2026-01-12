<?php
$product = $product ?? null;
$categories = $categories ?? [];

if (!$product) {
  echo "<p>Product not found.</p>";
  return;
}

$errors = Validation::errors();
$old = $_SESSION['old_input'] ?? [];
Validation::clear();

// CSRF token dari layout main.php (pastikan ada)
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>


<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3 mb-0">Edit Product</h1>
  <a href="<?php echo $baseUrl; ?>/admin/products" class="btn btn-secondary">
    <i class="fa-solid fa-arrow-left me-2"></i> Back to List
  </a>
</div>

<div class="dash-container-card">
  <div class="dash-header">
    <h5 class="dash-title">Product Details</h5>
  </div>
  <div class="dash-body">

    <form method="post" action="<?php echo $baseUrl; ?>/admin/products/update/<?php echo (int) $product['id']; ?>"
      enctype="multipart/form-data">

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
              value="<?php echo htmlspecialchars($old['name'] ?? $product['name'], ENT_QUOTES, 'UTF-8'); ?>">
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold text-muted small text-uppercase">Short Description</label>
            <textarea name="short_description" class="form-control" rows="3"><?php
            echo htmlspecialchars($old['short_description'] ?? ($product['short_description'] ?? ''), ENT_QUOTES, 'UTF-8');
            ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold text-muted small text-uppercase">Full Description</label>
            <textarea name="description" class="form-control" rows="6"><?php
            echo htmlspecialchars($old['description'] ?? ($product['description'] ?? ''), ENT_QUOTES, 'UTF-8');
            ?></textarea>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold text-muted small text-uppercase">Shopee URL</label>
              <input type="url" name="shopee_url" class="form-control" placeholder="https://shopee.co.id/..."
                value="<?php echo htmlspecialchars($old['shopee_url'] ?? ($product['shopee_url'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold text-muted small text-uppercase">Tokopedia URL</label>
              <input type="url" name="tokopedia_url" class="form-control" placeholder="https://tokopedia.com/..."
                value="<?php echo htmlspecialchars($old['tokopedia_url'] ?? ($product['tokopedia_url'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label fw-bold text-muted small text-uppercase">Work Time</label>
              <textarea name="work_time" rows="3" class="form-control"
                placeholder="Order 1-10 unit: 1-2 days..."><?php echo htmlspecialchars($old['work_time'] ?? ($product['work_time'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold text-muted small text-uppercase">Product Notes</label>
              <textarea name="product_notes" rows="3" class="form-control"
                placeholder="Prices include frame..."><?php echo htmlspecialchars($old['product_notes'] ?? ($product['product_notes'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label fw-bold text-muted small text-uppercase">Specifications</label>
              <textarea name="specs" rows="3" class="form-control"
                placeholder="Size: 80x200cm..."><?php echo htmlspecialchars($old['specs'] ?? ($product['specs'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold text-muted small text-uppercase">Upload Rules</label>
              <textarea name="upload_rules" rows="3" class="form-control"
                placeholder="Accepted files: .pdf, .ai..."><?php echo htmlspecialchars($old['upload_rules'] ?? ($product['upload_rules'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
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
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                  <?php
                  $selectedId = $old['category_id'] ?? $product['category_id'] ?? '';
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
              <input type="number" step="0.01" min="0" name="base_price" class="form-control fw-bold"
                value="<?php echo htmlspecialchars($old['base_price'] ?? ($product['base_price'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold text-muted small text-uppercase">Stock *</label>
              <input type="number" name="stock" class="form-control" min="0" required
                value="<?php echo htmlspecialchars($old['stock'] ?? ($product['stock'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold text-muted small text-uppercase">Thumbnail</label>
              <input type="file" name="thumbnail" class="form-control mb-2">
              <?php if (!empty($product['thumbnail'])): ?>
                <div class="card p-1">
                  <img src="<?php echo $baseUrl . '/' . htmlspecialchars($product['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>"
                    alt="Thumbnail" class="img-fluid rounded">
                </div>
              <?php endif; ?>
            </div>

            <hr>

            <h6 class="fw-bold mb-2 small text-uppercase text-muted">Discount</h6>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label text-muted small">Type</label>
                <select name="discount_type" class="form-select form-select-sm">
                  <?php $discountType = $old['discount_type'] ?? ($product['discount_type'] ?? 'none'); ?>
                  <option value="none" <?php echo $discountType === 'none' ? 'selected' : ''; ?>>None</option>
                  <option value="percent" <?php echo $discountType === 'percent' ? 'selected' : ''; ?>>%</option>
                  <option value="fixed" <?php echo $discountType === 'fixed' ? 'selected' : ''; ?>>Rp</option>
                </select>
              </div>
              <div class="col-6">
                <label class="form-label text-muted small">Value</label>
                <input type="number" step="0.01" min="0" name="discount_value" class="form-control form-control-sm"
                  value="<?php echo htmlspecialchars($old['discount_value'] ?? ($product['discount_value'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
              </div>
            </div>

            <hr>

            <div class="form-check form-switch mb-2">
              <?php $feat = !empty($old) ? !empty($old['is_featured']) : !empty($product['is_featured']); ?>
              <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="featuredCheck" <?php echo $feat ? 'checked' : ''; ?>>
              <label class="form-check-label" for="featuredCheck">Featured Product</label>
            </div>
            <div class="form-check form-switch">
              <?php $act = !empty($old) ? !empty($old['is_active']) : !empty($product['is_active']); ?>
              <input type="checkbox" name="is_active" value="1" class="form-check-input" id="activeCheck" <?php echo $act ? 'checked' : ''; ?>>
              <label class="form-check-label" for="activeCheck">Active Status</label>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-4 pt-4 border-top">
        <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-save me-2"></i> Update Product</button>
      </div>

    </form>
  </div>
</div>



<!-- Price Tiers Section -->
<div class="dash-container-card mt-4 fade-in delay-2" id="tier-pricing">
  <div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h5 class="fw-bold text-primary mb-1"><i class="fas fa-tags me-2"></i>Tier Pricing</h5>
        <p class="text-muted small mb-0">Atur harga grosir berdasarkan jumlah pembelian (Grosir)</p>
      </div>
      <button type="button" class="btn btn-sm btn-success shadow-sm" id="btnAddTier">
        <i class="fas fa-plus me-1"></i> Add Tier
      </button>
    </div>

    <div id="tiersList" class="position-relative min-h-200">
      <div class="text-center py-5 text-muted">
        <div class="spinner-border text-primary spinner-border-sm mb-2" role="status"></div>
        <p class="small mb-0">Loading price tiers...</p>
      </div>
    </div>
  </div>
</div>

<script>
  // Pass product ID to JavaScript
  window.PRODUCT_ID = <?php echo (int) $product['id']; ?>;
  window.TIERS_API_URL = "<?php echo rtrim($baseUrl, '/') . '/admin/api/products/' . (int) $product['id'] . '/tiers'; ?>";
  window.TIERS_DELETE_API_URL = "<?php echo rtrim($baseUrl, '/') . '/admin/api/tiers/delete'; ?>";
</script>
<script src="<?php echo $baseUrl; ?>/assets/admin/js/product-options-tiers.js"></script>