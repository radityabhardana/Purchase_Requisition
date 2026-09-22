<?php
/**
 * Authentication & Role-Based Access Control Helper
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Helpers;

class AuthHelper
{
    /**
     * Inisialisasi session jika belum aktif
     */
    public static function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Simpan data pengguna ke session setelah login berhasil
     */
    public static function login(array $user): void
    {
        self::initSession();
        // Jangan simpan hash password di session untuk alasan keamanan
        unset($user['password']);
        $_SESSION['nkp_user'] = $user;
    }

    /**
     * Hapus session saat logout
     */
    public static function logout(): void
    {
        self::initSession();
        unset($_SESSION['nkp_user']);
        session_destroy();
    }

    /**
     * Cek apakah ada user yang sedang login
     */
    public static function isLoggedIn(): bool
    {
        self::initSession();
        return isset($_SESSION['nkp_user']) && !empty($_SESSION['nkp_user']['id']);
    }

    /**
     * Ambil data user yang sedang aktif login
     */
    public static function user(): ?array
    {
        self::initSession();
        return $_SESSION['nkp_user'] ?? null;
    }

    /**
     * Cek apakah user memiliki salah satu dari role yang diizinkan
     */
    public static function hasRole(string|array $roles): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }

        // Admin selalu memiliki akses ke semua modul
        if ($user['role'] === 'admin') {
            return true;
        }

        if (is_string($roles)) {
            return $user['role'] === $roles;
        }

        return in_array($user['role'], $roles, true);
    }

    /**
     * Proteksi halaman: Wajib login
     */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            self::setFlash('warning', 'Silakan masuk ke akun Anda terlebih dahulu.');
            header('Location: index.php?page=login');
            exit;
        }
    }

    /**
     * Proteksi halaman: Wajib memiliki role tertentu
     */
    public static function requireRole(string|array $roles): void
    {
        self::requireLogin();
        if (!self::hasRole($roles)) {
            self::setFlash('error', 'Akses ditolak. Anda tidak memiliki izin untuk membuka halaman tersebut.');
            header('Location: index.php?page=dashboard');
            exit;
        }
    }

    /**
     * Set notifikasi flash message
     */
    public static function setFlash(string $type, string $message): void
    {
        self::initSession();
        $_SESSION['flash_message'] = [
            'type'    => $type, // 'success', 'error', 'warning', 'info'
            'message' => $message,
        ];
    }

    /**
     * Ambil dan bersihkan notifikasi flash message
     */
    public static function getFlash(): ?array
    {
        self::initSession();
        if (isset($_SESSION['flash_message'])) {
            $flash = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $flash;
        }
        return null;
    }
}
