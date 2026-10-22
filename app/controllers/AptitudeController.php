<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Test;
use App\Models\User;
use App\Helpers\PdfHelper;

class AptitudeController {
    /**
     * Test Interface / Landing Page (/dashboard/test)
     */
    public function showTest(): void {
        $userId = (int) auth()['id'];
        $db = Database::getConnection();

        // Check if candidate already has an attempt
        $stmtAttempt = $db->prepare("SELECT a.*, s.set_code, s.name as set_name 
            FROM `test_attempts` a 
            LEFT JOIN `aptitude_sets` s ON a.set_id = s.id 
            WHERE a.user_id = :uid 
            ORDER BY a.id DESC LIMIT 1");
        $stmtAttempt->execute([':uid' => $userId]);
        $attempt = $stmtAttempt->fetch();

        $testConfig = $db->query("SELECT * FROM `tests` WHERE `id` = 1 LIMIT 1")->fetch();

        // If completed and no re-attempt allowed, show result view
        if ($attempt && in_array($attempt['status'], ['completed', 'timed_out'], true) && empty($attempt['allow_reattempt'])) {
            require APP_PATH . '/views/candidate/test_result.php';
            return;
        }

        // If in-progress or freshly starting
        if ($attempt && $attempt['status'] === 'in_progress') {
            $session = Test::startOrResumeTest($userId, 1);
            if ($session['success']) {
                $questions = $session['questions'];
                $timeRemaining = $session['time_remaining_seconds'];
                require APP_PATH . '/views/candidate/test_active.php';
                return;
            }
        }

        // Check candidate onboarding completion status
        $user = User::findById($userId);

        // Instructions screen
        require APP_PATH . '/views/candidate/test_instructions.php';
    }

    /**
     * Start Assessment Session (POST /dashboard/test/start)
     */
    public function startTest(): void {
        $userId = (int) auth()['id'];
        $session = Test::startOrResumeTest($userId, 1);

        if (!$session['success']) {
            flash('error', $session['error']);
            redirect('/dashboard/test');
            return;
        }

        redirect('/dashboard/test');
    }

    /**
     * Autosave Answer via AJAX (POST /dashboard/test/answer)
     */
    public function saveAnswer(): void {
        $userId = (int) auth()['id'];
        $attemptId = (int) ($_POST['attempt_id'] ?? 0);
        $questionId = (int) ($_POST['question_id'] ?? 0);
        $optionId = !empty($_POST['option_id']) ? (int)$_POST['option_id'] : null;
        $isMarked = !empty($_POST['is_marked']);

        $res = Test::saveAnswer($attemptId, $userId, $questionId, $optionId, $isMarked);
        response_json($res);
    }

    /**
     * Log Anti-Cheat Warning (POST /dashboard/test/warning)
     */
    public function logWarning(): void {
        $userId = (int) auth()['id'];
        $attemptId = (int) ($_POST['attempt_id'] ?? 0);
        $type = $_POST['type'] ?? 'tab_switch';

        $res = Test::logAntiCheatViolation($attemptId, $userId, $type);
        response_json($res);
    }

    /**
     * Finalize & Submit Test (POST /dashboard/test/submit)
     */
    public function submitTest(): void {
        $userId = (int) auth()['id'];
        $attemptId = (int) ($_POST['attempt_id'] ?? 0);

        $res = Test::finalizeTestAttempt($attemptId, 'completed');
        flash('success', 'Your Aptitude Assessment has been submitted successfully! Review your result below.');
        redirect('/dashboard/test');
    }

    /**
     * Download Scorecard PDF (/dashboard/test/scorecard/{id})
     */
    public function downloadScorecard(int $attemptId): void {
        $currentUser = auth();
        $currentUserId = (int) ($currentUser['id'] ?? 0);
        $role = $currentUser['role'] ?? 'candidate';
        $isAdmin = in_array($role, ['super_admin', 'hr_manager', 'reviewer'], true);

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT a.*, s.set_code, s.name as set_name 
            FROM `test_attempts` a 
            LEFT JOIN `aptitude_sets` s ON a.set_id = s.id 
            WHERE a.id = :aid LIMIT 1");
        $stmt->execute([':aid' => $attemptId]);
        $attempt = $stmt->fetch();

        if (!$attempt) {
            http_response_code(404);
            die("Scorecard record not found.");
        }

        // Candidate can only access their own scorecard; Admins can access any
        $targetUserId = (int) $attempt['user_id'];
        if (!$isAdmin && $targetUserId !== $currentUserId) {
            http_response_code(403);
            die("Unauthorized access to assessment scorecard.");
        }

        $user = User::findById($targetUserId);
        if (!$user) {
            // Guest or deleted user fallback
            $user = [
                'id' => $targetUserId,
                'full_name' => $attempt['guest_name'] ?? 'Candidate',
                'email' => $attempt['guest_email'] ?? 'N/A',
                'mobile' => $attempt['guest_mobile'] ?? 'N/A'
            ];
        }

        // Fetch category breakdown
        $stmtCat = $db->prepare("SELECT 
                c.name as category_name,
                COUNT(aa.id) as total_in_cat,
                SUM(CASE WHEN aa.selected_option_id IS NOT NULL THEN 1 ELSE 0 END) as answered_in_cat,
                SUM(CASE WHEN aa.is_correct = 1 THEN 1 ELSE 0 END) as correct_in_cat,
                SUM(aa.marks_awarded) as score_in_cat
            FROM `attempt_answers` aa
            JOIN `questions` q ON aa.question_id = q.id
            JOIN `test_categories` c ON q.category_id = c.id
            WHERE aa.attempt_id = :aid
            GROUP BY c.id, c.name
            ORDER BY c.id ASC");
        $stmtCat->execute([':aid' => $attemptId]);
        $categoryBreakdown = $stmtCat->fetchAll();

        // Graceful fallback for incomplete attempts or missing scores
        if ($attempt['score'] === null) {
            $stmtSum = $db->prepare("SELECT 
                COUNT(id) as total_q,
                SUM(CASE WHEN selected_option_id IS NOT NULL THEN 1 ELSE 0 END) as total_ans,
                SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as total_corr,
                SUM(marks_awarded) as total_score
                FROM `attempt_answers` WHERE attempt_id = :aid");
            $stmtSum->execute([':aid' => $attemptId]);
            $sumRes = $stmtSum->fetch();
            $attempt['score'] = max(0.00, round((float)($sumRes['total_score'] ?? 0), 2));
            $attempt['total_questions'] = (int)($sumRes['total_q'] ?? 15);
            $attempt['total_answered'] = (int)($sumRes['total_ans'] ?? 0);
            $attempt['total_correct'] = (int)($sumRes['total_corr'] ?? 0);
            $attempt['percentage'] = $attempt['total_questions'] > 0 ? round(($attempt['score'] / $attempt['total_questions']) * 100, 2) : 0;
            $attempt['passed'] = ($attempt['percentage'] >= 50.0) ? 1 : 0;
        }

        try {
            $pdfBinary = PdfHelper::generateScorecard($attempt, $user, $categoryBreakdown);
        } catch (\Throwable $e) {
            error_log("Scorecard generation error for attempt {$attemptId}: " . $e->getMessage());
            http_response_code(500);
            die("Unable to generate scorecard PDF. Please contact system administrator.");
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Realty_Smartz_Scorecard_' . $targetUserId . '.pdf"');
        header('Content-Length: ' . strlen($pdfBinary));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        echo $pdfBinary;
        exit;
    }

    /**
     * Show QR Code 2 Aptitude Registration Form (GET /aptitude/register)
     */
    public function showAptitudeRegister(): void {
        if (auth_check()) {
            redirect('/dashboard/test');
            return;
        }
        require APP_PATH . '/views/candidate/aptitude_register.php';
    }

    /**
     * Process QR Code 2 Candidate Registration (POST /aptitude/register)
     */
    public function registerAptitudeCandidate(): void {
        $fullName = trim($_POST['full_name'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $cleanMobile = preg_replace('/\D/', '', $mobile);
        $email = strtolower(trim($_POST['email'] ?? ''));

        if (empty($fullName) || strlen($fullName) < 2) {
            flash('error', 'Please provide your full legal name.');
            redirect('/aptitude/register');
            return;
        }

        if (empty($cleanMobile) || strlen($cleanMobile) !== 10) {
            flash('error', 'Please provide a valid 10-digit mobile number.');
            redirect('/aptitude/register');
            return;
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please provide a valid email address.');
            redirect('/aptitude/register');
            return;
        }

        $db = Database::getConnection();

        // Check if candidate already exists with email or mobile
        $stmtUser = $db->prepare("SELECT * FROM `users` WHERE `email` = :em OR `mobile` = :mob LIMIT 1");
        $stmtUser->execute([':em' => $email, ':mob' => $cleanMobile]);
        $existing = $stmtUser->fetch();

        if ($existing) {
            $userId = (int) $existing['id'];
            $db->prepare("UPDATE `users` SET `created_at` = NOW() WHERE `id` = :id")->execute([':id' => $userId]);
            $user = User::findById($userId);
        } else {
            $tempPass = bin2hex(random_bytes(8));
            $userId = User::createCandidate([
                'full_name'      => $fullName,
                'email'          => $email,
                'mobile'         => $cleanMobile,
                'password'       => $tempPass,
                'candidate_type' => 'aptitude_only',
                'source'         => 'qr_aptitude'
            ]);

            // Do not auto-verify email. User must verify email if they ever register for full onboarding.
            $user = User::findById($userId);
        }

        // Establish authenticated session
        session_regenerate_id(true);
        $_SESSION['user'] = $user;

        // Sync candidate to Google Sheets
        try {
            \App\Helpers\GoogleSheetsService::syncCandidate($userId);
        } catch (\Throwable $e) {
            error_log("Google Sheets sync failed in registerAptitudeCandidate: " . $e->getMessage());
        }

        flash('success', 'Welcome, ' . e($fullName) . '! Please review the test instructions and begin when ready.');
        redirect('/dashboard/test');
    }
}

