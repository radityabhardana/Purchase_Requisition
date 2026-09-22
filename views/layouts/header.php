<?php
use App\Helpers\AuthHelper;
use App\Helpers\FormatHelper;

$currentUser = AuthHelper::user();
$flash = AuthHelper::getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>SIP-NKP | PT Nandya Karya Perkasa</title>
    
    <!-- Favicon Resmi PT NKP -->
    <link rel="shortcut icon" href="logo-nkp-kecil-2.png" type="image/png">
    
    <!-- Local Bootstrap 5.3.3 CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    
    <!-- Local Bootstrap Icons -->
    <link rel="stylesheet" href="css/bootstrap-icons.min.css">
    
    <!-- Custom Theme & Local Fonts CSS PT NKP -->
    <link rel="stylesheet" href="css/bootstrap-custom.css">
    
    <!-- Local Lucide Icons -->
    <script src="js/lucide.min.js"></script>
</head>
<body>

<div id="appLayout">
    <!-- Sidebar Navigasi (Desktop & Offcanvas Mobile) -->
    <?php require __DIR__ . '/sidebar.php'; ?>

    <!-- Konten Utama Wrapper -->
    <div id="contentWrapper">
        <!-- Top Navbar -->
        <header class="nkp-top-navbar no-print">
            <div class="container-fluid px-3 px-md-4 py-2 d-flex align-items-center justify-content-between">
                <!-- Sisi Kiri: Tombol Menu Mobile, Plant Badge, Tanggal & Realtime Clock -->
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" title="Buka Navigasi">
                        <i data-lucide="menu" style="width: 18px; height: 18px;"></i>
                    </button>
                    
                    <span class="badge bg-dark text-warning px-2.5 py-1.5 d-inline-flex align-items-center font-monospace fw-bold">
                        <i data-lucide="factory" class="me-1.5" style="width: 14px; height: 14px;"></i>
                        PLANT CILEUNGSI
                    </span>

                    <span class="text-muted d-none d-md-inline">|</span>

                    <div class="d-none d-md-flex align-items-center gap-2 small">
                        <span class="text-secondary d-inline-flex align-items-center">
                            <i data-lucide="calendar" class="me-1.5 text-muted" style="width: 14px; height: 14px;"></i>
                            <span id="realtimeDate"><?= FormatHelper::dateIndo(date('Y-m-d')) ?></span>
                        </span>

                        <span class="text-muted">•</span>

                        <!-- Digital Real-time Clock Badge -->
                        <div class="badge bg-dark text-light px-2.5 py-1.5 border border-secondary shadow-sm d-inline-flex align-items-center font-monospace fw-bold" title="Waktu Server Lokal (Real-time)">
                            <span class="spinner-grow spinner-grow-sm text-success me-2" style="width: 8px; height: 8px;" role="status"></span>
                            <span id="realtimeClock" class="text-warning">--:--:--</span>
                            <span class="ms-1 text-muted" style="font-size: 0.65rem;">WIB</span>
                        </div>
                    </div>
                </div>

                <!-- Sisi Kanan: Profil User & Logout -->
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-dark text-warning d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 34px; height: 34px; font-size: 0.8rem;">
                            <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 2)) ?>
                        </div>
                        <div class="d-none d-sm-block text-start lh-sm">
                            <div class="fw-bold text-dark small"><?= htmlspecialchars($currentUser['name'] ?? '') ?></div>
                            <div class="text-muted text-capitalize" style="font-size: 0.7rem;">
                                <?= ucfirst($currentUser['role'] ?? '') ?> • <?= htmlspecialchars($currentUser['department'] ?? '') ?>
                            </div>
                        </div>
                    </div>

                    <a href="index.php?page=logout" class="btn btn-sm btn-outline-danger p-1.5 rounded-2" title="Keluar dari Sistem">
                        <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
                    </a>
                </div>
            </div>

            <!-- Flash Message Banner -->
            <?php if ($flash): ?>
                <?php
                $bsAlert = match($flash['type']) {
                    'success' => 'alert-success',
                    'error'   => 'alert-danger',
                    'warning' => 'alert-warning',
                    default   => 'alert-info',
                };
                $icon = match($flash['type']) {
                    'success' => 'check-circle-2',
                    'error'   => 'alert-circle',
                    'warning' => 'alert-triangle',
                    default   => 'info',
                };
                ?>
                <div class="alert <?= $bsAlert ?> alert-dismissible fade show rounded-0 mb-0 py-2 px-4 d-flex align-items-center justify-content-between" role="alert">
                    <div class="d-flex align-items-center gap-2 small fw-semibold">
                        <i data-lucide="<?= $icon ?>" style="width: 16px; height: 16px;"></i>
                        <span><?= htmlspecialchars($flash['message']) ?></span>
                    </div>
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </header>

        <!-- Main Body Scrollable Container -->
        <main class="flex-grow-1 p-3 p-md-4">
