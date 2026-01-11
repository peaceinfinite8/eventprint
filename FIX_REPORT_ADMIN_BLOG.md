# Laporan Akhir Perbaikan Modul Admin Blog - EventPrint

Berikut adalah rekapitulasi lengkap perbaikan yang dilakukan untuk memastikan modul Admin Blog berfungsi normal (Create, Update, List, dan Upload).

## 1. Perbaikan Tombol Simpan & Flash Message (CRITICAL)
**Gejala:** Tombol "Simpan" ditekan tapi tidak ada respon, halaman refresh tanpa notifikasi, atau artikel tampak tidak tersimpan.
**Akar Masalah:**
- Error CSRF tidak tampil karena konflik format session (`flash_error` vs `flash['error']`).
- Pesan sukses tidak muncul karena Controller menggunakan session manual.
**Solusi:**
- Memperbaiki `app/core/route.php` untuk menampilkan error sistem (seperti CSRF).
- Memperbarui `BlogController.php` untuk menggunakan notifikasi `$this->setFlash()` yang standar.
- Memperbarui `app/core/controller.php` untuk fallback compatibility.

## 2. Perbaikan Artikel Baru "Hilang" (Sorting Bug)
**Gejala:** Setelah klik "Tambah Artikel", notifikasi sukses muncul tapi artikel baru tidak ada di urutan teratas daftar artikel.
**Akar Masalah:** Query `INSERT` tidak menyertakan timestamp `created_at`. Sorting default mengandalkan tanggal tersebut. Karena nilainya kosong `0000-00-00`, artikel baru dilempar ke urutan paling bawah/belakang.
**Solusi:**
- Memodifikasi `app/models/Post.php` pada fungsi `create()` dan `update()` untuk secara eksplisit mengisi kolom `created_at` dan `updated_at` dengan waktu server saat ini.

## 3. Perbaikan Upload Foto & UI
**Gejala:** Upload foto gagal karena path salah, dan tidak ada preview gambar.
**Solusi:**
- Mengoreksi upload path server-side menjadi `uploads/blog/` (Root) sesuai konfigurasi XAMPP.
- Menambahkan **JavaScript Preview & Validation** pada `create.php` dan `edit.php`:
  - Preview gambar muncul seketika sebelum disimpan.
  - Validasi otomatis menolak file > 2MB atau bukan gambar (JPG/PNG).

## Checklist Pengujian
Silakan coba skenario berikut:
1. **Tambah Artikel Baru:**
   - Isi judul "Test Artikel Baru".
   - Upload gambar -> Pastikan preview muncul.
   - Klik Simpan -> Notifikasi "Berhasil" muncul.
   - Cek List Artikel -> **Artikel harus ada di Baris Pertama (No. 1)**.
   
2. **Edit Artikel:**
   - Klik Edit pada artikel tadi.
   - Ubah judul atau ganti gambar.
   - Klik Simpan -> Notifikasi "Berhasil" muncul.
   - Judul di list terupdate.

Perbaikan selesai. Semua fitur admin blog sekarang berjalan sesuai spesifikasi.
