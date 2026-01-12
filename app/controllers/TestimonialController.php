<?php
// app/controllers/TestimonialController.php

require_once __DIR__ . '/../models/Testimonial.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Upload.php';
require_once __DIR__ . '/../helpers/logging.php';

class TestimonialController extends Controller
{
    protected Testimonial $testimonial;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->testimonial = new Testimonial();
    }

    public function index()
    {
        $items = $this->testimonial->getAll();

        $this->renderAdmin('testimonials/index', [
            'items' => $items
        ], 'Testimonials');
    }

    public function create()
    {
        $errors = $_SESSION['validation_errors'] ?? [];
        $old = $_SESSION['old_input'] ?? [];
        unset($_SESSION['validation_errors'], $_SESSION['old_input']);

        $this->renderAdmin('testimonials/create', [
            'errors' => $old,
            'old' => $old
        ], 'Tambah Testimonial');
    }

    public function store()
    {
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF token invalid";
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $rating = (int) ($_POST['rating'] ?? 5);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $sort_order = (int) ($_POST['sort_order'] ?? 0);

        $errors = [];
        if ($name === '')
            $errors['name'][] = 'Nama wajib diisi.';
        if ($message === '')
            $errors['message'][] = 'Pesan wajib diisi.';

        if (!empty($errors)) {
            $_SESSION['validation_errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ' . $this->baseUrl('admin/testimonials/create'));
            exit;
        }

        $photo = null;
        if (!empty($_FILES['photo']['name'])) {
            try {
                $photo = Upload::image($_FILES['photo'], 'testimonials');
            } catch (Exception $e) {
                $_SESSION['validation_errors'] = ['photo' => [$e->getMessage()]];
                $_SESSION['old_input'] = $_POST;
                header('Location: ' . $this->baseUrl('admin/testimonials/create'));
                exit;
            }
        }

        $this->testimonial->create([
            'name' => $name,
            'position' => $position,
            'photo' => $photo,
            'rating' => $rating,
            'message' => $message,
            'is_active' => $is_active,
            'sort_order' => $sort_order
        ]);

        log_admin_action('CREATE', "Menambah testimonial dari: $name", ['entity' => 'testimonial', 'name' => $name]);

        $this->redirectWithSuccess('admin/testimonials', 'Testimonial berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $id = (int) $id;
        $item = $this->testimonial->find($id);
        if (!$item) {
            http_response_code(404);
            echo "Testimonial not found";
            return;
        }

        $errors = $_SESSION['validation_errors'] ?? [];
        $old = $_SESSION['old_input'] ?? [];
        unset($_SESSION['validation_errors'], $_SESSION['old_input']);

        $this->renderAdmin('testimonials/edit', [
            'item' => $item,
            'errors' => $errors,
            'old' => $old
        ], 'Edit Testimonial');
    }

    public function update($id)
    {
        $id = (int) $id;
        $item = $this->testimonial->find($id);
        if (!$item) {
            http_response_code(404);
            echo "Testimonial not found";
            return;
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF token invalid";
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $rating = (int) ($_POST['rating'] ?? 5);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $sort_order = (int) ($_POST['sort_order'] ?? 0);

        $errors = [];
        if ($name === '')
            $errors['name'][] = 'Nama wajib diisi.';
        if ($message === '')
            $errors['message'][] = 'Pesan wajib diisi.';

        if (!empty($errors)) {
            $_SESSION['validation_errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            $this->redirect('admin/testimonials/edit/' . $id);
        }

        $photo = $item['photo'];
        if (!empty($_FILES['photo']['name'])) {
            try {
                $newPhoto = Upload::image($_FILES['photo'], 'testimonials');
                if ($newPhoto) {
                    // Delete old photo
                    if ($photo) {
                        $oldPath = __DIR__ . '/../../public/' . $photo;
                        if (is_file($oldPath))
                            @unlink($oldPath);
                    }
                    $photo = $newPhoto;
                }
            } catch (Exception $e) {
                $_SESSION['validation_errors'] = ['photo' => [$e->getMessage()]];
                $_SESSION['old_input'] = $_POST;
                $this->redirect('admin/testimonials/edit/' . $id);
            }
        }

        $this->testimonial->update($id, [
            'name' => $name,
            'position' => $position,
            'photo' => $photo,
            'rating' => $rating,
            'message' => $message,
            'is_active' => $is_active,
            'sort_order' => $sort_order
        ]);

        log_admin_action('UPDATE', "Mengubah testimonial dari: $name", ['entity' => 'testimonial', 'id' => $id, 'name' => $name]);

        $this->redirectWithSuccess('admin/testimonials', 'Testimonial berhasil diperbarui.');
    }

    public function delete($id)
    {
        $id = (int) $id;
        $item = $this->testimonial->find($id);
        if (!$item) {
            $this->redirectWithError('admin/testimonials', 'Testimonial tidak ditemukan.');
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $this->redirectWithError('admin/testimonials', 'CSRF token invalid.');
        }

        // Delete photo
        if ($item['photo']) {
            $oldPath = __DIR__ . '/../../public/' . $item['photo'];
            if (is_file($oldPath))
                @unlink($oldPath);
        }

        $this->testimonial->delete($id);

        log_admin_action('DELETE', "Menghapus testimonial dari: " . $item['name'], ['entity' => 'testimonial', 'id' => $id, 'name' => $item['name']]);

        $this->redirectWithSuccess('admin/testimonials', 'Testimonial berhasil dihapus.');
    }
}
