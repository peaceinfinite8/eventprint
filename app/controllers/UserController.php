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

        $baseUrl = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');
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
            $_SESSION['flash'] = ['success' => null, 'error' => $e->getMessage()];
            header('Location: ' . $this->baseUrl('admin/login'));
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $_SESSION['flash'] = ['success' => null, 'error' => 'Email dan password wajib diisi.'];
            header('Location: ' . $this->baseUrl('admin/login'));
            exit;
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
            $_SESSION['flash'] = ['success' => null, 'error' => 'Email atau password salah.'];
            header('Location: ' . $this->baseUrl('admin/login'));
            exit;
        }

        // 2. Check Lock Status (Managed by is_active now)
        // If account was deactivated, is_active check below handles it.
        // We can remove the 'locked_until' check or keep it as legacy data cleanup?
        // Let's remove specific locked_until check to rely on is_active.

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

            $_SESSION['flash'] = ['success' => null, 'error' => $errorMsg];
            header('Location: ' . $this->baseUrl('admin/login'));
            exit;
        }

        // 4. Check Active Status
        if (empty($user['is_active'])) {
            // This catches manually deactivated users AND automatically deactivated users
            $_SESSION['flash'] = ['success' => null, 'error' => 'Akun dinonaktifkan. Hubungi Super Admin untuk mengaktifkan kembali.'];
            header('Location: ' . $this->baseUrl('admin/login'));
            exit;
        }

        // 5. Success - Reset counters
        $uid = (int) $user['id'];
        // Also ensure is_active stays 1 (redundant but safe) and clear failed_attempts
        $db->query("UPDATE users SET last_login_at = NOW(), failed_attempts = 0, locked_until = NULL WHERE id = {$uid}");

        // Login (Using updated Auth supporting concurrent sessions)
        Auth::login($user);

        // Log login
        log_event('info', 'auth', "User logged in: {$user['name']}", ['user_id' => $uid, 'email' => $user['email']]);

        header('Location: ' . $this->baseUrl('admin/dashboard'));
        exit;
    }

    public function logout()
    {
        $user = Auth::user();
        if ($user) {
            log_event('info', 'auth', "User logged out: {$user['name']}", ['user_id' => $user['id']]);
        }

        Auth::logout();
        session_start(); // karena logout destroy session
        $_SESSION['flash'] = ['success' => 'Logout berhasil.', 'error' => null];

        header('Location: ' . $this->baseUrl('admin/login'));
        exit;
    }
}
