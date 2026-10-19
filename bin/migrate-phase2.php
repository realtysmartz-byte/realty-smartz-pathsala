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

if (!columnExists($db, 'profiles', 'whatsapp')) {
    $db->exec("ALTER TABLE `profiles` ADD COLUMN `whatsapp` VARCHAR(20) NULL AFTER `father_name`");
    echo "Added profiles.whatsapp\n";
} else {
    echo "profiles.whatsapp already exists\n";
}
