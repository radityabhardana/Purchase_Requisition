<?php
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Surat Jalan - <?= htmlspecialchars($dn['sj_number']) ?></title>
    
    <!-- Local Bootstrap 5.3.3 CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/bootstrap-custom.css">
    
    <!-- Local Lucide Icons -->
    <script src="js/lucide.min.js"></script>

    <style>
        @page {
            size: A4;
            margin: 10mm 12mm;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #0f172a;
            background-color: #f1f5f9;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        .page-container {
            width: 100%;
            max-width: 960px;
            background: #ffffff;
        }
        @media print {
            .no-print, .d-print-none {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .page-container {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="py-4 px-2 d-flex flex-column align-items-center">

<!-- TOOLBAR AKSI (HANYA MUNCUL DI LAYAR BROWSER) -->
<div class="page-container mb-3 d-flex align-items-center justify-content-between d-print-none">
    <a href="index.php?page=delivery-notes" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
        <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
        <span>Kembali ke Daftar Surat Jalan</span>
    </a>

    <div class="d-flex align-items-center gap-2">
        <button onclick="window.print()" class="btn btn-dark btn-sm fw-bold d-inline-flex align-items-center gap-2 shadow-sm text-warning">
            <i data-lucide="printer" style="width: 16px; height: 16px;"></i>
            <span>Cetak Dokumen Sekarang (Print / PDF)</span>
        </button>
    </div>
</div>

<!-- CONTAINER LEMBAR KERJA A4 RESMI PT NANDYA KARYA PERKASA -->
<div class="page-container border border-secondary-subtle shadow-sm rounded-3 p-4 p-sm-5 text-dark small leading-relaxed">
    
    <!-- KOP PERUSAHAAN PT NANDYA KARYA PERKASA -->
    <div class="d-flex align-items-start justify-content-between pb-3 border-bottom border-2 border-dark">
        <div class="d-flex align-items-center gap-3">
            <img src="images/logo.jpg" 
                 onerror="this.onerror=null; this.src='logo-nkp-kecil-2.png';" 
                 alt="PT NANDYA KARYA PERKASA" 
                 style="height: 52px; width: auto; object-fit: contain;">
            <div class="border-start border-2 border-secondary-subtle ps-3">
                <h1 class="h6 fw-bold mb-0 text-dark text-uppercase tracking-wider">PT NANDYA KARYA PERKASA</h1>
                <p class="mb-0 fw-semibold text-dark" style="font-size: 11px;">Automotive Component Manufacturer • Metal Stamping, Dies, Welding & Injection</p>
                <p class="mb-0 text-secondary" style="font-size: 10px;">Plant: Jl. Raya Narogong Km. 16, Cileungsi, Bogor 16820 - Jawa Barat | Telp: (021) 823-4567</p>
            </div>
        </div>

        <div class="text-end">
            <div class="font-mono text-muted text-uppercase fw-bold" style="font-size: 9px;">ISO 9001 / IATF 16949 Certified</div>
            <div class="font-mono text-muted" style="font-size: 9px;">Doc No: NKP-LOG-F-008 Rev.01</div>
            <div class="mt-1 d-inline-block px-2 py-0.5 bg-light rounded text-dark border border-secondary-subtle font-mono fw-bold" style="font-size: 9px;">
                ORIGINAL (LEMBAR 1)
            </div>
        </div>
    </div>

    <!-- JUDUL DOKUMEN -->
    <div class="text-center my-3">
        <h2 class="h5 fw-black text-uppercase tracking-wider text-dark mb-1">SURAT JALAN PENGIRIMAN BARANG</h2>
        <span class="font-mono text-secondary small">DELIVERY NOTE / GOODS ISSUE SLIP</span>
    </div>

    <!-- BARCODE SIMULASI & NOMOR DOKUMEN -->
    <div class="d-flex justify-content-between align-items-center bg-light p-2.5 rounded-2 border mb-3">
        <div>
            <span class="text-muted text-uppercase fw-bold" style="font-size: 10px;">NOMOR SURAT JALAN:</span>
            <div class="font-mono fw-black text-dark fs-6"><?= htmlspecialchars($dn['sj_number']) ?></div>
        </div>

        <!-- Barcode CSS Industrial Standard -->
        <div class="text-end">
            <div class="d-inline-flex gap-0.5 bg-white p-1.5 border rounded">
                <span style="display:inline-block; width:2px; height:24px; background:#000;"></span>
                <span style="display:inline-block; width:1px; height:24px; background:#000; margin-left:1px;"></span>
                <span style="display:inline-block; width:3px; height:24px; background:#000; margin-left:2px;"></span>
                <span style="display:inline-block; width:1px; height:24px; background:#000; margin-left:1px;"></span>
                <span style="display:inline-block; width:2px; height:24px; background:#000; margin-left:2px;"></span>
                <span style="display:inline-block; width:4px; height:24px; background:#000; margin-left:1px;"></span>
                <span style="display:inline-block; width:1px; height:24px; background:#000; margin-left:3px;"></span>
                <span style="display:inline-block; width:2px; height:24px; background:#000; margin-left:1px;"></span>
                <span style="display:inline-block; width:3px; height:24px; background:#000; margin-left:2px;"></span>
                <span style="display:inline-block; width:1px; height:24px; background:#000; margin-left:1px;"></span>
                <span style="display:inline-block; width:2px; height:24px; background:#000; margin-left:2px;"></span>
            </div>
            <div class="font-mono text-muted text-center" style="font-size: 8px;"><?= htmlspecialchars($dn['sj_number']) ?></div>
        </div>
    </div>

    <!-- METADATA PENGIRIMAN & PENERIMA -->
    <div class="row g-3 mb-3">
        <!-- Kolom Kiri: Tujuan Pengiriman -->
        <div class="col-7">
            <div class="p-3 border rounded-2 h-100 bg-white">
                <span class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 10px;">KEPADA YTH. (PENERIMA):</span>
                <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($dn['recipient_name']) ?></div>
                <div class="text-secondary small mt-1 lh-sm">
                    <?= nl2br(htmlspecialchars($dn['recipient_address'])) ?>
                </div>
                <div class="mt-2 text-muted" style="font-size: 11px;">
                    Kategori: <strong class="text-dark"><?= htmlspecialchars($dn['recipient_type']) ?></strong>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Ekspedisi & Kendaraan -->
        <div class="col-5">
            <div class="p-3 border rounded-2 h-100 bg-white">
                <table class="table table-borderless table-sm mb-0 text-dark" style="font-size: 11px;">
                    <tr>
                        <td class="text-muted px-0 py-0.5">Tanggal Kirim</td>
                        <td class="fw-bold px-1 py-0.5">: <?= FormatHelper::dateIndo($dn['delivery_date']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted px-0 py-0.5">No. PO Pemesan</td>
                        <td class="font-mono fw-bold px-1 py-0.5">: <?= !empty($dn['customer_po_no']) ? htmlspecialchars($dn['customer_po_no']) : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted px-0 py-0.5">Plat No. Truk</td>
                        <td class="font-mono fw-bold px-1 py-0.5">: <?= htmlspecialchars($dn['vehicle_no']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted px-0 py-0.5">Pengemudi (Driver)</td>
                        <td class="fw-semibold px-1 py-0.5">: <?= htmlspecialchars($dn['driver_name']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted px-0 py-0.5">Status Pengiriman</td>
                        <td class="fw-bold px-1 py-0.5">: <?= htmlspecialchars($dn['status']) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- TABEL RINCIAN BARANG -->
    <div class="table-responsive mb-3">
        <table class="table table-bordered border-dark table-sm align-middle mb-0 text-dark" style="font-size: 11px;">
            <thead class="text-center fw-bold bg-light">
                <tr>
                    <th style="width: 35px;">NO</th>
                    <th style="width: 130px;">KODE PART / SKU</th>
                    <th>NAMA BARANG & SPESIFIKASI</th>
                    <th style="width: 80px;">JUMLAH</th>
                    <th style="width: 65px;">SATUAN</th>
                    <th style="width: 120px;">KEMASAN / BOX</th>
                    <th style="width: 140px;">KETERANGAN / LOT</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $grandTotalQty = 0;
                foreach ($items as $item): 
                    $grandTotalQty += (int)$item['qty_shipped'];
                ?>
                <tr>
                    <td class="text-center font-mono py-1.5"><?= $no++ ?></td>
                    <td class="font-mono fw-bold py-1.5"><?= htmlspecialchars($item['item_code']) ?></td>
                    <td class="fw-semibold py-1.5">
                        <?= htmlspecialchars($item['item_name']) ?>
                        <div class="text-muted" style="font-size: 9px;">Kategori: <?= htmlspecialchars($item['category']) ?> • Lokasi Rak: <?= htmlspecialchars($item['location_rack']) ?></div>
                    </td>
                    <td class="text-center font-mono fw-bold fs-6 py-1.5"><?= number_format((int)$item['qty_shipped'], 0, ',', '.') ?></td>
                    <td class="text-center py-1.5"><?= htmlspecialchars($item['unit']) ?></td>
                    <td class="py-1.5"><?= htmlspecialchars($item['packaging']) ?></td>
                    <td class="py-1.5 text-secondary"><?= !empty($item['remarks']) ? htmlspecialchars($item['remarks']) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="fw-bold bg-light">
                <tr>
                    <td colspan="3" class="text-end py-1.5">TOTAL BARANG DIKIRIM:</td>
                    <td class="text-center font-mono fs-6 py-1.5"><?= number_format($grandTotalQty, 0, ',', '.') ?></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- CATATAN & KLAUSUL RESMI PENGIRIMAN -->
    <div class="p-2 border rounded bg-white mb-4" style="font-size: 10px;">
        <div class="fw-bold text-dark mb-1">KETENTUAN & SYARAT PENGIRIMAN:</div>
        <ol class="mb-0 ps-3 text-secondary lh-sm">
            <li>Barang-barang di atas telah diperiksa dalam kondisi baik, jumlah sesuai, dan telah melewati uji mutu standar PT NKP.</li>
            <li>Surat Jalan ini merupakan bukti sah pengeluaran dan penyerahan barang dari pabrik PT Nandya Karya Perkasa.</li>
            <li>Surat Jalan Lembar Asli wajib ditandatangani, diberi nama jelas, dan distempel oleh pihak penerima resmi saat muatan tiba.</li>
            <li>Klaim selisih kuantitas atau cacat fisik wajib diajukan maksimal 2 x 24 jam setelah barang diterima.</li>
        </ol>
    </div>

    <!-- 4 KOLOM TANDA TANGAN RESMI STANDAR INDUSTRI OTOMOTIF -->
    <div class="row g-2 text-center text-dark" style="font-size: 10px;">
        <!-- Kolom 1: Logistik / Gudang -->
        <div class="col-3">
            <div class="border rounded p-2 h-100 d-flex flex-column justify-content-between">
                <div>
                    <span class="text-muted fw-bold d-block text-uppercase">Dibuat Oleh:</span>
                    <span class="text-muted" style="font-size: 9px;">Bagian Logistik & Gudang</span>
                </div>
                <div class="my-4">
                    <!-- Tanda Tangan Simbolis -->
                    <span class="text-muted fst-italic" style="font-size: 9px;">[ Paraf Petugas ]</span>
                </div>
                <div class="border-top pt-1">
                    <strong class="d-block text-dark"><?= htmlspecialchars($dn['creator_name']) ?></strong>
                    <span class="text-muted" style="font-size: 8px;">PT Nandya Karya Perkasa</span>
                </div>
            </div>
        </div>

        <!-- Kolom 2: Security / QC Gate -->
        <div class="col-3">
            <div class="border rounded p-2 h-100 d-flex flex-column justify-content-between">
                <div>
                    <span class="text-muted fw-bold d-block text-uppercase">Diperiksa Oleh:</span>
                    <span class="text-muted" style="font-size: 9px;">Security / Pos Keluar Gerbang</span>
                </div>
                <div class="my-4">
                    <span class="text-muted fst-italic" style="font-size: 9px;">[ Stempel Pos Keluar ]</span>
                </div>
                <div class="border-top pt-1">
                    <span class="text-muted d-block">( ........................................ )</span>
                    <span class="text-muted" style="font-size: 8px;">Petugas Keamanan Gate</span>
                </div>
            </div>
        </div>

        <!-- Kolom 3: Pengemudi / Ekspedisi -->
        <div class="col-3">
            <div class="border rounded p-2 h-100 d-flex flex-column justify-content-between">
                <div>
                    <span class="text-muted fw-bold d-block text-uppercase">Dibawa Oleh:</span>
                    <span class="text-muted" style="font-size: 9px;">Pengemudi / Supir Truk</span>
                </div>
                <div class="my-4">
                    <span class="text-muted fst-italic" style="font-size: 9px;">[ Tanda Tangan Supir ]</span>
                </div>
                <div class="border-top pt-1">
                    <strong class="d-block text-dark"><?= htmlspecialchars($dn['driver_name']) ?></strong>
                    <span class="text-muted font-mono" style="font-size: 8px;">No. Pol: <?= htmlspecialchars($dn['vehicle_no']) ?></span>
                </div>
            </div>
        </div>

        <!-- Kolom 4: Customer / Penerima -->
        <div class="col-3">
            <div class="border rounded p-2 h-100 d-flex flex-column justify-content-between bg-light">
                <div>
                    <span class="text-muted fw-bold d-block text-uppercase">Diterima Oleh:</span>
                    <span class="text-muted" style="font-size: 9px;">Customer / Pelanggan</span>
                </div>
                <div class="my-4">
                    <span class="text-muted fst-italic" style="font-size: 9px;">[ Cap / Stempel Basah ]</span>
                </div>
                <div class="border-top pt-1">
                    <span class="text-muted d-block">( ........................................ )</span>
                    <span class="text-muted" style="font-size: 8px;">Nama Jelas & Tanggal Terima</span>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER LEMBAR CETAK -->
    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top text-muted" style="font-size: 8px;">
        <span>Dicetak otomatis melalui <strong>SIP-NKP</strong> pada <?= date('d/m/Y H:i') ?> WIB</span>
        <span>Lembar 1: Asli (Customer) • Lembar 2: Arsip Finance • Lembar 3: Gudang & Logistik</span>
    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>

</body>
</html>
