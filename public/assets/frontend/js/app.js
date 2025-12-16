// public/assets/frontend/js/app.js
// ============================================
// EventPrint - Main Application (Backend-safe)
// ============================================

async function initPartials() {
  try {
    const siteData = await loadData('data/site.json');
    await renderNavbar(siteData);
    await renderFooter(siteData);
  } catch (error) {
    console.error('Error initializing partials:', error);
  }
}

// Map href lama (.html) -> route backend
function normalizeHref(href) {
  const h = String(href || '').trim();
  if (!h) return '/';

  // biarin url external
  if (h.startsWith('http://') || h.startsWith('https://')) return h;

  // buang base url kalau kepasang dobel
  const base = (window.EP_BASE_URL || '').replace(/\/+$/, '');
  const cleaned = h.replace(base, '');

  // normalisasi
  const x = cleaned.replace(/^\/+/, '');

  // handle product detail with slug
  // product-detail.html?slug=xxx -> /product?slug=xxx
  if (x.startsWith('product-detail.html')) {
    const q = x.includes('?') ? x.slice(x.indexOf('?')) : '';
    return '/product' + q;
  }

  // pages
  if (x === 'home.html' || x === 'index.html') return '/';
  if (x === 'products.html') return '/products';
  if (x === 'our-home.html') return '/our-home';
  if (x === 'blog.html') return '/blog';
  if (x === 'contact.html') return '/contact';

  // kalau sudah route-style
  if (cleaned.startsWith('/')) return cleaned;

  // fallback: jadikan /<href>
  return '/' + x;
}

async function renderNavbar(siteData) {
  const navbarHTML = `
    <nav class="navbar">
      <div class="container">
        <a href="${route('/')}" class="navbar-brand">${siteData?.brand?.logoText || 'EventPrint'}</a>
        <ul class="navbar-nav">
          ${(siteData?.nav || []).map(item => {
            const fixed = normalizeHref(item.href);
            return `<li><a href="${route(fixed)}" class="nav-link">${item.label}</a></li>`;
          }).join('')}
        </ul>
      </div>
    </nav>
  `;

  const navbarContainer = document.getElementById('navbar');
  if (navbarContainer) {
    navbarContainer.innerHTML = navbarHTML;
    setActiveNavFromPath();
  }
}

async function renderFooter(siteData) {
  const footerHTML = `
    <footer class="footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-column">
            <h4>EventPrint</h4>
            <ul class="footer-links">
              <li><a href="${route('/')}">Home</a></li>
              <li><a href="${route('/products')}">All Product</a></li>
              <li><a href="${route('/our-home')}">Our Home</a></li>
              <li><a href="${route('/blog')}">Blog</a></li>
              <li><a href="${route('/contact')}">Contact</a></li>
            </ul>
          </div>

          <div class="footer-column">
            <h4>Produk Kami</h4>
            <ul class="footer-links">
              ${(siteData?.footer?.produk_kami || []).map(item => `
                <li><a href="${route('/products')}">${item}</a></li>
              `).join('')}
            </ul>
          </div>

          <div class="footer-column">
            <h4>Alamat</h4>
            <p class="footer-text">${siteData?.footer?.alamat || ''}</p>
            <h4 class="mt-3">Jam Operasional</h4>
            <div class="footer-text">
              ${(siteData?.footer?.jam_operasional || []).map(j => `<div>${j}</div>`).join('')}
            </div>
          </div>
        </div>
      </div>

      <div class="footer-bar">
        <p>${siteData?.footer?.copyright || ''}</p>
      </div>
    </footer>
  `;

  const footerContainer = document.getElementById('footer');
  if (footerContainer) footerContainer.innerHTML = footerHTML;
}

// route helper pakai EP_BASE_URL
function route(path) {
  const base = (window.EP_BASE_URL || '').replace(/\/+$/, '');
  const p = String(path || '').trim();
  if (!p) return base + '/';
  if (p.startsWith('http')) return p;
  return base + (p.startsWith('/') ? p : '/' + p);
}

function setActiveNavFromPath() {
  const path = window.location.pathname.replace(/\/+$/, '');
  document.querySelectorAll('.nav-link').forEach(link => {
    const href = link.getAttribute('href') || '';
    const base = (window.EP_BASE_URL || '').replace(/\/+$/, '');
    const target = href.replace(base, '').replace(/\/+$/, '');
    const active = target && path === target;
    link.classList.toggle('active', !!active);
  });
}

async function initPage() {
  try {
    await initPartials();

    const page = (window.EP_PAGE || '').toString();

    if (page === 'product_detail') { if (typeof initProductDetailPage === 'function') initProductDetailPage(); return; }
    if (page === 'products') { if (typeof initProductsPage === 'function') initProductsPage(); return; }
    if (page === 'home') { if (typeof initHomePage === 'function') initHomePage(); return; }
    if (page === 'blog') { if (typeof initBlogPage === 'function') initBlogPage(); return; }
    if (page === 'contact') { if (typeof initContactPage === 'function') initContactPage(); return; }
    if (page === 'our_home') { if (typeof initOurHomePage === 'function') initOurHomePage(); return; }

    console.warn('EP_PAGE kosong / tidak dikenal:', page);
  } catch (e) {
    console.error('initPage failed:', e);
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initPage);
} else {
  initPage();
}
