# EventPrint Contact Page Image Guidelines

Dokumen ini adalah panduan teknis untuk aset gambar visual pada halaman **Contact Us**.

---

## 1. Komponen Utama

### A. Map Placeholder / Static Map
Jika Anda memutuskan untuk menggunakan *Static Image* sebagai pengganti Google Maps Interactive (untuk alasan performa atau API cost saver).

*   **HTML ID**: `#mapPlaceholder` / `.contact-box`
*   **Recommended Size**: 1200 x 400 px.
*   **Aspect Ratio**: 3:1 (Ultra Wide).
*   **Content**: Screenshot peta lokasi HQ yang diberi pin merah jelas.
*   **Format**: PNG/JPG.
*   **Note**: Jika menggunakan Google Maps Embed API (Iframe), panduan ini tidak berlaku.

### B. Social Media Icons (Optional Custom)
Secara default, website menggunakan SVG Icons (Vector) yang ditarik lewat kode. Namun jika di masa depan ingin mengganti dengan Custom Image Icon (misal: Icon 3D, Icon Warna Brand).

*   **HTML ID**: `#socialIcons .social-icon img` (If customized).
*   **Recommended Size**: 48 x 48 px.
*   **Aspect Ratio**: 1:1.
*   **Format**: PNG Transparent Background (Penting!).
*   **Style**: Pastikan semua icon memiliki gaya visual yang sama (Flat, 3D, atau Outline). Jangan mencampur gaya.

---

## 2. Folder Structure

Jika ada aset statis kontak:
``public/uploads/images/contact/``

*   `static-map-hq.jpg`
*   `icon-wa-custom.png`

---
*Dibuat oleh Assistant Agent untuk Dokumentasi EventPrint*
