<?php

// API Test Script for Roles List
// Usage: php test_roles_api.php

$baseUrl = 'http://dimoconstructions.test';

echo "=== Roles API Test ===\n\n";

// Test 1: Public roles endpoint
echo "1. Testing Public Roles Endpoint\n";
echo "URL: {$baseUrl}/api/roles/public\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/roles/public');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: $httpCode\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['roles']) && is_array($data['roles'])) {
        echo "✅ SUCCESS! Found " . count($data['roles']) . " roles:\n";
        foreach ($data['roles'] as $role) {
            $description = isset($role['description']) ? $role['description'] : 'No description';
            echo "   • {$role['name']}: {$description}\n";
        }
    } else {
        echo "❌ Error: Unexpected response format\n";
        echo "Response: " . $response . "\n";
    }
} else {
    echo "❌ Error: API request failed\n";
    echo "Response: " . $response . "\n";
}

echo "\n";

// Test 2: Protected roles endpoint (should return 401)
echo "2. Testing Protected Roles Endpoint (should fail without auth)\n";
echo "URL: {$baseUrl}/api/roles\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/roles');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: $httpCode\n";

if ($httpCode === 401) {
    echo "✅ SUCCESS! Protected endpoint correctly returns 401 (Unauthorized)\n";
} else {
    echo "❌ Unexpected response for protected endpoint\n";
    echo "Response: " . $response . "\n";
}

echo "\n";

// Test 3: Individual role endpoint (should return 401)
echo "3. Testing Individual Role Endpoint (should fail without auth)\n";
echo "URL: {$baseUrl}/api/roles/1\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/roles/1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: $httpCode\n";

if ($httpCode === 401) {
    echo "✅ SUCCESS! Individual role endpoint correctly returns 401 (Unauthorized)\n";
} else {
    echo "❌ Unexpected response for individual role endpoint\n";
    echo "Response: " . $response . "\n";
}

echo "\n=== Test Complete ===\n";
echo "To test the frontend, visit: {$baseUrl}/dashboard\n"; 