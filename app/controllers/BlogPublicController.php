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

    /* ================= INDEX (LIST ADMIN) ================= */

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

        $this->renderAdmin('blog/index', [
            'posts'      => $result['items'],
            'filter_q'   => $q,
            'pagination' => [
                'total'    => $result['total'],
                'page'     => $result['page'],
                'per_page' => $result['per_page'],
            ],
        ], 'Artikel');
    }

    /* ================= CREATE ================= */

    public function create()
    {
        $csrfToken = Security::csrfToken();

        $this->renderAdmin('blog/create', [
            'csrfToken' => $csrfToken,
        ], 'Tambah Artikel');
    }

    protected function handleUpload(?array $file): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed, true)) {
            throw new Exception("Format thumbnail tidak diperbolehkan.");
        }

        $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = 'post-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $ext;

        $uploadDir = __DIR__ . '/../../public/uploads/blog/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $dest = $uploadDir . $name;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new Exception("Gagal memindahkan file upload.");
        }

        // path relatif dari public/
        return 'uploads/blog/' . $name;
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
            http_response_code(419);
            echo "CSRF token tidak valid atau sesi kadaluarsa.";
            return;
        }

        $title   = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '' || $content === '') {
            $_SESSION['flash_error'] = 'Judul dan konten wajib diisi.';
            header('Location: ' . $this->baseUrl('admin/blog/create'));
            exit;
        }

        $thumbnailPath = null;
        if (!empty($_FILES['thumbnail']['name'])) {
            try {
                $thumbnailPath = $this->handleUpload($_FILES['thumbnail']);
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Upload thumbnail gagal: ' . $e->getMessage();
                header('Location: ' . $this->baseUrl('admin/blog/create'));
                exit;
            }
        }

        $data = [
            'title'        => $title,
            'slug'         => trim($_POST['slug'] ?? ''), // optional, model akan generate kalau kosong
            'excerpt'      => trim($_POST['excerpt'] ?? ''),
            'content'      => $content,
            'thumbnail'    => $thumbnailPath,
            'is_published' => isset($_POST['is_published']) ? 1 : 0,
        ];

        try {
            $this->post->create($data);
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Gagal menyimpan artikel: ' . $e->getMessage();
            header('Location: ' . $this->baseUrl('admin/blog/create'));
            exit;
        }

        $_SESSION['flash_success'] = 'Artikel berhasil ditambahkan.';
        header('Location: ' . $this->baseUrl('admin/blog'));
        exit;
    }

    /* ================= EDIT ================= */

    public function edit($id)
    {
        $id   = (int)$id;
        $post = $this->post->find($id);

        if (!$post) {
            http_response_code(404);
            echo "Artikel tidak ditemukan.";
            return;
        }

        $csrfToken = Security::csrfToken();

        $this->renderAdmin('blog/edit', [
            'post'      => $post,
            'csrfToken' => $csrfToken,
        ], 'Edit Artikel');
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
            http_response_code(419);
            echo "CSRF token tidak valid atau sesi kadaluarsa.";
            return;
        }

        $id      = (int)$id;
        $postOld = $this->post->find($id);
        if (!$postOld) {
            http_response_code(404);
            echo "Artikel tidak ditemukan.";
            return;
        }

        $title   = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '' || $content === '') {
            $_SESSION['flash_error'] = 'Judul dan konten wajib diisi.';
            header('Location: ' . $this->baseUrl('admin/blog/edit/' . $id));
            exit;
        }

        $thumbnailPath = $postOld['thumbnail'];

        if (!empty($_FILES['thumbnail']['name'])) {
            try {
                $newThumb = $this->handleUpload($_FILES['thumbnail']);
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Upload thumbnail gagal: ' . $e->getMessage();
                header('Location: ' . $this->baseUrl('admin/blog/edit/' . $id));
                exit;
            }

            if ($newThumb) {
                // hapus file lama
                if (!empty($postOld['thumbnail'])) {
                    $oldPath = __DIR__ . '/../../public/' . $postOld['thumbnail'];
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $thumbnailPath = $newThumb;
            }
        }

        $data = [
            'title'        => $title,
            'slug'         => trim($_POST['slug'] ?? ''),
            'excerpt'      => trim($_POST['excerpt'] ?? ''),
            'content'      => $content,
            'thumbnail'    => $thumbnailPath,
            'is_published' => isset($_POST['is_published']) ? 1 : 0,
        ];

        try {
            $this->post->update($id, $data);
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Gagal memperbarui artikel: ' . $e->getMessage();
            header('Location: ' . $this->baseUrl('admin/blog/edit/' . $id));
            exit;
        }

        $_SESSION['flash_success'] = 'Artikel berhasil diperbarui.';
        header('Location: ' . $this->baseUrl('admin/blog'));
        exit;
    }

    /* ================= DELETE ================= */

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
            http_response_code(419);
            echo "CSRF token tidak valid atau sesi kadaluarsa.";
            return;
        }

        $id   = (int)$id;
        $post = $this->post->find($id);

        if (!$post) {
            http_response_code(404);
            echo "Artikel tidak ditemukan.";
            return;
        }

        // optional: hapus thumbnail dari disk
        if (!empty($post['thumbnail'])) {
            $oldPath = __DIR__ . '/../../public/' . $post['thumbnail'];
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $this->post->delete($id);

        $_SESSION['flash_success'] = 'Artikel berhasil dihapus.';
        header('Location: ' . $this->baseUrl('admin/blog'));
        exit;
    }
}
