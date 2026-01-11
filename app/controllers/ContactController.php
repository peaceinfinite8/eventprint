<?php
// app/controllers/ContactController.php

require_once __DIR__ . '/../models/ContactMessage.php';
require_once __DIR__ . '/../helpers/Validation.php';
require_once __DIR__ . '/../helpers/Security.php'; // ← INI YANG KURANG

class ContactController extends Controller
{
    protected ContactMessage $message;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->message = new ContactMessage();
    }

    /* ===================== HUB CONTACT (ADMIN) ===================== */

    public function adminIndex()
    {
        $total = $this->message->countAll();
        $unread = $this->message->countUnread();
        $latest = $this->message->getLatest(5);

        $sections = [
            [
                'key' => 'messages',
                'name' => 'Pesan Masuk',
                'description' => 'Semua pesan yang dikirim dari form Contact di website.',
                'stats' => [
                    'total' => $total,
                    'unread' => $unread,
                ],
                'latest' => $latest,
                'manage_url' => $this->baseUrl('admin/contact/messages'),
            ],
        ];

        $this->renderAdmin('contact/index', [
            'sections' => $sections,
        ], 'Contact');
    }

    /* ===================== LIST & DETAIL PESAN (ADMIN) ===================== */

    public function adminMessages()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 20;

        $data = $this->message->paginate($page, $perPage);

        $this->renderAdmin('contact/messages', [
            'messages' => $data['items'],
            'total' => $data['total'],
            'page' => $data['page'],
            'perPage' => $data['per_page'],
        ], 'Pesan Kontak');
    }

    public function adminShow($id)
    {
        $id = (int) $id;
        $message = $this->message->find($id);

        if (!$message) {
            http_response_code(404);
            echo "Pesan tidak ditemukan.";
            return;
        }

        if (empty($message['is_read'])) {
            $this->message->markAsRead($id);
            $message['is_read'] = 1;
        }

        $this->renderAdmin('contact/show', [
            'message' => $message,
        ], 'Detail Pesan Kontak');
    }

    public function adminDelete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        // CSRF PROTECT
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $this->redirectWithError('admin/contact/messages', 'CSRF token tidak valid atau sesi kadaluarsa.');
        }

        $id = (int) $id;
        $this->message->delete($id);

        $this->redirectWithSuccess('admin/contact/messages', 'Pesan berhasil dihapus.');
    }

    /* ===================== PUBLIC SEND (FRONTEND) ===================== */

    public function send()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        // aturan validasi
        $rules = [
            'name' => 'required|min:3|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|max:50',
            'subject' => 'nullable|max:150',
            'message' => 'required|min:5',
        ];

        // jalankan validasi pakai helper
        $clean = Validation::validate($_POST, $rules);
        $errors = Validation::errors();

        if (!empty($errors)) {
            http_response_code(422);
            // kalau mau simpel:
            echo "Data tidak valid.";
            // kalau mau debug:
            // echo json_encode($errors);
            return;
        }

        // pakai data yang sudah dibersihkan
        $payload = [
            'name' => $clean['name'],
            'email' => $clean['email'],
            'phone' => $clean['phone'] ?? null,
            'subject' => $clean['subject'] ?? null,
            'message' => $clean['message'],
        ];

        $this->message->create($payload);

        echo "OK";
    }
}
