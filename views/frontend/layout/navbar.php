<?php
// app/views/frontend/layout/navbar.php
$baseUrl = rtrim(($vars['baseUrl'] ?? '/eventprint/public'), '/');
?>
<nav class="navbar navbar-expand-lg bg-white sticky-top ep-navbar shadow-sm">
  <div class="container-fluid px-4">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $baseUrl ?>/" aria-label="EventPrint">
      <div class="ep-brand-mark"><i class="bi bi-printer-fill"></i></div>
      <div class="lh-1">
        <div class="ep-brand-name">EventPrint</div>
        <div class="ep-brand-sub">Online</div>
      </div>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#epNavMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 ep-navlinks" id="epNavLinks">
        <li class="nav-item"><a class="nav-link" data-nav="home" href="<?= $baseUrl ?>/">Home</a></li>
        <li class="nav-item"><a class="nav-link" data-nav="products" href="<?= $baseUrl ?>/products">Produk &amp; Layanan</a></li>
        <li class="nav-item"><a class="nav-link" data-nav="our-home" href="<?= $baseUrl ?>/our-home">Our Home</a></li>
        <li class="nav-item"><a class="nav-link" data-nav="articles" href="<?= $baseUrl ?>/articles">Artikel</a></li>
        <li class="nav-item"><a class="nav-link" data-nav="contact" href="<?= $baseUrl ?>/contact">Kontak</a></li>
      </ul>

      <div class="d-flex align-items-center gap-2">
        <a class="btn btn-primary ep-cta" href="<?= $baseUrl ?>/contact#order">
          <i class="bi bi-lightning-charge-fill me-1"></i>Order Sekarang
        </a>
      </div>
    </div>
  </div>
</nav>
