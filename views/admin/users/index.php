<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$users = $users ?? [];
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">Users Management</h1>
    <p class="text-muted small mb-0">Kelola pengguna dan hak akses</p>
  </div>
  <a class="btn btn-primary shadow-sm" href="<?php echo $baseUrl; ?>/admin/users/create">
    <i class="fas fa-plus me-2"></i> Tambah User
  </a>
</div>

<div class="dash-container-card fade-in delay-1">
  <div class="p-0">
    <div class="table-responsive">
      <table class="table table-custom table-striped align-middle mb-0">
        <thead>
          <tr>
            <th class="ps-4" style="width: 70px;">ID</th>
            <th>Nama User</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Last Login</th>
            <th class="text-end pe-4" style="width:150px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($users)): ?>
            <?php foreach ($users as $u): ?>
              <tr>
                <td class="ps-4 text-muted small"><?php echo (int) $u['id']; ?></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div
                      class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                      style="width: 35px; height: 35px; font-weight: bold;">
                      <?php echo strtoupper(substr($u['name'] ?? 'U', 0, 1)); ?>
                    </div>
                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($u['name'] ?? ''); ?></span>
                  </div>
                </td>
                <td class="small text-muted"><?php echo htmlspecialchars($u['email'] ?? ''); ?></td>
                <td>
                  <?php if (($u['role'] ?? '') === 'super_admin' || ($u['role'] ?? '') === 'superadmin'): ?>
                    <span class="badge bg-purple-subtle text-purple border border-purple-subtle rounded-pill">
                      <i class="fas fa-shield-alt me-1"></i> Super Admin
                    </span>
                  <?php else: ?>
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill">
                      <i class="fas fa-user me-1"></i> Admin
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($u['is_active'])): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                      <i class="fas fa-check-circle me-1"></i> Active
                    </span>
                  <?php else: ?>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                      <i class="fas fa-ban me-1"></i> Inactive
                    </span>
                  <?php endif; ?>
                </td>
                <td class="small text-muted">
                  <?php echo !empty($u['last_login_at']) ? date('d M Y H:i', strtotime($u['last_login_at'])) : '-'; ?>
                </td>
                <td class="text-end pe-4">
                  <div class="btn-group">
                    <a class="btn btn-icon btn-sm text-primary" title="Edit"
                      href="<?php echo $baseUrl; ?>/admin/users/edit/<?php echo (int) $u['id']; ?>">
                      <i class="fas fa-edit"></i>
                    </a>

                    <form action="<?php echo $baseUrl; ?>/admin/users/delete/<?php echo (int) $u['id']; ?>" method="post"
                      class="d-inline" onsubmit="return confirm('Yakin hapus user ini?');">
                      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                      <button class="btn btn-icon btn-sm text-danger" title="Hapus" type="submit">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">Belum ada user terdaftar.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>