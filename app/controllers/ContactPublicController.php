<?php
// app/controllers/ContactPublicController.php

class ContactPublicController extends Controller
{
    protected mysqli $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
    }

    public function index()
    {
        $this->renderFrontend('contact/index', [
            'page' => 'contact',
        ], 'EventPrint — Kontak');
    }

    public function send()
    {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '' || $message === '') {
            $_SESSION['flash_error'] = 'Nama dan pesan wajib diisi.';
            header('Location: ' . ($this->config['baseUrl'] ?? '/eventprint/public') . '/contact#form');
            exit;
        }

        $sql = "INSERT INTO contact_messages (name, email, phone, message, is_read, created_at)
                VALUES (?, ?, ?, ?, 0, NOW())";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ssss', $name, $email, $phone, $message);
            $stmt->execute();
            $stmt->close();
        }

        $_SESSION['flash_success'] = 'Pesan berhasil dikirim. Kami akan hubungi kamu.';
        header('Location: ' . ($this->config['baseUrl'] ?? '/eventprint/public') . '/contact#form');
        exit;
    }
}
