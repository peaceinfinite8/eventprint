<?php
$baseUrl = $baseUrl ?? '/eventprint/public'; // sesuaikan
$title   = $title ?? 'EventPrint';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title) ?></title>

  <!-- Bootstrap CSS (WAJIB) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons (WAJIB kalau pakai bi bi-*) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Custom CSS kamu -->
  <link rel="stylesheet" href="<?= $baseUrl ?>/assets/frontend/css/style.css">
</head>
<body>
