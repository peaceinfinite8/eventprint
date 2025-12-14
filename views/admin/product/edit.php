<?php
$baseUrl    = $baseUrl ?? '/eventprint/public';
$product    = $product ?? null;
$categories = $categories ?? [];

if (!$product) {
  echo "<p>Product not found.</p>";
  return;
}

$errors = Validation::errors();
$old    = $_SESSION['old_input'] ?? [];
Validation::clear();

// CSRF token dari layout main.php (pastikan ada)
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<h1 class="h3 mb-3">Edit Produk</h1>

<div class="card">
  <div class="card-body">

    <form method="post"
          action="<?php echo $baseUrl; ?>/admin/products/update/<?php echo (int)$product['id']; ?>"
          enctype="multipart/form-data">

      <input type="hidden" name="_token"
             value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $fieldErrors): ?>
              <?php foreach ((array)$fieldErrors as $msg): ?>
                <li><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></li>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Nama Produk *</label>
        <input type="text"
               name="name"
               class="form-control"
               required
               value="<?php echo htmlspecialchars($old['name'] ?? $product['name'], ENT_QUOTES, 'UTF-8'); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-select">
          <option value="">-- Pilih Kategori --</option>
          <?php foreach ($categories as $cat): ?>
            <?php
              $selectedId = $old['category_id'] ?? $product['category_id'] ?? '';
              $isSelected = ((string)$selectedId === (string)$cat['id']);
            ?>
            <option value="<?php echo (int)$cat['id']; ?>" <?php echo $isSelected ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Deskripsi Singkat</label>
        <textarea name="short_description" rows="3" class="form-control"><?php
          echo htmlspecialchars($old['short_description'] ?? ($product['short_description'] ?? ''), ENT_QUOTES, 'UTF-8');
        ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Deskripsi Lengkap</label>
        <textarea name="description" rows="6" class="form-control"><?php
          echo htmlspecialchars($old['description'] ?? ($product['description'] ?? ''), ENT_QUOTES, 'UTF-8');
        ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Thumbnail Produk</label>
        <input type="file" name="thumbnail" class="form-control">
        <?php if (!empty($product['thumbnail'])): ?>
          <div class="mt-2">
            <small class="text-muted d-block mb-1">Thumbnail saat ini:</small>
            <img src="<?php echo $baseUrl . '/' . htmlspecialchars($product['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>"
                 alt="Thumbnail"
                 style="max-height: 140px; border-radius: 6px;">
          </div>
        <?php endif; ?>
        <small class="text-muted d-block">Biarkan kosong jika tidak ingin mengubah thumbnail.</small>
      </div>

      <div class="mb-3">
        <label class="form-label">Harga Dasar</label>
        <input type="number" step="0.01" min="0"
               name="base_price"
               class="form-control"
               value="<?php echo htmlspecialchars($old['base_price'] ?? ($product['base_price'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Stock *</label>
        <input type="number"
               name="stock"
               class="form-control"
               min="0"
               required
               value="<?php echo htmlspecialchars($old['stock'] ?? ($product['stock'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
      </div>

      <div class="form-check mb-2">
        <?php $feat = !empty($old) ? !empty($old['is_featured']) : !empty($product['is_featured']); ?>
        <input type="checkbox" name="is_featured" value="1"
               class="form-check-input" id="featuredCheck"
               <?php echo $feat ? 'checked' : ''; ?>>
        <label class="form-check-label" for="featuredCheck">Produk Featured</label>
      </div>

      <div class="form-check mb-3">
        <?php $act = !empty($old) ? !empty($old['is_active']) : !empty($product['is_active']); ?>
        <input type="checkbox" name="is_active" value="1"
               class="form-check-input" id="activeCheck"
               <?php echo $act ? 'checked' : ''; ?>>
        <label class="form-check-label" for="activeCheck">Aktif</label>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="<?php echo $baseUrl; ?>/admin/products" class="btn btn-secondary">Kembali</a>
      </div>

    </form>
  </div>
</div>
