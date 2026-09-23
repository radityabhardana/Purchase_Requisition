<?php
/**
 * DeliveryNoteController - Modul Pengeluaran Barang & Penerbitan Surat Jalan
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\DeliveryNote;
use App\Models\Item;
use Exception;

class DeliveryNoteController
{
    /**
     * Tampilkan daftar riwayat dokumen Surat Jalan
     */
    public function index(): void
    {
        AuthHelper::requireLogin();

        $status = trim($_GET['status'] ?? '');
        $search = trim($_GET['search'] ?? '');

        $deliveryNotes = DeliveryNote::getAll($status ?: null, $search ?: null);
        $activeCount = DeliveryNote::countActive();

        require __DIR__ . '/../../views/delivery_notes/index.php';
    }

    /**
     * Tampilkan form pembuatan Surat Jalan baru
     */
    public function create(): void
    {
        AuthHelper::requireRole(['warehouse', 'admin']);

        // Ambil barang yang memiliki stok > 0 untuk dikirim
        $items = Item::getAll();
        $nextSjNumber = DeliveryNote::generateNumber();

        require __DIR__ . '/../../views/delivery_notes/create.php';
    }

    /**
     * Simpan proses penerbitan Surat Jalan & kurangi stok fisik gudang
     */
    public function store(): void
    {
        AuthHelper::requireRole(['warehouse', 'admin']);
        $user = AuthHelper::user();

        $recipientType = trim($_POST['recipient_type'] ?? 'Customer');
        $recipientName = trim($_POST['recipient_name'] ?? '');
        $recipientAddress = trim($_POST['recipient_address'] ?? '');
        $customerPoNo = trim($_POST['customer_po_no'] ?? '');
        $vehicleNo = trim($_POST['vehicle_no'] ?? '');
        $driverName = trim($_POST['driver_name'] ?? '');
        $deliveryDate = trim($_POST['delivery_date'] ?? date('Y-m-d'));
        $notes = trim($_POST['notes'] ?? '');

        $itemIds = $_POST['item_id'] ?? [];
        $quantities = $_POST['qty_shipped'] ?? [];
        $packagings = $_POST['packaging'] ?? [];
        $remarks = $_POST['remarks'] ?? [];

        if (empty($recipientName) || empty($recipientAddress) || empty($vehicleNo) || empty($driverName) || empty($itemIds)) {
            AuthHelper::setFlash('error', 'Semua kolom bertanda bintang (*) dan minimal satu barang wajib diisi.');
            header('Location: index.php?page=delivery-note-create');
            exit;
        }

        $items = [];
        for ($i = 0; $i < count($itemIds); $i++) {
            $itemId = (int)$itemIds[$i];
            $qty = (int)($quantities[$i] ?? 0);
            if ($itemId > 0 && $qty > 0) {
                $items[] = [
                    'item_id'     => $itemId,
                    'qty_shipped' => $qty,
                    'packaging'   => $packagings[$i] ?? 'Box / Pallet',
                    'remarks'     => $remarks[$i] ?? '',
                ];
            }
        }

        if (empty($items)) {
            AuthHelper::setFlash('error', 'Masukkan minimal satu barang dengan kuantitas pengiriman yang valid (> 0).');
            header('Location: index.php?page=delivery-note-create');
            exit;
        }

        try {
            $dnId = DeliveryNote::createWithTransaction([
                'created_by'        => $user['id'],
                'recipient_type'    => $recipientType,
                'recipient_name'    => $recipientName,
                'recipient_address' => $recipientAddress,
                'customer_po_no'    => $customerPoNo,
                'vehicle_no'        => $vehicleNo,
                'driver_name'       => $driverName,
                'delivery_date'     => $deliveryDate,
                'status'            => 'Shipped',
                'notes'             => $notes,
            ], $items);

            AuthHelper::setFlash('success', 'Surat Jalan berhasil diterbitkan! Stok inventaris telah otomatis terpotong (Stock OUT).');
            header("Location: index.php?page=delivery-note-print&id={$dnId}");
            exit;
        } catch (Exception $e) {
            AuthHelper::setFlash('error', 'Gagal menerbitkan Surat Jalan: ' . $e->getMessage());
            header('Location: index.php?page=delivery-note-create');
            exit;
        }
    }

    /**
     * Tampilkan detail dokumen Surat Jalan
     */
    public function detail(): void
    {
        AuthHelper::requireLogin();

        $id = (int)($_GET['id'] ?? 0);
        $dn = DeliveryNote::findById($id);

        if (!$dn) {
            AuthHelper::setFlash('error', 'Dokumen Surat Jalan tidak ditemukan.');
            header('Location: index.php?page=delivery-notes');
            exit;
        }

        $items = DeliveryNote::getItems($id);

        require __DIR__ . '/../../views/delivery_notes/detail.php';
    }

    /**
     * Konfirmasi penerimaan customer (Ubah status Shipped -> Delivered)
     */
    public function updateStatus(): void
    {
        AuthHelper::requireRole(['warehouse', 'admin']);

        $id = (int)($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? 'Delivered');

        $allowedStatuses = ['Shipped', 'Delivered', 'Cancelled'];
        if (!in_array($status, $allowedStatuses, true)) {
            AuthHelper::setFlash('error', 'Status Surat Jalan tidak valid.');
            header("Location: index.php?page=delivery-note-detail&id={$id}");
            exit;
        }

        DeliveryNote::updateStatus($id, $status);
        AuthHelper::setFlash('success', "Status Surat Jalan berhasil diperbarui menjadi {$status}.");
        header("Location: index.php?page=delivery-note-detail&id={$id}");
        exit;
    }

    /**
     * Tampilan cetak resmi dokumen Surat Jalan A4 standar ISO PT Nandya Karya Perkasa
     */
    public function printView(): void
    {
        AuthHelper::requireLogin();

        $id = (int)($_GET['id'] ?? 0);
        $dn = DeliveryNote::findById($id);

        if (!$dn) {
            AuthHelper::setFlash('error', 'Dokumen Surat Jalan tidak ditemukan.');
            header('Location: index.php?page=delivery-notes');
            exit;
        }

        $items = DeliveryNote::getItems($id);

        require __DIR__ . '/../../views/delivery_notes/print.php';
    }
}
