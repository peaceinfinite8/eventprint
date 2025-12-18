<?php
/**
 * STEP A: Verify index.php Execution
 * Test if index.php is even being executed
 */

// Force error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html><html><head><title>EventPrint Debug</title></head><body>";
echo "<h1>🔍 EventPrint Diagnostic Check</h1>";
echo "<p><strong>Step A:</strong> index.php IS EXECUTING ✅</p>";
echo "<hr>";

// Check file paths
echo "<h2>File System Check</h2>";
echo "Current file: " . __FILE__ . "<br>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script name: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "<hr>";

// Check if vendor/autoload exists
echo "<h2>Autoload Check</h2>";
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    echo "✅ Autoload exists: $autoloadPath<br>";
    require_once $autoloadPath;
    echo "✅ Autoload loaded successfully<br>";
} else {
    echo "❌ Autoload NOT FOUND: $autoloadPath<br>";
    echo "<strong>ACTION: Run 'composer install'</strong><br>";
}
echo "<hr>";

// Check if config exists
echo "<h2>Config Check</h2>";
$configPath = __DIR__ . '/../app/config/app.php';
if (file_exists($configPath)) {
    echo "✅ Config exists: $configPath<br>";
    try {
        $appConfig = require $configPath;
        echo "✅ Config loaded: <pre>" . print_r($appConfig, true) . "</pre>";
    } catch (Exception $e) {
        echo "❌ Config error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Config NOT FOUND: $configPath<br>";
}
echo "<hr>";

// Check if db config exists
echo "<h2>Database Config Check</h2>";
$dbConfigPath = __DIR__ . '/../app/config/db.php';
if (file_exists($dbConfigPath)) {
    echo "✅ DB Config exists: $dbConfigPath<br>";
    try {
        require_once $dbConfigPath;
        echo "✅ DB Config loaded<br>";

        // Test DB connection
        $testDb = db();
        if ($testDb->ping()) {
            echo "✅ Database connection: SUCCESS<br>";
        } else {
            echo "❌ Database connection: FAILED<br>";
        }
    } catch (Exception $e) {
        echo "❌ DB Config error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ DB Config NOT FOUND: $dbConfigPath<br>";
}
echo "<hr>";

// Check core files
echo "<h2>Core Files Check</h2>";
$coreFiles = [
    'route.php' => __DIR__ . '/../app/core/route.php',
    'controller.php' => __DIR__ . '/../app/core/controller.php',
    'auth.php' => __DIR__ . '/../app/core/auth.php',
    'url.php' => __DIR__ . '/../app/helpers/url.php',
];

foreach ($coreFiles as $name => $path) {
    if (file_exists($path)) {
        echo "✅ $name exists<br>";
        try {
            require_once $path;
            echo "✅ $name loaded successfully<br>";
        } catch (Exception $e) {
            echo "❌ $name load error: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ $name NOT FOUND: $path<br>";
    }
}
echo "<hr>";

// Check route files
echo "<h2>Route Files Check</h2>";
$routeFiles = [
    'web.php' => __DIR__ . '/../routes/web.php',
    'admin.php' => __DIR__ . '/../routes/admin.php',
];

foreach ($routeFiles as $name => $path) {
    if (file_exists($path)) {
        echo "✅ $name exists at: $path<br>";
    } else {
        echo "❌ $name NOT FOUND: $path<br>";
    }
}
echo "<hr>";

// Test Router initialization
echo "<h2>Router Test</h2>";
try {
    if (isset($appConfig)) {
        $router = new Router($appConfig);
        echo "✅ Router initialized successfully<br>";

        // Try to load routes
        require_once __DIR__ . '/../routes/web.php';
        require_once __DIR__ . '/../routes/admin.php';
        echo "✅ Routes loaded successfully<br>";
    } else {
        echo "❌ Cannot test router: appConfig not loaded<br>";
    }
} catch (Exception $e) {
    echo "❌ Router error: " . $e->getMessage() . "<br>";
    echo "Stack trace:<pre>" . $e->getTraceAsString() . "</pre>";
}
echo "<hr>";

// Summary
echo "<h2>🎯 Summary</h2>";
echo "<p>If you see this page, then:</p>";
echo "<ul>";
echo "<li>✅ Apache is serving PHP files correctly</li>";
echo "<li>✅ public/index.php is accessible</li>";
echo "<li>Check errors above to find the actual problem</li>";
echo "</ul>";

echo "<p><strong>Next Step:</strong> Check the actual index.php file for fatal errors</p>";
echo "<a href='/eventprint/public/'>Try Homepage Again</a>";

echo "</body></html>";
