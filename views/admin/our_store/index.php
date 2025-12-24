<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$items = $items ?? [];
$filter_q = $filter_q ?? '';
$pagination = $pagination ?? ['total' => 0, 'page' => 1, 'per_page' => 10];

$total = (int) ($pagination['total'] ?? 0);
$page = (int) ($pagination['page'] ?? 1);
$perPage = (int) ($pagination['per_page'] ?? 10);
$lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">Our Store</h1>
    <p class="text-muted small mb-0">Kelola daftar toko dan cabang</p>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-primary shadow-sm" href="<?php echo $baseUrl; ?>/admin/our-home/stores/create">
      <i class="fas fa-plus me-2"></i>Tambah Store
    </a>
  </div>
</div>

<div class="dash-container-card mb-4 fade-in delay-1">
  <div class="p-3">
    <form class="row g-2 align-items-center" method="get" action="<?php echo $baseUrl; ?>/admin/our-home/stores">
      <div class="col-md-5">
        <div class="input-group">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-search"></i></span>
          <input type="text" name="q" class="form-control border-start-0 ps-0"
            placeholder="Cari nama, kota, atau alamat..." value="<?php echo htmlspecialchars($filter_q); ?>">
        </div>
      </div>
      <div class="col-md-7 text-md-end">
        <button class="btn btn-primary px-4" type="submit">Cari</button>
        <a class="btn btn-outline-secondary px-3" href="<?php echo $baseUrl; ?>/admin/our-home/stores">
          <i class="fas fa-undo me-1"></i> Reset
        </a>
      </div>
    </form>
  </div>
</div>

<div class="dash-container-card fade-in delay-2">
  <div class="table-responsive">
    <table class="table table-custom table-striped align-middle mb-0">
      <thead>
        <tr>
          <th style="width:70px;" class="text-center">#</th>
          <th>Store Info</th>
          <th style="width:140px;">Tipe</th>
          <th style="width:140px;">Kota</th>
          <th style="width:120px;">Status</th>
          <th style="width:150px;" class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($items)): ?>
          <?php foreach ($items as $i => $it): ?>
            <tr>
              <td class="text-center text-muted small"><?php echo ($i + 1) + ($page - 1) * $perPage; ?></td>
              <td>
                <div class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($it['name']); ?></div>
                <div class="small text-muted mb-1"><i class="fas fa-link me-1 opacity-50"></i>
                  <?php echo htmlspecialchars($it['slug']); ?></div>
                <div class="small text-muted"><i class="fas fa-map-marker-alt me-1 opacity-50"></i>
                  <?php echo htmlspecialchars($it['address']); ?></div>
              </td>
              <td>
                <?php if (($it['office_type'] ?? '') === 'hq'): ?>
                  <span
                    class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill">HEADQUARTER</span>
                <?php else: ?>
                  <span
                    class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill">BRANCH</span>
                <?php endif; ?>
              </td>
              <td><?php echo htmlspecialchars($it['city']); ?></td>
              <td>
                <?php if (!empty($it['is_active'])): ?>
                  <span class="dash-badge active">Aktif</span>
                <?php else: ?>
                  <span class="dash-badge inactive">Nonaktif</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <div class="btn-group">
                  <a class="btn btn-icon btn-sm text-primary"
                    href="<?php echo $baseUrl; ?>/admin/our-home/stores/edit/<?php echo (int) $it['id']; ?>" title="Edit">
                    <i class="fas fa-pencil-alt"></i>
                  </a>

                  <form class="d-inline"
                    action="<?php echo $baseUrl; ?>/admin/our-home/stores/delete/<?php echo (int) $it['id']; ?>"
                    method="post" onsubmit="return confirm('Yakin hapus store ini?');">
                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <button class="btn btn-icon btn-sm text-danger" type="submit" title="Hapus">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">Belum ada data store.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($lastPage > 1): ?>
    <div class="p-3 border-top">
      <nav>
        <ul class="pagination pagination-sm mb-0 justify-content-end">
          <?php for ($p = 1; $p <= $lastPage; $p++): ?>
            <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>">
              <a class="page-link"
                href="<?php echo $baseUrl; ?>/admin/our-home/stores?page=<?php echo $p; ?>&q=<?php echo urlencode($filter_q); ?>">
                <?php echo $p; ?>
              </a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    </div>
  <?php endif; ?>
</div>