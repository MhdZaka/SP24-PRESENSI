<?php
$url = 'https://sp24api.wind.my.id/api/auth/admin/login';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['username' => 'admin', 'password' => 'admin']));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "<br>";
echo "CURL Error: " . ($curlError ?: 'Tidak ada') . "<br>";
echo "Response: " . ($response ?: 'Kosong') . "<br>";
echo "<hr>";
if ($response) {
    $decoded = json_decode($response, true);
    echo "<pre>";
    print_r($decoded);
    echo "</pre>";
}
?>