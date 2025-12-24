<?php
// app/controllers/LaminationController.php

require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Validation.php';
require_once __DIR__ . '/../helpers/logging.php';

class LaminationController extends Controller
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
        return trim($text, '-') ?: uniqid('lam-');
    }

    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = $this->slugify($name);
        $slug = $base;
        $i = 2;

        while (true) {
            $sql = "SELECT id FROM laminations WHERE slug = ?";
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
        $laminations = [];
        $result = $this->db->query("
            SELECT l.*, 
                   (SELECT COUNT(*) FROM category_laminations cl WHERE cl.lamination_id = l.id) as category_count
            FROM laminations l 
            ORDER BY l.sort_order ASC, l.name ASC
        ");
        while ($row = $result->fetch_assoc()) {
            $laminations[] = $row;
        }

        $this->renderAdmin('laminations/index', [
            'laminations' => $laminations,
        ], 'Kelola Laminasi');
    }

    /* ===================== CREATE ===================== */
    public function create()
    {
        $result = $this->db->query("SELECT MAX(sort_order) + 1 as next FROM laminations");
        $row = $result->fetch_assoc();
        $nextSort = $row['next'] ?? 1;

        $this->renderAdmin('laminations/create', [
            'nextSortOrder' => $nextSort,
        ], 'Tambah Laminasi');
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
            $this->baseUrl('admin/laminations/create')
        );

        $name = $input['name'];
        $slug = $this->generateUniqueSlug($name);
        $priceDelta = (float) ($input['price_delta'] ?? 0);
        $sortOrder = (int) ($input['sort_order'] ?? 0);
        $isActive = !empty($input['is_active']) ? 1 : 0;

        $stmt = $this->db->prepare("
            INSERT INTO laminations (name, slug, price_delta, sort_order, is_active)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('ssdii', $name, $slug, $priceDelta, $sortOrder, $isActive);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            log_admin_action('CREATE', "Menambah laminasi: $name", ['entity' => 'lamination', 'name' => $name]);
            $_SESSION['flash_success'] = 'Laminasi berhasil ditambahkan.';
            header('Location: ' . $this->baseUrl('admin/laminations'));
        } else {
            $_SESSION['flash_error'] = 'Gagal menyimpan laminasi.';
            header('Location: ' . $this->baseUrl('admin/laminations/create'));
        }
        exit;
    }

    /* ===================== EDIT ===================== */
    public function edit($id)
    {
        $id = (int) $id;
        $stmt = $this->db->prepare("SELECT * FROM laminations WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $lamination = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$lamination) {
            http_response_code(404);
            echo "Lamination not found";
            return;
        }

        $this->renderAdmin('laminations/edit', [
            'lamination' => $lamination,
        ], 'Edit Laminasi');
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
            $this->baseUrl('admin/laminations/edit/' . $id)
        );

        $name = $input['name'];
        $slug = $this->generateUniqueSlug($name, $id);
        $priceDelta = (float) ($input['price_delta'] ?? 0);
        $sortOrder = (int) ($input['sort_order'] ?? 0);
        $isActive = !empty($input['is_active']) ? 1 : 0;

        $stmt = $this->db->prepare("
            UPDATE laminations 
            SET name = ?, slug = ?, price_delta = ?, sort_order = ?, is_active = ?
            WHERE id = ?
        ");
        $stmt->bind_param('ssdiii', $name, $slug, $priceDelta, $sortOrder, $isActive, $id);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            log_admin_action('UPDATE', "Mengubah laminasi: $name", ['entity' => 'lamination', 'id' => $id, 'name' => $name]);
            $_SESSION['flash_success'] = 'Laminasi berhasil diperbarui.';
            header('Location: ' . $this->baseUrl('admin/laminations'));
        } else {
            $_SESSION['flash_error'] = 'Gagal memperbarui laminasi.';
            header('Location: ' . $this->baseUrl('admin/laminations/edit/' . $id));
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

        // Check if lamination is used in mappings
        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM category_laminations WHERE lamination_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($row['cnt'] > 0) {
            $_SESSION['flash_error'] = "Tidak bisa menghapus. Laminasi masih digunakan oleh {$row['cnt']} kategori.";
            header('Location: ' . $this->baseUrl('admin/laminations'));
            exit;
        }

        // Get name before delete
        $stmt = $this->db->prepare("SELECT name FROM laminations WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $lam = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $lamName = $lam['name'] ?? 'Unknown';

        $stmt = $this->db->prepare("DELETE FROM laminations WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();

        log_admin_action('DELETE', "Menghapus laminasi: $lamName", ['entity' => 'lamination', 'id' => $id, 'name' => $lamName]);

        $_SESSION['flash_success'] = 'Laminasi berhasil dihapus.';
        header('Location: ' . $this->baseUrl('admin/laminations'));
        exit;
    }
}
