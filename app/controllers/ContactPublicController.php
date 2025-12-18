<?php
// app/controllers/ContactPublicController.php

require_once __DIR__ . '/../core/Controller.php';

class ContactPublicController extends Controller
{
    protected mysqli $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
    }

    public function index(): void
    {
        // Settings auto-injected

        // Get pre-filled product name from query string (if redirected from product page)
        $productName = $_GET['product'] ?? '';

        $this->renderFrontend('contact/index', [
            'page' => 'contact',
            'title' => 'Contact Us',
            // settings auto-injected
            'productName' => $productName,
            'additionalJs' => [
                'frontend/js/render/renderContact.js'
            ]
        ]);
    }

    public function send(): void
    {
        // Validate CSRF token (if implemented)
        // For now, simple validation

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Validation
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Nama wajib diisi';
        }

        if (empty($email)) {
            $errors[] = 'Email wajib diisi';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid';
        }

        if (empty($message)) {
            $errors[] = 'Pesan wajib diisi';
        }

        if (!empty($errors)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            return;
        }

        // Save to database
        $stmt = $this->db->prepare("
            INSERT INTO contact_messages (name, email, phone, subject, message, is_read)
            VALUES (?, ?, ?, ?, ?, 0)
        ");

        $stmt->bind_param('sssss', $name, $email, $phone, $subject, $message);

        if ($stmt->execute()) {
            $stmt->close();

            // Return success response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Pesan Anda berhasil terkirim! Kami akan segera menghubungi Anda.'
            ]);
        } else {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'errors' => ['Terjadi kesalahan saat menyimpan pesan. Silakan coba lagi.']
            ]);
        }
    }

    public function apiContact(): void
    {
        header('Content-Type: application/json');

        // Get settings
        $res = $this->db->query("SELECT * FROM settings WHERE id=1 LIMIT 1");
        $settings = $res ? $res->fetch_assoc() : [];

        echo json_encode([
            'success' => true,
            'settings' => $settings
        ]);
    }
}
