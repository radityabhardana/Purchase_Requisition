<div class="d-flex flex-column gap-4">
    <!-- Header Halaman & Tombol Tambah -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small fw-semibold">
                    <i data-lucide="shield" style="width: 13px; height: 13px;" class="me-1"></i> Khusus Administrator IT
                </span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-1">Manajemen Akun Pengguna & Karyawan</h1>
            <p class="text-muted small mb-0">Kelola data login personal, hak akses operasional (RBAC), dan kata sandi karyawan pabrik PT NKP.</p>
        </div>
        <div>
            <button type="button" onclick="openAddModal()" 
                    class="btn btn-warning text-dark fw-bold d-inline-flex align-items-center gap-2 shadow-sm">
                <i data-lucide="user-plus" style="width: 16px; height: 16px;"></i>
                <span>Tambah Akun Karyawan</span>
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik Role Karyawan -->
    <?php
    $roleCounts = [
        'admin' => 0,
        'supervisor' => 0,
        'purchasing' => 0,
        'warehouse' => 0,
        'requester' => 0
    ];
    foreach ($users as $u) {
        if (isset($roleCounts[$u['role']])) {
            $roleCounts[$u['role']]++;
        }
    }
    ?>
    <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-5">
        <div class="col">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="text-muted small fw-medium">Total Akun</div>
                <div class="h4 fw-bold text-dark mb-0 mt-1"><?= count($users) ?></div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="text-primary small fw-medium">Purchasing</div>
                <div class="h4 fw-bold text-dark mb-0 mt-1"><?= $roleCounts['purchasing'] ?> <span class="fs-6 fw-normal text-muted">Staf</span></div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="text-warning small fw-medium">Warehouse</div>
                <div class="h4 fw-bold text-dark mb-0 mt-1"><?= $roleCounts['warehouse'] ?> <span class="fs-6 fw-normal text-muted">Staf</span></div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="text-success small fw-medium">Supervisor</div>
                <div class="h4 fw-bold text-dark mb-0 mt-1"><?= $roleCounts['supervisor'] ?> <span class="fs-6 fw-normal text-muted">SPV</span></div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="text-info small fw-medium">Teknisi / User</div>
                <div class="h4 fw-bold text-dark mb-0 mt-1"><?= $roleCounts['requester'] ?> <span class="fs-6 fw-normal text-muted">Staf</span></div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Akun Pengguna -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="users" class="text-secondary" style="width: 18px; height: 18px;"></i>
                <h2 class="h6 fw-bold text-dark mb-0">Daftar Akun Karyawan Terdaftar</h2>
            </div>
            <div>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle p-2 fw-medium">
                    <i data-lucide="info" style="width: 14px; height: 14px;" class="me-1"></i> Password disimpan teks biasa agar dapat dipantau langsung oleh Admin IT
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light text-secondary text-uppercase small">
                    <tr>
                        <th class="py-3 px-3">Nama Karyawan & Departemen</th>
                        <th class="py-3 px-3">Username (ID Login)</th>
                        <th class="py-3 px-3">Password Akun</th>
                        <th class="py-3 px-3">Role / Hak Akses</th>
                        <th class="py-3 px-3">Terdaftar</th>
                        <th class="py-3 px-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="py-5 text-center text-muted">
                                Tidak ada data pengguna.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <!-- Nama & Departemen -->
                                <td class="py-3 px-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-dark text-warning fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px; font-size: 11px;">
                                            <?= strtoupper(substr($u['name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($u['name']) ?></div>
                                            <div class="text-muted small"><?= htmlspecialchars($u['department']) ?></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Username -->
                                <td class="py-3 px-3">
                                    <span class="font-monospace fw-semibold text-dark bg-light px-2 py-1 rounded border">
                                        <?= htmlspecialchars($u['username']) ?>
                                    </span>
                                </td>

                                <!-- Password (Bisa diintip / dilihat langsung) -->
                                <td class="py-3 px-3">
                                    <div class="d-inline-flex align-items-center gap-2 bg-light px-2.5 py-1 rounded border">
                                        <span id="passText_<?= $u['id'] ?>" class="fw-bold text-secondary font-monospace" style="letter-spacing: 0.08em; font-size: 0.85rem;">
                                            ••••••••
                                        </span>
                                        <button type="button" 
                                                onclick="toggleTableRowPass(<?= $u['id'] ?>, '<?= htmlspecialchars($u['password'], ENT_QUOTES) ?>')"
                                                class="btn btn-link p-0 text-muted hover-dark"
                                                title="Lihat / Sembunyikan Password">
                                            <i id="passIcon_<?= $u['id'] ?>" data-lucide="eye" style="width: 15px; height: 15px;"></i>
                                        </button>
                                    </div>
                                </td>

                                <!-- Role -->
                                <td class="py-3 px-3">
                                    <?php
                                    $roleBadge = match($u['role']) {
                                        'admin'      => 'bg-dark text-warning border-dark',
                                        'supervisor' => 'bg-success-subtle text-success-emphasis border-success-subtle',
                                        'purchasing' => 'bg-primary-subtle text-primary-emphasis border-primary-subtle',
                                        'warehouse'  => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                        default      => 'bg-secondary-subtle text-secondary-emphasis border-secondary-subtle',
                                    };
                                    $roleLabel = match($u['role']) {
                                        'admin'      => 'Admin IT',
                                        'supervisor' => 'Supervisor (Approval)',
                                        'purchasing' => 'Purchasing',
                                        'warehouse'  => 'Warehouse (Gudang)',
                                        default      => 'Teknisi / Requester',
                                    };
                                    ?>
                                    <span class="badge rounded-pill border <?= $roleBadge ?> px-2.5 py-1">
                                        <?= $roleLabel ?>
                                    </span>
                                </td>

                                <!-- Tanggal -->
                                <td class="py-3 px-3 text-muted">
                                    <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                                </td>

                                <!-- Aksi -->
                                <td class="py-3 px-3 text-end">
                                    <div class="btn-group">
                                        <button type="button" 
                                                onclick='openEditModal(<?= json_encode($u) ?>)'
                                                class="btn btn-sm btn-outline-secondary"
                                                title="Edit Akun">
                                            <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                        </button>

                                        <?php if ((int)$u['id'] !== (int)($_SESSION['user']['id'] ?? 0)): ?>
                                            <form action="index.php?page=user-delete" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun karyawan ini?');">
                                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Hapus Akun">
                                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border ms-1 py-1">Akun Anda</span>
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
</div>

<!-- ================= MODAL TAMBAH PENGGUNA (BOOTSTRAP 5) ================= -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-warning-subtle text-warning-emphasis p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i>
                    </div>
                    <h5 class="modal-title h6 fw-bold text-dark mb-0" id="addModalLabel">Tambah Akun Karyawan Baru</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="index.php?page=user-store" method="POST">
                <div class="modal-body small p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Nama Lengkap Karyawan <span class="text-danger">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Doni Hermawan, S.T." class="form-control">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary">Username (ID) <span class="text-danger">*</span></label>
                            <input type="text" name="username" required placeholder="Contoh: doni" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary">Password <span class="text-danger">*</span></label>
                            <input type="text" name="password" required placeholder="Contoh: password123" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Role / Hak Akses Operasional <span class="text-danger">*</span></label>
                        <select name="role" required class="form-select">
                            <option value="">-- Pilih Hak Akses --</option>
                            <option value="purchasing">Purchasing (Terbitkan PO & Konversi PR)</option>
                            <option value="warehouse">Warehouse (Penerimaan Barang GR & Stok)</option>
                            <option value="supervisor">Supervisor (Approval & Review PR)</option>
                            <option value="requester">Teknisi / Karyawan (Pengajuan PR)</option>
                            <option value="admin">Administrator IT (Akses Penuh Sistem)</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary">Departemen / Bagian <span class="text-danger">*</span></label>
                        <input type="text" name="department" required placeholder="Contoh: Procurement & Purchasing" class="form-control">
                    </div>
                </div>

                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold px-3 shadow-sm">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT PENGGUNA (BOOTSTRAP 5) ================= -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i data-lucide="edit-3" style="width: 18px; height: 18px;"></i>
                    </div>
                    <h5 class="modal-title h6 fw-bold text-dark mb-0" id="editModalLabel">Edit Akun Karyawan</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="index.php?page=user-update" method="POST">
                <input type="hidden" id="edit_id" name="id">

                <div class="modal-body small p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Nama Lengkap Karyawan <span class="text-danger">*</span></label>
                        <input type="text" id="edit_name" name="name" required class="form-control">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary">Username / NIK <span class="text-danger">*</span></label>
                            <input type="text" id="edit_username" name="username" required class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary">Password Baru (Teks)</label>
                            <input type="text" id="edit_password" name="password" placeholder="Kosongkan jika tak diubah" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Role / Hak Akses <span class="text-danger">*</span></label>
                        <select id="edit_role" name="role" required class="form-select">
                            <option value="purchasing">Purchasing (Terbitkan PO & Konversi PR)</option>
                            <option value="warehouse">Warehouse (Penerimaan Barang GR & Stok)</option>
                            <option value="supervisor">Supervisor (Approval & Review PR)</option>
                            <option value="requester">Teknisi / Karyawan (Pengajuan PR)</option>
                            <option value="admin">Administrator IT (Akses Penuh Sistem)</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary">Departemen / Bagian <span class="text-danger">*</span></label>
                        <input type="text" id="edit_department" name="department" required class="form-control">
                    </div>
                </div>

                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold px-3 shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let addModalInstance, editModalInstance;

    document.addEventListener('DOMContentLoaded', function() {
        const addEl = document.getElementById('addModal');
        const editEl = document.getElementById('editModal');
        if (addEl && typeof bootstrap !== 'undefined') {
            addModalInstance = new bootstrap.Modal(addEl);
        }
        if (editEl && typeof bootstrap !== 'undefined') {
            editModalInstance = new bootstrap.Modal(editEl);
        }
    });

    function openAddModal() {
        if (addModalInstance) {
            addModalInstance.show();
        } else {
            const m = new bootstrap.Modal(document.getElementById('addModal'));
            m.show();
        }
    }

    function openEditModal(userData) {
        document.getElementById('edit_id').value = userData.id;
        document.getElementById('edit_name').value = userData.name;
        document.getElementById('edit_username').value = userData.username;
        document.getElementById('edit_password').value = userData.password || '';
        document.getElementById('edit_role').value = userData.role;
        document.getElementById('edit_department').value = userData.department;

        if (editModalInstance) {
            editModalInstance.show();
        } else {
            const m = new bootstrap.Modal(document.getElementById('editModal'));
            m.show();
        }
    }

    // Toggle lihat password di baris tabel
    function toggleTableRowPass(userId, actualPass) {
        const textEl = document.getElementById('passText_' + userId);
        const iconEl = document.getElementById('passIcon_' + userId);
        if (!textEl) return;

        if (textEl.textContent.trim() === '••••••••') {
            textEl.textContent = actualPass;
            textEl.classList.remove('text-secondary');
            textEl.classList.add('text-warning-emphasis', 'fw-bold');
            if (iconEl) iconEl.setAttribute('data-lucide', 'eye-off');
        } else {
            textEl.textContent = '••••••••';
            textEl.classList.remove('text-warning-emphasis', 'fw-bold');
            textEl.classList.add('text-secondary');
            if (iconEl) iconEl.setAttribute('data-lucide', 'eye');
        }
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
</script>
