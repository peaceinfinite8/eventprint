<?php
// app/controllers/UserController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/logging.php';

class UserController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            header('Location: ' . $this->baseUrl('admin/dashboard'));
            exit;
        }

        $baseUrl = rtrim($this->config['base_url'] ?? '/eventprint', '/');
        $csrf = Security::csrfToken();
        $flash = $_SESSION['flash'] ?? ['success' => null, 'error' => null];
        unset($_SESSION['flash']);

        require __DIR__ . '/../../views/admin/auth/login.php';
    }

    public function login()
    {
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $this->redirectWithError('admin/login', $e->getMessage());
        }

        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->redirectWithError('admin/login', 'Email dan password wajib diisi.');
        }

        $db = db();
        // Fetch user data including lock status
        $stmt = $db->prepare("SELECT id, name, email, password, role, is_active, failed_attempts, locked_until FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        // 1. Check if User Exists
        if (!$user) {
            // Generic error to prevent enumeration
            $this->redirectWithError('admin/login', 'Email atau password salah.');
        }

        // 2. Check Lock Status (Managed by is_active now)

        // 3. Verify Password
        if (!password_verify($password, (string) $user['password'])) {
            // Increment failed attempts
            $attempts = (int) $user['failed_attempts'] + 1;
            $maxAttempts = 3;

            // Update failed attempts
            $query = "UPDATE users SET failed_attempts = {$attempts} WHERE id = {$user['id']}";
            $db->query($query);

            // Check if limit reached
            if ($attempts >= $maxAttempts) {
                // DEACTIVATE ACCOUNT
                $query = "UPDATE users SET is_active = 0 WHERE id = {$user['id']}";
                $db->query($query);

                $errorMsg = "Akun dinonaktifkan karena terlalu banyak percobaan gagal. Silakan hubungi Super Admin.";
            } else {
                $remaining = $maxAttempts - $attempts;
                $errorMsg = "Password salah. Sisa percobaan: {$remaining}";
            }

            $this->redirectWithError('admin/login', $errorMsg);
        }

        // 4. Check Active Status
        if (empty($user['is_active'])) {
            $this->redirectWithError('admin/login', 'Akun dinonaktifkan. Hubungi Super Admin untuk mengaktifkan kembali.');
        }

        // 5. Success - Reset counters
        $uid = (int) $user['id'];
        $db->query("UPDATE users SET last_login_at = NOW(), failed_attempts = 0, locked_until = NULL WHERE id = {$uid}");

        // Login
        Auth::login($user);

        // Log login
        log_event('info', 'auth', "User logged in: {$user['name']}", ['user_id' => $uid, 'email' => $user['email']]);

        $this->redirect('admin/dashboard');
    }

    public function logout()
    {
        $user = Auth::user();
        if ($user) {
            log_event('info', 'auth', "User logged out: {$user['name']}", ['user_id' => $user['id']]);
        }

        Auth::logout();
        session_start(); // restart session

        $this->redirectWithSuccess('admin/login', 'Logout berhasil.');
    }

    /**
     * Handle /admin redirect logic
     */
    public function adminRoot()
    {
        if (Auth::check()) {
            $this->redirect('admin/dashboard');
        }

        $this->redirect('admin/login');
    }
}
