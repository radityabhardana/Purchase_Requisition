<?php
/**
 * DashboardController - Menampilkan Ringkasan Metrik KPI & Alert Stok Kritis
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\Item;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseOrder;
use App\Models\Supplier;

class DashboardController
{
    /**
     * Tampilkan halaman utama dashboard
     */
    public function index(): void
    {
        AuthHelper::requireLogin();

        // 1. Ambil data metrik KPI
        $kpi = [
            'pending_pr'     => PurchaseRequisition::countPending(),
            'active_po'      => PurchaseOrder::countActive(),
            'critical_stock' => Item::countCritical(),
            'total_spend'    => PurchaseOrder::totalSpend(),
            'total_items'    => Item::countTotal(),
            'total_vendors'  => Supplier::count(),
        ];

        // 2. Ambil daftar barang dengan stok kritis (Safety Stock Alert)
        $criticalItems = Item::getCriticalStock();

        // 3. Ambil aktivitas terbaru
        $user = AuthHelper::user();
        $userId = ($user['role'] ?? '') === 'requester' ? (int)$user['id'] : null;
        $recentPRs = array_slice(PurchaseRequisition::getAll($userId), 0, 5);
        $recentPOs = array_slice(PurchaseOrder::getAll(), 0, 5);

        require __DIR__ . '/../../views/dashboard/index.php';
    }
}
