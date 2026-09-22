<?php
/**
 * AuthController - Menangani Login, Logout, dan Pengalihan Sesi
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Controllers;

use App\Models\User;
use App\Helpers\AuthHelper;

class AuthController
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin(): void
    {
        if (AuthHelper::isLoggedIn()) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        require __DIR__ . '/../../views/auth/login.php';
    }

    /**
     * Proses submit form login
     */
    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            AuthHelper::setFlash('error', 'Username dan password wajib diisi.');
            header('Location: index.php?page=login');
            exit;
        }

        $user = User::findByUsername($username);

        $isPasswordValid = false;
        if ($user) {
            // Cek kecocokan password teks biasa (permintaan Admin IT) atau hash bcrypt
            if ($password === $user['password'] || password_verify($password, $user['password'])) {
                $isPasswordValid = true;
            }
        }

        if ($isPasswordValid) {
            AuthHelper::login($user);
            AuthHelper::setFlash('success', 'Selamat datang kembali, ' . htmlspecialchars($user['name']) . ' (' . ucfirst($user['role']) . ')!');
            header('Location: index.php?page=dashboard');
            exit;
        }

        AuthHelper::setFlash('error', 'Username atau password yang Anda masukkan salah.');
        header('Location: index.php?page=login');
        exit;
    }

    /**
     * Logout dari sistem
     */
    public function logout(): void
    {
        AuthHelper::logout();
        AuthHelper::setFlash('info', 'Anda telah berhasil keluar dari sistem.');
        header('Location: index.php?page=login');
        exit;
    }
}
