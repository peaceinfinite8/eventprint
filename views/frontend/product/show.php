<?php
$id = (int)($productId ?? ($product['id'] ?? 0));
?>



<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($vars['title']) ?></title>

  <!-- CSS cuma 1 -->
  <link rel="stylesheet" href="<?= rtrim($vars['baseUrl'],'/') ?>/assets/frontend/css/main.css">
</head>
<body>

<div class="container my-4">
  <div id="productDetailContent" data-product-id="<?= (int)($productId ?? 0) ?>"></div>

</div>

<div id="footer"></div>

<?php require __DIR__ . '/../layout/script.php'; ?>