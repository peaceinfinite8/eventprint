<?php
$baseUrl   = $baseUrl ?? '/eventprint/public';
$csrfToken = $csrfToken ?? Security::csrfToken();

$mode  = $mode ?? 'index';   // index | create | edit
$items = $items ?? [];
$item  = $item  ?? [];

$isForm = in_array($mode, ['create','edit'], true);

if ($isForm) {
  $key = (string)($item['item_key'] ?? '');

  $action = ($mode === 'edit')
    ? $baseUrl . '/admin/home/hero/update/' . urlencode($key)
    : $baseUrl . '/admin/home/hero/store';
}
?>

<?php if (!$isForm): ?>

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0">Hero Slides</h1>
    <a class="btn btn-primary" href="<?= $baseUrl ?>/admin/home/hero/create">+ Tambah Slide</a>
  </div>

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th style="width:220px;">Key</th>
            <th>Judul</th>
            <th style="width:90px;">Posisi</th>
            <th style="width:120px;">Status</th>
            <th class="text-end" style="width:200px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($items)): ?>
          <tr><td colspan="5" class="text-muted">Belum ada slide.</td></tr>
        <?php else: ?>
          <?php foreach ($items as $it): ?>
            <?php
              $k        = (string)($it['item_key'] ?? '');
              $title    = (string)($it['title'] ?? '');
              $position = (int)($it['position'] ?? 1);
              $active   = (int)($it['is_active'] ?? 1) === 1;
            ?>
            <tr>
              <td><code><?= htmlspecialchars($k) ?></code></td>
              <td><?= htmlspecialchars($title) ?></td>
              <td><?= $position ?></td>
              <td><?= $active ? 'Aktif' : 'Nonaktif' ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary"
                   href="<?= $baseUrl ?>/admin/home/hero/edit/<?= urlencode($k) ?>">Edit</a>

                <form method="post"
                      action="<?= $baseUrl ?>/admin/home/hero/delete/<?= urlencode($k) ?>"
                      class="d-inline"
                      onsubmit="return confirm('Hapus slide ini?')">
                  <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                  <button class="btn btn-sm btn-outline-danger">Hapus</button>
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

<?php else: ?>

  <h1 class="h3 mb-3"><?= $mode === 'edit' ? 'Edit Slide' : 'Tambah Slide' ?></h1>

  <div class="card">
    <div class="card-body">
      <form method="post" action="<?= htmlspecialchars($action) ?>">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="mb-3">
          <label class="form-label">Judul</label>
          <input class="form-control" name="title" required
                 value="<?= htmlspecialchars($item['title'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Subtitle</label>
          <textarea class="form-control" name="subtitle" rows="3"><?= htmlspecialchars($item['subtitle'] ?? '') ?></textarea>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Badge</label>
            <input class="form-control" name="badge" value="<?= htmlspecialchars($item['badge'] ?? '') ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Image (path/url)</label>
            <input class="form-control" name="image"
                   value="<?= htmlspecialchars($item['image'] ?? '') ?>"
                   placeholder="/uploads/settings/banner.jpg">
          </div>

          <div class="col-md-6">
            <label class="form-label">CTA Text</label>
            <input class="form-control" name="cta_text" value="<?= htmlspecialchars($item['cta_text'] ?? '') ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">CTA Link</label>
            <input class="form-control" name="cta_link"
                   value="<?= htmlspecialchars($item['cta_link'] ?? '') ?>"
                   placeholder="/contact#order">
          </div>

          <div class="col-md-3">
            <label class="form-label">Position</label>
            <input type="number" class="form-control" name="position" min="1"
                   value="<?= (int)($item['position'] ?? 1) ?>">
          </div>

          <div class="col-md-3 d-flex align-items-end">
            <div class="form-check">
              <?php $checked = ((int)($item['is_active'] ?? 1) === 1) ? 'checked' : ''; ?>
              <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= $checked ?>>
              <label class="form-check-label" for="is_active">Aktif</label>
            </div>
          </div>
        </div>

        <div class="mt-4 d-flex gap-2">
          <button class="btn btn-primary">Simpan</button>
          <a class="btn btn-secondary" href="<?= $baseUrl ?>/admin/home/hero">Kembali</a>
        </div>
      </form>
    </div>
  </div>

<?php endif; ?>
