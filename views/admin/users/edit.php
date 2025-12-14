<?php
$baseUrl   = $baseUrl ?? '/eventprint/public';
$editUser  = $editUser ?? [];

$id      = (int)($editUser['id'] ?? 0);
$active  = !empty($editUser['is_active']);
?>

<h1 class="h3 mb-3">Edit User</h1>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?php echo $baseUrl; ?>/admin/users/update/<?php echo $id; ?>">
      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

      <div class="mb-3">
        <label class="form-label">Nama</label>
        <input class="form-control" name="name" required
               value="<?php echo htmlspecialchars($editUser['name'] ?? ''); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" required
               value="<?php echo htmlspecialchars($editUser['email'] ?? ''); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Role</label>
        <select class="form-select" name="role">
          <option value="admin" <?php echo (($editUser['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>admin</option>
          <option value="super_admin" <?php echo (($editUser['role'] ?? '') === 'super_admin') ? 'selected' : ''; ?>>super_admin</option>
        </select>
      </div>

      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
               <?php echo $active ? 'checked' : ''; ?>>
        <label class="form-check-label" for="is_active">Active</label>
      </div>

      <div class="mb-3">
        <label class="form-label">Password baru (opsional)</label>
        <input class="form-control" type="password" name="password"
               placeholder="Kosongkan jika tidak ingin ganti">
      </div>

      <button class="btn btn-primary" type="submit">Update</button>
      <a class="btn btn-light" href="<?php echo $baseUrl; ?>/admin/users">Kembali</a>
    </form>
  </div>
</div>
