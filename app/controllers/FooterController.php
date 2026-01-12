<?php
// app/controllers/FooterController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/logging.php';

class FooterController extends Controller
{
    protected mysqli $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
    }

    public function index()
    {
        // Fetch footer contents
        $content = [];
        $res = $this->db->query("
            SELECT field, value 
            FROM page_contents 
            WHERE page_slug='footer' AND section='main'
        ");

        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $content[$r['field']] = $r['value'];
            }
        }

        // Fetch Categories for Dropdown
        // Use Model Pattern if possible, or direct query matching the Model
        // Model doesn't use Soft Deletes (deleted_at), so we remove that check.
        $categories = [];
        $catRes = $this->db->query("SELECT id, name, slug FROM product_categories ORDER BY sort_order ASC");
        if ($catRes) {
            while ($c = $catRes->fetch_assoc()) {
                $categories[] = $c;
            }
        }

        $this->renderAdmin('footer/index', [
            'content' => $content,
            'categories' => $categories,
            'title' => 'Manage Footer'
        ], 'Manage Footer');
    }

    public function update()
    {
        try {
            Security::requireCsrfToken();

            // 1. Copyright Text
            $copyright = trim($_POST['copyright'] ?? '');
            $this->saveContent('copyright', $copyright);

            // 2. Product Links (JSON)
            $productLinks = [];
            $pNames = $_POST['product_names'] ?? [];
            $pUrls = $_POST['product_urls'] ?? [];

            if (is_array($pNames)) {
                foreach ($pNames as $i => $name) {
                    $name = trim($name);
                    $url = trim($pUrls[$i] ?? '');
                    if ($name !== '') {
                        $productLinks[] = ['label' => $name, 'url' => $url];
                    }
                }
            }
            $this->saveContent('product_links', json_encode($productLinks));

            // 3. Payment Methods (JSON with Images)
            $paymentMethods = [];
            $payLabels = $_POST['payment_labels'] ?? [];
            $payOldImages = $_POST['payment_old_images'] ?? [];

            // Debug Logging
            $errors = [];

            if (is_array($payLabels)) {
                foreach ($payLabels as $i => $label) {
                    $label = trim($label);
                    $imagePath = $payOldImages[$i] ?? '';

                    // Check for new upload at this index
                    if (!empty($_FILES['payment_images']['name'][$i])) {
                        $file = [
                            'name' => $_FILES['payment_images']['name'][$i],
                            'type' => $_FILES['payment_images']['type'][$i],
                            'tmp_name' => $_FILES['payment_images']['tmp_name'][$i],
                            'error' => $_FILES['payment_images']['error'][$i],
                            'size' => $_FILES['payment_images']['size'][$i],
                        ];

                        if ($file['error'] === UPLOAD_ERR_OK) {
                            try {
                                $uploadRes = $this->uploadPaymentIcon($file);
                                if ($uploadRes) {
                                    // Delete old image if it existed and is local
                                    if ($imagePath && file_exists(__DIR__ . '/../../' . $imagePath)) {
                                        @unlink(__DIR__ . '/../../' . $imagePath);
                                    }
                                    $imagePath = $uploadRes;
                                }
                            } catch (Exception $uploadErr) {
                                $errors[] = "Error uploading {$label}: " . $uploadErr->getMessage();
                                error_log("Upload Error for $label: " . $uploadErr->getMessage());
                            }
                        } else {
                            $errors[] = "Upload failed for {$label} with error code: " . $file['error'];
                        }
                    }

                    if ($label !== '' || $imagePath !== '') {
                        $paymentMethods[] = ['label' => $label, 'image' => $imagePath];
                    }
                }
            }
            $this->saveContent('payment_methods', json_encode($paymentMethods));

            log_admin_action('UPDATE', 'Updated footer settings', ['page' => 'footer']);

            if (!empty($errors)) {
                $this->redirectWithError($this->baseUrl('admin/footer'), 'Some uploads failed: ' . implode(', ', $errors));
            } else {
                $this->redirectWithSuccess($this->baseUrl('admin/footer'), 'Footer content updated successfully.');
            }

        } catch (Exception $e) {
            $this->redirectWithError($this->baseUrl('admin/footer'), $e->getMessage());
        }
    }

    private function getContent($field)
    {
        $field = $this->db->real_escape_string($field);
        $res = $this->db->query("SELECT value FROM page_contents WHERE page_slug='footer' AND section='main' AND field='$field' LIMIT 1");
        if ($res && $row = $res->fetch_assoc()) {
            return $row['value'];
        }
        return '';
    }

    private function saveContent($field, $value)
    {
        $field = $this->db->real_escape_string($field);
        $value = $this->db->real_escape_string($value);

        $check = $this->db->query("SELECT id FROM page_contents WHERE page_slug='footer' AND section='main' AND field='$field'");

        if ($check && $check->num_rows > 0) {
            $this->db->query("UPDATE page_contents SET value='$value', updated_at=NOW() WHERE page_slug='footer' AND section='main' AND field='$field'");
        } else {
            $this->db->query("INSERT INTO page_contents (page_slug, section, item_key, field, value, created_at, updated_at) VALUES ('footer', 'main', '0', '$field', '$value', NOW(), NOW())");
        }
    }

    private function uploadPaymentIcon($file)
    {
        $allowed = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp', 'image/svg+xml'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
            throw new Exception("Invalid file type: $mime. Allowed: " . implode(', ', $allowed));
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = 'payment_' . time() . '_' . rand(100, 999) . '.' . $ext;
        // CORRECTED: Upload to root/uploads/payment, not public/uploads/payment
        $targetDir = __DIR__ . '/../../uploads/payment';

        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);

        $targetPath = $targetDir . '/' . $name;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'uploads/payment/' . $name;
        }

        throw new Exception("Failed to move uploaded file.");
    }
}
            return 'uploads/payment/' . $name;
        }
        return false;
    }
}
