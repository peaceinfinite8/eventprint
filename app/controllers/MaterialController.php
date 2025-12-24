<?php
// app/controllers/MaterialController.php

require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Validation.php';
require_once __DIR__ . '/../helpers/logging.php';

class MaterialController extends Controller
{
    protected $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
    }

    protected function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-') ?: uniqid('mat-');
    }

    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = $this->slugify($name);
        $slug = $base;
        $i = 2;

        while (true) {
            $sql = "SELECT id FROM materials WHERE slug = ?";
            if ($ignoreId) {
                $sql .= " AND id != $ignoreId";
            }
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('s', $slug);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows === 0) {
                break;
            }
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    /* ===================== INDEX ===================== */
    public function index()
    {
        $materials = [];
        $result = $this->db->query("
            SELECT m.*, 
                   (SELECT COUNT(*) FROM category_materials cm WHERE cm.material_id = m.id) as category_count
            FROM materials m 
            ORDER BY m.sort_order ASC, m.name ASC
        ");
        while ($row = $result->fetch_assoc()) {
            $materials[] = $row;
        }

        $this->renderAdmin('materials/index', [
            'materials' => $materials,
        ], 'Kelola Bahan (Materials)');
    }

    /* ===================== CREATE ===================== */
    public function create()
    {
        // Get next sort order
        $result = $this->db->query("SELECT MAX(sort_order) + 1 as next FROM materials");
        $row = $result->fetch_assoc();
        $nextSort = $row['next'] ?? 1;

        $this->renderAdmin('materials/create', [
            'nextSortOrder' => $nextSort,
        ], 'Tambah Bahan');
    }

    public function store()
    {
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF token tidak valid.";
            return;
        }

        $rules = [
            'name' => 'required|min:2|max:100',
            'price_delta' => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];

        $input = Validation::validateOrRedirect(
            $_POST,
            $rules,
            $this->baseUrl('admin/materials/create')
        );

        $name = $input['name'];
        $slug = $this->generateUniqueSlug($name);
        $priceDelta = (float) ($input['price_delta'] ?? 0);
        $sortOrder = (int) ($input['sort_order'] ?? 0);
        $isActive = !empty($input['is_active']) ? 1 : 0;

        $stmt = $this->db->prepare("
            INSERT INTO materials (name, slug, price_delta, sort_order, is_active)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('ssdii', $name, $slug, $priceDelta, $sortOrder, $isActive);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            log_admin_action('CREATE', "Menambah bahan: $name", ['entity' => 'material', 'name' => $name]);
            $_SESSION['flash_success'] = 'Bahan berhasil ditambahkan.';
            header('Location: ' . $this->baseUrl('admin/materials'));
        } else {
            $_SESSION['flash_error'] = 'Gagal menyimpan bahan.';
            header('Location: ' . $this->baseUrl('admin/materials/create'));
        }
        exit;
    }

    /* ===================== EDIT ===================== */
    public function edit($id)
    {
        $id = (int) $id;
        $stmt = $this->db->prepare("SELECT * FROM materials WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $material = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$material) {
            http_response_code(404);
            echo "Material not found";
            return;
        }

        $this->renderAdmin('materials/edit', [
            'material' => $material,
        ], 'Edit Bahan');
    }

    public function update($id)
    {
        $id = (int) $id;

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF token tidak valid.";
            return;
        }

        $rules = [
            'name' => 'required|min:2|max:100',
            'price_delta' => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];

        $input = Validation::validateOrRedirect(
            $_POST,
            $rules,
            $this->baseUrl('admin/materials/edit/' . $id)
        );

        $name = $input['name'];
        $slug = $this->generateUniqueSlug($name, $id);
        $priceDelta = (float) ($input['price_delta'] ?? 0);
        $sortOrder = (int) ($input['sort_order'] ?? 0);
        $isActive = !empty($input['is_active']) ? 1 : 0;

        $stmt = $this->db->prepare("
            UPDATE materials 
            SET name = ?, slug = ?, price_delta = ?, sort_order = ?, is_active = ?
            WHERE id = ?
        ");
        $stmt->bind_param('ssdiii', $name, $slug, $priceDelta, $sortOrder, $isActive, $id);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            log_admin_action('UPDATE', "Mengubah bahan: $name", ['entity' => 'material', 'id' => $id, 'name' => $name]);
            $_SESSION['flash_success'] = 'Bahan berhasil diperbarui.';
            header('Location: ' . $this->baseUrl('admin/materials'));
        } else {
            $_SESSION['flash_error'] = 'Gagal memperbarui bahan.';
            header('Location: ' . $this->baseUrl('admin/materials/edit/' . $id));
        }
        exit;
    }

    /* ===================== DELETE ===================== */
    public function delete($id)
    {
        $id = (int) $id;

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF token tidak valid.";
            return;
        }

        // Check if material is used in mappings
        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM category_materials WHERE material_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($row['cnt'] > 0) {
            $_SESSION['flash_error'] = "Tidak bisa menghapus. Bahan masih digunakan oleh {$row['cnt']} kategori.";
            header('Location: ' . $this->baseUrl('admin/materials'));
            exit;
        }

        $stmt = $this->db->prepare("SELECT name FROM materials WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $mat = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $matName = $mat['name'] ?? 'Unknown';

        $stmt = $this->db->prepare("DELETE FROM materials WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();

        log_admin_action('DELETE', "Menghapus bahan: $matName", ['entity' => 'material', 'id' => $id, 'name' => $matName]);

        $_SESSION['flash_success'] = 'Bahan berhasil dihapus.';
        header('Location: ' . $this->baseUrl('admin/materials'));
        exit;
    }
}
