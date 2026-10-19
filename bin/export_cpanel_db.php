<?php
/**
 * Export complete clean database dump for cPanel phpMyAdmin import
 */
require __DIR__ . '/../app/config/config.php';
require __DIR__ . '/../app/config/Database.php';

$pdo = App\Config\Database::getConnection();
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

$dump = "-- Realty Smartz Pathshala Complete Production Database Dump\n";
$dump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
$dump .= "-- Compatible with cPanel MySQL / MariaDB and phpMyAdmin\n\n";
$dump .= "SET FOREIGN_KEY_CHECKS = 0;\n";
$dump .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
$dump .= "SET time_zone = '+00:00';\n\n";

foreach ($tables as $t) {
    $create = $pdo->query("SHOW CREATE TABLE `{$t}`")->fetch();
    $dump .= "-- --------------------------------------------------------\n";
    $dump .= "-- Table structure for `{$t}`\n";
    $dump .= "-- --------------------------------------------------------\n";
    $dump .= "DROP TABLE IF EXISTS `{$t}`;\n";
    $dump .= $create['Create Table'] . ";\n\n";

    $rows = $pdo->query("SELECT * FROM `{$t}`")->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($rows)) {
        $dump .= "-- Dumping data for table `{$t}`\n";
        $cols = array_keys($rows[0]);
        $escapedCols = array_map(fn($c) => "`{$c}`", $cols);
        $dump .= "INSERT INTO `{$t}` (" . implode(', ', $escapedCols) . ") VALUES\n";

        $valRows = [];
        foreach ($rows as $r) {
            $vals = array_map(function($v) use ($pdo) {
                return ($v === null) ? 'NULL' : $pdo->quote($v);
            }, array_values($r));
            $valRows[] = '  (' . implode(', ', $vals) . ')';
        }
        $dump .= implode(",\n", $valRows) . ";\n\n";
    }
}

$dump .= "SET FOREIGN_KEY_CHECKS = 1;\n";
$target = __DIR__ . '/../database/cpanel_full_db.sql';
file_put_contents($target, $dump);
echo "Successfully exported database/cpanel_full_db.sql (" . number_format(strlen($dump) / 1024, 2) . " KB)\n";
