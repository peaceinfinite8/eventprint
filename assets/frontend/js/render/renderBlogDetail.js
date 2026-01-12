// ============================================
// EventPrint - Blog Detail Renderer (PHP API Version)
// Layout matched with Blog List Page (Grid System)
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

        const container = document.getElementById('blogDetailContent');
        if (container) {
            container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted">Memuat artikel...</p></div>';
        }

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
        renderBlogNotFound('Gagal memuat artikel.');
    }
}

function getBlogSlugFromURL() {
    const pathParts = window.location.pathname.split('/');
    const index = pathParts.findIndex(p => p === 'blog');
    if (index !== -1 && index + 1 < pathParts.length) {
        return pathParts[index + 1];
    }
    return null;
}

function resolveImageUrl(path) {
    if (!path || path === 'null') return null;
    if (path.startsWith('http')) return path;
    if (path.includes('placeholder')) return null;

    const baseUrl = window.EP_BASE_URL || '';
    const cleanPath = path.replace(/^\/+/, '');
    const cleanBase = baseUrl.replace(/\/+$/, '');
    return `${cleanBase}/${cleanPath}`;
}

/**
 * Render blog detail content
 * Layout: Grid 2 Column (Main Content Left, Related Sidebar Right) matches Index Page Grid
 */
function renderBlogDetail(post, relatedPosts) {
    const container = document.getElementById('blogDetailContent');
    if (!container) return;

    const baseUrl = window.EP_BASE_URL || '';
    const date = new Date(post.published_at).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    });
    const imageUrl = resolveImageUrl(post.thumbnail);

    // Custom CSS for Grid Layout & Newsletter Styling
    const style = `
    <style>
        .related-title {
            font-size: 0.95rem;
        }
        .blog-detail-grid {
            display: grid;
            grid-template-columns: 2fr 1fr; /* Ratio mirip Hero Section */
            gap: 32px;
            margin-bottom: 40px;
        }
        
        /* Main Content Styling */
        .blog-detail-main {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .blog-detail-image {
            width: 100%;
            height: 450px; /* FIXED HEIGHT Match Design Spec */
            background: #f1f5f9;
            position: relative;
            overflow: hidden;
        }

        .blog-detail-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .blog-detail-body {
            padding: 32px;
        }

        .blog-detail-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #334155;
            margin-top: 24px;
        }

        .blog-detail-content img {
            max-width: 100%;
            border-radius: 8px;
            margin: 16px 0;
        }

        /* Sidebar Styling */
        .blog-sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .sidebar-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            padding: 20px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .related-item {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
            text-decoration: none;
            color: inherit;
            align-items: center;
        }
        .related-item:last-child { margin-bottom: 0; }
        
        .related-thumb {
            width: 90px;
            height: 90px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
            background: #eee;
        }

        /* Newsletter Custom Styles */
        .newsletter-input {
            border-radius: 48px;
            padding: 12px 18px;
            border: 2px solid rgba(0,0,0,0.05);
            background: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            font-size: 0.9rem;
            color: #475569;
        }
        .newsletter-input:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }
        .newsletter-input:focus {
            outline: none;
            border-color: #2563eb;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }
        
        .newsletter-btn {
            border-radius: 48px;
            padding: 10px 24px;
            font-weight: 700;
            font-size: 0.9rem;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            border: none;
            width: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
            cursor: pointer;
            letter-spacing: 0.5px;
        }
        .newsletter-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        @media (max-width: 768px) {
            .blog-detail-grid {
                 /* Maintain 2 columns but adjust ratio and gap */
                grid-template-columns: 1.5fr 1fr; 
                gap: 8px; /* Tighter gap */
            }

            .blog-detail-image {
                height: 140px; /* Reduced fixed height again */
            }
            
            .blog-detail-body {
                padding: 12px; /* Very tight padding */
            }

            .blog-detail-content {
                font-size: 0.75rem; /* Miniature text */
                line-height: 1.5;
            }

            /* Force H1 small */
            h1.display-6 {
                font-size: 1.1rem !important; 
                margin-bottom: 0.5rem !important;
                line-height: 1.3;
            }

            /* Meta row small */
            .blog-detail-body .mb-3 {
                margin-bottom: 0.5rem !important;
                display: flex;
                flex-wrap: wrap;
                gap: 4px;
            }
            .blog-detail-body .badge {
                font-size: 0.6rem !important;
                padding: 3px 8px !important;
            }
            .blog-detail-body .text-muted.small {
                font-size: 0.6rem !important;
                margin-left: 4px !important;
            }
            
            /* Sidebar adjustments */
            .blog-sidebar {
                gap: 12px;
            }
            
            .sidebar-card {
                padding: 10px;
            }
            
            /* Sidebar Title */
            .sidebar-card h5 {
                font-size: 0.9rem !important;
                margin-bottom: 0.75rem !important;
            }

            .related-item {
                flex-direction: row; /* Match Desktop Layout (Side-by-side) */
                gap: 8px;
                align-items: center;
            }
            
            .related-thumb {
                width: 50px; /* Even smaller thumb */
                height: 50px;
                min-width: 50px;
            }
            
            .related-title {
                font-size: 0.7rem; /* Tiny title */
                line-height: 1.2;
            }
            
            .related-item small {
                font-size: 0.6rem !important;
            }

            /* Newsletter adjustments */
            .newsletter-input {
                padding: 6px 10px;
                font-size: 0.7rem;
                border-radius: 20px;
                margin-bottom: 0.5rem !important;
            }
            .newsletter-btn {
                padding: 6px 10px;
                font-size: 0.7rem;
                border-radius: 20px;
            }
            
            /* Share Buttons - Force small */
            .share-buttons .btn {
                padding: 4px 8px !important;
                font-size: 0.65rem !important;
            }
             .share-buttons i {
                margin-right: 4px !important;
            }
        }
    </style>
  `;

    let html = style + `
    <div class="blog-detail-grid">
        
        <!-- MAIN CONTENT (Left) -->
        <article class="blog-detail-main">
            ${imageUrl ?
            `<div class="blog-detail-image">
                    <img src="${imageUrl}" alt="${post.title}" onerror="this.parentElement.style.display='none'">
                 </div>` : ''
        }
            
            <div class="blog-detail-body">
                <div class="mb-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary text-uppercase px-3 py-2 rounded-pill fw-bold" style="font-size:0.75rem; letter-spacing:1px;">
                        ${post.post_category || 'Artikel'}
                    </span>
                    <span class="text-muted ms-3 small"><i class="far fa-calendar me-1"></i> ${date}</span>
                </div>

                <h1 class="fw-bold mb-4 text-dark display-6">${post.title}</h1>

                <div class="blog-detail-content">
                    ${post.content || '<p class="text-muted fst-italic">Konten tidak tersedia.</p>'}
                </div>

                <div class="mt-5 pt-4 border-top">
                    <h6 class="fw-bold mb-3">Bagikan:</h6>
                    <div class="d-flex gap-2 share-buttons">
                        <a href="https://wa.me/?text=${encodeURIComponent(post.title + ' ' + window.location.href)}" target="_blank" class="btn btn-sm btn-success px-4 rounded-pill"><i class="fab fa-whatsapp me-2"></i> WhatsApp</a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}" target="_blank" class="btn btn-sm btn-primary px-4 rounded-pill"><i class="fab fa-facebook-f me-2"></i> Facebook</a>
                    </div>
                </div>
            </div>
        </article>

        <!-- SIDEBAR (Right) -->
        <aside class="blog-sidebar">
            <div class="sidebar-card">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">Artikel Terkait</h5>
                <div class="d-flex flex-column gap-3">
                    ${relatedPosts.length > 0 ? relatedPosts.map(rp => {
            const rpImg = resolveImageUrl(rp.thumbnail);
            return `
                        <a href="${baseUrl}/blog/${rp.slug || rp.id}" class="related-item group">
                            ${rpImg ?
                    `<img src="${rpImg}" class="related-thumb shadow-sm" alt="${rp.title}">` :
                    `<div class="related-thumb d-flex align-items-center justify-content-center bg-light text-muted small"><i class="fas fa-image"></i></div>`
                }
                            <div>
                                <h6 class="fw-bold mb-1 text-dark line-clamp-2 related-title">${rp.title}</h6>
                                <small class="text-muted">${new Date(rp.published_at).toLocaleDateString('id-ID')}</small>
                            </div>
                        </a>
                        `;
        }).join('') : '<p class="text-muted small">Tidak ada artikel terkait.</p>'}
                </div>
            </div>

            <div class="sidebar-card border-0" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                <h5 class="fw-bold mb-2 text-primary">Newsletter</h5>
                <p class="small text-muted mb-4">Dapatkan update artikel terbaru langsung di email Anda.</p>
                <div class="d-flex flex-column">
                    <input type="email" class="newsletter-input shadow-sm mb-3" placeholder="Masukkan Email Anda...">
                    <button class="newsletter-btn">Ingatkan saya</button>
                </div>
            </div>
        </aside>

    </div>
  `;

    container.innerHTML = html;
}

function renderBlogNotFound(msg) {
    const container = document.getElementById('blogDetailContent');
    if (container) {
        container.innerHTML = `<div class="text-center py-5"><h3>Oops!</h3><p>${msg}</p><a href="${window.EP_BASE_URL || ''}/blog" class="btn btn-primary mt-3">Kembali</a></div>`;
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBlogDetailPage);
} else {
    initBlogDetailPage();
}
