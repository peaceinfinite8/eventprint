<?php
// views/admin/our_store/edit.php

$baseUrl = $baseUrl ?? '/eventprint/public';
$item    = $item ?? null;

if (!$item) {
  echo "<p class='text-muted'>Data tidak ditemukan.</p>";
  return;
}

$errors = $errors ?? (class_exists('Validation') ? Validation::errors() : []);
$old    = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
if (class_exists('Validation')) Validation::clear();

$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');

$val = function(string $key, $default = '') use ($old, $item) {
    $v = $old[$key] ?? ($item[$key] ?? $default);
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
};

$checked = function(string $key, bool $default = false) use ($old, $item) {
    if (array_key_exists($key, $old)) {
        return ($old[$key] == 1 || $old[$key] === 'on') ? 'checked' : '';
    }
    if (isset($item[$key])) return !empty($item[$key]) ? 'checked' : '';
    return $default ? 'checked' : '';
};

$sel = function(string $key, string $value, string $fallback = '') use ($old, $item) {
    $current = (string)($old[$key] ?? ($item[$key] ?? $fallback));
    return $current === $value ? 'selected' : '';
};
?>

<h1 class="h3 mb-3">Edit Store</h1>

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
    <form action="<?php echo $baseUrl; ?>/admin/our-store/update/<?php echo (int)$item['id']; ?>"
          method="post" enctype="multipart/form-data">

      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

      <div class="row g-3">
        <!-- Nama -->
        <div class="col-12">
          <label class="form-label">Nama Store <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" required
                 value="<?php echo $val('name'); ?>">
        </div>

        <!-- Slug -->
        <div class="col-md-6">
          <label class="form-label">Slug (opsional)</label>
          <input type="text" name="slug" class="form-control"
                 placeholder="Kosongkan untuk pakai slug lama"
                 value="<?php echo htmlspecialchars((string)($old['slug'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
          <div class="form-text">Slug sekarang: <code><?php echo htmlspecialchars($item['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?></code></div>
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
          <textarea name="address" class="form-control" rows="3" required><?php echo $val('address'); ?></textarea>
        </div>

        <!-- City -->
        <div class="col-md-6">
          <label class="form-label">Kota <span class="text-danger">*</span></label>
          <input type="text" name="city" class="form-control" required
                 value="<?php echo $val('city'); ?>">
        </div>

        <!-- Sort order -->
        <div class="col-md-3">
          <label class="form-label">Urutan Tampil</label>
          <input type="number" name="sort_order" class="form-control" min="1"
                 value="<?php echo $val('sort_order', 1); ?>">
        </div>

        <!-- Active -->
        <div class="col-md-3 d-flex align-items-end">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                   <?php echo $checked('is_active'); ?>>
            <label class="form-check-label" for="is_active">Aktif</label>
          </div>
        </div>

        <hr class="my-2">

        <!-- Phone -->
        <div class="col-md-4">
          <label class="form-label">Telepon (opsional)</label>
          <input type="text" name="phone" class="form-control"
                 value="<?php echo $val('phone'); ?>">
        </div>

        <!-- WhatsApp -->
        <div class="col-md-4">
          <label class="form-label">WhatsApp (opsional)</label>
          <input type="text" name="whatsapp" class="form-control"
                 value="<?php echo $val('whatsapp'); ?>">
        </div>

        <!-- Gmaps -->
        <div class="col-md-4">
          <label class="form-label">Google Maps URL (opsional)</label>
          <input type="text" name="gmaps_url" class="form-control"
                 value="<?php echo $val('gmaps_url'); ?>">
        </div>

        <!-- Thumbnail -->
        <div class="col-12">
          <label class="form-label">Foto / Thumbnail (opsional)</label>

          <?php if (!empty($item['thumbnail'])): ?>
            <div class="mb-2">
              <img src="<?php echo $baseUrl . '/' . htmlspecialchars($item['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>"
                   alt="Thumbnail" style="max-height:120px;border-radius:.5rem;">
            </div>
          <?php endif; ?>

          <input type="file" name="thumbnail" class="form-control" accept="image/*">
          <div class="form-text">Kosongkan jika tidak ingin mengganti foto.</div>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="<?php echo $baseUrl; ?>/admin/our-store" class="btn btn-secondary">Kembali</a>
      </div>

    </form>
  </div>
</div>
