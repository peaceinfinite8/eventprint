<?php
// views/frontend/pages/blog_detail.php
// STRICT FRONTEND-FIRST APPROACH
// Reference: frontend/public/views/blog-detail.html
?>

<!-- Blog Article Content -->
<!-- Blog Article Content -->
<style>
    /* Scoped Styles for Blog Detail */
    .blog-page-wrapper {
        margin-top: 30px;
        /* Jarak aman dari Navbar Fixed */
        min-height: 80vh;
        /* Dorong footer ke bawah */
        position: relative;
        z-index: 1;
        /* Pastikan di atas elemen background lain */
    }

    .blog-detail-container {
        background: #fff;
        border-radius: 12px;
        /* Opsional: Shadow halus */
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        padding: 40px;
    }

    @media (max-width: 768px) {
        .blog-page-wrapper {
            margin-top: 20px;
            padding-left: 8px;
            /* Lebih lebar untuk konten */
            padding-right: 8px;
        }

        .blog-detail-container {
            padding: 10px;
            /* Minimal padding container */
            box-shadow: none;
            background: transparent;
            /* Remove white box on mobile maybe? Or keep it but simple */
        }
    }
</style>

<section class="blog-page-wrapper">
    <div class="container">
        <div id="blogDetailContent" class="blog-detail-container">
            <!-- Loading State -->
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-3">Memuat artikel...</p>
            </div>
        </div>
    </div>
</section>