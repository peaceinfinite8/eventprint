<?php
$baseUrl = $baseUrl ?? ($vars['baseUrl'] ?? '/eventprint');
$categories = $categories ?? ($vars['categories'] ?? []);
$homeContent = $homeContent ?? ($vars['homeContent'] ?? []);
$csrfToken = $csrfToken ?? ($vars['csrfToken'] ?? '');
$stats = $stats ?? ($vars['stats'] ?? []);

$heroUrl = $baseUrl . '/admin/home/hero';
$contentUrl = $baseUrl . '/admin/home/content';
$previewUrl = $baseUrl . '/';

$heroTotal = (int) ($stats['hero_total'] ?? 0);
$heroActive = (int) ($stats['hero_active'] ?? 0);
$contactPct = (int) ($stats['contact_pct'] ?? 0);
$mappingPct = (int) ($stats['mapping_pct'] ?? 0);

$printId = (int) ($stats['print_id'] ?? 0);
$mediaId = (int) ($stats['media_id'] ?? 0);
$printName = (string) ($stats['print_name'] ?? '');
$mediaName = (string) ($stats['media_name'] ?? '');

$printCount = (int) ($stats['print_prod_count'] ?? 0);
$mediaCount = (int) ($stats['media_prod_count'] ?? 0);
$featCount = (int) ($stats['featured_count'] ?? 0);

$smallBannerTotal = (int) ($stats['small_banner_total'] ?? 0);
$smallBannerActive = (int) ($stats['small_banner_active'] ?? 0);

$testimonialTotal = (int) ($stats['testimonial_total'] ?? 0);
$testimonialActive = (int) ($stats['testimonial_active'] ?? 0);
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
  <div>
    <h1 class="h4 mb-1 fw-bold text-gradient">Konten Beranda</h1>
    <p class="text-muted small mb-0">Kelola semua elemen visual di halaman depan website</p>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-primary bg-white shadow-sm" href="<?= htmlspecialchars($previewUrl) ?>" target="_blank"
      rel="noopener">
      <i class="fas fa-external-link-alt me-2"></i>Live Preview
    </a>
  </div>
</div>

<?php if ($heroActive <= 0): ?>
  <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4">
    <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
    <div>
      <strong>Hero Slides kosong atau tidak aktif!</strong> Pengunjung akan melihat area kosong di bagian atas.
      <a href="<?= htmlspecialchars($heroUrl) ?>" class="alert-link text-decoration-none ms-1">Fix Now</a>
    </div>
  </div>
<?php endif; ?>

<div class="row g-4 mb-4">
  <!-- Hero Slides Card -->
  <div class="col-12 col-lg-4">
    <div class="dash-container-card h-100 p-4 position-relative overflow-hidden">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div class="icon-circle bg-primary bg-opacity-10 text-primary mb-3">
          <i class="fas fa-images"></i>
        </div>
        <span class="badge bg-light text-dark border">Total: <?= $heroTotal ?></span>
      </div>
      <h5 class="fw-bold mb-2">Hero Slides</h5>
      <p class="text-muted small mb-4">Banner utama (carousel) di bagian paling atas halaman depan.</p>

      <div class="d-flex align-items-center justify-content-between mt-auto">
        <div class="small text-muted">Active: <span
            class="fw-bold text-<?= $heroActive > 0 ? 'success' : 'danger' ?>"><?= $heroActive ?></span></div>
        <a href="<?= htmlspecialchars($heroUrl) ?>" class="btn btn-sm btn-primary rounded-pill px-3">
          Kelola <i class="fas fa-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Mapping Access Card -->
  <div class="col-12 col-lg-4">
    <div class="dash-container-card h-100 p-4 position-relative overflow-hidden">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div class="icon-circle bg-info bg-opacity-10 text-info mb-3">
          <i class="fas fa-sitemap"></i>
        </div>
        <span class="badge bg-light text-dark border"><?= $mappingPct ?>% Setup</span>
      </div>
      <h5 class="fw-bold mb-2">Category Mapping</h5>
      <p class="text-muted small mb-4">Tentukan kategori produk untuk section Print & Media.</p>

      <div class="d-flex align-items-center justify-content-between mt-auto">
        <div class="small text-muted">
          Print: <strong><?= $printCount ?></strong> | Media: <strong><?= $mediaCount ?></strong>
        </div>
        <a href="#mapping-section" class="btn btn-sm btn-info text-white rounded-pill px-3">
          Atur <i class="fas fa-arrow-down ms-1"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Testimonials Card -->
  <div class="col-12 col-lg-4">
    <div class="dash-container-card h-100 p-4 position-relative overflow-hidden">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div class="icon-circle bg-warning bg-opacity-10 text-warning mb-3">
          <i class="fas fa-comment-alt"></i>
        </div>
        <span class="badge bg-light text-dark border">Total: <?= $testimonialTotal ?></span>
      </div>
      <h5 class="fw-bold mb-2">Testimonials</h5>
      <p class="text-muted small mb-4">Ulasan pelanggan untuk membangun kepercayaan.</p>

      <div class="d-flex align-items-center justify-content-between mt-auto">
        <div class="small text-muted">Active: <span
            class="fw-bold text-<?= $testimonialActive > 0 ? 'success' : 'danger' ?>"><?= $testimonialActive ?></span>
        </div>
        <a href="<?= $baseUrl ?>/admin/testimonials" class="btn btn-sm btn-warning text-dark rounded-pill px-3">
          Kelola <i class="fas fa-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-5">
  <!-- Why Choose Us -->
  <div class="col-md-6">
    <div class="dash-container-card p-3 d-flex align-items-center gap-3 h-100">
      <div class="icon-square bg-success bg-opacity-10 text-success rounded-3 p-3">
        <i class="fas fa-check-circle fa-lg"></i>
      </div>
      <div class="flex-grow-1">
        <h6 class="fw-bold mb-1">Why Choose Us</h6>
        <p class="text-muted small mb-0">Judul, deskripsi & ilustrasi keunggulan.</p>
      </div>
      <a href="<?= $baseUrl ?>/admin/home/why-choose" class="btn btn-icon btn-light text-primary">
        <i class="fas fa-pencil-alt"></i>
      </a>
    </div>
  </div>

  <!-- Small Banners -->
  <div class="col-md-6">
    <div class="dash-container-card p-3 d-flex align-items-center gap-3 h-100">
      <div class="icon-square bg-danger bg-opacity-10 text-danger rounded-3 p-3">
        <i class="fas fa-ad fa-lg"></i>
      </div>
      <div class="flex-grow-1">
        <h6 class="fw-bold mb-1">Small Banners (Promo)</h6>
        <p class="text-muted small mb-0"><?= $smallBannerActive ?> Banner Aktif</p>
      </div>
      <a href="<?= $baseUrl ?>/admin/home/small-banner" class="btn btn-icon btn-light text-primary">
        <i class="fas fa-pencil-alt"></i>
      </a>
    </div>
  </div>

  <!-- Store Info Update -->
  <div class="col-md-6">
    <div class="dash-container-card p-3 d-flex align-items-center gap-3 h-100">
      <div class="icon-square bg-secondary bg-opacity-10 text-secondary rounded-3 p-3">
        <i class="fas fa-info-circle fa-lg"></i>
      </div>
      <div class="flex-grow-1">
        <h6 class="fw-bold mb-1">Info Kontak & Store</h6>
        <p class="text-muted small mb-0">Alamat, Email, WhatsApp di Footer/Home.</p>
      </div>
      <a href="<?= $baseUrl ?>/admin/home/content" class="btn btn-icon btn-light text-primary">
        <i class="fas fa-pencil-alt"></i>
      </a>
    </div>
  </div>
</div>

<!-- Section Mapping Kategori -->
<div id="mapping-section" class="dash-container-card fade-in">
  <div class="card-header bg-white border-bottom p-4">
    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-sitemap me-2"></i>Mapping Kategori Homepage</h5>
  </div>
  <div class="p-4">
    <p class="text-muted mb-4">
      Pilih kategori produk yang ingin ditampilkan secara otomatis pada section di halaman depan.
    </p>

    <form method="post" id="sectionsForm" action="<?= $baseUrl ?>/admin/home/category-map"
      enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

      <!-- Container for Dynamic Sections -->
      <div id="sections-container" class="d-flex flex-column gap-3">
        <!-- Rows will be injected here by JS -->
      </div>

      <div class="mt-3">
        <button type="button" class="btn btn-outline-primary dashed-border w-100" onclick="addSection()">
          <i class="fas fa-plus me-2"></i> Tambah Section Baru
        </button>
      </div>

      <div class="mt-4 text-end">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save me-2"></i> Simpan Mapping
        </button>
      </div>
    </form>
  </div>
</div>

<?php
// Prepare Data for JS
$savedSections = json_decode($homeContent['home_sections'] ?? '[]', true);
if (empty($savedSections)) {
  // Default Fallback if empty
  $savedSections = [
    ['id' => uniqid('sec_'), 'label' => 'Section 1', 'category_id' => 0, 'theme' => 'red', 'layout' => 'standard', 'image' => '']
  ];
}
?>

<style>
  /* Smart Card UI */
  .smart-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    transition: all 0.2s ease;
    margin-bottom: 24px;
    overflow: visible;
    /* Allow dropdowns */
  }

  .smart-card:hover {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    border-color: #cbd5e1;
  }

  .smart-card-header {
    background: #f8fafc;
    border-bottom: 1px solid #edf2f7;
    padding: 16px 20px;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .smart-card-body {
    padding: 24px;
  }

  /* Theme Chips */
  .theme-options {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .theme-chip {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.2s;
    position: relative;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .theme-chip.active {
    transform: scale(1.15);
    border-color: #fff;
    box-shadow: 0 0 0 2px #3b82f6;
    /* Focus ring */
    z-index: 2;
  }

  .theme-chip:hover {
    transform: scale(1.1);
  }

  /* Theme Colors */
  .bg-theme-red {
    background: linear-gradient(135deg, #ef4444, #b91c1c);
  }

  .bg-theme-blue {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  }

  .bg-theme-green {
    background: linear-gradient(135deg, #10b981, #047857);
  }

  .bg-theme-orange {
    background: linear-gradient(135deg, #f97316, #c2410c);
  }

  .bg-theme-purple {
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
  }

  .bg-theme-pink {
    background: linear-gradient(135deg, #ec4899, #be185d);
  }

  .bg-theme-teal {
    background: linear-gradient(135deg, #14b8a6, #0f766e);
  }

  .bg-theme-yellow {
    background: linear-gradient(135deg, #eab308, #a16207);
  }

  .bg-theme-gray {
    background: linear-gradient(135deg, #64748b, #334155);
  }

  .bg-theme-black {
    background: linear-gradient(135deg, #1e293b, #000000);
  }

  /* Layout Visual Toggle */
  .layout-toggle {
    display: flex;
    background: #f1f5f9;
    padding: 4px;
    border-radius: 8px;
    width: fit-content;
  }

  .layout-btn {
    padding: 6px 16px;
    border-radius: 6px;
    border: none;
    background: transparent;
    color: #64748b;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s;
  }

  .layout-btn.active {
    background: #fff;
    color: #0f172a;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }

  .layout-btn:hover:not(.active) {
    color: #334155;
    background: rgba(255, 255, 255, 0.5);
  }

  /* Custom File Upload */
  .custom-file-upload {
    border: 2px dashed #cbd5e1;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    background: #f8fafc;
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100px;
  }

  .custom-file-upload:hover {
    border-color: #3b82f6;
    background: #eff6ff;
  }

  .custom-file-upload i {
    color: #94a3b8;
    margin-bottom: 8px;
  }

  .custom-file-upload span {
    color: #64748b;
    font-size: 0.85rem;
  }

  .custom-file-upload.drag-over {
    border-color: #3b82f6;
    background-color: #e0f2fe;
    transform: scale(1.02);
  }

  .custom-file-upload.has-file {
    border-style: solid;
    border-color: #3b82f6;
    background: #eff6ff;
  }

  /* Section Title Input */
  .section-title-input {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    border: 1px dashed transparent;
    border-radius: 6px;
    padding: 4px 12px;
    transition: all 0.2s;
    background: transparent;
    width: auto;
    min-width: 200px;
    max-width: 400px;
  }

  .section-title-input:hover {
    border-color: #cbd5e1;
    background: #fff;
  }

  .section-title-input:focus {
    border-color: #3b82f6;
    background: #fff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }

  /* Overlay Visuals */
  .overlay-option {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px;
    cursor: pointer;
    transition: all 0.2s;
    background: #fff;
    position: relative;
    overflow: hidden;
  }

  .overlay-option:hover {
    border-color: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  }

  .overlay-option.active {
    border-color: #3b82f6;
    background-color: #eff6ff;
    box-shadow: 0 0 0 1px #3b82f6;
  }

  .overlay-preview-box {
    height: 60px;
    width: 100%;
    border-radius: 6px;
    margin-bottom: 8px;
    background-color: #ddd;
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    align-items: flex-end;
    padding: 6px;
  }

  /* Mock background for preview */
  .overlay-preview-box.bg-mock {
    background-image: url('https://placehold.co/400x150/e2e8f0/ffffff?text=Image');
  }

  .overlay-preview-box .mock-text {
    color: #fff;
    font-size: 0.65rem;
    font-weight: bold;
    z-index: 2;
    position: relative;
  }

  .overlay-gradient {
    position: absolute;
    inset: 0;
    border-radius: 6px;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.6) 100%);
  }

  .overlay-none {
    /* No overlay */
  }

  /* Premium Modern Color Picker */
  .premium-color-picker {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    transition: all 0.2s;
  }

  .premium-color-picker:focus-within {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
  }

  .color-trigger-wrapper {
    position: relative;
    width: 42px;
    height: 42px;
    flex-shrink: 0;
  }

  .color-picker-hidden {
    position: absolute;
    inset: 0;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    z-index: 2;
  }

  .color-visual-preview {
    width: 100%;
    height: 100%;
    border-radius: 10px;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px #e2e8f0;
    transition: transform 0.2s;
  }

  .color-trigger-wrapper:hover .color-visual-preview {
    transform: scale(1.05);
    box-shadow: 0 0 0 2px #cbd5e1;
  }

  .hex-input-group {
    flex-grow: 1;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .hex-hash {
    color: #94a3b8;
    font-weight: bold;
    font-size: 1.1rem;
  }

  .hex-input-clean {
    border: none;
    background: transparent;
    font-family: monospace;
    font-size: 1rem;
    font-weight: 600;
    color: #334155;
    width: 100%;
    text-transform: uppercase;
    outline: none;
  }

  .hex-input-clean::placeholder {
    color: #cbd5e1;
  }

  /* Modern Banner Preview */
  .banner-preview-container {
    width: 100%;
    height: 180px;
    border-radius: 12px;
    position: relative;
    background-size: cover;
    background-position: center;
    border: 2px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.2s;
    background-color: #f8fafc;
  }

  .banner-preview-container:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  }

  .banner-preview-overlay {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.0);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    opacity: 0;
    transition: all 0.3s ease;
    backdrop-filter: blur(0px);
  }

  .banner-preview-container:hover .banner-preview-overlay {
    background: rgba(15, 23, 42, 0.6);
    opacity: 1;
    backdrop-filter: blur(2px);
  }

  .btn-action-preview {
    transform: translateY(10px);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    opacity: 0;
  }

  .banner-preview-container:hover .btn-action-preview {
    transform: translateY(0);
    opacity: 1;
  }

  .file-input-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
  }
</style>

<script>
  let sectionsData = <?= json_encode($savedSections) ?>;
  const categories = <?= json_encode($categories) ?>;
  const baseUrl = "<?= $baseUrl ?>";

  // Theme Labels Mapping (Fix: Was missing)
  const themeLabels = {
    'red': 'Red Passion',
    'blue': 'Ocean Blue',
    'green': 'Nature Green',
    'orange': 'Sunset Orange',
    'purple': 'Royal Purple',
    'pink': 'Hot Pink',
    'teal': 'Teal Breeze',
    'yellow': 'Sunny Yellow',
    'gray': 'Slate Gray',
    'black': 'Midnight Black'
  };


  function updateSectionData(index, field, value) {
    if (sectionsData[index]) {
      sectionsData[index][field] = value;
    }
  }

  // File Preview Handler
  function handleFileSelection(input, index) {
    if (input.files && input.files[0]) {
      const file = input.files[0];

      // Validate
      if (file.size > 2 * 1024 * 1024) {
        Swal.fire({ icon: 'error', title: 'File Too Large', text: 'Max 2MB', timer: 2000, showConfirmButton: false });
        input.value = ''; // Reset
        return;
      }

      // Generate Preview
      const reader = new FileReader();
      reader.onload = function (e) {
        sectionsData[index].temp_preview = e.target.result;
        renderSections();
      };
      reader.readAsDataURL(file);
    }
  }

  /* DRAG & DROP HANDLERS */
  function handleDragOver(e, index) {
    e.preventDefault();
    e.stopPropagation();
    const label = document.getElementById(`upload_label_${index}`);
    if (label) label.classList.add('drag-over');
  }

  function handleDragLeave(e, index) {
    e.preventDefault();
    e.stopPropagation();
    const label = document.getElementById(`upload_label_${index}`);
    if (label) label.classList.remove('drag-over');
  }

  function handleDrop(e, index) {
    e.preventDefault();
    e.stopPropagation();

    const label = document.getElementById(`upload_label_${index}`);
    if (label) label.classList.remove('drag-over');

    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      const file = e.dataTransfer.files[0];

      // Validate Type
      const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
      if (!validTypes.includes(file.type)) {
        Swal.fire({
          icon: 'error',
          title: 'Format Salah',
          text: 'Harap upload gambar JPG, PNG, atau WebP.',
          timer: 3000,
          showConfirmButton: false
        });
        return;
      }

      // Assign to Input
      const input = document.getElementById(`file_input_${index}`);
      const dt = new DataTransfer();
      dt.items.add(file);
      input.files = dt.files;

      // Trigger Preview
      handleFileSelection(input, index);
    }
  }

  // New Smart Render Function
  function renderSections() {
    const container = document.getElementById('sections-container');
    container.innerHTML = '';

    sectionsData.forEach((sec, index) => {
      const themes = ['red', 'blue', 'green', 'orange', 'purple', 'pink', 'teal', 'yellow', 'gray', 'black'];

      const row = document.createElement('div');
      row.className = 'smart-card fade-in';

      // Image Logic
      const hasImage = sec.image || sec.temp_preview;
      const imageUrl = sec.temp_preview ? sec.temp_preview : (sec.image ? `${baseUrl}/${sec.image}` : '');

      let imageHtml = '';

      if (hasImage) {
        // FILLED STATE: Show Preview Card
        imageHtml = `
            <div class="banner-preview-container shadow-sm" style="background-image: url('${imageUrl}');">
                <div class="banner-preview-overlay">
                    <button type="button" class="btn btn-light btn-sm fw-bold shadow-sm btn-action-preview" onclick="document.getElementById('file_input_${index}').click()">
                        <i class="fas fa-pencil-alt text-primary me-2"></i> Ganti
                    </button>
                    <button type="button" class="btn btn-danger btn-sm fw-bold shadow-sm btn-action-preview" onclick="deleteImage(${index})">
                        <i class="fas fa-trash-alt me-2"></i> Hapus
                    </button>
                </div>
                <div class="position-absolute bottom-0 start-0 p-3 w-100 bg-gradient-to-t from-black/50 to-transparent">
                     <span class="badge bg-black bg-opacity-50 border border-white border-opacity-25 backdrop-blur-sm">
                        <i class="fas fa-image me-1"></i> ${sec.temp_preview ? 'Preview Upload Baru' : 'Banner Aktif'}
                     </span>
                </div>
            </div>
            <input type="file" id="file_input_${index}" class="d-none" name="sections[${index}][image]" accept="image/*" onchange="handleFileSelection(this, ${index})">
          `;
      } else {
        // EMPTY STATE: Show Upload Box
        imageHtml = `
            <input type="file" id="file_input_${index}" class="d-none" name="sections[${index}][image]" accept="image/*" onchange="handleFileSelection(this, ${index})">
            <label for="file_input_${index}" 
                   class="custom-file-upload h-100" 
                   id="upload_label_${index}"
                   ondragover="handleDragOver(event, ${index})" 
                   ondragleave="handleDragLeave(event, ${index})" 
                   ondrop="handleDrop(event, ${index})">
                <div class="py-4">
                    <div class="mb-3 text-primary bg-primary bg-opacity-10 rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-cloud-upload-alt fa-2x"></i>
                    </div>
                    <div class="fw-bold text-dark mb-1">Klik atau Drop Banner Disini</div>
                    <div class="text-xs text-muted">JPG, PNG, WebP (Max 2MB)</div>
                </div>
            </label>
          `;
      }

      // Pre-calc background for Overlay Preview
      const currentImageBg = hasImage ? `style="background-image: url('${imageUrl}');"` : 'style="background-image: url(\'https://placehold.co/400x150/e2e8f0/94a3b8?text=No+Image\');"';

      const isOverlayLight = (sec.overlay_style === 'light');
      const currentTheme = sec.theme || 'red';
      const currentThemeLabel = themeLabels[currentTheme] || 'Red Passion';

      row.innerHTML = `
        <div class="smart-card-header">
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <div class="d-flex flex-column">
                     <label class="text-secondary fw-bold text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Label Section (Admin)</label>
                     <div class="d-flex align-items-center">
                          <span class="badge bg-light text-dark border me-3">#${index + 1}</span>
                          <div class="position-relative">
                               <input type="text" 
                                   class="section-title-input" 
                                   name="sections[${index}][label]" 
                                   value="${sec.label || 'New Section'}" 
                                   placeholder="Beri Nama Section..."
                                   oninput="updateSectionData(${index}, 'label', this.value)">
                               <i class="fas fa-pencil-alt text-muted position-absolute end-0 top-50 translate-middle-y me-2 pe-none small opacity-50"></i>
                          </div>
                     </div>
                </div>
                <input type="hidden" name="sections[${index}][id]" value="${sec.id || ''}">
                <input type="hidden" name="sections[${index}][existing_image]" id="existing_image_${index}" value="${sec.image || ''}">
            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-icon text-muted" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                    <li><h6 class="dropdown-header">Aksi</h6></li>
                    <li>
                        <button type="button" class="dropdown-item text-danger small" onclick="removeSection(${index})">
                            <i class="fas fa-trash-alt me-2"></i> Hapus Section
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="smart-card-body">
            <div class="row g-4">
                <!-- Primary Settings -->
                <div class="col-md-5">
                    <label class="form-label fw-bold text-muted small text-uppercase">Kategori Produk</label>
                    <select class="form-select" name="sections[${index}][category_id]" onchange="updateSectionData(${index}, 'category_id', this.value)">
                        <option value="0" class="text-muted">- Pilih Kategori -</option>
                        ${categories.map(c => {
        const count = c.product_count !== undefined ? c.product_count : 0;
        return `<option value="${c.id}" ${c.id == sec.category_id ? 'selected' : ''}>${c.name} (${count} produk)</option>`;
      }).join('')}
                    </select>
                    <div class="form-text small text-muted">Produk dari kategori ini akan muncul otomatis.</div>
                </div>

                <div class="col-md-7">
                    <label class="form-label fw-bold text-muted small text-uppercase mb-2">Banner Image</label>
                    ${imageHtml}
                </div>

                <!-- Text Customization -->
                <div class="col-12">
                     <div class="p-3 bg-white rounded border border-light shadow-sm">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-3">
                            <i class="fas fa-font me-2"></i>Kustomisasi Teks (Opsional)
                        </label>
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="small text-muted mb-1">Judul Banner</label>
                                <input type="text" class="form-control form-control-sm" 
                                       name="sections[${index}][custom_title]"
                                       placeholder="Kosong = Nama Kategori"
                                       value="${sec.custom_title || ''}"
                                       oninput="updateSectionData(${index}, 'custom_title', this.value)">
                            </div>
                            <div class="col-md-7">
                                <label class="small text-muted mb-1">Deskripsi</label>
                                <input type="text" class="form-control form-control-sm" 
                                       name="sections[${index}][custom_description]"
                                       placeholder="Kosong = Tidak muncul"
                                       value="${sec.custom_description || ''}"
                                       oninput="updateSectionData(${index}, 'custom_description', this.value)">
                            </div>
                        </div>
                     </div>
                </div>

                <hr class="my-2 border-light">

                <!-- Visual Settings -->
                <div class="col-md-7">
                    <label class="form-label fw-bold text-muted small text-uppercase mb-3">Custom Theme Color</label>
                    
                    <!-- Hidden input stores the final value -->
                    <input type="hidden" name="sections[${index}][theme]" id="theme_input_${index}" value="${currentTheme}">
                    
                    <div class="premium-color-picker">
                        <!-- Color Trigger -->
                        <div class="color-trigger-wrapper">
                            <input type="color" 
                                   class="color-picker-hidden" 
                                   id="color_picker_${index}" 
                                   value="${currentTheme.startsWith('#') ? currentTheme : '#ef4444'}" 
                                   title="Choose Color"
                                   oninput="updateColor(${index}, this.value)">
                            <div class="color-visual-preview" 
                                 id="color_preview_${index}" 
                                 style="background: ${currentTheme.startsWith('#') ? currentTheme : 'linear-gradient(135deg, #ef4444, #b91c1c)'};"></div>
                        </div>

                        <!-- Hex Input Group -->
                        <div class="hex-input-group">
                            <span class="hex-hash">#</span>
                            <input type="text" 
                                   class="hex-input-clean" 
                                   id="hex_input_${index}" 
                                   value="${currentTheme.startsWith('#') ? currentTheme.substring(1) : currentTheme}" 
                                   maxlength="6"
                                   placeholder="RRGGBB"
                                   oninput="updateColor(${index}, this.value)">
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-bold text-muted small text-uppercase mb-3">Posisi Banner</label>
                    <input type="hidden" name="sections[${index}][layout]" id="layout_input_${index}" value="${sec.layout || 'standard'}">
                    <div class="layout-toggle">
                        <button type="button" class="layout-btn ${(sec.layout === 'standard' || !sec.layout) ? 'active' : ''}" 
                                onclick="setLayout(${index}, 'standard')">
                            <i class="fas fa-align-left me-2"></i> Kiri
                        </button>
                        <button type="button" class="layout-btn ${sec.layout === 'reverse' ? 'active' : ''}" 
                                onclick="setLayout(${index}, 'reverse')">
                             Kanan <i class="fas fa-align-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Inline Advanced Settings Collapsible -->
            <div class="mt-4">
                <a class="advanced-settings-btn" data-bs-toggle="collapse" href="#advanced_${index}" role="button" aria-expanded="false">
                    <i class="fas fa-sliders-h"></i> Pengaturan Lanjutan
                </a>
                <div class="collapse mt-3" id="advanced_${index}">
                    <div class="p-3 bg-light rounded border border-light">
                        <div class="row">
                             <div class="col-md-8">
                                <label class="small fw-bold text-dark mb-3 d-block">Overlay Style (Readability Text)</label>
                                <input type="hidden" name="sections[${index}][overlay_style]" id="overlay_input_${index}" value="${sec.overlay_style || 'dark'}">
                                
                                <div class="row g-3">
                                    <!-- Option: Dark (Default) -->
                                    <div class="col-6 col-md-4">
                                        <div class="overlay-option ${(!isOverlayLight) ? 'active' : ''}" onclick="setOverlay(${index}, 'dark')">
                                            <div class="overlay-preview-box" ${currentImageBg}>
                                                <div class="overlay-gradient"></div>
                                                <div class="mock-text">Text Terbaca</div>
                                            </div>
                                            <div class="text-center small fw-bold">Gelap (Default)</div>
                                            <div class="text-center text-xs text-muted">Gradient hitam agar teks putih jelas.</div>
                                        </div>
                                    </div>

                                    <!-- Option: Light/None -->
                                    <div class="col-6 col-md-4">
                                        <div class="overlay-option ${(isOverlayLight) ? 'active' : ''}" onclick="setOverlay(${index}, 'light')">
                                            <div class="overlay-preview-box" ${currentImageBg}>
                                                <div class="overlay-none"></div>
                                                <div class="mock-text" style="color: #000; text-shadow: 0 0 2px #fff;">Text Original</div>
                                            </div>
                                            <div class="text-center small fw-bold">Terang / Asli</div>
                                            <div class="text-center text-xs text-muted">Tanpa gradient (sesuai foto asli).</div>
                                        </div>
                                    </div>
                                </div>

                             </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
      `;
      container.appendChild(row);
    });
  }

  // Helper Functions for Visual Controls
  function updateColor(index, val) {
    let hex = val;

    // If typing in hex input, add hash for internal logic
    // But we must assume 'val' could be either full hex (from picker) or partial (from text)
    if (!val.startsWith('#')) {
      hex = '#' + val;
    }

    // Allow valid hex only for syncing color picker
    // Check 3 or 6 chars
    const isValidHex = /^#([0-9A-F]{3}){1,2}$/i.test(hex);

    // Update data model
    if (sectionsData[index]) {
      sectionsData[index].theme = hex;
    }

    // Update DOM Elements
    const hiddenInput = document.getElementById(`theme_input_${index}`);
    const colorPicker = document.getElementById(`color_picker_${index}`);
    const hexInput = document.getElementById(`hex_input_${index}`);
    const previewBox = document.getElementById(`color_preview_${index}`);

    // 1. Sync Text Input (Show clean hex without hash)
    if (hexInput && document.activeElement !== hexInput) {
      hexInput.value = hex.replace('#', '').toUpperCase();
    }

    // 2. Sync Color Picker (Needs valid #RRGGBB)
    if (colorPicker && isValidHex) {
      // Input range expects 6 digits hex usually
      // Expand 3 digit to 6 if needed for input[type=color] compliance
      let fullHex = hex;
      if (hex.length === 4) {
        fullHex = '#' + hex[1] + hex[1] + hex[2] + hex[2] + hex[3] + hex[3];
      }
      colorPicker.value = fullHex;
    }

    // 3. Sync Preview Box & Hidden Input
    if (hiddenInput) hiddenInput.value = hex;
    if (previewBox) previewBox.style.background = hex;
  }

  function setLayout(index, layout) {
    sectionsData[index].layout = layout;
    renderSections();
  }



  function setOverlay(index, style) {
    sectionsData[index].overlay_style = style;
    renderSections();
  }

  function deleteImage(index) {
    if (sectionsData[index]) {
      // Clear logical flags
      sectionsData[index].image = '';
      sectionsData[index].temp_preview = '';

      // Clear Input
      const input = document.getElementById(`file_input_${index}`);
      if (input) input.value = '';

      // Re-render
      renderSections();

      // Notify
      Toast.fire({ icon: 'info', title: 'Banner dihapus' });
    }
  }


  // SweetAlert2 Toast Mixin
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  });

  function addSection() {
    sectionsData.push({
      id: 'sec_' + Math.random().toString(36).substr(2, 9),
      label: 'New Section',
      category_id: 0,
      theme: 'red',
      layout: 'standard'
    });
    renderSections();

    Toast.fire({
      icon: 'success',
      title: 'Section berhasil ditambahkan'
    });
  }

  function removeSection(index) {
    Swal.fire({
      title: 'Hapus section ini?',
      text: "Data section akan dihapus dari daftar mapping!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        sectionsData.splice(index, 1);
        renderSections();
        Toast.fire({
          icon: 'success',
          title: 'Section telah dihapus'
        });
      }
    });
  }

  // Init
  document.addEventListener('DOMContentLoaded', renderSections);
</script>

<!-- SweetAlert2 Toast Logic for Flash Messages -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });

    <?php if (!empty($vars['flash']['success'])): ?>
      Toast.fire({
        icon: 'success',
        title: '<?= addslashes($vars['flash']['success']) ?>'
      });
    <?php endif; ?>

    <?php if (!empty($vars['flash']['error'])): ?>
      Toast.fire({
        icon: 'error',
        title: '<?= addslashes($vars['flash']['error']) ?>'
      });
    <?php endif; ?>
  });
</script>