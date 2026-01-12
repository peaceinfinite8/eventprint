<?php
// Load config for production baseUrl
$configPath = __DIR__ . '/../../../app/config/app.php';
$config = file_exists($configPath) ? require $configPath : [];
$baseUrl = $config['base_url'] ?? 'https://infopeaceinfinite.id';

// Load image helper
require_once __DIR__ . '/../../../app/helpers/url.php';

$csrfToken = $vars['csrfToken'] ?? '';
$items = $vars['items'] ?? [];
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">Hero Slides</h1>
    <p class="text-muted small mb-0">Manage homepage slider visuals</p>
  </div>
  <a class="btn btn-primary shadow-sm" href="<?= $baseUrl ?>/admin/home/hero/create">
    <i class="fas fa-plus me-2"></i>Tambah Slide
  </a>
</div>

<div class="dash-container-card fade-in delay-1">
  <div class="table-responsive">
    <table class="table table-custom table-striped align-middle mb-0">
      <thead>
        <tr>
          <th style="width:60px; text-align:center;">ID</th>
          <th style="width:100px;">Preview</th>
          <th>Judul</th>
          <th style="width:150px;">Lokasi</th>
          <th style="width:100px;">Posisi</th>
          <th style="width:120px;">Status</th>
          <th class="text-end" style="width:150px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($items)): ?>
          <tr>
            <td colspan="7" class="text-center py-5">
              <div class="text-muted">
                <i class="fas fa-images fa-3x mb-3 opacity-50"></i>
                <p class="mb-0">Belum ada slide banner.</p>
              </div>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($items as $it): ?>
            <?php
            $id = (int) ($it['id'] ?? 0);
            $title = (string) ($it['title'] ?? '');
            $position = (int) ($it['position'] ?? 1);
            $active = (int) ($it['is_active'] ?? 1) === 1;
            $active = (int) ($it['is_active'] ?? 1) === 1;
            $image = (string) ($it['image'] ?? '');
            $pageSlug = (string) ($it['page_slug'] ?? 'home');
            $locLabel = 'Main Banner';
            $locClass = 'bg-primary text-white';
            if ($pageSlug === 'home_right_top') { $locLabel = 'Right Top'; $locClass = 'bg-info text-white'; }
            if ($pageSlug === 'home_right_bottom') { $locLabel = 'Right Bottom'; $locClass = 'bg-warning text-dark'; }
            ?>
            <tr>
              <td class="text-center text-muted small"><?= $id ?></td>
              <td>
                <?php if ($image): ?>
                  <img src="<?= safeImageUrl($image, 'product', $baseUrl) ?>" alt="Thumb" class="rounded border"
                    style="width: 60px; height: 36px; object-fit: cover;">
                <?php else: ?>
                  <span class="badge bg-light text-secondary"
                    title="Path: '<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>'">No img</span>
                <?php endif; ?>
              </td>
              <td class="fw-medium text-dark"><?= htmlspecialchars($title) ?></td>
              <td>
                <span class="badge <?= $locClass ?> border shadow-sm"><?= $locLabel ?></span>
              </td>
              <td>
                <span class="badge bg-light text-dark shadow-sm border">
                  <?= $position ?>
                </span>
              </td>
              <td>
                <span class="dash-badge <?= $active ? 'active' : 'inactive' ?>">
                  <?= $active ? 'Aktif' : 'Nonaktif' ?>
                </span>
              </td>
              <td class="text-end">
                <div class="btn-group">
                  <a class="btn btn-icon btn-sm text-primary" href="<?= $baseUrl ?>/admin/home/hero/edit/<?= $id ?>"
                    title="Edit">
                    <i class="fas fa-pencil-alt"></i>
                  </a>

                  <form method="post" action="<?= $baseUrl ?>/admin/home/hero/delete/<?= $id ?>"
                    class="d-inline delete-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <button class="btn btn-icon btn-sm text-danger" type="button" onclick="confirmDelete(this)"
                      title="Hapus">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="mt-4">
  <a href="<?= $baseUrl ?>/admin/home" class="btn btn-link text-decoration-none text-muted">
    <i class="fas fa-arrow-left me-2"></i>Kembali ke Pengaturan Home
  </a>
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