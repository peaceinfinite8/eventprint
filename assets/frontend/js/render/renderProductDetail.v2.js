// ============================================
// EventPrint - Product Detail Page Renderer (PHP API Version)
// Reference: frontend/public/assets/js/render/renderProductDetail.js
// PARITY VERSION: 100% match with reference HTML structure
// ============================================

let productData = null;
let selectedMaterialId = null;
let selectedLaminationId = null;
let quantity = 1;

let currentNote = '';
let selectedOptions = { pricing: {} };
let uploadedFileName = null;
let currentDesignLink = '';
const GOOGLE_SCRIPT_URL = 'https://script.google.com/macros/s/AKfycby9whH2lKMbgn38Rwoev8V3NYWIEOfkflSV0FnwekHD4kHAAbmZiseF1q_8PzLIe1V4/exec';

// Guard flags to prevent double rendering
let isRendering = false;
let isRendered = false;

/**
 * Initialize Product Detail page
 */
async function initProductDetailPage() {
  // GUARD: Prevent double initialization
  if (isRendering || isRendered) {
    console.log('[ProductDetail] Already rendering or rendered, skipping duplicate call');
    return;
  }

  isRendering = true;
  console.log('[ProductDetail] Starting initialization...');

  try {
    const slug = getProductSlugFromURL();

    if (!slug) {
      renderProductNotFound('Produk tidak ditemukan. URL tidak valid.');
      return;
    }

    showLoading('productDetailContent', 1);

    // Load from PHP API
    const baseUrl = window.EP_BASE_URL || '';
    const productResponse = await loadData(`/api/products/slug/${slug}`);

    if (!productResponse || !productResponse.success || !productResponse.product) {
      console.error('Failed to load product:', productResponse);
      renderProductNotFound('Gagal memuat data produk.');
      return;
    }

    productData = productResponse.product;
    console.log('[ProductDetail] Loaded product:', productData.name);

    // Normalize data structure to match reference format
    normalizeProductData();

    // Set default selections
    quantity = 1;

    renderProductDetail();

    // Initial price update and validation state check
    updatePriceAndSubtotal();

    // Mark as successfully rendered
    isRendered = true;
    isRendering = false;

  } catch (error) {
    console.error('Error loading product detail:', error);
    renderProductNotFound('Gagal memuat produk. Silakan coba lagi.');
    isRendering = false; // Reset on error
  }
}

/**
 * Normalize product data from PHP API to match reference structure
 * Includes deduplication of options to prevent duplicate chips
 */
function normalizeProductData() {
  // Map gallery/thumbnail to images[] array (reference format)
  // Normalize Images
  const baseUrl = window.EP_BASE_URL || '';
  const cleanBase = baseUrl.replace(/\/$/, '');

  const processImage = (img) => {
    if (!img) return '';
    if (img.match(/^https?:\/\//)) return img;
    if (img.startsWith('/')) return img;
    return cleanBase + '/' + img;
  };

  if (!productData.images) {
    const gallery = productData.gallery || [];
    const thumbnail = productData.thumbnail || '';

    if (gallery.length > 0) {
      productData.images = gallery.map(processImage);
    } else if (thumbnail) {
      productData.images = [processImage(thumbnail)];
    } else {
      productData.images = [];
    }
  } else {
    // If images already exist, ensure they are processed
    productData.images = productData.images.map(processImage);
  }

  // Ensure description is string (convert if needed)
  if (Array.isArray(productData.description)) {
    productData.description_array = productData.description;
    productData.description = productData.description.join('\n\n');
  }

  // Parse JSON fields if they're strings
  if (typeof productData.work_time === 'string') {
    try {
      productData.work_time = JSON.parse(productData.work_time);
    } catch (e) {
      productData.work_time = [];
    }
  }

  if (typeof productData.notes === 'string') {
    try {
      productData.notes = JSON.parse(productData.notes);
    } catch (e) {
      productData.notes = [];
    }
  }

  if (typeof productData.specs === 'string') {
    try {
      productData.specs = JSON.parse(productData.specs);
    } catch (e) {
      productData.specs = [];
    }
  }

  // DEDUPLICATE OPTIONS - prevent duplicate chips
  if (productData.options?.materials?.items) {
    productData.options.materials.items = deduplicateOptions(productData.options.materials.items);
    productData.options.materials.enabled = productData.options.materials.items.length > 0;
  }

  if (productData.options?.laminations?.items) {
    productData.options.laminations.items = deduplicateOptions(productData.options.laminations.items);
    productData.options.laminations.enabled = productData.options.laminations.items.length > 0;
  }

  // DO NOT auto-select options - user must choose manually
  // selectedMaterialId and selectedLaminationId stay null until clicked
}

/**
 * Deduplicate options array by slug (or id if slug missing)
 * @param {Array} items - Array of option objects with id/slug/name
 * @returns {Array} - Deduplicated array
 */
function deduplicateOptions(items) {
  if (!Array.isArray(items)) return [];

  const seen = new Set();
  return items.filter(item => {
    const key = item.slug || item.id || item.name;
    if (seen.has(key)) {
      return false;
    }
    seen.add(key);
    return true;
  });
}

/**
 * Get product slug from URL path
 * URL format: /eventprint/products/{slug}
 */
function getProductSlugFromURL() {
  const pathParts = window.location.pathname.split('/');
  // Find 'products' index and get next segment
  const productsIndex = pathParts.indexOf('products');
  if (productsIndex !== -1 && productsIndex + 1 < pathParts.length) {
    const slug = pathParts[productsIndex + 1];
    return slug && slug.trim() !== '' ? slug : null;
  }
  return null;
}

/**
 * Render product not found state
 */
function renderProductNotFound(message) {
  const container = document.getElementById('productDetailContent');
  if (!container) return;

  const baseUrl = window.EP_BASE_URL || '';
  container.innerHTML = `
    <div class="product-not-found">
      <h2>Produk Tidak Ditemukan</h2>
      <p>${message}</p>
      <a href="${baseUrl}/products" class="btn btn-primary">Kembali ke All Product</a>
    </div>
  `;
}

/**
 * Render complete product detail page
 * STRICT PARITY: DOM structure matches reference HTML exactly
 */
function renderProductDetail() {
  const container = document.getElementById('productDetailContent');
  if (!container) return;

  // Images array (normalized)
  const images = productData.images || [];
  const hasImages = images.length > 0;

  // Calculate stock status globally for template
  const stock = parseInt(productData.stock) || 0;
  const isOutOfStock = stock <= 0;

  const html = `
    <!-- 3 Column Layout -->
    <div class="product-detail-container">
      <!-- Column 1: Gallery -->
      <div class="gallery-section">
        <div id="mainImage" class="main-image">
          ${hasImages && images[0] ? `<img src="${images[0]}" alt="${productData.name}">` : '<span>📷 Gambar Produk</span>'}
        </div>
        <div class="thumbnail-list">
          ${images.slice(1, 4).map((img, index) => `
            <div class="thumbnail ${index === 0 ? 'active' : ''}" onclick="switchMainImage(${index + 1})" data-index="${index + 1}">
              ${img ? `<img src="${img}" alt="Thumbnail ${index + 1}">` : '<span>Gambar Produk</span>'}
            </div>
          `).join('')}
        </div>
      </div>
      
      <!-- Column 2: Options -->
      <div class="options-section">
        <h1>${productData.name}</h1>
        ${(() => {
      // DISCOUNT CALCULATION LOGIC
      const basePrice = parseFloat(productData.base_price || 0);
      let finalPrice = basePrice;
      let discountBadgeHtml = '';

      // Check for valid discount
      if (productData.discount_value > 0) {
        let discountAmount = 0;
        let discountPercent = 0;

        if (productData.discount_type === 'percentage') {
          discountPercent = parseFloat(productData.discount_value);
          discountAmount = basePrice * (discountPercent / 100);
        } else if (productData.discount_type === 'fixed') {
          discountAmount = parseFloat(productData.discount_value);
          if (basePrice > 0) {
            discountPercent = (discountAmount / basePrice) * 100;
          }
        }

        // Apply discount if valid
        if (discountAmount > 0) {
          finalPrice = Math.max(0, basePrice - discountAmount);

          // Format percentage for badge
          const percentDisplay = discountPercent % 1 === 0 ? discountPercent.toFixed(0) : discountPercent.toFixed(1);

          discountBadgeHtml = `
            <span style="background: #ef4444; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; vertical-align: middle; margin-left: 8px;">
                Hemat ${percentDisplay}%
            </span>
           `;
        }
      }

      if (isOutOfStock) {
        return `
              <div class="price-display out-of-stock" id="displayPrice">
                <span class="strikethrough">${formatPrice(finalPrice)}</span>
                <div class="stock-status-badge">Stok Habis</div>
              </div>
            `;
      } else {
        // If discount exists, show crossed out original price
        if (finalPrice < basePrice) {
          return `
                <div class="price-display">
                    <span style="text-decoration: line-through; color: #9ca3af; font-size: 1rem; margin-right: 8px;">${formatPrice(basePrice)}</span>
                    <span id="displayPrice" style="color: var(--primary-cyan); font-weight: 700;">${formatPrice(finalPrice)}</span>
                     ${discountBadgeHtml}
                </div>
             `;
        } else {
          return `<div class="price-display" id="displayPrice">${formatPrice(basePrice)}</div>`;
        }
      }
    })()}
        
        ${renderMarketplaceCTAs()}
        
        ${productData.options?.materials?.enabled ? `
        <!-- Material Selection -->
        <div class="option-group" data-option-type="material">
          <label class="option-label">Pilih Bahan</label>
          <div class="chips-container">
            ${productData.options.materials.items.map((material) => `
              <button type="button" class="chip" 
                      data-id="${material.id}"
                      data-type="material"
                      onclick="selectMaterial('${material.id}')">
                ${material.name}
              </button>
            `).join('')}
          </div>
        </div>
        ` : ''}
        
        ${productData.options?.laminations?.enabled ? `
        <!-- Lamination Selection -->
        <div class="option-group" data-option-type="lamination">
          <label class="option-label">Pilih Laminasi</label>
          <div class="chips-container">
            ${productData.options.laminations.items.map((lam) => `
              <button type="button" class="chip" 
                      data-id="${lam.id}"
                      data-type="lamination"
                      onclick="selectLamination('${lam.id}')">
                ${lam.name}
              </button>
            `).join('')}
          </div>
        </div>
        ` : ''}
        
        ${renderPricingOptions()}
        
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
                     accept=".pdf,.ai,.psd,.jpg,.png" 
                     onchange="handleFileUpload(event)">
            </label>
            <div id="fileStatus"></div>
          </div>
        </div>
      </div>
      
      <!-- Column 3: Checkout Box -->
      <div class="checkout-box">
        <h3 class="checkout-title">Atur Jumlah dan Catatan</h3>
        
        <div class="quantity-stepper" ${isOutOfStock ? 'style="opacity:0.5; pointer-events:none;"' : ''}>
          <span class="quantity-label">Quantity :</span>
          <div class="stepper-controls">
            <button type="button" class="stepper-btn" onclick="decreaseQuantity()" ${isOutOfStock ? 'disabled' : ''}>-</button>
            <input type="number" class="quantity-input" id="quantityInput" value="1" min="1" max="${stock > 0 ? stock : 999999}"
                   pattern="[0-9]*" inputmode="numeric" maxlength="6"
                   onchange="updateQuantityManual(this.value)" oninput="updateQuantityManual(this.value)"
                   style="width: 60px; text-align: center; border: none; font-weight: bold; font-size: 16px;"
                   title="${stock > 0 ? `Stok tersedia: ${stock} unit` : ''}"
                   ${isOutOfStock ? 'disabled' : ''}>
            <button type="button" class="stepper-btn" onclick="increaseQuantity()" ${isOutOfStock ? 'disabled' : ''}>+</button>
          </div>
        </div>

        <!-- NEW: Order Summary (User Request) -->
        <div id="orderSummary" class="order-summary" style="display:none;"></div>
        
        <div class="subtotal-row">
          <span class="subtotal-label">Subtotal</span>
          <span class="subtotal-value" id="subtotalValue">${formatPrice(productData.base_price || 0)}</span>
        </div>
        
        <button type="button" class="checkout-btn" 
                onclick="${isOutOfStock ? '' : 'handleCheckout()'}"
                ${isOutOfStock ? 'disabled style="background-color: #ccc; cursor: not-allowed; opacity: 0.7;"' : ''}>
          <i class="fab fa-whatsapp" style="font-size: 1.1em; margin-right: 5px;"></i> ${isOutOfStock ? 'Order ke WA' : 'Order via WhatsApp'}
        </button>
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
      ${productData.description ? `
        <div class="info-section">
          <h3>Keterangan Produk</h3>
          ${Array.isArray(productData.description_array)
        ? productData.description_array.map(p => `<p>${p}</p>`).join('')
        : `<p>${productData.description}</p>`}
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
 * Shows buttons only if URLs are configured for this product
 */
function renderMarketplaceCTAs() {
  const shopeeUrl = productData.shopee_url || 'javascript:void(0)';
  const tokopediaUrl = productData.tokopedia_url || 'javascript:void(0)';

  // Always render buttons (User Request)
  let buttons = '';

  // Shopee button with logo
  buttons += `
    <a href="${shopeeUrl}" target="${shopeeUrl === 'javascript:void(0)' ? '_self' : '_blank'}" rel="noopener noreferrer" class="marketplace-btn shopee" ${shopeeUrl === 'javascript:void(0)' ? 'style="opacity: 0.6; cursor: default;" onclick="return false;"' : ''}>
      <svg viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C8.13 2 5 5.13 5 9c0 1.61.59 3.09 1.56 4.23L12 22l5.44-8.77A6.938 6.938 0 0019 9c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
      </svg>
      Beli di Shopee
    </a>
  `;

  // Tokopedia button with logo
  buttons += `
    <a href="${tokopediaUrl}" target="${tokopediaUrl === 'javascript:void(0)' ? '_self' : '_blank'}" rel="noopener noreferrer" class="marketplace-btn tokopedia" ${tokopediaUrl === 'javascript:void(0)' ? 'style="opacity: 0.6; cursor: default;" onclick="return false;"' : ''}>
      <svg viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
      </svg>
      Beli di Tokopedia
    </a>
  `;

  return `
    <div class="marketplace-buttons" style="display: flex; gap: 10px; margin: 12px 0; flex-wrap: wrap;">
      ${buttons}
    </div>
  `;
}

/**
 * Render pricing option groups (quantity, size, etc.)
 * These are product-specific options set in admin panel
 */
function renderPricingOptions() {
  const optionGroups = productData.option_groups || [];

  if (!optionGroups || optionGroups.length === 0) {
    return '';
  }

  return optionGroups.map(group => {
    const values = group.values || [];
    if (values.length === 0) return '';

    const isRequired = group.is_required == 1;
    const inputType = group.input_type || 'select';

    // Generate value chips/radio/select based on input_type
    let valuesHtml = '';

    if (inputType === 'select') {
      valuesHtml = `
        <select class="pricing-option-select" 
                data-group-id="${group.id}" 
                data-group-name="${group.name}"
                onchange="selectPricingOption(${group.id}, this.value)"
                ${isRequired ? 'required' : ''}>
          <option value="">-- Pilih ${group.name} --</option>
          ${values.map(v => `
            <option value="${v.id}" 
                    data-price-type="${v.price_type}" 
                    data-price-value="${v.price_value}">
              ${v.label} ${formatPriceDelta(v.price_type, v.price_value)}
            </option>
          `).join('')}
        </select>
      `;
    } else {
      // Radio or Checkbox - render as chips
      valuesHtml = `
        <div class="chips-container">
          ${values.map(v => `
            <button type="button" class="chip pricing-chip" 
                    data-group-id="${group.id}"
                    data-value-id="${v.id}"
                    data-price-type="${v.price_type}"
                    data-price-value="${v.price_value}"
                    onclick="selectPricingOption(${group.id}, ${v.id})">
              ${v.label} ${formatPriceDelta(v.price_type, v.price_value)}
            </button>
          `).join('')}
        </div>
      `;
    }

    return `
      <div class="option-group" data-option-type="pricing" data-group-id="${group.id}">
        <label class="option-label">
          ${group.name}
          ${isRequired ? '<span class="required-badge">*Wajib</span>' : ''}
        </label>
        ${valuesHtml}
      </div>
    `;
  }).join('');
}

/**
 * Format price delta for display
 */
function formatPriceDelta(priceType, priceValue) {
  const value = parseFloat(priceValue) || 0;
  if (value === 0) return '';

  if (priceType === 'percent') {
    return `(+${value}%)`;
  } else {
    return `(+Rp ${value.toLocaleString('id-ID')})`;
  }
}

/**
 * Select pricing option (for groups like quantity, size)
 */
function selectPricingOption(groupId, valueId) {
  if (!valueId) {
    // Deselect
    delete selectedOptions.pricing[groupId];
  } else {
    // Store selection
    if (!selectedOptions.pricing) {
      selectedOptions.pricing = {};
    }
    selectedOptions.pricing[groupId] = valueId;
  }

  // Update chip active states
  const container = document.querySelector(`[data-group-id="${groupId}"][data-option-type="pricing"]`);
  if (container) {
    const chips = container.querySelectorAll('.pricing-chip');
    chips.forEach(chip => {
      if (chip.dataset.valueId == valueId) {
        chip.classList.add('selected');
      } else {
        chip.classList.remove('selected');
      }
    });
  }

  // Recalculate price
  updatePriceAndSubtotal();

  console.log('Selected pricing options:', selectedOptions.pricing);
}


/**
 * Switch main image when thumbnail clicked
 * FIXED: Index handling matches reference exactly
 */
function switchMainImage(index) {
  const mainImage = document.getElementById('mainImage');
  const thumbnails = document.querySelectorAll('.thumbnail');

  // Update active thumbnail
  thumbnails.forEach((thumb, i) => {
    if (i === index - 1) {
      thumb.classList.add('active');
    } else {
      thumb.classList.remove('active');
    }
  });

  // Update main image
  const images = productData.images || [];
  const thumbImg = images[index];

  if (mainImage) {
    mainImage.innerHTML = thumbImg
      ? `<img src="${thumbImg}" alt="${productData.name}">`
      : '<span>📷 Gambar Produk</span>';
  }
}

/**
 * Helper to update main image
 */
function updateMainImage(url) {
  const mainImage = document.getElementById('mainImage');
  if (mainImage && url) {
    // Ensure we keep the container structure if needed, or just replace img
    // The existing switchMainImage replaces innerHTML, so we do same.
    mainImage.innerHTML = `<img src="${url}" alt="${productData.name}" class="img-fluid w-100 rounded" style="width: 100%; height: auto; display: block;">`;
  }
}

/**
 * Select material option (Toggle logic)
 */
function selectMaterial(materialId) {
  // Toggle: if same ID selected, deselect it
  if (selectedMaterialId === materialId) {
    selectedMaterialId = null;

    // Revert to default image (thumbnail)
    // Revert to default image (thumbnail)
    const defaultImage = productData.main_image || (productData.images && productData.images[0]);
    if (defaultImage) {
      updateMainImage(defaultImage);
    }
  } else {
    selectedMaterialId = materialId;

    // Switch image if available
    if (productData.options && productData.options.materials && productData.options.materials.items) {
      const mat = productData.options.materials.items.find(m => m.id === materialId);
      if (mat && mat.image) {
        updateMainImage(mat.image);
      }
    }
  }

  // Update UI - toggle active class on material chips only
  const materialChips = document.querySelectorAll('.chip[data-type="material"]');
  materialChips.forEach(chip => {
    if (chip.dataset.id === materialId && selectedMaterialId) {
      chip.classList.add('active');
    } else {
      chip.classList.remove('active');
    }
  });

  updatePriceAndSubtotal();
  updateCheckoutButtonState();
}

/**
 * Select lamination option (Toggle logic)
 */
function selectLamination(laminationId) {
  // Toggle: if same ID selected, deselect it
  if (selectedLaminationId === laminationId) {
    selectedLaminationId = null;

    // Revert to default image (thumbnail)
    // Check if Material is selected -> Show Material Image. Else Main.

    if (selectedMaterialId && productData.options.materials && productData.options.materials.items) {
      const mat = productData.options.materials.items.find(m => m.id === selectedMaterialId);
      if (mat && mat.image) {
        updateMainImage(mat.image);
      } else {
        const defaultImage = productData.main_image || (productData.images && productData.images[0]);
        if (defaultImage) updateMainImage(defaultImage);
      }
    } else {
      const defaultImage = productData.main_image || (productData.images && productData.images[0]);
      if (defaultImage) updateMainImage(defaultImage);
    }

  } else {
    selectedLaminationId = laminationId;

    // Switch image if available (Lamination image takes precedence)
    if (productData.options && productData.options.laminations && productData.options.laminations.items) {
      const lam = productData.options.laminations.items.find(l => l.id === laminationId);
      if (lam && lam.image) {
        updateMainImage(lam.image);
      }
    }
  }

  // Update UI
  const laminationChips = document.querySelectorAll('.chip[data-type="lamination"]');
  laminationChips.forEach(chip => {
    if (chip.dataset.id === laminationId && selectedLaminationId) {
      chip.classList.add('active');
    } else {
      chip.classList.remove('active');
    }
  });

  updatePriceAndSubtotal();
  updateCheckoutButtonState();
}



/**
 * Calculate unit price with material, lamination, and pricing options
 */
function calculateUnitPrice() {
  // CRITICAL: Parse to float to avoid string concatenation!
  let basePrice = parseFloat(productData.base_price) || 0;

  // TIER PRICING LOGIC
  if (productData.price_tiers && productData.price_tiers.length > 0) {
    // Find tier (quantity is global)
    const tiers = [...productData.price_tiers].sort((a, b) => b.qty_min - a.qty_min);
    const matched = tiers.find(t => quantity >= t.qty_min && (!t.qty_max || quantity <= t.qty_max));
    if (matched) {
      basePrice = parseFloat(matched.unit_price);
      // console.log('[Tier Pricing] Applied:', matched);
    }
  }
  let materialDelta = 0;
  let laminationDelta = 0;
  let pricingOptionsDelta = 0;

  // Add material price delta
  if (selectedMaterialId && productData.options?.materials?.enabled) {
    const material = productData.options.materials.items.find(
      m => m.id === selectedMaterialId || m.slug === selectedMaterialId
    );
    materialDelta = material ? parseFloat(material.price_delta) || 0 : 0;
  }

  // Add lamination price delta
  if (selectedLaminationId && productData.options?.laminations?.enabled) {
    const lamination = productData.options.laminations.items.find(
      l => l.id === selectedLaminationId || l.slug === selectedLaminationId
    );
    laminationDelta = lamination ? parseFloat(lamination.price_delta) || 0 : 0;
  }

  // Add pricing options delta (quantity, size, etc.)
  if (selectedOptions.pricing && productData.option_groups) {
    Object.entries(selectedOptions.pricing).forEach(([groupId, valueId]) => {
      const group = productData.option_groups.find(g => g.id == groupId);
      if (group && group.values) {
        const value = group.values.find(v => v.id == valueId);
        if (value) {
          const priceValue = parseFloat(value.price_value) || 0;
          if (value.price_type === 'percent') {
            pricingOptionsDelta += basePrice * (priceValue / 100);
          } else {
            pricingOptionsDelta += priceValue;
          }
        }
      }
    });
  }

  const finalPrice = basePrice + materialDelta + laminationDelta + pricingOptionsDelta;

  // Calculate discount if available
  let discountedPrice = finalPrice;
  if (window.productDiscount && window.productDiscount.amount > 0) {
    if (window.productDiscount.type === 'percent') {
      discountedPrice = finalPrice - (finalPrice * (window.productDiscount.amount / 100));
    } else {
      discountedPrice = Math.max(0, finalPrice - window.productDiscount.amount);
    }
  }

  return { original: finalPrice, final: discountedPrice, hasDiscount: discountedPrice < finalPrice };
}

/**
 * Update displayed price and subtotal
 */
function updatePriceAndSubtotal() {
  const priceData = calculateUnitPrice();
  const unitPrice = priceData.final;
  const subtotal = unitPrice * quantity;

  // Update price display
  const displayPrice = document.getElementById('displayPrice');
  if (displayPrice) {
    if (priceData.hasDiscount) {
      // Show Strikethrough + Discounted Price
      displayPrice.innerHTML = `
         <span style="text-decoration: line-through; color: #999; font-size: 0.8em; margin-right: 8px;">
           ${formatPrice(priceData.original)}
         </span>
         <span style="color: #dc3545;">
           ${formatPrice(unitPrice)}
         </span>
         <div class="badge bg-danger ms-2" style="font-size: 0.6em; vertical-align: middle;">
           Hemat ${window.productDiscount.type === 'percent' ? window.productDiscount.amount + '%' : formatPrice(window.productDiscount.amount)}
         </div>
       `;
    } else {
      // Normal Price
      displayPrice.textContent = formatPrice(unitPrice);
    }
  }

  // Update subtotal
  const subtotalValue = document.querySelector('.subtotal-value');
  if (subtotalValue) {
    subtotalValue.textContent = formatPrice(subtotal);
  }

  // NEW: Update checkout button state
  updateCheckoutButtonState();

  // Update Order Summary List
  updateOrderSummary();
}


/**
 * Update checkout button enabled/disabled state
 */
function updateCheckoutButtonState() {
  const btn = document.querySelector('.checkout-btn');
  if (!btn) return;

  // PRIORITY 1: Out of Stock
  /*
     Check stock status from productData.
     We assume isOutOfStock logic: stock <= 0 && is_unlimited != 1
  */
  const isOutOfStock = (productData.stock <= 0 && productData.is_unlimited != 1);

  if (isOutOfStock) {
    btn.disabled = true;
    btn.style.opacity = '0.7'; // Visual disabled state
    btn.style.cursor = 'not-allowed';
    btn.style.backgroundColor = '#ccc'; // Gray
    btn.innerHTML = '<i class="fab fa-whatsapp" style="font-size: 1.1em; margin-right: 5px;"></i> Order ke WA';
    btn.onclick = null; // Prevent click (or handle empty) as set in HTML
    return; // STOP HERE
  }

  // PRIORITY 2: Option Validation (In Stock)
  const materialEnabled = productData.options?.materials?.enabled;
  const laminationEnabled = productData.options?.laminations?.enabled;

  let isValid = true;
  let missing = [];

  if (materialEnabled && !selectedMaterialId) {
    isValid = false;
    missing.push('Bahan');
  }

  if (laminationEnabled && !selectedLaminationId) {
    isValid = false;
    missing.push('Laminasi');
  }

  // Disable button if invalid
  btn.disabled = !isValid;

  // Update styling
  if (!isValid) {
    btn.style.opacity = '0.5';
    btn.style.cursor = 'not-allowed';
    btn.style.backgroundColor = ''; // Reset to default (or primary but dim)
    btn.title = `Silakan pilih ${missing.join(' dan ')} terlebih dahulu`;
    btn.textContent = `Pilih ${missing[0]} Dulu...`;
  } else {
    btn.style.opacity = '1';
    btn.style.cursor = 'pointer';
    btn.style.backgroundColor = ''; // Reset
    btn.title = '';
    // Standardize text
    btn.innerHTML = '<i class="fab fa-whatsapp" style="font-size: 1.1em; margin-right: 5px;"></i> Order via WhatsApp';
  }
}

/**
 * Update Order Summary HTML based on selections
 */
function updateOrderSummary() {
  const container = document.getElementById('orderSummary');
  if (!container) return;

  let itemsHtml = '';

  // Quantity
  itemsHtml += `<li class="order-summary-item"><span>Jumlah</span><span>${quantity}</span></li>`;

  // Material
  if (selectedMaterialId && productData.options?.materials?.items) {
    // Handle string vs number ID mismatch by using loose comparison or find
    const mat = productData.options.materials.items.find(m => m.id == selectedMaterialId);
    if (mat) {
      itemsHtml += `<li class="order-summary-item"><span>Bahan</span><span>${mat.name}</span></li>`;
    }
  }

  // Lamination
  if (selectedLaminationId && productData.options?.laminations?.items) {
    const lam = productData.options.laminations.items.find(l => l.id == selectedLaminationId);
    if (lam) {
      itemsHtml += `<li class="order-summary-item"><span>Laminasi</span><span>${lam.name}</span></li>`;
    }
  }

  // Pricing Options
  if (selectedOptions.pricing && productData.option_groups) {
    Object.entries(selectedOptions.pricing).forEach(([groupId, valueId]) => {
      const group = productData.option_groups.find(g => g.id == groupId);
      if (group && group.values) {
        const value = group.values.find(v => v.id == valueId);
        if (value) {
          itemsHtml += `<li class="order-summary-item"><span>${group.name}</span><span>${value.label}</span></li>`;
        }
      }
    });
  }

  if (itemsHtml) {
    container.innerHTML = `<h4>Detail Pesanan</h4><ul class="order-summary-list">${itemsHtml}</ul>`;
    container.style.display = 'block';
  } else {
    container.style.display = 'none';
    container.innerHTML = '';
  }
}



/**
 * Decrease quantity
 */
function decreaseQuantity() {
  if (quantity > 1) {
    quantity--;
    const input = document.getElementById('quantityInput');
    if (input) input.value = quantity;
    updatePriceAndSubtotal();
  }
}

/**
 * Increase quantity
 * STOCK-AWARE: Max is available stock, not hardcoded limit
 */
function increaseQuantity() {
  const stock = parseInt(productData?.stock) || 0;
  const MAX_QUANTITY = stock > 0 ? stock : 999999; // Use stock if available

  if (quantity < MAX_QUANTITY) {
    quantity++;
    const input = document.getElementById('quantityInput');
    if (input) input.value = quantity;
    updatePriceAndSubtotal();
  } else {
    // Show stock limit warning
    if (stock > 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Stok Terbatas',
          text: `Stok tersedia hanya ${stock} unit`,
          confirmButtonColor: '#00afb9',
          confirmButtonText: 'OK'
        });
      } else {
        alert(`Stok tersedia hanya ${stock} unit`);
      }
    }
    console.warn('[Quantity] Maximum stock reached:', MAX_QUANTITY);
  }
}

// Make functions globally available for HTML inline events
window.updateQuantityManual = updateQuantityManual;
window.increaseQuantity = increaseQuantity;
window.decreaseQuantity = decreaseQuantity;
window.selectMaterial = selectMaterial;
window.selectLamination = selectLamination;
window.handleCheckout = handleCheckout;

/**
 * Handle manual quantity input
 * STOCK-AWARE: Validates against available stock
 */
function updateQuantityManual(val) {
  // 1. Handle Empty Input (User deleting text)
  if (val === '') {
    quantity = 1; // Default for price calculation
    updatePriceAndSubtotal();
    return; // Allow input to remain empty visually
  }

  // 2. Clean Input (Remove non-digits)
  const cleanVal = String(val).replace(/[^0-9]/g, '');

  // 3. Handle Invalid Input (e.g. only text)
  if (cleanVal === '') {
    quantity = 1;
    updatePriceAndSubtotal();
    return;
  }

  // 4. Parse & Validate
  let v = parseInt(cleanVal, 10);
  const stock = parseInt(productData?.stock) || 0;
  // Use 10000 as safe limit for unlimited stock (consistent with logic)
  const MAX_QUANTITY = stock > 0 ? stock : 10000;

  // Enforce Max
  if (v > MAX_QUANTITY) {
    v = MAX_QUANTITY;

    // Show Warning
    if (stock > 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Stok Terbatas',
          text: `Stok tersedia hanya ${stock} unit`,
          confirmButtonColor: '#00afb9'
        });
      } else {
        alert(`Stok tersedia hanya ${stock} unit`);
      }
    }
  }

  // Enforce Min
  if (v < 1) v = 1;

  quantity = v;
  updatePriceAndSubtotal();

  // 5. Sync UI (Only if value was clamped)
  // If user typed 500 and limit is 100 -> Force change to 100
  // If user typed 50 and limit is 100 -> Keep 50 (don't interfere)
  const input = document.getElementById('quantityInput');
  if (input) {
    // We only force update if the parsed value is DIFFERENT from what was typed (logic check)
    // AND if the cleanVal is not equal to v (meaning it was clamped or cleaned of valid leading zeros etc)
    // Actually, simple check: is v different from what they typed?
    if (parseInt(cleanVal) !== v) {
      input.value = v;
    }
  }
}

/**
 * Update note text
 */
function updateNote(value) {
  currentNote = value;
}


/**
 * Handle file upload (Direct to Google Drive)
 */
function handleFileUpload(event) {
  const file = event.target.files[0];
  const statusEl = document.getElementById('fileStatus');

  if (!statusEl) return;

  if (!file) {
    statusEl.innerHTML = '';
    uploadedFileName = null;
    currentDesignLink = '';
    return;
  }

  // Validate file extension
  const fileName = file.name;
  const fileExt = '.' + fileName.split('.').pop().toLowerCase();
  const allowedExts = ['.pdf', '.ai', '.psd', '.jpg', '.jpeg', '.png'];

  if (!allowedExts.includes(fileExt)) {
    statusEl.innerHTML = `<div class="file-error">❌ Format file tidak didukung. Gunakan: ${allowedExts.join(', ')}</div>`;
    uploadedFileName = null;
    return;
  }

  // Validate file size (max 50MB for Drive)
  const fileSizeMB = file.size / (1024 * 1024);
  if (fileSizeMB > 50) {
    statusEl.innerHTML = `<div class="file-error">❌ Ukuran file terlalu besar. Maksimal 50MB</div>`;
    uploadedFileName = null;
    return;
  }

  // Upload Process
  statusEl.innerHTML = `<div class="file-loading">⏳ Mengupload ke Google Drive... (${fileSizeMB.toFixed(2)}MB)</div>`;

  const reader = new FileReader();
  reader.readAsDataURL(file);

  reader.onload = function (e) {
    const base64Data = e.target.result.split(',')[1]; // Remove "data:application/..." prefix

    const payload = {
      filename: fileName,
      mimeType: file.type,
      bytes: base64Data
    };

    // Send to Google Apps Script
    // Note: 'no-cors' mode is used, but we handle result via JSONP or text/plain hack if needed.
    // However, Apps Script usually handles CORS if "Anyone" is set.
    // Try standard fetch first.
    fetch(GOOGLE_SCRIPT_URL, {
      method: "POST",
      body: JSON.stringify(payload)
    })
      .then(response => response.json())
      .then(data => {
        if (data.result === 'success') {
          uploadedFileName = fileName;
          currentDesignLink = data.url;
          statusEl.innerHTML = `
          <div class="file-success" style="color: green; font-size: 0.9rem;">
            ✅ <strong>Berhasil Upload!</strong><br>
            <a href="${data.url}" target="_blank" style="text-decoration: underline;">Buka File di Drive</a>
          </div>`;
        } else {
          throw new Error(data.message || 'Unknown error');
        }
      })
      .catch(error => {
        console.error('Drive Upload Error:', error);
        statusEl.innerHTML = `<div class="file-error">❌ Gagal Upload: ${error.message}. Coba lagi.</div>`;
        uploadedFileName = null;
        currentDesignLink = '';
      });
  };

  reader.onerror = function () {
    statusEl.innerHTML = `<div class="file-error">❌ Gagal membaca file.</div>`;
  };
}

/**
 * Handle checkout button click (WhatsApp)
 */
function handleCheckout() {
  const priceData = calculateUnitPrice();
  const unitPrice = priceData.final; // Access .final from price object
  const subtotal = unitPrice * quantity;

  // Validation: Check if required options are selected
  const materialEnabled = productData.options?.materials?.enabled;
  const laminationEnabled = productData.options?.laminations?.enabled;

  if (materialEnabled && !selectedMaterialId) {
    showToast('⚠️ Silakan pilih bahan terlebih dahulu');
    return;
  }

  if (laminationEnabled && !selectedLaminationId) {
    showToast('⚠️ Silakan pilih laminasi terlebih dahulu');
    return;
  }

  // Build engaging WhatsApp message with emoji and formatting
  let message = `🛒 *PESANAN BARU*\n`;
  message += `━━━━━━━━━━━━━━━━\n\n`;

  message += `📦 *Produk:* ${productData.name}\n`;

  if (priceData.hasDiscount) {
    message += `💰 *Harga Normal:* ~${formatPrice(priceData.original)}~\n`;
    message += `🏷️ *Harga Diskon:* ${formatPrice(unitPrice)}\n`;
  } else {
    message += `💰 *Harga Satuan:* ${formatPrice(unitPrice)}\n`;
  }

  message += `🔢 *Jumlah:* ${quantity} unit\n`;

  // Add selected material if any
  if (selectedMaterialId && productData.options?.materials?.items) {
    const material = productData.options.materials.items.find(m => m.id == selectedMaterialId);
    if (material) {
      message += `🎨 *Bahan:* ${material.name}\n`;
    }
  }

  // Add selected lamination if any
  if (selectedLaminationId && productData.options?.laminations?.items) {
    const lamination = productData.options.laminations.items.find(l => l.id == selectedLaminationId);
    if (lamination) {
      message += `✨ *Laminasi:* ${lamination.name}\n`;
    }
  }

  // Add pricing options if any
  if (selectedOptions.pricing && productData.option_groups) {
    Object.entries(selectedOptions.pricing).forEach(([groupId, valueId]) => {
      const group = productData.option_groups.find(g => g.id == groupId);
      if (group && group.values) {
        const value = group.values.find(v => v.id == valueId);
        if (value) {
          message += `📏 *${group.name}:* ${value.label}\n`;
        }
      }
    });
  }

  message += `\n💵 *TOTAL:* ${formatPrice(subtotal)}\n`;
  message += `━━━━━━━━━━━━━━━━\n\n`;

  // Add notes if provided
  if (currentNote && currentNote.trim() !== '') {
    message += `📝 *Catatan Khusus:*\n${currentNote}\n\n`;
  }

  // Add file info (Google Drive Link)
  if (uploadedFileName && currentDesignLink) {
    message += `📎 *File Design:* ${uploadedFileName}\n`;
    message += `🔗 *Link Drive:* ${currentDesignLink}\n`;
    message += `_(File otomatis ter-upload ke Drive Admin)_\n\n`;
  } else if (uploadedFileName) {
    // Fallback if uploading finished but link missing (unlikely)
    message += `📎 *File Design:* ${uploadedFileName}\n`;
  }
  if (currentDesignLink) {
    message += `� *Link File:* ${currentDesignLink}\n`;
    message += `_(File ada di link tersebut)_\n\n`;
  }

  message += `Mohon konfirmasi ketersediaan dan estimasi pengerjaan. Terima kasih! 🙏`;

  // Get WhatsApp number
  const waNumber = productData.category_whatsapp || window.EP_SETTINGS?.whatsapp || '6281234567890';
  const waUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(message)}`;

  // Show confirmation dialog with clear instructions
  const confirmMessage = `📱 Anda akan diarahkan ke WhatsApp\n\n` +
    `✅ Pesan sudah disiapkan otomatis\n` +
    `✅ Tinggal klik tombol "Send" di WhatsApp\n\n` +
    `Lanjutkan ke WhatsApp?`;

  if (confirm(confirmMessage)) {
    // Open WhatsApp in new tab
    window.open(waUrl, '_blank');
    showToast('✓ Membuka WhatsApp... Silakan klik "Send" untuk mengirim pesanan');
  }
}


/**
 * Handle WhatsApp Order for Out of Stock items
 */
function handleWhatsAppOrder() {
  // Build simple inquiry message
  let message = `👋 *Halo Admin, saya ingin pesan produk ini (Stok Habis/Pre-Order):*\n`;
  message += `━━━━━━━━━━━━━━━━\n\n`;
  message += `📦 *Produk:* ${productData.name}\n`;
  message += `🔗 *Link:* ${window.location.href}\n\n`;

  // Add selected options if any (optional)
  if (selectedMaterialId && productData.options?.materials?.items) {
    const m = productData.options.materials.items.find(x => x.id == selectedMaterialId);
    if (m) message += `🎨 *Bahan:* ${m.name}\n`;
  }

  message += `\nMohon info ketersediaan stok atau cara pre-order. Terima kasih! 🙏`;

  // Get WhatsApp number
  const waNumber = productData.category_whatsapp || window.EP_SETTINGS?.whatsapp || '6281234567890';
  const waUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(message)}`;

  window.open(waUrl, '_blank');
}

/**
 * Show toast notification
 */
function showToast(message) {
  const toast = document.getElementById('toast');
  if (!toast) return;

  toast.textContent = message;
  toast.classList.add('show');

  setTimeout(() => {
    toast.classList.remove('show');
  }, 3000);
}
// Auto-init removed - page initializes via app.js only
// This prevents double rendering when app.js calls initProductDetailPage()
