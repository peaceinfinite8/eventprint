// ============================================
// EventPrint - Product Detail Page Renderer
// ============================================

let productData = null;
let selectedMaterialId = null;
let selectedLaminationId = null;
let quantity = 1;
let uploadedFileName = null;
let currentNote = '';

/**
 * Initialize Product Detail page
 */
async function initProductDetailPage() {
  try {
    const slug = getProductSlugFromURL();

    if (!slug) {
      renderProductNotFound('Produk tidak ditemukan. URL tidak valid.');
      return;
    }

    // Show loading
    showLoading('productDetailContent', 1);

    // Simulate loading delay
    await new Promise(resolve => setTimeout(resolve, 400));

    // Load all products from products.json
    // PATH REVISED: From /views/product-detail.html to /data/products.json is ../data/products.json
    const productsData = await loadData('../data/products.json');

    if (!productsData || !productsData.products) {
      console.error('Failed to load products data structure');
      renderProductNotFound('Gagal memuat data produk (System Error).');
      return;
    }

    console.log(`[ProductDetail] Searching for slug: "${slug}"`);
    console.log(`[ProductDetail] Available products:`, productsData.products.length);

    // Find product by slug
    const product = productsData.products.find(p => p.slug === slug);

    if (!product) {
      console.warn(`[ProductDetail] Product not found for slug: ${slug}`);
      renderProductNotFound(`Produk dengan slug "${slug}" tidak ditemukan.`);
      return;
    }

    productData = product;

    // Set default selections based on enabled options
    if (product.options.materials.enabled && product.options.materials.items.length > 0) {
      selectedMaterialId = product.options.materials.items[0].id;
    } else {
      selectedMaterialId = null;
    }

    if (product.options.laminations.enabled && product.options.laminations.items.length > 0) {
      selectedLaminationId = product.options.laminations.items[0].id;
    } else {
      selectedLaminationId = null;
    }

    quantity = 1;

    // Render page
    renderProductDetail();

  } catch (error) {
    console.error('Error loading product detail:', error);
    renderProductNotFound('Gagal memuat produk. Silakan coba lagi.');
  }
}

/**
 * Get product slug from URL query string
 */
function getProductSlugFromURL() {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get('slug');
}

/**
 * Render product not found state
 */
function renderProductNotFound(message) {
  const container = document.getElementById('productDetailContent');
  container.innerHTML = `
    <div class="product-not-found">
      <h2>Produk Tidak Ditemukan</h2>
      <p>${message}</p>
      <a href="products.html" class="btn btn-primary">Kembali ke All Product</a>
    </div>
  `;
}

/**
 * Render complete product detail page
 */
function renderProductDetail() {
  const container = document.getElementById('productDetailContent');

  const html = `
    <!-- 3 Column Layout -->
    <div class="product-detail-container">
      <!-- Column 1: Gallery -->
      <div class="gallery-section">
        <div id="mainImage" class="main-image">
          ${productData.images[0] ? `<img src="${productData.images[0]}" alt="${productData.name}">` : '<span>Gambar Produk</span>'}
        </div>
        <div class="thumbnail-list">
          ${productData.images.slice(1, 4).map((img, index) => `
            <div class="thumbnail ${index === 0 ? 'active' : ''}" onclick="switchMainImage(${index + 1})" data-index="${index + 1}">
              ${img ? `<img src="${img}" alt="Thumbnail ${index + 1}">` : '<span>Gambar Produk</span>'}
            </div>
          `).join('')}
        </div>
      </div>
      
      <!-- Column 2: Options -->
      <div class="options-section">
        <h1>${productData.name}</h1>
        <div class="price-display" id="displayPrice">${formatPrice(productData.base_price)}</div>
        
        ${renderMarketplaceCTAs(productData.marketplace)}
        
        ${productData.options.materials.enabled ? `
        <!-- Material Selection -->
        <div class="option-group">
          <label class="option-label">Pilih Bahan</label>
          <div class="chips-container">
            ${productData.options.materials.items.map(material => `
              <button class="chip ${material.id === selectedMaterialId ? 'active' : ''}" 
                      onclick="selectMaterial('${material.id}')">
                ${material.name}
              </button>
            `).join('')}
          </div>
        </div>
        ` : ''}
        
        ${productData.options.laminations.enabled ? `
        <!-- Lamination Selection -->
        <div class="option-group">
          <label class="option-label">Pilih Laminasi</label>
          <div class="chips-container">
            ${productData.options.laminations.items.map(lam => `
              <button class="chip ${lam.id === selectedLaminationId ? 'active' : ''}" 
                      onclick="selectLamination('${lam.id}')">
                ${lam.name}
              </button>
            `).join('')}
          </div>
        </div>
        ` : ''}
        
        <!-- Note Textarea -->
        <div class="option-group">
          <label class="option-label">Keterangan (Opsional)</label>
          <textarea class="note-textarea" 
                    id="productNote" 
                    placeholder="Tambahkan catatan untuk pesanan Anda..."
                    oninput="updateNote(this.value)"></textarea>
        </div>
        
        <!-- File Upload -->
        <div class="option-group">
          <label class="option-label">Upload File Siap Cetak</label>
          <div class="file-upload-wrapper">
            <label class="file-upload-btn">
              <span>📁 Pilih File</span>
              <input type="file" 
                     accept="${productData.upload_rules.accept.join(',')}" 
                     onchange="handleFileUpload(event)">
            </label>
            <div id="fileStatus"></div>
          </div>
        </div>
      </div>
      
      <!-- Column 3: Checkout Box -->
      <div class="checkout-box">
        <h3 class="checkout-title">Atur Jumlah dan Catatan</h3>
        
        <div class="quantity-stepper">
          <span class="quantity-label">Quantity :</span>
          <div class="stepper-controls">
            <button class="stepper-btn" onclick="decreaseQuantity()">-</button>
            <span class="quantity-value" id="quantityValue">1</span>
            <button class="stepper-btn" onclick="increaseQuantity()">+</button>
          </div>
        </div>
        
        <div class="subtotal-row">
          <span class="subtotal-label">Subtotal</span>
          <span class="subtotal-value" id="subtotalValue">${formatPrice(productData.base_price)}</span>
        </div>
        
        <button class="checkout-btn" onclick="handleCheckout()">Beli Sekarang</button>
      </div>
    </div>
    
    <!-- Info Sections Below -->
    <div class="info-sections">
      <!-- Production Time -->
      ${productData.work_time && productData.work_time.length > 0 ? `
        <div class="info-section">
          <h3>Lama Pengerjaan</h3>
          <ul>
            ${productData.work_time.map(item => `<li>${item}</li>`).join('')}
          </ul>
        </div>
      ` : ''}
      
      <!-- Notes -->
      ${productData.notes && productData.notes.length > 0 ? `
        <div class="info-section">
          <h3>Catatan</h3>
          <ul>
            ${productData.notes.map(item => `<li>${item}</li>`).join('')}
          </ul>
        </div>
      ` : ''}
      
      <!-- Product Description -->
      ${productData.description && productData.description.length > 0 ? `
        <div class="info-section">
          <h3>Keterangan Produk</h3>
          ${productData.description.map(p => `<p>${p}</p>`).join('')}
        </div>
      ` : ''}
      
      <!-- Specifications -->
      ${productData.specs && productData.specs.length > 0 ? `
        <div class="info-section">
          <h3>Spesifikasi</h3>
          <ul>
            ${productData.specs.map(item => `<li>${item}</li>`).join('')}
          </ul>
        </div>
      ` : ''}
    </div>
  `;

  container.innerHTML = html;
}

/**
 * Render marketplace CTAs (Shopee, Tokopedia)
 */
function renderMarketplaceCTAs(marketplace) {
  if (!marketplace) return '';

  const buttons = [];

  // Shopee button
  if (marketplace.shopee && marketplace.shopee.enabled && marketplace.shopee.url) {
    buttons.push(`
      <a href="${marketplace.shopee.url}" 
         target="_blank" 
         rel="noopener noreferrer"
         class="marketplace-btn shopee">
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M21 8c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2s.9-2 2-2h14c1.1 0 2 .9 2 2zM3 11v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7"/>
        </svg>
        Shopee
      </a>
    `);
  }

  // Tokopedia button
  if (marketplace.tokopedia && marketplace.tokopedia.enabled && marketplace.tokopedia.url) {
    buttons.push(`
      <a href="${marketplace.tokopedia.url}" 
         target="_blank" 
         rel="noopener noreferrer"
         class="marketplace-btn tokopedia">
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M21 8c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2s.9-2 2-2h14c1.1 0 2 .9 2 2zM3 11v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7"/>
        </svg>
        Tokopedia
      </a>
    `);
  }

  if (buttons.length === 0) return '';

  return `
    <div class="marketplace-ctas">
      ${buttons.join('')}
    </div>
  `;
}

/**
 * Switch main image when thumbnail clicked
 */
function switchMainImage(index) {
  const mainImage = document.getElementById('mainImage');
  const thumbnails = document.querySelectorAll('.thumbnail');

  // Update active thumbnail
  thumbnails.forEach((thumb, i) => {
    if (i === index) {
      thumb.classList.add('active');
    } else {
      thumb.classList.remove('active');
    }
  });

  // Update main image
  const thumbImg = productData.images[index];
  mainImage.innerHTML = thumbImg
    ? `<img src="${thumbImg}" alt="${productData.name}">`
    : '<span>Gambar Produk</span>';
}

/**
 * Select material
 */
function selectMaterial(materialId) {
  selectedMaterialId = materialId;

  // Update UI
  const chips = document.querySelectorAll('.option-group .chip');
  chips.forEach(chip => {
    const btnText = chip.textContent.trim();
    const material = productData.options.materials.items.find(m => m.name === btnText);
    if (material && material.id === materialId) {
      chip.classList.add('active');
    } else {
      chip.classList.remove('active');
    }
  });

  updatePriceAndSubtotal();
}

/**
 * Select lamination
 */
function selectLamination(laminationId) {
  selectedLaminationId = laminationId;

  // Update UI
  const chips = document.querySelectorAll('.option-group .chip');
  chips.forEach(chip => {
    const btnText = chip.textContent.trim();
    const lam = productData.options.laminations.items.find(l => l.name === btnText);
    if (lam && lam.id === laminationId) {
      chip.classList.add('active');
    } else {
      chip.classList.remove('active');
    }
  });

  updatePriceAndSubtotal();
}

/**
 * Calculate unit price based on selections
 */
function calculateUnitPrice() {
  let materialDelta = 0;
  let laminationDelta = 0;

  if (selectedMaterialId && productData.options.materials.enabled) {
    const material = productData.options.materials.items.find(m => m.id === selectedMaterialId);
    materialDelta = material ? material.price_delta : 0;
  }

  if (selectedLaminationId && productData.options.laminations.enabled) {
    const lamination = productData.options.laminations.items.find(l => l.id === selectedLaminationId);
    laminationDelta = lamination ? lamination.price_delta : 0;
  }

  return productData.base_price + materialDelta + laminationDelta;
}

/**
 * Update displayed price and subtotal
 */
function updatePriceAndSubtotal() {
  const unitPrice = calculateUnitPrice();
  const subtotal = unitPrice * quantity;

  // Update display
  document.getElementById('displayPrice').textContent = formatPrice(unitPrice);
  document.getElementById('subtotalValue').textContent = formatPrice(subtotal);
}

/**
 * Increase quantity
 */
function increaseQuantity() {
  quantity++;
  document.getElementById('quantityValue').textContent = quantity;
  updatePriceAndSubtotal();
}

/**
 * Decrease quantity
 */
function decreaseQuantity() {
  if (quantity > 1) {
    quantity--;
    document.getElementById('quantityValue').textContent = quantity;
    updatePriceAndSubtotal();
  }
}

/**
 * Update note text
 */
function updateNote(value) {
  currentNote = value;
}

/**
 * Handle file upload
 */
function handleFileUpload(event) {
  const file = event.target.files[0];
  const statusEl = document.getElementById('fileStatus');

  if (!file) {
    statusEl.innerHTML = '';
    uploadedFileName = null;
    return;
  }

  // Validate file extension
  const fileName = file.name;
  const fileExt = '.' + fileName.split('.').pop().toLowerCase();
  const allowedExts = productData.upload_rules.accept.split(',');

  if (!allowedExts.includes(fileExt)) {
    statusEl.innerHTML = `<div class="file-error">❌ Format file tidak didukung. Gunakan: ${productData.upload_rules.accept}</div>`;
    uploadedFileName = null;
    return;
  }

  // Validate file size
  const fileSizeMB = file.size / (1024 * 1024);
  if (fileSizeMB > productData.upload_rules.max_mb) {
    statusEl.innerHTML = `<div class="file-error">❌ Ukuran file terlalu besar. Maksimal ${productData.upload_rules.max_mb}MB</div>`;
    uploadedFileName = null;
    return;
  }

  // Valid file
  uploadedFileName = fileName;
  statusEl.innerHTML = `<div class="file-name">✓ ${fileName} (${fileSizeMB.toFixed(2)}MB)</div>`;
}

/**
 * Handle checkout button click
 */
function handleCheckout() {
  const unitPrice = calculateUnitPrice();
  const subtotal = unitPrice * quantity;

  const checkoutDraft = {
    product_id: productData.slug,
    slug: productData.slug,
    product_name: productData.name,
    material_id: selectedMaterialId,
    lamination_id: selectedLaminationId,
    quantity: quantity,
    note: currentNote,
    subtotal: subtotal,
    unit_price: unitPrice,
    file_name: uploadedFileName,
    timestamp: new Date().toISOString()
  };

  // Save to localStorage
  try {
    localStorage.setItem('eventprint_checkout_draft', JSON.stringify(checkoutDraft));
    showToast('✓ Draft pesanan tersimpan!');
    console.log('Checkout draft saved:', checkoutDraft);
  } catch (error) {
    console.error('Error saving to localStorage:', error);
    showToast('❌ Gagal menyimpan draft');
  }
}

/**
 * Show toast notification
 */
function showToast(message) {
  const toast = document.getElementById('toast');
  toast.textContent = message;
  toast.classList.add('show');

  setTimeout(() => {
    toast.classList.remove('show');
  }, 3000);
}
