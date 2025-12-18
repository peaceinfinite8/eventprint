<?php
$baseUrl   = $vars['baseUrl'] ?? '/eventprint/public';
$csrfToken = $vars['csrfToken'] ?? '';
$items     = $vars['items'] ?? [];
?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h3 mb-0">Hero Slides</h1>
  <a class="btn btn-primary" href="<?= $baseUrl ?>/admin/home/hero/create">+ Tambah Slide</a>
</div>

<div class="card">
  <div class="card-body table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th style="width:80px;">ID</th>
          <th>Judul</th>
          <th style="width:90px;">Posisi</th>
          <th style="width:120px;">Status</th>
          <th class="text-end" style="width:220px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($items)): ?>
        <tr><td colspan="5" class="text-muted">Belum ada slide.</td></tr>
      <?php else: ?>
        <?php foreach ($items as $it): ?>
          <?php
            $id       = (int)($it['id'] ?? 0);
            $title    = (string)($it['title'] ?? '');
            $position = (int)($it['position'] ?? 1);
            $active   = (int)($it['is_active'] ?? 1) === 1;
          ?>
          <tr>
            <td><?= $id ?></td>
            <td><?= htmlspecialchars($title) ?></td>
            <td><?= $position ?></td>
            <td><?= $active ? 'Aktif' : 'Nonaktif' ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary"
                 href="<?= $baseUrl ?>/admin/home/hero/edit/<?= $id ?>">Edit</a>

              <form method="post"
                    action="<?= $baseUrl ?>/admin/home/hero/delete/<?= $id ?>"
                    class="d-inline"
                    onsubmit="return confirm('Hapus slide ini?')">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>

    <a href="<?= $baseUrl ?>/admin/home" class="btn btn-secondary mt-2">Kembali</a>
  </div>
</div>
