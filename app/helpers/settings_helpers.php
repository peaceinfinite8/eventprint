<?php
declare(strict_types=1);

/**
 * Build logo url for preview with cache-busting if file exists in document root.
 */
function buildLogoPreviewUrl(string $baseUrl, array $settings): ?string
{
    if (empty($settings['logo'])) return null;

    $logoRel = ltrim((string)$settings['logo'], '/');
    $url = rtrim($baseUrl, '/') . '/' . $logoRel;

    $docRoot = rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''), '/\\');
    if ($docRoot !== '') {
        $abs = $docRoot . '/' . $logoRel;
        if (is_file($abs)) {
            $url .= '?v=' . (string)@filemtime($abs);
        }
    }

    return $url;
}
