<?php
// app/controllers/FooterController.php

require_once __DIR__ . '/../core/controller.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Upload.php';
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

        $this->renderAdmin('footer/index', [
            'content' => $content,
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
            // Expecting arrays: product_names[], product_urls[]
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
            // Existing data to preserve images if not updated
            $existingPaymentMethodsRaw = $this->getContent('payment_methods');
            $existingPaymentMethods = json_decode($existingPaymentMethodsRaw, true) ?? [];

            $paymentMethods = [];
            $payLabels = $_POST['payment_labels'] ?? [];
            $payOldImages = $_POST['payment_old_images'] ?? [];

            // Handle new uploads key: payment_images[]
            // Since file inputs in repeater are tricky, we rely on index matching if possible,
            // OR simpler: we iterate over posted indices.

            if (is_array($payLabels)) {
                foreach ($payLabels as $i => $label) {
                    $label = trim($label);
                    // if ($label === '') continue; // Allow empty label if image exists? Better enforce label.

                    $imagePath = $payOldImages[$i] ?? ''; // Keep old image by default

                    // Check for new upload at this index
                    // $_FILES['payment_images']['name'][$i]
                    if (!empty($_FILES['payment_images']['name'][$i])) {
                        $file = [
                            'name' => $_FILES['payment_images']['name'][$i],
                            'type' => $_FILES['payment_images']['type'][$i],
                            'tmp_name' => $_FILES['payment_images']['tmp_name'][$i],
                            'error' => $_FILES['payment_images']['error'][$i],
                            'size' => $_FILES['payment_images']['size'][$i],
                        ];

                        if ($file['error'] === UPLOAD_ERR_OK) {
                            $uploadRes = $this->uploadPaymentIcon($file);
                            if ($uploadRes) {
                                // Delete old image if it existed and is local
                                if ($imagePath && file_exists(__DIR__ . '/../../public/' . $imagePath)) {
                                    @unlink(__DIR__ . '/../../public/' . $imagePath);
                                }
                                $imagePath = $uploadRes;
                            }
                        }
                    }

                    if ($label !== '' || $imagePath !== '') {
                        $paymentMethods[] = ['label' => $label, 'image' => $imagePath];
                    }
                }
            }
            $this->saveContent('payment_methods', json_encode($paymentMethods));

            log_admin_action('UPDATE', 'Updated footer settings', ['page' => 'footer']);

            $this->redirectWithSuccess($this->baseUrl('admin/footer'), 'Footer content updated successfully.');

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

        // UPSERT
        $check = $this->db->query("SELECT id FROM page_contents WHERE page_slug='footer' AND section='main' AND field='$field'");

        if ($check && $check->num_rows > 0) {
            $this->db->query("UPDATE page_contents SET value='$value', updated_at=NOW() WHERE page_slug='footer' AND section='main' AND field='$field'");
        } else {
            $this->db->query("INSERT INTO page_contents (page_slug, section, item_key, field, value, created_at, updated_at) VALUES ('footer', 'main', '0', '$field', '$value', NOW(), NOW())");
        }
    }

    private function uploadPaymentIcon($file)
    {
        
        return Upload::image($file, 'payment', ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg']);
    }
}
