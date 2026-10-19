<?php
define('APP_PATH', dirname(__DIR__) . '/app');
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once APP_PATH . '/config/config.php';

$db = \App\Config\Database::getConnection();
$user = $db->query("SELECT id FROM users WHERE role = 'candidate' LIMIT 1")->fetch();
if (!$user) {
    echo "No candidate found in DB for test.\n";
    exit;
}

$candidate = \App\Models\Profile::getFullProfile((int)$user['id']);
$pdfBytes = \App\Helpers\PdfHelper::generateFullCandidateFile($candidate);
echo "SUCCESS: Full File PDF generated for candidate #{$user['id']}! Size: " . strlen($pdfBytes) . " bytes (Header: " . substr($pdfBytes, 0, 5) . ")\n";
