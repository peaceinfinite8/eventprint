<?php
// views/frontend/layout/script.php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$pageVar = $vars['page'] ?? ($page ?? 'home');
$dataPreloaded = $vars['data'] ?? null;

$baseUrl = rtrim($baseUrl, '/');
if ($baseUrl === '') $baseUrl = '/eventprint/public';
?>

<script>
  window.EP_BASE_URL = <?= json_encode($baseUrl, JSON_UNESCAPED_SLASHES) ?>;
  window.EP_PAGE     = <?= json_encode($pageVar, JSON_UNESCAPED_SLASHES) ?>;
  window.EP_DATA     = window.EP_DATA || {};
  window.EP_DATA_PRELOADED = <?= json_encode($dataPreloaded, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?= $baseUrl ?>/assets/frontend/js/utils.js"></script>
<script src="<?= $baseUrl ?>/assets/frontend/js/app.js"></script>

<?php if ($pageVar === 'home'): ?>
  <script src="<?= $baseUrl ?>/assets/frontend/js/render/renderHome.js"></script>
<?php elseif ($pageVar === 'products'): ?>
  <script src="<?= $baseUrl ?>/assets/frontend/js/render/renderProducts.js"></script>
<?php elseif ($pageVar === 'our_home'): ?>
  <script src="<?= $baseUrl ?>/assets/frontend/js/render/renderOurHome.js"></script>
<?php elseif ($pageVar === 'blog'): ?>
  <script src="<?= $baseUrl ?>/assets/frontend/js/render/renderBlog.js"></script>
<?php elseif ($pageVar === 'contact'): ?>
  <script src="<?= $baseUrl ?>/assets/frontend/js/render/renderContact.js"></script>
<?php elseif ($pageVar === 'product_detail'): ?>
  <script src="<?= $baseUrl ?>/assets/frontend/js/render/renderProductDetail.js"></script>
<?php endif; ?>

</body>
</html>
