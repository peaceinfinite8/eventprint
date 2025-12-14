<?php
// app/models/ContactMessage.php

class ContactMessage
{
    protected function db()
    {
        return db(); // pakai helper global dari app/config/db.php
    }

    /**
     * Simpan pesan baru dari form contact.
     * @return int ID yang baru dibuat
     */
    public function create(array $data): int
    {
        $db = $this->db();

        $sql = "INSERT INTO contact_messages
                (name, email, phone, subject, message, is_read, created_at)
                VALUES (?, ?, ?, ?, ?, 0, NOW())";

        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }

        $name    = $data['name']    ?? '';
        $email   = $data['email']   ?? '';
        $phone   = $data['phone']   ?? '';
        $subject = $data['subject'] ?? '';
        $message = $data['message'] ?? '';

        $stmt->bind_param('sssss', $name, $email, $phone, $subject, $message);
        $stmt->execute();

        $id = (int)$stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function countAll(): int
    {
        $db = $this->db();
        $total = 0;

        $res = $db->query("SELECT COUNT(*) AS total FROM contact_messages");
        if ($res && $row = $res->fetch_assoc()) {
            $total = (int)$row['total'];
        }
        return $total;
    }

    public function countUnread(): int
    {
        $db = $this->db();
        $total = 0;

        $res = $db->query("SELECT COUNT(*) AS total FROM contact_messages WHERE is_read = 0");
        if ($res && $row = $res->fetch_assoc()) {
            $total = (int)$row['total'];
        }
        return $total;
    }

    /**
     * Ambil beberapa pesan terbaru.
     */
    public function getLatest(int $limit = 5): array
    {
        $db = $this->db();
        $limit = max(1, $limit);

        $sql  = "SELECT * FROM contact_messages
                 ORDER BY created_at DESC
                 LIMIT ?";
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }

        $stmt->bind_param('i', $limit);
        $stmt->execute();

        $res   = $stmt->get_result();
        $rows  = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return $rows;
    }

    /**
     * Pagination list pesan.
     */
    public function paginate(int $page, int $perPage): array
    {
        $db      = $this->db();
        $page    = max(1, $page);
        $perPage = max(1, $perPage);
        $offset  = ($page - 1) * $perPage;

        // total
        $total = $this->countAll();

        // items
        $sql  = "SELECT * FROM contact_messages
                 ORDER BY created_at DESC
                 LIMIT ?, ?";
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }

        $stmt->bind_param('ii', $offset, $perPage);
        $stmt->execute();

        $res   = $stmt->get_result();
        $items = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }

    public function find(int $id): ?array
    {
        $db = $this->db();

        $sql  = "SELECT * FROM contact_messages WHERE id = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }

    public function markAsRead(int $id): void
    {
        $db = $this->db();

        $sql  = "UPDATE contact_messages SET is_read = 1 WHERE id = ?";
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }

    public function delete(int $id): void
    {
        $db = $this->db();

        $sql  = "DELETE FROM contact_messages WHERE id = ?";
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
}
