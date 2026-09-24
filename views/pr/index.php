<?php
$statusFilter = $_GET['status'] ?? '';
$isApprovalPage = ($statusFilter === 'Pending');
$pageTitle = $isApprovalPage ? 'Persetujuan SPV (Approval Inbox)' : 'Permintaan Pembelian (PR)';
require __DIR__ . '/../layouts/header.php';
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;

$user = AuthHelper::user();
$canApprove = in_array($user['role'], ['supervisor', 'admin']);
?>

<!-- HEADER SECTION -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <?php if ($isApprovalPage): ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 small fw-bold">
                    <span class="badge bg-success p-1 rounded-circle me-1"> </span>TAHAP 2 DARI 4 • VERIFIKASI & PERSETUJUAN SPV
                </span>
            <?php else: ?>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 small fw-bold">
                    <span class="badge bg-warning p-1 rounded-circle me-1"> </span>TAHAP 1 DARI 4 • PENGAJUAN KEBUTUHAN
                </span>
            <?php endif; ?>
        </div>
        <h1 class="h4 fw-bold text-dark mb-1">
            <?= $isApprovalPage ? 'Meja Persetujuan SPV (Approval Inbox)' : 'Dokumen Purchase Requisition (PR)' ?>
        </h1>
        <p class="text-secondary small mb-0">
            <?php if ($isApprovalPage): ?>
                Meja kerja verifikasi pengajuan suku cadang dari teknisi. Periksa urgensi & spesifikasi, lalu berikan persetujuan agar dokumen diteruskan ke tim Purchasing.
            <?php else: ?>
                Langkah awal: Formulir pengajuan kebutuhan barang dan suku cadang sebelum diteruskan ke persetujuan Supervisor.
            <?php endif; ?>
        </p>
    </div>

    <!-- Tombol Aksi Kanan -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <?php if ($isApprovalPage): ?>
            <span class="badge bg-warning text-dark px-3 py-2 fw-bold border shadow-sm d-inline-flex align-items-center gap-1.5">
                <i data-lucide="inbox" style="width: 15px; height: 15px;"></i>
                <span><?= count($prs) ?> Antre Approval</span>
            </span>
        <?php endif; ?>
        <a href="index.php?page=pr-create" 
           class="btn btn-warning btn-sm fw-bold px-3 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm text-dark" style="background-color: var(--nkp-amber-500); border: none;">
            <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
            <span>+ Buat Pengajuan PR</span>
        </a>
    </div>
</div>

<?php if ($isApprovalPage && !empty($prs)): ?>
<!-- Alert Petunjuk Khusus Approval -->
<div class="alert alert-light border shadow-sm mb-4 d-flex align-items-center gap-2.5 p-3">
    <span class="badge bg-warning text-dark p-1.5 rounded-2 d-flex align-items-center justify-content-center flex-shrink-0">
        <i data-lucide="shield-check" style="width: 16px; height: 16px;"></i>
    </span>
    <div class="small">
        <strong class="text-dark">Petunjuk Supervisor / Admin:</strong>
        <span class="text-secondary">Klik <strong>Periksa</strong> untuk meninjau rincian spesifikasi & alasan pemakaian suku cadang, atau klik tombol hijau <strong>Setujui</strong> jika dokumen sudah sesuai SOP.</span>
    </div>
</div>
<?php endif; ?>

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

<!-- TABEL DAFTAR PR -->
<?php if ($isApprovalPage && empty($prs)): ?>
<!-- Empty State Khusus Approval Inbox -->
<div class="card border shadow-sm mb-4">
    <div class="card-body p-5 text-center">
        <div class="d-inline-flex p-3 bg-success-subtle text-success rounded-circle shadow-sm mb-3">
            <i data-lucide="check-circle-2" style="width: 36px; height: 36px;"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">Semua Pengajuan Telah Diverifikasi!</h5>
        <p class="text-secondary small mb-3" style="max-width: 480px; margin: 0 auto;">
            Tidak ada dokumen PR yang menunggu persetujuan Supervisor saat ini. Seluruh antrean pengadaan dalam kondisi bersih (*Clean Inbox*).
        </p>
        <a href="index.php?page=pr" class="btn btn-sm btn-outline-secondary fw-semibold">
            Lihat Riwayat Seluruh PR
        </a>
    </div>
</div>
<?php else: ?>
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
                    <?php if (!$isApprovalPage): ?>
                    <th>Disetujui Oleh</th>
                    <?php endif; ?>
                    <th class="text-end"><?= $isApprovalPage ? 'Tindakan Verifikasi' : 'Aksi' ?></th>
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
                    <?php if (!$isApprovalPage): ?>
                    <td class="text-secondary small">
                        <?= htmlspecialchars($pr['approver_name'] ?? '-') ?>
                    </td>
                    <?php endif; ?>
                    <td class="text-end text-nowrap">
                        <?php if ($isApprovalPage): ?>
                            <div class="d-inline-flex align-items-center gap-1.5">
                                <a href="index.php?page=pr-detail&id=<?= $pr['id'] ?>"
                                   class="btn btn-sm btn-light border py-1 px-2.5 fw-semibold d-inline-flex align-items-center gap-1" title="Periksa rincian lengkap">
                                    <i data-lucide="file-search" style="width: 14px; height: 14px;"></i>
                                    <span>Periksa</span>
                                </a>
                                <?php if ($canApprove): ?>
                                <form action="index.php?page=pr-approve" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin menyetujui pengajuan PR <?= htmlspecialchars($pr['pr_number']) ?> sekarang?');">
                                    <input type="hidden" name="id" value="<?= $pr['id'] ?>">
                                    <input type="hidden" name="redirect" value="pending">
                                    <button type="submit" class="btn btn-sm btn-success py-1 px-2.5 fw-bold d-inline-flex align-items-center gap-1 text-white shadow-sm" title="Setujui PR ini langsung">
                                        <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                                        <span>Setujui</span>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <a href="index.php?page=pr-detail&id=<?= $pr['id'] ?>"
                               class="btn btn-sm btn-light border py-1 px-2.5 fw-semibold d-inline-flex align-items-center gap-1">
                                <span>Periksa</span>
                                <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
