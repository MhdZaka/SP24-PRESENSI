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
            StudentModel::delete($id);
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
            StudentModel::create($data);
            header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=siswa');
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
            StudentModel::update($id, $data);
            header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=siswa');
            exit();
        }

        if ($action == 'get' && $_SERVER['REQUEST_METHOD'] == 'GET') {
            $id = $_GET['id'];
            $result = StudentModel::request('/students/' . $id, 'GET');
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
            TeacherModel::delete($id);
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
            TeacherModel::create($data);
            header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru');
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
            TeacherModel::update($id, $data);
            header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru');
            exit();
        }

        if ($action == 'get' && $_SERVER['REQUEST_METHOD'] == 'GET') {
            $id = $_GET['id'];
            $result = TeacherModel::request('/teachers/' . $id, 'GET');
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
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=siswa&msg=photo_uploaded');
            } else {
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
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru&msg=photo_uploaded');
            } else {
                header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru&error=Gagal upload foto');
            }
            exit();
        }
        header('Location: ' . APP_URL . '/app/index.php?route=admin/dashboard&tab=guru');
        exit();
    }
}
