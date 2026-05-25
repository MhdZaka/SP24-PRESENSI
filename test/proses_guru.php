<?php
require_once 'config.php';
redirectIfNotLoggedIn();

if ($_SESSION['user']['role'] != 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ========== CREATE GURU ==========
if ($action == 'create' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'username' => $_POST['username'],
        'password' => $_POST['password'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'gender' => $_POST['gender'],
        'age' => (int)$_POST['age']
    ];
    
    $result = apiRequest('/teachers', 'POST', $data, true);
    
    if ($result['status'] == 201 || $result['status'] == 200) {
        header('Location: dashboard-admin.php?tab=guru&msg=created');
    } else {
        $error = $result['data']['message'] ?? 'Gagal menambah guru';
        header('Location: dashboard-admin.php?tab=guru&error=' . urlencode($error));
    }
    exit();
}

// ========== UPDATE GURU ==========
if ($action == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['teacher_id'];
    $data = [
        'username' => $_POST['username'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'gender' => $_POST['gender'],
        'age' => (int)$_POST['age']
    ];
    
    // Only include password if provided
    if (!empty($_POST['password'])) {
        $data['password'] = $_POST['password'];
    }
    
    $result = apiRequest('/teachers/' . $id, 'PUT', $data, true);
    
    if ($result['status'] == 200) {
        header('Location: dashboard-admin.php?tab=guru&msg=updated');
    } else {
        $error = $result['data']['message'] ?? 'Gagal update guru';
        header('Location: dashboard-admin.php?tab=guru&error=' . urlencode($error));
    }
    exit();
}

// ========== DELETE GURU ==========
if ($action == 'delete' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = $_GET['id'];
    $result = apiRequest('/teachers/' . $id, 'DELETE', null, true);
    
    if ($result['status'] == 200) {
        header('Location: dashboard-admin.php?tab=guru&msg=deleted');
    } else {
        $error = $result['data']['message'] ?? 'Gagal hapus guru';
        header('Location: dashboard-admin.php?tab=guru&error=' . urlencode($error));
    }
    exit();
}

// ========== GET GURU BY ID (untuk edit form) ==========
if ($action == 'get' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = $_GET['id'];
    $result = apiRequest('/teachers/' . $id, 'GET', null, true);
    
    header('Content-Type: application/json');
    echo json_encode($result['data']['data'] ?? []);
    exit();
}

header('Location: dashboard-admin.php?tab=guru');
exit();
?>