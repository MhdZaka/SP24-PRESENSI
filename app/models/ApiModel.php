<?php

class ApiModel {
    protected static function request($endpoint, $method = 'GET', $data = null, $useAuth = true) {
        $cacheKey = 'api_cache_' . md5($endpoint);
        if ($method === 'GET') {
            if (isset($_SESSION[$cacheKey]) && (time() - $_SESSION[$cacheKey]['time']) < 60) {
                return $_SESSION[$cacheKey]['response'];
            }
        } else {
            foreach ($_SESSION as $key => $value) {
                if (strpos($key, 'api_cache_') === 0) {
                    unset($_SESSION[$key]);
                }
            }
        }

        $url = API_BASE_URL . $endpoint;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        
        $headers = ['Content-Type: application/json'];
        if ($useAuth && isset($_SESSION['access_token'])) {
            $headers[] = 'Authorization: Bearer ' . $_SESSION['access_token'];
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrNo = curl_errno($ch);
        curl_close($ch);
        
        // Log curl errors
        if ($curlErrNo != 0) {
            error_log('cURL Error #' . $curlErrNo . ': ' . $curlError . ' - Endpoint: ' . $endpoint);
        }
        
        if ($response === false) {
            $response = json_encode([
                'status' => false,
                'message' => 'cURL Error: ' . ($curlError ?: 'Unknown error')
            ]);
        }
        
        if ($useAuth && $httpCode == 401) {
            session_destroy();
            header('Location: ' . APP_URL . '/app/');
            exit();
        }

        $result = [
            'status' => $httpCode,
            'data' => json_decode($response, true)
        ];

        if ($method === 'GET' && $httpCode === 200) {
            $_SESSION[$cacheKey] = [
                'time' => time(),
                'response' => $result
            ];
        }
        
        return $result;
    }
}
