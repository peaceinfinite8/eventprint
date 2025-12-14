<?php
// app/controllers/SettingsController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Setting.php';
require_once __DIR__ . '/../helpers/Security.php';

class SettingsController extends Controller
{
    protected Setting $setting;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->setting = new Setting();
    }

    public function index()
    {
        $settings = $this->setting->getAll();

        $this->renderAdmin('settings/index', [
            'title'    => 'General Settings',
            'settings' => $settings,
        ], 'General Settings');
    }

    public function update()
    {
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $this->redirectWithError(
                $this->baseUrl('admin/settings'),
                $e->getMessage()
            );
        }

        $current = $this->setting->getAll();

        $data = [
            'site_name'    => trim($_POST['site_name'] ?? ''),
            'site_tagline' => trim($_POST['site_tagline'] ?? ''),
            'phone'        => trim($_POST['phone'] ?? ''),
            'email'        => trim($_POST['email'] ?? ''),
            'address'      => trim($_POST['address'] ?? ''),
            'facebook'     => trim($_POST['facebook'] ?? ''),
            'instagram'    => trim($_POST['instagram'] ?? ''),
            'whatsapp'     => trim($_POST['whatsapp'] ?? ''),
        ];

        if ($data['site_name'] === '') {
            $this->redirectWithError(
                $this->baseUrl('admin/settings'),
                'Nama website tidak boleh kosong.'
            );
        }

        // ===== Upload Logo =====
        if (!empty($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $tmpPath  = $_FILES['logo']['tmp_name'];
            $fileName = $_FILES['logo']['name'];
            $fileSize = (int)$_FILES['logo']['size'];

            $maxSize = 2 * 1024 * 1024; // 2MB
            if ($fileSize > $maxSize) {
                $this->redirectWithError($this->baseUrl('admin/settings'), 'Ukuran logo maksimal 2MB.');
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $tmpPath);
            finfo_close($finfo);

            $allowed = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'];
            if (!in_array($mime, $allowed, true)) {
                $this->redirectWithError($this->baseUrl('admin/settings'), 'File logo harus gambar (JPG/PNG/GIF/WEBP).');
            }

            $uploadDir = __DIR__ . '/../../public/uploads/settings';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $safeBase = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($fileName, PATHINFO_FILENAME));
            $newName  = 'logo_' . $safeBase . '_' . time() . '.' . $ext;

            $destPath = $uploadDir . '/' . $newName;

            if (!move_uploaded_file($tmpPath, $destPath)) {
                $this->redirectWithError($this->baseUrl('admin/settings'), 'Gagal mengunggah logo.');
            }

            // hapus logo lama (kalau ada)
            if (!empty($current['logo'])) {
                $oldAbs = __DIR__ . '/../../public/' . ltrim($current['logo'], '/');
                if (is_file($oldAbs)) {
                    @unlink($oldAbs);
                }
            }

            $data['logo'] = 'uploads/settings/' . $newName;
        }

        $this->setting->saveAll($data);

        $this->redirectWithSuccess(
            $this->baseUrl('admin/settings'),
            'Settings berhasil diperbarui.'
        );
    }
}
