# EventPrint Our Home (About & Locations) Image Guidelines

Dokumen ini adalah panduan teknis untuk aset gambar pada halaman **Our Home**. Halaman ini fokus pada kredibilitas, menampilkan lokasi fisik toko (cabang) dan fasilitas mesin produksi.

---

## 1. Ringkasan Cepat (Quick Reference)

| Komponen | Posisi | Recommended (px) | Rasio | Format | Max Size |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Store Thumbnail** | Grid Lokasi Cabang | 500 x 500 | 1:1 | JPG | 100KB |
| **Machine Gallery** | Galeri Fasilitas | 800 x 600 | 4:3 | JPG | 150KB |

---

## 2. Detail Per Komponen

### A. Store Location Thumbnail
Gambar yang mewakili setiap cabang toko (EventPrint Store).

*   **HTML Class**: `.store-image`
*   **Purpose**: Memudahkan customer mengenali bangunan toko saat berkunjung (wayfinding).
*   **Recommended Size**: 500 x 500 px.
*   **Aspect Ratio**: 1:1 (Square).
    *   Mengapa Square? Layout kartu toko di desktop menggunakan thumbnail di sebelah kiri yang lebih rapi jika kotak.
*   **Content**:
    *   **WAJIB**: Foto tampak depan toko (Fasade) yang jelas, menampilkan plang nama "EventPrint".
    *   **Alternatif**: Foto interior area pelayanan customer.
*   **Format**: JPG High Quality.

### B. Machine & Facility Gallery
Galeri foto mesin produks untuk menunjukkan kapasitas dan kualitas cetak.

*   **HTML Class**: `.gallery-item img`
*   **Recommended Size**: 800 x 600 px.
*   **Aspect Ratio**: 4:3 (Landscape).
*   **Content**: Close-up mesin, proses cetak, atau tumpukan hasil jadi yang rapi. Hindari foto mesin berantakan atau kabel semrawut.
*   **Overlay Text**: Sistem CMS akan menambahkan Judul & Caption di atas gambar saat hover, jadi pastikan bagian tengah/bawah gambar tidak terlalu "ramai" agar teks terbaca jelas (atau sistem akan memberi overlay gelap).

---

## 3. Folder & Naming Convention

**Root Path**: ``public/uploads/images/about/``

| Folder Path | Kegunaan | Contoh Nama File |
| :--- | :--- | :--- |
| ``/about/stores/`` | Foto Cabang | ``store-tebet-facade.jpg`` |
| ``/about/machines/`` | Foto Mesin | ``machine-indoor-eco-solvent.jpg`` |

**Aturan Penamaan**:
*   **Toko**: `store-[nama_cabang]-[tampak].jpg`
    *   Contoh: `store-bekasi-depan.jpg`, `store-bogor-interior.jpg`.
*   **Mesin**: `machine-[jenis]-[merk].jpg`
    *   Contoh: `machine-uv-flatbed-mimaki.jpg`.

---

## 4. CMS Validation Notes

1.  **Uniformity**: Galeri mesin akan terlihat sangat buruk jika rasio gambar campur aduk (ada yang portrait, ada yang landscape lebar). CMS harus me-rapatkan ke **4:3** atau **3:2**.
2.  **Metadata**: Untuk galeri mesin, CMS wajib meminta input:
    *   **Title**: Nama Mesin (misal: Heidelberg SM 52).
    *   **Caption**: Fungsi Singkat (misal: Cetak Brosur Cepat).
    *   **Type/Badge**: Tag kategori (misal: "Outdoor", "A3+").

---
*Dibuat oleh Assistant Agent untuk Dokumentasi EventPrint*
