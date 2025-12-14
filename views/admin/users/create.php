<?php $baseUrl = $baseUrl ?? '/eventprint/public'; ?>

<h1 class="h3 mb-3">Tambah User</h1>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?php echo $baseUrl; ?>/admin/users/store">
      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

      <div class="mb-3">
        <label class="form-label">Nama</label>
        <input class="form-control" name="name" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Role</label>
        <select class="form-select" name="role">
          <option value="admin" selected>admin</option>
          <option value="super_admin">super_admin</option>
        </select>
      </div>

      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
        <label class="form-check-label" for="is_active">Active</label>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input class="form-control" type="password" name="password" required>
      </div>

      <button class="btn btn-primary" type="submit">Simpan</button>
      <a class="btn btn-light" href="<?php echo $baseUrl; ?>/admin/users">Batal</a>
    </form>
  </div>
</div>
