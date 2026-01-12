// ============================================
// EventPrint - Blog Page Renderer (PHP API Version)
// ============================================

/**
 * Initialize Blog page
 */
async function initBlogPage() {
  try {
    showLoading('blogHero', 1);
    showLoading('unggulanCarousel', 4);
    showLoading('trenGrid', 4);

    const data = await loadData('/api/blog');

    if (data && data.success) {
      // Fallback: If no featured posts, use recent posts for Hero
      let heroData = (data.featured && data.featured.length > 0) ? data.featured : (data.recent || []);

      renderHeroMosaic(heroData);
      renderUnggulanCarousel(data.featured || []);
      renderTrenGrid(data.recent || []);
    }

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
  const baseUrl = window.EP_BASE_URL || '';

  let html = `
    <div class="blog-hero-main">
      <div class="blog-hero-main-image">
        <a href="${mainPost.external_url || `${baseUrl}/blog/${mainPost.slug || mainPost.id}`}" target="${mainPost.link_target || '_self'}" style="display:block; width:100%; height:100%;">
          ${mainPost.thumbnail ?
      `<img src="${mainPost.thumbnail}" alt="${mainPost.title}">` :
      '<span>Gambar Berita</span>'}
        </a>
      </div>
      <div class="blog-hero-main-content">
        <h3><a href="${mainPost.external_url || `${baseUrl}/blog/${mainPost.slug || mainPost.id}`}" target="${mainPost.link_target || '_self'}">${mainPost.title}</a></h3>
        <p>${mainPost.excerpt || ''}</p>
      </div>
    </div>
    
    <div class="blog-hero-aside">
  `;

  sidePosts.forEach(post => {
    html += `
      <div class="blog-hero-small">
        <div class="blog-hero-small-image">
          <a href="${post.external_url || `${baseUrl}/blog/${post.slug || post.id}`}" target="${post.link_target || '_self'}" style="display:block; width:100%; height:100%;">
            ${post.thumbnail ?
        `<img src="${post.thumbnail}" alt="${post.title}">` :
        'Gambar Berita'}
          </a>
        </div>
        <div class="blog-hero-small-content">
          <p><a href="${post.external_url || `${baseUrl}/blog/${post.slug || post.id}`}" target="${post.link_target || '_self'}">${post.title}</a></p>
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

  const baseUrl = window.EP_BASE_URL || '';
  const colors = ['bg-blue', 'bg-green', 'bg-purple', 'bg-orange'];

  const html = posts.map((post, index) => `
    <div class="blog-carousel-card ${colors[index % colors.length]}">
      <div class="blog-carousel-title">
        <a href="${post.external_url || `${baseUrl}/blog/${post.slug || post.id}`}" target="${post.link_target || '_self'}" style="color: inherit; text-decoration: none;">
          ${post.title}
        </a>
      </div>
      <div class="blog-carousel-excerpt">${post.excerpt || ''}</div>
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

  const baseUrl = window.EP_BASE_URL || '';
  const html = posts.map(post => `
    <div class="blog-card">
      <div class="blog-card-image">
        <a href="${post.external_url || `${baseUrl}/blog/${post.slug || post.id}`}" target="${post.link_target || '_self'}" style="display:block; width:100%; height:100%;">
          ${post.thumbnail ?
      `<img src="${post.thumbnail}" alt="${post.title}">` :
      '<span>Gambar Berita</span>'}
        </a>
      </div>
      <div class="blog-card-content">
        <h4 class="blog-card-title">
          <a href="${post.external_url || `${baseUrl}/blog/${post.slug || post.id}`}" target="${post.link_target || '_self'}">${post.title}</a>
        </h4>
        <p class="blog-card-excerpt">${post.excerpt || ''}</p>
      </div>
    </div>
  `).join('');

  container.innerHTML = html;
}
