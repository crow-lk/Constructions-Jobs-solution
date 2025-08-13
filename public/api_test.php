<?php

/**
 * Simple script to test API functionality
 * Upload this to your production server's public directory and access it via:
 * http://staging.homebuilders.lk/api_test.php
 */

// Display PHP info
echo "<h1>Environment Information</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";

// Test route resolution
echo "<h1>Route Resolution Test</h1>";
$routes = [
    '/api/register',
    '/api/login',
    '/api/roles',
    '/api/categories',
];

foreach ($routes as $route) {
    echo "<p>Testing route: $route</p>";
    
    // Create cURL resource
    $ch = curl_init();
    
    // Set URL and other options
    curl_setopt($ch, CURLOPT_URL, "http://" . $_SERVER['HTTP_HOST'] . $route);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    // Send the request and get the response
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo "<p>HTTP Code: $httpCode</p>";
    echo "<pre>" . htmlspecialchars(substr($response, 0, 300)) . "...</pre>";
    
    curl_close($ch);
}

// Display .htaccess content if possible
echo "<h1>.htaccess Check</h1>";
$htaccessPath = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess';
if (file_exists($htaccessPath)) {
    echo "<p>.htaccess exists</p>";
    if (is_readable($htaccessPath)) {
        echo "<pre>" . htmlspecialchars(file_get_contents($htaccessPath)) . "</pre>";
    } else {
        echo "<p>.htaccess not readable</p>";
    }
} else {
    echo "<p>.htaccess does not exist</p>";
}

// Check mod_rewrite
echo "<h1>mod_rewrite Check</h1>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    $mod_rewrite = in_array('mod_rewrite', $modules);
    echo "<p>mod_rewrite enabled: " . ($mod_rewrite ? 'Yes' : 'No') . "</p>";
} else {
    echo "<p>Unable to check if mod_rewrite is enabled</p>";
}
