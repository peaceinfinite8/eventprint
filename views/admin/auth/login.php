<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Login | Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom Login CSS -->
  <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl); ?>/assets/admin/css/admin-login.css">
</head>

<body>

  <div class="login-wrapper">
    <div class="login-card">
      <div class="brand-logo">
        <i class="fa-solid fa-print"></i>
      </div>
      <h1 class="login-title">🔐 Admin Login 🔐</h1>
      <p class="login-subtitle">Sign in to access the admin panel.</p>

      <?php if (!empty($flash['error'])): ?>
        <div class="alert alert-danger">
          <i class="fa-solid fa-circle-exclamation"></i>
          <span><?php echo htmlspecialchars($flash['error']); ?></span>
        </div>
      <?php endif; ?>

      <?php if (!empty($flash['success'])): ?>
        <div class="alert alert-success">
          <i class="fa-solid fa-check-circle"></i>
          <span><?php echo htmlspecialchars($flash['success']); ?></span>
        </div>
      <?php endif; ?>

      <form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/admin/login">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf); ?>">

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <div class="input-group">
            <input class="form-control" type="email" name="email" placeholder="admin@eventprint.com" required autofocus>
            <i class="fa-regular fa-envelope input-icon"></i>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-group">
            <input class="form-control" type="password" name="password" placeholder="••••••••" required>
            <i class="fa-solid fa-lock input-icon"></i>
          </div>
        </div>

        <button class="btn-login" type="submit">
          Sign In <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
        </button>
      </form>
    </div>
  </div>

  <div class="footer-note">
    &copy; <?php echo date('Y'); ?> EventPrint Admin. All rights reserved.
  </div>

</body>

</html>