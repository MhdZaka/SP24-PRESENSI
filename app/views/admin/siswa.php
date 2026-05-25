<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users"></i> Manajemen Data Siswa</h3>
        <button class="btn-add" onclick="showTambahSiswa()">
            <i class="fas fa-plus"></i> Tambah Siswa
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Orang Tua</th>
                        <th>Tag ID</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataSiswa as $s): ?>
                    <tr>
                        <td class="avatar-cell">
                            <img src="<?= APP_URL ?>/app/serve_foto.php?id=<?= $s['student_id'] ?>&type=siswa" 
                                style="width:45px;height:45px;border-radius:50%;object-fit:cover; border:2px solid #EBF4F6; background:#EBF4F6;"
                                onerror="this.onerror=null; this.src='<?= APP_URL ?>/app/serve_foto.php?id=0';">
                        </td>
                        <td><?= htmlspecialchars($s['nis'] ?? '-') ?></td>
                        <td><?= htmlspecialchars(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($s['class'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($s['parent'] ?? '-') ?></td>
                        <td><code><?= htmlspecialchars($s['tag_id'] ?? '-') ?></code></td>
                        <td class="action-buttons">
                            <button class="btn-edit" onclick="editSiswa(<?= $s['student_id'] ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn-delete" onclick="hapusSiswa(<?= $s['student_id'] ?>)">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                            <button class="btn-upload" onclick="uploadFotoSiswa(<?= $s['student_id'] ?>)">
                                <i class="fas fa-camera"></i> Foto
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
