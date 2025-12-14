<?php
// app/controllers/ProductCategoryController.php

require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Validation.php';
require_once __DIR__ . '/../models/ProductCategory.php';

class ProductCategoryController extends Controller
{
    protected ProductCategory $category;
    protected $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->db       = db();
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
        $i    = 2;

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
            'name'        => 'required|min:2|max:100',
            'slug'        => 'nullable|max:150',
            'description' => 'nullable|max:255',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ];

        $input = Validation::validateOrRedirect(
            $_POST,
            $rules,
            $this->baseUrl('admin/product-categories/create')
        );

        $name        = $input['name'];
        $slugInput   = $input['slug'] ?? '';
        $description = $input['description'] ?? null;
        $sort_order  = (int)($input['sort_order'] ?? 0);
        $is_active   = !empty($input['is_active']) ? 1 : 0;

        $baseSlug = $slugInput !== '' ? $slugInput : $name;
        $slug     = $this->generateUniqueSlug($baseSlug);

        $ok = $this->category->create([
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description !== '' ? $description : null,
            'sort_order'  => $sort_order,
            'is_active'   => $is_active,
        ]);

        if ($ok) {
            $_SESSION['flash_success'] = 'Kategori berhasil ditambahkan.';
            header('Location: ' . $this->baseUrl('admin/product-categories'));
        } else {
            $_SESSION['flash_error'] = 'Gagal menyimpan kategori.';
            header('Location: ' . $this->baseUrl('admin/product-categories/create'));
        }
        exit;
    }

    /* ===================== EDIT ===================== */

    public function edit($id)
    {
        $id  = (int)$id;
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

    public function update($id)
    {
        $id  = (int)$id;
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
            'name'        => 'required|min:2|max:100',
            'slug'        => 'nullable|max:150',
            'description' => 'nullable|max:255',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'nullable|boolean',
        ];

        $input = Validation::validateOrRedirect(
            $_POST,
            $rules,
            $this->baseUrl('admin/product-categories/edit/' . $id)
        );

        $name        = $input['name'];
        $slugInput   = $input['slug'] ?? '';
        $description = $input['description'] ?? null;
        $sort_order  = (int)($input['sort_order'] ?? 0);
        $is_active   = !empty($input['is_active']) ? 1 : 0;

        if ($slugInput === '') {
            $slug = $cat['slug'];
        } else {
            $slug = $this->generateUniqueSlug($slugInput, $id);
        }

        $ok = $this->category->update($id, [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description !== '' ? $description : null,
            'sort_order'  => $sort_order,
            'is_active'   => $is_active,
        ]);

        if ($ok) {
            $_SESSION['flash_success'] = 'Kategori berhasil diperbarui.';
            header('Location: ' . $this->baseUrl('admin/product-categories'));
        } else {
            $_SESSION['flash_error'] = 'Gagal memperbarui kategori.';
            header('Location: ' . $this->baseUrl('admin/product-categories/edit/' . $id));
        }
        exit;
    }

    /* ===================== DELETE ===================== */

    public function delete($id)
    {
        $id  = (int)$id;
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
            $_SESSION['flash_error'] = "Tidak bisa menghapus kategori. Masih digunakan oleh {$count} produk.";
            header('Location: ' . $this->baseUrl('admin/product-categories'));
            exit;
        }

        $this->category->delete($id);

        $_SESSION['flash_success'] = 'Kategori berhasil dihapus.';
        header('Location: ' . $this->baseUrl('admin/product-categories'));
        exit;
    }
}
