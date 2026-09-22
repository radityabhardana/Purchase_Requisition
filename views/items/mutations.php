<?php
$pageTitle = 'Kartu Stok Mutasi';
require __DIR__ . '/../layouts/header.php';
use App\Helpers\FormatHelper;
?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border px-2.5 py-1 small fw-bold">
                <i data-lucide="history" class="me-1 text-muted" style="width: 14px; height: 14px;"></i>DATA MASTER • AUDIT TRAIL MUTASI STOK
            </span>
        </div>
        <h1 class="h4 fw-bold text-dark mb-1">Kartu Stok & Audit Mutasi Barang</h1>
        <p class="text-secondary small mb-0">
            Riwayat log keluar-masuk barang yang tercatat otomatis dari proses Goods Receipt (GR).
        </p>
    </div>

    <!-- Filter Pilih Barang -->
    <form action="index.php" method="GET" class="d-flex align-items-center gap-2">
        <input type="hidden" name="page" value="mutations">
        <select name="item_id" onchange="this.form.submit()" class="form-select form-select-sm shadow-sm" style="min-width: 260px;">
            <option value="">-- Tampilkan Seluruh Mutasi Barang --</option>
            <?php foreach ($allItems as $it): ?>
                <option value="<?= $it['id'] ?>" <?= ($selectedItem['id'] ?? 0) === $it['id'] ? 'selected' : '' ?>>
                    [<?= $it['item_code'] ?>] <?= htmlspecialchars($it['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($selectedItem)): ?>
            <a href="index.php?page=mutations" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
            </a>
        <?php endif; ?>
    </form>
</div>

<?php if (!empty($selectedItem)): ?>
<!-- Info Detail Barang yang Dipilih -->
<div class="card border-warning-subtle bg-warning-subtle shadow-sm mb-4">
    <div class="card-body p-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 bg-warning text-dark p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                <i data-lucide="package" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($selectedItem['name']) ?></h6>
                <div class="text-secondary small font-monospace">Kode: <?= htmlspecialchars($selectedItem['item_code']) ?> • Rak: <?= htmlspecialchars($selectedItem['location_rack']) ?></div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-4 text-sm">
            <div>
                <span class="text-secondary small d-block">Stok Saat Ini</span>
                <span class="h5 fw-bold text-dark mb-0"><?= $selectedItem['stock'] ?> <small class="text-muted fw-normal"><?= $selectedItem['unit'] ?></small></span>
            </div>
            <div>
                <span class="text-secondary small d-block">Batas Minimum</span>
                <span class="h5 fw-bold text-dark mb-0"><?= $selectedItem['min_stock'] ?> <small class="text-muted fw-normal"><?= $selectedItem['unit'] ?></small></span>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- TABEL MUTASI -->
<div class="card border shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Waktu Transaksi</th>
                    <th>Kode & Nama Part</th>
                    <th class="text-center">Tipe Mutasi</th>
                    <th>Nomor Referensi Dokumen</th>
                    <th class="text-center">Masuk (+IN)</th>
                    <th class="text-center">Keluar (-OUT)</th>
                    <th class="text-center">Saldo Akhir</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mutations)): ?>
                <tr>
                    <td colspan="8" class="py-5 text-center text-muted small">Belum ada riwayat mutasi stok.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($mutations as $m): ?>
                <tr>
                    <td class="text-secondary small text-nowrap">
                        <?= FormatHelper::dateIndo($m['created_at'], true) ?>
                    </td>
                    <td>
                        <div class="font-monospace fw-bold text-dark"><?= htmlspecialchars($m['item_code']) ?></div>
                        <div class="text-secondary small text-truncate" style="max-width: 240px;"><?= htmlspecialchars($m['item_name']) ?></div>
                    </td>
                    <td class="text-center">
                        <?php if ($m['mutation_type'] === 'IN'): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small fw-bold">
                                <i data-lucide="arrow-down-left" style="width: 12px; height: 12px;"></i> IN (Masuk)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small fw-bold">
                                <i data-lucide="arrow-up-right" style="width: 12px; height: 12px;"></i> OUT (Keluar)
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="font-monospace fw-semibold text-dark">
                        <?= htmlspecialchars($m['reference_no']) ?>
                    </td>
                    <td class="text-center fw-bold text-success font-monospace">
                        <?= $m['qty_in'] > 0 ? '+' . $m['qty_in'] : '-' ?>
                    </td>
                    <td class="text-center fw-bold text-danger font-monospace">
                        <?= $m['qty_out'] > 0 ? '-' . $m['qty_out'] : '-' ?>
                    </td>
                    <td class="text-center fw-black text-dark font-monospace bg-light">
                        <?= $m['balance'] ?> <small class="text-muted fw-normal"><?= htmlspecialchars($m['unit']) ?></small>
                    </td>
                    <td class="text-secondary small text-truncate" style="max-width: 220px;">
                        <?= htmlspecialchars($m['notes'] ?? '-') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
