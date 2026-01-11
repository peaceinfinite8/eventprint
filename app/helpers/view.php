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

        if (empty($path) || $path === '/')
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

if (!function_exists('renderPagination')) {
    /**
     * Render pagination HTML for admin panel
     * @param string $baseUrl Base URL
     * @param string $route Route path (e.g., '/admin/products')
     * @param array $pagination Array with 'total', 'page', 'per_page'
     * @param array $queryParams Additional query parameters (e.g., ['q' => 'search', 'category_id' => '5'])
     * @return string HTML pagination markup
     */
    function renderPagination(string $baseUrl, string $route, array $pagination, array $queryParams = []): string
    {
        $total = (int) ($pagination['total'] ?? 0);
        $page = (int) ($pagination['page'] ?? 1);
        $perPage = (int) ($pagination['per_page'] ?? 10);
        $lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

        // Don't show pagination if only one page
        if ($lastPage <= 1) {
            return '';
        }

        // Calculate from/to
        $from = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
        $to = min($page * $perPage, $total);

        // Build query string helper
        $buildQuery = function ($pageNum) use ($queryParams) {
            $params = array_merge($queryParams, ['page' => $pageNum]);
            $query = [];
            foreach ($params as $k => $v) {
                if ($v === null || $v === '')
                    continue;
                $query[] = urlencode($k) . '=' . urlencode($v);
            }
            return $query ? ('?' . implode('&', $query)) : '';
        };

        $baseUrl = rtrim($baseUrl, '/');
        $route = '/' . trim($route, '/');

        $prev = $page - 1;
        $next = $page + 1;
        $prevQuery = $buildQuery($prev);
        $nextQuery = $buildQuery($next);

        $html = '<div class="d-flex justify-content-between align-items-center p-4 border-top">';
        $html .= '<div class="text-muted small">';
        $html .= 'Showing <strong>' . $from . '-' . $to . '</strong> of <strong>' . $total . '</strong>';
        $html .= '</div>';
        $html .= '<nav aria-label="Pagination">';
        $html .= '<ul class="pagination pagination-sm mb-0">';

        // Previous button
        $html .= '<li class="page-item' . ($page <= 1 ? ' disabled' : '') . '">';
        $html .= '<a class="page-link" href="' . ($page <= 1 ? '#' : $baseUrl . $route . $prevQuery) . '"';
        $html .= ($page <= 1 ? ' tabindex="-1"' : '') . '>';
        $html .= '<i class="fa-solid fa-chevron-left"></i>';
        $html .= '</a>';
        $html .= '</li>';

        // Page numbers
        for ($p = 1; $p <= $lastPage; $p++) {
            $active = ($p === $page);
            $query = $buildQuery($p);
            $html .= '<li class="page-item' . ($active ? ' active' : '') . '">';
            $html .= '<a class="page-link" href="' . $baseUrl . $route . $query . '">';
            $html .= $p;
            $html .= '</a>';
            $html .= '</li>';
        }

        // Next button
        $html .= '<li class="page-item' . ($page >= $lastPage ? ' disabled' : '') . '">';
        $html .= '<a class="page-link" href="' . ($page >= $lastPage ? '#' : $baseUrl . $route . $nextQuery) . '"';
        $html .= ($page >= $lastPage ? ' tabindex="-1"' : '') . '>';
        $html .= '<i class="fa-solid fa-chevron-right"></i>';
        $html .= '</a>';
        $html .= '</li>';

        $html .= '</ul>';
        $html .= '</nav>';
        $html .= '</div>';

        return $html;
    }
}

