<?php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$title   = $vars['title']   ?? 'EventPrint';
$page    = $vars['page']    ?? '';
$css     = $vars['css']     ?? [];
if (!is_array($css)) $css = [];

if (empty($view) || !is_file($view)) {
  echo "View frontend tidak ditemukan: " . htmlspecialchars((string)$view);
  exit;
}

require __DIR__ . '/header.php';
?>

<div class="ep-topbar d-none d-lg-block">
  <div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between py-2">
      <div class="d-flex align-items-center gap-3 text-white-50 small">
        <span class="d-inline-flex align-items-center gap-2"><i class="bi bi-whatsapp"></i> CS</span>
        <span class="d-inline-flex align-items-center gap-2"><i class="bi bi-chevron-left"></i> Cetak online terbesar <i class="bi bi-chevron-right"></i></span>
      </div>
      <div class="d-flex align-items-center gap-3 text-white-50 small">
        <span class="d-inline-flex align-items-center gap-2"><i class="bi bi-geo-alt"></i> Order tracking</span>
        <span class="d-inline-flex align-items-center gap-2"><span class="ep-flag-id"></span> Ind / Rp</span>
      </div>
    </div>
  </div>
</div>

<?php
require __DIR__ . '/navbar.php';
require $view;
require __DIR__ . '/footer.php';
require __DIR__ . '/scripts.php';
