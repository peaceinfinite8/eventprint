<?php
// views/admin/layout/main.php
// Load config untuk production baseUrl
$configPath = __DIR__ . '/../../../app/config/app.php';
$config = file_exists($configPath) ? require $configPath : [];
$baseUrl = $config['base_url'] ?? 'https://infopeaceinfinite.id';

// Load helpers
require_once __DIR__ . '/../../../app/helpers/url.php';
$flash = $vars['flash'] ?? ['success' => null, 'error' => null];
$title = $vars['title'] ?? 'Admin Panel';

require_once __DIR__ . '/../../../app/helpers/Security.php';
$csrfToken = Security::csrfToken();

// extract variable lain biar view bisa pakai langsung
foreach ($vars as $k => $v) {
  if (in_array($k, ['baseUrl', 'flash', 'title'], true))
    continue;
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

      <?php if (!empty($flash['success']) || !empty($flash['error'])): ?>
        <script>
          document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
              }
            });

            <?php if (!empty($flash['success'])): ?>
              Toast.fire({
                icon: 'success',
                title: '<?php echo htmlspecialchars($flash['success']); ?>'
              });
            <?php endif; ?>

            <?php if (!empty($flash['error'])): ?>
              Toast.fire({
                icon: 'error',
                title: '<?php echo htmlspecialchars($flash['error']); ?>'
              });
            <?php endif; ?>
          });
        </script>
      <?php endif; ?>

      <?php include $__viewPath; ?>

    </div>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>