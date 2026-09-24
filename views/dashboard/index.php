<?php
$pageTitle = 'Dashboard Operasional';
require __DIR__ . '/../layouts/header.php';
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;

$user = AuthHelper::user();
?>

<!-- Header Title & Quick Action -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Ringkasan Pengadaan & Inventaris</h1>
        <p class="text-secondary small mb-0">
            Monitoring stok suku cadang mesin press & alur dokumen pembelian PT Nandya Karya Perkasa.
        </p>
    </div>

    <!-- Tombol Aksi Cepat -->
    <div class="d-flex align-items-center gap-2">
        <a href="index.php?page=pr-create" 
           class="btn btn-warning btn-sm fw-bold px-3 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm text-dark" style="background-color: var(--nkp-amber-500); border: none;">
            <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
            <span>+ Buat Pengajuan PR</span>
        </a>
        
        <?php if (in_array($user['role'], ['warehouse', 'admin'])): ?>
            <a href="index.php?page=gr" 
               class="btn btn-dark btn-sm fw-semibold px-3 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm text-white">
                <i data-lucide="package-check" class="text-warning" style="width: 16px; height: 16px;"></i>
                <span>Penerimaan Barang</span>
            </a>
            <a href="index.php?page=delivery-note-create" 
               class="btn btn-primary btn-sm fw-semibold px-3 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm text-white">
                <i data-lucide="truck" style="width: 16px; height: 16px;"></i>
                <span>+ Buat Surat Jalan</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- PETA ALUR KERJA RINGKAS (STEPPER PIPELINE) -->
<div class="card border shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3 pb-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark p-1.5 rounded-2 d-flex align-items-center justify-content-center">
                    <i data-lucide="git-merge" style="width: 15px; height: 15px;"></i>
                </span>
                <span class="h6 fw-bold text-dark text-uppercase mb-0 tracking-wide small">
                    Alur Kerja Pengadaan Barang (Urutan 1 s.d 4)
                </span>
            </div>
            <div class="d-flex align-items-center gap-1.5 small text-muted">
                <span>Peran:</span>
                <span class="badge bg-light text-dark border px-2 py-1 text-uppercase fw-bold">
                    <?= htmlspecialchars($user['department'] ?? ucfirst($user['role'])) ?>
                </span>
            </div>
        </div>

        <!-- 4 Step Pipeline -->
        <div class="row g-2 align-items-center">
            <!-- Step 1: PR -->
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="index.php?page=pr" class="workflow-stepper-item d-flex align-items-center p-2.5 rounded-3 text-decoration-none text-dark <?= in_array($user['role'], ['requester', 'admin']) ? 'active-step' : '' ?>" title="Klik untuk membuka menu Pengajuan PR">
                    <div class="stepper-circle bg-warning text-dark me-2.5">1</div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fw-bold small text-truncate">Pengajuan PR</span>
                            <?php if (in_array($user['role'], ['requester', 'admin'])): ?>
                                <span class="badge bg-warning text-dark px-1.5 py-0.5 rounded-pill" style="font-size: 0.6rem;">Tugas Anda</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-secondary" style="font-size: 0.72rem;">Teknisi / Pemohon</div>
                    </div>
                </a>
            </div>

            <!-- Step 2: Approval SPV -->
            <div class="col-12 col-sm-6 col-xl-3">
                <?php if (in_array($user['role'], ['supervisor', 'admin'])): ?>
                <a href="index.php?page=pr&status=Pending" class="workflow-stepper-item d-flex align-items-center p-2.5 rounded-3 text-decoration-none text-dark active-step" title="Klik untuk memeriksa PR yang menunggu persetujuan">
                    <div class="stepper-circle bg-success text-white me-2.5">2</div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fw-bold small text-truncate">Persetujuan SPV</span>
                            <?php if ($kpi['pending_pr'] > 0): ?>
                                <span class="badge bg-danger text-white px-1.5 py-0.5 rounded-pill" style="font-size: 0.6rem;"><?= $kpi['pending_pr'] ?> Antre</span>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success px-1.5 py-0.5 rounded-pill" style="font-size: 0.6rem;">Selesai</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-secondary" style="font-size: 0.72rem;">Supervisor Dept</div>
                    </div>
                </a>
                <?php else: ?>
                <div class="workflow-stepper-item d-flex align-items-center p-2.5 rounded-3 text-muted bg-light border-0">
                    <div class="stepper-circle bg-secondary text-white opacity-50 me-2.5">2</div>
                    <div class="flex-grow-1 overflow-hidden">
                        <span class="fw-semibold small text-truncate text-secondary d-block">Persetujuan SPV</span>
                        <div class="text-muted" style="font-size: 0.72rem;">Supervisor Dept</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Step 3: Purchase Order -->
            <div class="col-12 col-sm-6 col-xl-3">
                <?php if (in_array($user['role'], ['purchasing', 'admin'])): ?>
                <a href="index.php?page=po" class="workflow-stepper-item d-flex align-items-center p-2.5 rounded-3 text-decoration-none text-dark active-step" title="Klik untuk menerbitkan Purchase Order">
                    <div class="stepper-circle bg-primary text-white me-2.5">3</div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fw-bold small text-truncate">Penerbitan PO</span>
                            <span class="badge bg-primary text-white px-1.5 py-0.5 rounded-pill" style="font-size: 0.6rem;">Tugas Anda</span>
                        </div>
                        <div class="text-secondary" style="font-size: 0.72rem;">Purchasing Dept</div>
                    </div>
                </a>
                <?php else: ?>
                <div class="workflow-stepper-item d-flex align-items-center p-2.5 rounded-3 text-muted bg-light border-0">
                    <div class="stepper-circle bg-secondary text-white opacity-50 me-2.5">3</div>
                    <div class="flex-grow-1 overflow-hidden">
                        <span class="fw-semibold small text-truncate text-secondary d-block">Penerbitan PO</span>
                        <div class="text-muted" style="font-size: 0.72rem;">Purchasing Dept</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Step 4: Penerimaan Barang -->
            <div class="col-12 col-sm-6 col-xl-3">
                <?php if (in_array($user['role'], ['warehouse', 'admin'])): ?>
                <a href="index.php?page=gr" class="workflow-stepper-item d-flex align-items-center p-2.5 rounded-3 text-decoration-none text-dark active-step" title="Klik untuk memproses penerimaan barang (GR)">
                    <div class="stepper-circle text-white me-2.5" style="background-color: #0d9488;">4</div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fw-bold small text-truncate">Penerimaan (GR)</span>
                            <span class="badge text-white px-1.5 py-0.5 rounded-pill" style="font-size: 0.6rem; background-color: #0d9488;">Tugas Anda</span>
                        </div>
                        <div class="text-secondary" style="font-size: 0.72rem;">Warehouse (Gudang)</div>
                    </div>
                </a>
                <?php else: ?>
                <div class="workflow-stepper-item d-flex align-items-center p-2.5 rounded-3 text-muted bg-light border-0">
                    <div class="stepper-circle bg-secondary text-white opacity-50 me-2.5">4</div>
                    <div class="flex-grow-1 overflow-hidden">
                        <span class="fw-semibold small text-truncate text-secondary d-block">Penerimaan (GR)</span>
                        <div class="text-muted" style="font-size: 0.72rem;">Warehouse (Gudang)</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- 4 KPI METRIC CARDS -->
<div class="row g-3 mb-4">
    <!-- Card 1: Pending PR -->
    <div class="col-12 col-sm-6 col-lg-3">
        <a href="index.php?page=pr&status=Pending" class="kpi-card d-block text-decoration-none text-dark h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-bold text-uppercase">PR Menunggu Approval</span>
                <div class="kpi-icon-amber">
                    <i data-lucide="clock" style="width: 18px; height: 18px;"></i>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-baseline mt-2">
                <div class="h3 fw-black text-dark mb-0 font-monospace"><?= $kpi['pending_pr'] ?></div>
                <?php if ($kpi['pending_pr'] > 0): ?>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5 rounded-pill fw-semibold">Butuh SPV &rarr;</span>
                <?php else: ?>
                    <span class="text-secondary small">Normal</span>
                <?php endif; ?>
            </div>
        </a>
    </div>

    <!-- Card 2: Active PO -->
    <div class="col-12 col-sm-6 col-lg-3">
        <?php if (in_array($user['role'], ['purchasing', 'admin'])): ?>
        <a href="index.php?page=po" class="kpi-card d-block text-decoration-none text-dark h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-bold text-uppercase">PO Aktif (Dalam Proses)</span>
                <div class="kpi-icon-blue">
                    <i data-lucide="truck" style="width: 18px; height: 18px;"></i>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-baseline mt-2">
                <div class="h3 fw-black text-dark mb-0 font-monospace"><?= $kpi['active_po'] ?></div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 rounded-pill fw-semibold">Dalam Kirim &rarr;</span>
            </div>
        </a>
        <?php else: ?>
        <div class="kpi-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-bold text-uppercase">PO Aktif (Dalam Proses)</span>
                <div class="kpi-icon-blue">
                    <i data-lucide="truck" style="width: 18px; height: 18px;"></i>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-baseline mt-2">
                <div class="h3 fw-black text-dark mb-0 font-monospace"><?= $kpi['active_po'] ?></div>
                <span class="small text-muted d-flex align-items-center gap-1">
                    <i data-lucide="lock" style="width: 12px; height: 12px;"></i> Khusus Purchasing
                </span>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Card 3: Safety Stock Alert -->
    <div class="col-12 col-sm-6 col-lg-3">
        <a href="index.php?page=items" class="kpi-card d-block text-decoration-none text-dark h-100 <?= $kpi['critical_stock'] > 0 ? 'border-danger bg-danger-subtle bg-opacity-10' : '' ?>">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-bold text-uppercase">Stok Suku Cadang Kritis</span>
                <div class="kpi-icon-rose <?= $kpi['critical_stock'] > 0 ? 'bg-danger text-white' : '' ?>">
                    <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-baseline mt-2">
                <div class="h3 fw-black <?= $kpi['critical_stock'] > 0 ? 'text-danger' : 'text-dark' ?> mb-0 font-monospace"><?= $kpi['critical_stock'] ?></div>
                <?php if ($kpi['critical_stock'] > 0): ?>
                    <span class="badge bg-danger text-white px-2 py-0.5 rounded-pill fw-bold">Segera di-PO</span>
                <?php else: ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill">Aman</span>
                <?php endif; ?>
            </div>
        </a>
    </div>

    <!-- Card 4: Total Spend -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="kpi-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-bold text-uppercase">Total Nilai Pengadaan</span>
                <div class="kpi-icon-emerald">
                    <i data-lucide="wallet" style="width: 18px; height: 18px;"></i>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-baseline mt-2">
                <div class="h5 fw-black text-dark text-truncate mb-0 font-monospace" title="<?= FormatHelper::rupiah($kpi['total_spend']) ?>">
                    <?= FormatHelper::rupiah($kpi['total_spend']) ?>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill fw-semibold">Tahun Berjalan</span>
            </div>
        </div>
    </div>
</div>

<!-- SAFETY STOCK ALERT BANNER & TABLE -->
<?php if (!empty($criticalItems)): ?>
<div class="card border-danger shadow-sm mb-4">
    <div class="card-header bg-danger-subtle text-danger-emphasis py-2.5 px-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-1">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger rounded-circle p-1"> </span>
            <strong class="small">
                Peringatan Dini: <?= count($criticalItems) ?> Suku Cadang Mesin di Bawah Batas Minimum (Safety Stock)
            </strong>
        </div>
        <span class="small text-danger fw-semibold">Dapat menyebabkan Line Stop jika tidak segera dipesan</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Kode Part</th>
                    <th>Nama Suku Cadang / Material</th>
                    <th>Kategori</th>
                    <th class="text-center">Stok Saat Ini</th>
                    <th class="text-center">Safety Stock</th>
                    <th>Lokasi Rak</th>
                    <th class="text-end">Tindakan Cepat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($criticalItems as $crit): ?>
                <tr>
                    <td class="font-monospace fw-bold text-dark"><?= htmlspecialchars($crit['item_code']) ?></td>
                    <td class="fw-semibold text-dark"><?= htmlspecialchars($crit['name']) ?></td>
                    <td><span class="badge bg-light text-secondary border"><?= htmlspecialchars($crit['category']) ?></span></td>
                    <td class="text-center">
                        <span class="badge bg-danger text-white px-2 py-1">
                            <?= $crit['stock'] ?> <?= $crit['unit'] ?>
                        </span>
                    </td>
                    <td class="text-center text-muted fw-semibold">
                        <?= $crit['min_stock'] ?> <?= $crit['unit'] ?>
                    </td>
                    <td class="font-monospace text-secondary"><?= htmlspecialchars($crit['location_rack']) ?></td>
                    <td class="text-end">
                        <a href="index.php?page=pr-create&item_id=<?= $crit['id'] ?>"
                           class="btn btn-warning btn-sm fw-bold px-2.5 py-1 d-inline-flex align-items-center gap-1 shadow-sm" style="background-color: var(--nkp-amber-500); border: none; color: #020617; font-size: 0.75rem;">
                            <i data-lucide="file-plus" style="width: 14px; height: 14px;"></i>
                            <span>Ajukan PR</span>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- RECENT ACTIVITIES (DUA KOLOM: PR TERBARU & PO TERBARU) -->
<div class="row g-3">
    <!-- Kolom 1: Pengajuan PR Terkini -->
    <div class="col-12 col-lg-6">
        <div class="card border shadow-sm h-100">
            <div class="card-header py-2.5 px-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="file-text" class="text-warning" style="width: 18px; height: 18px;"></i>
                    <span class="fw-bold small text-dark">Permintaan Pembelian (PR) Terkini</span>
                </div>
                <a href="index.php?page=pr" class="small text-decoration-none fw-semibold text-warning" style="color: #d97706 !important;">Lihat Semua &rarr;</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentPRs)): ?>
                    <div class="p-4 text-center text-muted small">Belum ada pengajuan PR.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentPRs as $pr): ?>
                        <div class="list-group-item p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <a href="index.php?page=pr-detail&id=<?= $pr['id'] ?>" class="font-monospace fw-bold text-dark text-decoration-none hover-primary">
                                        <?= htmlspecialchars($pr['pr_number']) ?>
                                    </a>
                                    <?= FormatHelper::priorityBadge($pr['priority']) ?>
                                </div>
                                <div class="text-muted small">
                                    Oleh: <strong class="text-dark"><?= htmlspecialchars($pr['requester_name']) ?></strong> (<?= htmlspecialchars($pr['requester_dept']) ?>)
                                </div>
                                <div class="fw-semibold text-dark small mt-1" style="max-width: 320px;">
                                    <i data-lucide="wrench" class="text-primary me-1" style="width: 12px; height: 12px;"></i>
                                    <?= htmlspecialchars($pr['item_summary'] ?? '-') ?>
                                </div>
                                <div class="text-muted small mt-0.5" style="font-size: 0.72rem;">
                                    Target: <?= FormatHelper::dateIndo($pr['target_date']) ?> • <?= $pr['total_items'] ?> Macam Barang
                                </div>
                            </div>
                            <div class="text-end">
                                <?= FormatHelper::statusBadge($pr['status']) ?>
                                <div class="mt-1">
                                    <a href="index.php?page=pr-detail&id=<?= $pr['id'] ?>" class="text-decoration-none small text-secondary fw-semibold">
                                        Detail &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Kolom 2: Purchase Order (PO) Terkini -->
    <div class="col-12 col-lg-6">
        <div class="card border shadow-sm h-100">
            <div class="card-header py-2.5 px-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="shopping-cart" class="text-primary" style="width: 18px; height: 18px;"></i>
                    <span class="fw-bold small text-dark">Purchase Order (PO) Terkini</span>
                </div>
                <?php if (in_array($user['role'], ['purchasing', 'admin'])): ?>
                    <a href="index.php?page=po" class="small text-decoration-none fw-semibold text-primary">Lihat Semua &rarr;</a>
                <?php else: ?>
                    <span class="small text-muted d-flex align-items-center gap-1">
                        <i data-lucide="lock" style="width: 12px; height: 12px;"></i> Khusus Purchasing
                    </span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentPOs)): ?>
                    <div class="p-4 text-center text-muted small">Belum ada dokumen PO yang diterbitkan.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentPOs as $po): ?>
                        <div class="list-group-item p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <?php if (in_array($user['role'], ['purchasing', 'admin'])): ?>
                                        <a href="index.php?page=po-print&id=<?= $po['id'] ?>" class="font-monospace fw-bold text-dark text-decoration-none hover-primary">
                                            <?= htmlspecialchars($po['po_number']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="font-monospace fw-bold text-dark">
                                            <?= htmlspecialchars($po['po_number']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-muted small">
                                    Vendor: <strong class="text-dark"><?= htmlspecialchars($po['supplier_name']) ?></strong>
                                </div>
                                <div class="fw-semibold text-dark small mt-1" style="max-width: 320px;">
                                    <i data-lucide="package" class="text-primary me-1" style="width: 12px; height: 12px;"></i>
                                    <?= htmlspecialchars($po['item_summary'] ?? '-') ?>
                                </div>
                                <div class="text-muted small mt-0.5" style="font-size: 0.72rem;">
                                    Deadline: <?= FormatHelper::dateIndo($po['delivery_deadline']) ?> • <?= FormatHelper::rupiah($po['grand_total']) ?>
                                </div>
                            </div>
                            <div class="text-end">
                                <?= FormatHelper::statusBadge($po['status']) ?>
                                <?php if (in_array($user['role'], ['purchasing', 'admin'])): ?>
                                    <div class="mt-1">
                                        <a href="index.php?page=po-print&id=<?= $po['id'] ?>" class="text-decoration-none small text-secondary fw-semibold">
                                            Lihat Dokumen &rarr;
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="mt-1 small text-muted d-flex align-items-center justify-content-end gap-1" style="font-size: 0.72rem;">
                                        <i data-lucide="lock" style="width: 10px; height: 10px;"></i> Read-only
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
