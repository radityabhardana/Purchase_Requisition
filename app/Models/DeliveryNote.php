<?php
/**
 * DeliveryNote Model (Surat Jalan & Pengeluaran Stok Gudang)
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;
use Exception;

class DeliveryNote
{
    /**
     * Ambil seluruh riwayat dokumen Surat Jalan
     */
    public static function getAll(?string $status = null, ?string $search = null): array
    {
        $db = Database::getConnection();
        $sql = "
            SELECT dn.*, 
                   u.name as creator_name, u.department as creator_dept,
                   (SELECT COUNT(*) FROM delivery_note_items WHERE delivery_note_id = dn.id) as total_items,
                   (SELECT COALESCE(SUM(qty_shipped), 0) FROM delivery_note_items WHERE delivery_note_id = dn.id) as total_qty
            FROM delivery_notes dn
            JOIN users u ON dn.created_by = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($status)) {
            $sql .= " AND dn.status = :status";
            $params['status'] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (dn.sj_number LIKE :search OR dn.recipient_name LIKE :search OR dn.vehicle_no LIKE :search OR dn.driver_name LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $sql .= " ORDER BY dn.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Cari dokumen Surat Jalan berdasarkan ID
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT dn.*, 
                   u.name as creator_name, u.department as creator_dept, u.role as creator_role
            FROM delivery_notes dn
            JOIN users u ON dn.created_by = u.id
            WHERE dn.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $dn = $stmt->fetch();
        return $dn ?: null;
    }

    /**
     * Ambil rincian item dalam Surat Jalan
     */
    public static function getItems(int $deliveryNoteId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT dni.*, 
                   i.item_code, i.name as item_name, i.category, i.unit, i.stock as current_stock, i.location_rack
            FROM delivery_note_items dni
            JOIN items i ON dni.item_id = i.id
            WHERE dni.delivery_note_id = :dn_id
            ORDER BY dni.id ASC
        ");
        $stmt->execute(['dn_id' => $deliveryNoteId]);
        return $stmt->fetchAll();
    }

    /**
     * Generate nomor dokumen Surat Jalan resmi (SJ/NKP/YYYY/MM/XXXX)
     */
    public static function generateNumber(): string
    {
        $db = Database::getConnection();
        $yearMonth = date('Y/m');
        $prefix = "SJ/NKP/{$yearMonth}/";

        $stmt = $db->prepare("SELECT sj_number FROM delivery_notes WHERE sj_number LIKE :prefix ORDER BY id DESC LIMIT 1");
        $stmt->execute(['prefix' => "{$prefix}%"]);
        $last = $stmt->fetchColumn();

        if ($last) {
            $parts = explode('/', $last);
            $seq = (int)end($parts) + 1;
        } else {
            $seq = 1;
        }

        return sprintf('%s%04d', $prefix, $seq);
    }

    /**
     * TITIK KRITIS: Terbitkan Surat Jalan, Kurangi Stok Fisik Barang (Stock OUT) & Catat Mutasi
     * dalam satu Database Transaction (ACID)
     */
    public static function createWithTransaction(array $headerData, array $items): int
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $sjNumber = self::generateNumber();

            // 1. Simpan Header Dokumen Surat Jalan
            $stmtHeader = $db->prepare("
                INSERT INTO delivery_notes (
                    sj_number, created_by, recipient_type, recipient_name, recipient_address, 
                    customer_po_no, vehicle_no, driver_name, delivery_date, status, notes
                ) VALUES (
                    :sj_number, :created_by, :recipient_type, :recipient_name, :recipient_address, 
                    :customer_po_no, :vehicle_no, :driver_name, :delivery_date, :status, :notes
                )
            ");
            $stmtHeader->execute([
                'sj_number'         => $sjNumber,
                'created_by'        => (int)$headerData['created_by'],
                'recipient_type'    => $headerData['recipient_type'] ?? 'Customer',
                'recipient_name'    => $headerData['recipient_name'],
                'recipient_address' => $headerData['recipient_address'],
                'customer_po_no'    => !empty($headerData['customer_po_no']) ? $headerData['customer_po_no'] : null,
                'vehicle_no'        => $headerData['vehicle_no'],
                'driver_name'       => $headerData['driver_name'],
                'delivery_date'     => $headerData['delivery_date'] ?? date('Y-m-d'),
                'status'            => $headerData['status'] ?? 'Shipped',
                'notes'             => $headerData['notes'] ?? null,
            ]);

            $deliveryNoteId = (int)$db->lastInsertId();

            // Siapkan Prepared Statement queries untuk detail & stok
            $stmtItem = $db->prepare("
                INSERT INTO delivery_note_items (delivery_note_id, item_id, qty_shipped, packaging, remarks)
                VALUES (:dn_id, :item_id, :qty_shipped, :packaging, :remarks)
            ");

            $stmtCheckStock = $db->prepare("SELECT name, stock FROM items WHERE id = :item_id FOR UPDATE");

            $stmtDeductStock = $db->prepare("
                UPDATE items SET stock = stock - :qty WHERE id = :item_id
            ");

            $stmtMutation = $db->prepare("
                INSERT INTO stock_mutations (item_id, mutation_type, reference_no, qty_in, qty_out, balance, notes)
                VALUES (:item_id, 'OUT', :reference_no, 0, :qty_out, :balance, :notes)
            ");

            // 2. Loop setiap item yang dikirim
            foreach ($items as $item) {
                $itemId = (int)$item['item_id'];
                $qtyShipped = (int)$item['qty_shipped'];
                $packaging = !empty($item['packaging']) ? trim($item['packaging']) : 'Box / Pallet';
                $remarks = $item['remarks'] ?? null;

                if ($qtyShipped <= 0) {
                    continue;
                }

                // A. Validasi kecukupan stok fisik dengan lock row (FOR UPDATE)
                $stmtCheckStock->execute(['item_id' => $itemId]);
                $itemInfo = $stmtCheckStock->fetch();

                if (!$itemInfo) {
                    throw new Exception("Barang dengan ID {$itemId} tidak ditemukan dalam database.");
                }

                $availableStock = (int)$itemInfo['stock'];
                if ($availableStock < $qtyShipped) {
                    throw new Exception("Stok barang '{$itemInfo['name']}' tidak mencukupi. Tersedia: {$availableStock}, Diminta: {$qtyShipped}.");
                }

                // B. Simpan item ke rincian Surat Jalan
                $stmtItem->execute([
                    'dn_id'       => $deliveryNoteId,
                    'item_id'     => $itemId,
                    'qty_shipped' => $qtyShipped,
                    'packaging'   => $packaging,
                    'remarks'     => $remarks,
                ]);

                // C. Kurangi stok fisik di master items
                $stmtDeductStock->execute([
                    'qty'     => $qtyShipped,
                    'item_id' => $itemId,
                ]);

                // D. Hitung saldo baru dan catat ke kartu mutasi (Audit Trail OUT)
                $newBalance = $availableStock - $qtyShipped;
                $stmtMutation->execute([
                    'item_id'      => $itemId,
                    'reference_no' => $sjNumber,
                    'qty_out'      => $qtyShipped,
                    'balance'      => $newBalance,
                    'notes'        => "Pengiriman Surat Jalan ke " . $headerData['recipient_name'] . " (" . $headerData['vehicle_no'] . ")",
                ]);
            }

            $db->commit();
            return $deliveryNoteId;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Update status Surat Jalan (misal Shipped -> Delivered)
     */
    public static function updateStatus(int $id, string $status): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE delivery_notes SET status = :status WHERE id = :id");
        return $stmt->execute([
            'status' => $status,
            'id'     => $id,
        ]);
    }

    /**
     * Hitung jumlah Surat Jalan aktif / sedang dalam pengiriman
     */
    public static function countActive(): int
    {
        $db = Database::getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM delivery_notes WHERE status = 'Shipped'")->fetchColumn();
    }
}
