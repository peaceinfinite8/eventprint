// ============================================
// EventPrint - Main Application
// ============================================

/**
 * Initialize navbar and footer partials with site data
 * Note: In PHP mode, navbar and footer are rendered server-side,
 * so this function is optional and won't break the page if site.json is missing
 */
async function initPartials() {
  try {
    const siteData = await loadData('../data/site.json');
    await renderNavbar(siteData);
    await renderFooter(siteData);
    renderMobileFab(siteData.nav);
  } catch (error) {
    // Silently handle missing site.json - PHP views already render navbar/footer
    console.log('Note: site.json not found. Using PHP-rendered partials instead.');
  }
}

/**
 * Render Mobile FAB Navigation
 */
function renderMobileFab(navData) {
  // Only render if not already present
  if (document.getElementById('fabNav')) return;

  const activePage = getCurrentPage();

  const fabHTML = `
    <div id="fabNav" class="fab-nav-container">
      <div class="fab-overlay" onclick="toggleFabNav()"></div>
      <div class="fab-menu-panel">
        <div class="fab-menu-header">
          <h5>Menu</h5>
          <button class="fab-close" onclick="toggleFabNav()">&times;</button>
        </div>
        <ul class="fab-menu-list">
          ${navData.map(item => `
            <li>
              <a href="${item.href}" class="fab-link ${item.href.includes(activePage) ? 'active' : ''}">
                ${item.label}
              </a>
            </li>
          `).join('')}
        </ul>
      </div>
      <button class="fab-main-btn" onclick="toggleFabNav()">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
        </svg>
      </button>
    </div>
  `;

  document.body.insertAdjacentHTML('beforeend', fabHTML);
}

// Global toggle function
window.toggleFabNav = function () {
  const container = document.getElementById('fabNav');
  if (container) {
    container.classList.toggle('open');
  }
};

/**
 * Render navbar with site data
 */
async function renderNavbar(siteData) {
  // 1. Render Topbar
  const topbarHTML = siteData.topbar ? `
    <div class="topbar--fullbleed">
      <div class="topbar__inner">
        <span class="topbar-text">${siteData.topbar}</span>
        ${siteData.topbarWhatsapp ? `
          <a href="https://wa.me/${siteData.topbarWhatsapp.number}" target="_blank" class="topbar-cta">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
            </svg>
            ${siteData.topbarWhatsapp.label}
          </a>
        ` : ''}
      </div>
    </div>
  ` : '';

  const navbarHTML = `
    ${topbarHTML}
    <nav class="navbar">
      <div class="container">
        <a href="home.html" class="navbar-brand">${siteData.brand.logoText}</a>
        
        <div id="navSearchContainer"></div>
        
        <ul class="navbar-nav desktop-nav">
          ${siteData.nav.map(item => `
            <li><a href="${item.href}" class="nav-link">${item.label}</a></li>
          `).join('')}
        </ul>
      </div>
    </nav>
  `;

  const navbarContainer = document.getElementById('navbar');
  if (navbarContainer) {
    navbarContainer.innerHTML = navbarHTML;
    setActiveNav(getCurrentPage());

    // Initialize Search
    if (typeof initNavSearch === 'function') {
      initNavSearch();
    }
  }
}

/**
 * Render footer with site data
 */
async function renderFooter(siteData) {
  const footerHTML = `
    <footer class="footer">
      <div class="container footer-wrapper-flex">
        <!-- Footer Content (Links) -->
        <div class="footer-content">
          <div class="footer-column">
            <h4>EventPrint</h4>
            <ul class="footer-links">
              <li><a href="home.html">Home</a></li>
              <li><a href="products.html">All Product</a></li>
              <li><a href="our-home.html">Our Home</a></li>
              <li><a href="blog.html">Blog</a></li>
              <li><a href="contact.html">Contact</a></li>
            </ul>
          </div>
          
          <div class="footer-column">
            <h4>Produk Kami</h4>
            <ul class="footer-links">
              ${siteData.footer.produk_kami.map(item => `
                <li><a href="products.html">${item}</a></li>
              `).join('')}
            </ul>
          </div>
          
          <div class="footer-column">
            <h4>Alamat</h4>
            <p class="footer-text">${siteData.footer.alamat}</p>
            <h4 class="mt-3">Jam Operasional</h4>
            <div class="footer-text">
              ${siteData.footer.jam_operasional.map(jam => `<div>${jam}</div>`).join('')}
            </div>
          </div>
        </div>
      
        <!-- Footer Extras (Social & Payment) -->
        <div class="footer-extras">
          
          <!-- Social Media -->
          <div class="footer-extra-panel">
            <h5>Ikuti Kami</h5>
            <div class="footer-social-icons">
               ${siteData.footer.social ? siteData.footer.social.map(soc => `
                 <a href="${soc.url}" class="social-icon-btn" aria-label="${soc.label}" target="_blank">
                   <!-- Placeholder Icon SVG -->
                   <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-2 16h-2v-6h2v-2c0-1.1.9-2 2-2h2v2h-2v2h2l-1 2h-1v6z"/></svg>
                 </a>
               `).join('') : ''}
            </div>
          </div>

          <!-- Payment Methods -->
          <div class="footer-extra-panel">
            <h5>Pembayaran</h5>
            <div class="footer-payment-icons">
               ${siteData.footer.payment ? siteData.footer.payment.map(pay => `
                 <div class="payment-icon" title="${pay.label}">
                   <img src="${pay.image}" alt="${pay.label}">
                 </div>
               `).join('') : ''}
            </div>
          </div>
          
        </div>
      </div>
      
      <div class="footer-bar">
        <p>${siteData.footer.copyright}</p>
      </div>
    </footer>
  `;

  const footerContainer = document.getElementById('footer');
  if (footerContainer) {
    footerContainer.innerHTML = footerHTML;
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
  } else if (currentPage.includes('home')) {
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
