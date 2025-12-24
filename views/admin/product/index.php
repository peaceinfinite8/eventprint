<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$products = $products ?? [];
$categoriesOptions = $categoriesOptions ?? [];
$filter_q = $filter_q ?? '';
$filter_category_id = $filter_category_id ?? null;
$pagination = $pagination ?? ['total' => 0, 'page' => 1, 'per_page' => 10];

$total = (int) $pagination['total'];
$page = (int) $pagination['page'];
$perPage = (int) $pagination['per_page'];
$lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');


function buildQuery(array $params): string
{
  $query = [];
  foreach ($params as $k => $v) {
    if ($v === null || $v === '')
      continue;
    $query[] = urlencode($k) . '=' . urlencode($v);
  }
  return $query ? ('?' . implode('&', $query)) : '';
}
?>


<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3 mb-0">All Products</h1>
  <a href="<?php echo $baseUrl; ?>/admin/products/create" class="btn btn-primary">
    <i class="fa-solid fa-plus me-2"></i> Add Product
  </a>
</div>

<!-- Filter Section -->
<div class="dash-card mb-4">
  <form class="row g-3 align-items-end" method="get" action="<?php echo $baseUrl; ?>/admin/products">
    <div class="col-md-4">
      <label class="form-label text-muted small fw-bold text-uppercase">Search</label>
      <div class="input-group">
        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
        <input type="text" name="q" class="form-control border-start-0 ps-0"
          placeholder="Product name or description..." value="<?php echo htmlspecialchars($filter_q); ?>">
      </div>
    </div>
    <div class="col-md-4">
      <label class="form-label text-muted small fw-bold text-uppercase">Category</label>
      <select name="category_id" class="form-select">
        <option value="">All Categories</option>
        <?php foreach ($categoriesOptions as $cat): ?>
          <option value="<?php echo (int) $cat['id']; ?>" <?php echo ($filter_category_id == $cat['id']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($cat['name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4 d-flex gap-2">
      <button type="submit" class="btn btn-primary px-4">Filter</button>
      <a href="<?php echo $baseUrl; ?>/admin/products" class="btn btn-light">Reset</a>
    </div>
  </form>
</div>

<!-- Products Table -->
<div class="dash-container-card">
  <div class="dash-header">
    <h5 class="dash-title">Product List</h5>
    <span class="badge bg-light text-dark border"><?php echo $total; ?> Items</span>
  </div>
  <div class="dash-body">
    <?php if (!empty($products)): ?>
      <div class="table-responsive">
        <table class="table-custom">
          <thead>
            <tr>
              <th width="5%">#</th>
              <th width="35%">Product Details</th>
              <th width="15%">Category</th>
              <th width="15%">Price</th>
              <th width="10%">Status</th>
              <th width="10%">Stock</th>
              <th width="10%" class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $i => $p): ?>
              <tr>
                <td class="text-muted"><?php echo ($i + 1) + ($page - 1) * $perPage; ?></td>
                <td>
                  <div class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($p['name']); ?></div>
                  <div class="text-muted small mb-1">
                    <i class="fa-solid fa-link me-1" style="font-size:10px;"></i><?php echo htmlspecialchars($p['slug']); ?>
                  </div>
                  <?php if (!empty($p['is_featured'])): ?>
                    <span class="dash-badge active me-1" style="font-size:10px;">Featured</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge bg-light text-dark border fw-normal">
                    <?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?>
                  </span>
                </td>
                <td class="fw-bold text-dark">
                  Rp <?php echo number_format(isset($p['base_price']) ? (float) $p['base_price'] : 0, 0, ',', '.'); ?>
                </td>
                <td>
                  <?php if (!empty($p['is_active'])): ?>
                    <span class="dash-badge active">Active</span>
                  <?php else: ?>
                    <span class="dash-badge inactive">Inactive</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <span class="fw-semibold"><?php echo (int) ($p['stock'] ?? 0); ?></span>
                  </div>
                </td>
                <td class="text-end">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                      <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                      <li>
                        <h6 class="dropdown-header">Manage</h6>
                      </li>
                      <li><a class="dropdown-item"
                          href="<?php echo $baseUrl; ?>/admin/products/edit/<?php echo $p['id']; ?>"><i
                            class="fa-solid fa-pen-to-square me-2 text-primary"></i> Edit Product</a></li>
                      <li><a class="dropdown-item" href="<?= $baseUrl ?>/admin/products/<?= (int) $p['id'] ?>/options"><i
                            class="fa-solid fa-tag me-2 text-warning"></i> Tier Pricing</a></li>
                      <li><a class="dropdown-item"
                          href="<?= $baseUrl ?>/admin/products/<?= (int) $p['id'] ?>/product-options"><i
                            class="fa-solid fa-layer-group me-2 text-info"></i> Options (Mat/Lam)</a></li>
                      <li>
                        <hr class="dropdown-divider">
                      </li>
                      <li>
                        <form action="<?php echo $baseUrl; ?>/admin/products/delete/<?php echo $p['id']; ?>" method="post"
                          onsubmit="return confirm('Are you sure?');">
                          <input type="hidden" name="_token"
                            value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                          <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-trash me-2"></i>
                            Delete</button>
                        </form>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($lastPage > 1): ?>
        <div class="d-flex justify-content-between align-items-center p-4 border-top">
          <div class="text-muted small">
            Showing <strong><?php echo $from; ?>-<?php echo $to; ?></strong> of <strong><?php echo $total; ?></strong>
          </div>
          <nav>
            <ul class="pagination pagination-sm mb-0">
              <?php
              $prev = $page - 1;
              $prevQuery = buildQuery(['q' => $filter_q, 'category_id' => $filter_category_id, 'page' => $prev]);
              ?>
              <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link border-0" href="<?php echo $baseUrl; ?>/admin/products<?php echo $prevQuery; ?>">
                  <i class="fa-solid fa-chevron-left"></i>
                </a>
              </li>

              <?php for ($p = 1; $p <= $lastPage; $p++): ?>
                <?php
                $active = ($p === $page);
                $query = buildQuery(['q' => $filter_q, 'category_id' => $filter_category_id, 'page' => $p]);
                ?>
                <li class="page-item <?php echo $active ? 'active' : ''; ?>">
                  <a class="page-link shadow-none <?php echo $active ? 'bg-primary text-white' : 'text-dark'; ?> border-0 rounded mx-1"
                    href="<?php echo $baseUrl; ?>/admin/products<?php echo $query; ?>">
                    <?php echo $p; ?>
                  </a>
                </li>
              <?php endfor; ?>

              <?php
              $next = $page + 1;
              $nextQuery = buildQuery(['q' => $filter_q, 'category_id' => $filter_category_id, 'page' => $next]);
              ?>
              <li class="page-item <?php echo ($page >= $lastPage) ? 'disabled' : ''; ?>">
                <a class="page-link border-0" href="<?php echo $baseUrl; ?>/admin/products<?php echo $nextQuery; ?>">
                  <i class="fa-solid fa-chevron-right"></i>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      <?php endif; ?>

    <?php else: ?>
      <div class="empty-state">
        <div class="text-center py-5">
          <i class="fa-solid fa-box-open fa-3x text-muted mb-3 opacity-25"></i>
          <p class="text-muted mb-0">No products found matching your criteria.</p>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>