<?php
/**
 * ItemController - Manajemen Master Barang & Kartu Stok Mutasi
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\Item;

class ItemController
{
    /**
     * Tampilkan katalog dan master data barang
     */
    public function index(): void
    {
        AuthHelper::requireLogin();

        $search = trim($_GET['search'] ?? '');
        $category = trim($_GET['category'] ?? '');

        $items = Item::getAll($search ?: null, $category ?: null);
        $criticalCount = Item::countCritical();

        require __DIR__ . '/../../views/items/index.php';
    }

    /**
     * Tambah barang baru ke sistem
     */
    public function store(): void
    {
        AuthHelper::requireRole(['admin', 'warehouse']);

        $itemCode = trim($_POST['item_code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $unit = trim($_POST['unit'] ?? 'Pcs');
        $stock = (int)($_POST['stock'] ?? 0);
        $minStock = (int)($_POST['min_stock'] ?? 5);
        $locationRack = trim($_POST['location_rack'] ?? '');
        $unitPrice = (float)($_POST['unit_price'] ?? 0);

        if (empty($itemCode) || empty($name) || empty($category) || empty($locationRack)) {
            AuthHelper::setFlash('error', 'Semua kolom bertanda bintang wajib diisi.');
            header('Location: index.php?page=items');
            exit;
        }

        try {
            Item::create([
                'item_code'     => $itemCode,
                'name'          => $name,
                'category'      => $category,
                'unit'          => $unit,
                'stock'         => $stock,
                'min_stock'     => $minStock,
                'location_rack' => $locationRack,
                'unit_price'    => $unitPrice,
            ]);

            AuthHelper::setFlash('success', "Barang {$name} ({$itemCode}) berhasil didaftarkan ke inventaris.");
        } catch (\Exception $e) {
            AuthHelper::setFlash('error', 'Gagal menyimpan barang: ' . $e->getMessage());
        }

        header('Location: index.php?page=items');
        exit;
    }

    /**
     * Perbarui data master barang
     */
    public function update(): void
    {
        AuthHelper::requireRole(['admin', 'warehouse']);

        $id = (int)($_POST['id'] ?? 0);
        $item = Item::findById($id);

        if (!$item) {
            AuthHelper::setFlash('error', 'Data barang tidak ditemukan.');
            header('Location: index.php?page=items');
            exit;
        }

        try {
            Item::update($id, [
                'item_code'     => trim($_POST['item_code'] ?? $item['item_code']),
                'name'          => trim($_POST['name'] ?? $item['name']),
                'category'      => trim($_POST['category'] ?? $item['category']),
                'unit'          => trim($_POST['unit'] ?? $item['unit']),
                'min_stock'     => (int)($_POST['min_stock'] ?? $item['min_stock']),
                'location_rack' => trim($_POST['location_rack'] ?? $item['location_rack']),
                'unit_price'    => (float)($_POST['unit_price'] ?? $item['unit_price']),
            ]);

            AuthHelper::setFlash('success', "Data barang {$item['name']} berhasil diperbarui.");
        } catch (\Exception $e) {
            AuthHelper::setFlash('error', 'Gagal memperbarui barang: ' . $e->getMessage());
        }

        header('Location: index.php?page=items');
        exit;
    }

    /**
     * Hapus barang
     */
    public function delete(): void
    {
        AuthHelper::requireRole(['admin']);

        $id = (int)($_POST['id'] ?? 0);
        try {
            Item::delete($id);
            AuthHelper::setFlash('success', 'Barang berhasil dihapus dari inventaris.');
        } catch (\Exception $e) {
            AuthHelper::setFlash('error', 'Barang tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }

        header('Location: index.php?page=items');
        exit;
    }

    /**
     * Halaman riwayat kartu stok mutasi (Audit Trail)
     */
    public function mutations(): void
    {
        AuthHelper::requireLogin();

        $itemId = isset($_GET['item_id']) ? (int)$_GET['item_id'] : null;
        $selectedItem = null;

        if ($itemId) {
            $selectedItem = Item::findById($itemId);
            $mutations = Item::getMutations($itemId);
        } else {
            $mutations = Item::getAllMutations(150);
        }

        $allItems = Item::getAll();

        require __DIR__ . '/../../views/items/mutations.php';
    }
}
