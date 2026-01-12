<?php
// app/controllers/ProductCategoryController.php

require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Validation.php';
require_once __DIR__ . '/../helpers/logging.php';
require_once __DIR__ . '/../models/ProductCategory.php';

class ProductCategoryController extends Controller
{
    protected ProductCategory $category;
    protected $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->db = db();
        $this->category = new ProductCategory();
    }

    protected function slugifyBase(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        return $text ?: uniqid('cat-');
    }

    protected function generateUniqueSlug(string $nameOrSlug, ?int $ignoreId = null): string
    {
        $base = $this->slugifyBase($nameOrSlug);
        $slug = $base;
        $i = 2;

        while ($this->category->slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    /* ===================== INDEX ===================== */

    public function index()
    {
        $categories = $this->category->getAll();

        $this->renderAdmin('product_category/index', [
            'categories' => $categories,
        ], 'Kategori Produk');
    }

    /* ===================== CREATE ===================== */

    public function create()
    {
        $nextSortOrder = $this->category->getNextSortOrder();

        $this->renderAdmin('product_category/create', [
            'nextSortOrder' => $nextSortOrder,
        ], 'Tambah Kategori Produk');
    }

    /* ===================== HELPER: UPLOAD ICON ===================== */
    private function uploadIcon(string $inputName, string $oldPath = ''): string
    {
        if (empty($_FILES[$inputName]) || ($_FILES[$inputName]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return (string) $oldPath;
        }

        $f = $_FILES[$inputName];
        if (($f['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new Exception('Upload gagal. Kode error: ' . (int) $f['error']);
        }

        // Limit 2MB
        $max = 2 * 1024 * 1024;
        if (($f['size'] ?? 0) > $max) {
            throw new Exception('Ukuran gambar terlalu besar. Maksimal 2MB.');
        }

        $tmp = $f['tmp_name'] ?? '';
        if (!$tmp || !is_uploaded_file($tmp)) {
            throw new Exception('File upload tidak valid.');
        }

        $fi = new finfo(FILEINFO_MIME_TYPE);
        $mime = $fi->file($tmp) ?: '';
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
        ];

        if (!isset($allowed[$mime])) {
            throw new Exception('Format gambar tidak didukung. Gunakan JPG, PNG, WebP, atau SVG.');
        }

        $ext = $allowed[$mime];
        $name = 'cat_icon_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        $publicDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/eventprint';
        $targetDir = $publicDir . '/uploads/categories';

        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        $dest = $targetDir . '/' . $name;
        if (!move_uploaded_file($tmp, $dest)) {
            throw new Exception('Gagal memindahkan file icon.');
        }

        // Delete old file
        $oldPath = trim((string) $oldPath);
        if ($oldPath !== '' && !preg_match('#^https?://#i', $oldPath)) {
            $oldAbs = $publicDir . '/' . ltrim($oldPath, '/');
            if (is_file($oldAbs))
                @unlink($oldAbs);
        }

        return 'uploads/categories/' . $name;
    }

    /* ===================== STORE ===================== */

    public function store()
    {
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF token tidak valid atau sesi kadaluarsa.";
            return;
        }

        $rules = [
            'name' => 'required|min:2|max:100',
            'slug' => 'nullable|max:150',
            'description' => 'nullable|max:5000',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];

        $input = Validation::validateOrRedirect(
            $_POST,
            $rules,
            $this->baseUrl('admin/product-categories/create')
        );

        $name = $input['name'];
        $slugInput = $input['slug'] ?? '';
        $description = $input['description'] ?? null;
        $sort_order = (int) ($input['sort_order'] ?? 0);
        $is_active = !empty($input['is_active']) ? 1 : 0;
        $wa = trim($_POST['whatsapp_number'] ?? '');

        // Handle Icon Upload
        try {
            $icon = $this->uploadIcon('icon', '');
        } catch (Exception $e) {
            $this->redirectWithError('admin/product-categories/create', $e->getMessage());
        }

        $baseSlug = $slugInput !== '' ? $slugInput : $name;
        $slug = $this->generateUniqueSlug($baseSlug);

        $ok = $this->category->create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description !== '' ? $description : null,
            'sort_order' => $sort_order,
            'is_active' => $is_active,
            'whatsapp_number' => $wa !== '' ? $wa : null,
            'icon' => $icon
        ]);

        if ($ok) {
            log_admin_action('CREATE', "Menambah kategori: $name", ['entity' => 'category', 'name' => $name]);
            $this->redirectWithSuccess('admin/product-categories', 'Kategori berhasil ditambahkan.');
        } else {
            $this->redirectWithError('admin/product-categories/create', 'Gagal menyimpan kategori.');
        }
    }

    /* ===================== EDIT ===================== */

    public function edit($id)
    {
        $id = (int) $id;
        $cat = $this->category->find($id);

        if (!$cat) {
            http_response_code(404);
            echo "Category not found";
            return;
        }

        $this->renderAdmin('product_category/edit', [
            'category' => $cat,
        ], 'Edit Kategori Produk');
    }

    /* ===================== UPDATE ===================== */

    public function update($id)
    {
        $id = (int) $id;
        $cat = $this->category->find($id);

        if (!$cat) {
            http_response_code(404);
            echo "Category not found";
            return;
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF token tidak valid atau sesi kadaluarsa.";
            return;
        }

        $rules = [
            'name' => 'required|min:2|max:100',
            'slug' => 'nullable|max:150',
            'description' => 'nullable|max:5000',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];

        $input = Validation::validateOrRedirect(
            $_POST,
            $rules,
            $this->baseUrl('admin/product-categories/edit/' . $id)
        );

        $name = $input['name'];
        $slugInput = $input['slug'] ?? '';
        $description = $input['description'] ?? null;
        $sort_order = (int) ($input['sort_order'] ?? 0);
        $is_active = !empty($input['is_active']) ? 1 : 0;
        $wa = trim($_POST['whatsapp_number'] ?? '');

        // Handle Icon Upload
        try {
            $icon = $this->uploadIcon('icon', $cat['icon'] ?? '');
        } catch (Exception $e) {
            $this->redirectWithError('admin/product-categories/edit/' . $id, $e->getMessage());
        }

        if ($slugInput === '') {
            $slug = $cat['slug'];
        } else {
            $slug = $this->generateUniqueSlug($slugInput, $id);
        }

        $ok = $this->category->update($id, [
            'name' => $name,
            'slug' => $slug,
            'description' => $description !== '' ? $description : null,
            'sort_order' => $sort_order,
            'is_active' => $is_active,
            'whatsapp_number' => $wa !== '' ? $wa : null,
            'icon' => $icon
        ]);

        if ($ok) {
            log_admin_action('UPDATE', "Mengubah kategori: $name", ['entity' => 'category', 'id' => $id, 'name' => $name]);
            $this->redirectWithSuccess('admin/product-categories', 'Kategori berhasil diperbarui.');
        } else {
            $this->redirectWithError('admin/product-categories/edit/' . $id, 'Gagal memperbarui kategori.');
        }
    }

    /* ===================== DELETE ===================== */

    public function delete($id)
    {
        $id = (int) $id;
        $cat = $this->category->find($id);

        if (!$cat) {
            http_response_code(404);
            echo "Category not found";
            return;
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF token tidak valid atau sesi kadaluarsa.";
            return;
        }

        $count = $this->category->hasProducts($id);
        if ($count > 0) {
            $this->redirectWithError('admin/product-categories', "Tidak bisa menghapus kategori. Masih digunakan oleh {$count} produk.");
        }

        $catName = $cat['name'];
        $iconPath = $cat['icon'] ?? '';

        try {
            $this->category->delete($id);

            // Delete icon file if exists
            if ($iconPath !== '' && !preg_match('#^https?://#i', $iconPath)) {
                $publicDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/eventprint';
                $abs = $publicDir . '/' . ltrim($iconPath, '/');
                if (is_file($abs))
                    @unlink($abs);
            }
        } catch (Exception $e) {
            // Check for Integrity Constraint Violation
            if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                $msg = "Gagal menghapus: Kategori ini masih digunakan oleh Produk atau data lain.";
            } else {
                $msg = "Terjadi kesalahan sistem saat menghapus kategori.";
            }
            // Use native error_log if log_error generic helper is missing
            error_log("Delete Category Error: " . $e->getMessage());
            $this->redirectWithError('admin/product-categories', $msg);
        }

        log_admin_action('DELETE', "Menghapus kategori: $catName", ['entity' => 'category', 'id' => $id, 'name' => $catName]);

        $this->redirectWithSuccess('admin/product-categories', 'Kategori berhasil dihapus.');
    }
}
