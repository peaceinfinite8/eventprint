<?php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';

require_once __DIR__ . '/../../../app/core/Auth.php';
$authUser = Auth::user();

$role = strtolower((string)($authUser['role'] ?? ($_SESSION['user']['role'] ?? '')));
$isAdmin = ($role === 'admin');
$isSuper = ($role === 'super_admin');

// teks kecil biar orang awam ngerti
$roleLabel = $isSuper ? 'Super Admin' : ($isAdmin ? 'Admin' : ucfirst($role));
?>

<nav id="sidebar" class="sidebar js-sidebar">
  <div class="sidebar-content js-simplebar">

    <!-- BRAND -->
    <a class="sidebar-brand d-flex align-items-center" href="<?php echo $baseUrl; ?>/admin/dashboard">
      <span class="align-middle sidebar-text">Event Print Admin</span>
    </a>

    <!-- INFO ROLE (biar awam paham dia login sebagai apa) -->
    <div class="px-3 pb-3" style="opacity:.9;">
      <div style="font-size:.8rem; color:#94a3b8;">Login sebagai</div>
      <div style="font-weight:700; color:#e2e8f0; line-height:1.1;"><?php echo htmlspecialchars($roleLabel); ?></div>
      <?php if (!empty($authUser['name'])): ?>
        <div style="font-size:.85rem; color:#cbd5e1;"><?php echo htmlspecialchars($authUser['name']); ?></div>
      <?php endif; ?>
    </div>

    <ul class="sidebar-nav">

      <!-- DASHBOARD -->
      <li class="sidebar-item">
        <a class="sidebar-link" href="<?php echo $baseUrl; ?>/admin/dashboard">
          <i class="align-middle" data-feather="home"></i>
          <span class="align-middle sidebar-text">Dashboard</span>
        </a>
      </li>

      <!-- ===================== ADMIN (ARTIKEL ONLY) ===================== -->
      <?php if ($isAdmin): ?>

        <li class="sidebar-header">Kelola Konten</li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="<?php echo $baseUrl; ?>/admin/blog">
            <i class="align-middle" data-feather="file-text"></i>
            <span class="align-middle sidebar-text">Artikel</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="<?php echo $baseUrl; ?>/admin/contact">
            <i class="align-middle" data-feather="mail"></i>
            <span class="align-middle sidebar-text">Pesan Masuk</span>
          </a>
        </li>

      <?php endif; ?>

      <!-- ===================== SUPER ADMIN (FULL) ===================== -->
      <?php if ($isSuper): ?>

        <li class="sidebar-header">Kelola Website</li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="<?php echo $baseUrl; ?>/admin/home">
            <i class="align-middle" data-feather="layout"></i>
            <span class="align-middle sidebar-text">Konten Beranda</span>
          </a>
        </li>

        <li class="sidebar-header">Katalog Produk</li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="<?php echo $baseUrl; ?>/admin/products">
            <i class="align-middle" data-feather="box"></i>
            <span class="align-middle sidebar-text">Produk</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="<?php echo $baseUrl; ?>/admin/product-categories">
            <i class="align-middle" data-feather="tag"></i>
            <span class="align-middle sidebar-text">Kategori Produk</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="<?php echo $baseUrl; ?>/admin/discounts">
            <i class="align-middle" data-feather="percent"></i>
            <span class="align-middle sidebar-text">Diskon</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="<?php echo $baseUrl; ?>/admin/our-store">
            <i class="align-middle" data-feather="map-pin"></i>
            <span class="align-middle sidebar-text">Lokasi / Our Store</span>
          </a>
        </li>

        <li class="sidebar-header">Konten & Komunikasi</li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="<?php echo $baseUrl; ?>/admin/blog">
            <i class="align-middle" data-feather="file-text"></i>
            <span class="align-middle sidebar-text">Artikel</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="<?php echo $baseUrl; ?>/admin/contact">
            <i class="align-middle" data-feather="mail"></i>
            <span class="align-middle sidebar-text">Pesan Masuk</span>
          </a>
        </li>

        <li class="sidebar-header">Pengaturan</li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="<?php echo $baseUrl; ?>/admin/settings">
            <i class="align-middle" data-feather="settings"></i>
            <span class="align-middle sidebar-text">General Settings</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="<?php echo $baseUrl; ?>/admin/users">
            <i class="align-middle" data-feather="users"></i>
            <span class="align-middle sidebar-text">Users</span>
          </a>
        </li>

      <?php endif; ?>

      <!-- LOGOUT (SEMUA ROLE) -->
      <li class="sidebar-item">
        <a class="sidebar-link"
           href="<?php echo $baseUrl; ?>/admin/logout"
           onclick="return confirm('Yakin ingin logout?');">
          <i class="align-middle" data-feather="log-out"></i>
          <span class="align-middle sidebar-text">Logout</span>
        </a>
      </li>

    </ul>
  </div>
</nav>
