<?php
/**
 * Realty Smartz Pathshala - Automated CLI Database Backup Script
 * Usage: php bin/backup.php
 * Can be scheduled in cPanel Cron:
 * 0 2 * * * /usr/local/bin/php /home/USERNAME/pathshala_app/bin/backup.php > /dev/null 2>&1
 */

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('STORAGE_PATH', ROOT_PATH . '/storage');

require_once ROOT_PATH . '/vendor/autoload.php';

// Load environment variables
if (file_exists(ROOT_PATH . '/.env')) {
    $lines = file(ROOT_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        putenv(trim($k) . '=' . trim($v, " \t\n\r\0\x0B\"'"));
    }
}

try {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $name = getenv('DB_NAME') ?: (getenv('DB_DATABASE') ?: 'realty_smartz_db');
    $user = getenv('DB_USER') ?: (getenv('DB_USERNAME') ?: 'root');
    $pass = getenv('DB_PASS') ?: (getenv('DB_PASSWORD') ?: '');

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    $dump = "-- Realty Smartz Pathshala Automated CLI Snapshot\n";
    $dump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    $dump .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    foreach ($tables as $t) {
        $createTable = $pdo->query("SHOW CREATE TABLE `{$t}`")->fetch();
        $dump .= "DROP TABLE IF EXISTS `{$t}`;\n" . $createTable['Create Table'] . ";\n\n";

        $rows = $pdo->query("SELECT * FROM `{$t}`")->fetchAll();
        if (!empty($rows)) {
            $cols = array_keys($rows[0]);
            $escapedCols = array_map(fn($c) => "`{$c}`", $cols);
            $dump .= "INSERT INTO `{$t}` (" . implode(', ', $escapedCols) . ") VALUES\n";

            $valRows = [];
            foreach ($rows as $r) {
                $vals = array_map(function($v) use ($pdo) {
                    return ($v === null) ? "NULL" : $pdo->quote($v);
                }, array_values($r));
                $valRows[] = "(" . implode(', ', $vals) . ")";
            }
            $dump .= implode(",\n", $valRows) . ";\n\n";
        }
    }
    $dump .= "SET FOREIGN_KEY_CHECKS = 1;\n";

    $backupDir = STORAGE_PATH . '/backups';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    $filename = 'backup_cron_' . date('Y-m-d_H-i-s') . '.sql';
    $path = $backupDir . '/' . $filename;

    if (function_exists('gzencode')) {
        $gzData = gzencode($dump, 9);
        file_put_contents($path . '.gz', $gzData);
        echo "[SUCCESS] Compressed backup generated: {$filename}.gz (" . round(strlen($gzData)/1024, 2) . " KB)\n";
    } else {
        file_put_contents($path, $dump);
        echo "[SUCCESS] SQL backup generated: {$filename} (" . round(strlen($dump)/1024, 2) . " KB)\n";
    }

    exit(0);
} catch (Throwable $e) {
    echo "[ERROR] Database backup failed: " . $e->getMessage() . "\n";
    exit(1);
}
