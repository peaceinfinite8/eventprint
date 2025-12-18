# EventPrint Blog Image Guidelines (CMS Ready)

Dokumen ini adalah panduan teknis untuk mempersiapkan aset gambar yang digunakan pada halaman **Blog / Artikel**. Konsistensi visual pada blog sangat penting untuk kenyamanan membaca dan "scannability" konten.

---

## 1. Ringkasan Cepat (Quick Reference)

| Komponen | Posisi | Recommended Size (px) | Aspect Ratio | Format | Max Size |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Hero Main Image** | Artikel Utama (Atas) | 800 x 450 | 16:9 | JPG | 150KB |
| **Hero Side Image** | Artikel Samping (List) | 300 x 200 | 3:2 | JPG | 80KB |
| **Trending Thumb** | Grid Artikel Bawah | 600 x 400 | 3:2 | JPG | 100KB |
| **Content Body** | Dalam Artikel | 800+ Width | Flexible | JPG/WebP | 200KB |

---

## 2. Detail Per Komponen

### A. Hero Main Image (Headline)
Gambar besar untuk artikel utama yang tampil paling atas di halaman Blog.

*   **HTML Class**: `.blog-hero-main-image`
*   **Display Size**: Responsive width, fixed height (320px desktop).
*   **Recommended Size**: 800 x 450 px hingga 1280 x 720 px.
*   **Aspect Ratio**: 16:9 (Cinematic/Wide).
*   **Crop Behavior**:
    *   System menggunakan `object-fit: cover`.
    *   Pastikan subjek utama berada di tengah (center-weighted) karena gambar akan terpotong atas/bawah jika layar sangat lebar, atau terpotong samping jika layar mobile.
*   **Fallback**: Pastikan CMS memiliki *default placeholder image* jika user lupa upload (misal: logo EventPrint pattern).

### B. Hero Side Image (Highlight Lists)
Thumbnail kecil untuk 3 artikel di samping headline utama.

*   **HTML Class**: `.blog-hero-small-image`
*   **Display Size**: Kecil (~100px height).
*   **Recommended Size**: 300 x 200 px.
*   **Aspect Ratio**: 3:2 (Standard Photo).
*   **Optimization**: Karena ukurannya kecil, **JANGAN** menggunakan file gambar HD (2MB+). Ini akan sangat memperlambat loading page (LCP Score).

### C. Trending Card Thumbnail
Gambar thumbnail untuk daftar artikel "Sedang Tren" di bagian bawah.

*   **HTML Class**: `.blog-card-image`
*   **Recommended Size**: 600 x 400 px.
*   **Aspect Ratio**: 3:2 (Standard Landscape) atau 4:3.
*   **Consistency**: Sangat penting untuk menggunakan rasio yang SAMA untuk semua artikel di section ini agar grid kartu sejajar rapi (tidak ada kartu yang lebih tinggi dari sebelahnya).

### D. Content Body Images (Detail Artikel)
Gambar yang disisipkan di dalam teks artikel (bukan thumbnail).

*   **Width**: Minimal 800px lebar agar tajam di desktop.
*   **Height**: Flexible (Auto).
*   **Caption**: CMS harus menyediakan field `caption` atau `alt text` untuk SEO.

---

## 3. Folder & Naming Convention

**Root Path**: ``public/uploads/images/blog/``

| Folder Path | Kegunaan | Contoh Nama File |
| :--- | :--- | :--- |
| ``/blog/covers/`` | Thumbnail Utama | ``tips-cetak-murah-cover.jpg`` |
| ``/blog/content/`` | Gambar Isi Artikel | ``mesin-offset-detail.jpg`` |

**Aturan Penamaan**:
*   Gunakan judul artikel yang dipersingkat sebagai nama file.
*   `[topik-artikel]-[keyword].jpg`
*   Contoh: `tips-memilih-kertas-brosur.jpg`

---

## 4. CMS Validation Notes

1.  **Auto Resize**:
    *   Saat user upload Cover Image, sistem sebaiknya otomatis membuat versi thumbnail (300px width) untuk sidebar list. Jangan load full image 1200px di kotak kecil 100px.
2.  **Alt Text**: Wajib ada untuk SEO Blog.
3.  **Lazy Loading**: Pastikan tag HTML `<img>` memiliki properti `loading="lazy"` untuk artikel yang berada di bawah fold (body & trending grid).

---
*Dibuat oleh Assistant Agent untuk Dokumentasi EventPrint*
