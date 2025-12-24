// ============================================
// EventPrint - Product Detail Page Renderer (PHP API Version)
// Reference: frontend/public/assets/js/render/renderProductDetail.js
// PARITY VERSION: 100% match with reference HTML structure
// ============================================

let productData = null;
let selectedMaterialId = null;
let selectedLaminationId = null;
let quantity = 1;
let uploadedFileName = null;
let currentNote = '';
let selectedOptions = { pricing: {} };

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
  if (!productData.images) {
    const gallery = productData.gallery || [];
    const thumbnail = productData.thumbnail || '';

    if (gallery.length > 0) {
      productData.images = gallery;
    } else if (thumbnail) {
      productData.images = [thumbnail];
    } else {
      productData.images = [];
    }
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
 * URL format: /eventprint/public/products/{slug}
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
            <input type="number" class="quantity-input" id="quantityInput" value="1" min="1" 
                   onchange="updateQuantityManual(this.value)" oninput="updateQuantityManual(this.value)"
                   style="width: 60px; text-align: center; border: none; font-weight: bold; font-size: 16px;"
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
        
        <button type="button" class="checkout-btn" onclick="handleCheckout()" 
                ${isOutOfStock ? 'disabled style="background:#cccccc; cursor:not-allowed;"' : ''}>
          ${isOutOfStock ? 'Stok Habis' : 'Beli Sekarang'}
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
 * Select material option
 * Updates active state and recalculates price
 */
function selectMaterial(materialId) {
  selectedMaterialId = materialId;

  // Update UI - toggle active class on material chips only
  const materialChips = document.querySelectorAll('.chip[data-type="material"]');
  materialChips.forEach(chip => {
    if (chip.dataset.id === materialId) {
      chip.classList.add('active');
    } else {
      chip.classList.remove('active');
    }
  });

  updatePriceAndSubtotal();
  console.log('[ProductDetail] Selected material:', materialId);
}

/**
 * Select lamination option
 * Updates active state and recalculates price
 */
function selectLamination(laminationId) {
  selectedLaminationId = laminationId;

  // Update UI - toggle active class on lamination chips only
  const laminationChips = document.querySelectorAll('.chip[data-type="lamination"]');
  laminationChips.forEach(chip => {
    if (chip.dataset.id === laminationId) {
      chip.classList.add('active');
    } else {
      chip.classList.remove('active');
    }
  });

  updatePriceAndSubtotal();
  console.log('[ProductDetail] Selected lamination:', laminationId);
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

  return basePrice + materialDelta + laminationDelta + pricingOptionsDelta;
}

/**
 * Update displayed price and subtotal
 */
function updatePriceAndSubtotal() {
  const unitPrice = calculateUnitPrice();
  const subtotal = unitPrice * quantity;

  console.log('[Price Update]', {
    base: productData?.base_price,
    material: selectedMaterialId,
    lamination: selectedLaminationId,
    unitPrice,
    quantity,
    subtotal
  });

  // Update price display
  const displayPrice = document.getElementById('displayPrice');
  if (displayPrice) {
    displayPrice.textContent = formatPrice(unitPrice);
  } else {
    console.warn('displayPrice element not found');
  }

  // Update subtotal (using class selector as it might not have ID)
  const subtotalValue = document.querySelector('.subtotal-value');
  if (subtotalValue) {
    subtotalValue.textContent = formatPrice(subtotal);
  } else {
    console.warn('subtotal-value element not found');
  }

  // NEW: Update Order Summary List
  updateOrderSummary();
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
 */
function increaseQuantity() {
  quantity++;
  const input = document.getElementById('quantityInput');
  if (input) input.value = quantity;
  updatePriceAndSubtotal();
}

/**
 * Handle manual quantity input
 */
function updateQuantityManual(val) {
  let v = parseInt(val);
  if (isNaN(v) || v < 1) v = 1;
  quantity = v;
  // Sync input value if needed (e.g. invalid chars)
  // const input = document.getElementById('quantityInput');
  // if(input && input.value != v) input.value = v; 
  updatePriceAndSubtotal();
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

  if (!statusEl) return;

  if (!file) {
    statusEl.innerHTML = '';
    uploadedFileName = null;
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

  // Validate file size (max 10MB)
  const fileSizeMB = file.size / (1024 * 1024);
  if (fileSizeMB > 10) {
    statusEl.innerHTML = `<div class="file-error">❌ Ukuran file terlalu besar. Maksimal 10MB</div>`;
    uploadedFileName = null;
    return;
  }

  // Valid file
  uploadedFileName = fileName;
  statusEl.innerHTML = `<div class="file-name">✓ ${fileName} (${fileSizeMB.toFixed(2)}MB)</div>`;
}

/**
 * Handle checkout button click (WhatsApp)
 */
function handleCheckout() {
  const unitPrice = calculateUnitPrice();
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
  let message = `🛒 *PESANAN BARU*\\n`;
  message += `━━━━━━━━━━━━━━━━\\n\\n`;

  message += `📦 *Produk:* ${productData.name}\\n`;
  message += `💰 *Harga Satuan:* ${formatPrice(unitPrice)}\\n`;
  message += `🔢 *Jumlah:* ${quantity} unit\\n`;

  // Add selected material if any
  if (selectedMaterialId && productData.options?.materials?.items) {
    const material = productData.options.materials.items.find(m => m.id == selectedMaterialId);
    if (material) {
      message += `🎨 *Bahan:* ${material.name}\\n`;
    }
  }

  // Add selected lamination if any
  if (selectedLaminationId && productData.options?.laminations?.items) {
    const lamination = productData.options.laminations.items.find(l => l.id == selectedLaminationId);
    if (lamination) {
      message += `✨ *Laminasi:* ${lamination.name}\\n`;
    }
  }

  // Add pricing options if any
  if (selectedOptions.pricing && productData.option_groups) {
    Object.entries(selectedOptions.pricing).forEach(([groupId, valueId]) => {
      const group = productData.option_groups.find(g => g.id == groupId);
      if (group && group.values) {
        const value = group.values.find(v => v.id == valueId);
        if (value) {
          message += `📏 *${group.name}:* ${value.label}\\n`;
        }
      }
    });
  }

  message += `\\n💵 *TOTAL:* ${formatPrice(subtotal)}\\n`;
  message += `━━━━━━━━━━━━━━━━\\n\\n`;

  // Add notes if provided
  if (currentNote && currentNote.trim() !== '') {
    message += `📝 *Catatan Khusus:*\\n${currentNote}\\n\\n`;
  }

  // Add file info if uploaded
  if (uploadedFileName) {
    message += `📎 *File Design:* ${uploadedFileName}\\n`;
    message += `_(File akan dikirim terpisah)_\\n\\n`;
  }

  message += `Mohon konfirmasi ketersediaan dan estimasi pengerjaan. Terima kasih! 🙏`;

  // Get WhatsApp number
  const waNumber = productData.category_whatsapp || window.EP_SETTINGS?.whatsapp || '6281234567890';
  const waUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(message)}`;

  // Show confirmation dialog with clear instructions
  const confirmMessage = `📱 Anda akan diarahkan ke WhatsApp\\n\\n` +
    `✅ Pesan sudah disiapkan otomatis\\n` +
    `✅ Tinggal klik tombol "Send" di WhatsApp\\n\\n` +
    `Lanjutkan ke WhatsApp?`;

  if (confirm(confirmMessage)) {
    // Open WhatsApp in new tab
    window.open(waUrl, '_blank');
    showToast('✓ Membuka WhatsApp... Silakan klik "Send" untuk mengirim pesanan');
  }
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
