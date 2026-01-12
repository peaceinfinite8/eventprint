<?php
// app/controllers/ProductPublicController.php

$controllerPath = realpath(__DIR__ . '/../core/Controller.php');
if ($controllerPath)
    require_once $controllerPath;

$productModelPath = realpath(__DIR__ . '/../models/Product.php');
if ($productModelPath)
    require_once $productModelPath;

$categoryModelPath = realpath(__DIR__ . '/../models/ProductCategory.php');
if ($categoryModelPath)
    require_once $categoryModelPath;

class ProductPublicController extends Controller
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


        // Get category filter from query string
        $categorySlug = $_GET['category'] ?? null;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        // Fetch all categories for sidebar with icon and product count
        $categories = [];
        $res = $this->db->query("
            SELECT c.id, c.name, c.slug, c.icon, c.sort_order,
                   COUNT(p.id) AS product_count
            FROM product_categories c
            LEFT JOIN products p
              ON p.category_id = c.id 
              AND p.is_active = 1 
              AND p.deleted_at IS NULL
            WHERE c.is_active = 1
            GROUP BY c.id, c.name, c.slug, c.icon, c.sort_order
            ORDER BY c.sort_order ASC, c.id ASC
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $categories[] = $r;
            }
        }

        // Calculate total products count for "Semua Produk"
        $totalProductsCountResult = $this->db->query("
            SELECT COUNT(*) as total 
            FROM products 
            WHERE is_active=1 AND deleted_at IS NULL
        ");
        $totalProductsCount = $totalProductsCountResult->fetch_assoc()['total'];

        // Build query based on category filter
        $whereClause = "p.is_active=1 AND p.deleted_at IS NULL";
        $categoryId = null;

        if ($categorySlug) {
            // Get category ID from slug
            $stmt = $this->db->prepare("SELECT id FROM product_categories WHERE slug=? AND is_active=1 LIMIT 1");
            $stmt->bind_param('s', $categorySlug);
            $stmt->execute();
            $catResult = $stmt->get_result();
            $catRow = $catResult->fetch_assoc();
            $stmt->close();

            if ($catRow) {
                $categoryId = $catRow['id'];
                $whereClause .= " AND p.category_id=" . (int) $categoryId;
            }
        }

        // Count total products
        $countResult = $this->db->query("SELECT COUNT(*) as total FROM products p WHERE $whereClause");
        $totalProducts = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalProducts / $perPage);

        // Fetch products
        $products = [];
        $productSql = "
            SELECT p.id, p.name, p.slug, p.base_price, p.thumbnail, pc.name as category_name
            FROM products p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE $whereClause
            ORDER BY p.created_at DESC
            LIMIT $perPage OFFSET $offset
        ";

        $result = $this->db->query($productSql);
        while ($r = $result->fetch_assoc()) {
            $products[] = $r;
        }

        $this->renderFrontend('product/index', [
            'page' => 'products',
            'title' => 'All Products',
            // settings auto-injected
            'categories' => $categories,
            'products' => $products,
            'currentCategory' => $categorySlug,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'totalProductsCount' => $totalProductsCount,
            'allProductsIcon' => '📦',
            // REFERENCE SCRIPT ORDER: utils → dataClient → navSearch → urlState → app → renderProducts
            'additionalJsBefore' => [
                'frontend/js/lib/dataClient.js',
                'frontend/js/components/navSearch.js',
                'frontend/js/lib/urlState.js',
            ],
            'additionalJs' => [
                'frontend/js/render/renderProducts.js'
            ]
        ]);
    }

    public function show($slug): void
    {
        // Settings auto-injected by base Controller

        // Fetch product by slug
        $stmt = $this->db->prepare("
            SELECT p.*, pc.name as category_name, pc.slug as category_slug, pc.whatsapp_number as category_whatsapp
            FROM products p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE p.slug=? AND p.is_active=1 AND p.deleted_at IS NULL
        ");
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();

        if (!$product) {
            http_response_code(404);
            $this->renderFrontend('errors/404', [
                // settings auto-injected
                'title' => 'Product Not Found'
            ]);
            return;
        }

        // Fetch product gallery
        $gallery = [];
        $stmt = $this->db->prepare("
            SELECT image_path
            FROM product_images
            WHERE product_id=?
            ORDER BY sort_order ASC
        ");
        $stmt->bind_param('i', $product['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($r = $result->fetch_assoc()) {
            $gallery[] = $r['image_path'];
        }
        $stmt->close();

        // Fetch product options
        $optionGroups = [];
        $stmt = $this->db->prepare("
            SELECT id, name, input_type, is_required
            FROM product_option_groups
            WHERE product_id=? AND is_active=1
            ORDER BY sort_order ASC
        ");
        $stmt->bind_param('i', $product['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($group = $result->fetch_assoc()) {
            // Fetch values for this group
            $stmt2 = $this->db->prepare("
                SELECT id, label, price_type, price_value
                FROM product_option_values
                WHERE group_id=? AND is_active=1
                ORDER BY sort_order ASC
            ");
            $stmt2->bind_param('i', $group['id']);
            $stmt2->execute();
            $valuesResult = $stmt2->get_result();
            $values = [];
            while ($v = $valuesResult->fetch_assoc()) {
                $values[] = $v;
            }
            $stmt2->close();

            $group['values'] = $values;
            $optionGroups[] = $group;
        }
        $stmt->close();

        // Fetch active discount
        $discount = null;
        $stmt = $this->db->prepare("
            SELECT discount_type, discount_value
            FROM product_discounts
            WHERE product_id=? AND is_active=1
              AND (start_at IS NULL OR start_at <= NOW())
              AND (end_at IS NULL OR end_at >= NOW())
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->bind_param('i', $product['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $discount = $result->fetch_assoc();
        $stmt->close();

        $this->renderFrontend('pages/product_detail', [
            'page' => 'product_detail',
            'title' => e($product['name']) . ' - Products',
            // settings auto-injected
            'product' => $product,
            'gallery' => $gallery,
            'optionGroups' => $optionGroups,
            'discount' => $discount,
            'additionalJsBefore' => [
                'frontend/js/lib/dataClient.js',
                'frontend/js/components/navSearch.js',
            ],
            'additionalJs' => [
                'frontend/js/render/renderProductDetail.js'
            ]
        ]);

    }

    // API endpoints (keep for backward compatibility)
    public function detailBySlug(): void
    {
        $slug = trim($_GET['slug'] ?? '');
        if ($slug) {
            $this->show($slug);
        } else {
            http_response_code(404);
            echo "Product not found";
        }
    }

    // GET /api/products - List products with pagination  
    public function apiList(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $page = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['per_page'] ?? 100); // Increased default to 100
        $categoryParam = $_GET['category'] ?? null; // Can be ID or slug
        $offset = ($page - 1) * $perPage;

        // Build WHERE clause
        $where = "p.is_active = 1 AND p.deleted_at IS NULL";
        $params = [];
        $types = '';

        if ($categoryParam && $categoryParam !== '') {
            // Try to resolve category: could be ID (numeric) or slug (string)
            $categoryId = null;

            if (is_numeric($categoryParam)) {
                // Direct ID
                $categoryId = (int) $categoryParam;
            } else {
                // Lookup by slug
                $stmt = $this->db->prepare("SELECT id FROM product_categories WHERE slug=? AND is_active=1 LIMIT 1");
                $stmt->bind_param('s', $categoryParam);
                $stmt->execute();
                $catResult = $stmt->get_result();
                $catRow = $catResult->fetch_assoc();
                $stmt->close();

                if ($catRow) {
                    $categoryId = (int) $catRow['id'];
                }
            }

            if ($categoryId) {
                $where .= " AND p.category_id = ?";
                $params[] = $categoryId;
                $types .= 'i';
            }
        }

        // Count total
        $countSql = "SELECT COUNT(*) as total FROM products p WHERE $where";
        if ($types) {
            $stmt = $this->db->prepare($countSql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $totalProducts = $stmt->get_result()->fetch_assoc()['total'];
            $stmt->close();
        } else {
            $result = $this->db->query($countSql);
            $totalProducts = $result->fetch_assoc()['total'];
        }

        // Fetch products - INCLUDE STOCK FIELD
        $products = [];
        $sql = "
            SELECT p.id, p.name, p.slug, p.base_price, p.thumbnail, p.stock,
                   p.discount_type, p.discount_value,
                   pc.name as category_name, pc.slug as category_slug, p.category_id
            FROM products p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE $where
            ORDER BY p.id DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($sql);
        $params[] = $perPage;
        $params[] = $offset;
        $types .= 'ii';
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $row['thumbnail'] = safeImageUrl($row['thumbnail'] ?? '', 'product');
            $row['main_image'] = $row['thumbnail']; // Alias for JS compatibility
            $row['stock'] = (int) ($row['stock'] ?? 0); // Ensure stock is integer
            $products[] = $row;
        }
        $stmt->close();

        echo json_encode([
            'success' => true,
            'products' => $products,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => (int) $totalProducts,
                'total_pages' => ceil($totalProducts / $perPage)
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function apiCategories(): void
    {
        header('Content-Type: application/json');

        // Count total active products (not soft-deleted)
        $totalCountResult = $this->db->query("
            SELECT COUNT(*) AS total_count
            FROM products
            WHERE is_active = 1 AND deleted_at IS NULL
        ");
        $totalCount = 0;
        if ($totalCountResult) {
            $row = $totalCountResult->fetch_assoc();
            $totalCount = (int) $row['total_count'];
        }

        // Fetch all active categories with product count (excluding soft-deleted products)
        $categories = [];
        $res = $this->db->query("
            SELECT c.id, c.name, c.slug, c.icon, c.description, c.sort_order,
                   COUNT(DISTINCT p.id) as product_count
            FROM product_categories c
            LEFT JOIN products p 
              ON p.category_id = c.id 
              AND p.is_active = 1 
              AND p.deleted_at IS NULL
            WHERE c.is_active = 1
            GROUP BY c.id, c.name, c.slug, c.icon, c.description, c.sort_order
            ORDER BY c.sort_order ASC, c.name ASC
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $categories[] = $r;
            }
        }

        echo json_encode([
            'ok' => true,
            'data' => [
                'total_count' => $totalCount,
                'categories' => $categories
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * API: Get single product by slug
     * GET /api/products/slug/{slug}
     */
    public function apiDetailBySlug($slug): void
    {
        // Prevent HTML warnings from breaking JSON
        error_reporting(0);
        ini_set('display_errors', '0');

        header('Content-Type: application/json; charset=utf-8');

        try {
            // Fetch product by slug with category info
            $stmt = $this->db->prepare("
                SELECT p.*, pc.name as category_name, pc.slug as category_slug, pc.whatsapp_number as category_whatsapp
                FROM products p
                LEFT JOIN product_categories pc ON p.category_id = pc.id
                WHERE p.slug=? AND p.is_active=1 AND p.deleted_at IS NULL
            ");
            $stmt->bind_param('s', $slug);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $stmt->close();

            if (!$product) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Product not found'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }

            // Fetch product gallery
            $gallery = [];
            $stmt = $this->db->prepare("
                SELECT image_path
                FROM product_images
                WHERE product_id=?
                ORDER BY sort_order ASC
            ");
            $stmt->bind_param('i', $product['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($r = $result->fetch_assoc()) {
                $gallery[] = safeImageUrl($r['image_path'] ?? '', 'product');
            }
            $stmt->close();

            // Fetch product options
            $optionGroups = [];
            $stmt = $this->db->prepare("
                SELECT id, name, input_type, is_required
                FROM product_option_groups
                WHERE product_id=? AND is_active=1
                ORDER BY sort_order ASC
            ");
            $stmt->bind_param('i', $product['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($group = $result->fetch_assoc()) {
                // Fetch values for this group
                $stmt2 = $this->db->prepare("
                    SELECT id, label, price_type, price_value
                    FROM product_option_values
WHERE group_id=? AND is_active=1
                    ORDER BY sort_order ASC
                ");
                $stmt2->bind_param('i', $group['id']);
                $stmt2->execute();
                $valuesResult = $stmt2->get_result();
                $values = [];
                while ($v = $valuesResult->fetch_assoc()) {
                    $values[] = $v;
                }
                $stmt2->close();

                $group['values'] = $values;
                $optionGroups[] = $group;
            }
            $stmt->close();

            // Fetch active discount
            $discount = null;
            $stmt = $this->db->prepare("
                SELECT discount_type, discount_value
                FROM product_discounts
                WHERE product_id=? AND is_active=1
                  AND (start_at IS NULL OR start_at <= NOW())
                  AND (end_at IS NULL OR end_at >= NOW())
                ORDER BY created_at DESC
                LIMIT 1
            ");
            $stmt->bind_param('i', $product['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $discount = $result->fetch_assoc();
            $stmt->close();

            //  Process thumbnail and gallery with safe URLs
            $product['thumbnail'] = safeImageUrl($product['thumbnail'] ?? '', 'product');
            $product['gallery'] = $gallery;
            $product['option_groups'] = $optionGroups;
            $product['discount'] = $discount;

            require_once __DIR__ . '/../helpers/pricing.php';
            $product['price_tiers'] = get_product_tiers($product['id']);

            // PHASE 3: Add category-based options (materials/laminations)
            $product['options'] = $this->getProductOptions($product['id'], (int) $product['category_id']);

            echo json_encode([
                'success' => true,
                'product' => $product
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;

        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
    }

    /**
     * API endpoint for product pricing information
     * GET /api/products/{id}/pricing
     * Returns: options, tiers, discount info for realtime calculation
     */
    public function apiPricing($id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $id = (int) $id;

            // Get product details
            $stmt = $this->db->prepare("
                SELECT id, name, slug, base_price, discount_type, discount_value
                FROM products
                WHERE id = ? AND is_active = 1 AND deleted_at IS NULL
            ");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $stmt->close();

            if (!$product) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Product not found'
                ]);
                exit;
            }

            // Get active options (using pricing helper)
            require_once __DIR__ . '/../helpers/pricing.php';
            $options = get_product_options($id);

            // Get active price tiers
            $tiers = get_product_tiers($id);

            // Return pricing data
            echo json_encode([
                'success' => true,
                'product' => [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'base_price' => (float) $product['base_price'],
                    'discount_type' => $product['discount_type'],
                    'discount_value' => (float) $product['discount_value']
                ],
                'options' => $options,
                'tiers' => $tiers
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch pricing data',
                'message' => $e->getMessage()
            ]);
        }

        exit;
    }

    /**
     * Get product options (materials/laminations) 
     * Supports: category-based, product-specific, or hybrid (both)
     * @param int $productId
     * @param int|null $categoryId - If not provided, will be fetched from product
     * @param string $optionsSource - 'category', 'product', or 'both' (default: 'category')
     * @return array ['materials' => [], 'laminations' => []]
     */
    private function getProductOptions(int $productId, ?int $categoryId = null, string $optionsSource = 'category'): array
    {
        $materials = [];
        $laminations = [];

        try {
            // Get category_id and options_source if not provided
            if ($categoryId === null || $optionsSource === 'category') {
                $stmt = $this->db->prepare("SELECT category_id, options_source FROM products WHERE id = ?");
                $stmt->bind_param('i', $productId);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $stmt->close();
                $categoryId = $row['category_id'] ?? null;
                $optionsSource = $row['options_source'] ?? 'category';
            }

            // Fetch category-based materials if source is 'category' or 'both'
            if (($optionsSource === 'category' || $optionsSource === 'both') && $categoryId) {
                $stmt = $this->db->prepare("
                SELECT 
                    m.id,
                    m.name,
                    m.slug,
                    COALESCE(cm.price_delta_override, m.price_delta) as price_delta,
                    m.sort_order,
                    m.image_path
                FROM materials m
                INNER JOIN category_materials cm ON m.id = cm.material_id
                WHERE cm.category_id = ? 
                  AND cm.is_active = 1 
                  AND m.is_active = 1
                ORDER BY m.sort_order ASC, m.name ASC
            ");
                $stmt->bind_param('i', $categoryId);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $materials[$row['id']] = [
                        'id' => $row['slug'] ?: (string) $row['id'],
                        'slug' => $row['slug'],
                        'name' => $row['name'],
                        'price_delta' => (float) $row['price_delta'],
                        'image' => !empty($row['image_path']) ? safeImageUrl($row['image_path'], 'product') : null
                    ];
                }
                $stmt->close();
            }

            // Fetch product-specific materials if source is 'product' or 'both'
            if ($optionsSource === 'product' || $optionsSource === 'both') {
                $stmt = $this->db->prepare("
                SELECT 
                    m.id,
                    m.name,
                    m.slug,
                    COALESCE(pm.price_delta_override, m.price_delta) as price_delta,
                    m.sort_order,
                    m.image_path
                FROM materials m
                INNER JOIN product_materials pm ON m.id = pm.material_id
                WHERE pm.product_id = ? 
                  AND pm.is_active = 1 
                  AND m.is_active = 1
                ORDER BY m.sort_order ASC, m.name ASC
            ");
                $stmt->bind_param('i', $productId);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    // Product-specific overrides category
                    $materials[$row['id']] = [
                        'id' => $row['slug'] ?: (string) $row['id'],
                        'slug' => $row['slug'],
                        'name' => $row['name'],
                        'price_delta' => (float) $row['price_delta'],
                        'image' => !empty($row['image_path']) ? safeImageUrl($row['image_path'], 'product') : null
                    ];
                }
                $stmt->close();
            }

            // Fetch category-based laminations if source is 'category' or 'both'
            if (($optionsSource === 'category' || $optionsSource === 'both') && $categoryId) {
                $stmt = $this->db->prepare("
                SELECT 
                    l.id,
                    l.name,
                    l.slug,
                    COALESCE(cl.price_delta_override, l.price_delta) as price_delta,
                    l.sort_order,
                    l.image_path
                FROM laminations l
                INNER JOIN category_laminations cl ON l.id = cl.lamination_id
                WHERE cl.category_id = ? 
                  AND cl.is_active = 1 
                  AND l.is_active = 1
                ORDER BY l.sort_order ASC, l.name ASC
            ");
                $stmt->bind_param('i', $categoryId);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $laminations[$row['id']] = [
                        'id' => $row['slug'] ?: (string) $row['id'],
                        'slug' => $row['slug'],
                        'name' => $row['name'],
                        'price_delta' => (float) $row['price_delta'],
                        'image' => !empty($row['image_path']) ? safeImageUrl($row['image_path'], 'product') : null
                    ];
                }
                $stmt->close();
            }

            // Fetch product-specific laminations if source is 'product' or 'both'
            if ($optionsSource === 'product' || $optionsSource === 'both') {
                $stmt = $this->db->prepare("
                SELECT 
                    l.id,
                    l.name,
                    l.slug,
                    COALESCE(pl.price_delta_override, l.price_delta) as price_delta,
                    l.sort_order,
                    l.image_path
                FROM laminations l
                INNER JOIN product_laminations pl ON l.id = pl.lamination_id
                WHERE pl.product_id = ? 
                  AND pl.is_active = 1 
                  AND l.is_active = 1
                ORDER BY l.sort_order ASC, l.name ASC
            ");
                $stmt->bind_param('i', $productId);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    // Product-specific overrides 
                    $laminations[$row['id']] = [
                        'id' => $row['slug'] ?: (string) $row['id'],
                        'slug' => $row['slug'],
                        'name' => $row['name'],
                        'price_delta' => (float) $row['price_delta'],
                        'image' => !empty($row['image_path']) ? safeImageUrl($row['image_path'], 'product') : null
                    ];
                }
                $stmt->close();
            }
        } catch (\Exception $e) {
            // If tables don't exist yet, return empty arrays
            error_log("getProductOptions error: " . $e->getMessage());
        }

        // Convert associative arrays to indexed arrays
        $materialsArray = array_values($materials);
        $laminationsArray = array_values($laminations);

        return [
            'materials' => [
                'enabled' => count($materialsArray) > 0,
                'items' => $materialsArray
            ],
            'laminations' => [
                'enabled' => count($laminationsArray) > 0,
                'items' => $laminationsArray
            ]
        ];
    }

    /**
     * Return empty options structure
     */
    private function emptyOptions(): array
    {
        return [
            'materials' => ['enabled' => false, 'items' => []],
            'laminations' => ['enabled' => false, 'items' => []]
        ];
    }
}
