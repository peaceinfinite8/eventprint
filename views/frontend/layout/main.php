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
  <title><?= e($title ?? 'EventPrint') ?> | EventPrint</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="<?= assetUrl('frontend/images/favicon.png') ?>">

  <!-- Stylesheet -->
  <link rel="stylesheet" href="<?= assetUrl('frontend/css/main.css') ?>">

  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Additional CSS -->
  <?php if (isset($additionalCss)): ?>
    <?php foreach ($additionalCss as $css): ?>
      <link rel="stylesheet" href="<?= assetUrl($css) ?>">
    <?php endforeach; ?>
  <?php endif; ?>
</head>

<body>
  <!-- Navbar -->
  <?php include __DIR__ . '/../partials/navbar.php'; ?>

  <!-- Main Content -->
  <main class="main-content">
    <?php
    // Load the specific page content
    if (isset($__viewPath) && file_exists($__viewPath)) {
      require $__viewPath;
    }
    ?>
  </main>

  <!-- Footer -->
  <?php include __DIR__ . '/../partials/footer.php'; ?>

  <!-- Core Scripts -->
  <script src="<?= assetUrl('frontend/js/utils.js') ?>"></script>
  <script src="<?= assetUrl('frontend/js/app.js') ?>"></script>

  <!-- Additional JS -->
  <?php if (isset($additionalJs)): ?>
    <?php foreach ($additionalJs as $js): ?>
      <script src="<?= assetUrl($js) ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
</body>

</html>