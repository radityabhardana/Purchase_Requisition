<?php
/**
 * Item Model (Master Barang & Suku Cadang)
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Models;

use App\Config\Database;
use PDO;

class Item
{
    /**
     * Ambil seluruh data barang dengan opsi pencarian & filter kategori
     */
    public static function getAll(?string $search = null, ?string $category = null): array
    {
        $db = Database::getConnection();
        $sql = "SELECT * FROM items WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (item_code LIKE :search OR name LIKE :search OR location_rack LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($category)) {
            $sql .= " AND category = :category";
            $params['category'] = $category;
        }

        $sql .= " ORDER BY (stock <= min_stock) DESC, name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ambil hanya barang yang berada di bawah batas Safety Stock
     */
    public static function getCriticalStock(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM items WHERE stock <= min_stock ORDER BY stock ASC");
        return $stmt->fetchAll();
    }

    /**
     * Hitung jumlah barang dengan stok kritis
     */
    public static function countCritical(): int
    {
        $db = Database::getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM items WHERE stock <= min_stock")->fetchColumn();
    }

    /**
     * Hitung total jenis barang
     */
    public static function countTotal(): int
    {
        $db = Database::getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM items")->fetchColumn();
    }

    /**
     * Cari barang berdasarkan ID
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM items WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $item = $stmt->fetch();
        return $item ?: null;
    }

    /**
     * Tambah barang baru
     */
    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO items (item_code, name, category, unit, stock, min_stock, location_rack, unit_price)
            VALUES (:item_code, :name, :category, :unit, :stock, :min_stock, :location_rack, :unit_price)
        ");
        $stmt->execute([
            'item_code'     => $data['item_code'],
            'name'          => $data['name'],
            'category'      => $data['category'],
            'unit'          => $data['unit'] ?? 'Pcs',
            'stock'         => (int)($data['stock'] ?? 0),
            'min_stock'     => (int)($data['min_stock'] ?? 5),
            'location_rack' => $data['location_rack'],
            'unit_price'    => (float)($data['unit_price'] ?? 0),
        ]);

        $itemId = (int)$db->lastInsertId();

        // Jika ada stok awal, catat ke kartu mutasi
        if (!empty($data['stock']) && (int)$data['stock'] > 0) {
            $stmtMut = $db->prepare("
                INSERT INTO stock_mutations (item_id, mutation_type, reference_no, qty_in, qty_out, balance, notes)
                VALUES (:item_id, 'IN', 'SALDO-AWAL', :qty, 0, :balance, 'Saldo awal input barang baru')
            ");
            $stmtMut->execute([
                'item_id' => $itemId,
                'qty'     => (int)$data['stock'],
                'balance' => (int)$data['stock'],
            ]);
        }

        return $itemId;
    }

    /**
     * Perbarui data barang
     */
    public static function update(int $id, array $data): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE items SET
                item_code     = :item_code,
                name          = :name,
                category      = :category,
                unit          = :unit,
                min_stock     = :min_stock,
                location_rack = :location_rack,
                unit_price    = :unit_price
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'            => $id,
            'item_code'     => $data['item_code'],
            'name'          => $data['name'],
            'category'      => $data['category'],
            'unit'          => $data['unit'] ?? 'Pcs',
            'min_stock'     => (int)($data['min_stock'] ?? 5),
            'location_rack' => $data['location_rack'],
            'unit_price'    => (float)($data['unit_price'] ?? 0),
        ]);
    }

    /**
     * Hapus data barang (jika belum ada relasi transaksi)
     */
    public static function delete(int $id): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM items WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Ambil riwayat mutasi kartu stok untuk barang tertentu
     */
    public static function getMutations(int $itemId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT m.*, i.name as item_name, i.item_code, i.unit
            FROM stock_mutations m
            JOIN items i ON m.item_id = i.id
            WHERE m.item_id = :item_id
            ORDER BY m.created_at DESC, m.id DESC
        ");
        $stmt->execute(['item_id' => $itemId]);
        return $stmt->fetchAll();
    }

    /**
     * Ambil semua riwayat mutasi stok global
     */
    public static function getAllMutations(int $limit = 100): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT m.*, i.name as item_name, i.item_code, i.unit
            FROM stock_mutations m
            JOIN items i ON m.item_id = i.id
            ORDER BY m.created_at DESC, m.id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
