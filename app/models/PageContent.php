<?php
// app/models/PageContent.php

class PageContent
{
    protected function db()
    {
        return db();
    }

    public function getValue(string $pageSlug, string $section, string $field, $default = '')
    {
        $db = $this->db();

        $sql  = "SELECT value FROM page_contents
                 WHERE page_slug = ? AND section = ? AND field = ?
                 LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('sss', $pageSlug, $section, $field);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ? $row['value'] : $default;
    }

    public function getFields(string $pageSlug, string $section, array $fields): array
    {
        $result = [];
        foreach ($fields as $field) {
            $result[$field] = $this->getValue($pageSlug, $section, $field, '');
        }
        return $result;
    }

    public function saveFields(string $pageSlug, string $section, array $data): void
    {
        $db = $this->db();

        $sql = "INSERT INTO page_contents (page_slug, section, field, value)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE value = VALUES(value)";

        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }

        foreach ($data as $field => $value) {
            $field = (string)$field;
            $value = (string)$value;

            $stmt->bind_param('ssss', $pageSlug, $section, $field, $value);
            $ok = $stmt->execute();
            if (!$ok) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
        }

        $stmt->close();
    }
}
