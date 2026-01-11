<h1 class="h3 mb-3">Produk – Master Data</h1>

<div class="card mb-3">
  <div class="card-body">
    <p class="mb-0 text-muted">Kelola semua produk dan kategori dalam satu tempat.</p>
  </div>
</div>

<div class="card">
  <div class="card-body">

    <table class="table align-middle">
      <thead>
      <tr>
        <th>Section</th>
        <th>Deskripsi</th>
        <th>Ringkasan</th>
        <th class="text-end">Aksi</th>
      </tr>
      </thead>

      <tbody>
      <?php foreach ($sections as $s): ?>
        <tr>
          <td><strong><?= $s['name']; ?></strong></td>
          <td class="small text-muted"><?= $s['description']; ?></td>

          <td class="small">
            <strong>Total:</strong> <?= $s['stats']['total']; ?>

            <?php if (!empty($s['latest'])): ?>
              <br>
              <small class="text-muted">Terbaru:</small>
              <ul class="ps-3">
                <?php foreach ($s['latest'] as $p): ?>
                  <li><?= htmlspecialchars($p['name']); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </td>

          <td class="text-end">
            <a href="<?= $s['manage_url']; ?>" class="btn btn-sm btn-primary">Kelola</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>

    </table>

  </div>
</div>
