<?php
// views/admin/our_home/gallery/edit.php

$baseUrl = $baseUrl ?? '/eventprint/public';
$item = $item ?? null;
$stores = $stores ?? [];

if (!$item) {
    echo "<p class='text-muted'>Data tidak ditemukan.</p>";
    return;
}

$errors = $errors ?? (class_exists('Validation') ? Validation::errors() : []);
$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
if (class_exists('Validation'))
    Validation::clear();

$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');

$val = function (string $key, $default = '') use ($old, $item) {
    $v = $old[$key] ?? ($item[$key] ?? $default);
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
};
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Edit Gallery Photo</h1>
        <p class="text-muted small mb-0">Update informasi atau ganti foto galeri</p>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <div class="d-flex align-items-start">
            <i class="fas fa-exclamation-circle me-2 mt-1"></i>
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $fieldErrors): ?>
                    <?php foreach ((array) $fieldErrors as $msg): ?>
                        <li><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<div class="dash-container-card fade-in delay-1">
    <div class="p-4">
        <form action="<?php echo $baseUrl; ?>/admin/our-home/gallery/update/<?php echo (int) $item['id']; ?>"
            method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

            <div class="row g-4">
                <div class="col-lg-7">
                    <h5 class="fw-bold text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Foto</h5>

                    <div class="mb-3">
                        <label class="dash-form-label">STORE TERKAIT <span class="text-danger">*</span></label>
                        <select name="store_id" class="form-select" required>
                            <?php foreach ($stores as $store): ?>
                                <option value="<?php echo (int) $store['id']; ?>" <?php echo (($old['store_id'] ?? $item['store_id']) == $store['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($store['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">CAPTION (OPSIONAL)</label>
                        <input type="text" name="caption" class="form-control" maxlength="255"
                            placeholder="Deskripsi foto" value="<?php echo $val('caption'); ?>">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">SORT ORDER</label>
                            <input type="number" name="sort_order" class="form-control" min="1"
                                value="<?php echo $val('sort_order'); ?>">
                            <div class="form-text small">Urutan tampil (angka kecil muncul duluan)</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="p-3 bg-light rounded border">
                        <label class="dash-form-label mb-2">PREVIEW SAAT INI</label>
                        <div class="mb-3">
                            <img src="<?php echo $baseUrl . '/' . htmlspecialchars($item['image_path'], ENT_QUOTES, 'UTF-8'); ?>"
                                alt="Current" class="img-fluid rounded shadow-sm border">
                        </div>

                        <div class="mt-4">
                            <label class="dash-form-label mb-2">GANTI GAMBAR (OPSIONAL)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div class="form-text mt-2 small">Format: JPG/PNG/WEBP. Kosongkan jika tidak ingin
                                mengganti.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-5 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i> Update Photo
                </button>
                <a href="<?php echo $baseUrl; ?>/admin/our-home/gallery" class="btn btn-outline-secondary px-4">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>