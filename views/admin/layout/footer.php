<?php
// views/admin/layout/footer.php
$baseUrl  = $vars['baseUrl'] ?? '/eventprint/public';
$siteName = $siteName ?? 'EventPrint';
?>
<footer class="footer">
  <div class="container-fluid">
    <div class="row text-muted">
      <div class="col-6 text-start">
        <p class="mb-0">
          <strong><?php echo htmlspecialchars($siteName); ?></strong> &copy; <?php echo date('Y'); ?>
        </p>
      </div>
      <div class="col-6 text-end">
        <p class="mb-0">Admin Panel</p>
      </div>
    </div>
  </div>
</footer>

</div> <!-- .main -->
</div> <!-- .wrapper -->

<script src="<?php echo $baseUrl; ?>/assets/admin/js/app.js"></script>
</body>
</html>
