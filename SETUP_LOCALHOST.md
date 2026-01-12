# Setup Localhost EventPrint

Berikut adalah panduan untuk menjalankan project ini di Localhost (XAMPP).

## 1. Persiapan Database
Project ini mendeteksi environment `local` dan akan mencoba connect ke database dengan setting default XAMPP:
- **Host**: `localhost`
- **User**: `root`
- **Pass**: *(kosong)*
- **DB Name**: `eventprint`

### Langkah-langkah:
1. Buka [phpMyAdmin](http://localhost/phpmyadmin).
2. Buat database baru dengan nama: **`eventprint`**.
3. Import file database. Gunakan file migrasi yang tersedia di folder `migrations/`.
   - Import **`migrations/migration_p0_simple.sql`** (Schema utama).
   - Jika membutuhkan data dummy tambahan atau update terbaru, cek folder `database/migrations`.

## 2. Konfigurasi (Otomatis)
Saya telah memodifikasi file konfigurasi agar otomatis mendeteksi jika dijalankan di localhost.
- **`.htaccess`**: Sudah diperbaiki agar TIDAK me-redirect ke domain production saat di localhost.
- **`app/config/app.php`**: Base URL otomatis menjadi `http://localhost/eventprint`.
- **`app/config/db.php`**: Credential DB otomatis pindah ke `root` saat di localhost.

## 3. Menjalankan Project
1. Pastikan folder project berada di `C:\xampp\htdocs\eventprint`.
2. Nyalakan Apache dan MySQL di XAMPP Control Panel.
3. Buka browser dan akses:
   [http://localhost/eventprint](http://localhost/eventprint)

## 4. Troubleshooting
Jika tampilan berantakan (CSS/JS 404):
- Pastikan folder project bernama `eventprint`.
- Jika nama folder berbeda, sesuaikan `base_url` di `app/config/app.php` (Line 4).

Jika error Database:
- Pastikan nama database di phpMyAdmin adalah `eventprint`.
- Jika password mysql user `root` Anda tidak kosong, edit file `app/config/db.php` line 7-8.
