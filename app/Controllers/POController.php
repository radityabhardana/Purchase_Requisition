<?php
/**
 * POController - Penerbitan Purchase Order & Cetak Dokumen A4 Standar Industri
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\Supplier;

class POController
{
    /**
     * Daftar dokumen PO dan PR yang siap diproses
     */
    public function index(): void
    {
        AuthHelper::requireRole(['purchasing', 'admin']);

        $status = trim($_GET['status'] ?? '');
        $pos = PurchaseOrder::getAll($status ?: null);
        $approvedPRs = PurchaseRequisition::getApprovedPRs();

        require __DIR__ . '/../../views/po/index.php';
    }

    /**
     * Tampilkan form pembuatan PO dari PR yang sudah di-approve
     */
    public function create(): void
    {
        AuthHelper::requireRole(['purchasing', 'admin']);

        $prId = (int)($_GET['pr_id'] ?? 0);
        $pr = PurchaseRequisition::findById($prId);

        if (!$pr || $pr['status'] !== 'Approved') {
            AuthHelper::setFlash('error', 'Silakan pilih dokumen PR yang telah berstatus Approved.');
            header('Location: index.php?page=po');
            exit;
        }

        $prItems = PurchaseRequisition::getItems($prId);
        $suppliers = Supplier::getActive();

        require __DIR__ . '/../../views/po/create.php';
    }

    /**
     * Simpan penerbitan Purchase Order baru
     */
    public function store(): void
    {
        AuthHelper::requireRole(['purchasing', 'admin']);
        $user = AuthHelper::user();

        $prId = (int)($_POST['pr_id'] ?? 0);
        $supplierId = (int)($_POST['supplier_id'] ?? 0);
        $deliveryDeadline = trim($_POST['delivery_deadline'] ?? '');
        $taxPercent = (float)($_POST['tax_percent'] ?? 11.00);
        $notes = trim($_POST['notes'] ?? '');

        $itemIds = $_POST['item_id'] ?? [];
        $quantities = $_POST['qty_ordered'] ?? [];
        $prices = $_POST['unit_price'] ?? [];

        if (empty($prId) || empty($supplierId) || empty($deliveryDeadline) || empty($itemIds)) {
            AuthHelper::setFlash('error', 'Semua kolom bertanda bintang wajib diisi.');
            header("Location: index.php?page=po-create&pr_id={$prId}");
            exit;
        }

        $items = [];
        for ($i = 0; $i < count($itemIds); $i++) {
            if (!empty($itemIds[$i])) {
                $items[] = [
                    'item_id'     => (int)$itemIds[$i],
                    'qty_ordered' => (int)($quantities[$i] ?? 1),
                    'unit_price'  => (float)($prices[$i] ?? 0),
                ];
            }
        }

        try {
            $poId = PurchaseOrder::create([
                'pr_id'             => $prId,
                'supplier_id'       => $supplierId,
                'created_by'        => $user['id'],
                'delivery_deadline' => $deliveryDeadline,
                'tax_percent'       => $taxPercent,
                'notes'             => $notes,
            ], $items);

            AuthHelper::setFlash('success', 'Purchase Order resmi berhasil diterbitkan!');
            header("Location: index.php?page=po-print&id={$poId}");
            exit;
        } catch (\Exception $e) {
            AuthHelper::setFlash('error', 'Gagal menerbitkan PO: ' . $e->getMessage());
            header("Location: index.php?page=po-create&pr_id={$prId}");
            exit;
        }
    }

    /**
     * Tampilan cetak resmi dokumen PO format A4 standar ISO PT NKP
     */
    public function printView(): void
    {
        AuthHelper::requireRole(['purchasing', 'admin']);

        $id = (int)($_GET['id'] ?? 0);
        $po = PurchaseOrder::findById($id);

        if (!$po) {
            AuthHelper::setFlash('error', 'Dokumen PO tidak ditemukan.');
            header('Location: index.php?page=po');
            exit;
        }

        $items = PurchaseOrder::getItems($id);

        require __DIR__ . '/../../views/po/print.php';
    }
}
