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
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;
        return Upload::image($file, 'our_store'); // /public/uploads/our_store/
    }

    public function index()
    {
        $q       = trim($_GET['q'] ?? '');
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        $result = $this->store->searchWithPagination($q !== '' ? $q : null, $page, $perPage);

        $this->renderAdmin('our_store/index', [
            'items'      => $result['items'],
            'filter_q'   => $q,
            'pagination' => [
                'total'    => $result['total'],
                'page'     => $result['page'],
                'per_page' => $result['per_page'],
            ],
        ], 'Our Store');
    }

    public function create()
    {
        // client cuma punya 2 kantor
        if ($this->store->countAll() >= 2) {
            $this->redirectWithError('admin/our-store', 'Client ini hanya punya 2 kantor. Hapus salah satu dulu kalau mau ganti.');
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

        try { Security::requireCsrfToken(); }
        catch (Exception $e) { $this->redirectWithError('admin/our-store/create', 'CSRF token tidak valid atau sesi kadaluarsa.'); }

        // limit 2 kantor
        if ($this->store->countAll() >= 2) {
            $this->redirectWithError('admin/our-store', 'Client ini hanya punya 2 kantor. Hapus salah satu dulu kalau mau ganti.');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') $this->redirectWithError('admin/our-store/create', 'Nama store wajib diisi.');

        $officeType = trim($_POST['office_type'] ?? 'branch');
        if (!in_array($officeType, ['hq','branch'], true)) $officeType = 'branch';

        $address = trim($_POST['address'] ?? '');
        $city    = trim($_POST['city'] ?? '');
        if ($address === '' || $city === '') {
            $this->redirectWithError('admin/our-store/create', 'Alamat dan Kota wajib diisi.');
        }

        $slugInput = trim($_POST['slug'] ?? '');
        $slug = ($slugInput === '')
            ? $this->uniqueSlug($name, null)
            : $this->uniqueSlug($slugInput, null);

        $thumb = null;
        if (!empty($_FILES['thumbnail']['name'])) {
            $thumb = $this->handleUpload($_FILES['thumbnail']);
            if ($thumb === null) $this->redirectWithError('admin/our-store/create', 'Gagal upload thumbnail.');
        }

        $data = [
            'name'        => $name,
            'slug'        => $slug,
            'office_type' => $officeType,
            'address'     => $address,
            'city'        => $city,
            'phone'       => $this->v($_POST['phone'] ?? null),
            'whatsapp'    => $this->v($_POST['whatsapp'] ?? null),
            'gmaps_url'   => $this->v($_POST['gmaps_url'] ?? null),
            'thumbnail'   => $thumb,
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
            'sort_order'  => max(1, (int)($_POST['sort_order'] ?? 1)),
        ];

        try {
            $this->store->create($data);
        } catch (Exception $e) {
            $this->redirectWithError('admin/our-store/create', 'Gagal menyimpan: ' . $e->getMessage());
        }

        $this->redirectWithSuccess('admin/our-store', 'Store berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $id   = (int)$id;
        $item = $this->store->find($id);
        if (!$item) {
            http_response_code(404);
            echo "Store tidak ditemukan.";
            return;
        }

        $this->renderAdmin('our_store/edit', ['item' => $item], 'Edit Store');
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        try { Security::requireCsrfToken(); }
        catch (Exception $e) { $this->redirectWithError("admin/our-store/edit/$id", 'CSRF tidak valid.'); }

        $id   = (int)$id;
        $item = $this->store->find($id);
        if (!$item) {
            http_response_code(404);
            echo "Store tidak ditemukan.";
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') $this->redirectWithError("admin/our-store/edit/$id", 'Nama store wajib diisi.');

        $officeType = trim($_POST['office_type'] ?? ($item['office_type'] ?? 'branch'));
        if (!in_array($officeType, ['hq','branch'], true)) $officeType = 'branch';

        $address = trim($_POST['address'] ?? '');
        $city    = trim($_POST['city'] ?? '');
        if ($address === '' || $city === '') {
            $this->redirectWithError("admin/our-store/edit/$id", 'Alamat dan Kota wajib diisi.');
        }

        $slugInput = trim($_POST['slug'] ?? '');
        $slug = ($slugInput === '')
            ? ($item['slug'] ?? $this->uniqueSlug($name, $id))
            : $this->uniqueSlug($slugInput, $id);

        $thumb = $item['thumbnail'] ?? null;
        if (!empty($_FILES['thumbnail']['name'])) {
            $newThumb = $this->handleUpload($_FILES['thumbnail']);
            if ($newThumb === null) $this->redirectWithError("admin/our-store/edit/$id", 'Gagal upload thumbnail baru.');

            if (!empty($thumb)) {
                $oldPath = __DIR__ . '/../../public/' . $thumb;
                if (is_file($oldPath)) @unlink($oldPath);
            }
            $thumb = $newThumb;
        }

        $data = [
            'name'        => $name,
            'slug'        => $slug,
            'office_type' => $officeType,
            'address'     => $address,
            'city'        => $city,
            'phone'       => $this->v($_POST['phone'] ?? null),
            'whatsapp'    => $this->v($_POST['whatsapp'] ?? null),
            'gmaps_url'   => $this->v($_POST['gmaps_url'] ?? null),
            'thumbnail'   => $thumb,
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
            'sort_order'  => max(1, (int)($_POST['sort_order'] ?? ($item['sort_order'] ?? 1))),
        ];

        try {
            $this->store->update($id, $data);
        } catch (Exception $e) {
            $this->redirectWithError("admin/our-store/edit/$id", 'Gagal update: ' . $e->getMessage());
        }

        $this->redirectWithSuccess('admin/our-store', 'Store berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        try { Security::requireCsrfToken(); }
        catch (Exception $e) { $this->redirectWithError("admin/our-store", 'CSRF tidak valid.'); }

        $id   = (int)$id;
        $item = $this->store->find($id);
        if (!$item) {
            http_response_code(404);
            echo "Store tidak ditemukan.";
            return;
        }

        if (!empty($item['thumbnail'])) {
            $oldPath = __DIR__ . '/../../public/' . $item['thumbnail'];
            if (is_file($oldPath)) @unlink($oldPath);
        }

        try {
            $this->store->delete($id);
        } catch (Exception $e) {
            $this->redirectWithError("admin/our-store", 'Gagal hapus: ' . $e->getMessage());
        }

        $this->redirectWithSuccess('admin/our-store', 'Store berhasil dihapus.');
    }
}
