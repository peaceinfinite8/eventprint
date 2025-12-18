<?php
// app/controllers/DashboardController.php

require_once __DIR__ . '/../core/Controller.php';

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
        if (!$res) return $default;
        $row = $res->fetch_row();
        return $row ? $row[0] : $default;
    }

    public function index(): void
    {
        $baseUrl = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');

        // ====== COUNTS ======
        $productsActive = $this->tableExists('products')
            ? (int)$this->scalar("SELECT COUNT(*) FROM products WHERE is_active=1 AND deleted_at IS NULL", 0)
            : 0;

        $categoriesActive = $this->tableExists('product_categories')
            ? (int)$this->scalar("SELECT COUNT(*) FROM product_categories WHERE is_active=1", 0)
            : 0;

        $heroActive = $this->tableExists('hero_slides')
            ? (int)$this->scalar("SELECT COUNT(*) FROM hero_slides WHERE page_slug='home' AND is_active=1", 0)
            : 0;

        // Pesan: sesuaikan tabel kamu. Aku bikin fallback.
        $messagesTotal = 0;
        $messagesUnread = 0;

        if ($this->tableExists('contact_messages')) {
            $messagesTotal = (int)$this->scalar("SELECT COUNT(*) FROM contact_messages", 0);

            if ($this->colExists('contact_messages', 'is_read')) {
                $messagesUnread = (int)$this->scalar("SELECT COUNT(*) FROM contact_messages WHERE is_read=0", 0);
            } elseif ($this->colExists('contact_messages', 'status')) {
                // fallback: status='unread'
                $messagesUnread = (int)$this->scalar("SELECT COUNT(*) FROM contact_messages WHERE status='unread'", 0);
            }
        }

        // ====== LATEST ======
        $latestProducts = [];
        if ($this->tableExists('products')) {
            $res = $this->db->query("
                SELECT id, name, created_at, is_active
                FROM products
                WHERE deleted_at IS NULL
                ORDER BY id DESC
                LIMIT 5
            ");
            if ($res) while ($r = $res->fetch_assoc()) $latestProducts[] = $r;
        }

        $latestMessages = [];
        if ($this->tableExists('contact_messages')) {
            // kolom umum: name/email/message/created_at
            $res = $this->db->query("
                SELECT id,
                       COALESCE(name,'') AS name,
                       COALESCE(email,'') AS email,
                       COALESCE(message,'') AS message,
                       COALESCE(created_at, NOW()) AS created_at
                FROM contact_messages
                ORDER BY id DESC
                LIMIT 5
            ");
            if ($res) while ($r = $res->fetch_assoc()) $latestMessages[] = $r;
        }

        $stats = [
            'products_active'   => $productsActive,
            'categories_active' => $categoriesActive,
            'hero_active'       => $heroActive,
            'messages_total'    => $messagesTotal,
            'messages_unread'   => $messagesUnread,
        ];

        $this->renderAdmin('dashboard/index', [
            'baseUrl'         => $baseUrl,
            'stats'           => $stats,
            'latestProducts'  => $latestProducts,
            'latestMessages'  => $latestMessages,
        ], 'Dashboard');
    }
}
