<?php
require_once 'config.php';

if (isLoggedIn()) {
    $role = $_SESSION['user']['role'] ?? 'guru';
    header('Location: ' . ($role == 'admin' ? 'dashboard-admin.php' : 'dashboard-guru.php'));
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    if ($role == 'admin') {
        $loginSuccess = loginAdmin($username, $password);
    } else {
        $loginSuccess = loginGuru($username, $password);
    }
    
    if ($loginSuccess) {
        header('Location: ' . ($role == 'admin' ? 'dashboard-admin.php' : 'dashboard-guru.php'));
        exit();
    } else {
        $error = 'Login gagal! Periksa username/password atau role.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SP24 Presensi - Sistem Absensi Sekolah</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-brand">
                <div class="brand-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h1>SP24 Presensi</h1>
                <p>Sistem Absensi Digital Sekolah</p>
            </div>

            <div class="login-card">
                <div class="login-header">
                    <h2>Login SP24</h2>
                    <p>Masuk sebagai Admin atau Guru</p>
                </div>

                <div class="login-body">
                    <?php if ($error): ?>
                        <div class="alert-error">
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="login-form">
                        <div class="input-group">
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <input type="text" name="username" placeholder="Username" class="login-input" required>
                        </div>

                        <div class="input-group">
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" name="password" placeholder="Password" class="login-input" required>
                        </div>

                        <div class="input-group">
                            <div class="input-icon">
                                <i class="fas fa-user-tag"></i>
                            </div>
                            <select name="role" class="login-input" required>
                                <option value="guru">📖 Login sebagai Guru</option>
                                <option value="admin">👑 Login sebagai Administrator</option>
                            </select>
                        </div>

                        <button type="submit" class="login-btn">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </form>

                    <div class="login-footer">
                        <a href="cek-anak.php" class="cek-anak-link">
                            <i class="fas fa-child"></i> Cek Absensi Anak
                        </a>
                    </div>
                </div>
            </div>

            <div class="login-copyright">
                <p>&copy; 2025 SP24 Presensi - Sistem Absensi Digital</p>
            </div>
        </div>
    </div>
</body>
</html>