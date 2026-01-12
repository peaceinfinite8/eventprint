<?php
// views/admin/our_store/edit.php

$baseUrl = $baseUrl ?? '/eventprint';
$item = $item ?? null;

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

$checked = function (string $key, bool $default = false) use ($old, $item) {
    if (array_key_exists($key, $old)) {
        return ($old[$key] == 1 || $old[$key] === 'on') ? 'checked' : '';
    }
    if (isset($item[$key]))
        return !empty($item[$key]) ? 'checked' : '';
    return $default ? 'checked' : '';
};

$sel = function (string $key, string $value, string $fallback = '') use ($old, $item) {
    $current = (string) ($old[$key] ?? ($item[$key] ?? $fallback));
    return $current === $value ? 'selected' : '';
};
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Edit Store</h1>
        <p class="text-muted small mb-0">Perbarui informasi lokasi toko atau kantor Anda</p>
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
        <form action="<?php echo $baseUrl; ?>/admin/our-home/stores/update/<?php echo (int) $item['id']; ?>"
            method="post" enctype="multipart/form-data">

            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

            <div class="row g-4">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <h5 class="fw-bold text-primary mb-3"><i class="fas fa-store me-2"></i>Informasi Utama</h5>

                    <div class="mb-3">
                        <label class="dash-form-label">NAMA STORE <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required
                            value="<?php echo $val('name'); ?>">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">SLUG (OPSIONAL)</label>
                            <input type="text" name="slug" class="form-control"
                                placeholder="Kosongkan untuk pakai slug lama"
                                value="<?php echo htmlspecialchars((string) ($old['slug'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="form-text small">Slug sekarang: <code
                                    class="text-dark bg-light px-1 rounded"><?php echo htmlspecialchars($item['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?></code>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">TIPE KANTOR <span class="text-danger">*</span></label>
                            <select name="office_type" class="form-select" required>
                                <option value="hq" <?php echo $sel('office_type', 'hq', 'branch'); ?>>HQ (Kantor Pusat)
                                </option>
                                <option value="branch" <?php echo $sel('office_type', 'branch', 'branch'); ?>>Branch
                                    (Cabang)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">ALAMAT LENGKAP <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i
                                    class="fas fa-map-marker-alt"></i></span>
                            <textarea name="address" class="form-control" rows="3"
                                required><?php echo $val('address'); ?></textarea>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="dash-form-label">KOTA <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control" required
                                value="<?php echo $val('city'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">GOOGLE MAPS URL</label>
                            <input type="text" name="gmaps_url" class="form-control"
                                value="<?php echo $val('gmaps_url'); ?>">
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-3 mt-4"><i class="fas fa-address-book me-2"></i>Kontak & Jam
                        Operasional</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="dash-form-label">TELEPON</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fas fa-phone"></i></span>
                                <input type="text" name="phone" class="form-control"
                                    value="<?php echo $val('phone'); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="dash-form-label">WHATSAPP</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i
                                        class="fab fa-whatsapp"></i></span>
                                <input type="text" name="whatsapp" class="form-control"
                                    value="<?php echo $val('whatsapp'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">JAM OPERASIONAL (JSON)</label>
                        <textarea name="hours" class="form-control font-monospace text-muted" rows="4"
                            placeholder='["Senin – Jumat : 09.00 – 18.00","Sabtu : 08.00 – 18.00","Minggu : Libur"]'><?php echo $val('hours'); ?></textarea>
                        <div class="form-text small">
                            Format JSON array. Contoh: <code>["Senin-Jumat: 09.00-17.00", "Sabtu: 09.00-14.00"]</code>
                        </div>
                    </div>
                </div>

                <!-- Sidebar / Settings -->
                <div class="col-lg-4">
                    <div class="card border border-light bg-light">
                        <div class="card-body">
                            <label class="dash-form-label mb-2">PENGATURAN</label>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Urutan Tampil</label>
                                <input type="number" name="sort_order" class="form-control" min="1"
                                    value="<?php echo $val('sort_order', 1); ?>">
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                        value="1" <?php echo $checked('is_active'); ?>>
                                    <label class="form-check-label fw-medium" for="is_active">Status Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border border-light bg-light mt-3">
                        <div class="card-body">
                            <label class="dash-form-label mb-2">FOTO / THUMBNAIL</label>

                            <?php if (!empty($item['thumbnail'])): ?>
                                <div class="mb-3 p-2 bg-white rounded border text-center">
                                    <img src="<?php echo $baseUrl . '/' . htmlspecialchars($item['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>"
                                        alt="Thumbnail" class="img-fluid rounded shadow-sm" style="max-height:150px;">
                                    <div class="small text-muted mt-1">Saat ini</div>
                                </div>
                            <?php endif; ?>

                            <input type="file" name="thumbnail" class="form-control"
                                accept="image/jpeg,image/png,image/webp" data-cropper="true" data-aspect-ratio="1">
                            <div class="form-text small mt-2">
                                <i class="fas fa-info-circle me-1"></i> Rasio 1:1 (Kotak, misal 500x500px).
                                Kosongkan jika tidak ingin mengganti foto.
                            </div>

                            <!-- Live Preview -->
                            <div id="imgPreviewContainer" class="mt-3" style="display:none">
                                <label class="form-label small text-muted text-uppercase fw-bold">Preview (Akan
                                    disimpan)</label>
                                <div class="p-2 border rounded bg-light d-inline-block">
                                    <img id="imgPreview"
                                        style="max-width: 200px; max-height: 150px; object-fit: contain;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="d-flex gap-2 mt-5 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>Update Store</button>
                <a href="<?php echo $baseUrl; ?>/admin/our-home/stores" class="btn btn-outline-secondary px-4"><i
                        class="fas fa-arrow-left me-2"></i>Kembali</a>
            </div>

        </form>
    </div>
</div>

<!-- Include Cropper Modal & Handler -->