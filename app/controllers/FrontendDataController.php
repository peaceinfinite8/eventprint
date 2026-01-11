<?php
// app/controllers/FrontendDataController.php

class FrontendDataController extends Controller
{
    protected mysqli $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db(); // pakai helper db() kamu (mysqli)
    }

    // GET /data/{file}
    public function serve($file)
    {
        $file = basename((string)$file);

        if ($file === '' || !str_ends_with($file, '.json')) {
            return $this->json404('Not found');
        }

        // products.json -> dibuat dari DB (opsi C)
        if ($file === 'products.json') {
            return $this->serveProductsJsonFromDb();
        }

        // file json lain tetap statis dari public/data
        $path = __DIR__ . '/../../public/data/' . $file;
        if (!is_file($path)) {
            return $this->json404('File tidak ditemukan');
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        readfile($path);
    }

    private function serveProductsJsonFromDb()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

        try {
            $baseUrl = rtrim($this->config['base_url'] ?? ($this->config['baseUrl'] ?? '/eventprint'), '/');

            // 1) categories (no subcategories in your schema)
            $categories = $this->fetchCategories();

            // 2) products
            $productsRaw = $this->fetchProducts();

            // 3) options (materials/laminations) from product_option_groups + product_option_values
            $products = [];
            foreach ($productsRaw as $p) {
                $productId = (int)$p['id'];
                $basePrice = (float)$p['base_price'];

                [$materials, $laminations] = $this->buildMaterialAndLaminationOptions($productId, $basePrice);

                $thumb = (string)($p['thumbnail'] ?? '');
                $images = [];
                if ($thumb !== '') {
                    // kalau thumbnail sudah full url, keep. kalau relatif, prefix baseUrl
                    if (preg_match('~^https?://~i', $thumb)) $images[] = $thumb;
                    else $images[] = $baseUrl . '/' . ltrim($thumb, '/');
                }

                // mapping categoryId pakai slug biar match sidebar id (string)
                $categoryId = (string)($p['category_slug'] ?? 'all');

                // minimal payload biar renderer temanmu jalan
                $products[] = [
                    'slug'       => (string)$p['slug'],
                    'name'       => (string)$p['name'],
                    'base_price' => $basePrice,
                    'images'     => $images,

                    // renderer kamu filter pakai categoryId/subcategoryId string
                    'categoryId'    => $categoryId ?: 'all',
                    'subcategoryId' => '',

                    // product-detail renderer butuh ini (minimal)
                    'options' => [
                        'materials'   => $materials,
                        'laminations' => $laminations,
                    ],

                    // default aman
                    'upload_rules' => [
                        // BIKIN AMAN: bentuk string CSV biar handleFileUpload split(',') jalan
                        // (kalau renderer kamu masih pakai join(), segera ubah di renderProductDetail.js)
                        'accept' => '.pdf,.cdr,.ai,.psd,.jpg,.jpeg,.png',
                        'max_mb' => 25,
                    ],
                    'work_time'   => [],
                    'notes'       => [],
                    'description' => $this->splitParagraphs((string)($p['description'] ?? '')),
                    'specs'       => [],
                    'marketplace' => null,
                ];
            }

            $payload = [
                'categories' => $categories,
                'products'   => $products,
            ];

            echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'ok'      => false,
                'message' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    private function fetchCategories(): array
    {
        // frontend kamu expect ada item "all"
        $out = [
            [
                'id' => 'all',
                'name' => 'Semua Produk',
                'subcategories' => [],
            ]
        ];

        $sql = "SELECT id, name, slug
                FROM product_categories
                WHERE is_active = 1
                ORDER BY sort_order ASC, name ASC";

        $res = $this->db->query($sql);
        if (!$res) return $out;

        while ($row = $res->fetch_assoc()) {
            $slug = (string)($row['slug'] ?? '');
            $name = (string)($row['name'] ?? '');
            if ($slug === '' || $name === '') continue;

            $out[] = [
                'id' => $slug,
                'name' => $name,
                'subcategories' => [], // schema kamu memang tidak punya subcategory
            ];
        }

        return $out;
    }

    private function fetchProducts(): array
    {
        $sql = "SELECT
                    p.id, p.name, p.slug, p.thumbnail, p.base_price,
                    c.slug AS category_slug
                FROM products p
                LEFT JOIN product_categories c ON c.id = p.category_id
                WHERE p.is_active = 1 AND p.deleted_at IS NULL
                ORDER BY p.is_featured DESC, p.created_at DESC";

        $res = $this->db->query($sql);
        if (!$res) return [];

        $rows = [];
        while ($row = $res->fetch_assoc()) {
            // frontend butuh slug & name. skip kalau kosong
            if (empty($row['slug']) || empty($row['name'])) continue;
            $rows[] = $row;
        }
        return $rows;
    }

    private function buildMaterialAndLaminationOptions(int $productId, float $basePrice): array
    {
        $materials = ['enabled' => false, 'items' => []];
        $laminations = ['enabled' => false, 'items' => []];

        $sql = "SELECT
                    g.id AS group_id, g.name AS group_name,
                    v.id AS value_id, v.label, v.price_type, v.price_value
                FROM product_option_groups g
                LEFT JOIN product_option_values v
                    ON v.group_id = g.id AND v.is_active = 1
                WHERE g.product_id = ? AND g.is_active = 1
                ORDER BY g.sort_order ASC, v.sort_order ASC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [$materials, $laminations];

        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($r = $res->fetch_assoc()) {
            $gname = strtolower(trim((string)($r['group_name'] ?? '')));
            $vid   = (string)($r['value_id'] ?? '');
            $label = (string)($r['label'] ?? '');

            // kalau group ada tapi belum punya value, skip
            if ($vid === '' || $label === '') continue;

            $ptype  = (string)($r['price_type'] ?? 'fixed');
            $pvalue = (float)($r['price_value'] ?? 0);

            $delta = 0.0;
            if ($ptype === 'percent') {
                $delta = round(($basePrice * $pvalue) / 100.0, 2);
            } else {
                $delta = $pvalue;
            }

            $item = [
                'id' => $vid,            // string id
                'name' => $label,
                'price_delta' => $delta,
            ];

            // mapping group -> materials / laminations (berdasarkan nama group)
            if (str_contains($gname, 'bahan') || str_contains($gname, 'material')) {
                $materials['items'][] = $item;
            } elseif (str_contains($gname, 'lamin')) {
                $laminations['items'][] = $item;
            }
        }

        $stmt->close();

        $materials['enabled'] = count($materials['items']) > 0;
        $laminations['enabled'] = count($laminations['items']) > 0;

        return [$materials, $laminations];
    }

    private function splitParagraphs(string $text): array
    {
        $t = trim($text);
        if ($t === '') return [];
        // pecah berdasarkan newline
        $parts = preg_split("/\R+/", $t);
        $out = [];
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p !== '') $out[] = $p;
        }
        return $out;
    }

    private function json404(string $msg)
    {
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'message' => $msg], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
