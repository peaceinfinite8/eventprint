<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$editUser = $editUser ?? [];
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
$id = (int) ($editUser['id'] ?? 0);
$active = !empty($editUser['is_active']);
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">Edit User</h1>
    <p class="text-muted small mb-0">Perbarui informasi akun pengguna</p>
  </div>
</div>

<div class="dash-container-card fade-in delay-1">
  <div class="p-4">
    <form method="post" action="<?php echo $baseUrl; ?>/admin/users/update/<?php echo $id; ?>">
      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

      <div class="row g-4">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="dash-form-label">NAMA LENGKAP</label>
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="fas fa-user"></i></span>
              <input class="form-control" name="name" required placeholder="Contoh: John Doe"
                value="<?php echo htmlspecialchars($editUser['name'] ?? ''); ?>">
            </div>
          </div>

          <div class="mb-3">
            <label class="dash-form-label">ALAMAT EMAIL</label>
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="fas fa-envelope"></i></span>
              <input class="form-control" type="email" name="email" required placeholder="user@eventprint.id"
                value="<?php echo htmlspecialchars($editUser['email'] ?? ''); ?>">
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="mb-3">
            <label class="dash-form-label">PASSWORD BARU</label>
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="fas fa-key"></i></span>
              <input class="form-control" type="password" name="password"
                placeholder="Kosongkan jika tidak ingin mengganti">
            </div>
            <div class="form-text small">Minimal 6 karakter jika ingin diubah.</div>
          </div>

          <div class="mb-3">
            <label class="dash-form-label">ROLE AKSES</label>
            <select class="form-select" name="role">
              <option value="admin" <?php echo (($editUser['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>Admin
                (Standard)</option>
              <option value="super_admin" <?php echo (($editUser['role'] ?? '') === 'super_admin') ? 'selected' : ''; ?>>
                Super Admin (Full Access)</option>
            </select>
          </div>
        </div>
      </div>

      <div class="d-flex align-items-center justify-content-between pt-3 mt-3 border-top">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo $active ? 'checked' : ''; ?>>
          <label class="form-check-label fw-medium text-dark" for="is_active">Status Akun Aktif</label>
        </div>

        <div class="d-flex gap-2">
          <a class="btn btn-outline-secondary px-4" href="<?php echo $baseUrl; ?>/admin/users">Kembali</a>
          <button class="btn btn-primary px-4 shadow-sm" type="submit"><i class="fas fa-save me-2"></i> Update
            User</button>
        </div>
      </div>
    </form>
  </div>
</div>