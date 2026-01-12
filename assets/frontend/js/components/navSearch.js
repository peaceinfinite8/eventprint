/**
 * Nav Search Component with Live Typeahead + Services Carousel Integration
 * Ranked search:
 * - startsWith(query) gets higher priority than includes(query)
 * - results are sorted by score then by name
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
      <div class="search-input-wrapper">
        <input type="text" id="globalSearchInput" class="search-input" placeholder="Search Product" autocomplete="off">
        <button type="submit" class="header-search-icon" style="background:none;border:none;padding:0;cursor:pointer;">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
          </svg>
        </button>
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
    DataClient.getProducts().then((data) => {
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
  function extractCategories() {
    const categoriesContainer = document.getElementById('categories');
    if (!categoriesContainer) return;

    const categoryElements = categoriesContainer.querySelectorAll('.category-item');
    cachedCategories = Array.from(categoryElements).map((el) => {
      const nameEl = el.querySelector('.category-name');
      return {
        element: el,
        name: nameEl ? nameEl.textContent.trim() : '',
        slug: el.dataset.categorySlug || '',
      };
    });
  }
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
    return (str || '').toString().toLowerCase().trim();
  }

  /**
   * Ranking:
   * 3 = startsWith
   * 1 = includes
   * 0 = no match
   */
  function matchScore(obj, query) {
    const q = normalize(query);
    if (!q) return 0;

    const fields = [
      normalize(obj.name),
      normalize(obj.slug),
      normalize(obj.title),
    ].filter(Boolean);

    let best = 0;
    for (const f of fields) {
      if (f.startsWith(q)) best = Math.max(best, 3);
      else if (f.includes(q)) best = Math.max(best, 1);
    }
    return best;
  }

  function sortByScoreThenName(a, b) {
    if (b.score !== a.score) return b.score - a.score;
    const an = normalize(a.item?.name || a.item?.title || a.item?.slug);
    const bn = normalize(b.item?.name || b.item?.title || b.item?.slug);
    return an.localeCompare(bn);
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
    cachedCategories.forEach((cat) => {
      const isMatch = matchedCategories.some((m) => m.element === cat.element);
      cat.element.classList.toggle('is-dimmed', !isMatch);
    });
  }

  // Restore all categories to normal
  function restoreCategories() {
    cachedCategories.forEach((cat) => {
      cat.element.classList.remove('is-dimmed');
    });
  }

  // Main Search Logic (handles dropdown + services hint)
  const handleSearch = (query) => {
    dropdown.innerHTML = '';

    if (!query || query.length < 1) {
      dropdown.classList.add('hidden');
      hideServicesHint();
      restoreCategories();
      return;
    }

    // === PRODUCTS: score + sort ===
    const scoredProducts = cachedProducts
      .map((p) => ({ item: p, score: matchScore(p, query) }))
      .filter((x) => x.score > 0)
      .sort(sortByScoreThenName);

    const filteredProducts = scoredProducts.map((x) => x.item);

    // === POSTS: score + sort (count only, optional if you want to render later) ===
    const scoredPosts = cachedPosts
      .map((p) => ({ item: p, score: matchScore(p, query) }))
      .filter((x) => x.score > 0)
      .sort(sortByScoreThenName);

    const filteredPosts = scoredPosts.map((x) => x.item);

    // === CATEGORIES/SERVICES: score + sort (for dimming + count) ===
    const scoredServices = cachedCategories
      .map((c) => ({ item: c, score: matchScore(c, query) }))
      .filter((x) => x.score > 0)
      .sort(sortByScoreThenName);

    const filteredServices = scoredServices.map((x) => x.item);

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
    // Helper for highlighting text
    const highlightMatch = (text, query) => {
      if (!query) return text;
      // Escape special regex chars
      const safeQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
      // Create regex for case-insensitive matching
      const regex = new RegExp(`(${safeQuery})`, 'gi');
      // Wrap match in blue span (using standard blue or primary theme color)
      return text.replace(regex, '<span style="color: #00aeef; font-weight: 600;">$1</span>');
    };

    const baseUrl = window.EP_BASE_URL || '';
    dropdown.innerHTML = displayProducts
      .map(
        (p) => `
        <div class="search-item" onclick="window.location.href='${baseUrl}/products/${p.slug || p.id}'">
          <div class="search-item-info">
            <div class="search-item-name">${highlightMatch(p.name || '', query)}</div>
            ${p.base_price || p.price
            ? `<div class="search-item-price">Rp ${parseFloat(p.base_price || p.price).toLocaleString('id-ID', { maximumFractionDigits: 0 })}</div>`
            : ''
          }
          </div>
        </div>
      `
      )
      .join('');

    dropdown.classList.remove('hidden');
  };

  // Events - use 150ms debounce for live feel
  input.addEventListener('input', debounce((e) => handleSearch(e.target.value), 150));

  input.addEventListener('focus', () => {
    // Biar konsisten sama handleSearch (>=1)
    if (input.value.length >= 1) dropdown.classList.remove('hidden');
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
