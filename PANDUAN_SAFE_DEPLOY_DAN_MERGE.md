---
description: Panduan aman deploy ke production jika ada tim lain yang mengedit langsung di server.
---

# Panduan Safe Deploy & Merge (Anti Bentrok)

Dokumen ini adalah skema Penyelamatan Kode saat kondisi:
1.  Anda bekerja di **Local**.
2.  Tim lain bekerja langsung di **Production (Live)**.
3.  Anda ingin update fitur tanpa menghapus kerjaan tim lain.

---

## ⚠️ PERINGATAN KERAS
**JANGAN PERNAH** melakukan "Select All -> Upload -> Overwrite" via FTP/File Manager jika Anda tidak yakin file di server masih 100% sama dengan backup terakhir Anda. Kodingan teman Anda akan hilang permanen!

---

## Langkah 1: Identifikasi File yang Anda Ubah
List file yang baru saja Anda kerjakan. Contoh kasus fitur Blog hari ini:
*   `app/controllers/BlogController.php`
*   `app/models/Post.php`
*   `views/admin/blog/articles.php`

## Langkah 2: "Pull" (Download) File Production
Sebelum upload, **Download dulu** file-file tersebut dari hosting ke folder terpisah di komputer Anda (misal: folder `_production_backup`).
*   Download `app/controllers/BlogController.php` (Versi Live)
*   Simpan di folder `_production_backup/app/controllers/`

## Langkah 3: Bandingkan & Gabung (Merge) di VS Code
Ini langkah kuncinya. Kita akan menyisipkan kode Anda ke dalam kode Live.

1.  Buka VS Code.
2.  Buka file versi **Local** (yang fitur baru).
3.  Buka file versi **Live** (yang dari `_production_backup`).
4.  Klik kanan pada tab file Local -> Pilih **"Select for Compare"**.
5.  Klik kanan pada tab file Live -> Pilih **"Compare with Selected"**.
6.  VS Code akan membelah layar (Split View):
    *   **Kiri**: Kode Anda (Local).
    *   **Kanan**: Kode Tim Lain (Live).
7.  **Analisis Perbedaan**:
    *   Jika perbedaannya hanya fitur Anda (Blok kode yang Anda buat), dan sisi Kanan kosong di bagian itu -> **Aman**.
    *   Jika sisi Kanan memiliki baris kode asing (buatan tim lain) yang tidak ada di Kiri -> **BAHAYA**.
8.  **Cara Menggabungkan (Merging)**:
    *   Copy blok kode fitur baru Anda dari file Local.
    *   Paste ke file Live (di posisi yang benar, jangan menimpa kode tim lain).
    *   Lakukan ini sampai file Live memiliki **Kedua Fitur** (Fitur Anda + Fitur Tim Lain).

## Langkah 4: Validasi & Upload
1.  Simpan file Live yang sudah di-merge tadi (file gabungan).
2.  Sekarang file ini berisi: **Kode Asli Live + Kode Tim Lain + Kode Baru Anda**.
3.  Upload file gabungan ini ke Hosting.

## Skema Database
Untuk database, karena Anda hanya **Menambah Kolom** (`ADD COLUMN`), biasanya aman dijalankan di production meskipun tim lain mengedit data. Script `deploy_db_update.php` yang saya buat sebelumnya bertipe "Non-Destructive" (Hanya menambah jika belum ada, tidak menghapus apa-apa).
*   Upload `deploy_db_update.php`.
*   Jalankan di browser.

---

## Saran Jangka Panjang (Best Practice)
Kondisi "Edit di Production" adalah bom waktu. Segera sarankan tim untuk menggunakan **Git (GitHub/GitLab)**:
1.  Semua orang push ke Git.
2.  Production melakukan `git pull`.
3.  Conflict akan dideteksi otomatis oleh Git, bukan manual seperti cara di atas.
