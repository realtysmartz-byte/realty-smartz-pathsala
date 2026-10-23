<?php
require_once __DIR__ . '/../app/config/config.php';

$db = \App\Config\Database::getConnection();

echo "Running Migration...\n";

// 1. Add applied_for_type to interview_applications if not exists
$cols = $db->query("SHOW COLUMNS FROM `interview_applications` LIKE 'applied_for_type'")->fetchAll();
if (empty($cols)) {
    $db->exec("ALTER TABLE `interview_applications` ADD COLUMN `applied_for_type` VARCHAR(50) NOT NULL DEFAULT 'Commercial' AFTER `post_applied`");
    echo "[OK] Added column `applied_for_type` to `interview_applications`.\n";
} else {
    echo "[INFO] Column `applied_for_type` already exists.\n";
}

// 2. Update QR slug to 'interview-candidate'
$stmt = $db->prepare("UPDATE `qr_settings` SET `slug` = 'interview-candidate' WHERE `type` = 'interview_form'");
$stmt->execute();
echo "[OK] Updated QR slug to 'interview-candidate'.\n";

$currentQr = \App\Models\InterviewApplication::getQrSettings();
echo "[VERIFY] QR Settings: Type={$currentQr['type']}, Slug={$currentQr['slug']}, Accepting={$currentQr['accepting_responses']}\n";
