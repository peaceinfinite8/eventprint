<?php
$baseUrl = $baseUrl ?? '/eventprint';
$sections = $sections ?? [];
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">Blog Master Data</h1>
    <p class="text-muted small mb-0">Overview dan pengaturan artikel blog</p>
  </div>
</div>

<div class="dash-container-card fade-in delay-1">
  <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-layer-group me-2"></i>Sections Overview</h5>
  </div>

  <div class="p-0">
    <?php if (!empty($sections)): ?>
      <div class="table-responsive">
        <table class="table table-custom table-striped align-middle mb-0">
          <thead>
            <tr>
              <th class="ps-4" style="width: 250px;">Section Name</th>
              <th>Stats Summary</th>
              <th>Latest Article</th>
              <th class="text-end pe-4" style="width: 150px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sections as $s): ?>
              <tr>
                <td class="ps-4">
                  <div class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($s['name']); ?></div>
                  <div class="small text-muted"><?php echo htmlspecialchars($s['description']); ?></div>
                </td>

                <td>
                  <?php
                  $stats = $s['stats'] ?? [];
                  $latest = $s['latest'] ?? [];
                  ?>
                  <div class="d-flex gap-3 small">
                    <div class="text-center px-2 py-1 bg-light rounded border">
                      <div class="fw-bold text-dark"><?php echo (int) ($stats['total'] ?? 0); ?></div>
                      <div class="text-muted" style="font-size: 0.75rem;">TOTAL</div>
                    </div>
                    <div class="text-center px-2 py-1 bg-success-subtle rounded border border-success-subtle">
                      <div class="fw-bold text-success"><?php echo (int) ($stats['published'] ?? 0); ?></div>
                      <div class="text-success-emphasis" style="font-size: 0.75rem;">PUBLISHED</div>
                    </div>
                    <div class="text-center px-2 py-1 bg-warning-subtle rounded border border-warning-subtle">
                      <div class="fw-bold text-warning-emphasis"><?php echo (int) ($stats['draft'] ?? 0); ?></div>
                      <div class="text-warning-emphasis" style="font-size: 0.75rem;">DRAFT</div>
                    </div>
                  </div>
                </td>

                <td>
                  <?php if (!empty($latest)): ?>
                    <ul class="list-unstyled mb-0 small">
                      <?php foreach ($latest as $p): ?>
                        <li class="mb-1 text-truncate" style="max-width: 250px;">
                          <i class="fas fa-file-alt text-primary me-1 opacity-50"></i>
                          <span class="fw-medium"><?php echo htmlspecialchars($p['title']); ?></span>
                          <span class="text-muted ms-1" style="font-size: 0.75rem;">
                            <?php echo date('d M', strtotime($p['published_at'] ?: $p['created_at'])); ?>
                          </span>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php else: ?>
                    <span class="badge bg-light text-muted border">No articles yet</span>
                  <?php endif; ?>
                </td>

                <td class="text-end pe-4">
                  <a href="<?php echo $s['manage_url']; ?>" class="btn btn-sm btn-outline-primary shadow-sm">
                    <i class="fas fa-cog me-1"></i> Manage
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="text-center py-5">
        <i class="fas fa-folder-open fa-3x text-muted opacity-25 mb-3"></i>
        <p class="text-muted mb-0">Belum ada section yang dikonfigurasi.</p>
      </div>
    <?php endif; ?>
  </div>
</div>