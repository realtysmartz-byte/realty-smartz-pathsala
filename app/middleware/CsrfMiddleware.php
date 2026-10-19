<?php
namespace App\Middleware;

class CsrfMiddleware {
    /**
     * Verify CSRF token on mutating HTTP requests (POST, PUT, DELETE, PATCH)
     */
    public static function handle(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if (in_array(strtoupper($method), ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
            $token = $_POST['_csrf_token'] ?? $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            $sessionToken = $_SESSION['_csrf_token'] ?? null;

            if (empty($token) || empty($sessionToken) || !hash_equals($sessionToken, $token)) {
                // If it's an AJAX/API call, return JSON
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    response_json(['success' => false, 'error' => 'Invalid or expired CSRF token. Please refresh the page.'], 419);
                }

                http_response_code(419);
                die("<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Session Expired</title><style>body{background:#F7F5EF;color:#26332D;font-family:'DM Sans',system-ui,-apple-system,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px;}div{text-align:center;background:#FFFFFF;padding:36px 32px;border-radius:20px;border:1px solid #E2DDD0;box-shadow:0 12px 32px rgba(38,51,45,0.06);max-width:440px;}h2{color:#26332D;margin-bottom:8px;}p{color:#6D766F;font-size:14px;margin-bottom:20px;}a{color:#1F5C4A;text-decoration:none;font-weight:500;display:inline-flex;align-items:center;gap:6px;}</style></head><body><div><h2>Security Token Expired</h2><p>Your session or security token has expired. Please go back and refresh the page to continue.</p><p><a href='javascript:history.back()'>← Go Back</a></p></div></body></html>");
            }
        }
    }
}
