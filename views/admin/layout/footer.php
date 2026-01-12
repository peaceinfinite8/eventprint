<?php
// views/admin/layout/footer.php
$baseUrl = $vars['baseUrl'] ?? '/eventprint';
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

<!-- Global Toast Notifications -->
<?php
// Retrieve flash from variables passed by Controller::renderAdmin
$flashData = $vars['flash'] ?? [];
$flashSuccess = $flashData['success'] ?? null;
$flashError = $flashData['error'] ?? null;

// Fallback: Check session directly if not passed in vars
if (!$flashSuccess && !$flashError && isset($_SESSION['flash'])) {
  $flashSuccess = $_SESSION['flash']['success'] ?? null;
  $flashError = $_SESSION['flash']['error'] ?? null;
  unset($_SESSION['flash']);
}
?>
<?php if ($flashSuccess || $flashError): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      });

      <?php if ($flashSuccess): ?>
        Toast.fire({
          icon: 'success',
          title: '<?= addslashes($flashSuccess) ?>'
        });
      <?php endif; ?>

      <?php if ($flashError): ?>
        Toast.fire({
          icon: 'error',
          title: '<?= addslashes($flashError) ?>'
        });
      <?php endif; ?>
    });
  </script>
<?php endif; ?>

</body>

</html>