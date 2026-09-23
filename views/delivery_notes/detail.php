<?php
$pageTitle = 'Detail Surat Jalan - ' . htmlspecialchars($dn['sj_number']);
require __DIR__ . '/../layouts/header.php';
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;

$user = AuthHelper::user();
$canManage = in_array($user['role'], ['warehouse', 'admin']);
?>

<div class="mb-4">
    <a href="index.php?page=delivery-notes" class="text-secondary text-decoration-none small d-inline-flex align-items-center gap-1 mb-2 hover-primary">
        <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
        <span>Kembali ke Daftar Surat Jalan</span>
    </a>
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-dark text-warning font-monospace fw-bold px-2 py-1"><?= htmlspecialchars($dn['sj_number']) ?></span>
                <?= FormatHelper::statusBadge($dn['status']) ?>
            </div>
            <h1 class="h3 fw-bold text-dark mb-1">Rincian Dokumen Surat Jalan</h1>
            <p class="text-secondary small mb-0">Tujuan: <strong><?= htmlspecialchars($dn['recipient_name']) ?></strong> • Tanggal: <?= FormatHelper::dateIndo($dn['delivery_date']) ?></p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="index.php?page=delivery-note-print&id=<?= $dn['id'] ?>" target="_blank"
               class="btn btn-dark btn-sm text-warning fw-bold px-3 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm">
                <i data-lucide="printer" style="width: 16px; height: 16px;"></i>
                <span>Cetak Lembar A4 (Print / PDF)</span>
            </a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- INFORMASI PENGIRIMAN & PENERIMA -->
    <div class="col-12 col-lg-5">
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="map-pin" class="text-primary" style="width: 18px; height: 18px;"></i>
                    <h2 class="h6 fw-bold text-dark mb-0">Tujuan & Detail Penerima</h2>
                </div>
                <span class="badge bg-light text-secondary border"><?= htmlspecialchars($dn['recipient_type']) ?></span>
            </div>
            <div class="card-body p-3 p-sm-4 small">
                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 0.75rem;">Nama Pelanggan / Institusi:</span>
                    <strong class="text-dark fs-6"><?= htmlspecialchars($dn['recipient_name']) ?></strong>
                </div>

                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 0.75rem;">Alamat Lengkap Pengiriman:</span>
                    <p class="text-secondary mb-0 lh-sm"><?= nl2br(htmlspecialchars($dn['recipient_address'])) ?></p>
                </div>

                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 0.75rem;">Nomor PO Pemesan (Customer Ref):</span>
                    <span class="font-monospace fw-bold text-dark"><?= !empty($dn['customer_po_no']) ? htmlspecialchars($dn['customer_po_no']) : '-' ?></span>
                </div>

                <hr class="my-3">

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <span class="text-muted d-block" style="font-size: 0.75rem;">Plat No. Truk/Armada:</span>
                        <span class="font-monospace fw-bold text-dark badge bg-light border text-dark px-2 py-1"><?= htmlspecialchars($dn['vehicle_no']) ?></span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block" style="font-size: 0.75rem;">Pengemudi (Driver):</span>
                        <strong class="text-dark"><?= htmlspecialchars($dn['driver_name']) ?></strong>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 0.75rem;">Petugas Logistik PT NKP:</span>
                    <strong class="text-dark"><?= htmlspecialchars($dn['creator_name']) ?></strong>
                    <span class="text-muted">(<?= htmlspecialchars($dn['creator_dept']) ?>)</span>
                </div>

                <?php if (!empty($dn['notes'])): ?>
                <div class="p-2.5 bg-light rounded border">
                    <span class="text-muted fw-bold d-block mb-1" style="font-size: 0.7rem;">Catatan Pengiriman:</span>
                    <span class="text-secondary"><?= nl2br(htmlspecialchars($dn['notes'])) ?></span>
                </div>
                <?php endif; ?>

                <?php if ($canManage && $dn['status'] === 'Shipped'): ?>
                <div class="mt-4 pt-3 border-top">
                    <form action="index.php?page=delivery-note-status" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menandai barang telah sampai dan diterima di lokasi pelanggan?');">
                        <input type="hidden" name="id" value="<?= $dn['id'] ?>">
                        <input type="hidden" name="status" value="Delivered">
                        <button type="submit" class="btn btn-success btn-sm w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm">
                            <i data-lucide="check-check" style="width: 16px; height: 16px;"></i>
                            <span>Konfirmasi Barang Telah Diterima (Delivered)</span>
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- TABEL DAFTAR BARANG YANG DIKIRIM -->
    <div class="col-12 col-lg-7">
        <div class="card border shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="package" class="text-primary" style="width: 18px; height: 18px;"></i>
                    <h2 class="h6 fw-bold text-dark mb-0">Rincian Muatan Suku Cadang</h2>
                </div>
                <span class="badge bg-dark text-warning"><?= count($items) ?> Jenis Barang</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-secondary text-uppercase" style="font-size: 0.7rem;">
                        <tr>
                            <th class="py-3 px-3">Kode Part</th>
                            <th class="py-3 px-3">Nama Barang</th>
                            <th class="py-3 px-3 text-center">Qty Kirim</th>
                            <th class="py-3 px-3">Kemasan</th>
                            <th class="py-3 px-3">Rak Asal</th>
                            <th class="py-3 px-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalPcs = 0;
                        foreach ($items as $item): 
                            $totalPcs += (int)$item['qty_shipped'];
                        ?>
                        <tr>
                            <td class="py-3 px-3 font-monospace fw-bold text-dark"><?= htmlspecialchars($item['item_code']) ?></td>
                            <td class="py-3 px-3">
                                <div class="fw-bold text-dark"><?= htmlspecialchars($item['item_name']) ?></div>
                                <span class="badge bg-light text-muted border" style="font-size: 0.65rem;"><?= htmlspecialchars($item['category']) ?></span>
                            </td>
                            <td class="py-3 px-3 text-center font-monospace fw-bold fs-6 text-dark">
                                <?= number_format((int)$item['qty_shipped'], 0, ',', '.') ?>
                                <span class="small text-muted fw-normal"><?= htmlspecialchars($item['unit']) ?></span>
                            </td>
                            <td class="py-3 px-3 text-secondary"><?= htmlspecialchars($item['packaging']) ?></td>
                            <td class="py-3 px-3 font-monospace text-muted"><?= htmlspecialchars($item['location_rack']) ?></td>
                            <td class="py-3 px-3 text-muted"><?= !empty($item['remarks']) ? htmlspecialchars($item['remarks']) : '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2" class="py-3 px-3 text-end text-dark">TOTAL MUATAN PENGIRIMAN:</td>
                            <td class="py-3 px-3 text-center font-monospace fs-6 text-dark"><?= number_format($totalPcs, 0, ',', '.') ?> Pcs</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card border shadow-sm mt-4 bg-light">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 text-secondary small">
                    <i data-lucide="shield-check" class="text-success" style="width: 20px; height: 20px;"></i>
                    <span>Pengeluaran barang ini telah tercatat secara otomatis pada Kartu Mutasi Stok PT NKP.</span>
                </div>
                <a href="index.php?page=mutations" class="btn btn-outline-secondary btn-sm fw-semibold">
                    <span>Lihat Kartu Stok Mutasi</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
