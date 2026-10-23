<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\User;
use App\Models\Profile;
use App\Models\AuditLog;
use App\Helpers\Uploader;
use App\Helpers\CryptoHelper;
use Exception;

class CandidateController {
    /**
     * Candidate Dashboard Overview (/dashboard)
     */
    public function dashboard(): void {
        $userId = (int) auth()['id'];
        $candidate = Profile::getFullProfile($userId);
        if (($candidate['candidate_type'] ?? '') === 'aptitude_only') {
            redirect('/');
            return;
        }
        $db = Database::getConnection();

        // Fetch recent reviewer comments
        $stmtComments = $db->prepare("SELECT c.*, a.full_name as admin_name
            FROM `admin_comments` c
            JOIN `users` a ON c.admin_id = a.id
            WHERE c.user_id = :uid AND c.comment_type = 'public_to_candidate'
            ORDER BY c.created_at DESC");
        $stmtComments->execute([':uid' => $userId]);
        $comments = $stmtComments->fetchAll();

        // Fetch status history
        $stmtHistory = $db->prepare("SELECT * FROM `status_history` WHERE `user_id` = :uid ORDER BY `created_at` DESC");
        $stmtHistory->execute([':uid' => $userId]);
        $history = $stmtHistory->fetchAll();

        require APP_PATH . '/views/candidate/dashboard.php';
    }

    /**
     * 4-Step Candidate Onboarding Wizard (/dashboard/onboarding)
     */
    public function onboarding(): void {
        $userId = (int) auth()['id'];
        $candidate = Profile::getFullProfile($userId);
        if (($candidate['candidate_type'] ?? '') === 'aptitude_only') {
            redirect('/');
            return;
        }

        if (empty($candidate['email_verified_at'])) {
            flash('warning', 'Please verify your email address to access onboarding.');
            redirect('/verify-email');
            return;
        }

        $stepStatus = Profile::getOnboardingStepStatus($userId);
        $reqStep = $_GET['step'] ?? 'step_a';
        $validSteps = ['step_a', 'step_b', 'step_c', 'step_d'];
        if (!in_array($reqStep, $validSteps, true)) {
            $reqStep = 'step_a';
        }

        $isLocked = !empty($candidate['profile']['is_locked']) && !in_array($candidate['status'], ['changes_requested'], true);
        if (!$isLocked) {
            $stepWeights = ['step_a' => 1, 'step_b' => 2, 'step_c' => 3, 'step_d' => 4];
            $maxWeight = $stepWeights[$stepStatus['max_allowed_step']] ?? 1;
            $reqWeight = $stepWeights[$reqStep] ?? 1;

            if ($reqWeight > $maxWeight) {
                $activeStep = $stepStatus['max_allowed_step'];
                if (isset($_GET['step'])) {
                    $stepNames = [
                        'step_a' => 'Step 1 (Basic Details)',
                        'step_b' => 'Step 2 (Social Links)',
                        'step_c' => 'Step 3 (Documents & KYC)',
                        'step_d' => 'Step 4 (Review & Sign)'
                    ];
                    flash('warning', "Please complete {$stepNames[$activeStep]} before proceeding.");
                    redirect('/dashboard/onboarding?step=' . $activeStep);
                    return;
                }
            } else {
                $activeStep = $reqStep;
            }
        } else {
            $activeStep = $reqStep;
        }

        require APP_PATH . '/views/candidate/onboarding.php';
    }

    /**
     * Auto-save Onboarding Step (POST /dashboard/onboarding/save-step)
     */
    public function saveOnboardingStep(): void {
        $userId = (int) auth()['id'];
        $candidate = Profile::getFullProfile($userId);
        $isAjax = $this->isAjax();

        if (!empty($candidate['profile']['is_locked']) && !in_array($candidate['status'], ['changes_requested'], true)) {
            $msg = 'Your onboarding application has already been submitted and is locked under review.';
            if ($isAjax) {
                response_json(['success' => false, 'error' => $msg], 403);
            }
            flash('error', $msg);
            redirect('/dashboard/onboarding');
            return;
        }

        $step = $_POST['step'] ?? 'step_a';

        // Enforce sequential step saving: cannot save later steps without completing earlier steps
        $stepStatus = Profile::getOnboardingStepStatus($userId);
        if (($step === 'step_b' || $step === 'social') && !$stepStatus['step_a_completed']) {
            $err = 'Please complete and save Step 1 (Basic Details) first.';
            if ($isAjax) {
                response_json(['success' => false, 'error' => $err], 422);
            }
            flash('error', $err);
            redirect('/dashboard/onboarding?step=step_a');
            return;
        }
        if (($step === 'step_c' || $step === 'documents') && !$stepStatus['step_a_completed']) {
            $err = 'Please complete and save Step 1 (Basic Details) first.';
            if ($isAjax) {
                response_json(['success' => false, 'error' => $err], 422);
            }
            flash('error', $err);
            redirect('/dashboard/onboarding?step=step_a');
            return;
        }

        try {
            switch ($step) {
                case 'step_a':
                case 'basic':
                    Profile::saveStepA($userId, $_POST);
                    $msg = 'Basic details saved successfully.';
                    break;

                case 'step_b':
                case 'social':
                    Profile::saveStepB($userId, $_POST);
                    $msg = 'Social links saved successfully.';
                    break;

                case 'step_c':
                case 'documents':
                    Profile::saveStepC($userId, $_POST);
                    $msg = 'Identity & emergency contacts saved successfully.';
                    break;

                default:
                    throw new Exception("Invalid onboarding step.");
            }

            $fresh = Profile::getFullProfile($userId);
            $completion = $fresh['profile']['completion_percentage'] ?? 0;
            $updatedStepStatus = Profile::getOnboardingStepStatus($userId);

            if ($isAjax) {
                response_json([
                    'success'               => true,
                    'message'               => $msg,
                    'completion_percentage' => $completion,
                    'step_status'           => $updatedStepStatus,
                    'max_allowed_step'      => $updatedStepStatus['max_allowed_step']
                ]);
            }

            flash('success', $msg);
            $next = match($step) {
                'step_a', 'basic' => 'step_b',
                'step_b', 'social' => 'step_c',
                'step_c', 'documents' => 'step_d',
                default => 'step_d'
            };
            redirect('/dashboard/onboarding?step=' . $next);

        } catch (Exception $e) {
            if ($isAjax) {
                response_json(['success' => false, 'error' => $e->getMessage()], 422);
            }
            flash('error', $e->getMessage());
            redirect('/dashboard/onboarding?step=' . $step);
        }
    }

    /**
     * Submit Onboarding Wizard & Unlock Aptitude (POST /dashboard/onboarding/submit)
     */
    public function submitOnboarding(): void {
        $userId = (int) auth()['id'];
        $candidate = Profile::getFullProfile($userId);
        $isAjax = $this->isAjax();

        if (!empty($candidate['profile']['is_locked']) && !in_array($candidate['status'], ['changes_requested'], true)) {
            $msg = 'Your onboarding application has already been submitted and is locked under review.';
            if ($isAjax) {
                response_json(['success' => false, 'error' => $msg, 'redirect' => url('/dashboard/aptitude')], 403);
            }
            flash('info', $msg);
            redirect('/dashboard/aptitude');
            return;
        }

        $res = Profile::submitStepD($userId, $_POST);

        if (!$res['success']) {
            if ($isAjax) {
                response_json(['success' => false, 'error' => $res['error']], 422);
            }
            flash('error', $res['error']);
            redirect('/dashboard/onboarding?step=step_d');
            return;
        }

        // Sync candidate updated onboarding data to Google Sheets
        try {
            \App\Helpers\GoogleSheetsService::syncCandidate($userId);
        } catch (\Throwable $e) {
            error_log("Google Sheets sync failed on submitOnboarding: " . $e->getMessage());
        }

        // Realtime Telegram Alert to Group
        try {
            \App\Helpers\TelegramService::sendStudentCandidateAlert($userId);
        } catch (\Throwable $e) {
            \App\Helpers\TelegramService::log("Telegram alert failed on submitOnboarding ({$userId}): " . $e->getMessage());
        }

        $successMsg = '🎉 Onboarding submitted successfully! Please complete your aptitude assessment.';
        if ($isAjax) {
            response_json([
                'success'  => true,
                'message'  => $successMsg,
                'redirect' => url('/dashboard/aptitude')
            ]);
        }

        flash('success', $successMsg);
        redirect('/dashboard/aptitude');
    }

    /**
     * Multi-Step Profile Wizard (/dashboard/profile)
     */
    public function profile(): void {
        redirect('/dashboard/onboarding');
    }

    /**
     * Save Legacy Profile Section (POST /dashboard/profile/save)
     */
    public function saveSection(string $section = ''): void {
        $userId = (int) auth()['id'];
        $candidate = Profile::getFullProfile($userId);
        $isAjax = $this->isAjax();

        if (!empty($candidate['profile']['is_locked']) && !in_array($candidate['status'], ['changes_requested'], true)) {
            $msg = 'Your profile is locked under review. Details cannot be modified.';
            if ($isAjax) {
                response_json(['success' => false, 'error' => $msg], 403);
            }
            flash('error', $msg);
            redirect('/dashboard/onboarding');
            return;
        }

        if (empty($section)) {
            $section = $_POST['section'] ?? 'personal';
        }

        try {
            switch ($section) {
                case 'personal':
                    Profile::savePersonal($userId, $_POST);
                    break;

                case 'identity':
                    Profile::saveIdentity($userId, $_POST);
                    break;

                case 'education':
                    Profile::saveEducation($userId, $_POST);
                    break;

                case 'experience':
                    Profile::saveExperience($userId, $_POST);
                    break;

                case 'bank':
                    Profile::saveBank($userId, $_POST);
                    break;

                case 'emergency':
                    Profile::saveEmergency($userId, $_POST);
                    break;

                case 'declaration':
                    Profile::saveDeclaration($userId, $_POST);
                    break;

                default:
                    throw new Exception("Invalid form section.");
            }

            $fresh = Profile::getFullProfile($userId);
            $completion = $fresh['profile']['completion_percentage'] ?? 0;

            if ($isAjax) {
                response_json([
                    'success' => true,
                    'message' => ucfirst($section) . ' details saved successfully.',
                    'completion_percentage' => $completion
                ]);
            }

            flash('success', ucfirst($section) . ' details saved successfully.');
            $nextStep = $this->getNextStep($section);
            redirect('/dashboard/profile?step=' . $nextStep);

        } catch (Exception $e) {
            if ($isAjax) {
                response_json(['success' => false, 'error' => $e->getMessage()], 422);
            }
            flash('error', $e->getMessage());
            redirect('/dashboard/profile?step=' . $section);
        }
    }

    /**
     * Handle Document Upload (POST /dashboard/document/upload)
     */
    public function uploadDocument(): void {
        $userId = (int) auth()['id'];
        $candidate = Profile::getFullProfile($userId);
        $docType = $_POST['document_type'] ?? '';
        $isAjax = $this->isAjax();

        if (!empty($candidate['profile']['is_locked']) && !in_array($candidate['status'], ['changes_requested'], true)) {
            $msg = 'Your profile is locked under review. Documents cannot be uploaded or modified.';
            if ($isAjax) {
                response_json(['success' => false, 'error' => $msg], 403);
            }
            flash('error', $msg);
            redirect('/dashboard/onboarding?step=step_c');
            return;
        }

        if (empty($_FILES['file']) || empty($docType)) {
            $msg = 'No file or document type specified.';
            if ($isAjax) response_json(['success' => false, 'error' => $msg], 400);
            flash('error', $msg);
            redirect('/dashboard/onboarding?step=step_c');
            return;
        }

        try {
            $doc = Uploader::process($_FILES['file'], $userId, $docType);

            // Update candidate profile photo path if document_type is profile_photo
            if ($docType === 'profile_photo') {
                $db = Database::getConnection();
                $db->prepare("UPDATE `profiles` SET `photo_path` = :path WHERE `user_id` = :uid")
                   ->execute([':path' => $doc['file_path'], ':uid' => $userId]);
            }

            AuditLog::log('document_uploaded', 'documents', $doc['id'], null, [
                'type' => $docType,
                'version' => $doc['version'],
                'size' => $doc['file_size_after']
            ], $userId);

            if ($isAjax) {
                $updatedStepStatus = Profile::getOnboardingStepStatus($userId);
                $freshProfile = Profile::getFullProfile($userId);
                response_json([
                    'success'               => true,
                    'message'               => 'Document uploaded and verified successfully.',
                    'document'              => $doc,
                    'step_status'           => $updatedStepStatus,
                    'completion_percentage' => $freshProfile['profile']['completion_percentage'] ?? 0
                ]);
            }

            flash('success', 'Document uploaded successfully.');
            redirect('/dashboard/onboarding?step=step_c');

        } catch (\Throwable $e) {
            error_log("Upload Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
            if ($isAjax) {
                response_json(['success' => false, 'error' => $e->getMessage()], 422);
            }
            flash('error', $e->getMessage());
            redirect('/dashboard/onboarding?step=step_c');
        }
    }

    /**
     * Submit Profile for Review (POST /dashboard/profile/submit)
     */
    public function submitProfile(): void {
        $userId = (int) auth()['id'];
        $res = Profile::submitForReview($userId);

        if (!$res['success']) {
            flash('error', $res['error']);
            redirect('/dashboard/profile');
            return;
        }

        flash('success', '🎉 Your profile has been submitted for review! Our recruitment team will verify your credentials.');
        redirect('/dashboard');
    }

    /**
     * Download Document with Authentication Guard (/document/download/{id})
     */
    public function downloadDocument(int $docId): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `documents` WHERE `id` = :id LIMIT 1");
        $stmt->execute([':id' => $docId]);
        $doc = $stmt->fetch();

        if (!$doc) {
            http_response_code(404);
            die("Document not found.");
        }

        $currentUser = auth();
        $isOwner = ((int)$doc['user_id'] === (int)$currentUser['id']);
        $isAdmin = in_array($currentUser['role'] ?? '', ['super_admin', 'hr_manager', 'reviewer'], true);

        if (!$isOwner && !$isAdmin) {
            http_response_code(403);
            die("Unauthorized access to document.");
        }

        $filePath = $doc['file_path'];
        if (!file_exists($filePath)) {
            http_response_code(404);
            die("File no longer exists on storage server.");
        }

        $disposition = (!empty($_GET['download']) && $_GET['download'] === '1') ? 'attachment' : 'inline';
        header('Content-Type: ' . $doc['mime_type']);
        header('Content-Disposition: ' . $disposition . '; filename="' . basename($doc['original_name']) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, max-age=3600');
        readfile($filePath);
        exit;
    }

    /**
     * Download Document Version with Authentication Guard (/document-version/download/{id})
     */
    public function downloadDocumentVersion(int $verId): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `document_versions` WHERE `id` = :id LIMIT 1");
        $stmt->execute([':id' => $verId]);
        $ver = $stmt->fetch();

        if (!$ver) {
            http_response_code(404);
            die("Document version archive not found.");
        }

        $currentUser = auth();
        $isOwner = ((int)$ver['user_id'] === (int)$currentUser['id']);
        $isAdmin = in_array($currentUser['role'] ?? '', ['super_admin', 'hr_manager', 'reviewer'], true);

        if (!$isOwner && !$isAdmin) {
            http_response_code(403);
            die("Unauthorized access to document version.");
        }

        $filePath = $ver['file_path'];
        if (!file_exists($filePath)) {
            http_response_code(404);
            die("Archived file no longer exists on storage server.");
        }

        $disposition = (!empty($_GET['download']) && $_GET['download'] === '1') ? 'attachment' : 'inline';
        header('Content-Type: ' . $ver['mime_type']);
        header('Content-Disposition: ' . $disposition . '; filename="' . basename($ver['original_name']) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, max-age=3600');
        readfile($filePath);
        exit;
    }

    private function getNextStep(string $current): string {
        $steps = ['personal', 'identity', 'education', 'experience', 'bank', 'emergency', 'declaration'];
        $idx = array_search($current, $steps, true);
        if ($idx !== false && isset($steps[$idx + 1])) {
            return $steps[$idx + 1];
        }
        return 'declaration';
    }

    private function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               (!empty($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json')) ||
               (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) ||
               (isset($_POST['is_ajax']) && $_POST['is_ajax'] === '1');
    }
}
