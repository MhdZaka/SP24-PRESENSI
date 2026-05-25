<?php
require_once 'config.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/GuruController.php';
require_once 'controllers/PublicController.php';

$route = $_GET['route'] ?? 'auth/login';

$authController = new AuthController();
$adminController = new AdminController();
$guruController = new GuruController();
$publicController = new PublicController();

switch ($route) {
    case 'auth/login':
        $authController->login();
        break;
    case 'auth/logout':
        $authController->logout();
        break;
    case 'admin/dashboard':
        $adminController->dashboard();
        break;
    case 'admin/prosesSiswa':
        $adminController->prosesSiswa();
        break;
    case 'admin/prosesGuru':
        $adminController->prosesGuru();
        break;
    case 'admin/uploadFotoSiswa':
        $adminController->uploadFotoSiswa();
        break;
    case 'admin/uploadFotoGuru':
        $adminController->uploadFotoGuru();
        break;
    case 'guru/dashboard':
        $guruController->dashboard();
        break;
    case 'public/cekAnak':
        $publicController->cekAnak();
        break;
    case 'public/serveFoto':
        $publicController->serveFoto();
        break;
    default:
        $authController->login();
        break;
}