
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Absensi Anak - SP24 Presensi</title>
    <link rel="shortcut icon" type="image/png" href="<?= APP_URL ?>/public/assets/images/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #09637E 0%, #088395 100%);
            min-height: 100vh;
        }
        .container { max-width: 1000px; margin: 0 auto; padding: 2rem 1rem; }
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
            margin-bottom: 1.5rem;
            transition: 0.2s;
        }
        .back-btn:hover { background: rgba(255,255,255,0.25); transform: translateX(-5px); }
        .hero { text-align: center; color: white; margin-bottom: 2rem; }
        .hero-icon { width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 25px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2.5rem; }
        .hero h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        .hero p { opacity: 0.85; }
        .card { background: white; border-radius: 28px; overflow: hidden; box-shadow: 0 20px 35px -10px rgba(0,0,0,0.2); }
        .card-header { background: linear-gradient(135deg, #09637E, #088395); padding: 1.5rem; text-align: center; color: white; }
        .card-header i { font-size: 2rem; margin-bottom: 0.5rem; }
        .card-header h3 { font-size: 1.3rem; margin-bottom: 0.2rem; }
        .card-body { padding: 2rem; }
        .search-form { display: flex; gap: 1rem; flex-wrap: wrap; }
        .search-input { flex: 1; padding: 0.9rem 1rem; border: 2px solid #e2e8f0; border-radius: 20px; font-size: 1rem; transition: 0.2s; }
        .search-input:focus { outline: none; border-color: #088395; }
        .search-btn { padding: 0.9rem 2rem; background: linear-gradient(135deg, #09637E, #088395); border: none; border-radius: 20px; color: white; font-weight: 600; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px; }
        .search-btn:hover { transform: translateY(-2px); }
        .info-siswa {
            background: #F8FAFC;
            border-radius: 20px;
            padding: 1.2rem;
            margin: 1.5rem 0;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: space-between;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #334155;
            font-size: 0.85rem;
        }
        .info-item i { color: #088395; width: 20px; }
        .info-item strong { color: #09637E; }
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th { background: #F8FAFC; padding: 1rem; text-align: left; font-size: 0.75rem; color: #09637E; text-transform: uppercase; letter-spacing: 0.5px; }
        .table td { padding: 1rem; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem; }
        .table tr:hover td { background: #F8FAFC; }
        .status-hadir { background: #d1fae5; color: #065f46; padding: 0.2rem 0.8rem; border-radius: 40px; font-size: 0.7rem; font-weight: 600; display: inline-block; }
        .alert-error { background: #fee2e2; border-left: 4px solid #ef4444; padding: 1rem; border-radius: 16px; margin-top: 1.5rem; color: #b91c1c; display: flex; align-items: center; gap: 8px; }
        .empty-data { text-align: center; padding: 2rem; color: #94a3b8; }
        .empty-data i { font-size: 3rem; margin-bottom: 1rem; }
        @media (max-width: 640px) { .search-form { flex-direction: column; } .search-btn { justify-content: center; } .info-siswa { flex-direction: column; } }
    </style>
</head>
<body>
<div class="container">
    <a href="<?= APP_URL ?>/app/" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali ke Login</a>
    
    <div class="hero">
        <div class="hero-icon"><i class="fas fa-child"></i></div>
        <h1>Cek Absensi Anak</h1>
        <p>Pantau kehadiran putra/putri Anda secara real-time</p>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-search"></i>
            <h3>Cari Berdasarkan NIS</h3>
            <p>Masukkan Nomor Induk Siswa (NIS)</p>
        </div>
        <div class="card-body">
            <form method="GET" class="search-form">
                <input type="hidden" name="route" value="public/cekAnak">
                <input type="text" name="nis" class="search-input" placeholder="Contoh: 12345" value="<?= htmlspecialchars($nis) ?>" required>
                <button type="submit" class="search-btn"><i class="fas fa-magnifying-glass"></i> Cek Sekarang</button>
            </form>

            <?php if ($nis && $result && count($result) > 0): ?>
                
                <?php if ($siswaInfo): ?>
                <div class="info-siswa">
                    <div class="info-item"><i class="fas fa-id-card"></i> <strong>NIS:</strong> <?= htmlspecialchars($siswaInfo['nis']) ?></div>
                    <div class="info-item"><i class="fas fa-user"></i> <strong>Nama:</strong> <?= htmlspecialchars($siswaInfo['first_name'] . ' ' . $siswaInfo['last_name']) ?></div>
                    <div class="info-item"><i class="fas fa-school"></i> <strong>Kelas:</strong> <?= htmlspecialchars($siswaInfo['class']) ?></div>
                    <div class="info-item"><i class="fas fa-users"></i> <strong>Orang Tua:</strong> <?= htmlspecialchars($siswaInfo['parent']) ?></div>
                </div>
                <?php endif; ?>

                <table class="table">
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
                            <td><?= $p['exit'] ? date('H:i:s', strtotime($p['exit'])) : '<span style="color:#f59e0b;">Belum pulang</span>' ?></td>
                            <td><span class="status-hadir"><i class="fas fa-check-circle"></i> Hadir</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif ($nis && $error): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php elseif ($nis && empty($result)): ?>
                <div class="empty-data">
                    <i class="fas fa-inbox"></i>
                    <p>Belum ada data absensi untuk NIS ini</p>
                    <small>Silakan coba NIS lain atau hubungi pihak sekolah</small>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>