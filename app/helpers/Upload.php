<?php
// app/helpers/Upload.php

class Upload
{
    /**
     * Upload file gambar ke folder /public/uploads/{$subDir}/
     *
     * @param array  $file       Array $_FILES['...']
     * @param string $subDir     Nama subfolder (misal: 'products', 'blog', 'our_store')
     * @param array  $allowedExt Ekstensi file yang diperbolehkan
     * @param int    $maxSize    Ukuran maksimum (bytes), default 5MB
     *
     * @return string|null  path relatif dari folder public (mis: "uploads/products/file123.jpg")
     */
    public static function image(
        array $file,
        string $subDir,
        array $allowedExt = ['jpg','jpeg','png','webp'],
        int $maxSize = 5242880
    ): ?string {
        // tidak ada file yang diupload
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (!isset($file['tmp_name'], $file['name'])) {
            // data upload nggak valid → anggap gagal
            return null;
        }

        // error upload standar PHP (size terlalu besar, partial, dll)
        if (!empty($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // batas ukuran
        if (!empty($file['size']) && $file['size'] > $maxSize) {
            return null;
        }

        $originalName = $file['name'];
        $tmpPath      = $file['tmp_name'];

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            // ekstensi ditolak
            return null;
        }

        // cek mime kalau fungsi tersedia (tidak fatal kalau nggak ada)
        if (function_exists('mime_content_type')) {
            $mime = @mime_content_type($tmpPath);
            if ($mime !== false && strpos($mime, 'image/') !== 0) {
                // bukan image
                return null;
            }
        }

        // generate nama unik
        try {
            $rand    = bin2hex(random_bytes(4));
        } catch (\Throwable $t) {
            // kalau random_bytes gagal, fallback nama biasa
            $rand = mt_rand(1000, 9999);
        }

        $newName = date('Ymd_His') . '_' . $rand . '.' . $ext;

        // base dir = root project (eventprint/)
        $baseDir   = dirname(__DIR__, 2); // dari app/helpers → naik 2x → /eventprint
        $uploadDir = $baseDir . '/public/uploads/' . trim($subDir, '/');

        if (!is_dir($uploadDir)) {
            // coba bikin folder; kalau gagal, return null, bukan exception
            if (!mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                return null;
            }
        }

        $targetPath = $uploadDir . '/' . $newName;

        if (!move_uploaded_file($tmpPath, $targetPath)) {
            return null;
        }

        // path relatif untuk disimpan ke DB
        return 'uploads/' . trim($subDir, '/') . '/' . $newName;
    }
}
