<?php
$baseUrl  = $baseUrl ?? '/eventprint/public';
$category = $category ?? null;

if (!$category) {
    echo "<p>Kategori tidak ditemukan.</p>";
    return;
}

// errors & old dari controller (kalau ada)
$errors = $errors ?? [];
$old    = $old ?? [];

// kalau ada old input (habis gagal submit), override nilai category
if (!empty($old)) {
    $category['name']        = $old['name']        ?? $category['name'];
    $category['slug']        = $old['slug']        ?? $category['slug'];
    $category['description'] = $old['description'] ?? $category['description'];
    $category['sort_order']  = $old['sort_order']  ?? $category['sort_order'];
    if (array_key_exists('is_active', $old)) {
        $category['is_active'] = (int)$old['is_active'];
    }
}
?>

<h1 class="h3 mb-3">Edit Kategori Produk</h1>

<div class="card">
  <div class="card-body">

    <form method="post"
          action="<?php echo $baseUrl; ?>/admin/product-categories/update/<?php echo (int)$category['id']; ?>">

      <input type="hidden" name="_token"
             value="<?php echo htmlspecialchars($csrfToken ?? Security::csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $fieldErrors): ?>
              <?php foreach ($fieldErrors as $msg): ?>
                <li><?php echo htmlspecialchars($msg); ?></li>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Nama Kategori</label>
        <input type="text"
               name="name"
               class="form-control"
               required
               value="<?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">
          Slug (opsional – kosongkan untuk pakai slug sebelumnya)
        </label>
        <input type="text"
               name="slug"
               class="form-control"
               value="<?php echo htmlspecialchars($old['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <small class="text-muted">
          Slug saat ini:
          <code><?php echo htmlspecialchars($category['slug'], ENT_QUOTES, 'UTF-8'); ?></code>
        </small>
      </div>

      <div class="mb-3">
        <label class="form-label">Deskripsi (opsional)</label>
        <textarea name="description"
                  rows="3"
                  class="form-control"><?php
          echo htmlspecialchars($category['description'] ?? '', ENT_QUOTES, 'UTF-8');
        ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Urutan Tampil (sort_order)</label>
        <input type="number"
               name="sort_order"
               class="form-control"
               value="<?php echo htmlspecialchars((int)$category['sort_order'], ENT_QUOTES, 'UTF-8'); ?>">
      </div>

      <div class="form-check mb-3">
        <input type="checkbox"
               name="is_active"
               value="1"
               class="form-check-input"
               id="activeCheck"
            <?php echo !empty($category['is_active']) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="activeCheck">
          Aktif
        </label>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="<?php echo $baseUrl; ?>/admin/product-categories"
           class="btn btn-secondary">Kembali</a>
      </div>

    </form>

  </div>
</div>
