<?php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$csrfToken = $vars['csrfToken'] ?? '';
$mode = $vars['mode'] ?? 'create'; // create | edit
$item = $vars['item'] ?? [];

$pageTitle = ($mode === 'edit') ? 'Edit Small Banner' : 'Tambah Small Banner';
$actionUrl = ($mode === 'edit')
    ? $baseUrl . '/admin/home/small-banner/update/' . ($item['id'] ?? 0)
    : $baseUrl . '/admin/home/small-banner/store';
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient"><?= $pageTitle ?></h1>
        <p class="text-muted small mb-0">Kelola banner kecil / promo di beranda</p>
    </div>
</div>

<div class="dash-container-card fade-in delay-1">
    <div class="p-4">
        <form method="post" action="<?= $actionUrl ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <?php if ($mode === 'edit'): ?>
                <input type="hidden" name="old_image" value="<?= htmlspecialchars($item['image'] ?? '') ?>">
            <?php endif; ?>

            <div class="row g-4">
                <!-- Left Column: Main Info -->
                <div class="col-lg-8">
                    <div class="mb-4">
                        <label class="dash-form-label">ALT TEXT / JUDUL</label>
                        <input type="text" class="form-control" name="title"
                            value="<?= htmlspecialchars($item['title'] ?? '') ?>" required
                            placeholder="Contoh: Promo kemerdekaan">
                        <div class="form-text text-muted">Digunakan sebagai alt text gambar untuk SEO.</div>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">TARGET LINK (OPSIONAL)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-link"></i></span>
                            <input type="text" class="form-control" name="cta_link"
                                value="<?= htmlspecialchars($item['cta_link'] ?? '') ?>"
                                placeholder="https://... atau /products">
                        </div>
                        <div class="form-text text-muted">Jika diisi, banner akan bisa diklik menuju link ini.</div>
                    </div>
                </div>

                <!-- Right Column: Image & Settings -->
                <div class="col-lg-4 border-start-lg ps-lg-4">
                    <div class="bg-light p-3 rounded-3 mb-4 border">
                        <label class="dash-form-label mb-2">GAMBAR BANNER</label>
                        <?php if (!empty($item['image'])): ?>
                            <div class="mb-3 text-center">
                                <img src="<?= $baseUrl . '/' . ltrim($item['image'], '/') ?>"
                                    class="img-fluid rounded shadow-sm border"
                                    style="max-height: 120px; width: 100%; object-fit: cover;">
                            </div>
                        <?php endif; ?>

                        <input type="file" class="form-control form-control-sm" name="image_file" accept="image/*"
                            <?= ($mode === 'create') ? 'required' : '' ?>>
                        <div class="text-muted extra-small mt-2">
                            <i class="fas fa-info-circle me-1"></i> Format: JPG, PNG, WebP. Max 3MB.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">URUTAN</label>
                        <input type="number" class="form-control" name="position"
                            value="<?= (int) ($item['position'] ?? 1) ?>" min="1">
                    </div>

                    <div class="mb-3">
                        <div
                            class="form-check form-switch p-0 d-flex align-items-center gap-3 bg-white p-3 rounded border">
                            <label class="form-check-label mb-0 fw-medium" for="is_active">Status Aktif</label>
                            <input class="form-check-input ms-auto m-0" type="checkbox" role="switch" name="is_active"
                                id="is_active" value="1" <?= (isset($item['is_active']) && $item['is_active'] == 1) ? 'checked' : '' ?>>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-5 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
                <a href="<?= $baseUrl ?>/admin/home/small-banner" class="btn btn-outline-secondary px-4">
                    <i class="fas fa-arrow-left me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>