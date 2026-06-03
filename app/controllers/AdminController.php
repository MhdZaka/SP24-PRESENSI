<?php
require_once __DIR__ . '/../models/StudentModel.php';
require_once __DIR__ . '/../models/TeacherModel.php';
require_once __DIR__ . '/../models/PresenceModel.php';

class AdminController {
    public function dashboard() {
        if (!isAdmin()) {
            header('Location: ' . APP_URL . '/app/index.php?route=guru/dashboard');
            exit();
        }

        $students = StudentModel::getAll();
        $teachers = TeacherModel::getAll();
        $presences = PresenceModel::getAll();

        $dataSiswa = $students['data']['data'] ?? [];
        $dataGuru = $teachers['data']['data'] ?? [];
        $totalSiswa = count($dataSiswa);
        $totalGuru = count($dataGuru);

        $dataPresensi = $presences['data']['data'] ?? [];
        $hadirHariIni = 0;
        $hariIni = date('Y-m-d');
        foreach ($dataPresensi as $p) {
            if (strpos($p['enter'], $hariIni) === 0) $hadirHariIni++;
        }

        $tab = $_GET['tab'] ?? 'dashboard';
        
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    public function prosesSiswa() {
        if (!isAdmin()) exit();
        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        if ($action == 'delete' && $_SERVER['REQUEST_METHOD'] == 'GET') {
            $id = $_GET['id'];
            $result = StudentModel::delete($id);
            if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 200, 'data' => ['success' => true]]);
                exit();
            }
            header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=siswa');
            exit();
        }

        if ($action == 'create' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nis' => $_POST['nis'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'class' => $_POST['class'],
                'parent' => $_POST['parent'],
                'tag_id' => $_POST['tag_id'],
                'age' => (int)$_POST['age']
            ];
            $result = StudentModel::create($data);
            if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            if ($result['status'] >= 200 && $result['status'] < 300) {
                if (isset($result['data']['success']) && $result['data']['success'] == false) {
                    $errorMsg = urlencode($result['data']['message'] ?? 'Gagal menambah siswa.');
                    header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=siswa&error=' . $errorMsg);
                    exit();
                }
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=siswa&msg=added');
            } else {
                $errorMsg = urlencode($result['data']['message'] ?? 'Terjadi kesalahan pada server.');
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=siswa&error=' . $errorMsg);
            }
            exit();
        }

        if ($action == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['student_id'];
            $data = [
                'nis' => $_POST['nis'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'class' => $_POST['class'],
                'parent' => $_POST['parent'],
                'tag_id' => $_POST['tag_id'],
                'age' => (int)$_POST['age']
            ];
            $result = StudentModel::update($id, $data);
            if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            if ($result['status'] >= 200 && $result['status'] < 300) {
                if (isset($result['data']['success']) && $result['data']['success'] == false) {
                    $errorMsg = urlencode($result['data']['message'] ?? 'Gagal mengedit siswa.');
                    header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=siswa&error=' . $errorMsg);
                    exit();
                }
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=siswa&msg=updated');
            } else {
                $errorMsg = urlencode($result['data']['message'] ?? 'Terjadi kesalahan pada server.');
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=siswa&error=' . $errorMsg);
            }
            exit();
        }

        if ($action == 'get' && $_SERVER['REQUEST_METHOD'] == 'GET') {
            $id = $_GET['id'];
            $result = StudentModel::getById($id);
            header('Content-Type: application/json');
            echo json_encode($result['data']['data'] ?? []);
            exit();
        }

        header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=siswa');
        exit();
    }

    public function prosesGuru() {
        if (!isAdmin()) exit();
        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        if ($action == 'delete' && $_SERVER['REQUEST_METHOD'] == 'GET') {
            $id = $_GET['id'];
            $result = TeacherModel::delete($id);
            if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 200, 'data' => ['success' => true]]);
                exit();
            }
            header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru');
            exit();
        }

        if ($action == 'create' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'age' => (int)$_POST['age'],
                'gender' => $_POST['gender']
            ];
            $result = TeacherModel::create($data);
            if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            if ($result['status'] >= 200 && $result['status'] < 300) {
                if (isset($result['data']['success']) && $result['data']['success'] == false) {
                    $errorMsg = urlencode($result['data']['message'] ?? 'Gagal menambah guru.');
                    header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru&error=' . $errorMsg);
                    exit();
                }
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru&msg=added');
            } else {
                $errorMsg = urlencode($result['data']['message'] ?? 'Terjadi kesalahan pada server.');
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru&error=' . $errorMsg);
            }
            exit();
        }

        if ($action == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['teacher_id'];
            $data = [
                'username' => $_POST['username'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'age' => (int)$_POST['age'],
                'gender' => $_POST['gender']
            ];
            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }
            $result = TeacherModel::update($id, $data);
            if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            if ($result['status'] >= 200 && $result['status'] < 300) {
                if (isset($result['data']['success']) && $result['data']['success'] == false) {
                    $errorMsg = urlencode($result['data']['message'] ?? 'Gagal mengedit guru.');
                    header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru&error=' . $errorMsg);
                    exit();
                }
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru&msg=updated');
            } else {
                $errorMsg = urlencode($result['data']['message'] ?? 'Terjadi kesalahan pada server.');
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru&error=' . $errorMsg);
            }
            exit();
        }

        if ($action == 'get' && $_SERVER['REQUEST_METHOD'] == 'GET') {
            $id = $_GET['id'];
            $result = TeacherModel::getById($id);
            header('Content-Type: application/json');
            echo json_encode($result['data']['data'] ?? []);
            exit();
        }

        header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru');
        exit();
    }

    public function uploadFotoSiswa() {
        if (!isAdmin()) exit();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['photo'])) {
            $id = $_POST['student_id'];
            $file = $_FILES['photo'];
            
            $uploadDir = UPLOAD_PATH_SISWA;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'siswa_' . $id . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 200, 'data' => ['success' => true]]);
                    exit();
                }
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=siswa&msg=photo_uploaded');
            } else {
                if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 400, 'data' => ['message' => 'Gagal upload foto']]);
                    exit();
                }
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=siswa&error=Gagal upload foto');
            }
            exit();
        }
        header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=siswa');
        exit();
    }

    public function uploadFotoGuru() {
        if (!isAdmin()) exit();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['photo'])) {
            $id = $_POST['teacher_id'];
            $file = $_FILES['photo'];
            
            $uploadDir = UPLOAD_PATH_GURU;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'guru_' . $id . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 200, 'data' => ['success' => true]]);
                    exit();
                }
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru&msg=photo_uploaded');
            } else {
                if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 400, 'data' => ['message' => 'Gagal upload foto']]);
                    exit();
                }
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru&error=Gagal upload foto');
            }
            exit();
        }
        header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru');
        exit();
    }
}
