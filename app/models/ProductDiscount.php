<?php

class ProductDiscount
{
    protected function db(): mysqli { return db(); }

    public function find(int $id): ?array
    {
        $db = $this->db();
        $sql = "SELECT d.*, p.name AS product_name, p.stock AS product_stock, p.base_price AS product_price
                FROM product_discounts d
                JOIN products p ON p.id = d.product_id
                WHERE d.id = ?
                LIMIT 1";
        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row ?: null;
    }

    public function paginate(?string $q, int $page, int $perPage): array
    {
        $db = $this->db();
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $where = "WHERE 1=1";
        $params = [];
        $types = "";

        $q = $q !== null ? trim($q) : null;
        if ($q !== null && $q !== '') {
            $where .= " AND (p.name LIKE ? OR d.discount_type LIKE ?)";
            $like = "%{$q}%";
            $params[] = $like;
            $params[] = $like;
            $types .= "ss";
        }

        // total
        $sqlCount = "SELECT COUNT(*) AS total
                     FROM product_discounts d
                     JOIN products p ON p.id = d.product_id
                     $where";
        $stmt = $db->prepare($sqlCount);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $total = $row ? (int)$row['total'] : 0;
        $stmt->close();

        // data
        $sqlData = "SELECT d.*, p.name AS product_name, p.stock AS product_stock, p.base_price AS product_price
                    FROM product_discounts d
                    JOIN products p ON p.id = d.product_id
                    $where
                    ORDER BY d.id DESC
                    LIMIT ?, ?";
        $stmt = $db->prepare($sqlData);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        if ($params) {
            $params2 = $params;
            $types2  = $types . "ii";
            $params2[] = $offset;
            $params2[] = $perPage;
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

    public function hasOverlappingActive(int $productId, ?string $startAt, ?string $endAt, ?int $ignoreId = null): bool
    {
        $db = $this->db();

        $sql = "SELECT id
                FROM product_discounts
                WHERE product_id = ?
                  AND is_active = 1
                  AND qty_used < qty_total
                  AND (
                       COALESCE(end_at, '2999-12-31 23:59:59') >= COALESCE(?, '1000-01-01 00:00:00')
                   AND COALESCE(?, '2999-12-31 23:59:59') >= COALESCE(start_at, '1000-01-01 00:00:00')
                  )";

        if ($ignoreId !== null) $sql .= " AND id <> ?";
        $sql .= " LIMIT 1";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        if ($ignoreId !== null) $stmt->bind_param('issi', $productId, $startAt, $endAt, $ignoreId);
        else $stmt->bind_param('iss', $productId, $startAt, $endAt);

        $stmt->execute();
        $res = $stmt->get_result();
        $exists = $res && $res->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    public function create(array $d): int
    {
        $db = $this->db();
        $sql = "INSERT INTO product_discounts
                (product_id, discount_type, discount_value, qty_total, qty_used, start_at, end_at, is_active, created_by, created_at, updated_at)
                VALUES (?, ?, ?, ?, 0, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        $productId = (int)$d['product_id'];
        $type      = (string)$d['discount_type'];
        $value     = (float)$d['discount_value'];
        $qtyTotal  = (int)$d['qty_total'];
        $startAt   = $d['start_at']; // null|string
        $endAt     = $d['end_at'];   // null|string
        $isActive  = (int)$d['is_active'];
        $createdBy = (int)($d['created_by'] ?? 0);

        // i s d i s s i i
        $stmt->bind_param('isdissii', $productId, $type, $value, $qtyTotal, $startAt, $endAt, $isActive, $createdBy);

        $stmt->execute();
        $id = (int)$stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function update(int $id, array $d): bool
    {
        $db = $this->db();
        $sql = "UPDATE product_discounts SET
                    product_id=?,
                    discount_type=?,
                    discount_value=?,
                    qty_total=?,
                    start_at=?,
                    end_at=?,
                    is_active=?,
                    updated_at=NOW()
                WHERE id=?
                LIMIT 1";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        $productId = (int)$d['product_id'];
        $type      = (string)$d['discount_type'];
        $value     = (float)$d['discount_value'];
        $qtyTotal  = (int)$d['qty_total'];
        $startAt   = $d['start_at'];
        $endAt     = $d['end_at'];
        $isActive  = (int)$d['is_active'];

        // i s d i s s i i
        $stmt->bind_param('isdissii', $productId, $type, $value, $qtyTotal, $startAt, $endAt, $isActive, $id);

        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }

    public function delete(int $id): bool
    {
        $db = $this->db();
        $sql = "DELETE FROM product_discounts WHERE id=? LIMIT 1";
        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        $stmt->close();
        return $ok;
    }
}
