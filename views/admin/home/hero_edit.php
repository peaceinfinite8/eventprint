<?php
// views/admin/home/hero_edit.php

$baseUrl = $baseUrl ?? '/eventprint/public';
$hero    = $hero ?? [
    'title'        => '',
    'subtitle'     => '',
    'button_text'  => '',
    'button_link'  => '',
];

// Ambil error & old input kalau ada validasi gagal sebelumnya
$errors = class_exists('Validation') ? Validation::errors() : [];
$old    = $_SESSION['old_input'] ?? [];
if (class_exists('Validation')) {
    Validation::clear();
}

// CSRF token dari layout, fallback kalau belum ada
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<h1 class="h3 mb-3">Edit Hero Home</h1>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?php echo $baseUrl; ?>/admin/home/hero">
      <input type="hidden" name="_token"
             value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $fieldErrors): ?>
              <?php foreach ($fieldErrors as $msg): ?>
                <li><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></li>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Judul / Headline</label>
        <input type="text"
               name="title"
               class="form-control"
               value="<?php
                 echo htmlspecialchars($old['title'] ?? ($hero['title'] ?? ''), ENT_QUOTES, 'UTF-8');
               ?>"
               placeholder="Contoh: Solusi Cetak Digital Lengkap untuk Bisnis Anda">
      </div>

      <div class="mb-3">
        <label class="form-label">Subjudul / Deskripsi Singkat</label>
        <textarea name="subtitle"
                  rows="3"
                  class="form-control"
                  placeholder="Deskripsi singkat layanan atau nilai utama EventPrint."><?php
          echo htmlspecialchars($old['subtitle'] ?? ($hero['subtitle'] ?? ''), ENT_QUOTES, 'UTF-8');
        ?></textarea>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Teks Tombol CTA</label>
          <input type="text"
                 name="button_text"
                 class="form-control"
                 value="<?php
                   echo htmlspecialchars($old['button_text'] ?? ($hero['button_text'] ?? ''), ENT_QUOTES, 'UTF-8');
                 ?>"
                 placeholder="Contoh: Konsultasi Sekarang">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Link Tombol CTA</label>
          <input type="text"
                 name="button_link"
                 class="form-control"
                 value="<?php
                   echo htmlspecialchars($old['button_link'] ?? ($hero['button_link'] ?? ''), ENT_QUOTES, 'UTF-8');
                 ?>"
                 placeholder="/contact atau link WhatsApp">
        </div>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">
          Simpan Perubahan
        </button>
        <a href="<?php echo $baseUrl; ?>/admin/home"
           class="btn btn-secondary">
          Kembali
        </a>
      </div>
    </form>
  </div>
</div>

