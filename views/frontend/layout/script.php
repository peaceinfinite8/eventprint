<?php
$baseUrl = rtrim(($vars['baseUrl'] ?? '/eventprint/public'), '/');
$pageVar = (string)($vars['page'] ?? 'home');
$dataPreloaded = $vars['data'] ?? null;

$pageVarNormalized = str_replace('-', '_', $pageVar);

$renderMap = [
  'home'           => '/assets/frontend/js/render/renderHome.js',
  'products'       => '/assets/frontend/js/render/renderProducts.js',
  'our_home'       => '/assets/frontend/js/render/renderOurHome.js',
  'blog'           => '/assets/frontend/js/render/renderBlog.js',
  'contact'        => '/assets/frontend/js/render/renderContact.js',
  'product_detail' => '/assets/frontend/js/render/renderProductDetail.js',
];

$renderer = $renderMap[$pageVarNormalized] ?? null;
?>
<script>
  window.EP_BASE_URL = <?= json_encode($baseUrl, JSON_UNESCAPED_SLASHES) ?>;
  window.EP_PAGE     = <?= json_encode($pageVarNormalized, JSON_UNESCAPED_SLASHES) ?>;
  window.EP_DATA_PRELOADED = <?= json_encode($dataPreloaded, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
</script>

<!-- 1) utils dulu (route/loadData/formatPrice/showLoading) -->
<script defer src="<?= $baseUrl ?>/assets/frontend/js/utils.js"></script>

<!-- 2) renderer dulu supaya initHomePage/initProductsPage tersedia -->
<?php if ($renderer): ?>
  <script defer src="<?= $baseUrl . $renderer ?>"></script>
<?php endif; ?>

<!-- 3) app TERAKHIR karena dia yang memanggil initPage() -->
<script defer src="<?= $baseUrl ?>/assets/frontend/js/app.js"></script>

</body>
</html>
