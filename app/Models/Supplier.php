<?php
/**
 * Supplier Model (Vendor Rekanan PT NKP)
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Models;

use App\Config\Database;
use PDO;

class Supplier
{
    /**
     * Ambil seluruh vendor
     */
    public static function getAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM suppliers ORDER BY company_name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Ambil hanya vendor yang berstatus aktif
     */
    public static function getActive(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM suppliers WHERE is_active = 1 ORDER BY company_name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Cari vendor berdasarkan ID
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM suppliers WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $supplier = $stmt->fetch();
        return $supplier ?: null;
    }

    /**
     * Tambah supplier baru
     */
    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO suppliers (supplier_code, company_name, contact_person, phone, email, address, payment_term, is_active)
            VALUES (:supplier_code, :company_name, :contact_person, :phone, :email, :address, :payment_term, :is_active)
        ");
        $stmt->execute([
            'supplier_code'  => $data['supplier_code'],
            'company_name'   => $data['company_name'],
            'contact_person' => $data['contact_person'],
            'phone'          => $data['phone'],
            'email'          => $data['email'],
            'address'        => $data['address'],
            'payment_term'   => $data['payment_term'] ?? 'Net 30',
            'is_active'      => isset($data['is_active']) ? (int)$data['is_active'] : 1,
        ]);
        return (int)$db->lastInsertId();
    }

    /**
     * Perbarui data supplier
     */
    public static function update(int $id, array $data): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE suppliers SET
                supplier_code  = :supplier_code,
                company_name   = :company_name,
                contact_person = :contact_person,
                phone          = :phone,
                email          = :email,
                address        = :address,
                payment_term   = :payment_term,
                is_active      = :is_active
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'             => $id,
            'supplier_code'  => $data['supplier_code'],
            'company_name'   => $data['company_name'],
            'contact_person' => $data['contact_person'],
            'phone'          => $data['phone'],
            'email'          => $data['email'],
            'address'        => $data['address'],
            'payment_term'   => $data['payment_term'] ?? 'Net 30',
            'is_active'      => isset($data['is_active']) ? (int)$data['is_active'] : 1,
        ]);
    }

    /**
     * Hapus data supplier
     */
    public static function delete(int $id): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM suppliers WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Hitung total supplier aktif
     */
    public static function count(): int
    {
        $db = Database::getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM suppliers WHERE is_active = 1")->fetchColumn();
    }
}
