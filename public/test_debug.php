<?php
// public/test_debug.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/index.php';

// Simulate Request (hijack router?)
// No, just calling the controller method manually if possible, 
// OR simpler: use curl to hit the local endpoint?
// I cannot use curl easily.

// I will just instantiate controller and call method.
// Prerequisites are loaded by index.php but index.php runs router->dispatch.
// I can't include index.php because it exits.

// Let's replicate bootstrap.
require_once __DIR__ . '/../vendor/autoload.php';
$appConfig = require __DIR__ . '/../app/config/app.php';
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/core/route.php';
require_once __DIR__ . '/../app/core/controller.php';
require_once __DIR__ . '/../app/helpers/url.php';
require_once __DIR__ . '/../app/helpers/pricing.php';
require_once __DIR__ . '/../app/controllers/ProductPublicController.php';

// Mock $_GET
// I need a slug. I'll pick 'flyer-a5' from the screenshot.
$slug = 'flyer-a5';

echo "Testing Product: $slug\n";

$controller = new ProductPublicController($appConfig);
echo "Controller created.\n";

try {
    $controller->apiDetailBySlug($slug);
    echo "\nSuccess (Exit called internally?)\n";
} catch (Throwable $e) {
    echo "Caught Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
