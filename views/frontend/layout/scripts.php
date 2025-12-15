<?php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
?>
<script>
  window.EP_BASE_URL = "<?= $baseUrl ?>";
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $baseUrl ?>/assets/frontend/js/main.js"></script>
</body>
</html>
