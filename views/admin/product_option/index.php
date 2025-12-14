<?php
$baseUrl  = $baseUrl ?? '/eventprint/public';
$product  = $product ?? null;
$groups   = $groups ?? [];
$isSuper  = $isSuper ?? false;

if (!$product) {
  echo "<div class='alert alert-danger'>Produk tidak ditemukan.</div>";
  return;
}

// ambil csrf token (ikut pola project kamu)
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<h1 class="h3 mb-3">Opsi Harga Produk</h1>

<div class="card mb-3">
  <div class="card-body d-flex justify-content-between align-items-center">
    <div>
      <div class="fw-semibold"><?= htmlspecialchars($product['name'] ?? '-') ?></div>
      <div class="small text-muted">Kelola opsi harga untuk produk ini.</div>
    </div>
    <div>
      <a href="<?= $baseUrl ?>/admin/products" class="btn btn-sm btn-secondary">← Kembali</a>
    </div>
  </div>
</div>

<?php if ($isSuper): ?>
  <!-- ====== FORM TAMBAH GROUP ====== -->
  <div class="card mb-3">
    <div class="card-header"><strong>Tambah Group Opsi</strong></div>
    <div class="card-body">
      <form method="post" action="<?= $baseUrl ?>/admin/products/<?= (int)$product['id'] ?>/options/group/store" class="row g-2">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <div class="col-md-4">
          <label class="form-label">Nama Group *</label>
          <input type="text" name="name" class="form-control" required placeholder="Contoh: Bahan, Ukuran, Finishing">
        </div>

        <div class="col-md-2">
          <label class="form-label">Tipe Input</label>
          <select name="input_type" class="form-select">
            <option value="checkbox">Checkbox</option>
            <option value="radio">Radio</option>
            <option value="select">Select</option>
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label">Min</label>
          <input type="number" name="min_select" class="form-control" min="0" value="0">
        </div>

        <div class="col-md-2">
          <label class="form-label">Max</label>
          <input type="number" name="max_select" class="form-control" min="0" placeholder="0=unlimited">
        </div>

        <div class="col-md-2">
          <label class="form-label">Urutan</label>
          <input type="number" name="sort_order" class="form-control" min="0" value="0">
        </div>

        <div class="col-12 d-flex gap-3 mt-2">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_required" value="1" id="req">
            <label class="form-check-label" for="req">Wajib diisi</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="act" checked>
            <label class="form-check-label" for="act">Aktif</label>
          </div>
          <button class="btn btn-primary btn-sm ms-auto">Tambah Group</button>
        </div>

      </form>
    </div>
  </div>
<?php else: ?>
  <div class="alert alert-info">
    Kamu bisa melihat opsi harga. Untuk menambah/mengubah opsi, login sebagai <b>super_admin</b>.
  </div>
<?php endif; ?>

<!-- ====== LIST GROUPS + VALUES ====== -->
<div class="card">
  <div class="card-header"><strong>Daftar Group & Opsi</strong></div>
  <div class="card-body">

    <?php if (empty($groups)): ?>
      <p class="mb-0 text-muted">Belum ada group opsi untuk produk ini.</p>
    <?php else: ?>
      <?php foreach ($groups as $g): ?>
        <?php
          $gid    = (int)($g['id'] ?? 0);
          $gname  = (string)($g['name'] ?? '-');
          $values = $g['values'] ?? [];
        ?>

        <div class="border rounded p-3 mb-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-semibold"><?= htmlspecialchars($gname) ?></div>
              <div class="small text-muted">
                Tipe: <code><?= htmlspecialchars($g['input_type'] ?? '-') ?></code> |
                Min: <?= (int)($g['min_select'] ?? 0) ?> |
                Max: <?= (int)($g['max_select'] ?? 0) ?> |
                Required: <?= !empty($g['is_required']) ? 'Ya' : 'Tidak' ?> |
                Status: <?= !empty($g['is_active']) ? 'Aktif' : 'Nonaktif' ?>
              </div>
            </div>

            <?php if ($isSuper): ?>
              <form method="post"
                    action="<?= $baseUrl ?>/admin/options/group/delete/<?= $gid ?>"
                    onsubmit="return confirm('Hapus group ini? Semua opsi didalamnya ikut terhapus.');">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <button class="btn btn-sm btn-outline-danger">Hapus Group</button>
              </form>
            <?php endif; ?>
          </div>

          <hr>

          <!-- VALUES TABLE -->
          <?php if (!empty($values)): ?>
            <div class="table-responsive">
              <table class="table table-sm table-striped align-middle mb-3">
                <thead>
                  <tr>
                    <th>Label</th>
                    <th style="width:140px;">Tipe</th>
                    <th style="width:160px;">Nilai</th>
                    <th style="width:90px;">Aktif</th>
                    <?php if ($isSuper): ?>
                      <th class="text-end" style="width:120px;">Aksi</th>
                    <?php endif; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($values as $v): ?>
                    <tr>
                      <td><?= htmlspecialchars($v['label'] ?? '-') ?></td>
                      <td><code><?= htmlspecialchars($v['price_type'] ?? 'fixed') ?></code></td>
                      <td><?= (float)($v['price_value'] ?? 0) ?></td>
                      <td><?= !empty($v['is_active']) ? 'Ya' : 'Tidak' ?></td>

                      <?php if ($isSuper): ?>
                        <td class="text-end">
                          <form method="post"
                                action="<?= $baseUrl ?>/admin/options/value/delete/<?= (int)$v['id'] ?>"
                                class="d-inline"
                                onsubmit="return confirm('Hapus opsi ini?');">
                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                            <button class="btn btn-sm btn-danger">Hapus</button>
                          </form>
                        </td>
                      <?php endif; ?>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted mb-3">Belum ada opsi di group ini.</p>
          <?php endif; ?>

          <?php if ($isSuper): ?>
            <!-- FORM TAMBAH VALUE -->
            <form method="post" action="<?= $baseUrl ?>/admin/options/group/<?= $gid ?>/value/store" class="row g-2">
              <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

              <div class="col-md-5">
                <input type="text" name="label" class="form-control" required placeholder="Label opsi (contoh: Art Paper 150gsm)">
              </div>

              <div class="col-md-2">
                <select name="price_type" class="form-select">
                  <option value="fixed">fixed</option>
                  <option value="percent">percent</option>
                </select>
              </div>

              <div class="col-md-2">
                <input type="number" step="0.01" name="price_value" class="form-control" value="0">
              </div>

              <div class="col-md-1">
                <input type="number" name="sort_order" class="form-control" value="0" min="0">
              </div>

              <div class="col-md-1 d-flex align-items-center">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="is_active" value="1" id="va<?= $gid ?>" checked>
                  <label class="form-check-label" for="va<?= $gid ?>">Aktif</label>
                </div>
              </div>

              <div class="col-md-1 text-end">
                <button class="btn btn-sm btn-success w-100">Tambah</button>
              </div>
            </form>
          <?php endif; ?>

        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>
</div>
