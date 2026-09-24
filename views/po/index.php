<?php
$pageTitle = 'Purchase Order (PO)';
require __DIR__ . '/../layouts/header.php';
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;

$user = AuthHelper::user();
?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 small fw-bold">
                <span class="badge bg-primary p-1 rounded-circle me-1"> </span>TAHAP 3 DARI 4 • PENERBITAN PESANAN VENDOR (PO)
            </span>
        </div>
        <h1 class="h4 fw-bold text-dark mb-1">Dokumen Purchase Order (PO)</h1>
        <p class="text-secondary small mb-0">Langkah 3: Konversi PR yang disetujui menjadi pesanan pembelian resmi kepada vendor rekanan PT NKP.</p>
    </div>
</div>

<!-- SECTION 1: TABEL PR YANG SIAP DITERBITKAN PO -->
<?php if (!empty($approvedPRs)): ?>
<div class="card border-warning-subtle bg-warning-subtle shadow-sm mb-4">
    <div class="card-header bg-transparent border-warning-subtle py-2.5 px-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning text-dark p-1 rounded-circle"> </span>
            <h2 class="h6 fw-bold text-dark mb-0">
                <?= count($approvedPRs) ?> Pengajuan PR Telah Disetujui & Siap Diterbitkan PO
            </h2>
        </div>
        <span class="badge bg-warning text-dark px-2 py-1 small fw-bold">Divisi Purchasing</span>
    </div>

    <div class="card-body p-0 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>No PR</th>
                        <th>Pemohon</th>
                        <th>Disetujui Oleh</th>
                        <th>Target Kirim</th>
                        <th>Item Diminta</th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($approvedPRs as $apr): ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?= htmlspecialchars($apr['pr_number']) ?></td>
                        <td>
                            <div class="fw-semibold text-dark"><?= htmlspecialchars($apr['requester_name']) ?></div>
                            <div class="text-muted small" style="font-size: 0.72rem;"><?= htmlspecialchars($apr['requester_dept']) ?></div>
                        </td>
                        <td class="text-secondary small"><?= htmlspecialchars($apr['approver_name'] ?? 'Supervisor') ?></td>
                        <td>
                            <div class="fw-bold text-dark" style="max-width: 260px; line-height: 1.35;">
                                <?= htmlspecialchars($apr['item_summary'] ?? '-') ?>
                            </div>
                            <div class="text-muted small" style="font-size: 0.72rem;">
                                Total: <span class="badge bg-light text-secondary border"><?= $apr['total_items'] ?> Macam (<?= $apr['total_qty'] ?> unit)</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <a href="index.php?page=po-create&pr_id=<?= $apr['id'] ?>"
                               class="btn btn-warning btn-sm fw-bold px-3 py-1 shadow-sm d-inline-flex align-items-center gap-1 text-dark" style="background-color: var(--nkp-amber-500); border: none;">
                                <i data-lucide="file-plus" style="width: 14px; height: 14px;"></i>
                                <span>Terbitkan PO</span>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- SECTION 2: DAFTAR DOKUMEN PO YANG TELAH DITERBITKAN -->
<div class="card border shadow-sm mb-4">
    <div class="card-header py-2.5 px-3 bg-light border-bottom">
        <h2 class="h6 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="shopping-cart" class="text-primary" style="width: 18px; height: 18px;"></i>
            <span>Daftar Seluruh Purchase Order Resmi</span>
        </h2>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Nomor Dokumen PO</th>
                    <th>Tanggal Terbit</th>
                    <th>Vendor Rekanan</th>
                    <th>Ref Dokumen PR</th>
                    <th>Batas Pengiriman</th>
                    <th class="text-end">Grand Total (Inc. PPN)</th>
                    <th class="text-center">Status Pesanan</th>
                    <th class="text-end">Aksi Dokumen</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pos)): ?>
                <tr>
                    <td colspan="8" class="py-5 text-center text-muted small">Belum ada dokumen PO yang diterbitkan.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($pos as $po): ?>
                <tr>
                    <td>
                        <a href="index.php?page=po-print&id=<?= $po['id'] ?>" class="font-monospace fw-bold text-dark text-decoration-none d-inline-flex align-items-center gap-1.5">
                            <i data-lucide="file-check" class="text-secondary" style="width: 14px; height: 14px;"></i>
                            <span><?= htmlspecialchars($po['po_number']) ?></span>
                        </a>
                    </td>
                    <td class="text-secondary small text-nowrap">
                        <?= FormatHelper::dateIndo($po['po_date']) ?>
                    </td>
                    <td>
                        <div class="fw-bold text-dark"><?= htmlspecialchars($po['supplier_name']) ?></div>
                        <div class="text-muted small font-monospace" style="font-size: 0.72rem;"><?= htmlspecialchars($po['supplier_code']) ?> • <?= htmlspecialchars($po['contact_person']) ?></div>
                        <div class="text-secondary small mt-1" style="font-size: 0.72rem; max-width: 260px;">
                            <i data-lucide="package" style="width: 12px; height: 12px;" class="text-primary me-1"></i>
                            <?= htmlspecialchars($po['item_summary'] ?? '-') ?>
                        </div>
                    </td>
                    <td class="font-monospace text-secondary small">
                        <?= htmlspecialchars($po['pr_number'] ?? '-') ?>
                    </td>
                    <td class="text-secondary small text-nowrap">
                        <?= FormatHelper::dateIndo($po['delivery_deadline']) ?>
                    </td>
                    <td class="text-end font-monospace fw-bold text-dark">
                        <?= FormatHelper::rupiah($po['grand_total']) ?>
                    </td>
                    <td class="text-center">
                        <?= FormatHelper::statusBadge($po['status']) ?>
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="index.php?page=po-print&id=<?= $po['id'] ?>"
                           class="btn btn-sm btn-dark fw-bold px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-sm">
                            <i data-lucide="printer" class="text-warning" style="width: 14px; height: 14px;"></i>
                            <span class="text-warning">Cetak PDF</span>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
