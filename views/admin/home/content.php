<?php
$baseUrl = $vars['baseUrl'] ?? '/eventprint';
$content = $vars['content'] ?? [];
$csrfToken = $vars['csrfToken'] ?? '';
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">Edit Konten Home</h1>
    <p class="text-muted small mb-0">Kelola informasi kontak dan teks umum</p>
  </div>
</div>

<div class="dash-container-card fade-in delay-1">
  <div class="p-4">
    <form method="post" action="<?= $baseUrl ?>/admin/home/content/update">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

      <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-address-book me-2"></i>Informasi Kontak</h5>

      <div class="row g-4">
        <div class="col-lg-6">
          <div class="mb-3">
            <label class="dash-form-label">ALAMAT TOKO</label>
            <textarea class="form-control" name="contact_address" rows="4"
              placeholder="Masukkan alamat lengkap toko..."><?= htmlspecialchars($content['contact_address'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="mb-4">
            <label class="dash-form-label">EMAIL</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted"><i class="fas fa-envelope"></i></span>
              <input type="email" class="form-control" name="contact_email"
                value="<?= htmlspecialchars($content['contact_email'] ?? '') ?>" placeholder="info@example.com">
            </div>
          </div>

          <div class="mb-3">
            <label class="dash-form-label">WHATSAPP</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted"><i class="fab fa-whatsapp"></i></span>
              <input type="text" class="form-control" name="contact_whatsapp"
                value="<?= htmlspecialchars($content['contact_whatsapp'] ?? '') ?>" placeholder="081234567890">
            </div>
            <div class="form-text text-muted">Akan digunakan untuk tombol chat WA.</div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-5 pt-3 border-top">
        <button class="btn btn-primary px-4" type="submit">
          <i class="fas fa-save me-2"></i>Simpan
        </button>
        <a class="btn btn-outline-secondary px-4" href="<?= $baseUrl ?>/admin/home">
          <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
      </div>
    </form>
  </div>
</div>