<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$sections = $sections ?? [];
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">Our Home – Master Data</h1>
    <p class="text-muted small mb-0">Rangkuman data portfolio yang tampil di Our Home</p>
  </div>
</div>

<div class="dash-container-card fade-in delay-1">
  <div class="table-responsive">
    <?php if (!empty($sections)): ?>
      <table class="table table-custom table-striped align-middle mb-0">
        <thead>
          <tr>
            <th style="width: 220px;">Section</th>
            <th>Deskripsi</th>
            <th>Ringkasan</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sections as $s): ?>
            <tr>
              <td class="fw-bold text-dark"><?php echo htmlspecialchars($s['name']); ?></td>
              <td class="text-muted small">
                <?php echo htmlspecialchars($s['description']); ?>
              </td>
              <td class="small">
                <?php
                $stats = $s['stats'] ?? [];
                $latest = $s['latest'] ?? [];
                $total = (int) ($stats['total'] ?? 0);
                ?>
                <div class="mb-1">
                  <span class="badge bg-light text-dark border">Total: <?php echo $total; ?></span>
                </div>

                <?php if (!empty($latest)): ?>
                  <div class="text-muted mt-2 mb-1">Terbaru:</div>
                  <ul class="mb-0 ps-3 text-muted">
                    <?php foreach ($latest as $item): ?>
                      <li>
                        <span class="fw-medium text-dark"><?php echo htmlspecialchars($item['title']); ?></span>
                        <?php if (!empty($item['client_name'])): ?>
                          – <?php echo htmlspecialchars($item['client_name']); ?>
                        <?php endif; ?>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php else: ?>
                  <span class="text-muted fst-italic">(Belum ada data)</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <a href="<?php echo htmlspecialchars($s['manage_url']); ?>"
                  class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                  Kelola <i class="fas fa-arrow-right ms-1"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="text-center py-5">
        <i class="fas fa-folder-open fa-3x mb-3 text-muted opacity-50"></i>
        <p class="mb-0 text-muted">Belum ada section yang dikonfigurasi.</p>
      </div>
    <?php endif; ?>
  </div>
</div>