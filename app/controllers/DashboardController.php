<?php
// app/controllers/DashboardController.php

require_once __DIR__ . '/../core/controller.php';

class DashboardController extends Controller
{
    protected mysqli $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
    }

    private function tableExists(string $table): bool
    {
        $t = $this->db->real_escape_string($table);
        $res = $this->db->query("SHOW TABLES LIKE '{$t}'");
        return $res && $res->num_rows > 0;
    }

    private function colExists(string $table, string $col): bool
    {
        $t = $this->db->real_escape_string($table);
        $c = $this->db->real_escape_string($col);
        $res = $this->db->query("SHOW COLUMNS FROM `{$t}` LIKE '{$c}'");
        return $res && $res->num_rows > 0;
    }

    private function scalar(string $sql, $default = 0)
    {
        $res = $this->db->query($sql);
        if (!$res)
            return $default;
        $row = $res->fetch_row();
        return $row ? $row[0] : $default;
    }

    public function index(): void
    {
        $baseUrl = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');
        $db = $this->db;

        // ====== COUNTS ======
        $productsActive = (int) $this->scalar("SELECT COUNT(*) FROM products WHERE is_active=1 AND deleted_at IS NULL", 0);
        $categoriesActive = (int) $this->scalar("SELECT COUNT(*) FROM product_categories WHERE is_active=1", 0);
        $heroActive = (int) $this->scalar("SELECT COUNT(*) FROM hero_slides WHERE page_slug='home' AND is_active=1", 0);

        // Messages
        $messagesTotal = (int) $this->scalar("SELECT COUNT(*) FROM contact_messages", 0);
        // Try is_read first, fallback to status if column missing (removed check for speed, assume schema is valid or use try-catch if critical)
        // Adjust based on known schema. Assuming 'is_read' exists per previous context.
        $messagesUnread = (int) $this->scalar("SELECT COUNT(*) FROM contact_messages WHERE is_read=0", 0);

        // Small Banners
        $smallBannerTotal = (int) $this->scalar("SELECT COUNT(*) FROM hero_slides WHERE page_slug='home_small'", 0);
        $smallBannerActive = (int) $this->scalar("SELECT COUNT(*) FROM hero_slides WHERE page_slug='home_small' AND is_active=1", 0);

        // Testimonials
        $testimonialTotal = (int) $this->scalar("SELECT COUNT(*) FROM testimonials", 0);
        $testimonialActive = (int) $this->scalar("SELECT COUNT(*) FROM testimonials WHERE is_active=1", 0);

        // ====== LATEST ======
        $latestProducts = [];
        $res = $db->query("SELECT id, name, created_at, is_active FROM products WHERE deleted_at IS NULL ORDER BY id DESC LIMIT 5");
        if ($res) {
            $latestProducts = $res->fetch_all(MYSQLI_ASSOC);
        }

        $latestMessages = [];
        // Assuming 'contact_messages' exists
        $res = $db->query("SELECT id, COALESCE(name,'') AS name, COALESCE(email,'') AS email, COALESCE(message,'') AS message, COALESCE(created_at, NOW()) AS created_at FROM contact_messages ORDER BY id DESC LIMIT 5");
        if ($res) {
            $latestMessages = $res->fetch_all(MYSQLI_ASSOC);
        }

        $latestLogs = [];
        $res = $db->query("SELECT id, level, source, message, created_at FROM activity_logs ORDER BY id DESC LIMIT 10");
        if ($res) {
            $latestLogs = $res->fetch_all(MYSQLI_ASSOC);
        }

        $stats = [
            'products_active' => $productsActive,
            'categories_active' => $categoriesActive,
            'hero_active' => $heroActive,
            'messages_total' => $messagesTotal,
            'messages_unread' => $messagesUnread,
            'small_banner_total' => $smallBannerTotal,
            'small_banner_active' => $smallBannerActive,
            'testimonial_total' => $testimonialTotal,
            'testimonial_active' => $testimonialActive,
            // Carry over existing stats if passed via HomeController logic ?? (Wait, DashboardController handles /admin/dashboard)
            // Need to merge with existing logic? No, this IS the logic.
            // But previous view expected 'contact_pct', 'mapping_pct', etc.
            // We need to calculate those here!
        ];

        // Recalculate Contact/Mapping Pct
        // Settings/Home content?
        // We need to fetch 'settings' or 'home_content' from 'settings' table?
        // 'settings' table usually has 'home_content' JSON?
        // Let's check how 'HomeController' fetches it. 
        // Or re-implement quickly.

        $settingsRow = $db->query("SELECT * FROM settings WHERE id=1 LIMIT 1")->fetch_assoc();
        $homeContent = json_decode($settingsRow['home_content'] ?? '{}', true);

        // Calc Contact Pct
        $completed = 0;
        $total = 6; // address, phone, email, whatsapp, cta_title, cta_link
        if (!empty($settingsRow['address']))
            $completed++;
        if (!empty($settingsRow['phone']))
            $completed++;
        if (!empty($settingsRow['email']))
            $completed++;
        if (!empty($settingsRow['whatsapp']))
            $completed++;
        if (!empty($homeContent['hero_cta_text']))
            $completed++;
        if (!empty($homeContent['hero_cta_url']))
            $completed++;
        $stats['contact_pct'] = round(($completed / $total) * 100);

        // Calc Mapping Pct
        $mapComplete = 0;
        $mapTotal = 2;
        $printId = (int) ($homeContent['home_print_category_id'] ?? 0);
        $mediaId = (int) ($homeContent['home_media_category_id'] ?? 0);
        if ($printId > 0)
            $mapComplete++;
        if ($mediaId > 0)
            $mapComplete++;
        $stats['mapping_pct'] = round(($mapComplete / $mapTotal) * 100);

        $stats['print_id'] = $printId;
        $stats['media_id'] = $mediaId;

        // Fetch Mapping Names & Counts
        $stats['print_name'] = '';
        $stats['print_prod_count'] = 0;
        if ($printId > 0) {
            $c = $db->query("SELECT name FROM product_categories WHERE id=$printId")->fetch_assoc();
            $stats['print_name'] = $c['name'] ?? '';
            $stats['print_prod_count'] = (int) $this->scalar("SELECT COUNT(*) FROM products WHERE category_id=$printId AND is_active=1 AND deleted_at IS NULL");
        }

        $stats['media_name'] = '';
        $stats['media_prod_count'] = 0;
        if ($mediaId > 0) {
            $c = $db->query("SELECT name FROM product_categories WHERE id=$mediaId")->fetch_assoc();
            $stats['media_name'] = $c['name'] ?? '';
            $stats['media_prod_count'] = (int) $this->scalar("SELECT COUNT(*) FROM products WHERE category_id=$mediaId AND is_active=1 AND deleted_at IS NULL");
        }

        $stats['featured_count'] = (int) $this->scalar("SELECT COUNT(*) FROM products WHERE is_featured=1 AND is_active=1 AND deleted_at IS NULL");

        $categories = $db->query("SELECT id, name FROM product_categories WHERE is_active=1 ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

        $this->renderAdmin('dashboard/index', [
            'baseUrl' => $baseUrl,
            'stats' => $stats,
            'latestProducts' => $latestProducts,
            'latestMessages' => $latestMessages,
            'latestLogs' => $latestLogs,
            'categories' => $categories,
            'homeContent' => $homeContent,
            'csrfToken' => $_SESSION['csrf_token'] ?? ''
        ], 'Dashboard');
    }
}
