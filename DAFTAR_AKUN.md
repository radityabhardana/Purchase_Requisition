# 📑 Buku Panduan Akun Pengguna & Hak Akses (SIP-NKP)
**Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa**  
*Plant Komponen Otomotif — Narogong Km. 16, Cileungsi, Bogor*

---

> [!NOTE]
> **Tautan Akses Sistem Lokal:**
> - URL Aplikasi: [http://localhost/nkp_inventaris/](http://localhost/nkp_inventaris/)
> - Server Basis Data: MySQL / MariaDB (Port 3306)
> - Seluruh akun menggunakan kata sandi bawaan seragam: **`password123`**
> - Username disederhanakan menggunakan **nama depan** karyawan (huruf kecil).

---

## 1. Tabel Kredensial Login Cepat

Gunakan akun di bawah ini untuk menguji hak akses masing-masing departemen atau saat simulasi sidang PKL:

| No | Username | Password | Peran (Role) | Nama Lengkap | Departemen / Divisi | Tahap Alur Utama |
| :-: | :--- | :---: | :---: | :--- | :--- | :---: |
| 1 | **`budi`** | `password123` | `admin` | Budi Santoso | Information Technology (IT) | **Akses Penuh** 
| 2 | **`hendra`** | `password123` | `supervisor` | Ir. Hendra Gunawan | Production & Maintenance | **Tahap 2 (Approval)** |
| 3 | **`siti`** | `password123` | `purchasing` | Siti Rahmawati | Procurement & Purchasing | **Tahap 3 (Terbit PO)** |
| 4 | **`doni`** | `password123` | `purchasing` | Doni Hermawan | Procurement & Purchasing | **Tahap 3 (Vendor/PO)** |
| 5 | **`agus`** | `password123` | `warehouse` | Agus Setiawan | Warehouse & Logistics | **Tahap 4 (Terima GR)** |
| 6 | **`rizky`** | `password123` | `requester` | Rizky Pratama | Tooling & Stamping Press | **Tahap 1 (Ajukan PR)** |

---

## 2. Rincian Wewenang per Akun Karyawan

### 🛠️ 1. Rizky Pratama — Teknisi Mesin Stamping
- **Username:** `rizky` | **Password:** `password123` | **Role:** `requester`
- **Fokus Kerja:** Tahap 1 (Pengajuan Kebutuhan Barang)
- **Tanggung Jawab:**
  - Mengidentifikasi suku cadang mesin press yang rusak atau menipis di area lini perakitan.
  - Mengisi formulir **Purchase Requisition (PR)** dengan menentukan target tanggal dan tingkat urgensi (*Normal / Urgent / Emergency*).
  - Memantau status persetujuan dokumen PR miliknya secara *real-time*.

---

### 📋 2. Ir. Hendra Gunawan — Supervisor Produksi
- **Username:** `hendra` | **Password:** `password123` | **Role:** `supervisor`
- **Fokus Kerja:** Tahap 2 (Review & Persetujuan Dokumen)
- **Tanggung Jawab:**
  - Menerima antrean PR yang diajukan oleh para teknisi pabrik.
  - Memeriksa justifikasi pemakaian suku cadang dan anggaran pemeliharaan.
  - Melakukan **Approval (Setujui)** agar dokumen diteruskan ke Purchasing, atau **Reject (Tolak)** disertai alasan penolakan tertulis.

---

### 🏢 3. Siti Rahmawati & Doni Hermawan — Tim Purchasing
- **Username:** `siti` / `doni` | **Password:** `password123` | **Role:** `purchasing`
- **Fokus Kerja:** Tahap 3 (Kontrak Pesanan Vendor & PO)
- **Tanggung Jawab:**
  - Mengonversi dokumen PR yang telah disetujui Supervisor menjadi **Purchase Order (PO)** resmi.
  - Memilih vendor rekanan resmi PT NKP, menetapkan harga kesepakatan, dan kalkulasi PPN.
  - Mencetak lembar fisik PO standar **ISO 9001 / IATF 16949 (Format A4)** lengkap dengan kop perusahaan dan tanda tangan resmi.
  - Mengelola katalog dan data kontak vendor rekanan (*Supplier Management*).

---

### 📦 4. Agus Setiawan — Petugas Gudang & Logistik
- **Username:** `agus` | **Password:** `password123` | **Role:** `warehouse`
- **Fokus Kerja:** Tahap 4 (Penerimaan Fisik & Update Stok) & Pengeluaran Barang (Surat Jalan)
- **Tanggung Jawab:**
  - Menyambut kiriman barang dari ekspedisi/sopir vendor di area *Receiving Dock*.
  - Memeriksa nomor Surat Jalan (Delivery Note) dan hasil inspeksi mutu (*QC Passed / Rejected*).
  - Melakukan konfirmasi **Goods Receipt (GR)** yang secara otomatis menambah stok fisik (*Audit Trail IN*).
  - Menerbitkan **Surat Jalan (Delivery Note)** resmi PT NKP untuk pengiriman part/komponen ke customer atau antar-plant, yang secara otomatis:
    1. Mengurangi stok fisik di rak gudang (*Stock OUT*).
    2. Mencatat mutasi keluar pada kartu stok inventaris.
    3. Mencetak lembar fisik Surat Jalan format A4 standar ISO lengkap dengan 4 kolom tanda tangan.

---

### 💻 5. Budi Santoso — Administrator Sistem IT
- **Username:** `budi` | **Password:** `password123` | **Role:** `admin`
- **Fokus Kerja:** Administrasi, Keamanan, & Pemeliharaan
- **Tanggung Jawab:**
  - Mengelola master akun seluruh pengguna pabrik (tambah, perbarui profil, dan hapus akun).
  - Memantau kata sandi karyawan melalui fitur intip password di menu **Manajemen Akun User**.
  - Melakukan pengawasan menyeluruh terhadap transaksi database dan backup sistem.

---

## 3. Matriks Batasan Hak Akses (Role-Based Access Control)

Setiap role dibatasi secara ketat (*Separation of Duties*) untuk mencegah manipulasi data dan pesanan fiktif:

| Modul & Aksi Menu | Teknisi (`rizky`) | SPV (`hendra`) | Purchasing (`siti`) | Gudang (`agus`) | Admin IT (`budi`) |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **Tahap 1: Formulir Buat PR** | ✅ *(Milik Sendiri)* | ✅ *(Semua)* | ✅ *(Semua)* | ✅ *(Semua)* | ✅ *(Semua)* |
| **Tahap 2: Setujui / Tolak PR** | ❌ Akses Ditolak | ✅ **Wewenang Utama** | ❌ Akses Ditolak | ❌ Akses Ditolak | ✅ Akses Penuh |
| **Tahap 3: Terbitkan PO & Cetak A4** | ❌ Akses Ditolak | ❌ Akses Ditolak | ✅ **Wewenang Utama** | ❌ Akses Ditolak | ✅ Akses Penuh |
| **Tahap 4: Konfirmasi GR & Stok** | ❌ Akses Ditolak | ❌ Akses Ditolak | ❌ Akses Ditolak | ✅ **Wewenang Utama** | ✅ Akses Penuh |
| **Surat Jalan (Delivery Note & OUT)** | 👁️ Hanya Lihat | 👁️ Hanya Lihat | 👁️ Hanya Lihat | ✅ **Wewenang Utama** | ✅ Akses Penuh |
| **Katalog Suku Cadang & Rak** | 👁️ Hanya Lihat | 👁️ Hanya Lihat | 👁️ Hanya Lihat | ✅ Tambah / Edit | ✅ Akses Penuh |
| **Kartu Mutasi Stok Masuk/Keluar** | 👁️ Hanya Lihat | 👁️ Hanya Lihat | 👁️ Hanya Lihat | 👁️ Hanya Lihat | ✅ Akses Penuh |
| **Kelola Mitra Vendor Supplier** | ❌ Menu Tersembunyi | ❌ Menu Tersembunyi | ✅ Kelola Rekanan | ❌ Menu Tersembunyi | ✅ Akses Penuh |
| **Manajemen Akun & Kata Sandi** | ❌ Menu Tersembunyi | ❌ Menu Tersembunyi | ❌ Menu Tersembunyi | ❌ Menu Tersembunyi | ✅ **Khusus Admin** |

---

## 4. Panduan Simulasi Alur Sidang PKL (5 Langkah Berurutan)

Gunakan alur demonstrasi di bawah ini saat mempresentasikan sistem kepada dewan penguji:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  LANGKAH 1 ➔ TEKNISI (user: rizky | pass: password123)                                │
│  • Masuk ke sistem, klik menu "1. Pengajuan PR".                                       │
│  • Isi pengajuan kebutuhan part darurat (misal: Hydraulic Seal Kit 5 Set).             │
│  • Klik "Kirim Pengajuan PR" ➔ Status menjadi: PENDING.                                │
└────────────────────────────────────┬───────────────────────────────────────────────────┘
                                     │
                                     ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  LANGKAH 2 ➔ SUPERVISOR (user: hendra | pass: password123)                             │
│  • Masuk ke menu "2. Persetujuan SPV".                                                 │
│  • Buka rincian PR ➔ Klik tombol hijau "Setujui (Approve)".                            │
│  • Dokumen terverifikasi ➔ Status berubah menjadi: APPROVED.                           │
└────────────────────────────────────┬───────────────────────────────────────────────────┘
                                     │
                                     ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  LANGKAH 3 ➔ PURCHASING (user: siti | pass: password123)                               │
│  • Buka menu "3. Purchase Order" ➔ Terpajang notifikasi PR siap diproses.             │
│  • Klik "Terbitkan PO", pilih supplier rekanan, tentukan PPN 11%.                      │
│  • Klik "Terbitkan Dokumen PO Resmi" ➔ Tampilkan lembar cetak standar ISO A4.          │
└────────────────────────────────────┬───────────────────────────────────────────────────┘
                                     │
                                     ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  LANGKAH 4 ➔ WAREHOUSE GUDANG (user: agus | pass: password123)                         │
│  • Buka menu "4. Penerimaan (GR)" ➔ Pilih PO aktif yang baru terbit.                   │
│  • Masukkan Nomor Surat Jalan vendor & pastikan status QC "Passed".                    │
│  • Klik "Konfirmasi Penerimaan" ➔ Buka menu "Katalog & Stok", buktikan stok bertambah. │
│  • Buka menu "Kartu Mutasi", tunjukkan riwayat audit mutasi IN otomatis tercatat.      │
└────────────────────────────────────┬───────────────────────────────────────────────────┘
                                     │
                                     ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  LANGKAH 5 ➔ ADMINISTRATOR IT (user: budi | pass: password123)                         │
│  • Buka menu "Manajemen Akun User".                                                    │
│  • Tunjukkan daftar akun karyawan dan fitur intip kata sandi teks biasa.              │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

> [!IMPORTANT]
> **Kebijakan Penyimpanan Kata Sandi:**
> Password pengguna disimpan dalam bentuk teks biasa (*plain-text*) sesuai instruksi manajemen IT internal PT Nandya Karya Perkasa. Tujuannya adalah agar Administrator IT dapat mengawasi kredensial seluruh staf operasional pabrik dan membantu pemulihan akses secara instan saat terjadi kendala pergantian giliran kerja (*shift*) tanpa jeda birokrasi *token reset*.
