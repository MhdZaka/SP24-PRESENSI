<?php

class ApiModel {
    protected static function request($endpoint, $method = 'GET', $data = null, $useAuth = true, $isRetry = false) {
        $cacheKey = 'api_cache_' . md5($endpoint);
        if ($method === 'GET' && !$isRetry) {
            if (isset($_SESSION[$cacheKey]) && (time() - $_SESSION[$cacheKey]['time']) < 60) {
                return $_SESSION[$cacheKey]['response'];
            }
        } elseif (!$isRetry) {
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
        
        if (isset($_SESSION['api_cookies'])) {
            $cookieStr = [];
            foreach ($_SESSION['api_cookies'] as $k => $v) {
                $cookieStr[] = "$k=$v";
            }
            curl_setopt($ch, CURLOPT_COOKIE, implode('; ', $cookieStr));
        }

        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $header) {
            if (stripos($header, 'Set-Cookie:') === 0) {
                if (preg_match('/^Set-Cookie:\s*([^;]+)/', $header, $matches)) {
                    parse_str(strtr($matches[1], ['&' => '%26', '+' => '%2B', ';' => '&']), $cookies);
                    if (!isset($_SESSION['api_cookies'])) $_SESSION['api_cookies'] = [];
                    foreach ($cookies as $k => $v) {
                        $_SESSION['api_cookies'][$k] = $v;
                    }
                }
            }
            return strlen($header);
        });
        
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
        
        if ($useAuth && $httpCode == 401 && !$isRetry && isset($_SESSION['api_cookies']['refreshToken'])) {
            $refreshResult = self::request('/auth/refresh-token', 'POST', null, false, true);
            if ($refreshResult['status'] == 200 && isset($refreshResult['data']['data']['accessToken'])) {
                $_SESSION['access_token'] = $refreshResult['data']['data']['accessToken'];
                return self::request($endpoint, $method, $data, $useAuth, true);
            }
        }

        if ($useAuth && $httpCode == 401) {
            session_destroy();
            
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            $isFetch = isset($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] === 'empty';
            
            if ($isAjax || $isFetch) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Sesi login telah berakhir. Silakan muat ulang dan login kembali.']);
                exit();
            }
            
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
