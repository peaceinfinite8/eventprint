<?php
// public/index.php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

// Load config
$appConfig = require __DIR__ . '/../app/config/app.php';

require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/core/route.php';
require_once __DIR__ . '/../app/core/controller.php';
require_once __DIR__ . '/../app/core/auth.php';

// Init router
$router = new Router($appConfig);

// Load routes (root/routes)
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/admin.php';

// ===================== PATH PARSING (SAFE) =====================
// Works for both:
// - /eventprint/public/... (subfolder)
// - /... (vhost where public is docroot)

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

// base path = directory where index.php lives in URL space
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'), '/\\');
if ($basePath === '' || $basePath === '/') {
    $basePath = '';
}

// remove basePath prefix ONLY (do not str_replace)
if ($basePath !== '' && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

$path = '/' . trim($uri, '/');
if ($path === '') {
    $path = '/';
}

// Optional debug
// echo "<pre>METHOD: {$_SERVER['REQUEST_METHOD']}\nURI: {$uri}\nBASE: {$basePath}\nPATH: {$path}</pre>"; exit;

// Dispatch
$router->dispatch($path, $_SERVER['REQUEST_METHOD'] ?? 'GET');
