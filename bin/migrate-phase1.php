<?php
/**
 * Migration & Seed Script for Phase 1
 * Updates DB schema to support all Phase 1-5 requirements and seeds realistic sample data.
 */
define('APP_PATH', dirname(__DIR__) . '/app');
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once APP_PATH . '/config/config.php';

$db = \App\Config\Database::getConnection();

echo "Running Phase 1 Schema Migration...\n";

// Helper to check column existence
function columnExists($db, $table, $column) {
    try {
        $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return $stmt && $stmt->rowCount() > 0;
    } catch (\Exception $e) {
        return false;
    }
}

// 1. Update `users` table
if (!columnExists($db, 'users', 'father_name')) {
    $db->exec("ALTER TABLE `users` ADD COLUMN `father_name` VARCHAR(150) NULL AFTER `full_name`");
    echo "Added users.father_name\n";
}
if (!columnExists($db, 'users', 'whatsapp')) {
    $db->exec("ALTER TABLE `users` ADD COLUMN `whatsapp` VARCHAR(20) NULL AFTER `mobile`");
    echo "Added users.whatsapp\n";
}

// 2. Update `profiles` table
if (!columnExists($db, 'profiles', 'aadhaar_enc')) {
    $db->exec("ALTER TABLE `profiles` ADD COLUMN `aadhaar_enc` TEXT NULL AFTER `pincode`");
    echo "Added profiles.aadhaar_enc\n";
}
if (!columnExists($db, 'profiles', 'pan_enc')) {
    $db->exec("ALTER TABLE `profiles` ADD COLUMN `pan_enc` TEXT NULL AFTER `aadhaar_last4`");
    echo "Added profiles.pan_enc\n";
}
if (!columnExists($db, 'profiles', 'pan_last4')) {
    $db->exec("ALTER TABLE `profiles` ADD COLUMN `pan_last4` VARCHAR(4) NULL AFTER `pan_enc`");
    echo "Added profiles.pan_last4\n";
}
if (!columnExists($db, 'profiles', 'social_links')) {
    $db->exec("ALTER TABLE `profiles` ADD COLUMN `social_links` JSON NULL AFTER `passport_number`");
    echo "Added profiles.social_links\n";
}
if (!columnExists($db, 'profiles', 'address')) {
    $db->exec("ALTER TABLE `profiles` ADD COLUMN `address` TEXT NULL AFTER `photo_path`");
    echo "Added profiles.address\n";
}

// 3. Create `aptitude_sets` table
$db->exec("CREATE TABLE IF NOT EXISTS `aptitude_sets` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `set_code` VARCHAR(10) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `times_assigned` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
echo "Checked/created aptitude_sets table\n";

// Seed 5 sets if not exist
$setCount = (int)$db->query("SELECT COUNT(*) FROM `aptitude_sets`")->fetchColumn();
if ($setCount === 0) {
    $stmtSet = $db->prepare("INSERT INTO `aptitude_sets` (`set_code`, `name`, `description`, `times_assigned`, `is_active`) VALUES (:code, :name, :desc, :assigned, 1)");
    $sets = [
        ['Set A', 'Assessment Set A - Aptitude & Real Estate', 'Covers Logical Reasoning, Quantitative, English, Real Estate Basics, Sales', 12],
        ['Set B', 'Assessment Set B - Aptitude & Real Estate', 'Covers Logical Reasoning, Quantitative, English, Real Estate Basics, Sales', 10],
        ['Set C', 'Assessment Set C - Aptitude & Real Estate', 'Covers Logical Reasoning, Quantitative, English, Real Estate Basics, Sales', 9],
        ['Set D', 'Assessment Set D - Aptitude & Real Estate', 'Covers Logical Reasoning, Quantitative, English, Real Estate Basics, Sales', 8],
        ['Set E', 'Assessment Set E - Aptitude & Real Estate', 'Covers Logical Reasoning, Quantitative, English, Real Estate Basics, Sales', 7],
    ];
    foreach ($sets as $s) {
        $stmtSet->execute([':code' => $s[0], ':name' => $s[1], ':desc' => $s[2], ':assigned' => $s[3]]);
    }
    echo "Seeded 5 aptitude sets (Set A to Set E)\n";
}

// 4. Update `questions` table
if (!columnExists($db, 'questions', 'set_id')) {
    $db->exec("ALTER TABLE `questions` ADD COLUMN `set_id` INT UNSIGNED NULL AFTER `category_id`");
    echo "Added questions.set_id\n";
}
if (!columnExists($db, 'questions', 'difficulty')) {
    $db->exec("ALTER TABLE `questions` ADD COLUMN `difficulty` ENUM('easy', 'medium') NOT NULL DEFAULT 'medium' AFTER `marks`");
    echo "Added questions.difficulty\n";
}

// 5. Update `test_attempts` table
if (!columnExists($db, 'test_attempts', 'set_id')) {
    $db->exec("ALTER TABLE `test_attempts` ADD COLUMN `set_id` INT UNSIGNED NULL AFTER `test_id`");
    echo "Added test_attempts.set_id\n";
}
if (!columnExists($db, 'test_attempts', 'attempt_no')) {
    $db->exec("ALTER TABLE `test_attempts` ADD COLUMN `attempt_no` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `user_id`");
    echo "Added test_attempts.attempt_no\n";
}
if (!columnExists($db, 'test_attempts', 'deadline_at')) {
    $db->exec("ALTER TABLE `test_attempts` ADD COLUMN `deadline_at` DATETIME NULL AFTER `started_at`");
    echo "Added test_attempts.deadline_at\n";
}
if (!columnExists($db, 'test_attempts', 'submitted_at')) {
    $db->exec("ALTER TABLE `test_attempts` ADD COLUMN `submitted_at` DATETIME NULL AFTER `deadline_at`");
    echo "Added test_attempts.submitted_at\n";
}

// 6. Ensure `document_versions` table exists
$db->exec("CREATE TABLE IF NOT EXISTS `document_versions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `document_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `document_type` VARCHAR(50) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `stored_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(100) NOT NULL,
  `file_size` INT UNSIGNED NOT NULL,
  `version` INT UNSIGNED NOT NULL,
  `replaced_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `replaced_by` INT UNSIGNED NULL,
  INDEX `idx_doc_ver_doc` (`document_id`),
  INDEX `idx_doc_ver_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

echo "\nSeeding Realistic Sample Candidates & Test Results for Phase 1 Visual Review...\n";

// Sample candidates across realistic statuses and dates (last 30 days)
$candidatesSample = [
    [
        'name' => 'Aarav Mehta',
        'father' => 'Rajesh Mehta',
        'email' => 'aarav.mehta@example.com',
        'mobile' => '9820123456',
        'whatsapp' => '9820123456',
        'status' => 'approved',
        'city' => 'Gurugram',
        'state' => 'Haryana',
        'completion' => 100,
        'days_ago' => 24,
        'set' => 'Set A',
        'score' => 14,
        'passed' => 1,
        'docs' => ['aadhaar' => 'approved', 'pan' => 'approved', 'photo' => 'approved', 'degree' => 'approved'],
        'time_taken' => '07m 42s',
    ],
    [
        'name' => 'Priya Sharma',
        'father' => 'Suresh Sharma',
        'email' => 'priya.sharma99@example.com',
        'mobile' => '9811234567',
        'whatsapp' => '9811234567',
        'status' => 'under_review',
        'city' => 'New Delhi',
        'state' => 'Delhi',
        'completion' => 100,
        'days_ago' => 2,
        'set' => 'Set B',
        'score' => 12,
        'passed' => 1,
        'docs' => ['aadhaar' => 'pending', 'pan' => 'pending'],
        'time_taken' => '08m 15s',
    ],
    [
        'name' => 'Rohan Varma',
        'father' => 'Anil Varma',
        'email' => 'rohan.varma@example.com',
        'mobile' => '9876501234',
        'whatsapp' => '9876501234',
        'status' => 'test_completed',
        'city' => 'Noida',
        'state' => 'Uttar Pradesh',
        'completion' => 100,
        'days_ago' => 1,
        'set' => 'Set C',
        'score' => 11,
        'passed' => 1,
        'docs' => ['aadhaar' => 'pending', 'pan' => 'pending'],
        'time_taken' => '09m 04s',
    ],
    [
        'name' => 'Ananya Iyer',
        'father' => 'Ramanathan Iyer',
        'email' => 'ananya.iyer@example.com',
        'mobile' => '9845012345',
        'whatsapp' => '9845012345',
        'status' => 'changes_requested',
        'city' => 'Bengaluru',
        'state' => 'Karnataka',
        'completion' => 85,
        'days_ago' => 5,
        'set' => 'Set A',
        'score' => 13,
        'passed' => 1,
        'docs' => ['aadhaar' => 'reupload_requested', 'pan' => 'approved'],
        'time_taken' => '06m 55s',
    ],
    [
        'name' => 'Vikram Singhal',
        'father' => 'Mahesh Singhal',
        'email' => 'vikram.singhal@example.com',
        'mobile' => '9910012345',
        'whatsapp' => '9910012345',
        'status' => 'under_review',
        'city' => 'Faridabad',
        'state' => 'Haryana',
        'completion' => 100,
        'days_ago' => 3,
        'set' => 'Set D',
        'score' => 10,
        'passed' => 1,
        'docs' => ['aadhaar' => 'pending', 'pan' => 'pending'],
        'time_taken' => '09m 30s',
    ],
    [
        'name' => 'Sneha Patil',
        'father' => 'Deepak Patil',
        'email' => 'sneha.patil@example.com',
        'mobile' => '9822012345',
        'whatsapp' => '9822012345',
        'status' => 'approved',
        'city' => 'Pune',
        'state' => 'Maharashtra',
        'completion' => 100,
        'days_ago' => 18,
        'set' => 'Set B',
        'score' => 15,
        'passed' => 1,
        'docs' => ['aadhaar' => 'approved', 'pan' => 'approved', 'offer_letter' => 'approved'],
        'time_taken' => '07m 10s',
    ],
    [
        'name' => 'Karan Malhotra',
        'father' => 'Sunil Malhotra',
        'email' => 'karan.malhotra@example.com',
        'mobile' => '9810098765',
        'whatsapp' => '9810098765',
        'status' => 'email_verified',
        'city' => 'Gurugram',
        'state' => 'Haryana',
        'completion' => 25,
        'days_ago' => 1,
        'set' => null,
        'score' => null,
        'passed' => null,
        'docs' => [],
        'time_taken' => null,
    ],
    [
        'name' => 'Tanvi Chawla',
        'father' => 'Harish Chawla',
        'email' => 'tanvi.chawla@example.com',
        'mobile' => '9871122334',
        'whatsapp' => '9871122334',
        'status' => 'registered',
        'city' => 'Chandigarh',
        'state' => 'Punjab',
        'completion' => 0,
        'days_ago' => 0,
        'set' => null,
        'score' => null,
        'passed' => null,
        'docs' => [],
        'time_taken' => null,
    ],
    [
        'name' => 'Aditya Kashyap',
        'father' => 'Vivek Kashyap',
        'email' => 'aditya.kashyap@example.com',
        'mobile' => '9934012345',
        'whatsapp' => '9934012345',
        'status' => 'profile_completed',
        'city' => 'Patna',
        'state' => 'Bihar',
        'completion' => 75,
        'days_ago' => 4,
        'set' => null,
        'score' => null,
        'passed' => null,
        'docs' => ['aadhaar' => 'pending'], // Missing PAN card
        'time_taken' => null,
    ],
    [
        'name' => 'Divya Nair',
        'father' => 'Gopal Nair',
        'email' => 'divya.nair@example.com',
        'mobile' => '9847012345',
        'whatsapp' => '9847012345',
        'status' => 'test_completed',
        'city' => 'Kochi',
        'state' => 'Kerala',
        'completion' => 100,
        'days_ago' => 6,
        'set' => 'Set E',
        'score' => 7, // Failed test
        'passed' => 0,
        'docs' => ['aadhaar' => 'pending', 'pan' => 'pending'],
        'time_taken' => '10m 00s',
    ],
    [
        'name' => 'Mohit Agarwal',
        'father' => 'Pawan Agarwal',
        'email' => 'mohit.agarwal@example.com',
        'mobile' => '9839012345',
        'whatsapp' => '9839012345',
        'status' => 'under_review',
        'city' => 'Lucknow',
        'state' => 'Uttar Pradesh',
        'completion' => 100,
        'days_ago' => 2,
        'set' => 'Set A',
        'score' => 13,
        'passed' => 1,
        'docs' => ['aadhaar' => 'pending', 'pan' => 'pending'],
        'time_taken' => '08m 40s',
    ],
    [
        'name' => 'Pooja Deshmukh',
        'father' => 'Sanjay Deshmukh',
        'email' => 'pooja.deshmukh@example.com',
        'mobile' => '9823012345',
        'whatsapp' => '9823012345',
        'status' => 'approved',
        'city' => 'Nagpur',
        'state' => 'Maharashtra',
        'completion' => 100,
        'days_ago' => 20,
        'set' => 'Set C',
        'score' => 14,
        'passed' => 1,
        'docs' => ['aadhaar' => 'approved', 'pan' => 'approved'],
        'time_taken' => '07m 30s',
    ],
];

// Helper to encrypt Aadhaar / PAN for demo seed
$encKey = substr(hash('sha256', 'RealtySmartzSecretKey2026!'), 0, 32);
function quickEncrypt($data, $key) {
    $iv = openssl_random_pseudo_bytes(16);
    $enc = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $enc);
}

// Get set map
$setRows = $db->query("SELECT id, set_code FROM `aptitude_sets`")->fetchAll(PDO::FETCH_KEY_PAIR);

// Get default test ID
$testId = (int)$db->query("SELECT id FROM `tests` LIMIT 1")->fetchColumn();
if (!$testId) {
    $db->exec("INSERT INTO `tests` (`title`, `description`, `total_questions`, `time_limit_minutes`, `passing_percentage`) VALUES ('Standard Aptitude Test', 'Realty Smartz Aptitude & Domain Assessment', 15, 10, 60.00)");
    $testId = (int)$db->lastInsertId();
}

foreach ($candidatesSample as $c) {
    // Check if user already exists
    $existing = $db->prepare("SELECT id FROM `users` WHERE `email` = :email");
    $existing->execute([':email' => $c['email']]);
    $userId = $existing->fetchColumn();

    $createdAt = date('Y-m-d H:i:s', strtotime("-{$c['days_ago']} days"));

    if (!$userId) {
        $stmtUser = $db->prepare("INSERT INTO `users` 
            (`full_name`, `father_name`, `email`, `mobile`, `whatsapp`, `password_hash`, `role`, `status`, `email_verified`, `email_verified_at`, `created_at`, `updated_at`)
            VALUES
            (:name, :father, :email, :mobile, :wa, :hash, 'candidate', :status, 1, :ev_at, :created_at, :updated_at)");
        
        $stmtUser->execute([
            ':name' => $c['name'],
            ':father' => $c['father'],
            ':email' => $c['email'],
            ':mobile' => $c['mobile'],
            ':wa' => $c['whatsapp'],
            ':hash' => password_hash('Candidate@123', PASSWORD_BCRYPT, ['cost' => 11]),
            ':status' => $c['status'],
            ':ev_at' => $c['status'] !== 'registered' ? $createdAt : null,
            ':created_at' => $createdAt,
            ':updated_at' => $createdAt
        ]);
        $userId = (int)$db->lastInsertId();
    } else {
        // Update user fields
        $db->prepare("UPDATE `users` SET `father_name` = :father, `whatsapp` = :wa, `status` = :st WHERE `id` = :id")
            ->execute([':father' => $c['father'], ':wa' => $c['whatsapp'], ':st' => $c['status'], ':id' => $userId]);
    }

    // Insert or update Profile
    $socialJson = json_encode([
        'linkedin' => 'https://linkedin.com/in/' . strtolower(str_replace(' ', '', $c['name'])),
        'instagram' => 'https://instagram.com/' . strtolower(str_replace(' ', '', $c['name'])),
        'facebook' => '',
        'twitter' => '',
        'youtube' => '',
        'portfolio' => ''
    ]);

    $aadhaarRaw = '54829103' . str_pad((string)$userId, 4, '0', STR_PAD_LEFT);
    $panRaw = 'ABCDE' . str_pad((string)$userId, 4, '1', STR_PAD_LEFT) . 'Z';

    $stmtProf = $db->prepare("INSERT INTO `profiles` 
        (`user_id`, `father_name`, `dob`, `gender`, `address`, `city`, `state`, `pincode`, `aadhaar_enc`, `aadhaar_last4`, `pan_enc`, `pan_last4`, `social_links`, `completion_percentage`, `declaration_accepted`, `created_at`, `updated_at`)
        VALUES
        (:uid, :father, '1998-05-15', 'male', 'Flat 402, Green Glen Layout, Sector 48', :city, :state, '122001', :a_enc, :a_l4, :p_enc, :p_l4, :social, :comp, 1, :prof_created, :prof_updated)
        ON DUPLICATE KEY UPDATE 
        `father_name` = VALUES(`father_name`),
        `city` = VALUES(`city`),
        `state` = VALUES(`state`),
        `aadhaar_enc` = VALUES(`aadhaar_enc`),
        `aadhaar_last4` = VALUES(`aadhaar_last4`),
        `pan_enc` = VALUES(`pan_enc`),
        `pan_last4` = VALUES(`pan_last4`),
        `social_links` = VALUES(`social_links`),
        `completion_percentage` = VALUES(`completion_percentage`)");

    $stmtProf->execute([
        ':uid' => $userId,
        ':father' => $c['father'],
        ':city' => $c['city'],
        ':state' => $c['state'],
        ':a_enc' => quickEncrypt($aadhaarRaw, $encKey),
        ':a_l4' => substr($aadhaarRaw, -4),
        ':p_enc' => quickEncrypt($panRaw, $encKey),
        ':p_l4' => substr($panRaw, -4),
        ':social' => $socialJson,
        ':comp' => $c['completion'],
        ':prof_created' => $createdAt,
        ':prof_updated' => $createdAt
    ]);

    // Insert Documents
    foreach ($c['docs'] as $docType => $docStatus) {
        $docCheck = $db->prepare("SELECT id FROM `documents` WHERE `user_id` = :uid AND `document_type` = :dt");
        $docCheck->execute([':uid' => $userId, ':dt' => $docType]);
        if (!$docCheck->fetchColumn()) {
            $stmtDoc = $db->prepare("INSERT INTO `documents` 
                (`user_id`, `document_type`, `original_name`, `stored_name`, `file_path`, `mime_type`, `file_size_before`, `file_size_after`, `version`, `status`, `uploaded_at`)
                VALUES
                (:uid, :dt, :orig, :stored, :path, 'application/pdf', 1845200, 620400, 1, :st, :up_at)");
            $stmtDoc->execute([
                ':uid' => $userId,
                ':dt' => $docType,
                ':orig' => ucfirst($docType) . '_' . str_replace(' ', '', $c['name']) . '.pdf',
                ':stored' => md5($userId . $docType) . '.pdf',
                ':path' => 'storage/documents/' . $userId . '/' . md5($userId . $docType) . '.pdf',
                ':st' => $docStatus,
                ':up_at' => $createdAt
            ]);
        }
    }

    // Insert Test Attempt if candidate completed test
    if ($c['score'] !== null && $c['set']) {
        $setId = array_search($c['set'], $setRows);
        if (!$setId) $setId = 1;

        $checkAttempt = $db->prepare("SELECT id FROM `test_attempts` WHERE `user_id` = :uid");
        $checkAttempt->execute([':uid' => $userId]);
        if (!$checkAttempt->fetchColumn()) {
            $pct = round(($c['score'] / 15) * 100, 2);
            $startedAt = date('Y-m-d H:i:s', strtotime($createdAt . ' + 1 hour'));
            $submittedAt = date('Y-m-d H:i:s', strtotime($startedAt . ' + 8 minutes'));

            $stmtAttempt = $db->prepare("INSERT INTO `test_attempts`
                (`test_id`, `set_id`, `user_id`, `attempt_no`, `started_at`, `deadline_at`, `submitted_at`, `expires_at`, `completed_at`, `total_questions`, `total_answered`, `total_correct`, `total_wrong`, `score`, `percentage`, `passed`, `tab_switch_count`, `status`, `created_at`)
                VALUES
                (:tid, :sid, :uid, 1, :start, :deadline, :submit, :expires, :completed, 15, 15, :correct, :wrong, :score, :pct, :passed, 0, 'completed', :created_at)");

            $stmtAttempt->execute([
                ':tid' => $testId,
                ':sid' => $setId,
                ':uid' => $userId,
                ':start' => $startedAt,
                ':deadline' => date('Y-m-d H:i:s', strtotime($startedAt . ' + 10 minutes')),
                ':submit' => $submittedAt,
                ':expires' => date('Y-m-d H:i:s', strtotime($startedAt . ' + 10 minutes')),
                ':completed' => $submittedAt,
                ':correct' => $c['score'],
                ':wrong' => 15 - $c['score'],
                ':score' => $c['score'],
                ':pct' => $pct,
                ':passed' => $c['passed'],
                ':created_at' => $submittedAt
            ]);
        }
    }
}

echo "Migration and seeding completed successfully!\n";
