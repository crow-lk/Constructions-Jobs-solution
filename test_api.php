<?php

// Simple API test script
// Run this with: php test_api.php

$baseUrl = 'http://localhost:8000/api';

echo "Testing API endpoints...\n\n";

// Test 1: Register a new user
echo "1. Testing user registration...\n";
$registerData = [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/register');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($registerData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Response Code: $httpCode\n";
echo "Response: " . $response . "\n\n";

// Parse the response to get the token
$responseData = json_decode($response, true);
$token = $responseData['token'] ?? null;

if ($token) {
    echo "✅ Registration successful! Token: " . substr($token, 0, 20) . "...\n\n";
    
    // Test 2: Get current user
    echo "2. Testing get current user...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/user');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Response Code: $httpCode\n";
    echo "Response: " . $response . "\n\n";
    
    // Test 3: Create a project
    echo "3. Testing project creation...\n";
    $projectData = [
        'name' => 'Test Project',
        'description' => 'This is a test project',
        'status' => 'active',
        'start_date' => '2024-01-01',
        'budget' => 10000.00,
        'user_id' => $responseData['user']['id']
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/projects');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($projectData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Response Code: $httpCode\n";
    echo "Response: " . $response . "\n\n";
    
    // Test 4: Get projects list
    echo "4. Testing get projects list...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/projects');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Response Code: $httpCode\n";
    echo "Response: " . $response . "\n\n";
    
    // Test 5: Logout
    echo "5. Testing logout...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/logout');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Response Code: $httpCode\n";
    echo "Response: " . $response . "\n\n";
    
} else {
    echo "❌ Registration failed!\n";
}

echo "API testing completed!\n"; 