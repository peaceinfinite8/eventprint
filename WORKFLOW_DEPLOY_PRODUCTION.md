---
description: Panduan deploy update Blog dan Database ke Production (Hosting)
---

# Panduan Update ke Production (Live Server)

Ikuti langkah ini untuk memindahkan fitur Blog baru (Featured, Grid Large/Small) ke website asli tanpa error.

## 1. Backup (Wajib)
Selalu backup database dan file di hosting sebelum melakukan perubahan besar.

## 2. Upload File Code
Upload/Timpa (Overwrite) folder-folder berikut dari Local ke Hosting (menggunakan File Manager atau FTP):

*   **`app/controllers/`** (Terutama `BlogController.php` dan `BlogPublicController.php`)
*   **`app/models/`** (Terutama `Post.php`)
*   **`views/admin/blog/`** (Semua file di dalamnya)
*   **`views/frontend/pages/`** (`blog.php`)
*   **`assets/frontend/js/render/`** (`renderBlog.js` dan `renderBlogDetail.js`)
*   **`deploy_db_update.php`** (File script baru di root folder)

> **PENTING:** Jangan menimpa file `app/config/db.php` atau `app/config/app.php` di hosting jika konfigurasinya berbeda dengan local (misal password database berbeda).

## 3. Sinkronisasi Database (Otomatis)
Setelah file ter-upload, kita harus mengupdate struktur database di hosting agar memiliki kolom `is_featured`, `post_type`, dll.

1.  Buka browser dan akses alamat website Anda ditambah `/deploy_db_update.php`.
    *   Contoh: `https://eventprint.id/deploy_db_update.php`
2.  Anda akan melihat laporan status update database:
    *   `[UPDATE] Menambahkan kolom 'is_featured'... SUCCESS`
    *   ATAU `[OK] Kolom ... sudah ada.`
3.  Pastikan pesan terakhir adalah **DATABASE SYNC COMPLETED SUCCESSFULLY**.

## 4. Bersih-bersih
1.  **Hapus file `deploy_db_update.php`** dari hosting demi keamanan.
2.  Coba akses halaman admin dan edit salah satu artikel untuk tes fitur Featured.
3.  Coba akses halaman Blog publik untuk memastikan grid tampil benar.

## 5. Troubleshooting
Jika terjadi error "Unknown column...", berarti langkah nomor 3 belum berhasil dijalankan.
