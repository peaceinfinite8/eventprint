<?php
// views/admin/dashboard/index.php

$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$stats   = $vars['stats'] ?? [
  'products_active' => 0,
  'categories_active' => 0,
  'hero_active' => 0,
  'messages_total' => 0,
  'messages_unread' => 0,
];

$latestProducts = $vars['latestProducts'] ?? [];
$latestMessages = $vars['latestMessages'] ?? [];
?>

<h1 class="h3 mb-3">Dashboard</h1>

<div class="row g-3 mb-4">
  <div class="col-12 col-md-6 col-xl-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="text-muted small">Produk Aktif</div>
          <div class="h2 mb-0"><?= (int)$stats['products_active'] ?></div>
        </div>
        <div><i class="align-middle" data-feather="box"></i></div>
      </div>
      <div class="card-footer bg-transparent border-0 pt-0">
        <a href="<?= $baseUrl ?>/admin/products" class="small">Kelola Produk →</a>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="text-muted small">Kategori Aktif</div>
          <div class="h2 mb-0"><?= (int)$stats['categories_active'] ?></div>
        </div>
        <div><i class="align-middle" data-feather="tag"></i></div>
      </div>
      <div class="card-footer bg-transparent border-0 pt-0">
        <a href="<?= $baseUrl ?>/admin/product-categories" class="small">Kelola Kategori →</a>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="text-muted small">Hero Aktif</div>
          <div class="h2 mb-0"><?= (int)$stats['hero_active'] ?></div>
        </div>
        <div><i class="align-middle" data-feather="image"></i></div>
      </div>
      <div class="card-footer bg-transparent border-0 pt-0">
        <a href="<?= $baseUrl ?>/admin/home" class="small">Konten Beranda →</a>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="text-muted small">Pesan Masuk</div>
          <div class="h2 mb-0">
            <?= (int)$stats['messages_total'] ?>
            <?php if ((int)$stats['messages_unread'] > 0): ?>
              <span class="badge bg-danger ms-2"><?= (int)$stats['messages_unread'] ?> baru</span>
            <?php endif; ?>
          </div>
        </div>
        <div><i class="align-middle" data-feather="mail"></i></div>
      </div>
      <div class="card-footer bg-transparent border-0 pt-0">
        <a href="<?= $baseUrl ?>/admin/contact" class="small">Lihat Pesan →</a>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-xl-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent">
        <h5 class="card-title mb-0">Produk Terbaru</h5>
      </div>
      <div class="card-body p-0">
        <?php if (empty($latestProducts)): ?>
          <div class="p-3 text-muted">Belum ada data produk.</div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table mb-0 align-middle">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th style="width:120px;">Status</th>
                  <th style="width:140px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($latestProducts as $p): ?>
                  <tr>
                    <td><?= htmlspecialchars($p['name'] ?? '') ?></td>
                    <td>
                      <?php $active = (int)($p['is_active'] ?? 1) === 1; ?>
                      <span class="badge <?= $active ? 'bg-success' : 'bg-secondary' ?>">
                        <?= $active ? 'Aktif' : 'Nonaktif' ?>
                      </span>
                    </td>
                    <td>
                      <a class="btn btn-sm btn-outline-primary"
                         href="<?= $baseUrl ?>/admin/products/edit/<?= (int)($p['id'] ?? 0) ?>">Edit</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
      <div class="card-footer bg-transparent">
        <a href="<?= $baseUrl ?>/admin/products" class="small">Buka semua produk →</a>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Pesan Terbaru</h5>
        <a href="<?= $baseUrl ?>/admin/contact" class="small">Buka semua →</a>
      </div>
      <div class="card-body">
        <?php if (empty($latestMessages)): ?>
          <div class="text-muted">Belum ada pesan masuk.</div>
        <?php else: ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($latestMessages as $m): ?>
              <li class="list-group-item px-0">
                <div class="d-flex justify-content-between">
                  <div>
                    <div class="fw-semibold">
                      <?= htmlspecialchars(trim(($m['name'] ?? '') ?: 'Pengunjung')) ?>
                      <span class="text-muted small">· <?= htmlspecialchars($m['email'] ?? '') ?></span>
                    </div>
                    <div class="text-muted small">
                      <?= htmlspecialchars(mb_strimwidth((string)($m['message'] ?? ''), 0, 90, '…')) ?>
                    </div>
                  </div>
                  <div class="text-muted small">
                    <?= htmlspecialchars((string)($m['created_at'] ?? '')) ?>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
