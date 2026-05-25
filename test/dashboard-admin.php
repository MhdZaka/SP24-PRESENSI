<?php
require_once 'config.php';
redirectIfNotLoggedIn();

if ($_SESSION['user']['role'] != 'admin') {
    header('Location: dashboard-guru.php');
    exit();
}

// Panggil API
$students = apiRequest('/students', 'GET', null, true);
$teachers = apiRequest('/teachers', 'GET', null, true);

$totalSiswa = isset($students['data']['data']) ? count($students['data']['data']) : 0;
$totalGuru = isset($teachers['data']['data']) ? count($teachers['data']['data']) : 0;
$dataSiswa = $students['data']['data'] ?? [];
$dataGuru = $teachers['data']['data'] ?? [];

$tab = $_GET['tab'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SP24</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .btn-warning {
            background: #f59e0b;
            color: white;
        }
        .btn-warning:hover {
            background: #d97706;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            width: 90%;
            max-width: 500px;
            border-radius: 24px;
            overflow: hidden;
        }
        .modal-header {
            background: linear-gradient(120deg, #09637E, #088395);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h3 {
            margin: 0;
        }
        .modal-close {
            font-size: 1.8rem;
            cursor: pointer;
        }
        .modal-close:hover {
            opacity: 0.8;
        }
        .modal-content .form-group {
            padding: 0 1.5rem;
            margin-bottom: 1rem;
        }
        .modal-content form {
            padding-bottom: 1.5rem;
        }
        .modal-content .btn {
            margin: 0 1.5rem;
            width: calc(100% - 3rem);
        }
    </style>
</head>
<body>
<div class="admin-dashboard">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-qrcode"></i> SP24 Admin
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard-admin.php?tab=dashboard" class="nav-item <?= $tab == 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="dashboard-admin.php?tab=siswa" class="nav-item <?= $tab == 'siswa' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Data Siswa
            </a>
            <a href="dashboard-admin.php?tab=guru" class="nav-item <?= $tab == 'guru' ? 'active' : '' ?>">
                <i class="fas fa-chalkboard-user"></i> Data Guru
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
            <h1>Dashboard Admin</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i> 
                <?= htmlspecialchars($_SESSION['user']['username'] ?? 'Admin') ?>
            </div>
        </div>

        <?php if ($tab == 'dashboard'): ?>
            <!-- STATISTIK -->
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
            </div>

            <!-- DAFTAR SISWA RINGKASAN -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-users"></i> Daftar Siswa (Ringkasan)</h3>
                    <a href="dashboard-admin.php?tab=siswa" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>Orang Tua</th><th>Tag ID</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($dataSiswa, 0, 5) as $siswa): ?>
                                <tr>
                                    <td><?= htmlspecialchars($siswa['nis'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars(($siswa['first_name'] ?? '') . ' ' . ($siswa['last_name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars($siswa['class'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($siswa['parent'] ?? '-') ?></td>
                                    <td><code><?= htmlspecialchars($siswa['tag_id'] ?? '-') ?></code></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (count($dataSiswa) == 0): ?>
                                    <tr><td colspan="5" style="text-align: center;">Belum ada data siswa</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php elseif ($tab == 'siswa'): ?>
            <!-- MANAJEMEN SISWA -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-users"></i> Manajemen Data Siswa</h3>
                    <button class="btn-add" onclick="showTambahSiswa()">
                        <i class="fas fa-plus-circle"></i> Tambah Siswa
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>NIS</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Orang Tua</th>
                                    <th>Tag ID</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dataSiswa as $siswa): ?>
                                <tr>
                                    <td><?= htmlspecialchars($siswa['nis'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars(($siswa['first_name'] ?? '') . ' ' . ($siswa['last_name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars($siswa['class'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($siswa['parent'] ?? '-') ?></td>
                                    <td><code><?= htmlspecialchars($siswa['tag_id'] ?? '-') ?></code></td>
                                    <td class="action-buttons">
                                        <button class="btn-action btn-edit" onclick="editSiswa(<?= $siswa['student_id'] ?>)">
                                            <i class="fas fa-pen"></i> Edit
                                        </button>
                                        <button class="btn-action btn-delete" onclick="hapusSiswa(<?= $siswa['student_id'] ?>)">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (count($dataSiswa) == 0): ?>
                                    <tr><td colspan="6" style="text-align: center;">Belum ada data siswa. Klik "Tambah Siswa" untuk menambahkan.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php elseif ($tab == 'guru'): ?>
            <!-- MANAJEMEN GURU -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chalkboard-user"></i> Manajemen Data Guru</h3>
                    <button class="btn btn-success" onclick="showTambahGuru()">
                        <i class="fas fa-plus"></i> Tambah Guru
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>Gender</th>
                                    <th>Umur</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dataGuru as $guru): ?>
                                <tr>
                                    <td><?= htmlspecialchars($guru['username'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars(($guru['first_name'] ?? '') . ' ' . ($guru['last_name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars($guru['gender'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($guru['age'] ?? '-') ?></td>
                                    <td class="action-buttons">
                                        <button class="btn-icon btn-edit" onclick="editGuru(<?= $guru['teacher_id'] ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn-icon btn-delete" onclick="hapusGuru(<?= $guru['teacher_id'] ?>)">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (count($dataGuru) == 0): ?>
                                    <tr><td colspan="5" style="text-align: center;">Belum ada data guru. Klik "Tambah Guru" untuk menambahkan.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<!-- ========== MODAL FORM SISWA - DESAIN KEREN ========== -->
<div id="modalSiswa" class="modal">
    <div class="modal-content modal-modern">
        <div class="modal-header modern-header">
            <div class="header-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="header-text">
                <h3 id="modalSiswaTitle">Tambah Siswa</h3>
                <p>Isi data siswa dengan lengkap</p>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('modalSiswa')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="formSiswa" method="POST" action="proses_siswa.php">
            <input type="hidden" name="action" id="siswaAction" value="create">
            <input type="hidden" name="student_id" id="student_id">
            
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group-modern">
                            <label><i class="fas fa-id-card"></i> NIS</label>
                            <input type="text" name="nis" id="nis" class="form-control-modern" placeholder="Masukkan NIS" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group-modern">
                            <label><i class="fas fa-tag"></i> Tag ID / RFID</label>
                            <input type="text" name="tag_id" id="tag_id" class="form-control-modern" placeholder="Scan kartu RFID">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group-modern">
                            <label><i class="fas fa-user"></i> Nama Depan</label>
                            <input type="text" name="first_name" id="first_name" class="form-control-modern" placeholder="Nama depan" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group-modern">
                            <label><i class="fas fa-user"></i> Nama Belakang</label>
                            <input type="text" name="last_name" id="last_name" class="form-control-modern" placeholder="Nama belakang (opsional)">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group-modern">
                            <label><i class="fas fa-school"></i> Kelas</label>
                            <input type="text" name="class" id="class" class="form-control-modern" placeholder="Contoh: 6A, 7B" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group-modern">
                            <label><i class="fas fa-calendar-alt"></i> Umur</label>
                            <input type="number" name="age" id="age" class="form-control-modern" placeholder="Umur siswa" min="4" max="20">
                        </div>
                    </div>
                </div>

                <div class="form-group-modern">
                    <label><i class="fas fa-users"></i> Nama Orang Tua / Wali</label>
                    <input type="text" name="parent" id="parent" class="form-control-modern" placeholder="Contoh: Bapak/Ibu ...">
                </div>
            </div>

            <div class="modal-footer modern-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modalSiswa')">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========== MODAL FORM GURU - DESAIN KEREN ========== -->
<div id="modalGuru" class="modal">
    <div class="modal-content modal-modern">
        <div class="modal-header modern-header">
            <div class="header-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="header-text">
                <h3 id="modalGuruTitle">Tambah Guru</h3>
                <p>Isi data guru dengan lengkap</p>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('modalGuru')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="formGuru" method="POST" action="proses_guru.php">
            <input type="hidden" name="action" id="guruAction" value="create">
            <input type="hidden" name="teacher_id" id="teacher_id">
            
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group-modern">
                            <label><i class="fas fa-user-circle"></i> Username</label>
                            <input type="text" name="username" id="guru_username" class="form-control-modern" placeholder="Username untuk login" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group-modern">
                            <label><i class="fas fa-lock"></i> Password</label>
                            <input type="password" name="password" id="guru_password" class="form-control-modern" placeholder="Minimal 4 karakter">
                            <small class="form-hint">Kosongkan jika tidak ingin mengubah</small>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group-modern">
                            <label><i class="fas fa-user"></i> Nama Depan</label>
                            <input type="text" name="first_name" id="guru_first_name" class="form-control-modern" placeholder="Nama depan" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group-modern">
                            <label><i class="fas fa-user"></i> Nama Belakang</label>
                            <input type="text" name="last_name" id="guru_last_name" class="form-control-modern" placeholder="Nama belakang (opsional)">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group-modern">
                            <label><i class="fas fa-venus-mars"></i> Gender</label>
                            <select name="gender" id="guru_gender" class="form-control-modern">
                                <option value="L">👨 Laki-laki</option>
                                <option value="P">👩 Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group-modern">
                            <label><i class="fas fa-calendar-alt"></i> Umur</label>
                            <input type="number" name="age" id="guru_age" class="form-control-modern" placeholder="Umur guru" min="20" max="60">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer modern-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('modalGuru')">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ========== SISWA CRUD ==========
function showTambahSiswa() {
    document.getElementById('modalSiswaTitle').innerText = 'Tambah Siswa';
    document.getElementById('siswaAction').value = 'create';
    document.getElementById('formSiswa').reset();
    document.getElementById('student_id').value = '';
    document.getElementById('modalSiswa').style.display = 'block';
}

function editSiswa(id) {
    fetch('proses_siswa.php?action=get&id=' + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalSiswaTitle').innerText = 'Edit Siswa';
            document.getElementById('siswaAction').value = 'update';
            document.getElementById('student_id').value = data.student_id;
            document.getElementById('nis').value = data.nis;
            document.getElementById('first_name').value = data.first_name;
            document.getElementById('last_name').value = data.last_name || '';
            document.getElementById('class').value = data.class || '';
            document.getElementById('parent').value = data.parent || '';
            document.getElementById('tag_id').value = data.tag_id || '';
            document.getElementById('age').value = data.age || '';
            document.getElementById('modalSiswa').style.display = 'block';
        })
        .catch(error => {
            alert('Gagal mengambil data siswa: ' + error);
        });
}

function hapusSiswa(id) {
    if (confirm('Yakin ingin menghapus siswa ini?')) {
        window.location.href = 'proses_siswa.php?action=delete&id=' + id;
    }
}

// ========== GURU CRUD ==========
function showTambahGuru() {
    document.getElementById('modalGuruTitle').innerText = 'Tambah Guru';
    document.getElementById('guruAction').value = 'create';
    document.getElementById('formGuru').reset();
    document.getElementById('teacher_id').value = '';
    document.getElementById('guru_password').required = false;
    document.getElementById('modalGuru').style.display = 'block';
}

function editGuru(id) {
    fetch('proses_guru.php?action=get&id=' + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalGuruTitle').innerText = 'Edit Guru';
            document.getElementById('guruAction').value = 'update';
            document.getElementById('teacher_id').value = data.teacher_id;
            document.getElementById('guru_username').value = data.username;
            document.getElementById('guru_first_name').value = data.first_name;
            document.getElementById('guru_last_name').value = data.last_name || '';
            document.getElementById('guru_gender').value = data.gender || 'L';
            document.getElementById('guru_age').value = data.age || '';
            document.getElementById('guru_password').value = '';
            document.getElementById('modalGuru').style.display = 'block';
        })
        .catch(error => {
            alert('Gagal mengambil data guru: ' + error);
        });
}

function hapusGuru(id) {
    if (confirm('Yakin ingin menghapus guru ini?')) {
        window.location.href = 'proses_guru.php?action=delete&id=' + id;
    }
}

// ========== MODAL UTILITY ==========
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// ========== PESAN SUKSES/ERROR ==========
<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] == 'created'): ?>
        alert('✅ Data berhasil ditambahkan!');
    <?php elseif ($_GET['msg'] == 'updated'): ?>
        alert('✅ Data berhasil diupdate!');
    <?php elseif ($_GET['msg'] == 'deleted'): ?>
        alert('✅ Data berhasil dihapus!');
    <?php endif; ?>
    window.history.replaceState({}, document.title, window.location.pathname + '?tab=<?= $tab ?>');
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    alert('❌ Error: <?= htmlspecialchars($_GET['error']) ?>');
    window.history.replaceState({}, document.title, window.location.pathname + '?tab=<?= $tab ?>');
<?php endif; ?>
</script>
</body>
</html>