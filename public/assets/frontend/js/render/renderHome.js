// public/assets/frontend/js/render/renderHome.js
// ============================================
// EventPrint - Homepage Renderer (Backend-safe)
// ============================================

let currentSlide = 0;
let bannerData = [];
let carouselInterval = null;

function epRoute(path) {
  const base = (window.EP_BASE_URL || '').replace(/\/+$/, '');
  if (!path) return base + '/';
  if (String(path).startsWith('http')) return path;
  return base + (path.startsWith('/') ? path : '/' + path);
}

async function initHomePage() {
  try {
    showLoading('bannerCarousel', 1);
    showLoading('testimonials', 4);
    showLoading('featuredProducts', 4);
    showLoading('printProducts', 4);
    showLoading('mediaProducts', 4);

    const data = (window.EP_DATA_PRELOADED && typeof window.EP_DATA_PRELOADED === 'object')
      ? window.EP_DATA_PRELOADED
      : await loadData('data/home.json');

    renderBannerCarousel(data.banners || []);
    renderTestimonials(data.testimonials || []);
    renderCategories(data.categories || []);
    renderProductGrid(data.featuredProducts || [], 'featuredProducts');
    renderProductGrid(data.printProducts || [], 'printProducts');
    renderProductGrid(data.mediaProducts || [], 'mediaProducts');
    renderContactInfo(data.contact || {});

  } catch (error) {
    console.error('Error loading home page:', error);
    showError('bannerCarousel', 'Gagal memuat data. Silakan refresh halaman.');
  }
}

function renderBannerCarousel(banners) {
  const container = document.getElementById('bannerCarousel');
  if (!container || !banners || banners.length === 0) {
    showEmpty('bannerCarousel', 'Banner tidak tersedia');
    return;
  }

  bannerData = banners;
  currentSlide = 0;

  container.innerHTML = `
    <div class="banner-slide"></div>
    <button class="carousel-arrow left" type="button" onclick="previousSlide()">‹</button>
    <button class="carousel-arrow right" type="button" onclick="nextSlide()">›</button>
    ${createCarouselDots(banners.length, 0)}
  `;

  const dots = container.querySelectorAll('.carousel-dot');
  dots.forEach((dot, index) => dot.addEventListener('click', () => goToSlide(index)));

  updateCarousel();
  startCarousel();
}

function goToSlide(index) {
  currentSlide = index;
  updateCarousel();
  resetCarousel();
}

function nextSlide() {
  currentSlide = (currentSlide + 1) % bannerData.length;
  updateCarousel();
  resetCarousel();
}

function previousSlide() {
  currentSlide = (currentSlide - 1 + bannerData.length) % bannerData.length;
  updateCarousel();
  resetCarousel();
}

function updateCarousel() {
  const container = document.getElementById('bannerCarousel');
  if (!container) return;

  const banner = bannerData[currentSlide] || {};
  const slide = container.querySelector('.banner-slide');
  if (!slide) return;

  slide.innerHTML = `
    <h1 class="banner-title">${banner.title || ''}</h1>
    <p class="banner-subtitle">${banner.subtitle || ''}</p>
    <a href="${epRoute('/products')}" class="btn btn-primary">${banner.cta || 'Lihat Produk'}</a>
  `;

  const dots = container.querySelectorAll('.carousel-dot');
  dots.forEach((dot, i) => dot.classList.toggle('active', i === currentSlide));
}

function startCarousel() {
  carouselInterval = setInterval(() => nextSlide(), 5000);
}

function resetCarousel() {
  if (carouselInterval) clearInterval(carouselInterval);
  startCarousel();
}

function renderTestimonials(testimonials) {
  const container = document.getElementById('testimonials');
  if (!container) return;

  if (!testimonials || testimonials.length === 0) {
    showEmpty('testimonials', 'Testimoni belum tersedia');
    return;
  }

  container.innerHTML = testimonials.map(t => `
    <div class="testimonial-card">
      <div class="testimonial-stars">${createStars(t.stars || 5)}</div>
      <p class="testimonial-text">${t.text || ''}</p>
      <p class="testimonial-author">— ${t.author || 'Customer'}</p>
    </div>
  `).join('');
}

function renderCategories(categories) {
  const container = document.getElementById('categories');
  if (!container || !categories || categories.length === 0) return;

  // Klik category -> arahkan ke /products?category=...
  container.innerHTML = categories.map(c => `
    <a class="category-item" href="${epRoute(`/products?category=${encodeURIComponent(c.id || '')}`)}">
      <div class="category-icon">${c.icon || '🖨️'}</div>
      <div>${c.label || ''}</div>
    </a>
  `).join('');
}

function renderProductGrid(products, containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;

  if (!products || products.length === 0) {
    showEmpty(containerId, 'Produk belum tersedia');
    return;
  }

  container.innerHTML = products.map(p => `
    <a href="${epRoute(`/product?slug=${encodeURIComponent(p.slug || '')}`)}" class="product-card-link">
      <div class="product-card">
        <div class="product-card-image">
          ${p.image ? `<img src="${p.image}" alt="${p.name || ''}">` : '<span>Gambar Produk</span>'}
        </div>
        <div class="product-card-info">
          <h3 class="product-card-name">${p.name || ''}</h3>
          <p class="product-card-price">${formatPrice(p.price || 0)}</p>
        </div>
      </div>
    </a>
  `).join('');
}

function renderContactInfo(contact) {
  const container = document.getElementById('contactInfo');
  if (!container) return;

  container.innerHTML = `
    <div class="contact-detail"><div class="contact-icon">📍</div><div class="contact-text">${contact.address || ''}</div></div>
    <div class="contact-detail"><div class="contact-icon">✉️</div><div class="contact-text">${contact.email || ''}</div></div>
    <div class="contact-detail"><div class="contact-icon">💬</div><div class="contact-text">${contact.whatsapp || ''}</div></div>
  `;
}

window.addEventListener('beforeunload', () => {
  if (carouselInterval) clearInterval(carouselInterval);
});
