<?php $baseUrl = $vars['baseUrl'] ?? '/eventprint/public'; ?>

<footer class="ep-footer py-4">
  <div class="container-fluid px-4">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="d-flex align-items-center gap-2 mb-2">
          <div class="ep-brand-mark ep-brand-mark--sm" aria-hidden="true"><i class="bi bi-printer-fill"></i></div>
          <div class="fw-semibold">EventPrint</div>
        </div>
        <div class="text-muted small">
          Template frontend digital printing (Bootstrap 5). Struktur class/id konsisten untuk di-consume backend.
        </div>
      </div>

      <div class="col-6 col-lg-2">
        <div class="ep-footer-title">Menu</div>
        <ul class="ep-footer-links list-unstyled">
          <li><a href="<?= $baseUrl ?>/">Home</a></li>
          <li><a href="<?= $baseUrl ?>/products">Produk &amp; Layanan</a></li>
          <li><a href="<?= $baseUrl ?>/our-home">Our Home</a></li>
          <li><a href="<?= $baseUrl ?>/articles">Artikel</a></li>
          <li><a href="<?= $baseUrl ?>/contact">Kontak</a></li>
        </ul>
      </div>

      <div class="col-6 col-lg-3">
        <div class="ep-footer-title">Produk Kami</div>
        <ul class="ep-footer-links list-unstyled" id="epFooterProducts"></ul>
      </div>

      <div class="col-lg-3">
        <div class="ep-footer-title">Sosial Media</div>
        <div class="d-flex gap-2">
          <a class="ep-social" href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a class="ep-social" href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a class="ep-social" href="#" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
          <a class="ep-social" href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
        </div>
      </div>
    </div>

    <hr class="my-4">

    <div class="d-flex flex-wrap justify-content-between gap-2 small text-muted">
      <div>© <span id="epYear"></span> EventPrint by Peace Infinite</div>
      <div>Public Site · Bootstrap 5 · PHP MVC</div>
    </div>
  </div>
</footer>

<a class="ep-wa" id="epWaFloat" href="#" aria-label="WhatsApp">
  <i class="bi bi-whatsapp"></i>
</a>
