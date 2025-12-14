<?php
// app/controllers/BlogController.php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../helpers/Security.php';

class BlogController extends Controller
{
    protected Post $post;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->post = new Post();
    }

    /* ================== ADMIN LIST (INDEX) ================== */

    public function index()
    {
        $q       = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page    = !empty($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;

        $result = $this->post->searchWithPagination(
            $q !== '' ? $q : null,
            $page,
            $perPage
        );

        // ambil error & old input kalau habis gagal validasi di store/update
        $errors = $_SESSION['validation_errors'] ?? [];
        $old    = $_SESSION['old_input'] ?? [];
        unset($_SESSION['validation_errors'], $_SESSION['old_input']);

        $this->renderAdmin('blog/index', [
            'posts'      => $result['items'],
            'filter_q'   => $q,
            'pagination' => [
                'total'    => $result['total'],
                'page'     => $result['page'],
                'per_page' => $result['per_page'],
            ],
            'errors' => $errors,
            'old'    => $old,
        ], 'Artikel');
    }

    /* ================== CREATE FORM ================== */

    public function create()
    {
        // ambil error & input lama dari sesi kalau habis gagal validasi
        $errors = $_SESSION['validation_errors'] ?? [];
        $old    = $_SESSION['old_input'] ?? [];
        unset($_SESSION['validation_errors'], $_SESSION['old_input']);

        $this->renderAdmin('blog/create', [
            'errors' => $errors,
            'old'    => $old,
        ], 'Tambah Artikel');
    }

    /* ================== STORE (SIMPAN BARU) ================== */

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        // CSRF
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF token tidak valid atau sesi kadaluarsa.";
            return;
        }

        $title   = trim($_POST['title'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $isPub   = isset($_POST['is_published']) ? 1 : 0;

        $errors = [];

        if ($title === '') {
            $errors['title'][] = 'Judul wajib diisi.';
        }
        if ($content === '') {
            $errors['content'][] = 'Konten wajib diisi.';
        }

        // simpan input lama untuk diisi ulang di form
        $_SESSION['old_input'] = [
            'title'        => $title,
            'excerpt'      => $excerpt,
            'content'      => $content,
            'is_published' => $isPub,
        ];

        if (!empty($errors)) {
            $_SESSION['validation_errors'] = $errors;
            header('Location: ' . $this->baseUrl('admin/blog/create'));
            exit;
        }

        // handle upload thumbnail (opsional)
        $thumbnailPath = $this->handleUpload($_FILES['thumbnail'] ?? null);

        $data = [
            'title'        => $title,
            'excerpt'      => $excerpt,
            'content'      => $content,
            'thumbnail'    => $thumbnailPath,
            'is_published' => $isPub,
        ];

        // simpan ke DB via model
        $this->post->create($data);

        // bersihkan old_input karena sudah sukses
        unset($_SESSION['old_input'], $_SESSION['validation_errors']);

        $_SESSION['flash_success'] = 'Artikel berhasil ditambahkan.';
        header('Location: ' . $this->baseUrl('admin/blog'));
        exit;
    }

    /* ================== EDIT FORM ================== */

    public function edit($id)
    {
        $id   = (int)$id;
        $post = $this->post->find($id);

        if (!$post) {
            http_response_code(404);
            echo "Artikel tidak ditemukan.";
            return;
        }

        $errors = $_SESSION['validation_errors'] ?? [];
        $old    = $_SESSION['old_input'] ?? [];
        unset($_SESSION['validation_errors'], $_SESSION['old_input']);

        // kalau ada old input (habis gagal submit), override field-nya
        if (!empty($old)) {
            $post['title']        = $old['title']        ?? $post['title'];
            $post['excerpt']      = $old['excerpt']      ?? $post['excerpt'];
            $post['content']      = $old['content']      ?? $post['content'];
            $post['is_published'] = $old['is_published'] ?? $post['is_published'];
        }

        $this->renderAdmin('blog/edit', [
            'post'   => $post,
            'errors' => $errors,
        ], 'Edit Artikel');
    }

    /* ================== UPDATE (SIMPAN EDIT) ================== */

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        $id      = (int)$id;
        $postOld = $this->post->find($id);

        if (!$postOld) {
            http_response_code(404);
            echo "Artikel tidak ditemukan.";
            return;
        }

        // CSRF
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF token tidak valid atau sesi kadaluarsa.";
            return;
        }

        $title   = trim($_POST['title'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $isPub   = isset($_POST['is_published']) ? 1 : 0;

        $errors = [];

        if ($title === '') {
            $errors['title'][] = 'Judul wajib diisi.';
        }
        if ($content === '') {
            $errors['content'][] = 'Konten wajib diisi.';
        }

        $_SESSION['old_input'] = [
            'title'        => $title,
            'excerpt'      => $excerpt,
            'content'      => $content,
            'is_published' => $isPub,
        ];

        if (!empty($errors)) {
            $_SESSION['validation_errors'] = $errors;
            header('Location: ' . $this->baseUrl('admin/blog/edit/' . $id));
            exit;
        }

        $newThumb = $this->handleUpload($_FILES['thumbnail'] ?? null);
        $thumb    = $newThumb ?: $postOld['thumbnail'];

        if ($newThumb && !empty($postOld['thumbnail'])) {
            $oldPath = __DIR__ . '/../../public/' . $postOld['thumbnail'];
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $data = [
            'title'        => $title,
            'excerpt'      => $excerpt,
            'content'      => $content,
            'thumbnail'    => $thumb,
            'is_published' => $isPub,
        ];

        $this->post->update($id, $data);

        unset($_SESSION['old_input'], $_SESSION['validation_errors']);

        $_SESSION['flash_success'] = 'Artikel berhasil diperbarui.';
        header('Location: ' . $this->baseUrl('admin/blog'));
        exit;
    }

    /* ================== DELETE ================== */

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'ID artikel tidak valid.';
            header('Location: ' . $this->baseUrl('admin/blog'));
            exit;
        }

        // CSRF (jangan echo doang, redirect biar UX bener)
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'CSRF token tidak valid atau sesi kadaluarsa.';
            header('Location: ' . $this->baseUrl('admin/blog'));
            exit;
        }

        $post = $this->post->find($id);
        if (!$post) {
            $_SESSION['flash_error'] = 'Artikel tidak ditemukan.';
            header('Location: ' . $this->baseUrl('admin/blog'));
            exit;
        }

        try {
            // hapus thumbnail fisik dulu (optional)
            if (!empty($post['thumbnail'])) {
                $oldPath = __DIR__ . '/../../public/' . $post['thumbnail'];
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $ok = $this->post->delete($id);

            if ($ok) $_SESSION['flash_success'] = 'Artikel berhasil dihapus.';
            else     $_SESSION['flash_error']   = 'Gagal hapus artikel (mungkin sudah terhapus).';

        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Gagal hapus: ' . $e->getMessage();
        }

        header('Location: ' . $this->baseUrl('admin/blog'));
        exit;
    }


    /* ================== UPLOAD HELPER ================== */

    protected function handleUpload(?array $file): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed, true)) {
            return null;
        }

        $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = 'post-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $ext;

        $uploadDir = __DIR__ . '/../../public/uploads/blog/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $dest = $uploadDir . $name;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return null;
        }

        // path relatif dari public/
        return 'uploads/blog/' . $name;
    }
}
