<?php
require_once 'config.php';

$nis = $_GET['nis'] ?? '';
$result = null;
$error = '';
$siswa = null;

if ($nis) {
    $response = apiRequest('/presences/parent/' . $nis, 'GET', null, false);
    
    if ($response['status'] == 200 && isset($response['data']['data'])) {
        $result = $response['data']['data'];
        if (empty($result)) {
            $error = 'Tidak ada riwayat absensi untuk NIS ini.';
        }
    } else {
        $error = $response['data']['message'] ?? 'NIS tidak ditemukan. Periksa kembali nomor induk siswa.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Absensi Anak - SP24 Presensi</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Additional styles for cek-anak page */
        .cek-anak-page {
            background: linear-gradient(135deg, #09637E 0%, #088395 50%, #7AB2B2 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
        }
        .container-cek {
            max-width: 1000px;
            margin: 0 auto;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            padding: 0.6rem 1.2rem;
            border-radius: 40px;
            color: white;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }
        .back-btn:hover {
            background: rgba(255,255,255,0.25);
            transform: translateX(-5px);
        }
        .hero-cek {
            text-align: center;
            color: white;
            margin-bottom: 2rem;
        }
        .hero-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
            backdrop-filter: blur(10px);
        }
        .hero-cek h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        .hero-cek p {
            opacity: 0.85;
        }
        .card-cek {
            background: white;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            margin-bottom: 1.5rem;
        }
        .card-cek-header {
            background: linear-gradient(135deg, #09637E, #088395);
            padding: 1.8rem;
            text-align: center;
            color: white;
        }
        .card-cek-header i {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .card-cek-header h3 {
            font-size: 1.3rem;
            font-weight: 600;
        }
        .card-cek-body {
            padding: 2rem;
        }
        .search-form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .search-input-group {
            flex: 1;
            position: relative;
        }
        .search-input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .search-input {
            width: 100%;
            padding: 0.9rem 1rem 0.9rem 2.8rem;
            border: 2px solid #e2e8f0;
            border-radius: 20px;
            font-size: 1rem;
            transition: all 0.2s;
        }
        .search-input:focus {
            outline: none;
            border-color: #088395;
            box-shadow: 0 0 0 3px rgba(8,131,149,0.1);
        }
        .search-btn {
            padding: 0.9rem 2rem;
            background: linear-gradient(135deg, #09637E, #088395);
            border: none;
            border-radius: 20px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(8,131,149,0.4);
        }
        .result-container {
            margin-top: 2rem;
            border-top: 1px solid #e2e8f0;
            padding-top: 2rem;
        }
        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .result-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .result-title i {
            font-size: 1.5rem;
            color: #088395;
        }
        .result-title h4 {
            font-size: 1.1rem;
            color: #09637E;
        }
        .nis-badge {
            background: #EBF4F6;
            padding: 0.4rem 1rem;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #09637E;
        }
        .table-cek {
            width: 100%;
            border-collapse: collapse;
        }
        .table-cek th {
            background: #F8FAFC;
            padding: 1rem;
            text-align: left;
            font-size: 0.8rem;
            font-weight: 600;
            color: #09637E;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table-cek td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.9rem;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #d1fae5;
            color: #065f46;
            padding: 0.3rem 0.8rem;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-belum {
            background: #fef3c7;
            color: #92400e;
            padding: 0.3rem 0.8rem;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .error-message {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 1rem;
            border-radius: 16px;
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #b91c1c;
        }
        .empty-message {
            text-align: center;
            padding: 2rem;
            color: #94a3b8;
        }
        .empty-message i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .footer-cek {
            text-align: center;
            color: rgba(255,255,255,0.7);
            font-size: 0.75rem;
        }
        @media (max-width: 640px) {
            .search-form {
                flex-direction: column;
            }
            .search-btn {
                justify-content: center;
            }
            .result-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .table-cek th, .table-cek td {
                padding: 0.75rem;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body class="cek-anak-page">
    <div class="container-cek">
        <!-- Tombol Kembali -->
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali ke Login
        </a>

        <!-- Hero Section -->
        <div class="hero-cek">
            <div class="hero-icon">
                <i class="fas fa-child"></i>
            </div>
            <h1>Cek Absensi Anak</h1>
            <p>Pantau kehadiran putra/putri Anda secara real-time</p>
        </div>

        <!-- Card Pencarian -->
        <div class="card-cek">
            <div class="card-cek-header">
                <i class="fas fa-search"></i>
                <h3>Cari Berdasarkan NIS</h3>
                <p style="opacity:0.8; margin-top:5px;">Masukkan Nomor Induk Siswa (NIS)</p>
            </div>
            <div class="card-cek-body">
                <form method="GET" class="search-form">
                    <div class="search-input-group">
                        <i class="fas fa-id-card"></i>
                        <input type="text" name="nis" class="search-input" 
                               placeholder="Contoh: 12345" 
                               value="<?= htmlspecialchars($nis) ?>" 
                               required>
                    </div>
                    <button type="submit" class="search-btn">
                        <i class="fas fa-magnifying-glass"></i> Cek Sekarang
                    </button>
                </form>

                <?php if ($nis && $result && count($result) > 0): ?>
                    <!-- Hasil Absensi -->
                    <div class="result-container">
                        <div class="result-header">
                            <div class="result-title">
                                <i class="fas fa-calendar-check"></i>
                                <h4>Riwayat Absensi Siswa</h4>
                            </div>
                            <span class="nis-badge">
                                <i class="fas fa-id-card"></i> NIS: <?= htmlspecialchars($nis) ?>
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table-cek">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-calendar-day"></i> Tanggal</th>
                                        <th><i class="fas fa-clock"></i> Jam Masuk</th>
                                        <th><i class="fas fa-clock"></i> Jam Keluar</th>
                                        <th><i class="fas fa-flag-checkered"></i> Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result as $p): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($p['enter'])) ?></td>
                                        <td><?= date('H:i:s', strtotime($p['enter'])) ?></td>
                                        <td>
                                            <?php if ($p['exit']): ?>
                                                <?= date('H:i:s', strtotime($p['exit'])) ?>
                                            <?php else: ?>
                                                <span class="status-belum">Belum pulang</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge">
                                                <i class="fas fa-check-circle"></i> Hadir
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php elseif ($nis && $error): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php elseif ($nis && !$result): ?>
                    <div class="empty-message">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada data absensi untuk NIS ini</p>
                        <small>Silakan coba NIS lain atau hubungi pihak sekolah</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-cek">
            <p><i class="fas fa-shield-alt"></i> Data absensi diperbarui secara real-time</p>
            <p>&copy; 2025 SP24 Presensi - Sistem Absensi Digital</p>
        </div>
    </div>
</body>
</html>