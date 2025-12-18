// ============================================
// EventPrint - Products Page Renderer
// ============================================

let allProducts = [];
let allCategories = [];

/**
 * Initialize Products Page
 */
async function initProductsPage() {
  try {
    // Show loading state
    showLoading('productGrid', 6);

    // Load data
    const data = await loadData('../data/products.json');
    if (!data) {
      showError('productGrid', 'Gagal memuat data produk.');
      return;
    }

    allProducts = data.products;
    allCategories = data.categories;

    console.log(`[Init] Loaded ${allProducts.length} products and ${allCategories.length} categories.`);

    // Setup Global Event Listener for Sidebar (Event Delegation)
    setupSidebarEvents();

    // Initial render based on URL
    handleUrlState();

    // Listen to browser history changes
    window.addEventListener('popstate', handleUrlState);

  } catch (error) {
    console.error('Error init products page:', error);
    showError('productGrid', 'Terjadi kesalahan sistem.');
  }
}

/**
 * Setup Sidebar Event Delegation (Once)
 */
function setupSidebarEvents() {
  const container = document.getElementById('categorySidebar');
  if (!container) return;

  // Remove existing listeners by cloning (simple trick if needed, but assuming init runs once)
  // container.replaceWith(container.cloneNode(true)); 
  // But let's stick to simple efficient delegation:

  container.addEventListener('click', (e) => {
    // 1. Handle Subcategory Item Click
    const subItem = e.target.closest('.sidebar-item');
    if (subItem) {
      e.preventDefault(); // STOP href navigation if any

      const cat = subItem.dataset.cat;
      const sub = subItem.dataset.sub;

      console.log(`[Sidebar Click] Subcategory: Cat=${cat}, Sub=${sub}`);

      if (typeof updateQueryParams === 'function') {
        updateQueryParams({ cat, sub });
        handleUrlState();
      }
      return;
    }

    // 2. Handle "Semua Produk" / Reset
    const headBtn = e.target.closest('.sidebar-head');
    if (headBtn && headBtn.dataset.action === 'reset-all') {
      console.log('[Sidebar Click] Reset All');
      if (typeof updateQueryParams === 'function') {
        updateQueryParams({ cat: null, sub: null });
        handleUrlState();
      }
      return;
    }

    // 3. Handle Group Toggle
    if (headBtn && headBtn.hasAttribute('aria-expanded')) {
      const expanded = headBtn.getAttribute('aria-expanded') === 'true';
      const parentGroup = headBtn.closest('.sidebar-group');
      const groupBody = parentGroup ? parentGroup.querySelector('.sidebar-body') : null;

      console.log(`[Sidebar Click] Toggle Group: currently ${expanded ? 'Open' : 'Closed'}`);

      if (groupBody) {
        if (expanded) {
          // Close
          groupBody.setAttribute('hidden', '');
          headBtn.setAttribute('aria-expanded', 'false');
        } else {
          // Open
          groupBody.removeAttribute('hidden');
          headBtn.setAttribute('aria-expanded', 'true');
        }
      }
    }
  });
}

/**
 * Handle URL State and Routing
 */
function handleUrlState() {
  if (typeof getQueryParams !== 'function') {
    console.warn('urlState.js not loaded');
    return;
  }

  const { cat, sub } = getQueryParams();
  console.log(`[URL State] Processing: Cat=${cat}, Sub=${sub}`);

  renderSidebar(cat, sub);
  filterAndRenderProducts(cat, sub);
  updateBreadcrumbs(cat, sub);
}

/**
 * Render Sidebar (Strict Structure, HTML String Only)
 */
function renderSidebar(activeCatId, activeSubId) {
  const container = document.getElementById('categorySidebar');
  if (!container) return;

  const html = allCategories.map(cat => {
    if (!cat.id) return '';

    // Active State Logic
    const isActiveCategory = (activeCatId === cat.id);
    const hasSubcats = cat.subcategories && cat.subcategories.length > 0;

    // Determine expanded state: Expand if active
    const isExpanded = isActiveCategory;

    // Special Case: "Semua Produk"
    if (cat.id === 'all') {
      const isAllActive = !activeCatId || activeCatId === 'all';
      return `
        <li class="sidebar-group">
          <button type="button" 
                  class="sidebar-head ${isAllActive ? 'active' : ''}" 
                  data-action="reset-all">
            <span>${cat.name}</span>
          </button>
        </li>
       `;
    }

    // Regular Category
    let subBodyHtml = '';
    if (hasSubcats) {
      const subItems = cat.subcategories.map(sub => {
        const isSubActive = (activeSubId === sub.id);
        // data-cat & data-sub MUST MATCH products.json values
        return `
          <button type="button" 
                  class="sidebar-item ${isSubActive ? 'active' : ''}"
                  data-cat="${cat.id}" 
                  data-sub="${sub.id}">
             ${sub.name}
          </button>
        `;
      }).join('');

      subBodyHtml = `
        <div class="sidebar-body" id="group-${cat.id}" ${isExpanded ? '' : 'hidden'}>
           ${subItems}
        </div>
      `;
    }

    const headActiveClass = isActiveCategory ? 'active' : '';
    const ariaExpanded = isExpanded ? 'true' : 'false';

    return `
      <li class="sidebar-group">
        <button type="button" 
                class="sidebar-head ${headActiveClass}"
                aria-expanded="${ariaExpanded}">
          <span>${cat.name}</span>
          ${hasSubcats ? `
            <svg class="category-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 9l6 6 6-6"></path>
            </svg>
          ` : ''}
        </button>
        ${subBodyHtml}
      </li>
    `;
  }).join('');

  container.innerHTML = html;
}

/**
 * Filter and Render Products (Strict Match)
 */
function filterAndRenderProducts(catId, subId) {
  let filtered = allProducts;

  // Debug Logic
  console.log(`[Filter Operations] Total Items: ${allProducts.length}`);

  if (catId && catId !== 'all') {
    filtered = filtered.filter(p => p.categoryId === catId);
    console.log(`[Filter Operations] After Category Filter (${catId}): ${filtered.length}`);

    if (subId) {
      // STRICT COMPARISON: string vs string
      filtered = filtered.filter(p => p.subcategoryId === subId);
      console.log(`[Filter Operations] After SubCategory Filter (${subId}): ${filtered.length}`);
    }
  }

  const grid = document.getElementById('productGrid');
  if (!grid) return;

  if (filtered.length === 0) {
    showEmpty('productGrid', 'Produk untuk kategori/subkategori ini belum tersedia.');
    return;
  }

  const html = filtered.map(product => `
    <a href="product-detail.html?slug=${product.slug}" class="product-card-link">
      <div class="product-card">
        <div class="product-card-image">
           ${product.images && product.images[0]
      ? `<img src="${product.images[0]}" alt="${product.name}" loading="lazy">`
      : `<img src="https://placehold.co/400x300?text=${encodeURIComponent(product.name)}" alt="${product.name}">`
    }
        </div>
        <div class="product-card-info">
          <h3 class="product-card-name">${product.name}</h3>
          <p class="product-card-price">${formatPrice(product.base_price)}</p>
        </div>
      </div>
    </a>
  `).join('');

  grid.innerHTML = html;
}

/**
 * Update Breadcrumbs
 */
function updateBreadcrumbs(catId, subId) {
  const titleEl = document.getElementById('pageTitle');
  if (!titleEl) return;

  if (!catId || catId === 'all') {
    titleEl.innerHTML = 'Product';
    return;
  }

  const category = allCategories.find(c => c.id === catId);
  if (!category) return;

  let html = `Product > <span>${category.name}</span>`;

  if (subId) {
    const sub = category.subcategories?.find(s => s.id === subId);
    if (sub) {
      html += ` > <span class="current-category-name">${sub.name}</span>`;
    }
  }

  titleEl.innerHTML = html;
}
