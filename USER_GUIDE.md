# USER_GUIDE.md - Buku Manual Penggunaan Sistem
## Proyek: SIP-NKP (Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa)

Buku panduan ini mendokumentasikan langkah-langkah penggunaan aplikasi untuk setiap role pengguna. Berkas ini dapat dijadikan lampiran atau isi dari **Bab 4 (Implementasi dan Pengujian Sistem)** pada Laporan PKL.

---

## 1. Panduan Masuk ke Sistem (Login)

1. Buka peramban web (*Google Chrome* atau *Microsoft Edge*).
2. Masukkan alamat URL: `http://localhost/nkp_inventaris/` (atau `http://nkp_inventaris.test/`).
3. Pada halaman login:
   - Masukkan **Username** (contoh: `teknisi_press`, `spv_stamping`, `purchasing`, atau `warehouse`).
   - Masukkan **Password** (default: `password123`).
   - Klik tombol **"Masuk ke Sistem"**.
4. Sistem akan secara otomatis mengarahkan ke dashboard yang sesuai dengan wewenang (*role*) masing-masing pengguna.

---

## 2. Panduan Role 1: Requester (Teknisi / Staff Pemohon)

### 2.1 Memeriksa Ketersediaan Stok
1. Klik menu **"Katalog Barang"** pada bilah navigasi kiri (*Sidebar*).
2. Gunakan kolom pencarian untuk mengetik nama atau kode part suku cadang (contoh: *"Proximity"*, *"Seal"*).
3. Perhatikan kolom **Stok Saat Ini** dan **Batas Minimum**:
   - Jika berwarna **Hijau**: Stok masih aman.
   - Jika berwarna **Merah (*Badge Warning*)**: Stok sudah menipis atau habis.

### 2.2 Membuat Pengajuan Pembelian Baru (Purchase Requisition - PR)
1. Klik menu **"Permintaan Pembelian (PR)"**, lalu klik tombol **"+ Buat Pengajuan PR"**.
2. Isi formulir dengan data yang dibutuhkan:
   - **Target Tanggal Dibutuhkan:** Pilih tanggal barang harus tiba di lini produksi.
   - **Tingkat Urgensi:** Pilih *Normal* (kebutuhan rutin) atau *Urgent* (mesin berhenti beroperasi).
   - **Keterangan Keperluan:** Tuliskan alasan spesifik (contoh: *"Penggantian berkala punch pin Dies AHM Part No 1234"*).
3. Pada tabel item:
   - Pilih barang dari menu *dropdown*.
   - Masukkan jumlah (*quantity*) yang diminta.
   - Klik **"+ Tambah Baris"** jika ada lebih dari satu barang.
4. Klik tombol **"Kirim Pengajuan PR"**. Dokumen akan berstatus `Pending` menunggu verifikasi pimpinan.

---

## 3. Panduan Role 2: Approver (Supervisor / Manager)

### 3.1 Memverifikasi Pengajuan PR dari Staff
1. Masuk menggunakan akun berhak akses approval (contoh: `spv_stamping`).
2. Pada dashboard utama, perhatikan kartu notifikasi **"PR Menunggu Persetujuan"**.
3. Klik menu **"Approval PR"** untuk melihat daftar pengajuan baru.
4. Klik tombol **"Detail"** pada baris PR yang ingin diperiksa:
   - Tinjau justifikasi pemakaian barang, jumlah yang diajukan, dan sisa stok fisik di gudang.
5. Menentukan Tindakan:
   - **Jika Disetujui:** Klik tombol hijau **"Setujui Pengajuan (Approve)"**. Status dokumen berubah menjadi `Approved` dan otomatis diteruskan ke bagian Purchasing.
   - **Jika Ditolak:** Klik tombol merah **"Tolak Pengajuan (Reject)"**, masukkan alasan penolakan pada kotak dialog (contoh: *"Gunakan stok alternatif di Rak B-02"*), lalu klik konfirmasi.

---

## 4. Panduan Role 3: Purchasing Officer

### 4.1 Mengelola Data Master Supplier / Vendor
1. Klik menu **"Master Data" ➔ "Data Supplier"**.
2. Untuk menambah vendor baru, klik tombol **"+ Tambah Supplier"**.
3. Lengkapi formulir: Kode Vendor, Nama Perusahaan, Alamat Pabrik, Kontak Sales, Nomor Telepon, Email, dan Syarat Pembayaran (*Term of Payment* / TOP).
4. Klik **"Simpan Data"**.

### 4.2 Menerbitkan Surat Pesanan Pembelian (Purchase Order - PO)
1. Klik menu **"Purchase Order (PO)"**, lalu pilih tab **"PR Siap Diproses"**.
2. Pilih nomor PR yang telah disetujui supervisor, lalu klik tombol **"Buat Dokumen PO"**.
3. Sistem akan memuat barang-barang dari PR tersebut:
   - Pilih **Supplier Rekanan** yang ditunjuk.
   - Masukkan **Batas Waktu Pengiriman (*Delivery Deadline*)**.
   - Masukkan **Harga Satuan Kesepakatan** dari penawaran vendor.
   - Sistem secara otomatis menghitung *Subtotal*, PPN 11%, dan *Grand Total*.
4. Klik **"Terbitkan PO Resmi"**. Sistem akan membuat nomor PO otomatis (contoh: `PO/NKP/2026/09/0001`).

### 4.3 Mencetak Dokumen PO Format Standar Industri
1. Pada daftar dokumen PO, klik tombol **"Cetak / PDF"** pada PO yang bersangkutan.
2. Tampilan dokumen resmi standar A4 PT Nandya Karya Perkasa akan terbuka:
   - Dilengkapi kop perusahaan, nomor registrasi dokumen, tabel barang bergaris tegas, dan 3 kolom tanda tangan (Dibuat, Disetujui, Diterima).
3. Tekan kombinasi tombol `Ctrl + P` untuk mencetak fisik atau memilih *Save as PDF*.

---

## 5. Panduan Role 4: Petugas Gudang (Warehouse / Receiving)

### 5.1 Menerima Kedatangan Barang dari Supplier
1. Saat kurir/sopir vendor tiba di pos penerimaan pabrik, klik menu **"Penerimaan Barang (GR)"**.
2. Klik tombol **"+ Input Penerimaan Barang"**.
3. Pilih **Nomor PO** yang tertera pada lembar Surat Jalan vendor.
4. Isi data verifikasi fisik:
   - **Nomor Surat Jalan:** Masukkan nomor dokumen resmi pengantar barang dari vendor.
   - **Tanggal Terima:** Tanggal fisik barang diturunkan di gudang.
5. Verifikasi Kuantitas Barang:
   - Masukkan jumlah fisik yang dihitung secara nyata.
   - Tentukan status pemeriksaan mutu: **Passed** (bagus/lolos) atau **Rejected** (cacat/rusak).
6. Klik tombol **"Konfirmasi Penerimaan (Simpan GR)"**.

### 5.2 Memeriksa Pembaruan Stok Otomatis
1. Segera setelah tombol konfirmasi ditekan, buka menu **"Kartu Stok / Mutasi"**.
2. Anda akan melihat baris baru bertipe `IN` dengan nomor referensi GR tersebut, dan kolom saldo akhir (*balance*) otomatis bertambah tanpa perlu penyesuaian manual.

### 5.3 Menerbitkan Surat Jalan Pengeluaran Barang (Delivery Note)
1. Saat bagian logistik akan mengirimkan komponen hasil produksi atau suku cadang ke pelanggan (misal: PT Astra Honda Motor) atau antar-plant:
2. Buka menu **"Surat Jalan (SJ)"** di sidebar, lalu klik tombol **"+ Buat Surat Jalan Baru"**.
3. Isi informasi ekspedisi & tujuan:
   - **Kategori & Nama Penerima:** Pilih kategori (Customer / Vendor / Plant) dan masukkan nama perusahaan tujuan.
   - **Alamat Lengkap:** Lokasi bongkar muat ekspedisi.
   - **No. Plat Kendaraan & Nama Pengemudi:** Armada dan supir yang bertugas mengantar.
   - **Nomor PO Pemesan:** No. Purchase Order dari pihak customer (jika ada).
4. Tambahkan rincian barang:
   - Pilih suku cadang / komponen dari katalog. Sistem akan otomatis menampilkan stok fisik yang tersedia di rak gudang.
   - Masukkan jumlah yang dikirim (**Qty Kirim**). Sistem secara otomatis membatasi agar tidak melebihi stok yang ada.
   - Masukkan jenis kemasan (misal: *Box / Pallet*) dan keterangan nomor lot.
5. Klik **"Konfirmasi & Terbitkan Surat Jalan"**.
6. Sistem akan langsung memotong stok fisik gudang (*Stock OUT*), mencatat riwayat ke Kartu Mutasi, dan membuka halaman **Cetak Surat Jalan A4** standar ISO lengkap dengan barcode dan 4 kolom tanda tangan resmi.
