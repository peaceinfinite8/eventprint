<?php
$baseUrl    = $baseUrl ?? '/eventprint/public';
$posts      = $posts ?? [];
$filter_q   = $filter_q ?? '';
$pagination = $pagination ?? ['total' => 0, 'page' => 1, 'per_page' => 10];

// ✅ FIX: pastikan token ada di halaman index
$csrfToken  = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : ($_SESSION['_token'] ?? ''));

$total    = (int)($pagination['total'] ?? 0);
$page     = (int)($pagination['page'] ?? 1);
$perPage  = (int)($pagination['per_page'] ?? 10);
$lastPage = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
?>


<h1 class="h3 mb-3">Artikel / Blog</h1>

<div class="card mb-3">
  <div class="card-body">
    <form class="row g-2" method="get" action="<?php echo $baseUrl; ?>/admin/blog">
      <div class="col-md-4">
        <input type="text"
               name="q"
               class="form-control"
               placeholder="Cari judul / excerpt..."
               value="<?php echo htmlspecialchars($filter_q); ?>">
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-primary">
          Cari
        </button>
        <a href="<?php echo $baseUrl; ?>/admin/blog" class="btn btn-outline-secondary">
          Reset
        </a>
      </div>
      <div class="col-md-4 text-end">
        <a href="<?php echo $baseUrl; ?>/admin/blog/create" class="btn btn-success">
          + Tambah Artikel
        </a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <?php if (!empty($posts)): ?>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
          <tr>
            <th style="width:60px;">#</th>
            <th>Judul</th>
            <th style="width:140px;">Status</th>
            <th style="width:180px;">Tanggal</th>
            <th class="text-end" style="width:140px;">Aksi</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($posts as $i => $post): ?>
            <tr>
              <td><?php echo ($i + 1) + ($page - 1) * $perPage; ?></td>
              <td>
                <div class="fw-semibold text-truncate"
                     style="max-width: 260px;"
                     title="<?php echo htmlspecialchars($post['title']); ?>">
                  <?php echo htmlspecialchars($post['title']); ?>
                </div>
                <?php if (!empty($post['excerpt'])): ?>
                  <div class="small text-muted text-truncate" style="max-width: 260px;">
                    <?php echo htmlspecialchars($post['excerpt']); ?>
                  </div>
                <?php endif; ?>
                <div class="small text-muted">
                  Slug: <code><?php echo htmlspecialchars($post['slug']); ?></code>
                </div>
              </td>
              <td>
                <?php if (!empty($post['is_published'])): ?>
                  <span class="badge bg-success">Published</span>
                <?php else: ?>
                  <span class="badge bg-secondary">Draft</span>
                <?php endif; ?>
              </td>
              <td class="small text-muted">
                <?php
                $ts = $post['published_at'] ?: $post['created_at'];
                echo $ts ? htmlspecialchars($ts) : '-';
                ?>
              </td>
              <td class="text-end">
                <a href="<?php echo $baseUrl; ?>/admin/blog/edit/<?php echo (int)$post['id']; ?>"
                   class="btn btn-sm btn-primary">
                  Edit
                </a>
                <form action="<?php echo $baseUrl; ?>/admin/blog/delete/<?php echo (int)$post['id']; ?>"
                      method="post"
                      class="d-inline"
                      onsubmit="return confirm('Yakin ingin menghapus artikel ini?');">
                      <input type="hidden" name="_token"
       value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

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

      <?php if ($lastPage > 1): ?>
        <nav>
          <ul class="pagination pagination-sm mb-0">
            <?php for ($p = 1; $p <= $lastPage; $p++): ?>
              <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>">
                <a class="page-link"
                   href="<?php echo $baseUrl; ?>/admin/blog?page=<?php echo $p; ?>&q=<?php echo urlencode($filter_q); ?>">
                  <?php echo $p; ?>
                </a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
      <?php endif; ?>

    <?php else: ?>
      <p class="mb-0 text-muted">
        Belum ada artikel.
        <a href="<?php echo $baseUrl; ?>/admin/blog/create">Tambah artikel pertama</a>.
      </p>
    <?php endif; ?>
  </div>
</div>
