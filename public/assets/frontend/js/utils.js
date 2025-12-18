// ============================================
// EventPrint - Utility Functions
// ============================================

/**
 * Format price to Indonesian Rupiah format
 * @param {number} amount - Price amount
 * @returns {string} Formatted price string
 */
function formatPrice(amount) {
  return `Rp ${amount.toLocaleString('id-ID')}`;
}

/**
 * Show loading skeleton in container
 * @param {string} containerId - ID of container element
 * @param {number} count - Number of skeleton cards to show
 */
function showLoading(containerId, count = 4) {
  const container = document.getElementById(containerId);
  if (!container) return;

  container.innerHTML = '';
  for (let i = 0; i < count; i++) {
    const skeleton = document.createElement('div');
    skeleton.className = 'skeleton-card loading-skeleton';
    container.appendChild(skeleton);
  }
}

/**
 * Show empty state message
 * @param {string} containerId - ID of container element
 * @param {string} message - Message to display
 */
function showEmpty(containerId, message = 'Data belum tersedia') {
  const container = document.getElementById(containerId);
  if (!container) return;

  container.innerHTML = `
    <div class="empty-state">
      <div class="empty-state-icon">📭</div>
      <p class="empty-state-text">${message}</p>
    </div>
  `;
}

/**
 * Show error state message
 * @param {string} containerId - ID of container element
 * @param {string} message - Error message to display
 */
function showError(containerId, message = 'Gagal memuat data. Coba lagi.') {
  const container = document.getElementById(containerId);
  if (!container) return;

  container.innerHTML = `
    <div class="error-state">
      <div class="error-state-icon">⚠️</div>
      <p class="error-state-text">${message}</p>
    </div>
  `;
}

/**
 * Load JSON data from API or file
 * @param {string} path - Path to JSON file or API endpoint
 * @returns {Promise<Object>} Parsed JSON data
 */
async function loadData(path) {
  try {
    // Prepend base URL if path starts with /api/
    let fullPath = path;
    if (path.startsWith('/api/')) {
      const baseUrl = window.EP_BASE_URL || '';
      fullPath = baseUrl + path;
    }

    const response = await fetch(fullPath);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error('Error loading data:', error);
    throw error;
  }
}

/**
 * Load HTML partial into target element
 * @param {string} url - URL of partial HTML file
 * @param {string} targetId - ID of target element
 */
async function loadPartial(url, targetId) {
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const html = await response.text();
    const target = document.getElementById(targetId);
    if (target) {
      target.innerHTML = html;
    }
  } catch (error) {
    console.error('Error loading partial:', error);
  }
}

/**
 * Get current page name from URL
 * @returns {string} Current page name
 */
function getCurrentPage() {
  const path = window.location.pathname;
  const page = path.substring(path.lastIndexOf('/') + 1);
  return page || 'home.html';
}

/**
 * Set active navigation link based on current page
 * @param {string} currentPage - Current page filename
 */
function setActiveNav(currentPage) {
  const navLinks = document.querySelectorAll('.nav-link');
  navLinks.forEach(link => {
    const href = link.getAttribute('href');
    if (href === currentPage) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });
}

/**
 * Create star rating HTML
 * @param {number} rating - Star rating (1-5)
 * @returns {string} HTML string with stars
 */
function createStars(rating) {
  let stars = '';
  for (let i = 0; i < 5; i++) {
    stars += `<span class="star">${i < rating ? '★' : '☆'}</span>`;
  }
  return stars;
}

/**
 * Debounce function for performance optimization
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @returns {Function} Debounced function
 */
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Smooth scroll to element
 * @param {string} elementId - ID of target element
 */
function scrollToElement(elementId) {
  const element = document.getElementById(elementId);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

/**
 * Create carousel navigation
 * @param {number} totalSlides - Total number of slides
 * @param {Function} onDotClick - Callback for dot click
 * @returns {string} HTML string for carousel dots
 */
function createCarouselDots(totalSlides, currentIndex = 0) {
  let dots = '<div class="carousel-dots">';
  for (let i = 0; i < totalSlides; i++) {
    dots += `<div class="carousel-dot ${i === currentIndex ? 'active' : ''}" data-index="${i}"></div>`;
  }
  dots += '</div>';
  return dots;
}

/**
 * Get icon HTML for social media
 * @param {string} platform - Social media platform name
 * @returns {string} Icon HTML or emoji
 */
function getSocialIcon(platform) {
  const icons = {
    instagram: '📷',
    tiktok: '🎵',
    youtube: '▶️',
    pinterest: '📌',
    whatsapp: '💬',
    email: '✉️'
  };
  return icons[platform.toLowerCase()] || '🔗';
}

/**
 * Truncate text to specified length
 * @param {string} text - Text to truncate
 * @param {number} maxLength - Maximum length
 * @returns {string} Truncated text
 */
function truncateText(text, maxLength) {
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + '...';
}
