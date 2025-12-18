# EventPrint - Installation Guide

## 📋 System Requirements

### Minimum
- **PHP**: 8.0 atau lebih tinggi
- **MySQL**: 5.7+ atau MariaDB 10.4+
- **Apache**: 2.4+ dengan mod_rewrite enabled
- **Storage**: 500MB free space
- **RAM**: 512MB minimum

### Recommended
- **PHP**: 8.2
- **MySQL**: 8.0 atau MariaDB 10.6+
- **Apache**: 2.4+
- **Storage**: 2GB (untuk uploads)
- **RAM**: 1GB+

### PHP Extensions Required
```
- mysqli
- gd or imagick
- fileinfo
- mbstring
- json
- session
```

---

## 🚀 Installation Steps

### Option A: XAMPP (Subfolder Setup)

#### 1. Download & Install XAMPP
```
Download dari: https://www.apachefriends.org/
Install ke: C:\xampp\
```

#### 2. Copy Project ke htdocs
```bash
# Dari folder sumber:
D:\eventprint\eventprint backend\

# Copy ke:
C:\xampp\htdocs\eventprint-backend\
```

Atau buat symlink (Windows, run as Administrator):
```cmd
mklink /D "C:\xampp\htdocs\eventprint-backend" "D:\eventprint\eventprint backend"
```

#### 3. Konfigurasi RewriteBase

**Edit file**: `C:\xampp\htdocs\eventprint-backend\public\.htaccess`

```apache
# Line 3, ubah:
RewriteBase /eventprint-backend/public/
```

#### 4. Start XAMPP Services
- Buka XAMPP Control Panel
- Start **Apache**
- Start **MySQL**

#### 5. Create Database

**Akses phpMyAdmin**: http://localhost/phpmyadmin

```sql
-- 1. Buat database baru
CREATE DATABASE eventprint CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Klik database "eventprint"
-- 3. Tab "Import"
-- 4. Choose file: C:\xampp\htdocs\eventprint-backend\eventprint.sql
-- 5. Klik "Go"

-- 6. Verify (harus ada 13+ tabel)
SHOW TABLES;
```

#### 6. Konfigurasi App

**Edit**: `app/config/app.php`
```php
<?php
return [
    'name'      => 'EventPrint',
    'base_url'  => 'http://localhost/eventprint-backend/public',
    'env'       => 'local',
    'debug'     => true,
];
```

**Edit**: `app/config/db.php`
```php
<?php
$host = 'localhost';
$user = 'root';
$pass = '';           // XAMPP default kosong
$db   = 'eventprint';
```

#### 7. Set Upload Directory Permission

**Windows**: Klik kanan folder `public/uploads/` → Properties → Security → Edit → Users → Full Control

**Linux/Mac**:
```bash
chmod -R 777 public/uploads/
```

#### 8. Test Installation

**Public Homepage**:
```
http://localhost/eventprint-backend/public/
```
Expected: Homepage muncul (bisa masih kosong/mock data)

**Admin Login**:
```
http://localhost/eventprint-backend/public/admin/login
```

**Default Credentials**:
- Email: `superadmin1@example.com`
- Password: `password123`

---

### Option B: Virtual Host (Production-like)

#### 1. Konfigurasi Virtual Host

**Edit**: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

Tambahkan di akhir file:
```apache
<VirtualHost *:80>
    ServerName eventprint.local
    ServerAlias www.eventprint.local
    DocumentRoot "D:/eventprint/eventprint backend/public"
    
    <Directory "D:/eventprint/eventprint backend/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/eventprint-error.log"
    CustomLog "logs/eventprint-access.log" common
</VirtualHost>
```

#### 2. Edit Hosts File

**Windows**: Edit `C:\Windows\System32\drivers\etc\hosts` (as Administrator)

Tambahkan:
```
127.0.0.1    eventprint.local
127.0.0.1    www.eventprint.local
```

**Mac/Linux**: Edit `/etc/hosts`

#### 3. Update RewriteBase

**Edit**: `public/.htaccess`

```apache
# Comment atau hapus RewriteBase:
# RewriteBase /eventprint-backend/public/
```

#### 4. Update App Config

**Edit**: `app/config/app.php`
```php
'base_url' => 'http://eventprint.local',
```

#### 5. Restart Apache

XAMPP Control Panel → Apache → Stop → Start

#### 6. Test Virtual Host

```
http://eventprint.local/
http://eventprint.local/admin/login
```

---

## 🗄️ Database Schema Overview

### Core Tables

| Table | Rows (Initial) | Purpose |
|-------|----------------|---------|
| `users` | 4 | Admin login (super_admin, admin) |
| `products` | 13 | Catalog produk |
| `product_categories` | 8 | Kategori produk |
| `product_option_groups` | 4 | Group opsi (ukuran, bahan, dll) |
| `product_option_values` | 8 | Value opsi |
| `product_discounts` | 8 | Diskon produk |
| `hero_slides` | 1 | Banner homepage |
| `posts` | 3 | Blog/artikel |
| `our_store` | 2 | Portfolio/toko |
| `contact_messages` | 2 | Inbox contact form |
| `settings` | 1 | Global site settings |
| `page_contents` | 50 | Dynamic content (EAV) |

### Optional (Create Later)
- `testimonials` (P0) - untuk homepage "Kata Mereka"
- `post_categories` (P1) - untuk filter blog
- `product_images` (P0) - untuk gallery detail produk

---

## 🔧 Troubleshooting

### Error: "404 Not Found" di Semua Route

**Penyebab**: mod_rewrite tidak aktif atau .htaccess tidak dibaca

**Solusi**:
```apache
# 1. Edit: C:\xampp\apache\conf\httpd.conf
# 2. Cari dan uncomment (hapus #):
LoadModule rewrite_module modules/mod_rewrite.so

# 3. Cari AllowOverride None, ubah jadi:
AllowOverride All

# 4. Restart Apache
```

### Error: "Access Denied for user 'root'@'localhost'"

**Penyebab**: Password MySQL salah

**Solusi**:
```php
// Cek password MySQL di XAMPP:
// Biasanya kosong ('') untuk fresh install

// Edit app/config/db.php:
$pass = '';  // XAMPP default

// Jika sudah set password:
$pass = 'your_mysql_password';
```

### Error: Gambar Upload Tidak Muncul

**Penyebab**: Path salah atau permission denied

**Solusi**:
```bash
# 1. Cek folder ada:
public/uploads/products/
public/uploads/blog/
public/uploads/our_store/

# 2. Buat manual jika belum ada
mkdir public/uploads/products
mkdir public/uploads/blog
mkdir public/uploads/our_store
mkdir public/uploads/settings

# 3. Set permission (Windows: Full Control, Linux: 777)
chmod -R 777 public/uploads/
```

### Error: PHP Notice/Warning Muncul

**Penyebab**: Debug mode aktif

**Solusi Sementara**:
```php
// Edit app/config/app.php:
'debug' => false,  // matikan warning
```

**Solusi Permanen**: Fix kode yang warning (undefined index, dll).

### Error: Session "Already Started"

**Penyebab**: Session start ganda

**Solusi**: Sudah fixed di `public/index.php` line 18-22 (conditional session).

---

## 🔐 Security Checklist

### Development (Local)
- [x] Debug mode ON (`debug => true`)
- [x] Display errors ON
- [x] Default password OK untuk testing
- [ ] HTTPS optional

### Production (Live Server)
- [ ] Debug mode OFF (`debug => false`)
- [ ] Display errors OFF
- [ ] Ganti password default users
- [ ] HTTPS WAJIB (SSL certificate)
- [ ] File permission: 644 (files), 755 (folders)
- [ ] Upload folder: scan anti-malware berkala
- [ ] Backup database harian

---

## 📦 Seed Data (Optional)

Jika ingin tambah data dummy untuk testing:

```sql
-- Testimonials (perlu buat tabel dulu, lihat implementation_plan.md)
INSERT INTO `testimonials` (`name`, `position`, `rating`, `message`, `sort_order`) VALUES
('Budi Santoso', 'Owner Toko ABC', 5, 'Kualitas cetak bagus!', 1),
('Siti Nurhaliza', 'Event Organizer', 5, 'Pelayanan cepat.', 2),
('Andi Wijaya', 'Pengusaha UMKM', 4, 'Recommend!', 3);

-- Update settings
UPDATE `settings` SET
  `operating_hours` = 'Senin - Sabtu: 08:00 - 17:00',
  `whatsapp` = '62812345678'
WHERE `id` = 1;

-- Mark products as featured
UPDATE `products` SET `is_featured` = 1 WHERE `id` IN (2, 4, 7, 8) LIMIT 4;
```

---

## 📞 Support

Jika ada masalah instalasi:

1. Cek error log: `C:\xampp\apache\logs\error.log`
2. Cek PHP info: Buat file `info.php` di `public/`:
```php
<?php phpinfo(); ?>
```
Akses: http://localhost/eventprint-backend/public/info.php

3. Verifikasi requirements:
```bash
php -v          # Cek versi PHP
php -m          # Cek extensions
mysql --version # Cek MySQL
```

---

## ✅ Post-Installation Checklist

- [ ] Homepage public bisa diakses
- [ ] Admin login works
- [ ] Dashboard muncul tanpa error
- [ ] Upload test image di Settings → berhasil
- [ ] CRUD test: buat 1 product baru → berhasil
- [ ] Public products page: produk muncul
- [ ] Contact form: submit → save to DB
- [ ] No PHP notice/warning di screen

**Jika semua ✅, instalasi SUKSES!**

---

**Last Updated**: 2025-12-18  
**Version**: 1.0
