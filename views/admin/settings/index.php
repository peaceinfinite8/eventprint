<?php
$baseUrl  = $baseUrl ?? '/eventprint/public';
$settings = $settings ?? [];
?>

<h1 class="h3 mb-3">General Settings</h1>

<div class="card">
  <div class="card-body">
    <form action="<?php echo $baseUrl; ?>/admin/settings/update"
          method="post"
          enctype="multipart/form-data">
      <input type="hidden" name="_token"
             value="<?php echo htmlspecialchars($csrfToken ?? Security::csrfToken()); ?>">

      <div class="mb-3">
        <label class="form-label">Nama Website</label>
        <input type="text"
               name="site_name"
               class="form-control"
               value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Tagline</label>
        <input type="text"
               name="site_tagline"
               class="form-control"
               value="<?php echo htmlspecialchars($settings['site_tagline'] ?? ''); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">No. Telepon</label>
        <input type="text"
               name="phone"
               class="form-control"
               value="<?php echo htmlspecialchars($settings['phone'] ?? ''); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email"
               name="email"
               class="form-control"
               value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="address" rows="3" class="form-control"><?php
          echo htmlspecialchars($settings['address'] ?? '');
        ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Facebook URL</label>
        <input type="text"
               name="facebook"
               class="form-control"
               value="<?php echo htmlspecialchars($settings['facebook'] ?? ''); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Instagram URL</label>
        <input type="text"
               name="instagram"
               class="form-control"
               value="<?php echo htmlspecialchars($settings['instagram'] ?? ''); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">WhatsApp (nomor atau link)</label>
        <input type="text"
               name="whatsapp"
               class="form-control"
               value="<?php echo htmlspecialchars($settings['whatsapp'] ?? ''); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label d-block">Logo Website</label>

        <?php if (!empty($settings['logo'])): ?>
          <div class="mb-2">
            <img src="<?php echo $baseUrl . '/' . htmlspecialchars($settings['logo']); ?>"
                 alt="Logo saat ini"
                 style="max-height:60px;">
          </div>
        <?php endif; ?>

        <input type="file"
               name="logo"
               class="form-control"
               accept="image/*">
        <small class="text-muted">
          Biarkan kosong kalau tidak ingin mengganti logo.
        </small>
      </div>

      <button type="submit" class="btn btn-primary">
        Simpan Settings
      </button>
    </form>
  </div>
</div>
