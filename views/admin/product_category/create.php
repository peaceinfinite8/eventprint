<?php
$baseUrl       = $baseUrl ?? '/eventprint/public';
$nextSortOrder = $nextSortOrder ?? 0;

// dari controller (kalau ada error / old input)
$errors = $errors ?? [];
$old    = $old ?? [];
?>

<h1 class="h3 mb-3">Tambah Kategori Produk</h1>

<div class="card">
  <div class="card-body">

    <form method="post"
          action="<?php echo $baseUrl; ?>/admin/product-categories/store">

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
               value="<?php echo htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">
          Slug (opsional – kalau dikosongkan akan digenerate dari nama)
        </label>
        <input type="text"
               name="slug"
               class="form-control"
               placeholder="contoh: kartu-nama, banner-x-stand"
               value="<?php echo htmlspecialchars($old['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Deskripsi (opsional)</label>
        <textarea name="description"
                  rows="3"
                  class="form-control"><?php
          echo htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8');
        ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Urutan Tampil (sort_order)</label>
        <input type="number"
               name="sort_order"
               class="form-control"
               value="<?php
                 echo htmlspecialchars(
                   $old['sort_order'] ?? (int)$nextSortOrder,
                   ENT_QUOTES,
                   'UTF-8'
                 );
               ?>">
        <small class="text-muted">
          Angka lebih kecil akan tampil lebih atas di daftar.
        </small>
      </div>

      <?php
      // default: checked (aktif)
      // kalau ada old input, pakai value old
      $isActiveChecked = 'checked';
      if (array_key_exists('is_active', $old)) {
          $isActiveChecked = ((string)$old['is_active'] === '1') ? 'checked' : '';
      }
      ?>
      <div class="form-check mb-3">
        <input type="checkbox"
               name="is_active"
               value="1"
               class="form-check-input"
               id="activeCheck"
               <?php echo $isActiveChecked; ?>>
        <label class="form-check-label" for="activeCheck">
          Aktif
        </label>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">Simpan Kategori</button>
        <a href="<?php echo $baseUrl; ?>/admin/product-categories"
           class="btn btn-secondary">Kembali</a>
      </div>

    </form>

  </div>
</div>
