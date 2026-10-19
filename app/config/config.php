<?php
/**
 * Realty Smartz Pathshala - Core Configuration & Bootstrap
 */

// Define absolute path constants
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__));
}
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(APP_PATH));
}
if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', ROOT_PATH . '/storage');
}
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', ROOT_PATH . '/public');
}

// Ensure storage directories exist
@mkdir(STORAGE_PATH . '/uploads', 0755, true);
@mkdir(STORAGE_PATH . '/logs', 0755, true);
@mkdir(STORAGE_PATH . '/backups', 0755, true);

// ----------------------------------------------------------------
// Simple .env Loader (supports quoted strings with special characters like @, $, #)
// ----------------------------------------------------------------
$envFile = ROOT_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $val = trim($parts[1]);
            // Strip matching quotes safely
            if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                $val = substr($val, 1, -1);
            }
            putenv("{$key}={$val}");
            $_ENV[$key] = $val;
            $_SERVER[$key] = $val;
        }
    }
}

// ----------------------------------------------------------------
// Universal Application Autoloader (supports Linux case-sensitivity)
// ----------------------------------------------------------------
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $baseDir = APP_PATH . '/';

    // 1. Direct path check
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }

    // 2. Linux case-insensitive directory fallback (e.g., Controllers/ -> controllers/)
    $parts = explode('\\', $relativeClass);
    $className = array_pop($parts);
    $loweredDir = implode('/', array_map('strtolower', $parts));
    $dirPrefix = ($loweredDir !== '' ? $loweredDir . '/' : '');

    $fallback = $baseDir . $dirPrefix . $className . '.php';
    if (file_exists($fallback)) {
        require_once $fallback;
        return;
    }

    // 3. Lowercase filename fallback (e.g. database.php)
    $lowerFile = $baseDir . $dirPrefix . strtolower($className) . '.php';
    if (file_exists($lowerFile)) {
        require_once $lowerFile;
        return;
    }
});

// ----------------------------------------------------------------
// Helper: config()
// ----------------------------------------------------------------
if (!function_exists('config')) {
    function config(string $key, $default = null) {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }
        $val = getenv($key);
        if ($val !== false) {
            return $val;
        }
        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }
        return $default;
    }
}

// Error handling settings
$isLocal = (config('APP_ENV', 'production') === 'local');
$isDebug = filter_var(config('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN);

if ($isDebug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Timezone
date_default_timezone_set(config('APP_TIMEZONE', 'Asia/Kolkata'));

// Global error logging
set_exception_handler(function (Throwable $e) use ($isDebug) {
    $logMsg = sprintf(
        "[%s] Exception: %s in %s:%d\nStack trace:\n%s\n",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );
    @file_put_contents(STORAGE_PATH . '/logs/app.log', $logMsg, FILE_APPEND);

    if (php_sapi_name() === 'cli') {
        echo "Error: " . $e->getMessage() . PHP_EOL;
        exit(1);
    }

    if (!headers_sent()) {
        http_response_code(500);
    }

    if ($isDebug) {
        echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Application Error</title><style>body{font-family:'DM Sans',system-ui,-apple-system,sans-serif;background:#F7F5EF;color:#26332D;padding:40px;line-height:1.6;}h1{color:#C0392B;}pre{background:#FFFFFF;padding:20px;border:1px solid #E2DDD0;border-radius:12px;overflow:auto;color:#26332D;font-size:13px;box-shadow:0 4px 12px rgba(38,51,45,0.04);}</style></head><body>";
        echo "<h1>Application Error</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</body></html>";
    } else {
        if (file_exists(APP_PATH . '/views/public/500.php')) {
            require APP_PATH . '/views/public/500.php';
        } else {
            echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Something went wrong</title><style>body{font-family:'DM Sans',system-ui,-apple-system,sans-serif;background:#F7F5EF;color:#26332D;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px;}.card{max-width:480px;text-align:center;padding:40px 32px;background:#FFFFFF;border:1px solid #E2DDD0;border-radius:20px;box-shadow:0 12px 32px rgba(38,51,45,0.06);}h1{color:#26332D;font-size:1.6rem;margin-bottom:12px;}p{color:#6D766F;font-size:14px;margin-bottom:24px;}a{display:inline-block;background:#1F5C4A;color:#FFFFFF;text-decoration:none;font-weight:500;padding:12px 28px;border-radius:999px;font-size:14px;}</style></head><body>";
            echo "<div class='card'><h1>Temporarily Unavailable</h1><p>Our engineers have been notified. Please try again in a few moments.</p><a href='/'>Return to Home</a></div>";
            echo "</body></html>";
        }
    }
    exit(1);
});

// ----------------------------------------------------------------
// Session Security Settings
// ----------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    $lifetime = (int) config('SESSION_LIFETIME', 120) * 60;
    $isHttps = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') || 
               (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
               (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) ||
               filter_var(config('SESSION_SECURE_COOKIE', false), FILTER_VALIDATE_BOOLEAN);

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_maxlifetime', (string)$lifetime);

    // Host-only cookie without explicit domain attribute prevents cross-subdomain/HTTPS redirect session breakage
    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

// ----------------------------------------------------------------
// Global Helper Functions
// ----------------------------------------------------------------
require_once APP_PATH . '/helpers/IconHelper.php';

/**
 * Escapes HTML output safely
 */
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Generates absolute or relative URL
 */
function url(string $path = ''): string {
    $envUrl = trim(config('BASE_URL', config('APP_URL', '')));
    $isHttps = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') || 
               (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
               (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
    $currentScheme = $isHttps ? 'https' : 'http';
    $currentHost = $_SERVER['HTTP_HOST'] ?? '';

    // If accessed through web request (browser), dynamically adapt to the active host
    // (e.g. 43.242.226.197, realtysmartzpathshala.in, or localhost)
    if (!empty($currentHost)) {
        $base = "{$currentScheme}://{$currentHost}";
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $scriptDir = str_replace('\\', '/', dirname($scriptName));
        if ($scriptDir !== '/' && $scriptDir !== '.' && $scriptDir !== '') {
            $base .= rtrim($scriptDir, '/');
        }
    } elseif ($envUrl !== '') {
        $base = rtrim($envUrl, '/');
    } else {
        $base = 'http://localhost';
    }

    $cleanPath = '/' . ltrim($path, '/');
    return rtrim($base, '/') . ($cleanPath === '/' ? '' : $cleanPath);
}

/**
 * Assets URL helper with hashed manifest support
 */
function asset(string $path): string {
    static $manifest = null;
    $cleanPath = ltrim($path, '/');

    if ($manifest === null) {
        $manifestPath = ROOT_PATH . '/public/assets/dist/manifest.json';
        if (file_exists($manifestPath)) {
            $json = json_decode(file_get_contents($manifestPath), true);
            $manifest = is_array($json) ? $json : [];
        } else {
            $manifest = [];
        }
    }

    if (isset($manifest[$cleanPath])) {
        return url('assets/' . ltrim($manifest[$cleanPath], '/'));
    }

    $noAssets = preg_replace('#^assets/#', '', $cleanPath);
    if (isset($manifest[$noAssets])) {
        return url('assets/' . ltrim($manifest[$noAssets], '/'));
    }

    if ($cleanPath === 'assets/css/style.css' && isset($manifest['css/style.css'])) {
        return url('assets/' . ltrim($manifest['css/style.css'], '/'));
    }
    if ($cleanPath === 'assets/js/main.js' && isset($manifest['js/main.js'])) {
        return url('assets/' . ltrim($manifest['js/main.js'], '/'));
    }

    return url('/' . $cleanPath);
}

/**
 * Get or set session flash message
 */
function flash(?string $key = null, $message = null) {
    if ($key === null) {
        $flashes = $_SESSION['_flash'] ?? [];
        $_SESSION['_flash'] = [];
        return $flashes;
    }
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }
    $msg = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $msg;
}

/**
 * Old input helper
 */
function old(string $key, $default = '') {
    return $_SESSION['_old_input'][$key] ?? $default;
}

/**
 * CSRF Token Generator & Validator
 */
function csrf_token(): string {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Authenticated user helper
 */
function auth(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * Check if current user is logged in
 */
function auth_check(): bool {
    return !empty($_SESSION['user']['id']);
}

/**
 * Check if user has specific role
 */
function auth_has_role(array|string $roles): bool {
    if (!auth_check()) return false;
    $roles = (array) $roles;
    return in_array($_SESSION['user']['role'] ?? '', $roles, true);
}

/**
 * JSON response helper
 */
function response_json(array $data, int $statusCode = 200): void {
    if (!headers_sent()) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
    }
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Redirect helper
 */
function redirect(string $path, int $statusCode = 302): void {
    if (!headers_sent()) {
        header("Location: " . url($path), true, $statusCode);
    } else {
        echo "<script>window.location.href='" . e(url($path)) . "';</script>";
    }
    exit;
}
