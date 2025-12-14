<?php
// views/admin/our_store/create.php

$baseUrl = $baseUrl ?? '/eventprint/public';
$errors  = $errors ?? [];
$old     = $old ?? ($_SESSION['old_input'] ?? []);
unset($_SESSION['old_input']);

$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');

$val = function(string $key, $default = '') use ($old) {
    return htmlspecialchars((string)($old[$key] ?? $default), ENT_QUOTES, 'UTF-8');
};

$checked = function(string $key, bool $default = false) use ($old) {
    $v = $old[$key] ?? null;
    if ($v === null) return $default ? 'checked' : '';
    return ($v == 1 || $v === 'on') ? 'checked' : '';
};

$sel = function(string $key, string $value, string $default = '') use ($old) {
    $current = (string)($old[$key] ?? $default);
    return $current === $value ? 'selected' : '';
};
?>

<h1 class="h3 mb-3">Tambah Store</h1>

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

<div class="card">
  <div class="card-body">

    <form method="post"
          action="<?php echo $baseUrl; ?>/admin/our-store/store"
          enctype="multipart/form-data">

      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

      <div class="row g-3">
        <!-- Nama -->
        <div class="col-12">
          <label class="form-label">Nama Store <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" required
                 placeholder="Contoh: Kantor Pusat"
                 value="<?php echo $val('name'); ?>">
        </div>

        <!-- Slug -->
        <div class="col-md-6">
          <label class="form-label">Slug (opsional)</label>
          <input type="text" name="slug" class="form-control"
                 placeholder="Kosongkan untuk auto-generate"
                 value="<?php echo $val('slug'); ?>">
          <div class="form-text">Gunakan huruf kecil dan strip, contoh: <code>kantor-pusat</code></div>
        </div>

        <!-- Office type -->
        <div class="col-md-6">
          <label class="form-label">Tipe Kantor <span class="text-danger">*</span></label>
          <select name="office_type" class="form-select" required>
            <option value="hq" <?php echo $sel('office_type', 'hq', 'branch'); ?>>HQ (Kantor Pusat)</option>
            <option value="branch" <?php echo $sel('office_type', 'branch', 'branch'); ?>>Branch (Cabang)</option>
          </select>
        </div>

        <!-- Address -->
        <div class="col-12">
          <label class="form-label">Alamat <span class="text-danger">*</span></label>
          <textarea name="address" class="form-control" rows="3" required
                    placeholder="Alamat lengkap"><?php echo $val('address'); ?></textarea>
        </div>

        <!-- City -->
        <div class="col-md-6">
          <label class="form-label">Kota <span class="text-danger">*</span></label>
          <input type="text" name="city" class="form-control" required
                 placeholder="Contoh: Jakarta Pusat"
                 value="<?php echo $val('city'); ?>">
        </div>

        <!-- Sort order -->
        <div class="col-md-3">
          <label class="form-label">Urutan Tampil</label>
          <input type="number" name="sort_order" class="form-control" min="1"
                 value="<?php echo $val('sort_order', $nextSortOrder ?? 1); ?>">
        </div>

        <!-- Active -->
        <div class="col-md-3 d-flex align-items-end">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                   <?php echo $checked('is_active', true); ?>>
            <label class="form-check-label" for="is_active">Aktif</label>
          </div>
        </div>

        <hr class="my-2">

        <!-- Phone -->
        <div class="col-md-4">
          <label class="form-label">Telepon (opsional)</label>
          <input type="text" name="phone" class="form-control"
                 placeholder="Contoh: 021-xxxxxxx"
                 value="<?php echo $val('phone'); ?>">
        </div>

        <!-- WhatsApp -->
        <div class="col-md-4">
          <label class="form-label">WhatsApp (opsional)</label>
          <input type="text" name="whatsapp" class="form-control"
                 placeholder="Contoh: 62812xxxxxxx"
                 value="<?php echo $val('whatsapp'); ?>">
        </div>

        <!-- Gmaps -->
        <div class="col-md-4">
          <label class="form-label">Google Maps URL (opsional)</label>
          <input type="text" name="gmaps_url" class="form-control"
                 placeholder="Paste link Google Maps"
                 value="<?php echo $val('gmaps_url'); ?>">
        </div>

        <!-- Thumbnail -->
        <div class="col-12">
          <label class="form-label">Foto / Thumbnail (opsional)</label>
          <input type="file" name="thumbnail" class="form-control" accept="image/*">
          <div class="form-text">Disimpan di <code>uploads/our_store/</code>. Format: JPG/PNG/WEBP.</div>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?php echo $baseUrl; ?>/admin/our-store" class="btn btn-secondary">Batal</a>
      </div>

    </form>
  </div>
</div>
