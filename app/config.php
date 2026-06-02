<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

define('APP_NAME', 'SP24 Presensi');
$isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
           (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$protocol = $isHttps ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

$isTunnel = strpos($host, 'devtunnels.ms') !== false || 
            strpos($host, 'vscode.dev') !== false ||
            strpos($host, '127.0.0.1') !== false && $_SERVER['SERVER_PORT'] != 80;

$basePath = (strpos($host, 'localhost') !== false) ? '/sp24-presensi' : '';
define('APP_URL', $protocol . $host . $basePath);

if ($isTunnel || strpos($host, 'localhost') !== false) {
    define('APP_MODE', 'local');
} else {
    define('APP_MODE', 'production');
}

if (APP_MODE == 'local') {
    define('API_BASE_URL', 'https://sp24api.wind.my.id/api');
} else {
    define('API_BASE_URL', 'https://sp24api.wind.my.id/api');
}

define('UPLOAD_PATH', dirname(__DIR__) . '/public/uploads/');
define('UPLOAD_PATH_SISWA', UPLOAD_PATH . 'siswa/');
define('UPLOAD_PATH_GURU', UPLOAD_PATH . 'guru/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024);

if (!file_exists(UPLOAD_PATH_SISWA)) mkdir(UPLOAD_PATH_SISWA, 0777, true);
if (!file_exists(UPLOAD_PATH_GURU)) mkdir(UPLOAD_PATH_GURU, 0777, true);



function isLoggedIn() {
    return isset($_SESSION['access_token']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/app/');
        exit();
    }
}

function isAdmin() {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 'admin';
}

function isGuru() {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 'guru';
}


?>