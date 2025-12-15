<?php $vars = $vars ?? []; $baseUrl = $vars['baseUrl'] ?? '/eventprint/public'; ?>



  <!-- Products by Category Sections -->
  <section class="ep-section py-4">
    <div class="container-fluid px-4">
      <div class="ep-section-head d-flex align-items-end justify-content-between gap-3 flex-wrap mb-4">
        <div>
          <div class="ep-eyebrow-sm">Kategori Produk</div>
          <h2 class="ep-title-sm">Semua Produk</h2>
        </div>
        <a class="btn btn-primary" href="contact.html#order">
          <i class="bi bi-lightning-charge-fill me-2"></i>Order Sekarang
        </a>
      </div>

      <div class="ep-category-slider-wrapper">
        <button class="ep-category-arrow ep-category-arrow--prev" id="epCategoryPrev">
          <i class="bi bi-chevron-left"></i>
        </button>
        <button class="ep-category-arrow ep-category-arrow--next" id="epCategoryNext">
          <i class="bi bi-chevron-right"></i>
        </button>
        
        <div class="ep-category-slider" id="epCategorySlider">
          <div class="ep-category-scroll" id="epCategoryPills"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Product Categories with Carousels -->
  <div id="epProductCategories"></div>

  <!-- Testimonial Section (same size as category cards) -->
  <section class="ep-section py-4">
    <div class="container-fluid px-4">
      <div id="epTestiCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2000">
        <div class="carousel-indicators" id="epTestiIndicators"></div>
        <div class="carousel-inner" id="epTestiSlides"></div>
        
        <button class="carousel-control-prev ep-hero-control" type="button" data-bs-target="#epTestiCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Prev</span>
        </button>
        <button class="carousel-control-next ep-hero-control" type="button" data-bs-target="#epTestiCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="ep-section py-5">
    <div class="container-fluid px-4">
      <div class="ep-section-head d-flex align-items-end justify-content-between gap-3 flex-wrap mb-4">
        <div>
          <div class="ep-eyebrow-sm">Hubungi Kami</div>
          <h2 class="ep-title-sm">Kunjungi Workshop Kami</h2>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-7">
          <div class="ep-contact-map">
            <div class="ep-map-placeholder">
              <i class="bi bi-geo-alt-fill"></i>
              <div class="ep-map-text">Peta Lokasi</div>
              <div class="ep-map-subtext">EventPrint Workshop</div>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="ep-contact-info">
            <div class="ep-contact-item">
              <div class="ep-contact-icon">
                <i class="bi bi-geo-alt-fill"></i>
              </div>
              <div class="ep-contact-details">
                <h4 class="ep-contact-title">Alamat</h4>
                <p class="ep-contact-text">
                  Jl. Sudirman No. 123, Jakarta Pusat<br>
                  DKI Jakarta 10110<br>
                  Indonesia
                </p>
              </div>
            </div>

            <div class="ep-contact-item">
              <div class="ep-contact-icon">
                <i class="bi bi-envelope-fill"></i>
              </div>
              <div class="ep-contact-details">
                <h4 class="ep-contact-title">Email</h4>
                <p class="ep-contact-text">
                  info@eventprint.id<br>
                  support@eventprint.id
                </p>
              </div>
            </div>

            <div class="ep-contact-item">
              <div class="ep-contact-icon">
                <i class="bi bi-whatsapp"></i>
              </div>
              <div class="ep-contact-details">
                <h4 class="ep-contact-title">WhatsApp</h4>
                <p class="ep-contact-text">
                  +62 812-3456-7890<br>
                  Senin - Sabtu, 08:00 - 18:00
                   <a class="btn btn-success btn-lg w-100" href="https://wa.me/6281234567890" target="_blank">
                <i class="bi bi-whatsapp me-2"></i>Hubungi Kami
              </a>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Article CTA Cards Section -->
  <section class="ep-section py-5 bg-light">
    <div class="container-fluid px-4">
      <div class="row g-4">
        <div class="col-md-6">
          <a class="ep-article-cta-card" href="articles.html">
            <div class="ep-article-cta-icon">
              <i class="bi bi-journal-text-fill"></i>
            </div>
            <div class="ep-article-cta-content">
              <h3 class="ep-article-cta-title">Tips Desain & Panduan Cetak</h3>
              <p class="ep-article-cta-text">Pelajari cara membuat desain yang sempurna untuk hasil cetak terbaik</p>
              <div class="ep-article-cta-btn">
                <span>Baca Artikel</span>
                <i class="bi bi-arrow-right"></i>
              </div>
            </div>
          </a>
        </div>
        <div class="col-md-6">
          <a class="ep-article-cta-card" href="articles.html">
            <div class="ep-article-cta-icon">
              <i class="bi bi-lightbulb-fill"></i>
            </div>
            <div class="ep-article-cta-content">
              <h3 class="ep-article-cta-title">Material & Teknik Printing</h3>
              <p class="ep-article-cta-text">Kenali berbagai jenis material dan teknik cetak untuk kebutuhan Anda</p>
              <div class="ep-article-cta-btn">
                <span>Jelajahi Lebih Lanjut</span>
                <i class="bi bi-arrow-right"></i>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </section>