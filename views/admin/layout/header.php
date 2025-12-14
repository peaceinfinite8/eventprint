<?php
// views/admin/layout/header.php

$title   = $vars['title']   ?? 'Admin Panel';
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';

// ambil settings dari DB
require_once __DIR__ . '/../../../app/models/Setting.php';
$settingModel   = new Setting();
$globalSettings = $settingModel->getAll();

$siteName = $globalSettings['site_name'] ?? 'EventPrint';
$siteLogo = !empty($globalSettings['logo']) ? $baseUrl . '/' . $globalSettings['logo'] : null;

$pageTitle = trim($title);
if ($siteName) $pageTitle .= ' - ' . $siteName . ' Admin';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- AdminKit -->
  <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/admin/css/app.css">

  <!-- Bootstrap Icons (buat icon percent dll) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Custom Admin -->
  <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/admin/css/custom.css">
</head>
<body>
<div class="wrapper">
