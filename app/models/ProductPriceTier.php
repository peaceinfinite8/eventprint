<?php
// app/models/ProductPriceTier.php

class ProductPriceTier
{
    protected function db(): mysqli
    {
        return db();
    }

    /**
     * Get all price tiers for a product
     */
    public function getByProduct(int $productId): array
    {
        $db = $this->db();
        // Updated sorting: Min Qty ASC
        $stmt = $db->prepare("
            SELECT * FROM product_price_tiers
            WHERE product_id = ? AND is_active = 1
            ORDER BY qty_min ASC
        ");

        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $r['id'] = (int) $r['id'];
                $r['product_id'] = (int) $r['product_id'];
                $r['qty_min'] = (int) $r['qty_min'];
                $r['qty_max'] = $r['qty_max'] ? (int) $r['qty_max'] : null;
                $r['unit_price'] = (float) $r['unit_price'];
                $r['is_active'] = (int) $r['is_active'];
                $rows[] = $r;
            }
        }
        $stmt->close();
        return $rows;
    }

    /**
     * Create a new price tier
     */
    public function create(array $data): int
    {
        $db = $this->db();

        $productId = (int) ($data['product_id'] ?? 0);
        $qtyMin = (int) ($data['qty_min'] ?? 1);
        $qtyMax = !empty($data['qty_max']) ? (int) $data['qty_max'] : null;
        $unitPrice = (float) ($data['unit_price'] ?? 0);
        $isActive = (int) ($data['is_active'] ?? 1);

        if ($productId <= 0)
            throw new Exception("Invalid Product ID");
        if ($qtyMin < 1)
            $qtyMin = 1;
        if ($qtyMax !== null && $qtyMax < $qtyMin)
            $qtyMax = $qtyMin; // validation

        $sql = "INSERT INTO product_price_tiers (product_id, qty_min, qty_max, unit_price, is_active, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = $db->prepare($sql);
        if (!$stmt)
            throw new Exception("Prepare failed: " . $db->error);

        $stmt->bind_param('iiidi', $productId, $qtyMin, $qtyMax, $unitPrice, $isActive);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    /**
     * Delete a tier
     */
    public function delete(int $id): bool
    {
        $db = $this->db();
        $stmt = $db->prepare("DELETE FROM product_price_tiers WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        $stmt->close();
        return $ok;
    }

    /**
     * Check overlapping (Advanced - optional for now but good practice)
     * For now, we trust admin inputs or handle basic overlap via JS
     */
}
