<?php
// app/controllers/SystemLogsController.php
// Realtime log viewer for super admin only

require_once __DIR__ . '/../core/controller.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../helpers/logging.php';

class SystemLogsController extends Controller
{
    protected mysqli $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();

        // SuperAdmin check - return JSON for API endpoints
        if (!Auth::isSuperAdmin()) {
            $requestUri = $_SERVER['REQUEST_URI'] ?? '';
            $isApiRequest = (strpos($requestUri, '/api/') !== false);

            if ($isApiRequest) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'error' => 'Forbidden',
                    'message' => 'Super Admin access required'
                ]);
                exit;
            }

            // For view pages, redirect
            http_response_code(403);
            die('Access denied. Super Admin only.');
        }
    }

    /**
     * Render log viewer page
     */
    public function index(): void
    {
        $this->renderAdmin('system-logs/index', [
            'title' => 'System Logs',
            'page' => 'system-logs'
        ]);
    }

    /**
     * API endpoint for fetching logs
     * GET /admin/api/system-logs?after_id=123&limit=200
     */
    public function apiLogs(): void
    {
        // CRITICAL: Set JSON header FIRST
        header('Content-Type: application/json; charset=utf-8');

        try {
            $afterId = isset($_GET['after_id']) ? (int) $_GET['after_id'] : 0;
            $limit = isset($_GET['limit']) ? min((int) $_GET['limit'], 500) : 200;
            $level = $_GET['level'] ?? null;
            $source = $_GET['source'] ?? null;

            $conditions = [];
            $params = [];
            $types = '';

            // Filter by after_id for pagination/realtime
            if ($afterId > 0) {
                $conditions[] = 'id > ?';
                $params[] = $afterId;
                $types .= 'i';
            }

            // Filter by level
            if ($level && in_array($level, ['info', 'warning', 'error'])) {
                $conditions[] = 'level = ?';
                $params[] = $level;
                $types .= 's';
            }

            // Filter by source
            if ($source && in_array($source, ['api', 'admin', 'system'])) {
                $conditions[] = 'source = ?';
                $params[] = $source;
                $types .= 's';
            }

            $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

            $sql = "
                SELECT id, level, source, message, context, created_at
                FROM activity_logs
                {$where}
                ORDER BY id DESC
                LIMIT ?
            ";

            $params[] = $limit;
            $types .= 'i';

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new Exception('Database prepare failed: ' . $this->db->error);
            }

            if ($types) {
                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                throw new Exception('Query execution failed: ' . $stmt->error);
            }

            $result = $stmt->get_result();

            $logs = [];
            while ($row = $result->fetch_assoc()) {
                // Decode context JSON
                $row['context'] = $row['context'] ? json_decode($row['context'], true) : null;
                $logs[] = $row;
            }

            $stmt->close();

            echo json_encode([
                'success' => true,
                'logs' => $logs,
                'count' => count($logs)
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Internal Server Error',
                'message' => 'Failed to fetch logs: ' . $e->getMessage()
            ]);
        }

        exit; // CRITICAL: Prevent any further output
    }

    /**
     * Clear old logs (optional admin action)
     */
    public function clearOld(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'error' => 'Method not allowed'
            ]);
            exit;
        }

        try {
            // Delete logs older than 30 days
            $this->db->query("
                DELETE FROM activity_logs
                WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");

            $deleted = $this->db->affected_rows;

            log_admin_action('Clear Logs', "Deleted {$deleted} old log entries");

            echo json_encode([
                'success' => true,
                'deleted' => $deleted
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to clear logs: ' . $e->getMessage()
            ]);
        }

        exit;
    }
}
