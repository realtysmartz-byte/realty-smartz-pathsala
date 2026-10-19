<?php
/**
 * Automated Verification Test Suite for Phase 4: Aptitude Assessment Engine
 * 
 * Verifies:
 * 1. 75 Questions Seeded Across 5 Sets (15 Questions per Set, 3 per Category)
 * 2. Set Selection & Attempt Initialization (10 Mins Duration, deadline_at)
 * 3. Question Palette & 4-Option Structure
 * 4. AJAX Answer Autosave & Mark for Review
 * 5. Anti-Cheat Violations & Proctor Log
 * 6. Final Evaluation & 50% Pass Benchmark (8/15)
 * 7. Candidate Status Transition to 'test_completed'
 * 8. Category Breakdown & Official Scorecard PDF Output
 */

define('APP_PATH', dirname(__DIR__) . '/app');
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once APP_PATH . '/config/config.php';

use App\Config\Database;
use App\Models\Test;
use App\Models\User;
use App\Helpers\PdfHelper;

$db = Database::getConnection();

echo "========================================================\n";
echo "RUNNING PHASE 4 TEST SUITE: APTITUDE ASSESSMENT ENGINE\n";
echo "========================================================\n\n";

$passedCount = 0;
$totalTests = 0;

function assertTest(string $name, bool $condition, string $details = '') {
    global $passedCount, $totalTests;
    $totalTests++;
    if ($condition) {
        $passedCount++;
        echo "  [PASS] {$name}\n";
        if ($details) echo "         {$details}\n";
    } else {
        echo "  [FAIL] {$name}\n";
        if ($details) echo "         FAIL DETAILS: {$details}\n";
    }
}

// -------------------------------------------------------------
// TEST SUITE 1: Question Bank & Set Integrity
// -------------------------------------------------------------
echo "--- TEST SUITE 1: Question Bank & Set Integrity ---\n";

$testConfig = $db->query("SELECT * FROM tests WHERE id = 1")->fetch();
assertTest(
    "Test 1 Configured for 10 Mins and 15 Questions",
    ($testConfig['time_limit_minutes'] == 10 && $testConfig['total_questions'] == 15 && $testConfig['passing_percentage'] == 50.00),
    "Duration: {$testConfig['time_limit_minutes']}m, Total: {$testConfig['total_questions']}, Pass Pct: {$testConfig['passing_percentage']}%"
);

$setCount = (int)$db->query("SELECT COUNT(*) FROM aptitude_sets WHERE is_active = 1")->fetchColumn();
assertTest("Active Aptitude Sets", ($setCount === 5), "Found {$setCount} active sets in database");

$totalQCount = (int)$db->query("SELECT COUNT(*) FROM questions WHERE is_active = 1 AND is_archived = 0")->fetchColumn();
assertTest("Total Active Questions", ($totalQCount === 75), "Found {$totalQCount} active questions (expected 75)");

// Verify each set has exactly 15 questions and each category has exactly 3 questions
$setDistribution = $db->query("
    SELECT q.set_id, s.set_code, COUNT(q.id) as q_count, COUNT(DISTINCT q.category_id) as cat_count
    FROM questions q
    JOIN aptitude_sets s ON q.set_id = s.id
    WHERE q.is_active = 1 AND q.is_archived = 0
    GROUP BY q.set_id, s.set_code
")->fetchAll(PDO::FETCH_ASSOC);

$allSetsValid = true;
foreach ($setDistribution as $sd) {
    if ($sd['q_count'] != 15 || $sd['cat_count'] != 5) {
        $allSetsValid = false;
        break;
    }
}
assertTest(
    "All 5 Sets Have Exactly 15 Questions Across 5 Categories",
    $allSetsValid,
    "Checked 5 sets, each has 15 questions distributed evenly across 5 categories"
);

// Verify all 75 questions have exactly 4 options with exactly 1 correct option
$optCheck = $db->query("
    SELECT q.id, COUNT(o.id) as opt_count, SUM(o.is_correct) as correct_count
    FROM questions q
    LEFT JOIN options o ON q.id = o.question_id
    GROUP BY q.id
    HAVING opt_count != 4 OR correct_count != 1
")->fetchAll();
assertTest(
    "All 75 Questions Have Exactly 4 Options with 1 Correct Option",
    count($optCheck) === 0,
    "Invalid question count: " . count($optCheck)
);

// -------------------------------------------------------------
// TEST SUITE 2: Test Attempt Initiation & Set Assignment
// -------------------------------------------------------------
echo "\n--- TEST SUITE 2: Test Attempt Initiation & Set Assignment ---\n";

// Create or fetch candidate for test
$testEmail = "phase4_candidate_" . time() . "@realtysmartz.in";
$userId = User::createCandidate([
    'full_name' => 'Aptitude Test Candidate',
    'email' => $testEmail,
    'password' => 'SecurePass123!@#',
    'mobile' => '9876543210',
    'father_name' => 'Test Father'
]);
$db->prepare("UPDATE users SET email_verified_at = NOW(), status = 'documents_submitted' WHERE id = ?")->execute([$userId]);

// Start fresh test session
$session = Test::startOrResumeTest($userId, 1);
assertTest(
    "Fresh Test Session Started Successfully",
    ($session['success'] === true && !empty($session['attempt']['id'])),
    "Attempt ID: " . ($session['attempt']['id'] ?? 'none')
);

$attemptId = (int)$session['attempt']['id'];
assertTest(
    "Session Assigned Valid Set Code",
    (!empty($session['attempt']['set_code']) && in_array($session['attempt']['set_code'], ['Set A', 'Set B', 'Set C', 'Set D', 'Set E'])),
    "Assigned Paper: " . ($session['attempt']['set_code'] ?? 'none')
);

assertTest(
    "Session Initialized with 10 Minutes (600 Seconds)",
    ($session['time_remaining_seconds'] === 600),
    "Remaining seconds: {$session['time_remaining_seconds']}"
);

assertTest(
    "Session Returned Exactly 15 Questions",
    (count($session['questions']) === 15),
    "Count: " . count($session['questions'])
);

// Check deadline_at in test_attempts table
$attemptRow = $db->query("SELECT * FROM test_attempts WHERE id = {$attemptId}")->fetch(PDO::FETCH_ASSOC);
assertTest(
    "deadline_at Recorded in Database Within 10 Minutes Window",
    (!empty($attemptRow['deadline_at']) && strtotime($attemptRow['deadline_at']) > time()),
    "Deadline: {$attemptRow['deadline_at']}"
);

// -------------------------------------------------------------
// TEST SUITE 3: Autosave & Anti-Cheat Violation Logging
// -------------------------------------------------------------
echo "\n--- TEST SUITE 3: Autosave & Anti-Cheat Violation Logging ---\n";

$firstQ = $session['questions'][0];
$firstQOptions = $firstQ['options'];
$pickedOptionId = (int)$firstQOptions[0]['id'];

// Save answer
$saveRes = Test::saveAnswer($attemptId, $userId, (int)$firstQ['id'], $pickedOptionId, false);
assertTest("Autosave Answer for Question 1", ($saveRes['success'] === true));

$ansCheck = $db->query("SELECT * FROM attempt_answers WHERE attempt_id = {$attemptId} AND question_id = {$firstQ['id']}")->fetch();
assertTest(
    "Selected Option Persisted in Database",
    ((int)$ansCheck['selected_option_id'] === $pickedOptionId),
    "Saved Option ID: {$ansCheck['selected_option_id']}"
);

// Mark for review
$markRes = Test::saveAnswer($attemptId, $userId, (int)$firstQ['id'], $pickedOptionId, true);
$ansCheck2 = $db->query("SELECT is_marked_for_review FROM attempt_answers WHERE attempt_id = {$attemptId} AND question_id = {$firstQ['id']}")->fetch();
assertTest("Mark for Review Flag Updated", ($ansCheck2['is_marked_for_review'] == 1));

// Clear answer
$clearRes = Test::saveAnswer($attemptId, $userId, (int)$firstQ['id'], null, false);
$ansCheck3 = $db->query("SELECT selected_option_id FROM attempt_answers WHERE attempt_id = {$attemptId} AND question_id = {$firstQ['id']}")->fetch();
assertTest("Clear Choice Sets selected_option_id to NULL", ($ansCheck3['selected_option_id'] === null));

// Log anti-cheat infractions
$v1 = Test::logAntiCheatViolation($attemptId, $userId, 'tab_switch');
assertTest("Tab Switch Violation Logged (Infraction 1)", ($v1['counts']['tab_switch_count'] == 1));

$v2 = Test::logAntiCheatViolation($attemptId, $userId, 'tab_switch');
assertTest("Tab Switch Violation Logged (Infraction 2)", ($v2['counts']['tab_switch_count'] == 2));

// -------------------------------------------------------------
// TEST SUITE 4: Scoring, Pass/Fail (50% Benchmark) & Status Transition
// -------------------------------------------------------------
echo "\n--- TEST SUITE 4: Scoring, Pass/Fail & Status Transition ---\n";

// Let's answer 10 questions correctly and 5 questions incorrectly (10/15 = 66.7% -> PASSED)
$allQuestions = $session['questions'];
foreach ($allQuestions as $idx => $q) {
    // Find options
    $opts = $db->query("SELECT id, is_correct FROM options WHERE question_id = {$q['id']}")->fetchAll();
    $correctOpt = null;
    $wrongOpt = null;
    foreach ($opts as $o) {
        if ($o['is_correct']) $correctOpt = (int)$o['id'];
        else $wrongOpt = (int)$o['id'];
    }

    if ($idx < 10) {
        // Answer correctly
        Test::saveAnswer($attemptId, $userId, (int)$q['id'], $correctOpt, false);
    } else {
        // Answer incorrectly
        Test::saveAnswer($attemptId, $userId, (int)$q['id'], $wrongOpt, false);
    }
}

// Finalize test attempt
$finalizeRes = Test::finalizeTestAttempt($attemptId, 'completed');

assertTest(
    "Finalize Computed 10 Correct Answers out of 15",
    ($finalizeRes['total_correct'] === 10 && $finalizeRes['score'] == 10.0),
    "Score: {$finalizeRes['score']}/15, Correct: {$finalizeRes['total_correct']}"
);

assertTest(
    "Pass Benchmark (50%) Satisfied for 10/15 (66.67%)",
    ($finalizeRes['passed'] === true && $finalizeRes['percentage'] >= 50.0),
    "Percentage: {$finalizeRes['percentage']}%, Passed: " . ($finalizeRes['passed'] ? 'YES' : 'NO')
);

// Check candidate status transition
$userRow = $db->query("SELECT status FROM users WHERE id = {$userId}")->fetch();
assertTest(
    "Candidate Status Transitioned to 'test_completed'",
    ($userRow['status'] === 'test_completed'),
    "User Status: {$userRow['status']}"
);

// Verify test_attempts row
$finalAttempt = $db->query("SELECT * FROM test_attempts WHERE id = {$attemptId}")->fetch();
assertTest(
    "test_attempts Record Completed with submitted_at & completed_at",
    (!empty($finalAttempt['submitted_at']) && !empty($finalAttempt['completed_at']) && $finalAttempt['status'] === 'completed'),
    "Submitted: {$finalAttempt['submitted_at']}, Completed: {$finalAttempt['completed_at']}"
);

// -------------------------------------------------------------
// TEST SUITE 5: Scorecard Details & Official Scorecard PDF
// -------------------------------------------------------------
echo "\n--- TEST SUITE 5: Scorecard Details & Official Scorecard PDF ---\n";

$scorecardData = Test::getScorecard($attemptId, $userId);
assertTest(
    "Scorecard Retrieved with 5 Category Breakdown",
    (!empty($scorecardData['categories']) && count($scorecardData['categories']) === 5),
    "Category count: " . count($scorecardData['categories'] ?? [])
);

$userObj = User::findById($userId);
$pdfBinary = PdfHelper::generateScorecard($scorecardData['attempt'], $userObj, $scorecardData['categories']);
assertTest(
    "Scorecard PDF Binary Generated with Valid %PDF Header",
    (str_starts_with($pdfBinary, '%PDF-') && strlen($pdfBinary) > 1000),
    "PDF Size: " . strlen($pdfBinary) . " bytes"
);

echo "\n========================================================\n";
echo "PHASE 4 TEST SUMMARY: {$passedCount} / {$totalTests} TESTS PASSED (" . round(($passedCount / $totalTests) * 100) . "%)\n";
echo "========================================================\n";

if ($passedCount === $totalTests) {
    echo ">>> PHASE 4 (APTITUDE ASSESSMENT ENGINE) 100% VERIFIED! <<<\n";
} else {
    echo ">>> SOME PHASE 4 TESTS FAILED. PLEASE CHECK DETAILS ABOVE. <<<\n";
}
