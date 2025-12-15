<?php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$success = $_SESSION['flash_success'] ?? null;
$error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<section class="ep-section py-5">
  <div class="container-fluid px-4">
    <div class="ep-section-head d-flex align-items-end justify-content-between gap-3 flex-wrap mb-4">
      <div>
        <div class="ep-eyebrow-sm">Kontak</div>
        <h1 class="ep-title-sm">Hubungi Kami</h1>
      </div>
      <a class="btn btn-outline-secondary" href="<?= $baseUrl ?>/products">Lihat Produk</a>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row g-4">
      <div class="col-lg-6">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <h5 class="fw-bold mb-3" id="form">Kirim Pesan</h5>

            <form method="post" action="<?= $baseUrl ?>/contact/send">
              <div class="mb-3">
                <label class="form-label">Nama *</label>
                <input class="form-control" name="name" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email">
              </div>

              <div class="mb-3">
                <label class="form-label">WhatsApp / Telepon</label>
                <input class="form-control" name="phone">
              </div>

              <div class="mb-3">
                <label class="form-label">Pesan *</label>
                <textarea class="form-control" name="message" rows="5" required></textarea>
              </div>

              <button class="btn btn-primary" type="submit">
                <i class="bi bi-send-fill me-2"></i>Kirim
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-6" id="order">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <h5 class="fw-bold mb-2">Order / Quotation</h5>
            <p class="text-muted mb-3">Pilih produk dulu, lalu hitung harga di detail produk.</p>
            <a class="btn btn-success w-100" href="https://wa.me/6281234567890" target="_blank" rel="noopener">
              <i class="bi bi-whatsapp me-2"></i>Chat WhatsApp
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
