// public/assets/frontend/js/render/renderProductDetail.js
(() => {
  'use strict';

  if (window.__EP_PRODUCT_DETAIL_LOADED__) return;
  window.__EP_PRODUCT_DETAIL_LOADED__ = true;

  let productData = null;
  let quantity = 1;
  let uploadedFileName = null;
  let currentNote = '';

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

  async function fetchJSON(path) {
    const url = epUrl(path);
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    const data = await res.json().catch(() => null);
    return data;
  }

  function imgUrl(src) {
    if (!src) return '';
    const s = String(src);
    if (s.startsWith('http')) return s;
    return epBase() + '/' + s.replace(/^\/+/, '');
  }

  function getIdFromPath() {
    // contoh: /eventprint/public/products/3
    const parts = window.location.pathname.split('/').filter(Boolean);
    const last = parts[parts.length - 1];
    const id = parseInt(last, 10);
    return Number.isFinite(id) ? id : null;
  }

  function getIdFromDom() {
    const el = document.getElementById('productDetailContent');
    if (!el) return null;
    const v = el.getAttribute('data-product-id');
    const id = parseInt(v, 10);
    return Number.isFinite(id) ? id : null;
  }

  function ensureToast() {
    let toast = document.getElementById('toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'toast';
      toast.className = 'toast';
      document.body.appendChild(toast);
    }
    return toast;
  }

  function showToast(message) {
    const toast = ensureToast();
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
  }

  function renderProductNotFound(message) {
    const container = document.getElementById('productDetailContent');
    if (!container) return;

    container.innerHTML = `
      <div class="product-not-found">
        <h2>Produk Tidak Ditemukan</h2>
        <p>${message}</p>
        <a href="${epUrl('/products')}" class="btn btn-primary">Kembali ke All Product</a>
      </div>
    `;
  }

  function calculateUnitPrice() {
    // versi DB basic: pakai base_price saja
    // (kalau nanti kamu mau support opsi dari DB, baru dikembangkan)
    return Number(productData?.base_price ?? productData?.basePrice ?? 0);
  }

  function updatePriceAndSubtotal() {
    const unitPrice = calculateUnitPrice();
    const subtotal = unitPrice * quantity;

    const priceEl = document.getElementById('displayPrice');
    const subEl = document.getElementById('subtotalValue');
    const qtyEl = document.getElementById('quantityValue');

    if (priceEl) priceEl.textContent = fmtPrice(unitPrice);
    if (subEl) subEl.textContent = fmtPrice(subtotal);
    if (qtyEl) qtyEl.textContent = String(quantity);
  }

  function wireDetailEvents() {
    // thumbnails
    document.querySelectorAll('.thumbnail').forEach(btn => {
      btn.addEventListener('click', () => {
        const idx = Number(btn.getAttribute('data-index'));
        const images = Array.isArray(productData.images) ? productData.images : [];
        const img = images[idx] || '';
        const mainImage = document.getElementById('mainImage');
        if (mainImage) {
          mainImage.innerHTML = img ? `<img src="${imgUrl(img)}" alt="${productData.name || 'Produk'}">` : '<span>Gambar Produk</span>';
        }
        document.querySelectorAll('.thumbnail').forEach((t, i) => t.classList.toggle('active', i === idx));
      });
    });

    // note
    const note = document.getElementById('productNote');
    if (note) note.addEventListener('input', () => (currentNote = note.value || ''));

    // qty
    const minus = document.getElementById('qtyMinus');
    const plus = document.getElementById('qtyPlus');

    if (minus) minus.addEventListener('click', () => {
      if (quantity > 1) quantity--;
      updatePriceAndSubtotal();
    });
    if (plus) plus.addEventListener('click', () => {
      quantity++;
      updatePriceAndSubtotal();
    });

    // upload
    const input = document.getElementById('printFileInput');
    if (input) {
      input.addEventListener('change', (event) => {
        const file = event.target.files && event.target.files[0];
        const statusEl = document.getElementById('fileStatus');
        if (!statusEl) return;

        if (!file) {
          statusEl.innerHTML = '';
          uploadedFileName = null;
          return;
        }

        uploadedFileName = file.name;
        statusEl.innerHTML = `<div class="file-name">✓ ${file.name}</div>`;
      });
    }

    // checkout draft
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
      checkoutBtn.addEventListener('click', () => {
        const unitPrice = calculateUnitPrice();
        const subtotal = unitPrice * quantity;

        const draft = {
          product_id: productData.id,
          product_name: productData.name,
          quantity,
          note: currentNote,
          subtotal,
          unit_price: unitPrice,
          file_name: uploadedFileName,
          timestamp: new Date().toISOString()
        };

        try {
          localStorage.setItem('eventprint_checkout_draft', JSON.stringify(draft));
          showToast('✓ Draft pesanan tersimpan!');
        } catch (e) {
          console.error(e);
          showToast('❌ Gagal menyimpan draft');
        }
      });
    }
  }

  function normalizeImages(p) {
    // dukung thumbnail/images
    const imgs = Array.isArray(p.images) ? p.images.filter(Boolean) : [];
    if (imgs.length) return imgs;

    const thumb = p.thumbnail || p.thumb || p.image || p.image_url || '';
    if (thumb) return [thumb];

    return [];
  }

  function renderProductDetail() {
    const container = document.getElementById('productDetailContent');
    if (!container) return;

    const images = normalizeImages(productData);
    const mainImg = images[0] || '';

    container.innerHTML = `
      <div class="product-detail-container">
        <div class="gallery-section">
          <div id="mainImage" class="main-image">
            ${mainImg ? `<img src="${imgUrl(mainImg)}" alt="${productData.name || 'Produk'}">` : '<span>Gambar Produk</span>'}
          </div>

          <div class="thumbnail-list">
            ${images.slice(0, 4).map((img, idx) => `
              <button class="thumbnail ${idx === 0 ? 'active' : ''}" type="button" data-index="${idx}">
                ${img ? `<img src="${imgUrl(img)}" alt="Thumbnail ${idx + 1}">` : '<span>Gambar</span>'}
              </button>
            `).join('')}
          </div>
        </div>

        <div class="options-section">
          <h1>${productData.name || 'Produk'}</h1>
          <div class="price-display" id="displayPrice">${fmtPrice(calculateUnitPrice())}</div>

          ${productData.short_description ? `<p class="product-short-desc">${productData.short_description}</p>` : ''}

          <div class="option-group">
            <label class="option-label">Keterangan (Opsional)</label>
            <textarea class="note-textarea" id="productNote" placeholder="Tambahkan catatan..."></textarea>
          </div>

          <div class="option-group">
            <label class="option-label">Upload File Siap Cetak</label>
            <div class="file-upload-wrapper">
              <label class="file-upload-btn">
                <span>📁 Pilih File</span>
                <input id="printFileInput" type="file">
              </label>
              <div id="fileStatus"></div>
            </div>
          </div>

          ${productData.description ? `
            <div class="product-long-desc">
              ${String(productData.description).trim() ? `<p>${productData.description}</p>` : ''}
            </div>
          ` : ''}
        </div>

        <div class="checkout-box">
          <h3 class="checkout-title">Atur Jumlah dan Catatan</h3>

          <div class="quantity-stepper">
            <span class="quantity-label">Quantity :</span>
            <div class="stepper-controls">
              <button class="stepper-btn" type="button" id="qtyMinus">-</button>
              <span class="quantity-value" id="quantityValue">1</span>
              <button class="stepper-btn" type="button" id="qtyPlus">+</button>
            </div>
          </div>

          <div class="subtotal-row">
            <span class="subtotal-label">Subtotal</span>
            <span class="subtotal-value" id="subtotalValue">${fmtPrice(calculateUnitPrice())}</span>
          </div>

          <button class="checkout-btn" type="button" id="checkoutBtn">Beli Sekarang</button>
        </div>
      </div>
    `;

    wireDetailEvents();
    updatePriceAndSubtotal();
  }

  window.initProductDetailPage = async function initProductDetailPage() {
    try {
      const id = getIdFromPath() || getIdFromDom();
      if (!id) return renderProductNotFound('ID produk tidak ditemukan.');

      if (typeof window.showLoading === 'function') window.showLoading('productDetailContent', 1);

      const res = await fetchJSON(`/api/products/${id}`);
      if (!res?.ok || !res?.item) return renderProductNotFound('Produk tidak ditemukan.');

      productData = res.item;
      quantity = 1;
      uploadedFileName = null;
      currentNote = '';

      renderProductDetail();
    } catch (e) {
      console.error(e);
      renderProductNotFound('Gagal memuat produk dari server.');
    }
  };
})();
