<?php
/**
 * CLI Background Email Dispatcher for Interview Application Automation
 * Usage:
 *   php bin/send_interview_email.php --id=10 --type=new_form
 *   php bin/send_interview_email.php --id=10 --type=selected
 *   php bin/send_interview_email.php --id=10 --type=rejected
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once __DIR__ . '/../app/config/config.php';

$options = getopt('', ['id:', 'type:']);
$id = (int)($options['id'] ?? 0);
$type = $options['type'] ?? '';

if (!$id || !$type) {
    exit("Usage: php bin/send_interview_email.php --id=[ID] --type=[new_form|selected|rejected]\n");
}

$app = \App\Models\InterviewApplication::findById($id);
if (!$app) {
    exit("Application ID {$id} not found.\n");
}

\App\Models\InterviewApplication::sendAutomationEmails($id, $type, $app);
echo "Automation emails processed for Application {$id} ({$type})\n";
