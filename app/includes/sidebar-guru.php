<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo"><img src="<?= APP_URL ?>/public/assets/images/nfc-logo.png" alt="logo" class="logo-img"> SP24 Guru</div>
    </div>
    <nav class="sidebar-nav">
        <a href="?route=guru/dashboard&tab=dashboard" class="nav-item <?= ($tab ?? 'dashboard') == 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="?route=guru/dashboard&tab=presensi" class="nav-item <?= ($tab ?? '') == 'presensi' ? 'active' : '' ?>">
            <i class="fas fa-clock"></i> Rekap Presensi
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="<?= APP_URL ?>/app/index.php?route=auth/logout" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</aside>