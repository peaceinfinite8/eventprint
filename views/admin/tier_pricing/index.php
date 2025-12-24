<?php
// views/admin/tier_pricing/index.php

$products = $products ?? [];
$pagination = $pagination ?? ['total' => 0, 'page' => 1, 'per_page' => 10];
$q = $q ?? '';
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Tier Pricing Management</h1>
        <p class="text-muted small mb-0">Kelola harga bertingkat (grosir) untuk produk</p>
    </div>
</div>

<div class="dash-container-card fade-in delay-1">
    <div class="p-4 border-bottom">
        <form method="get" class="row g-2 align-items-center">
            <div class="col-auto">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Cari pruduk..."
                        value="<?= htmlspecialchars($q) ?>">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary shadow-sm">Search</button>
            </div>
        </form>
    </div>

    <div class="p-0">
        <div class="table-responsive">
            <table class="table table-custom table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Product Info</th>
                        <th class="text-end">Base Price</th>
                        <th class="text-center">Active Tiers</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-2x mb-2 opacity-25"></i>
                                <p class="mb-0">No products found.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if (!empty($p['thumbnail'])): ?>
                                            <img src="<?= $baseUrl . '/' . htmlspecialchars($p['thumbnail']) ?>"
                                                class="rounded border" style="width:40px;height:40px;object-fit:cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded border d-flex align-items-center justify-content-center text-muted small"
                                                style="width:40px;height:40px;">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($p['name']) ?></div>
                                            <div class="small text-muted">ID: <?= $p['id'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end font-monospace text-dark">
                                    Rp <?= number_format($p['base_price'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($p['tier_count'] > 0): ?>
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> <?= $p['tier_count'] ?> Tiers
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">0
                                            Tiers</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= $baseUrl ?>/admin/products/edit/<?= $p['id'] ?>#tier-pricing"
                                        class="btn btn-sm btn-outline-primary shadow-sm bg-white">
                                        <i class="fas fa-tags me-1"></i> Manage Tiers
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Pagination -->
    <?php echo renderPagination($baseUrl, '/admin/tier-pricing', $pagination, ['q' => $q]); ?>
</div>