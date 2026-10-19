<?php
define('APP_PATH', dirname(__DIR__) . '/app');
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once APP_PATH . '/config/config.php';

$db = \App\Config\Database::getConnection();
$cols = $db->query("SHOW COLUMNS FROM `profiles`")->fetchAll(PDO::FETCH_COLUMN);
echo "Profiles columns:\n" . implode(', ', $cols) . "\n\n";

$uCols = $db->query("SHOW COLUMNS FROM `users`")->fetchAll(PDO::FETCH_COLUMN);
echo "Users columns:\n" . implode(', ', $uCols) . "\n";
