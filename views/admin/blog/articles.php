<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$posts = $posts ?? [];
$filter_q = $filter_q ?? '';
$pagination = $pagination ?? [];

$total = $pagination['total'] ?? 0;
$page = $pagination['page'] ?? 1;
$perPage = $pagination['per_page'] ?? 10;

$lastPage = max(1, ceil($total / $perPage));
?>

<h1 class="h3 mb-3">Daftar Artikel</h1>

<div class="card">
  <div class="card-body">

    <!-- Toolbar -->
    <div class="d-flex justify-content-between mb-3">
      <div>
        <a href="<?php echo $baseUrl; ?>/admin/blog/articles/create" class="btn btn-primary">
          Tambah Artikel
        </a>
      </div>

      <form method="get" class="d-flex" style="max-width: 300px;">
        <input type="text" name="q" value="<?php echo htmlspecialchars($filter_q); ?>"
          class="form-control form-control-sm" placeholder="Cari judul...">
        <button class="btn btn-sm btn-outline-secondary ms-2">Cari</button>
      </form>
    </div>

    <?php if (!empty($posts)): ?>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Judul</th>
              <th>Kategori</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($posts as $i => $p): ?>
              <tr>
                <td><?php echo ($i + 1) + ($page - 1) * $perPage; ?></td>

                <td>
                  <strong><?php echo htmlspecialchars($p['title']); ?></strong>
                  <br>
                  <small class="text-muted"><?php echo htmlspecialchars($p['slug']); ?></small>
                </td>

                <td>
                  <!-- Logic: Show Featured Badge ONLY if is_featured == 1 -->
                  <?php if (!empty($p['is_featured'])): ?>
                    <span class="badge bg-info text-dark mb-1">FEATURED</span>
                  <?php endif; ?>

                  <?php if (!empty($p['post_category']) && strtolower($p['post_category']) !== 'featured'): ?>
                    <span class="badge bg-secondary bg-opacity-10 text-dark border">
                      <?php echo htmlspecialchars(ucfirst($p['post_category'])); ?>
                    </span>
                  <?php endif; ?>
                </td>

                <td>
                  <?php if ($p['is_published']): ?>
                    <span class="badge bg-success">Published</span>
                  <?php else: ?>
                    <span class="badge bg-warning text-dark">Draft</span>
                  <?php endif; ?>
                </td>

                <td class="small text-muted">
                  <?php echo htmlspecialchars($p['published_at'] ?: $p['created_at']); ?>
                </td>

                <td class="text-end">
                  <a href="<?php echo $baseUrl; ?>/admin/blog/articles/edit/<?php echo $p['id']; ?>"
                    class="btn btn-sm btn-outline-primary">
                    Edit
                  </a>

                  <form action="<?php echo $baseUrl; ?>/admin/blog/articles/delete/<?php echo $p['id']; ?>" method="post"
                    class="d-inline" onsubmit="return confirm('Hapus artikel ini?');">

                    <button type="submit" class="btn btn-sm btn-outline-danger">
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
        <nav>
          <ul class="pagination pagination-sm">
            <?php for ($p = 1; $p <= $lastPage; $p++): ?>
              <li class="page-item <?php echo $page === $p ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $p; ?>&q=<?php echo urlencode($filter_q); ?>">
                  <?php echo $p; ?>
                </a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
      <?php endif; ?>

    <?php else: ?>
      <p class="text-muted mb-0">Belum ada artikel.</p>
    <?php endif; ?>

  </div>
</div>