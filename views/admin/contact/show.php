<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$message = $message ?? null;

if (!$message) {
    echo "<p>Pesan tidak ditemukan.</p>";
    return;
}
?>

<h1 class="h3 mb-3">Detail Pesan Kontak</h1>

<div class="card mb-3">
  <div class="card-body">
    <dl class="row mb-0">
      <dt class="col-sm-3">Nama</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars($message['name']); ?></dd>

      <dt class="col-sm-3">Email</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars($message['email']); ?></dd>

      <?php if (!empty($message['phone'])): ?>
      <dt class="col-sm-3">Phone</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars($message['phone']); ?></dd>
      <?php endif; ?>

      <dt class="col-sm-3">Subjek</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars($message['subject'] ?? ''); ?></dd>

      <dt class="col-sm-3">Tanggal</dt>
      <dd class="col-sm-9 small text-muted"><?php echo htmlspecialchars($message['created_at'] ?? ''); ?></dd>

      <dt class="col-sm-3">Status</dt>
      <dd class="col-sm-9">
        <?php if (!empty($message['is_read'])): ?>
          <span class="badge bg-success">Sudah dibaca</span>
        <?php else: ?>
          <span class="badge bg-warning text-dark">Baru</span>
        <?php endif; ?>
      </dd>
    </dl>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header">
    Pesan
  </div>
  <div class="card-body">
    <p class="mb-0" style="white-space: pre-line;">
      <?php echo htmlspecialchars($message['message']); ?>
    </p>
  </div>
</div>

<div class="d-flex justify-content-between">
  <a href="<?php echo $baseUrl; ?>/admin/contact" class="btn btn-outline-secondary">
    &laquo; Kembali
  </a>

  <?php if (!empty(Auth::user()) && in_array(strtolower(Auth::user()['role']), ['superadmin','super_admin','super admin'], true)): ?>
  <form action="<?php echo $baseUrl; ?>/admin/contact/<?php echo $message['id']; ?>/delete"
        method="post"
        onsubmit="return confirm('Yakin ingin menghapus pesan ini?');">
    <button type="submit" class="btn btn-outline-danger">
      Hapus Pesan
    </button>
  </form>
  <?php endif; ?>
</div>
