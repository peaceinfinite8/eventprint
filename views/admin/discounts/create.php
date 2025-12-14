<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$products = $products ?? [];
$csrfToken = $csrfToken ?? '';
?>

<h1 class="h3 mb-3">Tambah Diskon</h1>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?php echo $baseUrl; ?>/admin/discounts/store">
      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Produk *</label>
          <select class="form-select" name="product_id" required>
            <option value="">-- Pilih Produk --</option>
            <?php foreach ($products as $p): ?>
              <option value="<?php echo (int)$p['id']; ?>">
                <?php echo htmlspecialchars($p['name']); ?> (Stok: <?php echo (int)$p['stock']; ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">Kuota diskon tidak boleh melebihi stok.</div>
        </div>

        <div class="col-md-3">
          <label class="form-label">Tipe *</label>
          <select class="form-select" name="discount_type" required>
            <option value="percent">Percent (%)</option>
            <option value="fixed">Potongan (Rp)</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Nilai Diskon *</label>
          <input class="form-control" type="number" step="0.01" min="0" name="discount_value" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Kuota Diskon *</label>
          <input class="form-control" type="number" min="1" name="qty_total" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Mulai</label>
          <input class="form-control" type="datetime-local" name="start_at">
          <div class="form-text">Kosong = mulai sekarang.</div>
        </div>

        <div class="col-md-4">
          <label class="form-label">Selesai</label>
          <input class="form-control" type="datetime-local" name="end_at">
          <div class="form-text">Kosong = sampai kuota habis.</div>
        </div>

        <div class="col-md-4 d-flex align-items-end">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
            <label class="form-check-label" for="is_active">Aktif</label>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-secondary" href="<?php echo $baseUrl; ?>/admin/discounts">Kembali</a>
      </div>
    </form>
  </div>
</div>
