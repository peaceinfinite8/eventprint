<?php
// views/admin/home/hero_form.php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$baseUrl = rtrim($baseUrl, '/');
$mode = $vars['mode'] ?? 'create'; // create|edit
$item = $vars['item'] ?? [];
$csrfToken = $vars['csrfToken'] ?? '';

$id = (int) ($item['id'] ?? 0);

$action = ($mode === 'edit')
  ? $baseUrl . '/admin/home/hero/update/' . $id
  : $baseUrl . '/admin/home/hero/store';

$currentImage = (string) ($item['image'] ?? ''); // path relatif di DB
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient"><?= $mode === 'edit' ? 'Edit Slide' : 'Tambah Slide' ?></h1>
    <p class="text-muted small mb-0">Atur konten slide banner utama</p>
  </div>
</div>

<div class="dash-container-card fade-in delay-1">
  <div class="p-4">
    <form method="post" action="<?= htmlspecialchars($action) ?>" enctype="multipart/form-data" class="save-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="old_image" value="<?= htmlspecialchars($currentImage, ENT_QUOTES, 'UTF-8') ?>">

      <div class="row g-4">
        <!-- Left Column: Main Content -->
        <div class="col-lg-8">
          <div class="mb-4">
            <label class="dash-form-label">JUDUL SLIDE</label>
            <input class="form-control" name="title" value="<?= htmlspecialchars($item['title'] ?? '') ?>"
              placeholder="Masukkan judul menarik...">
          </div>

          <div class="mb-4">
            <label class="dash-form-label">SUBTITLE</label>
            <textarea class="form-control" name="subtitle" rows="3"
              placeholder="Deskripsi singkat slide..."><?= htmlspecialchars($item['subtitle'] ?? '') ?></textarea>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="dash-form-label">CTA TEXT</label>
              <input class="form-control" name="cta_text" value="<?= htmlspecialchars($item['cta_text'] ?? '') ?>"
                placeholder="Contoh: Belanja Sekarang">
            </div>
            <div class="col-md-6">
              <label class="dash-form-label">CTA LINK</label>
              <input class="form-control" name="cta_link" value="<?= htmlspecialchars($item['cta_link'] ?? '') ?>"
                placeholder="/products">
            </div>
          </div>
        </div>

        <!-- Right Column: Image & Settings -->
        <div class="col-lg-4 border-start-lg ps-lg-4">
          <div class="bg-light p-3 rounded-3 mb-4 border">
            <label class="dash-form-label mb-2">IMAGE SLIDE</label>

            <div class="mb-3 text-center" id="imgPreviewContainer" <?= empty($currentImage) ? 'style="display:none;"' : '' ?>>
              <img id="imgPreview"
                src="<?= !empty($currentImage) ? htmlspecialchars($baseUrl . '/' . ltrim($currentImage, '/')) : '#' ?>"
                alt="Preview" class="img-fluid rounded shadow-sm border" style="max-height: 150px; object-fit: cover;">
            </div>

            <input class="form-control form-control-sm" type="file" name="image_file" id="imageInput"
              accept="image/jpeg,image/png,image/webp" <?= $mode === 'create' ? 'required' : '' ?>>
            <div class="text-muted extra-small mt-2">
              <i class="fas fa-info-circle me-1"></i> Wajib diisi (JPG/PNG/WebP).
            </div>
          </div>

          <div class="mb-3">
            <label class="dash-form-label">BADGE (OPSIONAL)</label>
            <input class="form-control" name="badge" value="<?= htmlspecialchars($item['badge'] ?? '') ?>"
              placeholder="Contoh: New Arrival">
          </div>

          <div class="mb-3">
            <label class="dash-form-label">BANNER LOCATION</label>
            <select class="form-select" name="page_slug">
              <option value="home" <?= ($item['page_slug'] ?? 'home') === 'home' ? 'selected' : '' ?>>Main Banner (Left)
              </option>
              <option value="home_right_top" <?= ($item['page_slug'] ?? '') === 'home_right_top' ? 'selected' : '' ?>>Right
                Banner (Top)</option>
              <option value="home_right_bottom" <?= ($item['page_slug'] ?? '') === 'home_right_bottom' ? 'selected' : '' ?>>Right Banner (Bottom)</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="dash-form-label">POSITION</label>
            <input type="number" class="form-control" name="position" min="1"
              value="<?= (int) ($item['position'] ?? 1) ?>">
          </div>

          <div class="mb-3">
            <div class="form-check form-switch p-0 d-flex align-items-center gap-3 bg-white p-3 rounded border">
              <label class="form-check-label mb-0 fw-medium" for="is_active">Status Aktif</label>
              <?php $checked = ((int) ($item['is_active'] ?? 1) === 1) ? 'checked' : ''; ?>
              <input class="form-check-input ms-auto m-0" type="checkbox" role="switch" name="is_active" id="is_active"
                <?= $checked ?>>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-5 pt-3 border-top">
        <button class="btn btn-primary px-4" type="submit">
          <i class="fas fa-save me-2"></i>Simpan Slide
        </button>
        <a class="btn btn-outline-secondary px-4" href="<?= $baseUrl ?>/admin/home/hero">
          <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
      </div>
    </form>
  </div>
</div>

<script>
  // Image Preview Logic
  document.getElementById('imageInput').addEventListener('change', function (event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('imgPreviewContainer');
    const previewImage = document.getElementById('imgPreview');

    if (file) {
      const reader = new FileReader();

      reader.onload = function (e) {
        previewImage.src = e.target.result;
        previewContainer.style.display = 'block';
      }

      reader.readAsDataURL(file);
    } else {
      if (!this.value && !'<?= $currentImage ?>') {
        previewContainer.style.display = 'none';
      }
    }
  });

  // Save Confirmation with Form Change Detection
  const form = document.querySelector('.save-form');
  let formChanged = false;

  // Track form changes
  form.addEventListener('input', () => {
    formChanged = true;
  });

  form.addEventListener('change', () => {
    formChanged = true;
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    Swal.fire({
      title: 'Simpan Perubahan?',
      text: formChanged ? 'Perubahan pada slide akan disimpan.' : 'Tidak ada perubahan yang terdeteksi.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#0ea5e9',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Simpan!',
      cancelButtonText: 'Batal',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
  });
</script>