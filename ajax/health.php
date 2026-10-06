<?php

require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$url = API_HEALTH;

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 3);

$response = curl_exec($ch);

if ($response === false) {

    echo json_encode([
        'success' => false,
        'status' => 'offline',
        'message' => curl_error($ch)
    ]);

    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300) {

    echo json_encode([
        'success' => true,
        'status' => 'online',
        'message' => 'Flask API is online'
    ]);
} else {

    echo json_encode([
        'success' => false,
        'status' => 'offline',
        'http_code' => $httpCode
    ]);
}
