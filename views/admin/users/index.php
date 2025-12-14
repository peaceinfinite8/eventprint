<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$users   = $users ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 mb-0">Users</h1>
  <a class="btn btn-primary" href="<?php echo $baseUrl; ?>/admin/users/create">+ Tambah User</a>
</div>

<div class="card">
  <div class="card-body table-responsive">
    <table class="table table-striped mb-0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Last Login</th>
          <th style="width:180px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!empty($users)): ?>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?php echo (int)$u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($u['email'] ?? ''); ?></td>
            <td>
              <span class="badge <?php echo (($u['role'] ?? '') === 'super_admin') ? 'bg-success' : 'bg-secondary'; ?>">
                <?php echo htmlspecialchars($u['role'] ?? ''); ?>
              </span>
            </td>
            <td>
              <?php if (!empty($u['is_active'])): ?>
                <span class="badge bg-primary">Active</span>
              <?php else: ?>
                <span class="badge bg-danger">Inactive</span>
              <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($u['last_login_at'] ?? '-'); ?></td>
            <td>
              <a class="btn btn-sm btn-outline-primary"
                href="<?php echo $baseUrl; ?>/admin/users/edit/<?php echo (int)$u['id']; ?>">
                Edit
              </a>

              <form action="<?php echo $baseUrl; ?>/admin/users/delete/<?php echo (int)$u['id']; ?>"
                    method="post"
                    style="display:inline-block;"
                    onsubmit="return confirm('Yakin hapus user ini?');">
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="7" class="text-muted">Belum ada user.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
