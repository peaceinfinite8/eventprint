<?php
// app/controllers/HomePublicController.php

require_once __DIR__ . '/../models/OurStore.php';
require_once __DIR__ . '/../models/Post.php';

class HomePublicController extends Controller
{
    protected mysqli $db;
    protected OurStore $store;
    protected Post $post;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db    = db();
        $this->store = new OurStore();
        $this->post  = new Post();
    }

    private function getHeroData(): array
    {
        $sql = "SELECT field, value
                FROM page_contents
                WHERE page_slug='home' AND section='hero'";
        $res = $this->db->query($sql);

        $data = [
            'title'        => '',
            'subtitle'     => '',
            'button_text'  => '',
            'button_link'  => '',
            'image'        => '',
        ];

        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $f = (string)($r['field'] ?? '');
                if (array_key_exists($f, $data)) $data[$f] = (string)($r['value'] ?? '');
            }
        }

        return $data;
    }

    private function getPublicCategories(): array
    {
        $db = db();

        // product_categories kamu memang TIDAK punya deleted_at, jadi jangan dipakai.
        $sql = "SELECT id, name, slug, description, sort_order
                FROM product_categories
                WHERE is_active = 1
                ORDER BY sort_order ASC, id DESC";

        $res = $db->query($sql);
        $rows = [];
        if ($res) while ($r = $res->fetch_assoc()) $rows[] = $r;

        return $rows;
    }

    private function getProductsByCategory(int $categoryId, int $limit = 10): array
    {
        $limit = max(1, (int)$limit);

        // products kamu sudah pakai deleted_at (karena sebelumnya kamu memang punya konsep soft delete)
        $sql = "SELECT id, name, slug, short_description, base_price, thumbnail, is_featured
                FROM products
                WHERE category_id=? AND is_active=1 AND deleted_at IS NULL
                ORDER BY is_featured DESC, created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];

        $stmt->bind_param('ii', $categoryId, $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return $rows;
    }

    public function index()
    {
        $hero = $this->getHeroData();
        $categories = $this->getPublicCategories();

        $categoryBlocks = [];
        foreach ($categories as $c) {
            $cid = (int)($c['id'] ?? 0);
            $categoryBlocks[] = [
                'category' => $c,
                'products' => $cid ? $this->getProductsByCategory($cid, 10) : [],
            ];
        }

        $stores = [];
        try { $stores = $this->store->getLatest(1); } catch (\Throwable $e) { $stores = []; }
        $store = !empty($stores) ? $stores[0] : null;

        $latestPosts = [];
        try { $latestPosts = $this->post->getLatest(3); } catch (\Throwable $e) { $latestPosts = []; }

        $this->renderFrontend(
        'home/index',
        ['baseUrl' => '/eventprint/public', 'page' => 'home'],
        'EventPrint — Home'
        );

    }
}
