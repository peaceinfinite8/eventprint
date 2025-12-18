<?php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$content = $vars['content'] ?? [];
$csrfToken = $vars['csrfToken'] ?? '';
?>

<h1 class="h3 mb-3">Edit Konten Home</h1>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?= $baseUrl ?>/admin/home/content/update">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

      <h5 class="mb-3">Kontak</h5>

      <div class="mb-3">
        <label class="form-label">Alamat</label>
        <textarea class="form-control" name="contact_address" rows="3"><?= htmlspecialchars($content['contact_address'] ?? '') ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control" name="contact_email" value="<?= htmlspecialchars($content['contact_email'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">WhatsApp</label>
        <input class="form-control" name="contact_whatsapp" value="<?= htmlspecialchars($content['contact_whatsapp'] ?? '') ?>">
      </div>

      <hr class="my-4">

      <h5 class="mb-3">CTA Bar</h5>

      <div class="row g-3">
        <div class="col-12 col-lg-6">
          <label class="form-label">CTA Kiri Text</label>
          <input class="form-control" name="cta_left_text" value="<?= htmlspecialchars($content['cta_left_text'] ?? '') ?>">
        </div>
        <div class="col-12 col-lg-6">
          <label class="form-label">CTA Kiri Link</label>
          <input class="form-control" name="cta_left_link" value="<?= htmlspecialchars($content['cta_left_link'] ?? '') ?>">
        </div>

        <div class="col-12 col-lg-6">
          <label class="form-label">CTA Kanan Text</label>
          <input class="form-control" name="cta_right_text" value="<?= htmlspecialchars($content['cta_right_text'] ?? '') ?>">
        </div>
        <div class="col-12 col-lg-6">
          <label class="form-label">CTA Kanan Link</label>
          <input class="form-control" name="cta_right_link" value="<?= htmlspecialchars($content['cta_right_link'] ?? '') ?>">
        </div>
      </div>

      <button class="btn btn-primary mt-4" type="submit">Simpan</button>
      <a class="btn btn-outline-secondary mt-4" href="<?= $baseUrl ?>/admin/home">Kembali</a>
    </form>
  </div>
</div>
