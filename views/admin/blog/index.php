<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$posts = $posts ?? [];
$filter_q = $filter_q ?? '';
$pagination = $pagination ?? ['total' => 0, 'page' => 1, 'per_page' => 10];
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : ($_SESSION['_token'] ?? ''));

$total = (int) ($pagination['total'] ?? 0);
$page = (int) ($pagination['page'] ?? 1);
$perPage = (int) ($pagination['per_page'] ?? 10);
$lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">Artikel / Blog</h1>
    <p class="text-muted small mb-0">Kelola postingan artikel dan berita</p>
  </div>
  <a href="<?php echo $baseUrl; ?>/admin/blog/create" class="btn btn-primary shadow-sm">
    <i class="fas fa-pen-nib me-2"></i>Tambah Artikel
  </a>
</div>

<div class="dash-container-card fade-in delay-1">
  <div class="p-4 border-bottom bg-light bg-opacity-50">
    <form class="row g-2 align-items-center" method="get" action="<?php echo $baseUrl; ?>/admin/blog">
      <div class="col-md-5">
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
          <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Cari judul atau excerpt..."
            value="<?php echo htmlspecialchars($filter_q); ?>">
        </div>
      </div>
      <div class="col-md-auto">
        <button type="submit" class="btn btn-primary px-3">Cari</button>
        <a href="<?php echo $baseUrl; ?>/admin/blog" class="btn btn-outline-secondary px-3 ms-1">Reset</a>
      </div>
    </form>
  </div>

  <div class="p-0">
    <?php if (!empty($posts)): ?>
      <div class="table-responsive">
        <table class="table table-custom table-striped align-middle mb-0">
          <thead>
            <tr>
              <th class="ps-4" style="width:60px;">#</th>
              <th>Judul & Info</th>
              <th style="width:150px;">Kategori</th>
              <th style="width:140px;">Status</th>
              <th style="width:180px;">Tanggal</th>
              <th class="text-end pe-4" style="width:140px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($posts as $i => $post): ?>
              <tr>
                <td class="ps-4 text-muted small"><?php echo ($i + 1) + ($page - 1) * $perPage; ?></td>
                <td>
                  <div class="fw-bold text-dark mb-1 text-truncate" style="max-width: 350px;"
                    title="<?php echo htmlspecialchars($post['title']); ?>">
                    <?php echo htmlspecialchars($post['title']); ?>
                  </div>
                  <?php if (!empty($post['excerpt'])): ?>
                    <div class="small text-muted text-truncate mb-1" style="max-width: 350px;">
                      <?php echo htmlspecialchars($post['excerpt']); ?>
                    </div>
                  <?php endif; ?>
                  <div class="d-flex align-items-center gap-2">
                    <code
                      class="small text-muted bg-light px-1 rounded border"><?php echo htmlspecialchars($post['slug']); ?></code>
                    <?php if (!empty($post['is_featured'])): ?>
                      <span
                        class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle small py-0 px-2 rounded-pill">
                        <i class="fas fa-star me-1" style="font-size:10px;"></i> Featured
                      </span>
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <?php if (!empty($post['post_category'])): ?>
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill">
                      <?php echo htmlspecialchars(ucfirst($post['post_category'])); ?>
                    </span>
                  <?php else: ?>
                    <span class="text-muted small">-</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($post['is_published'])): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                      <i class="fas fa-check-circle me-1"></i> Published
                    </span>
                  <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                      <i class="fas fa-file me-1"></i> Draft
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="small text-muted">
                    <i class="far fa-calendar-alt me-1"></i>
                    <?php
                    $ts = $post['published_at'] ?: $post['created_at'];
                    echo $ts ? date('d M Y, H:i', strtotime($ts)) : '-';
                    ?>
                  </div>
                </td>
                <td class="text-end pe-4">
                  <div class="btn-group">
                    <a href="<?php echo $baseUrl; ?>/admin/blog/edit/<?php echo (int) $post['id']; ?>"
                      class="btn btn-icon btn-sm text-primary" title="Edit">
                      <i class="fas fa-pencil-alt"></i>
                    </a>
                    <form action="<?php echo $baseUrl; ?>/admin/blog/delete/<?php echo (int) $post['id']; ?>" method="post"
                      class="d-inline" onsubmit="return confirm('Yakin ingin menghapus artikel ini?');">
                      <input type="hidden" name="_token"
                        value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                      <button type="submit" class="btn btn-icon btn-sm text-danger" title="Hapus">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>


      <!-- Pagination -->
      <?php echo renderPagination($baseUrl, '/admin/blog', $pagination, ['q' => $filter_q]); ?>

    <?php else: ?>
      <div class="text-center py-5">
        <div class="mb-3">
          <i class="fas fa-newspaper fa-3x text-muted opacity-25"></i>
        </div>
        <p class="text-muted mb-3">Belum ada artikel yang ditemukan.</p>
        <a href="<?php echo $baseUrl; ?>/admin/blog/create" class="btn btn-outline-primary btn-sm">
          Mulai Tulis Artikel
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>