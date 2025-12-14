<?php
// app/models/ProductOption.php

class ProductOption
{
    protected function db(): mysqli
    {
        return db();
    }

    private function normalizeInputType(string $t): string
    {
        $t = strtolower(trim($t));
        return in_array($t, ['select','radio','checkbox'], true) ? $t : 'checkbox';
    }

    private function normalizePriceType(string $t): string
    {
        $t = strtolower(trim($t));
        return in_array($t, ['fixed','percent'], true) ? $t : 'fixed';
    }

    private function bindParams(mysqli_stmt $stmt, string $types, array $params): void
    {
        $refs = [];
        foreach ($params as $k => $v) $refs[$k] = &$params[$k];
        array_unshift($refs, $types);
        call_user_func_array([$stmt, 'bind_param'], $refs);
    }

    /* ========================= LIST OPTIONS FOR ADMIN ========================= */

    public function getGroupsByProduct(int $productId): array
    {
        $db = $this->db();
        $sql = "SELECT id, product_id, name, input_type, min_select, max_select, is_required,
                       sort_order, is_active, created_at, updated_at
                FROM product_option_groups
                WHERE product_id = ? AND is_active = 1
                ORDER BY sort_order ASC, id ASC";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: ".$db->error);

        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $res  = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        foreach ($rows as &$g) {
            $g['id']          = (int)$g['id'];
            $g['product_id']  = (int)$g['product_id'];
            $g['name']        = (string)$g['name'];
            $g['input_type']  = $this->normalizeInputType((string)$g['input_type']);
            $g['min_select']  = (int)$g['min_select'];
            $g['max_select']  = (int)$g['max_select']; // 0 = unlimited (checkbox)
            $g['is_required'] = (int)$g['is_required'];
            $g['sort_order']  = (int)$g['sort_order'];
            $g['is_active']   = (int)$g['is_active'];
        }

        return $rows;
    }

    public function getValuesByGroupIds(array $groupIds): array
    {
        $groupIds = array_values(array_unique(array_map('intval', $groupIds)));
        $groupIds = array_values(array_filter($groupIds, fn($x) => $x > 0));
        if (empty($groupIds)) return [];

        $db = $this->db();

        $placeholders = implode(',', array_fill(0, count($groupIds), '?'));
        $types = str_repeat('i', count($groupIds));

        $sql = "SELECT id, group_id, label, price_type, price_value, sort_order, is_active, created_at, updated_at
                FROM product_option_values
                WHERE group_id IN ($placeholders) AND is_active = 1
                ORDER BY group_id ASC, sort_order ASC, id ASC";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: ".$db->error);

        $this->bindParams($stmt, $types, $groupIds);

        $stmt->execute();
        $res  = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        $map = [];
        foreach ($rows as $r) {
            $gid = (int)$r['group_id'];
            if (!isset($map[$gid])) $map[$gid] = [];
            $map[$gid][] = [
                'id'         => (int)$r['id'],
                'group_id'   => $gid,
                'label'      => (string)$r['label'],
                'price_type' => $this->normalizePriceType((string)$r['price_type']),
                'price_value'=> (float)$r['price_value'],
                'sort_order' => (int)$r['sort_order'],
                'is_active'  => (int)$r['is_active'],
            ];
        }
        return $map;
    }

    public function getOptionsForProduct(int $productId): array
    {
        $groups   = $this->getGroupsByProduct($productId);
        $groupIds = array_map(fn($g) => (int)$g['id'], $groups);
        $values   = $this->getValuesByGroupIds($groupIds);

        foreach ($groups as &$g) {
            $gid = (int)$g['id'];
            $g['values'] = $values[$gid] ?? [];
        }
        return $groups;
    }

    public function getGroupsWithValues(int $productId, bool $onlyActive = true): array
    {
        $db = db();

        // 1) ambil groups
        $sqlGroups = "
            SELECT
                g.id, g.product_id, g.name, g.input_type,
                g.min_select, g.max_select, g.sort_order,
                g.is_required, g.is_active
            FROM product_option_groups g
            WHERE g.product_id = ?
        " . ($onlyActive ? " AND g.is_active = 1 " : "") . "
            ORDER BY g.sort_order, g.id
        ";

        $stmt = $db->prepare($sqlGroups);
        if (!$stmt) return [];

        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $res = $stmt->get_result();

        $groups = [];
        $groupIds = [];

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $row['id'] = (int)$row['id'];
                $row['product_id'] = (int)$row['product_id'];
                $row['min_select'] = (int)($row['min_select'] ?? 0);
                $row['max_select'] = (int)($row['max_select'] ?? 0);
                $row['sort_order'] = (int)($row['sort_order'] ?? 0);
                $row['is_required'] = (int)($row['is_required'] ?? 0);
                $row['is_active'] = (int)($row['is_active'] ?? 0);
                $row['values'] = [];

                $groups[$row['id']] = $row;
                $groupIds[] = $row['id'];
            }
        }

        $stmt->close();

        if (empty($groupIds)) {
            return [];
        }

        // 2) ambil values untuk semua group di atas
        $placeholders = implode(',', array_fill(0, count($groupIds), '?'));
        $types = str_repeat('i', count($groupIds));

        $sqlValues = "
            SELECT
                v.id, v.group_id, v.label, v.price_type, v.price_value,
                v.sort_order, v.is_active
            FROM product_option_values v
            WHERE v.group_id IN ($placeholders)
        " . ($onlyActive ? " AND v.is_active = 1 " : "") . "
            ORDER BY v.sort_order, v.id
        ";

        $stmt2 = $db->prepare($sqlValues);
        if (!$stmt2) {
            // tetep balikin groups tanpa values biar nggak blank total
            return array_values($groups);
        }

        // bind_param butuh variadic by reference
        $params = [];
        $params[] = & $types;
        foreach ($groupIds as $k => $gid) {
            $params[] = & $groupIds[$k];
        }
        call_user_func_array([$stmt2, 'bind_param'], $params);

        $stmt2->execute();
        $res2 = $stmt2->get_result();

        if ($res2) {
            while ($v = $res2->fetch_assoc()) {
                $v['id'] = (int)$v['id'];
                $v['group_id'] = (int)$v['group_id'];
                $v['sort_order'] = (int)($v['sort_order'] ?? 0);
                $v['is_active'] = (int)($v['is_active'] ?? 0);
                $v['price_value'] = (float)($v['price_value'] ?? 0);

                $gid = $v['group_id'];
                if (isset($groups[$gid])) {
                    $groups[$gid]['values'][] = $v;
                }
            }
        }

        $stmt2->close();

        // balikin dalam bentuk list (bukan map)
        return array_values($groups);
    }


    /* ========================= CRUD GROUP ========================= */

    public function createGroup(array $d): int
    {
        $db = $this->db();

        $productId  = (int)($d['product_id'] ?? 0);
        $name       = trim((string)($d['name'] ?? ''));
        $inputType  = $this->normalizeInputType((string)($d['input_type'] ?? 'checkbox'));
        $minSelect  = max(0, (int)($d['min_select'] ?? 0));
        $maxSelect  = max(0, (int)($d['max_select'] ?? 0)); // 0 = unlimited (checkbox)
        $isRequired = (int)($d['is_required'] ?? 0);
        $sortOrder  = max(0, (int)($d['sort_order'] ?? 0));
        $isActive   = (int)($d['is_active'] ?? 1);

        if ($productId <= 0) throw new Exception("product_id tidak valid.");
        if ($name === '') throw new Exception("Nama group wajib diisi.");

        // normalize rules for select/radio
        if ($inputType === 'select' || $inputType === 'radio') {
            $maxSelect = 1;
            $minSelect = $isRequired ? 1 : 0;
        } else {
            // checkbox
            if ($isRequired && $minSelect < 1) $minSelect = 1;
            if ($maxSelect > 0 && $maxSelect < $minSelect) $maxSelect = $minSelect;
        }

        $sql = "INSERT INTO product_option_groups
                (product_id, name, input_type, min_select, max_select, is_required, sort_order, is_active, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: ".$db->error);

        $stmt->bind_param(
            'issiiiii',
            $productId,
            $name,
            $inputType,
            $minSelect,
            $maxSelect,
            $isRequired,
            $sortOrder,
            $isActive
        );

        $stmt->execute();
        $id = (int)$stmt->insert_id;
        $stmt->close();
        return $id;
    }

    public function updateGroup(int $id, array $d): bool
    {
        $db = $this->db();

        $id         = (int)$id;
        $name       = trim((string)($d['name'] ?? ''));
        $inputType  = $this->normalizeInputType((string)($d['input_type'] ?? 'checkbox'));
        $minSelect  = max(0, (int)($d['min_select'] ?? 0));
        $maxSelect  = max(0, (int)($d['max_select'] ?? 0));
        $isRequired = (int)($d['is_required'] ?? 0);
        $sortOrder  = max(0, (int)($d['sort_order'] ?? 0));
        $isActive   = (int)($d['is_active'] ?? 1);

        if ($id <= 0) throw new Exception("id group tidak valid.");
        if ($name === '') throw new Exception("Nama group wajib diisi.");

        if ($inputType === 'select' || $inputType === 'radio') {
            $maxSelect = 1;
            $minSelect = $isRequired ? 1 : 0;
        } else {
            if ($isRequired && $minSelect < 1) $minSelect = 1;
            if ($maxSelect > 0 && $maxSelect < $minSelect) $maxSelect = $minSelect;
        }

        $sql = "UPDATE product_option_groups SET
                    name = ?,
                    input_type = ?,
                    min_select = ?,
                    max_select = ?,
                    is_required = ?,
                    sort_order = ?,
                    is_active = ?,
                    updated_at = NOW()
                WHERE id = ?
                LIMIT 1";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: ".$db->error);

        $stmt->bind_param('ssiiiiii', $name, $inputType, $minSelect, $maxSelect, $isRequired, $sortOrder, $isActive, $id);
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }

    public function deleteGroup(int $id): bool
    {
        $db = $this->db();
        $id = (int)$id;

        // hapus values dulu biar gak nyisain orphan
        $stmt = $db->prepare("DELETE FROM product_option_values WHERE group_id=?");
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }

        $stmt2 = $db->prepare("DELETE FROM product_option_groups WHERE id=? LIMIT 1");
        if (!$stmt2) throw new Exception("Prepare failed: ".$db->error);

        $stmt2->bind_param('i', $id);
        $stmt2->execute();
        $ok = $stmt2->affected_rows > 0;
        $stmt2->close();
        return $ok;
    }

    /* ========================= CRUD VALUE ========================= */

    public function createValue(array $d): int
    {
        $db = $this->db();

        $groupId   = (int)($d['group_id'] ?? 0);
        $label     = trim((string)($d['label'] ?? ''));
        $priceType = $this->normalizePriceType((string)($d['price_type'] ?? 'fixed'));
        $priceVal  = (float)($d['price_value'] ?? 0);
        $sortOrder = max(0, (int)($d['sort_order'] ?? 0));
        $isActive  = (int)($d['is_active'] ?? 1);

        if ($groupId <= 0) throw new Exception("group_id tidak valid.");
        if ($label === '') throw new Exception("Label opsi wajib diisi.");
        if ($priceVal < 0) $priceVal = 0;

        $sql = "INSERT INTO product_option_values
                (group_id, label, price_type, price_value, sort_order, is_active, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: ".$db->error);

        $stmt->bind_param('issdii', $groupId, $label, $priceType, $priceVal, $sortOrder, $isActive);
        $stmt->execute();
        $id = (int)$stmt->insert_id;
        $stmt->close();
        return $id;
    }

    public function updateValue(int $id, array $d): bool
    {
        $db = $this->db();

        $id        = (int)$id;
        $label     = trim((string)($d['label'] ?? ''));
        $priceType = $this->normalizePriceType((string)($d['price_type'] ?? 'fixed'));
        $priceVal  = (float)($d['price_value'] ?? 0);
        $sortOrder = max(0, (int)($d['sort_order'] ?? 0));
        $isActive  = (int)($d['is_active'] ?? 1);

        if ($id <= 0) throw new Exception("id value tidak valid.");
        if ($label === '') throw new Exception("Label opsi wajib diisi.");
        if ($priceVal < 0) $priceVal = 0;

        $sql = "UPDATE product_option_values SET
                    label = ?,
                    price_type = ?,
                    price_value = ?,
                    sort_order = ?,
                    is_active = ?,
                    updated_at = NOW()
                WHERE id = ?
                LIMIT 1";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: ".$db->error);

        $stmt->bind_param('ssdiii', $label, $priceType, $priceVal, $sortOrder, $isActive, $id);
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }

    public function deleteValue(int $id): bool
    {
        $db = $this->db();
        $id = (int)$id;

        $stmt = $db->prepare("DELETE FROM product_option_values WHERE id=? LIMIT 1");
        if (!$stmt) throw new Exception("Prepare failed: ".$db->error);

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        $stmt->close();
        return $ok;
    }

    /* ========================= PRICING VALIDATION (ROBUST) ========================= */

    public function getSelectedValuesForProduct(int $productId, array $selectedValueIds): array
    {
        $selectedValueIds = array_values(array_unique(array_map('intval', $selectedValueIds)));
        $selectedValueIds = array_values(array_filter($selectedValueIds, fn($x) => $x > 0));
        if (empty($selectedValueIds)) return [];

        $db = $this->db();

        $placeholders = implode(',', array_fill(0, count($selectedValueIds), '?'));
        $types = 'i' . str_repeat('i', count($selectedValueIds));

        $sql = "SELECT v.id, v.group_id, v.label, v.price_type, v.price_value
                FROM product_option_values v
                JOIN product_option_groups g ON g.id = v.group_id
                WHERE g.product_id = ?
                  AND g.is_active = 1
                  AND v.is_active = 1
                  AND v.id IN ($placeholders)";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: ".$db->error);

        $params = array_merge([$productId], $selectedValueIds);
        $this->bindParams($stmt, $types, $params);

        $stmt->execute();
        $res  = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        $map = [];
        foreach ($rows as $r) {
            $id = (int)$r['id'];
            $map[$id] = [
                'id'          => $id,
                'group_id'    => (int)$r['group_id'],
                'label'       => (string)$r['label'],
                'price_type'  => $this->normalizePriceType((string)$r['price_type']),
                'price_value' => (float)$r['price_value'],
            ];
        }
        return $map;
    }

    public function validateSelectionsForProduct(int $productId, array $selectedValueIds): array
    {
        $groups = $this->getGroupsByProduct($productId);

        $selectedValueIds = array_values(array_unique(array_map('intval', $selectedValueIds)));
        $selectedValueIds = array_values(array_filter($selectedValueIds, fn($x) => $x > 0));

        $valueDetail = $this->getSelectedValuesForProduct($productId, $selectedValueIds);

        $errors = [];

        // groupId -> selected IDs
        $selMap = [];
        foreach ($valueDetail as $vid => $v) {
            $gid = (int)$v['group_id'];
            if (!isset($selMap[$gid])) $selMap[$gid] = [];
            $selMap[$gid][] = (int)$vid;
        }

        // invalid IDs
        $invalid = [];
        foreach ($selectedValueIds as $vid) {
            if (!isset($valueDetail[$vid])) $invalid[] = $vid;
        }
        if (!empty($invalid)) {
            $errors[] = "Opsi tidak valid untuk produk ini: " . implode(',', $invalid);
        }

        foreach ($groups as $g) {
            $gid  = (int)$g['id'];
            $name = (string)$g['name'];

            $type = $this->normalizeInputType((string)$g['input_type']);
            $isRequired = ((int)$g['is_required']) === 1;

            $minDb = (int)$g['min_select'];
            $maxDb = (int)$g['max_select']; // 0 = unlimited (checkbox)

            $count = isset($selMap[$gid]) ? count($selMap[$gid]) : 0;

            // “required” bisa datang dari is_required ATAU min_select > 0
            $minRequired = max($minDb, $isRequired ? 1 : 0);

            if ($type === 'select' || $type === 'radio') {
                // untuk select/radio max selalu 1
                $max = 1;

                // untuk select/radio, min pakai minRequired (jadi kalau min_select=1 tetap wajib)
                $min = min($minRequired, 1);

                if ($count < $min) {
                    $errors[] = "Group '{$name}' minimal pilih {$min}.";
                }
                if ($count > $max) {
                    $errors[] = "Group '{$name}' hanya boleh pilih 1.";
                }
            } else {
                // checkbox
                $min = $minRequired;
                $max = ($maxDb > 0) ? $maxDb : null;

                if ($count < $min) {
                    $errors[] = "Group '{$name}' minimal pilih {$min}.";
                }
                if ($max !== null && $count > $max) {
                    $errors[] = "Group '{$name}' maksimal pilih {$max}.";
                }
            }
        }

        return [
            'ok'           => empty($errors),
            'errors'       => $errors,
            'groups'       => $groups,
            'selected_map' => $selMap,
            'value_detail' => $valueDetail,
        ];
    }

}
