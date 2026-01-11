<?php
$baseUrl = $baseUrl ?? '/eventprint';
$items = $items ?? [];
$filter_q = $filter_q ?? '';
$pagination = $pagination ?? ['total' => 0, 'page' => 1, 'per_page' => 10];
$isSuper = $isSuper ?? false;

$total = (int) ($pagination['total'] ?? 0);
$page = (int) ($pagination['page'] ?? 1);
$perPage = (int) ($pagination['per_page'] ?? 10);
$lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
?>

<h1 class="h3 mb-3">Diskon Produk</h1>

<div class="card mb-3">
  <div class="card-body">
    <form class="row g-2 align-items-end" method="get" action="<?php echo $baseUrl; ?>/admin/discounts">
      <div class="col-md-6">
        <label class="form-label">Cari</label>
        <input class="form-control" name="q" value="<?php echo htmlspecialchars($filter_q); ?>"
          placeholder="nama produk / tipe">
      </div>
      <div class="col-md-6 text-md-end">
        <button class="btn btn-primary" type="submit">Cari</button>
        <a class="btn btn-outline-secondary" href="<?php echo $baseUrl; ?>/admin/discounts">Reset</a>
        <?php if ($isSuper): ?>
          <a class="btn btn-success" href="<?php echo $baseUrl; ?>/admin/discounts/create">+ Tambah Diskon</a>
        <?php else: ?>
          <span class="badge bg-light text-muted ms-2">Admin hanya bisa melihat</span>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th style="width:70px;">#</th>
          <th>Produk</th>
          <th style="width:160px;">Diskon</th>
          <th style="width:160px;">Kuota</th>
          <th style="width:220px;">Periode</th>
          <th style="width:120px;">Status</th>
          <?php if ($isSuper): ?>
            <th class="text-end" style="width:180px;">Aksi</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($items)): ?>
          <?php foreach ($items as $i => $it): ?>
            <tr>
              <td><?php echo ($i + 1) + ($page - 1) * $perPage; ?></td>
              <td>
                <div class="fw-semibold"><?php echo htmlspecialchars($it['product_name'] ?? '-'); ?></div>
                <div class="small text-muted">
                  Harga: Rp <?php echo number_format((float) ($it['product_price'] ?? 0), 0, ',', '.'); ?> •
                  Stok: <?php echo (int) ($it['product_stock'] ?? 0); ?>
                </div>
              </td>
              <td>
                <?php if (($it['discount_type'] ?? '') === 'fixed'): ?>
                  <span class="badge bg-dark">Rp
                    <?php echo number_format((float) $it['discount_value'], 0, ',', '.'); ?></span>
                <?php else: ?>
                  <span class="badge bg-primary"><?php echo rtrim(rtrim((string) $it['discount_value'], '0'), '.'); ?>%</span>
                <?php endif; ?>
              </td>
              <td>
                <div class="small"><?php echo (int) ($it['qty_used'] ?? 0); ?> / <?php echo (int) ($it['qty_total'] ?? 0); ?>
                </div>
                <div class="progress" style="height:6px;">
                  <?php
                  $used = (int) ($it['qty_used'] ?? 0);
                  $tot = max(1, (int) ($it['qty_total'] ?? 0));
                  $pct = min(100, (int) round(($used / $tot) * 100));
                  ?>
                  <div class="progress-bar" role="progressbar" style="width: <?php echo $pct; ?>%"></div>
                </div>
              </td>
              <td class="small text-muted">
                <?php echo !empty($it['start_at']) ? htmlspecialchars($it['start_at']) : '<span class="text-muted">Mulai: -</span>'; ?><br>
                <?php echo !empty($it['end_at']) ? htmlspecialchars($it['end_at']) : '<span class="text-muted">Selesai: -</span>'; ?>
              </td>
              <td>
                <?php if (!empty($it['is_active'])): ?>
                  <span class="badge bg-success">Aktif</span>
                <?php else: ?>
                  <span class="badge bg-secondary">Nonaktif</span>
                <?php endif; ?>
              </td>

              <?php if ($isSuper): ?>
                <td class="text-end">
                  <a class="btn btn-sm btn-primary"
                    href="<?php echo $baseUrl; ?>/admin/discounts/edit/<?php echo (int) $it['id']; ?>">Edit</a>
                  <form class="d-inline" method="post"
                    action="<?php echo $baseUrl; ?>/admin/discounts/delete/<?php echo (int) $it['id']; ?>"
                    onsubmit="return confirm('Yakin hapus diskon ini?');">
                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken ?? ''); ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                  </form>
                </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="<?php echo $isSuper ? 7 : 6; ?>" class="text-muted">Belum ada diskon.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>


    <!-- Pagination -->
    <?php echo renderPagination($baseUrl, '/admin/discounts', $pagination, ['q' => $filter_q]); ?>
  </div>
</div>