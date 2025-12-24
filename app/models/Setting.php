<?php
// app/models/Setting.php

class Setting
{
    protected mysqli $db;

    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Ambil row settings pertama (global config).
     */
    public function getAll(): array
    {
        $sql = "SELECT * FROM settings ORDER BY id ASC LIMIT 1";
        $res = $this->db->query($sql);

        if ($res && $row = $res->fetch_assoc()) {
            return $row;
        }

        return [];
    }

    /**
     * Insert/update semua field settings dalam satu kali jalan.
     * Kalau belum ada row, INSERT. Kalau sudah ada, UPDATE by id.
     */
    public function saveAll(array $data): bool
    {
        // field yang diizinkan (sesuai struktur tabel)
        $fields = [
            'site_name',
            'site_tagline',
            'logo',
            'phone',
            'email',
            'address',
            'maps_link',
            'facebook',
            'instagram',
            'tiktok',
            'twitter',
            'youtube',
            'linkedin',
            'whatsapp',
            'gmaps_embed',
            'operating_hours',
            'sales_contacts',
        ];

        // sanitasi & ambil hanya field yang ada di $data
        $clean = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $clean[$field] = $this->db->real_escape_string((string) $data[$field]);
            }
        }

        if (empty($clean)) {
            return true; // nothing to save
        }

        // cek apakah sudah ada row settings
        $sqlCheck = "SELECT id FROM settings ORDER BY id ASC LIMIT 1";
        $res = $this->db->query($sqlCheck);
        $row = ($res && $res->num_rows > 0) ? $res->fetch_assoc() : null;

        if ($row) {
            // ================= UPDATE =================
            $id = (int) $row['id'];
            $setParts = [];

            foreach ($clean as $field => $value) {
                $setParts[] = "`$field` = '$value'";
            }

            $sql = "UPDATE settings SET " . implode(', ', $setParts) . " WHERE id = {$id}";
        } else {
            // ================= INSERT =================
            $columns = [];
            $values = [];

            foreach ($clean as $field => $value) {
                $columns[] = "`$field`";
                $values[] = "'$value'";
            }

            $sql = "INSERT INTO settings (" . implode(', ', $columns) . ")
                    VALUES (" . implode(', ', $values) . ")";
        }

        return (bool) $this->db->query($sql);
    }
}
