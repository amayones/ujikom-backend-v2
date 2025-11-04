<?php

// Simple API Test Script
// Run: php test_api.php

$baseUrl = 'http://localhost:8000/api';

function makeRequest($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    $headers = ['Content-Type: application/json'];
    
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

echo "=== Testing Backend AC API ===\n\n";

// Test 1: Get Films
echo "1. Testing GET /films\n";
$response = makeRequest($baseUrl . '/films');
echo "Status: " . $response['code'] . "\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Login
echo "2. Testing POST /login\n";
$loginData = [
    'email' => 'customer@test.com',
    'password' => 'password123'
];
$response = makeRequest($baseUrl . '/login', 'POST', $loginData);
echo "Status: " . $response['code'] . "\n";

if ($response['code'] == 200 && isset($response['body']['data']['token'])) {
    $token = $response['body']['data']['token'];
    echo "Login successful! Token: " . substr($token, 0, 20) . "...\n\n";
    
    // Test 3: Get Profile
    echo "3. Testing GET /profile (with token)\n";
    $response = makeRequest($baseUrl . '/profile', 'GET', null, $token);
    echo "Status: " . $response['code'] . "\n";
    echo "User: " . $response['body']['data']['name'] . " (" . $response['body']['data']['role'] . ")\n\n";
    
} else {
    echo "Login failed!\n";
    echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";
}

// Test 4: Get Schedules
echo "4. Testing GET /schedules/1\n";
$response = makeRequest($baseUrl . '/schedules/1');
echo "Status: " . $response['code'] . "\n";
if ($response['code'] == 200) {
    echo "Schedules count: " . count($response['body']['data']) . "\n\n";
} else {
    echo "Error: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";
}

echo "=== Test completed ===\n";