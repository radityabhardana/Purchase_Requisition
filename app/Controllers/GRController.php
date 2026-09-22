<?php
/**
 * GRController - Goods Receipt (Penerimaan Barang & Auto-Sync Stok Gudang)
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;

class GRController
{
    /**
     * Tampilkan riwayat penerimaan barang dan PO yang siap diterima
     */
    public function index(): void
    {
        AuthHelper::requireRole(['warehouse', 'admin']);

        $receipts = GoodsReceipt::getAll();
        $openPOs = PurchaseOrder::getOpenPOsForGR();

        require __DIR__ . '/../../views/gr/index.php';
    }

    /**
     * Tampilkan form penerimaan barang berdasarkan PO
     */
    public function create(): void
    {
        AuthHelper::requireRole(['warehouse', 'admin']);

        $poId = (int)($_GET['po_id'] ?? 0);
        $po = PurchaseOrder::findById($poId);

        if (!$po || in_array($po['status'], ['Completed', 'Cancelled'])) {
            AuthHelper::setFlash('error', 'Silakan pilih dokumen PO yang masih aktif dan menunggu pengiriman.');
            header('Location: index.php?page=gr');
            exit;
        }

        $items = PurchaseOrder::getItems($poId);

        require __DIR__ . '/../../views/gr/create.php';
    }

    /**
     * Simpan proses penerimaan barang fisik & eksekusi auto-update stok gudang
     */
    public function store(): void
    {
        AuthHelper::requireRole(['warehouse', 'admin']);
        $user = AuthHelper::user();

        $poId = (int)($_POST['po_id'] ?? 0);
        $deliveryNoteNo = trim($_POST['delivery_note_no'] ?? '');
        $receivedDate = trim($_POST['received_date'] ?? date('Y-m-d'));
        $notes = trim($_POST['notes'] ?? '');

        $itemIds = $_POST['item_id'] ?? [];
        $receivedQtys = $_POST['qty_received'] ?? [];
        $qcStatuses = $_POST['qc_status'] ?? [];
        $itemNotes = $_POST['item_notes'] ?? [];

        if (empty($poId) || empty($deliveryNoteNo) || empty($itemIds)) {
            AuthHelper::setFlash('error', 'Nomor Surat Jalan dan minimal satu barang wajib diverifikasi.');
            header("Location: index.php?page=gr-create&po_id={$poId}");
            exit;
        }

        $items = [];
        for ($i = 0; $i < count($itemIds); $i++) {
            if (!empty($itemIds[$i])) {
                $items[] = [
                    'item_id'      => (int)$itemIds[$i],
                    'qty_received' => (int)($receivedQtys[$i] ?? 0),
                    'qc_status'    => $qcStatuses[$i] ?? 'Passed',
                    'notes'        => $itemNotes[$i] ?? '',
                ];
            }
        }

        try {
            $grId = GoodsReceipt::createWithTransaction([
                'po_id'            => $poId,
                'received_by'      => $user['id'],
                'delivery_note_no' => $deliveryNoteNo,
                'received_date'    => $receivedDate,
                'status'           => 'Completed',
                'notes'            => $notes,
            ], $items);

            AuthHelper::setFlash('success', 'Penerimaan barang berhasil dikonfirmasi! Stok gudang telah bertambah secara otomatis.');
            header('Location: index.php?page=items');
            exit;
        } catch (\Exception $e) {
            AuthHelper::setFlash('error', 'Gagal memproses penerimaan: ' . $e->getMessage());
            header("Location: index.php?page=gr-create&po_id={$poId}");
            exit;
        }
    }
}
