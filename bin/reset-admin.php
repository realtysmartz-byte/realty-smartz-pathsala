<?php
/**
 * Realty Smartz Pathshala - Super Admin Reset & Provisioning CLI
 * Usage: 
 *   Interactive mode:  php bin/reset-admin.php
 *   Direct args mode:  php bin/reset-admin.php [email] [password]
 * Note: Never execute from web / public root.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "Direct web access prohibited. This script can only be run via CLI.\n";
    exit(1);
}

// Bootstrap environment
require_once dirname(__DIR__) . '/app/config/config.php';
require_once dirname(__DIR__) . '/app/config/database.php';
require_once dirname(__DIR__) . '/app/models/User.php';
require_once dirname(__DIR__) . '/app/middleware/RateLimitMiddleware.php';
require_once dirname(__DIR__) . '/app/controllers/AuthController.php';

use App\Config\Database;
use App\Models\User;
use App\Middleware\RateLimitMiddleware;
use App\Controllers\AuthController;

echo "\n";
echo "===============================================================\n";
echo " Realty Smartz Pathshala - Super Admin Management CLI Tool    \n";
echo "===============================================================\n\n";

// Helper for cleaning CLI string input
function cleanCliInput(string $str): string {
    // Strip control characters, BOM, and trailing newlines/returns
    $str = preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/', '', $str);
    return trim($str);
}

// Helper for CLI prompts
function prompt(string $message, string $default = ''): string {
    if ($default !== '') {
        echo "{$message} [{$default}]: ";
    } else {
        echo "{$message}: ";
    }
    $raw = fgets(STDIN);
    if ($raw === false) {
        return $default;
    }
    $input = cleanCliInput($raw);
    return $input !== '' ? $input : $default;
}

// Helper for interactive password prompt
function promptPassword(string $message): string {
    echo "{$message}: ";
    $raw = fgets(STDIN);
    return cleanCliInput($raw ?: '');
}

try {
    $db = Database::getConnection();

    // 1. Determine Email (from argv or interactive prompt)
    $argEmail = isset($argv[1]) ? cleanCliInput($argv[1]) : '';
    if (!empty($argEmail)) {
        $email = strtolower($argEmail);
        echo "Using email: {$email}\n";
    } else {
        $defaultEmail = 'admin@realtysmartzpathshala.in';
        $email = strtolower(prompt("Enter Super Admin Email Address", $defaultEmail));
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Error: Invalid email address format ('{$email}').\n";
        exit(1);
    }

    // 2. Check if user already exists
    $existing = User::findByEmail($email);
    $fullName = "Super Admin";
    $mobile = "+919876543210";

    if ($existing) {
        echo "[INFO] Found existing account for '{$email}' (ID: #{$existing['id']}, Current Name: {$existing['full_name']}).\n";
        $fullName = $existing['full_name'] ?: "Super Admin";
        $mobile = $existing['mobile'] ?: "+919876543210";
    } else {
        echo "[INFO] No account found for '{$email}'. A new Super Admin account will be created.\n";
        if (empty($argv[1])) {
            $fullName = prompt("Enter Super Admin Full Name", "Super Admin");
            $mobile = prompt("Enter Mobile Number", "+919876543210");
        }
    }

    // 3. Determine Password (from argv or interactive prompt)
    $argPassword = isset($argv[2]) ? cleanCliInput($argv[2]) : '';
    if (!empty($argPassword)) {
        $password = $argPassword;
    } else {
        echo "\n";
        $pwd1 = promptPassword("Enter new password (min 8 chars)");
        if (strlen($pwd1) < 8) {
            echo "Error: Password must be at least 8 characters long.\n";
            exit(1);
        }

        $pwd2 = promptPassword("Confirm new password");
        if ($pwd1 !== $pwd2) {
            echo "Error: Passwords do not match. Aborting.\n";
            exit(1);
        }
        $password = $pwd1;
    }

    if (strlen($password) < 8) {
        echo "Error: Password must be at least 8 characters long.\n";
        exit(1);
    }

    // 4. Hash password with PASSWORD_BCRYPT (cost 11)
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 11]);

    // 5. Update or Insert
    if ($existing) {
        $stmt = $db->prepare("UPDATE `users` SET 
            `full_name` = :name,
            `mobile` = :mobile,
            `password_hash` = :hash,
            `role` = 'super_admin',
            `status` = 'active',
            `email_verified` = 1,
            `email_verified_at` = IFNULL(`email_verified_at`, NOW()),
            `is_locked` = 0,
            `locked_until` = NULL,
            `failed_login_attempts` = 0,
            `is_archived` = 0,
            `updated_at` = NOW()
            WHERE `id` = :id");
        $stmt->execute([
            ':name'   => $fullName,
            ':mobile' => $mobile,
            ':hash'   => $hash,
            ':id'     => $existing['id']
        ]);
        $adminId = (int)$existing['id'];
        $action = "Updated & Activated";
    } else {
        $stmt = $db->prepare("INSERT INTO `users` 
            (`full_name`, `email`, `mobile`, `password_hash`, `role`, `status`, `email_verified`, `email_verified_at`, `is_locked`, `failed_login_attempts`, `is_archived`, `created_at`, `updated_at`)
            VALUES 
            (:name, :email, :mobile, :hash, 'super_admin', 'active', 1, NOW(), 0, 0, 0, NOW(), NOW())");
        $stmt->execute([
            ':name'   => $fullName,
            ':email'  => $email,
            ':mobile' => $mobile,
            ':hash'   => $hash
        ]);
        $adminId = (int)$db->lastInsertId();
        $action = "Created & Provisioned";
    }

    // 6. Clear all rate limit / failed attempt records for this email
    RateLimitMiddleware::clearAllForEmail($email);

    // 7. Log to /storage/logs/auth.log
    AuthController::logAuthEvent('CLI_RESET', $email, '127.0.0.1', "Super admin {$action} successfully via bin/reset-admin.php", $adminId);

    echo "\n===============================================================\n";
    echo " SUCCESS! Super Admin Account Ready\n";
    echo "===============================================================\n";
    echo " Action:         {$action}\n";
    echo " User ID:        #{$adminId}\n";
    echo " Email:          {$email}\n";
    echo " Full Name:      {$fullName}\n";
    echo " Role:           super_admin\n";
    echo " Status:         active\n";
    echo " Email Verified: 1 (Active)\n";
    echo " Login Locks:    Cleared (0 active locks)\n";
    echo " Login URL:      " . url('/admin/login') . "\n";
    echo "===============================================================\n\n";

    exit(0);
} catch (Throwable $e) {
    echo "\nFATAL ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
