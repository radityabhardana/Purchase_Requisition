<?php
$pageTitle = 'Permintaan Pembelian (PR)';
require __DIR__ . '/../layouts/header.php';
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;

$user = AuthHelper::user();
$statusFilter = $_GET['status'] ?? '';
?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 small fw-bold">
                <span class="badge bg-warning p-1 rounded-circle me-1"> </span>TAHAP 1 DARI 4 • PENGAJUAN KEBUTUHAN
            </span>
        </div>
        <h1 class="h4 fw-bold text-dark mb-1">Dokumen Purchase Requisition (PR)</h1>
        <p class="text-secondary small mb-0">Langkah awal: Formulir pengajuan kebutuhan barang sebelum diteruskan ke persetujuan Supervisor.</p>
    </div>

    <a href="index.php?page=pr-create" 
       class="btn btn-action-primary btn-sm px-3 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm">
        <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
        <span>+ Buat Pengajuan PR Baru</span>
    </a>
</div>

<!-- STATUS FILTER TABS -->
<div class="card border shadow-sm mb-4">
    <div class="card-body p-2 p-sm-3">
        <div class="d-flex flex-wrap gap-1">
            <a href="index.php?page=pr" 
               class="btn btn-sm <?= empty($statusFilter) ? 'btn-dark' : 'btn-light border text-secondary' ?> py-1 px-2.5 small">
                Semua Pengajuan
            </a>
            <a href="index.php?page=pr&status=Pending" 
               class="btn btn-sm <?= $statusFilter === 'Pending' ? 'btn-warning fw-bold text-dark' : 'btn-light border text-secondary' ?> py-1 px-2.5 small">
                Menunggu Approval (Pending)
            </a>
            <a href="index.php?page=pr&status=Approved" 
               class="btn btn-sm <?= $statusFilter === 'Approved' ? 'btn-success text-white' : 'btn-light border text-secondary' ?> py-1 px-2.5 small">
                Disetujui (Approved)
            </a>
            <a href="index.php?page=pr&status=PO Issued" 
               class="btn btn-sm <?= $statusFilter === 'PO Issued' ? 'btn-primary text-white' : 'btn-light border text-secondary' ?> py-1 px-2.5 small">
                PO Telah Diterbitkan
            </a>
            <a href="index.php?page=pr&status=Rejected" 
               class="btn btn-sm <?= $statusFilter === 'Rejected' ? 'btn-danger text-white' : 'btn-light border text-secondary' ?> py-1 px-2.5 small">
                Ditolak (Rejected)
            </a>
        </div>
    </div>
</div>

<!-- TABEL PR -->
<div class="card border shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Nomor Dokumen PR</th>
                    <th>Tanggal Diajukan</th>
                    <th>Pemohon (Departemen)</th>
                    <th class="text-center">Urgensi</th>
                    <th>Target Dibutuhkan</th>
                    <th>Rincian Alat & Suku Cadang</th>
                    <th class="text-center">Status Dokumen</th>
                    <th>Disetujui Oleh</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($prs)): ?>
                <tr>
                    <td colspan="9" class="py-5 text-center text-muted small">Belum ada dokumen pengajuan PR yang sesuai filter.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($prs as $pr): ?>
                <tr>
                    <td>
                        <a href="index.php?page=pr-detail&id=<?= $pr['id'] ?>" class="font-monospace fw-bold text-dark text-decoration-none d-inline-flex align-items-center gap-1.5">
                            <i data-lucide="file-text" class="text-secondary" style="width: 14px; height: 14px;"></i>
                            <span><?= htmlspecialchars($pr['pr_number']) ?></span>
                        </a>
                    </td>
                    <td class="text-secondary small text-nowrap">
                        <?= FormatHelper::dateIndo($pr['pr_date']) ?>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($pr['requester_name']) ?></div>
                        <div class="text-muted small" style="font-size: 0.72rem;"><?= htmlspecialchars($pr['requester_dept']) ?></div>
                    </td>
                    <td class="text-center">
                        <?= FormatHelper::priorityBadge($pr['priority']) ?>
                    </td>
                    <td class="text-secondary small text-nowrap">
                        <?= FormatHelper::dateIndo($pr['target_date']) ?>
                    </td>
                    <td>
                        <div class="fw-bold text-dark" style="max-width: 280px; line-height: 1.35;">
                            <?= htmlspecialchars($pr['item_summary'] ?? '-') ?>
                        </div>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                            Total: <span class="badge bg-light text-secondary border"><?= $pr['total_items'] ?> macam (<?= $pr['total_qty'] ?> unit)</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <?= FormatHelper::statusBadge($pr['status']) ?>
                    </td>
                    <td class="text-secondary small">
                        <?= htmlspecialchars($pr['approver_name'] ?? '-') ?>
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="index.php?page=pr-detail&id=<?= $pr['id'] ?>"
                           class="btn btn-sm btn-light border py-1 px-2.5 fw-semibold d-inline-flex align-items-center gap-1">
                            <span>Periksa</span>
                            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
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
