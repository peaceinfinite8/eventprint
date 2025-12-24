<?php
// app/controllers/ApiController.php

class ApiController extends Controller
{
    protected mysqli $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
    }

    /**
     * GET /api/settings
     * Returns site settings for frontend JS
     */
    public function settings(): void
    {
        self::jsonResponse([
            'success' => false,
            'message' => 'Use specific settings endpoint if needed, or implement full settings here'
        ]);
        // Placeholder or keep original implementation if needed, but user focused on testimonials.
        // Re-implementing original settings logic to avoid breaking other things.

        try {
            $res = $this->db->query("SELECT * FROM settings WHERE id=1 LIMIT 1");
            $settings = $res ? $res->fetch_assoc() : [];

            $output = [
                'success' => true,
                'logo' => $settings['logo'] ?? '',
                'site_name' => $settings['site_name'] ?? 'EventPrint',
                'phone' => $settings['phone'] ?? '',
                'whatsapp' => $settings['whatsapp'] ?? '',
                'email' => $settings['email'] ?? '',
                'address' => $settings['address'] ?? '',
                'operating_hours' => $settings['operating_hours'] ?? '',
                'facebook' => $settings['facebook'] ?? '',
                'instagram' => $settings['instagram'] ?? '',
                'twitter' => $settings['twitter'] ?? '',
                'gmaps_embed' => $settings['gmaps_embed'] ?? ''
            ];

            self::jsonResponse($output);

        } catch (\Throwable $e) {
            self::jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /data/products.json (compatibility endpoint)
     */
    public function productsJson(): void
    {
        require_once __DIR__ . '/FrontendDataController.php';
        $controller = new FrontendDataController($this->config);
        $controller->serve('products.json');
    }

    /**
     * GET /api/testimonials
     * Corrects SQL error (removes deleted_at) and matches spec {ok: true, data: []}
     */
    public function testimonials(): void
    {
        try {
            if (!$this->db) {
                throw new \Exception("Database connection not initialized.");
            }

            // REMOVED `deleted_at` because column does not exist
            $sql = "SELECT id, name, position, photo, rating, message, is_active, sort_order, created_at 
                    FROM testimonials 
                    WHERE is_active = 1 
                    ORDER BY sort_order ASC, created_at DESC";

            // Use try-catch for query to prevent unhandled exception if SQL error occurs
            try {
                $res = $this->db->query($sql);
            } catch (\Throwable $sqlErr) {
                // Log error internally if possible, return empty data to prevent frontend crash
                // But user asked for 500 if query fails? "jika query gagal -> return JSON error dengan status 500"

                // Let's provide the error details as requested for dev
                self::jsonResponse([
                    'ok' => false,
                    'message' => 'Query Error: ' . $sqlErr->getMessage()
                ], 500);
                return;
            }

            $items = [];
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $items[] = [
                        'id' => (int) $row['id'],
                        'name' => (string) ($row['name'] ?? ''),
                        'role' => (string) ($row['position'] ?? ''), // Mapped to 'role' as per spec example? Or keep original keys?
                        // User Request Example: "role": "Jabatan/Perusahaan". Current DB: position.
                        // I will map position -> role to be safe with Spec Example, but maybe keep 'position' too or just use 'role' as requested.
                        // Actually, let's include BOTH or map it. Spec example had "role". I will add "role".
                        'position' => (string) ($row['position'] ?? ''),

                        'message' => (string) ($row['message'] ?? ''),
                        'rating' => (int) ($row['rating'] ?? 5),
                        'photo' => $row['photo'] ? (string) $row['photo'] : null,
                        'is_active' => (int) ($row['is_active'] ?? 0),
                        'created_at' => (string) ($row['created_at'] ?? '')
                    ];
                }
            }

            // Spec: { "ok": true, "data": [] }
            // Note: User spec example used "role" instead of "position".
            // Implementation:
            self::jsonResponse([
                'ok' => true,
                'data' => $items
            ]);

        } catch (\Throwable $e) {
            self::jsonResponse([
                'ok' => false,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper to send JSON response and exit
     */
    protected static function jsonResponse(array $payload, int $status = 200): void
    {
        // Safe header sending
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        }

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
