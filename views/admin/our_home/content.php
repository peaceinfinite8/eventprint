<?php
// views/admin/our_home/content.php
$content = $content ?? [];
$baseUrl = $baseUrl ?? '';
$csrfToken = $csrfToken ?? '';
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Edit Konten Our Home</h1>
        <p class="text-muted small mb-0">Kelola judul header dan teks untuk halaman Our Home</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= $baseUrl ?>/admin/our-home/stores" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="dash-container-card fade-in delay-1">
    <div class="p-4">
        <form action="<?= $baseUrl ?>/admin/our-home/content/update" method="POST">
            <input type="hidden" name="_token" value="<?= e($csrfToken) ?>">

            <div class="row g-4">
                <div class="col-lg-6">
                    <h5 class="fw-bold text-primary mb-3"><i class="fas fa-heading me-2"></i>Header Halaman</h5>
                    <div class="p-3 bg-light rounded border">
                        <div class="mb-3">
                            <label class="dash-form-label">JUDUL HALAMAN (PAGE TITLE)</label>
                            <input type="text" name="page_title" class="form-control"
                                value="<?= e($content['page_title']) ?>" placeholder="Our Home">
                            <div class="form-text small">Muncul sebagai judul utama di atas grid toko.</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <h5 class="fw-bold text-primary mb-3"><i class="fas fa-images me-2"></i>Header Galeri</h5>
                    <div class="p-3 bg-light rounded border">
                        <div class="mb-3">
                            <label class="dash-form-label">JUDUL GALERI</label>
                            <input type="text" name="gallery_title" class="form-control"
                                value="<?= e($content['gallery_title']) ?>" placeholder="Galeri Mesin Produksi">
                        </div>

                        <div class="mb-3">
                            <label class="dash-form-label">SUB-JUDUL GALERI</label>
                            <textarea name="gallery_subtitle" class="form-control" rows="3"
                                placeholder="Deskripsi singkat..."><?= e($content['gallery_subtitle']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-start mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>