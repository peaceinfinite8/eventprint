// ============================================
// EventPrint - Main Application (PHP Backend Version)
// ============================================

/**
 * Initialize navbar and footer partials with site data
 */
async function initPartials() {
  try {
    // For PHP backend, navbar and footer are already rendered server-side
    // We just need to check if search should be initialized
    if (typeof initNavSearch === 'function') {
      const container = document.getElementById('navSearchContainer');
      if (container) {
        initNavSearch();
      }
    }
  } catch (error) {
    console.error('Error initializing partials:', error);
  }
}

/**
 * Initialize page based on current route
 */
async function initPage() {
  const currentPage = getCurrentPage();

  // Initialize partials first
  await initPartials();

  // Load page-specific renderer
  // Check specific pages before generic ones to avoid false matches
  if (currentPage.includes('product-detail')) {
    if (typeof initProductDetailPage === 'function') initProductDetailPage();
  } else if (currentPage.includes('our-home')) {
    if (typeof initOurHomePage === 'function') initOurHomePage();
  } else if (currentPage.includes('products')) {
    if (typeof initProductsPage === 'function') initProductsPage();
  } else if (currentPage.includes('home') || currentPage === '/') {
    if (typeof initHomePage === 'function') initHomePage();
  } else if (currentPage.includes('blog')) {
    if (typeof initBlogPage === 'function') initBlogPage();
  } else if (currentPage.includes('contact')) {
    if (typeof initContactPage === 'function') initContactPage();
  }
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initPage);
} else {
  initPage();
}
