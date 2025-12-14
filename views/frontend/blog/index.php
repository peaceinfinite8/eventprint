<?php require __DIR__ . '/../layout/header.php'; ?>


<body data-page="articles">
  <div class="ep-topbar d-none d-lg-block">
    <div class="container-fluid px-4">
      <div class="d-flex align-items-center justify-content-between py-2">
        <div class="d-flex align-items-center gap-3 text-white-50 small">
          <span class="d-inline-flex align-items-center gap-2"><i class="bi bi-whatsapp"></i> CS</span>
          <span class="d-inline-flex align-items-center gap-2"><i class="bi bi-chevron-left"></i> Cetak online terbesar <i class="bi bi-chevron-right"></i></span>
        </div>
        <div class="d-flex align-items-center gap-3 text-white-50 small">
          <span class="d-inline-flex align-items-center gap-2"><i class="bi bi-geo-alt"></i> Order tracking</span>
          <span class="d-inline-flex align-items-center gap-2"><span class="ep-flag-id"></span> Ind / Rp</span>
        </div>
      </div>
    </div>
  </div>
  <nav id="epNavbar" class="navbar navbar-expand-lg bg-white sticky-top ep-navbar shadow-sm">
    <div class="container-fluid px-4">
      <a class="navbar-brand d-flex align-items-center gap-2" href="index.html" aria-label="EventPrint">
        <div class="ep-brand-mark" aria-hidden="true"><i class="bi bi-printer-fill"></i></div>
        <div class="lh-1">
          <div class="ep-brand-name">EventPrint</div>
          <div class="ep-brand-sub">Online</div>
        </div>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#epNavMenu" aria-controls="epNavMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="epNavMenu">
        <form class="ep-search d-none d-lg-flex mx-lg-4" role="search" id="epSearchForm">
          <i class="bi bi-search"></i>
          <input id="epSearchInput" class="form-control form-control-sm" type="search" placeholder="Search for products" aria-label="Search">
        </form>

        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 ep-navlinks" id="epNavLinks">
          <li class="nav-item"><a class="nav-link" data-nav="home" href="index.html">Home</a></li>
          <li class="nav-item"><a class="nav-link" data-nav="products" href="products.html">Produk &amp; Layanan</a></li>
          <li class="nav-item"><a class="nav-link" data-nav="our-home" href="our-home.html">Our Home</a></li>
          <li class="nav-item"><a class="nav-link" data-nav="articles" href="articles.html">Artikel</a></li>
          <li class="nav-item"><a class="nav-link" data-nav="contact" href="contact.html">Kontak</a></li>
        </ul>

        <div class="d-flex align-items-center gap-2">
          <a class="btn btn-primary ep-cta" href="contact.html#order"><i class="bi bi-lightning-charge-fill me-1"></i>Order Sekarang</a>
        </div>
      </div>
    </div>
  </nav>

  <main class="ep-section py-5">
    <div class="container-fluid px-4">
      <div class="ep-section-head d-flex align-items-end justify-content-between gap-3 flex-wrap">
        <div>
          <div class="ep-eyebrow">Artikel</div>
          <h1 class="ep-title" style="font-size:clamp(1.6rem,2.6vw,2.4rem)">Semua Artikel</h1>
          <p class="ep-subtitle">List artikel siap diambil dari CMS.</p>
        </div>
        <a class="btn btn-outline-secondary" href="contact.html#order">Konsultasi</a>
      </div>

      <div class="row g-4 mt-2" id="epArticleListGrid"></div>
    </div>
  </main>

  <footer class="ep-footer py-5">
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
            <li><a href="index.html">Home</a></li>
            <li><a href="products.html">Produk &amp; Layanan</a></li>
            <li><a href="our-home.html">Our Home</a></li>
            <li><a href="articles.html">Artikel</a></li>
            <li><a href="contact.html">Kontak</a></li>
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
        <div>Frontend-only · Bootstrap 5 · HTML/CSS/JS</div>
      </div>
    </div>
  </footer>

  <a class="ep-wa" id="epWaFloat" href="#" aria-label="WhatsApp">
    <i class="bi bi-whatsapp"></i>
  </a>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
<?php require __DIR__ . '/../layout/footer.php'; ?>
<?php require __DIR__ . '/../layout/scripts.php'; ?>
