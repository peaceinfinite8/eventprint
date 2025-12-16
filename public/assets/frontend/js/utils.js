// public/assets/frontend/js/utils.js

function epBase() {
  return (window.EP_BASE_URL || '').replace(/\/+$/, '');
}

async function loadData(path) {
  const clean = String(path || '').replace(/^\/+/, '');
  const url = epBase() + '/' + clean;
  const res = await fetch(url, { headers: { Accept: 'application/json' } });
  if (!res.ok) throw new Error(`HTTP ${res.status} for ${clean}`);
  return res.json();
}

function formatPrice(amount) {
  const n = Number(amount || 0);
  return `Rp ${n.toLocaleString('id-ID')}`;
}

function showLoading(containerId, count = 4) {
  const container = document.getElementById(containerId);
  if (!container) return;
  container.innerHTML = '';
  for (let i = 0; i < count; i++) {
    const el = document.createElement('div');
    el.className = 'skeleton-card loading-skeleton';
    container.appendChild(el);
  }
}

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

function createStars(rating) {
  let stars = '';
  for (let i = 0; i < 5; i++) stars += `<span class="star">${i < rating ? '★' : '☆'}</span>`;
  return stars;
}

function createCarouselDots(totalSlides, currentIndex = 0) {
  let dots = '<div class="carousel-dots">';
  for (let i = 0; i < totalSlides; i++) {
    dots += `<div class="carousel-dot ${i === currentIndex ? 'active' : ''}" data-index="${i}"></div>`;
  }
  dots += '</div>';
  return dots;
}

function getSocialIcon(platform) {
  const icons = { instagram:'📷', tiktok:'🎵', youtube:'▶️', pinterest:'📌', whatsapp:'💬', email:'✉️' };
  return icons[String(platform || '').toLowerCase()] || '🔗';
}
