<?php
$baseUrl = $baseUrl ?? '/eventprint';
$laminations = $laminations ?? [];
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Manage Laminations</h1>
        <p class="text-muted small mb-0">Daftar jenis laminasi yang tersedia untuk produk</p>
    </div>
    <a href="<?php echo $baseUrl; ?>/admin/laminations/create" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-2"></i> Tambah Laminasi
    </a>
</div>

<div class="dash-container-card fade-in delay-1">
    <div class="p-0">
        <?php if (!empty($laminations)): ?>
            <div class="table-responsive">
                <table class="table table-custom table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Nama Laminasi</th>
                            <th>Slug</th>
                            <th>Delta Harga</th>
                            <th>Urutan</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($laminations as $i => $lam): ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?php echo $i + 1; ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($lam['name']); ?></div>
                                </td>
                                <td class="small text-muted font-monospace">
                                    <?php echo htmlspecialchars($lam['slug']); ?>
                                </td>
                                <td>
                                    <?php if ((float) $lam['price_delta'] > 0): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            +Rp <?php echo number_format((float) $lam['price_delta'], 0, ',', '.'); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?php echo (int) $lam['sort_order']; ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                        <i class="fas fa-layer-group me-1"></i> <?php echo (int) $lam['category_count']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($lam['is_active'])): ?>
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                                            <i class="fas fa-ban me-1"></i> Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="<?php echo $baseUrl; ?>/admin/laminations/edit/<?php echo $lam['id']; ?>"
                                            class="btn btn-icon btn-sm text-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form
                                            action="<?php echo $baseUrl; ?>/admin/laminations/delete/<?php echo $lam['id']; ?>"
                                            method="post" class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus laminasi ini?');">
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
        <?php else: ?>
            <div class="p-5 text-center text-muted">
                <div class="mb-3"><i class="fas fa-box-open fa-3x opacity-25"></i></div>
                <p class="mb-0">Belum ada laminasi. Klik "Tambah Laminasi" untuk menambahkan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>