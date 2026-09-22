<?php
/**
 * Database Connection Handler (PDO Singleton)
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    private const DB_HOST = 'localhost';
    private const DB_NAME = 'nkp_inventaris';
    private const DB_USER = 'root';
    private const DB_PASS = '';
    private const DB_PORT = '3306';

    /**
     * Dapatkan instance PDO koneksi database tunggal (Singleton pattern)
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                self::DB_HOST,
                self::DB_PORT,
                self::DB_NAME
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
            } catch (PDOException $e) {
                die('<div style="font-family:sans-serif;padding:20px;background:#fee2e2;color:#991b1b;border:1px solid #f87171;border-radius:8px;max-width:600px;margin:50px auto;">
                    <h3 style="margin-top:0;">Gagal Terhubung ke Database MySQL</h3>
                    <p>Pastikan MySQL di Laragon telah berjalan dan database <b>nkp_inventaris</b> telah diimpor.</p>
                    <small>Error: ' . htmlspecialchars($e->getMessage()) . '</small>
                </div>');
            }
        }

        return self::$instance;
    }
}
