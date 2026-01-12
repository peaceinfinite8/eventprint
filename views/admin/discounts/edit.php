<?php
$baseUrl = $baseUrl ?? '/eventprint';
$item = $item ?? null;
$products = $products ?? [];
$csrfToken = $csrfToken ?? '';

if (!$item) {
  echo "Data tidak ditemukan";
  return;
}

function dtLocal(?string $dt): string
{
  if (!$dt)
    return '';
  $ts = strtotime($dt);
  return $ts ? date('Y-m-d\TH:i', $ts) : '';
}
?>

<h1 class="h3 mb-3">Edit Diskon</h1>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?php echo $baseUrl; ?>/admin/discounts/update/<?php echo (int) $item['id']; ?>">
      <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Produk *</label>
          <select class="form-select" name="product_id" required>
            <?php foreach ($products as $p): ?>
              <option value="<?php echo (int) $p['id']; ?>" <?php echo ((int) $item['product_id'] === (int) $p['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($p['name']); ?> (Stok: <?php echo (int) $p['stock']; ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Tipe *</label>
          <select class="form-select" name="discount_type" required>
            <option value="percent" <?php echo ($item['discount_type'] === 'percent') ? 'selected' : ''; ?>>Percent (%)
            </option>
            <option value="fixed" <?php echo ($item['discount_type'] === 'fixed') ? 'selected' : ''; ?>>Potongan (Rp)
            </option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Nilai Diskon *</label>
          <input class="form-control" type="number" step="0.01" min="0" name="discount_value" required
            value="<?php echo htmlspecialchars((string) $item['discount_value']); ?>">
        </div>


        <div class="col-md-4">
          <label class="form-label">Mulai</label>
          <input class="form-control" type="datetime-local" name="start_at"
            value="<?php echo htmlspecialchars(dtLocal($item['start_at'] ?? null)); ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Selesai</label>
          <input class="form-control" type="datetime-local" name="end_at"
            value="<?php echo htmlspecialchars(dtLocal($item['end_at'] ?? null)); ?>">
        </div>

        <div class="col-md-4 d-flex align-items-end">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo !empty($item['is_active']) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="is_active">Aktif</label>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <button class="btn btn-primary" type="submit">Update</button>
        <a class="btn btn-secondary" href="<?php echo $baseUrl; ?>/admin/discounts">Kembali</a>
      </div>
    </form>
  </div>
</div>