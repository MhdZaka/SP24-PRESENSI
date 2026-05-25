<?php
require_once __DIR__ . '/../models/StudentModel.php';
require_once __DIR__ . '/../models/TeacherModel.php';
require_once __DIR__ . '/../models/PresenceModel.php';

class GuruController {
    public function dashboard() {
        if (!isLoggedIn()) {
            header('Location: ' . APP_URL . '/app/');
            exit();
        }

        $students = StudentModel::getAll();
        $teachers = TeacherModel::getAll();
        $presences = PresenceModel::getAll();

        $dataSiswa = $students['data']['data'] ?? [];
        $dataGuru = $teachers['data']['data'] ?? [];
        $dataPresensi = $presences['data']['data'] ?? [];

        $totalSiswa = count($dataSiswa);
        $totalGuru = count($dataGuru);
        $hadirHariIni = 0;
        $hariIni = date('Y-m-d');
        foreach ($dataPresensi as $p) {
            if (strpos($p['enter'], $hariIni) === 0) $hadirHariIni++;
        }

        $tab = $_GET['tab'] ?? 'dashboard';

        require_once __DIR__ . '/../views/guru/dashboard.php';
    }
}
