<?php
/**
 * PurchaseOrder Model (PO / Surat Pesanan Pembelian)
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Models;

use App\Config\Database;
use PDO;

class PurchaseOrder
{
    /**
     * Ambil seluruh data PO beserta relasi vendor, pembuat, dan PR
     */
    public static function getAll(?string $status = null): array
    {
        $db = Database::getConnection();
        $sql = "
            SELECT po.*, 
                   s.company_name as supplier_name, s.supplier_code, s.contact_person, s.phone as supplier_phone,
                   u.name as creator_name,
                   pr.pr_number,
                   (SELECT COUNT(*) FROM po_items WHERE po_id = po.id) as total_items,
                   (SELECT SUM(qty_ordered) FROM po_items WHERE po_id = po.id) as total_qty,
                   (SELECT GROUP_CONCAT(CONCAT(i.name, ' (', poi.qty_ordered, ' ', i.unit, ')') SEPARATOR ', ')
                    FROM po_items poi
                    JOIN items i ON poi.item_id = i.id
                    WHERE poi.po_id = po.id) as item_summary
            FROM purchase_orders po
            JOIN suppliers s ON po.supplier_id = s.id
            JOIN users u ON po.created_by = u.id
            LEFT JOIN purchase_requisitions pr ON po.pr_id = pr.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($status)) {
            $sql .= " AND po.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY po.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ambil PO yang masih aktif (dalam proses pengiriman / belum komplit)
     */
    public static function countActive(): int
    {
        $db = Database::getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM purchase_orders WHERE status IN ('Issued', 'Partial Received')")->fetchColumn();
    }

    /**
     * Total pengeluaran pengadaan (Grand Total seluruh PO selain Cancelled)
     */
    public static function totalSpend(): float
    {
        $db = Database::getConnection();
        return (float)$db->query("SELECT COALESCE(SUM(grand_total), 0) FROM purchase_orders WHERE status != 'Cancelled'")->fetchColumn();
    }

    /**
     * Cari PO berdasarkan ID
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT po.*, 
                   s.company_name as supplier_name, s.supplier_code, s.contact_person, s.phone as supplier_phone, s.email as supplier_email, s.address as supplier_address, s.payment_term as supplier_payment_term,
                   u.name as creator_name, u.department as creator_dept,
                   pr.pr_number, pr.general_notes as pr_notes,
                   req.name as requester_name
            FROM purchase_orders po
            JOIN suppliers s ON po.supplier_id = s.id
            JOIN users u ON po.created_by = u.id
            LEFT JOIN purchase_requisitions pr ON po.pr_id = pr.id
            LEFT JOIN users req ON pr.user_id = req.id
            WHERE po.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $po = $stmt->fetch();
        return $po ?: null;
    }

    /**
     * Ambil daftar rincian barang dari suatu PO
     */
    public static function getItems(int $poId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT poi.*, i.item_code, i.name as item_name, i.unit, i.stock as current_stock, i.location_rack
            FROM po_items poi
            JOIN items i ON poi.item_id = i.id
            WHERE poi.po_id = :po_id
            ORDER BY poi.id ASC
        ");
        $stmt->execute(['po_id' => $poId]);
        return $stmt->fetchAll();
    }

    /**
     * Generate nomor dokumen PO resmi (PO/NKP/YYYY/MM/XXXX)
     */
    public static function generateNumber(): string
    {
        $db = Database::getConnection();
        $yearMonth = date('Y/m');
        $prefix = "PO/NKP/{$yearMonth}/";

        $stmt = $db->prepare("SELECT po_number FROM purchase_orders WHERE po_number LIKE :prefix ORDER BY id DESC LIMIT 1");
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
     * Buat dokumen PO resmi dari PR yang disetujui
     */
    public static function create(array $poData, array $items): int
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $poNumber = self::generateNumber();

            // Hitung subtotal
            $subtotal = 0;
            foreach ($items as $item) {
                $qty = (int)$item['qty_ordered'];
                $price = (float)$item['unit_price'];
                $subtotal += ($qty * $price);
            }

            $taxPercent = (float)($poData['tax_percent'] ?? 11.00);
            $taxAmount = ($subtotal * $taxPercent) / 100;
            $grandTotal = $subtotal + $taxAmount;

            $stmt = $db->prepare("
                INSERT INTO purchase_orders (po_number, pr_id, supplier_id, created_by, po_date, delivery_deadline, subtotal, tax_percent, tax_amount, grand_total, status, notes)
                VALUES (:po_number, :pr_id, :supplier_id, :created_by, :po_date, :delivery_deadline, :subtotal, :tax_percent, :tax_amount, :grand_total, 'Issued', :notes)
            ");
            $stmt->execute([
                'po_number'         => $poNumber,
                'pr_id'             => $poData['pr_id'],
                'supplier_id'       => $poData['supplier_id'],
                'created_by'        => $poData['created_by'],
                'po_date'           => $poData['po_date'] ?? date('Y-m-d'),
                'delivery_deadline' => $poData['delivery_deadline'],
                'subtotal'          => $subtotal,
                'tax_percent'       => $taxPercent,
                'tax_amount'        => $taxAmount,
                'grand_total'       => $grandTotal,
                'notes'             => $poData['notes'] ?? null,
            ]);

            $poId = (int)$db->lastInsertId();

            $stmtItem = $db->prepare("
                INSERT INTO po_items (po_id, item_id, qty_ordered, unit_price, subtotal)
                VALUES (:po_id, :item_id, :qty_ordered, :unit_price, :subtotal)
            ");

            foreach ($items as $item) {
                $qty = (int)$item['qty_ordered'];
                $price = (float)$item['unit_price'];
                $rowSubtotal = $qty * $price;

                $stmtItem->execute([
                    'po_id'       => $poId,
                    'item_id'     => (int)$item['item_id'],
                    'qty_ordered' => $qty,
                    'unit_price'  => $price,
                    'subtotal'    => $rowSubtotal,
                ]);
            }

            // Update status PR menjadi 'PO Issued'
            if (!empty($poData['pr_id'])) {
                PurchaseRequisition::markAsPOCreated((int)$poData['pr_id']);
            }

            $db->commit();
            return $poId;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Dapatkan daftar PO yang masih membuka penerimaan barang di gudang
     */
    public static function getOpenPOsForGR(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT po.*, s.company_name as supplier_name, s.supplier_code,
                   (SELECT GROUP_CONCAT(CONCAT(i.name, ' (', poi.qty_ordered, ' ', i.unit, ')') SEPARATOR ', ')
                    FROM po_items poi
                    JOIN items i ON poi.item_id = i.id
                    WHERE poi.po_id = po.id) as item_summary
            FROM purchase_orders po
            JOIN suppliers s ON po.supplier_id = s.id
            WHERE po.status IN ('Issued', 'Partial Received')
            ORDER BY po.id DESC
        ");
        return $stmt->fetchAll();
    }
}
