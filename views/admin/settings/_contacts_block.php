<h5 class="fw-bold text-primary mb-3">
  <i class="fas fa-headset me-2"></i>Kontak Sales Tambahan
</h5>

<p class="text-muted small mb-2">
  Tambahkan kontak sales (label + nomor WhatsApp). Format nomor: 08… atau 62… (tanpa spasi lebih baik).
</p>

<div class="p-3 bg-light rounded border border-light">
  <div id="contacts-container">
    <?php
      $contacts = json_decode($settings['sales_contacts'] ?? '[]', true);
      if (!is_array($contacts)) $contacts = [];
      foreach ($contacts as $c):
        $name = (string)($c['name'] ?? '');
        $num  = (string)($c['number'] ?? '');
    ?>
      <div class="row g-2 align-items-center mb-2 contact-row">
        <div class="col-12 col-md-5">
          <input
            type="text"
            name="sales_contacts[name][]"
            class="form-control form-control-sm"
            placeholder="Nama / Label"
            value="<?= htmlspecialchars($name) ?>"
            autocomplete="off"
          >
        </div>

        <div class="col-12 col-md-5">
          <input
            type="tel"
            inputmode="numeric"
            name="sales_contacts[number][]"
            class="form-control form-control-sm wa-number"
            placeholder="Nomor WhatsApp (contoh: 6281… atau 081…)"
            value="<?= htmlspecialchars($num) ?>"
            autocomplete="tel"
          >
        </div>

        <div class="col-12 col-md-2">
          <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary btn-sm flex-grow-1 btn-wa-test is-disabled"
               role="button" aria-disabled="true" title="Test WhatsApp">
              ↗
            </a>

            <button type="button"
                    class="btn btn-outline-danger btn-sm btn-icon remove-contact"
                    title="Hapus kontak">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <button type="button" class="btn btn-sm btn-outline-primary mt-2 w-100 border-dashed" id="add-contact">
    <i class="fas fa-plus me-1"></i> Tambah Kontak Sales
  </button>
</div>
