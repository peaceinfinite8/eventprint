<?php
// views/admin/dashboard/index.php

$baseUrl = $vars['baseUrl'] ?? '/eventprint';
$stats = $vars['stats'] ?? [
  'products_active' => 0,
  'categories_active' => 0,
  'hero_active' => 0,
  'messages_total' => 0,
  'messages_unread' => 0,
];

$latestProducts = $vars['latestProducts'] ?? [];
$latestMessages = $vars['latestMessages'] ?? [];
$latestLogs = $vars['latestLogs'] ?? [];

// Greetings logic
$hour = (int) date('H');
if ($hour < 12)
  $greeting = "Selamat Pagi";
else if ($hour < 18)
  $greeting = "Selamat Sore";
else
  $greeting = "Selamat Malam";
?>

<div class="welcome-section animate-enter">
  <div class="welcome-content">
    <h1 class="welcome-title"><?= $greeting ?>, Admin! 👋</h1>
    <p class="welcome-subtitle">Here's what's happening with your store today.</p>
  </div>
</div>

<!-- Stats Grid -->
<div class="row g-4 mb-4">
  <!-- Active Products -->
  <div class="col-12 col-md-6 col-xl-3 animate-enter delay-1">
    <div class="dash-card">
      <div class="d-flex justify-content-between align-items-start">
        <div class="stat-icon-wrapper bg-light-primary">
          <i class="fa-solid fa-box-open"></i>
        </div>
        <a href="<?= $baseUrl ?>/admin/products" class="stat-link" title="Manage Products">
          <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
      <div class="stat-value"><?= (int) $stats['products_active'] ?></div>
      <div class="stat-label">Active Products</div>
    </div>
  </div>

  <!-- Active Categories -->
  <div class="col-12 col-md-6 col-xl-3 animate-enter delay-2">
    <div class="dash-card">
      <div class="d-flex justify-content-between align-items-start">
        <div class="stat-icon-wrapper bg-light-info">
          <i class="fa-solid fa-tags"></i>
        </div>
        <a href="<?= $baseUrl ?>/admin/product-categories" class="stat-link" title="Manage Categories">
          <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
      <div class="stat-value"><?= (int) $stats['categories_active'] ?></div>
      <div class="stat-label">Product Categories</div>
    </div>
  </div>

  <!-- Messages -->
  <div class="col-12 col-md-6 col-xl-3 animate-enter delay-3">
    <div class="dash-card">
      <div class="d-flex justify-content-between align-items-start">
        <div class="stat-icon-wrapper bg-light-warning">
          <i class="fa-regular fa-envelope"></i>
        </div>
        <a href="<?= $baseUrl ?>/admin/contact-messages" class="stat-link" title="Read Messages">
          <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
      <div class="stat-value">
        <?= (int) $stats['messages_total'] ?>
        <?php if ((int) $stats['messages_unread'] > 0): ?>
          <span style="font-size:14px; color:var(--dash-danger);">
            (<?= (int) $stats['messages_unread'] ?> New)
          </span>
        <?php endif; ?>
      </div>
      <div class="stat-label">Messages Received</div>
    </div>
  </div>

  <!-- Hero Active -->
  <div class="col-12 col-md-6 col-xl-3 animate-enter delay-4">
    <div class="dash-card">
      <div class="d-flex justify-content-between align-items-start">
        <div class="stat-icon-wrapper bg-light-success">
          <i class="fa-regular fa-image"></i>
        </div>
        <a href="<?= $baseUrl ?>/admin/home" class="stat-link" title="Manage Content">
          <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
      <div class="stat-value"><?= (int) $stats['hero_active'] ?></div>
      <div class="stat-label">Active Sliders</div>
    </div>
  </div>
</div>

<!-- Main Content Grid -->
<div class="row g-4">
  <!-- Recent Products -->
  <div class="col-12 col-xl-8 animate-enter delay-2">
    <div class="dash-container-card">
      <div class="dash-header">
        <h5 class="dash-title">Latest Products</h5>
        <a href="<?= $baseUrl ?>/admin/products" class="btn btn-sm btn-light">View All</a>
      </div>
      <div class="dash-body">
        <?php if (empty($latestProducts)): ?>
          <div class="empty-state">
            <i class="fa-solid fa-box-open empty-icon"></i>
            <p>No products found.</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table-custom">
              <thead>
                <tr>
                  <th>Product Name</th>
                  <th>Created At</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach (array_slice($latestProducts, 0, 5) as $p): ?>
                  <tr>
                    <td>
                      <div class="fw-bold"><?= htmlspecialchars($p['name'] ?? '') ?></div>
                    </td>
                    <td style="color:var(--dash-text-muted);">
                      <?php echo isset($p['created_at']) ? date('M d, Y', strtotime($p['created_at'])) : '-'; ?>
                    </td>
                    <td>
                      <?php $active = ((int) ($p['is_active'] ?? 0) === 1); ?>
                      <span class="dash-badge <?= $active ? 'active' : 'inactive' ?>">
                        <?= $active ? 'Active' : 'Inactive' ?>
                      </span>
                    </td>
                    <td>
                      <a href="<?= $baseUrl ?>/admin/products/edit/<?= (int) ($p['id'] ?? 0) ?>"
                        class="btn btn-sm btn-light">
                        Edit
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Sidebar: Recent Messages & Activity -->
  <div class="col-12 col-xl-4 d-flex flex-column gap-4 animate-enter delay-3">

    <!-- Recent Messages -->
    <div class="dash-container-card">
      <div class="dash-header">
        <h5 class="dash-title">Recent Messages</h5>
        <a href="<?= $baseUrl ?>/admin/contact-messages" class="btn btn-sm btn-light">View All</a>
      </div>
      <div class="dash-body">
        <?php if (empty($latestMessages)): ?>
          <div class="empty-state">
            <i class="fa-regular fa-envelope-open empty-icon"></i>
            <p>No messages yet.</p>
          </div>
        <?php else: ?>
          <div class="msg-list">
            <?php foreach (array_slice($latestMessages, 0, 4) as $m): ?>
              <div class="msg-list-item">
                <div class="user-meta">
                  <span class="user-name"><?= htmlspecialchars(trim(($m['name'] ?? '') ?: 'Guest')) ?></span>
                  <span class="user-email"><?= htmlspecialchars($m['email'] ?? '') ?></span>
                  <div class="mt-1 small text-muted">
                    <?= htmlspecialchars(mb_strimwidth((string) ($m['message'] ?? ''), 0, 50, '…')) ?>
                  </div>
                </div>
                <small class="text-muted" style="white-space:nowrap; font-size:11px;">
                  <?= isset($m['created_at']) ? date('d M', strtotime($m['created_at'])) : '' ?>
                </small>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- System Logs (Mini) -->
    <?php if (!empty($latestLogs)): ?>
      <div class="dash-container-card">
        <div class="dash-header">
          <h5 class="dash-title">System Activity</h5>
        </div>
        <div class="dash-body p-0">
          <div class="table-responsive" style="max-height: 250px; overflow-y:auto;">
            <table class="table-custom">
              <tbody>
                <?php foreach (array_slice($latestLogs, 0, 5) as $log): ?>
                  <tr>
                    <td width="10">
                      <div style="width:8px; height:8px; border-radius:50%; background: #94a3b8;"></div>
                    </td>
                    <td>
                      <div style="font-size:13px; line-height:1.3;">
                        <?= htmlspecialchars(mb_strimwidth($log['message'], 0, 60, '…')) ?>
                      </div>
                      <div style="font-size:11px; color:var(--dash-text-muted); margin-top:2px;">
                        <?= date('H:i', strtotime($log['created_at'])) ?> · <?= htmlspecialchars($log['level']) ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>