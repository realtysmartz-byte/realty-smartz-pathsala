<?php
/**
 * Test HTTP Routes and Response Codes
 */

$baseUrl = 'http://127.0.0.1:8000';

$routes = [
    '/' => 200,
    '/projects' => 200,
    '/about' => 200,
    '/contact' => 200,
    '/login' => 200,
    '/register' => 200,
    '/verify-email' => 302, // redirects to /register without pending session
    '/verify-otp' => 302,   // redirects to /register without pending session
    '/forgot-password' => 200,
    '/admin/login' => 200,
    '/dashboard' => 302, // redirects to /login when unauthenticated
    '/admin/dashboard' => 302, // redirects to /login or /admin/login
    '/admin/candidates' => 302,
    '/admin/assessments' => 302,
    '/admin/questions' => 302,
];

echo "========================================================\n";
echo "   HTTP ENDPOINTS & ROUTING SMOKE TEST                  \n";
echo "========================================================\n\n";

$allPassed = true;
foreach ($routes as $path => $expectedStatus) {
    $url = $baseUrl . $path;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($status === $expectedStatus) {
        echo "  [PASS] {$path} -> HTTP {$status} (Expected {$expectedStatus})\n";
    } else {
        echo "  [FAIL] {$path} -> HTTP {$status} (Expected {$expectedStatus})" . ($err ? " Error: {$err}" : '') . "\n";
        $allPassed = false;
    }
}

echo "\n========================================================\n";
if ($allPassed) {
    echo "   ALL ROUTE SMOKE TESTS PASSED!\n";
} else {
    echo "   SOME ROUTE SMOKE TESTS FAILED!\n";
}
echo "========================================================\n";
