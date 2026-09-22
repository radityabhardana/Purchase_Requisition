<?php
$pageTitle = 'Detail PR: ' . $pr['pr_number'];
require __DIR__ . '/../layouts/header.php';
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;

$user = AuthHelper::user();
$isApprover = in_array($user['role'], ['supervisor', 'admin']) && $pr['status'] === 'Pending';
$canConvertPO = in_array($user['role'], ['purchasing', 'admin']) && $pr['status'] === 'Approved';
?>

<div class="container-fluid" style="max-width: 1040px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="index.php?page=pr" class="text-decoration-none text-secondary">Permintaan Pembelian (PR)</a></li>
            <li class="breadcrumb-item active font-monospace fw-semibold text-dark" aria-current="page"><?= htmlspecialchars($pr['pr_number']) ?></li>
        </ol>
    </nav>

    <!-- HEADER TITLE & BADGES -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 small fw-bold">
                    <span class="badge bg-success p-1 rounded-circle me-1"> </span>TAHAP 2 DARI 4 • VERIFIKASI & PERSETUJUAN SPV
                </span>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2 my-1">
                <h1 class="h3 fw-black text-dark font-monospace mb-0"><?= htmlspecialchars($pr['pr_number']) ?></h1>
                <?= FormatHelper::statusBadge($pr['status']) ?>
                <?= FormatHelper::priorityBadge($pr['priority']) ?>
            </div>
            <p class="text-secondary small mb-0">
                Diajukan oleh <strong class="text-dark"><?= htmlspecialchars($pr['requester_name']) ?></strong> (<?= htmlspecialchars($pr['requester_dept']) ?>) pada <?= FormatHelper::dateIndo($pr['pr_date']) ?>
            </p>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="d-flex align-items-center gap-2">
            <?php if ($canConvertPO): ?>
                <a href="index.php?page=po-create&pr_id=<?= $pr['id'] ?>"
                   class="btn btn-primary fw-bold px-3 py-2 d-inline-flex align-items-center gap-2 shadow-sm">
                    <i data-lucide="shopping-cart" style="width: 16px; height: 16px;"></i>
                    <span>Terbitkan PO</span>
                </a>
            <?php endif; ?>

            <?php if ($isApprover): ?>
                <form action="index.php?page=pr-approve" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin menyetujui pengajuan PR ini?');">
                    <input type="hidden" name="id" value="<?= $pr['id'] ?>">
                    <button type="submit" class="btn btn-success fw-bold px-3 py-2 d-inline-flex align-items-center gap-2 shadow-sm">
                        <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                        <span>Setujui (Approve)</span>
                    </button>
                </form>

                <button type="button" class="btn btn-danger fw-bold px-3 py-2 d-inline-flex align-items-center gap-2 shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#modalReject">
                    <i data-lucide="x-circle" style="width: 16px; height: 16px;"></i>
                    <span>Tolak (Reject)</span>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- ORDER LIFECYCLE STEPPER -->
    <div class="card border shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="text-secondary small fw-bold text-uppercase mb-3" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                Tahapan Siklus Pengadaan Dokumen (Procurement Lifecycle)
            </div>
            
            <?php
            $step1 = true; // PR Diajukan
            $step2 = in_array($pr['status'], ['Approved', 'PO Issued']);
            $step3 = $pr['status'] === 'PO Issued';
            $step4 = false; // Barangnya belum tentu tiba
            ?>
            <div class="row text-center position-relative g-2">
                <!-- Step 1 -->
                <div class="col-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold shadow-sm mb-2" style="width: 36px; height: 36px;">
                            <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                        </div>
                        <span class="small fw-bold text-dark">PR Diajukan</span>
                        <span class="text-muted small" style="font-size: 0.7rem;"><?= FormatHelper::dateIndo($pr['pr_date']) ?></span>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="rounded-circle <?= $step2 ? 'bg-success text-white' : ($pr['status'] === 'Rejected' ? 'bg-danger text-white' : 'bg-warning text-dark') ?> d-flex align-items-center justify-content-center fw-bold shadow-sm mb-2" style="width: 36px; height: 36px;">
                            <?php if ($step2): ?>
                                <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                            <?php elseif ($pr['status'] === 'Rejected'): ?>
                                <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                            <?php else: ?>
                                <i data-lucide="clock" style="width: 18px; height: 18px;"></i>
                            <?php endif; ?>
                        </div>
                        <span class="small fw-bold <?= $step2 ? 'text-dark' : 'text-secondary' ?>">
                            <?= $pr['status'] === 'Rejected' ? 'Ditolak SPV' : 'Persetujuan SPV' ?>
                        </span>
                        <span class="text-muted small" style="font-size: 0.7rem;"><?= $pr['approver_name'] ?? 'Menunggu...' ?></span>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="rounded-circle <?= $step3 ? 'bg-success text-white' : 'bg-light text-secondary border' ?> d-flex align-items-center justify-content-center fw-bold shadow-sm mb-2" style="width: 36px; height: 36px;">
                            <?php if ($step3): ?>
                                <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                            <?php else: ?>
                                <span>3</span>
                            <?php endif; ?>
                        </div>
                        <span class="small fw-bold <?= $step3 ? 'text-dark' : 'text-muted' ?>">Penerbitan PO</span>
                        <span class="text-muted small" style="font-size: 0.7rem;">Purchasing PT NKP</span>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="col-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="rounded-circle bg-light text-secondary border d-flex align-items-center justify-content-center fw-bold shadow-sm mb-2" style="width: 36px; height: 36px;">
                            <span>4</span>
                        </div>
                        <span class="small fw-bold text-muted">Barang Datang (GR)</span>
                        <span class="text-muted small" style="font-size: 0.7rem;">Gudang & QC</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NOTIFIKASI ALASAN JIKA DITOLAK -->
    <?php if ($pr['status'] === 'Rejected'): ?>
    <div class="alert alert-danger shadow-sm d-flex align-items-start gap-2 mb-4">
        <i data-lucide="alert-circle" class="flex-shrink-0 mt-0.5" style="width: 18px; height: 18px;"></i>
        <div>
            <strong class="d-block small">Pengajuan PR Ditolak oleh Supervisor:</strong>
            <span class="small fst-italic">"<?= htmlspecialchars($pr['rejection_notes'] ?? 'Tanpa catatan spesifik.') ?>"</span>
        </div>
    </div>
    <?php endif; ?>

    <!-- DETAIL CARD TABEL BARANG -->
    <div class="card border shadow-sm mb-4">
        <div class="card-header py-2.5 px-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h2 class="h6 fw-bold text-dark mb-0 text-uppercase" style="font-size: 0.75rem;">Rincian Barang Kebutuhan</h2>
            <span class="small text-secondary">Target Tiba: <strong class="text-dark"><?= FormatHelper::dateIndo($pr['target_date']) ?></strong></span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Kode Part</th>
                        <th>Nama Suku Cadang / Material</th>
                        <th class="text-center">Stok Gudang</th>
                        <th class="text-center">Qty Diminta</th>
                        <th class="text-end">Harga Est Satuan</th>
                        <th class="text-end">Subtotal Estimasi</th>
                        <th>Catatan Penggunaan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $grandTotalEst = 0;
                    foreach ($items as $it): 
                        $sub = $it['qty_requested'] * $it['unit_price'];
                        $grandTotalEst += $sub;
                    ?>
                    <tr>
                        <td class="text-muted small"><?= $no++ ?></td>
                        <td class="font-monospace fw-bold text-dark"><?= htmlspecialchars($it['item_code']) ?></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($it['item_name']) ?></td>
                        <td class="text-center font-monospace <?= $it['current_stock'] <= $it['min_stock'] ? 'text-danger fw-bold' : 'text-secondary' ?>">
                            <?= $it['current_stock'] ?> <?= $it['unit'] ?>
                        </td>
                        <td class="text-center fw-bold text-dark bg-warning-subtle font-monospace">
                            <?= $it['qty_requested'] ?> <?= $it['unit'] ?>
                        </td>
                        <td class="text-end font-monospace text-secondary"><?= FormatHelper::rupiah($it['unit_price']) ?></td>
                        <td class="text-end font-monospace fw-bold text-dark"><?= FormatHelper::rupiah($sub) ?></td>
                        <td class="text-secondary small fst-italic"><?= htmlspecialchars($it['remarks'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="6" class="text-end text-dark">Perkiraan Nilai Total Pembelian:</td>
                        <td class="text-end font-monospace fw-black text-warning" style="color: #d97706 !important; font-size: 1rem;"><?= FormatHelper::rupiah($grandTotalEst) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- GENERAL NOTES -->
    <?php if (!empty($pr['general_notes'])): ?>
    <div class="card border shadow-sm mb-4">
        <div class="card-body p-3 small">
            <strong class="text-dark d-block mb-1">Catatan / Justifikasi Tambahan:</strong>
            <p class="text-secondary mb-0"><?= nl2br(htmlspecialchars($pr['general_notes'])) ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- MODAL TOLAK PR (BOOTSTRAP 5) -->
<?php if ($isApprover): ?>
<div class="modal fade" id="modalReject" tabindex="-1" aria-labelledby="modalRejectLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title h6 fw-bold d-flex align-items-center gap-2" id="modalRejectLabel">
                    <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
                    <span>Konfirmasi Penolakan Pengajuan PR</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=pr-reject" method="POST">
                <input type="hidden" name="id" value="<?= $pr['id'] ?>">
                <div class="modal-body p-4 small">
                    <label class="form-label fw-semibold text-secondary mb-1">Alasan Penolakan Wajib Diisi *</label>
                    <textarea name="rejection_notes" required rows="3" placeholder="Contoh: Stok cadangan masih tersedia di Rak B-02, silakan cek ulang..."
                              class="form-control form-control-sm"></textarea>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold shadow-sm">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
