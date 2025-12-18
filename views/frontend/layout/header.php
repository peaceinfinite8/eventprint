<?php
$baseUrl = rtrim(($vars['baseUrl'] ?? '/eventprint/public'), '/');
$title   = $vars['title'] ?? 'EventPrint';
$page    = $vars['page'] ?? 'home';
$metaDescription = $vars['metaDescription']
  ?? 'EventPrint - Solusi cetak digital berkualitas untuk kebutuhan event dan promosi Anda.';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8') ?>">
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/assets/frontend/css/main.css">
</head>
<body data-page="<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>">
