<?php
// app/models/OurStore.php

class OurStore
{
    protected function db(): mysqli
    {
        return db();
    }

    public function countAll(): int
    {
        $db = $this->db();
        $res = $db->query("SELECT COUNT(*) AS total FROM our_store");
        $row = $res ? $res->fetch_assoc() : null;
        return $row ? (int) $row['total'] : 0;
    }

    public function getLatest(int $limit = 5): array
    {
        $db = $this->db();
        $limit = max(1, (int) $limit);

        $sql = "SELECT *
                FROM our_store
                ORDER BY is_active DESC, sort_order ASC, updated_at DESC
                LIMIT ?";

        $stmt = $db->prepare($sql);
        if (!$stmt)
            throw new Exception("Prepare failed: " . $db->error);

        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return $rows;
    }

    // ============================
// PUBLIC (FRONTEND)
// ============================

    public function publicList(int $limit = 50): array
    {
        $db = $this->db();
        $limit = max(1, (int) $limit);

        $sql = "SELECT *
                FROM our_store
                WHERE is_active = 1
                ORDER BY sort_order ASC, updated_at DESC
                LIMIT ?";

        $stmt = $db->prepare($sql);
        if (!$stmt)
            throw new Exception("Prepare failed: " . $db->error);

        $stmt->bind_param("i", $limit);
        $stmt->execute();

        $res = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return $rows;
    }

    public function publicFirst(): ?array
    {
        $rows = $this->publicList(1);
        return $rows[0] ?? null;
    }


    public function getNextSortOrder(): int
    {
        $db = $this->db();
        $res = $db->query("SELECT COALESCE(MAX(sort_order),0)+1 AS next_order FROM our_store");
        $row = $res ? $res->fetch_assoc() : null;
        return $row ? (int) $row['next_order'] : 1;
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $db = $this->db();
        $slug = trim($slug);
        if ($slug === '')
            return false;

        $sql = "SELECT id FROM our_store WHERE slug = ?";
        if ($ignoreId !== null)
            $sql .= " AND id <> ?";
        $sql .= " LIMIT 1";

        $stmt = $db->prepare($sql);
        if (!$stmt)
            throw new Exception("Prepare failed: " . $db->error);

        if ($ignoreId !== null)
            $stmt->bind_param("si", $slug, $ignoreId);
        else
            $stmt->bind_param("s", $slug);

        $stmt->execute();
        $res = $stmt->get_result();
        $ok = $res && $res->num_rows > 0;
        $stmt->close();

        return $ok;
    }

    public function searchWithPagination(?string $keyword, int $page, int $perPage): array
    {
        $db = $this->db();
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $where = "WHERE 1=1";
        $params = [];
        $types = "";

        $keyword = $keyword !== null ? trim($keyword) : null;
        if ($keyword !== null && $keyword !== '') {
            $where .= " AND (name LIKE ? OR city LIKE ? OR address LIKE ? OR office_type LIKE ?)";
            $like = "%{$keyword}%";
            $params = [$like, $like, $like, $like];
            $types = "ssss";
        }

        // count
        $sqlCount = "SELECT COUNT(*) AS total FROM our_store $where";
        $stmt = $db->prepare($sqlCount);
        if (!$stmt)
            throw new Exception("Prepare failed: " . $db->error);
        if (!empty($params))
            $stmt->bind_param($types, ...$params);

        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $total = $row ? (int) $row['total'] : 0;
        $stmt->close();

        // data
        $sqlData = "SELECT *
                    FROM our_store
                    $where
                    ORDER BY is_active DESC, sort_order ASC, updated_at DESC
                    LIMIT ?, ?";

        $stmt = $db->prepare($sqlData);
        if (!$stmt)
            throw new Exception("Prepare failed: " . $db->error);

        if (!empty($params)) {
            $types2 = $types . "ii";
            $params2 = array_merge($params, [$offset, $perPage]);
            $stmt->bind_param($types2, ...$params2);
        } else {
            $stmt->bind_param("ii", $offset, $perPage);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        $items = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    public function find(int $id): ?array
    {
        $db = $this->db();
        $stmt = $db->prepare("SELECT * FROM our_store WHERE id=? LIMIT 1");
        if (!$stmt)
            throw new Exception("Prepare failed: " . $db->error);

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }

    public function create(array $d): int
    {
        $db = $this->db();

        $sql = "INSERT INTO our_store
                (name, slug, office_type, address, city, phone, whatsapp, gmaps_url, thumbnail, is_active, sort_order)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        if (!$stmt)
            throw new Exception("Prepare failed: " . $db->error);

        $isActive = (int) ($d['is_active'] ?? 1);
        $sortOrder = (int) ($d['sort_order'] ?? 1);

        $stmt->bind_param(
            "sssssssssii",
            $d['name'],
            $d['slug'],
            $d['office_type'],
            $d['address'],
            $d['city'],
            $d['phone'],
            $d['whatsapp'],
            $d['gmaps_url'],
            $d['thumbnail'],
            $isActive,
            $sortOrder
        );

        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function update(int $id, array $d): bool
    {
        $db = $this->db();

        $sql = "UPDATE our_store SET
                name=?, slug=?, office_type=?, address=?, city=?,
                phone=?, whatsapp=?, gmaps_url=?, thumbnail=?,
                is_active=?, sort_order=?,
                updated_at=NOW()
                WHERE id=? LIMIT 1";

        $stmt = $db->prepare($sql);
        if (!$stmt)
            throw new Exception("Prepare failed: " . $db->error);

        $isActive = (int) ($d['is_active'] ?? 1);
        $sortOrder = (int) ($d['sort_order'] ?? 1);

        $stmt->bind_param(
            "sssssssssiii",
            $d['name'],
            $d['slug'],
            $d['office_type'],
            $d['address'],
            $d['city'],
            $d['phone'],
            $d['whatsapp'],
            $d['gmaps_url'],
            $d['thumbnail'],
            $isActive,
            $sortOrder,
            $id
        );

        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();

        return $ok;
    }

    public function delete(int $id): bool
    {
        $db = $this->db();
        $stmt = $db->prepare("DELETE FROM our_store WHERE id=? LIMIT 1");
        if (!$stmt)
            throw new Exception("Prepare failed: " . $db->error);

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        $stmt->close();

        return $ok;
    }
}
