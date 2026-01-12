<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$message = $message ?? null;
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');

if (!$message) {
  echo "<p>Pesan tidak ditemukan.</p>";
  return;
}
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">Detail Pesan</h1>
    <p class="text-muted small mb-0">
      <a href="<?php echo $baseUrl; ?>/admin/contact/messages" class="text-decoration-none text-muted">Inbox</a>
      <i class="fas fa-chevron-right mx-1" style="font-size: 10px;"></i>
      Detail
    </p>
  </div>
</div>

<div class="row g-4 fade-in delay-1">
  <div class="col-lg-4">
    <div class="dash-container-card h-100">
      <div class="p-4 border-bottom">
        <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-user-circle me-2"></i>Informasi Pengirim</h5>
      </div>
      <div class="p-4">
        <div class="mb-3">
          <label class="small text-muted fw-bold d-block mb-1">NAMA PENGIRIM</label>
          <div class="fw-medium text-dark"><?php echo htmlspecialchars($message['name']); ?></div>
        </div>
        <div class="mb-3">
          <label class="small text-muted fw-bold d-block mb-1">EMAIL</label>
          <div class="fw-medium text-dark">
            <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" class="text-decoration-none">
              <?php echo htmlspecialchars($message['email']); ?>
            </a>
          </div>
        </div>
        <?php if (!empty($message['phone'])): ?>
          <div class="mb-3">
            <label class="small text-muted fw-bold d-block mb-1">PHONE</label>
            <div class="fw-medium text-dark"><?php echo htmlspecialchars($message['phone']); ?></div>
          </div>
        <?php endif; ?>

        <div class="mb-4">
          <label class="small text-muted fw-bold d-block mb-1">TANGGAL KIRIM</label>
          <div class="fw-medium text-dark">
            <i class="far fa-clock me-1 text-muted"></i>
            <?php echo date('d F Y, H:i', strtotime($message['created_at'])); ?> WIB
          </div>
        </div>

        <div class="mb-3">
          <label class="small text-muted fw-bold d-block mb-1">STATUS</label>
          <?php if (!empty($message['is_read'])): ?>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">
              <i class="fas fa-check-double me-1"></i> Sudah dibaca
            </span>
          <?php else: ?>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3">
              <i class="fas fa-envelope me-1"></i> Baru
            </span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="dash-container-card h-100">
      <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-primary fs-5">
          <i class="fas fa-comment-dots me-2"></i>Isi Pesan
        </h5>
        <?php if (empty($message['is_read'])): ?>
          <span class="badge bg-warning text-dark">New Message</span>
        <?php endif; ?>
      </div>
      <div class="p-4">
        <div class="mb-3">
          <h6 class="fw-bold text-dark mb-1">Subjek:</h6>
          <div class="p-3 bg-light rounded border border-light">
            <?php echo htmlspecialchars($message['subject'] ?? '(No Subject)'); ?>
          </div>
        </div>

        <div>
          <h6 class="fw-bold text-dark mb-2">Pesan:</h6>
          <div class="p-3 bg-white border rounded shadow-sm" style="min-height: 200px; white-space: pre-line;">
            <?php echo htmlspecialchars($message['message']); ?>
          </div>
        </div>

        <div class="d-flex justify-content-between mt-5 pt-3 border-top">
          <a href="<?php echo $baseUrl; ?>/admin/contact/messages" class="btn btn-outline-secondary px-4">
            <i class="fas fa-arrow-left me-2"></i>Kembali
          </a>

          <div class="d-flex gap-2">
            <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" class="btn btn-primary px-4">
              <i class="fas fa-reply me-2"></i>Balas Email
            </a>

            <?php if (!empty(Auth::user()) && in_array(strtolower(Auth::user()['role']), ['superadmin', 'super_admin', 'super admin'], true)): ?>
              <form action="<?php echo $baseUrl; ?>/admin/contact/<?php echo $message['id']; ?>/delete" method="post"
                onsubmit="return confirm('Yakin ingin menghapus pesan ini?');">
                <input type="hidden" name="_token"
                  value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="btn btn-outline-danger">
                  <i class="fas fa-trash-alt me-1"></i>Hapus
                </button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>