<?php
// app/models/PageContent.php

class PageContent
{
    protected function db()
    {
        return db(); // mysqli
    }

    private function hasItemKey(): bool
    {
        $db = $this->db();
        $res = $db->query("SHOW COLUMNS FROM page_contents LIKE 'item_key'");
        return ($res && $res->num_rows > 0);
    }

    /* =========================
       BACKWARD (default item)
       ========================= */
    public function getValue(string $pageSlug, string $section, string $field, $default = '')
    {
        $db = $this->db();

        if ($this->hasItemKey()) {
            $sql = "SELECT value FROM page_contents
                    WHERE page_slug=? AND section=? AND item_key='default' AND field=?
                    LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('sss', $pageSlug, $section, $field);
        } else {
            $sql = "SELECT value FROM page_contents
                    WHERE page_slug=? AND section=? AND field=?
                    LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('sss', $pageSlug, $section, $field);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ? $row['value'] : $default;
    }

    public function saveFields(string $pageSlug, string $section, array $data): void
    {
        $db = $this->db();

        if ($this->hasItemKey()) {
            $sql = "INSERT INTO page_contents (page_slug, section, item_key, field, value)
                    VALUES (?, ?, 'default', ?, ?)
                    ON DUPLICATE KEY UPDATE value = VALUES(value)";
        } else {
            $sql = "INSERT INTO page_contents (page_slug, section, field, value)
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE value = VALUES(value)";
        }

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        foreach ($data as $field => $value) {
            $field = (string)$field;
            $value = (string)$value;

            if ($this->hasItemKey()) {
                $stmt->bind_param('ssss', $pageSlug, $section, $field, $value);
            } else {
                $stmt->bind_param('ssss', $pageSlug, $section, $field, $value);
            }

            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
        }

        $stmt->close();
    }

    /* =========================
       MULTI ITEM (hero slides)
       ========================= */

    public function getSectionItems(string $pageSlug, string $section): array
    {
        $db = $this->db();

        if (!$this->hasItemKey()) {
            // fallback: anggap cuma 1 item
            $sql = "SELECT field, value FROM page_contents
                    WHERE page_slug=? AND section=?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('ss', $pageSlug, $section);
            $stmt->execute();
            $res = $stmt->get_result();

            $item = ['item_key' => 'default'];
            while ($row = $res->fetch_assoc()) {
                $item[$row['field']] = $row['value'];
            }
            $stmt->close();
            return [$item];
        }

        $sql = "SELECT item_key, field, value
                FROM page_contents
                WHERE page_slug=? AND section=?
                ORDER BY item_key ASC";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ss', $pageSlug, $section);
        $stmt->execute();
        $res = $stmt->get_result();

        $items = [];
        while ($row = $res->fetch_assoc()) {
            $k = $row['item_key'];
            if (!isset($items[$k])) $items[$k] = ['item_key' => $k];
            $items[$k][$row['field']] = $row['value'];
        }
        $stmt->close();

        return array_values($items);
    }

    public function saveItemFields(string $pageSlug, string $section, string $itemKey, array $data): void
    {
        $db = $this->db();

        if (!$this->hasItemKey()) {
            // kalau belum upgrade, jatuhin ke default (cuma 1)
            $this->saveFields($pageSlug, $section, $data);
            return;
        }

        $sql = "INSERT INTO page_contents (page_slug, section, item_key, field, value)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE value = VALUES(value)";
        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        foreach ($data as $field => $value) {
            $field = (string)$field;
            $value = (string)$value;
            $stmt->bind_param('sssss', $pageSlug, $section, $itemKey, $field, $value);
            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
        }

        $stmt->close();
    }

    public function deleteItem(string $pageSlug, string $section, string $itemKey): void
    {
        if (!$this->hasItemKey()) return;

        $db = $this->db();
        $sql = "DELETE FROM page_contents WHERE page_slug=? AND section=? AND item_key=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('sss', $pageSlug, $section, $itemKey);
        $stmt->execute();
        $stmt->close();
    }
}
