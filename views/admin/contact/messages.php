<?php
$baseUrl  = $baseUrl ?? '/eventprint/public';
$messages = $messages ?? [];
$total    = $total ?? 0;
$page     = $page ?? 1;
$perPage  = $perPage ?? 20;

$lastPage = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
?>

<h1 class="h3 mb-3">Pesan Kontak</h1>

<div class="card">
  <div class="card-body">
    <?php if (!empty($messages)): ?>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
          <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Subjek</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th class="text-end">Aksi</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($messages as $i => $msg): ?>
            <tr>
              <td><?php echo ($i + 1) + ($page - 1) * $perPage; ?></td>
              <td><?php echo htmlspecialchars($msg['name']); ?></td>
              <td class="small">
                <?php echo htmlspecialchars($msg['email']); ?>
              </td>
              <td class="small">
                <?php echo htmlspecialchars($msg['subject'] ?? ''); ?>
              </td>
              <td class="small text-muted">
                <?php echo htmlspecialchars($msg['created_at'] ?? ''); ?>
              </td>
              <td>
                <?php if (!empty($msg['is_read'])): ?>
                  <span class="badge bg-success">Sudah dibaca</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">Baru</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <a href="<?php echo $baseUrl; ?>/admin/contact/<?php echo $msg['id']; ?>"
                   class="btn btn-sm btn-outline-primary">
                  Lihat
                </a>
                <?php if (!empty(Auth::user()) && in_array(strtolower(Auth::user()['role']), ['superadmin','super_admin','super admin'], true)): ?>
                      <form action="<?php echo $baseUrl; ?>/admin/contact/<?php echo $msg['id']; ?>/delete"
                            method="post"
                            class="d-inline"
                            onsubmit="return confirm('Yakin ingin menghapus pesan ini?');">
                        <input type="hidden" name="_token"
                              value="<?php echo htmlspecialchars($csrfToken ?? Security::csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                          Hapus
                        </button>
                      </form>

                </form>
                <?php endif; ?>
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
                <a class="page-link" href="<?php echo $baseUrl; ?>/admin/contact/messages?page=<?php echo $p; ?>">
                  <?php echo $p; ?>
                </a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
      <?php endif; ?>

    <?php else: ?>
      <p class="mb-0 text-muted">Belum ada pesan yang masuk.</p>
    <?php endif; ?>
  </div>
</div>
