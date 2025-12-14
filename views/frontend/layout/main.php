<?php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$title   = $vars['title'] ?? 'EventPrint';

$view = $view ?? null;
if (!$view || !is_file($view)) {
  http_response_code(500);
  exit('View not set / not found: ' . htmlspecialchars((string)$view));
}

require __DIR__ . '/header.php';
require $view;
require __DIR__ . '/scripts.php';
require __DIR__ . '/footer.php';



