<?php
$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = sys_get_temp_dir() . '/candidate_cookie_' . time() . '.txt';

echo "1. Registering Candidate via HTTP POST /register...\n";

// First get CSRF token from /register
$ch = curl_init($baseUrl . '/register');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
$regPage = curl_exec($ch);
curl_close($ch);

preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $regPage, $csrfMatches);
$csrfToken = $csrfMatches[1] ?? '';
echo "  CSRF Token retrieved: " . substr($csrfToken, 0, 10) . "...\n";

// Register candidate
$suffix = time();
$email = "web_candidate_{$suffix}@realtysmartzpathshala.in";
$mobile = '9876' . str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

$postData = [
    '_csrf_token'           => $csrfToken,
    'full_name'             => 'Rohit Verma',
    'father_name'           => 'Mahesh Verma',
    'email'                 => $email,
    'mobile'                => $mobile,
    'password'              => 'RohitPass@2026',
    'password_confirmation' => 'RohitPass@2026'
];

$ch = curl_init($baseUrl . '/register');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

echo "  Register POST Code: {$code} -> Redirects to: {$redirectUrl}\n";

// Access /verify-email with the session
$ch = curl_init($baseUrl . '/verify-email');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
$verifyHtml = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "  /verify-email Page Response: [{$code}] (contains 'Verify Your Email': " . (str_contains($verifyHtml, 'Verify Your Email') ? 'YES' : 'NO') . ")\n";

// Fetch OTP from database directly to complete verification
define('APP_PATH', dirname(__DIR__) . '/app');
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once APP_PATH . '/config/config.php';
$db = \App\Config\Database::getConnection();
$userRow = $db->query("SELECT id FROM `users` WHERE `email` = '{$email}' LIMIT 1")->fetch();
$userId = (int)$userRow['id'];
$otpRow = $db->query("SELECT otp, token FROM `email_verifications` WHERE `user_id` = {$userId} LIMIT 1")->fetch();
$otp = $otpRow['otp'];
echo "  Database OTP for user #{$userId}: {$otp}\n";

// Verify with one-click token link
$ch = curl_init($baseUrl . '/verify-email?token=' . $otpRow['token']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);
echo "  One-click Token Verification Code: {$code} -> Redirects to: {$redirectUrl}\n";

// Access /dashboard/onboarding as verified candidate
$ch = curl_init($baseUrl . '/dashboard/onboarding');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
$onboardingHtml = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "  /dashboard/onboarding Response: [{$code}] (contains 'Candidate Onboarding Wizard': " . (str_contains($onboardingHtml, 'Candidate Onboarding Wizard') ? 'YES' : 'NO') . ")\n";

// Access /dashboard overview as verified candidate
$ch = curl_init($baseUrl . '/dashboard');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
$dashboardHtml = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "  /dashboard Overview Response: [{$code}] (contains 'Application Progress Timeline': " . (str_contains($dashboardHtml, 'Application Progress Timeline') ? 'YES' : 'NO') . ")\n";

@unlink($cookieFile);
echo "\nCandidate Flow Verification Complete!\n";
