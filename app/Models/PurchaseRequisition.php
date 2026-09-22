<?php
/**
 * PurchaseRequisition Model (PR / Permintaan Pembelian)
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Models;

use App\Config\Database;
use PDO;

class PurchaseRequisition
{
    /**
     * Ambil seluruh PR dengan relasi nama pemohon dan approver
     */
    public static function getAll(?int $userId = null, ?string $status = null): array
    {
        $db = Database::getConnection();
        $sql = "
            SELECT pr.*, 
                   u.name as requester_name, u.department as requester_dept,
                   app.name as approver_name,
                   (SELECT COUNT(*) FROM pr_items WHERE pr_id = pr.id) as total_items,
                   (SELECT SUM(qty_requested) FROM pr_items WHERE pr_id = pr.id) as total_qty,
                   (SELECT GROUP_CONCAT(CONCAT(i.name, ' (', pi.qty_requested, ' ', i.unit, ')') SEPARATOR ', ')
                    FROM pr_items pi
                    JOIN items i ON pi.item_id = i.id
                    WHERE pi.pr_id = pr.id) as item_summary
            FROM purchase_requisitions pr
            JOIN users u ON pr.user_id = u.id
            LEFT JOIN users app ON pr.approved_by = app.id
            WHERE 1=1
        ";
        $params = [];

        if ($userId !== null) {
            $sql .= " AND pr.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        if (!empty($status)) {
            $sql .= " AND pr.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY pr.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ambil PR yang menunggu persetujuan supervisor
     */
    public static function getPendingApprovals(): array
    {
        return self::getAll(null, 'Pending');
    }

    /**
     * Hitung total PR yang pending
     */
    public static function countPending(): int
    {
        $db = Database::getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM purchase_requisitions WHERE status = 'Pending'")->fetchColumn();
    }

    /**
     * Cari PR berdasarkan ID
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT pr.*, 
                   u.name as requester_name, u.department as requester_dept,
                   app.name as approver_name
            FROM purchase_requisitions pr
            JOIN users u ON pr.user_id = u.id
            LEFT JOIN users app ON pr.approved_by = app.id
            WHERE pr.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $pr = $stmt->fetch();
        return $pr ?: null;
    }

    /**
     * Ambil daftar rincian barang dari suatu PR
     */
    public static function getItems(int $prId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT pri.*, i.item_code, i.name as item_name, i.unit, i.stock as current_stock, i.min_stock, i.unit_price
            FROM pr_items pri
            JOIN items i ON pri.item_id = i.id
            WHERE pri.pr_id = :pr_id
            ORDER BY pri.id ASC
        ");
        $stmt->execute(['pr_id' => $prId]);
        return $stmt->fetchAll();
    }

    /**
     * Generate nomor dokumen PR otomatis (PR/NKP/YYYY/MM/XXXX)
     */
    public static function generateNumber(): string
    {
        $db = Database::getConnection();
        $yearMonth = date('Y/m');
        $prefix = "PR/NKP/{$yearMonth}/";

        $stmt = $db->prepare("SELECT pr_number FROM purchase_requisitions WHERE pr_number LIKE :prefix ORDER BY id DESC LIMIT 1");
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
     * Buat pengajuan PR baru beserta item-itemnya dalam satu transaksi
     */
    public static function create(array $prData, array $items): int
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $prNumber = self::generateNumber();
            $stmt = $db->prepare("
                INSERT INTO purchase_requisitions (pr_number, user_id, pr_date, target_date, priority, status, general_notes)
                VALUES (:pr_number, :user_id, :pr_date, :target_date, :priority, 'Pending', :general_notes)
            ");
            $stmt->execute([
                'pr_number'     => $prNumber,
                'user_id'       => $prData['user_id'],
                'pr_date'       => $prData['pr_date'] ?? date('Y-m-d'),
                'target_date'   => $prData['target_date'],
                'priority'      => $prData['priority'] ?? 'Normal',
                'general_notes' => $prData['general_notes'] ?? null,
            ]);

            $prId = (int)$db->lastInsertId();

            $stmtItem = $db->prepare("
                INSERT INTO pr_items (pr_id, item_id, qty_requested, remarks)
                VALUES (:pr_id, :item_id, :qty_requested, :remarks)
            ");

            foreach ($items as $item) {
                if (!empty($item['item_id']) && !empty($item['qty']) && (int)$item['qty'] > 0) {
                    $stmtItem->execute([
                        'pr_id'         => $prId,
                        'item_id'       => (int)$item['item_id'],
                        'qty_requested' => (int)$item['qty'],
                        'remarks'       => $item['remarks'] ?? null,
                    ]);
                }
            }

            $db->commit();
            return $prId;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Setujui PR oleh Supervisor
     */
    public static function approve(int $id, int $approverId): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE purchase_requisitions 
            SET status = 'Approved', approved_by = :approver_id, rejection_notes = NULL 
            WHERE id = :id AND status = 'Pending'
        ");
        return $stmt->execute([
            'id'          => $id,
            'approver_id' => $approverId,
        ]);
    }

    /**
     * Tolak PR oleh Supervisor
     */
    public static function reject(int $id, int $approverId, string $notes): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE purchase_requisitions 
            SET status = 'Rejected', approved_by = :approver_id, rejection_notes = :notes 
            WHERE id = :id AND status = 'Pending'
        ");
        return $stmt->execute([
            'id'          => $id,
            'approver_id' => $approverId,
            'notes'       => $notes,
        ]);
    }

    /**
     * Update status PR setelah PO diterbitkan
     */
    public static function markAsPOCreated(int $id): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE purchase_requisitions SET status = 'PO Issued' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Ambil PR yang berstatus Approved dan siap diproses menjadi PO
     */
    public static function getApprovedPRs(): array
    {
        return self::getAll(null, 'Approved');
    }
}
