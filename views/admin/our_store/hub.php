<?php
$baseUrl  = $baseUrl ?? '/eventprint/public';
$sections = $sections ?? [];
?>

<h1 class="h3 mb-3">Our Home – Master Data</h1>

<div class="card mb-3">
  <div class="card-body">
    <p class="mb-0 text-muted">
      Halaman ini merangkum semua data portfolio yang tampil di Our Home.
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
            <th style="width: 220px;">Section</th>
            <th>Deskripsi</th>
            <th>Ringkasan</th>
            <th class="text-end">Aksi</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($sections as $s): ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($s['name']); ?></strong></td>
              <td class="small text-muted">
                <?php echo htmlspecialchars($s['description']); ?>
              </td>
              <td class="small">
                <?php
                $stats  = $s['stats'] ?? [];
                $latest = $s['latest'] ?? [];
                $total  = (int)($stats['total'] ?? 0);
                ?>
                <div class="mb-1">
                  <strong>Total portfolio:</strong> <?php echo $total; ?>
                </div>

                <?php if (!empty($latest)): ?>
                  <div class="small text-muted">Terbaru:</div>
                  <ul class="mb-0 ps-3">
                    <?php foreach ($latest as $item): ?>
                      <li>
                        <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                        <?php if (!empty($item['client_name'])): ?>
                          <span class="text-muted">
                            – <?php echo htmlspecialchars($item['client_name']); ?>
                          </span>
                        <?php endif; ?>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php else: ?>
                  <span class="text-muted">(Belum ada data)</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <a href="<?php echo htmlspecialchars($s['manage_url']); ?>"
                   class="btn btn-sm btn-primary">
                  Kelola
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="mb-0 text-muted">Belum ada section yang dikonfigurasi.</p>
    <?php endif; ?>
  </div>
</div>
