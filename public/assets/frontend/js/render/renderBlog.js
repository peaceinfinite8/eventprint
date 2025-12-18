// ============================================
// EventPrint - Blog Page Renderer
// ============================================

/**
 * Initialize Blog page
 */
async function initBlogPage() {
    try {
        showLoading('blogHero', 1);
        showLoading('unggulanCarousel', 4);
        showLoading('trenGrid', 4);

        const data = await loadData('../data/blog.json');

        renderHeroMosaic(data.featured);
        renderUnggulanCarousel(data.unggulan);
        renderTrenGrid(data.tren);

    } catch (error) {
        console.error('Error loading blog page:', error);
        showError('blogHero', 'Gagal memuat data. Silakan refresh halaman.');
    }
}

/**
 * Render hero mosaic layout
 */
function renderHeroMosaic(featured) {
    const container = document.getElementById('blogHero');
    if (!container) return;

    if (!featured || featured.length === 0) {
        showEmpty('blogHero', 'Belum ada artikel');
        return;
    }

    const mainPost = featured[0];
    const sidePosts = featured.slice(1, 4);

    let html = `
    <div class="blog-hero-main">
      <div class="blog-hero-main-image">
        ${mainPost.image ? `<img src="${mainPost.image}" alt="${mainPost.title}">` : '<span>Gambar Berita</span>'}
      </div>
      <div class="blog-hero-main-content">
        <h3>${mainPost.title}</h3>
        <p>${mainPost.excerpt}</p>
      </div>
    </div>
    
    <div class="blog-hero-aside">
  `;

    sidePosts.forEach(post => {
        html += `
      <div class="blog-hero-small">
        <div class="blog-hero-small-image">
          ${post.image ? `<img src="${post.image}" alt="${post.title}">` : 'Gambar Berita'}
        </div>
        <div class="blog-hero-small-content">
          <p>${post.title}</p>
        </div>
      </div>
    `;
    });

    html += '</div>';
    container.innerHTML = html;
}

/**
 * Render Postingan Unggulan carousel
 */
function renderUnggulanCarousel(posts) {
    const container = document.getElementById('unggulanCarousel');
    if (!container) return;

    if (!posts || posts.length === 0) {
        showEmpty('unggulanCarousel', 'Belum ada postingan unggulan');
        return;
    }

    const html = posts.map(post => `
    <div class="blog-carousel-card ${post.bgColor}">
      <div class="blog-carousel-title">${post.title}</div>
      <div class="blog-carousel-excerpt">${post.excerpt}</div>
    </div>
  `).join('');

    container.innerHTML = html;
}

/**
 * Render Sedang Tren grid
 */
function renderTrenGrid(posts) {
    const container = document.getElementById('trenGrid');
    if (!container) return;

    if (!posts || posts.length === 0) {
        showEmpty('trenGrid', 'Belum ada artikel trending');
        return;
    }

    const html = posts.map(post => `
    <div class="blog-card">
      <div class="blog-card-image">
        ${post.image ? `<img src="${post.image}" alt="${post.title}">` : '<span>Gambar Berita</span>'}
      </div>
      <div class="blog-card-content">
        <h4 class="blog-card-title">${post.title}</h4>
        <p class="blog-card-excerpt">${post.excerpt}</p>
      </div>
    </div>
  `).join('');

    container.innerHTML = html;
}
