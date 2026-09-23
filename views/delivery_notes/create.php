<?php
$pageTitle = 'Terbitkan Surat Jalan Baru';
require __DIR__ . '/../layouts/header.php';
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;

$user = AuthHelper::user();
?>

<div class="mb-4">
    <a href="index.php?page=delivery-notes" class="text-secondary text-decoration-none small d-inline-flex align-items-center gap-1 mb-2 hover-primary">
        <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
        <span>Kembali ke Daftar Surat Jalan</span>
    </a>
    <div class="d-flex align-items-center gap-2 mb-1">
        <span class="badge bg-warning text-dark font-monospace fw-bold px-2 py-1">SURAT JALAN PENGELUARAN BARANG</span>
    </div>
    <h1 class="h3 fw-bold text-dark mb-1">Form Penerbitan Surat Jalan (Delivery Note)</h1>
    <p class="text-secondary small mb-0">Pengurangan stok fisik gudang (Stock OUT) dan pembuatan dokumen pengiriman resmi PT Nandya Karya Perkasa.</p>
</div>

<form action="index.php?page=delivery-note-store" method="POST" id="formDeliveryNote">
    <div class="row g-4">
        <!-- KOLOM KIRI: INFORMASI PENGIRIMAN & EKSPEDISI -->
        <div class="col-12 col-lg-5">
            <div class="card border shadow-sm h-100">
                <div class="card-header bg-dark text-white py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i data-lucide="truck" class="text-warning" style="width: 18px; height: 18px;"></i>
                        <span class="fw-bold small text-uppercase">Informasi Ekspedisi & Tujuan</span>
                    </div>
                    <span class="badge bg-secondary font-monospace" style="font-size: 0.7rem;"><?= htmlspecialchars($nextSjNumber) ?></span>
                </div>
                <div class="card-body p-3 p-sm-4">
                    <!-- Tanggal Pengiriman -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Tanggal Pengiriman <span class="text-danger">*</span></label>
                        <input type="date" name="delivery_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <!-- Tipe Penerima -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Kategori Penerima <span class="text-danger">*</span></label>
                        <select name="recipient_type" class="form-select form-select-sm" required>
                            <option value="Customer" selected>Customer / Pelanggan Industri</option>
                            <option value="Vendor/Subcont">Vendor Rekanan / Subkontraktor</option>
                            <option value="Internal Plant">Internal Antar-Plant / Lini Produksi</option>
                        </select>
                    </div>

                    <!-- Nama Tujuan / Perusahaan -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Nama Pelanggan / Tujuan <span class="text-danger">*</span></label>
                        <input type="text" name="recipient_name" class="form-control form-control-sm" 
                               placeholder="Contoh: PT Astra Honda Motor (Plant 3 Cikarang)" 
                               list="customerRecommendations" required>
                        <datalist id="customerRecommendations">
                            <option value="PT Astra Honda Motor (Plant 3 Cikarang)">
                            <option value="PT Astra Honda Motor (Plant 4 Karawang)">
                            <option value="PT Yamaha Indonesia Motor Mfg (YIMM Pulogadung)">
                            <option value="PT Showa Indonesia Manufacturing">
                            <option value="PT Musashi Auto Parts Indonesia">
                            <option value="Plant 2 Cileungsi - Divisi Welding & Assembly">
                        </datalist>
                    </div>

                    <!-- Alamat Pengiriman -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Alamat Lengkap Pengiriman <span class="text-danger">*</span></label>
                        <textarea name="recipient_address" rows="2" class="form-control form-control-sm" 
                                  placeholder="Kawasan Industri, Blok, Kota/Kabupaten, Kode Pos..." required>Kawasan Industri MM2100 Blok NN, Cikarang Barat, Bekasi 17520</textarea>
                    </div>

                    <!-- No PO Customer -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Nomor PO Customer (Jika Ada)</label>
                        <input type="text" name="customer_po_no" class="form-control form-control-sm font-monospace" 
                               placeholder="Contoh: PO-AHM-2026-X8812">
                    </div>

                    <div class="row g-2 mb-3">
                        <!-- Nomor Kendaraan / Plat Truk -->
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">No. Plat Kendaraan <span class="text-danger">*</span></label>
                            <input type="text" name="vehicle_no" class="form-control form-control-sm font-monospace text-uppercase" 
                                   placeholder="Contoh: B 9481 NKP" required>
                        </div>
                        <!-- Nama Supir -->
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Nama Pengemudi <span class="text-danger">*</span></label>
                            <input type="text" name="driver_name" class="form-control form-control-sm" 
                                   placeholder="Nama supir armada" required>
                        </div>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-dark">Catatan Khusus Pengiriman</label>
                        <textarea name="notes" rows="2" class="form-control form-control-sm" 
                                  placeholder="Instruksi penanganan, stempel receiving gate, dsb..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: RINCIAN DAFTAR BARANG YANG DIKIRIM -->
        <div class="col-12 col-lg-7">
            <div class="card border shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i data-lucide="boxes" class="text-primary" style="width: 18px; height: 18px;"></i>
                        <span class="fw-bold small text-uppercase text-dark">Rincian Barang yang Dikeluarkan</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-dark text-warning fw-bold d-inline-flex align-items-center gap-1" id="btnAddRow">
                        <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                        <span>Tambah Baris Part</span>
                    </button>
                </div>
                <div class="card-body p-3">
                    <div class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-3">
                        <i data-lucide="info" class="flex-shrink-0" style="width: 16px; height: 16px;"></i>
                        <span>Kuantitas yang dikirim akan langsung memotong stok fisik di gudang dan tercatat di <strong>Kartu Mutasi (Stock OUT)</strong>.</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle" id="tableItems">
                            <thead class="table-light text-secondary text-uppercase" style="font-size: 0.7rem;">
                                <tr>
                                    <th style="min-width: 220px;">Suku Cadang / Barang <span class="text-danger">*</span></th>
                                    <th style="width: 110px;" class="text-center">Qty Kirim <span class="text-danger">*</span></th>
                                    <th style="width: 130px;">Kemasan / Box</th>
                                    <th>Keterangan / Lot No</th>
                                    <th style="width: 40px;" class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody id="itemsContainer">
                                <!-- Baris Pertama Bawaan -->
                                <tr class="item-row">
                                    <td>
                                        <select name="item_id[]" class="form-select form-select-sm select-item" required onchange="handleItemChange(this)">
                                            <option value="" disabled selected>-- Pilih Barang --</option>
                                            <?php foreach ($items as $it): ?>
                                                <option value="<?= $it['id'] ?>" 
                                                        data-stock="<?= (int)$it['stock'] ?>"
                                                        data-unit="<?= htmlspecialchars($it['unit']) ?>"
                                                        data-rack="<?= htmlspecialchars($it['location_rack']) ?>"
                                                        <?= $it['stock'] <= 0 ? 'disabled' : '' ?>>
                                                    <?= htmlspecialchars($it['item_code']) ?> - <?= htmlspecialchars($it['name']) ?> 
                                                    (Stok: <?= $it['stock'] ?> <?= htmlspecialchars($it['unit']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="stock-info text-muted small mt-1 font-monospace" style="font-size: 0.7rem;">
                                            Pilih barang untuk melihat saldo rak gudang.
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="qty_shipped[]" min="1" class="form-control text-center input-qty" placeholder="0" required oninput="validateQty(this)">
                                            <span class="input-group-text unit-badge text-muted" style="font-size: 0.7rem;">Pcs</span>
                                        </div>
                                        <div class="qty-warning text-danger small mt-0.5" style="font-size: 0.65rem; display: none;">
                                            Melebihi stok!
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="packaging[]" class="form-control form-control-sm" placeholder="Box / Pallet" value="Box / Pallet">
                                    </td>
                                    <td>
                                        <input type="text" name="remarks[]" class="form-control form-control-sm" placeholder="Lot / Keterangan">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm p-1" onclick="removeRow(this)" title="Hapus Baris">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between">
                        <span class="text-muted small">
                            Petugas: <strong><?= htmlspecialchars($user['name']) ?></strong> (<?= ucfirst($user['role']) ?>)
                        </span>
                        <button type="submit" class="btn btn-warning fw-bold px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm" style="background-color: var(--nkp-amber-500); border: none; color: #020617;">
                            <i data-lucide="send" style="width: 16px; height: 16px;"></i>
                            <span>Konfirmasi & Terbitkan Surat Jalan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function handleItemChange(selectEl) {
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const row = selectEl.closest('.item-row');
        const stock = parseInt(selectedOption.getAttribute('data-stock') || '0', 10);
        const unit = selectedOption.getAttribute('data-unit') || 'Pcs';
        const rack = selectedOption.getAttribute('data-rack') || '-';

        const stockInfo = row.querySelector('.stock-info');
        const unitBadge = row.querySelector('.unit-badge');
        const inputQty = row.querySelector('.input-qty');

        if (selectedOption.value) {
            stockInfo.innerHTML = `<span class="badge ${stock <= 5 ? 'bg-danger' : 'bg-secondary'} me-1">Stok: ${stock} ${unit}</span> Rak: ${rack}`;
            unitBadge.textContent = unit;
            inputQty.max = stock;
            validateQty(inputQty);
        } else {
            stockInfo.textContent = 'Pilih barang untuk melihat saldo rak gudang.';
            unitBadge.textContent = 'Pcs';
            inputQty.removeAttribute('max');
        }
    }

    function validateQty(inputEl) {
        const row = inputEl.closest('.item-row');
        const select = row.querySelector('.select-item');
        const selectedOption = select.options[select.selectedIndex];
        const warningEl = row.querySelector('.qty-warning');
        
        if (!selectedOption || !selectedOption.value) return;

        const maxStock = parseInt(selectedOption.getAttribute('data-stock') || '0', 10);
        const enteredQty = parseInt(inputEl.value || '0', 10);

        if (enteredQty > maxStock) {
            inputEl.classList.add('is-invalid');
            warningEl.style.display = 'block';
            warningEl.textContent = `Maksimal: ${maxStock}`;
        } else {
            inputEl.classList.remove('is-invalid');
            warningEl.style.display = 'none';
        }
    }

    function removeRow(btn) {
        const tbody = document.getElementById('itemsContainer');
        if (tbody.querySelectorAll('.item-row').length > 1) {
            btn.closest('.item-row').remove();
        } else {
            alert('Minimal satu barang harus disertakan dalam Surat Jalan.');
        }
    }

    document.getElementById('btnAddRow').addEventListener('click', function() {
        const tbody = document.getElementById('itemsContainer');
        const firstRow = tbody.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);

        // Reset inputs
        newRow.querySelector('.select-item').selectedIndex = 0;
        newRow.querySelector('.stock-info').textContent = 'Pilih barang untuk melihat saldo rak gudang.';
        const inputQty = newRow.querySelector('.input-qty');
        inputQty.value = '';
        inputQty.classList.remove('is-invalid');
        newRow.querySelector('.unit-badge').textContent = 'Pcs';
        newRow.querySelector('.qty-warning').style.display = 'none';
        newRow.querySelectorAll('input[type="text"]').forEach(input => {
            if (input.name === 'packaging[]') input.value = 'Box / Pallet';
            else input.value = '';
        });

        tbody.appendChild(newRow);

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    document.getElementById('formDeliveryNote').addEventListener('submit', function(e) {
        const invalidInputs = document.querySelectorAll('.input-qty.is-invalid');
        if (invalidInputs.length > 0) {
            e.preventDefault();
            alert('Terdapat kuantitas barang yang melebihi saldo fisik gudang. Mohon periksa kembali.');
            invalidInputs[0].focus();
            return;
        }

        if (!confirm('Apakah Anda yakin ingin menerbitkan Surat Jalan ini? Stok inventaris akan langsung berkurang secara otomatis.')) {
            e.preventDefault();
        }
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
