<?php
/**
 * UserController - Manajemen Akun Pengguna & Karyawan (Khusus Administrator IT)
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Controllers;

use App\Models\User;
use App\Helpers\AuthHelper;

class UserController
{
    /**
     * Tampilkan daftar seluruh pengguna/karyawan
     */
    public function index(): void
    {
        AuthHelper::requireRole('admin');

        $users = User::getAll();
        $pageTitle = 'Manajemen Akun Pengguna';

        require __DIR__ . '/../../views/layouts/header.php';
        require __DIR__ . '/../../views/users/index.php';
        require __DIR__ . '/../../views/layouts/footer.php';
    }

    /**
     * Simpan akun pengguna baru
     */
    public function store(): void
    {
        AuthHelper::requireRole('admin');

        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $department = trim($_POST['department'] ?? '');

        if (empty($name) || empty($username) || empty($password) || empty($role) || empty($department)) {
            AuthHelper::setFlash('error', 'Semua kolom wajib diisi lengkap.');
            header('Location: index.php?page=users');
            exit;
        }

        // Cek username duplikat
        if (User::usernameExists($username)) {
            AuthHelper::setFlash('error', "Username '{$username}' sudah digunakan oleh karyawan lain.");
            header('Location: index.php?page=users');
            exit;
        }

        $validRoles = ['admin', 'supervisor', 'purchasing', 'warehouse', 'requester'];
        if (!in_array($role, $validRoles)) {
            AuthHelper::setFlash('error', 'Role pengguna tidak valid.');
            header('Location: index.php?page=users');
            exit;
        }

        $success = User::create([
            'name'       => $name,
            'username'   => $username,
            'password'   => $password, // Disimpan teks biasa sesuai arahan
            'role'       => $role,
            'department' => $department,
        ]);

        if ($success) {
            AuthHelper::setFlash('success', "Akun karyawan '{$name}' berhasil ditambahkan.");
        } else {
            AuthHelper::setFlash('error', 'Gagal menambahkan akun pengguna.');
        }

        header('Location: index.php?page=users');
        exit;
    }

    /**
     * Perbarui data akun pengguna
     */
    public function update(): void
    {
        AuthHelper::requireRole('admin');

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? ''); // Opsional, jika kosong jangan diubah
        $role = trim($_POST['role'] ?? '');
        $department = trim($_POST['department'] ?? '');

        if ($id <= 0 || empty($name) || empty($username) || empty($role) || empty($department)) {
            AuthHelper::setFlash('error', 'Data pengguna tidak valid atau belum lengkap.');
            header('Location: index.php?page=users');
            exit;
        }

        // Cek username duplikat pada akun lain
        if (User::usernameExists($username, $id)) {
            AuthHelper::setFlash('error', "Username '{$username}' sudah digunakan oleh pengguna lain.");
            header('Location: index.php?page=users');
            exit;
        }

        $success = User::update($id, [
            'name'       => $name,
            'username'   => $username,
            'password'   => $password,
            'role'       => $role,
            'department' => $department,
        ]);

        if ($success) {
            AuthHelper::setFlash('success', "Data akun '{$name}' berhasil diperbarui.");
        } else {
            AuthHelper::setFlash('error', 'Gagal memperbarui data akun.');
        }

        header('Location: index.php?page=users');
        exit;
    }

    /**
     * Hapus akun pengguna
     */
    public function delete(): void
    {
        AuthHelper::requireRole('admin');

        $id = (int)($_POST['id'] ?? 0);
        $currentUser = AuthHelper::user();

        // Cegah admin menghapus akunnya sendiri yang sedang aktif
        if ($id === (int)($currentUser['id'] ?? 0)) {
            AuthHelper::setFlash('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
            header('Location: index.php?page=users');
            exit;
        }

        $user = User::findById($id);
        if (!$user) {
            AuthHelper::setFlash('error', 'Akun pengguna tidak ditemukan.');
            header('Location: index.php?page=users');
            exit;
        }

        $success = User::delete($id);

        if ($success) {
            AuthHelper::setFlash('success', "Akun '{$user['name']}' berhasil dihapus dari sistem.");
        } else {
            AuthHelper::setFlash('error', 'Gagal menghapus akun pengguna.');
        }

        header('Location: index.php?page=users');
        exit;
    }
}
