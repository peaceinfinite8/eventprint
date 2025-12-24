<?php
// views/admin/our_home/gallery/index.php

$baseUrl = $baseUrl ?? '/eventprint/public';
$items = $items ?? [];
$pagination = $pagination ?? ['total' => 0, 'page' => 1, 'per_page' => 20];

$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Gallery Management</h1>
        <p class="text-muted small mb-0">Kelola foto-foto galeri di halaman Our Home</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= $baseUrl ?>/admin/our-home/stores" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Back to Stores
        </a>
        <a href="<?php echo $baseUrl; ?>/admin/our-home/gallery/create" class="btn btn-primary shadow-sm">
            <i class="fas fa-upload me-2"></i>Upload Photo
        </a>
    </div>
</div>

<div class="dash-container-card fade-in delay-1">
    <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
        <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-images me-2"></i>Daftar Foto
            (<?php echo $pagination['total']; ?>)</h5>
    </div>

    <div class="p-0">
        <?php if (empty($items)): ?>
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-camera-retro fa-3x text-muted opacity-25"></i>
                </div>
                <p class="text-muted mb-0">Belum ada foto galeri.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="100" class="ps-4">Preview</th>
                            <th>Store & Caption</th>
                            <th width="100" class="text-center">Order</th>
                            <th width="150">Uploaded</th>
                            <th width="120" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="ratio ratio-1x1 rounded shadow-sm overflow-hidden" style="width: 70px;">
                                        <img src="<?php echo $baseUrl . '/' . htmlspecialchars($item['image_path'], ENT_QUOTES, 'UTF-8'); ?>"
                                            class="object-fit-cover" alt="Preview">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-1">
                                        <?php echo htmlspecialchars($item['store_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                    <div class="small text-muted">
                                        <?php if (!empty($item['caption'])): ?>
                                            <i class="fas fa-quote-left me-1 opacity-50"></i>
                                            <?php echo htmlspecialchars($item['caption'], ENT_QUOTES, 'UTF-8'); ?>
                                        <?php else: ?>
                                            <span class="fst-italic opacity-50">Tanpa caption</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center"><span
                                        class="badge bg-light text-dark border"><?php echo (int) $item['sort_order']; ?></span>
                                </td>
                                <td>
                                    <div class="small text-muted">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        <?php echo date('d M Y', strtotime($item['created_at'])); ?>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="<?php echo $baseUrl; ?>/admin/our-home/gallery/edit/<?php echo (int) $item['id']; ?>"
                                            class="btn btn-icon btn-sm text-primary" title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form
                                            action="<?php echo $baseUrl; ?>/admin/our-home/gallery/delete/<?php echo (int) $item['id']; ?>"
                                            method="post" class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus photo ini?');">
                                            <input type="hidden" name="_token"
                                                value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                            <button type="submit" class="btn btn-icon btn-sm text-danger" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>


            <!-- Pagination -->
            <?php echo renderPagination($baseUrl, '/admin/our-home/gallery', $pagination, []); ?>
        <?php endif; ?>
    </div>
</div>