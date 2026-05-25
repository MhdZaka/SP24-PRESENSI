<?php
require_once 'config.php';
redirectIfNotLoggedIn();

// Panggil API
$students = apiRequest('/students', 'GET', null, true);
$teachers = apiRequest('/teachers', 'GET', null, true);
$presences = apiRequest('/presences', 'GET', null, true);

$dataSiswa = $students['data']['data'] ?? [];
$dataGuru = $teachers['data']['data'] ?? [];
$dataPresensi = $presences['data']['data'] ?? [];

$totalSiswa = count($dataSiswa);
$totalGuru = count($dataGuru);
$totalHadir = 0;
$hariIni = date('Y-m-d');

foreach ($dataPresensi as $p) {
    if (strpos($p['enter'], $hariIni) === 0) {
        $totalHadir++;
    }
}

$tab = $_GET['tab'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - SP24 Presensi</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Additional premium styles for guru dashboard */
        .stats-premium {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-premium-card {
            background: white;
            border-radius: 28px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border: 1px solid rgba(8,131,149,0.1);
        }
        .stat-premium-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(8,131,149,0.15);
        }
        .stat-premium-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #09637E15, #08839515);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #088395;
        }
        .stat-premium-info h3 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #09637E, #088395);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .guru-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }
        .guru-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border: 1px solid rgba(8,131,149,0.1);
        }
        .guru-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(8,131,149,0.15);
        }
        .guru-card-header {
            background: linear-gradient(135deg, #09637E, #088395);
            padding: 1.5rem;
            text-align: center;
            color: white;
            position: relative;
        }
        .guru-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
        }
        .guru-card-header h4 {
            font-size: 1.2rem;
            margin-bottom: 0.2rem;
        }
        .guru-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 0.2rem 0.8rem;
            border-radius: 40px;
            font-size: 0.7rem;
        }
        .guru-card-body {
            padding: 1.2rem;
        }
        .guru-info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.6rem 0;
            border-bottom: 1px solid #f0f2f5;
        }
        .guru-info-label {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
        }
        .guru-info-value {
            font-weight: 600;
            color: #1e293b;
        }
        .gender-male {
            color: #3b82f6;
        }
        .gender-female {
            color: #ec489a;
        }
        .guru-card-footer {
            padding: 1rem 1.2rem;
            background: #F8FAFC;
            display: flex;
            gap: 0.8rem;
        }
        .btn-guru-action {
            flex: 1;
            padding: 0.5rem;
            border-radius: 40px;
            border: none;
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-edit-guru {
            background: #FEF3C7;
            color: #D97706;
        }
        .btn-edit-guru:hover {
            background: #F59E0B;
            color: white;
        }
        .btn-delete-guru {
            background: #FEE2E2;
            color: #DC2626;
        }
        .btn-delete-guru:hover {
            background: #EF4444;
            color: white;
        }
        .empty-guru {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 24px;
        }
        @media (max-width: 768px) {
            .guru-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="admin-dashboard">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-chalkboard-user"></i> SP24 Guru
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard-guru.php?tab=dashboard" class="nav-item <?= $tab == 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="dashboard-guru.php?tab=siswa" class="nav-item <?= $tab == 'siswa' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Data Siswa
            </a>
            <a href="dashboard-guru.php?tab=guru" class="nav-item <?= $tab == 'guru' ? 'active' : '' ?>">
                <i class="fas fa-chalkboard-user"></i> Data Guru
            </a>
            <a href="dashboard-guru.php?tab=presensi" class="nav-item <?= $tab == 'presensi' ? 'active' : '' ?>">
                <i class="fas fa-clock"></i> Rekap Presensi
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>

    <main class="main-panel">
        <div class="top-bar">
            <h1>Dashboard Guru</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i> 
                <?= htmlspecialchars($_SESSION['user']['first_name'] ?? $_SESSION['user']['username'] ?? 'Guru') ?>
            </div>
        </div>

        <?php if ($tab == 'dashboard'): ?>
            <div class="stats-premium">
                <div class="stat-premium-card">
                    <div class="stat-premium-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-premium-info">
                        <h3><?= $totalSiswa ?></h3>
                        <p>Total Siswa</p>
                    </div>
                </div>
                <div class="stat-premium-card">
                    <div class="stat-premium-icon"><i class="fas fa-chalkboard-user"></i></div>
                    <div class="stat-premium-info">
                        <h3><?= $totalGuru ?></h3>
                        <p>Total Guru</p>
                    </div>
                </div>
                <div class="stat-premium-card">
                    <div class="stat-premium-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-premium-info">
                        <h3><?= $totalHadir ?></h3>
                        <p>Hadir Hari Ini</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-users"></i> Data Siswa Terkini</h3>
                    <a href="dashboard-guru.php?tab=siswa" class="btn-add" style="padding: 0.4rem 1rem; font-size: 0.8rem;">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead><tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>Orang Tua</th></tr></thead>
                            <tbody>
                                <?php foreach (array_slice($dataSiswa, 0, 5) as $siswa): ?>
                                <tr>
                                    <td><?= htmlspecialchars($siswa['nis'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars(($siswa['first_name'] ?? '') . ' ' . ($siswa['last_name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars($siswa['class'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($siswa['parent'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php elseif ($tab == 'siswa'): ?>
            <div class="card">
                <div class="card-header"><h3><i class="fas fa-users"></i> Data Siswa</h3></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead><tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>Orang Tua</th><th>Tag ID</th></tr></thead>
                            <tbody>
                                <?php foreach ($dataSiswa as $siswa): ?>
                                <tr>
                                    <td><?= htmlspecialchars($siswa['nis'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars(($siswa['first_name'] ?? '') . ' ' . ($siswa['last_name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars($siswa['class'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($siswa['parent'] ?? '-') ?></td>
                                    <td><code><?= htmlspecialchars($siswa['tag_id'] ?? '-') ?></code></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php elseif ($tab == 'guru'): ?>
            <!-- DATA GURU MANTAB - GRID CARD LAYOUT -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chalkboard-user"></i> Data Guru</h3>
                    <span class="nis-badge" style="background:#EBF4F6; padding:0.3rem 1rem; border-radius:40px;">
                        <i class="fas fa-users"></i> Total: <?= $totalGuru ?> Guru
                    </span>
                </div>
                <div class="card-body">
                    <?php if (count($dataGuru) > 0): ?>
                        <div class="guru-grid">
                            <?php foreach ($dataGuru as $guru): ?>
                            <div class="guru-card">
                                <div class="guru-card-header">
                                    <div class="guru-avatar">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <h4><?= htmlspecialchars(($guru['first_name'] ?? '') . ' ' . ($guru['last_name'] ?? '')) ?></h4>
                                    <span class="guru-badge">
                                        <i class="fas fa-id-card"></i> <?= htmlspecialchars($guru['username'] ?? '-') ?>
                                    </span>
                                </div>
                                <div class="guru-card-body">
                                    <div class="guru-info-row">
                                        <span class="guru-info-label"><i class="fas fa-venus-mars"></i> Gender</span>
                                        <span class="guru-info-value <?= ($guru['gender'] ?? '') == 'L' ? 'gender-male' : 'gender-female' ?>">
                                            <?= ($guru['gender'] ?? '-') == 'L' ? '👨 Laki-laki' : '👩 Perempuan' ?>
                                        </span>
                                    </div>
                                    <div class="guru-info-row">
                                        <span class="guru-info-label"><i class="fas fa-calendar-alt"></i> Umur</span>
                                        <span class="guru-info-value"><?= htmlspecialchars($guru['age'] ?? '-') ?> tahun</span>
                                    </div>
                                    <div class="guru-info-row">
                                        <span class="guru-info-label"><i class="fas fa-calendar-day"></i> Bergabung</span>
                                        <span class="guru-info-value"><?= date('d M Y', strtotime($guru['created_at'] ?? 'now')) ?></span>
                                    </div>
                                </div>
                                <div class="guru-card-footer">
                                    <button class="btn-guru-action btn-edit-guru" onclick="editGuru(<?= $guru['teacher_id'] ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn-guru-action btn-delete-guru" onclick="hapusGuru(<?= $guru['teacher_id'] ?>)">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-guru">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
                            <p style="margin-top: 1rem; color: #94a3b8;">Belum ada data guru</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif ($tab == 'presensi'): ?>
            <div class="card">
                <div class="card-header"><h3><i class="fas fa-clock"></i> Rekap Presensi Terkini</h3></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead><tr><th>Tanggal</th><th>Siswa</th><th>Jam Masuk</th><th>Jam Keluar</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php foreach (array_slice($dataPresensi, 0, 20) as $p): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($p['enter'])) ?></td>
                                    <td><?= htmlspecialchars($p['student']['first_name'] ?? '-') ?></td>
                                    <td><?= date('H:i:s', strtotime($p['enter'])) ?></td>
                                    <td><?= $p['exit'] ? date('H:i:s', strtotime($p['exit'])) : '<span class="status-belum">Belum pulang</span>' ?></td>
                                    <td><span class="status-badge"><i class="fas fa-check-circle"></i> Hadir</span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
function editGuru(id) {
    alert('Edit guru ID: ' + id + ' (Fitur menyusul)');
}
function hapusGuru(id) {
    if(confirm('Yakin hapus guru ini?')) {
        alert('Hapus guru ID: ' + id + ' (Fitur menyusul)');
    }
}
</script>
</body>
</html>