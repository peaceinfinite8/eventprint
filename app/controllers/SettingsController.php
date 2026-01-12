<?php
// app/controllers/SettingsController.php

require_once __DIR__ . '/../core/controller.php';
require_once __DIR__ . '/../models/Setting.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/logging.php';

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
            'title' => 'General Settings',
            'settings' => $settings,
        ], 'General Settings');
    }

    public function update()
    {
        // Enforce CSRF token validation.
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $this->redirectWithError($this->baseUrl('admin/settings'), $e->getMessage());
        }

        // Load current settings for safe replacement (e.g., old logo cleanup).
        $current = $this->setting->getAll();

        // Collect and normalize input fields.
        $data = [
            'site_name' => trim($_POST['site_name'] ?? ''),
            'site_tagline' => trim($_POST['site_tagline'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'maps_link' => trim($_POST['maps_link'] ?? ''),
            'facebook' => trim($_POST['facebook'] ?? ''),
            'instagram' => trim($_POST['instagram'] ?? ''),
            'tiktok' => trim($_POST['tiktok'] ?? ''),
            'twitter' => trim($_POST['twitter'] ?? ''),
            'youtube' => trim($_POST['youtube'] ?? ''),
            'linkedin' => trim($_POST['linkedin'] ?? ''),
            'whatsapp' => trim($_POST['whatsapp'] ?? ''),
            'gmaps_embed' => trim($_POST['gmaps_embed'] ?? ''),
            'operating_hours' => trim($_POST['operating_hours'] ?? ''),
        ];

        // Basic validation.
        if ($data['site_name'] === '') {
            $this->redirectWithError($this->baseUrl('admin/settings'), 'Nama website tidak boleh kosong.');
        }

        // Validate Tagline (Max 25 Words)
        if (str_word_count($data['site_tagline']) > 25) {
            $this->redirectWithError($this->baseUrl('admin/settings'), 'Tagline tidak boleh lebih dari 25 kata.');
        }

        // Build sales contacts JSON payload.
        $contactsRaw = $_POST['sales_contacts'] ?? [];
        $contacts = [];

        if (isset($contactsRaw['name']) && is_array($contactsRaw['name'])) {
            $count = count($contactsRaw['name']);
            for ($i = 0; $i < $count; $i++) {
                $name = trim($contactsRaw['name'][$i] ?? '');
                $number = trim($contactsRaw['number'][$i] ?? '');
                if ($name !== '' && $number !== '') {
                    $contacts[] = ['name' => $name, 'number' => $number];
                }
            }
        }

        $data['sales_contacts'] = !empty($contacts) ? json_encode($contacts) : null;

        // Handle optional logo upload.
        if (isset($_FILES['logo']) && is_array($_FILES['logo'])) {
            $err = (int) ($_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE);

            // Skip when no file is provided.
            if ($err !== UPLOAD_ERR_NO_FILE) {

                // Translate PHP upload errors to user-friendly messages.
                $uploadErrMsg = match ($err) {
                    UPLOAD_ERR_OK => null,
                    UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Ukuran logo melebihi batas (maks 2MB).',
                    UPLOAD_ERR_PARTIAL => 'Upload logo tidak lengkap (partial).',
                    UPLOAD_ERR_NO_TMP_DIR => 'Server error: folder temporary tidak ada.',
                    UPLOAD_ERR_CANT_WRITE => 'Server error: gagal menulis file ke disk.',
                    UPLOAD_ERR_EXTENSION => 'Upload diblokir oleh ekstensi server.',
                    default => 'Upload logo gagal (error code: ' . $err . ').'
                };
                if ($uploadErrMsg !== null) {
                    $this->redirectWithError($this->baseUrl('admin/settings'), $uploadErrMsg);
                }

                $tmpPath = (string) ($_FILES['logo']['tmp_name'] ?? '');
                $fileSize = (int) ($_FILES['logo']['size'] ?? 0);

                // Ensure the file is a genuine HTTP upload.
                if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
                    $this->redirectWithError($this->baseUrl('admin/settings'), 'Upload tidak valid.');
                }

                // Enforce file size constraint.
                $maxSize = 2 * 1024 * 1024;
                if ($fileSize <= 0 || $fileSize > $maxSize) {
                    $this->redirectWithError($this->baseUrl('admin/settings'), 'Ukuran logo maksimal 2MB.');
                }

                // Validate MIME type using finfo.
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = (string) $finfo->file($tmpPath);

                $allowedMimeToExt = [
                    'image/png' => 'png',
                    'image/jpeg' => 'jpg',
                    'image/webp' => 'webp',
                ];
                if (!isset($allowedMimeToExt[$mime])) {
                    $this->redirectWithError($this->baseUrl('admin/settings'), 'File logo harus PNG / JPG / WEBP.');
                }

                // Validate that the payload is a real image.
                $imgInfo = @getimagesize($tmpPath);
                if ($imgInfo === false || empty($imgInfo[0]) || empty($imgInfo[1])) {
                    $this->redirectWithError($this->baseUrl('admin/settings'), 'File logo tidak terdeteksi sebagai gambar yang valid.');
                }

                // Resolve the web root directory.
                $docRoot = rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''), '/\\');
                if ($docRoot === '') {
                    $this->redirectWithError($this->baseUrl('admin/settings'), 'Gagal menentukan document root server.');
                }

                // Prepare upload target directory.
                $uploadDirRel = 'uploads/settings';
                $uploadDirAbs = $docRoot . '/' . $uploadDirRel;

                if (!is_dir($uploadDirAbs)) {
                    if (!@mkdir($uploadDirAbs, 0755, true) && !is_dir($uploadDirAbs)) {
                        $this->redirectWithError($this->baseUrl('admin/settings'), 'Gagal membuat folder uploads/settings.');
                    }
                }
                if (!is_writable($uploadDirAbs)) {
                    $this->redirectWithError($this->baseUrl('admin/settings'), 'Folder uploads/settings tidak writable.');
                }

                // Write a defensive .htaccess inside the upload directory (best effort).
                $htaccessPath = $uploadDirAbs . '/.htaccess';
                if (!is_file($htaccessPath)) {
                    @file_put_contents($htaccessPath, implode("\n", [
                        "Options -Indexes",
                        "<IfModule mod_php.c>",
                        "  php_flag engine off",
                        "</IfModule>",
                        "<IfModule mod_php7.c>",
                        "  php_flag engine off",
                        "</IfModule>",
                        "<FilesMatch \"\\.(php|phtml|php3|php4|php5|phar)$\">",
                        "  Deny from all",
                        "</FilesMatch>",
                        "",
                    ]));
                }

                // Generate an unpredictable filename.
                $ext = $allowedMimeToExt[$mime];
                $rand = bin2hex(random_bytes(16));
                $newName = 'logo_' . $rand . '.' . $ext;

                $destAbs = $uploadDirAbs . '/' . $newName;

                // Move the uploaded file to the public uploads directory.
                if (!move_uploaded_file($tmpPath, $destAbs)) {
                    $this->redirectWithError($this->baseUrl('admin/settings'), 'Gagal mengunggah logo.');
                }

                // Ensure conservative permissions.
                @chmod($destAbs, 0644);

                // Delete previous logo file (only within the expected directory).
                $oldRel = (string) ($current['logo'] ?? '');
                if ($oldRel !== '' && str_starts_with(ltrim($oldRel, '/'), $uploadDirRel . '/')) {
                    $oldAbs = $docRoot . '/' . ltrim($oldRel, '/');
                    if (is_file($oldAbs)) {
                        @unlink($oldAbs);
                    }
                }

                // Persist the relative public path.
                $data['logo'] = $uploadDirRel . '/' . $newName;
            }
        }

        // Persist all settings.
        $this->setting->saveAll($data);

        // Write audit log.
        log_admin_action('UPDATE', 'Memperbarui pengaturan umum website', ['entity' => 'settings']);

        $this->redirectWithSuccess($this->baseUrl('admin/settings'), 'Settings berhasil diperbarui.');
    }
}
