<?php
// app/controllers/HomePublicController.php

require_once __DIR__ . '/../core/controller.php';
require_once __DIR__ . '/../models/Product.php';

class HomePublicController extends Controller
{
    protected $productModel;
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->db = db();
    }

    public function index()
    {
        $settings = $this->getSettings();

        // --- 1. HERO SLIDES (Safe Fetch) ---
        $heroSlides = [];
        try {
            $heroRes = $this->db->query("SELECT * FROM hero_slides WHERE is_active=1 AND page_slug='home' ORDER BY id ASC");
            if ($heroRes) {
                $heroSlides = $heroRes->fetch_all(MYSQLI_ASSOC);
            }
        } catch (\Throwable $e) {
        }

        // --- 2. SMALL BANNERS (Optional/Fallback) ---
        $smallBanners = [];
        try {
            try {
                $sbRes = $this->db->query("SELECT * FROM small_banners WHERE is_active=1 LIMIT 3");
                if ($sbRes) {
                    $smallBanners = $sbRes->fetch_all(MYSQLI_ASSOC);
                }
            } catch (\Throwable $e) {
                $sbRes = $this->db->query("SELECT * FROM hero_slides WHERE is_active=1 AND page_slug='home_small' LIMIT 3");
                if ($sbRes) {
                    $smallBanners = $sbRes->fetch_all(MYSQLI_ASSOC);
                }
            }
        } catch (\Throwable $e) {
        }

        // --- 3. DYNAMIC HOME SECTIONS ---
        $dynamicSections = [];
        $rawSections = $settings['home_sections'] ?? '[]';
        $sectionsConfig = json_decode($rawSections, true);

        if (is_array($sectionsConfig)) {
            foreach ($sectionsConfig as $config) {
                try {
                    $catId = $config['category_id'] ?? $config['categoryId'] ?? 0;
                    $catSql = "SELECT name, slug FROM product_categories WHERE id = " . (int) $catId . " LIMIT 1";
                    $catRes = $this->db->query($catSql);
                    $catRow = ($catRes && $catRes->num_rows > 0) ? $catRes->fetch_assoc() : null;

                    if ($catRow) {
                        $prodSql = "SELECT id, name, slug, thumbnail, base_price, discount_value, discount_type
                                    FROM products 
                                    WHERE category_id = " . (int) $catId . " 
                                    AND is_active = 1 
                                    AND deleted_at IS NULL 
                                    ORDER BY created_at DESC 
                                    LIMIT 6";
                        $prodRes = $this->db->query($prodSql);
                        $products = ($prodRes) ? $prodRes->fetch_all(MYSQLI_ASSOC) : [];

                        $dynamicSections[] = [
                            'config' => $config,
                            'id' => $config['id'] ?? uniqid(),
                            'label' => $config['label'] ?? $catRow['name'],
                            'category_id' => $catId,
                            'category_name' => $catRow['name'],
                            'category_slug' => $catRow['slug'],
                            'theme' => $config['theme'] ?? 'red',
                            'layout' => $config['layout'] ?? 'standard',
                            'image' => $config['image'] ?? '',
                            'custom_title' => $config['custom_title'] ?? '',
                            'custom_description' => $config['custom_description'] ?? '',
                            'custom_button_text' => $config['custom_button_text'] ?? '',
                            'overlay_style' => $config['overlay_style'] ?? 'dark',
                            'products' => $products
                        ];
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }

        // --- 4. TESTIMONIALS ---
        $testimonials = [];
        try {
            $testiRes = $this->db->query("SELECT * FROM testimonials WHERE is_active=1 ORDER BY created_at DESC LIMIT 10");
            if ($testiRes) {
                $testimonials = $testiRes->fetch_all(MYSQLI_ASSOC);
            }
        } catch (\Throwable $e) {
        }

        // --- 5. BLOG ARTICLES ---
        $articles = [];
        try {
            $blogRes = $this->db->query("SELECT id, title, slug, thumbnail, created_at, content FROM blog_posts WHERE status='published' ORDER BY created_at DESC LIMIT 3");
            if ($blogRes) {
                $articles = $blogRes->fetch_all(MYSQLI_ASSOC);
            }
        } catch (\Throwable $e) {
        }

        $data = [
            'heroSlides' => $heroSlides,
            'smallBanners' => $smallBanners,
            'dynamicSections' => $dynamicSections,
            'testimonials' => $testimonials,
            'articles' => $articles,
            'title' => 'Home',
            'additionalJs' => [
                'frontend/js/lib/dataClient.js?v=2.2',
                'frontend/js/render/renderHome.js?v=2.2'
            ],
            'additionalCss' => [
                'frontend/css/home-marketplace.css?v=2.2',
                'frontend/css/ourhome-parity.css?v=2.2'
            ]
        ];

        $this->renderFrontend('home/index', $data);
    }

    public function apiHome()
    {
        header('Content-Type: application/json');

        $response = [
            'success' => true,
            'banners' => [],
            'categories' => [],
            'featuredProducts' => [],
            'testimonials' => [],
            'whyChoose' => null,
            'infrastructureGallery' => []
        ];

        try {
            $heroRes = $this->db->query("SELECT * FROM hero_slides WHERE is_active=1 AND page_slug='home' ORDER BY id ASC");
            if ($heroRes) {
                $slides = $heroRes->fetch_all(MYSQLI_ASSOC);
                foreach ($slides as &$slide) {
                    $slide['image_url'] = $this->baseUrl($slide['image']);
                }
                $response['banners'] = $slides;
            }
        } catch (\Throwable $e) {
        }

        try {
            $catRes = $this->db->query("SELECT id, name, slug, icon FROM product_categories WHERE is_active=1 ORDER BY id ASC LIMIT 8");
            if ($catRes) {
                $cats = $catRes->fetch_all(MYSQLI_ASSOC);
                foreach ($cats as &$c) {
                    $c['label'] = $c['name'];
                }
                $response['categories'] = $cats;
            }
        } catch (\Throwable $e) {
        }

        try {
            $featSql = "SELECT id, name, slug, thumbnail as image, base_price, stock, discount_value, discount_type 
                        FROM products WHERE is_active=1 AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 8";
            $featRes = $this->db->query($featSql);
            if ($featRes) {
                $feats = $featRes->fetch_all(MYSQLI_ASSOC);
                foreach ($feats as &$f) {
                    if (!empty($f['image']))
                        $f['image'] = $this->baseUrl($f['image']);
                }
                $response['featuredProducts'] = $feats;
            }
        } catch (\Throwable $e) {
        }

        try {
            $testiRes = $this->db->query("SELECT * FROM testimonials WHERE is_active=1 ORDER BY created_at DESC LIMIT 10");
            if ($testiRes) {
                $response['testimonials'] = $testiRes->fetch_all(MYSQLI_ASSOC);
            }
        } catch (\Throwable $e) {
        }

        // Fetch Why Choose data from page_contents table
        try {
            $page = 'home';
            $section = 'why_choose';
            $stmt = $this->db->prepare("SELECT field, value FROM page_contents WHERE page_slug=? AND section=? ORDER BY field ASC");
            $stmt->bind_param('ss', $page, $section);
            $stmt->execute();
            $res = $stmt->get_result();

            $whyData = [];
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $whyData[(string) $r['field']] = (string) ($r['value'] ?? '');
                }
            }
            $stmt->close();

            if (!empty($whyData)) {
                // Parse description (stored as newline-separated text)
                $descText = $whyData['description'] ?? '';
                $descArray = array_filter(array_map('trim', explode("\n", $descText)));

                $response['whyChoose'] = [
                    'title' => $whyData['title'] ?? 'Mengapa Memilih Kami?',
                    'subtitle' => $whyData['subtitle'] ?? 'Kualitas Terbaik untuk Kebutuhan Anda',
                    'description' => !empty($descArray) ? array_values($descArray) : [
                        'Kami menggunakan mesin cetak terbaru untuk hasil maksimal.',
                        'Tim profesional siap membantu desain dan layout.',
                        'Harga kompetitif dengan pengerjaan tepat waktu.'
                    ],
                    'image' => !empty($whyData['image']) ? $this->baseUrl($whyData['image']) : $this->baseUrl('assets/frontend/images/whychoose/eventprint-2.jpg')
                ];
            } else {
                // Fallback to default values if no database entry
                $response['whyChoose'] = [
                    'title' => 'Mengapa Memilih Kami?',
                    'subtitle' => 'Kualitas Terbaik untuk Kebutuhan Anda',
                    'description' => [
                        'Kami menggunakan mesin cetak terbaru untuk hasil maksimal.',
                        'Tim profesional siap membantu desain dan layout.',
                        'Harga kompetitif dengan pengerjaan tepat waktu.'
                    ],
                    'image' => $this->baseUrl('assets/frontend/images/whychoose/eventprint-2.jpg')
                ];
            }
        } catch (\Throwable $e) {
            // Fallback on error
            $response['whyChoose'] = [
                'title' => 'Mengapa Memilih Kami?',
                'subtitle' => 'Kualitas Terbaik untuk Kebutuhan Anda',
                'description' => [
                    'Kami menggunakan mesin cetak terbaru untuk hasil maksimal.',
                    'Tim profesional siap membantu desain dan layout.',
                    'Harga kompetitif dengan pengerjaan tepat waktu.'
                ],
                'image' => $this->baseUrl('assets/frontend/images/whychoose/eventprint-2.jpg')
            ];
        }

        try {
            $sbData = [];
            try {
                $sbRes = $this->db->query("SELECT * FROM small_banners WHERE is_active=1 LIMIT 3");
                if ($sbRes) {
                    $sbData = $sbRes->fetch_all(MYSQLI_ASSOC);
                }
            } catch (\Throwable $e) {
                $sbRes = $this->db->query("SELECT * FROM hero_slides WHERE is_active=1 AND page_slug='home_small' LIMIT 3");
                if ($sbRes) {
                    $sbData = $sbRes->fetch_all(MYSQLI_ASSOC);
                }
            }
            foreach ($sbData as &$b) {
                $b['image'] = $this->baseUrl($b['image']);
            }
            $response['infrastructureGallery'] = $sbData;
        } catch (\Throwable $e) {
        }

        echo json_encode($response);
        exit;
    }
}
