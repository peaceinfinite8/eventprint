<?php
$baseUrl = $baseUrl ?? '/eventprint';
$product = $product ?? [];
$allMaterials = $allMaterials ?? [];
$allLaminations = $allLaminations ?? [];
$productMaterials = $productMaterials ?? [];
$productLaminations = $productLaminations ?? [];
$categoryMaterials = $categoryMaterials ?? [];
$categoryLaminations = $categoryLaminations ?? [];
?>

<h1 class="h3 mb-3">Opsi Produk: <?php echo htmlspecialchars($product['name']); ?></h1>

<form method="post" action="<?php echo $baseUrl; ?>/admin/products/<?php echo $product['id']; ?>/product-options/save">
  <input type="hidden" name="_token"
         value="<?php echo htmlspecialchars($csrfToken ?? Security::csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">

  <div class="row">
    <!-- Options Source -->
    <div class="col-12">
      <div class="card mb-3">
        <div class="card-header">
          <strong>Sumber Opsi</strong>
        </div>
        <div class="card-body">
          <p class="text-muted small mb-3">
            Pilih dari mana opsi bahan dan laminasi diambil untuk produk ini:
          </p>
          
          <div class="form-check mb-2">
            <input type="radio" name="options_source" value="category" id="srcCategory"
                   class="form-check-input"
                   <?php echo ($product['options_source'] ?? 'category') === 'category' ? 'checked' : ''; ?>>
            <label class="form-check-label" for="srcCategory">
              <strong>Dari Kategori</strong>
              <span class="text-muted small d-block">Gunakan opsi yang sudah di-mapping ke kategori produk ini</span>
            </label>
          </div>

          <div class="form-check mb-2">
            <input type="radio" name="options_source" value="product" id="srcProduct"
                   class="form-check-input"
                   <?php echo ($product['options_source'] ?? 'category') === 'product' ? 'checked' : ''; ?>>
            <label class="form-check-label" for="srcProduct">
              <strong>Khusus Produk Ini</strong>
              <span class="text-muted small d-block">Hanya gunakan opsi yang dipilih di bawah (abaikan kategori)</span>
            </label>
          </div>

          <div class="form-check mb-2">
            <input type="radio" name="options_source" value="both" id="srcBoth"
                   class="form-check-input"
                   <?php echo ($product['options_source'] ?? 'category') === 'both' ? 'checked' : ''; ?>>
            <label class="form-check-label" for="srcBoth">
              <strong>Gabungkan Keduanya</strong>
              <span class="text-muted small d-block">Tampilkan opsi dari kategori + opsi khusus produk</span>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Materials -->
    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>Bahan (Materials)</strong>
          <span class="badge bg-primary"><?php echo count($productMaterials); ?> dipilih</span>
        </div>
        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
          <?php if (empty($allMaterials)): ?>
            <p class="text-muted mb-0">Belum ada bahan. <a href="<?php echo $baseUrl; ?>/admin/materials/create">Tambah bahan</a></p>
          <?php else: ?>
            <?php foreach ($allMaterials as $mat): ?>
              <?php
                $isFromCategory = in_array((int)$mat['id'], $categoryMaterials);
                $isSelected = array_key_exists((int)$mat['id'], $productMaterials);
              ?>
              <div class="form-check mb-2">
                <input type="checkbox"
                       name="materials[]"
                       value="<?php echo $mat['id']; ?>"
                       class="form-check-input"
                       id="mat_<?php echo $mat['id']; ?>"
                       <?php echo $isSelected ? 'checked' : ''; ?>>
                <label class="form-check-label" for="mat_<?php echo $mat['id']; ?>">
                  <?php echo htmlspecialchars($mat['name']); ?>
                  <?php if ((float)$mat['price_delta'] > 0): ?>
                    <small class="text-success">(+Rp <?php echo number_format((float)$mat['price_delta'], 0, ',', '.'); ?>)</small>
                  <?php endif; ?>
                  <?php if ($isFromCategory): ?>
                    <span class="badge bg-info ms-1">dari kategori</span>
                  <?php endif; ?>
                </label>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Laminations -->
    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>Laminasi</strong>
          <span class="badge bg-primary"><?php echo count($productLaminations); ?> dipilih</span>
        </div>
        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
          <?php if (empty($allLaminations)): ?>
            <p class="text-muted mb-0">Belum ada laminasi. <a href="<?php echo $baseUrl; ?>/admin/laminations/create">Tambah laminasi</a></p>
          <?php else: ?>
            <?php foreach ($allLaminations as $lam): ?>
              <?php
                $isFromCategory = in_array((int)$lam['id'], $categoryLaminations);
                $isSelected = array_key_exists((int)$lam['id'], $productLaminations);
              ?>
              <div class="form-check mb-2">
                <input type="checkbox"
                       name="laminations[]"
                       value="<?php echo $lam['id']; ?>"
                       class="form-check-input"
                       id="lam_<?php echo $lam['id']; ?>"
                       <?php echo $isSelected ? 'checked' : ''; ?>>
                <label class="form-check-label" for="lam_<?php echo $lam['id']; ?>">
                  <?php echo htmlspecialchars($lam['name']); ?>
                  <?php if ((float)$lam['price_delta'] > 0): ?>
                    <small class="text-success">(+Rp <?php echo number_format((float)$lam['price_delta'], 0, ',', '.'); ?>)</small>
                  <?php endif; ?>
                  <?php if ($isFromCategory): ?>
                    <span class="badge bg-info ms-1">dari kategori</span>
                  <?php endif; ?>
                </label>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body d-flex gap-2">
      <button type="submit" class="btn btn-primary btn-lg">
        💾 Simpan Opsi Produk
      </button>
      <a href="<?php echo $baseUrl; ?>/admin/products/edit/<?php echo $product['id']; ?>" class="btn btn-secondary">
        ← Kembali ke Edit Produk
      </a>
      <a href="<?php echo $baseUrl; ?>/admin/products" class="btn btn-outline-secondary">
        Daftar Produk
      </a>
    </div>
  </div>
</form>

<div class="card mt-4">
  <div class="card-header">
    <strong>Keterangan</strong>
  </div>
  <div class="card-body small">
    <ul class="mb-0">
      <li><span class="badge bg-info">dari kategori</span> = Opsi ini sudah tersedia dari mapping kategori</li>
      <li><strong>Dari Kategori</strong> = Produk akan menggunakan opsi sesuai kategori-nya (tidak perlu pilih manual)</li>
      <li><strong>Khusus Produk</strong> = Produk HANYA gunakan opsi yang dicentang di halaman ini</li>
      <li><strong>Gabungkan</strong> = Tampilkan opsi kategori + opsi khusus yang dicentang di sini</li>
    </ul>
  </div>
</div>
