<?php
// clear-cache.php - Place in public/ folder
// Access via: https://infopeaceinfinite.id/clear-cache.php
// Then delete this file after use

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OpCache cleared!<br>";
} else {
    echo "❌ OpCache not available<br>";
}

if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo "✅ APCu cleared!<br>";
} else {
    echo "❌ APCu not available<br>";
}

// Force reload config
echo "✅ Cache clear script executed<br>";
echo "<br>Now delete this file and refresh your admin page.";
