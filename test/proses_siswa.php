<?php
require_once 'config.php';
redirectIfNotLoggedIn();

if ($_SESSION['user']['role'] != 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ========== CREATE SISWA ==========
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
    
    $result = apiRequest('/students', 'POST', $data, true);
    
    if ($result['status'] == 201 || $result['status'] == 200) {
        header('Location: dashboard-admin.php?tab=siswa&msg=created');
    } else {
        $error = $result['data']['message'] ?? 'Gagal menambah siswa';
        header('Location: dashboard-admin.php?tab=siswa&error=' . urlencode($error));
    }
    exit();
}

// ========== UPDATE SISWA ==========
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
    
    $result = apiRequest('/students/' . $id, 'PUT', $data, true);
    
    if ($result['status'] == 200) {
        header('Location: dashboard-admin.php?tab=siswa&msg=updated');
    } else {
        $error = $result['data']['message'] ?? 'Gagal update siswa';
        header('Location: dashboard-admin.php?tab=siswa&error=' . urlencode($error));
    }
    exit();
}

// ========== DELETE SISWA ==========
if ($action == 'delete' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = $_GET['id'];
    $result = apiRequest('/students/' . $id, 'DELETE', null, true);
    
    if ($result['status'] == 200) {
        header('Location: dashboard-admin.php?tab=siswa&msg=deleted');
    } else {
        $error = $result['data']['message'] ?? 'Gagal hapus siswa';
        header('Location: dashboard-admin.php?tab=siswa&error=' . urlencode($error));
    }
    exit();
}

// ========== GET SISWA BY ID (untuk edit form) ==========
if ($action == 'get' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = $_GET['id'];
    $result = apiRequest('/students/' . $id, 'GET', null, true);
    
    header('Content-Type: application/json');
    echo json_encode($result['data']['data'] ?? []);
    exit();
}

header('Location: dashboard-admin.php?tab=siswa');
exit();
?>