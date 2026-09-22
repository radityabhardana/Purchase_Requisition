# AGENTS.md - Development Guide & Project Directives
## Project: SIP-NKP (Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa)

Dokumen ini berisi standar arsitektur, panduan penulisan kode (*coding standards*), dan aturan teknis yang harus dipatuhi oleh seluruh agent atau developer yang mengerjakan proyek ini.

---

## 1. Lingkungan Pengembangan (*Environment*)

- **Web Server:** Apache (Laragon Localhost)
- **Database Engine:** MySQL / MariaDB (Port 3306, default Laragon root:blank)
- **Backend Language:** PHP 8.3 (Gunakan fitur modern: Typed properties, Nullsafe operator, Match expression, Prepared Statement PDO)
- **Frontend & Styling:** Tailwind CSS / Modern CSS3 (Inter Font, Slate/Zinc neutral dark/light aesthetic, responsive flex/grid)
- **Icons & Visuals:** Lucide Icons / Heroicons (SVG format, clean dan modern)
- **Directory Root:** `c:\laragon\www\nkp_inventaris\`
- **Local URL:** `http://localhost/nkp_inventaris/` atau `http://nkp_inventaris.test/`

---

## 2. Prinsip Desain & User Experience (Design Philosophy)

Sistem ini didesain khusus untuk lingkungan manufaktur modern dengan standar visual tinggi:
1. **Palet Warna Industri Presisi (Industrial Automotive Palette):**
   - **Primary:** Deep Slate / Navy (`#0f172a`, `#1e293b`) memberikan kesan korporat dan profesional.
   - **Accent / Manufacturing Energy:** Safety Amber / Warm Orange (`#f59e0b`, `#ea580c`) untuk tombol aksi penting dan alert.
   - **Status Indicators (Universal Industrial Standard):**
     - `Pending / Review`: Amber / Yellow (`#fef3c7` bg, `#d97706` text)
     - `Approved / Completed / Good Stock`: Emerald Green (`#d1fae5` bg, `#059669` text)
     - `Rejected / Critical Stockout`: Crimson Red (`#fee2e2` bg, `#dc2626` text)
     - `In Transit / On Delivery`: Sky Blue (`#e0f2fe` bg, `#0284c7` text)
2. **Komponen Visual Dashboard:**
   - **KPI Metric Cards:** Angka besar dengan indikator tren dan ikon latar transparan.
   - **Order Lifecycle Stepper:** Garis visual yang menunjukkan tahapan dokumen (`Draft` ➔ `PR Disetujui` ➔ `PO Diterbitkan` ➔ `Barang Tiba` ➔ `Stok Masuk`).
   - **Printable PO Layout:** Lembar cetak berformat standar ISO A4 dengan kop resmi PT Nandya Karya Perkasa, nomor PO, tabel rincian, dan 3 kolom tanda tangan (Dibuat oleh Purchasing, Disetujui oleh Manager, Diterima oleh Supplier).

---

## 3. Struktur Direktori Proyek (Modular MVC Sederhana)

Untuk kemudahan pemeliharaan dan agar siswa PKL mudah menjelaskan kodenya di sidang:

```text
nkp_inventaris/
├── app/
│   ├── Config/
│   │   └── Database.php          # Koneksi database PDO aman
│   ├── Controllers/
│   │   ├── AuthController.php    # Login, Logout, Session guard
│   │   ├── DashboardController.php # Ringkasan statistik & alert stok
│   │   ├── ItemController.php    # Master barang & mutasi stok
│   │   ├── SupplierController.php # Master vendor rekanan
│   │   ├── PRController.php      # Form pengajuan & approval PR
│   │   ├── POController.php      # Penerbitan PO & Cetak PDF
│   │   └── GRController.php      # Penerimaan barang & auto-sync stok
│   ├── Models/                   # Query database terisolasi
│   └── Helpers/
│       ├── AuthHelper.php        # Pengecekan role & izin halaman
│       └── FormatHelper.php      # Format mata uang Rupiah, tanggal ID, nomor nota
├── public/
│   ├── css/
│   ├── js/
│   └── index.php                 # Front controller
├── views/
│   ├── layouts/
│   │   ├── header.php            # Navbar, user badge, sidebar toggle
│   │   ├── sidebar.php           # Navigasi menu dinamis per role
│   │   └── footer.php
│   ├── auth/
│   │   └── login.php             # Form login elegan dengan latar industrial
│   ├── dashboard/
│   ├── items/                    # CRUD data barang & kartu stok
│   ├── suppliers/                # CRUD vendor
│   ├── pr/                       # List PR, Buat PR, Review PR
│   ├── po/                       # List PO, Buat PO, Print View A4
│   └── gr/                       # Verifikasi barang masuk
├── database/
│   ├── schema.sql                # Skema DDL tabel MySQL
│   └── seeders.sql               # Data awal realistis PT NKP (Barang, Supplier, User)
├── PRD.md                        # Dokumen spesifikasi kebutuhan produk
└── AGENTS.md                     # File panduan ini
```

---

## 4. Standar Kode & Keamanan (*Coding Rules*)

1. **Keamanan Database:** Wajib menggunakan **PDO Prepared Statements** dengan *parameter binding*. Dilarang keras menggabungkan variabel string langsung ke dalam SQL query (`SELECT * FROM users WHERE id = " . $id`).
2. **Validasi Session & Role-Based Access Control:**
   - Halaman Approval hanya boleh diakses oleh role `supervisor` dan `admin`.
   - Halaman Penerbitan PO hanya boleh diakses oleh role `purchasing` dan `admin`.
   - Halaman Goods Receipt hanya boleh diakses oleh role `warehouse` dan `admin`.
3. **Data Integrity (Database Transactions):**
   - Saat proses konfirmasi Goods Receipt (GR), gunakan PDO Transaction (`$pdo->beginTransaction()`, `$pdo->commit()`, `$pdo->rollBack()`) karena proses melibatkan:
     1. Insert data ke tabel `goods_receipts`.
     2. Update status `purchase_orders` menjadi `closed/partial`.
     3. Update jumlah kolom `stock` di tabel `items`.
     4. Insert riwayat perubahan ke tabel `stock_mutations`.
4. **Data Dummy Realistis:** Selalu gunakan nama barang, kategori, supplier, dan kode part yang mencerminkan industri otomotif PT Nandya Karya Perkasa (contoh: *Sensor Proximity Omron, Baut Hex Flange M6x12, Plat SPHC 1.2mm, Dies Upper Punch Pin, Sarung Tangan Safety Kevlar*).

---

## 5. Panduan Presentasi Sidang PKL

Saat mempresentasikan sistem ini kepada guru/dosen penguji:
1. **Fokuskan pada Solusi Masalah:** Jelaskan bagaimana sistem ini mengubah proses manual berbasis kertas menjadi alur digital terpadu tanpa risiko kehilangan arsip.
2. **Demonstrasikan Fitur Unggulan:**
   - Peringatan warna merah saat stok sparepart di bawah batas aman (*Safety Stock Alert*).
   - Satu klik konfirmasi barang datang langsung memperbarui stok fisik gudang.
   - Dokumen PO siap cetak dengan tata letak profesional lengkap dengan barcode dan kop PT NKP.
