# DATABASE.md - Kamus Data & Perancangan Basis Data
## Proyek: SIP-NKP (Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa)

Dokumen ini berisi spesifikasi teknis basis data (*Data Dictionary* / Kamus Data) yang dapat langsung dimasukkan ke dalam **Bab 3 (Metodologi & Perancangan Sistem)** pada Laporan PKL.

---

## 1. Spesifikasi Teknis DBMS
- **Database Management System:** MySQL 8.0 / MariaDB 10.4+
- **Collation:** `utf8mb4_unicode_ci`
- **Storage Engine:** `InnoDB` (Wajib untuk mendukung *Foreign Key Constraints* dan *ACID Transactions*)
- **Nama Database Default:** `nkp_inventaris`

---

## 2. Kamus Data Lengkap (Data Dictionary)

### 2.1 Tabel `users` (Data Akun & Hak Akses)
Menyimpan identitas pengguna sistem beserta peran (*role*) dan departemen kerjanya.

| Nama Kolom | Tipe Data | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT AUTO_INCREMENT | PRIMARY KEY | ID unik pengguna |
| `name` | VARCHAR(100) | NOT NULL | Nama lengkap karyawan |
| `username` | VARCHAR(50) | NOT NULL, UNIQUE | Username untuk login sistem |
| `password` | VARCHAR(255) | NOT NULL | Password terenkripsi (Bcrypt hash) |
| `role` | ENUM | NOT NULL | `'admin'`, `'supervisor'`, `'purchasing'`, `'warehouse'`, `'requester'` |
| `department` | VARCHAR(50) | NOT NULL | Stamping Press, Tooling, Maintenance, QC, Logistics, Purchasing |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu akun dibuat |

---

### 2.2 Tabel `suppliers` (Master Vendor Rekanan)
Menyimpan data vendor atau pemasok suku cadang/material resmi PT Nandya Karya Perkasa.

| Nama Kolom | Tipe Data | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT AUTO_INCREMENT | PRIMARY KEY | ID unik vendor |
| `supplier_code` | VARCHAR(20) | NOT NULL, UNIQUE | Kode vendor (contoh: `VND-001`, `VND-002`) |
| `company_name` | VARCHAR(150) | NOT NULL | Nama resmi perusahaan rekanan (PT/CV) |
| `contact_person` | VARCHAR(100) | NOT NULL | Nama kontak sales / penanggung jawab |
| `phone` | VARCHAR(30) | NOT NULL | Nomor telepon / WhatsApp kantor vendor |
| `email` | VARCHAR(100) | NOT NULL | Alamat email pemesanan PO vendor |
| `address` | TEXT | NOT NULL | Alamat kantor / pabrik supplier |
| `payment_term` | VARCHAR(50) | NOT NULL | Termin pembayaran: `'COD'`, `'Net 30'`, `'Net 60'` |
| `is_active` | TINYINT(1) | DEFAULT 1 | Status rekanan: 1 = Aktif, 0 = Nonaktif |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu data dicatat |

---

### 2.3 Tabel `items` (Master Barang & Suku Cadang Inventaris)
Menyimpan data katalog barang, suku cadang mesin, dan bahan pembantu yang dikelola pabrik.

| Nama Kolom | Tipe Data | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT AUTO_INCREMENT | PRIMARY KEY | ID unik barang |
| `item_code` | VARCHAR(30) | NOT NULL, UNIQUE | Kode part / SKU (contoh: `SPR-PRS-001`, `RAW-STL-002`) |
| `name` | VARCHAR(150) | NOT NULL | Nama barang (contoh: *Sensor Proximity Omron E2B*) |
| `category` | ENUM | NOT NULL | `'Mechanical'`, `'Electrical'`, `'Raw Material'`, `'Consumables'`, `'Safety/APD'` |
| `unit` | VARCHAR(20) | NOT NULL | Satuan ukuran: `'Pcs'`, `'Box'`, `'Liter'`, `'Roll'`, `'Kg'`, `'Set'` |
| `stock` | INT | NOT NULL, DEFAULT 0 | Kuantitas stok fisik terkini di gudang |
| `min_stock` | INT | NOT NULL, DEFAULT 5 | Batas minimum aman (*Safety Stock Threshold*) |
| `location_rack`| VARCHAR(50) | NOT NULL | Letak rak penyimpanan (contoh: `Rak-A2-04`, `Bin-C-12`) |
| `unit_price` | DECIMAL(15,2) | NOT NULL, DEFAULT 0.00 | Estimasi harga satuan standar (IDR) |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu barang didaftarkan |
| `updated_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Waktu terakhir stok diperbarui |

---

### 2.4 Tabel `purchase_requisitions` (Form Permintaan Pembelian / PR)
Mencatat dokumen pengajuan pengadaan dari departemen yang membutuhkan barang.

| Nama Kolom | Tipe Data | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT AUTO_INCREMENT | PRIMARY KEY | ID unik PR |
| `pr_number` | VARCHAR(40) | NOT NULL, UNIQUE | Nomor seri PR (contoh: `PR/NKP/2026/09/0001`) |
| `user_id` | INT | FOREIGN KEY (`users.id`) | ID karyawan pemohon |
| `approved_by` | INT NULL | FOREIGN KEY (`users.id`) | ID supervisor yang menyetujui/menolak |
| `pr_date` | DATE | NOT NULL | Tanggal dokumen PR diajukan |
| `target_date` | DATE | NOT NULL | Tanggal estimasi barang dibutuhkan di lini pabrik |
| `priority` | ENUM | NOT NULL, DEFAULT `'Normal'` | Tingkat urgensi: `'Normal'`, `'Urgent'`, `'Emergency'` |
| `status` | ENUM | NOT NULL, DEFAULT `'Pending'` | Status pengajuan: `'Pending'`, `'Approved'`, `'Rejected'`, `'PO Issued'` |
| `rejection_notes`| TEXT NULL | - | Catatan alasan supervisor menolak pengajuan |
| `general_notes`| TEXT NULL | - | Keterangan tujuan pemakaian atau nomor mesin |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Timestamp sistem |

---

### 2.5 Tabel `pr_items` (Rincian Item dalam PR)
Menghubungkan dokumen PR dengan daftar barang-barang yang diminta.

| Nama Kolom | Tipe Data | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT AUTO_INCREMENT | PRIMARY KEY | ID unik baris rincian |
| `pr_id` | INT | FOREIGN KEY (`purchase_requisitions.id`) | Relasi ke dokumen PR (ON DELETE CASCADE) |
| `item_id` | INT | FOREIGN KEY (`items.id`) | Relasi ke master barang |
| `qty_requested`| INT | NOT NULL | Jumlah kuantitas yang diminta |
| `remarks` | VARCHAR(255) NULL | - | Keterangan detail per item (contoh: *Untuk Mesin Komatsu 160T*) |

---

### 2.6 Tabel `purchase_orders` (Dokumen Purchase Order / PO)
Mencatat pesanan resmi yang diterbitkan staf Purchasing ke supplier rekanan.

| Nama Kolom | Tipe Data | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT AUTO_INCREMENT | PRIMARY KEY | ID unik PO |
| `po_number` | VARCHAR(40) | NOT NULL, UNIQUE | Nomor resmi PO (contoh: `PO/NKP/2026/09/0001`) |
| `pr_id` | INT | FOREIGN KEY (`purchase_requisitions.id`) | Referensi dokumen PR asal |
| `supplier_id` | INT | FOREIGN KEY (`suppliers.id`) | Rekanan vendor yang ditunjuk |
| `created_by` | INT | FOREIGN KEY (`users.id`) | Staf purchasing yang menerbitkan |
| `po_date` | DATE | NOT NULL | Tanggal penerbitan PO resmi |
| `delivery_deadline` | DATE | NOT NULL | Batas tanggal supplier wajib mengantar barang (*Lead Time*) |
| `subtotal` | DECIMAL(15,2) | NOT NULL | Total harga sebelum pajak |
| `tax_percent` | DECIMAL(5,2) | NOT NULL, DEFAULT 11.00 | Persentase PPN Indonesia (11%) |
| `tax_amount` | DECIMAL(15,2) | NOT NULL | Nilai rupiah PPN |
| `grand_total` | DECIMAL(15,2) | NOT NULL | Total akhir yang harus dibayarkan (Subtotal + PPN) |
| `status` | ENUM | NOT NULL, DEFAULT `'Issued'` | Status pesanan: `'Issued'`, `'Partial Received'`, `'Completed'`, `'Cancelled'` |
| `notes` | TEXT NULL | - | Syarat dan instruksi pengiriman ke pabrik Cileungsi |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Timestamp penerbitan |

---

### 2.7 Tabel `po_items` (Rincian Item dalam PO)
Menyimpan daftar kuantitas dan harga satuan kesepakatan dengan supplier.

| Nama Kolom | Tipe Data | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT AUTO_INCREMENT | PRIMARY KEY | ID unik baris rincian PO |
| `po_id` | INT | FOREIGN KEY (`purchase_orders.id`) | Relasi ke dokumen PO (ON DELETE CASCADE) |
| `item_id` | INT | FOREIGN KEY (`items.id`) | Relasi ke master barang |
| `qty_ordered` | INT | NOT NULL | Jumlah kuantitas yang dipesan ke vendor |
| `unit_price` | DECIMAL(15,2) | NOT NULL | Harga per unit satuan dari vendor |
| `subtotal` | DECIMAL(15,2) | NOT NULL | Hasil kali: `qty_ordered * unit_price` |

---

### 2.8 Tabel `goods_receipts` (Penerimaan Barang / GR)
Mencatat bukti kedatangan barang fisik di pos gudang penerimaan (*receiving dock*).

| Nama Kolom | Tipe Data | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT AUTO_INCREMENT | PRIMARY KEY | ID unik tanda penerimaan |
| `gr_number` | VARCHAR(40) | NOT NULL, UNIQUE | Nomor seri LPB/GR (contoh: `GR/NKP/2026/09/0001`) |
| `po_id` | INT | FOREIGN KEY (`purchase_orders.id`) | Dokumen PO yang diterima |
| `received_by` | INT | FOREIGN KEY (`users.id`) | Staf gudang yang memverifikasi fisik |
| `delivery_note_no` | VARCHAR(100) | NOT NULL | Nomor Surat Jalan resmi dari sopir supplier |
| `received_date`| DATE | NOT NULL | Tanggal fisik barang diturunkan di gudang |
| `status` | ENUM | NOT NULL, DEFAULT `'Completed'` | `'Completed'`, `'Partial'` |
| `notes` | TEXT NULL | - | Catatan kondisi paket / fisik kemasan |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Timestamp penerimaan |

---

### 2.9 Tabel `gr_items` (Rincian Item Barang yang Diterima)
Menyimpan jumlah fisik yang diterima per barang beserta verifikasi mutu (*QC Check*).

| Nama Kolom | Tipe Data | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT AUTO_INCREMENT | PRIMARY KEY | ID rincian GR |
| `gr_id` | INT | FOREIGN KEY (`goods_receipts.id`) | Relasi ke dokumen GR (ON DELETE CASCADE) |
| `item_id` | INT | FOREIGN KEY (`items.id`) | Relasi ke barang |
| `qty_received`| INT | NOT NULL | Jumlah kuantitas fisik yang tiba & sesuai |
| `qc_status` | ENUM | NOT NULL, DEFAULT `'Passed'` | Hasil uji QC: `'Passed'`, `'Rejected'`, `'Rework'` |
| `notes` | VARCHAR(255) NULL | - | Catatan jika ada barang cacat/penyok |

---

### 2.10 Tabel `stock_mutations` (Buku Besar Mutasi Kartu Stok)
Catatan audit riwayat keluar-masuknya barang untuk menjaga transparansi dan akuntabilitas stok gudang.

| Nama Kolom | Tipe Data | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT AUTO_INCREMENT | PRIMARY KEY | ID mutasi |
| `item_id` | INT | FOREIGN KEY (`items.id`) | Relasi ke barang yang bermutasi |
| `mutation_type`| ENUM | NOT NULL | `'IN'` (Barang Masuk) atau `'OUT'` (Barang Keluar/Pemakaian/Surat Jalan) |
| `reference_no` | VARCHAR(50) | NOT NULL | Nomor dokumen rujukan (Nomor GR atau Nomor SJ) |
| `qty_in` | INT | NOT NULL, DEFAULT 0 | Jumlah unit yang bertambah |
| `qty_out` | INT | NOT NULL, DEFAULT 0 | Jumlah unit yang berkurang |
| `balance` | INT | NOT NULL | Sisa stok akhir setelah mutasi terjadi |
| `notes` | VARCHAR(255) NULL | - | Keterangan mutasi (contoh: *Penerimaan PO* atau *Surat Jalan AHM*) |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu pencatatan mutasi |

---

### 2.11 Tabel `delivery_notes` (Header Surat Jalan / Pengiriman Barang)
Dokumen resmi pengeluaran barang dari gudang pabrik PT NKP ke pelanggan atau antar-plant.

| Nama Kolom | Tipe Data | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT AUTO_INCREMENT | PRIMARY KEY | ID unik dokumen Surat Jalan |
| `sj_number` | VARCHAR(40) | UNIQUE, NOT NULL | Nomor seri resmi: `SJ/NKP/YYYY/MM/XXXX` |
| `created_by` | INT | FOREIGN KEY (`users.id`) | Petugas gudang/logistik pembuat dokumen |
| `recipient_type` | ENUM | NOT NULL, DEFAULT `'Customer'` | Kategori: `'Customer'`, `'Vendor/Subcont'`, `'Internal Plant'` |
| `recipient_name` | VARCHAR(150) | NOT NULL | Nama perusahaan / entitas penerima barang |
| `recipient_address` | TEXT | NOT NULL | Alamat tujuan pengantaran barang |
| `customer_po_no` | VARCHAR(100) NULL | - | Nomor pesanan / PO dari customer |
| `vehicle_no` | VARCHAR(30) | NOT NULL | Nomor polisi kendaraan ekspedisi (plat truk) |
| `driver_name` | VARCHAR(100) | NOT NULL | Nama pengemudi armada logistik |
| `delivery_date` | DATE | NOT NULL | Tanggal barang diberangkatkan |
| `status` | ENUM | NOT NULL, DEFAULT `'Shipped'` | Status: `'Draft'`, `'Shipped'`, `'Delivered'`, `'Cancelled'` |
| `notes` | TEXT NULL | - | Instruksi khusus penanganan muatan |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Timestamp penerbitan dokumen |

---

### 2.12 Tabel `delivery_note_items` (Rincian Barang Surat Jalan)
Daftar suku cadang atau part yang dimuat ke dalam armada beserta jenis kemasan.

| Nama Kolom | Tipe Data | Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | INT AUTO_INCREMENT | PRIMARY KEY | ID rincian item pengiriman |
| `delivery_note_id` | INT | FOREIGN KEY (`delivery_notes.id`) | Relasi ke Surat Jalan (ON DELETE CASCADE) |
| `item_id` | INT | FOREIGN KEY (`items.id`) | Relasi ke master barang (ON DELETE RESTRICT) |
| `qty_shipped` | INT | NOT NULL | Kuantitas unit yang dikeluarkan |
| `packaging` | VARCHAR(50) | NOT NULL, DEFAULT `'Box / Pallet'` | Jenis kemasan logistik (Box, Pallet, dsb) |
| `remarks` | VARCHAR(255) NULL | - | Keterangan lot part / nomor cetakan |

---

## 3. Logika Transaksi & Integritas Data (Database Transaction)

### 3.1 Transaksi Penerimaan Barang (Goods Receipt - Stock IN)
```sql
START TRANSACTION;

-- 1. Catat dokumen penerimaan barang
INSERT INTO goods_receipts (gr_number, po_id, received_by, delivery_note_no, received_date)
VALUES ('GR/NKP/2026/09/0001', 1, 4, 'SJ-VENDOR-8891', CURDATE());

-- 2. Tambah kuantitas fisik di master barang
UPDATE items 
SET stock = stock + 10 
WHERE id = 5;

-- 3. Catat kartu stok (Audit Trail IN)
INSERT INTO stock_mutations (item_id, mutation_type, reference_no, qty_in, qty_out, balance, notes)
VALUES (5, 'IN', 'GR/NKP/2026/09/0001', 10, 0, 25, 'Penerimaan PO PO/NKP/2026/09/0001');

-- 4. Update status PO menjadi Completed jika seluruh item telah diterima
UPDATE purchase_orders 
SET status = 'Completed' 
WHERE id = 1;

COMMIT;
```

### 3.2 Transaksi Pengeluaran Barang (Surat Jalan - Stock OUT)
```sql
START TRANSACTION;

-- 1. Catat dokumen Surat Jalan
INSERT INTO delivery_notes (sj_number, created_by, recipient_type, recipient_name, recipient_address, vehicle_no, driver_name, delivery_date, status)
VALUES ('SJ/NKP/2026/09/0003', 4, 'Customer', 'PT Astra Honda Motor', 'Kawasan MM2100', 'B 9481 NKP', 'Mulyadi', CURDATE(), 'Shipped');

-- 2. Kurangi kuantitas fisik di master barang (Validasi stock >= qty_shipped)
UPDATE items 
SET stock = stock - 50 
WHERE id = 7 AND stock >= 50;

-- 3. Catat kartu mutasi pengeluaran (Audit Trail OUT)
INSERT INTO stock_mutations (item_id, mutation_type, reference_no, qty_in, qty_out, balance, notes)
VALUES (7, 'OUT', 'SJ/NKP/2026/09/0003', 0, 50, 1200, 'Pengiriman Surat Jalan ke PT Astra Honda Motor (B 9481 NKP)');

COMMIT;
```
Jika salah satu dari perintah di atas gagal atau stok barang tidak mencukupi, sistem secara otomatis mengeksekusi `ROLLBACK;` sehingga tidak akan terjadi selisih antara stok fisik dan catatan sistem.
