<?php
define('APP_PATH', dirname(__DIR__) . '/app');
require_once APP_PATH . '/config/config.php';

$email = $argv[1] ?? '';
if (!$email) {
    echo '';
    exit(0);
}

$u = \App\Models\User::findByEmail($email);
if (!$u) {
    echo '';
    exit(0);
}

$db = \App\Config\Database::getConnection();
$stmt = $db->prepare('SELECT otp FROM email_verifications WHERE user_id = :u ORDER BY id DESC LIMIT 1');
$stmt->execute([':u' => $u['id']]);
echo $stmt->fetchColumn() ?: '';
