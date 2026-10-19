<?php
require 'app/config/config.php';
require 'app/config/Database.php';

$db = App\Config\Database::getConnection();

echo "Aptitude Sets count: " . $db->query("SELECT COUNT(*) FROM aptitude_sets")->fetchColumn() . "\n";
echo "Questions count: " . $db->query("SELECT COUNT(*) FROM questions")->fetchColumn() . "\n";

echo "=== aptitude_sets ===\n";
foreach ($db->query("DESCRIBE `aptitude_sets`")->fetchAll(PDO::FETCH_ASSOC) as $c) {
    echo "  {$c['Field']} ({$c['Type']})\n";
}

echo "=== questions ===\n";
foreach ($db->query("DESCRIBE `questions`")->fetchAll(PDO::FETCH_ASSOC) as $c) {
    echo "  {$c['Field']} ({$c['Type']})\n";
}

echo "=== test_attempts ===\n";
foreach ($db->query("DESCRIBE `test_attempts`")->fetchAll(PDO::FETCH_ASSOC) as $c) {
    echo "  {$c['Field']} ({$c['Type']})\n";
}

echo "=== ALL TABLES ===\n";
foreach ($db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN) as $t) {
    echo "  $t\n";
}

echo "=== Questions Breakdown (Set x Category) ===\n";
$stmt = $db->query("SELECT set_id, category_id, COUNT(*) as c FROM questions GROUP BY set_id, category_id ORDER BY set_id, category_id");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo "  Set {$r['set_id']} | Category {$r['category_id']} : {$r['c']} question(s)\n";
}

