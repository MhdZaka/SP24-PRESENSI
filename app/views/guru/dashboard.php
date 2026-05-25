
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/style.css">
    <style>
        .filter-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        .filter-input {
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.8rem;
            background: white;
        }
        .btn-sm-primary {
            background: linear-gradient(135deg, #09637E, #088395);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 500;
            transition: 0.2s;
        }
        .btn-sm-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(8,131,149,0.3);
        }
        .siswa-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .siswa-avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #EBF4F6;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .siswa-avatar-sm i {
            font-size: 1.2rem;
            color: #09637E;
        }
        .siswa-avatar-sm img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .siswa-name {
            font-weight: 500;
            color: #1e293b;
        }
        .time-cell {
            font-family: monospace;
            font-size: 0.85rem;
        }
        .time-badge {
            background: #EBF4F6;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: #09637E;
        }
        .time-badge-out {
            background: #E0F2FE;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: #0284C7;
        }
        .status-belum-pulang {
            background: #FEF3C7;
            color: #D97706;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        .status-hadir-badge {
            background: #d1fae5;
            color: #065f46;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        .date-cell {
            font-weight: 500;
            color: #09637E;
        }
        .empty-data {
            text-align: center;
            padding: 2rem;
            color: #94a3b8;
        }
        .empty-data i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <?php include 'includes/sidebar-guru.php'; ?>
    <main class="main-content">
        <?php include 'includes/topbar.php'; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3><?= $totalSiswa ?></h3>
                    <p>Total Siswa</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chalkboard-user"></i></div>
                <div class="stat-info">
                    <h3><?= $totalGuru ?></h3>
                    <p>Total Guru</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-info">
                    <h3><?= $hadirHariIni ?></h3>
                    <p>Hadir Hari Ini</p>
                </div>
            </div>
        </div>

        <?php if ($tab == 'dashboard'): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-users"></i> Data Siswa</h3>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Orang Tua</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($dataSiswa, 0, 10) as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['nis'] ?? '-') ?></td>
                                <td><?= htmlspecialchars(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')) ?></td>
                                <td><?= htmlspecialchars($s['class'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($s['parent'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (count($dataSiswa) == 0): ?>
                                <tr>
                                    <td colspan="4" class="empty-data">Belum ada data siswa</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($tab == 'presensi'): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-clock"></i> Rekap Presensi Terkini</h3>
                    <div class="filter-group">
                        <input type="date" id="filterDate" class="filter-input">
                        <button onclick="filterPresensi()" class="btn-sm-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <button onclick="resetFilter()" class="btn-sm-primary" style="background:#64748b;">
                            <i class="fas fa-undo-alt"></i> Reset
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table" id="presensiTable">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-calendar-day"></i> Tanggal</th>
                                    <th><i class="fas fa-user"></i> Nama Siswa</th>
                                    <th><i class="fas fa-clock"></i> Jam Masuk</th>
                                    <th><i class="fas fa-clock"></i> Jam Keluar</th>
                                    <th><i class="fas fa-flag-checkered"></i> Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($dataPresensi) > 0): ?>
                                    <?php foreach ($dataPresensi as $p): ?>
                                    <tr>
                                        <td class="date-cell">
                                            <?= date('d/m/Y', strtotime($p['enter'])) ?>
                                        </td>
                                        <td>
                                            <div class="siswa-info">
                                                <div class="siswa-avatar-sm">
                                                    <?php
                                                    $studentId = $p['student']['student_id'] ?? 0;
                                                    $uploadDir = UPLOAD_PATH_SISWA;
                                                    $files = glob($uploadDir . 'siswa_' . $studentId . '_*');
                                                    if (!empty($files)) {
                                                        $filename = basename($files[0]);
                                                        echo '<img src="' . APP_URL . '/public/uploads/siswa/' . $filename . '">';
                                                    } else {
                                                        echo '<i class="fas fa-user-circle"></i>';
                                                    }
                                                    ?>
                                                </div>
                                                <span class="siswa-name">
                                                    <?= htmlspecialchars($p['student']['first_name'] ?? '') ?> 
                                                    <?= htmlspecialchars($p['student']['last_name'] ?? '') ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="time-cell">
                                            <span class="time-badge">
                                                <i class="fas fa-sign-in-alt"></i> <?= date('H:i:s', strtotime($p['enter'])) ?>
                                            </span>
                                        </td>
                                        <td class="time-cell">
                                            <?php if ($p['exit']): ?>
                                                <span class="time-badge-out">
                                                    <i class="fas fa-sign-out-alt"></i> <?= date('H:i:s', strtotime($p['exit'])) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="status-belum-pulang">
                                                    <i class="fas fa-clock"></i> Belum pulang
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-hadir-badge">
                                                <i class="fas fa-check-circle"></i> Hadir
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="empty-data">
                                            <i class="fas fa-inbox"></i><br>
                                            Belum ada data presensi
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <script>
                function filterPresensi() {
                    let filterDate = document.getElementById('filterDate').value;
                    if (!filterDate) {
                        alert('Pilih tanggal terlebih dahulu');
                        return;
                    }
                    
                    let rows = document.querySelectorAll('#presensiTable tbody tr');
                    let hasVisible = false;
                    
                    rows.forEach(row => {
                        let dateCell = row.querySelector('.date-cell');
                        if (dateCell) {
                            let parts = dateCell.innerText.split('/');
                            let rowDate = parts[2] + '-' + parts[1] + '-' + parts[0];
                            if (rowDate === filterDate) {
                                row.style.display = '';
                                hasVisible = true;
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    });
                    
                    if (!hasVisible) {
                        alert('Tidak ada data presensi untuk tanggal ' + filterDate);
                    }
                }
                
                function resetFilter() {
                    let rows = document.querySelectorAll('#presensiTable tbody tr');
                    rows.forEach(row => {
                        row.style.display = '';
                    });
                    document.getElementById('filterDate').value = '';
                }
            </script>
        <?php endif; ?>
    </main>
</div>
</body>
</html>