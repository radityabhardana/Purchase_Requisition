<?php
$pageTitle = 'Proses Penerimaan Barang: ' . $po['po_number'];
require __DIR__ . '/../layouts/header.php';
use App\Helpers\FormatHelper;
?>

<div class="row justify-content-center mb-5">
    <div class="col-xl-10 col-lg-11">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="index.php?page=gr" class="text-decoration-none text-muted">Penerimaan Barang (GR)</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Verifikasi Kedatangan Fisik</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <div class="mb-2">
                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-1 rounded-pill small">
                        <span class="d-inline-block rounded-circle bg-success me-1" style="width: 7px; height: 7px;"></span>
                        TAHAP 4 DARI 4 • VERIFIKASI FISIK & MASUK STOK GUDANG
                    </span>
                </div>
                <h1 class="h3 fw-bold text-dark mb-1">Verifikasi & Penerimaan Barang (GR)</h1>
                <p class="text-muted small mb-0">
                    Langkah 4: Pencocokan fisik barang dengan dokumen <span class="font-monospace fw-bold text-dark"><?= htmlspecialchars($po['po_number']) ?></span> dari vendor <span class="fw-bold text-dark"><?= htmlspecialchars($po['supplier_name']) ?></span>.
                </p>
            </div>
        </div>

        <form action="index.php?page=gr-store" method="POST" onsubmit="return confirm('Apakah fisik barang telah diperiksa dan Anda yakin ingin menambah stok gudang secara otomatis?');">
            <input type="hidden" name="po_id" value="<?= $po['id'] ?>">

            <!-- CARD 1: INFORMASI SURAT JALAN SUPPLIER -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold text-dark pb-3 mb-3 border-bottom d-flex align-items-center gap-2">
                        <i data-lucide="file-check-2" class="text-success" style="width: 18px; height: 18px;"></i>
                        <span>Data Pengantar dari Vendor (Surat Jalan)</span>
                    </h2>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Nomor Surat Jalan Supplier <span class="text-danger">*</span></label>
                            <input type="text" name="delivery_note_no" required placeholder="Contoh: SJ-KL-2026-0881" class="form-control font-monospace fw-semibold">
                            <div class="form-text small text-muted">Sesuai dengan nomor yang tertera pada lembar fisik pengantar sopir vendor.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Tanggal Kedatangan Fisik <span class="text-danger">*</span></label>
                            <input type="date" name="received_date" required value="<?= date('Y-m-d') ?>" class="form-control">
                        </div>
                    </div>

                    <div>
                        <label class="form-label small fw-semibold text-secondary">Catatan Kondisi Paket / Ekspedisi</label>
                        <input type="text" name="notes" placeholder="Contoh: Dus dalam kondisi segel rapat, tidak ada basah atau penyok." class="form-control">
                    </div>
                </div>
            </div>

            <!-- CARD 2: VERIFIKASI KUANTITAS FISIK & UJI QC -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold text-dark pb-3 mb-3 border-bottom d-flex align-items-center gap-2">
                        <i data-lucide="check-square" class="text-success" style="width: 18px; height: 18px;"></i>
                        <span>Verifikasi Jumlah Fisik & Status Uji Mutu (QC)</span>
                    </h2>

                    <div class="table-responsive mb-4">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary text-uppercase small">
                                <tr>
                                    <th class="py-2.5 px-3">Kode & Nama Part</th>
                                    <th class="py-2.5 px-3 text-center" style="width: 120px;">Qty Dipesan</th>
                                    <th class="py-2.5 px-3 text-center" style="width: 140px;">Qty Tiba Fisik *</th>
                                    <th class="py-2.5 px-3 text-center" style="width: 160px;">Uji Mutu (QC) *</th>
                                    <th class="py-2.5 px-3">Catatan Inspeksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $it): ?>
                                <tr>
                                    <td class="py-3 px-3">
                                        <input type="hidden" name="item_id[]" value="<?= $it['item_id'] ?>">
                                        <div class="font-monospace fw-bold text-dark"><?= htmlspecialchars($it['item_code']) ?></div>
                                        <div class="small text-secondary"><?= htmlspecialchars($it['item_name']) ?></div>
                                        <div class="small text-muted" style="font-size: 11px;">Lokasi Rak: <?= htmlspecialchars($it['location_rack']) ?></div>
                                    </td>
                                    <td class="py-3 px-3 text-center fw-bold text-dark">
                                        <?= $it['qty_ordered'] ?> <?= htmlspecialchars($it['unit']) ?>
                                    </td>
                                    <td class="py-3 px-3">
                                        <input type="number" name="qty_received[]" required min="1" max="<?= $it['qty_ordered'] ?>" value="<?= $it['qty_ordered'] ?>"
                                               class="form-control form-control-sm text-center fw-bold text-success">
                                    </td>
                                    <td class="py-3 px-3">
                                        <select name="qc_status[]" required class="form-select form-select-sm fw-bold text-success">
                                            <option value="Passed">Passed (Lolos)</option>
                                            <option value="Rejected">Rejected (Cacat)</option>
                                            <option value="Rework">Rework (Perbaikan)</option>
                                        </select>
                                    </td>
                                    <td class="py-3 px-3">
                                        <input type="text" name="item_notes[]" placeholder="Kondisi visual part..." class="form-control form-control-sm">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- PERINGATAN AUTO-SYNC STOK -->
                    <div class="alert alert-success d-flex align-items-start gap-3 border-0 shadow-sm mb-0">
                        <i data-lucide="zap" class="text-success flex-shrink-0 mt-1" style="width: 20px; height: 20px;"></i>
                        <div class="small">
                            <div class="fw-bold">Otomasi Database Terintegrasi (Database Transaction):</div>
                            <p class="mb-0 text-success-emphasis">
                                Menekan tombol konfirmasi akan secara atomik menambah stok fisik di tabel <b>items</b>, mencatat riwayat ke <b>kartu stok mutasi (IN)</b>, dan mengubah status PO menjadi <b>Completed</b>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit & Batal -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-2">
                <a href="index.php?page=gr" class="btn btn-outline-secondary px-4">
                    Batal
                </a>
                <button type="submit" class="btn btn-success px-4 fw-bold d-inline-flex align-items-center gap-2 shadow-sm">
                    <i data-lucide="package-plus" style="width: 18px; height: 18px;"></i>
                    <span>Konfirmasi Penerimaan (Update Stok)</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
