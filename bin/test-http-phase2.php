<?php
$baseUrl = 'http://127.0.0.1:8000';

$urls = [
    '/register',
    '/login',
    '/forgot-password',
    '/verify-email',
    '/dashboard/onboarding',
    '/dashboard',
    '/dashboard/aptitude'
];

echo "Testing HTTP Routes against {$baseUrl}...\n";

foreach ($urls as $uri) {
    $ch = curl_init($baseUrl . $uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects to check redirect codes
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);

    $extra = $redirectUrl ? " -> Redirects to: {$redirectUrl}" : "";
    echo "  [{$code}] {$uri}{$extra}\n";
}
