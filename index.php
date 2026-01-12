<?php
/* ============================================================================
   Bootstrap / Entry Point
   - Load config + core
   - Register routes
   - Dispatch request
   ========================================================================== */

/* DEBUG (disable on production) */
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

function secure_require(string $path)
{
    if (!file_exists($path)) {
        header('HTTP/1.1 503 Service Unavailable');
        die(
            'CRITICAL ERROR: Missing required file: ' . htmlspecialchars($path) .
            '<br>Current Dir: ' . __DIR__
        );
    }
    return require_once $path;
}

/* ROOT DETECTION */
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    $root = __DIR__;
} elseif (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    $root = dirname(__DIR__);
} else {
    die('CRITICAL: Cannot detect project root. vendor/autoload.php not found.');
}

/* CORE LOAD */
secure_require($root . '/vendor/autoload.php');

$appConfig = secure_require($root . '/app/config/app.php');
$config = $appConfig;

if (file_exists($root . '/app/config/db.php')) {
    require_once $root . '/app/config/db.php';
} else {
    die('CRITICAL ERROR: Missing db.php');
}

secure_require($root . '/app/core/route.php');
secure_require($root . '/app/core/controller.php');
secure_require($root . '/app/core/auth.php');

secure_require($root . '/app/helpers/url.php');
secure_require($root . '/app/helpers/view.php');

$uploadPath = $root . '/app/helpers/Upload.php';
if (!file_exists($uploadPath))
    $uploadPath = $root . '/app/helpers/upload.php';
secure_require($uploadPath);

secure_require($root . '/app/helpers/logging.php');
secure_require($root . '/app/helpers/pricing.php');

/* ROUTER */
$router = new Router($appConfig);

/* ROUTES */
$webRoutes = $root . '/routes/web.php';
if (file_exists($webRoutes)) {
    require_once $webRoutes;
} else {
    die('Missing routes/web.php');
}

$adminRoutes = $root . '/routes/admin.php';
if (file_exists($adminRoutes)) {
    require_once $adminRoutes;
} else {
    die('Missing routes/admin.php');
}

/* DISPATCH */
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'), '/\\');
if ($basePath === '' || $basePath === '/')
    $basePath = '';

if ($basePath !== '' && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

if (strpos($uri, '/index.php') === 0) {
    $uri = substr($uri, 10);
}

$path = '/' . trim($uri, '/');
if ($path === '')
    $path = '/';

$router->dispatch($path, $_SERVER['REQUEST_METHOD'] ?? 'GET');
