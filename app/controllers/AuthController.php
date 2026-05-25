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
                $error = 'Login gagal! Periksa username/password atau role.';
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
