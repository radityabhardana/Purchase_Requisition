<?php
use App\Helpers\AuthHelper;
$flash = AuthHelper::getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIP-NKP | PT Nandya Karya Perkasa</title>
    
    <!-- Local Bootstrap 5.3.3 CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    
    <!-- Local Bootstrap Icons -->
    <link rel="stylesheet" href="css/bootstrap-icons.min.css">
    
    <!-- Custom Theme & Local Fonts CSS -->
    <link rel="stylesheet" href="css/bootstrap-custom.css">
    
    <!-- Local Lucide Icons -->
    <script src="js/lucide.min.js"></script>
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 p-3">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
            
            <!-- Logo & Identitas Perusahaan -->
            <div class="text-center mb-4">
                <div class="bg-white rounded-3 p-2 shadow-sm border d-inline-flex align-items-center justify-content-center mb-2" style="width: 72px; height: 72px;">
                    <img src="logo-nkp-kecil-2.png" 
                         onerror="this.onerror=null; this.src='images/logo-nkp-kecil-2.png';" 
                         alt="Logo PT Nandya Karya Perkasa" 
                         style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
                <h1 class="h3 fw-black text-dark mb-0 tracking-tight">SIP - NKP</h1>
                <p class="small fw-bold text-warning text-uppercase mb-1 tracking-wider" style="color: #d97706 !important;">PT Nandya Karya Perkasa</p>
                <p class="text-muted small mb-0">Sistem Informasi Purchasing & Pengendalian Inventaris</p>
            </div>

            <!-- Card Login Bootstrap -->
            <div class="card shadow border-0 rounded-3 overflow-hidden position-relative">
                <!-- Top Accent Border Line -->
                <div style="height: 4px; background: linear-gradient(90deg, #f59e0b, #d97706, #b45309);"></div>

                <div class="card-body p-4 p-sm-4">
                    <!-- Flash Alert Message -->
                    <?php if ($flash): ?>
                        <?php
                        $alertClass = match($flash['type']) {
                            'success' => 'alert-success',
                            'error'   => 'alert-danger',
                            'warning' => 'alert-warning',
                            default   => 'alert-info',
                        };
                        ?>
                        <div class="alert <?= $alertClass ?> py-2 px-3 small d-flex align-items-center gap-2 mb-3 rounded-2" role="alert">
                            <i data-lucide="<?= $flash['type'] === 'error' ? 'alert-circle' : 'check-circle-2' ?>" style="width: 16px; height: 16px;" class="flex-shrink-0"></i>
                            <span><?= htmlspecialchars($flash['message']) ?></span>
                        </div>
                    <?php endif; ?>

                    <form id="loginForm" action="index.php?page=login" method="POST" class="needs-validation">
                        <div class="mb-3">
                            <label for="username" class="form-label small fw-semibold text-secondary mb-1">Username Pengguna</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary border-end-0">
                                    <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                                </span>
                                <input type="text" id="username" name="username" required
                                       class="form-control form-control-sm border-start-0 py-2"
                                       placeholder="Contoh: budi, siti, hendra...">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label small fw-semibold text-secondary mb-1">Kata Sandi (Password)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary border-end-0">
                                    <i data-lucide="lock" style="width: 16px; height: 16px;"></i>
                                </span>
                                <input type="password" id="password" name="password" required
                                       class="form-control form-control-sm border-start-0 border-end-0 py-2"
                                       placeholder="Masukkan password...">
                                <button type="button" onclick="togglePasswordVisibility()" 
                                        class="input-group-text bg-light text-secondary border-start-0" 
                                        title="Tampilkan / Sembunyikan Password">
                                    <i id="passwordEyeIcon" data-lucide="eye" style="width: 16px; height: 16px;"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn"
                                class="btn btn-warning w-100 fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2 mt-4" style="background-color: var(--nkp-amber-500); border: none; color: #020617;">
                            <span>Masuk ke Sistem</span>
                            <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="text-center mt-3 text-secondary small">
                PT Nandya Karya Perkasa • Plant Cileungsi, Bogor
            </div>
        </div>
    </div>
</div>

<!-- Local Bootstrap 5.3.3 JS Bundle -->
<script src="js/bootstrap.bundle.min.js"></script>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('passwordEyeIcon');
        if (!passwordInput) return;

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.setAttribute('data-lucide', 'eye-off');
        } else {
            passwordInput.type = 'password';
            eyeIcon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }
</script>

</body>
</html>
