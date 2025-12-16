// public/assets/frontend/js/render/renderProducts.js
(() => {
  'use strict';

  // stop kalau file kepanggil 2x
  if (window.__EP_PRODUCTS_RENDERER_LOADED__) return;
  window.__EP_PRODUCTS_RENDERER_LOADED__ = true;

  let allProducts = [];
  let derivedCategories = []; // optional (buat sidebar kalau mau)

  function epBase() {
    return (window.EP_BASE_URL || '').replace(/\/+$/, '');
  }

  function epUrl(path) {
    const base = epBase();
    if (!path) return base + '/';
    if (String(path).startsWith('http')) return path;
    return base + (String(path).startsWith('/') ? path : '/' + path);
  }

  function fmtPrice(n) {
    if (typeof window.formatPrice === 'function') return window.formatPrice(n);
    const x = Number(n || 0);
    try {
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(x);
    } catch {
      return 'Rp ' + x.toLocaleString('id-ID');
    }
  }

  function showLoadingFallback(targetId, count = 6) {
    const el = document.getElementById(targetId);
    if (!el) return;
    el.innerHTML = Array.from({ length: count }).map(() => `
      <div class="product-card">
        <div class="product-card-image" style="min-height:160px;background:#1118270d;border-radius:14px"></div>
        <div class="product-card-info">
          <div style="height:14px;background:#1118270d;border-radius:8px;margin:10px 0"></div>
          <div style="height:12px;background:#1118270d;border-radius:8px;width:50%"></div>
        </div>
      </div>
    `).join('');
  }

  function showMsg(targetId, msg) {
    const el = document.getElementById(targetId);
    if (!el) return;
    el.innerHTML = `<div class="ep-empty">${msg}</div>`;
  }

  async function fetchJSON(path) {
    // path contoh: '/api/products'
    const url = epUrl(path);
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    const data = await res.json().catch(() => null);
    return data;
  }

  function getQueryParamsSafe() {
    // support kalau kamu punya urlState.js, tapi tetap jalan walau gak ada
    if (typeof window.getQueryParams === 'function') return window.getQueryParams();
    const sp = new URLSearchParams(window.location.search);
    return { cat: sp.get('cat'), sub: sp.get('sub') };
  }

  function updateQueryParamsSafe(next) {
    if (typeof window.updateQueryParams === 'function') return window.updateQueryParams(next);

    const url = new URL(window.location.href);
    if (next.cat === null) url.searchParams.delete('cat');
    else if (typeof next.cat !== 'undefined' && next.cat !== undefined) url.searchParams.set('cat', next.cat);

    if (next.sub === null) url.searchParams.delete('sub');
    else if (typeof next.sub !== 'undefined' && next.sub !== undefined) url.searchParams.set('sub', next.sub);

    history.pushState({}, '', url.toString());
  }

  function buildCategoriesFromProducts(items) {
    // Kalau API kamu ngasih category_slug/category_name, ini kepakai.
    // Kalau gak ada, sidebar akan jadi minimal.
    const map = new Map();

    // default "Semua Produk"
    map.set('all', { id: 'all', name: 'Semua Produk', subcategories: [] });

    for (const p of items) {
      const slug =
        p.category_slug ||
        p.categorySlug ||
        p.category ||
        p.category_id ||
        p.categoryId;

      const name =
        p.category_name ||
        p.categoryName ||
        p.category_label ||
        p.categoryTitle ||
        'Kategori';

      if (!slug) continue;
      const key = String(slug);

      if (!map.has(key)) {
        map.set(key, { id: key, name, subcategories: [] });
      }
    }

    return Array.from(map.values());
  }

  function renderSidebar(activeCatId, activeSubId) {
    const container = document.getElementById('categorySidebar');
    if (!container) return;

    // kalau kamu gak butuh sidebar, boleh kosongin container di view
    const cats = derivedCategories.length ? derivedCategories : [{ id: 'all', name: 'Semua Produk', subcategories: [] }];

    const html = cats.map(cat => {
      if (cat.id === 'all') {
        const isAllActive = !activeCatId || activeCatId === 'all';
        return `
          <li class="sidebar-group">
            <button type="button" class="sidebar-head ${isAllActive ? 'active' : ''}" data-action="reset-all">
              <span>${cat.name}</span>
            </button>
          </li>
        `;
      }

      const isActiveCategory = (activeCatId === cat.id);
      const hasSubcats = Array.isArray(cat.subcategories) && cat.subcategories.length > 0;
      const isExpanded = isActiveCategory;

      let subBodyHtml = '';
      if (hasSubcats) {
        const subItems = cat.subcategories.map(sub => {
          const isSubActive = (activeSubId === sub.id);
          return `
            <button type="button"
                    class="sidebar-item ${isSubActive ? 'active' : ''}"
                    data-cat="${cat.id}"
                    data-sub="${sub.id}">
              ${sub.name}
            </button>
          `;
        }).join('');

        subBodyHtml = `
          <div class="sidebar-body" id="group-${cat.id}" ${isExpanded ? '' : 'hidden'}>
            ${subItems}
          </div>
        `;
      }

      return `
        <li class="sidebar-group">
          <button type="button"
                  class="sidebar-head ${isActiveCategory ? 'active' : ''}"
                  aria-expanded="${isExpanded ? 'true' : 'false'}">
            <span>${cat.name}</span>
            ${hasSubcats ? `
              <svg class="category-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6"></path>
              </svg>
            ` : ''}
          </button>
          ${subBodyHtml}
        </li>
      `;
    }).join('');

    container.innerHTML = html;
  }

  function renderBreadcrumbs(catId, subId) {
    const titleEl = document.getElementById('pageTitle');
    if (!titleEl) return;

    if (!catId || catId === 'all') {
      titleEl.textContent = 'Product';
      return;
    }

    const cat = (derivedCategories || []).find(x => x.id === catId);
    if (!cat) {
      titleEl.textContent = 'Product';
      return;
    }

    let text = `Product > ${cat.name}`;
    if (subId) {
      const sub = (cat.subcategories || []).find(s => s.id === subId);
      if (sub) text += ` > ${sub.name}`;
    }
    titleEl.textContent = text;
  }

  function imgUrl(src) {
    if (!src) return '';
    const s = String(src);
    if (s.startsWith('http')) return s;
    return epBase() + '/' + s.replace(/^\/+/, '');
  }

  function normalizeProductImage(p) {
    // dukung beberapa field: thumbnail, images[0], image_url
    const thumb = p.thumbnail || p.thumb || p.image || p.image_url || '';
    if (thumb) return imgUrl(thumb);

    if (Array.isArray(p.images) && p.images[0]) return imgUrl(p.images[0]);

    return '';
  }

  function filterProducts(catId, subId) {
    let filtered = allProducts;

    // catId/subId cuma kepakai kalau API kamu memang punya datanya
    if (catId && catId !== 'all') {
      filtered = filtered.filter(p => {
        const v = p.category_slug || p.categorySlug || p.category || p.category_id || p.categoryId;
        return String(v || '') === String(catId);
      });

      if (subId) {
        filtered = filtered.filter(p => {
          const v = p.subcategory_slug || p.subcategorySlug || p.subcategory || p.subcategory_id || p.subcategoryId;
          return String(v || '') === String(subId);
        });
      }
    }

    return filtered;
  }

  function renderProducts(catId, subId) {
    const grid = document.getElementById('productGrid');
    if (!grid) return;

    const filtered = filterProducts(catId, subId);

    if (!filtered.length) {
      if (typeof window.showEmpty === 'function') window.showEmpty('productGrid', 'Produk belum tersedia.');
      else showMsg('productGrid', 'Produk belum tersedia.');
      return;
    }

    grid.innerHTML = filtered.map(p => {
      const img = normalizeProductImage(p);
      const price = Number(p.base_price ?? p.basePrice ?? 0);
      const href = epUrl(`/products/${p.id}`);

      return `
        <a href="${href}" class="product-card-link">
          <div class="product-card">
            <div class="product-card-image">
              ${
                img
                  ? `<img src="${img}" alt="${p.name || 'Produk'}" loading="lazy">`
                  : `<img src="https://placehold.co/400x300?text=${encodeURIComponent(p.name || 'Produk')}" alt="${p.name || 'Produk'}">`
              }
            </div>
            <div class="product-card-info">
              <h3 class="product-card-name">${p.name || 'Produk'}</h3>
              <p class="product-card-price">${fmtPrice(price)}</p>
            </div>
          </div>
        </a>
      `;
    }).join('');
  }

  function setupSidebarEvents() {
    const container = document.getElementById('categorySidebar');
    if (!container) return;

    container.addEventListener('click', (e) => {
      const subItem = e.target.closest('.sidebar-item');
      if (subItem) {
        e.preventDefault();
        const cat = subItem.dataset.cat || null;
        const sub = subItem.dataset.sub || null;
        updateQueryParamsSafe({ cat, sub });
        handleUrlState();
        return;
      }

      const headBtn = e.target.closest('.sidebar-head');

      if (headBtn && headBtn.dataset.action === 'reset-all') {
        updateQueryParamsSafe({ cat: null, sub: null });
        handleUrlState();
        return;
      }

      if (headBtn && headBtn.hasAttribute('aria-expanded')) {
        const expanded = headBtn.getAttribute('aria-expanded') === 'true';
        const parentGroup = headBtn.closest('.sidebar-group');
        const groupBody = parentGroup ? parentGroup.querySelector('.sidebar-body') : null;

        if (groupBody) {
          if (expanded) {
            groupBody.setAttribute('hidden', '');
            headBtn.setAttribute('aria-expanded', 'false');
          } else {
            groupBody.removeAttribute('hidden');
            headBtn.setAttribute('aria-expanded', 'true');
          }
        }
      }
    });
  }

  function handleUrlState() {
    const { cat, sub } = getQueryParamsSafe();
    renderSidebar(cat, sub);
    renderBreadcrumbs(cat, sub);
    renderProducts(cat, sub);
  }

  window.initProductsPage = async function initProductsPage() {
    try {
      if (typeof window.showLoading === 'function') window.showLoading('productGrid', 6);
      else showLoadingFallback('productGrid', 6);

      const data = await fetchJSON('/api/products');
      if (!data || data.ok !== true) {
        if (typeof window.showError === 'function') window.showError('productGrid', 'Gagal memuat produk dari server.');
        else showMsg('productGrid', 'Gagal memuat produk dari server.');
        return;
      }

      allProducts = Array.isArray(data.items) ? data.items : [];
      derivedCategories = buildCategoriesFromProducts(allProducts);

      setupSidebarEvents();
      handleUrlState();
      window.addEventListener('popstate', handleUrlState);

    } catch (err) {
      console.error(err);
      if (typeof window.showError === 'function') window.showError('productGrid', 'Terjadi kesalahan saat memuat produk.');
      else showMsg('productGrid', 'Terjadi kesalahan saat memuat produk.');
    }
  };
})();
