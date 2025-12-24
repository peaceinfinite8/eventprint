<?php
// app/controllers/ContactMessagesController.php
// Contact messages CRUD for admin

require_once __DIR__ . '/../core/controller.php';
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../helpers/logging.php';

class ContactMessagesController extends Controller
{
    protected mysqli $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();

        // Admin auth check
        if (!Auth::isAdmin()) {
            http_response_code(403);
            die('Access denied.');
        }
    }

    /**
     * List all contact messages
     */
    public function index(): void
    {
        // Pagination
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Count total
        $countResult = $this->db->query("SELECT COUNT(*) as total FROM contact_messages");
        $total = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($total / $perPage);

        // Fetch messages
        $messages = [];
        $result = $this->db->query("
            SELECT id, name, email, phone, subject, created_at, is_read
            FROM contact_messages
            ORDER BY created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ");

        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }

        $this->renderAdmin('contact-messages/index', [
            'title' => 'Contact Messages',
            'page' => 'contact-messages',
            'messages' => $messages,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }

    /**
     * View single message
     */
    public function view($id): void
    {
        $id = (int) $id;

        $stmt = $this->db->prepare("
            SELECT * FROM contact_messages WHERE id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $message = $result->fetch_assoc();
        $stmt->close();

        if (!$message) {
            http_response_code(404);
            die('Message not found');
        }

        // Auto-mark as read when viewing
        if (!$message['is_read']) {
            $this->db->query("UPDATE contact_messages SET is_read = 1 WHERE id = {$id}");
            log_admin_action('View Message', "Viewed and auto-marked message #{$id} as read");
        }

        $this->renderAdmin('contact-messages/view', [
            'title' => 'View Message',
            'page' => 'contact-messages',
            'message' => $message
        ]);
    }

    /**
     * Toggle read/unread status
     */
    public function toggleRead($id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $id = (int) $id;

        $stmt = $this->db->prepare("SELECT is_read FROM contact_messages WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $message = $result->fetch_assoc();
        $stmt->close();

        if (!$message) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Not found']);
            return;
        }

        $newStatus = $message['is_read'] ? 0 : 1;

        $stmt = $this->db->prepare("UPDATE contact_messages SET is_read = ? WHERE id = ?");
        $stmt->bind_param('ii', $newStatus, $id);
        $stmt->execute();
        $stmt->close();

        log_admin_action('Toggle Message Status', "Marked message #{$id} as " . ($newStatus ? 'read' : 'unread'));

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'is_read' => $newStatus
        ]);
    }

    /**
     * Delete message
     */
    public function delete($id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            die('Method not allowed');
        }

        $id = (int) $id;

        $stmt = $this->db->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            log_admin_action('Delete Message', "Deleted contact message #{$id}");
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success
        ]);
    }
}
