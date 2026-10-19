<?php
namespace App\Middleware;

use App\Config\Database;
use PDO;

class RateLimitMiddleware {
    /**
     * Check if an IP/email pair is currently throttled / locked out (5 attempts = 15 min lock)
     * Lock logic is strictly per IP + email, resets automatically after 15 minutes.
     */
    public static function checkLoginThrottle(string $email, string $ip): ?string {
        $db = Database::getConnection();
        $email = strtolower(trim($email));

        // Auto-cleanup stale attempts older than 15 minutes or expired locks
        $cleanup = $db->prepare("DELETE FROM `login_attempts` WHERE (`locked_until` IS NOT NULL AND `locked_until` <= NOW()) OR (`locked_until` IS NULL AND `last_attempt` < DATE_SUB(NOW(), INTERVAL 15 MINUTE))");
        $cleanup->execute();

        // Check active lock for this specific email + IP combination
        $stmt = $db->prepare("SELECT `id`, `attempts`, `locked_until`, TIMESTAMPDIFF(SECOND, NOW(), `locked_until`) AS `seconds_left` 
                               FROM `login_attempts` 
                               WHERE `email` = :email AND `ip_address` = :ip AND `is_locked` = 1 AND `locked_until` > NOW() 
                               LIMIT 1");
        $stmt->execute([':email' => $email, ':ip' => $ip]);
        $locked = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($locked && isset($locked['seconds_left']) && (int)$locked['seconds_left'] > 0) {
            $minutes = max(1, (int)ceil((int)$locked['seconds_left'] / 60));
            return "Too many attempts, try again in {$minutes} minutes.";
        }

        return null;
    }

    /**
     * Record a failed login attempt for a specific IP + email pair
     */
    public static function recordFailedLogin(string $email, string $ip): void {
        $db = Database::getConnection();
        $email = strtolower(trim($email));

        $stmt = $db->prepare("SELECT `id`, `attempts`, `last_attempt`, TIMESTAMPDIFF(MINUTE, `last_attempt`, NOW()) AS `mins_since_last` 
                               FROM `login_attempts` 
                               WHERE `email` = :email AND `ip_address` = :ip 
                               LIMIT 1");
        $stmt->execute([':email' => $email, ':ip' => $ip]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // If the last attempt was > 15 minutes ago, restart attempt counter at 1
            $isExpired = ((int)($row['mins_since_last'] ?? 0) >= 15);
            $newAttempts = $isExpired ? 1 : ((int)$row['attempts'] + 1);
            $isLocked = ($newAttempts >= 5) ? 1 : 0;

            if ($isLocked) {
                $upd = $db->prepare("UPDATE `login_attempts` 
                                     SET `attempts` = :att, `last_attempt` = NOW(), `is_locked` = 1, `locked_until` = DATE_ADD(NOW(), INTERVAL 15 MINUTE) 
                                     WHERE `id` = :id");
                $upd->execute([':att' => $newAttempts, ':id' => $row['id']]);
            } else {
                $upd = $db->prepare("UPDATE `login_attempts` 
                                     SET `attempts` = :att, `last_attempt` = NOW(), `is_locked` = 0, `locked_until` = NULL 
                                     WHERE `id` = :id");
                $upd->execute([':att' => $newAttempts, ':id' => $row['id']]);
            }
        } else {
            $ins = $db->prepare("INSERT INTO `login_attempts` (`ip_address`, `email`, `attempts`, `last_attempt`, `is_locked`, `locked_until`) 
                                 VALUES (:ip, :email, 1, NOW(), 0, NULL)");
            $ins->execute([':ip' => $ip, ':email' => $email]);
        }
    }

    /**
     * Clear failed attempts upon successful login for this IP + email
     */
    public static function clearLoginThrottle(string $email, string $ip): void {
        $db = Database::getConnection();
        $email = strtolower(trim($email));
        $stmt = $db->prepare("DELETE FROM `login_attempts` WHERE `email` = :email AND `ip_address` = :ip");
        $stmt->execute([':email' => $email, ':ip' => $ip]);
    }

    /**
     * Clear all throttle locks for a given email across all IPs (used by CLI admin reset)
     */
    public static function clearAllForEmail(string $email): void {
        $db = Database::getConnection();
        $email = strtolower(trim($email));
        $stmt = $db->prepare("DELETE FROM `login_attempts` WHERE `email` = :email");
        $stmt->execute([':email' => $email]);
    }
}
