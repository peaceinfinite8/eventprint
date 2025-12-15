<?php
$baseUrl  = $vars['baseUrl'] ?? '/eventprint/public';
$title    = $vars['title']   ?? 'EventPrint';
$page     = $vars['page']    ?? '';
$extraCss = $vars['css']     ?? [];
if (!is_array($extraCss)) $extraCss = [];
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title) ?></title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- MAIN CSS -->
  <link rel="stylesheet" href="<?= $baseUrl ?>/assets/frontend/css/style.css">

  <!-- EXTRA CSS PER PAGE -->
  <?php foreach ($extraCss as $file): ?>
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/frontend/css/<?= htmlspecialchars($file) ?>">
  <?php endforeach; ?>
</head>
<body data-page="<?= htmlspecialchars($page) ?>">
