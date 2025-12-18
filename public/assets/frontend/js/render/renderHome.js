// ============================================
// EventPrint - Homepage Renderer
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
    showLoading('testimonials', 4);
    showLoading('featuredProducts', 4);
    showLoading('printProducts', 4);
    showLoading('mediaProducts', 4);

    const data = await loadData('../data/home.json');

    renderBannerCarousel(data.banners);
    renderTestimonials(data.testimonials);
    renderCategories(data.categories);
    renderProductGrid(data.featuredProducts, 'featuredProducts');
    renderProductGrid(data.printProducts, 'printProducts');
    renderProductGrid(data.mediaProducts, 'mediaProducts');
    renderWhyChoose(data.whyChoose);
    renderPromoCarousel(data.infrastructureGallery);

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
  if (!container || !banners || banners.length === 0) {
    showEmpty('bannerCarousel', 'Banner tidak tersedia');
    return;
  }

  bannerData = banners;
  currentSlide = 0;

  const html = `
    <div class="banner-slide" style="${banners[0].image ? `background-image: url('${banners[0].image}');` : ''}">
      <div class="hero__inner">
        <h1 class="banner-title">${banners[0].title}</h1>
        <p class="banner-subtitle">${banners[0].subtitle}</p>
        <a href="products.html" class="btn btn-primary">${banners[0].cta}</a>
      </div>
    </div>
    <button class="carousel-arrow left" onclick="previousSlide()">‹</button>
    <button class="carousel-arrow right" onclick="nextSlide()">›</button>
    ${createCarouselDots(banners.length, 0)}
  `;

  container.innerHTML = html;

  // Add dot click handlers
  const dots = container.querySelectorAll('.carousel-dot');
  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => goToSlide(index));
  });

  // Auto-advance carousel
  startCarousel();
}

/**
 * Navigate to specific slide
 */
function goToSlide(index) {
  currentSlide = index;
  updateCarousel();
}

/**
 * Go to next slide
 */
function nextSlide() {
  currentSlide = (currentSlide + 1) % bannerData.length;
  updateCarousel();
  resetCarousel();
}

/**
 * Go to previous slide
 */
function previousSlide() {
  currentSlide = (currentSlide - 1 + bannerData.length) % bannerData.length;
  updateCarousel();
  resetCarousel();
}

/**
 * Update carousel display
 */
function updateCarousel() {
  const container = document.getElementById('bannerCarousel');
  if (!container) return;

  const banner = bannerData[currentSlide];
  const slideContent = container.querySelector('.banner-slide');

  if (slideContent) {
    if (banner.image) {
      slideContent.style.backgroundImage = `url('${banner.image}')`;
    } else {
      slideContent.style.backgroundImage = '';
    }

    slideContent.innerHTML = `
      <div class="hero__inner">
        <h1 class="banner-title">${banner.title}</h1>
        <p class="banner-subtitle">${banner.subtitle}</p>
        <a href="products.html" class="btn btn-primary">${banner.cta}</a>
      </div>
    `;
  }

  // Update dots
  const dots = container.querySelectorAll('.carousel-dot');
  dots.forEach((dot, index) => {
    if (index === currentSlide) {
      dot.classList.add('active');
    } else {
      dot.classList.remove('active');
    }
  });
}

/**
 * Start auto-carousel
 */
function startCarousel() {
  carouselInterval = setInterval(() => {
    nextSlide();
  }, 5000);
}

/**
 * Reset carousel timer
 */
function resetCarousel() {
  if (carouselInterval) {
    clearInterval(carouselInterval);
  }
  startCarousel();
}

/**
 * Render testimonials
 */
function renderTestimonials(testimonials) {
  const container = document.getElementById('testimonials');
  if (!container) return;

  if (!testimonials || testimonials.length === 0) {
    showEmpty('testimonials', 'Testimoni belum tersedia');
    return;
  }

  const html = testimonials.map(testimonial => `
    <div class="testimonial-card">
      <div class="testimonial-stars">
        ${createStars(testimonial.stars)}
      </div>
      <p class="testimonial-text">${testimonial.text}</p>
      <p class="testimonial-author">— ${testimonial.author}</p>
    </div>
  `).join('');

  container.innerHTML = html;
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

  const html = categories.map(category => `
    <div class="category-item" onclick="window.location.href='products.html#${category.id}'">
      <div class="category-icon">${category.icon}</div>
      <div>${category.label}</div>
    </div>
  `).join('');

  container.innerHTML = html;
}

/**
 * Render product grid (reusable)
 */
function renderProductGrid(products, containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;

  if (!products || products.length === 0) {
    showEmpty(containerId, 'Produk belum tersedia');
    return;
  }

  const html = products.map(product => `
    <a href="product-detail.html?slug=${product.slug}" class="product-card-link">
      <div class="product-card">
        <div class="product-card-image">
          ${product.image ? `<img src="${product.image}" alt="${product.name}">` : '<span>Gambar Produk</span>'}
        </div>
        <div class="product-card-info">
          <h3 class="product-card-name">${product.name}</h3>
          <p class="product-card-price">${formatPrice(product.price)}</p>
        </div>
      </div>
    </a>
  `).join('');

  container.innerHTML = html;
}

/**
 * Render Promo Carousel
 */
function renderPromoCarousel(banners) {
  if (!banners || banners.length === 0) return;
  // Initialize the carousel class
  new SmallBannerCarousel('promoCarousel', banners);
}

/**
 * Render Why Choose Section
 */
function renderWhyChoose(data) {
  if (!data) return;

  const container = document.getElementById('whyChooseSection');
  if (container) {
    container.innerHTML = `
      <div class="why-choose-row">
        <div class="why-choose-media">
          <img src="${data.image}" alt="Why Choose EventPrint" onerror="this.style.display='none';">
        </div>
        <div class="why-choose-content">
          <h2 class="why-choose-title">${data.title}</h2>
          ${data.subtitle ? `<h3 class="why-choose-subtitle">${data.subtitle}</h3>` : ''}
          ${data.description.map(p => `<p class="why-choose-text">${p}</p>`).join('')}
        </div>
      </div>
      
      <!-- Mini Banner Carousel inside the same container section -->
      <div id="promoCarousel" class="promo-carousel">
        <!-- Rendered later by renderPromoCarousel -->
      </div>
    `;

    // Note: The element #promoCarousel is checked by renderPromoCarousel later. 
    // Since renderWhyChoose runs first, it recreates the DOM with #promoCarousel.
    // ensure renderPromoCarousel runs AFTER this.
  }
}

// NOTE: We need to ensure renderHome calls this order:
// 1. renderWhyChoose (creates #promoCarousel)
// 2. renderPromoCarousel (populates it)

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
  if (carouselInterval) {
    clearInterval(carouselInterval);
  }
});
