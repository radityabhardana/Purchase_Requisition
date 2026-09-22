<?php
/**
 * User Model
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Models;

use App\Config\Database;
use PDO;

class User
{
    /**
     * Cari user berdasarkan username
     */
    public static function findByUsername(string $username): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Cari user berdasarkan ID
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, name, username, password, role, department, created_at FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Ambil semua data pengguna beserta password teks biasa untuk Admin IT
     */
    public static function getAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT id, name, username, password, role, department, created_at FROM users ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    /**
     * Tambah Akun Pengguna Baru (Password disimpan teks biasa sesuai instruksi)
     */
    public static function create(array $data): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO users (name, username, password, role, department) 
            VALUES (:name, :username, :password, :role, :department)
        ");

        return $stmt->execute([
            'name'       => $data['name'],
            'username'   => $data['username'],
            'password'   => $data['password'], // Plain-text
            'role'       => $data['role'],
            'department' => $data['department'],
        ]);
    }

    /**
     * Perbarui Data Pengguna
     */
    public static function update(int $id, array $data): bool
    {
        $db = Database::getConnection();

        // Jika password diisi, perbarui password (teks biasa)
        if (!empty($data['password'])) {
            $stmt = $db->prepare("
                UPDATE users 
                SET name = :name, username = :username, password = :password, role = :role, department = :department 
                WHERE id = :id
            ");
            return $stmt->execute([
                'id'         => $id,
                'name'       => $data['name'],
                'username'   => $data['username'],
                'password'   => $data['password'],
                'role'       => $data['role'],
                'department' => $data['department'],
            ]);
        }

        // Jika password dikosongkan, jangan ubah password
        $stmt = $db->prepare("
            UPDATE users 
            SET name = :name, username = :username, role = :role, department = :department 
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'         => $id,
            'name'       => $data['name'],
            'username'   => $data['username'],
            'role'       => $data['role'],
            'department' => $data['department'],
        ]);
    }

    /**
     * Hapus Akun Pengguna
     */
    public static function delete(int $id): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Cek apakah username sudah terdaftar
     */
    public static function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $db = Database::getConnection();
        if ($excludeId !== null) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :username AND id != :excludeId");
            $stmt->execute(['username' => $username, 'excludeId' => $excludeId]);
        } else {
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
        }
        return (int)$stmt->fetchColumn() > 0;
    }
}
