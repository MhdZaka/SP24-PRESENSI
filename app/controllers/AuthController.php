<?php
require_once __DIR__ . '/../models/TeacherModel.php';

class AuthController {
    public function login() {
        if (isLoggedIn()) {
            $role = $_SESSION['user']['role'] ?? 'guru';
            header('Location: ' . APP_URL . '/app/index.php?route=' . ($role == 'admin' ? 'admin/dashboard' : 'guru/dashboard'));
            exit();
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'guru';
            
            if ($role == 'admin') {
                $result = TeacherModel::loginAdmin($username, $password);
            } else {
                $result = TeacherModel::loginGuru($username, $password);
            }
            
            // Debug: Log response
            error_log('Login attempt - Role: ' . $role . ', Response: ' . json_encode($result));
            
            if ($result['status'] == 200 && isset($result['data']['data']['accessToken'])) {
                $data = $result['data']['data'];
                $_SESSION['access_token'] = $data['accessToken'];
                
                if ($role == 'admin') {
                    $_SESSION['user'] = [
                        'id' => $data['admin']['admin_id'],
                        'username' => $data['admin']['username'],
                        'role' => 'admin',
                        'first_name' => $data['admin']['username']
                    ];
                } else {
                    $_SESSION['user'] = [
                        'id' => $data['teacher']['teacher_id'],
                        'username' => $data['teacher']['username'],
                        'role' => 'guru',
                        'first_name' => $data['teacher']['first_name'] ?? $data['teacher']['username']
                    ];
                }
                header('Location: ' . APP_URL . '/app/index.php?route=' . ($role == 'admin' ? 'admin/dashboard' : 'guru/dashboard'));
                exit();
            } else {
                // Provide more detailed error message
                $errorMsg = 'Login gagal! ';
                if (isset($result['data']['message'])) {
                    $errorMsg .= $result['data']['message'];
                } elseif ($result['status'] == 0) {
                    $errorMsg .= 'Koneksi ke server API gagal. Pastikan server API berjalan.';
                } elseif ($result['status'] == 401 || $result['status'] == 403) {
                    $errorMsg .= 'Username atau password salah.';
                } elseif ($result['status'] >= 500) {
                    $errorMsg .= 'Server API sedang bermasalah. Coba lagi nanti.';
                } else {
                    $errorMsg .= 'Periksa username/password atau role. (Status: ' . $result['status'] . ')';
                }
                $error = $errorMsg;
            }
        }
        
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: ' . APP_URL . '/app/');
        exit();
    }
}
