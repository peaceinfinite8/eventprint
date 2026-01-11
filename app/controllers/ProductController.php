<?php
// app/controllers/ProductController.php

require_once __DIR__ . '/../core/controller.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Validation.php';
require_once __DIR__ . '/../helpers/Upload.php';
require_once __DIR__ . '/../helpers/logging.php';
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
        $i = 2;

        while ($this->product->slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }

    protected function getCategories(): array
    {
        $data = [];
        $sql = "SELECT id, name
                 FROM product_categories
                 WHERE is_active = 1
                 ORDER BY sort_order ASC, name ASC";
        $res = $this->db->query($sql);
        if ($res)
            while ($row = $res->fetch_assoc())
                $data[] = $row;
        return $data;
    }

    protected function uploadThumbnail(?array $file): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            // Log why upload was skipped
            error_log('Upload skipped: ' . ($file ? "Error code: " . ($file['error'] ?? 'unknown') : 'No file'));
            return null;
        }

        $result = Upload::image($file, 'products');

        // Log if Upload::image returned null
        if ($result === null) {
            error_log('Upload::image() returned NULL for file: ' . ($file['name'] ?? 'unknown'));
        }

        return $result;
    }

    public function adminList()
    {
        $q = trim($_GET['q'] ?? '');
        $categoryId = ($_GET['category_id'] ?? '') !== '' ? (int) $_GET['category_id'] : null;

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        $categoriesOptions = $this->getCategories();
        $result = $this->product->searchWithPagination($q !== '' ? $q : null, $categoryId, $page, $perPage);

        $this->renderAdmin('product/index', [
            'products' => $result['items'],
            'categoriesOptions' => $categoriesOptions,
            'filter_q' => $q,
            'filter_category_id' => $categoryId,
            'pagination' => [
                'total' => (int) $result['total'],
                'page' => (int) $result['page'],
                'per_page' => (int) $result['per_page'],
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
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF tidak valid.";
            return;
        }

        $rules = [
            'name' => 'required|min:3|max:150',
            'category_id' => 'nullable|integer',
            'short_description' => 'nullable|max:255',
            'description' => 'nullable',
            'base_price' => 'required|numeric|min_value:0',
            'stock' => 'required|integer|min_value:0',
        ];

        $input = Validation::validateOrRedirect($_POST, $rules, $this->baseUrl('admin/products/create'));

        // ... variable assignments remain same, just ensure they use $input ...
        $name = $input['name'];
        $category_id = ($input['category_id'] ?? '') !== '' ? (int) $input['category_id'] : null;
        $short_desc = $input['short_description'] ?? null;
        $desc = $input['description'] ?? null;
        $base_price = (float) ($input['base_price'] ?? 0);
        $stock = (int) ($input['stock'] ?? 0);
        if ($stock < 0)
            $stock = 0;

        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // NEW FIELDS
        $currency = $_POST['currency'] ?? 'IDR';
        $shopee_url = trim($_POST['shopee_url'] ?? '');
        $tokopedia_url = trim($_POST['tokopedia_url'] ?? '');
        $work_time = trim($_POST['work_time'] ?? '');
        $product_notes = trim($_POST['product_notes'] ?? '');
        $specs = trim($_POST['specs'] ?? '');
        $upload_rules = trim($_POST['upload_rules'] ?? '');

        // Discount fields
        $discount_type = $_POST['discount_type'] ?? 'none';
        $discount_value = (float) ($_POST['discount_value'] ?? 0);

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
                'category_id' => $category_id,
                'name' => $name,
                'slug' => $slug,
                'short_description' => $short_desc,
                'description' => $desc,
                'thumbnail' => $thumbnail,
                'base_price' => $base_price,
                'stock' => $stock,
                'is_featured' => $is_featured,
                'is_active' => $is_active,
                // NEW FIELDS
                'currency' => $currency,
                'shopee_url' => $shopee_url ?: null,
                'tokopedia_url' => $tokopedia_url ?: null,
                'work_time' => $work_time ?: null,
                'product_notes' => $product_notes ?: null,
                'specs' => $specs ?: null,
                'upload_rules' => $upload_rules ?: null,
                // Discount fields
                'discount_value' => $discount_value,
            ]);
        } catch (Exception $e) {
            $this->redirectWithError('admin/products/create', 'Gagal menyimpan produk: ' . $e->getMessage());
        }

        // Log activity
        log_admin_action('CREATE', "Menambah produk: $name", ['entity' => 'product', 'name' => $name]);

        $this->redirectWithSuccess('admin/products', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $id = (int) $id;
        $product = $this->product->find($id);
        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            return;
        }

        $this->renderAdmin('product/edit', [
            'product' => $product,
            'categories' => $this->getCategories(),
            'gallery' => $this->product->getGallery($id)
        ], 'Edit Produk');
    }

    public function uploadGallery($id)
    {
        $id = (int) $id;
        $product = $this->product->find($id);
        if (!$product) {
            $this->redirectWithError('admin/products', 'Produk tidak ditemukan.');
        }

        if (!empty($_FILES['gallery']['name'][0])) {
            $files = $_FILES['gallery'];
            $count = count($files['name']);
            $successCount = 0;
            $errors = [];

            for ($i = 0; $i < $count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    try {
                        // Prepare single file array for Upload helper
                        $file = [
                            'name' => $files['name'][$i],
                            'type' => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error' => $files['error'][$i],
                            'size' => $files['size'][$i]
                        ];

                        $path = Upload::image($file, 'products/gallery');
                        if ($path) {
                            $this->product->addGalleryImage($id, $path);
                            $successCount++;
                        }
                    } catch (Exception $e) {
                        $errors[] = $files['name'][$i] . ": " . $e->getMessage();
                    }
                }
            }

            if ($successCount > 0) {
                $msg = "$successCount foto berhasil diupload.";
                if (!empty($errors)) {
                    $msg .= " Gagal: " . implode(', ', $errors);
                    $this->redirectWithError('admin/products/edit/' . $id, $msg);
                } else {
                    $this->redirectWithSuccess('admin/products/edit/' . $id, $msg);
                }
            } else {
                $this->redirectWithError('admin/products/edit/' . $id, 'Gagal upload foto. ' . implode(', ', $errors));
            }
        } else {
            $this->redirectWithError('admin/products/edit/' . $id, 'Pilih foto terlebih dahulu.');
        }
    }

    public function deleteGalleryImage($imgId)
    {
        $imgId = (int) $imgId;
        $img = $this->product->getGalleryImageById($imgId);
        if ($img) {
            // Delete file
            $path = __DIR__ . '/../../public/' . $img['image_path'];
            if (is_file($path)) {
                @unlink($path);
            }

            $this->product->deleteGalleryImage($imgId);
            $this->redirectWithSuccess('admin/products/edit/' . $img['product_id'], 'Foto berhasil dihapus.');
        } else {
            $this->redirectWithError('admin/products', 'Foto tidak ditemukan.');
        }
    }

    public function update($id)
    {
        $id = (int) $id;
        $product = $this->product->find($id);
        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            return;
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF tidak valid.";
            return;
        }

        $rules = [
            'name' => 'required|min:3|max:150',
            'category_id' => 'nullable|integer',
            'short_description' => 'nullable|max:255',
            'description' => 'nullable',
            'base_price' => 'required|numeric|min_value:0',
            'stock' => 'required|integer|min_value:0',
        ];

        $input = Validation::validateOrRedirect($_POST, $rules, $this->baseUrl('admin/products/edit/' . $id));

        $name = $input['name'];
        $category_id = ($input['category_id'] ?? '') !== '' ? (int) $input['category_id'] : null;
        $short_desc = $input['short_description'] ?? null;
        $desc = $input['description'] ?? null;
        $base_price = (float) ($input['base_price'] ?? 0);
        $stock = (int) ($input['stock'] ?? 0);
        if ($stock < 0)
            $stock = 0;

        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // NEW FIELDS
        $currency = $_POST['currency'] ?? 'IDR';
        $shopee_url = trim($_POST['shopee_url'] ?? '');
        $tokopedia_url = trim($_POST['tokopedia_url'] ?? '');
        $work_time = trim($_POST['work_time'] ?? '');
        $product_notes = trim($_POST['product_notes'] ?? '');
        $specs = trim($_POST['specs'] ?? '');
        $upload_rules = trim($_POST['upload_rules'] ?? '');

        // Discount fields
        $discount_type = $_POST['discount_type'] ?? 'none';
        $discount_value = (float) ($_POST['discount_value'] ?? 0);

        $slug = $product['slug']; // keep slug
        $thumbnail = $product['thumbnail'];

        if (!empty($_FILES['thumbnail']['name'])) {
            try {
                $newThumb = $this->uploadThumbnail($_FILES['thumbnail']);
                if ($newThumb) {
                    if (!empty($product['thumbnail'])) {
                        $oldPath = __DIR__ . '/../../public/' . $product['thumbnail'];
                        if (is_file($oldPath))
                            @unlink($oldPath);
                    }
                    $thumbnail = $newThumb;
                }
            } catch (Exception $e) {
                $_SESSION['validation_errors'] = ['thumbnail' => ['Gagal upload thumbnail: ' . $e->getMessage()]];
                $_SESSION['old_input'] = $input;
                $this->redirect('admin/products/edit/' . $id);
            }
        }

        try {
            $this->product->update($id, [
                'category_id' => $category_id,
                'name' => $name,
                'slug' => $slug,
                'short_description' => $short_desc,
                'description' => $desc,
                'thumbnail' => $thumbnail,
                'base_price' => $base_price,
                'stock' => $stock,
                'is_featured' => $is_featured,
                'is_active' => $is_active,
                // NEW FIELDS  
                'currency' => $currency,
                'shopee_url' => $shopee_url ?: null,
                'tokopedia_url' => $tokopedia_url ?: null,
                'work_time' => $work_time ?: null,
                'product_notes' => $product_notes ?: null,
                'specs' => $specs ?: null,
                'upload_rules' => $upload_rules ?: null,
                // Discount fields
                'discount_type' => $discount_type,
                'discount_value' => $discount_value,
            ]);
        } catch (Exception $e) {
            $this->redirectWithError('admin/products/edit/' . $id, 'Gagal memperbarui produk: ' . $e->getMessage());
        }

        // Log activity
        log_admin_action('UPDATE', "Mengubah produk: $name", ['entity' => 'product', 'id' => $id, 'name' => $name]);

        $this->redirectWithSuccess('admin/products', 'Produk berhasil diperbarui.');
    }

    public function delete($id)
    {
        $id = (int) $id;
        $product = $this->product->find($id);
        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            return;
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF tidak valid.";
            return;
        }

        if (!empty($product['thumbnail'])) {
            $oldPath = __DIR__ . '/../../public/' . $product['thumbnail'];
            if (is_file($oldPath))
                @unlink($oldPath);
        }

        $productName = $product['name'];
        $this->product->delete($id);

        // Log activity
        log_admin_action('DELETE', "Menghapus produk: $productName", ['entity' => 'product', 'id' => $id, 'name' => $productName]);

        $this->redirectWithSuccess('admin/products', 'Produk berhasil dihapus.');
    }
}
