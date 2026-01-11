<!DOCTYPE html>
<html lang="id">

<head>
  <?php
  // Extract all variables from $vars array passed by controller
  if (isset($vars) && is_array($vars)) {
    extract($vars, EXTR_SKIP);
  }
  ?>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description"
    content="<?= e($metaDescription ?? 'EventPrint - Solusi cetak digital berkualitas untuk kebutuhan event dan promosi Anda') ?>">
  <title><?= e($title ?? 'EventPrint') ?> | <?= e($settings['site_name'] ?? 'EventPrint') ?></title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="<?= assetUrl('frontend/images/favicon.png') ?>">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Stylesheet (REFERENCE ORDER: main.css only) -->
  <link rel="stylesheet" href="<?= assetUrl('frontend/css/main.css') ?>?v=<?= time() ?>">
  <link rel="stylesheet" href="<?= assetUrl('frontend/css/animations.css') ?>">
  <link rel="stylesheet" href="<?= assetUrl('frontend/css/responsive.css') ?>">
  <link rel="stylesheet" href="<?= assetUrl('frontend/css/floating-elements.css') ?>">
  <link rel="stylesheet" href="<?= assetUrl('frontend/css/card-enhancements.css') ?>">
  <link rel="stylesheet" href="<?= assetUrl('frontend/css/mobile-menu-fix.css') ?>">
  <link rel="stylesheet" href="<?= assetUrl('frontend/css/hero-3banner.css') ?>">
  <link rel="stylesheet" href="<?= assetUrl('frontend/css/social-icons-animated.css') ?>">

  <!-- Page-specific inline styles will be in individual view files -->
</head>

<body>
  <!-- Base URL for JavaScript (moved to before navbar for early availability) -->
  <script>
    window.EP_BASE_URL = '<?= $baseUrl ?? '' ?>';
    // Enable debug logging with ?debug=1 URL parameter
    window.EP_DEBUG = <?= (($_GET['debug'] ?? '') === '1') ? 'true' : 'false' ?>;
    // Inject settings for JavaScript use
    window.EP_SETTINGS = {
      whatsapp: '<?= e($settings['whatsapp'] ?? '') ?>',
      phone: '<?= e($settings['phone'] ?? '') ?>',
      siteName: '<?= e($settings['site_name'] ?? 'EventPrint') ?>'
    };
  </script>

  <!-- Navbar -->
  <?php include __DIR__ . '/../partials/navbar.php'; ?>

  <!-- Page Content (direct child of body, no wrapper) -->
  <?php
  // Load the specific page content
  if (isset($__viewPath) && file_exists($__viewPath)) {
    require $__viewPath;
  }
  ?>

  <!-- Footer -->
  <?php include __DIR__ . '/../partials/footer.php'; ?>

  <!-- FAB Navigation (Mobile) -->
  <?php include __DIR__ . '/../partials/fab_nav.php'; ?>

  <!-- Core Scripts (REFERENCE ORDER: utils → app → page-specific) -->
  <script src="<?= assetUrl('frontend/js/utils.js') ?>"></script>
  <script src="<?= assetUrl('frontend/js/lib/dataClient.js') ?>"></script>
  <script src="<?= assetUrl('frontend/js/components/navSearch.js') ?>"></script>

  <!-- Additional JS (loaded before app.js if needed for dependencies) -->
  <?php if (isset($additionalJsBefore)): ?>
    <?php foreach ($additionalJsBefore as $js): ?>
      <script src="<?= assetUrl($js) ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>

  <script src="<?= assetUrl('frontend/js/app.js') ?>"></script>

  <!-- Enhancement Scripts (WhatsApp float, scroll to top) -->
  <script src="<?= assetUrl('frontend/js/enhancements.js') ?>"></script>

  <!-- Hero Carousel (Banner 1) -->
  <script src="<?= assetUrl('frontend/js/components/heroCarousel.js') ?>"></script>

  <!-- Additional JS (loaded after app.js - page-specific renderers) -->
  <?php if (isset($additionalJs)): ?>
    <?php foreach ($additionalJs as $js): ?>
      <script src="<?= assetUrl($js) ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
</body>

</html>