<?php
// app/controllers/HomePublicController.php

require_once __DIR__ . '/../core/Controller.php';

class HomePublicController extends Controller
{
    protected mysqli $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
    }

    public function index(): void
    {
        // Settings auto-injected by base Controller

        // Fetch hero slides
        $heroSlides = [];
        if (
            $stmt = $this->db->prepare("
            SELECT title, subtitle, badge, cta_text, cta_link, image
            FROM hero_slides
            WHERE page_slug='home' AND is_active=1
            ORDER BY position ASC
        ")
        ) {
            $stmt->execute();
            $rs = $stmt->get_result();
            while ($r = $rs->fetch_assoc()) {
                $heroSlides[] = $r;
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
            WHERE is_active=1 AND is_featured=1
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
                SELECT name, position, photo, rating, message
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
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'testimonials' => $testimonials,
            'contact' => $contact,
            'cta' => $cta,
            'additionalJs' => [
                'frontend/js/smallCarousel.js',
                'frontend/js/render/renderHome.js'
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

        echo json_encode([
            'ok' => true,
            'data' => [
                'banners' => $banners,
                'categories' => $categories,
                'featuredProducts' => $featuredProducts,
                'printProducts' => $printProducts,
                'mediaProducts' => $mediaProducts,
                'testimonials' => [],
                'contact' => $contact,
                'cta' => $cta,
            ],
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
        SELECT id, name, slug, base_price, thumbnail
        FROM products
        WHERE is_active=1
         
          AND {$where}
        ORDER BY created_at DESC
        LIMIT {$limit}
    ";

        $rows = [];
        $res = $this->db->query($sql);
        if (!$res)
            return $rows;

        while ($r = $res->fetch_assoc()) {
            $img = trim((string) ($r['thumbnail'] ?? ''));
            if ($img !== '' && !preg_match('#^https?://#i', $img)) {
                $img = $baseUrl . '/' . ltrim($img, '/');
            }

            $rows[] = [
                'id' => (int) $r['id'],
                'name' => (string) ($r['name'] ?? ''),
                'slug' => (string) ($r['slug'] ?? ''),
                'price' => (float) ($r['base_price'] ?? 0),
                'image' => $img,
            ];
        }

        return $rows;
    }



}
