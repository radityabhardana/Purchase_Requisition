<?php
$pageTitle = 'Master Supplier & Vendor';
require __DIR__ . '/../layouts/header.php';
use App\Helpers\AuthHelper;

$user = AuthHelper::user();
$canManage = in_array($user['role'], ['admin', 'purchasing']);
?>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border px-2.5 py-1 small fw-bold">
                <i data-lucide="truck" class="me-1 text-muted" style="width: 14px; height: 14px;"></i>DATA MASTER • REKANAN VENDOR
            </span>
        </div>
        <h1 class="h4 fw-bold text-dark mb-1">Master Vendor & Supplier Rekanan</h1>
        <p class="text-secondary small mb-0">Daftar mitra pemasok bahan baku, suku cadang, dan perlengkapan PT Nandya Karya Perkasa.</p>
    </div>

    <?php if ($canManage): ?>
    <button type="button" class="btn btn-warning btn-sm fw-bold px-3 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm"
            data-bs-toggle="modal" data-bs-target="#modalAddSupplier"
            style="background-color: var(--nkp-amber-500); border: none; color: #020617;">
        <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
        <span>Tambah Vendor Baru</span>
    </button>
    <?php endif; ?>
</div>

<!-- TABEL SUPPLIERS -->
<div class="card border shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Kode Vendor</th>
                    <th>Nama Perusahaan</th>
                    <th>Kontak Person</th>
                    <th>Telepon / WhatsApp</th>
                    <th>Email Penawaran</th>
                    <th>Alamat Pabrik / Kantor</th>
                    <th class="text-center">Termin Bayar (TOP)</th>
                    <th class="text-center">Status</th>
                    <?php if ($canManage): ?>
                    <th class="text-end">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($suppliers)): ?>
                <tr>
                    <td colspan="9" class="py-5 text-center text-muted small">Belum ada data supplier rekanan.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($suppliers as $s): ?>
                <tr>
                    <td class="font-monospace fw-bold text-dark"><?= htmlspecialchars($s['supplier_code']) ?></td>
                    <td class="fw-semibold text-dark">
                        <?= htmlspecialchars($s['company_name']) ?>
                    </td>
                    <td class="text-secondary small"><?= htmlspecialchars($s['contact_person']) ?></td>
                    <td class="font-monospace small text-dark"><?= htmlspecialchars($s['phone']) ?></td>
                    <td class="small text-muted"><?= htmlspecialchars($s['email']) ?></td>
                    <td class="small text-secondary text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($s['address']) ?>">
                        <?= htmlspecialchars($s['address']) ?>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-secondary border font-monospace">
                            <?= htmlspecialchars($s['payment_term']) ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <?php if ($s['is_active']): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                Aktif
                            </span>
                        <?php else: ?>
                            <span class="badge bg-light text-muted border px-2 py-1">
                                Nonaktif
                            </span>
                        <?php endif; ?>
                    </td>
                    <?php if ($canManage): ?>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" onclick='openEditSupplierModal(<?= json_encode($s) ?>)' class="btn btn-outline-primary" title="Edit Data Vendor">
                                <i data-lucide="pencil" style="width: 14px; height: 14px;"></i>
                            </button>
                            <?php if ($user['role'] === 'admin'): ?>
                            <form action="index.php?page=supplier-delete" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus vendor <?= htmlspecialchars(addslashes($s['company_name'])) ?>?');">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <button type="submit" class="btn btn-outline-danger" title="Hapus Vendor">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH SUPPLIER (BOOTSTRAP 5) -->
<?php if ($canManage): ?>
<div class="modal fade" id="modalAddSupplier" tabindex="-1" aria-labelledby="modalAddSupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title h6 fw-bold d-flex align-items-center gap-2" id="modalAddSupplierLabel">
                    <i data-lucide="truck" class="text-warning" style="width: 18px; height: 18px;"></i>
                    <span>Tambah Vendor Rekanan Baru</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=supplier-store" method="POST">
                <div class="modal-body p-4 small">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Kode Vendor *</label>
                            <input type="text" name="supplier_code" required placeholder="Contoh: VND-005"
                                   class="form-control form-control-sm font-monospace">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Termin Pembayaran (TOP) *</label>
                            <select name="payment_term" required class="form-select form-select-sm">
                                <option value="Net 30">Net 30 (30 Hari)</option>
                                <option value="Net 60">Net 60 (60 Hari)</option>
                                <option value="COD">COD (Cash on Delivery)</option>
                                <option value="Advance">Advance (DP di Muka)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary mb-1">Nama Perusahaan (PT / CV) *</label>
                            <input type="text" name="company_name" required placeholder="Contoh: PT Surya Metalindo Presisi"
                                   class="form-control form-control-sm">
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Kontak Person (Sales/PIC) *</label>
                            <input type="text" name="contact_person" required placeholder="Nama lengkap..."
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Nomor Telepon / WA *</label>
                            <input type="text" name="phone" required placeholder="021-xxxxxx atau 0812xxxx"
                                   class="form-control form-control-sm font-monospace">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary mb-1">Alamat Email Resmi</label>
                            <input type="email" name="email" placeholder="sales@perusahaan.com"
                                   class="form-control form-control-sm">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary mb-1">Alamat Kantor / Fasilitas Pabrik</label>
                            <textarea name="address" rows="2" placeholder="Jalan, Kawasan Industri, Kota..."
                                      class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-warning fw-bold shadow-sm" style="background-color: var(--nkp-amber-500); border: none; color: #020617;">Simpan Vendor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT SUPPLIER (BOOTSTRAP 5) -->
<div class="modal fade" id="modalEditSupplier" tabindex="-1" aria-labelledby="modalEditSupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title h6 fw-bold d-flex align-items-center gap-2" id="modalEditSupplierLabel">
                    <i data-lucide="pencil" class="text-warning" style="width: 18px; height: 18px;"></i>
                    <span>Edit Data Vendor Rekanan</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?page=supplier-update" method="POST">
                <input type="hidden" name="id" id="edit_sup_id">
                <div class="modal-body p-4 small">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Kode Vendor *</label>
                            <input type="text" name="supplier_code" id="edit_sup_code" required
                                   class="form-control form-control-sm font-monospace">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Termin Pembayaran *</label>
                            <select name="payment_term" id="edit_sup_payment_term" required class="form-select form-select-sm">
                                <option value="Net 30">Net 30 (30 Hari)</option>
                                <option value="Net 60">Net 60 (60 Hari)</option>
                                <option value="COD">COD (Cash on Delivery)</option>
                                <option value="Advance">Advance (DP di Muka)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary mb-1">Nama Perusahaan *</label>
                            <input type="text" name="company_name" id="edit_sup_name" required
                                   class="form-control form-control-sm">
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Kontak Person *</label>
                            <input type="text" name="contact_person" id="edit_sup_contact" required
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Telepon / WA *</label>
                            <input type="text" name="phone" id="edit_sup_phone" required
                                   class="form-control form-control-sm font-monospace">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary mb-1">Email</label>
                            <input type="email" name="email" id="edit_sup_email"
                                   class="form-control form-control-sm">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary mb-1">Alamat Kantor / Pabrik</label>
                            <textarea name="address" id="edit_sup_address" rows="2"
                                      class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-warning fw-bold shadow-sm" style="background-color: var(--nkp-amber-500); border: none; color: #020617;">Perbarui Vendor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditSupplierModal(sup) {
    document.getElementById('edit_sup_id').value = sup.id;
    document.getElementById('edit_sup_code').value = sup.supplier_code;
    document.getElementById('edit_sup_name').value = sup.company_name;
    document.getElementById('edit_sup_contact').value = sup.contact_person;
    document.getElementById('edit_sup_phone').value = sup.phone;
    document.getElementById('edit_sup_email').value = sup.email;
    document.getElementById('edit_sup_address').value = sup.address;
    document.getElementById('edit_sup_payment_term').value = sup.payment_term;
    
    const editModal = new bootstrap.Modal(document.getElementById('modalEditSupplier'));
    editModal.show();
}
</script>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
