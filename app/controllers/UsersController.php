<?php
// app/controllers/UsersController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/users.php';   // SELARAS: users.php
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../core/Auth.php';

class UsersController extends Controller
{
    protected Users $userModel; // SELARAS: class Users

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->userModel = new Users();
    }

    public function index()
    {
        $users = $this->userModel->getAll();

        $this->renderAdmin('users/index', [
            'title' => 'Users',
            'users' => $users,
        ], 'Users');
    }

    public function create()
    {
        $this->renderAdmin('users/create', [
            'title' => 'Tambah User',
        ], 'Tambah User');
    }

    public function store()
    {
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $this->redirectWithError('admin/users', $e->getMessage());
        }

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $role     = trim($_POST['role'] ?? 'admin');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $password = (string)($_POST['password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $this->redirectWithError('admin/users/create', 'Nama, email, dan password wajib diisi.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithError('admin/users/create', 'Format email tidak valid.');
        }

        if (!in_array($role, ['super_admin', 'admin'], true)) {
            $this->redirectWithError('admin/users/create', 'Role tidak valid.');
        }

        if ($this->userModel->emailExists($email)) {
            $this->redirectWithError('admin/users/create', 'Email sudah dipakai.');
        }

        $ok = $this->userModel->create([
            'name'      => $name,
            'email'     => $email,
            'role'      => $role,
            'is_active' => $isActive,
            'password'  => $password,
        ]);

        if (!$ok) {
            $this->redirectWithError('admin/users/create', 'Gagal membuat user.');
        }

        $this->redirectWithSuccess('admin/users', 'User berhasil dibuat.');
    }

    public function edit($id)
    {
        $editUser = $this->userModel->find((int)$id);
        if (!$editUser) {
            $this->redirectWithError('admin/users', 'User tidak ditemukan.');
        }

        $this->renderAdmin('users/edit', [
            'title'    => 'Edit User',
            'editUser' => $editUser,
        ], 'Edit User');
    }

    public function update($id)
    {
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $this->redirectWithError('admin/users', $e->getMessage());
        }

        $id       = (int)$id;
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $role     = trim($_POST['role'] ?? 'admin');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $password = trim($_POST['password'] ?? '');

        if ($name === '' || $email === '') {
            $this->redirectWithError("admin/users/edit/$id", 'Nama dan email wajib diisi.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirectWithError("admin/users/edit/$id", 'Format email tidak valid.');
        }

        if (!in_array($role, ['super_admin', 'admin'], true)) {
            $this->redirectWithError("admin/users/edit/$id", 'Role tidak valid.');
        }

        if ($this->userModel->emailExists($email, $id)) {
            $this->redirectWithError("admin/users/edit/$id", 'Email sudah dipakai.');
        }

        $me = Auth::user();
        if (!empty($me['id']) && (int)$me['id'] === $id && $isActive === 0) {
            $this->redirectWithError("admin/users/edit/$id", 'Tidak bisa menonaktifkan akun sendiri.');
        }

        $ok = $this->userModel->update($id, [
            'name'      => $name,
            'email'     => $email,
            'role'      => $role,
            'is_active' => $isActive,
            'password'  => $password ?: null,
        ]);

        if (!$ok) {
            $this->redirectWithError("admin/users/edit/$id", 'Gagal update user.');
        }

        $this->redirectWithSuccess('admin/users', 'User berhasil diupdate.');
    }

    public function delete($id)
    {
        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            $this->redirectWithError('admin/users', $e->getMessage());
        }

        $id = (int)$id;
        $me = Auth::user();

        if (!empty($me['id']) && (int)$me['id'] === $id) {
            $this->redirectWithError('admin/users', 'Tidak bisa menghapus akun sendiri.');
        }

        $ok = $this->userModel->delete($id);
        if (!$ok) {
            $this->redirectWithError('admin/users', 'Gagal menghapus user.');
        }

        $this->redirectWithSuccess('admin/users', 'User berhasil dihapus.');
    }
}
