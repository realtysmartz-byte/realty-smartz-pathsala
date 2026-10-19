<?php
define('APP_PATH', dirname(__DIR__) . '/app');
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once APP_PATH . '/config/config.php';

// Simulate authenticated session
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'super_admin';

// Test AdminController::dashboard output
ob_start();
$ctrl = new \App\Controllers\AdminController();
$ctrl->dashboard();
$output = ob_get_clean();

echo "Dashboard HTML Length: " . strlen($output) . PHP_EOL;
echo "Has admin-sidebar: " . (str_contains($output, 'admin-sidebar') ? 'YES' : 'NO') . PHP_EOL;
echo "Has Total Candidates: " . (str_contains($output, 'Total Candidates') ? 'YES' : 'NO') . PHP_EOL;
echo "Has Funnel: " . (str_contains($output, 'Candidate Funnel') ? 'YES' : 'NO') . PHP_EOL;
echo "Has Action Required: " . (str_contains($output, 'Action Required') ? 'YES' : 'NO') . PHP_EOL;
echo "Has Recent Test Results: " . (str_contains($output, 'Recent Aptitude Test Results') ? 'YES' : 'NO') . PHP_EOL;

// Test AdminController::candidates output
ob_start();
$ctrl->candidates();
$outputCandidates = ob_get_clean();

echo "\nCandidates HTML Length: " . strlen($outputCandidates) . PHP_EOL;
echo "Has Candidates Table: " . (str_contains($outputCandidates, 'candidatesMainTable') ? 'YES' : 'NO') . PHP_EOL;
echo "Has Father Name col: " . (str_contains($outputCandidates, 'col-father') ? 'YES' : 'NO') . PHP_EOL;
echo "Has WhatsApp button: " . (str_contains($outputCandidates, 'whatsapp-btn') ? 'YES' : 'NO') . PHP_EOL;
echo "Has Column Chooser: " . (str_contains($outputCandidates, 'colChooserBtn') ? 'YES' : 'NO') . PHP_EOL;
echo "Has Bulk Actions: " . (str_contains($outputCandidates, 'bulkActionsBar') ? 'YES' : 'NO') . PHP_EOL;

// Test AdminController::documents output
ob_start();
$ctrl->documents();
$outputDocs = ob_get_clean();

echo "\nDocuments HTML Length: " . strlen($outputDocs) . PHP_EOL;
echo "Has Documents Table: " . (str_contains($outputDocs, 'Candidate Documents Table') ? 'YES' : 'NO') . PHP_EOL;

// Test AdminController::assessments output
ob_start();
$ctrl->assessments();
$outputAssessments = ob_get_clean();

echo "\nAssessments HTML Length: " . strlen($outputAssessments) . PHP_EOL;
echo "Has 5 Sets: " . (str_contains($outputAssessments, 'Set A') && str_contains($outputAssessments, 'Set E') ? 'YES' : 'NO') . PHP_EOL;
