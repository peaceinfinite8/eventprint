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
    showLoading('printProducts', 4);
    showLoading('mediaProducts', 4);
    showLoading('merchProducts', 4);

    const homeData = await loadData('/api/home');

    if (homeData && homeData.success) {
      renderBannerCarousel(homeData.banners || []);

      if (homeData.categories && homeData.categories.length > 0) {
        renderCategories(homeData.categories);
      }

      renderFeaturedProducts(homeData.featuredProducts || []);
      renderProductGrid(homeData.printProducts || [], 'printProducts');
      renderProductGrid(homeData.mediaProducts || [], 'mediaProducts');
      renderProductGrid(homeData.merchProducts || [], 'merchProducts');
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
/**
 * Render Hero Grid (3 Banners)
 * Layout: 1 Large Left, 2 Small Right (Stacked)
 */
function renderBannerCarousel(banners) {
  const container = document.getElementById('bannerCarousel');
  if (!container) return;

  if (!banners || banners.length === 0) {
    container.innerHTML = ''; // Hide or show placeholder
    return;
  }

  // We need at least 1 banner. Ideal is 3.
  const mainBanner = banners[0];
  const topSmall = banners[1] || mainBanner; // Fallback to main if missing
  const bottomSmall = banners[2] || banners[1] || mainBanner;

  // Helper to generate banner HTML
  const createBannerHtml = (banner, isMain = false) => {
    const img = banner.image_url || banner.image || '';
    const title = banner.title || '';
    const ctaLink = banner.cta_link || '#';

    // Different styling for main vs small potentially, but for now consistent structure
    return `
        <a href="${ctaLink}" class="hero-banner-item ${isMain ? 'hero-main' : 'hero-small'}">
            <img src="${img}" alt="${title}">
            <!-- Optional Overlay Text could go here if design requires -->
        </a>
      `;
  };

  // Grid Structure
  const html = `
    <div class="hero-grid-container">
        
        <!-- Left Column: Main Banner -->
        <div class="hero-grid-left">
            ${createBannerHtml(mainBanner, true)}
        </div>

        <!-- Right Column: 2 Stacked Banners -->
        <div class="hero-grid-right">
            <div>
                ${createBannerHtml(topSmall)}
            </div>
            <div>
                ${createBannerHtml(bottomSmall)}
            </div>
        </div>

    </div>
  `;

  container.innerHTML = html;

  // Clean up legacy
  container.className = ''; // Remove all classes including hero--fullbleed
  container.style = ''; // Remove all inline styles on container
}

// Remove/Disable unused carousel functions to prevent errors
// Legacy carousel functions disabled
function goToSlide() { }
function nextSlide() { }
function previousSlide() { }
function updateCarousel() { }
function startCarousel() { }
function resetCarousel() { }

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
    const icon = category.icon || '📦';

    return `
    <div class="category-item" onclick="window.location.href='${baseUrl}/products?category=${encodeURIComponent(slug)}'">
      <div class="category-icon">${icon}</div>
      <div class="category-name">${name}</div>
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
/**
 * Render testimonials carousel (Refactored to Blob Card Design)
 * Design Match: 'Gambar #2' (Static Grid Layout)
 */
function renderTestimonials(testimonials) {
  const container = document.getElementById('testimonialsContainer');
  if (!container) return;

  // Fallback data matching the design image (Lorem ipsum version)
  const fallbackTestimonials = [
    {
      name: "Juna",
      role: "Mahasiswa",
      message: "Pesan disini cepat sampai, admin nya juga ramah",
      rating: 5,
    },
    {
      name: "Zulkifli",
      role: "Bos Tambang",
      message: "pesan banyak disini dapet harga khusus",
      rating: 5,
    },
    {
      name: "bahlil",
      role: "Customer",
      message: "murah dan cepat",
      rating: 5,
    },
    {
      name: "Asrul",
      role: "Customer",
      message: "pengerjaan sangat rapih dan cepat, harga juga murah",
      rating: 5,
    }
  ];

  const items = (!testimonials || testimonials.length === 0) ? fallbackTestimonials : testimonials;
  const blobColors = ['blob-purple', 'blob-cyan', 'blob-sky'];

  // Default User Icon (SVG)
  const defaultUserIcon = `data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%239CA3AF'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E`;

  const cardsHtml = items.map((item, index) => {
    const rating = item.rating || item.stars || 5;
    const message = item.message || item.quote || item.text || '';
    const name = item.name || item.author || '';
    const role = item.role || item.subtitle || item.position || 'Customer';

    let imageSrc = item.photo || item.image || item.avatar;
    if (imageSrc && !imageSrc.startsWith('http') && !imageSrc.startsWith('data:')) {
      const baseUrl = window.EP_BASE_URL || '';
      imageSrc = baseUrl.replace(/\/+$/, '') + '/' + imageSrc.replace(/^\/+/, '');
    }
    if (!imageSrc) imageSrc = defaultUserIcon;

    // Background Color Logic
    let blobClass = '';
    let blobStyle = '';

    if (item.bg_color && item.bg_color.startsWith('#')) {
      blobStyle = `background-color: ${item.bg_color} !important;`;
    } else {
      blobClass = blobColors[index % blobColors.length];
    }

    return `
    <div class="review-card-wrapper">
       <div class="review-blob ${blobClass}" style="${blobStyle}"></div>
       <div class="review-avatar">
          <img src="${imageSrc}" alt="${name}" style="padding: ${imageSrc === defaultUserIcon ? '15px' : '0'}; object-fit: ${imageSrc === defaultUserIcon ? 'contain' : 'cover'}; width: 100%; height: 100%;">
       </div>
       <div class="review-card">
          <h3 class="review-title">${name}</h3>
          <p class="review-role">${role}</p>
          <p class="review-text">"${message}"</p>
       </div>
       <div class="review-star-badge">
          <span style="font-weight: 700; font-size: 0.9em; margin-right: 4px; color: #FFD700;">${rating}</span> ★
       </div>
    </div>
    `;
  }).join('');

  // Wrap in slider structure with buttons
  // Using onclick for immediate binding reliability
  window.scrollTesti = function (dir) {
    const track = document.getElementById('testimonialsTrack');
    if (track) {
      const amount = 350;
      track.scrollBy({ left: amount * dir, behavior: 'smooth' });
    }
  };

  container.innerHTML = `
    <div class="testi-slider-container">
        <button id="testiPrev" class="testi-nav-btn prev" type="button" aria-label="Previous" onclick="window.scrollTesti(-1)">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>
        
        <div id="testimonialsTrack" class="ep-testimonials-track">
            ${cardsHtml}
        </div>

        <button id="testiNext" class="testi-nav-btn next" type="button" aria-label="Next" onclick="window.scrollTesti(1)">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>
    </div>
  `;

  // No need for separate addEventListener block
}

/**
 * Setup testimonials carousel navigation
 * (Disabled for now to support Flex Grid layout)
 */
function setupTestimonialsCarousel() {
  // Functions disabled as per design requirement
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
