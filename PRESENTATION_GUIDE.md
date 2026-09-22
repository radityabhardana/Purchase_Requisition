# PRESENTATION_GUIDE.md - Panduan Sidang & Pertanyaan Penguji PKL
## Proyek: SIP-NKP (Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa)

Dokumen ini disusun untuk membantu siswa dalam menghadapi **Sidang Pertanggungjawaban Laporan PKL / Uji Kompetensi Kejuruan (UKK)**. Berisi tips presentasi, simulasi tanya jawab teknis dari dewan penguji, serta draf **Bab 5 (Kesimpulan dan Saran)**.

---

## 1. Strategi Pembukaan Presentasi (3 Menit Pertama yang Memukau)

Gunakan pembukaan yang berfokus pada **solusi nyata atas masalah di pabrik**, bukan sekadar membacakan kode program:

> *"Selamat pagi/siang Bapak/Ibu Dewan Penguji. Terima kasih atas kesempatan yang diberikan.*  
> *Selama melaksanakan Praktik Kerja Lapangan (PKL) di **PT Nandya Karya Perkasa**, yang merupakan pabrik manufaktur komponen otomotif pemasok Astra Honda Motor, saya mengamati bahwa proses pengadaan suku cadang mesin press dan pencatatan barang gudang masih mengandalkan formulir kertas nota manual.*  
> *Hal ini berisiko menyebabkan berkas hilang, keterlambatan persetujuan, dan yang paling fatal adalah **stok habis mendadak yang dapat menghentikan lini produksi (Line Stop)**.*  
> *Oleh karena itu, saya merancang **SIP-NKP (Sistem Informasi Purchasing & Pengendalian Inventaris Terintegrasi)**. Sistem ini mendigitalkan alur pengadaan dari pengajuan teknisi, persetujuan atasan, penerbitan PO ke supplier, hingga saat barang datang di gudang, stok inventaris langsung bertambah secara otomatis tanpa perlu pencatatan ganda."*

---

## 2. Simulasi Tanya Jawab Dewan Penguji & Kunci Jawabannya

Berikut adalah pertanyaan yang **paling sering ditanyakan oleh penguji** beserta jawaban teknis yang teruji:

### Q1: *"Apa bedanya Purchase Requisition (PR) dan Purchase Order (PO)?"*
* **Kunci Jawaban:**  
  *"**PR (Purchase Requisition)** adalah dokumen internal perusahaan yang dibuat oleh divisi pemohon (misalnya teknisi mesin) untuk meminta izin pembelian barang kepada atasannya. Sedangkan **PO (Purchase Order)** adalah dokumen eksternal resmi yang diterbitkan oleh bagian Purchasing dan dikirimkan kepada pihak ketiga (Supplier/Vendor) sebagai kontrak pemesanan resmi yang sah."*

---

### Q2: *"Bagaimana sistem Anda mencegah terjadinya selisih stok saat barang datang?"*
* **Kunci Jawaban:**  
  *"Sistem menggunakan fitur **Goods Receipt (GR)** yang terintegrasi langsung dengan database melalui mekanisme **Database Transaction (ACID)**. Saat petugas gudang mengonfirmasi penerimaan barang, sistem secara atomik mengeksekusi penambahan stok pada master barang dan mencatat riwayat mutasi kartu stok dalam satu kesatuan. Jika terjadi gangguan jaringan saat proses simpan, sistem otomatis melakukan `Rollback`, sehingga data tidak akan pernah corrupt atau tercatat sebagian."*

---

### Q3: *"Bagaimana cara sistem memberi tahu bahwa stok barang sudah mau habis?"*
* **Kunci Jawaban:**  
  *"Pada master barang terdapat parameter `min_stock` (Safety Stock). Sistem menjalankan kueri perbandingan antara `stock <= min_stock`. Jika kondisi tersebut terpenuhi, sistem secara otomatis menandai barang tersebut dengan badge peringatan merah pada dashboard utama serta menampilkan tombol pintasan agar teknisi atau staf dapat langsung membuat pengajuan PR tanpa perlu mencari barang secara manual."*

---

### Q4: *"Kenapa Anda menggunakan prepared statements dalam kueri database?"*
* **Kunci Jawaban:**  
  *"Untuk menerapkan standar keamanan industri yaitu mencegah celah keamanan **SQL Injection**. Dengan PDO Prepared Statements dan parameter binding, kueri SQL dan data input pengguna dipisahkan, sehingga karakter berbahaya yang diinputkan pengguna tidak akan dieksekusi sebagai perintah basis data."*

---

### Q5: *"Apakah role Teknisi bisa menyetujui pengajuannya sendiri atau menerbitkan PO?"*
* **Kunci Jawaban:**  
  *"Tidak bisa, Pak/Bu. Sistem menerapkan **Role-Based Access Control (RBAC)** berbasis sesi server. Halaman approval diproteksi oleh middleware/session check yang memastikan hanya pengguna dengan role `supervisor` yang bisa mengaksesnya, dan halaman penerbitan PO hanya bisa diakses oleh role `purchasing`."*

---

## 3. Draf Bab 5 Laporan PKL: Kesimpulan dan Saran

Draf ini dapat langsung disalin ke naskah laporan bab penutup:

### 5.1 Kesimpulan
Berdasarkan hasil perancangan, implementasi, dan pengujian Sistem Informasi Purchasing dan Inventaris pada PT Nandya Karya Perkasa, dapat ditarik kesimpulan sebagai berikut:
1. Sistem berhasil mengintegrasikan seluruh siklus pengadaan barang manufaktur—mulai dari *Purchase Requisition (PR)*, verifikasi persetujuan pimpinan, penerbitan *Purchase Order (PO)*, hingga penerimaan fisik barang (*Goods Receipt*) ke dalam satu platform terpusat.
2. Fitur *Early Warning Safety Stock* terbukti efektif memberikan indikasi visual secara *real-time* terhadap suku cadang mesin yang berada di bawah batas minimum, sehingga meminimalisasi risiko terhentinya operasional lini produksi (*line stop*).
3. Penerapan integrasi otomatis (*auto-sync*) antara modul penerimaan barang dan kartu stok gudang berhasil mengeliminasi proses pencatatan berulang (*double entry*) serta meminimalkan potensi kesalahan pencatatan manusia (*human error*).

### 5.2 Saran
Untuk pengembangan sistem lebih lanjut pada masa mendatang, disarankan beberapa hal berikut:
1. **Integrasi Notifikasi Instan:** Menambahkan integrasi WhatsApp Gateway atau Bot Telegram API untuk mengirimkan notifikasi otomatis kepada Supervisor saat terdapat dokumen PR baru yang mendesak (*Urgent*).
2. **Implementasi Barcode Scanner Fisik:** Mengembangkan antarmuka pemindaian barcode/QR code menggunakan kamera gawai atau *handheld barcode scanner* guna mempercepat proses verifikasi barang di pos gudang penerimaan.
3. **Analisis Prediksi Stok:** Menerapkan metode peramalan kebutuhan (*Forecasting*) seperti *Economic Order Quantity (EOQ)* atau *Moving Average* untuk membantu bagian Purchasing memperkirakan volume pembelian bahan baku pada periode berikutnya.
