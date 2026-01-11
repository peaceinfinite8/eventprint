<?php
$baseUrl = $vars['baseUrl'] ?? '/eventprint';
$content = $vars['content'] ?? [];
$csrfToken = $vars['csrfToken'] ?? '';
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Why Choose Us</h1>
        <p class="text-muted small mb-0">Edit konten bagian "Mengapa Memilih Kami"</p>
    </div>
</div>

<div class="dash-container-card fade-in delay-1">
    <div class="p-4">
        <form method="post" action="<?= $baseUrl ?>/admin/home/why-choose/update" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

            <div class="row g-4">
                <!-- Left Column: Text Content -->
                <div class="col-lg-8">
                    <div class="mb-4">
                        <label class="dash-form-label">JUDUL UTAMA</label>
                        <input class="form-control" name="title"
                            value="<?= htmlspecialchars($content['title'] ?? '') ?>"
                            placeholder="Contoh: Mengapa Memilih Kami?">
                    </div>

                    <div class="mb-4">
                        <label class="dash-form-label">SUB JUDUL</label>
                        <input class="form-control" name="subtitle"
                            value="<?= htmlspecialchars($content['subtitle'] ?? '') ?>"
                            placeholder="Contoh: Keunggulan layanan kami">
                    </div>

                    <div class="mb-3">
                        <label class="dash-form-label">DESKRIPSI</label>
                        <textarea class="form-control" name="description" rows="6"
                            placeholder="Jelaskan keunggulan toko Anda..."><?= htmlspecialchars($content['description'] ?? '') ?></textarea>
                        <div class="form-text text-muted">Pisahkan paragraf dengan baris baru.</div>
                    </div>
                </div>

                <!-- Right Column: Image -->
                <div class="col-lg-4 border-start-lg ps-lg-4">
                    <div class="bg-light p-3 rounded-3 mb-4 border">
                        <label class="dash-form-label mb-2">GAMBAR ILLUSTRASI</label>
                        <?php if (!empty($content['image'])): ?>
                            <div class="mb-3 text-center">
                                <img src="<?= $baseUrl . '/' . ltrim($content['image'], '/') ?>"
                                    class="img-fluid rounded shadow-sm border"
                                    style="max-height: 200px; width: auto; object-fit: cover;">
                            </div>
                        <?php endif; ?>

                        <input class="form-control form-control-sm" type="file" name="image_file"
                            accept="image/jpeg,image/png,image/webp" data-cropper="true" data-aspect-ratio="1.3333">
                        <div class="text-muted extra-small mt-2">
                            <i class="fas fa-info-circle me-1"></i> Rasio 4:3. Biarkan kosong jika tidak ingin mengubah
                            gambar.
                        </div>

                        <!-- Live Preview -->
                        <div id="imgPreviewContainer" class="mt-3" style="display:none">
                            <label class="form-label small text-muted text-uppercase fw-bold">Preview (Akan
                                disimpan)</label>
                            <div class="p-2 border rounded bg-light d-inline-block">
                                <img id="imgPreview" style="max-width: 200px; max-height: 150px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-5 pt-3 border-top">
                <button class="btn btn-primary px-4" type="submit">
                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                </button>
                <a class="btn btn-outline-secondary px-4" href="<?= $baseUrl ?>/admin/home">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Include Cropper Modal & Handler -->