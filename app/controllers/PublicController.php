<?php
require_once __DIR__ . '/../models/ApiModel.php';

class PublicController {
    public function cekAnak() {
        $nis = $_GET['nis'] ?? '';
        $result = null;
        $siswaInfo = null;
        $error = '';

        if ($nis) {
            $response = ApiModel::request('/presences/parent/' . $nis, 'GET', null, false);
            
            if ($response['status'] == 200 && isset($response['data']['success']) && $response['data']['success'] == true) {
                $data = $response['data']['data'];
                
                $siswaInfo = [
                    'nis' => $data['nis'] ?? $nis,
                    'first_name' => $data['first_name'] ?? '',
                    'last_name' => $data['last_name'] ?? '',
                    'class' => $data['class'] ?? '',
                    'parent' => $data['parent'] ?? ''
                ];
                
                if (isset($data['presences']) && is_array($data['presences'])) {
                    $result = $data['presences'];
                }
                
                if (empty($result)) {
                    $error = 'Tidak ada riwayat absensi untuk NIS ini.';
                }
            } else {
                $error = $response['data']['message'] ?? 'Gagal mengambil data. Periksa kembali NIS.';
            }
        }
        
        require_once __DIR__ . '/../views/public/cek-anak.php';
    }

    public function serveFoto() {
        if (!isLoggedIn()) {
            header("HTTP/1.0 403 Forbidden");
            exit;
        }

        $type = $_GET['type'] ?? '';
        $id = $_GET['id'] ?? '';

        if (!in_array($type, ['siswa', 'guru']) || empty($id)) {
            header("HTTP/1.0 404 Not Found");
            exit;
        }

        $dir = $type == 'siswa' ? UPLOAD_PATH_SISWA : UPLOAD_PATH_GURU;
        $pattern = $dir . $type . '_' . $id . '_*.*';
        $files = glob($pattern);

        if (empty($files)) {
            header("HTTP/1.0 404 Not Found");
            exit;
        }

        $file = $files[0];
        $mime = mime_content_type($file);

        header("Content-Type: $mime");
        header("Content-Length: " . filesize($file));
        readfile($file);
        exit;
    }
}
