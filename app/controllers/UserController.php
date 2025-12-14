<?php
// app/controllers/UserController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../helpers/Security.php';

class UserController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            header('Location: ' . $this->baseUrl('admin/dashboard'));
            exit;
        }

        $baseUrl = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');
        $csrf    = Security::csrfToken();
        $flash   = $_SESSION['flash'] ?? ['success' => null, 'error' => null];
        unset($_SESSION['flash']);

        require __DIR__ . '/../../views/admin/auth/login.php';
    }

    public function login()
    {
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $_SESSION['flash'] = ['success' => null, 'error' => $e->getMessage()];
            header('Location: ' . $this->baseUrl('admin/login'));
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $_SESSION['flash'] = ['success' => null, 'error' => 'Email dan password wajib diisi.'];
            header('Location: ' . $this->baseUrl('admin/login'));
            exit;
        }

        $db = db();
        $stmt = $db->prepare("SELECT id, name, email, password, role, is_active FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res  = $stmt->get_result();
        $user = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        if (!$user || !password_verify($password, (string)$user['password'])) {
            $_SESSION['flash'] = ['success' => null, 'error' => 'Email atau password salah.'];
            header('Location: ' . $this->baseUrl('admin/login'));
            exit;
        }

        if (empty($user['is_active'])) {
            $_SESSION['flash'] = ['success' => null, 'error' => 'Akun nonaktif. Hubungi super admin.'];
            header('Location: ' . $this->baseUrl('admin/login'));
            exit;
        }

        Auth::login($user);

        $uid = (int)$user['id'];
        $db->query("UPDATE users SET last_login_at = NOW() WHERE id = {$uid}");

        header('Location: ' . $this->baseUrl('admin/dashboard'));
        exit;
    }

    public function logout()
    {
        Auth::logout();
        session_start(); // karena logout destroy session
        $_SESSION['flash'] = ['success' => 'Logout berhasil.', 'error' => null];

        header('Location: ' . $this->baseUrl('admin/login'));
        exit;
    }
}
