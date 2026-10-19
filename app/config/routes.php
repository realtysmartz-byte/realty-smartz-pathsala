<?php
/**
 * Realty Smartz Pathshala - Unified Application Routing System
 * ALL web and portal routes are defined in this ONE single file.
 */

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\CandidateController;
use App\Controllers\AptitudeController;
use App\Controllers\AdminController;
use App\Middleware\CsrfMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

class Router {
    private static array $routes = [];

    public static function get(string $path, array $handler, array $middlewares = []): void {
        self::add('GET', $path, $handler, $middlewares);
    }

    public static function post(string $path, array $handler, array $middlewares = []): void {
        self::add('POST', $path, $handler, $middlewares);
    }

    private static function add(string $method, string $path, array $handler, array $middlewares = []): void {
        self::$routes[] = [
            'method'      => $method,
            'path'        => '/' . trim($path, '/'),
            'handler'     => $handler,
            'middlewares' => $middlewares
        ];
    }

    public static function dispatch(string $uri, string $requestMethod): void {
        if ($requestMethod === 'HEAD') {
            $requestMethod = 'GET';
        }

        // Strip query string
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = '/' . trim(rawurldecode($uri), '/');

        // Check for API endpoints first -> handover to app/api/api.php
        if (str_starts_with($uri, '/api/')) {
            require APP_PATH . '/api/api.php';
            exit;
        }

        foreach (self::$routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Convert route path to regex with parameters: e.g. /projects/{slug} -> /projects/(?P<slug>[^/]+)
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                // Execute Middlewares
                foreach ($route['middlewares'] as $mw) {
                    match ($mw) {
                        'csrf'  => CsrfMiddleware::handle(),
                        'auth'  => AuthMiddleware::handle(),
                        'admin' => AdminMiddleware::handle(['super_admin', 'hr_manager', 'reviewer']),
                        'super' => AdminMiddleware::handle(['super_admin']),
                        default => null
                    };
                }

                // Extract named parameter arguments
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                [$controllerClass, $action] = $route['handler'];
                $controller = new $controllerClass();
                call_user_func_array([$controller, $action], array_values($params));
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        if (file_exists(APP_PATH . '/views/public/404.php')) {
            require APP_PATH . '/views/public/404.php';
        } else {
            echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>404 Not Found</title><style>body{background:#F7F5EF;color:#26332D;font-family:'DM Sans',system-ui,-apple-system,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px;}.error-card{text-align:center;background:#FFFFFF;padding:36px 32px;border-radius:20px;border:1px solid #E2DDD0;box-shadow:0 12px 32px rgba(38,51,45,0.06);max-width:440px;}h1{color:#26332D;margin-bottom:8px;}p{color:#6D766F;font-size:14px;margin-bottom:20px;}a{display:inline-block;background:#1F5C4A;color:#FFFFFF;text-decoration:none;font-weight:500;padding:10px 24px;border-radius:999px;font-size:13.5px;}</style></head><body><div class='error-card'><h1>404 Page Not Found</h1><p>The page you are looking for does not exist or has been moved.</p><a href='/'>Return to Home</a></div></body></html>";
        }
        exit;
    }
}

// ================================================================
// 1. PUBLIC WEBSITE ROUTES
// ================================================================
Router::get('/', [HomeController::class, 'index']);
Router::get('/projects', [HomeController::class, 'projects']);
Router::get('/projects/{slug}', [HomeController::class, 'projectDetail']);
Router::get('/about', [HomeController::class, 'about']);
Router::get('/blog', [HomeController::class, 'blog']);
Router::get('/blog/{slug}', [HomeController::class, 'blogDetail']);
Router::get('/contact', [HomeController::class, 'contact']);
Router::post('/contact', [HomeController::class, 'submitContact'], ['csrf']);

// ================================================================
// 2. AUTHENTICATION ROUTES
// ================================================================
Router::get('/register', [AuthController::class, 'showRegister']);
Router::post('/register', [AuthController::class, 'register'], ['csrf']);

Router::get('/verify-email', [AuthController::class, 'showVerifyEmail']);
Router::post('/verify-email', [AuthController::class, 'verifyEmailOtp'], ['csrf']);
Router::get('/verify-otp', [AuthController::class, 'showVerifyEmail']);
Router::post('/verify-otp', [AuthController::class, 'verifyEmailOtp'], ['csrf']);
Router::post('/resend-otp', [AuthController::class, 'resendOtp'], ['csrf']);

Router::get('/login', [AuthController::class, 'showLogin']);
Router::post('/login', [AuthController::class, 'login'], ['csrf']);
Router::get('/logout', [AuthController::class, 'logout']);

Router::get('/forgot-password', [AuthController::class, 'showForgotPassword']);
Router::post('/forgot-password', [AuthController::class, 'sendResetLink'], ['csrf']);
Router::get('/reset-password', [AuthController::class, 'showResetPassword']);
Router::post('/reset-password', [AuthController::class, 'resetPassword'], ['csrf']);

Router::get('/admin/login', [AuthController::class, 'showAdminLogin']);
Router::post('/admin/login', [AuthController::class, 'adminLogin'], ['csrf']);
Router::get('/login/admin', [AuthController::class, 'showAdminLogin']);
Router::post('/login/admin', [AuthController::class, 'adminLogin'], ['csrf']);

// ================================================================
// 3. CANDIDATE ONBOARDING PORTAL ROUTES (Auth Required)
// ================================================================
Router::get('/dashboard', [CandidateController::class, 'dashboard'], ['auth']);
Router::get('/dashboard/onboarding', [CandidateController::class, 'onboarding'], ['auth']);
Router::post('/dashboard/onboarding/save-step', [CandidateController::class, 'saveOnboardingStep'], ['auth', 'csrf']);
Router::post('/dashboard/onboarding/submit', [CandidateController::class, 'submitOnboarding'], ['auth', 'csrf']);

// Backward compatibility profile routes
Router::get('/dashboard/profile', [CandidateController::class, 'onboarding'], ['auth']);
Router::post('/dashboard/profile/save', [CandidateController::class, 'saveSection'], ['auth', 'csrf']);
Router::post('/dashboard/document/upload', [CandidateController::class, 'uploadDocument'], ['auth', 'csrf']);
Router::post('/dashboard/profile/submit', [CandidateController::class, 'submitOnboarding'], ['auth', 'csrf']);

// Aptitude Assessment
Router::get('/dashboard/aptitude', [AptitudeController::class, 'showTest'], ['auth']);
Router::get('/dashboard/test', [AptitudeController::class, 'showTest'], ['auth']);
Router::post('/dashboard/test/start', [AptitudeController::class, 'startTest'], ['auth', 'csrf']);
Router::post('/dashboard/test/answer', [AptitudeController::class, 'saveAnswer'], ['auth', 'csrf']);
Router::post('/dashboard/test/warning', [AptitudeController::class, 'logWarning'], ['auth', 'csrf']);
Router::post('/dashboard/test/submit', [AptitudeController::class, 'submitTest'], ['auth', 'csrf']);
Router::get('/dashboard/test/scorecard/{id}', [AptitudeController::class, 'downloadScorecard'], ['auth']);

Router::get('/document/download/{id}', [CandidateController::class, 'downloadDocument'], ['auth']);
Router::get('/document-version/download/{id}', [CandidateController::class, 'downloadDocumentVersion'], ['auth']);

// ================================================================
// 4. ADMIN PANEL ROUTES (Admin Roles Required)
// ================================================================
Router::get('/admin', [AdminController::class, 'dashboard'], ['admin']);
Router::get('/admin/dashboard', [AdminController::class, 'dashboard'], ['admin']);

Router::get('/admin/candidates', [AdminController::class, 'candidates'], ['admin']);
Router::get('/admin/candidates/{id}', [AdminController::class, 'candidateDetail'], ['admin']);
Router::post('/admin/candidates/{id}/status', [AdminController::class, 'updateCandidateStatus'], ['admin', 'csrf']);
Router::post('/admin/candidates/{id}/reveal-identity', [AdminController::class, 'revealIdentity'], ['admin', 'csrf']);
Router::post('/admin/candidates/{id}/allow-reattempt', [AdminController::class, 'allowReattempt'], ['admin', 'csrf']);
Router::post('/admin/candidates/{id}/update', [AdminController::class, 'updateCandidate'], ['admin', 'csrf']);
Router::post('/admin/candidates/{id}/archive', [AdminController::class, 'archiveCandidate'], ['admin', 'csrf']);
Router::post('/admin/candidates/{id}/delete', [AdminController::class, 'archiveCandidate'], ['admin', 'csrf']);
Router::post('/admin/candidates/{id}/restore', [AdminController::class, 'restoreCandidate'], ['admin', 'csrf']);

Router::get('/admin/candidates/{id}/export-full-pdf', [AdminController::class, 'exportFullPdf'], ['admin']);
Router::get('/admin/candidates/{id}/export-pdf', [AdminController::class, 'exportPdf'], ['admin']);
Router::get('/admin/candidates/{id}/export-docx', [AdminController::class, 'exportDocx'], ['admin']);
Router::get('/admin/candidates/{id}/export-zip', [AdminController::class, 'exportZip'], ['admin']);
Router::get('/admin/candidates/bulk-pdf-zip', [AdminController::class, 'bulkPdfZip'], ['admin']);
Router::post('/admin/candidates/bulk-pdf-zip', [AdminController::class, 'bulkPdfZip'], ['admin', 'csrf']);
Router::get('/admin/candidates/export-excel', [AdminController::class, 'exportExcel'], ['admin']);
Router::post('/admin/candidates/export-excel', [AdminController::class, 'exportExcel'], ['admin', 'csrf']);
Router::get('/admin/candidates/export-csv', [AdminController::class, 'exportExcel'], ['admin']);

// Guest Test Link Admin Management
Router::post('/admin/guest-links/create', [AdminController::class, 'createGuestLink'], ['admin', 'csrf']);
Router::post('/admin/guest-links/{id}/revoke', [AdminController::class, 'revokeGuestLink'], ['admin', 'csrf']);
Router::post('/admin/guest-links/{id}/reattempt', [AdminController::class, 'reattemptGuestLink'], ['admin', 'csrf']);

// Public Guest Test Assessment Routes (No login required; Token-authenticated only)
Router::get('/test/g/{token}', [\App\Controllers\GuestTestController::class, 'showGuestTest']);
Router::post('/test/g/{token}/identify', [\App\Controllers\GuestTestController::class, 'identifyGuest'], ['csrf']);
Router::post('/test/g/{token}/start', [\App\Controllers\GuestTestController::class, 'startGuestTest'], ['csrf']);
Router::post('/test/g/{token}/answer', [\App\Controllers\GuestTestController::class, 'saveGuestAnswer'], ['csrf']);
Router::post('/test/g/{token}/submit', [\App\Controllers\GuestTestController::class, 'submitGuestTest'], ['csrf']);

Router::get('/admin/documents', [AdminController::class, 'documents'], ['admin']);
Router::post('/admin/documents/{id}/status', [AdminController::class, 'updateDocumentStatus'], ['admin', 'csrf']);
Router::get('/admin/documents/export-zip', [AdminController::class, 'exportDocumentsZip'], ['admin']);
Router::post('/admin/documents/bulk-action', [AdminController::class, 'bulkDocumentAction'], ['admin', 'csrf']);
Router::get('/admin/assessments', [AdminController::class, 'assessments'], ['admin']);
Router::get('/admin/questions', [AdminController::class, 'questions'], ['admin']);
Router::post('/admin/questions/save', [AdminController::class, 'saveQuestion'], ['admin', 'csrf']);

Router::get('/admin/content', [AdminController::class, 'siteContent'], ['admin']);
Router::post('/admin/content/settings', [AdminController::class, 'saveSettings'], ['admin', 'csrf']);
Router::post('/admin/email/test', [AdminController::class, 'sendTestEmail'], ['admin', 'csrf']);
Router::get('/admin/audit-logs', [AdminController::class, 'auditLogs'], ['admin']);
Router::get('/admin/backups', [AdminController::class, 'backups'], ['admin']);
Router::post('/admin/backups/create', [AdminController::class, 'createBackup'], ['admin', 'csrf']);
Router::get('/admin/backups/download/{file}', [AdminController::class, 'downloadBackup'], ['admin']);

// Migration runner (Super Admin only)
Router::get('/admin/migrate', [AdminController::class, 'runMigrations'], ['super']);
Router::post('/admin/migrate', [AdminController::class, 'runMigrations'], ['super', 'csrf']);
