# SIP-NKP: Sistem Informasi Purchasing & Pengendalian Inventaris Terintegrasi
### Studi Kasus: PT Nandya Karya Perkasa (Plant Komponen Otomotif)

[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://php.net)
[![Database](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![Server](https://img.shields.io/badge/Environment-Laragon%20Localhost-0E7090?logo=apache&logoColor=white)](https://laragon.org)
[![Bootstrap](https://img.shields.io/badge/UI%20Framework-Bootstrap%205.3.3-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![Icons](https://img.shields.io/badge/Icons-Lucide%20Icons-F59E0B?logo=feather&logoColor=white)](https://lucide.dev)

Aplikasi berbasis web untuk digitalisasi pengadaan barang (*Purchasing*) dan pengendalian inventaris suku cadang (*Inventory Control*) yang dirancang khusus untuk memenuhi standar operasional manufaktur otomotif pada **PT Nandya Karya Perkasa**. Proyek ini dikembangkan sebagai karya ilmiah dan tugas akhir Praktik Kerja Lapangan (PKL).

---

## 📑 Daftar Dokumentasi Proyek

Untuk mempermudah penulisan laporan PKL dan pengujian sistem, dokumentasi lengkap telah disusun ke dalam berkas-berkas berikut:

1. **[DAFTAR_AKUN.md](file:///c:/laragon/www/nkp_inventaris/DAFTAR_AKUN.md)** - *Buku Panduan Akun Pengguna & Hak Akses*: Daftar kredensial login seluruh divisi, matriks wewenang RBAC, dan skenario simulasi sidang.
2. **[PRD.md](file:///c:/laragon/www/nkp_inventaris/PRD.md)** - *Product Requirements Document*: Latar belakang masalah, profil PT NKP, analisis kebutuhan fungsional, dan skema ERD.
3. **[AGENTS.md](file:///c:/laragon/www/nkp_inventaris/AGENTS.md)** - *Developer Directives*: Panduan penulisan kode, keamanan PDO prepared statements, database transactions, dan standar palet visual industri.
4. **[DATABASE.md](file:///c:/laragon/www/nkp_inventaris/DATABASE.md)** - *Kamus Data & Arsitektur Database*: Spesifikasi seluruh tabel MySQL, relasi foreign key, tipe data, dan integritas data.
5. **[SYSTEM_WORKFLOW.md](file:///c:/laragon/www/nkp_inventaris/SYSTEM_WORKFLOW.md)** - *Diagram Sistem*: UML Use Case Diagram, Activity Diagram, dan Sequence Diagram (format Mermaid untuk Bab 3 Laporan PKL).
6. **[USER_GUIDE.md](file:///c:/laragon/www/nkp_inventaris/USER_GUIDE.md)** - *Buku Manual Pengguna*: Panduan operasional per role (Requester, Supervisor, Purchasing, Warehouse, Admin) untuk Bab 4 Laporan PKL.
7. **[PRESENTATION_GUIDE.md](file:///c:/laragon/www/nkp_inventaris/PRESENTATION_GUIDE.md)** - *Panduan Sidang PKL*: Kisi-kisi pertanyaan dewan penguji, kunci jawaban teknis, dan kesimpulan/saran untuk Bab 5 Laporan PKL.

---

## 🔄 Siklus Alur Bisnis Pengadaan & Logistik Terpadu

Sistem ini merefleksikan alur kerja industri manufaktur sesungguhnya dari hulu ke hilir:

```
[1. Permintaan PR] ➔ [2. Persetujuan SPV] ➔ [3. Penerbitan PO] ➔ [4. Penerimaan GR] ➔ [5. Surat Jalan SJ]
(Teknisi Mesin)       (Supervisor Dept)     (Purchasing Dept)     (Petugas Gudang)      (Logistik / Distribusi)
```

1. **Tahap 1 — Pengajuan Kebutuhan (Purchase Requisition / PR):**
   Teknisi lini perakitan/maintenance mengajukan kebutuhan suku cadang mesin press yang menipis atau rusak, lengkap dengan target tanggal dibutuhkan dan skala prioritas (*Normal / Urgent / Emergency*).
2. **Tahap 2 — Verifikasi & Persetujuan (Supervisor Approval Inbox):**
   Supervisor departemen meninjau justifikasi pemakaian dan menyetujui dokumen secara digital (*1-Click Approval* di baris tabel atau melalui halaman review detail).
3. **Tahap 3 — Kontrak Pesanan Vendor (Purchase Order / PO):**
   Tim Purchasing mengonversi dokumen PR yang telah disetujui menjadi PO resmi, memilih vendor rekanan, menetapkan harga kesepakatan, mengalkulasi PPN 11%, dan mencetak lembar fisik PO standar **ISO 9001 / IATF 16949 (Format A4)** dengan kop resmi dan 3 kolom tanda tangan.
4. **Tahap 4 — Penerimaan Fisik Barang (Goods Receipt / GR - Inbound):**
   Petugas gudang menerima barang dari ekspedisi vendor di *receiving dock*, memeriksa nomor surat jalan vendor dan hasil QC inspection, lalu melakukan konfirmasi GR yang secara otomatis:
   - Menambah stok fisik barang di rak penyimpanan.
   - Mencatat mutasi masuk (**Audit Trail IN**) pada kartu stok inventaris.
5. **Tahap 5 — Pengeluaran Barang Pabrik (Surat Jalan / Delivery Note - Outbound):**
   Petugas logistik menerbitkan Surat Jalan resmi PT NKP untuk pengiriman komponen jadi ke customer atau transfer antar-plant, yang secara otomatis:
   - Mengurangi stok fisik di rak gudang (**Stock OUT**).
   - Mencatat mutasi keluar (**Audit Trail OUT**) pada kartu stok.
   - Mencetak lembar fisik Surat Jalan format ISO A4 lengkap dengan 4 kolom tanda tangan legal.

---

## 🚀 Fitur Unggulan Sistem

- **Menu Pengadaan Terpadu (*Unified PR Navigation*):**
  Navigasi modular yang rapi melalui menu tunggal **Permintaan Pembelian (PR)** dengan filter status tab (*Semua, Menunggu Approval, Disetujui, PO Terbit, Ditolak*) dan lencana antrean real-time (*Badge Counter*).
- **Early Warning Safety Stock:**
  Peringatan visual warna merah otomatis pada dashboard saat stok suku cadang mesin press berada di bawah batas aman minimum.
- **Integritas Transaksi Database Atomik (ACID):**
  Konfirmasi GR dan penerbitan Surat Jalan menggunakan transaksi PDO (`$pdo->beginTransaction()`, `$pdo->commit()`, `$pdo->rollBack()`) untuk memastikan pembaruan status dokumen, kalkulasi stok, dan pencatatan buku besar mutasi selalu sinkron 100% tanpa risiko data ganda atau korup.
- **Format Cetak Dokumen Standar ISO/A4 Siap Pakai:**
  Tata letak dokumen cetak resmi (Purchase Order & Surat Jalan) siap cetak/simpan PDF dengan format standar industri manufaktur otomotif, lengkap dengan kop PT NKP, barcode dokumen, dan tanda tangan legal.
- **Role-Based Access Control (RBAC) & Akun Master Admin IT:**
  Pemisahan wewenang ketat antar divisi (*Teknisi, Supervisor, Purchasing, Warehouse*) dengan akun Administrator IT (`budi`) yang memiliki wewenang master (*Superuser*) untuk mengelola master data, menghapus data, dan mengelola akun pengguna karyawan.

---

## 👥 Akun Uji Coba Default (Demo Credentials)

Gunakan akun di bawah ini untuk pengujian hak akses masing-masing departemen atau simulasi sidang PKL:

| No | Username | Password | Peran (Role) | Nama Lengkap | Departemen / Divisi | Wewenang Utama |
| :-: | :--- | :---: | :---: | :--- | :--- | :--- |
| 1 | **`budi`** | `password123` | `admin` | Budi Santoso | Information Technology (IT) | **Akses Penuh Seluruh Modul & Manajemen Akun** |
| 2 | **`hendra`** | `password123` | `supervisor` | Ir. Hendra Gunawan | Stamping & Press Plant | **Tahap 2: Verifikasi & Persetujuan Dokumen PR** |
| 3 | **`siti`** | `password123` | `purchasing` | Siti Rahmawati | Procurement & Purchasing | **Tahap 3: Pembuatan Kontrak Vendor & Terbit PO** |
| 4 | **`doni`** | `password123` | `purchasing` | Doni Hermawan | Procurement & Purchasing | **Tahap 3: Pembuatan Kontrak Vendor & Terbit PO** |
| 5 | **`agus`** | `password123` | `warehouse` | Agus Setiawan | Logistics & Warehouse | **Tahap 4 & 5: Penerimaan GR, Stok, & Surat Jalan** |
| 6 | **`rizky`** | `password123` | `requester` | Rizky Pratama | Maintenance & Tooling | **Tahap 1: Pengajuan Suku Cadang Mesin (PR)** |

> [!TIP]
> Rincian lengkap hak akses dan skenario 5 langkah demonstrasi sidang PKL dapat dilihat pada dokumen:  
> ➔ **[DAFTAR_AKUN.md](file:///c:/laragon/www/nkp_inventaris/DAFTAR_AKUN.md)**

---

## 🛠️ Kebutuhan Sistem & Spesifikasi Lingkungan

- **Web Server:** Apache 2.4+ (Bawaan Laragon Localhost)
- **Bahasa Pemrograman:** PHP 8.1 / 8.2 / 8.3 (PDO MySQL Driver aktif)
- **Database Server:** MySQL 8.0+ / MariaDB 10.4+ (Port default 3306)
- **Frontend & Styling:** Bootstrap 5.3.3, Custom Industrial CSS (`css/bootstrap-custom.css`), Lucide Icons SVG
- **Peramban Web:** Google Chrome, Microsoft Edge, atau Mozilla Firefox versi terbaru

---

## 📦 Panduan Instalasi Lokal (Laragon)

1. **Letakkan Proyek di Direktori Root Web Laragon:**
   Pastikan folder proyek berada pada:
   ```text
   C:\laragon\www\nkp_inventaris\
   ```

2. **Jalankan Layanan Apache & MySQL:**
   Buka aplikasi Laragon, lalu klik tombol **Start All**.

3. **Buat Basis Data MySQL:**
   - Buka phpMyAdmin di browser (`http://localhost/phpmyadmin`) atau buka HeidiSQL bawaan Laragon.
   - Buat basis data baru bernama: **`nkp_inventaris`** (Collation: `utf8mb4_unicode_ci`).

4. **Impor Skema & Data Awal:**
   Jalankan file SQL berikut secara berurutan:
   - `database/schema.sql` (Struktur 9 tabel relasional)
   - `database/seeders.sql` (Data awal pengguna, supplier, master suku cadang realistis PT NKP)

5. **Konfigurasi Koneksi Database:**
   Periksa konfigurasi pada file `app/Config/Database.php`:
   ```php
   private const HOST = 'localhost';
   private const DB_NAME = 'nkp_inventaris';
   private const USERNAME = 'root';
   private const PASSWORD = '';
   private const PORT = 3306;
   ```

6. **Akses Aplikasi:**
   Buka browser dan buka tautan:
   ```text
   http://localhost/nkp_inventaris/
   ```
   *atau jika menggunakan Virtual Host Laragon:*
   ```text
   http://nkp_inventaris.test/
   ```

---

## 🏢 Tentang Studi Kasus
**PT Nandya Karya Perkasa (PT NKP)**  
*Precision Automotive Metal Stamping, Dies & Mold, Welding, and Plastic Injection Component.*  
Kawasan Industri Narogong Km. 16, Cileungsi, Bogor, Jawa Barat - Indonesia.
