<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-chalkboard-user"></i> Manajemen Data Guru</h3>
        <button class="btn-add" onclick="showTambahGuru()">
            <i class="fas fa-plus"></i> Tambah Guru
        </button>
    </div>
    <div class="card-body">
        <div class="guru-grid-container">
            <?php foreach ($dataGuru as $g): ?>
            <div class="guru-premium-card">
                <div class="guru-premium-header">
                    <div class="guru-premium-avatar">
                        <?php
                        $teacherId = $g['teacher_id'];
                        $uploadDir = dirname(__DIR__, 3) . '/public/uploads/guru/';
                        $files = glob($uploadDir . 'guru_' . $teacherId . '_*');
                        
                        if (!empty($files) && file_exists($files[0])) {
                            $photoPath = '/public/uploads/guru/' . basename($files[0]);
                            echo '<img src="' . $photoPath . '" class="avatar-img" style="width:70px;height:70px;border-radius:50%;object-fit:cover;">';
                        } else {
                            echo '<i class="fas fa-user-graduate"></i>';
                        }
                        ?>
                    </div>
                    <h4><?= htmlspecialchars(($g['first_name'] ?? '') . ' ' . ($g['last_name'] ?? '')) ?></h4>
                    <span class="guru-premium-badge"><?= htmlspecialchars($g['username'] ?? '-') ?></span>
                </div>
                <div class="guru-premium-body">
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-venus-mars"></i> Gender</span>
                        <span class="info-value"><?= ($g['gender'] ?? '-') == 'L' ? 'Laki-laki' : 'Perempuan' ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-calendar-alt"></i> Umur</span>
                        <span class="info-value"><?= htmlspecialchars($g['age'] ?? '-') ?> tahun</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-calendar-day"></i> Bergabung</span>
                        <span class="info-value"><?= date('d M Y', strtotime($g['created_at'] ?? 'now')) ?></span>
                    </div>
                </div>
                <div class="guru-premium-footer">
                    <button class="btn-edit" onclick="editGuru(<?= $g['teacher_id'] ?>)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-delete" onclick="hapusGuru(<?= $g['teacher_id'] ?>)">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                    <button class="btn-upload" onclick="uploadFotoGuru(<?= $g['teacher_id'] ?>)">
                        <i class="fas fa-camera"></i> Foto
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>