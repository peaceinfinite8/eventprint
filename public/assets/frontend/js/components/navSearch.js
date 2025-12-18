/**
 * Nav Search Component
 * Handles search input, fetching products, and rendering suggestions.
 */
function initNavSearch() {
    const container = document.getElementById('navSearchContainer');
    if (!container) return;

    // Render Search Bar HTML
    container.innerHTML = `
    <div class="nav-search">
      <input type="text" id="globalSearchInput" class="search-input" placeholder="Cari produk..." autocomplete="off">
      <div class="search-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
          <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
        </svg>
      </div>
      <div id="searchDropdown" class="search-dropdown hidden"></div>
    </div>
  `;

    const input = document.getElementById('globalSearchInput');
    const dropdown = document.getElementById('searchDropdown');
    let products = [];

    // Load products data
    if (window.DataClient) {
        DataClient.getProducts().then(data => {
            // Handle API response format
            if (data && data.success && data.products) {
                products = data.products;
            } else if (data && data.products) {
                products = data.products;
            } else if (Array.isArray(data)) {
                products = data;
            }
        });
    }

    // Debounce helper
    const debounce = (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };

    // Search Logic
    const handleSearch = (query) => {
        dropdown.innerHTML = '';

        if (!query || query.length < 2) {
            dropdown.classList.add('hidden');
            return;
        }

        const lowerQuery = query.toLowerCase();

        // Filter products
        const filtered = products.filter(p => {
            return (p.name && p.name.toLowerCase().includes(lowerQuery)) ||
                (p.slug && p.slug.toLowerCase().includes(lowerQuery));
        }).slice(0, 6); // Max 6 results

        if (filtered.length === 0) {
            dropdown.innerHTML = `<div class="search-item-empty">Produk tidak ditemukan</div>`;
            dropdown.classList.remove('hidden');
            return;
        }

        // Render results
        const baseUrl = window.EP_BASE_URL || '';
        dropdown.innerHTML = filtered.map(p => `
      <div class="search-item" onclick="window.location.href='${baseUrl}/products/${p.slug || p.id}'">
        <div class="search-item-info">
          <div class="search-item-name">${p.name}</div>
          ${p.base_price || p.price ? `<div class="search-item-price">Rp ${(p.base_price || p.price).toLocaleString('id-ID')}</div>` : ''}
        </div>
      </div>
    `).join('');

        dropdown.classList.remove('hidden');
    };

    // Events
    input.addEventListener('input', debounce((e) => handleSearch(e.target.value), 200));

    input.addEventListener('focus', () => {
        if (input.value.length >= 2) dropdown.classList.remove('hidden');
    });

    // Keyboard navigation
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') dropdown.classList.add('hidden');
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!container.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

// Make global
window.initNavSearch = initNavSearch;
