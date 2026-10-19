<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Test {
    /**
     * Get or create active test session for candidate
     */
    public static function startOrResumeTest(int $userId, int $testId = 1): array {
        $db = Database::getConnection();

        // 1. Fetch test settings
        $stmtTest = $db->prepare("SELECT * FROM `tests` WHERE `id` = :id AND `is_active` = 1 AND `is_archived` = 0 LIMIT 1");
        $stmtTest->execute([':id' => $testId]);
        $test = $stmtTest->fetch();

        if (!$test) {
            return ['success' => false, 'error' => 'Aptitude test is currently not available. Please contact HR.'];
        }

        // 2. Check existing attempt
        $stmtAttempt = $db->prepare("SELECT a.*, s.set_code, s.name as set_name 
            FROM `test_attempts` a 
            LEFT JOIN `aptitude_sets` s ON a.set_id = s.id 
            WHERE a.user_id = :uid AND a.test_id = :tid 
            ORDER BY a.id DESC LIMIT 1");
        $stmtAttempt->execute([':uid' => $userId, ':tid' => $testId]);
        $attempt = $stmtAttempt->fetch();

        if ($attempt) {
            // Check if already completed or expired
            if (in_array($attempt['status'], ['completed', 'timed_out'], true)) {
                if (empty($attempt['allow_reattempt'])) {
                    return [
                        'success' => false,
                        'completed' => true,
                        'attempt_id' => (int)$attempt['id'],
                        'error' => 'You have already completed this aptitude assessment. Re-attempts are locked unless granted by an administrator.'
                    ];
                }
            }

            // Check if active session is still within time window
            $now = time();
            $expiresAt = strtotime($attempt['expires_at'] ?? $attempt['deadline_at']);

            if ($attempt['status'] === 'in_progress') {
                if ($now > $expiresAt) {
                    // Auto submit on timeout
                    self::finalizeTestAttempt((int)$attempt['id'], 'timed_out');
                    return [
                        'success' => false,
                        'completed' => true,
                        'attempt_id' => (int)$attempt['id'],
                        'error' => 'Your test time limit has expired. The test has been automatically submitted.'
                    ];
                }

                // Resume in-progress attempt
                $questions = self::getAttemptQuestions((int)$attempt['id']);
                return [
                    'success' => true,
                    'attempt' => $attempt,
                    'questions' => $questions,
                    'time_remaining_seconds' => max(0, $expiresAt - $now)
                ];
            }
        }

        // 3. Create fresh test attempt
        $timeLimitMinutes = (int) ($test['time_limit_minutes'] ?? 10);
        if ($timeLimitMinutes <= 0) $timeLimitMinutes = 10;
        $startedAt = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', time() + ($timeLimitMinutes * 60));

        // Select active set with least times_assigned
        $stmtSet = $db->query("SELECT id, set_code, name FROM `aptitude_sets` WHERE `is_active` = 1 ORDER BY `times_assigned` ASC, RAND() LIMIT 1");
        $chosenSet = $stmtSet->fetch();
        $setId = $chosenSet ? (int)$chosenSet['id'] : 1;
        $setCode = $chosenSet['set_code'] ?? 'Set A';

        // Increment times_assigned for this set
        $db->prepare("UPDATE `aptitude_sets` SET `times_assigned` = `times_assigned` + 1 WHERE `id` = :sid")->execute([':sid' => $setId]);

        // Select all 15 questions belonging to this set
        $stmtQ = $db->prepare("SELECT q.id, q.category_id, q.question_text, q.marks, q.negative_marks
                 FROM `questions` q
                 WHERE q.set_id = :sid AND q.is_active = 1 AND q.is_archived = 0
                 ORDER BY q.id ASC");
        $stmtQ->execute([':sid' => $setId]);
        $selectedQuestions = $stmtQ->fetchAll();

        // Fallback if set has no questions
        if (count($selectedQuestions) === 0) {
            $stmtQFallback = $db->query("SELECT q.id, q.category_id, q.question_text, q.marks, q.negative_marks
                 FROM `questions` q
                 WHERE q.is_active = 1 AND q.is_archived = 0
                 ORDER BY q.id ASC
                 LIMIT 15");
            $selectedQuestions = $stmtQFallback->fetchAll();
        }

        if (count($selectedQuestions) === 0) {
            return ['success' => false, 'error' => 'No active assessment questions found in the question bank.'];
        }

        $stmtInsAttempt = $db->prepare("INSERT INTO `test_attempts`
            (`test_id`, `set_id`, `user_id`, `started_at`, `deadline_at`, `expires_at`, `total_questions`, `status`, `created_at`)
            VALUES (:tid, :sid, :uid, :start, :deadline, :exp, :total, 'in_progress', NOW())");

        $stmtInsAttempt->execute([
            ':tid'      => $testId,
            ':sid'      => $setId,
            ':uid'      => $userId,
            ':start'    => $startedAt,
            ':deadline' => $expiresAt,
            ':exp'      => $expiresAt,
            ':total'    => count($selectedQuestions)
        ]);

        $attemptId = (int) $db->lastInsertId();

        // Seed attempt_answers table
        $stmtInsAns = $db->prepare("INSERT INTO `attempt_answers` (`attempt_id`, `question_id`, `marks_awarded`) VALUES (:aid, :qid, 0.00)");
        foreach ($selectedQuestions as $q) {
            $stmtInsAns->execute([':aid' => $attemptId, ':qid' => $q['id']]);
        }

        AuditLog::log('test_started', 'test_attempts', $attemptId, null, [
            'user_id' => $userId,
            'test_id' => $testId,
            'set_id' => $setId,
            'set_code' => $setCode
        ], $userId);

        $attemptData = [
            'id' => $attemptId,
            'test_id' => $testId,
            'set_id' => $setId,
            'set_code' => $setCode,
            'user_id' => $userId,
            'started_at' => $startedAt,
            'deadline_at' => $expiresAt,
            'expires_at' => $expiresAt,
            'total_questions' => count($selectedQuestions),
            'status' => 'in_progress',
            'tab_switch_count' => 0
        ];

        return [
            'success' => true,
            'attempt' => $attemptData,
            'questions' => self::getAttemptQuestions($attemptId),
            'time_remaining_seconds' => $timeLimitMinutes * 60
        ];
    }

    /**
     * Retrieve questions, options, and saved responses for an attempt
     */
    public static function getAttemptQuestions(int $attemptId): array {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT
                q.id,
                q.category_id,
                c.name as category_name,
                q.question_text,
                q.marks,
                q.negative_marks,
                aa.selected_option_id,
                aa.is_marked_for_review
            FROM `attempt_answers` aa
            JOIN `questions` q ON aa.question_id = q.id
            JOIN `test_categories` c ON q.category_id = c.id
            WHERE aa.attempt_id = :aid
            ORDER BY aa.id ASC");

        $stmt->execute([':aid' => $attemptId]);
        $questions = $stmt->fetchAll();

        // Fetch options for these questions
        $stmtOpt = $db->prepare("SELECT id, question_id, option_text, option_order FROM `options` WHERE question_id = :qid ORDER BY option_order ASC");

        foreach ($questions as &$q) {
            $stmtOpt->execute([':qid' => $q['id']]);
            $q['options'] = $stmtOpt->fetchAll();
        }

        return $questions;
    }

    /**
     * Autosave candidate answer or mark for review
     */
    public static function saveAnswer(int $attemptId, int $userId, int $questionId, ?int $optionId, bool $isMarked): array {
        $db = Database::getConnection();

        // Verify attempt belongs to user and is in progress
        $stmt = $db->prepare("SELECT * FROM `test_attempts` WHERE `id` = :aid AND `user_id` = :uid AND `status` = 'in_progress' LIMIT 1");
        $stmt->execute([':aid' => $attemptId, ':uid' => $userId]);
        $attempt = $stmt->fetch();

        if (!$attempt) {
            return ['success' => false, 'error' => 'Test session is closed or invalid.'];
        }

        if (time() > strtotime($attempt['expires_at'])) {
            self::finalizeTestAttempt($attemptId, 'timed_out');
            return ['success' => false, 'expired' => true, 'error' => 'Time has expired.'];
        }

        $stmtUpd = $db->prepare("UPDATE `attempt_answers` SET
            `selected_option_id` = :opt,
            `is_marked_for_review` = :marked,
            `updated_at` = NOW()
            WHERE `attempt_id` = :aid AND `question_id` = :qid");

        $stmtUpd->execute([
            ':opt'    => $optionId,
            ':marked' => $isMarked ? 1 : 0,
            ':aid'    => $attemptId,
            ':qid'    => $questionId
        ]);

        return ['success' => true];
    }

    /**
     * Record anti-cheat violation (tab switch or fullscreen exit)
     */
    public static function logAntiCheatViolation(int $attemptId, int $userId, string $type): array {
        $db = Database::getConnection();

        $col = ($type === 'fullscreen_exit') ? 'fullscreen_exit_count' : 'tab_switch_count';

        $stmt = $db->prepare("UPDATE `test_attempts` SET `{$col}` = `{$col}` + 1 WHERE `id` = :aid AND `user_id` = :uid");
        $stmt->execute([':aid' => $attemptId, ':uid' => $userId]);

        $stmtCount = $db->prepare("SELECT `tab_switch_count`, `fullscreen_exit_count` FROM `test_attempts` WHERE `id` = :aid");
        $stmtCount->execute([':aid' => $attemptId]);
        $counts = $stmtCount->fetch();

        AuditLog::log('anti_cheat_warning', 'test_attempts', $attemptId, null, ['type' => $type, 'counts' => $counts], $userId);

        return ['success' => true, 'counts' => $counts];
    }

    /**
     * Finalize and calculate score, percentage, and pass/fail status
     */
    public static function finalizeTestAttempt(int $attemptId, string $status = 'completed'): array {
        $db = Database::getConnection();

        $stmtAttempt = $db->prepare("SELECT a.*, t.passing_percentage, t.negative_marking_per_question
            FROM `test_attempts` a
            JOIN `tests` t ON a.test_id = t.id
            WHERE a.id = :id LIMIT 1");
        $stmtAttempt->execute([':id' => $attemptId]);
        $attempt = $stmtAttempt->fetch();

        if (!$attempt || $attempt['status'] === 'completed') {
            return ['success' => true, 'attempt' => $attempt];
        }

        // Fetch all answers with correct options
        $stmtAnswers = $db->prepare("SELECT
                aa.id as answer_id,
                aa.question_id,
                aa.selected_option_id,
                q.marks,
                q.negative_marks,
                opt.is_correct
            FROM `attempt_answers` aa
            JOIN `questions` q ON aa.question_id = q.id
            LEFT JOIN `options` opt ON aa.selected_option_id = opt.id
            WHERE aa.attempt_id = :aid");
        $stmtAnswers->execute([':aid' => $attemptId]);
        $answers = $stmtAnswers->fetchAll();

        $totalQuestions = count($answers);
        $totalAnswered = 0;
        $totalCorrect = 0;
        $totalWrong = 0;
        $totalScore = 0.00;

        $updAns = $db->prepare("UPDATE `attempt_answers` SET `is_correct` = :corr, `marks_awarded` = :marks WHERE `id` = :aid");

        foreach ($answers as $ans) {
            $isCorrect = 0;
            $marksAwarded = 0.00;

            if (!empty($ans['selected_option_id'])) {
                $totalAnswered++;
                if (!empty($ans['is_correct'])) {
                    $totalCorrect++;
                    $isCorrect = 1;
                    $marksAwarded = (float) $ans['marks'];
                } else {
                    $totalWrong++;
                    $marksAwarded = - (float) $ans['negative_marks'];
                }
            }

            $totalScore += $marksAwarded;

            $updAns->execute([
                ':corr'  => $isCorrect,
                ':marks' => $marksAwarded,
                ':aid'   => $ans['answer_id']
            ]);
        }

        // Prevent negative total score
        $finalScore = max(0.00, round($totalScore, 2));
        $maxPossibleScore = max(1, $totalQuestions);
        $percentage = round(($finalScore / $maxPossibleScore) * 100, 2);

        $passingPct = (float) ($attempt['passing_percentage'] ?? 50.00);
        $passed = ($percentage >= $passingPct) ? 1 : 0;

        $stmtUpdAttempt = $db->prepare("UPDATE `test_attempts` SET
            `submitted_at` = NOW(),
            `completed_at` = NOW(),
            `total_answered` = :ans,
            `total_correct` = :corr,
            `total_wrong` = :wrong,
            `score` = :score,
            `percentage` = :pct,
            `passed` = :passed,
            `status` = :st
            WHERE `id` = :aid");

        $stmtUpdAttempt->execute([
            ':ans'    => $totalAnswered,
            ':corr'   => $totalCorrect,
            ':wrong'  => $totalWrong,
            ':score'  => $finalScore,
            ':pct'    => $percentage,
            ':passed' => $passed,
            ':st'     => $status,
            ':aid'    => $attemptId
        ]);

        // Update user status to test_completed
        User::updateStatus((int)$attempt['user_id'], 'test_completed', "Aptitude Assessment completed with score {$finalScore}/{$totalQuestions} ({$percentage}%) - " . ($passed ? 'PASSED' : 'FAILED'), (int)$attempt['user_id']);

        AuditLog::log('test_finalized', 'test_attempts', $attemptId, null, [
            'score' => $finalScore,
            'percentage' => $percentage,
            'passed' => $passed
        ], (int)$attempt['user_id']);

        return [
            'success' => true,
            'score' => $finalScore,
            'percentage' => $percentage,
            'passed' => (bool)$passed,
            'total_correct' => $totalCorrect,
            'total_wrong' => $totalWrong,
            'total_answered' => $totalAnswered,
            'total_questions' => $totalQuestions
        ];
    }

    /**
     * Get complete scorecard including category performance and set details
     */
    public static function getScorecard(int $attemptId, ?int $userId = null): ?array {
        $db = Database::getConnection();

        $sql = "SELECT a.*, s.set_code, s.name as set_name, u.full_name, u.email, u.mobile
                FROM `test_attempts` a
                LEFT JOIN `aptitude_sets` s ON a.set_id = s.id
                LEFT JOIN `users` u ON a.user_id = u.id
                WHERE a.id = :aid";
        $params = [':aid' => $attemptId];
        if ($userId !== null) {
            $sql .= " AND a.user_id = :uid";
            $params[':uid'] = $userId;
        }
        $sql .= " LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $attempt = $stmt->fetch();

        if (!$attempt) {
            return null;
        }

        // Fetch category breakdown
        $stmtCat = $db->prepare("SELECT 
                c.id as category_id,
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
        $categories = $stmtCat->fetchAll();

        return [
            'attempt' => $attempt,
            'categories' => $categories
        ];
    }
}
