<?php
$baseUrl = $baseUrl ?? ($vars['baseUrl'] ?? '/eventprint/public');
$page    = $page    ?? ($vars['page'] ?? '');

function epActive(string $key, string $page): string {
  return $key === $page ? 'active' : '';
}
?>
<nav id="epNavbar" class="navbar navbar-expand-lg bg-white sticky-top ep-navbar shadow-sm">
  <div class="container-fluid px-4">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $baseUrl ?>/" aria-label="EventPrint">
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
        <li class="nav-item"><a class="nav-link <?= epActive('home',$page) ?>" href="<?= $baseUrl ?>/">Home</a></li>
        <li class="nav-item"><a class="nav-link <?= epActive('products',$page) ?>" href="<?= $baseUrl ?>/products">Produk &amp; Layanan</a></li>
        <li class="nav-item"><a class="nav-link <?= epActive('our-home',$page) ?>" href="<?= $baseUrl ?>/our-home">Our Home</a></li>
        <li class="nav-item"><a class="nav-link <?= epActive('articles',$page) ?>" href="<?= $baseUrl ?>/articles">Artikel</a></li>
        <li class="nav-item"><a class="nav-link <?= epActive('contact',$page) ?>" href="<?= $baseUrl ?>/contact">Kontak</a></li>
      </ul>

      <div class="d-flex align-items-center gap-2">
        <a class="btn btn-primary ep-cta" href="<?= $baseUrl ?>/contact#order">
          <i class="bi bi-lightning-charge-fill me-1"></i>Order Sekarang
        </a>
      </div>
    </div>
  </div>
</nav>
