<?php
/**
 * SupplierController - Manajemen Vendor Rekanan PT Nandya Karya Perkasa
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\Supplier;

class SupplierController
{
    /**
     * Tampilkan daftar vendor rekanan
     */
    public function index(): void
    {
        AuthHelper::requireRole(['purchasing', 'admin']);
        $suppliers = Supplier::getAll();
        require __DIR__ . '/../../views/suppliers/index.php';
    }

    /**
     * Tambah vendor baru
     */
    public function store(): void
    {
        AuthHelper::requireRole(['admin', 'purchasing']);

        $supplierCode = trim($_POST['supplier_code'] ?? '');
        $companyName  = trim($_POST['company_name'] ?? '');
        $contactPerson = trim($_POST['contact_person'] ?? '');
        $phone        = trim($_POST['phone'] ?? '');
        $email        = trim($_POST['email'] ?? '');
        $address      = trim($_POST['address'] ?? '');
        $paymentTerm  = trim($_POST['payment_term'] ?? 'Net 30');

        if (empty($supplierCode) || empty($companyName) || empty($contactPerson) || empty($phone)) {
            AuthHelper::setFlash('error', 'Semua kolom bertanda bintang wajib diisi.');
            header('Location: index.php?page=suppliers');
            exit;
        }

        try {
            Supplier::create([
                'supplier_code'  => $supplierCode,
                'company_name'   => $companyName,
                'contact_person' => $contactPerson,
                'phone'          => $phone,
                'email'          => $email,
                'address'        => $address,
                'payment_term'   => $paymentTerm,
                'is_active'      => 1,
            ]);

            AuthHelper::setFlash('success', "Vendor {$companyName} ({$supplierCode}) berhasil ditambahkan.");
        } catch (\Exception $e) {
            AuthHelper::setFlash('error', 'Gagal menyimpan vendor: ' . $e->getMessage());
        }

        header('Location: index.php?page=suppliers');
        exit;
    }

    /**
     * Update data vendor
     */
    public function update(): void
    {
        AuthHelper::requireRole(['admin', 'purchasing']);

        $id = (int)($_POST['id'] ?? 0);
        $supplier = Supplier::findById($id);

        if (!$supplier) {
            AuthHelper::setFlash('error', 'Data vendor tidak ditemukan.');
            header('Location: index.php?page=suppliers');
            exit;
        }

        try {
            Supplier::update($id, [
                'supplier_code'  => trim($_POST['supplier_code'] ?? $supplier['supplier_code']),
                'company_name'   => trim($_POST['company_name'] ?? $supplier['company_name']),
                'contact_person' => trim($_POST['contact_person'] ?? $supplier['contact_person']),
                'phone'          => trim($_POST['phone'] ?? $supplier['phone']),
                'email'          => trim($_POST['email'] ?? $supplier['email']),
                'address'        => trim($_POST['address'] ?? $supplier['address']),
                'payment_term'   => trim($_POST['payment_term'] ?? $supplier['payment_term']),
                'is_active'      => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1,
            ]);

            AuthHelper::setFlash('success', "Data vendor {$supplier['company_name']} berhasil diperbarui.");
        } catch (\Exception $e) {
            AuthHelper::setFlash('error', 'Gagal memperbarui data vendor: ' . $e->getMessage());
        }

        header('Location: index.php?page=suppliers');
        exit;
    }

    /**
     * Hapus vendor
     */
    public function delete(): void
    {
        AuthHelper::requireRole(['admin']);

        $id = (int)($_POST['id'] ?? 0);
        try {
            Supplier::delete($id);
            AuthHelper::setFlash('success', 'Vendor berhasil dihapus dari sistem.');
        } catch (\Exception $e) {
            AuthHelper::setFlash('error', 'Vendor tidak dapat dihapus karena telah memiliki riwayat pesanan PO.');
        }

        header('Location: index.php?page=suppliers');
        exit;
    }
}
