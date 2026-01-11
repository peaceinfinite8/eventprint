<?php
$baseUrl              = $baseUrl ?? '/eventprint';
$categories           = $categories ?? [];
$selectedCategoryId   = $selectedCategoryId ?? null;
$selectedCategory     = $selectedCategory ?? null;
$categoryMaterials    = $categoryMaterials ?? [];
$categoryLaminations  = $categoryLaminations ?? [];
$allMaterials         = $allMaterials ?? [];
$allLaminations       = $allLaminations ?? [];
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Category Options Mapping</h1>
        <p class="text-muted small mb-0">Atur ketersediaan bahan dan laminasi untuk setiap kategori produk</p>
    </div>
</div>

<div class="row g-4 fade-in delay-1">
  <!-- Left Column: Category Selection & Tools -->
  <div class="col-lg-4">
    <!-- Category Selector -->
    <div class="dash-container-card mb-4">
      <div class="p-4">
        <h6 class="fw-bold text-primary mb-3"><i class="fas fa-filter me-2"></i>Pilih Kategori</h6>
        <form method="get" action="<?php echo $baseUrl; ?>/admin/category-options">
          <div class="mb-3">
            <select name="category" class="form-select" onchange="this.form.submit()">
              <option value="">-- Pilih Kategori Produk --</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"
                  <?php echo ($selectedCategoryId == $cat['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($cat['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </form>

        <?php if ($selectedCategory): ?>
          <div class="p-3 bg-info-subtle border border-info-subtle rounded text-info-emphasis small">
            <div class="fw-bold mb-1"><i class="fas fa-info-circle me-1"></i> Info Kategori</div>
            <div><strong>Nama:</strong> <?php echo htmlspecialchars($selectedCategory['name']); ?></div>
            <div><strong>Slug:</strong> <?php echo htmlspecialchars($selectedCategory['slug']); ?></div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Quick Copy Tool -->
    <?php if (!empty($categories) && count($categories) > 1): ?>
    <div class="dash-container-card">
      <div class="p-4">
        <h6 class="fw-bold text-primary mb-3"><i class="fas fa-copy me-2"></i>Copy Mapping</h6>
        <p class="text-muted small mb-3">Salin pengaturan opsi dari satu kategori ke kategori lain dengan cepat.</p>
        
        <form method="post" action="<?php echo $baseUrl; ?>/admin/category-options/copy">
          <input type="hidden" name="_token"
                 value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

          <div class="mb-3">
            <label class="dash-form-label">DARI KATEGORI (SUMBER)</label>
            <select name="from_category" class="form-select form-select-sm">
              <option value="">-- Pilih Sumber --</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>">
                  <?php echo htmlspecialchars($cat['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="dash-form-label">KE KATEGORI (TUJUAN)</label>
            <select name="to_category" class="form-select form-select-sm">
              <option value="">-- Pilih Tujuan --</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>">
                  <?php echo htmlspecialchars($cat['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <button type="submit" class="btn btn-outline-primary w-100 btn-sm shadow-sm"
                  onclick="return confirm('Yakin copy mapping? Mapping lama di kategori tujuan akan dihapus.');">
            <i class="fas fa-clone me-1"></i> Copy Mapping
          </button>
        </form>
      </div>
    </div>
    <?php endif; ?>
    
    <!-- Quick Links -->
    <div class="mt-4">
        <label class="dash-form-label mb-2">QUICK LINKS</label>
        <div class="d-grid gap-2">
            <a href="<?php echo $baseUrl; ?>/admin/materials" class="btn btn-light border btn-sm text-start">
               <i class="fas fa-scroll me-2 text-secondary"></i> Kelola Bahan (Materials)
            </a>
            <a href="<?php echo $baseUrl; ?>/admin/laminations" class="btn btn-light border btn-sm text-start">
               <i class="fas fa-layer-group me-2 text-secondary"></i> Kelola Laminasi
            </a>
            <a href="<?php echo $baseUrl; ?>/admin/product-categories" class="btn btn-light border btn-sm text-start">
               <i class="fas fa-tags me-2 text-secondary"></i> Kelola Kategori
            </a>
        </div>
    </div>
  </div>

  <!-- Right Column: Mapping Form -->
  <div class="col-lg-8">
    <?php if ($selectedCategory): ?>
    <form method="post" action="<?php echo $baseUrl; ?>/admin/category-options/save">
      <input type="hidden" name="_token"
             value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="category_id" value="<?php echo $selectedCategoryId; ?>">

      <!-- Materials -->
      <div class="dash-container-card mb-4">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold text-primary mb-0"><i class="fas fa-scroll me-2"></i>Pilih Bahan (Materials)</h6>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">
                    <?php echo count($categoryMaterials); ?> Dipilih
                </span>
            </div>
            
            <?php if (empty($allMaterials)): ?>
                <div class="text-center py-4 bg-light rounded border border-light border-dashed">
                    <p class="text-muted mb-2">Belum ada data bahan.</p>
                    <a href="<?php echo $baseUrl; ?>/admin/materials/create" class="btn btn-sm btn-outline-primary">Tambah Bahan Baru</a>
                </div>
            <?php else: ?>
                <div class="row g-3">
                <?php foreach ($allMaterials as $mat): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="form-check custom-card-check p-3 border rounded h-100 bg-white shadow-sm position-relative">
                            <input type="checkbox"
                                    name="materials[]"
                                    value="<?php echo $mat['id']; ?>"
                                    class="form-check-input position-static me-2"
                                    id="mat_<?php echo $mat['id']; ?>"
                                    style="transform: scale(1.2);"
                                    <?php echo in_array((int)$mat['id'], $categoryMaterials) ? 'checked' : ''; ?>>
                            <label class="form-check-label w-100 stretched-link" for="mat_<?php echo $mat['id']; ?>">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($mat['name']); ?></div>
                                <?php if ((float)$mat['price_delta'] > 0): ?>
                                    <small class="badge bg-success-subtle text-success border border-success-subtle mt-1">
                                        +Rp <?php echo number_format((float)$mat['price_delta'], 0, ',', '.'); ?>
                                    </small>
                                <?php endif; ?>
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
      </div>

      <!-- Laminations -->
      <div class="dash-container-card mb-4">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold text-primary mb-0"><i class="fas fa-layer-group me-2"></i>Pilih Laminasi</h6>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">
                    <?php echo count($categoryLaminations); ?> Dipilih
                </span>
            </div>
            
             <?php if (empty($allLaminations)): ?>
                 <div class="text-center py-4 bg-light rounded border border-light border-dashed">
                    <p class="text-muted mb-2">Belum ada data laminasi.</p>
                    <a href="<?php echo $baseUrl; ?>/admin/laminations/create" class="btn btn-sm btn-outline-primary">Tambah Laminasi Baru</a>
                </div>
            <?php else: ?>
                <div class="row g-3">
                <?php foreach ($allLaminations as $lam): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="form-check custom-card-check p-3 border rounded h-100 bg-white shadow-sm position-relative">
                             <input type="checkbox"
                                name="laminations[]"
                                value="<?php echo $lam['id']; ?>"
                                class="form-check-input position-static me-2"
                                id="lam_<?php echo $lam['id']; ?>"
                                style="transform: scale(1.2);"
                                <?php echo in_array((int)$lam['id'], $categoryLaminations) ? 'checked' : ''; ?>>
                            <label class="form-check-label w-100 stretched-link" for="lam_<?php echo $lam['id']; ?>">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($lam['name']); ?></div>
                                 <?php if ((float)$lam['price_delta'] > 0): ?>
                                    <small class="badge bg-success-subtle text-success border border-success-subtle mt-1">
                                        +Rp <?php echo number_format((float)$lam['price_delta'], 0, ',', '.'); ?>
                                    </small>
                                <?php endif; ?>
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
      </div>

      <div class="d-grid pt-2 pb-5">
        <button type="submit" class="btn btn-primary btn-lg shadow">
          <i class="fas fa-save me-2"></i> Simpan Mapping "<?php echo htmlspecialchars($selectedCategory['name']); ?>"
        </button>
      </div>
    </form>
    <?php else: ?>
    <div class="dash-container-card h-100 d-flex align-items-center justify-content-center p-5">
      <div class="text-center text-muted p-5">
        <div class="mb-3"><i class="fas fa-arrow-left fa-3x opacity-25"></i></div>
        <h5 class="fw-bold text-dark">Pilih Kategori</h5>
        <p class="small text-muted mb-0">
          Silakan pilih kategori di panel sebelah kiri untuk mulai mengatur ketersediaan bahan dan laminasi (mapping).
        </p>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<style>
/* Custom checkbox styling for card selection */
.custom-card-check:hover {
    border-color: var(--bs-primary) !important;
    background-color: #f8fbff !important;
}
.custom-card-check .form-check-input:checked ~ label .fw-bold {
    color: var(--bs-primary) !important;
}
.custom-card-check:has(.form-check-input:checked) {
    border-color: var(--bs-primary) !important;
    background-color: #f0f7ff !important;
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
}
</style>
