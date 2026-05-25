<?php
session_start();
require_once 'config.php';

echo "<pre>";
echo "Token ada? " . (isset($_SESSION['access_token']) ? "YES" : "NO") . "\n";
echo "Token value: " . ($_SESSION['access_token'] ?? 'NULL') . "\n\n";

// Test panggil students
$result = apiRequest('/students', 'GET', null, true);
echo "GET /students response:\n";
print_r($result);
echo "\n";

// Test panggil teachers  
$result2 = apiRequest('/teachers', 'GET', null, true);
echo "GET /teachers response:\n";
print_r($result2);
echo "</pre>";
?>