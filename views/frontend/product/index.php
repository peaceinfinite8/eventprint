<?php
$vars = $vars ?? [];
$vars['title']   = $vars['title'] ?? 'Produk - EventPrint';
$vars['page']    = 'products';
$vars['baseUrl'] = $vars['baseUrl'] ?? '/eventprint/public';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($vars['title']) ?></title>
  <link rel="stylesheet" href="<?= rtrim($vars['baseUrl'],'/') ?>/assets/frontend/css/main.css">
</head>
<body>

<div class="container py-4">
  <h1 id="pageTitle" class="mb-3">Product</h1>

  <div class="row">
    <div class="col-lg-3">
      <ul id="categorySidebar" class="list-unstyled"></ul>
    </div>
    <div class="col-lg-9">
      <div id="productGrid" class="grid grid-3"></div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layout/script.php'; ?>
