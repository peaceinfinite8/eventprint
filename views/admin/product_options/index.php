<?php
/**
 * Admin view for managing product pricing options (groups + values)
 * Path: views/admin/product_options/index.php
 */

$baseUrl = $baseUrl ?? '/eventprint/public';
$product = $product ?? [];
$groups = $groups ?? [];
$isSuper = $isSuper ?? false;
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<h1 class="h3 mb-3">Opsi Harga: <?php echo htmlspecialchars($product['name'] ?? 'Produk'); ?></h1>

<div class="row">
  <div class="col-12">
    <a href="<?php echo $baseUrl; ?>/admin/products" class="btn btn-outline-secondary mb-3">
      ← Kembali ke Daftar Produk
    </a>
  </div>
</div>

<?php if ($isSuper): ?>
<!-- Add New Group Form -->
<div class="card mb-4">
  <div class="card-header bg-primary text-white">
    <strong>+ Tambah Group Opsi Baru</strong>
  </div>
  <div class="card-body">
    <form method="post" action="<?php echo $baseUrl; ?>/admin/products/<?php echo $product['id']; ?>/options/group/store">
      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
      
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Nama Group</label>
          <input type="text" name="name" class="form-control" required placeholder="e.g. Ukuran, Jumlah">
        </div>
        <div class="col-md-3">
          <label class="form-label">Tipe Input</label>
          <select name="input_type" class="form-select">
            <option value="select">Select (Dropdown)</option>
            <option value="radio">Radio Button</option>
            <option value="checkbox">Checkbox</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Urutan</label>
          <input type="number" name="sort_order" class="form-control" value="0" min="0">
        </div>
        <div class="col-md-3 d-flex align-items-end gap-3">
          <div class="form-check">
            <input type="checkbox" name="is_required" class="form-check-input" id="newGroupRequired">
            <label class="form-check-label" for="newGroupRequired">Wajib</label>
          </div>
          <div class="form-check">
            <input type="checkbox" name="is_active" class="form-check-input" id="newGroupActive" checked>
            <label class="form-check-label" for="newGroupActive">Aktif</label>
          </div>
          <button type="submit" class="btn btn-success">Tambah</button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Existing Groups -->
<?php if (empty($groups)): ?>
  <div class="card">
    <div class="card-body text-center text-muted py-5">
      <p class="mb-0">Belum ada group opsi untuk produk ini.</p>
      <?php if ($isSuper): ?>
        <p class="text-primary mt-2">Gunakan form di atas untuk menambahkan group opsi.</p>
      <?php endif; ?>
    </div>
  </div>
<?php else: ?>
  <?php foreach ($groups as $group): ?>
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <strong><?php echo htmlspecialchars($group['name']); ?></strong>
          <span class="badge bg-info ms-2"><?php echo htmlspecialchars($group['input_type']); ?></span>
          <?php if ($group['is_required']): ?>
            <span class="badge bg-warning text-dark ms-1">Wajib</span>
          <?php endif; ?>
          <?php if (!$group['is_active']): ?>
            <span class="badge bg-secondary ms-1">Nonaktif</span>
          <?php endif; ?>
        </div>
        
        <?php if ($isSuper): ?>
        <div class="btn-group">
          <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#editGroup<?php echo $group['id']; ?>">
            Edit Group
          </button>
          <form method="post" action="<?php echo $baseUrl; ?>/admin/options/group/delete/<?php echo $group['id']; ?>" 
                class="d-inline" onsubmit="return confirm('Hapus group dan semua opsi di dalamnya?');">
            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
          </form>
        </div>
        <?php endif; ?>
      </div>

      <?php if ($isSuper): ?>
      <!-- Edit Group Form (Collapsed) -->
      <div class="collapse" id="editGroup<?php echo $group['id']; ?>">
        <div class="card-body border-bottom bg-light">
          <form method="post" action="<?php echo $baseUrl; ?>/admin/options/group/update/<?php echo $group['id']; ?>">
            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Nama Group</label>
                <input type="text" name="name" class="form-control" required 
                       value="<?php echo htmlspecialchars($group['name']); ?>">
              </div>
              <div class="col-md-3">
                <label class="form-label">Tipe Input</label>
                <select name="input_type" class="form-select">
                  <option value="select" <?php echo $group['input_type'] === 'select' ? 'selected' : ''; ?>>Select</option>
                  <option value="radio" <?php echo $group['input_type'] === 'radio' ? 'selected' : ''; ?>>Radio</option>
                  <option value="checkbox" <?php echo $group['input_type'] === 'checkbox' ? 'selected' : ''; ?>>Checkbox</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">Urutan</label>
                <input type="number" name="sort_order" class="form-control" 
                       value="<?php echo (int)$group['sort_order']; ?>" min="0">
              </div>
              <div class="col-md-3 d-flex align-items-end gap-3">
                <div class="form-check">
                  <input type="checkbox" name="is_required" class="form-check-input" 
                         <?php echo $group['is_required'] ? 'checked' : ''; ?>>
                  <label class="form-check-label">Wajib</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" name="is_active" class="form-check-input" 
                         <?php echo $group['is_active'] ? 'checked' : ''; ?>>
                  <label class="form-check-label">Aktif</label>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <?php endif; ?>

      <div class="card-body">
        <!-- Values Table -->
        <table class="table table-sm table-striped mb-3">
          <thead>
            <tr>
              <th>Label</th>
              <th>Tipe Harga</th>
              <th>Nilai</th>
              <th>Urutan</th>
              <th>Status</th>
              <?php if ($isSuper): ?>
              <th class="text-end">Aksi</th>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($group['values'])): ?>
              <tr>
                <td colspan="<?php echo $isSuper ? 6 : 5; ?>" class="text-center text-muted">
                  Belum ada opsi untuk group ini.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($group['values'] as $val): ?>
                <tr>
                  <td><?php echo htmlspecialchars($val['label']); ?></td>
                  <td>
                    <span class="badge bg-<?php echo $val['price_type'] === 'fixed' ? 'primary' : 'success'; ?>">
                      <?php echo $val['price_type'] === 'fixed' ? 'Fixed (+Rp)' : 'Percent (%)'; ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($val['price_type'] === 'fixed'): ?>
                      +Rp <?php echo number_format((float)$val['price_value'], 0, ',', '.'); ?>
                    <?php else: ?>
                      +<?php echo (float)$val['price_value']; ?>%
                    <?php endif; ?>
                  </td>
                  <td><?php echo (int)$val['sort_order']; ?></td>
                  <td>
                    <?php if ($val['is_active']): ?>
                      <span class="badge bg-success">Aktif</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Nonaktif</span>
                    <?php endif; ?>
                  </td>
                  <?php if ($isSuper): ?>
                  <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-primary" 
                            data-bs-toggle="collapse" data-bs-target="#editVal<?php echo $val['id']; ?>">
                      Edit
                    </button>
                    <form method="post" action="<?php echo $baseUrl; ?>/admin/options/value/delete/<?php echo $val['id']; ?>" 
                          class="d-inline" onsubmit="return confirm('Hapus opsi ini?');">
                      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                    </form>
                  </td>
                  <?php endif; ?>
                </tr>
                
                <?php if ($isSuper): ?>
                <!-- Edit Value Row -->
                <tr class="collapse" id="editVal<?php echo $val['id']; ?>">
                  <td colspan="6" class="bg-light">
                    <form method="post" action="<?php echo $baseUrl; ?>/admin/options/value/update/<?php echo $val['id']; ?>" class="row g-2 align-items-end">
                      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                      <div class="col-md-3">
                        <input type="text" name="label" class="form-control" required 
                               value="<?php echo htmlspecialchars($val['label']); ?>" placeholder="Label">
                      </div>
                      <div class="col-md-2">
                        <select name="price_type" class="form-select">
                          <option value="fixed" <?php echo $val['price_type'] === 'fixed' ? 'selected' : ''; ?>>Fixed</option>
                          <option value="percent" <?php echo $val['price_type'] === 'percent' ? 'selected' : ''; ?>>Percent</option>
                        </select>
                      </div>
                      <div class="col-md-2">
                        <input type="number" name="price_value" class="form-control" step="0.01" 
                               value="<?php echo (float)$val['price_value']; ?>" placeholder="Nilai">
                      </div>
                      <div class="col-md-1">
                        <input type="number" name="sort_order" class="form-control" min="0" 
                               value="<?php echo (int)$val['sort_order']; ?>">
                      </div>
                      <div class="col-md-2 d-flex align-items-center">
                        <div class="form-check">
                          <input type="checkbox" name="is_active" class="form-check-input" 
                                 <?php echo $val['is_active'] ? 'checked' : ''; ?>>
                          <label class="form-check-label">Aktif</label>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                      </div>
                    </form>
                  </td>
                </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>

        <?php if ($isSuper): ?>
        <!-- Add New Value Form -->
        <div class="border-top pt-3">
          <h6 class="mb-3">+ Tambah Opsi Baru</h6>
          <form method="post" action="<?php echo $baseUrl; ?>/admin/options/group/<?php echo $group['id']; ?>/value/store" class="row g-2 align-items-end">
            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="col-md-3">
              <input type="text" name="label" class="form-control" required placeholder="Label (e.g. 100 pcs)">
            </div>
            <div class="col-md-2">
              <select name="price_type" class="form-select">
                <option value="fixed">Fixed (+Rp)</option>
                <option value="percent">Percent (%)</option>
              </select>
            </div>
            <div class="col-md-2">
              <input type="number" name="price_value" class="form-control" step="0.01" value="0" placeholder="Nilai">
            </div>
            <div class="col-md-1">
              <input type="number" name="sort_order" class="form-control" min="0" value="0" placeholder="Urutan">
            </div>
            <div class="col-md-2 d-flex align-items-center">
              <div class="form-check">
                <input type="checkbox" name="is_active" class="form-check-input" checked>
                <label class="form-check-label">Aktif</label>
              </div>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-success btn-sm">Tambah</button>
            </div>
          </form>
        </div>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
