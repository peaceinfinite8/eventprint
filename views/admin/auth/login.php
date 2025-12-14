<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl); ?>/assets/admin/css/app.css">
</head>
<body>
  <div class="container py-5" style="max-width:480px;">
    <h1 class="h3 mb-3">Admin Login</h1>

    <?php if (!empty($flash['error'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($flash['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($flash['success'])): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($flash['success']); ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body">
        <form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/admin/login">
          <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf); ?>">

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" type="password" name="password" required>
          </div>

          <button class="btn btn-primary w-100" type="submit">Login</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
