<?php
// views/admin/layout/sidebar.php

$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$baseUrl = rtrim($baseUrl, '/');

require_once __DIR__ . '/../../../app/core/Auth.php';
$authUser = Auth::user();

$role = strtolower((string) ($authUser['role'] ?? ($_SESSION['user']['role'] ?? '')));
$isAdmin = ($role === 'admin');
$isSuper = ($role === 'super_admin');

$roleLabel = $isSuper ? 'Super Admin' : ($isAdmin ? 'Admin' : ucfirst($role));

/**
 * ===== Active state helper =====
 * Cocok buat MVC kamu yang pakai baseUrl /eventprint/public
 */
$reqUri = $_SERVER['REQUEST_URI'] ?? '/';
$reqPath = parse_url($reqUri, PHP_URL_PATH) ?: '/';

// ambil path base (misal /eventprint/public)
$basePath = parse_url($baseUrl, PHP_URL_PATH) ?: '';
$basePath = rtrim($basePath, '/');

// normalisasi current path tanpa basePath
$current = $reqPath;
if ($basePath && str_starts_with($current, $basePath)) {
  $current = substr($current, strlen($basePath));
}
$current = '/' . ltrim($current, '/');
$current = rtrim($current, '/') ?: '/';

// Note: isActive() function is now defined in app/helpers/url.php

/**
 * ===== Unread badge =====
 * Aman kalau table/kolom belum ada.
 */
$unreadCount = 0;
try {
  $db = db();
  $t = $db->query("SHOW TABLES LIKE 'contact_messages'");
  if ($t && $t->num_rows > 0) {
    $colIsRead = $db->query("SHOW COLUMNS FROM contact_messages LIKE 'is_read'");
    if ($colIsRead && $colIsRead->num_rows > 0) {
      $r = $db->query("SELECT COUNT(*) AS c FROM contact_messages WHERE is_read=0");
      if ($r)
        $unreadCount = (int) ($r->fetch_assoc()['c'] ?? 0);
    } else {
      $colStatus = $db->query("SHOW COLUMNS FROM contact_messages LIKE 'status'");
      if ($colStatus && $colStatus->num_rows > 0) {
        $r = $db->query("SELECT COUNT(*) AS c FROM contact_messages WHERE status='unread'");
        if ($r)
          $unreadCount = (int) ($r->fetch_assoc()['c'] ?? 0);
      }
    }
  }
} catch (Throwable $e) {
  $unreadCount = 0;
}
?>

<nav id="sidebar" class="sidebar js-sidebar">
  <div class="sidebar-content js-simplebar">

    <a class="sidebar-brand d-flex align-items-center" href="<?= $baseUrl; ?>/admin/dashboard">
      <span class="align-middle sidebar-text">Event Print Admin</span>
    </a>

    <div class="px-3 pb-3" style="opacity:.9;">
      <div style="font-size:.8rem; color:#94a3b8;">Login sebagai</div>
      <div style="font-weight:700; color:#e2e8f0; line-height:1.1;"><?= htmlspecialchars($roleLabel) ?></div>
      <?php if (!empty($authUser['name'])): ?>
        <div style="font-size:.85rem; color:#cbd5e1;"><?= htmlspecialchars($authUser['name']) ?></div>
      <?php endif; ?>
    </div>

    <ul class="sidebar-nav">

      <!-- Dashboard -->
      <li class="sidebar-item <?= isActive('/admin/dashboard') ? 'active' : '' ?>">
        <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/dashboard">
          <i class="align-middle" data-feather="home"></i>
          <span class="align-middle sidebar-text">Dashboard</span>
        </a>
      </li>

      <?php if ($isSuper): ?>
        <!-- PRODUK & KATALOG (Consolidated) -->
        <li class="sidebar-header">Produk & Katalog</li>

        <li class="sidebar-item <?= isActive('/admin/products') ? 'active' : '' ?>">
          <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/products">
            <i class="align-middle" data-feather="box"></i>
            <span class="align-middle sidebar-text">Semua Produk</span>
          </a>
        </li>

        <li class="sidebar-item <?= isActive('/admin/product-categories') ? 'active' : '' ?>">
          <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/product-categories">
            <i class="align-middle" data-feather="tag"></i>
            <span class="align-middle sidebar-text">Kategori</span>
          </a>
        </li>

        <li class="sidebar-item <?= isActive('/admin/materials') ? 'active' : '' ?>">
          <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/materials">
            <i class="align-middle" data-feather="layers"></i>
            <span class="align-middle sidebar-text">Bahan (Materials)</span>
          </a>
        </li>

        <li class="sidebar-item <?= isActive('/admin/laminations') ? 'active' : '' ?>">
          <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/laminations">
            <i class="align-middle" data-feather="droplet"></i>
            <span class="align-middle sidebar-text">Laminasi</span>
          </a>
        </li>

        <li class="sidebar-item <?= isActive('/admin/category-options') ? 'active' : '' ?>">
          <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/category-options">
            <i class="align-middle" data-feather="sliders"></i>
            <span class="align-middle sidebar-text">Mapping Opsi</span>
          </a>
        </li>

        <li class="sidebar-item <?= isActive('/admin/tier-pricing') ? 'active' : '' ?>">
          <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/tier-pricing">
            <i class="align-middle" data-feather="list"></i>
            <span class="align-middle sidebar-text">Tier Pricing</span>
          </a>
        </li>
      <?php endif; ?>

      <!-- KONTEN WEBSITE -->
      <?php if ($isSuper || $isAdmin): ?>
        <li class="sidebar-header">Konten Website</li>

        <?php if ($isSuper): ?>
          <li class="sidebar-item <?= isActive('/admin/home') ? 'active' : '' ?>">
            <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/home">
              <i class="align-middle" data-feather="layout"></i>
              <span class="align-middle sidebar-text">Home Page</span>
            </a>
          </li>

          <li
            class="sidebar-item <?= (isActive('/admin/our-home/stores') || isActive('/admin/our-home/gallery')) ? 'active' : '' ?>">
            <a data-bs-target="#ourHomeSubmenu" data-bs-toggle="collapse"
              class="sidebar-link <?= (isActive('/admin/our-home/stores') || isActive('/admin/our-home/gallery')) ? '' : 'collapsed' ?>">
              <i class="align-middle" data-feather="map"></i>
              <span class="align-middle sidebar-text">Our Home</span>
            </a>
            <ul id="ourHomeSubmenu"
              class="sidebar-dropdown list-unstyled collapse <?= (isActive('/admin/our-home/stores') || isActive('/admin/our-home/gallery')) ? 'show' : '' ?>"
              data-bs-parent="#sidebar">
              <li class="sidebar-item <?= isActive('/admin/our-home/stores') ? 'active' : '' ?>">
                <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/our-home/stores">
                  <i class="align-middle" data-feather="map-pin"></i>
                  <span class="sidebar-text">Stores</span>
                </a>
              </li>
              <li class="sidebar-item <?= isActive('/admin/our-home/gallery') ? 'active' : '' ?>">
                <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/our-home/gallery">
                  <i class="align-middle" data-feather="image"></i>
                  <span class="sidebar-text">Gallery</span>
                </a>
              </li>
              <li class="sidebar-item <?= isActive('/admin/our-home/content') ? 'active' : '' ?>">
                <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/our-home/content">
                  <i class="align-middle" data-feather="edit"></i>
                  <span class="sidebar-text">Edit Content</span>
                </a>
              </li>
            </ul>
          </li>

          <li class="sidebar-item <?= isActive('/admin/testimonials') ? 'active' : '' ?>">
            <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/testimonials">
              <i class="align-middle" data-feather="message-circle"></i>
              <span class="align-middle sidebar-text">Testimonials</span>
            </a>
          </li>
        <?php endif; ?>

        <!-- Blog accessible by Admin & Super -->
        <li class="sidebar-item <?= isActive('/admin/blog') ? 'active' : '' ?>">
          <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/blog">
            <i class="align-middle" data-feather="file-text"></i>
            <span class="align-middle sidebar-text">Blog / Artikel</span>
          </a>
        </li>

        <?php if ($isSuper): ?>
          <li class="sidebar-item <?= isActive('/admin/footer') ? 'active' : '' ?>">
            <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/footer">
              <i class="align-middle" data-feather="columns"></i>
              <span class="align-middle sidebar-text">Footer</span>
            </a>
          </li>
        <?php endif; ?>

        <!-- KOMUNIKASI -->
        <li class="sidebar-header">Komunikasi</li>

        <li class="sidebar-item <?= isActive('/admin/contact-messages') ? 'active' : '' ?>">
          <a class="sidebar-link d-flex align-items-center justify-content-between"
            href="<?= $baseUrl; ?>/admin/contact-messages">
            <span class="d-flex align-items-center gap-2">
              <i class="align-middle" data-feather="mail"></i>
              <span class="align-middle sidebar-text">Messages</span>
            </span>
            <?php if ($unreadCount > 0): ?>
              <span class="badge bg-danger" style="font-size:.7rem;"><?= (int) $unreadCount ?></span>
            <?php endif; ?>
          </a>
        </li>
      <?php endif; ?>

      <?php if ($isSuper): ?>
        <!-- SYSTEM -->
        <li class="sidebar-header">System</li>

        <li class="sidebar-item <?= isActive('/admin/settings') ? 'active' : '' ?>">
          <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/settings">
            <i class="align-middle" data-feather="settings"></i>
            <span class="align-middle sidebar-text">Pengaturan Umum</span>
          </a>
        </li>

        <li class="sidebar-item <?= isActive('/admin/users') ? 'active' : '' ?>">
          <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/users">
            <i class="align-middle" data-feather="users"></i>
            <span class="align-middle sidebar-text">Pengguna (Users)</span>
          </a>
        </li>

        <li class="sidebar-item <?= isActive('/admin/system-logs') ? 'active' : '' ?>">
          <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/system-logs">
            <i class="align-middle" data-feather="activity"></i>
            <span class="align-middle sidebar-text">System Logs</span>
          </a>
        </li>
      <?php endif; ?>

      <!-- LOGOUT -->
      <li class="sidebar-item mt-3">
        <a class="sidebar-link" href="<?= $baseUrl; ?>/admin/logout" onclick="return confirm('Yakin ingin logout?');">
          <i class="align-middle" data-feather="log-out"></i>
          <span class="align-middle sidebar-text">Logout</span>
        </a>
      </li>

    </ul>
  </div>
</nav>