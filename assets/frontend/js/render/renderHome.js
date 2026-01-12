// ============================================
// EventPrint - Homepage Renderer (PHP API Version)
// ============================================

/**
 * Small Banner Carousel (Vanilla JS)
 * Handles swipe, drag, auto-slide, dots, and arrows.
 * Revised: Enlarge & Arrows
 */
class SmallBannerCarousel {
  constructor(containerId, data) {
    this.container = document.getElementById(containerId);
    this.data = data;
    this.currentSlide = 0;
    this.interval = null;
    this.isDragging = false;
    this.startPos = 0;
    this.currentTranslate = 0;
    this.prevTranslate = 0;
    this.animationID = 0;
    this.wrapper = null;
    this.slideWidth = 0;

    if (this.container && this.data && this.data.length > 0) {
      this.init();
    }
  }

  init() {
    this.render();
    this.initEvents();
    this.startAutoSlide();
    this.updateDots();

    // Resize handler
    window.addEventListener('resize', () => {
      if (this.wrapper) {
        this.wrapper.style.transition = 'none';
        this.updatePosition();
        setTimeout(() => {
          this.wrapper.style.transition = 'transform 0.3s ease-out';
        }, 50);
      }
    });
  }

  render() {
    this.container.innerHTML = `
      <div class="promo-wrapper">
        ${this.data.map(item => `
          <div class="promo-slide">
             <a href="${item.link || '#'}" style="cursor: ${item.link ? 'pointer' : 'default'}">
                <img src="${item.image}" alt="${item.alt}" draggable="false" onerror="this.src=(window.EP_BASE_URL || '') + '/assets/frontend/images/placeholder-store.png'">
             </a>
          </div>
        `).join('')}
      </div>
      
      <button class="promo-arrow promo-prev" aria-label="Previous Slide">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
        </svg>
      </button>
      <button class="promo-arrow promo-next" aria-label="Next Slide">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
        </svg>
      </button>

      <div class="promo-dots">
        ${this.data.map((_, i) => `<div class="promo-dot" data-index="${i}"></div>`).join('')}
      </div>
    `;

    this.wrapper = this.container.querySelector('.promo-wrapper');
    this.wrapper.style.width = `${this.data.length * 100}%`;

    // FIX: Set explicit width for slides relative to wrapper
    const slideWidthPCT = (100 / this.data.length);
    this.container.querySelectorAll('.promo-slide').forEach(slide => {
      slide.style.width = `${slideWidthPCT}%`;
      slide.style.minWidth = `${slideWidthPCT}%`; // Override CSS min-width
      slide.style.flex = `0 0 ${slideWidthPCT}%`;
    });

    // Add dot listeners
    this.container.querySelectorAll('.promo-dot').forEach((dot, index) => {
      dot.addEventListener('click', (e) => {
        e.stopPropagation();
        this.goToSlide(index);
      });
    });

    // Add arrow listeners
    this.container.querySelector('.promo-prev').addEventListener('click', (e) => {
      e.stopPropagation();
      this.prevSlide();
    });
    this.container.querySelector('.promo-next').addEventListener('click', (e) => {
      e.stopPropagation();
      this.nextSlide();
    });
  }

  initEvents() {
    // Touch events
    this.wrapper.addEventListener('touchstart', this.touchStart.bind(this));
    this.wrapper.addEventListener('touchend', this.touchEnd.bind(this));
    this.wrapper.addEventListener('touchmove', this.touchMove.bind(this));

    // Mouse events
    this.wrapper.addEventListener('mousedown', this.touchStart.bind(this));
    this.wrapper.addEventListener('mouseup', this.touchEnd.bind(this));
    this.wrapper.addEventListener('mouseleave', () => {
      if (this.isDragging) this.touchEnd();
      this.startAutoSlide();
    });
    this.wrapper.addEventListener('mousemove', this.touchMove.bind(this));

    // Pause on hover (desktop)
    this.container.addEventListener('mouseenter', () => this.stopAutoSlide());
    this.container.addEventListener('mouseleave', () => this.startAutoSlide());
  }

  touchStart(event) {
    this.isDragging = true;
    this.stopAutoSlide();
    this.startPos = this.getPositionX(event);
    this.animationID = requestAnimationFrame(this.animation.bind(this));
    this.wrapper.style.cursor = 'grabbing';
    window.carouselDragging = false;
  }

  touchMove(event) {
    if (this.isDragging) {
      const currentPosition = this.getPositionX(event);
      if (Math.abs(currentPosition - this.startPos) > 5) {
        window.carouselDragging = true;
      }
      this.currentTranslate = this.prevTranslate + currentPosition - this.startPos;
    }
  }

  touchEnd() {
    this.isDragging = false;
    cancelAnimationFrame(this.animationID);
    this.wrapper.style.cursor = 'grab';

    const movedBy = this.currentTranslate - this.prevTranslate;

    if (movedBy < -50) {
      this.nextSlide();
    } else if (movedBy > 50) {
      this.prevSlide();
    } else {
      this.updatePosition();
    }
    this.startAutoSlide();
  }

  getPositionX(event) {
    return event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
  }

  animation() {
    this.setSliderPosition();
    if (this.isDragging) requestAnimationFrame(this.animation.bind(this));
  }

  setSliderPosition() {
    this.wrapper.style.transform = `translateX(${this.currentTranslate}px)`;
  }

  updatePosition() {
    const width = this.container.clientWidth;
    this.currentTranslate = this.currentSlide * -width;
    this.prevTranslate = this.currentTranslate;
    this.wrapper.style.transform = `translateX(${this.currentTranslate}px)`;
    this.updateDots();
  }

  goToSlide(index) {
    this.currentSlide = index;
    if (this.currentSlide < 0) this.currentSlide = this.data.length - 1;
    if (this.currentSlide >= this.data.length) this.currentSlide = 0;
    this.updatePosition();
  }

  nextSlide() {
    this.goToSlide(this.currentSlide + 1);
  }

  prevSlide() {
    this.goToSlide(this.currentSlide - 1);
  }

  updateDots() {
    this.container.querySelectorAll('.promo-dot').forEach((dot, index) => {
      dot.classList.toggle('active', index === this.currentSlide);
    });
  }

  startAutoSlide() {
    this.stopAutoSlide();
    this.interval = setInterval(() => {
      this.nextSlide();
    }, 5000);
  }

  stopAutoSlide() {
    if (this.interval) clearInterval(this.interval);
  }
}


let currentSlide = 0;
let bannerData = [];
let carouselInterval = null;

/**
 * Reusable carousel binder
 */
function bindCarousel({ track, prev, next, itemSelector, name }) {
  if (!track || !prev || !next) return false;

  // Prevent double-binding
  if (track.dataset.carouselBound === "1") return true;
  track.dataset.carouselBound = "1";

  const getStep = () => {
    const item = track.querySelector(itemSelector);
    if (!item) return 320;
    return item.getBoundingClientRect().width + 24;
  };

  const updateButtons = () => {
    const maxScroll = track.scrollWidth - track.clientWidth - 1;
    prev.disabled = track.scrollLeft <= 0;
    next.disabled = track.scrollLeft >= maxScroll;
    prev.style.opacity = prev.disabled ? '0.5' : '1';
    next.style.opacity = next.disabled ? '0.5' : '1';
  };

  prev.addEventListener("click", (e) => {
    e.preventDefault();
    track.scrollBy({ left: -getStep(), behavior: "smooth" });
    setTimeout(updateButtons, 300);
  });

  next.addEventListener("click", (e) => {
    e.preventDefault();
    track.scrollBy({ left: getStep(), behavior: "smooth" });
    setTimeout(updateButtons, 300);
  });

  track.addEventListener("scroll", updateButtons, { passive: true });
  window.addEventListener("resize", updateButtons, { passive: true });

  setTimeout(updateButtons, 100);
  return true;
}

/**
 * Initialize homepage
 */
async function initHomePage() {
  try {
    showLoading('bannerCarousel', 1);
    showLoading('categories', 3);
    showLoading('featuredProducts', 4);
    // showLoading('printProducts', 4);
    // showLoading('mediaProducts', 4);
    // showLoading('merchProducts', 4);

    const homeData = await loadData('/api/home');

    if (homeData && homeData.success) {
      renderBannerCarousel(homeData.banners || []);

      if (homeData.categories && homeData.categories.length > 0) {
        renderCategories(homeData.categories);
      }

      renderFeaturedProducts(homeData.featuredProducts || []);
      // renderProductGrid(homeData.printProducts || [], 'printProducts'); // REMOVED: SSR
      // renderProductGrid(homeData.mediaProducts || [], 'mediaProducts'); // REMOVED: SSR
      // renderProductGrid(homeData.merchProducts || [], 'merchProducts'); // REMOVED: SSR
      renderTestimonials(homeData.testimonials || []);
      renderWhyChoose(homeData.whyChoose);
      renderPromoCarousel(homeData.infrastructureGallery || []);
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

  // CTA Logic
  const ctaText = firstBanner.cta_text || firstBanner.button_text || firstBanner.cta || '';
  const ctaLink = firstBanner.cta_link || firstBanner.link || `${baseUrl}/products`;
  const btnHtml = ctaText ? `<a href="${ctaLink}" class="btn btn-primary">${ctaText}</a>` : '';

  const html = `
    <div class="banner-slide" style="${bgImage ? `background-image: url('${bgImage}');` : ''}">
      <div class="hero__inner">
        <h1 class="banner-title">${firstBanner.title || ''}</h1>
        <p class="banner-subtitle">${firstBanner.subtitle || firstBanner.description || ''}</p>
        ${btnHtml}
      </div>
    </div>
    <button type="button" class="carousel-arrow left" onclick="previousSlide()">‹</button>
    <button type="button" class="carousel-arrow right" onclick="nextSlide()">›</button>
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

    // CTA Logic
    const ctaText = banner.cta_text || banner.button_text || banner.cta || '';
    const ctaLink = banner.cta_link || banner.link || `${baseUrl}/products`;
    const btnHtml = ctaText ? `<a href="${ctaLink}" class="btn btn-primary">${ctaText}</a>` : '';

    slideContent.innerHTML = `
      <div class="hero__inner">
        <h1 class="banner-title">${banner.title || ''}</h1>
        <p class="banner-subtitle">${banner.subtitle || banner.description || ''}</p>
        ${btnHtml}
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

  if (!categories || categories.length === 0) return;

  const baseUrl = window.EP_BASE_URL || '';
  const html = categories.map(category => {
    const name = category.label || category.name || '';
    const slug = category.slug || category.id || '';
    const iconValue = category.icon || '';

    let iconHtml = '<span style="font-size: 2rem;">📦</span>';
    if (iconValue) {
      if (iconValue.includes('/') || iconValue.includes('.')) {
        // Image Icon
        iconHtml = `<img src="${baseUrl}/${iconValue}" alt="${name}" class="service-icon-img" style="width: 64px; height: 64px; object-fit: contain;">`;
      } else if (iconValue.startsWith('bi-') || iconValue.startsWith('fa-') || iconValue.includes(' ')) {
        // CSS Class Icon (Bootstrap/FontAwesome)
        iconHtml = `<i class="${iconValue} service-icon text-primary fs-1"></i>`;
      } else {
        // Default / Emoji / Text
        iconHtml = `<span style="font-size: 2rem;">${iconValue}</span>`;
      }
    }

    return `
    <div class="category-item" onclick="window.location.href='${baseUrl}/products?category=${encodeURIComponent(slug)}'">
      <div class="category-icon mb-3">${iconHtml}</div>
      <div class="category-name fw-bold small text-uppercase">${name}</div>
    </div>
  `;
  }).join('');

  container.innerHTML = html;
  setTimeout(() => setupServicesCarousel(), 100);
}

/**
 * Setup services carousel navigation
 */
function setupServicesCarousel() {
  const track = document.getElementById('categories');
  const prevBtn = document.getElementById('servPrev');
  const nextBtn = document.getElementById('servNext');

  bindCarousel({
    track: track,
    prev: prevBtn,
    next: nextBtn,
    itemSelector: '.category-item',
    name: 'ServicesCarousel'
  });
}

/**
 * Setup featured product carousel navigation
 */
function setupFeaturedCarousel() {
  const viewport = document.getElementById('featuredViewport');
  const prevBtn = document.getElementById('featuredPrev');
  const nextBtn = document.getElementById('featuredNext');

  if (!viewport) return;

  bindCarousel({
    track: viewport,
    prev: prevBtn,
    next: nextBtn,
    itemSelector: '.product-card-link',
    name: 'FeaturedProducts'
  });
}

/**
 * Helper: Format Price
 */
function formatPrice(amount) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount).replace('IDR', 'Rp');
}

/**
 * Helper: Render Product Card
 */
function createProductCard(product, baseUrl) {
  const image = product.main_image || product.image || '';
  const name = product.name || '';
  const slug = product.slug || product.id || '';
  const basePrice = parseFloat(product.base_price || product.price || 0);
  const stock = parseInt(product.stock) || 0;
  const isOutOfStock = stock <= 0;
  const discountType = product.discount_type || 'none';
  const discountValue = parseFloat(product.discount_value || 0);

  // Discount Calculation
  let finalPrice = basePrice;
  let hasDiscount = false;
  let discountBadgeHtml = '';

  if (!isOutOfStock && discountValue > 0) {
    if (discountType === 'percent' || discountType === 'percentage') {
      const discountAmount = (basePrice * discountValue) / 100;
      finalPrice = basePrice - discountAmount;
      hasDiscount = true;
      discountBadgeHtml = `
        <div style="position: absolute; top: 10px; right: 10px; background: #ef4444; color: white; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 2;">
          Hemat ${discountValue % 1 === 0 ? discountValue.toFixed(0) : discountValue.toFixed(1)}%
        </div>
      `;
    } else if (discountType === 'fixed') {
      finalPrice = basePrice - discountValue;
      hasDiscount = true;
      // For fixed discount, maybe show nothing or different badge, but user asked for "Hemat X%" which implies percentage.
      // If fixed is used, we might calculate percentage for badge or just show price.
      // Let's stick to showing crossed out price.
    }
  }

  // Ensure final price isn't negative
  finalPrice = Math.max(0, finalPrice);

  let priceHtml = '';
  if (isOutOfStock) {
    priceHtml = `<p class="product-card-price out-of-stock">
         <span class="strikethrough">${formatPrice(basePrice)}</span>
         <span class="stock-label">Stok Habis</span>
       </p>`;
  } else {
    if (hasDiscount && finalPrice < basePrice) {
      priceHtml = `<p class="product-card-price">
           <span style="text-decoration: line-through; color: #9ca3af; font-size: 0.875rem; margin-right: 4px;">${formatPrice(basePrice)}</span>
           <span style="color: #ef4444; font-weight: bold;">${formatPrice(finalPrice)}</span>
       </p>`;
    } else {
      priceHtml = `<p class="product-card-price">${formatPrice(basePrice)}</p>`;
    }
  }

  return `
    <a href="${baseUrl}/products/${slug}" class="product-card-link ${isOutOfStock ? 'out-of-stock' : ''}">
      <div class="product-card ${isOutOfStock ? 'out-of-stock' : ''}" style="position: relative;">
        ${discountBadgeHtml}
        <div class="product-card-image">
          ${image ? `<img src="${image}" alt="${name}">` : '<span>Gambar Produk</span>'}
          ${isOutOfStock ? '<div class="out-of-stock-overlay">Stok Habis</div>' : ''}
        </div>
        <div class="product-card-info">
          <h3 class="product-card-name">${name}</h3>
          ${priceHtml}
        </div>
      </div>
    </a>
  `;
}

/**
 * Render featured products with carousel setup
 */
function renderFeaturedProducts(products) {
  const container = document.getElementById('featuredProducts');
  if (!container) return;

  if (!products || products.length === 0) {
    showEmpty('featuredProducts', 'Produk unggulan belum tersedia');
    return;
  }

  const baseUrl = window.EP_BASE_URL || '';
  const html = products.map(product => createProductCard(product, baseUrl)).join('');

  container.innerHTML = html;
  setTimeout(() => setupFeaturedCarousel(), 100);
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
  const html = products.map(product => createProductCard(product, baseUrl)).join('');

  container.innerHTML = html;
}

/**
 * Render testimonials carousel
 */
function renderTestimonials(testimonials) {
  const container = document.getElementById('testimonialsContainer');
  if (!container) return;

  // Fallback data if API returns empty
  const fallbackTestimonials = [
    {
      name: "Budi Santoso",
      role: "Event Organizer",
      message: "Hasil cetakan sangat memuaskan dan pengiriman tepat waktu. Sangat membantu untuk event kami!",
      rating: 5
    },
    {
      name: "Siti Nurhaliza",
      role: "Marketing Manager",
      message: "Kualitas print terbaik dengan harga yang kompetitif. Tim sangat responsif dan profesional.",
      rating: 5
    },
    {
      name: "Ahmad Wijaya",
      role: "Business Owner",
      message: "Sudah langganan bertahun-tahun, selalu puas dengan hasil dan pelayanannya. Recommended!",
      rating: 5
    }
  ];

  const items = (!testimonials || testimonials.length === 0) ? fallbackTestimonials : testimonials;

  const html = items.map(item => {
    // Handle field name variations
    const rating = item.rating || item.stars || 5;
    const message = item.message || item.quote || item.text || '';
    const name = item.name || item.author || '';
    const role = item.role || item.subtitle || item.position || '';

    // Generate stars
    const stars = '★'.repeat(Math.min(rating, 5));

    return `
    <div class="ep-testimonial-card">
      <div class="ep-stars">${stars}</div>
      <p class="ep-message">${message}</p>
      <div class="ep-author">
        ${name}
        ${role ? `<span class="ep-role">${role}</span>` : ''}
      </div>
    </div>
    `;
  }).join('');

  container.innerHTML = html;
  setTimeout(() => setupTestimonialsCarousel(), 100);
}

/**
 * Setup testimonials carousel navigation
 */
function setupTestimonialsCarousel() {
  const track = document.getElementById('testimonialsContainer');
  const prevBtn = document.getElementById('testiPrev');
  const nextBtn = document.getElementById('testiNext');

  bindCarousel({
    track: track,
    prev: prevBtn,
    next: nextBtn,
    itemSelector: '.ep-testimonial-card',
    name: 'TestimonialsCarousel'
  });
}

/**
 * Render Promo Carousel (Bottom Banner)
 */
function renderPromoCarousel(banners) {
  const container = document.getElementById('promoCarousel');
  if (!container) return;

  // Fallback banner data if API returns empty
  const fallbackBanners = [
    {
      image: (window.EP_BASE_URL || '') + '/assets/frontend/images/placeholder-store.png',
      alt: 'EventPrint Infrastructure'
    },
    {
      image: (window.EP_BASE_URL || '') + '/assets/frontend/images/placeholder-store.png',
      alt: 'Production Facility'
    }
  ];

  const items = (!banners || banners.length === 0) ? fallbackBanners : banners;

  // Initialize SmallBannerCarousel if class is available
  if (typeof SmallBannerCarousel !== 'undefined') {
    new SmallBannerCarousel('promoCarousel', items);
  }
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
    
    <!-- Promo Carousel (Bottom Banner) -->
    <div id="promoCarousel" class="promo-carousel">
      <!-- Rendered by renderPromoCarousel -->
    </div>
  `;
}

window.addEventListener('beforeunload', () => {
  if (carouselInterval) clearInterval(carouselInterval);
});
