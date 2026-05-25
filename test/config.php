<?php
session_start();

// ======================== KONFIGURASI ========================
define('APP_MODE', 'production');   // 'local' atau 'production'

if (APP_MODE == 'local') {
    define('API_BASE_URL', 'http://localhost:3000/api');
} else {
    define('API_BASE_URL', 'https://sp24api.wind.my.id/api');
}
// =============================================================

function apiRequest($endpoint, $method = 'GET', $data = null, $useAuth = true) {
    $url = API_BASE_URL . $endpoint;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'SP24-Presensi/1.0');
    
    $headers = ['Content-Type: application/json'];
    if ($useAuth && isset($_SESSION['access_token'])) {
        $headers[] = 'Authorization: Bearer ' . $_SESSION['access_token'];
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method == 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method == 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    } elseif ($method == 'GET' && $data) {
        $url .= '?' . http_build_query($data);
        curl_setopt($ch, CURLOPT_URL, $url);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        file_put_contents('curl_error.log', date('Y-m-d H:i:s') . ' - ' . $curlError . PHP_EOL, FILE_APPEND);
    }
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true),
        'curl_error' => $curlError
    ];
}

// ✅ LOGIN ADMIN
function loginAdmin($username, $password) {
    $result = apiRequest('/auth/admin/login', 'POST', [
        'username' => $username,
        'password' => $password
    ], false);
    
    if ($result['status'] == 200 && isset($result['data']['data']['accessToken'])) {
        $data = $result['data']['data'];
        $token = $data['accessToken'];
        $admin = $data['admin'];
        
        $_SESSION['access_token'] = $token;
        $_SESSION['user'] = [
            'id' => $admin['admin_id'],
            'username' => $admin['username'],
            'role' => 'admin',
            'first_name' => $admin['username']
        ];
        
        return true;
    }
    return false;
}

// ✅ LOGIN GURU
function loginGuru($username, $password) {
    $result = apiRequest('/auth/teacher/login', 'POST', [
        'username' => $username,
        'password' => $password
    ], false);
    
    if ($result['status'] == 200 && isset($result['data']['data']['accessToken'])) {
        $data = $result['data']['data'];
        $token = $data['accessToken'];
        $teacher = $data['teacher'];
        
        $_SESSION['access_token'] = $token;
        $_SESSION['user'] = [
            'id' => $teacher['teacher_id'],
            'username' => $teacher['username'],
            'role' => 'guru',
            'first_name' => $teacher['first_name'] ?? $teacher['username']
        ];
        
        return true;
    }
    return false;
}

function isLoggedIn() {
    return isset($_SESSION['access_token']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>