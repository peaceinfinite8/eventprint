<?php
// app/models/ProductCategory.php

class ProductCategory
{
    protected $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function getAll(): array
    {
        $sql = "SELECT *
                FROM product_categories
                ORDER BY sort_order ASC, name ASC";
        $res = $this->db->query($sql);

        $data = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM product_categories WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        return $row ?: null;
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        if ($ignoreId) {
            $stmt = $this->db->prepare("SELECT id FROM product_categories WHERE slug = ? AND id != ? LIMIT 1");
            $stmt->bind_param('si', $slug, $ignoreId);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM product_categories WHERE slug = ? LIMIT 1");
            $stmt->bind_param('s', $slug);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        return (bool) $res->fetch_assoc();
    }

    public function getNextSortOrder(): int
    {
        $sql = "SELECT MAX(sort_order) AS max_sort FROM product_categories";
        $res = $this->db->query($sql);
        if ($res && $row = $res->fetch_assoc()) {
            return ((int) $row['max_sort']) + 1;
        }
        return 1;
    }

    public function hasProducts(int $id): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM products WHERE category_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        return (int) ($row['total'] ?? 0);
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO product_categories
                (name, slug, description, sort_order, is_active, whatsapp_number, icon)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        $name = $data['name'];
        $slug = $data['slug'];
        $description = $data['description'] ?? null;
        $sort_order = (int) ($data['sort_order'] ?? 0);
        $is_active = (int) ($data['is_active'] ?? 1);
        $wa = $data['whatsapp_number'] ?? null;
        $icon = $data['icon'] ?? null;

        $stmt->bind_param(
            'sssisss',
            $name,
            $slug,
            $description,
            $sort_order,
            $is_active,
            $wa,
            $icon
        );

        return $stmt->execute();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE product_categories
                SET name = ?, slug = ?, description = ?, sort_order = ?, is_active = ?, whatsapp_number = ?, icon = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);

        $name = $data['name'];
        $slug = $data['slug'];
        $description = $data['description'] ?? null;
        $sort_order = (int) ($data['sort_order'] ?? 0);
        $is_active = (int) ($data['is_active'] ?? 1);
        $wa = $data['whatsapp_number'] ?? null;
        $icon = $data['icon'] ?? null;

        $stmt->bind_param(
            'sssisssi',
            $name,
            $slug,
            $description,
            $sort_order,
            $is_active,
            $wa,
            $icon,
            $id
        );

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM product_categories WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
