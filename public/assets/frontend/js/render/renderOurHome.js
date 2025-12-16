// ============================================
// EventPrint - Our Home Page Renderer
// ============================================

/**
 * Initialize Our Home page
 */
async function initOurHomePage() {
  try {
    // Show loading
    showLoading('storesGrid', 8);

    // Simulate loading delay
    await new Promise(resolve => setTimeout(resolve, 400));

    const data = await loadData('../data/ourhome.json');

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

/**
 * Render store cards
 */
function renderStores(stores) {
  const container = document.getElementById('storesGrid');
  if (!container) return;

  const html = stores.map(store => `
    <div class="store-card">
      <div class="store-image">
        ${store.image ? `<img src="${store.image}" alt="${store.title}">` : '<span>Gambar</span>'}
        <div class="store-label">EventPrint Tempat</div>
      </div>
      
      <div class="store-info">
        <!-- Address -->
        <div class="info-row">
          <div class="info-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1 1 18 0z"></path>
              <circle cx="12" cy="10" r="3"></circle>
            </svg>
          </div>
          <div class="info-content">
            <div class="info-label">Alamat</div>
            <div class="info-text">${store.address}</div>
          </div>
        </div>

        <!-- Email -->
        <div class="info-row">
          <div class="info-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="5" width="18" height="14" rx="2"></rect>
              <path d="M3 7l9 6 9-6"></path>
            </svg>
          </div>
          <div class="info-content">
            <div class="info-label">Email</div>
            <div class="info-text">${store.email}</div>
          </div>
        </div>

        <!-- WhatsApp -->
        <div class="info-row">
          <div class="info-icon">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
          </div>
          <div class="info-content">
            <div class="info-label">WhatsApp</div>
            <div class="info-text">${store.whatsapp}</div>
          </div>
        </div>

        <!-- Operating Hours -->
        <div class="info-row">
          <div class="info-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M12 6v6l4 2"></path>
            </svg>
          </div>
          <div class="info-content">
            <div class="info-label">Jam Kerja</div>
            <div class="info-text">${Array.isArray(store.hours) ? store.hours.join('<br>') : store.hours}</div>
          </div>
        </div>
      </div>
    </div>
  `).join('');

  container.innerHTML = html;
  container.className = 'stores-grid';
}