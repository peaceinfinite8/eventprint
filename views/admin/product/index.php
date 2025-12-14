<?php
$baseUrl            = $baseUrl ?? '/eventprint/public';
$products           = $products ?? [];
$categoriesOptions  = $categoriesOptions ?? [];
$filter_q           = $filter_q ?? '';
$filter_category_id = $filter_category_id ?? null;
$pagination         = $pagination ?? ['total' => 0, 'page' => 1, 'per_page' => 10];

$total    = (int)$pagination['total'];
$page     = (int)$pagination['page'];
$perPage  = (int)$pagination['per_page'];
$lastPage = $perPage > 0 ? (int)ceil($total / $perPage) : 1;

$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');


function buildQuery(array $params): string {
    $query = [];
    foreach ($params as $k => $v) {
        if ($v === null || $v === '') continue;
        $query[] = urlencode($k) . '=' . urlencode($v);
    }
    return $query ? ('?' . implode('&', $query)) : '';
}
?>

<h1 class="h3 mb-3">All Produk</h1>

<!-- Filter & Action -->
<div class="card mb-3">
  <div class="card-body">
    <form class="row g-2 align-items-end" method="get" action="<?php echo $baseUrl; ?>/admin/products">
      
      <div class="col-md-4">
        <label class="form-label">Cari Produk</label>
        <input type="text"
               name="q"
               class="form-control"
               placeholder="Nama / deskripsi singkat"
               value="<?php echo htmlspecialchars($filter_q); ?>">
      </div>

      <div class="col-md-4">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-select">
          <option value="">Semua Kategori</option>
          <?php foreach ($categoriesOptions as $cat): ?>
            <option value="<?php echo (int)$cat['id']; ?>"
              <?php echo ($filter_category_id == $cat['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($cat['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-4 text-md-end">
        <button type="submit" class="btn btn-primary mb-2">Filter</button>
        <a href="<?php echo $baseUrl; ?>/admin/products"
           class="btn btn-outline-secondary mb-2">
          Reset
        </a>
        <a href="<?php echo $baseUrl; ?>/admin/products/create"
           class="btn btn-success mb-2">
          + Tambah Produk
        </a>
      </div>
    </form>
  </div>
</div>

<!-- Tabel Produk -->
<div class="card">
  <div class="card-body">
    <?php if (!empty($products)): ?>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
          <tr>
            <th>#</th>
            <th>Produk</th>
            <th>Kategori</th>
            <th>Harga Dasar</th>
            <th>Featured</th>
            <th>Status</th>
            <th style="width:120px;">Stock</th>
            <th class="text-end">Aksi</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($products as $i => $p): ?>
            <tr>
              <td><?php echo ($i + 1) + ($page - 1) * $perPage; ?></td>
              <td>
                <strong><?php echo htmlspecialchars($p['name']); ?></strong><br>
                <small class="text-muted">
                  <?php echo htmlspecialchars($p['slug']); ?>
                </small>
                <?php if (!empty($p['short_description'])): ?>
                  <br>
                  <small><?php echo htmlspecialchars($p['short_description']); ?></small>
                <?php endif; ?>
              </td>
              <td><?php echo htmlspecialchars($p['category_name'] ?? ''); ?></td>
              <td>
                <?php
                $price = isset($p['base_price']) ? (float)$p['base_price'] : 0;
                echo number_format($price, 0, ',', '.');
                ?>
              </td>
              <td>
                <?php if (!empty($p['is_featured'])): ?>
                  <span class="badge bg-warning text-dark">Featured</span>
                <?php else: ?>
                  <span class="badge bg-light text-muted">Normal</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($p['is_active'])): ?>
                  <span class="badge bg-success">Aktif</span>
                <?php else: ?>
                  <span class="badge bg-secondary">Nonaktif</span>
                <?php endif; ?>
              </td>
              <td><?php echo (int)($p['stock'] ?? 0); ?></td>

              <td class="text-end">
                <a href="<?php echo $baseUrl; ?>/admin/products/edit/<?php echo $p['id']; ?>"
                   class="btn btn-sm btn-primary">
                  Edit
                </a>
                <a href="<?= $baseUrl ?>/admin/products/<?= (int)$p['id'] ?>/options" class="btn btn-sm btn-warning">
                  Harga
                </a>

                <form action="<?php echo $baseUrl; ?>/admin/products/delete/<?php echo $p['id']; ?>"
                      method="post"
                      class="d-inline"
                      onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                      <input type="hidden" name="_token"
       value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

                  <button type="submit" class="btn btn-sm btn-danger">
                    Hapus
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($lastPage > 1): ?>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <small class="text-muted">
            Menampilkan
            <?php
              $from = ($total > 0) ? (($page - 1) * $perPage + 1) : 0;
              $to   = min($page * $perPage, $total);
              echo $from . '–' . $to . ' dari ' . $total;
            ?>
          </small>

          <nav aria-label="Pagination">
            <ul class="pagination pagination-sm mb-0 justify-content-end">

              <!-- Prev -->
              <?php
                $prev = $page - 1;
                $prevQuery = buildQuery(['q'=>$filter_q,'category_id'=>$filter_category_id,'page'=>$prev]);
              ?>
              <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo $baseUrl; ?>/admin/products<?php echo $prevQuery; ?>" tabindex="-1">
                  &laquo;
                </a>
              </li>

              <!-- Pages -->
              <?php for ($p = 1; $p <= $lastPage; $p++): ?>
                <?php
                  $query = buildQuery([
                    'q'           => $filter_q,
                    'category_id' => $filter_category_id,
                    'page'        => $p,
                  ]);
                ?>
                <li class="page-item <?php echo ($p === $page) ? 'active' : ''; ?>">
                  <a class="page-link" href="<?php echo $baseUrl; ?>/admin/products<?php echo $query; ?>">
                    <?php echo $p; ?>
                  </a>
                </li>
              <?php endfor; ?>

              <!-- Next -->
              <?php
                $next = $page + 1;
                $nextQuery = buildQuery(['q'=>$filter_q,'category_id'=>$filter_category_id,'page'=>$next]);
              ?>
              <li class="page-item <?php echo ($page >= $lastPage) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo $baseUrl; ?>/admin/products<?php echo $nextQuery; ?>">
                  &raquo;
                </a>
              </li>

            </ul>
          </nav>
        </div>
      <?php endif; ?>

    <?php else: ?>
      <p class="mb-0 text-muted">Belum ada produk yang cocok dengan filter.</p>
    <?php endif; ?>
  </div>
</div>
