<?php
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Dokumen PO - <?= htmlspecialchars($po['po_number']) ?></title>
    
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

<!-- TOOLBAR AKSI (HANYA MUNCUL DI LAYAR) -->
<div class="page-container mb-3 d-flex align-items-center justify-content-between d-print-none">
    <a href="index.php?page=po" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
        <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
        <span>Kembali ke Daftar PO</span>
    </a>

    <div class="d-flex align-items-center gap-2">
        <button onclick="window.print()" class="btn btn-dark btn-sm fw-bold d-inline-flex align-items-center gap-2 shadow-sm text-warning">
            <i data-lucide="printer" style="width: 16px; height: 16px;"></i>
            <span>Cetak PO Sekarang (Print / PDF)</span>
        </button>
    </div>
</div>

<!-- CONTAINER LEMBAR A4 RESMI -->
<div class="page-container border border-secondary-subtle shadow-sm rounded-3 p-4 p-sm-5 text-dark small leading-relaxed">
    
    <!-- KOP PERUSAHAAN PT NANDYA KARYA PERKASA -->
    <div class="d-flex align-items-start justify-content-between pb-3 border-bottom border-2 border-dark">
        <div class="d-flex align-items-center gap-3">
            <img src="images/logo.jpg" 
                 onerror="this.onerror=null; this.src='logo-nkp-kecil-2.png';" 
                 alt="PT NANDYA KARYA PERKASA" 
                 style="height: 52px; width: auto; object-fit: contain;">
            <div class="border-start border-2 border-secondary-subtle ps-3">
                <p class="mb-0 fw-semibold text-dark" style="font-size: 11px;">Automotive Component Manufacturer • Metal Stamping, Dies, Welding & Injection</p>
                <p class="mb-0 text-secondary" style="font-size: 10px;">Plant: Jl. Raya Narogong Km. 16, Cileungsi, Bogor 16820 - Jawa Barat | Telp: (021) 823-4567</p>
            </div>
        </div>

        <div class="text-end">
            <div class="font-mono text-muted text-uppercase fw-bold" style="font-size: 9px;">ISO 9001 / IATF 16949 Certified</div>
            <div class="font-mono text-muted" style="font-size: 9px;">Doc No: NKP-PUR-F-004 Rev.02</div>
            <div class="mt-1 d-inline-block px-2 py-0.5 bg-light rounded text-dark border border-secondary-subtle font-mono fw-bold" style="font-size: 9px;">
                ORIGINAL COPY
            </div>
        </div>
    </div>

    <!-- JUDUL DOKUMEN -->
    <div class="text-center my-4">
        <h2 class="h5 fw-black text-uppercase tracking-wider text-dark mb-1">PURCHASE ORDER (SURAT PESANAN)</h2>
        <div class="font-mono fw-bold text-primary small">NOMOR: <?= htmlspecialchars($po['po_number']) ?></div>
    </div>

    <!-- DUA KOLOM INFORMASI: VENDOR vs METADATA PO -->
    <div class="row g-3 mb-4">
        <!-- Kolom Kiri: Kepada Rekanan Vendor -->
        <div class="col-md-6">
            <div class="border border-secondary-subtle rounded-3 p-3 bg-light bg-opacity-50 h-100">
                <div class="text-uppercase fw-bold text-muted mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Ditujukan Kepada (Vendor):</div>
                <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($po['supplier_name']) ?></div>
                <div class="text-secondary mt-1">
                    Attn: <span class="fw-semibold text-dark"><?= htmlspecialchars($po['contact_person']) ?></span>
                </div>
                <div class="text-muted small mt-0.5"><?= htmlspecialchars($po['supplier_address']) ?></div>
                <div class="text-muted small mt-0.5">Telp: <?= htmlspecialchars($po['supplier_phone']) ?> | Email: <?= htmlspecialchars($po['supplier_email']) ?></div>
            </div>
        </div>

        <!-- Kolom Kanan: Rincian Dokumen PO -->
        <div class="col-md-6">
            <div class="border border-secondary-subtle rounded-3 p-3 bg-light bg-opacity-50 h-100">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-secondary">Tanggal Terbit PO:</span>
                    <span class="fw-bold text-dark font-mono"><?= FormatHelper::dateIndo($po['po_date']) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-secondary">Batas Pengiriman (Deadline):</span>
                    <span class="fw-bold text-danger font-mono"><?= FormatHelper::dateIndo($po['delivery_deadline']) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-secondary">Termin Pembayaran (TOP):</span>
                    <span class="fw-bold text-dark font-mono"><?= htmlspecialchars($po['supplier_payment_term'] ?? 'Net 30') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-secondary">Referensi Dokumen PR:</span>
                    <span class="fw-bold text-dark font-mono"><?= htmlspecialchars($po['pr_number'] ?? '-') ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-secondary">Lokasi Penyerahan:</span>
                    <span class="fw-semibold text-dark">Gudang Material Plant Cileungsi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL RINCIAN BARANG -->
    <div class="table-responsive border border-secondary-subtle rounded-3 overflow-hidden mb-4">
        <table class="table table-bordered align-middle mb-0" style="font-size: 11px;">
            <thead class="table-light text-dark fw-bold text-uppercase" style="font-size: 10px;">
                <tr>
                    <th class="py-2 px-2 text-center" style="width: 35px;">No</th>
                    <th class="py-2 px-2" style="width: 120px;">Kode Part</th>
                    <th class="py-2 px-2">Deskripsi Suku Cadang / Material</th>
                    <th class="py-2 px-2 text-center" style="width: 90px;">Kuantitas</th>
                    <th class="py-2 px-2 text-end" style="width: 140px;">Harga Satuan (IDR)</th>
                    <th class="py-2 px-2 text-end" style="width: 150px;">Subtotal (IDR)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach ($items as $it): 
                ?>
                <tr>
                    <td class="py-2 px-2 text-center text-muted"><?= $no++ ?></td>
                    <td class="py-2 px-2 font-mono fw-bold text-dark"><?= htmlspecialchars($it['item_code']) ?></td>
                    <td class="py-2 px-2">
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($it['item_name']) ?></div>
                    </td>
                    <td class="py-2 px-2 text-center fw-bold text-dark">
                        <?= $it['qty_ordered'] ?> <?= htmlspecialchars($it['unit']) ?>
                    </td>
                    <td class="py-2 px-2 text-end font-mono text-secondary"><?= FormatHelper::rupiah($it['unit_price']) ?></td>
                    <td class="py-2 px-2 text-end font-mono fw-bold text-dark"><?= FormatHelper::rupiah($it['subtotal']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="fw-semibold bg-light">
                <tr>
                    <td colspan="4" rowspan="3" class="p-3 align-top text-secondary" style="font-size: 10px;">
                        <b>Catatan Khusus Pengiriman:</b><br>
                        <?= htmlspecialchars($po['notes'] ?? 'Harap melampirkan Surat Jalan resmi dan salinan PO ini saat pengiriman barang ke pabrik.') ?>
                    </td>
                    <td class="py-2 px-2 text-end text-secondary">Subtotal Barang:</td>
                    <td class="py-2 px-2 text-end font-mono fw-bold text-dark"><?= FormatHelper::rupiah($po['subtotal']) ?></td>
                </tr>
                <tr>
                    <td class="py-2 px-2 text-end text-secondary">PPN (<?= (float)$po['tax_percent'] ?>%):</td>
                    <td class="py-2 px-2 text-end font-mono fw-bold text-dark"><?= FormatHelper::rupiah($po['tax_amount']) ?></td>
                </tr>
                <tr class="table-light fs-6 fw-bold">
                    <td class="py-2 px-2 text-end text-dark">GRAND TOTAL:</td>
                    <td class="py-2 px-2 text-end font-mono text-primary"><?= FormatHelper::rupiah($po['grand_total']) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- SYARAT & KETENTUAN PENGADAAN PT NKP -->
    <div class="mb-4 p-3 bg-light rounded-3 border border-secondary-subtle" style="font-size: 10px;">
        <div class="fw-bold text-dark text-uppercase mb-1">Syarat & Ketentuan Pembelian (Purchasing Terms):</div>
        <div class="text-secondary">1. Barang yang dikirim wajib baru, memenuhi toleransi dimensi teknis otomotif, dan bebas cacat fisik.</div>
        <div class="text-secondary">2. Petugas Gudang PT NKP berhak menolak kedatangan barang apabila tidak disertai dokumen Surat Jalan sah.</div>
        <div class="text-secondary">3. Faktur tagihan (*Invoice*) wajib dilampiri salinan PO resmi dan Berita Acara Penerimaan Barang (GR).</div>
    </div>

    <!-- 3 KOLOM TANDA TANGAN RESMI -->
    <div class="row text-center mt-4">
        <div class="col-4">
            <div class="text-secondary mb-1" style="font-size: 11px;">Diterbitkan Oleh,</div>
            <div class="fw-bold text-dark">Purchasing Department</div>
            <div class="py-3 d-flex align-items-center justify-content-center" style="height: 70px;">
                <span class="badge bg-success-subtle text-success border border-success-subtle font-mono text-uppercase px-2 py-1" style="font-size: 9px;">DIGITALLY VERIFIED</span>
            </div>
            <div class="fw-bold text-decoration-underline text-dark"><?= htmlspecialchars($po['creator_name']) ?></div>
            <div class="text-muted" style="font-size: 10px;">Purchasing Staff</div>
        </div>

        <div class="col-4">
            <div class="text-secondary mb-1" style="font-size: 11px;">Disetujui Oleh,</div>
            <div class="fw-bold text-dark">PT Nandya Karya Perkasa</div>
            <div class="py-3 d-flex align-items-center justify-content-center" style="height: 70px;">
                <span class="badge bg-success-subtle text-success border border-success-subtle font-mono text-uppercase px-2 py-1" style="font-size: 9px;">MANAGEMENT APPROVED</span>
            </div>
            <div class="fw-bold text-decoration-underline text-dark">Ir. Bambang Trihatmojo</div>
            <div class="text-muted" style="font-size: 10px;">Plant General Manager</div>
        </div>

        <div class="col-4">
            <div class="text-secondary mb-1" style="font-size: 11px;">Konfirmasi Penerimaan PO,</div>
            <div class="fw-bold text-dark"><?= htmlspecialchars($po['supplier_name']) ?></div>
            <div class="py-3 d-flex align-items-center justify-content-center text-muted fst-italic" style="height: 70px; font-size: 11px;">
                (Tanda Tangan & Cap Vendor)
            </div>
            <div class="fw-bold text-dark">( .................................................. )</div>
            <div class="text-muted" style="font-size: 10px;">Nama Terang & Jabatan</div>
        </div>
    </div>

</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>
