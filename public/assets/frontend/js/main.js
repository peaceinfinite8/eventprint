
function escapeHTML(str){
  return String(str ?? "")
    .replaceAll("&","&amp;")
    .replaceAll("<","&lt;")
    .replaceAll(">","&gt;")
    .replaceAll('"',"&quot;")
    .replaceAll("'","&#039;");
}

/* missing js */


/* =========================================================
   MULTI-PAGE ROUTER (frontend-only)
   - Set body data-page="home|products|product-detail|articles|article-detail|our-home|contact"
   - Render only relevant sections per page.
   ========================================================= */
function setActiveNav(){
  const page = document.body.getAttribute("data-page") || "home";
  const links = document.querySelectorAll('#epNavLinks .nav-link[data-nav]');
  links.forEach(a => {
    if (a.getAttribute("data-nav") === page) a.classList.add("active");
    else a.classList.remove("active");
  });
}



// --- New: All products dataset (demo) ---
EP_DATA.allProducts = EP_DATA.allProducts || [
  { id: 1, name: "Print Warna A4/A3", category: "Digital Printing", icon: "bi-file-earmark-text", price: 12000,
    marketplaces: [{name:"Website",link:"contact.html#order",type:"web"},{name:"Shopee",link:"#",type:"shopee"}],
    options: { materials:["Art Paper 150","HVS 100"], finish:["Laminasi Glossy","Laminasi Doff"] }
  },
  { id: 2, name: "Sticker Vinyl (Die Cut)", category: "Sticker & Label", icon: "bi-sticky", price: 35000,
    marketplaces: [{name:"Tokopedia",link:"#",type:"tokopedia"}],
    options: { materials:["Vinyl","Transparan"], finish:["Cutting Die Cut"] }
  },
  { id: 3, name: "Roll Up Banner", category: "Media Promosi", icon: "bi-flag", price: 250000,
    marketplaces: [{name:"Website",link:"contact.html#order",type:"web"},{name:"Lazada",link:"#",type:"lazada"}],
    options: { materials:["Flexi Korea","Albatros"], finish:["Rangka standar","Rangka premium"] }
  },
  { id: 4, name: "Event Desk", category: "Media Promosi", icon: "bi-inboxes", price: 450000,
    marketplaces: [{name:"Shopee",link:"#",type:"shopee"},{name:"Tokopedia",link:"#",type:"tokopedia"}],
    options: { materials:["PVC Board","Foam Board"], finish:["Laminasi Doff"] }
  }
];

function moneyIDR(n){
  try { return new Intl.NumberFormat("id-ID",{style:"currency",currency:"IDR",maximumFractionDigits:0}).format(n); }
  catch { return "Rp " + n; }
}

function renderAllProducts(){
  const grid = document.getElementById("epAllProductsGrid");
  if (!grid) return;
  grid.innerHTML = "";
  EP_DATA.allProducts.forEach(p => {
    const col = epEl("div","col-12 col-md-6 col-xl-3");
    const markets = (p.marketplaces||[]).map(m => `
      <a class="ep-market-btn" href="${m.link}" target="_blank" rel="noopener">
        ${epMarketLogo(m.type)} ${m.name}
      </a>
    `).join("");
    col.innerHTML = `
      <div class="ep-product-card">
        <div class="ep-product-thumb">
          <span class="ep-thumb-label">${p.category}</span>
          <i class="bi ${p.icon}"></i>
        </div>
        <div class="ep-product-body">
          <h5 class="ep-product-name">${p.name}</h5>
          <div class="ep-product-meta">${moneyIDR(p.price)} • Harga dummy</div>
          <div class="d-flex gap-2 mt-3">
            <a class="btn btn-sm btn-primary flex-grow-1" href="product-detail.html?id=${p.id}">
              <i class="bi bi-eye-fill me-2"></i>Detail
            </a>
            <a class="btn btn-sm btn-outline-secondary" href="contact.html#order" title="Order">
              <i class="bi bi-bag-check-fill"></i>
            </a>
          </div>
          <div class="ep-market-row">${markets}</div>
        </div>
      </div>
    `;
    grid.appendChild(col);
  });
}

function renderProductDetail(){
  const wrap = document.getElementById("epProductDetail");
  if (!wrap) return;
  const params = new URLSearchParams(location.search);
  const id = Number(params.get("id") || "1");
  const p = EP_DATA.allProducts.find(x => x.id === id) || EP_DATA.allProducts[0];

  const materialOpts = (p.options?.materials||[]).map(x => `<option>${x}</option>`).join("");
  const finishOpts = (p.options?.finish||[]).map(x => `<option>${x}</option>`).join("");
  const markets = (p.marketplaces||[]).map(m => `
    <a class="ep-market-btn" href="${m.link}" target="_blank" rel="noopener">
      ${epMarketLogo(m.type)} ${m.name}
    </a>
  `).join("");

  wrap.innerHTML = `
    <div class="row g-4 align-items-start">
      <div class="col-lg-6">
        <div class="ep-product-card">
          <div class="ep-product-thumb" style="height:320px">
            <span class="ep-thumb-label">${p.category}</span>
            <i class="bi ${p.icon}" style="font-size:3.6rem"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="ep-eyebrow">Detail Produk</div>
        <h1 class="ep-title" style="font-size:clamp(1.6rem,2.4vw,2.2rem)">${p.name}</h1>
        <p class="ep-subtitle">${moneyIDR(p.price)} • Dummy price. Semua option siap dari backend.</p>

        <div class="row g-3 mt-2">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Material</label>
            <select class="form-select" id="epMaterial">${materialOpts}</select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Finishing</label>
            <select class="form-select" id="epFinishing">${finishOpts}</select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Qty</label>
            <div class="input-group">
              <button class="btn btn-outline-secondary" type="button" id="epQtyMinus">-</button>
              <input class="form-control text-center" id="epQty" value="1" inputmode="numeric" />
              <button class="btn btn-outline-secondary" type="button" id="epQtyPlus">+</button>
            </div>
          </div>
          <div class="col-md-6 d-grid">
            <label class="form-label fw-semibold">&nbsp;</label>
            <a class="btn btn-primary" href="contact.html#order"><i class="bi bi-bag-check-fill me-2"></i>Beli Sekarang</a>
          </div>
        </div>

        <div class="ep-market-row mt-3">${markets}</div>

        <div class="mt-4 p-3 rounded-4 border" style="border-color: rgba(2,8,23,.10)">
          <div class="fw-semibold mb-1">Catatan</div>
          <div class="text-muted small">Di backend nanti: kalkulasi harga berdasarkan material/finishing/qty, upload file, dan proofing.</div>
        </div>
      </div>
    </div>
  `;

  const qty = document.getElementById("epQty");
  const minus = document.getElementById("epQtyMinus");
  const plus = document.getElementById("epQtyPlus");
  if (qty && minus && plus){
    minus.addEventListener("click", () => qty.value = String(Math.max(1, Number(qty.value||1)-1)));
    plus.addEventListener("click", () => qty.value = String(Math.max(1, Number(qty.value||1)+1)));
  }
}

function renderArticleList(){
  const grid = document.getElementById("epArticleListGrid");
  if (!grid) return;
  grid.innerHTML = "";
  (EP_DATA.articles||[]).forEach((a, idx) => {
    const col = epEl("div","col-12 col-md-6 col-xl-4");
    col.innerHTML = `
      <div class="ep-article-card">
        <div class="ep-article-thumb"><i class="bi bi-journal-text"></i></div>
        <div class="ep-article-body">
          <h5 class="ep-article-title">${a.title}</h5>
          <div class="ep-article-excerpt">${a.excerpt}</div>
          <div class="mt-3 d-flex gap-2">
            <a class="btn btn-sm btn-outline-primary" href="article-detail.html?id=${idx+1}">Baca Artikel!</a>
            <a class="btn btn-sm btn-outline-secondary" href="contact.html#order">Konsultasi</a>
          </div>
        </div>
      </div>
    `;
    grid.appendChild(col);
  });
}

function renderArticleDetail(){
  const wrap = document.getElementById("epArticleDetail");
  if (!wrap) return;
  const params = new URLSearchParams(location.search);
  const id = Number(params.get("id") || "1");
  const a = (EP_DATA.articles||[])[id-1] || (EP_DATA.articles||[])[0] || {title:"Artikel",excerpt:""};
  wrap.innerHTML = `
    <div class="ep-eyebrow">Artikel</div>
    <h1 class="ep-title" style="font-size:clamp(1.6rem,2.6vw,2.4rem)">${a.title}</h1>
    <p class="ep-subtitle">${a.excerpt}</p>

    <div class="ep-article-card mt-4">
      <div class="ep-article-thumb" style="height:220px"><i class="bi bi-newspaper"></i></div>
      <div class="ep-article-body">
        <p class="text-muted">Ini dummy konten. Backend nanti akan supply konten HTML/markdown, author, kategori, tags, dan tanggal.</p>
        <p>Yang penting: struktur frontend sudah siap. Jangan hardcode konten real di HTML kalau targetnya CMS.</p>
        <a class="btn btn-outline-primary mt-2" href="articles.html"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
      </div>
    </div>
  `;
}

document.addEventListener("DOMContentLoaded", () => {
  // override original DOMContentLoaded (keep existing but route-aware)
});



document.addEventListener("DOMContentLoaded", () => {
  setActiveNav();
  bindMeta();

  const page = document.body.getAttribute("data-page") || "home";

  if (page === "home"){
    renderHero();
    renderUSP();
    renderCategories();
    renderServices();
    renderFeatured();
    renderPortfolio();
    renderAdvantages();
    renderTestimonials();
    renderArticles();
    initSearch();
    initContact(); // contact form exists only on contact page, safe
  }

  if (page === "products"){
    renderServices();
    renderAllProducts();
    initSearch();
  }

  if (page === "product-detail"){
    renderProductDetail();
    initSearch();
  }

  if (page === "articles"){
    renderArticleList();
    initSearch();
  }

  if (page === "article-detail"){
    renderArticleDetail();
    initSearch();
  }

  if (page === "our-home"){
    // bindMeta already fills store fields if present
    initSearch();
  }

  if (page === "contact"){
    initContact();
    initSearch();
  }
});


function renderHero(){

  const slidesEl = document.getElementById("epHeroSlides");
  const indEl = document.getElementById("epHeroIndicators");
  if (!slidesEl || !indEl) return;

  slidesEl.innerHTML = "";
  indEl.innerHTML = "";

  const banners = (EP_DATA.heroBanners || []).map((b, idx) => ({
    ...b,
    image: b.image || "",
    badge: b.badge || (idx === 0 ? "Cetak Online Terpercaya" : "Promo & Paket")
  }));

  banners.forEach((b, i) => {
    const btn = document.createElement("button");
    btn.type = "button";
    btn.setAttribute("data-bs-target", "#epHeroCarousel");
    btn.setAttribute("data-bs-slide-to", String(i));
    btn.setAttribute("aria-label", `Slide ${i+1}`);
    if (i === 0) btn.classList.add("active");
    indEl.appendChild(btn);

    const item = document.createElement("div");
    item.className = "carousel-item" + (i === 0 ? " active" : "");

    const bg = b.image
      ? `background-image:url('${b.image}')`
      : `background-image:
          radial-gradient(circle at 70% 30%, rgba(255,255,255,.18), rgba(255,255,255,0) 60%),
          linear-gradient(90deg, #0b4ea7, #00AEEF)`;

    item.innerHTML = `
      <div class="ep-hero-slide" style="${bg}">
        <div class="container-fluid px-4">
          <div class="ep-hero-container">
            <div class="ep-hero-copy">
              <div class="ep-hero-badge">
                <i class="bi bi-shield-check"></i>
                <span>${escapeHTML(b.badge)}</span>
              </div>

              <div class="ep-hero-title">${escapeHTML(b.title || "")}</div>
              <div class="ep-hero-sub">${escapeHTML(b.subtitle || "")}</div>

              <div class="ep-hero-actions">
                <a class="btn btn-light" href="${b.ctaLink || "contact.html#order"}">
                  <i class="bi bi-lightning-charge-fill me-2"></i>${escapeHTML(b.ctaText || "Order Sekarang")}
                </a>
                <a class="btn btn-outline-light" href="products.html">
                  <i class="bi bi-grid-1x2-fill me-2"></i>Lihat Produk
                </a>
              </div>
            </div>

            <div class="ep-hero-visual d-none d-lg-block">
              <div class="ep-hero-cardstack">
                <div class="ep-hero-mini">
                  <div class="t"><i class="bi bi-truck me-2"></i>Pengiriman Cepat</div>
                  <div class="d">Estimasi & tracking transparan (dummy).</div>
                </div>
                <div class="ep-hero-mini">
                  <div class="t"><i class="bi bi-stars me-2"></i>Quality Control</div>
                  <div class="d">Sebelum kirim, dicek ulang.</div>
                </div>
                <div class="ep-hero-mini">
                  <div class="t"><i class="bi bi-cash-coin me-2"></i>Harga Kompetitif</div>
                  <div class="d">Paket & promo mudah di-update.</div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    `;
    slidesEl.appendChild(item);
  });

  const carouselEl = document.getElementById("epHeroCarousel");
  if (carouselEl){
    carouselEl.classList.remove("carousel-fade");
    try{
      const inst = bootstrap.Carousel.getInstance(carouselEl);
      if (inst) inst.dispose();
      new bootstrap.Carousel(carouselEl, {
        interval: 5200,
        ride: "carousel",
        pause: false,
        touch: true,
        wrap: true
      });
    }catch(e){}
  }

}


(function () {
  const BASE = window.EP_BASE_URL || "";

  // ===== PRODUCTS LIST PAGE =====
  const grid = document.getElementById("epAllProductsGrid");
  if (grid) {
    fetch(`${BASE}/api/products`)
      .then(r => r.json())
      .then(j => {
        if (!j.ok) throw new Error(j.message || "Gagal load produk");
        grid.innerHTML = j.items.map(p => cardProduct(p)).join("");
      })
      .catch(err => {
        grid.innerHTML = `<div class="col-12"><div class="alert alert-danger">${escapeHtml(err.message)}</div></div>`;
      });
  }

  function cardProduct(p) {
    const thumb = p.thumbnail ? `${BASE}/${p.thumbnail}` : "";
    const price = formatRp(Number(p.base_price || 0));
    return `
      <div class="col-12 col-sm-6 col-lg-3">
        <a class="text-decoration-none" href="${BASE}/products/${p.id}">
          <div class="card h-100 shadow-sm">
            ${thumb ? `<img src="${thumb}" class="card-img-top" style="height:160px;object-fit:cover">` : ""}
            <div class="card-body">
              <div class="fw-semibold text-dark">${escapeHtml(p.name || "-")}</div>
              <div class="small text-muted mb-2">${escapeHtml(p.short_description || "")}</div>
              <div class="fw-bold text-dark">${price}</div>
            </div>
          </div>
        </a>
      </div>
    `;
  }

  // ===== PRODUCT DETAIL PAGE =====
  const detail = document.getElementById("epProductDetail");
  if (detail) {
    const productId = Number(detail.dataset.productId || 0);
    if (!productId) {
      detail.innerHTML = `<div class="alert alert-danger">product_id tidak valid</div>`;
      return;
    }

    Promise.all([
      fetch(`${BASE}/api/products/${productId}`).then(r => r.json()),
      fetch(`${BASE}/pricing/options?product_id=${productId}`).then(r => r.json())
    ])
      .then(([p, opt]) => {
        if (!p.ok) throw new Error(p.message || "Produk tidak ditemukan");
        if (!opt.ok) throw new Error(opt.message || "Opsi harga tidak ditemukan");

        renderDetail(detail, p.item, opt.groups || []);
        hookCalc(detail, productId);
      })
      .catch(err => {
        detail.innerHTML = `<div class="alert alert-danger">${escapeHtml(err.message)}</div>`;
      });
  }

  function renderDetail(root, p, groups) {
    const thumb = p.thumbnail ? `${BASE}/${p.thumbnail}` : "";
    root.innerHTML = `
      <div class="row g-4">
        <div class="col-12 col-lg-6">
          <div class="card shadow-sm">
            ${thumb ? `<img src="${thumb}" class="w-100" style="height:340px;object-fit:cover">` : ""}
            <div class="card-body">
              <h2 class="h4 mb-1">${escapeHtml(p.name || "-")}</h2>
              <div class="text-muted mb-3">${escapeHtml(p.short_description || "")}</div>
              <div class="fw-bold">Base: ${formatRp(Number(p.base_price || 0))}</div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-6">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Qty</label>
                <input type="number" class="form-control" id="epQty" value="1" min="1">
              </div>

              <div id="epOptions">
                ${groups.map(g => renderGroup(g)).join("")}
              </div>

              <button class="btn btn-primary w-100 mt-3" id="epBtnCalc">
                Hitung Harga
              </button>

              <div class="mt-3" id="epResult"></div>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  function renderGroup(g) {
    const type = (g.input_type || "select").toLowerCase();
    const name = `g_${g.id}`;

    if (type === "checkbox") {
      return `
        <div class="mb-3">
          <div class="fw-semibold mb-2">${escapeHtml(g.name || "-")}</div>
          ${ (g.values || []).map(v => `
            <label class="d-flex align-items-center gap-2 mb-2">
              <input type="checkbox" class="form-check-input ep-opt" value="${v.id}" data-group="${g.id}">
              <span>${escapeHtml(v.label)} <span class="text-muted">(+${formatRp(Number(v.price_value||0))})</span></span>
            </label>
          `).join("") }
        </div>
      `;
    }

    // select / radio -> pakai select biar simpel untuk orang awam
    return `
      <div class="mb-3">
        <label class="form-label fw-semibold">${escapeHtml(g.name || "-")}</label>
        <select class="form-select ep-opt-select" data-group="${g.id}" data-required="${g.is_required ? 1 : 0}">
          ${g.is_required ? `<option value="">-- pilih --</option>` : `<option value="">(opsional)</option>`}
          ${(g.values || []).map(v => `
            <option value="${v.id}">
              ${escapeHtml(v.label)} (+${formatRp(Number(v.price_value||0))})
            </option>
          `).join("")}
        </select>
      </div>
    `;
  }

  function hookCalc(root, productId) {
    const btn = root.querySelector("#epBtnCalc");
    const resBox = root.querySelector("#epResult");

    btn.addEventListener("click", () => {
      const qty = Number(root.querySelector("#epQty").value || 1);

      const ids = [];

      // checkbox
      root.querySelectorAll(".ep-opt:checked").forEach(el => ids.push(Number(el.value)));

      // select
      root.querySelectorAll(".ep-opt-select").forEach(sel => {
        const val = sel.value ? Number(sel.value) : 0;
        if (val) ids.push(val);
      });

      fetch(`${BASE}/pricing/calc`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ product_id: productId, qty, option_value_ids: ids })
      })
        .then(r => r.json())
        .then(j => {
          if (!j.ok) throw new Error(j.message || "Gagal menghitung");
          resBox.innerHTML = `
            <div class="alert alert-success mb-0">
              <div class="fw-semibold">Subtotal: ${formatRp(Number(j.breakdown?.subtotal || 0))}</div>
              <div class="small text-muted">Unit: ${formatRp(Number(j.breakdown?.unit_price || 0))}</div>
            </div>
          `;
        })
        .catch(err => {
          resBox.innerHTML = `<div class="alert alert-danger mb-0">${escapeHtml(err.message)}</div>`;
        });
    });
  }

  function formatRp(n) {
    const x = Math.round(Number(n || 0));
    return "Rp " + x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }

  function escapeHtml(s) {
    return String(s ?? "").replace(/[&<>"']/g, m => ({
      "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
    }[m]));
  }
})();
