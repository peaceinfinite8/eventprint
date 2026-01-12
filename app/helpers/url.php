<?php
// app/helpers/url.php
// Universal image URL resolver with file existence checking

if (!function_exists('safeImageUrl')) {
    /**
     * Safe image URL resolver - prevents 404s by checking file existence
     * 
     * @param string $path Image path from database (can be relative, absolute, or URL)
     * @param string $type Fallback type: 'product', 'blog', 'store', 'favicon'
     * @param string|null $baseUrl Base URL (auto-detected if null)
     * @return string Valid image URL (200 OK guaranteed)
     */
    function safeImageUrl(string $path, string $type = 'product', ?string $baseUrl = null): string
    {
        // Auto-detect baseUrl if not provided
        if ($baseUrl === null) {
            // Load config to get production baseUrl
            $configPath = __DIR__ . '/../config/app.php';
            if (file_exists($configPath)) {
                $config = require $configPath;
                $baseUrl = rtrim($config['base_url'] ?? 'https://infopeaceinfinite.id', '/');
            } else {
                $baseUrl = 'https://infopeaceinfinite.id';
            }
        }

        // Get base directory (2 levels up from app/helpers/)
        $baseDir = realpath(__DIR__ . '/../..');
        if (!$baseDir) {
            $baseDir = __DIR__ . '/../..';
        }

        // Detect structure: flat (production) vs nested (localhost)
        // Flat: /public_html/ has uploads/ directly
        // Nested: /eventprint/ has public/ subfolder with uploads/ inside
        if (is_dir($baseDir . '/uploads')) {
            // Flat structure (production): baseDir IS the public folder
            $publicPath = $baseDir;
        } elseif (is_dir($baseDir . '/public')) {
            // Nested structure (localhost): has public/ subfolder
            $publicPath = $baseDir . '/public';
        } else {
            // Fallback
            $publicPath = $baseDir;
        }

        // Empty or whitespace-only path -> return placeholder
        $trimmed = trim($path);
        if ($trimmed === '') {
            return getPlaceholderUrl($type, $baseUrl);
        }

        // Full URL (http/https) -> return as-is (assume external/CDN)
        if (preg_match('#^https?://#i', $trimmed)) {
            return $trimmed;
        }

        // Normalize path (remove leading slash, backslashes)
        $normalized = ltrim(str_replace('\\', '/', $trimmed), '/');

        // If just a filename (no directory separator), prefix with uploads/{type}/
        if (strpos($normalized, '/') === false && preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $normalized)) {
            // Map type to upload subdirectory - PHP 7.4 compatible
            $uploadDirs = [
                'our_store' => 'uploads/our_store/',
                'store' => 'uploads/our_store/',
                'blog' => 'uploads/blog/',
                'testimonial' => 'uploads/testimonials/',
            ];
            $uploadDir = $uploadDirs[$type] ?? 'uploads/products/';
            $normalized = $uploadDir . $normalized;
        }

        // Build absolute filesystem path
        $fullPath = $publicPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalized);

        // Check file existence


        if (file_exists($fullPath) && is_file($fullPath)) {
            return $baseUrl . '/' . $normalized;
        }

        // File not found -> return placeholder
        return getPlaceholderUrl($type, $baseUrl);
    }
}

if (!function_exists('getPlaceholderUrl')) {
    /**
     * Get placeholder image URL based on type
     * 
     * @param string $type Placeholder type
     * @param string $baseUrl Base URL
     * @return string Placeholder URL
     */
    function getPlaceholderUrl(string $type, string $baseUrl): string
    {
        $placeholders = array(
            'product' => '/assets/frontend/images/product-placeholder.jpg',
            'blog' => '/assets/frontend/images/blog-placeholder.jpg',
            'store' => '/assets/frontend/images/placeholder-store.png',
            'favicon' => '/assets/frontend/images/favicon.png',
            'logo' => '/assets/frontend/images/placeholder-logo.png',
        );

        $path = isset($placeholders[$type]) ? $placeholders[$type] : $placeholders['product'];
        return $baseUrl . $path;
    }
}
