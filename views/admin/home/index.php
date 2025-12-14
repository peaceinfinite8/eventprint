<?php
// views/admin/home/index.php

$baseUrl  = $baseUrl ?? '/eventprint/public';
$sections = $sections ?? [];
$hero     = $hero ?? [
    'title'        => '',
    'subtitle'     => '',
    'button_text'  => '',
    'button_link'  => '',
];


$isSuper = Auth::isSuperAdmin();
?>

<h1 class="h3 mb-3">Home – Master Data</h1>

<div class="card mb-3">
  <div class="card-body">
    <p class="mb-0 text-muted">
      Halaman ini merangkum semua konten utama yang tampil di halaman <strong>Home</strong> website,
      dimulai dari bagian <strong>Hero</strong>.
    </p>
  </div>
</div>

<div class="card mb-4">
  <div class="card-body">
    <?php if (!empty($sections)): ?>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
          <tr>
            <th style="width: 220px;">Section</th>
            <th>Deskripsi</th>
            <th>Ringkasan</th>
            <th class="text-end" style="width: 120px;">Aksi</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($sections as $section): ?>
            <tr>
              <td>
                <strong><?php echo htmlspecialchars($section['name'] ?? ''); ?></strong>
              </td>
              <td class="small text-muted">
                <?php echo htmlspecialchars($section['description'] ?? ''); ?>
              </td>
              <td class="small">
                <?php if (($section['key'] ?? '') === 'hero'): ?>
                  <div class="mb-1">
                    <strong>Judul:</strong>
                    <?php echo !empty($hero['title'])
                      ? htmlspecialchars($hero['title'])
                      : '<span class="text-muted">(Belum diisi)</span>'; ?>
                  </div>
                  <div class="mb-1">
                    <strong>Subjudul:</strong>
                    <?php echo !empty($hero['subtitle'])
                      ? nl2br(htmlspecialchars($hero['subtitle']))
                      : '<span class="text-muted">(Belum diisi)</span>'; ?>
                  </div>
                  <div>
                    <strong>CTA:</strong>
                    <?php echo !empty($hero['button_text'])
                      ? htmlspecialchars($hero['button_text'])
                      : '<span class="text-muted">(Belum diisi)</span>'; ?>
                  </div>
                <?php else: ?>
                  <span class="text-muted">(Belum ada ringkasan khusus)</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <?php if ($isSuper): ?>
                  <a href="<?php echo htmlspecialchars($section['manage_url'] ?? '#'); ?>"
                    class="btn btn-sm btn-primary">
                    Kelola
                  </a>
                <?php else: ?>
                  <button type="button" class="btn btn-sm btn-secondary" disabled
                          title="Hanya super admin yang bisa mengedit Home">
                    View Only
                  </button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="mb-0 text-muted">
        Belum ada section yang dikonfigurasi untuk Home.
      </p>
    <?php endif; ?>
  </div>
</div>
