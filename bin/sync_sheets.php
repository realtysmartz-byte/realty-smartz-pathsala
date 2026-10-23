<?php
/**
 * CLI Background Sync for Google Sheets
 * Usage:
 *   php bin/sync_sheets.php --type=interview --id=10
 *   php bin/sync_sheets.php --type=candidate --id=25
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once __DIR__ . '/../app/config/config.php';

$options = getopt('', ['type:', 'id:']);
$type = $options['type'] ?? '';
$id = (int)($options['id'] ?? 0);

if (!$type || !$id) {
    exit("Usage: php bin/sync_sheets.php --type=[interview|candidate] --id=[ID]\n");
}

if ($type === 'interview') {
    $res = \App\Helpers\GoogleSheetsService::syncInterviewApplication($id);
    echo "Interview sync result: " . json_encode($res) . "\n";
} elseif ($type === 'candidate') {
    $res = \App\Helpers\GoogleSheetsService::syncCandidate($id);
    echo "Candidate sync result: " . json_encode($res) . "\n";
} else {
    exit("Unknown type: {$type}\n");
}
