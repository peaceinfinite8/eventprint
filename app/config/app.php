<?php
// app/config/app.php - PRODUCTION VERSION
// Auto-detect environment
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isLocal = in_array($host, ['localhost', '127.0.0.1']) || strpos($host, '192.168.') === 0;

// Auto-detect Base URL (Works for any subfolder)
$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = rtrim($scheme . '://' . $host . $scriptDir, '/\\');

if (!$isLocal) {
    // Force specific URL for production if easier/safer, or keep dynamic attempt
    $baseUrl = 'https://infopeaceinfinite.id';
}

return [
    'name' => 'EventPrint',
    'base_url' => $baseUrl,
    'env' => $isLocal ? 'local' : 'production',
    'debug' => $isLocal,  // Auto enable debug on local

    // Asset versioning for cache busting
    'ASSET_VERSION' => time(),  // Auto-update on deploy

    // UI Enhancements enabled
    'EP_UI_ENHANCED' => true,
];
