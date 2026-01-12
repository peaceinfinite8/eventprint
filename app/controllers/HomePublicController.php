<?php
// app/controllers/HomePublicController.php

require_once __DIR__ . '/../core/controller.php';

class HomePublicController extends Controller
{
    protected mysqli $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
    }

    /**
     * Safe public image URL resolver with file existence check
     * Prevents 404s by returning placeholder for missing files
     */
    private function safePublicImage(string $path, string $type = 'product'): string
    {
        $baseUrl = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');
        $publicPath = realpath(__DIR__ . '/../../public');

        // Empty path -> return placeholder
        if (empty(trim($path))) {
            return $baseUrl . '/assets/frontend/images/' . $type . '-placeholder.jpg';
        }

        // Full URL -> return as-is (assume external/CDN)
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        // Normalize path
        $normalized = ltrim($path, '/');

        // If just filename without directory, prefix with uploads/products/
        if (!str_contains($normalized, '/') && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $normalized)) {
            $normalized = 'uploads/products/' . $normalized;
        }

        // Check file existence
        $fullPath = $publicPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalized);

        if (file_exists($fullPath) && is_file($fullPath)) {
            return $baseUrl . '/' . $normalized;
        }

        // File not found -> return placeholder
        return $baseUrl . '/assets/frontend/images/' . $type . '-placeholder.jpg';
    }

    public function index(): void
    {
        // Settings auto-injected by base Controller

        // Fetch hero slides for all locations
        $heroSlides = [];
        $heroSlidesRightTop = [];
        $heroSlidesRightBottom = [];

        if (
            $stmt = $this->db->prepare("
            SELECT title, subtitle, badge, cta_text, cta_link, image, page_slug
            FROM hero_slides
            WHERE page_slug IN ('home', 'home_right_top', 'home_right_bottom') AND is_active=1
            ORDER BY position ASC
        ")
        ) {
            $stmt->execute();
            $rs = $stmt->get_result();
            while ($r = $rs->fetch_assoc()) {
                $slug = $r['page_slug'] ?? 'home';
                if ($slug === 'home_right_top') {
                    $heroSlidesRightTop[] = $r;
                } elseif ($slug === 'home_right_bottom') {
                    $heroSlidesRightBottom[] = $r;
                } else {
                    $heroSlides[] = $r;
                }
            }
            $stmt->close();
        }

        // Fetch product categories
        $categories = [];
        $res = $this->db->query("
            SELECT id, name, slug, icon
            FROM product_categories
            WHERE is_active=1
            ORDER BY sort_order ASC
            LIMIT 7
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $categories[] = $r;
            }
        }

        // Fetch featured products
        $featuredProducts = [];
        $res = $this->db->query("
            SELECT id, name, slug, base_price, thumbnail
            FROM products
            WHERE is_active=1 AND is_featured=1 AND deleted_at IS NULL
            ORDER BY created_at DESC
            LIMIT 8
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $featuredProducts[] = $r;
            }
        }

        // Fetch testimonials (if table exists)
        $testimonials = [];
        $tableCheck = $this->db->query("SHOW TABLES LIKE 'testimonials'");
        if ($tableCheck && $tableCheck->num_rows > 0) {
            $res = $this->db->query("
                SELECT name, position, photo, rating, message, bg_color
                FROM testimonials
                WHERE is_active=1
                ORDER BY sort_order ASC
                LIMIT 6
            ");
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $testimonials[] = $r;
                }
            }
        }

        // Fetch page content (contact + CTA)
        $homeContent = [];
        if (
            $stmt = $this->db->prepare("
            SELECT field, value
            FROM page_contents
            WHERE page_slug='home' AND section='home_content'
        ")
        ) {
            $stmt->execute();
            $rs = $stmt->get_result();
            while ($r = $rs->fetch_assoc()) {
                $homeContent[(string) $r['field']] = (string) ($r['value'] ?? '');
            }
            $stmt->close();
        }

        $contact = [
            'address' => (string) ($homeContent['contact_address'] ?? ''),
            'email' => (string) ($homeContent['contact_email'] ?? ''),
            'whatsapp' => (string) ($homeContent['contact_whatsapp'] ?? ''),
        ];

        $cta = [
            'left_text' => (string) ($homeContent['cta_left_text'] ?? 'Baca Artikel!'),
            'left_link' => (string) ($homeContent['cta_left_link'] ?? 'blog'),
            'right_text' => (string) ($homeContent['cta_right_text'] ?? 'Kenapa Pilih Kami?'),
            'right_link' => (string) ($homeContent['cta_right_link'] ?? 'our-home'),
        ];

        $this->renderFrontend('home/index', [
            'page' => 'home',
            'title' => 'Home - Digital Printing & Media Promosi',
            // settings auto-injected
            'heroSlides' => $heroSlides,
            'heroSlidesRightTop' => $heroSlidesRightTop,
            'heroSlidesRightBottom' => $heroSlidesRightBottom,
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'testimonials' => $testimonials,
            'contact' => $contact,
            'cta' => $cta,
            // REFERENCE SCRIPT ORDER: utils → dataClient → navSearch → app → smallCarousel → renderHome
            'additionalJsBefore' => [
                'frontend/js/lib/dataClient.js',
                'frontend/js/components/navSearch.js',
            ],
            'additionalJs' => [
                'frontend/js/render/renderHome.js?v=1.2'
            ]
        ]);
    }

    // GET /api/home
    public function apiHome(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $baseUrl = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');

        // 1) HERO SLIDES
        $banners = [];
        if (
            $stmt = $this->db->prepare("
        SELECT id, title, subtitle, badge, cta_text, cta_link, image, position
        FROM hero_slides
        WHERE page_slug='home' AND is_active=1
        ORDER BY position ASC, id ASC
    ")
        ) {
            $stmt->execute();
            $rs = $stmt->get_result();
            while ($r = $rs->fetch_assoc()) {
                $img = trim((string) ($r['image'] ?? ''));
                if ($img !== '' && !preg_match('#^https?://#i', $img)) {
                    // Check if it's already an absolute path relative to project root
                    // If running from localhost/eventprint, baseUrl is http://localhost/eventprint
                    // Image in DB is usually 'uploads/hero_banner_new.jpg'
                    // So we want http://localhost/eventprint/uploads/hero_banner_new.jpg

                    // Just simple append 
                    $img = $baseUrl . '/' . ltrim($img, '/');
                }

                $banners[] = [
                    'id' => (int) $r['id'],
                    'title' => (string) ($r['title'] ?? ''),
                    'subtitle' => (string) ($r['subtitle'] ?? ''),
                    'badge' => (string) ($r['badge'] ?? ''),
                    'cta' => (string) ($r['cta_text'] ?? ''),
                    'cta_link' => (string) ($r['cta_link'] ?? ''),
                    'image' => $img,
                ];
            }
            $stmt->close();
        }

        // 1.5) SMALL BANNERS (infrastructureGallery)
        $infrastructureGallery = [];
        if ($stmt = $this->db->prepare("SELECT title, image, cta_link FROM hero_slides WHERE page_slug='home_small' AND is_active=1 ORDER BY position ASC")) {
            $stmt->execute();
            $rs = $stmt->get_result();
            while ($r = $rs->fetch_assoc()) {
                $img = trim((string) ($r['image'] ?? ''));
                if ($img !== '' && !preg_match('#^https?://#i', $img)) {
                    $img = $baseUrl . '/' . ltrim($img, '/');
                }

                $infrastructureGallery[] = [
                    'image' => $img,
                    'alt' => (string) ($r['title'] ?? ''),
                    'link' => (string) ($r['cta_link'] ?? ''),
                ];
            }
            $stmt->close();
        }

        // 2) HOME CONTENT (contact + CTA + mapping category)
        $homeContent = [];
        if (
            $stmt = $this->db->prepare("
        SELECT field, value
        FROM page_contents
        WHERE page_slug='home' AND section='home_content'
    ")
        ) {
            $stmt->execute();
            $rs = $stmt->get_result();
            while ($r = $rs->fetch_assoc()) {
                $homeContent[(string) $r['field']] = (string) ($r['value'] ?? '');
            }
            $stmt->close();
        }

        $printCatId = (int) ($homeContent['home_print_category_id'] ?? 0);
        $mediaCatId = (int) ($homeContent['home_media_category_id'] ?? 0);
        $merchCatId = (int) ($homeContent['home_merch_category_id'] ?? 0);

        // 3) CATEGORIES (FIX: pakai $this->db, bukan $db)
        $categories = [];
        $res = $this->db->query("
        SELECT id, name, slug, icon
        FROM product_categories
        WHERE is_active=1
        ORDER BY sort_order ASC, id ASC
    ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $categories[] = [
                    'id' => (int) $r['id'],
                    'label' => (string) $r['name'],
                    'slug' => (string) ($r['slug'] ?? ''),
                    'icon' => (string) ($r['icon'] ?? '🖨️'),
                ];
            }
        }

        // 4) PRODUCTS (lebih aman: pakai method parametrik)
        $featuredProducts = $this->pickProductsFeatured(8, $baseUrl);
        $printProducts = ($printCatId > 0) ? $this->pickProductsByCategoryId($printCatId, 8, $baseUrl) : [];
        $mediaProducts = ($mediaCatId > 0) ? $this->pickProductsByCategoryId($mediaCatId, 8, $baseUrl) : [];
        $merchProducts = ($merchCatId > 0) ? $this->pickProductsByCategoryId($merchCatId, 8, $baseUrl) : [];

        // 5) CONTACT + CTA
        $contact = [
            'address' => (string) ($homeContent['contact_address'] ?? ''),
            'email' => (string) ($homeContent['contact_email'] ?? ''),
            'whatsapp' => (string) ($homeContent['contact_whatsapp'] ?? ''),
        ];

        $cta = [
            'left_text' => (string) ($homeContent['cta_left_text'] ?? 'Baca Artikel!'),
            'left_link' => (string) ($homeContent['cta_left_link'] ?? ($baseUrl . '/blog')),
            'right_text' => (string) ($homeContent['cta_right_text'] ?? 'Kenapa Pilih Kami?'),
            'right_link' => (string) ($homeContent['cta_right_link'] ?? ($baseUrl . '/our-home')),
        ];

        // 6) TESTIMONIALS
        $testimonials = [];
        $tableCheck = $this->db->query("SHOW TABLES LIKE 'testimonials'");
        if ($tableCheck && $tableCheck->num_rows > 0) {
            $res = $this->db->query("
                SELECT name, position, photo, rating, message, is_active, bg_color
                FROM testimonials
                WHERE is_active=1
                ORDER BY sort_order ASC
                LIMIT 6
            ");
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $photoUrl = !empty($r['photo']) ? safeImageUrl($r['photo'], 'testimonial') : '';
                    $testimonials[] = [
                        'name' => (string) ($r['name'] ?? ''),
                        'position' => (string) ($r['position'] ?? ''),
                        'photo' => $photoUrl,
                        'rating' => (int) ($r['rating'] ?? 5),
                        'message' => (string) ($r['message'] ?? ''),
                        'is_active' => (int) ($r['is_active'] ?? 1),
                        'bg_color' => (string) ($r['bg_color'] ?? '#0EA5E9'),
                    ];
                }
            }
        }

        // 7) WHY CHOOSE
        $whyChoose = null;
        $whyChooseData = [];
        if (
            $stmt = $this->db->prepare("
            SELECT field, value
            FROM page_contents
            WHERE page_slug='home' AND section='why_choose'
        ")
        ) {
            $stmt->execute();
            $rs = $stmt->get_result();
            while ($r = $rs->fetch_assoc()) {
                $whyChooseData[(string) $r['field']] = (string) ($r['value'] ?? '');
            }
            $stmt->close();
        }

        // Build whyChoose object
        if (!empty($whyChooseData)) {
            $imgPath = safeImageUrl((string) ($whyChooseData['image'] ?? ''), 'store');

            // Parse description (could be JSON array or newline-separated text)
            $desc = (string) ($whyChooseData['description'] ?? '');
            if (!empty($desc)) {
                $descArray = json_decode($desc, true);
                if (!is_array($descArray)) {
                    $descArray = array_filter(explode("\n", $desc), fn($line) => trim($line) !== '');
                }
            } else {
                $descArray = [];
            }

            $whyChoose = [
                'title' => (string) ($whyChooseData['title'] ?? 'Mengapa Memilih Kami?'),
                'subtitle' => (string) ($whyChooseData['subtitle'] ?? ''),
                'image' => $imgPath,
                'description' => array_values($descArray),
            ];
        } else {
            // Default fallback if no DB data
            $whyChoose = [
                'title' => 'Mengapa Memilih EventPrint?',
                'subtitle' => 'Solusi Printing Profesional Anda',
                'image' => $baseUrl . '/assets/frontend/images/whychoose/eventprint-2.jpeg',
                'description' => [
                    'EventPrint menyediakan layanan digital printing berkualitas tinggi dengan harga terjangkau.',
                    'Kami melayani berbagai kebutuhan cetak untuk event, promosi, dan kebutuhan bisnis Anda.',
                    'Dengan pengalaman bertahun-tahun, kami siap membantu mewujudkan ide kreatif Anda.',
                ],
            ];
        }

        // FIXED: Changed 'ok' to 'success' and flattened 'data' wrapper
        // to match renderHome.js expectations
        echo json_encode([
            'success' => true,
            'banners' => $banners,
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'printProducts' => $printProducts,
            'mediaProducts' => $mediaProducts,
            'merchProducts' => $merchProducts,
            'testimonials' => $testimonials,
            'contact' => $contact,
            'cta' => $cta,
            'whyChoose' => $whyChoose,
            'infrastructureGallery' => $infrastructureGallery,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // Wrapper biar call lama tetap jalan
    private function pickProductsFeatured(int $limit, string $baseUrl): array
    {
        return $this->pickProducts("is_featured=1", $limit, $baseUrl);
    }

    private function pickProductsByCategoryId(int $categoryId, int $limit, string $baseUrl): array
    {
        $categoryId = (int) $categoryId;
        if ($categoryId <= 0)
            return [];
        return $this->pickProducts("category_id={$categoryId}", $limit, $baseUrl);
    }

    // Core helper (WAJIB ada)
    private function pickProducts(string $where, int $limit, string $baseUrl): array
    {
        $limit = max(1, (int) $limit);

        $sql = "
        SELECT id, name, slug, base_price, thumbnail, stock, discount_type, discount_value
        FROM products
        WHERE is_active=1 AND deleted_at IS NULL
         
          AND {$where}
        ORDER BY created_at DESC
        LIMIT {$limit}
    ";

        $rows = [];
        $res = $this->db->query($sql);
        if (!$res)
            return $rows;

        while ($r = $res->fetch_assoc()) {
            $img = safeImageUrl((string) ($r['thumbnail'] ?? ''), 'product');

            $rows[] = [
                'id' => (int) $r['id'],
                'name' => (string) ($r['name'] ?? ''),
                'slug' => (string) ($r['slug'] ?? ''),
                'price' => (float) ($r['base_price'] ?? 0),
                'base_price' => (float) ($r['base_price'] ?? 0),
                'stock' => (int) ($r['stock'] ?? 0),
                'discount_type' => (string) ($r['discount_type'] ?? 'none'),
                'discount_value' => (float) ($r['discount_value'] ?? 0),
                'image' => $img,
                'main_image' => $img,
            ];
        }

        return $rows;
    }



}
