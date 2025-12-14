<?php
// views/admin/layout/main.php

$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$flash   = $vars['flash']   ?? ['success' => null, 'error' => null];
$title   = $vars['title']   ?? 'Admin Panel';

require_once __DIR__ . '/../../../app/helpers/Security.php';
$csrfToken = Security::csrfToken();

// extract variable lain biar view bisa pakai langsung
foreach ($vars as $k => $v) {
  if (in_array($k, ['baseUrl', 'flash', 'title'], true)) continue;
  $$k = $v;
}

include __DIR__ . '/header.php';
include __DIR__ . '/sidebar.php';
?>

<div class="main">

  <!-- TOPBAR -->
  <nav class="navbar navbar-expand navbar-light navbar-bg">
    <span class="navbar-text ms-2 fw-semibold">
      <?php echo htmlspecialchars($title); ?>
    </span>
    <div class="navbar-collapse collapse">
      <ul class="navbar-nav ms-auto"></ul>
    </div>
  </nav>

  <main class="content">
    <div class="container-fluid p-0">

      <?php if (!empty($flash['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($flash['success']); ?></div>
      <?php endif; ?>

      <?php if (!empty($flash['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($flash['error']); ?></div>
      <?php endif; ?>

      <?php include $view; ?>

    </div>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
