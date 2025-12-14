<?php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$product = $vars['product'] ?? null;  // <- WAJIB dari $vars

if (!$product) {
  echo "<div class='alert alert-danger'>Produk tidak ditemukan.</div>";
  return;
}

$pid = (int)$product['id'];
?>

<section class="py-5">
  <div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
      <a class="btn btn-sm btn-outline-secondary" href="<?= $baseUrl ?>/products">← Kembali</a>
      <a class="btn btn-sm btn-outline-primary" href="<?= $baseUrl ?>/contact#order">Order</a>
    </div>

    <div class="row g-4">
      <div class="col-12 col-lg-6">
        <?php
          $img = !empty($product['thumbnail'])
            ? $baseUrl . '/' . ltrim($product['thumbnail'], '/')
            : $baseUrl . '/assets/admin/img/photos/unsplash-2.jpg';
        ?>
        <div class="card border-0 shadow-sm">
          <img src="<?= htmlspecialchars($img) ?>" alt=""
               style="width:100%;height:360px;object-fit:cover;border-radius:12px;">
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h1 class="h4 fw-bold mb-2"><?= htmlspecialchars($product['name'] ?? '-') ?></h1>

            <?php if (!empty($product['short_description'])): ?>
              <p class="text-muted mb-2"><?= htmlspecialchars($product['short_description']) ?></p>
            <?php endif; ?>

            <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-3">
              <div>
                <div class="text-muted small">Harga dasar</div>
                <div class="h5 fw-bold mb-0">Rp <?= number_format((float)($product['base_price'] ?? 0), 0, ',', '.') ?></div>
              </div>

              <div style="min-width:160px;">
                <label class="form-label small mb-1">Qty</label>
                <input id="epQty" type="number" min="1" value="1" class="form-control">
              </div>
            </div>

            <div class="mb-3">
              <div class="fw-semibold mb-2">Konfigurasi Opsi</div>
              <div id="epOptionsBox" class="border rounded p-3 bg-light">
                <div class="text-muted">Loading opsi harga…</div>
              </div>
            </div>

            <div class="border rounded p-3">
              <div class="d-flex justify-content-between">
                <span class="text-muted">Harga / pcs</span>
                <strong id="epUnitPrice">-</strong>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted">Subtotal</span>
                <strong id="epSubtotal">-</strong>
              </div>
              <div class="small text-muted mt-2" id="epBreakdown"></div>
            </div>

            <div class="alert alert-danger mt-3 d-none" id="epErr"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
(function(){
  const baseUrl = <?= json_encode(rtrim($baseUrl,'/')) ?>;
  const productId = <?= (int)$pid ?>;

  const elOptions = document.getElementById('epOptionsBox');
  const elQty = document.getElementById('epQty');
  const elUnit = document.getElementById('epUnitPrice');
  const elSub = document.getElementById('epSubtotal');
  const elBreak = document.getElementById('epBreakdown');
  const elErr = document.getElementById('epErr');

  function rupiah(n){
    n = Number(n||0);
    return 'Rp ' + n.toLocaleString('id-ID');
  }

  function showErr(msg){
    elErr.textContent = msg || 'Terjadi kesalahan.';
    elErr.classList.remove('d-none');
  }
  function hideErr(){ elErr.classList.add('d-none'); }

  function getSelectedValueIds(){
    const ids = [];
    elOptions.querySelectorAll('[data-ep-value]:checked').forEach(i => ids.push(parseInt(i.value,10)));
    elOptions.querySelectorAll('select[data-ep-group]').forEach(s => {
      const v = parseInt(s.value,10);
      if (v) ids.push(v);
    });
    return [...new Set(ids)].filter(Boolean);
  }

  async function fetchOptions(){
    hideErr();
    const url = `${baseUrl}/pricing/options?product_id=${productId}`;
    const res = await fetch(url);
    const json = await res.json();

    if(!json.ok){
      showErr(json.message || 'Gagal load opsi.');
      elOptions.innerHTML = '';
      return;
    }

    renderOptions(json.groups || []);
    await recalc();
  }

  function renderOptions(groups){
    if(!groups.length){
      elOptions.innerHTML = `<div class="text-muted">Produk ini belum punya opsi harga.</div>`;
      return;
    }

    elOptions.innerHTML = groups.map(g => {
      const gid = g.id;
      const title = g.name || 'Opsi';

      if(g.input_type === 'checkbox'){
        const items = (g.values||[]).map(v => `
          <label class="d-flex align-items-center gap-2 py-1">
            <input class="form-check-input m-0" type="checkbox" data-ep-value value="${v.id}">
            <span>${escapeHtml(v.label)} <span class="text-muted small">(+${escapeHtml(v.price_type)} ${escapeHtml(v.price_value)})</span></span>
          </label>
        `).join('');

        return `
          <div class="mb-3">
            <div class="fw-semibold mb-2">${escapeHtml(title)}</div>
            ${items || `<div class="text-muted small">Tidak ada opsi.</div>`}
          </div>
        `;
      }

      // select / radio => kita bikin select biar simpel buat orang awam
      const opts = (g.values||[]).map(v => `<option value="${v.id}">${escapeHtml(v.label)} (+${escapeHtml(v.price_type)} ${escapeHtml(v.price_value)})</option>`).join('');
      return `
        <div class="mb-3">
          <label class="form-label fw-semibold">${escapeHtml(title)} ${g.is_required ? '<span class="text-danger">*</span>' : ''}</label>
          <select class="form-select" data-ep-group="${gid}">
            ${g.is_required ? '' : '<option value="0">— Tidak dipilih —</option>'}
            ${opts}
          </select>
        </div>
      `;
    }).join('');

    // bind change events
    elOptions.querySelectorAll('input,select').forEach(el => {
      el.addEventListener('change', recalc);
    });
  }

  async function recalc(){
    hideErr();
    const qty = Math.max(1, parseInt(elQty.value||'1',10));
    const optionIds = getSelectedValueIds();

    const res = await fetch(`${baseUrl}/pricing/calc`, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({
        product_id: productId,
        qty: qty,
        option_value_ids: optionIds
      })
    });

    const json = await res.json();
    if(!json.ok){
      showErr(json.message || 'Gagal hitung harga.');
      elUnit.textContent = '-';
      elSub.textContent = '-';
      elBreak.textContent = '';
      return;
    }

    const b = json.breakdown || {};
    elUnit.textContent = rupiah(b.unit_price);
    elSub.textContent = rupiah(b.subtotal);
    elBreak.textContent =
      `Base: ${rupiah(b.base)} | Fixed add: ${rupiah(b.fixed_add)} | Percent: ${b.percent_add_total||0}%`;
  }

  function escapeHtml(str){
    return String(str ?? '').replace(/[&<>"']/g, s => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    }[s]));
  }

  elQty.addEventListener('input', () => {
    // debounced sederhana
    clearTimeout(window.__epT);
    window.__epT = setTimeout(recalc, 200);
  });

  fetchOptions().catch(e => showErr(e.message));
})();
</script>
