# EventPrint Homepage Image Guidelines (CMS Ready)

Dokumen ini adalah panduan teknis dan visual untuk mempersiapkan aset gambar Homepage EventPrint. Panduan ini ditujukan untuk desainer, content editor, dan pengembang CMS untuk memastikan konsistensi visual, performa loading yang cepat, dan tampilan yang responsif di semua perangkat.

---

## 1. Ringkasan Cepat (Quick Reference)

Gunakan tabel ini untuk referensi cepat saat export aset.

| Komponen | Recommended Size (px) | Aspect Ratio | Format | Max Size | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Hero Banner** | 1920 x 720 | 8:3 (2.66:1) | JPG/WebP | 300KB | *Safe area* di tengah sangat penting. |
| **Product Card** | 800 x 600 | 4:3 (1.33:1) | JPG/WebP | 150KB | Background putih/abu bersih. |
| **Service Icon** | 96 x 96 | 1:1 | SVG/PNG | 50KB | Transparan background. |
| **Why Choose** | 1200 x 900 | 4:3 (1.33:1) | JPG/WebP | 250KB | Gambar suasana/mesin/human. |
| **Promo Banner** | 1200 x 400 | 3:1 | JPG/WebP | 200KB | Banner text harus terbaca di mobile. |
| **Blog Card** | 800 x 500 | 16:10 | JPG/WebP | 150KB | *Optional section*. |

---

## 2. Detail Per Komponen

### A. Hero Banner Carousel
Banner utama di bagian paling atas halaman. Area ini menggunakan teknik ``background-image`` dengan ``background-size: cover``, yang artinya gambar akan **terpotong (crop)** di sisi kiri/kanan pada layar mobile/tablet.

*   **Purpose**: Menampilkan promosi utama, branding, atau penawaran spesial.
*   **Desktop Size**: 1920 x 720 px
*   **Mobile Size**: Sistem akan otomatis crop bagian tengah (center). Tidak perlu upload gambar terpisah, tapi pastikan **fokus utama ada di tengah**.
*   **Aspect Ratio**: 8:3 (Wide)
*   **Safe Area**: Pertahankan teks/objek penting dalam area **1000px tengah**. Hindari menaruh teks di pinggir kiri/kanan ekstrem karena akan hilang di mobile.
*   **Fallback Color**: ``#00AEEF`` (Primary Cyan) atau Dark Grey.
*   **Example Filename**: ``hero-promo-agustus-1920x720.jpg``

### B. Product Card Image (Produk Unggulan)
Gambar thumbnail untuk daftar produk (Grid). Konsistensi rasio sangat vital agar grid terlihat rapi.

*   **Purpose**: Menampilkan preview produk yang dijual.
*   **Recommended Size**: 800 x 600 px
*   **Aspect Ratio**: 4:3 (Landscape standar)
*   **Crop Behavior**: ``object-fit: cover``. Gambar akan mengisi kotak. Jika rasio beda, bagian atas/bawah akan terpotong.
*   **Guideline**:
    *   Gunakan foto produk dengan background bersih (putih/abu muda).
    *   Usahakan produk berada di tengah (center-weighted).
*   **Example Filename**: ``product-backwall-3x3-800x600.jpg``

### C. Service / Category Icons
Ikon kecil yang mewakili layanan (misal: Digital Print, Offset, Merchandise).

*   **Purpose**: Navigasi visual cepat untuk kategori.
*   **Recommended Size**: 96 x 96 px (Minimum 64px)
*   **Format**: **SVG** (Terbaik untuk ketajaman) atau PNG Transparan.
*   **Aspect Ratio**: 1:1 (Square)
*   **Style**: Flat icon atau Line icon monokrom (Putih/Biru) agar kontras dengan background bar kategori.
*   **Example Filename**: ``icon-digital-print.svg`` atau ``icon-offset.png``

### D. Why Choose Us Image
Gambar besar di samping teks "Why Choose EventPrint".

*   **Purpose**: Ilustrasi pendukung kredibilitas (foto mesin, workshop, atau tim).
*   **Recommended Size**: 1200 x 900 px
*   **Aspect Ratio**: 4:3
*   **Crop Behavior**:
    *   **Desktop**: Tampil utuh atau sedikit terpotong vertikal.
    *   **Mobile**: Gambar biasanya pindah ke atas teks dan terlihat utuh (full width).
*   **Example Filename**: ``about-workshop-machine-1200x900.jpg``

### E. Promo / Mini Carousel (Infrastructure Gallery)
Carousel kecil di bagian bawah (biasanya menampilkan mesin atau fasilitas).

*   **Purpose**: Showcase fasilitas tanpa mengambil terlalu banyak ruang vertikal.
*   **Recommended Size**: 1200 x 400 px
*   **Aspect Ratio**: 3:1 (Panoramic)
*   **Notes**: Karena dimensinya pendek dan lebar, hindari foto close-up wajah. Gunakan foto landscape luas.
*   **Example Filename**: ``promo-mesin-indoor-1200x400.jpg``

### F. Testimonial Avatar (Optional)
*Status: Saat ini layout homepage belum menampilkan foto avatar user, hanya Nama & Bintang.*

*   **Future Proofing**: Jika fitur avatar diaktifkan.
*   **Size**: 200 x 200 px
*   **Ratio**: 1:1 (Akan dicrop menjadi lingkaran/circle oleh CSS).

---

## 3. Folder & Naming Convention

Struktur folder ini dirancang untuk CMS agar aset terorganisir rapi dan tidak tercampur.

**Root Path**: ``public/uploads/images/home/``

| Folder Path | Kategori Gambar | Contoh Nama File |
| :--- | :--- | :--- |
| ``/home/hero/`` | Banner Utama | ``hero-01-promosi-jan.jpg`` |
| ``/home/products/`` | Thumbnail Produk | ``prod-roll-banner-std.jpg`` |
| ``/home/icons/`` | Ikon Kategori | ``cat-icon-offset.svg`` |
| ``/home/content/`` | Why Choose, Promo | ``content-workshop-v1.jpg`` |

**Aturan Penamaan (Naming Rules)**:
1.  **Lowercase**: Gunakan huruf kecil semua.
2.  **Hyphen**: Gunakan tanda strip (``-``) sebagai pemisah, JANGAN spasi.
3.  **Descriptive**: Masukkan kata kunci konten `[jenis]-[konten]-[ukuran].ext`.
    *   ✅ DO: ``hero-diskon-lebaran-1920.jpg``
    *   ❌ DON'T: ``IMG_2024_01.JPG``, ``Banner Baru Banget.png``

---

## 4. Compression & Export Guide

Agar website loading cepat (skor SEO hijau), ikuti aturan ini sebelum upload ke CMS.

1.  **Format File**:
    *   **JPG/JPEG**: Untuk foto (manusia, pemandangan, produk real). Set Quality ke **70-80%**.
    *   **PNG**: Hanya untuk logo atau ikon yang butuh background transparan. Jangan pakai PNG untuk foto banner (file size akan bengkak).
    *   **WebP**: Sangat disarankan jika CMS mendukung auto-convert. Ukuran file bisa 30% lebih kecil dari JPG.

2.  **Tools Kompresi (Gratis)**:
    *   [TinyJPG](https://tinyjpg.com) / [TinyPNG](https://tinypng.com)
    *   [Squoosh.app](https://squoosh.app) (Google)

3.  **Max File Size Limit**:
    *   Hero Banner: Max **300 KB** (Ideal < 200 KB)
    *   Product/Content: Max **150 KB**
    *   Icons: Max **50 KB**

---

## 5. CMS Implementation Notes (For Developers/PHP)

Panduan untuk integrasi Backend/CMS.

### Upload Validation Rules
*   **Allowed MIME Types**: ``image/jpeg``, ``image/png``, ``image/webp``, ``image/svg+xml`` (khusus admin).
*   **Max Upload Size**: Limit server upload ke **2MB** (User akan dipaksa kompres jika lebih, atau sistem auto-compress).

### Image Processing (Resize/Crop)
Saat admin upload gambar, sistem sebaiknya men-generate variant (thumbnails) agar browser tidak memuat gambar full HD untuk ikon kecil.

| Component | Variant Name | Resize Logic | Target Dimension |
| :--- | :--- | :--- | :--- |
| **Product Card** | ``thumb_md`` | Resize & Center Crop | 800w x 600h |
| **Product Card** | ``thumb_sm`` | Resize & Center Crop | 400w x 300h (Mobile load) |
| **Hero Banner** | ``hero_lg`` | Resize Width (Keep Ratio) | 1920w (Quality 80) |
| **Hero Banner** | ``hero_md`` | Resize Width (Keep Ratio) | 1080w (Mobile load) |

### Frontend Handling (HTML Picture)
Gunakan tag ``<picture>`` atau ``srcset`` untuk responsive images jika memungkinkan.

```html
<!-- Contoh Best Practice Hero -->
<picture>
  <source media="(max-width: 768px)" srcset="/uploads/home/hero/hero-01-mobile.jpg">
  <img src="/uploads/home/hero/hero-01-desktop.jpg" alt="Promo Event" style="object-fit:cover; width:100%;">
</picture>
```

---
*Dibuat oleh Assistant Agent untuk Dokumentasi EventPrint*
