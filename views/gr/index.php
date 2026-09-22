<?php
$pageTitle = 'Penerimaan Barang (GR)';
require __DIR__ . '/../layouts/header.php';
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;

$user = AuthHelper::user();
$canReceive = in_array($user['role'], ['warehouse', 'admin']);
?>

<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <div class="mb-2">
            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-1 rounded-pill small">
                <span class="d-inline-block rounded-circle bg-success me-1" style="width: 7px; height: 7px;"></span>
                TAHAP 4 DARI 4 • PENERIMAAN BARANG & UPDATE STOK (GR)
            </span>
        </div>
        <h1 class="h3 fw-bold text-dark mb-1">Penerimaan Barang (Goods Receipt)</h1>
        <p class="text-muted small mb-0">Langkah 4: Verifikasi kedatangan fisik barang dari vendor dan sinkronisasi otomatis ke stok gudang.</p>
    </div>
</div>

<!-- SECTION 1: PO YANG MENUNGGU PENERIMAAN DI GUDANG -->
<?php if (!empty($openPOs)): ?>
<div class="card border border-info-subtle bg-info-subtle bg-opacity-25 shadow-sm rounded-3 p-3 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3 px-1">
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-block rounded-circle bg-info" style="width: 10px; height: 10px;"></span>
            <h2 class="h6 fw-bold text-dark mb-0">
                <?= count($openPOs) ?> Dokumen PO Siap Diterima di Pos Kedatangan Gudang
            </h2>
        </div>
        <span class="badge bg-info text-white small">Logistics & Receiving Dock</span>
    </div>

    <div class="table-responsive bg-white rounded-3 border border-info-subtle shadow-sm">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-secondary small text-uppercase">
                <tr>
                    <th class="py-2.5 px-3">Nomor Dokumen PO</th>
                    <th class="py-2.5 px-3">Vendor Pemasok</th>
                    <th class="py-2.5 px-3">Batas Pengiriman</th>
                    <th class="py-2.5 px-3 text-center">Status</th>
                    <th class="py-2.5 px-3 text-end">Tindakan Gudang</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($openPOs as $opo): ?>
                <tr>
                    <td class="py-2.5 px-3 font-monospace fw-bold text-dark"><?= htmlspecialchars($opo['po_number']) ?></td>
                    <td class="py-2.5 px-3 fw-semibold text-secondary"><?= htmlspecialchars($opo['supplier_name']) ?></td>
                    <td class="py-2.5 px-3 text-muted"><?= FormatHelper::dateIndo($opo['delivery_deadline']) ?></td>
                    <td class="py-2.5 px-3 text-center"><?= FormatHelper::statusBadge($opo['status']) ?></td>
                    <td class="py-2.5 px-3 text-end">
                        <?php if ($canReceive): ?>
                        <a href="index.php?page=gr-create&po_id=<?= $opo['id'] ?>"
                           class="btn btn-dark btn-sm text-warning fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                            <i data-lucide="package-check" style="width: 15px; height: 15px;"></i>
                            <span>Proses Penerimaan</span>
                        </a>
                        <?php else: ?>
                        <span class="text-muted small fst-italic">Khusus Role Warehouse</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- SECTION 2: RIWAYAT PENERIMAAN BARANG (GOODS RECEIPTS) -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h2 class="h6 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="archive" class="text-success" style="width: 18px; height: 18px;"></i>
            <span>Riwayat Dokumen Bukti Penerimaan Barang (GR)</span>
        </h2>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-secondary text-uppercase small">
                <tr>
                    <th class="py-3 px-3">Nomor GR</th>
                    <th class="py-3 px-3">Tanggal Terima</th>
                    <th class="py-3 px-3">Nomor PO Terkait</th>
                    <th class="py-3 px-3">Nomor Surat Jalan</th>
                    <th class="py-3 px-3">Supplier Pemasok</th>
                    <th class="py-3 px-3 text-center">Total Kuantitas</th>
                    <th class="py-3 px-3">Diterima Oleh</th>
                    <th class="py-3 px-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($receipts)): ?>
                <tr>
                    <td colspan="8" class="py-5 text-center text-muted">Belum ada riwayat penerimaan barang.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($receipts as $gr): ?>
                <tr>
                    <td class="py-3 px-3 font-monospace fw-bold text-dark">
                        <span class="d-inline-flex align-items-center gap-1.5 text-success">
                            <i data-lucide="check-circle-2" style="width: 16px; height: 16px;"></i>
                            <span class="text-dark"><?= htmlspecialchars($gr['gr_number']) ?></span>
                        </span>
                    </td>
                    <td class="py-3 px-3 text-muted text-nowrap">
                        <?= FormatHelper::dateIndo($gr['received_date']) ?>
                    </td>
                    <td class="py-3 px-3 font-monospace fw-bold text-dark">
                        <?= htmlspecialchars($gr['po_number']) ?>
                    </td>
                    <td class="py-3 px-3 font-monospace fw-semibold text-secondary bg-light bg-opacity-50">
                        <?= htmlspecialchars($gr['delivery_note_no']) ?>
                    </td>
                    <td class="py-3 px-3 fw-medium text-dark"><?= htmlspecialchars($gr['supplier_name']) ?></td>
                    <td class="py-3 px-3 text-center fw-bold text-success">
                        <?= $gr['total_items'] ?> Macam (<?= $gr['total_qty'] ?> Pcs)
                    </td>
                    <td class="py-3 px-3 text-muted">
                        <?= htmlspecialchars($gr['receiver_name']) ?>
                    </td>
                    <td class="py-3 px-3 text-center">
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                            Stok Terupdate
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
