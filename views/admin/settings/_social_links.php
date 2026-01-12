<?php
/**
 * Social links form block.
 * - Normalizes URLs for the preview/open button (adds https:// when missing)
 * - Disables the open button when the field is empty
 */

if (!function_exists('normalize_url_for_href')) {
  function normalize_url_for_href(?string $raw): string
  {
    $raw = trim((string) $raw);
    if ($raw === '') return '';

    // Add scheme if missing to ensure valid absolute URL.
    if (!preg_match('#^https?://#i', $raw)) {
      $raw = 'https://' . ltrim($raw, '/');
    }

    return $raw;
  }
}

if (!function_exists('render_open_link_btn')) {
  function render_open_link_btn(?string $rawUrl): string
  {
    $rawUrl = trim((string) $rawUrl);

    if ($rawUrl === '') {
      return '<a class="btn btn-outline-secondary btn-open-link is-disabled" role="button" aria-disabled="true" title="Belum diisi">↗</a>';
    }

    $href = htmlspecialchars(normalize_url_for_href($rawUrl), ENT_QUOTES, 'UTF-8');

    return '<a class="btn btn-outline-secondary btn-open-link" href="' . $href . '" target="_blank" rel="noopener">↗</a>';
  }
}
?>

<h5 class="fw-bold text-primary mb-3">
  <i class="fas fa-share-alt me-2"></i>Social Media Links
</h5>
<p class="text-muted small mb-3">Isi dengan URL lengkap. Kosongkan jika tidak digunakan.</p>

<div class="row g-3">
  <!-- FACEBOOK -->
  <div class="col-md-6">
    <label class="dash-form-label small text-muted mb-1" for="facebook">Facebook</label>
    <div class="input-group">
      <span class="input-group-text bg-white justify-content-center" style="width:44px">
        <i class="fab fa-facebook-f"></i>
      </span>
      <input
        id="facebook"
        type="url"
        inputmode="url"
        autocomplete="url"
        name="facebook"
        class="form-control"
        placeholder="https://facebook.com/username"
        value="<?= htmlspecialchars($settings['facebook'] ?? '') ?>"
      >
      <?= render_open_link_btn($settings['facebook'] ?? '') ?>
    </div>
  </div>

  <!-- TWITTER/X -->
  <div class="col-md-6">
    <label class="dash-form-label small text-muted mb-1" for="twitter">Twitter / X</label>
    <div class="input-group">
      <span class="input-group-text bg-white justify-content-center" style="width:44px">
        <i class="fab fa-twitter"></i>
      </span>
      <input
        id="twitter"
        type="url"
        inputmode="url"
        autocomplete="url"
        name="twitter"
        class="form-control"
        placeholder="https://x.com/username"
        value="<?= htmlspecialchars($settings['twitter'] ?? '') ?>"
      >
      <?= render_open_link_btn($settings['twitter'] ?? '') ?>
    </div>
  </div>

  <!-- INSTAGRAM -->
  <div class="col-md-6">
    <label class="dash-form-label small text-muted mb-1" for="instagram">Instagram</label>
    <div class="input-group">
      <span class="input-group-text bg-white justify-content-center" style="width:44px">
        <i class="fab fa-instagram"></i>
      </span>
      <input
        id="instagram"
        type="url"
        inputmode="url"
        autocomplete="url"
        name="instagram"
        class="form-control"
        placeholder="https://instagram.com/username"
        value="<?= htmlspecialchars($settings['instagram'] ?? '') ?>"
      >
      <?= render_open_link_btn($settings['instagram'] ?? '') ?>
    </div>
  </div>

  <!-- TIKTOK -->
  <div class="col-md-6">
    <label class="dash-form-label small text-muted mb-1" for="tiktok">TikTok</label>
    <div class="input-group">
      <span class="input-group-text bg-white justify-content-center" style="width:44px">
        <i class="fab fa-tiktok"></i>
      </span>
      <input
        id="tiktok"
        type="url"
        inputmode="url"
        autocomplete="url"
        name="tiktok"
        class="form-control"
        placeholder="https://tiktok.com/@username"
        value="<?= htmlspecialchars($settings['tiktok'] ?? '') ?>"
      >
      <?= render_open_link_btn($settings['tiktok'] ?? '') ?>
    </div>
  </div>

  <!-- YOUTUBE -->
  <div class="col-md-6">
    <label class="dash-form-label small text-muted mb-1" for="youtube">YouTube</label>
    <div class="input-group">
      <span class="input-group-text bg-white justify-content-center" style="width:44px">
        <i class="fab fa-youtube"></i>
      </span>
      <input
        id="youtube"
        type="url"
        inputmode="url"
        autocomplete="url"
        name="youtube"
        class="form-control"
        placeholder="https://youtube.com/@channel"
        value="<?= htmlspecialchars($settings['youtube'] ?? '') ?>"
      >
      <?= render_open_link_btn($settings['youtube'] ?? '') ?>
    </div>
  </div>

  <!-- LINKEDIN -->
  <div class="col-md-6">
    <label class="dash-form-label small text-muted mb-1" for="linkedin">LinkedIn</label>
    <div class="input-group">
      <span class="input-group-text bg-white justify-content-center" style="width:44px">
        <i class="fab fa-linkedin-in"></i>
      </span>
      <input
        id="linkedin"
        type="url"
        inputmode="url"
        autocomplete="url"
        name="linkedin"
        class="form-control"
        placeholder="https://linkedin.com/in/username"
        value="<?= htmlspecialchars($settings['linkedin'] ?? '') ?>"
      >
      <?= render_open_link_btn($settings['linkedin'] ?? '') ?>
    </div>
  </div>
</div>
