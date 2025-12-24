// ============================================
// EventPrint - Products Page Renderer (PHP API Version)
// ============================================

let allProducts = [];
let allCategories = [];

/**
 * Initialize Products Page
 */
async function initProductsPage() {
  try {
    showLoading('productGrid', 6);
    showLoading('categorySidebar', 3);

    // Get category filter from URL
    const urlParams = new URLSearchParams(window.location.search);
    const categoryFilter = urlParams.get('category');

    // Load data from PHP API with category filter (server-side filtering)
    let productsUrl = '/api/products';
    if (categoryFilter) {
      productsUrl += `?category=${encodeURIComponent(categoryFilter)}`;
    }

    const productsData = await loadData(productsUrl);
    const categoriesData = await loadData('/api/categories');

    if (!productsData || !productsData.success) {
      showError('productGrid', 'Gagal memuat data produk.');
      return;
    }

    allProducts = productsData.products || [];

    // Handle both API response formats:
    // Format 1: {success: true, categories: [...]}
    // Format 2: {ok: true, data: {categories: [...]}}
    if (categoriesData) {
      if (categoriesData.success && categoriesData.categories) {
        allCategories = categoriesData.categories;
      } else if (categoriesData.ok && categoriesData.data && categoriesData.data.categories) {
        allCategories = categoriesData.data.categories;
      } else {
        allCategories = [];
      }
    } else {
      allCategories = [];
    }

    console.log(`[Init] Loaded ${allProducts.length} products and ${allCategories.length} categories.`);

    setupSidebarEvents();
    handleUrlState();

    window.addEventListener('popstate', handleUrlState);

  } catch (error) {
    console.error('Error init products page:', error);
    showError('productGrid', 'Terjadi kesalahan sistem.');
  }
}

/**
 * Setup Sidebar Event Delegation
 */
function setupSidebarEvents() {
  const sidebar = document.getElementById('categorySidebar');
  if (!sidebar) return;

  // GUARD: Prevent duplicate binding
  if (sidebar.dataset.sidebarBound === '1') return;
  sidebar.dataset.sidebarBound = '1';

  // Use event delegation on the sidebar container
  sidebar.addEventListener('click', (e) => {
    // Handle accordion toggle
    const headBtn = e.target.closest('.sidebar-head');
    if (headBtn) {
      e.preventDefault();
      const isExpanded = headBtn.getAttribute('aria-expanded') === 'true';
      headBtn.setAttribute('aria-expanded', !isExpanded);

      const body = headBtn.nextElementSibling;
      if (body && body.classList.contains('sidebar-body')) {
        body.hidden = isExpanded;
      }
      return;
    }

    // Handle category/subcategory click
    const catItem = e.target.closest('[data-category-id]');
    if (catItem) {
      e.preventDefault();
      const categoryId = catItem.dataset.categoryId;
      const baseUrl = window.EP_BASE_URL || '';
      window.location.href = `${baseUrl}/products?category=${categoryId}`;
      return;
    }

    // Handle filter reset
    const resetBtn = e.target.closest('[data-action="reset"]');
    if (resetBtn) {
      e.preventDefault();
      const baseUrl = window.EP_BASE_URL || '';
      window.location.href = `${baseUrl}/products`;
    }
  });
}

/**
 * Handle URL State
 */
function handleUrlState() {
  const urlParams = new URLSearchParams(window.location.search);
  const categoryFilter = urlParams.get('category');

  renderSidebar(categoryFilter);
  filterAndRenderProducts(categoryFilter);
  updateBreadcrumbs(categoryFilter);
}

/**
 * Render Sidebar with Grouped Categories (Digital Printing, Media Promosi, Sticker)
 */
function renderSidebar(activeCategory) {
  const container = document.getElementById('categorySidebar');
  if (!container) return;

  const baseUrl = window.EP_BASE_URL || '';

  // Arrow SVG icon for accordion
  const arrowIcon = `<svg class="category-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
  </svg>`;

  // Category grouping mapping
  const categoryGroups = {
    'Digital Printing': [
      'spanduk-banner',
      'poster-flyer',
      'brosur-katalog',
      'kartu-nama-stationery',
      'kalender',
      'undangan-kartu-ucapan'
    ],
    'Media Promosi': [
      'id-card-lanyard',
      'packaging-box',
      'akrilik-signage',
      'standee-display'
    ],
    'Sticker': [
      'sticker-label',
      'cutting-sticker',
      'sticker-vinyl'
    ]
  };

  // Group categories
  const groupedCategories = {
    'Digital Printing': [],
    'Media Promosi': [],
    'Sticker': []
  };

  // Categorize each category into groups
  allCategories.forEach(cat => {
    let grouped = false;
    for (const [groupName, slugs] of Object.entries(categoryGroups)) {
      if (slugs.includes(cat.slug)) {
        groupedCategories[groupName].push(cat);
        grouped = true;
        break;
      }
    }
    // If no match, add to Digital Printing as default
    if (!grouped) {
      groupedCategories['Digital Printing'].push(cat);
    }
  });

  // Start with "Semua Produk"
  let html = `
    <li class="sidebar-group">
      <a href="${baseUrl}/products" class="sidebar-item ${!activeCategory ? 'active' : ''}" data-action="reset" style="padding-left: 12px;">
        Semua Produk
      </a>
    </li>
  `;

  // Render each parent group
  Object.entries(groupedCategories).forEach(([groupName, categories]) => {
    if (categories.length === 0) return;

    // Check if any category in this group is active
    const hasActiveCategory = categories.some(cat =>
      activeCategory === cat.slug || activeCategory === String(cat.id)
    );

    html += `
      <li class="sidebar-group">
        <button class="sidebar-head ${hasActiveCategory ? 'active' : ''}" 
                aria-expanded="${hasActiveCategory ? 'true' : 'false'}" 
                type="button">
          <span>${groupName}</span>
          ${arrowIcon}
        </button>
        <div class="sidebar-body" ${hasActiveCategory ? '' : 'hidden'}>
    `;

    // Render categories under this group
    categories.forEach(cat => {
      const isActive = activeCategory === cat.slug || activeCategory === String(cat.id);
      html += `
          <a href="${baseUrl}/products?category=${cat.slug || cat.id}" 
             class="sidebar-item ${isActive ? 'active' : ''}" 
             data-category-id="${cat.slug || cat.id}">
            ${cat.name}
          </a>
      `;
    });

    html += `
        </div>
      </li>
    `;
  });

  container.innerHTML = html;
}

/**
 * Filter and Render Products (Server-side filtering)
 */
function filterAndRenderProducts(categoryFilter) {
  // Products are already filtered by server, just render them
  const products = allProducts;

  console.log(`[Render] Displaying ${products.length} products`);

  const grid = document.getElementById('productGrid');
  if (!grid) return;

  if (products.length === 0) {
    showEmpty('productGrid', 'Produk untuk kategori ini belum tersedia.');
    return;
  }

  const baseUrl = window.EP_BASE_URL || '';
  const html = products.map(product => {
    const productUrl = `${baseUrl}/products/${product.slug || product.id}`;
    const imageUrl = product.main_image || product.image || product.thumbnail || '';
    const stock = parseInt(product.stock) || 0;
    const isOutOfStock = stock <= 0;

    // Discount calculation
    let discountBadgeHtml = '';
    const discountValue = parseFloat(product.discount_value || 0);
    const basePrice = parseFloat(product.base_price || product.price || 0);
    let finalPrice = basePrice;
    let percent = 0;

    if (discountValue > 0) {
      if (product.discount_type === 'percentage' || product.discount_type === 'percent') {
        percent = discountValue;
        finalPrice = basePrice - (basePrice * percent / 100);
      } else if (product.discount_type === 'fixed' && basePrice > 0) {
        percent = (discountValue / basePrice) * 100;
        finalPrice = basePrice - discountValue;
      }

      // Format to 0 decimal places if whole number, otherwise 1 decimal
      const percentDisplay = percent % 1 === 0 ? percent.toFixed(0) : percent.toFixed(1);

      if (percent > 0) {
        discountBadgeHtml = `
          <div style="position: absolute; top: 10px; right: 10px; background: #ef4444; color: white; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 2;">
            Hemat ${percentDisplay}%
          </div>
        `;
      }
    }

    // Price display with out-of-stock styling
    let priceHtml = '';
    if (isOutOfStock) {
      priceHtml = `<p class="product-card-price out-of-stock">
           <span class="strikethrough">${formatPrice(basePrice)}</span>
           <span class="stock-label">Stok Habis</span>
         </p>`;
    } else {
      if (discountValue > 0 && finalPrice < basePrice) {
        priceHtml = `<p class="product-card-price">
              <span style="text-decoration: line-through; color: #9ca3af; font-size: 0.875rem;">${formatPrice(basePrice)}</span>
              <span style="color: #ef4444; font-weight: bold; margin-left: 4px;">${formatPrice(finalPrice)}</span>
          </p>`;
      } else {
        priceHtml = `<p class="product-card-price">${formatPrice(basePrice)}</p>`;
      }
    }

    return `
      <a href="${productUrl}" class="product-card-link ${isOutOfStock ? 'out-of-stock' : ''}">
        <div class="product-card ${isOutOfStock ? 'out-of-stock' : ''}" style="position: relative;">
          ${discountBadgeHtml}
          <div class="product-card-image">
            ${imageUrl ?
        `<img src="${imageUrl}" alt="${product.name}" loading="lazy">` :
        `<div style="background: #f3f4f6; display: flex; align-items: center; justify-content: center; height: 100%; color: #9ca3af; font-size: 0.875rem; text-align: center; padding: 16px;">${product.name}</div>`
      }
            ${isOutOfStock ? '<div class="out-of-stock-overlay">Stok Habis</div>' : ''}
          </div>
          <div class="product-card-info">
            <h3 class="product-card-name">${product.name}</h3>
            ${priceHtml}
          </div>
        </div>
      </a>
    `;
  }).join('');

  grid.innerHTML = html;
}

/**
 * Update Breadcrumbs
 */
function updateBreadcrumbs(categoryFilter) {
  const titleEl = document.getElementById('pageTitle');
  if (!titleEl) return;

  if (!categoryFilter) {
    titleEl.innerHTML = 'Product';
    return;
  }

  const category = allCategories.find(c =>
    c.slug === categoryFilter || String(c.id) === categoryFilter
  );

  if (category) {
    titleEl.innerHTML = `Product > <span class="current-category-name">${category.name}</span>`;
  } else {
    titleEl.innerHTML = 'Product';
  }
}
