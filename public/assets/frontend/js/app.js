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
  // Use regex patterns for accurate route matching

  // Product detail: /products/{slug} (has something after /products/)
  if (/\/products\/[^\/]+\/?$/.test(currentPage)) {
    if (typeof initProductDetailPage === 'function') initProductDetailPage();
  }
  // Product list: /products or /products/
  else if (/\/products\/?$/.test(currentPage)) {
    if (typeof initProductsPage === 'function') initProductsPage();
  }
  // Our Home
  else if (currentPage.includes('our-home')) {
    if (typeof initOurHomePage === 'function') initOurHomePage();
  }
  // Homepage
  else if (currentPage.includes('home') || currentPage.match(/\/public\/?$/) || currentPage === '/') {
    if (typeof initHomePage === 'function') initHomePage();
  }
  // Blog
  else if (currentPage.includes('blog') || currentPage.includes('articles')) {
    if (typeof initBlogPage === 'function') initBlogPage();
  }
  // Contact
  else if (currentPage.includes('contact')) {
    if (typeof initContactPage === 'function') initContactPage();
  }
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initPage);
} else {
  initPage();
}
