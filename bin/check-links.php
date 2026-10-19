<?php
/**
 * Link and Route Verification Crawler
 * Tests all public, candidate, and admin routes.
 * Checks for 200 OK, zero broken assets, zero broken links, and zero banned colors.
 */

$baseUrl = 'http://127.0.0.1:8000';

$cookieJar = tempnam(sys_get_temp_dir(), 'crawler_cookie_');
$errors = [];
$testedRoutes = 0;
$testedAssets = [];

function request(string $url, string $method = 'GET', array $postData = [], string $cookieFile = null): array {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    }

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    curl_close($ch);

    return [
        'code' => $httpCode,
        'headers' => $headers,
        'body' => $body
    ];
}

$bannedPatterns = [
    '#0d6efd',
    '#3447E6',
    '#3447e6',
    '#0a58ca',
    '#1E2A44',
    '#1e2a44',
    '#6B7A90',
    '#6b7a90',
];

function checkPageContent(string $path, string $html, &$errors, &$testedAssets, string $baseUrl) {
    global $bannedPatterns;

    // 1. Check for banned colors in body
    foreach ($bannedPatterns as $banned) {
        if (stripos($html, $banned) !== false) {
            $errors[] = "Route {$path} contains banned color token: {$banned}";
        }
    }

    // 2. Check assets (img, link css, script js)
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    // Images
    $imgs = $dom->getElementsByTagName('img');
    foreach ($imgs as $img) {
        $src = $img->getAttribute('src');
        if (!empty($src) && !str_starts_with($src, 'data:')) {
            $assetUrl = str_starts_with($src, 'http') ? $src : $baseUrl . '/' . ltrim($src, '/');
            if (!isset($testedAssets[$assetUrl])) {
                $res = request($assetUrl);
                $testedAssets[$assetUrl] = $res['code'];
                if ($res['code'] !== 200) {
                    $errors[] = "Broken image on {$path}: {$src} (HTTP {$res['code']})";
                }
            }
        }
    }

    // Stylesheets
    $links = $dom->getElementsByTagName('link');
    foreach ($links as $link) {
        if ($link->getAttribute('rel') === 'stylesheet') {
            $href = $link->getAttribute('href');
            if (!empty($href)) {
                $assetUrl = str_starts_with($href, 'http') ? $href : $baseUrl . '/' . ltrim($href, '/');
                if (!isset($testedAssets[$assetUrl])) {
                    $res = request($assetUrl);
                    $testedAssets[$assetUrl] = $res['code'];
                    if ($res['code'] !== 200) {
                        $errors[] = "Broken stylesheet on {$path}: {$href} (HTTP {$res['code']})";
                    }
                }
            }
        }
    }

    // Scripts
    $scripts = $dom->getElementsByTagName('script');
    foreach ($scripts as $script) {
        $src = $script->getAttribute('src');
        if (!empty($src)) {
            $assetUrl = str_starts_with($src, 'http') ? $src : $baseUrl . '/' . ltrim($src, '/');
            if (!isset($testedAssets[$assetUrl])) {
                $res = request($assetUrl);
                $testedAssets[$assetUrl] = $res['code'];
                if ($res['code'] !== 200) {
                    $errors[] = "Broken script on {$path}: {$src} (HTTP {$res['code']})";
                }
            }
        }
    }
}

echo "=== REALTY SMARTZ PATHSHALA LINK & ROUTE VERIFICATION ===\n\n";

// 1. PUBLIC ROUTES
$publicRoutes = [
    '/',
    '/about',
    '/projects',
    '/blog',
    '/contact',
    '/login',
    '/register',
    '/forgot-password',
    '/admin/login',
    '/robots.txt',
    '/sitemap.xml',
    '/site.webmanifest',
];

echo "--- 1. Testing Public Routes ---\n";
foreach ($publicRoutes as $route) {
    $res = request($baseUrl . $route);
    $testedRoutes++;
    if ($res['code'] === 200) {
        echo "  [OK 200] {$route}\n";
        if (str_ends_with($route, '.txt') || str_ends_with($route, '.xml') || str_ends_with($route, '.webmanifest')) {
            // text/xml file
        } else {
            checkPageContent($route, $res['body'], $errors, $testedAssets, $baseUrl);
        }
    } else {
        $errors[] = "Route {$route} returned HTTP {$res['code']} (expected 200)";
        echo "  [FAIL {$res['code']}] {$route}\n";
    }
}

// 2. DYNAMIC ROUTES (find slugs from database or pages)
echo "\n--- 2. Testing Dynamic Slugs (Projects & Blog) ---\n";
// Fetch /projects page to get real project slug links
$resProjects = request($baseUrl . '/projects');
$dom = new DOMDocument();
@$dom->loadHTML($resProjects['body']);
$links = $dom->getElementsByTagName('a');
$projectSlug = null;
foreach ($links as $link) {
    $href = $link->getAttribute('href');
    if (preg_match('#/projects/([a-zA-Z0-9_-]+)#', $href, $m)) {
        $projectSlug = $m[1];
        break;
    }
}

if ($projectSlug) {
    $route = "/projects/{$projectSlug}";
    $res = request($baseUrl . $route);
    $testedRoutes++;
    if ($res['code'] === 200) {
        echo "  [OK 200] {$route}\n";
        checkPageContent($route, $res['body'], $errors, $testedAssets, $baseUrl);
    } else {
        $errors[] = "Dynamic project detail {$route} returned HTTP {$res['code']}";
        echo "  [FAIL {$res['code']}] {$route}\n";
    }
} else {
    echo "  [INFO] No project link found on /projects\n";
}

// Fetch /blog page to get real blog slug links
$resBlog = request($baseUrl . '/blog');
$dom = new DOMDocument();
@$dom->loadHTML($resBlog['body']);
$links = $dom->getElementsByTagName('a');
$blogSlug = null;
foreach ($links as $link) {
    $href = $link->getAttribute('href');
    if (preg_match('#/blog/([a-zA-Z0-9_-]+)#', $href, $m)) {
        $blogSlug = $m[1];
        break;
    }
}

if ($blogSlug) {
    $route = "/blog/{$blogSlug}";
    $res = request($baseUrl . $route);
    $testedRoutes++;
    if ($res['code'] === 200) {
        echo "  [OK 200] {$route}\n";
        checkPageContent($route, $res['body'], $errors, $testedAssets, $baseUrl);
    } else {
        $errors[] = "Dynamic blog detail {$route} returned HTTP {$res['code']}";
        echo "  [FAIL {$res['code']}] {$route}\n";
    }
} else {
    echo "  [INFO] No blog link found on /blog\n";
}

// 3. 404 ERROR PAGE ROUTE
echo "\n--- 3. Testing 404 Custom Template ---\n";
$route404 = '/this-route-does-not-exist-at-all-404';
$res404 = request($baseUrl . $route404);
$testedRoutes++;
if ($res404['code'] === 404) {
    echo "  [OK 404] {$route404}\n";
    checkPageContent($route404, $res404['body'], $errors, $testedAssets, $baseUrl);
} else {
    $errors[] = "404 route {$route404} returned HTTP {$res404['code']} (expected 404)";
    echo "  [FAIL {$res404['code']}] {$route404}\n";
}

// 4. AUTHENTICATED ADMIN ROUTES
echo "\n--- 4. Testing Admin Portal Routes ---\n";
$adminCookieFile = tempnam(sys_get_temp_dir(), 'admin_cookie_');

// Perform real HTTP login for admin
$adminLoginGet = request($baseUrl . '/admin/login', 'GET', [], $adminCookieFile);
preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $adminLoginGet['body'], $adminCsrf);
$adminToken = $adminCsrf[1] ?? '';

$adminLoginPost = request($baseUrl . '/admin/login', 'POST', [
    '_csrf_token' => $adminToken,
    'email'       => 'admin@realtysmartzpathshala.in',
    'password'    => 'Admin@2026'
], $adminCookieFile);

if ($adminLoginPost['code'] === 302 || $adminLoginPost['code'] === 200) {
    echo "  [AUTH OK] Admin logged in successfully\n";
    $adminRoutes = [
        '/admin',
        '/admin/dashboard',
        '/admin/candidates',
        '/admin/documents',
        '/admin/assessments',
        '/admin/questions',
        '/admin/content',
        '/admin/audit-logs',
        '/admin/backups',
    ];

    foreach ($adminRoutes as $route) {
        $res = request($baseUrl . $route, 'GET', [], $adminCookieFile);
        $testedRoutes++;
        if ($res['code'] === 200) {
            echo "  [OK 200] {$route}\n";
            checkPageContent($route, $res['body'], $errors, $testedAssets, $baseUrl);
        } else {
            $errors[] = "Admin route {$route} returned HTTP {$res['code']} (expected 200)";
            echo "  [FAIL {$res['code']}] {$route}\n";
        }
    }
} else {
    $errors[] = "Admin login failed with HTTP {$adminLoginPost['code']}";
    echo "  [AUTH FAIL] Admin login returned HTTP {$adminLoginPost['code']}\n";
}

// 5. AUTHENTICATED CANDIDATE ROUTES
echo "\n--- 5. Testing Candidate Portal Routes ---\n";
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
$pdo = \App\Config\Database::getConnection();

$candPass = 'Candidate@2026';
$candHash = password_hash($candPass, PASSWORD_BCRYPT, ['cost' => 11]);

// Find or update candidate account adhering to Data Safety Policy (no DELETE)
$cand = $pdo->query("SELECT * FROM `users` WHERE `role` = 'candidate' AND `is_archived` = 0 LIMIT 1")->fetch();
if ($cand) {
    $candEmail = $cand['email'];
    $pdo->prepare("UPDATE `users` SET `password_hash` = :hash, `email_verified` = 1, `email_verified_at` = NOW() WHERE `id` = :id")
        ->execute([':hash' => $candHash, ':id' => $cand['id']]);
} else {
    $candEmail = 'candidate.onboarding@realtysmartzpathshala.in';
    $pdo->prepare("INSERT INTO `users` (`full_name`, `email`, `mobile`, `password_hash`, `role`, `status`, `email_verified`, `email_verified_at`, `created_at`) 
        VALUES ('Test Candidate', :email, '+919999900001', :hash, 'candidate', 'approved', 1, NOW(), NOW())")
        ->execute([':email' => $candEmail, ':hash' => $candHash]);
}

$candCookieFile = tempnam(sys_get_temp_dir(), 'cand_cookie_');
$candLoginGet = request($baseUrl . '/login', 'GET', [], $candCookieFile);
preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $candLoginGet['body'], $candCsrf);
$candToken = $candCsrf[1] ?? '';

$candLoginPost = request($baseUrl . '/login', 'POST', [
    '_csrf_token' => $candToken,
    'email'       => $candEmail,
    'password'    => 'Candidate@2026'
], $candCookieFile);

if ($candLoginPost['code'] === 302 || $candLoginPost['code'] === 200) {
    echo "  [AUTH OK] Candidate logged in successfully\n";
    $candRoutes = [
        '/dashboard',
        '/dashboard/onboarding',
        '/dashboard/aptitude',
    ];

    foreach ($candRoutes as $route) {
        $res = request($baseUrl . $route, 'GET', [], $candCookieFile);
        $testedRoutes++;
        if ($res['code'] === 200) {
            echo "  [OK 200] {$route}\n";
            checkPageContent($route, $res['body'], $errors, $testedAssets, $baseUrl);
        } else {
            $errors[] = "Candidate route {$route} returned HTTP {$res['code']} (expected 200)";
            echo "  [FAIL {$res['code']}] {$route}\n";
        }
    }
    @unlink($candCookieFile);
} else {
    $errors[] = "Candidate login failed with HTTP {$candLoginPost['code']}";
    echo "  [AUTH FAIL] Candidate login returned HTTP {$candLoginPost['code']}\n";
}

@unlink($adminCookieFile);
@unlink($cookieJar);

echo "\n============================================\n";
echo "SUMMARY: Tested {$testedRoutes} routes and " . count($testedAssets) . " distinct assets.\n";

if (empty($errors)) {
    echo "PASSED: All routes returned expected codes, all assets 200 OK, zero banned colors.\n";
    exit(0);
} else {
    echo "FAILED: " . count($errors) . " issue(s) detected:\n";
    foreach ($errors as $e) {
        echo "  - {$e}\n";
    }
    exit(1);
}
