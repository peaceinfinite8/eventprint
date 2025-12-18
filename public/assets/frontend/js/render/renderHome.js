// ============================================
// EventPrint - Homepage Renderer (PHP API Version)
// ============================================

let currentSlide = 0;
let bannerData = [];
let carouselInterval = null;

/**
 * Initialize homepage
 */
async function initHomePage() {
  try {
    showLoading('bannerCarousel', 1);
    showLoading('categories', 3);
    showLoading('featuredProducts', 4);
    showLoading('printProducts', 4);
    showLoading('mediaProducts', 4);

    // Load data from PHP API
    const homeData = await loadData('/api/home');
    const categoriesData = await loadData('/api/categories');

    if (homeData && homeData.success) {
      renderBannerCarousel(homeData.banners || []);
      renderProductGrid(homeData.featuredProducts || [], 'featuredProducts');
      renderProductGrid(homeData.printProducts || [], 'printProducts');
      renderProductGrid(homeData.mediaProducts || [], 'mediaProducts');
      renderWhyChoose(homeData.whyChoose);
    }

    if (categoriesData && categoriesData.success) {
      renderCategories(categoriesData.categories || []);
    }

  } catch (error) {
    console.error('Error loading home page:', error);
    showError('bannerCarousel', 'Gagal memuat data. Silakan refresh halaman.');
  }
}

/**
 * Render banner carousel
 */
function renderBannerCarousel(banners) {
  const container = document.getElementById('bannerCarousel');
  if (!container) return;

  if (!banners || banners.length === 0) {
    showEmpty('bannerCarousel', 'Banner tidak tersedia');
    return;
  }

  bannerData = banners;
  currentSlide = 0;

  const baseUrl = window.EP_BASE_URL || '';
  const firstBanner = banners[0];
  const bgImage = firstBanner.image_url || firstBanner.image || '';

  const html = `
    <div class="banner-slide" style="${bgImage ? `background-image: url('${bgImage}');` : ''}">
      <div class="hero__inner">
        <h1 class="banner-title">${firstBanner.title || ''}</h1>
        <p class="banner-subtitle">${firstBanner.subtitle || firstBanner.description || ''}</p>
        <a href="${baseUrl}/products" class="btn btn-primary">${firstBanner.button_text || 'Lihat Produk'}</a>
      </div>
    </div>
    <button class="carousel-arrow left" onclick="previousSlide()">‹</button>
    <button class="carousel-arrow right" onclick="nextSlide()">›</button>
    ${createCarouselDots(banners.length, 0)}
  `;

  container.innerHTML = html;

  const dots = container.querySelectorAll('.carousel-dot');
  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => goToSlide(index));
  });

  if (banners.length > 1) {
    startCarousel();
  }
}

function goToSlide(index) {
  currentSlide = index;
  updateCarousel();
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

  const banner = bannerData[currentSlide];
  const slideContent = container.querySelector('.banner-slide');
  const baseUrl = window.EP_BASE_URL || '';

  if (slideContent) {
    const bgImage = banner.image_url || banner.image || '';
    slideContent.style.backgroundImage = bgImage ? `url('${bgImage}')` : '';

    slideContent.innerHTML = `
      <div class="hero__inner">
        <h1 class="banner-title">${banner.title || ''}</h1>
        <p class="banner-subtitle">${banner.subtitle || banner.description || ''}</p>
        <a href="${baseUrl}/products" class="btn btn-primary">${banner.button_text || 'Lihat Produk'}</a>
      </div>
    `;
  }

  const dots = container.querySelectorAll('.carousel-dot');
  dots.forEach((dot, index) => {
    dot.classList.toggle('active', index === currentSlide);
  });
}

function startCarousel() {
  carouselInterval = setInterval(() => nextSlide(), 5000);
}

function resetCarousel() {
  if (carouselInterval) clearInterval(carouselInterval);
  startCarousel();
}

/**
 * Render categories icon row
 */
function renderCategories(categories) {
  const container = document.getElementById('categories');
  if (!container) return;

  if (!categories || categories.length === 0) {
    return;
  }

  const baseUrl = window.EP_BASE_URL || '';
  const html = categories.map(category => `
    <div class="category-item" onclick="window.location.href='${baseUrl}/products?category=${category.slug || category.id}'">
      <div class="category-icon">${category.icon || '📦'}</div>
      <div class="category-name">${category.name}</div>
    </div>
  `).join('');

  container.innerHTML = html;
  setupServicesCarousel();
}

function setupServicesCarousel() {
  const track = document.querySelector('[data-services-track]');
  const prevBtn = document.getElementById('servPrev');
  const nextBtn = document.getElementById('servNext');

  if (!track || !prevBtn || !nextBtn) return;

  const getStep = () => track.clientWidth * 0.7;

  nextBtn.addEventListener('click', () => {
    track.scrollBy({ left: getStep(), behavior: 'smooth' });
  });

  prevBtn.addEventListener('click', () => {
    track.scrollBy({ left: -getStep(), behavior: 'smooth' });
  });

  const updateButtons = () => {
    prevBtn.disabled = track.scrollLeft <= 5;
    const maxScroll = track.scrollWidth - track.clientWidth;
    nextBtn.disabled = track.scrollLeft >= maxScroll - 5;
  };

  track.addEventListener('scroll', updateButtons);
  setTimeout(updateButtons, 100);
}

/**
 * Render product grid
 */
function renderProductGrid(products, containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;

  if (!products || products.length === 0) {
    showEmpty(containerId, 'Produk belum tersedia');
    return;
  }

  const baseUrl = window.EP_BASE_URL || '';
  const html = products.map(product => `
    <a href="${baseUrl}/products/${product.slug || product.id}" class="product-card-link">
      <div class="product-card">
        <div class="product-card-image">
          ${product.main_image || product.image ?
      `<img src="${product.main_image || product.image}" alt="${product.name}">` :
      '<span>Gambar Produk</span>'}
        </div>
        <div class="product-card-info">
          <h3 class="product-card-name">${product.name}</h3>
          <p class="product-card-price">${formatPrice(product.base_price || product.price || 0)}</p>
        </div>
      </div>
    </a>
  `).join('');

  container.innerHTML = html;
}

/**
 * Render Why Choose Section
 */
function renderWhyChoose(data) {
  if (!data) return;

  const container = document.getElementById('whyChooseSection');
  if (!container) return;

  container.innerHTML = `
    <div class="why-choose-row">
      <div class="why-choose-media">
        <img src="${data.image || ''}" alt="Why Choose EventPrint" onerror="this.style.display='none';">
      </div>
      <div class="why-choose-content">
        <h2 class="why-choose-title">${data.title || 'Mengapa Memilih Kami?'}</h2>
        ${data.subtitle ? `<h3 class="why-choose-subtitle">${data.subtitle}</h3>` : ''}
        ${Array.isArray(data.description) ?
      data.description.map(p => `<p class="why-choose-text">${p}</p>`).join('') :
      `<p class="why-choose-text">${data.description || ''}</p>`}
      </div>
    </div>
  `;
}

window.addEventListener('beforeunload', () => {
  if (carouselInterval) clearInterval(carouselInterval);
});
