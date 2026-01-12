<?php
header('X-EP-View: views/frontend/product/show.php');
echo "<!-- EP_VIEW_USED: " . __FILE__ . " -->";
$id = (int) ($productId ?? ($product['id'] ?? 0));
?>



<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($vars['title']) ?></title>

  <!-- CSS cuma 1 -->
  <link rel="stylesheet" href="<?= rtrim($vars['baseUrl'], '/') ?>/assets/frontend/css/main.css">
</head>

<body>

  <div class="container my-4">
    <div id="productDetailContent" data-product-id="<?= (int) ($productId ?? 0) ?>"></div>

  </div>

  <div id="footer"> <?php
  // MIGRATED TO MAIN.PHP LAYOUT (was using legacy script.php)
  // This file is now deprecated - product detail should use ProductPublicController with main.php layout
  // If this file is still being used, it will now use the main.php layout for consistency
  ?>
    <?php require __DIR__ . '/../layout/main.php'; ?>