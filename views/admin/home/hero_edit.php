<?php
// views/admin/home/hero_edit.php

$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$csrfToken = $vars['csrfToken'] ?? '';

$mode = $vars['mode'] ?? 'index';   // index | create | edit
$items = $vars['items'] ?? [];
$item = $vars['item'] ?? [];

$isForm = in_array($mode, ['create', 'edit'], true);

$action = '';
if ($isForm) {
  $key = (string) ($item['item_key'] ?? '');
  $action = ($mode === 'edit')
    ? $baseUrl . '/admin/home/hero/update/' . urlencode($key)
    : $baseUrl . '/admin/home/hero/store';
}

// Helper: normalize image path to web URL
function hero_img_url(string $path): string
{
  $p = trim($path);
  if ($p === '')
    return '';
  // If already absolute URL
  if (preg_match('#^https?://#i', $p))
    return $p;
  // Ensure leading slash so it resolves from domain root
  return '/' . ltrim($p, '/');
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
            <tr>
              <td colspan="5" class="text-muted">Belum ada slide.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($items as $it): ?>
              <?php
              $k = (string) ($it['item_key'] ?? '');
              $title = (string) ($it['title'] ?? '');
              $position = (int) ($it['position'] ?? 1);
              $active = (int) ($it['is_active'] ?? 1) === 1;
              ?>
              <tr>
                <td><code><?= htmlspecialchars($k) ?></code></td>
                <td><?= htmlspecialchars($title) ?></td>
                <td><?= $position ?></td>
                <td><?= $active ? 'Aktif' : 'Nonaktif' ?></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary"
                    href="<?= $baseUrl ?>/admin/home/hero/edit/<?= urlencode($k) ?>">Edit</a>

                  <form method="post" action="<?= $baseUrl ?>/admin/home/hero/delete/<?= urlencode($k) ?>"
                    class="d-inline delete-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <button class="btn btn-sm btn-outline-danger" type="button" onclick="confirmDelete(this)">Hapus</button>
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

  <script>
    function confirmDelete(button) {
      Swal.fire({
        title: 'Hapus Hero Slide?',
        text: "Slide ini akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          button.closest('form').submit();
        }
      });
    }
  </script>

<?php else: ?>

  <h1 class="h3 mb-3"><?= $mode === 'edit' ? 'Edit Slide' : 'Tambah Slide' ?></h1>

  <div class="card">
    <div class="card-body">
      <form method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>"
        class="save-form">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="old_image" value="<?= htmlspecialchars($item['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="mb-3">
          <label class="form-label">Judul</label>
          <input class="form-control" name="title" required value="<?= htmlspecialchars($item['title'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Subtitle</label>
          <textarea class="form-control" name="subtitle"
            rows="3"><?= htmlspecialchars($item['subtitle'] ?? '') ?></textarea>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Badge</label>
            <input class="form-control" name="badge" value="<?= htmlspecialchars($item['badge'] ?? '') ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Image Slide</label>

            <?php
            $curImg = (string) ($item['image'] ?? '');
            $curUrl = hero_img_url($curImg);
            ?>
            <?php if ($curUrl !== ''): ?>
              <div class="mb-2">
                <div class="d-flex align-items-center gap-3">
                  <img src="<?= htmlspecialchars($curUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Preview"
                    style="width:220px;max-width:100%;height:auto;border:1px solid #e5e7eb;border-radius:10px;background:#fff;"
                    onerror="this.style.display='none';">
                  <div class="small text-muted" style="word-break:break-all;">
                    Current: <code><?= htmlspecialchars($curImg) ?></code>
                  </div>
                </div>
              </div>
            <?php else: ?>
              <div class="mb-2 small text-muted">Belum ada gambar.</div>
            <?php endif; ?>

            <input type="file" class="form-control" name="image_file" accept="image/*">
            <div class="form-text">Upload baru untuk mengganti gambar. JPG/PNG/WebP.</div>

            <div class="mt-2">
              <label class="form-label">Atau isi manual (path/url)</label>
              <input class="form-control" name="image" value="<?= htmlspecialchars($item['image'] ?? '') ?>"
                placeholder="uploads/hero/hero_xxx.jpg atau https://...">
              <div class="form-text">Kalau pakai manual, simpan format: <code>uploads/hero/namafile.jpg</code> (tanpa
                domain).</div>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">CTA Text</label>
            <input class="form-control" name="cta_text" value="<?= htmlspecialchars($item['cta_text'] ?? '') ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">CTA Link</label>
            <input class="form-control" name="cta_link" value="<?= htmlspecialchars($item['cta_link'] ?? '') ?>"
              placeholder="/contact#order">
          </div>

          <div class="col-md-3">
            <label class="form-label">Position</label>
            <input type="number" class="form-control" name="position" min="1"
              value="<?= (int) ($item['position'] ?? 1) ?>">
          </div>

          <div class="col-md-3 d-flex align-items-end">
            <div class="form-check">
              <?php $checked = ((int) ($item['is_active'] ?? 1) === 1) ? 'checked' : ''; ?>
              <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= $checked ?>>
              <label class="form-check-label" for="is_active">Aktif</label>
            </div>
          </div>
        </div>

        <div class="mt-4 d-flex gap-2">
          <button class="btn btn-primary" type="submit">Simpan</button>
          <a class="btn btn-secondary" href="<?= $baseUrl ?>/admin/home/hero">Kembali</a>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Save Confirmation with Form Change Detection
    const form = document.querySelector('.save-form');
    let formChanged = false;

    // Track form changes
    form.addEventListener('input', () => {
      formChanged = true;
    });

    form.addEventListener('change', () => {
      formChanged = true;
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      Swal.fire({
        title: 'Simpan Perubahan?',
        text: formChanged ? 'Perubahan pada slide akan disimpan.' : 'Tidak ada perubahan yang terdeteksi.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0ea5e9',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  </script>

<?php endif; ?>