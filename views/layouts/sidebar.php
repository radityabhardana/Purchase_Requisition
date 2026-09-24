<?php
use App\Helpers\AuthHelper;
use App\Models\PurchaseRequisition;
use App\Models\Item;
use App\Models\DeliveryNote;

$currentPage = $_GET['page'] ?? 'dashboard';
$user = AuthHelper::user();
$role = $user['role'] ?? 'requester';

$pendingPRCount = PurchaseRequisition::countPending();
$criticalStockCount = Item::countCritical();
$activeDNCount = DeliveryNote::countActive();

// Helper untuk merender list item navigasi
$renderNavLinks = function() use ($currentPage, $role, $pendingPRCount, $criticalStockCount, $activeDNCount) {
?>
    <!-- Dashboard Utama -->
    <a href="index.php?page=dashboard" 
       class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
        <i data-lucide="layout-dashboard" class="me-2" style="width: 17px; height: 17px;"></i>
        <span class="text-truncate">Dashboard Utama</span>
    </a>

    <!-- GRUP 1: PENGADAAN (PURCHASING) -->
    <div class="nkp-sidebar-heading">
        Pengadaan (Purchasing)
    </div>

    <!-- Permintaan Pembelian (PR) -->
    <a href="index.php?page=pr" 
       class="nav-link justify-content-between <?= in_array($currentPage, ['pr', 'pr-create', 'pr-detail']) ? 'active' : '' ?>">
        <div class="d-flex align-items-center text-truncate">
            <i data-lucide="clipboard-list" class="me-2" style="width: 17px; height: 17px;"></i>
            <span class="text-truncate">Permintaan Pembelian (PR)</span>
        </div>
        <?php if ($pendingPRCount > 0 && in_array($role, ['supervisor', 'admin'])): ?>
            <span class="badge rounded-pill <?= in_array($currentPage, ['pr', 'pr-create', 'pr-detail']) ? 'bg-dark text-warning' : 'badge-sidebar-amber' ?>" style="font-size: 0.65rem;" title="<?= $pendingPRCount ?> PR Menunggu Persetujuan">
                <?= $pendingPRCount ?> Antre
            </span>
        <?php endif; ?>
    </a>

    <!-- Purchase Order (PO) -->
    <?php if (in_array($role, ['purchasing', 'admin'])): ?>
        <a href="index.php?page=po" 
           class="nav-link <?= in_array($currentPage, ['po', 'po-create', 'po-print']) ? 'active' : '' ?>">
            <i data-lucide="shopping-cart" class="me-2" style="width: 17px; height: 17px;"></i>
            <span class="text-truncate">Purchase Order (PO)</span>
        </a>
    <?php endif; ?>

    <!-- GRUP 2: OPERASIONAL GUDANG & LOGISTIK -->
    <div class="nkp-sidebar-heading">
        Gudang & Logistik
    </div>

    <!-- Penerimaan Barang (GR) - Inbound -->
    <?php if (in_array($role, ['warehouse', 'admin'])): ?>
        <a href="index.php?page=gr" 
           class="nav-link <?= in_array($currentPage, ['gr', 'gr-create']) ? 'active' : '' ?>">
            <i data-lucide="package-check" class="me-2" style="width: 17px; height: 17px;"></i>
            <span class="text-truncate">Penerimaan Barang (GR)</span>
        </a>
    <?php endif; ?>

    <!-- Surat Jalan (SJ) - Outbound -->
    <a href="index.php?page=delivery-notes" 
       class="nav-link justify-content-between <?= in_array($currentPage, ['delivery-notes', 'delivery-note-create', 'delivery-note-detail', 'delivery-note-print']) ? 'active' : '' ?>">
        <div class="d-flex align-items-center text-truncate">
            <i data-lucide="truck" class="me-2" style="width: 17px; height: 17px;"></i>
            <span class="text-truncate">Surat Jalan (SJ)</span>
        </div>
        <?php if ($activeDNCount > 0): ?>
            <span class="badge rounded-pill <?= in_array($currentPage, ['delivery-notes', 'delivery-note-create']) ? 'bg-dark text-warning' : 'badge-sidebar-neutral' ?>" style="font-size: 0.65rem;">
                <?= $activeDNCount ?> Kirim
            </span>
        <?php endif; ?>
    </a>

    <!-- Kartu Mutasi Stok -->
    <a href="index.php?page=mutations" 
       class="nav-link <?= $currentPage === 'mutations' ? 'active' : '' ?>">
        <i data-lucide="arrow-left-right" class="me-2" style="width: 17px; height: 17px;"></i>
        <span class="text-truncate">Kartu Mutasi Stok</span>
    </a>

    <!-- GRUP 3: KATALOG & MASTER DATA -->
    <div class="nkp-sidebar-heading">
        Katalog & Master Data
    </div>

    <!-- Master Stok Barang -->
    <a href="index.php?page=items" 
       class="nav-link justify-content-between <?= $currentPage === 'items' ? 'active' : '' ?>">
        <div class="d-flex align-items-center text-truncate">
            <i data-lucide="boxes" class="me-2" style="width: 17px; height: 17px;"></i>
            <span class="text-truncate">Katalog & Stok</span>
        </div>
        <?php if ($criticalStockCount > 0): ?>
            <span class="badge rounded-pill <?= $currentPage === 'items' ? 'bg-dark text-warning' : 'badge-sidebar-crimson' ?>" style="font-size: 0.65rem;">
                <?= $criticalStockCount ?> Kritis
            </span>
        <?php endif; ?>
    </a>

    <!-- Master Supplier (Purchasing, Admin) -->
    <?php if (in_array($role, ['purchasing', 'admin'])): ?>
        <a href="index.php?page=suppliers" 
           class="nav-link <?= $currentPage === 'suppliers' ? 'active' : '' ?>">
            <i data-lucide="building-2" class="me-2" style="width: 17px; height: 17px;"></i>
            <span class="text-truncate">Data Supplier</span>
        </a>
    <?php endif; ?>

    <!-- GRUP 4: ADMINISTRASI SISTEM -->
    <?php if ($role === 'admin'): ?>
        <div class="nkp-sidebar-heading">
            Administrasi
        </div>
        <a href="index.php?page=users" 
           class="nav-link <?= in_array($currentPage, ['users']) ? 'active' : '' ?>">
            <i data-lucide="users" class="me-2" style="width: 17px; height: 17px;"></i>
            <span class="text-truncate">Manajemen User</span>
        </a>
    <?php endif; ?>
<?php
};
?>

<!-- 1. SIDEBAR DESKTOP (STICKY) -->
<aside id="sidebarDesktop" class="d-none d-lg-flex no-print">
    <div>
        <!-- Brand Header PT NKP -->
        <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom border-dark" style="background-color: var(--nkp-navy-950);">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-white rounded-2 p-1 d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;">
                    <img src="logo-nkp-kecil-2.png" 
                         onerror="this.onerror=null; this.src='images/logo-nkp-kecil-2.png';" 
                         alt="Logo PT NKP" 
                         style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
                <div>
                    <div class="fw-bold text-white text-sm tracking-wide">
                        SIP-NKP
                    </div>
                    <div class="text-secondary fw-semibold text-uppercase" style="font-size: 0.65rem;">PT Nandya Karya Perkasa</div>
                </div>
            </div>
        </div>

        <!-- Navigasi Menu Vertikal -->
        <nav class="p-3 nkp-sidebar-nav nav flex-column">
            <?php $renderNavLinks(); ?>
        </nav>
    </div>

    <!-- User Profile Footer Card di Sidebar -->
    <div class="p-3 border-top border-dark" style="background-color: var(--nkp-navy-950);">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-2 bg-dark text-warning d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 34px; height: 34px; font-size: 0.85rem;">
                <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="overflow-hidden">
                <div class="text-white fw-semibold small text-truncate"><?= htmlspecialchars($user['name'] ?? '') ?></div>
                <div class="text-warning small text-capitalize" style="font-size: 0.72rem;"><?= htmlspecialchars($user['role'] ?? '') ?></div>
            </div>
        </div>
    </div>
</aside>

<!-- 2. MOBILE OFFCANVAS SIDEBAR (BOOTSTRAP 5) -->
<div class="offcanvas offcanvas-start d-lg-none text-light no-print" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel" style="background-color: var(--nkp-navy-900); width: 280px;">
    <div class="offcanvas-header border-bottom border-dark" style="background-color: var(--nkp-navy-950);">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-white rounded-2 p-1 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                <img src="logo-nkp-kecil-2.png" 
                     onerror="this.onerror=null; this.src='images/logo-nkp-kecil-2.png';" 
                     alt="Logo PT NKP" 
                     style="max-width: 100%; max-height: 100%; object-fit: contain;">
            </div>
            <div>
                <div class="fw-bold text-white text-sm tracking-wide">
                    SIP-NKP
                </div>
                <div class="text-secondary small text-uppercase" style="font-size: 0.6rem;">PT Nandya Karya Perkasa</div>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3 nkp-sidebar-nav nav flex-column">
        <?php $renderNavLinks(); ?>
    </div>
    <div class="p-3 border-top border-dark" style="background-color: var(--nkp-navy-950);">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-2 bg-dark text-warning d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 0.8rem;">
                <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="overflow-hidden">
                <div class="text-white fw-semibold small text-truncate"><?= htmlspecialchars($user['name'] ?? '') ?></div>
                <div class="text-warning small text-capitalize" style="font-size: 0.7rem;"><?= htmlspecialchars($user['role'] ?? '') ?></div>
            </div>
        </div>
    </div>
</div>
