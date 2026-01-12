<?php
// app/controllers/ProductOptionsController2.php
// Manages per-product material and lamination options

require_once __DIR__ . '/../helpers/Security.php';

class ProductOptionsController2 extends Controller
{
    protected $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
    }

    /**
     * Show options management page for a specific product
     */
    public function edit($productId)
    {
        $productId = (int) $productId;

        // Get product details
        $stmt = $this->db->prepare("SELECT id, name, slug, category_id, options_source FROM products WHERE id = ?");
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            return;
        }

        // Get all materials
        $allMaterials = [];
        $result = $this->db->query("SELECT id, name, slug, price_delta FROM materials WHERE is_active = 1 ORDER BY sort_order ASC, name ASC");
        while ($row = $result->fetch_assoc()) {
            $allMaterials[] = $row;
        }

        // Get all laminations
        $allLaminations = [];
        $result = $this->db->query("SELECT id, name, slug, price_delta FROM laminations WHERE is_active = 1 ORDER BY sort_order ASC, name ASC");
        while ($row = $result->fetch_assoc()) {
            $allLaminations[] = $row;
        }

        // Get currently selected product materials
        $productMaterials = [];
        $stmt = $this->db->prepare("SELECT material_id, price_delta_override FROM product_materials WHERE product_id = ? AND is_active = 1");
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $productMaterials[$row['material_id']] = $row['price_delta_override'];
        }
        $stmt->close();

        // Get currently selected product laminations
        $productLaminations = [];
        $stmt = $this->db->prepare("SELECT lamination_id, price_delta_override FROM product_laminations WHERE product_id = ? AND is_active = 1");
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $productLaminations[$row['lamination_id']] = $row['price_delta_override'];
        }
        $stmt->close();

        // Get category options for reference
        $categoryMaterials = [];
        $categoryLaminations = [];
        if ($product['category_id']) {
            $stmt = $this->db->prepare("SELECT material_id FROM category_materials WHERE category_id = ? AND is_active = 1");
            $stmt->bind_param('i', $product['category_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $categoryMaterials[] = (int) $row['material_id'];
            }
            $stmt->close();

            $stmt = $this->db->prepare("SELECT lamination_id FROM category_laminations WHERE category_id = ? AND is_active = 1");
            $stmt->bind_param('i', $product['category_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $categoryLaminations[] = (int) $row['lamination_id'];
            }
            $stmt->close();
        }

        $this->renderAdmin('product_options/edit', [
            'product' => $product,
            'allMaterials' => $allMaterials,
            'allLaminations' => $allLaminations,
            'productMaterials' => $productMaterials,
            'productLaminations' => $productLaminations,
            'categoryMaterials' => $categoryMaterials,
            'categoryLaminations' => $categoryLaminations,
        ], 'Opsi Produk: ' . $product['name']);
    }

    /**
     * Save product options
     */
    public function save($productId)
    {
        $productId = (int) $productId;

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF token tidak valid.";
            return;
        }

        $optionsSource = $_POST['options_source'] ?? 'category';
        $selectedMaterials = isset($_POST['materials']) ? array_map('intval', $_POST['materials']) : [];
        $selectedLaminations = isset($_POST['laminations']) ? array_map('intval', $_POST['laminations']) : [];

        $this->db->begin_transaction();

        try {
            // Update product options_source
            $stmt = $this->db->prepare("UPDATE products SET options_source = ? WHERE id = ?");
            $stmt->bind_param('si', $optionsSource, $productId);
            $stmt->execute();
            $stmt->close();

            // Delete existing product materials
            $stmt = $this->db->prepare("DELETE FROM product_materials WHERE product_id = ?");
            $stmt->bind_param('i', $productId);
            $stmt->execute();
            $stmt->close();

            // Delete existing product laminations
            $stmt = $this->db->prepare("DELETE FROM product_laminations WHERE product_id = ?");
            $stmt->bind_param('i', $productId);
            $stmt->execute();
            $stmt->close();

            // Insert new product materials
            if (!empty($selectedMaterials)) {
                $stmt = $this->db->prepare("INSERT INTO product_materials (product_id, material_id, is_active) VALUES (?, ?, 1)");
                foreach ($selectedMaterials as $materialId) {
                    $stmt->bind_param('ii', $productId, $materialId);
                    $stmt->execute();
                }
                $stmt->close();
            }

            // Insert new product laminations
            if (!empty($selectedLaminations)) {
                $stmt = $this->db->prepare("INSERT INTO product_laminations (product_id, lamination_id, is_active) VALUES (?, ?, 1)");
                foreach ($selectedLaminations as $laminationId) {
                    $stmt->bind_param('ii', $productId, $laminationId);
                    $stmt->execute();
                }
                $stmt->close();
            }

            $this->db->commit();

            $_SESSION['flash_success'] = 'Opsi produk berhasil disimpan. Mode: ' . $optionsSource;

        } catch (Exception $e) {
            $this->db->rollback();
            $_SESSION['flash_error'] = 'Gagal menyimpan opsi: ' . $e->getMessage();
        }

        header('Location: ' . $this->baseUrl('admin/products/' . $productId . '/product-options'));
        exit;
    }
}
