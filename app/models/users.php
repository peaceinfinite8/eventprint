<?php
// app/models/users.php

class Users
{
    protected mysqli $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function getAll(): array
    {
        $sql = "SELECT id, name, email, role, is_active, last_login_at, created_at, updated_at
                FROM users
                ORDER BY id DESC";
        $res = $this->db->query($sql);

        $rows = [];
        if ($res) {
            while ($r = $res->fetch_assoc()) $rows[] = $r;
        }
        return $rows;
    }

    public function find(int $id): ?array
    {
        $id  = (int)$id;
        $sql = "SELECT id, name, email, role, is_active, last_login_at, created_at, updated_at
                FROM users WHERE id = $id LIMIT 1";
        $res = $this->db->query($sql);
        if ($res && $row = $res->fetch_assoc()) return $row;
        return null;
    }

    public function emailExists(string $email, ?int $exceptId = null): bool
    {
        $e = $this->db->real_escape_string($email);
        $sql = "SELECT id FROM users WHERE email = '$e'";
        if ($exceptId !== null) {
            $sql .= " AND id <> " . (int)$exceptId;
        }
        $sql .= " LIMIT 1";
        $res = $this->db->query($sql);
        return (bool)($res && $res->num_rows > 0);
    }

    public function create(array $data): bool
    {
        $name     = $this->db->real_escape_string($data['name'] ?? '');
        $email    = $this->db->real_escape_string($data['email'] ?? '');
        $role     = $this->db->real_escape_string($data['role'] ?? 'admin');
        $isActive = (int)($data['is_active'] ?? 1);

        $hash = password_hash((string)($data['password'] ?? ''), PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (name, email, password, role, is_active, created_at, updated_at)
                VALUES ('$name', '$email', '$hash', '$role', $isActive, NOW(), NOW())";
        return (bool)$this->db->query($sql);
    }

    public function update(int $id, array $data): bool
    {
        $id       = (int)$id;
        $name     = $this->db->real_escape_string($data['name'] ?? '');
        $email    = $this->db->real_escape_string($data['email'] ?? '');
        $role     = $this->db->real_escape_string($data['role'] ?? 'admin');
        $isActive = (int)($data['is_active'] ?? 1);

        $set = [
            "name = '$name'",
            "email = '$email'",
            "role = '$role'",
            "is_active = $isActive",
            "updated_at = NOW()",
        ];

        if (!empty($data['password'])) {
            $hash = password_hash((string)$data['password'], PASSWORD_BCRYPT);
            $set[] = "password = '$hash'";
        }

        $sql = "UPDATE users SET " . implode(', ', $set) . " WHERE id = $id";
        return (bool)$this->db->query($sql);
    }

    public function delete(int $id): bool
    {
        $id = (int)$id;
        $sql = "DELETE FROM users WHERE id = $id";
        return (bool)$this->db->query($sql);
    }
}
