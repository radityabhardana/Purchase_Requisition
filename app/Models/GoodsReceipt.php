<?php
/**
 * GoodsReceipt Model (Penerimaan Barang & Auto-Sync Stok Gudang)
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Models;

use App\Config\Database;
use PDO;

class GoodsReceipt
{
    /**
     * Ambil seluruh riwayat dokumen penerimaan barang
     */
    public static function getAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT gr.*, 
                   po.po_number, po.po_date,
                   s.company_name as supplier_name,
                   u.name as receiver_name,
                   (SELECT COUNT(*) FROM gr_items WHERE gr_id = gr.id) as total_items,
                   (SELECT SUM(qty_received) FROM gr_items WHERE gr_id = gr.id) as total_qty
            FROM goods_receipts gr
            JOIN purchase_orders po ON gr.po_id = po.id
            JOIN suppliers s ON po.supplier_id = s.id
            JOIN users u ON gr.received_by = u.id
            ORDER BY gr.id DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Cari dokumen GR berdasarkan ID
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT gr.*, 
                   po.po_number, po.po_date, po.grand_total,
                   s.company_name as supplier_name, s.supplier_code, s.phone as supplier_phone,
                   u.name as receiver_name, u.department as receiver_dept
            FROM goods_receipts gr
            JOIN purchase_orders po ON gr.po_id = po.id
            JOIN suppliers s ON po.supplier_id = s.id
            JOIN users u ON gr.received_by = u.id
            WHERE gr.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $gr = $stmt->fetch();
        return $gr ?: null;
    }

    /**
     * Ambil rincian item dalam dokumen GR
     */
    public static function getItems(int $grId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT gri.*, i.item_code, i.name as item_name, i.unit, i.location_rack
            FROM gr_items gri
            JOIN items i ON gri.item_id = i.id
            WHERE gri.gr_id = :gr_id
            ORDER BY gri.id ASC
        ");
        $stmt->execute(['gr_id' => $grId]);
        return $stmt->fetchAll();
    }

    /**
     * Generate nomor dokumen GR resmi (GR/NKP/YYYY/MM/XXXX)
     */
    public static function generateNumber(): string
    {
        $db = Database::getConnection();
        $yearMonth = date('Y/m');
        $prefix = "GR/NKP/{$yearMonth}/";

        $stmt = $db->prepare("SELECT gr_number FROM goods_receipts WHERE gr_number LIKE :prefix ORDER BY id DESC LIMIT 1");
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
     * TITIK KRITIS: Simpan Goods Receipt, Tambah Stok Barang & Catat Mutasi
     * dalam satu Database Transaction (ACID)
     */
    public static function createWithTransaction(array $grData, array $items): int
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $grNumber = self::generateNumber();
            $poId = (int)$grData['po_id'];

            // 1. Simpan Header Dokumen Goods Receipt
            $stmtGR = $db->prepare("
                INSERT INTO goods_receipts (gr_number, po_id, received_by, delivery_note_no, received_date, status, notes)
                VALUES (:gr_number, :po_id, :received_by, :delivery_note_no, :received_date, :status, :notes)
            ");
            $stmtGR->execute([
                'gr_number'        => $grNumber,
                'po_id'            => $poId,
                'received_by'      => $grData['received_by'],
                'delivery_note_no' => $grData['delivery_note_no'],
                'received_date'    => $grData['received_date'] ?? date('Y-m-d'),
                'status'           => $grData['status'] ?? 'Completed',
                'notes'            => $grData['notes'] ?? null,
            ]);

            $grId = (int)$db->lastInsertId();

            // Statement queries yang disiapkan di awal untuk efisiensi
            $stmtItem = $db->prepare("
                INSERT INTO gr_items (gr_id, item_id, qty_received, qc_status, notes)
                VALUES (:gr_id, :item_id, :qty_received, :qc_status, :notes)
            ");

            $stmtUpdateStock = $db->prepare("
                UPDATE items SET stock = stock + :qty WHERE id = :item_id
            ");

            $stmtGetStock = $db->prepare("
                SELECT stock FROM items WHERE id = :item_id LIMIT 1
            ");

            $stmtMutation = $db->prepare("
                INSERT INTO stock_mutations (item_id, mutation_type, reference_no, qty_in, qty_out, balance, notes)
                VALUES (:item_id, 'IN', :reference_no, :qty_in, 0, :balance, :notes)
            ");

            // 2. Loop setiap item yang diterima
            foreach ($items as $item) {
                $itemId = (int)$item['item_id'];
                $qtyReceived = (int)$item['qty_received'];
                $qcStatus = $item['qc_status'] ?? 'Passed';
                $itemNotes = $item['notes'] ?? null;

                if ($qtyReceived > 0) {
                    // A. Catat rincian GR
                    $stmtItem->execute([
                        'gr_id'        => $grId,
                        'item_id'      => $itemId,
                        'qty_received' => $qtyReceived,
                        'qc_status'    => $qcStatus,
                        'notes'        => $itemNotes,
                    ]);

                    // Hanya barang yang lolos QC (Passed) yang dimasukkan ke stok aktif
                    if ($qcStatus === 'Passed') {
                        // B. Tambah stok fisik di master barang
                        $stmtUpdateStock->execute([
                            'qty'     => $qtyReceived,
                            'item_id' => $itemId,
                        ]);

                        // C. Dapatkan saldo stok terkini setelah penambahan
                        $stmtGetStock->execute(['item_id' => $itemId]);
                        $newBalance = (int)$stmtGetStock->fetchColumn();

                        // D. Catat ke kartu stok mutasi (Audit Trail)
                        $stmtMutation->execute([
                            'item_id'      => $itemId,
                            'reference_no' => $grNumber,
                            'qty_in'       => $qtyReceived,
                            'balance'      => $newBalance,
                            'notes'        => "Penerimaan PO via Surat Jalan: " . $grData['delivery_note_no'],
                        ]);
                    }
                }
            }

            // 3. Perbarui Status PO menjadi 'Completed'
            $stmtUpdatePO = $db->prepare("UPDATE purchase_orders SET status = 'Completed' WHERE id = :po_id");
            $stmtUpdatePO->execute(['po_id' => $poId]);

            // Commit transaksi: Semua data tersimpan dengan aman
            $db->commit();
            return $grId;
        } catch (\Exception $e) {
            // Jika ada satu saja kueri yang gagal, batalkan seluruh perubahan
            $db->rollBack();
            throw $e;
        }
    }
}
