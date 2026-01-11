<?php
$baseUrl = $baseUrl ?? '/eventprint';
$messages = $messages ?? [];
$total = $total ?? 0;
$page = $page ?? 1;
$perPage = $perPage ?? 20;
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');

$lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">Inbox Pesan</h1>
    <p class="text-muted small mb-0">Daftar semua pesan dari formulir kontak</p>
  </div>
</div>

<div class="dash-container-card fade-in delay-1">
  <div class="p-0">
    <?php if (!empty($messages)): ?>
      <div class="table-responsive">
        <table class="table table-custom table-striped align-middle mb-0">
          <thead>
            <tr>
              <th class="ps-4" style="width: 60px;">#</th>
              <th>Pengirim & Email</th>
              <th>Subjek</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th class="text-end pe-4" style="width: 140px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($messages as $i => $msg): ?>
              <tr>
                <td class="ps-4 text-muted small"><?php echo ($i + 1) + ($page - 1) * $perPage; ?></td>
                <td>
                  <div class="fw-bold text-dark"><?php echo htmlspecialchars($msg['name']); ?></div>
                  <div class="small text-muted"><?php echo htmlspecialchars($msg['email']); ?></div>
                </td>
                <td>
                  <div class="text-truncate" style="max-width: 250px;">
                    <?php echo htmlspecialchars($msg['subject'] ?? '(No Subject)'); ?>
                  </div>
                </td>
                <td>
                  <?php if (!empty($msg['is_read'])): ?>
                    <span class="badge bg-light text-secondary border border-secondary-subtle rounded-pill">
                      <i class="fas fa-check-double me-1"></i> Dibaca
                    </span>
                  <?php else: ?>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                      <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Baru
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="small text-muted">
                    <i class="far fa-clock me-1"></i>
                    <?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?>
                  </div>
                </td>
                <td class="text-end pe-4">
                  <div class="btn-group">
                    <a href="<?php echo $baseUrl; ?>/admin/contact/<?php echo $msg['id']; ?>"
                      class="btn btn-icon btn-sm text-primary" title="Lihat Detail">
                      <i class="fas fa-eye"></i>
                    </a>

                    <?php if (!empty(Auth::user()) && in_array(strtolower(Auth::user()['role']), ['superadmin', 'super_admin', 'super admin'], true)): ?>
                      <form action="<?php echo $baseUrl; ?>/admin/contact/<?php echo $msg['id']; ?>/delete" method="post"
                        class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pesan ini?');">
                        <input type="hidden" name="_token"
                          value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-icon btn-sm text-danger" title="Hapus">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </form>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>


      <!-- Pagination -->
      <?php echo renderPagination($baseUrl, '/admin/contact/messages', ['total' => $total, 'page' => $page, 'per_page' => $perPage], []); ?>

    <?php else: ?>
      <div class="text-center py-5">
        <div class="mb-3">
          <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
            style="width: 80px; height: 80px;">
            <i class="fas fa-inbox fa-3x text-muted opacity-25"></i>
          </div>
        </div>
        <p class="text-muted mb-0">Belum ada pesan masuk.</p>
      </div>
    <?php endif; ?>
  </div>
</div>