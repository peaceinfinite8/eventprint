<?php
$baseUrl  = $baseUrl ?? '/eventprint/public';
$sections = $sections ?? [];
$isSuper  = Auth::isSuperAdmin();
?>

<h1 class="h3 mb-3">Home – Master Data</h1>

<div class="card mb-3">
  <div class="card-body">
    <p class="mb-0 text-muted">
      Halaman ini merangkum semua konten utama yang tampil di halaman <strong>Home</strong>.
    </p>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <?php if (!empty($sections)): ?>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
          <tr>
            <th style="width:220px;">Section</th>
            <th>Deskripsi</th>
            <th class="text-end" style="width:140px;">Aksi</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($sections as $section): ?>
            <tr>
              <td><strong><?= htmlspecialchars($section['name'] ?? '') ?></strong></td>
              <td class="text-muted small"><?= htmlspecialchars($section['description'] ?? '') ?></td>
              <td class="text-end">
                <?php if ($isSuper): ?>
                  <a class="btn btn-sm btn-primary" href="<?= htmlspecialchars($section['manage_url'] ?? '#') ?>">
                    Kelola
                  </a>
                <?php else: ?>
                  <button class="btn btn-sm btn-secondary" disabled>View Only</button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="mb-0 text-muted">Belum ada section.</p>
    <?php endif; ?>
  </div>
</div>
