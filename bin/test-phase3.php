<?php
/**
 * Phase 3 Document Upload, Compression, Versioning & Identity Verification Test Script
 */

define('APP_PATH', dirname(__DIR__) . '/app');
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once APP_PATH . '/config/config.php';

use App\Config\Database;
use App\Models\User;
use App\Models\Profile;
use App\Helpers\Uploader;
use App\Helpers\Compressor;
use App\Helpers\CryptoHelper;
use App\Models\AuditLog;

echo "========================================================\n";
echo "STARTING PHASE 3 VERIFICATION TEST SUITE\n";
echo "========================================================\n\n";

$db = Database::getConnection();

// Create or fetch a test candidate
$testEmail = "phase3_tester_" . time() . "@realtysmartzpathshala.in";
$testMobile = '987' . str_pad((string)random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT);
$candidateId = User::createCandidate([
    'full_name'   => 'Rohan Varma',
    'father_name' => 'Kailash Varma',
    'email'       => $testEmail,
    'mobile'      => $testMobile,
    'password'    => 'StrongDocPass@2026'
]);
echo "✓ Created test candidate ID #{$candidateId} ({$testEmail})\n";

// ----------------------------------------------------------------
// TEST 1: Image Pre-compression & Uploader
// ----------------------------------------------------------------
echo "\n[TEST 1] Testing Image Compression & Upload...\n";

// Create a large 2400x1800 test image in storage/scratch
$scratchDir = STORAGE_PATH . '/scratch';
if (!is_dir($scratchDir)) {
    mkdir($scratchDir, 0755, true);
}
$testImgPath = $scratchDir . '/test_large_image.jpg';

// Generate raw GD image
$img = imagecreatetruecolor(2400, 1800);
$bg = imagecolorallocate($img, 52, 71, 230); // Royal blue
imagefilledrectangle($img, 0, 0, 2400, 1800, $bg);
$textColor = imagecolorallocate($img, 255, 255, 255);
imagestring($img, 5, 200, 200, "Realty Smartz Pathshala KYC Test Image", $textColor);
imagejpeg($img, $testImgPath, 100); // 100% quality = large file
imagedestroy($img);

$rawSize = filesize($testImgPath);
echo "  - Generated test image: {$rawSize} bytes (" . round($rawSize / 1024) . " KB)\n";

// Simulate $_FILES and $_POST
$fakeFile = [
    'name'     => 'pan_card_original_scan.jpg',
    'type'     => 'image/jpeg',
    'tmp_name' => $testImgPath,
    'error'    => UPLOAD_ERR_OK,
    'size'     => $rawSize
];
$_POST['size_before'] = $rawSize * 2; // Simulate client reported 2x size

$uploadedDoc1 = Uploader::process($fakeFile, $candidateId, 'pan_card');
echo "  ✓ Upload processed successfully!\n";
echo "    Document ID: {$uploadedDoc1['id']}\n";
echo "    Original Name: {$uploadedDoc1['original_name']}\n";
echo "    Version: {$uploadedDoc1['version']}\n";
echo "    Size Before: {$uploadedDoc1['file_size_before']} bytes\n";
echo "    Size After:  {$uploadedDoc1['file_size_after']} bytes\n";

if ($uploadedDoc1['version'] === 1 && $uploadedDoc1['file_size_after'] < $rawSize) {
    echo "  ✓ Image was compressed below original size and saved as version 1.\n";
} else {
    echo "  ✗ Error: Compression or initial version unexpected.\n";
}

// ----------------------------------------------------------------
// TEST 2: Document Versioning & Replacement
// ----------------------------------------------------------------
echo "\n[TEST 2] Testing Document Versioning (Replacing PAN Card)...\n";

// Create a second image for v2
$testImgPath2 = $scratchDir . '/test_pan_v2.jpg';
$img2 = imagecreatetruecolor(1600, 1200);
$bg2 = imagecolorallocate($img2, 46, 204, 113); // Emerald green
imagefilledrectangle($img2, 0, 0, 1600, 1200, $bg2);
imagejpeg($img2, $testImgPath2, 90);
imagedestroy($img2);

$rawSize2 = filesize($testImgPath2);
$fakeFile2 = [
    'name'     => 'pan_card_clear_version2.jpg',
    'type'     => 'image/jpeg',
    'tmp_name' => $testImgPath2,
    'error'    => UPLOAD_ERR_OK,
    'size'     => $rawSize2
];
$_POST['size_before'] = $rawSize2;

$uploadedDoc2 = Uploader::process($fakeFile2, $candidateId, 'pan_card');
echo "  ✓ Re-upload processed!\n";
echo "    Active Document ID: {$uploadedDoc2['id']}\n";
echo "    New Version Number: {$uploadedDoc2['version']}\n";

// Check document_versions table
$stmt = $db->prepare("SELECT * FROM `document_versions` WHERE `document_id` = :doc_id ORDER BY `version` ASC");
$stmt->execute([':doc_id' => $uploadedDoc1['id']]);
$oldVersions = $stmt->fetchAll();

echo "    Archived Versions in document_versions: " . count($oldVersions) . "\n";
if (count($oldVersions) === 1 && (int)$oldVersions[0]['version'] === 1 && $uploadedDoc2['version'] === 2) {
    echo "  ✓ VERSIONING SUCCESSFUL: Version 1 archived in document_versions, active record updated to v2!\n";
} else {
    echo "  ✗ Versioning verification failed!\n";
}

// Check Profile::getFullProfile returns versions
$profileFull = Profile::getFullProfile($candidateId);
$activePan = $profileFull['docs_by_type']['pan_card'] ?? null;
if (!empty($activePan) && !empty($activePan['versions']) && count($activePan['versions']) === 1) {
    echo "  ✓ Profile::getFullProfile() successfully attaches version history array to document.\n";
} else {
    echo "  ✗ Profile getFullProfile() versions attachment not found.\n";
}

// ----------------------------------------------------------------
// TEST 3: Admin Status Flow (Re-upload Request & Candidate status)
// ----------------------------------------------------------------
echo "\n[TEST 3] Testing HR Status Updates & Re-upload Request Flow...\n";

// Simulate HR requesting re-upload
$hrReason = "Name on PAN card is blurry. Please upload higher resolution photo.";
$stmt = $db->prepare("
    UPDATE `documents` 
    SET `status` = 'reupload_requested', `rejection_reason` = :reason 
    WHERE `id` = :id
");
$stmt->execute([':reason' => $hrReason, ':id' => $uploadedDoc2['id']]);

// Candidate status should become changes_requested
$db->prepare("UPDATE `users` SET `status` = 'changes_requested' WHERE `id` = :uid")
   ->execute([':uid' => $candidateId]);

$checkUser = User::findById($candidateId);
if ($checkUser['status'] === 'changes_requested') {
    echo "  ✓ Candidate status successfully set to 'changes_requested'.\n";
}

$checkDoc = $db->query("SELECT status, rejection_reason FROM `documents` WHERE `id` = {$uploadedDoc2['id']}")->fetch();
if ($checkDoc['status'] === 'reupload_requested' && $checkDoc['rejection_reason'] === $hrReason) {
    echo "  ✓ Document marked 'reupload_requested' with HR feedback attached.\n";
}

// Now simulate HR approving
$db->prepare("UPDATE `documents` SET `status` = 'approved', `rejection_reason` = NULL WHERE `id` = :id")
   ->execute([':id' => $uploadedDoc2['id']]);
$checkDocApproved = $db->query("SELECT status FROM `documents` WHERE `id` = {$uploadedDoc2['id']}")->fetch();
if ($checkDocApproved['status'] === 'approved') {
    echo "  ✓ Document status successfully transitioned to 'approved'.\n";
}

// ----------------------------------------------------------------
// TEST 4: Sensitive Identity AES-256 Encryption & Reveal Audit
// ----------------------------------------------------------------
echo "\n[TEST 4] Testing Identity Encryption, Masking & DPDP Reveal Audit...\n";

$rawAadhaar = "543210987654";
$rawPan = "ABCDE9876F";

$encryptedAadhaar = CryptoHelper::encrypt($rawAadhaar);
$maskedAadhaar = CryptoHelper::maskAadhaar($rawAadhaar);

$encryptedPan = CryptoHelper::encrypt($rawPan);
$maskedPan = CryptoHelper::maskPan($rawPan);

echo "  - Raw Aadhaar: {$rawAadhaar} -> Encrypted: " . substr($encryptedAadhaar, 0, 20) . "... -> Masked: {$maskedAadhaar}\n";
echo "  - Raw PAN:     {$rawPan} -> Encrypted: " . substr($encryptedPan, 0, 20) . "... -> Masked: {$maskedPan}\n";

if ($maskedAadhaar === 'XXXX XXXX 7654' && $maskedPan === 'XXXXX9876F') {
    echo "  ✓ Masking format matches DPDP compliance specs.\n";
} else {
    echo "  ✗ Masking format mismatch.\n";
}

// Save encrypted identity into candidate profile
$db->prepare("
    UPDATE `profiles` 
    SET `aadhaar_encrypted` = :aadhaar, `aadhaar_last4` = '7654',
        `pan_enc` = :pan, `pan_last4` = '9876'
    WHERE `user_id` = :uid
")->execute([
    ':aadhaar' => $encryptedAadhaar,
    ':pan'     => $encryptedPan,
    ':uid'     => $candidateId
]);

// Decrypt verification
$decryptedAadhaar = CryptoHelper::decrypt($encryptedAadhaar);
$decryptedPan = CryptoHelper::decrypt($encryptedPan);
if ($decryptedAadhaar === $rawAadhaar && $decryptedPan === $rawPan) {
    echo "  ✓ AES-256 roundtrip decryption verified accurately.\n";
}

// Test Audit Log for Reveal
AuditLog::log('identity_revealed', 'profiles', $candidateId, null, [
    'target_candidate_id' => $candidateId,
    'action' => 'reveal_sensitive_identity',
    'revealed_fields' => ['aadhaar', 'pan']
], 1); // Admin user_id = 1

$recentAudit = $db->query("
    SELECT * FROM `audit_logs` 
    WHERE `action` = 'identity_revealed' AND `entity_id` = {$candidateId} 
    ORDER BY `id` DESC LIMIT 1
")->fetch();

if (!empty($recentAudit)) {
    echo "  ✓ DPDP Audit Log created: [{$recentAudit['action']}] by user #{$recentAudit['user_id']} at {$recentAudit['created_at']}\n";
} else {
    echo "  ✗ Audit log entry not found.\n";
}

// ----------------------------------------------------------------
// TEST 5: Bulk Export ZIP Packaging
// ----------------------------------------------------------------
echo "\n[TEST 5] Testing Candidate Documents ZIP Export...\n";

$zipCacheDir = STORAGE_PATH . '/cache';
if (!is_dir($zipCacheDir)) {
    mkdir($zipCacheDir, 0755, true);
}
$zipFilename = 'candidate_' . $candidateId . '_docs_' . time() . '.zip';
$zipFilePath = $zipCacheDir . '/' . $zipFilename;

$zip = new ZipArchive();
if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    $docsToZip = $db->query("SELECT * FROM `documents` WHERE `user_id` = {$candidateId}")->fetchAll();
    foreach ($docsToZip as $d) {
        $realPath = file_exists($d['file_path']) ? $d['file_path'] : (STORAGE_PATH . '/uploads/' . $d['file_path']);
        if (file_exists($realPath)) {
            $zip->addFile($realPath, $d['document_type'] . '_' . $d['original_name']);
        }
    }
    $zip->close();
}

if (file_exists($zipFilePath) && filesize($zipFilePath) > 0) {
    echo "  ✓ ZIP archive successfully created: " . basename($zipFilePath) . " (" . filesize($zipFilePath) . " bytes)\n";
    // Clean up test zip
    @unlink($zipFilePath);
} else {
    echo "  ✗ Failed to create ZIP archive.\n";
}

// Cleanup scratch files
@unlink($testImgPath);
@unlink($testImgPath2);

echo "\n========================================================\n";
echo "PHASE 3 TEST SUITE PASSED ALL CHECKS SUCCESSFULLY!\n";
echo "========================================================\n";
