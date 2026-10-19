<?php
/**
 * Phase 5 Automated Verification Suite: Export Suite & Final Review Polish
 * Tests:
 * 1. Candidate Full Profile data integrity
 * 2. Candidate Profile Dossier PDF generation (FPDF + RoundedRect + Light Theme)
 * 3. Candidate Profile Word DOCX generation (OpenXML + Light Theme)
 * 4. Candidate Document Package ZIP creation
 * 5. Candidate Directory CSV Export (UTF-8 BOM + Masked PII)
 * 6. Aptitude Test Scorecard PDF (10 min, 50% benchmark, 5 categories breakdown)
 * 7. Admin Assessment and Question Bank overview queries
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/Database.php';

// Autoloader or manual require
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require_once $file;
});

require_once ROOT_PATH . '/vendor/fpdf/fpdf.php';

use App\Config\Database;
use App\Models\Profile;
use App\Models\Test;
use App\Helpers\PdfHelper;
use App\Helpers\DocxHelper;
use App\Helpers\ExportHelper;

$db = Database::getConnection();

echo "========================================================\n";
echo "   PHASE 5: EXPORT SUITE & FINAL REVIEW TEST SUITE      \n";
echo "========================================================\n\n";

$testsPassed = 0;
$testsTotal = 0;

function assertTest(string $name, bool $condition, string $details = '') {
    global $testsPassed, $testsTotal;
    $testsTotal++;
    if ($condition) {
        $testsPassed++;
        echo "  [PASS] {$name}\n";
    } else {
        echo "  [FAIL] {$name}" . ($details ? " - {$details}" : '') . "\n";
    }
}

// Find a test candidate or create mock candidate
$candidateUser = $db->query("SELECT u.* FROM users u WHERE u.role = 'candidate' ORDER BY u.id DESC LIMIT 1")->fetch();

if (!$candidateUser) {
    echo "Creating temporary candidate for testing...\n";
    $db->prepare("INSERT INTO users (full_name, email, mobile, password, role, status, is_archived, created_at) 
                  VALUES ('Test Candidate Five', 'phase5_cand@test.com', '9876543215', 'hash', 'candidate', 'test_completed', 0, NOW())")
       ->execute();
    $userId = (int)$db->lastInsertId();
    $candidateUser = $db->query("SELECT * FROM users WHERE id = {$userId}")->fetch();
} else {
    $userId = (int)$candidateUser['id'];
}

echo "Testing with Candidate ID #{$userId} ({$candidateUser['full_name']})\n\n";

// 1. Full Profile Data Retrieval
echo "--- 1. CANDIDATE PROFILE RETRIEVAL ---\n";
$fullProfile = Profile::getFullProfile($userId);
assertTest("Profile::getFullProfile returns array", is_array($fullProfile));
assertTest("Candidate contains full_name and email", !empty($fullProfile['full_name']) && !empty($fullProfile['email']));
assertTest("Profile array exists", isset($fullProfile['profile']));
assertTest("Education array exists", isset($fullProfile['education']));
assertTest("Documents array exists", isset($fullProfile['documents']));

// 2. Candidate Dossier PDF Generation
echo "\n--- 2. CANDIDATE PROFILE DOSSIER PDF GENERATION ---\n";
try {
    $pdfBinary = PdfHelper::generateCandidateProfile($fullProfile);
    assertTest("PDF output is non-empty string", is_string($pdfBinary) && strlen($pdfBinary) > 1000, "Length: " . strlen($pdfBinary));
    assertTest("PDF output begins with %PDF header", str_starts_with($pdfBinary, '%PDF-'), "Header: " . substr($pdfBinary, 0, 8));
    assertTest("PDF output contains EOF marker", str_contains($pdfBinary, '%%EOF'));
} catch (Throwable $e) {
    assertTest("PDF generation threw no exceptions", false, $e->getMessage() . "\n" . $e->getTraceAsString());
}

// 3. Candidate Profile Word DOCX Generation
echo "\n--- 3. CANDIDATE PROFILE DOCX GENERATION ---\n";
try {
    $docxBinary = DocxHelper::generateCandidateDocx($fullProfile);
    assertTest("DOCX output is non-empty string", is_string($docxBinary) && strlen($docxBinary) > 500, "Length: " . strlen($docxBinary));
    assertTest("DOCX output begins with PK zip signature", str_starts_with($docxBinary, "PK\x03\x04"));

    // Verify DOCX is a valid ZIP archive containing word/document.xml
    $tempDocx = tempnam(sys_get_temp_dir(), 'test_docx_');
    file_put_contents($tempDocx, $docxBinary);
    $zip = new ZipArchive();
    $isOpen = $zip->open($tempDocx);
    assertTest("DOCX archive opens successfully with ZipArchive", $isOpen === true);
    if ($isOpen === true) {
        $hasDocXml = $zip->locateName('word/document.xml') !== false;
        assertTest("DOCX contains word/document.xml", $hasDocXml);
        $docXmlContent = $zip->getFromName('word/document.xml');
        assertTest("word/document.xml contains candidate name", str_contains($docXmlContent, htmlspecialchars($fullProfile['full_name'])));
        assertTest("word/document.xml uses brand primary color 9A6410", str_contains($docXmlContent, '9A6410'));
        $zip->close();
    }
    @unlink($tempDocx);
} catch (Throwable $e) {
    assertTest("DOCX generation threw no exceptions", false, $e->getMessage());
}

// 4. Candidate Document Package ZIP Export
echo "\n--- 4. CANDIDATE DOCUMENTS ZIP EXPORT ---\n";
try {
    // Create a mock document entry if none exists on disk
    $docsToZip = $fullProfile['documents'] ?? [];
    $tempDocFile = tempnam(sys_get_temp_dir(), 'test_doc_');
    file_put_contents($tempDocFile, "Sample Document Content for Candidate #{$userId}");
    $docsToZip[] = [
        'file_path' => $tempDocFile,
        'document_type' => 'pan_card',
        'version' => 1,
        'original_name' => 'pan_card_test.pdf'
    ];

    $zipBinary = ExportHelper::createCandidateDocumentsZip($docsToZip, $fullProfile['full_name']);
    assertTest("ZIP output is non-empty string", is_string($zipBinary) && strlen($zipBinary) > 100);
    assertTest("ZIP output begins with PK zip signature", str_starts_with($zipBinary, "PK\x03\x04"));

    $tempZip = tempnam(sys_get_temp_dir(), 'verify_zip_');
    file_put_contents($tempZip, $zipBinary);
    $vZip = new ZipArchive();
    $vIsOpen = $vZip->open($tempZip);
    assertTest("ZIP archive opens cleanly", $vIsOpen === true);
    if ($vIsOpen === true) {
        assertTest("ZIP contains files", $vZip->numFiles > 0, "Num files: " . $vZip->numFiles);
        $vZip->close();
    }
    @unlink($tempZip);
    @unlink($tempDocFile);
} catch (Throwable $e) {
    assertTest("ZIP generation threw no exceptions", false, $e->getMessage());
}

// 5. Filtered Candidates CSV Export
echo "\n--- 5. FILTERED CANDIDATES CSV EXPORT ---\n";
try {
    $candidatesList = [$fullProfile];
    $csvContent = ExportHelper::candidatesToCsv($candidatesList);
    assertTest("CSV output is non-empty string", is_string($csvContent) && strlen($csvContent) > 50);
    assertTest("CSV begins with UTF-8 BOM", str_starts_with($csvContent, "\xEF\xBB\xBF"));
    assertTest("CSV contains header row with 'Candidate ID'", str_contains($csvContent, 'Candidate ID') && str_contains($csvContent, 'Full Name'));
    assertTest("CSV contains candidate's name", str_contains($csvContent, $fullProfile['full_name']));
} catch (Throwable $e) {
    assertTest("CSV generation threw no exceptions", false, $e->getMessage());
}

// 6. Aptitude Test Scorecard PDF Generation
echo "\n--- 6. APTITUDE TEST SCORECARD PDF GENERATION ---\n";
try {
    $scorecardData = Test::getScorecard($userId);
    if (!$scorecardData) {
        // Fallback attempt mock
        $scorecardData = [
            'attempt' => [
                'id' => 999,
                'user_id' => $userId,
                'started_at' => date('Y-m-d H:i:s', strtotime('-10 minutes')),
                'completed_at' => date('Y-m-d H:i:s'),
                'score' => 12.0,
                'total_questions' => 15,
                'total_answered' => 15,
                'total_correct' => 12,
                'total_wrong' => 3,
                'percentage' => 80.0,
                'passed' => 1,
                'set_code' => 'Set A',
                'set_name' => 'Evaluation Set A',
                'tab_switch_count' => 0
            ],
            'categories' => [
                ['category_name' => 'Logical Reasoning', 'total_in_cat' => 3, 'answered_in_cat' => 3, 'correct_in_cat' => 3, 'score_in_cat' => 3.0],
                ['category_name' => 'Quantitative Ability', 'total_in_cat' => 3, 'answered_in_cat' => 3, 'correct_in_cat' => 2, 'score_in_cat' => 2.0],
                ['category_name' => 'English & Communication', 'total_in_cat' => 3, 'answered_in_cat' => 3, 'correct_in_cat' => 3, 'score_in_cat' => 3.0],
                ['category_name' => 'Real Estate Basics', 'total_in_cat' => 3, 'answered_in_cat' => 3, 'correct_in_cat' => 2, 'score_in_cat' => 2.0],
                ['category_name' => 'Sales & Negotiation', 'total_in_cat' => 3, 'answered_in_cat' => 3, 'correct_in_cat' => 2, 'score_in_cat' => 2.0]
            ]
        ];
    }

    $scorecardPdf = PdfHelper::generateScorecard($scorecardData['attempt'], $candidateUser, $scorecardData['categories']);
    assertTest("Scorecard PDF is non-empty string", is_string($scorecardPdf) && strlen($scorecardPdf) > 1000);
    assertTest("Scorecard PDF begins with %PDF", str_starts_with($scorecardPdf, '%PDF-'));
    assertTest("Scorecard PDF contains EOF marker", str_contains($scorecardPdf, '%%EOF'));
} catch (Throwable $e) {
    assertTest("Scorecard PDF generation threw no exceptions", false, $e->getMessage());
}

// 7. Admin Assessments Query & Set Distribution
echo "\n--- 7. ADMIN ASSESSMENTS QUERIES ---\n";
$sets = $db->query("SELECT s.*, 
    (SELECT COUNT(*) FROM questions q WHERE q.set_id = s.id AND q.is_archived = 0) as question_count
    FROM aptitude_sets s ORDER BY s.id ASC")->fetchAll();
assertTest("aptitude_sets has 5 sets", count($sets) === 5, "Found: " . count($sets));
$totalQuestions = 0;
foreach ($sets as $s) {
    $totalQuestions += (int)$s['question_count'];
}
assertTest("Total active questions in sets = 75", $totalQuestions === 75, "Count: {$totalQuestions}");

echo "\n========================================================\n";
echo "   PHASE 5 TEST RESULTS: {$testsPassed} / {$testsTotal} PASSED (" . round(($testsPassed / $testsTotal) * 100) . "%)\n";
echo "========================================================\n";

if ($testsPassed === $testsTotal) {
    exit(0);
} else {
    exit(1);
}
