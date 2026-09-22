<?php
$pageTitle = 'Terbitkan PO dari PR: ' . $pr['pr_number'];
require __DIR__ . '/../layouts/header.php';
use App\Helpers\FormatHelper;
?>

<div class="row justify-content-center mb-5">
    <div class="col-xl-10 col-lg-11">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="index.php?page=po" class="text-decoration-none text-muted">Purchase Order (PO)</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Penerbitan PO Resmi</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <div class="mb-2">
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1 rounded-pill small">
                        <span class="d-inline-block rounded-circle bg-info me-1" style="width: 7px; height: 7px;"></span>
                        TAHAP 3 DARI 4 • PENERBITAN KONTRAK PESANAN PO
                    </span>
                </div>
                <h1 class="h3 fw-bold text-dark mb-1">Penerbitan Dokumen Purchase Order (PO)</h1>
                <p class="text-muted small mb-0">
                    Langkah 3: Mengonversi pengajuan <span class="font-monospace fw-bold text-dark"><?= htmlspecialchars($pr['pr_number']) ?></span> menjadi kontrak pesanan resmi ke supplier.
                </p>
            </div>
        </div>

        <form action="index.php?page=po-store" method="POST">
            <input type="hidden" name="pr_id" value="<?= $pr['id'] ?>">

            <!-- CARD 1: INFORMASI VENDOR & PENGIRIMAN -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold text-dark pb-3 mb-3 border-bottom d-flex align-items-center gap-2">
                        <i data-lucide="truck" class="text-primary" style="width: 18px; height: 18px;"></i>
                        <span>Informasi Vendor Rekanan & Batas Pengiriman</span>
                    </h2>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Pilih Supplier / Vendor Rekanan <span class="text-danger">*</span></label>
                            <select name="supplier_id" required class="form-select">
                                <option value="">-- Pilih Rekanan Vendor Resmi PT NKP --</option>
                                <?php foreach ($suppliers as $sup): ?>
                                    <option value="<?= $sup['id'] ?>">
                                        [<?= $sup['supplier_code'] ?>] <?= htmlspecialchars($sup['company_name']) ?> (<?= htmlspecialchars($sup['payment_term']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Batas Waktu Pengiriman (Lead Time Deadline) <span class="text-danger">*</span></label>
                            <input type="date" name="delivery_deadline" required value="<?= $pr['target_date'] ?>" min="<?= date('Y-m-d') ?>" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Pajak Pertambahan Nilai (PPN %)</label>
                            <input type="number" id="tax_percent" name="tax_percent" value="11" step="0.5" oninput="calculateTotals()" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold text-secondary">Catatan Pengiriman ke Supplier</label>
                            <input type="text" name="notes" placeholder="Contoh: Kirim ke Dock Gudang Cileungsi. Lampirkan sertifikat mutu." class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 2: RINCIAN ITEM & HARGA KESEPAKATAN -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold text-dark pb-3 mb-3 border-bottom d-flex align-items-center gap-2">
                        <i data-lucide="calculator" class="text-primary" style="width: 18px; height: 18px;"></i>
                        <span>Penetapan Harga Satuan Barang</span>
                    </h2>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary text-uppercase small">
                                <tr>
                                    <th class="py-2.5 px-3">Kode & Nama Suku Cadang</th>
                                    <th class="py-2.5 px-3 text-center" style="width: 130px;">Kuantitas Pesan *</th>
                                    <th class="py-2.5 px-3 text-end" style="width: 190px;">Harga Satuan (IDR) *</th>
                                    <th class="py-2.5 px-3 text-end" style="width: 190px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prItems as $idx => $it): ?>
                                <tr class="item-calc-row">
                                    <td class="py-3 px-3">
                                        <input type="hidden" name="item_id[]" value="<?= $it['item_id'] ?>">
                                        <div class="font-monospace fw-bold text-dark"><?= htmlspecialchars($it['item_code']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($it['item_name']) ?></div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <input type="number" name="qty_ordered[]" required min="1" value="<?= $it['qty_requested'] ?>"
                                               oninput="calculateTotals()"
                                               class="qty-input form-control form-control-sm text-center fw-bold">
                                    </td>
                                    <td class="py-3 px-3">
                                        <input type="number" name="unit_price[]" required min="0" step="500" value="<?= $it['unit_price'] ?>"
                                               oninput="calculateTotals()"
                                               class="price-input form-control form-control-sm text-end font-monospace fw-semibold">
                                    </td>
                                    <td class="py-3 px-3 text-end font-monospace fw-bold text-dark row-subtotal">
                                        Rp 0
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- TOTAL SUMMARY BOX -->
                    <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                        <div style="width: 320px;" class="small">
                            <div class="d-flex justify-content-between text-muted mb-2">
                                <span>Subtotal Barang:</span>
                                <span id="displaySubtotal" class="font-monospace fw-bold text-dark">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted mb-2">
                                <span>PPN (<span id="taxLabel">11</span>%):</span>
                                <span id="displayTax" class="font-monospace fw-bold text-dark">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between fs-6 fw-bold text-dark pt-2 border-top">
                                <span>Grand Total PO:</span>
                                <span id="displayGrandTotal" class="font-monospace text-primary">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit & Batal -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-2">
                <a href="index.php?page=po" class="btn btn-outline-secondary px-4">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary px-4 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                    <i data-lucide="check-circle-2" style="width: 18px; height: 18px;"></i>
                    <span>Terbitkan Dokumen PO Resmi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function formatRupiah(num) {
    return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function calculateTotals() {
    const rows = document.querySelectorAll('.item-calc-row');
    let subtotal = 0;

    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const rowTotal = qty * price;
        row.querySelector('.row-subtotal').innerText = formatRupiah(rowTotal);
        subtotal += rowTotal;
    });

    const taxPercent = parseFloat(document.getElementById('tax_percent').value) || 0;
    const taxAmount = (subtotal * taxPercent) / 100;
    const grandTotal = subtotal + taxAmount;

    const taxLabel = document.getElementById('taxLabel');
    if (taxLabel) {
        taxLabel.innerText = taxPercent;
    }

    document.getElementById('displaySubtotal').innerText = formatRupiah(subtotal);
    document.getElementById('displayTax').innerText = formatRupiah(taxAmount);
    document.getElementById('displayGrandTotal').innerText = formatRupiah(grandTotal);
}

document.addEventListener('DOMContentLoaded', calculateTotals);
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
