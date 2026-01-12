<?php
// views/admin/layout/header.php

$title = $vars['title'] ?? 'Admin Panel';
$baseUrl = $vars['baseUrl'] ?? '/eventprint';

// ambil settings dari DB
require_once __DIR__ . '/../../../app/models/Setting.php';
$settingModel = new Setting();
$globalSettings = $settingModel->getAll();

$siteName = $globalSettings['site_name'] ?? 'EventPrint';
$siteLogo = !empty($globalSettings['logo']) ? $baseUrl . '/' . $globalSettings['logo'] : null;

$pageTitle = trim($title);
if ($siteName)
  $pageTitle .= ' - ' . $siteName . ' Admin';
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- AdminKit -->
  <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/admin/css/app.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Bootstrap Icons (buat icon percent dll) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Custom Admin -->
  <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/admin/css/custom.css">

  <!-- Premium Dashboard CSS -->
  <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/admin/css/admin-dashboard.css">

  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div class="wrapper">