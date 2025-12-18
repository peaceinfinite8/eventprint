# EventPrint - QA & UAT Checklist

Checklist lengkap untuk Quality Assurance dan User Acceptance Testing sebelum go-live.

---

## 🧪 QA CHECKLIST - FUNCTIONAL

### 1. Homepage (`/`)

#### Hero Carousel
- [ ] Minimal 1 slide tampil (dari `hero_slides` WHERE `is_active=1`)
- [ ] Auto-advance setiap 5 detik
- [ ] Navigation arrows (left/right) works
- [ ] Dot indicators tampil dan klik works
- [ ] CTA button link ke halaman yang benar
- [ ] Responsive: image fit di mobile/tablet/desktop

#### Kata Mereka (Testimonials)
- [ ] Tampil 4-6 testimonials (dari `testimonials` WHERE `is_active=1`)
- [ ] Rating stars tampil sesuai jumlah (1-5)
- [ ] Foto testimonial tampil (atau placeholder jika kosong)
- [ ] Urut sesuai `sort_order` ASC

#### Category Icons Row
- [ ] Tampil 7 kategori (dari `product_categories` ORDER BY `sort_order`)
- [ ] Icon tampil (FontAwesome atau emoji)
- [ ] Klik kategori → redirect ke `/products?category={slug}`
- [ ] Responsive: scroll horizontal di mobile

#### Produk Unggulan
- [ ] Tampil 4-8 produk (dari `products` WHERE `is_featured=1`)
- [ ] Thumbnail tampil (atau placeholder)
- [ ] Harga format Rp xxx.xxx
- [ ] Klik card → redirect ke product detail
- [ ] Responsive: 1 col (mobile), 2 col (tablet), 4 col (desktop)

#### Kontak Kami + Lokasi
- [ ] Info kontak dari `settings` (phone, email, address, WA)
- [ ] Google Maps embed tampil (dari `settings.gmaps_embed`)
- [ ] WA button link ke `https://wa.me/{whatsapp_number}`
- [ ] Responsive: stack di mobile, 2 col di desktop

#### CTA Bar
- [ ] 2 CTA button tampil
- [ ] Link ke halaman yang benar
- [ ] Responsive: stack di mobile

### 2. Products Page (`/products`)

#### Sidebar Filter
- [ ] List kategori dari `product_categories`
- [ ] Klik kategori → filter products by category
- [ ] Active category highlight (cyan background)
- [ ] "Semua Produk" option (show all)
- [ ] Sticky sidebar saat scroll (desktop)
- [ ] Responsive: collapse/accordion di mobile

#### Product Grid
- [ ] Tampil semua produk aktif (`is_active=1`)
- [ ] Filter by category works (via `?category={slug}`)
- [ ] 3 kolom di desktop, 2 di tablet, 1 di mobile
- [ ] Thumbnail tampil (atau placeholder)
- [ ] Harga format Rp xxx.xxx
- [ ] Klik card → product detail

#### Pagination
- [ ] Tampil jika produk > 12
- [ ] Next/Prev button works
- [ ] Page numbers tampil (max 5 pages visible)
- [ ] Current page highlight
- [ ] URL update: `?page=2`

#### Empty State
- [ ] Tampil jika tidak ada produk (kategori kosong)
- [ ] Message: "Belum ada produk di kategori ini"
- [ ] Button "Lihat Semua Produk"

### 3. Product Detail (`/product-detail?slug=xxx`)

#### Product Info
- [ ] Title, description, price tampil
- [ ] Thumbnail besar tampil
- [ ] Category badge tampil
- [ ] Stock info (jika <10: "Stok terbatas")

#### Gallery
- [ ] Tampil min 3 images (dari `product_images`)
- [ ] Klik thumbnail → ganti main image
- [ ] Zoom on hover (optional)
- [ ] Responsive: slider di mobile

#### Product Options
- [ ] Tampil semua option groups (ukuran, bahan, finishing)
- [ ] Select/radio/checkbox sesuai `input_type`
- [ ] Required validation works
- [ ] Price calculation dynamic (base + options)
- [ ] Display: "Harga: Rp xxx.xxx" (update on change)

#### Discount
- [ ] Badge "DISKON X%" tampil jika ada discount aktif
- [ ] Original price coret
- [ ] Final price bold
- [ ] Calculation correct (percent vs fixed)

#### Add to Cart (jika ada)
- [ ] Button "Tambah ke Keranjang"
- [ ] Validation: wajib pilih required options
- [ ] Success message/redirect

#### 404 Handling
- [ ] Slug tidak ada → redirect 404
- [ ] Product `is_active=0` → 404 (kecuali admin preview)

### 4. Blog List (`/blog`)

#### Hero Mosaic
- [ ] Tampil 1 besar + 3 kecil (dari `posts` WHERE `is_featured=1` LIMIT 4)
- [ ] Thumbnail tampil
- [ ] Title, excerpt tampil
- [ ] Date format: "12 Des 2025"
- [ ] Klik → blog detail
- [ ] Responsive: stack di mobile

#### Postingan Unggulan (Carousel)
- [ ] Tampil 6-10 post featured
- [ ] Scroll horizontal (carousel)
- [ ] Arrow navigation works
- [ ] Responsive: 1 item (mobile), 3 items (desktop)

#### Sedang Tren
- [ ] Tampil 6-10 post (ORDER BY views DESC atau published_at DESC)
- [ ] Grid 2 kolom desktop, 1 kolom mobile
- [ ] Pagination jika > 10

#### Empty State
- [ ] Tampil jika belum ada blog

### 5. Blog Detail (`/articles/{slug}`)

#### Post Content
- [ ] Title, thumbnail tampil
- [ ] Full content (rich text) render correct
- [ ] Date, author (optional)
- [ ] View count (P1 - jika ada)

#### Related Posts (P1)
- [ ] Tampil 3 post terkait
- [ ] Same category atau random

#### Social Share (P2)
- [ ] Button share FB, Twitter, WA
- [ ] Link correct

#### 404 Handling
- [ ] Slug tidak ada → 404
- [ ] `is_published=0` → 404 (kecuali admin preview)

### 6. Our Home (`/our-home`)

#### Store List
- [ ] Tampil semua toko aktif (`is_active=1`)
- [ ] Order by `sort_order` ASC
- [ ] HQ (Pusat) badge tampil
- [ ] Thumbnail tampil
- [ ] Info: alamat, city, phone, WA
- [ ] Button "Lihat di Maps" → link ke `gmaps_url`
- [ ] Responsive: 1 col mobile, 2 col desktop

#### Empty State
- [ ] Tampil jika belum ada toko

### 7. Contact Page (`/contact`)

#### Get in Touch
- [ ] Info dari `settings` (address, email, phone, WA)
- [ ] Social icons (FB, IG, Twitter, YouTube, TikTok) tampil jika ada
- [ ] Operating hours tampil (P0 - jika field sudah ada)

#### Form "Kirim Pesan"
- [ ] Field: Name, Email, Phone, Subject, Message
- [ ] Validation:
  - [ ] Name required
  - [ ] Email required + format valid
  - [ ] Message required
  - [ ] Phone optional
- [ ] Submit → save to `contact_messages`
- [ ] Success message tampil: "Pesan terkirim!"
- [ ] Error handling (DB fail, validation fail)

#### Google Maps
- [ ] Embed iframe tampil (dari `settings.gmaps_embed`)
- [ ] Full width, height 450px
- [ ] Responsive: height 300px di mobile

---

## 🎨 QA CHECKLIST - RESPONSIVE

Test di berbagai device/viewport:

### Breakpoints
- **Mobile**: 360px, 375px, 414px
- **Tablet**: 768px, 1024px
- **Desktop**: 1366px, 1440px, 1920px

### Navbar
- [ ] Logo tampil di semua breakpoint
- [ ] Menu items collapse di mobile (<768px)
- [ ] Hamburger menu works (jika ada)
- [ ] Dropdown (jika ada) works di mobile
- [ ] Sticky navbar (optional)

### Footer
- [ ] Stack di mobile (1 kolom)
- [ ] 2-3 kolom di tablet
- [ ] 4 kolom di desktop
- [ ] Social icons tidak pecah
- [ ] Copyright text center di mobile

### Forms
- [ ] Input full width di mobile
- [ ] Max-width di desktop (600px)
- [ ] Button tidak terlalu kecil (min 44px height)
- [ ] Touch-friendly (spacing cukup)

### Images
- [ ] Tidak pixelated (use srcset jika perlu)
- [ ] Tidak overflow container
- [ ] Aspect ratio maintained
- [ ] Lazy load (optional)

### Typography
- [ ] Font size readable di mobile (min 14px)
- [ ] Line height cukup (1.5-1.7)
- [ ] Tidak ada horizontal scroll
- [ ] Heading hierarchy correct (H1 > H2 > H3)

---

## 🔐 QA CHECKLIST - ADMIN PANEL

### Login
- [ ] URL `/admin/login` accessible
- [ ] Validation: email + password required
- [ ] Wrong credentials → error message
- [ ] Correct credentials → redirect dashboard
- [ ] Remember me (optional)

### Dashboard
- [ ] Statistik tampil (products, posts, messages, categories)
- [ ] Quick links works
- [ ] No PHP errors

### Products CRUD
- [ ] List: table tampil, pagination works
- [ ] Create: form submit → save DB → redirect list
- [ ] Edit: load data, update → save
- [ ] Delete: confirmation → soft delete → redirect
- [ ] Upload thumbnail: file save to `public/uploads/products/`
- [ ] Validation: name, slug required

### Product Options
- [ ] List groups per product
- [ ] Add group: form submit → save
- [ ] Add value to group: form submit → save
- [ ] Edit group/value: load data → update
- [ ] Delete group → cascade delete values
- [ ] Ordering works (numeric input)

### Product Categories
- [ ] CRUD same as products
- [ ] Icon field: text input (FA class atau emoji)
- [ ] Sort order works

### Discounts
- [ ] List: tampil per product
- [ ] Create: dropdown product, type, value
- [ ] Date range validation (start < end)
- [ ] Qty used auto-increment (P1 - jika implement)

### Blog CRUD
- [ ] WYSIWYG editor load (TinyMCE/CKEditor)
- [ ] Upload thumbnail works
- [ ] Slug auto-generate dari title
- [ ] Publish checkbox → set `is_published=1` + `published_at=NOW()`
- [ ] Featured checkbox works (P0 - jika field sudah ada)

### Our Store CRUD
- [ ] Upload thumbnail works
- [ ] GMaps URL validation (optional)
- [ ] Office type dropdown (HQ/Branch)

### Contact Messages
- [ ] List: newest first
- [ ] Unread bold
- [ ] Click → detail view → mark as read
- [ ] Delete works

### Hero Slides
- [ ] List: order by position
- [ ] Create: upload image works
- [ ] Position numeric input
- [ ] Is Active toggle works
- [ ] Preview link (P1)

### Settings
- [ ] Single form (update only)
- [ ] Upload logo works
- [ ] Textarea untuk GMaps embed
- [ ] Save → flash message "Settings updated"

### Users (Super Admin Only)
- [ ] Admin role tidak bisa akses
- [ ] CRUD works
- [ ] Password auto-hash (bcrypt)
- [ ] Email unique validation

### Logout
- [ ] Button works
- [ ] Session destroyed
- [ ] Redirect to login

---

## 👤 UAT CHECKLIST - CLIENT TEST

### UAT 1: Update Homepage Content

**Scenario**: Client ingin ganti banner homepage.

**Steps**:
1. [ ] Login admin (`/admin/login`)
2. [ ] Go to "Home" > "Hero Slides"
3. [ ] Klik "Edit" slide #1
4. [ ] Ubah title: "PROMO AKHIR TAHUN"
5. [ ] Ubah subtitle: "Diskon 50% untuk cetak banner"
6. [ ] Upload image baru (banner.jpg, 1320x610px)
7. [ ] Klik "Save"
8. [ ] Preview public homepage → banner updated
9. [ ] Check responsive (mobile/tablet)

**Expected**: Banner updated, no errors.

### UAT 2: Add New Product

**Scenario**: Client tambah produk baru "Kalender Meja 2025".

**Steps**:
1. [ ] Go to "Products" > "Add Product"
2. [ ] Fill:
   - Name: "Kalender Meja 2025"
   - Category: "Merchandise Cetak"
   - Price: 50000
   - Description: "Kalender meja custom..."
   - Upload thumbnail (kalender.jpg)
3. [ ] Check "Is Featured" (tampil homepage)
4. [ ] Check "Is Active"
5. [ ] Klik "Save"
6. [ ] Go to "Options" (icon gear)
7. [ ] Add group "Ukuran":
   - Type: Select
   - Required: Yes
8. [ ] Add values:
   - "A5" (price: +0)
   - "A4" (price: +10000)
9. [ ] Save options
10. [ ] View public homepage → product di "Produk Unggulan"
11. [ ] View `/products` → product di list
12. [ ] Klik product → detail page → options tampil

**Expected**: Product created, featured di homepage, options works.

### UAT 3: Publish Blog Post

**Scenario**: Client publish artikel "Tips Memilih Bahan Banner".

**Steps**:
1. [ ] Go to "Blog" > "Add Post"
2. [ ] Fill:
   - Title: "Tips Memilih Bahan Banner untuk Outdoor"
   - Content: (paste artikel, min 300 kata)
   - Upload thumbnail (banner-tips.jpg)
3. [ ] Check "Is Published"
4. [ ] Check "Is Featured" (tampil hero blog)
5. [ ] Published At: auto-fill (today)
6. [ ] Klik "Save"
7. [ ] View `/blog` → post di hero mosaic
8. [ ] Klik post → detail page → content tampil lengkap
9. [ ] Check responsive

**Expected**: Post published, tampil di blog list + hero.

### UAT 4: Update Contact Info

**Scenario**: Client ganti nomor WA dan tambah jam operasional.

**Steps**:
1. [ ] Go to "Settings"
2. [ ] Update:
   - WhatsApp: 62812345678 (baru)
   - Operating Hours: "Senin-Jumat: 08:00-17:00, Sabtu: 08:00-12:00"
   - GMaps Embed: (paste iframe code dari Google Maps)
3. [ ] Klik "Save"
4. [ ] View `/contact` → info updated
5. [ ] Klik button WA → redirect ke `https://wa.me/62812345678`
6. [ ] Maps embed tampil

**Expected**: Info updated real-time di public page.

### UAT 5: Read Customer Message

**Scenario**: Customer submit contact form, admin baca pesan.

**Steps**:
1. [ ] (Client role) View `/contact`
2. [ ] Fill form:
   - Name: "Test Customer"
   - Email: "test@example.com"
   - Subject: "Tanya harga banner"
   - Message: "Berapa harga banner 3x1 meter?"
3. [ ] Submit → success message
4. [ ] (Admin) Login → go to "Contact Messages"
5. [ ] Message baru tampil (unread = bold)
6. [ ] Klik message → detail view
7. [ ] Status change to "Read"
8. [ ] (Optional) Reply via email/WA

**Expected**: Message saved, admin dapat notif, mark as read works.

### UAT 6: Manage Product Discount

**Scenario**: Client kasih diskon 20% untuk produk "Stiker Vinyl".

**Steps**:
1. [ ] Go to "Discounts" > "Add Discount"
2. [ ] Product: "Stiker Vinyl"
3. [ ] Type: Percent
4. [ ] Value: 20
5. [ ] Qty Total: 100
6. [ ] End At: 31 Des 2025
7. [ ] Check "Is Active"
8. [ ] Save
9. [ ] View public product detail "Stiker Vinyl"
10. [ ] Badge "DISKON 20%" tampil
11. [ ] Original price: ~~Rp 25.000~~ → **Rp 20.000**

**Expected**: Discount applied, calculation correct.

---

## 🐛 BUG TRACKING

Jika menemukan bug saat QA/UAT, catat di format ini:

### Bug Template

**ID**: BUG-001  
**Module**: Products  
**Severity**: High / Medium / Low  
**Description**: Upload thumbnail gagal, error "Failed to move uploaded file"  
**Steps to Reproduce**:
1. Go to `/admin/products/create`
2. Upload JPG file (2MB)
3. Click Save

**Expected**: File uploaded, product saved  
**Actual**: Error message  
**Screenshot**: (attach)  
**Browser**: Chrome 120 / Windows 11  
**Status**: Open / In Progress / Fixed / Closed  

---

## ✅ ACCEPTANCE CRITERIA FINAL

Project dianggap **PASS QA/UAT** jika:

### Functional
- [ ] Semua 7 halaman public accessible tanpa error
- [ ] Semua konten dinamis dari DB (0% hardcode)
- [ ] Admin CRUD 10+ modul works (Products, Categories, Blog, etc)
- [ ] Upload validation enforced (type, size)
- [ ] Form submission save to DB tanpa error

### Security
- [ ] No SQL injection (tested dengan `' OR '1'='1`)
- [ ] No XSS (tested dengan `<script>alert('xss')</script>`)
- [ ] Upload gagal untuk file .php, .exe
- [ ] Admin routes protected (redirect login jika tidak auth)

### Responsive
- [ ] Layout OK di 360px, 768px, 1440px
- [ ] No horizontal scroll
- [ ] Touch-friendly (button min 44px)
- [ ] Image fit container

### Performance (P1)
- [ ] Page load < 3 detik (lokal)
- [ ] No N+1 query (use JOIN)
- [ ] Image optimized (<500KB per file)

### UX
- [ ] No PHP notice/warning visible
- [ ] Error pages 404/500 custom (bukan default)
- [ ] Form validation feedback jelas
- [ ] Success/error message tampil (flash message atau toast)

### Documentation
- [ ] INSTALLATION.md complete
- [ ] ADMIN_GUIDE.md complete
- [ ] CONTENT_STRUCTURE.md complete (jika perlu)
- [ ] README.md updated

### Client Training
- [ ] Client dapat login admin
- [ ] Client dapat CRUD min 1 product
- [ ] Client dapat publish min 1 blog
- [ ] Client dapat update settings
- [ ] Client paham flow upload file

---

## 📊 QA REPORT TEMPLATE

**Project**: EventPrint Integration  
**QA Date**: [DATE]  
**Tester**: [NAME]  
**Environment**: Local XAMPP / Staging / Production  

### Summary
- **Total Test Cases**: 150
- **Passed**: 145 ✅
- **Failed**: 3 ❌
- **Blocked**: 2 ⚠️
- **Not Tested**: 0

### Failed Tests
1. **BUG-001**: Upload thumbnail di Product gagal (file >5MB)
2. **BUG-002**: Blog pagination page 3 error 404
3. **BUG-003**: GMaps embed tidak tampil di mobile

### Blocked Tests
1. **BLK-001**: Testimonials belum ada tabel (waiting schema migration)
2. **BLK-002**: Product gallery belum implement

### Recommendations
- Fix BUG-001-003 sebelum UAT client
- Prioritas P0: testimonials table + migration
- Performance: optimize image upload (auto-resize to max 1200px width)

### Sign-off
- [ ] QA Approved
- [ ] Ready for UAT
- [ ] Client Approved
- [ ] Ready for Production

---

**Last Updated**: 2025-12-18  
**Version**: 1.0
