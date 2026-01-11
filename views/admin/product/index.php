<?php
$baseUrl = $baseUrl ?? '/eventprint';
$products = $products ?? [];
$categoriesOptions = $categoriesOptions ?? [];
$filter_q = $filter_q ?? '';
$filter_category_id = $filter_category_id ?? null;
$pagination = $pagination ?? ['total' => 0, 'page' => 1, 'per_page' => 10];

$total = (int) $pagination['total'];
$page = (int) $pagination['page'];
$perPage = (int) $pagination['per_page'];
$lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

// Calculate pagination display
$from = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
$to = min($page * $perPage, $total);

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


<!-- Live Search Section -->
<div class="dash-card mb-4" style="overflow: visible;">
  <div class="p-3">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label text-muted small fw-bold text-uppercase mb-2">Search</label>
        <div class="position-relative" style="z-index: 100;">
          <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
            <input type="text" id="adminProductSearch" class="form-control border-start-0 ps-0"
              placeholder="Search product name or description..." autocomplete="off"
              value="<?php echo htmlspecialchars($filter_q); ?>" data-base-url="<?php echo $baseUrl; ?>">
          </div>
          <div id="adminSearchDropdown" class="admin-search-dropdown" style="display:none;"></div>
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label text-muted small fw-bold text-uppercase mb-2">Category Filter</label>
        <select id="categoryFilter" class="form-select">
          <option value="">All Categories</option>
          <?php foreach ($categoriesOptions as $cat): ?>
            <option value="<?php echo (int) $cat['id']; ?>" <?php echo ($filter_category_id == $cat['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($cat['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>
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
              <th width="10%">Image</th>
              <th width="30%">Product Details</th>
              <th width="15%">Category</th>
              <th width="15%">Price</th>
              <th width="10%">Status</th>
              <th width="5%">Stock</th>
              <th width="10%" class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $i => $p): ?>
              <tr class="align-middle">
                <td class="text-muted"><?php echo ($i + 1) + ($page - 1) * $perPage; ?></td>
                <td>
                  <div class="d-flex align-items-center justify-content-center bg-light rounded border img-preview-trigger"
                    role="button"
                    onclick="window.openProductPreview('<?php echo safeImageUrl($p['thumbnail'] ?? '', 'product', $baseUrl); ?>', '<?php echo htmlspecialchars($p['name'], ENT_QUOTES); ?>')"
                    style="width: 50px; height: 50px; overflow: hidden; cursor: pointer;">
                    <img src="<?php echo safeImageUrl($p['thumbnail'] ?? '', 'product', $baseUrl); ?>"
                      alt="<?php echo htmlspecialchars($p['name']); ?>" class="img-fluid"
                      style="width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
                  </div>
                </td>
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

                      <li><a class="dropdown-item"
                          href="<?= $baseUrl ?>/admin/products/<?= (int) $p['id'] ?>/product-options"><i
                            class="fa-solid fa-layer-group me-2 text-info"></i> Options (Mat/Lam)</a></li>
                      <li>
                        <hr class="dropdown-divider">
                      </li>
                      <li>
                        <form action="<?php echo $baseUrl; ?>/admin/products/delete/<?php echo $p['id']; ?>" method="post"
                          onsubmit="return confirmDelete(event, this);">
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
      <?php echo renderPagination($baseUrl, '/admin/products', $pagination, ['q' => $filter_q, 'category_id' => $filter_category_id]); ?>

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

<!-- Admin Search Styles -->
<style>
  .admin-search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    max-height: 450px;
    overflow-y: auto;
    z-index: 1000;
    margin-top: 8px;
  }

  .admin-search-item {
    padding: 14px 18px;
    cursor: pointer;
    border-bottom: 1px solid #f5f5f5;
    transition: all 0.2s ease;
  }

  .admin-search-item:hover {
    background: linear-gradient(to right, #f8f9fa 0%, #e9ecef 100%);
    border-left: 3px solid #0d6efd;
    padding-left: 15px;
  }

  .admin-search-item:last-child {
    border-bottom: none;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
  }

  .admin-search-item:first-child:hover {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }

  .admin-search-empty {
    padding: 20px;
    text-align: center;
    color: #6c757d;
    font-style: italic;
  }

  /* Scrollbar styling */
  .admin-search-dropdown::-webkit-scrollbar {
    width: 6px;
  }

  .admin-search-dropdown::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 8px;
  }

  .admin-search-dropdown::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 8px;
  }

  .admin-search-dropdown::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
  }
</style>

<!-- Admin Search Script -->
<script src="<?= $baseUrl ?>/assets/admin/js/adminProductSearch.js"></script>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content bg-black border-0 overflow-hidden position-relative">

      <!-- Blurred Background -->
      <div id="previewModalBackground"
        style="position: absolute; inset: 0; background-size: cover; background-position: center; filter: blur(40px) brightness(0.4); transform: scale(1.2); z-index: 1; transition: background-image 0.3s ease;">
      </div>

      <!-- Content -->
      <div class="modal-body p-0 d-flex flex-column align-items-center justify-content-center position-relative"
        style="z-index: 2;">
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4 p-2 shadow-sm"
          data-bs-dismiss="modal" aria-label="Close" style="z-index: 10; opacity: 0.8;"></button>

        <img id="previewModalImage" src="" class="img-fluid rounded shadow-lg"
          style="max-height: 85vh; max-width: 90vw; object-fit: contain; box-shadow: 0 10px 40px rgba(0,0,0,0.5) !important;">

        <div id="previewModalCaption" class="mt-4 text-white p-3 rounded h5 fw-light text-center"
          style="text-shadow: 0 2px 4px rgba(0,0,0,0.8); background: rgba(0,0,0,0.3); backdrop-filter: blur(5px);">
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  // Global function to be called directly from HTML
  window.openProductPreview = function (src, alt) {
    console.log('openProductPreview called', src);

    let modalEl = document.getElementById('imagePreviewModal');

    // FIX: Move modal to body to ensure fixed positioning works (breaks out of relative containers)
    if (modalEl && modalEl.parentNode !== document.body) {
      document.body.appendChild(modalEl);
    }

    const modalImg = document.getElementById('previewModalImage');
    const modalBg = document.getElementById('previewModalBackground');
    const modalCaption = document.getElementById('previewModalCaption');

    if (!modalEl) {
      console.error('Modal element not found');
      return;
    }

    // Initialize modal if not already done
    let previewModal = bootstrap.Modal.getInstance(modalEl);
    if (!previewModal) {
      if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        previewModal = new bootstrap.Modal(modalEl, { backdrop: false });
      } else if (window.bootstrap && window.bootstrap.Modal) {
        previewModal = new window.bootstrap.Modal(modalEl, { backdrop: false });
      } else {
        console.error('Bootstrap not found');
        return;
      }
    }

    if (modalImg) modalImg.src = src;
    if (modalBg) modalBg.style.backgroundImage = `url('${src}')`;
    if (modalCaption) modalCaption.textContent = alt;

    previewModal.show();

    // Cleanup listener
    modalEl.addEventListener('hidden.bs.modal', function () {
      if (modalImg) modalImg.src = '';
      if (modalBg) modalBg.style.backgroundImage = 'none';
    }, {
      once: true
    });
  };

  // SweetAlert2 Delete Confirmation
  window.confirmDelete = function (e, form) {
    e.preventDefault();
    Swal.fire({
      title: 'Apakah Anda yakin?',
      text: "Produk ini akan dihapus secara permanen!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batal',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
    return false;
  };
</script>

<style>
  .img-preview-trigger {
    transition: transform 0.2s;
  }

  .img-preview-trigger:hover {
    transform: scale(1.1);
    border-color: #0d6efd !important;
  }

  /* Force Modal Styles if Bootstrap fails */
  #imagePreviewModal {
    z-index: 9999 !important;
    /* High enough to cover content */
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    overflow: hidden !important;
    display: none;
    pointer-events: none;
    /* KEY FIX: Pass clicks through when hidden */
  }

  /* Force Sidebar to be on top of the blurred backdrop */
  #sidebar {
    z-index: 10000 !important;
    position: relative !important;
    /* Changed from fixed to relative to preserve layout flow */
  }

  #imagePreviewModal.show {
    padding-right: 0 !important;
    display: block !important;
    background-color: rgba(0, 0, 0, 0.9);
    pointer-events: auto !important;
    /* Re-enable clicks when shown */
  }

  #imagePreviewModal .modal-dialog-fullscreen {
    width: 100vw !important;
    height: 100vh !important;
    max-width: 100vw !important;
    margin: 0 !important;
    padding: 0 !important;
    transform: none !important;
    left: 0 !important;
    top: 0 !important;
  }

  #imagePreviewModal .modal-content {
    height: 100vh !important;
    border: none;
    border-radius: 0;
    background: transparent;
  }

  /* Ensure body takes full height for centering */
  #imagePreviewModal .modal-body {
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    align-items: center !important;
    padding: 0 !important;
    padding-left: 260px !important;
    /* Offset for Sidebar width on Desktop */
  }

  /* Reset padding for Mobile */
  @media (max-width: 768px) {
    #imagePreviewModal .modal-body {
      padding-left: 0 !important;
    }
  }
</style>