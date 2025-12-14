<?php
// app/controllers/ProductController.php

require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Validation.php';
require_once __DIR__ . '/../helpers/Upload.php';
require_once __DIR__ . '/../models/Product.php';

class ProductController extends Controller
{
    protected Product $product;
    protected mysqli $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
        $this->product = new Product();
    }

    protected function slugifyBase(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        return $text ?: uniqid('product-');
    }

    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = $this->slugifyBase($name);
        $slug = $base;
        $i    = 2;

        while ($this->product->slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }

    protected function getCategories(): array
    {
        $data = [];
        $sql  = "SELECT id, name
                 FROM product_categories
                 WHERE is_active = 1
                 ORDER BY sort_order ASC, name ASC";
        $res  = $this->db->query($sql);
        if ($res) while ($row = $res->fetch_assoc()) $data[] = $row;
        return $data;
    }

    protected function uploadThumbnail(?array $file): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;
        return Upload::image($file, 'products');
    }

    public function adminList()
    {
        $q          = trim($_GET['q'] ?? '');
        $categoryId = ($_GET['category_id'] ?? '') !== '' ? (int)$_GET['category_id'] : null;

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        $categoriesOptions = $this->getCategories();
        $result = $this->product->searchWithPagination($q !== '' ? $q : null, $categoryId, $page, $perPage);

        $this->renderAdmin('product/index', [
            'products'           => $result['items'],
            'categoriesOptions'  => $categoriesOptions,
            'filter_q'           => $q,
            'filter_category_id' => $categoryId,
            'pagination'         => [
                'total'    => (int)$result['total'],
                'page'     => (int)$result['page'],
                'per_page' => (int)$result['per_page'],
            ],
        ], 'All Produk');
    }


    public function create()
    {
        $this->renderAdmin('product/create', [
            'categories' => $this->getCategories(),
        ], 'Tambah Produk');
    }

    public function store()
    {
        try { Security::requireCsrfToken(); }
        catch (Exception $e) { http_response_code(419); echo "CSRF tidak valid."; return; }

        $rules = [
            'name'              => 'required|min:3|max:150',
            'category_id'       => 'nullable|integer',
            'short_description' => 'nullable|max:255',
            'description'       => 'nullable',
            'base_price'        => 'required|numeric|min_value:0',
            'stock'             => 'required|integer|min_value:0',
        ];

        $input = Validation::validateOrRedirect($_POST, $rules, $this->baseUrl('admin/products/create'));

        $name        = $input['name'];
        $category_id = ($input['category_id'] ?? '') !== '' ? (int)$input['category_id'] : null;
        $short_desc  = $input['short_description'] ?? null;
        $desc        = $input['description'] ?? null;
        $base_price  = (float)($input['base_price'] ?? 0);
        $stock       = (int)($input['stock'] ?? 0);
        if ($stock < 0) $stock = 0;

        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active   = isset($_POST['is_active']) ? 1 : 0;

        $slug = $this->generateUniqueSlug($name);

        $thumbnail = null;
        if (!empty($_FILES['thumbnail']['name'])) {
            try {
                $thumbnail = $this->uploadThumbnail($_FILES['thumbnail']);
            } catch (Exception $e) {
                $_SESSION['validation_errors'] = ['thumbnail' => ['Gagal upload thumbnail: ' . $e->getMessage()]];
                $_SESSION['old_input'] = $input;
                header('Location: ' . $this->baseUrl('admin/products/create'));
                exit;
            }
        }

        try {
            $this->product->create([
                'category_id'       => $category_id,
                'name'              => $name,
                'slug'              => $slug,
                'short_description' => $short_desc,
                'description'       => $desc,
                'thumbnail'         => $thumbnail,
                'base_price'        => $base_price,
                'stock'             => $stock,
                'is_featured'       => $is_featured,
                'is_active'         => $is_active,
            ]);
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Gagal menyimpan produk: ' . $e->getMessage();
            header('Location: ' . $this->baseUrl('admin/products/create'));
            exit;
        }

        $_SESSION['flash_success'] = 'Produk berhasil ditambahkan.';
        header('Location: ' . $this->baseUrl('admin/products'));
        exit;
    }

    public function edit($id)
    {
        $id = (int)$id;
        $product = $this->product->find($id);
        if (!$product) { http_response_code(404); echo "Product not found"; return; }

        $this->renderAdmin('product/edit', [
            'product' => $product,
            'categories' => $this->getCategories(),
        ], 'Edit Produk');
    }

    public function update($id)
    {
        $id = (int)$id;
        $product = $this->product->find($id);
        if (!$product) { http_response_code(404); echo "Product not found"; return; }

        try { Security::requireCsrfToken(); }
        catch (Exception $e) { http_response_code(419); echo "CSRF tidak valid."; return; }

        $rules = [
            'name'              => 'required|min:3|max:150',
            'category_id'       => 'nullable|integer',
            'short_description' => 'nullable|max:255',
            'description'       => 'nullable',
            'base_price'        => 'required|numeric|min_value:0',
            'stock'             => 'required|integer|min_value:0',
        ];

        $input = Validation::validateOrRedirect($_POST, $rules, $this->baseUrl('admin/products/edit/' . $id));

        $name        = $input['name'];
        $category_id = ($input['category_id'] ?? '') !== '' ? (int)$input['category_id'] : null;
        $short_desc  = $input['short_description'] ?? null;
        $desc        = $input['description'] ?? null;
        $base_price  = (float)($input['base_price'] ?? 0);
        $stock       = (int)($input['stock'] ?? 0);
        if ($stock < 0) $stock = 0;

        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active   = isset($_POST['is_active']) ? 1 : 0;

        $slug = $product['slug']; // keep slug
        $thumbnail = $product['thumbnail'];

        if (!empty($_FILES['thumbnail']['name'])) {
            try {
                $newThumb = $this->uploadThumbnail($_FILES['thumbnail']);
                if ($newThumb) {
                    if (!empty($product['thumbnail'])) {
                        $oldPath = __DIR__ . '/../../public/' . $product['thumbnail'];
                        if (is_file($oldPath)) @unlink($oldPath);
                    }
                    $thumbnail = $newThumb;
                }
            } catch (Exception $e) {
                $_SESSION['validation_errors'] = ['thumbnail' => ['Gagal upload thumbnail: ' . $e->getMessage()]];
                $_SESSION['old_input'] = $input;
                header('Location: ' . $this->baseUrl('admin/products/edit/' . $id));
                exit;
            }
        }

        try {
            $this->product->update($id, [
                'category_id'       => $category_id,
                'name'              => $name,
                'slug'              => $slug,
                'short_description' => $short_desc,
                'description'       => $desc,
                'thumbnail'         => $thumbnail,
                'base_price'        => $base_price,
                'stock'             => $stock,
                'is_featured'       => $is_featured,
                'is_active'         => $is_active,
            ]);
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Gagal memperbarui produk: ' . $e->getMessage();
            header('Location: ' . $this->baseUrl('admin/products/edit/' . $id));
            exit;
        }

        $_SESSION['flash_success'] = 'Produk berhasil diperbarui.';
        header('Location: ' . $this->baseUrl('admin/products'));
        exit;
    }

    public function delete($id)
    {
        $id = (int)$id;
        $product = $this->product->find($id);
        if (!$product) { http_response_code(404); echo "Product not found"; return; }

        try { Security::requireCsrfToken(); }
        catch (Exception $e) { http_response_code(419); echo "CSRF tidak valid."; return; }

        if (!empty($product['thumbnail'])) {
            $oldPath = __DIR__ . '/../../public/' . $product['thumbnail'];
            if (is_file($oldPath)) @unlink($oldPath);
        }

        $this->product->delete($id);

        $_SESSION['flash_success'] = 'Produk berhasil dihapus.';
        header('Location: ' . $this->baseUrl('admin/products'));
        exit;
    }
}
