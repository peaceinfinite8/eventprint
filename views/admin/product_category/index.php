<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$categories = $categories ?? [];
?>


<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3 mb-0">Product Categories</h1>
  <a href="<?php echo $baseUrl; ?>/admin/product-categories/create" class="btn btn-primary">
    <i class="fa-solid fa-plus me-2"></i> Add Category
  </a>
</div>

<div class="dash-container-card">
  <div class="dash-header">
    <h5 class="dash-title">Categories</h5>
  </div>
  <div class="dash-body">
    <?php if (!empty($categories)): ?>
      <div class="table-responsive">
        <table class="table-custom">
          <thead>
            <tr>
              <th width="5%">#</th>
              <th width="30%">Name</th>
              <th width="30%">Slug</th>
              <th width="10%">Sort Order</th>
              <th width="10%">Status</th>
              <th width="15%" class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categories as $i => $cat): ?>
              <tr>
                <td class="text-muted"><?php echo $i + 1; ?></td>
                <td>
                  <span class="fw-bold text-dark"><?php echo htmlspecialchars($cat['name']); ?></span>
                </td>
                <td class="text-muted small">
                  <i class="fa-solid fa-link me-1 opacity-50"></i><?php echo htmlspecialchars($cat['slug']); ?>
                </td>
                <td>
                  <span class="badge bg-light text-dark border"><?php echo (int) $cat['sort_order']; ?></span>
                </td>
                <td>
                  <?php if (!empty($cat['is_active'])): ?>
                    <span class="dash-badge active">Active</span>
                  <?php else: ?>
                    <span class="dash-badge inactive">Inactive</span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <a href="<?php echo $baseUrl; ?>/admin/product-categories/edit/<?php echo $cat['id']; ?>"
                    class="btn btn-sm btn-light text-primary me-1" title="Edit">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <form action="<?php echo $baseUrl; ?>/admin/product-categories/delete/<?php echo $cat['id']; ?>"
                    method="post" class="d-inline" onsubmit="return confirm('Delete this category?');">

                    <input type="hidden" name="_token"
                      value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

                    <button type="submit" class="btn btn-sm btn-light text-danger" title="Delete">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <i class="fa-solid fa-tags fa-3x text-muted mb-3 opacity-25"></i>
        <p class="text-muted mb-0">No categories found.</p>
      </div>
    <?php endif; ?>
  </div>
</div>