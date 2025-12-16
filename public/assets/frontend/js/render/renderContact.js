// public/assets/frontend/js/render/renderOurHome.js
// ============================================
// EventPrint - Our Home Page Renderer (Backend-safe)
// ============================================

async function initOurHomePage() {
  try {
    showLoading('storesGrid', 8);

    const data = (window.EP_DATA_PRELOADED && typeof window.EP_DATA_PRELOADED === 'object')
      ? window.EP_DATA_PRELOADED
      : await loadData('data/ourhome.json');

    if (!data || !data.stores || data.stores.length === 0) {
      showEmpty('storesGrid', 'Data lokasi belum tersedia');
      return;
    }

    renderStores(data.stores);

  } catch (error) {
    console.error('Error loading our home page:', error);
    showError('storesGrid', 'Gagal memuat data. Silakan refresh halaman.');
  }
}

function renderStores(stores) {
  const container = document.getElementById('storesGrid');
  if (!container) return;

  container.innerHTML = stores.map(store => `
    <div class="store-card">
      <div class="store-image">
        ${store.image ? `<img src="${store.image}" alt="${store.title || ''}">` : '<span>Gambar</span>'}
        <div class="store-label">EventPrint Tempat</div>
      </div>

      <div class="store-info">
        <div class="info-row">
          <div class="info-icon">📍</div>
          <div class="info-content">
            <div class="info-label">Alamat</div>
            <div class="info-text">${store.address || ''}</div>
          </div>
        </div>

        <div class="info-row">
          <div class="info-icon">✉️</div>
          <div class="info-content">
            <div class="info-label">Email</div>
            <div class="info-text">${store.email || ''}</div>
          </div>
        </div>

        <div class="info-row">
          <div class="info-icon">💬</div>
          <div class="info-content">
            <div class="info-label">WhatsApp</div>
            <div class="info-text">${store.whatsapp || ''}</div>
          </div>
        </div>

        <div class="info-row">
          <div class="info-icon">🕒</div>
          <div class="info-content">
            <div class="info-label">Jam Kerja</div>
            <div class="info-text">${Array.isArray(store.hours) ? store.hours.join('<br>') : (store.hours || '')}</div>
          </div>
        </div>
      </div>
    </div>
  `).join('');

  container.className = 'stores-grid';
}
