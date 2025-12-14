<?php
$baseUrl    = $baseUrl ?? '/eventprint/public';
$items      = $items ?? [];
$filter_q   = $filter_q ?? '';
$pagination = $pagination ?? ['total' => 0, 'page' => 1, 'per_page' => 10];

$total    = (int)($pagination['total'] ?? 0);
$page     = (int)($pagination['page'] ?? 1);
$perPage  = (int)($pagination['per_page'] ?? 10);
$lastPage = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
?>

<h1 class="h3 mb-3">Our Store</h1>

<div class="card mb-3">
  <div class="card-body">
    <form class="row g-2 align-items-end" method="get" action="<?php echo $baseUrl; ?>/admin/our-store">
      <div class="col-md-6">
        <label class="form-label">Cari</label>
        <input type="text" name="q" class="form-control"
               placeholder="nama / kota / alamat"
               value="<?php echo htmlspecialchars($filter_q); ?>">
      </div>
      <div class="col-md-6 text-md-end">
        <button class="btn btn-primary" type="submit">Cari</button>
        <a class="btn btn-outline-secondary" href="<?php echo $baseUrl; ?>/admin/our-store">Reset</a>
        <a class="btn btn-success" href="<?php echo $baseUrl; ?>/admin/our-store/create">+ Tambah</a>
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
          <th>Store</th>
          <th style="width:140px;">Tipe</th>
          <th style="width:140px;">Kota</th>
          <th style="width:120px;">Status</th>
          <th style="width:180px;" class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!empty($items)): ?>
        <?php foreach ($items as $i => $it): ?>
          <tr>
            <td><?php echo ($i + 1) + ($page - 1) * $perPage; ?></td>
            <td>
              <div class="fw-semibold"><?php echo htmlspecialchars($it['name']); ?></div>
              <div class="small text-muted">Slug: <?php echo htmlspecialchars($it['slug']); ?></div>
              <div class="small text-muted"><?php echo htmlspecialchars($it['address']); ?></div>
            </td>
            <td>
              <?php if (($it['office_type'] ?? '') === 'hq'): ?>
                <span class="badge bg-primary">HQ</span>
              <?php else: ?>
                <span class="badge bg-info text-dark">Branch</span>
              <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($it['city']); ?></td>
            <td>
              <?php if (!empty($it['is_active'])): ?>
                <span class="badge bg-success">Aktif</span>
              <?php else: ?>
                <span class="badge bg-secondary">Nonaktif</span>
              <?php endif; ?>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-primary"
                 href="<?php echo $baseUrl; ?>/admin/our-store/edit/<?php echo (int)$it['id']; ?>">Edit</a>

              <form class="d-inline"
                    action="<?php echo $baseUrl; ?>/admin/our-store/delete/<?php echo (int)$it['id']; ?>"
                    method="post"
                    onsubmit="return confirm('Yakin hapus store ini?');">
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" class="text-muted">Belum ada data store.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>

    <?php if ($lastPage > 1): ?>
      <nav>
        <ul class="pagination pagination-sm mb-0 justify-content-end">
          <?php for ($p=1; $p <= $lastPage; $p++): ?>
            <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>">
              <a class="page-link"
                 href="<?php echo $baseUrl; ?>/admin/our-store?page=<?php echo $p; ?>&q=<?php echo urlencode($filter_q); ?>">
                <?php echo $p; ?>
              </a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </div>
</div>
