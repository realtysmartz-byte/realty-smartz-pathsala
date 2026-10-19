<?php
define('APP_PATH', dirname(__DIR__) . '/app');
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once APP_PATH . '/config/config.php';

$db = \App\Config\Database::getConnection();

function columnExists($db, $table, $column) {
    try {
        $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return $stmt && $stmt->rowCount() > 0;
    } catch (\Exception $e) {
        return false;
    }
}

function tableExists($db, $table) {
    try {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        return $stmt && $stmt->rowCount() > 0;
    } catch (\Exception $e) {
        return false;
    }
}

echo "Running Migration Phase 3 (Admin Shell & Guest Test Links)...\n";

// 1. Create guest_test_links table
if (!tableExists($db, 'guest_test_links')) {
    $db->exec("CREATE TABLE `guest_test_links` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `token_hash` VARCHAR(64) NOT NULL UNIQUE,
        `user_id` INT NULL,
        `guest_name` VARCHAR(150) NULL,
        `guest_mobile` VARCHAR(20) NULL,
        `guest_email` VARCHAR(150) NULL,
        `set_id` INT NULL,
        `expires_at` DATETIME NOT NULL,
        `max_attempts` INT NOT NULL DEFAULT 1,
        `attempts_used` INT NOT NULL DEFAULT 0,
        `status` ENUM('active', 'in_progress', 'submitted', 'expired', 'revoked') NOT NULL DEFAULT 'active',
        `created_by` INT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `first_used_at` DATETIME NULL,
        `submitted_at` DATETIME NULL,
        `revoked_at` DATETIME NULL,
        INDEX `idx_guest_token` (`token_hash`),
        INDEX `idx_guest_user` (`user_id`),
        INDEX `idx_guest_status` (`status`),
        INDEX `idx_guest_email` (`guest_email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Created table `guest_test_links`.\n";
} else {
    echo "Table `guest_test_links` already exists.\n";
}

// 2. Add guest_test_link_id to test_attempts
if (!columnExists($db, 'test_attempts', 'guest_test_link_id')) {
    $db->exec("ALTER TABLE `test_attempts` ADD COLUMN `guest_test_link_id` INT NULL AFTER `user_id`, ADD INDEX `idx_guest_link` (`guest_test_link_id`)");
    echo "Added `guest_test_link_id` to `test_attempts`.\n";
} else {
    echo "Column `guest_test_link_id` already exists in `test_attempts`.\n";
}

// 3. Add is_guest flag to users
if (!columnExists($db, 'users', 'is_guest')) {
    $db->exec("ALTER TABLE `users` ADD COLUMN `is_guest` TINYINT(1) NOT NULL DEFAULT 0 AFTER `role`");
    echo "Added `is_guest` to `users`.\n";
} else {
    echo "Column `is_guest` already exists in `users`.\n";
}

echo "Migration Phase 3 completed successfully.\n";
