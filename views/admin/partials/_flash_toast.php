<?php
// views/admin/partials/_flash_toast.php

// Helper to get and clear flash message
$success = $_SESSION['flash_success'] ?? null;
$error   = $_SESSION['flash_error'] ?? null;
$warning = $_SESSION['flash_warning'] ?? null;
$info    = $_SESSION['flash_info'] ?? null;

unset($_SESSION['flash_success'], $_SESSION['flash_error'], $_SESSION['flash_warning'], $_SESSION['flash_info']);
?>

<?php if ($success || $error || $warning || $info): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end', // or 'bottom-end'
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    <?php if ($success): ?>
    Toast.fire({
        icon: 'success',
        title: '<?= htmlspecialchars($success, ENT_QUOTES) ?>'
    });
    <?php endif; ?>

    <?php if ($error): ?>
    Toast.fire({
        icon: 'error',
        title: '<?= htmlspecialchars($error, ENT_QUOTES) ?>'
    });
    <?php endif; ?>

    <?php if ($warning): ?>
    Toast.fire({
        icon: 'warning',
        title: '<?= htmlspecialchars($warning, ENT_QUOTES) ?>'
    });
    <?php endif; ?>

    <?php if ($info): ?>
    Toast.fire({
        icon: 'info',
        title: '<?= htmlspecialchars($info, ENT_QUOTES) ?>'
    });
    <?php endif; ?>
});
</script>
<?php endif; ?>
