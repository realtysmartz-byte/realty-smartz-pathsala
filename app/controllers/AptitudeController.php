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
        $userId = (int) auth()['id'];
        $user = User::findById($userId);

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT a.*, s.set_code, s.name as set_name 
            FROM `test_attempts` a 
            LEFT JOIN `aptitude_sets` s ON a.set_id = s.id 
            WHERE a.id = :aid AND a.user_id = :uid LIMIT 1");
        $stmt->execute([':aid' => $attemptId, ':uid' => $userId]);
        $attempt = $stmt->fetch();

        if (!$attempt) {
            http_response_code(404);
            die("Scorecard record not found.");
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

        $pdfBinary = PdfHelper::generateScorecard($attempt, $user, $categoryBreakdown);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Realty_Smartz_Scorecard_' . $userId . '.pdf"');
        header('Content-Length: ' . strlen($pdfBinary));
        echo $pdfBinary;
        exit;
    }
}
