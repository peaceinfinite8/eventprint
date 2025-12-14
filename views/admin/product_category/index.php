<?php
$baseUrl    = $baseUrl ?? '/eventprint/public';
$categories = $categories ?? [];
?>

<h1 class="h3 mb-3">Kategori Produk</h1>

<div class="card mb-3">
  <div class="card-body d-flex justify-content-between align-items-center">
    <div class="small text-muted">
      Kelola kategori produk untuk grouping All Produk di website.
    </div>
    <div>
      <a href="<?php echo $baseUrl; ?>/admin/product-categories/create"
         class="btn btn-sm btn-success">
        + Tambah Kategori
      </a>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <?php if (!empty($categories)): ?>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
          <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Slug</th>
            <th>Urutan</th>
            <th>Status</th>
            <th class="text-end">Aksi</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($categories as $i => $cat): ?>
            <tr>
              <td><?php echo $i + 1; ?></td>
              <td>
                <strong><?php echo htmlspecialchars($cat['name']); ?></strong>
              </td>
              <td class="small text-muted">
                <?php echo htmlspecialchars($cat['slug']); ?>
              </td>
              <td><?php echo (int)$cat['sort_order']; ?></td>
              <td>
                <?php if (!empty($cat['is_active'])): ?>
                  <span class="badge bg-success">Aktif</span>
                <?php else: ?>
                  <span class="badge bg-secondary">Nonaktif</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <a href="<?php echo $baseUrl; ?>/admin/product-categories/edit/<?php echo $cat['id']; ?>"
                   class="btn btn-sm btn-primary">
                  Edit
                </a>
                <form action="<?php echo $baseUrl; ?>/admin/product-categories/delete/<?php echo $cat['id']; ?>"
                      method="post" class="d-inline"
                      onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">

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
    <?php else: ?>
      <p class="mb-0 text-muted">Belum ada kategori produk.</p>
    <?php endif; ?>
  </div>
</div>
