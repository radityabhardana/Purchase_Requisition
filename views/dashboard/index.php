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
           class="btn btn-warning btn-sm fw-bold px-3 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm" style="background-color: var(--nkp-amber-500); border: none; color: #020617;">
            <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
            <span>+ Buat Pengajuan PR</span>
        </a>
        
        <?php if (in_array($user['role'], ['warehouse', 'admin'])): ?>
            <a href="index.php?page=gr" 
               class="btn btn-dark btn-sm fw-semibold px-3 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm">
                <i data-lucide="package-check" class="text-warning" style="width: 16px; height: 16px;"></i>
                <span>Penerimaan Barang</span>
            </a>
            <a href="index.php?page=delivery-note-create" 
               class="btn btn-outline-dark btn-sm fw-semibold px-3 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm">
                <i data-lucide="truck" class="text-primary" style="width: 16px; height: 16px;"></i>
                <span>+ Buat Surat Jalan</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- PETA ALUR KERJA UTAMA PENGADAAN (PANDUAN VISUAL SEKUENSIAL 1 S.D 4) -->
<div class="card border mb-4 shadow-sm">
    <div class="card-body p-3 p-sm-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 pb-3 mb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark p-1.5 rounded-2 d-flex align-items-center justify-content-center">
                        <i data-lucide="git-merge" style="width: 16px; height: 16px;"></i>
                    </span>
                    <h2 class="h6 fw-bold text-dark text-uppercase mb-0 tracking-wide">
                        Peta Alur Kerja Pengadaan Barang (Urutan 1 s.d 4)
                    </h2>
                </div>
                <p class="text-secondary small mb-0 mt-1">
                    Alur kerja berjalan dari kiri ke kanan. Ikuti tahapan berurutan sesuai peran departemen Anda.
                </p>
            </div>

            <div class="d-flex align-items-center gap-2 small">
                <span class="text-muted">Departemen Anda:</span>
                <span class="badge bg-light text-dark border px-2.5 py-1.5 text-uppercase fw-bold">
                    <?= htmlspecialchars($user['department'] ?? ucfirst($user['role'])) ?>
                </span>
            </div>
        </div>

        <!-- 4 KARTU TAHAPAN BERURUTAN -->
        <div class="row g-3">
            <!-- TAHAP 1: PR -->
            <div class="col-12 col-md-6 col-xl-3">
                <div class="workflow-step-card h-100 d-flex flex-column justify-content-between <?= in_array($user['role'], ['requester', 'admin']) ? 'active-duty' : '' ?>">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">1</span>
                                <span class="fw-bold text-dark small">Pengajuan PR</span>
                            </div>
                            <?php if (in_array($user['role'], ['requester', 'admin'])): ?>
                                <span class="badge bg-warning text-dark px-1.5 py-0.5" style="font-size: 0.65rem;">Tugas Anda</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-muted fw-semibold small mb-1" style="font-size: 0.75rem;">Pelaksana: Teknisi / Pemohon</div>
                        <p class="text-secondary small mb-3 lh-sm" style="font-size: 0.75rem;">
                            Pilih barang dari katalog, tentukan tanggal target & jumlah kebutuhan mesin.
                        </p>
                    </div>
                    <a href="index.php?page=pr-create" 
                       class="btn btn-warning btn-sm w-100 fw-bold d-flex align-items-center justify-content-center gap-1.5 shadow-sm" style="background-color: var(--nkp-amber-500); border: none; color: #020617;">
                        <i data-lucide="plus-circle" style="width: 14px; height: 14px;"></i>
                        <span>+ Buat Pengajuan PR</span>
                    </a>
                </div>
            </div>

            <!-- TAHAP 2: APPROVAL SPV -->
            <div class="col-12 col-md-6 col-xl-3">
                <div class="workflow-step-card h-100 d-flex flex-column justify-content-between <?= in_array($user['role'], ['supervisor', 'admin']) ? 'border-success bg-light' : '' ?>">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark text-light rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">2</span>
                                <span class="fw-bold text-dark small">Persetujuan SPV</span>
                            </div>
                            <?php if ($kpi['pending_pr'] > 0): ?>
                                <span class="badge bg-danger px-1.5 py-0.5" style="font-size: 0.65rem;"><?= $kpi['pending_pr'] ?> Antre</span>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5" style="font-size: 0.65rem;">Selesai</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-muted fw-semibold small mb-1" style="font-size: 0.75rem;">Pelaksana: Supervisor (SPV)</div>
                        <p class="text-secondary small mb-3 lh-sm" style="font-size: 0.75rem;">
                            Verifikasi urgensi breakdown, cek anggaran, dan setujui / tolak dokumen PR.
                        </p>
                    </div>
                    <?php if (in_array($user['role'], ['supervisor', 'admin'])): ?>
                    <a href="index.php?page=pr&status=Pending" 
                       class="btn btn-outline-success btn-sm w-100 fw-bold d-flex align-items-center justify-content-center gap-1.5">
                        <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i>
                        <span>Periksa & Setujui PR</span>
                    </a>
                    <?php else: ?>
                    <button type="button" disabled 
                            class="btn btn-light btn-sm w-100 text-muted border fw-semibold d-flex align-items-center justify-content-center gap-1.5"
                            title="Khusus Supervisor & Admin">
                        <i data-lucide="lock" style="width: 14px; height: 14px;"></i>
                        <span>Khusus Supervisor</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TAHAP 3: PURCHASE ORDER -->
            <div class="col-12 col-md-6 col-xl-3">
                <div class="workflow-step-card h-100 d-flex flex-column justify-content-between <?= in_array($user['role'], ['purchasing', 'admin']) ? 'border-primary bg-light' : '' ?>">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">3</span>
                                <span class="fw-bold text-dark small">Penerbitan PO</span>
                            </div>
                            <?php if (in_array($user['role'], ['purchasing', 'admin'])): ?>
                                <span class="badge bg-primary px-1.5 py-0.5" style="font-size: 0.65rem;">Tugas Anda</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-muted fw-semibold small mb-1" style="font-size: 0.75rem;">Pelaksana: Purchasing Dept</div>
                        <p class="text-secondary small mb-3 lh-sm" style="font-size: 0.75rem;">
                            Pilih vendor rekanan resmi PT NKP, tentukan harga, PPN, dan cetak lembar PO A4.
                        </p>
                    </div>
                    <?php if (in_array($user['role'], ['purchasing', 'admin'])): ?>
                    <a href="index.php?page=po" 
                       class="btn btn-outline-primary btn-sm w-100 fw-bold d-flex align-items-center justify-content-center gap-1.5">
                        <i data-lucide="shopping-cart" style="width: 14px; height: 14px;"></i>
                        <span>Menu Purchase Order</span>
                    </a>
                    <?php else: ?>
                    <button type="button" disabled 
                            class="btn btn-light btn-sm w-100 text-muted border fw-semibold d-flex align-items-center justify-content-center gap-1.5"
                            title="Khusus Divisi Purchasing & Admin">
                        <i data-lucide="lock" style="width: 14px; height: 14px;"></i>
                        <span>Khusus Purchasing</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TAHAP 4: PENERIMAAN BARANG -->
            <div class="col-12 col-md-6 col-xl-3">
                <div class="workflow-step-card h-100 d-flex flex-column justify-content-between <?= in_array($user['role'], ['warehouse', 'admin']) ? 'border-teal bg-light' : '' ?>">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">4</span>
                                <span class="fw-bold text-dark small">Penerimaan (GR)</span>
                            </div>
                            <span class="badge bg-teal-subtle text-teal border border-teal-subtle px-1.5 py-0.5" style="font-size: 0.65rem; color: #0d9488;">Auto-Sync</span>
                        </div>
                        <div class="text-muted fw-semibold small mb-1" style="font-size: 0.75rem;">Pelaksana: Warehouse (Gudang)</div>
                        <p class="text-secondary small mb-3 lh-sm" style="font-size: 0.75rem;">
                            Cocokkan Surat Jalan vendor, input uji QC, dan stok gudang otomatis bertambah.
                        </p>
                    </div>
                    <?php if (in_array($user['role'], ['warehouse', 'admin'])): ?>
                    <a href="index.php?page=gr" 
                       class="btn btn-outline-teal btn-sm w-100 fw-bold d-flex align-items-center justify-content-center gap-1.5" style="color: #0f766e; border-color: #0f766e;">
                        <i data-lucide="package-check" style="width: 14px; height: 14px;"></i>
                        <span>Penerimaan Barang</span>
                    </a>
                    <?php else: ?>
                    <button type="button" disabled 
                            class="btn btn-light btn-sm w-100 text-muted border fw-semibold d-flex align-items-center justify-content-center gap-1.5"
                            title="Khusus Divisi Warehouse & Admin">
                        <i data-lucide="lock" style="width: 14px; height: 14px;"></i>
                        <span>Khusus Warehouse</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4 KPI METRIC CARDS -->
<div class="row g-3 mb-4">
    <!-- Card 1: Pending PR -->
    <div class="col-12 col-sm-6 col-lg-3">
        <a href="index.php?page=pr&status=Pending" class="card h-100 border text-decoration-none shadow-sm text-dark hover-shadow transition">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold text-uppercase">PR Menunggu Approval</span>
                    <div class="rounded-2 bg-warning-subtle text-warning p-2 d-flex align-items-center justify-content-center">
                        <i data-lucide="clock" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-baseline mt-2">
                    <div class="h3 fw-black text-dark mb-0"><?= $kpi['pending_pr'] ?></div>
                    <span class="small fw-semibold text-warning" style="color: #d97706 !important;">Butuh SPV &rarr;</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 2: Active PO -->
    <div class="col-12 col-sm-6 col-lg-3">
        <?php if (in_array($user['role'], ['purchasing', 'admin'])): ?>
        <a href="index.php?page=po" class="card h-100 border text-decoration-none shadow-sm text-dark hover-shadow transition">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold text-uppercase">PO Aktif (Dalam Proses)</span>
                    <div class="rounded-2 bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center">
                        <i data-lucide="truck" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-baseline mt-2">
                    <div class="h3 fw-black text-dark mb-0"><?= $kpi['active_po'] ?></div>
                    <span class="small fw-semibold text-primary">Kirim &rarr;</span>
                </div>
            </div>
        </a>
        <?php else: ?>
        <div class="card h-100 border shadow-sm text-dark">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold text-uppercase">PO Aktif (Dalam Proses)</span>
                    <div class="rounded-2 bg-light text-muted p-2 d-flex align-items-center justify-content-center">
                        <i data-lucide="truck" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-baseline mt-2">
                    <div class="h3 fw-black text-dark mb-0"><?= $kpi['active_po'] ?></div>
                    <span class="small text-muted d-flex align-items-center gap-1">
                        <i data-lucide="lock" style="width: 12px; height: 12px;"></i> Khusus Purchasing
                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Card 3: Safety Stock Alert -->
    <div class="col-12 col-sm-6 col-lg-3">
        <a href="index.php?page=items" class="card h-100 <?= $kpi['critical_stock'] > 0 ? 'border-danger bg-danger-subtle' : 'border' ?> text-decoration-none shadow-sm text-dark hover-shadow transition">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold text-uppercase">Stok Suku Cadang Kritis</span>
                    <div class="rounded-2 <?= $kpi['critical_stock'] > 0 ? 'bg-danger text-white' : 'bg-light text-muted' ?> p-2 d-flex align-items-center justify-content-center">
                        <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-baseline mt-2">
                    <div class="h3 fw-black <?= $kpi['critical_stock'] > 0 ? 'text-danger' : 'text-dark' ?> mb-0"><?= $kpi['critical_stock'] ?></div>
                    <span class="small fw-bold <?= $kpi['critical_stock'] > 0 ? 'text-danger' : 'text-success' ?>">
                        <?= $kpi['critical_stock'] > 0 ? 'Segera di-PO' : 'Aman' ?>
                    </span>
                </div>
            </div>
        </a>
    </div>

    <!-- Card 4: Total Spend -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100 border shadow-sm">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold text-uppercase">Total Nilai Pengadaan</span>
                    <div class="rounded-2 bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center">
                        <i data-lucide="wallet" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-baseline mt-2">
                    <div class="h5 fw-black text-dark text-truncate mb-0" title="<?= FormatHelper::rupiah($kpi['total_spend']) ?>">
                        <?= FormatHelper::rupiah($kpi['total_spend']) ?>
                    </div>
                    <span class="small text-success fw-semibold">Tahun Ini</span>
                </div>
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
