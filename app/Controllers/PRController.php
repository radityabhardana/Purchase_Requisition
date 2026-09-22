<?php
/**
 * PRController - Alur Pengajuan & Verifikasi Purchase Requisition
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\PurchaseRequisition;
use App\Models\Item;

class PRController
{
    /**
     * Tampilkan daftar dokumen PR
     */
    public function index(): void
    {
        AuthHelper::requireLogin();

        $user = AuthHelper::user();
        $status = trim($_GET['status'] ?? '');

        // Jika user adalah requester biasa, dia hanya melihat PR miliknya sendiri
        // Admin, SPV, dan Purchasing dapat melihat seluruh PR
        $userId = ($user['role'] === 'requester') ? (int)$user['id'] : null;

        $prs = PurchaseRequisition::getAll($userId, $status ?: null);
        $pendingCount = PurchaseRequisition::countPending();

        require __DIR__ . '/../../views/pr/index.php';
    }

    /**
     * Tampilkan form pembuatan PR baru
     */
    public function create(): void
    {
        AuthHelper::requireLogin();

        $items = Item::getAll();
        $autoSelectItemId = isset($_GET['item_id']) ? (int)$_GET['item_id'] : null;

        require __DIR__ . '/../../views/pr/create.php';
    }

    /**
     * Simpan pengajuan PR baru
     */
    public function store(): void
    {
        AuthHelper::requireLogin();
        $user = AuthHelper::user();

        $targetDate = trim($_POST['target_date'] ?? '');
        $priority = trim($_POST['priority'] ?? 'Normal');
        $generalNotes = trim($_POST['general_notes'] ?? '');

        $itemIds = $_POST['item_id'] ?? [];
        $quantities = $_POST['qty'] ?? [];
        $remarks = $_POST['remarks'] ?? [];

        if (empty($targetDate) || empty($itemIds)) {
            AuthHelper::setFlash('error', 'Tanggal dibutuhkan dan minimal satu barang wajib diisi.');
            header('Location: index.php?page=pr-create');
            exit;
        }

        $items = [];
        for ($i = 0; $i < count($itemIds); $i++) {
            if (!empty($itemIds[$i]) && !empty($quantities[$i])) {
                $items[] = [
                    'item_id' => (int)$itemIds[$i],
                    'qty'     => (int)$quantities[$i],
                    'remarks' => $remarks[$i] ?? '',
                ];
            }
        }

        if (empty($items)) {
            AuthHelper::setFlash('error', 'Masukkan minimal satu barang dengan kuantitas yang valid.');
            header('Location: index.php?page=pr-create');
            exit;
        }

        try {
            $prId = PurchaseRequisition::create([
                'user_id'       => $user['id'],
                'target_date'   => $targetDate,
                'priority'      => $priority,
                'general_notes' => $generalNotes,
            ], $items);

            AuthHelper::setFlash('success', 'Pengajuan Purchase Requisition berhasil dikirim! Menunggu persetujuan Supervisor.');
            header("Location: index.php?page=pr-detail&id={$prId}");
            exit;
        } catch (\Exception $e) {
            AuthHelper::setFlash('error', 'Gagal membuat pengajuan PR: ' . $e->getMessage());
            header('Location: index.php?page=pr-create');
            exit;
        }
    }

    /**
     * Detail dokumen PR
     */
    public function detail(): void
    {
        AuthHelper::requireLogin();

        $id = (int)($_GET['id'] ?? 0);
        $pr = PurchaseRequisition::findById($id);

        if (!$pr) {
            AuthHelper::setFlash('error', 'Dokumen PR tidak ditemukan.');
            header('Location: index.php?page=pr');
            exit;
        }

        $items = PurchaseRequisition::getItems($id);

        require __DIR__ . '/../../views/pr/detail.php';
    }

    /**
     * Otorisasi Approval PR oleh Supervisor
     */
    public function approve(): void
    {
        AuthHelper::requireRole(['supervisor', 'admin']);
        $user = AuthHelper::user();

        $id = (int)($_POST['id'] ?? 0);
        $pr = PurchaseRequisition::findById($id);

        if (!$pr || $pr['status'] !== 'Pending') {
            AuthHelper::setFlash('error', 'Dokumen tidak valid untuk disetujui.');
            header('Location: index.php?page=pr');
            exit;
        }

        PurchaseRequisition::approve($id, (int)$user['id']);
        AuthHelper::setFlash('success', "Dokumen {$pr['pr_number']} telah berhasil DISETUJUI dan diteruskan ke Purchasing.");
        header("Location: index.php?page=pr-detail&id={$id}");
        exit;
    }

    /**
     * Penolakan PR oleh Supervisor
     */
    public function reject(): void
    {
        AuthHelper::requireRole(['supervisor', 'admin']);
        $user = AuthHelper::user();

        $id = (int)($_POST['id'] ?? 0);
        $notes = trim($_POST['rejection_notes'] ?? '');

        if (empty($notes)) {
            AuthHelper::setFlash('error', 'Alasan penolakan pengajuan wajib diisi.');
            header("Location: index.php?page=pr-detail&id={$id}");
            exit;
        }

        PurchaseRequisition::reject($id, (int)$user['id'], $notes);
        AuthHelper::setFlash('info', 'Dokumen PR telah DITOLAK dengan catatan yang tersimpan.');
        header("Location: index.php?page=pr-detail&id={$id}");
        exit;
    }
}
