<?php
require __DIR__ . '/../app/config/config.php';
require __DIR__ . '/../app/config/Database.php';

$db = App\Config\Database::getConnection();
$tables = ['documents', 'document_versions', 'audit_logs'];

foreach ($tables as $t) {
    echo "=== $t ===\n";
    try {
        $cols = $db->query("DESCRIBE `$t`")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $c) {
            echo "  {$c['Field']} ({$c['Type']})\n";
        }
    } catch (Exception $e) {
        echo "  Table `$t` error or missing: " . $e->getMessage() . "\n";
    }
}
