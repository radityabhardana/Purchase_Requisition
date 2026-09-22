<?php
$pageTitle = 'Buat Pengajuan PR';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid" style="max-width: 960px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="index.php?page=pr" class="text-decoration-none text-secondary">Permintaan Pembelian (PR)</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Formulir Pengajuan Baru</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 small fw-bold">
                    <span class="badge bg-warning p-1 rounded-circle me-1"> </span>TAHAP 1 DARI 4 • PENGISIAN KEBUTUHAN BARANG
                </span>
            </div>
            <h1 class="h4 fw-bold text-dark mb-1">Formulir Purchase Requisition (PR)</h1>
            <p class="text-secondary small mb-0">Langkah 1: Isi rincian kebutuhan suku cadang atau material untuk diteruskan ke Supervisor.</p>
        </div>
    </div>

    <form action="index.php?page=pr-store" method="POST">
        <!-- CARD 1: INFORMASI HEADER PR -->
        <div class="card border shadow-sm mb-4">
            <div class="card-header py-2.5 px-3 bg-white border-bottom">
                <h2 class="h6 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="info" class="text-warning" style="width: 18px; height: 18px;"></i>
                    <span>Informasi Pengajuan & Urgensi</span>
                </h2>
            </div>
            <div class="card-body p-3 p-sm-4 small">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-secondary mb-1">Target Tanggal Dibutuhkan di Pabrik *</label>
                        <input type="date" name="target_date" required min="<?= date('Y-m-d') ?>"
                               class="form-control form-control-sm">
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">SOP PT NKP: Kebutuhan normal minimal 7 hari kalender sebelum pemasangan.</div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold text-secondary mb-1">Tingkat Urgensi Pengadaan *</label>
                        <select name="priority" required class="form-select form-select-sm fw-bold">
                            <option value="Normal">Normal - Kebutuhan Rutin / Persediaan Berkala</option>
                            <option value="Urgent">Urgent - Stok Kritis / Mesin Berisiko Trouble</option>
                            <option value="Emergency">Emergency - Mesin Breakdown / Line Stop</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-secondary mb-1">Keterangan / Alasan Kebutuhan Barang</label>
                        <textarea name="general_notes" rows="2" placeholder="Tuliskan alasan pemakaian (misal: Perbaikan dies bodi motor, penggantian berkala seal hidrolik mesin press...)"
                                  class="form-control form-control-sm"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 2: RINCIAN BARANG YANG DIMINTA -->
        <div class="card border shadow-sm mb-4">
            <div class="card-header py-2.5 px-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h2 class="h6 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="boxes" class="text-warning" style="width: 18px; height: 18px;"></i>
                    <span>Daftar Suku Cadang / Material yang Diminta</span>
                </h2>
                <button type="button" onclick="addRow()"
                        class="btn btn-sm btn-dark fw-bold d-inline-flex align-items-center gap-1">
                    <i data-lucide="plus" style="width: 14px; height: 14px;" class="text-warning"></i>
                    <span class="text-warning">Tambah Baris</span>
                </button>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="itemsTable">
                        <thead>
                            <tr class="bg-light text-secondary small">
                                <th>Pilih Barang dari Katalog *</th>
                                <th style="width: 120px;" class="text-center">Jumlah *</th>
                                <th>Catatan Khusus (No Mesin / Part No)</th>
                                <th style="width: 50px;" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- Baris Pertama -->
                            <tr class="item-row">
                                <td class="p-2">
                                    <select name="item_id[]" required class="form-select form-select-sm">
                                        <option value="">-- Pilih Barang --</option>
                                        <?php foreach ($items as $it): ?>
                                            <option value="<?= $it['id'] ?>" <?= ($autoSelectItemId === $it['id']) ? 'selected' : '' ?>>
                                                [<?= $it['item_code'] ?>] <?= htmlspecialchars($it['name']) ?> (Sisa: <?= $it['stock'] ?> <?= $it['unit'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="p-2">
                                    <input type="number" name="qty[]" required min="1" value="1"
                                           class="form-control form-control-sm text-center fw-bold">
                                </td>
                                <td class="p-2">
                                    <input type="text" name="remarks[]" placeholder="Catatan kegunaan..."
                                           class="form-control form-control-sm">
                                </td>
                                <td class="p-2 text-center">
                                    <button type="button" onclick="removeRow(this)" class="btn btn-sm btn-outline-danger p-1" title="Hapus Baris">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tombol Submit & Batal -->
        <div class="d-flex justify-content-end align-items-center gap-2 mb-5">
            <a href="index.php?page=pr" class="btn btn-outline-secondary px-4 fw-semibold">
                Batal
            </a>
            <button type="submit"
                    class="btn btn-warning fw-bold px-4 d-inline-flex align-items-center gap-2 shadow-sm" style="background-color: var(--nkp-amber-500); border: none; color: #020617;">
                <i data-lucide="send" style="width: 16px; height: 16px;"></i>
                <span>Kirim Pengajuan PR</span>
            </button>
        </div>
    </form>
</div>

<!-- Template Script Tambah Baris -->
<script>
const itemsOptions = `<?php foreach ($items as $it): ?><option value="<?= $it['id'] ?>">[<?= $it['item_code'] ?>] <?= addslashes(htmlspecialchars($it['name'])) ?> (Sisa: <?= $it['stock'] ?> <?= $it['unit'] ?>)</option><?php endforeach; ?>`;

function addRow() {
    const tbody = document.getElementById('itemsTableBody');
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
        <td class="p-2">
            <select name="item_id[]" required class="form-select form-select-sm">
                <option value="">-- Pilih Barang --</option>
                ${itemsOptions}
            </select>
        </td>
        <td class="p-2">
            <input type="number" name="qty[]" required min="1" value="1"
                   class="form-control form-control-sm text-center fw-bold">
        </td>
        <td class="p-2">
            <input type="text" name="remarks[]" placeholder="Catatan kegunaan..."
                   class="form-control form-control-sm">
        </td>
        <td class="p-2 text-center">
            <button type="button" onclick="removeRow(this)" class="btn btn-sm btn-outline-danger p-1" title="Hapus Baris">
                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        btn.closest('tr').remove();
    } else {
        alert('Pengajuan wajib memiliki minimal satu baris barang.');
    }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
