<?php
// app/helpers/view.php - View helper functions

if (!function_exists('assetUrl')) {
    /**
     * Generate asset URL with cache busting
     */
    function assetUrl(string $path): string
    {
        global $config;
        $baseUrl = rtrim($config['base_url'] ?? '/eventprint/public', '/');
        $path = ltrim($path, '/');
        $fullPath = $baseUrl . '/assets/' . $path;

        // Add cache busting for JS/CSS files
        if (preg_match('/\.(js|css)$/', $path)) {
            $fullPath .= '?v=' . date('Ymd_His'); // Added seconds for immediate refresh
        }

        return $fullPath;
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML entities
     */
    function e($value): string
    {
        if ($value === null)
            return '';
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('baseUrl')) {
    /**
     * Generate base URL
     */
    function baseUrl(string $path = ''): string
    {
        global $config;
        $base = rtrim($config['base_url'] ?? '/eventprint/public', '/');
        if (empty($path))
            return $base;
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('uploadUrl')) {
    /**
     * Generate upload URL  
     * Handles paths that may or may not already include 'uploads/' prefix
     */
    function uploadUrl(string $path): string
    {
        global $config;
        $baseUrl = rtrim($config['base_url'] ?? '/eventprint/public', '/');
        $path = ltrim($path, '/');

        // If path already starts with 'uploads/', don't add it again
        if (str_starts_with($path, 'uploads/')) {
            return $baseUrl . '/' . $path;
        }

        return $baseUrl . '/uploads/' . $path;
    }
}

if (!function_exists('currentPath')) {
    /**
     * Get current URL path
     */
    function currentPath(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }
}

if (!function_exists('isActive')) {
    /**
     * Check if current path matches given path
     */
    function isActive(string $path): bool
    {
        $current = currentPath();
        return str_starts_with($current, $path);
    }
}

if (!function_exists('normalizeWhatsApp')) {
    /**
     * Normalize WhatsApp number
     */
    function normalizeWhatsApp(string $number): string
    {
        // Remove all non-numeric characters
        $clean = preg_replace('/[^0-9]/', '', $number);

        // If starts with 0, replace with 62
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        }

        // If doesn't start with 62, prepend it
        if (!str_starts_with($clean, '62')) {
            $clean = '62' . $clean;
        }

        return $clean;
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format number as Indonesian Rupiah
     * @param float|int $amount Amount to format
     * @param bool $showSymbol Whether to show Rp symbol
     * @return string Formatted currency string
     */
    function format_rupiah($amount, bool $showSymbol = true): string
    {
        $formatted = number_format((float) $amount, 0, ',', '.');
        return $showSymbol ? "Rp {$formatted}" : $formatted;
    }
}
