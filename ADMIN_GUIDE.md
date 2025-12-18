# EventPrint - Admin Guide

Panduan lengkap untuk mengelola konten EventPrint melalui Admin Panel.

---

## 📍 Akses Admin

**URL**: `http://localhost/eventprint-backend/public/admin/login`  
**Default Login**:
- Super Admin: `superadmin1@example.com` / `password123`
- Admin: `admin@example.com` / `password123`

> ⚠️ **WAJIB** ganti password setelah first login di Production!

---

## 👤 User Roles & Permissions

### Super Admin
**Full Access** ke semua modul:
- Products & Categories
- Discounts
- Our Store
- Blog
- Contact Messages
- Hero Slides
- Settings
- Users (manage admin)

### Admin
**Limited Access**:
- Blog (CRUD artikel)
- Contact Messages (read inbox)

**Tidak bisa**:
- Products, Categories, Discounts
- Settings
- Users

---

## 🏠 Dashboard

**URL**: `/admin/dashboard`

Menampilkan statistik:
- Total Products
- Total Blog Posts
- Unread Messages
- Total Categories

---

## 🛍️ Products Management

### List Products

**URL**: `/admin/products`

**Actions**:
- **Add Product**: Tombol hijau kanan atas
- **Edit**: Icon pensil
- **Delete**: Icon trash (soft delete, bisa restore)
- **Options**: Icon gear (kelola opsi produk)

**Columns**:
- Thumbnail
- Name
- Category
- Base Price
- Stock
- Featured (bintang)
- Active (toggle)

### Add/Edit Product

**URL**: `/admin/products/create` atau `/admin/products/edit/{id}`

**Fields**:

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| Name | Text | ✅ | Nama produk |
| Slug | Text | ✅ | Auto dari name (URL-friendly) |
| Category | Dropdown | ❌ | Pilih kategori |
| Short Description | Textarea | ❌ | Max 255 char |
| Description | WYSIWYG | ❌ | Detail produk |
| Thumbnail | Upload | ❌ | Max 5MB, JPG/PNG/WebP |
| Base Price | Number | ✅ | Harga dasar (Rp) |
| Stock | Number | ✅ | 0 = unlimited |
| Is Featured | Checkbox | ❌ | Tampil di homepage |
| Is Active | Checkbox | ✅ | Publish/unpublish |

**Gallery** (P0 - jika sudah implement):
- Upload multiple images (max 10)
- Drag-drop untuk reorder
- Delete individual image

**Actions**:
- **Save**: Simpan dan kembali ke list
- **Save & Continue**: Simpan dan tetap di form
- **Cancel**: Kembali tanpa simpan

### Product Options

**URL**: `/admin/products/{id}/options`

**Untuk apa?**  
Produk dengan variasi (ukuran, bahan, finishing, dll) yang mempengaruhi harga.

**Contoh**:
- Spanduk → Ukuran (A4 +0, A3 +5000, A2 +15000)
- Spanduk → Bahan (Flexi 280 +0, Flexi 340 +3000)
- Spanduk → Finishing (Laminasi Doff +10000)

**Structure**:
1. **Option Group** (e.g., "Ukuran")
   - Input Type: select / radio / checkbox
   - Required: Ya/Tidak
   - Min/Max Select
2. **Option Values** (e.g., "A4", "A3", "A2")
   - Label
   - Price Type: fixed / percent
   - Price Value

**Flow**:
1. Buat Group baru (misal: "Bahan")
2. Tambah Values ke Group (misal: "Flexi 280gsm +0", "Flexi 340gsm +3000")
3. Ordering: numeric (smaller = pertama)

**Price Calculation** (di frontend):
```
Final Price = Base Price + Sum(Selected Options)
```

---

## 📂 Product Categories

**URL**: `/admin/product-categories`

**Fields**:
- Name: Nama kategori
- Slug: Auto-generate (URL-friendly)
- Description: Optional
- Icon: FontAwesome class atau emoji (untuk homepage category bar)
- Sort Order: Urutan tampil (smaller = pertama)
- Is Active: Publish/unpublish

**Tips**:
- Sort Order untuk kontrol urutan di sidebar frontend
- Icon pakai format: `fa-print` atau `🖨️`

---

## 💸 Discounts Management

**URL**: `/admin/discounts`

**Fields**:
- Product: Pilih dari dropdown
- Discount Type: Percent (%) atau Fixed (Rp)
- Discount Value: Jumlah diskon
- Qty Total: Kuota maksimal (0 = unlimited)
- Qty Used: Auto-increment saat dipakai
- Start At: Tanggal mulai (optional)
- End At: Tanggal berakhir (optional)
- Is Active: On/off

**Example**:
- Spanduk 4x1: Diskon 50% → Type: Percent, Value: 50
- Banner Indoor: Diskon Rp 9.999 → Type: Fixed, Value: 9999

**Notes**:
- 1 produk bisa punya multiple discounts, yang aktif adalah yang paling baru dan valid
- Frontend auto-calculate: final price = base price - discount

---

## 🏢 Our Store (Portfolio)

**URL**: `/admin/our-store`

**Untuk apa?**  
Menampilkan daftar kantor/toko/portfolio di halaman "Our Home".

**Fields**:
- Name: Nama toko/kantor
- Slug: Auto-generate
- Office Type: HQ (Pusat) / Branch (Cabang)
- Address: Alamat lengkap
- City: Kota
- Phone: Nomor telepon
- WhatsApp: Nomor WA (format: 628xxx)
- GMaps URL: Link Google Maps (untuk "Lihat di Maps" button)
- Thumbnail: Upload foto toko
- Is Active: Publish/unpublish
- Sort Order: Urutan tampil

**Tips**:
- HQ (Pusat) auto-highlight di frontend
- GMaps URL: Buka Google Maps → Share → Copy Link

---

## ✍️ Blog Management

**URL**: `/admin/blog`

**Roles**: Super Admin + Admin (sama-sama bisa CRUD).

### Add/Edit Blog Post

**Fields**:

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| Title | Text | ✅ | Judul artikel |
| Slug | Text | ✅ | Auto dari title |
| Excerpt | Textarea | ❌ | Summary (max 500 char) |
| Content | WYSIWYG | ✅ | Isi artikel (rich text) |
| Thumbnail | Upload | ❌ | Featured image (max 5MB) |
| Is Published | Checkbox | ❌ | Draft vs Published |
| Published At | Datetime | ❌ | Tanggal publish (auto-fill saat check Published) |
| Is Featured | Checkbox | ❌ | Tampil di hero homepage (P0 - perlu tambah field) |

**States**:
- **Draft** (`is_published = 0`): Tidak tampil di public
- **Published** (`is_published = 1`): Tampil di public

**Tips**:
- Slug otomatis dari title (bisa manual edit)
- Excerpt: ringkasan untuk list view, kalau kosong auto-truncate content
- Thumbnail: aspect ratio 16:9 recommended (1200x675px)

**WYSIWYG Editor**: 
- Format teks: Bold, Italic, Heading, List
- Insert image, link, blockquote
- HTML source mode (untuk advanced user)

---

## 📧 Contact Messages

**URL**: `/admin/contact/messages`

**Untuk apa?**  
Inbox pesan dari contact form public.

**Columns**:
- Name
- Email
- Subject
- Message (preview)
- Is Read (unread = bold)
- Date

**Actions**:
- **View**: Klik row → detail message
- **Mark as Read**: Auto saat view
- **Delete**: Icon trash (hard delete, hati-hati!)

**Detail View** (`/admin/contact/{id}`):
- Full message
- Sender info (name, email, phone)
- Timestamp
- Reply button (P1 - future: kirim email reply)

---

## 🎨 Home Content Management

### Hero Slides

**URL**: `/admin/home/hero`

**Untuk apa?**  
Banner carousel di homepage (auto-rotate).

**Fields**:
- Page Slug: `home` (fixed)
- Title: Judul besar
- Subtitle: Teks kecil di bawah title
- Badge: Label kecil di atas title (optional)
- CTA Text: Text tombol (misal: "Order Sekarang")
- CTA Link: Link tombol (misal: `/contact`)
- Image: Upload banner (1320x610px recommended)
- Position: Urutan (smaller = pertama)
- Is Active: Publish/unpublish

**Tips**:
- Max 5 slides (lebih banyak = lambat)
- Position: 1, 2, 3, ... (slide 1 = pertama tampil)
- Image size: max 2MB, aspect 1320:610 (otomatis crop di frontend)

### Home Content (CTA, Settings)

**URL**: `/admin/home/content`

**Untuk apa?**  
Edit konten dinamis homepage (CTA bar, category mapping, dll).

> ⚠️ Module ini complex (EAV pattern), lihat implementation_plan.md untuk detail.

**Simplified Usage** (P1 - future refactor):
- CTA Bar Top Text
- CTA Bar Top Link
- CTA Bar Bottom Text
- CTA Bar Bottom Link

---

## ⚙️ Settings

**URL**: `/admin/settings`

**Global site settings** (only 1 row, update saja).

**Fields**:

| Field | Type | Notes |
|-------|------|-------|
| Site Name | Text | Nama website |
| Site Tagline | Text | Slogan |
| Logo | Upload | Logo header (PNG transparent) |
| Phone | Text | Nomor telepon |
| Email | Email | Email kontak |
| Address | Textarea | Alamat lengkap |
| Operating Hours | Text | (P0) Jam operasional (misal: "Senin-Sabtu 08:00-17:00") |
| GMaps Embed | Textarea | (P0) Iframe embed Google Maps |
| WhatsApp | Text | Nomor WA (format: 628xxx) |
| Facebook | URL | Link profil FB |
| Instagram | URL | Link profil IG |
| Twitter | URL | (P0) Link Twitter |
| YouTube | URL | (P0) Link YouTube |
| TikTok | URL | (P0) Link TikTok |

**Tips**:
- GMaps Embed: Google Maps → Share → Embed a map → Copy `<iframe>` code
- Logo: 200x60px recommended (transparent PNG)
- Phone format: 0878-xxxx-xxxx (display) atau 62878xxxxxxxx (untuk WA link)

**Usage di Frontend**:
- Navbar: logo, site name
- Footer: alamat, phone, email, social links
- Contact page: semua info + maps embed

---

## 👥 Users Management

**URL**: `/admin/users`  
**Role**: Super Admin only

**Untuk apa?**  
Kelola akun admin panel.

**Fields**:
- Name: Nama lengkap
- Email: Email login (unique)
- Password: Min 8 char (auto-hash)
- Role: Super Admin / Admin
- Is Active: Enable/disable login

**Actions**:
- Add User: Buat akun admin baru
- Edit: Ubah nama, email, role
- Delete: Hapus akun (hati-hati!)

**Tips**:
- Password: Auto-hash (bcrypt), tidak bisa lihat plain text
- Edit tanpa ubah password: kosongkan field password
- Super Admin: jangan hapus semua (minimal 1 harus ada)

---

## 🔒 Security Best Practices

### Password
- Min 8 karakter
- Kombinasi huruf + angka + simbol
- Tidak pakai: password123, admin123, nama sendiri

### Upload
- Hanya upload gambar resmi (JPG, PNG, WebP)
- Jangan upload file .php, .exe, .zip
- Max 5MB per file

### Logout
- Selalu logout setelah selesai
- Jangan share session/cookie

### Backup
- Export database minimal 1x seminggu
- Simpan di tempat aman (Google Drive, Dropbox)

---

## 📱 Responsive Admin

Admin panel (AdminKit) sudah responsive:
- **Desktop**: Sidebar kiri, content luas
- **Tablet**: Sidebar collapse, toggle button
- **Mobile**: Full-width, sidebar overlay

**Tips Mobile**:
- Table: scroll horizontal
- Form: full-width input
- Upload: drag-drop atau tap to browse

---

## 🆘 Troubleshooting

### Upload Gagal
**Error**: "Failed to upload file"

**Solusi**:
1. Cek permission folder `public/uploads/` (777)
2. Cek size file (max 5MB)
3. Cek format file (jpg/png/webp only)

### Gambar Tidak Muncul di Public
**Error**: Broken image

**Solusi**:
1. Cek path di DB: harus `uploads/products/xxx.png` (tanpa `/` di depan)
2. Cek file exist di `public/uploads/products/`
3. Cek base_url di `app/config/app.php`

### WYSIWYG Editor Tidak Load
**Error**: Textarea biasa (tanpa toolbar)

**Solusi**:
1. Cek internet (TinyMCE/CKEditor load from CDN)
2. Refresh page (Ctrl+F5)
3. Cek browser console (F12) untuk error JS

### Logout Sendiri
**Error**: Auto-redirect ke login

**Solusi**:
1. Session timeout (default 30 menit)
2. Re-login
3. Setting session lifetime (advanced): edit `php.ini` → `session.gc_maxlifetime`

---

## ✅ Admin Workflow - Quick Guide

### Skenario: UPDATE PRODUK BARU

1. Login → `/admin/login`
2. Sidebar → Products → Add Product
3. Fill:
   - Name: "Stiker Vinyl Custom"
   - Category: Stiker
   - Price: 25000
   - Description: "Stiker vinyl premium..."
4. Upload thumbnail (drag & drop)
5. Check "Is Featured" (tampil homepage)
6. Check "Is Active"
7. Save
8. (Optional) Klik "Options" → tambah opsi ukuran
9. View public → `/products` → product muncul

### Skenario: PUBLISH BLOG

1. Sidebar → Blog → Add Post
2. Fill:
   - Title: "Tips Cetak Banner Outdoor"
   - Content: (tulis artikel)
   - Upload thumbnail
3. Check "Is Published"
4. Published At: auto-fill (hari ini)
5. Check "Is Featured" (tampil di homepage hero)
6. Save
7. View public → `/blog` → post muncul

### Skenario: UPDATE SETTINGS

1. Sidebar → Settings
2. Update:
   - Phone: 0878-xxxx-xxxx
   - WhatsApp: 6287xxxxxxxxx
   - Operating Hours: "Senin-Sabtu 08:00-17:00"
3. Save
4. View public → Contact page → info updated

---

## 📞 Support

Jika ada pertanyaan atau masalah:
1. Cek dokumentasi ini
2. Cek INSTALLATION.md untuk setup
3. Hubungi developer/technical support

---

**Last Updated**: 2025-12-18  
**Version**: 1.0
