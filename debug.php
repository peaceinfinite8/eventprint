<?php
/**
 * DEBUG SCRIPT - Test Routing di Hostinger
 * Upload ke: public_html/debug.php
 * Akses: https://infopeaceinfinite.id/debug.php
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>🔍 EventPrint Debug Info</h1>";
echo "<style>body{font-family:monospace;background:#1e1e1e;color:#fff;padding:20px} h2{color:#4af;border-bottom:1px solid #4af;padding-bottom:5px} pre{background:#2d2d2d;padding:10px;border-left:3px solid #4af;overflow-x:auto} .ok{color:#0f0} .error{color:#f00} .warning{color:#fa0}</style>";

// ===== 1. ENVIRONMENT INFO =====
echo "<h2>1️⃣ Environment</h2><pre>";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "HTTP Host: " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "\n";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "\n";
echo "</pre>";

// ===== 2. FILE STRUCTURE =====
echo "<h2>2️⃣ File Structure Check</h2><pre>";
$baseDir = dirname(__DIR__);

$criticalFiles = [
    'app/config/app.php',
    'app/core/route.php',
    'app/core/controller.php',
    'routes/web.php',
    'routes/admin.php',
    'public/index.php',
    'public/.htaccess',
];

foreach ($criticalFiles as $file) {
    $path = $baseDir . '/' . $file;
    if (file_exists($path)) {
        echo "<span class='ok'>✅</span> $file (exists)\n";
    } else {
        echo "<span class='error'>❌</span> $file (MISSING!)\n";
    }
}
echo "</pre>";

// ===== 3. APP CONFIG =====
echo "<h2>3️⃣ App Config</h2><pre>";
$appConfigPath = $baseDir . '/app/config/app.php';
if (file_exists($appConfigPath)) {
    $config = require $appConfigPath;
    echo "Base URL: " . ($config['base_url'] ?? 'NOT SET') . "\n";
    echo "Environment: " . ($config['env'] ?? 'NOT SET') . "\n";
    echo "Debug: " . (($config['debug'] ?? false) ? 'true' : 'false') . "\n";

    if (isset($config['base_url'])) {
        if (strpos($config['base_url'], 'localhost') !== false) {
            echo "<span class='ok'>✅ Localhost environment detected</span>\n";
        } elseif (strpos($config['base_url'], 'infopeaceinfinite.id') !== false) {
            echo "<span class='ok'>✅ base_url correct for production</span>\n";
        }
    }
} else {
    echo "<span class='error'>❌ Config file not found!</span>\n";
}
echo "</pre>";

// ===== 4. MOD_REWRITE CHECK =====
echo "<h2>4️⃣ mod_rewrite Status</h2><pre>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<span class='ok'>✅ mod_rewrite is ENABLED</span>\n";
    } else {
        echo "<span class='error'>❌ mod_rewrite is DISABLED or not detected</span>\n";
    }
} else {
    echo "<span class='warning'>⚠️  Cannot detect (not running Apache or CGI mode)</span>\n";
}
echo "</pre>";

// ===== 5. .HTACCESS CONTENT =====
echo "<h2>5️⃣ .htaccess Content</h2><pre>";
$htaccessPath = __DIR__ . '/.htaccess';
if (file_exists($htaccessPath)) {
    echo htmlspecialchars(file_get_contents($htaccessPath));
} else {
    echo "<span class='error'>❌ .htaccess NOT FOUND!</span>\n";
}
echo "</pre>";

// ===== 6. TEST ROUTING =====
echo "<h2>6️⃣ Test Routing</h2><pre>";
try {
    if (file_exists($baseDir . '/app/core/route.php')) {
        require_once $baseDir . '/app/core/route.php';
        require_once $baseDir . '/app/config/app.php';

        $router = new Router($config ?? []);

        // Load routes
        if (file_exists($baseDir . '/routes/web.php')) {
            require_once $baseDir . '/routes/web.php';
            echo "<span class='ok'>✅ Routes loaded successfully</span>\n";
            echo "Router initialized with config\n";
        } else {
            echo "<span class='error'>❌ routes/web.php not found!</span>\n";
        }
    } else {
        echo "<span class='error'>❌ Router class not found!</span>\n";
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ Error: " . $e->getMessage() . "</span>\n";
}
echo "</pre>";

// ===== 7. DATABASE CONNECTION =====
echo "<h2>7️⃣ Database Connection</h2><pre>";
if (file_exists($baseDir . '/app/config/db.php')) {
    require_once $baseDir . '/app/config/db.php';

    if (isset($host, $user, $pass, $db)) {
        echo "DB Host: " . $host . "\n";
        echo "DB Name: " . $db . "\n";
        echo "DB User: " . $user . "\n";

        try {
            // Check existing connection from include
            if (isset($mysqli) && !$mysqli->connect_error) {
                echo "<span class='ok'>✅ Database connected successfully (via db.php)</span>\n";
                $mysqli->close();
            } else {
                // Try manual connect if needed
                $testDb = new mysqli($host, $user, $pass, $db);
                if ($testDb->connect_error) {
                    echo "<span class='error'>❌ Connection failed: " . $testDb->connect_error . "</span>\n";
                } else {
                    echo "<span class='ok'>✅ Database connected successfully</span>\n";
                    $testDb->close();
                }
            }
        } catch (Exception $e) {
            echo "<span class='error'>❌ Error: " . $e->getMessage() . "</span>\n";
        }
    } else {
        echo "<span class='error'>❌ Database variables (\$host, \$user, etc) not defined in db.php</span>\n";
    }
} else {
    echo "<span class='error'>❌ db.php not found</span>\n";
}
echo "</pre>";

// ===== 8. TEST API ENDPOINT =====
echo "<h2>8️⃣ Direct API Test</h2><pre>";
echo "Try accessing these URLs:\n\n";
echo "1. <a href='/api/home' style='color:#4af' target='_blank'>https://infopeaceinfinite.id/api/home</a>\n";
echo "   Expected: JSON response\n\n";
echo "2. <a href='/products' style='color:#4af' target='_blank'>https://infopeaceinfinite.id/products</a>\n";
echo "   Expected: Products page\n\n";
echo "3. <a href='/' style='color:#4af' target='_blank'>https://infopeaceinfinite.id/</a>\n";
echo "   Expected: Homepage\n";
echo "</pre>";

// ===== 9. RECOMMENDATIONS =====
echo "<h2>9️⃣ Recommendations</h2><pre>";
echo "1. If mod_rewrite is disabled → Contact Hostinger support\n";
echo "2. If .htaccess is missing → Upload from localhost\n";
echo "3. If base_url is localhost → Edit app/config/app.php\n";
echo "4. If routes not loading → Check file permissions (644)\n";
echo "5. Clear browser cache: Ctrl + Shift + R\n";
echo "</pre>";

echo "<hr><p style='color:#888;text-align:center'>Debug completed at " . date('Y-m-d H:i:s') . "</p>";
