<?php
// app/models/Testimonial.php

class Testimonial
{
    protected mysqli $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM testimonials ORDER BY sort_order ASC, created_at DESC";
        $result = $this->db->query($sql);

        $items = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        return $items;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM testimonials WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO testimonials (name, position, photo, rating, message, is_active, sort_order, bg_color) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            'sssisiis',
            $data['name'],
            $data['position'],
            $data['photo'],
            $data['rating'],
            $data['message'],
            $data['is_active'],
            $data['sort_order'],
            $data['bg_color']
        );

        $stmt->execute();
        return $this->db->insert_id;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE testimonials 
             SET name = ?, position = ?, photo = ?, rating = ?, message = ?, is_active = ?, sort_order = ?, bg_color = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            'sssisiisi',
            $data['name'],
            $data['position'],
            $data['photo'],
            $data['rating'],
            $data['message'],
            $data['is_active'],
            $data['sort_order'],
            $data['bg_color'],
            $id
        );

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
