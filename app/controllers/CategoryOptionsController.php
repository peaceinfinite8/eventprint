<?php
// app/controllers/CategoryOptionsController.php

require_once __DIR__ . '/../helpers/Security.php';

class CategoryOptionsController extends Controller
{
    protected $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
    }

    /**
     * Main page: Select a category and manage its materials/laminations
     */
    public function index()
    {
        // Get all active categories
        $categories = [];
        $result = $this->db->query("
            SELECT id, name, slug 
            FROM product_categories 
            WHERE is_active = 1 
            ORDER BY sort_order ASC, name ASC
        ");
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        // Get selected category from query string
        $selectedCategoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;
        $selectedCategory = null;
        $categoryMaterials = [];
        $categoryLaminations = [];

        if ($selectedCategoryId) {
            // Get category details
            $stmt = $this->db->prepare("SELECT * FROM product_categories WHERE id = ?");
            $stmt->bind_param('i', $selectedCategoryId);
            $stmt->execute();
            $selectedCategory = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // Get currently mapped material IDs
            $stmt = $this->db->prepare("SELECT material_id FROM category_materials WHERE category_id = ? AND is_active = 1");
            $stmt->bind_param('i', $selectedCategoryId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $categoryMaterials[] = (int) $row['material_id'];
            }
            $stmt->close();

            // Get currently mapped lamination IDs
            $stmt = $this->db->prepare("SELECT lamination_id FROM category_laminations WHERE category_id = ? AND is_active = 1");
            $stmt->bind_param('i', $selectedCategoryId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $categoryLaminations[] = (int) $row['lamination_id'];
            }
            $stmt->close();
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

        $this->renderAdmin('category_options/index', [
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId,
            'selectedCategory' => $selectedCategory,
            'categoryMaterials' => $categoryMaterials,
            'categoryLaminations' => $categoryLaminations,
            'allMaterials' => $allMaterials,
            'allLaminations' => $allLaminations,
        ], 'Mapping Opsi per Kategori');
    }

    /**
     * Save mappings for a category
     */
    public function save()
    {
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF token tidak valid.";
            return;
        }

        $categoryId = (int) ($_POST['category_id'] ?? 0);
        if (!$categoryId) {
            $_SESSION['flash_error'] = 'Kategori tidak valid.';
            header('Location: ' . $this->baseUrl('admin/category-options'));
            exit;
        }

        // Get selected materials and laminations from form
        $selectedMaterials = isset($_POST['materials']) ? array_map('intval', $_POST['materials']) : [];
        $selectedLaminations = isset($_POST['laminations']) ? array_map('intval', $_POST['laminations']) : [];

        // Begin transaction
        $this->db->begin_transaction();

        try {
            // Delete existing mappings for this category
            $stmt = $this->db->prepare("DELETE FROM category_materials WHERE category_id = ?");
            $stmt->bind_param('i', $categoryId);
            $stmt->execute();
            $stmt->close();

            $stmt = $this->db->prepare("DELETE FROM category_laminations WHERE category_id = ?");
            $stmt->bind_param('i', $categoryId);
            $stmt->execute();
            $stmt->close();

            // Insert new material mappings
            if (!empty($selectedMaterials)) {
                $stmt = $this->db->prepare("INSERT INTO category_materials (category_id, material_id, is_active) VALUES (?, ?, 1)");
                foreach ($selectedMaterials as $materialId) {
                    $stmt->bind_param('ii', $categoryId, $materialId);
                    $stmt->execute();
                }
                $stmt->close();
            }

            // Insert new lamination mappings
            if (!empty($selectedLaminations)) {
                $stmt = $this->db->prepare("INSERT INTO category_laminations (category_id, lamination_id, is_active) VALUES (?, ?, 1)");
                foreach ($selectedLaminations as $laminationId) {
                    $stmt->bind_param('ii', $categoryId, $laminationId);
                    $stmt->execute();
                }
                $stmt->close();
            }

            $this->db->commit();

            $this->db->commit();

            $msg = 'Mapping berhasil disimpan. ' . count($selectedMaterials) . ' bahan, ' . count($selectedLaminations) . ' laminasi.';
            $this->redirectWithSuccess('admin/category-options?category=' . $categoryId, $msg);

        } catch (Exception $e) {
            $this->db->rollback();
            $this->redirectWithError('admin/category-options?category=' . $categoryId, 'Gagal menyimpan mapping: ' . $e->getMessage());
        }
    }

    /**
     * Quick copy mappings from one category to another
     */
    public function copy()
    {
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF token tidak valid.";
            return;
        }

        $fromCategoryId = (int) ($_POST['from_category'] ?? 0);
        $toCategoryId = (int) ($_POST['to_category'] ?? 0);

        if (!$fromCategoryId || !$toCategoryId || $fromCategoryId === $toCategoryId) {
            $_SESSION['flash_error'] = 'Pilih kategori sumber dan tujuan yang berbeda.';
            header('Location: ' . $this->baseUrl('admin/category-options'));
            exit;
        }

        $this->db->begin_transaction();

        try {
            // Copy materials
            $this->db->query("DELETE FROM category_materials WHERE category_id = $toCategoryId");
            $this->db->query("
                INSERT INTO category_materials (category_id, material_id, price_delta_override, is_active)
                SELECT $toCategoryId, material_id, price_delta_override, is_active
                FROM category_materials
                WHERE category_id = $fromCategoryId
            ");

            // Copy laminations
            $this->db->query("DELETE FROM category_laminations WHERE category_id = $toCategoryId");
            $this->db->query("
                INSERT INTO category_laminations (category_id, lamination_id, price_delta_override, is_active)
                SELECT $toCategoryId, lamination_id, price_delta_override, is_active
                FROM category_laminations
                WHERE category_id = $fromCategoryId
            ");

            $this->db->commit();

            $this->db->commit();

            $msg = 'Mapping berhasil dicopy ke kategori tujuan.';
            $this->redirectWithSuccess('admin/category-options?category=' . $toCategoryId, $msg);

        } catch (Exception $e) {
            $this->db->rollback();
            $this->redirectWithError('admin/category-options', 'Gagal mencopy mapping: ' . $e->getMessage());
        }
    }
}
