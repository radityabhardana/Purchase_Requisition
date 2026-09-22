# SIP-NKP: Sistem Informasi Purchasing & Pengendalian Inventaris Terintegrasi
### Studi Kasus: PT Nandya Karya Perkasa (Plant Komponen Otomotif)

[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://php.net)
[![Database](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![Server](https://img.shields.io/badge/Environment-Laragon%20Localhost-0E7090?logo=apache&logoColor=white)](https://laragon.org)
[![Tailwind CSS](https://img.shields.io/badge/Styling-Tailwind%20CSS-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com)

Aplikasi berbasis web untuk digitalisasi pengadaan barang (*Purchasing*) dan manajemen stok (*Inventory Control*) yang dirancang khusus untuk memenuhi standar operasional manufaktur otomotif pada **PT Nandya Karya Perkasa**. Proyek ini dikembangkan sebagai karya ilmiah dan tugas akhir Praktik Kerja Lapangan (PKL).

---

## 📑 Daftar Dokumentasi Proyek

Untuk mempermudah penulisan laporan PKL dan pengujian sistem, dokumentasi lengkap telah disusun ke dalam berkas-berkas berikut:

1. **[PRD.md](file:///c:/laragon/www/nkp_inventaris/PRD.md)** - *Product Requirements Document*: Latar belakang, profil PT NKP, analisis masalah, kebutuhan fungsional, dan skema ERD.
2. **[AGENTS.md](file:///c:/laragon/www/nkp_inventaris/AGENTS.md)** - *Developer Directives*: Panduan penulisan kode, aturan keamanan PDO, transaksi database, dan desain visual.
3. **[DATABASE.md](file:///c:/laragon/www/nkp_inventaris/DATABASE.md)** - *Kamus Data & Arsitektur Database*: Spesifikasi tabel, relasi foreign key, tipe data, dan integritas data.
4. **[SYSTEM_WORKFLOW.md](file:///c:/laragon/www/nkp_inventaris/SYSTEM_WORKFLOW.md)** - *Diagram Sistem*: UML Use Case Diagram, Activity Diagram, dan Sequence Diagram (format Mermaid untuk Bab 3 Laporan PKL).
5. **[USER_GUIDE.md](file:///c:/laragon/www/nkp_inventaris/USER_GUIDE.md)** - *Buku Manual Pengguna*: Panduan operasional per role (Requester, Supervisor, Purchasing, Warehouse) untuk Bab 4 Laporan PKL.
6. **[PRESENTATION_GUIDE.md](file:///c:/laragon/www/nkp_inventaris/PRESENTATION_GUIDE.md)** - *Panduan Sidang PKL*: Kisi-kisi pertanyaan penguji, kunci jawaban teknis, dan kesimpulan/saran untuk Bab 5.

---

## 🚀 Fitur Utama Sistem

- **Siklus Pengadaan Lengkap (End-to-End):**
  `Purchase Requisition (PR)` ➔ `Approval Supervisor` ➔ `Purchase Order (PO)` ➔ `Goods Receipt (GR)` ➔ `Auto-Update Stok`.
- **Early Warning Safety Stock:**
  Indikator warna merah otomatis pada dashboard saat stok suku cadang mesin press berada di bawah batas aman.
- **Penerbitan PO Standar ISO/A4:**
  Layout siap cetak berformat standar industri lengkap dengan kop resmi PT Nandya Karya Perkasa, barcode nomor dokumen, rincian PPN 11%, dan 3 kolom tanda tangan resmi.
- **Pencegahan Data Dobel (Auto-Sync via DB Transaction):**
  Konfirmasi penerimaan barang di gudang langsung menambah stok barang dan mencatat mutasi (*stock card ledger*) secara atomik tanpa risiko *race condition*.
- **Multi-Role Access Control:**
  Sistem keamanan hak akses terpisah untuk Staf Pemohon, Supervisor, Purchasing Officer, dan Gudang.

---

## 🛠️ Kebutuhan Sistem & Spesifikasi Lingkungan

- **Web Server:** Apache 2.4+ (Rekomendasi: Laragon Full)
- **Bahasa Pemrograman:** PHP 8.1 / 8.2 / 8.3
- **Database:** MariaDB 10.4+ / MySQL 8.0+
- **Browser:** Google Chrome, Microsoft Edge, atau Mozilla Firefox versi terbaru

---

## 📦 Panduan Instalasi Lokal (Laragon)

1. **Letakkan Proyek di Direktori Root Web:**
   Pastikan folder proyek berada di:
   ```text
   C:\laragon\www\nkp_inventaris\
   ```

2. **Buat Database MySQL:**
   - Buka aplikasi Laragon, klik tombol **Start All**.
   - Buka phpMyAdmin di browser (`http://localhost/phpmyadmin`) atau buka terminal HeidiSQL.
   - Buat database baru bernama: `nkp_inventaris`.

3. **Impor Skema dan Data Awal:**
   - Jalankan kueri SQL dari file:
     - `database/schema.sql` (Struktur tabel)
     - `database/seeders.sql` (Data awal pengguna, supplier, dan barang dummy PT NKP)

4. **Konfigurasi Koneksi Database:**
   - Periksa file `app/Config/Database.php`.
   - Pastikan parameter koneksi sesuai dengan konfigurasi Laragon:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'nkp_inventaris');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_PORT', '3306');
     ```

5. **Akses Aplikasi:**
   Buka peramban (browser) dan akses URL:
   ```text
   http://localhost/nkp_inventaris/
   ```
   *atau jika menggunakan Virtual Host Laragon:*
   ```text
   http://nkp_inventaris.test/
   ```

---

## 👥 Akun Uji Coba Default (Demo Credentials)

Untuk keperluan pengujian dan demonstrasi saat sidang PKL, sistem menyediakan 4 akun siap pakai:

| Role | Username | Password | Deskripsi Tugas |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `password123` | Akses penuh master data dan konfigurasi sistem |
| **Supervisor** | `spv_stamping` | `password123` | Menyetujui/menolak pengajuan PR dari teknisi |
| **Purchasing** | `purchasing` | `password123` | Membuat PO ke supplier dan cetak dokumen resmi |
| **Warehouse** | `warehouse` | `password123` | Konfirmasi barang datang (GR) dan cek stok fisik |
| **Teknisi (Requester)** | `teknisi_press` | `password123` | Mengajukan permintaan sparepart (PR) |

---

## 🏢 Tentang Studi Kasus
**PT Nandya Karya Perkasa**  
*Manufaktur Komponen Otomotif, Metal Stamping, Dies & Mold, Welding, and Plastic Injection.*  
Plant Cileungsi, Bogor, Jawa Barat - Indonesia.
