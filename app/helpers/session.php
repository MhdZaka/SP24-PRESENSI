<?php
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

function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

function logout() {
    session_destroy();
    header('Location: ' . APP_URL . '/app/');
    exit();
}
?>