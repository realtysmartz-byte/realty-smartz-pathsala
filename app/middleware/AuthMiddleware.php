<?php
namespace App\Middleware;

use App\Config\Database;

class AuthMiddleware {
    /**
     * Protect candidate routes
     */
    public static function handle(): void {
        if (!auth_check()) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                response_json(['success' => false, 'error' => 'Your session has expired. Please log in again.'], 401);
            }
            flash('error', 'Please log in to access your candidate dashboard.');
            redirect('/login');
        }

        $user = auth();

        // Verify account is active in DB
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, full_name, email, role, status, email_verified_at, is_locked, is_archived FROM `users` WHERE `id` = :id LIMIT 1");
        $stmt->execute([':id' => $user['id']]);
        $freshUser = $stmt->fetch();

        if (!$freshUser || $freshUser['is_archived']) {
            unset($_SESSION['user']);
            flash('error', 'This account has been deactivated or does not exist.');
            redirect('/login');
        }

        if ($freshUser['is_locked']) {
            unset($_SESSION['user']);
            flash('error', 'Your account is currently locked. Please contact HR support.');
            redirect('/login');
        }

        // Update session cache with latest database state
        $_SESSION['user'] = array_merge($_SESSION['user'], $freshUser);

        // Check email verification
        if (empty($freshUser['email_verified_at']) && $_SERVER['REQUEST_URI'] !== '/verify-email' && !str_starts_with($_SERVER['REQUEST_URI'], '/verify-email')) {
            flash('warning', 'Please verify your email address to unlock your onboarding profile.');
            redirect('/verify-email');
        }
    }
}
