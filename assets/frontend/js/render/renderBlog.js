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

  // LOGIG BARU: Priority 'large' post for Main
  let mainPost = featured.find(p => p.post_type === 'large');

  // Jika tidak ada yang diset 'large', ambil yang pertama
  if (!mainPost) {
    mainPost = featured[0];
  }

  // Filter mainPost dari list untuk sidePosts
  // Kita ambil maksimal 3 untuk side
  const sidePosts = featured
    .filter(p => p.id !== mainPost.id)
    .slice(0, 3);

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
        <div class="blog-excerpt text-muted small" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
            ${mainPost.excerpt || mainPost.content || ''}
        </div>
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
    <a href="${post.external_url || `${baseUrl}/blog/${post.slug || post.id}`}" 
       target="${post.link_target || '_self'}" 
       class="blog-carousel-card ${colors[index % colors.length]}"
       style="text-decoration: none; color: inherit;">
      <div class="blog-carousel-title">
          ${post.title}
      </div>
      <div class="blog-carousel-excerpt" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
          ${post.excerpt || post.content || ''}
      </div>
    </a>
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
        <div class="blog-card-excerpt" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
            ${post.excerpt || post.content || ''}
        </div>
      </div>
    </div>
  `).join('');

  container.innerHTML = html;
}
