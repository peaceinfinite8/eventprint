<?php
/**
 * Global URL Helper Functions
 * Provides centralized URL generation for base, assets, and uploads
 */

/**
 * Get base URL with optional path
 * @param string $path Optional path to append
 * @return string Full URL
 */
function baseUrl(string $path = ''): string
{
    global $appConfig;
    $base = rtrim($appConfig['base_url'] ?? '/', '/');
    return $base . '/' . ltrim($path, '/');
}

/**
 * Get asset URL (for CSS, JS, images in public/assets/)
 * @param string $path Path relative to assets folder
 * @return string Full asset URL
 */
function assetUrl(string $path = ''): string
{
    return baseUrl('assets/' . ltrim($path, '/'));
}

/**
 * Get upload URL (for user uploaded files in public/uploads/)
 * @param string $path Path relative to uploads folder
 * @return string Full upload URL
 */
function uploadUrl(string $path = ''): string
{
    if (empty($path)) {
        return baseUrl('uploads/');
    }
    // Remove 'uploads/' prefix if it already exists to prevent duplication
    $path = ltrim($path, '/');
    if (strpos($path, 'uploads/') === 0) {
        return baseUrl($path);
    }
    return baseUrl('uploads/' . $path);
}

/**
 * Escape output for HTML (XSS prevention)
 * @param mixed $str String to escape
 * @return string Escaped string
 */
function e($str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Get current route path (for active menu highlighting)
 * @return string Current path without base URL
 */
function currentPath(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

    // Remove base path if exists
    global $appConfig;
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $basePath = rtrim(dirname($scriptName), '/\\');

    if ($basePath !== '' && $basePath !== '/' && strpos($uri, $basePath) === 0) {
        $uri = substr($uri, strlen($basePath));
    }

    return '/' . trim($uri, '/');
}

/**
 * Check if current path matches (for active menu)
 * @param string $path Path to check
 * @return bool True if current path matches or starts with given path
 */
function isActive(string $path): bool
{
    $current = currentPath();
    if ($path === '/') {
        return $current === '/';
    }
    return strpos($current, $path) === 0;
}

/**
 * Format price to Rupiah
 * @param float|int $price Price value
 * @return string Formatted price (e.g., "Rp 12.000")
 */
function formatPrice($price): string
{
    return 'Rp ' . number_format((float) $price, 0, ',', '.');
}

/**
 * Format date to Indonesian format
 * @param string $date Date string
 * @param string $format Output format (default: 'd M Y')
 * @return string Formatted date
 */
function formatDate(string $date, string $format = 'd M Y'): string
{
    $months = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'Mei',
        'Jun',
        'Jul',
        'Agu',
        'Sep',
        'Oct',
        'Nov',
        'Des'
    ];

    $timestamp = strtotime($date);
    if (!$timestamp)
        return $date;

    $formatted = date($format, $timestamp);

    // Replace English month names with Indonesian
    foreach ($months as $idx => $month) {
        $engMonth = date('M', mktime(0, 0, 0, $idx + 1, 1, 2000));
        $formatted = str_replace($engMonth, $month, $formatted);
    }

    return $formatted;
}

/**
 * Generate image thumbnail URL with fallback
 * @param string|null $imagePath Image path from database
 * @param string $fallback Fallback image path
 * @return string Image URL
 */
function imageUrl(?string $imagePath, string $fallback = 'frontend/images/placeholder.jpg'): string
{
    if (empty($imagePath)) {
        return assetUrl($fallback);
    }

    // If path already starts with http, return as-is
    if (preg_match('/^https?:\/\//i', $imagePath)) {
        return $imagePath;
    }

    // If path starts with uploads/, use uploadUrl
    if (strpos($imagePath, 'uploads/') === 0) {
        return baseUrl($imagePath);
    }

    // Otherwise assume it's in uploads
    return uploadUrl($imagePath);
}


/**
 * Normalize WhatsApp number to wa.me URL
 * @param string $input Phone number or URL
 * @return string WhatsApp chat URL or empty string
 */
function normalizeWhatsApp(string $input): string
{
    if (empty($input)) {
        return '';
    }
    
    // If already a URL, use it directly
    if (filter_var($input, FILTER_VALIDATE_URL)) {
        return $input;
    }
    
    // Clean phone number: remove spaces, dashes, parentheses, etc.
    $cleaned = preg_replace('/[^0-9+]/', '', $input);
    
    // Remove leading 0 and add 62 (Indonesia country code)
    if (substr($cleaned, 0, 1) === '0') {
        $cleaned = '62' . substr($cleaned, 1);
    }
    
    // Remove + if present at the start
    $cleaned = ltrim($cleaned, '+');
    
    // Must be numeric after cleaning
    if (!is_numeric($cleaned)) {
        return '';
    }
    
    return 'https://wa.me/' . $cleaned;
}
