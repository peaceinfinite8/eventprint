<?php
// app/controllers/OurStoreController.php

require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Upload.php';
require_once __DIR__ . '/../models/OurStore.php';

class OurStoreController extends Controller
{
    protected OurStore $store;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->store = new OurStore();
    }

    protected function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        return $text ?: uniqid('store-');
    }

    protected function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $base = $this->slugify($base);
        $slug = $base;
        $i = 2;

        while ($this->store->slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }

    protected function v(?string $s): ?string
    {
        $s = $s !== null ? trim($s) : null;
        return ($s === '') ? null : $s;
    }

    protected function handleUpload(?array $file): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK)
            return null;
        return Upload::image($file, 'our_store'); // /public/uploads/our_store/
    }

    public function index()
    {
        $q = trim($_GET['q'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        $result = $this->store->searchWithPagination($q !== '' ? $q : null, $page, $perPage);

        $this->renderAdmin('our_store/index', [
            'items' => $result['items'],
            'filter_q' => $q,
            'pagination' => [
                'total' => $result['total'],
                'page' => $result['page'],
                'per_page' => $result['per_page'],
            ],
        ], 'Our Store');
    }

    public function create()
    {
        // client cuma punya 2 kantor
        if ($this->store->countAll() >= 2) {
            $this->redirectWithError('admin/our-home/stores', 'Client ini hanya punya 2 kantor. Hapus salah satu dulu kalau mau ganti.');
        }

        $this->renderAdmin('our_store/create', [
            'nextSortOrder' => $this->store->getNextSortOrder(),
        ], 'Tambah Store');
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $this->redirectWithError('admin/our-home/stores/create', 'CSRF token tidak valid atau sesi kadaluarsa.');
        }

        // limit 2 kantor
        if ($this->store->countAll() >= 2) {
            $this->redirectWithError('admin/our-home/stores', 'Client ini hanya punya 2 kantor. Hapus salah satu dulu kalau mau ganti.');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '')
            $this->redirectWithError('admin/our-home/stores/create', 'Nama store wajib diisi.');

        $officeType = trim($_POST['office_type'] ?? 'branch');
        if (!in_array($officeType, ['hq', 'branch'], true))
            $officeType = 'branch';

        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        if ($address === '' || $city === '') {
            $this->redirectWithError('admin/our-home/stores/create', 'Alamat dan Kota wajib diisi.');
        }

        $slugInput = trim($_POST['slug'] ?? '');
        $slug = ($slugInput === '')
            ? $this->uniqueSlug($name, null)
            : $this->uniqueSlug($slugInput, null);

        $thumb = null;
        if (!empty($_FILES['thumbnail']['name'])) {
            $thumb = $this->handleUpload($_FILES['thumbnail']);
            if ($thumb === null)
                $this->redirectWithError('admin/our-home/stores/create', 'Gagal upload thumbnail.');
        }

        $hours = trim($_POST['hours'] ?? '');

        $data = [
            'name' => $name,
            'slug' => $slug,
            'office_type' => $officeType,
            'address' => $address,
            'city' => $city,
            'phone' => $this->v($_POST['phone'] ?? null),
            'whatsapp' => $this->v($_POST['whatsapp'] ?? null),
            'gmaps_url' => $this->v($_POST['gmaps_url'] ?? null),
            'hours' => $hours ?: null,
            'thumbnail' => $thumb,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => max(1, (int) ($_POST['sort_order'] ?? 1)),
        ];

        try {
            $newId = $this->store->create($data);
            log_admin_action('Create Store', "Created new store '{$name}'", ['id' => $newId, 'name' => $name]);
        } catch (Exception $e) {
            $this->redirectWithError('admin/our-home/stores/create', 'Gagal menyimpan: ' . $e->getMessage());
        }

        $this->redirectWithSuccess('admin/our-home/stores', 'Store berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $id = (int) $id;
        $item = $this->store->find($id);
        if (!$item) {
            http_response_code(404);
            echo "Store tidak ditemukan.";
            return;
        }

        $this->renderAdmin('our_store/edit', ['item' => $item], 'Edit Store');
    }

    /**
     * Get all gallery images for a store
     */
    protected function getGalleryImages(int $storeId): array
    {
        $db = db();
        $stmt = $db->prepare("
            SELECT id, image_path, caption, sort_order
            FROM our_store_gallery
            WHERE store_id = ?
            ORDER BY sort_order ASC, id ASC
        ");
        $stmt->bind_param('i', $storeId);
        $stmt->execute();
        $result = $stmt->get_result();

        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
        $stmt->close();

        return $images;
    }

    /**
     * Upload a new gallery image for a store
     */
    public function uploadGalleryImage($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
            return;
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'CSRF token tidak valid.']);
            return;
        }

        $id = (int) $id;
        $item = $this->store->find($id);
        if (!$item) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Store tidak ditemukan.']);
            return;
        }

        // Handle file upload
        if (empty($_FILES['gallery_image']['name'])) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diupload.']);
            return;
        }

        $imagePath = $this->handleUpload($_FILES['gallery_image']);
        if ($imagePath === null) {
            echo json_encode(['success' => false, 'message' => 'Gagal upload gambar.']);
            return;
        }

        $caption = trim($_POST['caption'] ?? '');
        $sortOrder = max(1, (int) ($_POST['sort_order'] ?? 999));

        // Insert to database
        $db = db();
        $stmt = $db->prepare("
            INSERT INTO our_store_gallery (store_id, image_path, caption, sort_order)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('issi', $id, $imagePath, $caption, $sortOrder);

        if ($stmt->execute()) {
            $insertId = $stmt->insert_id;
            $stmt->close();

            echo json_encode([
                'success' => true,
                'message' => 'Gambar berhasil diupload.',
                'data' => [
                    'id' => $insertId,
                    'image_path' => $imagePath,
                    'caption' => $caption,
                    'sort_order' => $sortOrder
                ]
            ]);
        } else {
            $stmt->close();
            // Delete uploaded file if database insert fails
            $fullPath = __DIR__ . '/../../public/' . $imagePath;
            if (is_file($fullPath))
                @unlink($fullPath);

            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan ke database.']);
        }
    }

    /**
     * Delete a gallery image
     */
    public function deleteGalleryImage($galleryId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
            return;
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'CSRF token tidak valid.']);
            return;
        }

        $galleryId = (int) $galleryId;

        // Get image info
        $db = db();
        $stmt = $db->prepare("SELECT image_path FROM our_store_gallery WHERE id = ?");
        $stmt->bind_param('i', $galleryId);
        $stmt->execute();
        $result = $stmt->get_result();
        $image = $result->fetch_assoc();
        $stmt->close();

        if (!$image) {
            echo json_encode(['success' => false, 'message' => 'Gambar tidak ditemukan.']);
            return;
        }

        // Delete from database
        $stmt = $db->prepare("DELETE FROM our_store_gallery WHERE id = ?");
        $stmt->bind_param('i', $galleryId);

        if ($stmt->execute()) {
            $stmt->close();

            // Delete physical file
            if (!empty($image['image_path'])) {
                $fullPath = __DIR__ . '/../../public/' . $image['image_path'];
                if (is_file($fullPath))
                    @unlink($fullPath);
            }

            echo json_encode(['success' => true, 'message' => 'Gambar berhasil dihapus.']);
        } else {
            $stmt->close();
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus gambar.']);
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $this->redirectWithError("admin/our-home/stores/edit/$id", 'CSRF tidak valid.');
        }

        $id = (int) $id;
        $item = $this->store->find($id);
        if (!$item) {
            http_response_code(404);
            echo "Store tidak ditemukan.";
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '')
            $this->redirectWithError("admin/our-home/stores/edit/$id", 'Nama store wajib diisi.');

        $officeType = trim($_POST['office_type'] ?? ($item['office_type'] ?? 'branch'));
        if (!in_array($officeType, ['hq', 'branch'], true))
            $officeType = 'branch';

        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        if ($address === '' || $city === '') {
            $this->redirectWithError("admin/our-home/stores/edit/$id", 'Alamat dan Kota wajib diisi.');
        }

        $slugInput = trim($_POST['slug'] ?? '');
        $slug = ($slugInput === '')
            ? ($item['slug'] ?? $this->uniqueSlug($name, $id))
            : $this->uniqueSlug($slugInput, $id);

        $thumb = $item['thumbnail'] ?? null;
        if (!empty($_FILES['thumbnail']['name'])) {
            $newThumb = $this->handleUpload($_FILES['thumbnail']);
            if ($newThumb === null)
                $this->redirectWithError("admin/our-home/stores/edit/$id", 'Gagal upload thumbnail baru.');

            if (!empty($thumb)) {
                $oldPath = __DIR__ . '/../../public/' . $thumb;
                if (is_file($oldPath))
                    @unlink($oldPath);
            }
            $thumb = $newThumb;
        }

        $hours = trim($_POST['hours'] ?? '');

        $data = [
            'name' => $name,
            'slug' => $slug,
            'office_type' => $officeType,
            'address' => $address,
            'city' => $city,
            'phone' => $this->v($_POST['phone'] ?? null),
            'whatsapp' => $this->v($_POST['whatsapp'] ?? null),
            'gmaps_url' => $this->v($_POST['gmaps_url'] ?? null),
            'hours' => $hours ?: null,
            'thumbnail' => $thumb,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => max(1, (int) ($_POST['sort_order'] ?? ($item['sort_order'] ?? 1))),
        ];

        try {
            $this->store->update($id, $data);
            log_admin_action('Update Store', "Updated store ID {$id}", ['id' => $id, 'name' => $name]);
        } catch (Exception $e) {
            $this->redirectWithError("admin/our-home/stores/edit/$id", 'Gagal update: ' . $e->getMessage());
        }

        $this->redirectWithSuccess('admin/our-home/stores', 'Store berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $this->redirectWithError("admin/our-home/stores", 'CSRF tidak valid.');
        }

        $id = (int) $id;
        $item = $this->store->find($id);
        if (!$item) {
            http_response_code(404);
            echo "Store tidak ditemukan.";
            return;
        }

        if (!empty($item['thumbnail'])) {
            $oldPath = __DIR__ . '/../../public/' . $item['thumbnail'];
            if (is_file($oldPath))
                @unlink($oldPath);
        }

        try {
            $this->store->delete($id);
            log_admin_action('Delete Store', "Deleted store ID {$id}", ['id' => $id, 'name' => $item['name'] ?? '']);
        } catch (Exception $e) {
            $this->redirectWithError("admin/our-home/stores", 'Gagal hapus: ' . $e->getMessage());
        }

        $this->redirectWithSuccess('admin/our-home/stores', 'Store berhasil dihapus.');
    }

    // ==================== GALLERY MANAGEMENT ====================

    /**
     * Gallery Index - List all gallery photos
     */
    public function galleryIndex()
    {
        $db = db();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Get total count
        $res = $db->query("
            SELECT COUNT(*) as total
            FROM our_store_gallery g
            JOIN our_store s ON g.store_id = s.id
        ");
        $total = $res->fetch_assoc()['total'];

        // Get gallery items with store info
        $stmt = $db->prepare("
            SELECT g.id, g.image_path, g.caption, g.sort_order, g.created_at,
                   s.name as store_name, s.id as store_id
            FROM our_store_gallery g
            JOIN our_store s ON g.store_id = s.id
            ORDER BY g.created_at DESC, g.id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param('ii', $perPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();

        $this->renderAdmin('our_home/gallery/index', [
            'items' => $items,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
            ],
        ], 'Gallery Management');
    }

    /**
     * Gallery Create Form
     */
    public function galleryCreate()
    {
        // Get all stores for dropdown
        $stores = [];
        $db = db();
        $res = $db->query("
            SELECT id, name FROM our_store ORDER BY sort_order ASC, name ASC
        ");
        while ($row = $res->fetch_assoc()) {
            $stores[] = $row;
        }

        $this->renderAdmin('our_home/gallery/create', [
            'stores' => $stores,
        ], 'Upload Gallery Photo');
    }

    /**
     * Gallery Store - Handle upload
     */
    public function galleryStore()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $this->redirectWithError('admin/our-home/gallery/create', 'CSRF token tidak valid.');
            return;
        }

        $storeId = (int) ($_POST['store_id'] ?? 0);
        $caption = trim($_POST['caption'] ?? '');
        $sortOrder = max(1, (int) ($_POST['sort_order'] ?? 999));

        if ($storeId <= 0) {
            $this->redirectWithError('admin/our-home/gallery/create', 'Pilih store terlebih dahulu.');
            return;
        }

        // Verify store exists
        $store = $this->store->find($storeId);
        if (!$store) {
            $this->redirectWithError('admin/our-home/gallery/create', 'Store tidak ditemukan.');
            return;
        }

        // Handle file upload
        if (empty($_FILES['image']['name'])) {
            $this->redirectWithError('admin/our-home/gallery/create', 'Gambar wajib diupload.');
            return;
        }

        $imagePath = $this->handleUpload($_FILES['image']);
        if ($imagePath === null) {
            $this->redirectWithError('admin/our-home/gallery/create', 'Gagal upload gambar.');
            return;
        }

        // Insert to database
        $db = db();
        $stmt = $db->prepare("
            INSERT INTO our_store_gallery (store_id, image_path, caption, sort_order)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('issi', $storeId, $imagePath, $caption, $sortOrder);

        if (!$stmt->execute()) {
            $stmt->close();
            // Delete uploaded file
            $fullPath = __DIR__ . '/../../public/' . $imagePath;
            if (is_file($fullPath))
                @unlink($fullPath);
            $this->redirectWithError('admin/our-home/gallery/create', 'Gagal menyimpan ke database.');
            return;
        }

        $stmt->close();
        $this->redirectWithSuccess('admin/our-home/gallery', 'Gallery photo berhasil diupload.');
    }

    /**
     * Gallery Edit Form
     */
    public function galleryEdit($id)
    {
        $id = (int) $id;
        $db = db();

        // Get gallery item with store info
        $stmt = $db->prepare("
            SELECT g.*, s.name as store_name
            FROM our_store_gallery g
            JOIN our_store s ON g.store_id = s.id
            WHERE g.id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        $stmt->close();

        if (!$item) {
            http_response_code(404);
            echo "Gallery item tidak ditemukan.";
            return;
        }

        // Get all stores for dropdown
        $stores = [];
        $db = db();
        $res = $db->query("
            SELECT id, name FROM our_store ORDER BY sort_order ASC, name ASC
        ");
        while ($row = $res->fetch_assoc()) {
            $stores[] = $row;
        }

        $this->renderAdmin('our_home/gallery/edit', [
            'item' => $item,
            'stores' => $stores,
        ], 'Edit Gallery Photo');
    }

    /**
     * Gallery Update
     */
    public function galleryUpdate($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $this->redirectWithError("admin/our-home/gallery/edit/$id", 'CSRF tidak valid.');
            return;
        }

        $id = (int) $id;
        $db = db();

        // Get current item
        $stmt = $db->prepare("SELECT * FROM our_store_gallery WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        $stmt->close();

        if (!$item) {
            http_response_code(404);
            echo "Gallery item tidak ditemukan.";
            return;
        }

        $storeId = (int) ($_POST['store_id'] ?? $item['store_id']);
        $caption = trim($_POST['caption'] ?? '');
        $sortOrder = max(1, (int) ($_POST['sort_order'] ?? $item['sort_order']));

        // Handle file upload (optional)
        $imagePath = $item['image_path'];
        if (!empty($_FILES['image']['name'])) {
            $newImage = $this->handleUpload($_FILES['image']);
            if ($newImage === null) {
                $this->redirectWithError("admin/our-home/gallery/edit/$id", 'Gagal upload gambar baru.');
                return;
            }

            // Delete old image
            if (!empty($imagePath)) {
                $oldPath = __DIR__ . '/../../public/' . $imagePath;
                if (is_file($oldPath))
                    @unlink($oldPath);
            }
            $imagePath = $newImage;
        }

        // Update database
        $stmt = $db->prepare("
            UPDATE our_store_gallery
            SET store_id = ?, image_path = ?, caption = ?, sort_order = ?
            WHERE id = ?
        ");
        $stmt->bind_param('issii', $storeId, $imagePath, $caption, $sortOrder, $id);

        if (!$stmt->execute()) {
            $stmt->close();
            $this->redirectWithError("admin/our-home/gallery/edit/$id", 'Gagal update.');
            return;
        }

        $stmt->close();
        $this->redirectWithSuccess('admin/our-home/gallery', 'Gallery photo berhasil diupdate.');
    }

    /**
     * Gallery Delete
     */
    public function galleryDelete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $this->redirectWithError("admin/our-home/gallery", 'CSRF tidak valid.');
            return;
        }

        $id = (int) $id;
        $db = db();

        // Get image info
        $stmt = $db->prepare("SELECT image_path FROM our_store_gallery WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $image = $result->fetch_assoc();
        $stmt->close();

        if (!$image) {
            $this->redirectWithError("admin/our-home/gallery", 'Gallery item tidak ditemukan.');
            return;
        }

        // Delete from database
        $stmt = $db->prepare("DELETE FROM our_store_gallery WHERE id = ?");
        $stmt->bind_param('i', $id);

        if (!$stmt->execute()) {
            $stmt->close();
            $this->redirectWithError("admin/our-home/gallery", 'Gagal menghapus.');
            return;
        }

        $stmt->close();

        // Delete physical file
        if (!empty($image['image_path'])) {
            $fullPath = __DIR__ . '/../../public/' . $image['image_path'];
            if (is_file($fullPath))
                @unlink($fullPath);
        }

        $this->redirectWithSuccess('admin/our-home/gallery', 'Gallery photo berhasil dihapus.');
    }
    // ==================== CONTENT MANAGEMENT (Headers) ====================

    public function content(): void
    {
        $baseUrl = rtrim($this->config['base_url'] ?? '/eventprint', '/');
        $db = db();
        $page = 'our-home';

        // fetch fields
        $stmt = $db->prepare("SELECT field, value FROM page_contents WHERE page_slug=? AND section='our_home_content' ORDER BY field ASC");
        $stmt->bind_param('s', $page);
        $stmt->execute();
        $res = $stmt->get_result();

        $data = [];
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $data[(string) $r['field']] = (string) ($r['value'] ?? '');
            }
        }
        $stmt->close();

        // Default values
        $defaults = [
            'page_title' => 'Our Home',
            'gallery_title' => 'Galeri Mesin Produksi',
            'gallery_subtitle' => 'Lihat mesin yang kami gunakan untuk menjaga kualitas & kecepatan produksi',
        ];

        $data = array_merge($defaults, $data);

        $this->renderAdmin('our_home/content', [
            'baseUrl' => $baseUrl,
            'content' => $data,
            'csrfToken' => Security::csrfToken(),
        ], 'Edit Konten Our Home');
    }

    public function contentUpdate(): void
    {
        Security::requireCsrfToken();
        $db = db();
        $page = 'our-home';
        $section = 'our_home_content';

        $allowed = ['page_title', 'gallery_title', 'gallery_subtitle'];

        foreach ($allowed as $field) {
            $value = trim((string) ($_POST[$field] ?? ''));
            $this->upsertPageContent($db, $page, $section, $field, $value);
        }

        log_admin_action('Update Our Home Content', "Updated Our Home page content headers", []);

        $this->redirectWithSuccess('admin/our-home/content', 'Konten Our Home berhasil diupdate.');
    }

    private function upsertPageContent($db, string $page, string $section, string $field, string $value): void
    {
        // UPDATE dulu
        if ($stmt = $db->prepare("UPDATE page_contents SET value=?, updated_at=CURRENT_TIMESTAMP WHERE page_slug=? AND section=? AND field=?")) {
            $stmt->bind_param('ssss', $value, $page, $section, $field);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();

            if ($affected > 0)
                return;
        }

        // kalau belum ada row, INSERT
        if ($stmt = $db->prepare("INSERT INTO page_contents (page_slug, section, field, value) VALUES (?,?,?,?)")) {
            $stmt->bind_param('ssss', $page, $section, $field, $value);
            $stmt->execute();
            $stmt->close();
        }
    }
}
