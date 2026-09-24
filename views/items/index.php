<?php
$pageTitle = 'Katalog & Stok Barang';
require __DIR__ . '/../layouts/header.php';
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;

$user = AuthHelper::user();
$canManage = in_array($user['role'], ['admin', 'warehouse']);
$categories = ['Mechanical', 'Electrical', 'Raw Material', 'Consumables', 'Safety/APD'];
?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border px-2.5 py-1 small fw-bold">
                <i data-lucide="boxes" class="me-1 text-muted" style="width: 14px; height: 14px;"></i>DATA MASTER • INVENTARIS SUKU CADANG
            </span>
        </div>
        <h1 class="h4 fw-bold text-dark mb-1">Katalog & Manajemen Stok Barang</h1>
        <p class="text-secondary small mb-0">Daftar master suku cadang mesin, plat stamping, batas safety stock, dan lokasi rak gudang PT NKP.</p>
    </div>

    <?php if ($canManage): ?>
    <button type="button" class="btn btn-warning btn-sm fw-bold px-3 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm text-dark"
            data-bs-toggle="modal" data-bs-target="#modalAddItem" style="background-color: var(--nkp-amber-500); border: none;">
        <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
        <span>Tambah Barang Baru</span>
    </button>
    <?php endif; ?>
</div>

<!-- FILTER & SEARCH BAR -->
<div class="card border shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="index.php" method="GET" class="row g-2 align-items-center justify-content-between">
            <input type="hidden" name="page" value="items">
            
            <!-- Search Box -->
            <div class="col-12 col-md-5 col-lg-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-secondary">
                        <i data-lucide="search" style="width: 15px; height: 15px;"></i>
                    </span>
                    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                           placeholder="Cari kode part, nama, atau rak..."
                           class="form-control">
                    <?php if (!empty($_GET['search'])): ?>
                        <a href="index.php?page=items" class="btn btn-outline-secondary" title="Reset">
                            <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Filter Kategori Tabs -->
            <div class="col-12 col-md-7 col-lg-8">
                <div class="d-flex flex-wrap gap-1 justify-content-md-end">
                    <a href="index.php?page=items" 
                       class="btn btn-sm <?= empty($_GET['category']) ? 'btn-dark' : 'btn-light border text-secondary' ?> py-1 px-2.5 small">
                        Semua Kategori
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="index.php?page=items&category=<?= urlencode($cat) ?>"
                           class="btn btn-sm <?= ($_GET['category'] ?? '') === $cat ? 'btn-dark' : 'btn-light border text-secondary' ?> py-1 px-2.5 small">
                            <?= $cat ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- TABLE ITEMS -->
<div class="card border shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Kode Part</th>
                    <th>Nama Suku Cadang</th>
                    <th>Kategori</th>
                    <th class="text-center">Stok Fisik</th>
                    <th class="text-center">Safety Stock</th>
                    <th>Lokasi Rak</th>
                    <th class="text-end">Harga Satuan (Est)</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                <tr>
                    <td colspan="9" class="py-5 text-center text-muted small">Tidak ada data barang yang sesuai kriteria pencarian.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($items as $item): 
                    $isCritical = $item['stock'] <= $item['min_stock'];
                ?>
                <tr class="<?= $isCritical ? 'table-danger' : '' ?>">
                    <td class="font-monospace fw-bold text-dark"><?= htmlspecialchars($item['item_code']) ?></td>
                    <td class="fw-semibold text-dark">
                        <?= htmlspecialchars($item['name']) ?>
                    </td>
                    <td>
                        <span class="badge bg-light text-secondary border">
                            <?= htmlspecialchars($item['category']) ?>
                        </span>
                    </td>
                    <td class="text-center fw-bold <?= $isCritical ? 'text-danger' : 'text-dark' ?>">
                        <?= $item['stock'] ?> <span class="text-muted small fw-normal"><?= htmlspecialchars($item['unit']) ?></span>
                    </td>
                    <td class="text-center text-muted fw-semibold">
                        <?= $item['min_stock'] ?> <span class="small fw-normal"><?= htmlspecialchars($item['unit']) ?></span>
                    </td>
                    <td class="font-monospace text-secondary"><?= htmlspecialchars($item['location_rack']) ?></td>
                    <td class="text-end font-monospace"><?= FormatHelper::rupiah($item['unit_price']) ?></td>
                    <td class="text-center">
                        <?php if ($isCritical): ?>
                            <span class="badge bg-danger text-white px-2 py-1">Kritis (Order)</span>
                        <?php else: ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Aman</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="index.php?page=mutations&item_id=<?= $item['id'] ?>" class="btn btn-outline-secondary" title="Lihat Kartu Stok Mutasi">
                                <i data-lucide="history" style="width: 14px; height: 14px;"></i>
                            </a>
                            <a href="index.php?page=pr-create&item_id=<?= $item['id'] ?>" class="btn btn-outline-warning text-dark" title="Ajukan Pengadaan (PR)">
                                <i data-lucide="file-plus" style="width: 14px; height: 14px;"></i>
                            </a>
                            <?php if ($canManage): ?>
                            <button type="button" onclick="openEditModal(<?= htmlspecialchars(json_encode($item), ENT_QUOTES) ?>)" class="btn btn-outline-primary" title="Edit Data Barang">
                                <i data-lucide="pencil" style="width: 14px; height: 14px;"></i>
                            </button>
                            <?php if ($user['role'] === 'admin'): ?>
                            <form action="index.php?page=item-delete" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang <?= htmlspecialchars($item['name']) ?>?');">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button type="submit" class="btn btn-outline-danger" title="Hapus Barang">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH BARANG (BOOTSTRAP 5) -->
<?php if ($canManage): ?>
<div class="modal fade" id="modalAddItem" tabindex="-1" aria-labelledby="modalAddItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title h6 fw-bold d-flex align-items-center gap-2" id="modalAddItemLabel">
                    <i data-lucide="box" class="text-warning" style="width: 18px; height: 18px;"></i>
                    <span>Tambah Barang Baru ke Inventaris</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=item-store" method="POST">
                <div class="modal-body p-4 small">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Kode Part / SKU *</label>
                            <input type="text" name="item_code" required placeholder="Contoh: SPR-PRS-005"
                                   class="form-control form-control-sm font-monospace">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Kategori *</label>
                            <select name="category" required class="form-select form-select-sm">
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= $c ?>"><?= $c ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary mb-1">Nama Suku Cadang / Material *</label>
                            <input type="text" name="name" required placeholder="Contoh: Baut Hex Flange M8x25 Grade 8.8"
                                   class="form-control form-control-sm">
                        </div>

                        <div class="col-4">
                            <label class="form-label fw-semibold text-secondary mb-1">Satuan *</label>
                            <input type="text" name="unit" required value="Pcs" placeholder="Pcs/Box/Kg"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold text-secondary mb-1">Stok Awal</label>
                            <input type="number" name="stock" value="0" min="0"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold text-secondary mb-1">Safety Stock *</label>
                            <input type="number" name="min_stock" value="5" min="1"
                                   class="form-control form-control-sm">
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Lokasi Rak Gudang *</label>
                            <input type="text" name="location_rack" required placeholder="Contoh: Rak-B02-C"
                                   class="form-control form-control-sm font-monospace">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Estimasi Harga Satuan (IDR)</label>
                            <input type="number" name="unit_price" value="0" step="500"
                                   class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-warning fw-bold shadow-sm" style="background-color: var(--nkp-amber-500); border: none; color: #020617;">Simpan Barang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT BARANG (BOOTSTRAP 5) -->
<div class="modal fade" id="modalEditItem" tabindex="-1" aria-labelledby="modalEditItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title h6 fw-bold d-flex align-items-center gap-2" id="modalEditItemLabel">
                    <i data-lucide="pencil" class="text-warning" style="width: 18px; height: 18px;"></i>
                    <span>Edit Data Barang</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=item-update" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4 small">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Kode Part / SKU *</label>
                            <input type="text" name="item_code" id="edit_item_code" required
                                   class="form-control form-control-sm font-monospace">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Kategori *</label>
                            <select name="category" id="edit_category" required class="form-select form-select-sm">
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= $c ?>"><?= $c ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary mb-1">Nama Suku Cadang / Material *</label>
                            <input type="text" name="name" id="edit_name" required
                                   class="form-control form-control-sm">
                        </div>

                        <div class="col-4">
                            <label class="form-label fw-semibold text-secondary mb-1">Satuan *</label>
                            <input type="text" name="unit" id="edit_unit" required
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold text-secondary mb-1">Safety Stock *</label>
                            <input type="number" name="min_stock" id="edit_min_stock" required min="1"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold text-secondary mb-1">Lokasi Rak *</label>
                            <input type="text" name="location_rack" id="edit_location_rack" required
                                   class="form-control form-control-sm font-monospace">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary mb-1">Harga Satuan Standar (IDR)</label>
                            <input type="number" name="unit_price" id="edit_unit_price" step="500"
                                   class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-warning fw-bold shadow-sm" style="background-color: var(--nkp-amber-500); border: none; color: #020617;">Perbarui Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditModal(item) {
    document.getElementById('edit_id').value = item.id;
    document.getElementById('edit_item_code').value = item.item_code;
    document.getElementById('edit_name').value = item.name;
    document.getElementById('edit_category').value = item.category;
    document.getElementById('edit_unit').value = item.unit;
    document.getElementById('edit_min_stock').value = item.min_stock;
    document.getElementById('edit_location_rack').value = item.location_rack;
    document.getElementById('edit_unit_price').value = item.unit_price;
    
    const editModal = new bootstrap.Modal(document.getElementById('modalEditItem'));
    editModal.show();
}
</script>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
