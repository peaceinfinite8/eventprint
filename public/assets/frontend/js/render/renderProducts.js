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

    // Load data from PHP API
    const productsData = await loadData('/api/products');
    const categoriesData = await loadData('/api/categories');

    if (!productsData || !productsData.success) {
      showError('productGrid', 'Gagal memuat data produk.');
      return;
    }

    allProducts = productsData.products || [];
    allCategories = categoriesData && categoriesData.success ? categoriesData.categories : [];

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
  const container = document.getElementById('categorySidebar');
  if (!container) return;

  container.addEventListener('click', (e) => {
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
 * Render Sidebar
 */
function renderSidebar(activeCategory) {
  const container = document.getElementById('categorySidebar');
  if (!container) return;

  const baseUrl = window.EP_BASE_URL || '';

  // Add "Semua Produk" option
  let html = `
    <div class="sidebar-item ${!activeCategory ? 'active' : ''}" data-action="reset">
      <span>Semua Produk</span>
    </div>
  `;

  // Render categories
  html += allCategories.map(cat => {
    const isActive = activeCategory === cat.slug || activeCategory === String(cat.id);
    return `
      <div class="sidebar-item ${isActive ? 'active' : ''}" data-category-id="${cat.slug || cat.id}">
        <span>${cat.icon || '📦'} ${cat.name}</span>
        ${cat.product_count ? `<span class="category-count">(${cat.product_count})</span>` : ''}
      </div>
    `;
  }).join('');

  container.innerHTML = html;
}

/**
 * Filter and Render Products
 */
function filterAndRenderProducts(categoryFilter) {
  let filtered = allProducts;

  if (categoryFilter) {
    filtered = allProducts.filter(p => {
      return p.category_slug === categoryFilter ||
        p.category_id === categoryFilter ||
        String(p.category_id) === categoryFilter;
    });
  }

  console.log(`[Filter] Total: ${allProducts.length}, Filtered: ${filtered.length}`);

  const grid = document.getElementById('productGrid');
  if (!grid) return;

  if (filtered.length === 0) {
    showEmpty('productGrid', 'Produk untuk kategori ini belum tersedia.');
    return;
  }

  const baseUrl = window.EP_BASE_URL || '';
  const html = filtered.map(product => {
    const productUrl = `${baseUrl}/products/${product.slug || product.id}`;
    const imageUrl = product.main_image || product.image || product.thumbnail || '';

    return `
      <a href="${productUrl}" class="product-card-link">
        <div class="product-card">
          <div class="product-card-image">
            ${imageUrl ?
        `<img src="${imageUrl}" alt="${product.name}" loading="lazy">` :
        `<img src="https://placehold.co/400x300?text=${encodeURIComponent(product.name)}" alt="${product.name}">`
      }
          </div>
          <div class="product-card-info">
            <h3 class="product-card-name">${product.name}</h3>
            <p class="product-card-price">${formatPrice(product.base_price || product.price || 0)}</p>
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
    titleEl.innerHTML = `Product > <span>${category.name}</span>`;
  } else {
    titleEl.innerHTML = 'Product';
  }
}
