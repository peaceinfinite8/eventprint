<?php
// layout/main.php
$baseUrl = $vars['baseUrl'] ?? '';
$title   = $vars['title'] ?? 'EventPrint';
$page    = $vars['page'] ?? 'home';
$__viewPath = $vars['__viewPath'] ?? null;

require __DIR__ . '/header.php';
require __DIR__ . '/navbar.php';

if ($__viewPath && file_exists($__viewPath)) {
  require $__viewPath;
} else {
  echo "<div class='container py-5'><div class='alert alert-danger'>Missing view</div></div>";
}

require __DIR__ . '/footer.php';
require __DIR__ . '/script.php';
