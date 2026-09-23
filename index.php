<?php
/**
 * Front Controller & Router
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa (SIP-NKP)
 */

declare(strict_types=1);

// 1. Autoloader Sederhana untuk Namespace App\
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Helpers\AuthHelper;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ItemController;
use App\Controllers\SupplierController;
use App\Controllers\PRController;
use App\Controllers\POController;
use App\Controllers\GRController;
use App\Controllers\DeliveryNoteController;
use App\Controllers\UserController;

AuthHelper::initSession();

$page = $_GET['page'] ?? (AuthHelper::isLoggedIn() ? 'dashboard' : 'login');
$method = $_SERVER['REQUEST_METHOD'];

// 2. Dispatcher Route Sederhana
switch ($page) {
    // === AUTHENTICATION ===
    case 'login':
        $controller = new AuthController();
        if ($method === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    // === DASHBOARD ===
    case 'dashboard':
        (new DashboardController())->index();
        break;

    // === MASTER BARANG & STOK ===
    case 'items':
        (new ItemController())->index();
        break;

    case 'item-store':
        if ($method === 'POST') {
            (new ItemController())->store();
        }
        break;

    case 'item-update':
        if ($method === 'POST') {
            (new ItemController())->update();
        }
        break;

    case 'item-delete':
        if ($method === 'POST') {
            (new ItemController())->delete();
        }
        break;

    case 'mutations':
        (new ItemController())->mutations();
        break;

    // === MASTER SUPPLIER ===
    case 'suppliers':
        (new SupplierController())->index();
        break;

    case 'supplier-store':
        if ($method === 'POST') {
            (new SupplierController())->store();
        }
        break;

    case 'supplier-update':
        if ($method === 'POST') {
            (new SupplierController())->update();
        }
        break;

    case 'supplier-delete':
        if ($method === 'POST') {
            (new SupplierController())->delete();
        }
        break;

    // === PURCHASE REQUISITION (PR) ===
    case 'pr':
        (new PRController())->index();
        break;

    case 'pr-create':
        (new PRController())->create();
        break;

    case 'pr-store':
        if ($method === 'POST') {
            (new PRController())->store();
        }
        break;

    case 'pr-detail':
        (new PRController())->detail();
        break;

    case 'pr-approve':
        if ($method === 'POST') {
            (new PRController())->approve();
        }
        break;

    case 'pr-reject':
        if ($method === 'POST') {
            (new PRController())->reject();
        }
        break;

    // === PURCHASE ORDER (PO) ===
    case 'po':
        (new POController())->index();
        break;

    case 'po-create':
        (new POController())->create();
        break;

    case 'po-store':
        if ($method === 'POST') {
            (new POController())->store();
        }
        break;

    case 'po-print':
        (new POController())->printView();
        break;

    // === GOODS RECEIPT (GR / PENERIMAAN BARANG) ===
    case 'gr':
        (new GRController())->index();
        break;

    case 'gr-create':
        (new GRController())->create();
        break;

    case 'gr-store':
        if ($method === 'POST') {
            (new GRController())->store();
        }
        break;

    // === SURAT JALAN & PENGIRIMAN BARANG (DELIVERY NOTES) ===
    case 'delivery-notes':
        (new DeliveryNoteController())->index();
        break;

    case 'delivery-note-create':
        (new DeliveryNoteController())->create();
        break;

    case 'delivery-note-store':
        if ($method === 'POST') {
            (new DeliveryNoteController())->store();
        }
        break;

    case 'delivery-note-detail':
        (new DeliveryNoteController())->detail();
        break;

    case 'delivery-note-status':
        if ($method === 'POST') {
            (new DeliveryNoteController())->updateStatus();
        }
        break;

    case 'delivery-note-print':
        (new DeliveryNoteController())->printView();
        break;

    // === MANAJEMEN PENGGUNA & AKUN KARYAWAN (ADMIN IT) ===
    case 'users':
        (new UserController())->index();
        break;

    case 'user-store':
        if ($method === 'POST') {
            (new UserController())->store();
        }
        break;

    case 'user-update':
        if ($method === 'POST') {
            (new UserController())->update();
        }
        break;

    case 'user-delete':
        if ($method === 'POST') {
            (new UserController())->delete();
        }
        break;

    default:
        http_response_code(404);
        echo '<div style="font-family:sans-serif;text-align:center;padding:100px 20px;">
                <h1 style="font-size:48px;color:#0f172a;margin-bottom:8px;">404</h1>
                <p style="color:#64748b;font-size:18px;">Halaman yang Anda cari tidak ditemukan.</p>
                <a href="index.php?page=dashboard" style="display:inline-block;margin-top:20px;padding:10px 20px;background:#f59e0b;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;">Kembali ke Dashboard</a>
              </div>';
        break;
}
