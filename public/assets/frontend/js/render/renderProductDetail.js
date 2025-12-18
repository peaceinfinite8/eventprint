// ============================================
// EventPrint - Product Detail Page Renderer (PHP API Version)
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

    showLoading('productDetailContent', 1);

    // Load from API
    const productsData = await loadData('/api/products');

    if (!productsData || !productsData.success || !productsData.products) {
      console.error('Failed to load products data');
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

    // Set default selections
    if (product.options && product.options.materials && product.options.materials.enabled && product.options.materials.items.length > 0) {
      selectedMaterialId = product.options.materials.items[0].id;
    }

    if (product.options && product.options.laminations && product.options.laminations.enabled && product.options.laminations.items.length > 0) {
      selectedLaminationId = product.options.laminations.items[0].id;
    }

    quantity = 1;
    renderProductDetail();

  } catch (error) {
    console.error('Error loading product detail:', error);
    renderProductNotFound('Gagal memuat produk. Silakan coba lagi.');
  }
}

/**
 * Get product slug from URL
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
 * Render product detail (simplified for now - add full detail later if needed)
 */
function renderProductDetail() {
  const container = document.getElementById('productDetailContent');
  if (!container) return;

  const html = `
    <div class="product-detail-container">
      <h1>${productData.name}</h1>
      <p>Product detail page - full detail rendering coming soon</p>
      <p>Price: ${formatPrice(productData.base_price || productData.price || 0)}</p>
      ${productData.description ? `<p>${productData.description}</p>` : ''}
    </div>
  `;

  container.innerHTML = html;
}
