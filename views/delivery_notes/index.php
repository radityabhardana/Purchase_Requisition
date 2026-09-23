<?php
$pageTitle = 'Surat Jalan (Delivery Notes)';
require __DIR__ . '/../layouts/header.php';
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;

$user = AuthHelper::user();
$canCreate = in_array($user['role'], ['warehouse', 'admin']);
$currentStatus = $_GET['status'] ?? '';
$searchQuery = $_GET['search'] ?? '';
?>

<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <div class="mb-2">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small fw-bold">
                <i data-lucide="truck" class="me-1" style="width: 14px; height: 14px;"></i>
                LOGISTIK & EKSPEDISI • PENGELUARAN BARANG (STOCK OUT)
            </span>
        </div>
        <h1 class="h3 fw-bold text-dark mb-1">Daftar Surat Jalan (Delivery Notes)</h1>
        <p class="text-secondary small mb-0">Dokumen resmi pengeluaran dan pengiriman barang PT Nandya Karya Perkasa ke pelanggan atau antar-plant.</p>
    </div>

    <?php if ($canCreate): ?>
    <div class="d-flex align-items-center gap-2">
        <a href="index.php?page=delivery-note-create" 
           class="btn btn-warning btn-sm fw-bold px-3 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm"
           style="background-color: var(--nkp-amber-500); border: none; color: #020617;">
            <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
            <span>+ Buat Surat Jalan Baru</span>
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- FILTER & SEARCH CARD -->
<div class="card border shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="index.php" method="GET" class="row g-2 align-items-center justify-content-between">
            <input type="hidden" name="page" value="delivery-notes">

            <!-- Search Field -->
            <div class="col-12 col-md-5 col-lg-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-secondary">
                        <i data-lucide="search" style="width: 15px; height: 15px;"></i>
                    </span>
                    <input type="text" name="search" value="<?= htmlspecialchars($searchQuery) ?>"
                           placeholder="Cari No SJ, penerima, plat truk, supir..."
                           class="form-control">
                    <?php if (!empty($searchQuery)): ?>
                        <a href="index.php?page=delivery-notes" class="btn btn-outline-secondary" title="Reset">
                            <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status Tabs -->
            <div class="col-12 col-md-7 col-lg-8">
                <div class="d-flex flex-wrap gap-1 justify-content-md-end">
                    <a href="index.php?page=delivery-notes<?= !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '' ?>" 
                       class="btn btn-sm <?= empty($currentStatus) ? 'btn-dark' : 'btn-light border text-secondary' ?> py-1 px-2.5 small">
                        Semua Status
                    </a>
                    <a href="index.php?page=delivery-notes&status=Shipped<?= !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '' ?>" 
                       class="btn btn-sm <?= $currentStatus === 'Shipped' ? 'btn-dark' : 'btn-light border text-secondary' ?> py-1 px-2.5 small">
                        Sedang Dikirim
                    </a>
                    <a href="index.php?page=delivery-notes&status=Delivered<?= !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '' ?>" 
                       class="btn btn-sm <?= $currentStatus === 'Delivered' ? 'btn-dark' : 'btn-light border text-secondary' ?> py-1 px-2.5 small">
                        Diterima (Selesai)
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- TABEL SURAT JALAN -->
<div class="card border shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h2 class="h6 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="clipboard-list" class="text-primary" style="width: 18px; height: 18px;"></i>
            <span>Arsip Dokumen Pengiriman Fisik</span>
        </h2>
        <span class="badge bg-light text-secondary border">Total: <?= count($deliveryNotes) ?> Dokumen</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-secondary text-uppercase small">
                <tr>
                    <th class="py-3 px-3">Nomor Surat Jalan</th>
                    <th class="py-3 px-3">Tanggal Kirim</th>
                    <th class="py-3 px-3">Tujuan / Penerima</th>
                    <th class="py-3 px-3">Armada & Supir</th>
                    <th class="py-3 px-3">Ref PO Customer</th>
                    <th class="py-3 px-3 text-center">Total Muatan</th>
                    <th class="py-3 px-3">Petugas Logistik</th>
                    <th class="py-3 px-3 text-center">Status</th>
                    <th class="py-3 px-3 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($deliveryNotes)): ?>
                <tr>
                    <td colspan="9" class="py-5 text-center text-muted">
                        <i data-lucide="truck" class="d-block mx-auto mb-2 text-muted opacity-50" style="width: 32px; height: 32px;"></i>
                        Belum ada dokumen Surat Jalan yang diterbitkan.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($deliveryNotes as $dn): ?>
                <tr>
                    <td class="py-3 px-3 font-monospace fw-bold text-dark">
                        <a href="index.php?page=delivery-note-detail&id=<?= $dn['id'] ?>" class="text-decoration-none text-dark fw-bold hover-primary">
                            <?= htmlspecialchars($dn['sj_number']) ?>
                        </a>
                    </td>
                    <td class="py-3 px-3 text-secondary text-nowrap">
                        <?= FormatHelper::dateIndo($dn['delivery_date']) ?>
                    </td>
                    <td class="py-3 px-3">
                        <div class="fw-bold text-dark"><?= htmlspecialchars($dn['recipient_name']) ?></div>
                        <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                            <span class="badge bg-light text-secondary border px-1.5 py-0.5"><?= htmlspecialchars($dn['recipient_type']) ?></span>
                            <span class="text-truncate" style="max-width: 220px;"><?= htmlspecialchars($dn['recipient_address']) ?></span>
                        </div>
                    </td>
                    <td class="py-3 px-3">
                        <div class="font-monospace fw-semibold text-dark"><?= htmlspecialchars($dn['vehicle_no']) ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($dn['driver_name']) ?></div>
                    </td>
                    <td class="py-3 px-3 font-monospace text-secondary">
                        <?= !empty($dn['customer_po_no']) ? htmlspecialchars($dn['customer_po_no']) : '<span class="text-muted">-</span>' ?>
                    </td>
                    <td class="py-3 px-3 text-center">
                        <span class="badge bg-dark text-warning font-monospace px-2 py-1">
                            <?= $dn['total_items'] ?> Part (<?= number_format((int)$dn['total_qty'], 0, ',', '.') ?> Pcs)
                        </span>
                    </td>
                    <td class="py-3 px-3">
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($dn['creator_name']) ?></div>
                        <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($dn['creator_dept']) ?></div>
                    </td>
                    <td class="py-3 px-3 text-center">
                        <?= FormatHelper::statusBadge($dn['status']) ?>
                    </td>
                    <td class="py-3 px-3 text-end text-nowrap">
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="index.php?page=delivery-note-detail&id=<?= $dn['id'] ?>" class="btn btn-outline-secondary" title="Lihat Rincian Pengiriman">
                                <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                            </a>
                            <a href="index.php?page=delivery-note-print&id=<?= $dn['id'] ?>" class="btn btn-outline-primary" title="Cetak Surat Jalan A4">
                                <i data-lucide="printer" style="width: 14px; height: 14px;"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
