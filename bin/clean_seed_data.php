<?php
/**
 * Realty Smartz Pathshala - Production Seed Data Cleaner
 * Purges all mock/test candidate records, dummy KYC uploads, test attempts, and candidate audit logs.
 * Preserves admin accounts, test questions bank, site content, and settings.
 */

require __DIR__ . '/../app/config/config.php';
require __DIR__ . '/../app/config/Database.php';

$pdo = App\Config\Database::getConnection();

echo "Starting Seed Data Purge...\n";

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("DROP TRIGGER IF EXISTS `trg_prevent_user_delete`");
$pdo->exec("DROP TRIGGER IF EXISTS `trg_prevent_document_delete`");

// 1. Delete mock candidate records from users (keep super_admin, hr_manager, reviewer)
$deletedUsers = $pdo->exec("DELETE FROM users WHERE role = 'candidate'");
echo "Deleted {$deletedUsers} candidate user records.\n";

// 2. Truncate candidate-dependent tables
$tablesToClear = [
    'profiles',
    'documents',
    'document_versions',
    'emergency_contacts',
    'bank_details',
    'education',
    'experience',
    'test_attempts',
    'attempt_answers',
    'admin_comments',
    'guest_test_links',
    'email_verifications',
    'status_history',
    'login_attempts',
    'password_resets',
    'notifications',
    'enquiries',
    'interview_applications',
    'interview_employment_rows',
    'interview_field_versions',
    'interview_email_log'
];

foreach ($tablesToClear as $table) {
    $pdo->exec("TRUNCATE TABLE `{$table}`");
    echo "Cleared table: `{$table}`\n";
}

// 3. Clear candidate-specific audit logs
$pdo->exec("TRUNCATE TABLE `audit_logs`");
echo "Cleared candidate audit logs.\n";

// 4. Clean storage/uploads folder of test documents (preserve .gitkeep)
$uploadsDir = __DIR__ . '/../storage/uploads';
if (is_dir($uploadsDir)) {
    $files = scandir($uploadsDir);
    $removedCount = 0;
    foreach ($files as $f) {
        if ($f !== '.' && $f !== '..' && $f !== '.gitkeep') {
            @unlink($uploadsDir . '/' . $f);
            $removedCount++;
        }
    }
    echo "Removed {$removedCount} test uploaded files from storage/uploads.\n";
}

// 5. Re-create safety triggers
$pdo->exec("
CREATE TRIGGER `trg_prevent_user_delete`
BEFORE DELETE ON `users`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Data Safety Policy: Hard DELETE is prohibited on users. Use is_archived = 1 instead.';
END
");

$pdo->exec("
CREATE TRIGGER `trg_prevent_document_delete`
BEFORE DELETE ON `documents`
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Data Safety Policy: Hard DELETE is prohibited on documents. Use is_archived = 1 instead.';
END
");

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

echo "\nRemaining Users:\n";
$remaining = $pdo->query("SELECT id, full_name, email, role FROM users")->fetchAll(PDO::FETCH_ASSOC);
foreach ($remaining as $r) {
    echo "  [ID {$r['id']}] {$r['role']}: {$r['email']} ({$r['full_name']})\n";
}

echo "\nRemaining Core Content:\n";
echo "  - Questions: " . $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn() . "\n";
echo "  - Options: " . $pdo->query("SELECT COUNT(*) FROM options")->fetchColumn() . "\n";
echo "  - Aptitude Sets: " . $pdo->query("SELECT COUNT(*) FROM aptitude_sets")->fetchColumn() . "\n";
echo "  - Projects: " . $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn() . "\n";
echo "  - Settings: " . $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn() . "\n";
echo "  - Candidates: " . $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'candidate'")->fetchColumn() . " (clean slate)\n";

echo "\nRe-exporting clean production database dump to database/cpanel_full_db.sql...\n";
require __DIR__ . '/export_cpanel_db.php';

echo "\nSEED DATA PURGE COMPLETE!\n";
