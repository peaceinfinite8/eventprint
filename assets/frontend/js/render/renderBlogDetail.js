// ============================================
// EventPrint - Blog Detail Renderer (PHP API Version)
// ============================================

/**
 * Initialize Blog Detail page
 */
async function initBlogDetailPage() {
  try {
    const slug = getBlogSlugFromURL();
    if (!slug) {
      renderBlogNotFound('Artikel tidak ditemukan or URL invalid.');
      return;
    }

    showLoading('blogDetailContent', 1);

    // Fetch data
    const data = await loadData(`/api/blog/${slug}`);

    if (data && data.success && data.post) {
      document.title = `${data.post.title} - EventPrint Blog`;
      renderBlogDetail(data.post, data.relatedPosts || []);
    } else {
      renderBlogNotFound('Artikel tidak ditemukan.');
    }

  } catch (error) {
    console.error('Error loading blog detail:', error);
    renderBlogNotFound('Gagal memuat artikel. Silakan coba lagi.');
  }
}

/**
 * Get slug from URL
 */
function getBlogSlugFromURL() {
  const pathParts = window.location.pathname.split('/');
  // URL: /blog/{slug} or /articles/{slug}
  const index = pathParts.findIndex(p => p === 'blog' || p === 'articles');
  if (index !== -1 && index + 1 < pathParts.length) {
    return pathParts[index + 1];
  }
  return null;
}

/**
 * Render blog detail content
 */
function renderBlogDetail(post, relatedPosts) {
  const container = document.getElementById('blogDetailContent');
  if (!container) return;

  const baseUrl = window.EP_BASE_URL || '';
  const date = new Date(post.published_at).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric'
  });

  let html = `
    <article class="blog-detail">
      <!-- Header -->
      <header class="blog-header mb-4">
        <h1 class="blog-title mb-2">
            ${post.external_url ?
      `<a href="${post.external_url}" target="${post.link_target || '_blank'}" style="text-decoration: none; color: inherit;">${post.title} <small style="font-size: 0.6em">🔗</small></a>`
      : post.title}
        </h1>
        <div class="blog-meta text-muted small mb-4">
          <span>📅 ${date}</span>
          <span class="mx-2">•</span>
          <span>👤 Admin</span>
        </div>
        <div class="blog-thumbnail mb-4 rounded overflow-hidden" style="max-height: 500px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
          ${post.thumbnail ?
      `<img src="${post.thumbnail}" alt="${post.title}" style="width: 100%; height: auto; object-fit: cover;">` :
      `<span style="color: #999; font-size: 3rem;">📰</span>`
    }
        </div>
      </header>

      <!-- Content -->
      <div class="blog-content mb-5" style="line-height: 1.8; font-size: 1.1rem; color: #333;">
        ${post.content || '<p><i>Belum ada konten artikel.</i></p>'}
      </div>

      <!-- Share (Optional Placeholder) -->
      <div class="blog-share border-top pt-4 mb-5">
        <h5>Bagikan:</h5>
        <div class="d-flex gap-2">
           <a href="https://wa.me/?text=${encodeURIComponent(post.title + ' ' + window.location.href)}" target="_blank" class="btn btn-sm btn-outline-success">WhatsApp</a>
           <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}" target="_blank" class="btn btn-sm btn-outline-primary">Facebook</a>
        </div>
      </div>
    </article>
  `;

  // Related Posts
  if (relatedPosts && relatedPosts.length > 0) {
    html += `
      <section class="related-posts" style="margin-top: 80px; padding-top: 40px; border-top: 1px solid #eee;">
        <h3 class="mb-4">Artikel Terkait</h3>
        <div class="grid grid-3" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
          ${relatedPosts.map(rp => `
            <div class="card h-100 shadow-sm border-0" style="overflow: hidden; border-radius: 8px;">
               <div class="card-img-top" style="height: 200px; background: #eee; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                 ${rp.thumbnail ?
        `<img src="${rp.thumbnail}" alt="${rp.title}" style="width: 100%; height: 100%; object-fit: cover;">` :
        `<span>📰</span>`
      }
               </div>
               <div class="card-body p-3">
                 <h5 class="card-title" style="font-size: 1.1rem; margin-bottom: 0.5rem;">
                   <a href="${rp.external_url || `${baseUrl}/blog/${rp.slug || rp.id}`}" target="${rp.external_url ? (rp.link_target || '_blank') : '_self'}" style="text-decoration: none; color: inherit;">${rp.title} ${rp.external_url ? '🔗' : ''}</a>
                 </h5>
                 <p class="card-text small text-muted">${new Date(rp.published_at).toLocaleDateString('id-ID')}</p>
               </div>
            </div>
          `).join('')}
        </div>
      </section>
    `;
  }

  container.innerHTML = html;
}

/**
 * Render Not Found
 */
function renderBlogNotFound(msg) {
  const container = document.getElementById('blogDetailContent');
  if (container) {
    container.innerHTML = `
      <div class="text-center py-5">
        <h3>oops!</h3>
        <p>${msg}</p>
        <a href="${window.EP_BASE_URL || ''}/blog" class="btn btn-primary mt-3">Kembali ke Blog</a>
      </div>
    `;
  }
}

// Init
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initBlogDetailPage);
} else {
  initBlogDetailPage();
}
