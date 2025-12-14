<?php
$baseUrl    = $baseUrl ?? '/eventprint/public';
$categories = $categories ?? [];

$errors = Validation::errors();
$old    = $_SESSION['old_input'] ?? [];
Validation::clear();

$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<h1 class="h3 mb-3">Tambah Produk</h1>

<div class="card">
  <div class="card-body">

    <form method="post"
          action="<?php echo $baseUrl; ?>/admin/products/store"
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
               value="<?php echo htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-select">
          <option value="">— Pilih Kategori —</option>
          <?php foreach ($categories as $cat): ?>
            <?php
              $selectedId = $old['category_id'] ?? '';
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
        <textarea name="short_description" class="form-control" rows="3"><?php
          echo htmlspecialchars($old['short_description'] ?? '', ENT_QUOTES, 'UTF-8');
        ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Deskripsi Lengkap</label>
        <textarea name="description" class="form-control" rows="6"><?php
          echo htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8');
        ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Harga Dasar</label>
        <input type="number"
               name="base_price"
               class="form-control"
               min="0"
               step="0.01"
               value="<?php echo htmlspecialchars($old['base_price'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Stock *</label>
        <input type="number"
               name="stock"
               class="form-control"
               min="0"
               required
               value="<?php echo htmlspecialchars($old['stock'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Thumbnail Produk</label>
        <input type="file" name="thumbnail" class="form-control">
      </div>

      <div class="form-check mb-3">
        <input type="checkbox"
               name="is_featured"
               value="1"
               class="form-check-input"
               id="feat"
               <?php echo !empty($old['is_featured']) ? 'checked' : ''; ?>>
        <label for="feat" class="form-check-label">Tandai sebagai produk unggulan</label>
      </div>

      <div class="form-check mb-4">
        <input type="checkbox"
               name="is_active"
               value="1"
               class="form-check-input"
               id="active"
               <?php echo empty($old) ? 'checked' : (!empty($old['is_active']) ? 'checked' : ''); ?>>
        <label for="active" class="form-check-label">Aktif</label>
      </div>

      <button class="btn btn-primary">Simpan Produk</button>
      <a href="<?php echo $baseUrl; ?>/admin/products" class="btn btn-secondary">Kembali</a>
    </form>

  </div>
</div>
