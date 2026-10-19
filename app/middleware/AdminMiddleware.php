<?php
namespace App\Middleware;

use App\Config\Database;

class AdminMiddleware {
    /**
     * Enforce admin authentication and role permissions
     */
    public static function handle(array $allowedRoles = ['super_admin', 'hr_manager', 'reviewer']): void {
        if (!auth_check()) {
            flash('error', 'Administrator login required.');
            redirect('/admin/login');
        }

        $user = auth();
        if (!in_array($user['role'] ?? '', $allowedRoles, true)) {
            http_response_code(403);
            if (file_exists(APP_PATH . '/views/public/403.php')) {
                require APP_PATH . '/views/public/403.php';
            } else {
                die("<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>403 Forbidden</title><style>body{background:#F7F5EF;color:#26332D;font-family:'DM Sans',system-ui,-apple-system,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px;}div{text-align:center;background:#FFFFFF;padding:36px 32px;border-radius:20px;border:1px solid #E2DDD0;box-shadow:0 12px 32px rgba(38,51,45,0.06);max-width:440px;}h2{color:#C0392B;margin-bottom:8px;}p{color:#6D766F;font-size:14px;margin-bottom:20px;}a{display:inline-block;background:#1F5C4A;color:#FFFFFF;text-decoration:none;font-weight:500;padding:10px 24px;border-radius:999px;font-size:13.5px;}</style></head><body><div><h2>Access Denied</h2><p>You do not have administrative permission to view this section.</p><p><a href='/'>Return to Home</a></p></div></body></html>");
            }
            exit;
        }
    }
}
