/**
 * Nav Search Component with Live Typeahead + Services Carousel Integration
 * Handles search input, fetching products/posts, and rendering suggestions.
 * Updates services carousel with contextual search label and category filtering.
 */
function initNavSearch() {
    const container = document.getElementById('navSearchContainer');
    if (!container) return;

    // Check if search input already exists (server-rendered from navbar.php)
    let input = document.getElementById('globalSearchInput');
    let dropdown = document.getElementById('searchDropdown');

    // If server-rendered elements don't exist, create them
    if (!input || !dropdown) {
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

        // Re-query after creating elements
        input = document.getElementById('globalSearchInput');
        dropdown = document.getElementById('searchDropdown');
    }

    // Global caches for live filtering
    let cachedProducts = [];
    let cachedPosts = [];
    let cachedCategories = [];

    // Load products data
    if (window.DataClient) {
        DataClient.getProducts().then(data => {
            // Handle API response format
            if (data && data.success && data.products) {
                cachedProducts = data.products;
            } else if (data && data.products) {
                cachedProducts = data.products;
            } else if (Array.isArray(data)) {
                cachedProducts = data;
            }
        });
    }

    // Load posts/articles data
    async function loadPosts() {
        try {
            const response = await fetch((window.EP_BASE_URL || '') + '/api/posts');
            const data = await response.json();
            if (data && data.success && data.posts) {
                cachedPosts = data.posts;
            } else if (Array.isArray(data)) {
                cachedPosts = data;
            }
        } catch (error) {
            console.warn('Could not load posts for search:', error);
        }
    }
    loadPosts();

    // Extract categories from rendered DOM (after homepage loads)
    // GUARD: Only run on pages that have categories section
    function extractCategories() {
        const categoriesContainer = document.getElementById('categories');
        if (!categoriesContainer) {
            // Not on home page or categories not rendered yet
            return;
        }

        const categoryElements = categoriesContainer.querySelectorAll('.category-item');
        cachedCategories = Array.from(categoryElements).map(el => {
            const nameEl = el.querySelector('.category-name');
            return {
                element: el,
                name: nameEl ? nameEl.textContent.trim() : '',
                slug: el.dataset.categorySlug || ''
            };
        });
    }

    // Call after a short delay to ensure categories are rendered
    setTimeout(extractCategories, 500);

    // Debounce helper (150ms for live feel)
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

    // Normalize string for matching
    function normalize(str) {
        return (str || '').toLowerCase().trim();
    }

    // Match query against object
    function matchesQuery(obj, query) {
        const lowerQuery = normalize(query);
        return (
            normalize(obj.name).includes(lowerQuery) ||
            normalize(obj.slug).includes(lowerQuery) ||
            normalize(obj.title).includes(lowerQuery)
        );
    }

    // Update services search hint
    function updateServicesHint(query, prodCount, postCount, svcCount) {
        const hint = document.getElementById('servicesSearchHint');
        if (!hint) return;

        const total = prodCount + postCount + svcCount;
        if (total === 0) {
            hint.textContent = `Tidak ada hasil untuk "${query}"`;
        } else {
            hint.textContent = `Hasil pencarian "${query}" — Produk: ${prodCount}, Artikel: ${postCount}, Services: ${svcCount}`;
        }
        hint.hidden = false;
    }

    // Hide services hint
    function hideServicesHint() {
        const hint = document.getElementById('servicesSearchHint');
        if (hint) hint.hidden = true;
    }

    // Dim non-matching categories
    function dimNonMatchingCategories(matchedCategories) {
        cachedCategories.forEach(cat => {
            const isMatch = matchedCategories.some(m => m.element === cat.element);
            cat.element.classList.toggle('is-dimmed', !isMatch);
        });
    }

    // Restore all categories to normal
    function restoreCategories() {
        cachedCategories.forEach(cat => {
            cat.element.classList.remove('is-dimmed');
        });
    }

    // Main Search Logic (handles dropdown + services hint)
    const handleSearch = (query) => {
        dropdown.innerHTML = '';

        if (!query || query.length < 2) {
            dropdown.classList.add('hidden');
            hideServicesHint();
            restoreCategories();
            return;
        }

        const lowerQuery = query.toLowerCase();

        // Filter products
        const filteredProducts = cachedProducts.filter(p => matchesQuery(p, lowerQuery));

        // Filter posts
        const filteredPosts = cachedPosts.filter(p => matchesQuery(p, lowerQuery));

        // Filter categories/services
        const filteredServices = cachedCategories.filter(c => matchesQuery(c, lowerQuery));

        // Update services hint with counts
        updateServicesHint(query, filteredProducts.length, filteredPosts.length, filteredServices.length);

        // Dim non-matching categories
        dimNonMatchingCategories(filteredServices);

        // Render dropdown (products only, limit 6)
        const displayProducts = filteredProducts.slice(0, 6);

        if (displayProducts.length === 0) {
            dropdown.innerHTML = `<div class="search-item-empty">Produk tidak ditemukan</div>`;
            dropdown.classList.remove('hidden');
            return;
        }

        // Render results
        const baseUrl = window.EP_BASE_URL || '';
        dropdown.innerHTML = displayProducts.map(p => `
      <div class="search-item" onclick="window.location.href='${baseUrl}/products/${p.slug || p.id}'">
        <div class="search-item-info">
          <div class="search-item-name">${p.name}</div>
          ${p.base_price || p.price ? `<div class="search-item-price">Rp ${parseFloat(p.base_price || p.price).toLocaleString('id-ID', { maximumFractionDigits: 0 })}</div>` : ''}
        </div>
      </div>
    `).join('');

        dropdown.classList.remove('hidden');
    };

    // Events - use 150ms debounce for live feel
    input.addEventListener('input', debounce((e) => handleSearch(e.target.value), 150));

    input.addEventListener('focus', () => {
        if (input.value.length >= 2) dropdown.classList.remove('hidden');
    });

    // Keyboard navigation
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            dropdown.classList.add('hidden');
            input.value = '';
            hideServicesHint();
            restoreCategories();
        }
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
