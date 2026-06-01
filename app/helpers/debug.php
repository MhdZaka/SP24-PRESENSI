<?php
/**
 * Debug Helper untuk diagnosa masalah login dan API
 */

function testApiConnection() {
    $results = [];
    
    // Test 1: Check API_BASE_URL
    $results['api_base_url'] = API_BASE_URL;
    
    // Test 2: Check if cURL is enabled
    $results['curl_enabled'] = extension_loaded('curl');
    
    // Test 3: Check if allow_url_fopen is enabled
    $results['allow_url_fopen'] = ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled';
    
    // Test 4: Try to connect to API base
    $testUrl = API_BASE_URL . 'auth/admin/login';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['username' => 'test', 'password' => 'test']));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    $results['api_test_url'] = $testUrl;
    $results['api_test_http_code'] = $httpCode;
    $results['api_test_response_length'] = strlen($response);
    $results['api_test_curl_error'] = $curlError ?: 'None';
    $results['api_test_response'] = substr($response, 0, 200);
    
    // Test 5: Check file permissions
    $uploadPath = UPLOAD_PATH_SISWA;
    $results['upload_dir_exists'] = file_exists($uploadPath);
    $results['upload_dir_writable'] = is_writable($uploadPath);
    
    return $results;
}

function displayDebugInfo() {
    $info = testApiConnection();
    
    echo '<div style="background: #f5f5f5; padding: 20px; border-radius: 5px; margin: 20px 0; font-family: monospace; font-size: 12px;">';
    echo '<h3>🔧 API Debug Info</h3>';
    echo '<table style="width: 100%; border-collapse: collapse;">';
    
    foreach ($info as $key => $value) {
        $bgColor = strpos($key, 'error') !== false && $value ? '#ffcccc' : '#ffffff';
        echo '<tr style="border-bottom: 1px solid #ddd;">';
        echo '<td style="padding: 8px; background: ' . $bgColor . '; font-weight: bold; width: 30%;">' . ucfirst(str_replace('_', ' ', $key)) . ':</td>';
        echo '<td style="padding: 8px; background: ' . $bgColor . '">';
        
        if (is_array($value)) {
            echo json_encode($value);
        } elseif (is_bool($value)) {
            echo $value ? '✓ Yes' : '✗ No';
        } else {
            echo htmlspecialchars($value);
        }
        
        echo '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    echo '</div>';
}
?>
