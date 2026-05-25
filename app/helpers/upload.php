<?php
function uploadFoto($file, $type, $id) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload gagal'];
    }
    
    $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    if (!in_array($file['type'], $allowed)) {
        return ['success' => false, 'message' => 'Format harus JPG/PNG/WEBP'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'Ukuran maksimal 2MB'];
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $type . '_' . $id . '_' . time() . '.' . $ext;
    $targetPath = ($type == 'siswa') ? UPLOAD_PATH_SISWA . $filename : UPLOAD_PATH_GURU . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'path' => 'uploads/' . $type . '/' . $filename];
    }
    
    return ['success' => false, 'message' => 'Gagal menyimpan file'];
}
?>