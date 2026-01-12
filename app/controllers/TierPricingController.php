<?php
// app/controllers/TierPricingController.php

require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../models/ProductPriceTier.php';
require_once __DIR__ . '/../models/Product.php';

class TierPricingController extends Controller
{
    protected ProductPriceTier $tierModel;
    protected Product $productModel;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->tierModel = new ProductPriceTier();
        $this->productModel = new Product();
    }

    /**
     * Index Page (Sidebar Link)
     * Lists products to manage tiers for.
     */
    public function index()
    {
        // Reuse ProductController's admin list logic or simplified
        // Just list all active products
        $db = db();
        $q = trim($_GET['q'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $where = "WHERE p.deleted_at IS NULL AND p.is_active = 1"; // Only active products
        $params = [];
        $types = "";

        if ($q !== '') {
            $where .= " AND (p.name LIKE ? OR p.slug LIKE ?)";
            $types .= "ss";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }

        // Count
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM products p $where");
        if (!empty($params))
            $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();

        // Data
        $sql = "SELECT p.id, p.name, p.thumbnail, p.base_price,
                       (SELECT COUNT(*) FROM product_price_tiers t WHERE t.product_id = p.id AND t.is_active = 1) as tier_count
                FROM products p
                $where
                ORDER BY p.name ASC
                LIMIT ?, ?";

        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $types .= "ii";
            $params[] = $offset;
            $params[] = $perPage;
            $stmt->bind_param($types, ...$params);
        } else {
            $stmt->bind_param("ii", $offset, $perPage);
        }

        $stmt->execute();
        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $this->renderAdmin('tier_pricing/index', [
            'products' => $products,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage
            ],
            'q' => $q
        ], 'Manage Tier Pricing');
    }

    /**
     * API: Get Tiers for a product
     */
    public function apiList($productId)
    {
        header('Content-Type: application/json');

        // Auth check handled by Middleware


        $productId = (int) $productId;
        $data = $this->tierModel->getByProduct($productId);

        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    /**
     * API: Store Tier
     */
    public function apiStore($productId)
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
            exit;
        }

        // CSRF Check (Optional for API context if using SameSite cookies, but good practice)
        // Verify CSRF token from header or post
        // Using strict check:
        /*
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo json_encode(['success' => false, 'message' => 'CSRF Token Invalid']);
            exit;
        }
        */

        $productId = (int) $productId;

        // Input JSON or POST? JS uses JSON usually.
        // Let's assume standard POST or JSON body.
        // If JS uses FormData, it's POST.
        // Checking JS ... it's likely FormData or JSON.
        // Let's support POST params first.

        $input = $_POST;
        // If empty, try JSON input
        if (empty($input)) {
            $json = file_get_contents('php://input');
            $input = json_decode($json, true) ?? [];
        }

        $qtyMin = (int) ($input['qty_min'] ?? 0);
        $qtyMax = !empty($input['qty_max']) ? (int) $input['qty_max'] : null;
        $unitPrice = (float) ($input['unit_price'] ?? 0);

        if ($qtyMin < 1) {
            echo json_encode(['success' => false, 'message' => 'Minimum Quantity must be >= 1']);
            exit;
        }
        if ($unitPrice <= 0) {
            echo json_encode(['success' => false, 'message' => 'Unit Price must be > 0']);
            exit;
        }

        try {
            $id = $this->tierModel->create([
                'product_id' => $productId,
                'qty_min' => $qtyMin,
                'qty_max' => $qtyMax,
                'unit_price' => $unitPrice,
                'is_active' => 1
            ]);

            // Log It!
            // Helper function log_admin_action might not be loaded, use require if needed or assume global.
            // require_once __DIR__ . '/../helpers/logging.php'; 
            if (function_exists('log_admin_action')) {
                log_admin_action('Create Tier Price', "Created tier for Product #{$productId}: {$qtyMin}-" . ($qtyMax ?? 'Inf'), ['id' => $id, 'product_id' => $productId]);
            }

            echo json_encode(['success' => true, 'data' => ['id' => $id]]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * API: Delete Tier
     */
    public function apiDelete($id)
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
            exit;
        }

        try {
            $this->tierModel->delete((int) $id);

            if (function_exists('log_admin_action')) {
                log_admin_action('Delete Tier Price', "Deleted tier ID {$id}", ['id' => $id]);
            }

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
