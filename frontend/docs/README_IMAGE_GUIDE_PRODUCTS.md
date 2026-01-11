# EventPrint Product Page Image Guidelines (CMS Ready)

Dokumen ini adalah panduan teknis untuk mempersiapkan aset gambar yang digunakan pada **Halaman Daftar Produk (Catalog)** dan **Halaman Detail Produk**. Panduan ini memastikan tampilan katalog terlihat rapi, seragam, dan profesional.

---

## 1. Ringkasan Cepat (Quick Reference)

| Komponen | Posisi | Recommended (px) | Rasio | Format | Max Size |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Catalog Thumbnail** | Halaman Utama/List | 800 x 600 | 4:3 | JPG/WebP | 150KB |
| **Main Product Image** | Halaman Detail | 1200 x 900 | 4:3 | JPG/WebP | 300KB |
| **Category Icon** | Sidebar Menu | 24 x 24 | 1:1 | SVG | 5KB |
| **Marketplace Icon** | Tombol CTA Detail | 32 x 32 | 1:1 | SVG/PNG | 10KB |

---

## 2. Detail Per Komponen

### A. Catalog Thumbnail (Grid Image)
Gambar yang muncul di halaman "All Products" dalam grid (susunan kotak). Ketidakseragaman rasio di sini akan membuat layout terlihat berantakan (tinggi kartu tidak sama).

*   **Usage**: `products.html` (Grid List).
*   **Recommended Size**: 800 x 600 px.
*   **Aspect Ratio**: 4:3 (Landscape).
*   **Crop Behavior**: `object-fit: cover`.
    *   ⚠️ **Warning**: Jangan gunakan rasio 1:1 (Kotak) atau 9:16 (Portrait) kecuali semua produk konsisten menggunakan itu. Campuran rasio akan merusak kerapian grid.
*   **Content**: Tampilkan produk secara utuh dengan background putih atau abu-abu netral (``#F9FAFB``).
*   **Fallback**: Jika gambar kosong, gunakan placeholder warna abu dengan logo watermark.

### B. Product Detail Images (Gallery)
Gambar resolusi tinggi di halaman detail produk. Meliputi gambar utama (besar) dan thumbnail kecil di bawahnya.

*   **Usage**: `product-detail.html` (Main Gallery).
*   **Recommended Size**: 1200 x 900 px (HD).
*   **Safe Zone**:
    *   Karena container gambar memiliki tinggi fix (`400px`) dan lebar `100%`, gambar akan mengalami cropping di sisi atas/bawah pada layar lebar.
    *   **PENTING**: Pastikan objek produk berada tepat di **tengah (center)** dengan margin (ruang kosong) minimal 10-15% di sekelilingnya sehingga tidak terpotong saat di-crop.
*   **Style Guide**:
    *   **Foto 1 (Main)**: Foto produk utuh menghadap depan/serong.
    *   **Foto 2-4 (Support)**: Detail tekstur, contoh pengaplikasian (mockup), atau varian warna.
*   **Format**: JPG (Quality 80%) atau WebP.

### C. Sidebar Category Icons (Optional)
Jika sidebar kategori menggunakan ikon custom (bukan default chevron).

*   **Recommended Size**: 24 x 24 px (Pixel Perfect).
*   **Format**: **SVG** (Vector) sangat disarankan agar tajam di layar Retina/HP.
*   **Color**: Monokrom (Hitam/Abu). CSS akan otomatis mengubah warna saat aktif/hover jika menggunakan SVG inline, namun jika upload image, gunakan warna Abu Gelap (`#4B5563`).

---

## 3. Folder & Naming Convention

Pisahkan folder produk dengan homepage agar mudah dimanage saat produk mencapai ratusan item.

**Root Path**: ``public/uploads/images/products/``

| Folder Path | Kegunaan | Contoh Nama File |
| :--- | :--- | :--- |
| ``/products/backwall/`` | Kategori Backwall | ``backwall-curve-3x3-01.jpg`` |
| ``/products/banner/`` | Kategori Banner | ``banner-roll-up-alum.jpg`` |
| ``/products/merch/`` | Kategori Merchandise | ``mug-custom-white.jpg`` |

**Aturan Penamaan**:
*   Gunakan struktur: `[slug-produk]-[urutan].ext`
*   Contoh:
    *   `tripod-banner-01.jpg` (Gambar Utama)
    *   `tripod-banner-02-detail.jpg` (Gambar Detail)
    *   `tripod-banner-03-usage.jpg` (Gambar Penggunaan)

---

## 4. Validasi & CMS Logic (PHP/Backend)

### Upload & Resize Strategy
Untuk performa maksimal, sistem harus membuat variant ukuran saat upload:

1.  **Original (Source)**: Simpan master file (misal 2000px) untuk keperluan zoom (masa depan).
2.  **Detail Version (Large)**: Resize ke **1200px width**. Tampilkan di Main Image Detail.
3.  **Catalog Version (Medium)**: Resize & Crop ke **800x600px**. Gunakan di halaman "All Products".
4.  **Thumbnail Version (Small)**: Resize ke **150x150px**. Gunakan di navigasi thumbnail detail page.

### Layout Consistency Check
*   Pastikan CSS Product Card memiliki properti ini agar layout tidak pecah jika user upload gambar beda ukuran:
    ```css
    .product-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Wajib, menhindari gambar gepeng */
        object-position: center;
    }
    ```

---
*Dokumen Panduan Teknis EventPrint*
