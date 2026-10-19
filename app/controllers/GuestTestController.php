<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\AuditLog;
use App\Models\Test;
use App\Models\User;
use PDO;

class GuestTestController {

    /**
     * Display Guest Aptitude Assessment (/test/g/{token})
     */
    public function showGuestTest(string $token): void {
        header('X-Robots-Tag: noindex, nofollow');

        $tokenClean = trim($token);
        if (strlen($tokenClean) < 32) {
            http_response_code(404);
            $this->render404();
            return;
        }

        $tokenHash = hash('sha256', $tokenClean);
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT gtl.*, 
                u.full_name as user_full_name, 
                u.email as user_email, 
                u.mobile as user_mobile,
                s.set_code, s.name as set_name
            FROM `guest_test_links` gtl
            LEFT JOIN `users` u ON gtl.user_id = u.id
            LEFT JOIN `aptitude_sets` s ON gtl.set_id = s.id
            WHERE gtl.token_hash = :hash LIMIT 1");
        $stmt->execute([':hash' => $tokenHash]);
        $link = $stmt->fetch();

        if (!$link) {
            http_response_code(404);
            $this->render404();
            return;
        }

        // Fetch candidate/guest name
        $guestName = $link['user_full_name'] ?: ($link['guest_name'] ?: 'Candidate');
        $guestEmail = $link['user_email'] ?: ($link['guest_email'] ?: '');
        $guestMobile = $link['user_mobile'] ?: ($link['guest_mobile'] ?: '');

        // 1. Check if revoked
        if ($link['status'] === 'revoked') {
            $viewState = 'revoked';
            $pageTitle = "Link Inactive | Realty Smartz Pathshala";
            require APP_PATH . '/views/candidate/guest_test.php';
            return;
        }

        // 2. Check if an attempt is currently in progress
        $stmtActiveAtt = $db->prepare("SELECT ta.*, s.set_code, s.name as set_name 
            FROM `test_attempts` ta 
            LEFT JOIN `aptitude_sets` s ON ta.set_id = s.id 
            WHERE ta.guest_test_link_id = :gid ORDER BY ta.id DESC LIMIT 1");
        $stmtActiveAtt->execute([':gid' => $link['id']]);
        $attempt = $stmtActiveAtt->fetch();

        $now = time();

        if ($attempt && $attempt['status'] === 'in_progress') {
            $attExpiresAt = strtotime($attempt['expires_at'] ?? $attempt['deadline_at']);
            if ($now > $attExpiresAt) {
                // Auto-submit on 10-minute timeout
                Test::finalizeTestAttempt((int)$attempt['id'], 'timed_out');
                $db->prepare("UPDATE `guest_test_links` SET `status` = 'submitted', `submitted_at` = NOW() WHERE `id` = :id")
                   ->execute([':id' => $link['id']]);

                // Re-fetch finalized attempt
                $stmtActiveAtt->execute([':gid' => $link['id']]);
                $attempt = $stmtActiveAtt->fetch();

                $viewState = 'submitted';
                $pageTitle = "Assessment Completed | Realty Smartz Pathshala";
                require APP_PATH . '/views/candidate/guest_test.php';
                return;
            }

            // Test in progress -> Resume test with questions
            $questions = Test::getAttemptQuestions((int)$attempt['id']);
            $timeRemainingSeconds = max(0, $attExpiresAt - $now);

            $viewState = 'in_progress';
            $pageTitle = "Aptitude Assessment in Progress | Realty Smartz Pathshala";
            require APP_PATH . '/views/candidate/guest_test.php';
            return;
        }

        // 3. Check if already submitted
        if ($link['status'] === 'submitted' || (int)$link['attempts_used'] >= (int)$link['max_attempts']) {
            $viewState = 'submitted';
            $pageTitle = "Assessment Submitted | Realty Smartz Pathshala";
            require APP_PATH . '/views/candidate/guest_test.php';
            return;
        }

        // 4. Check if expired
        $expiresAt = strtotime($link['expires_at']);
        if ($now > $expiresAt || $link['status'] === 'expired') {
            if ($link['status'] !== 'expired') {
                $db->prepare("UPDATE `guest_test_links` SET `status` = 'expired' WHERE `id` = :id")->execute([':id' => $link['id']]);
            }
            $viewState = 'expired';
            $pageTitle = "Link Expired | Realty Smartz Pathshala";
            require APP_PATH . '/views/candidate/guest_test.php';
            return;
        }

        // 5. If open guest link without user identification, ask guest details
        if (empty($link['user_id']) && (empty($link['guest_email']) || empty($link['guest_name']))) {
            $viewState = 'identify';
            $pageTitle = "Welcome to Aptitude Assessment | Realty Smartz Pathshala";
            require APP_PATH . '/views/candidate/guest_test.php';
            return;
        }

        // 6. Start screen (Not started yet)
        $viewState = 'not_started';
        $pageTitle = "Aptitude Assessment | Realty Smartz Pathshala";
        require APP_PATH . '/views/candidate/guest_test.php';
    }

    /**
     * Identify Open Guest (POST /test/g/{token}/identify)
     */
    public function identifyGuest(string $token): void {
        header('X-Robots-Tag: noindex, nofollow');
        $tokenHash = hash('sha256', trim($token));
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM `guest_test_links` WHERE `token_hash` = :hash LIMIT 1");
        $stmt->execute([':hash' => $tokenHash]);
        $link = $stmt->fetch();

        if (!$link || $link['status'] !== 'active') {
            flash('error', 'Guest test link is not active.');
            redirect('/test/g/' . $token);
            return;
        }

        $name = trim($_POST['full_name'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));

        if (empty($name) || empty($mobile) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please provide a valid full name, mobile number, and email address.');
            redirect('/test/g/' . $token);
            return;
        }

        // Check if matching candidate exists by mobile or email
        $stmtUser = $db->prepare("SELECT id FROM `users` WHERE `email` = :em OR `mobile` = :mob LIMIT 1");
        $stmtUser->execute([':em' => $email, ':mob' => $mobile]);
        $existing = $stmtUser->fetch();

        if ($existing) {
            $userId = (int)$existing['id'];
        } else {
            // Create a new candidate record of type "Guest"
            $tempPassword = bin2hex(random_bytes(8));
            $passHash = password_hash($tempPassword, PASSWORD_DEFAULT);

            $stmtIns = $db->prepare("INSERT INTO `users` 
                (`full_name`, `email`, `mobile`, `password_hash`, `role`, `is_guest`, `status`, `is_archived`, `created_at`)
                VALUES (:name, :email, :mobile, :phash, 'candidate', 1, 'registered', 0, NOW())");
            $stmtIns->execute([
                ':name' => $name,
                ':email' => $email,
                ':mobile' => $mobile,
                ':phash' => $passHash
            ]);
            $userId = (int)$db->lastInsertId();

            // Seed profiles row
            $db->prepare("INSERT INTO `profiles` (`user_id`, `completion_percentage`, `created_at`) VALUES (:uid, 0, NOW())")
               ->execute([':uid' => $userId]);
        }

        // Attach user to guest_test_links
        $stmtUpd = $db->prepare("UPDATE `guest_test_links` SET
            `user_id` = :uid,
            `guest_name` = :gname,
            `guest_mobile` = :gmobile,
            `guest_email` = :gemail
            WHERE `id` = :id");
        $stmtUpd->execute([
            ':uid' => $userId,
            ':gname' => $name,
            ':gmobile' => $mobile,
            ':gemail' => $email,
            ':id' => $link['id']
        ]);

        AuditLog::log('guest_candidate_identified', 'guest_test_links', $link['id'], null, [
            'user_id' => $userId,
            'name' => $name,
            'email' => $email,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ], $userId);

        redirect('/test/g/' . $token);
    }

    /**
     * Start Guest Test Session (POST /test/g/{token}/start)
     */
    public function startGuestTest(string $token): void {
        header('X-Robots-Tag: noindex, nofollow');
        $tokenHash = hash('sha256', trim($token));
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM `guest_test_links` WHERE `token_hash` = :hash LIMIT 1");
        $stmt->execute([':hash' => $tokenHash]);
        $link = $stmt->fetch();

        if (!$link || $link['status'] !== 'active') {
            flash('error', 'Guest test link is not active.');
            redirect('/test/g/' . $token);
            return;
        }

        $userId = (int)$link['user_id'];
        if (!$userId) {
            flash('error', 'Please identify yourself before starting the assessment.');
            redirect('/test/g/' . $token);
            return;
        }

        // Choose set:
        $setId = !empty($link['set_id']) ? (int)$link['set_id'] : null;

        if (!$setId) {
            // Assign by rule: a set the person has not attempted before
            $stmtSet = $db->prepare("SELECT s.id FROM `aptitude_sets` s 
                WHERE s.is_active = 1 
                AND s.id NOT IN (SELECT ta.set_id FROM `test_attempts` ta WHERE ta.user_id = :uid AND ta.set_id IS NOT NULL)
                ORDER BY s.times_assigned ASC, RAND() LIMIT 1");
            $stmtSet->execute([':uid' => $userId]);
            $chosen = $stmtSet->fetch();

            if ($chosen) {
                $setId = (int)$chosen['id'];
            } else {
                $stmtFallback = $db->query("SELECT id FROM `aptitude_sets` WHERE `is_active` = 1 ORDER BY `times_assigned` ASC, RAND() LIMIT 1");
                $fallback = $stmtFallback->fetch();
                $setId = $fallback ? (int)$fallback['id'] : 1;
            }
        }

        // Increment times_assigned for this set
        $db->prepare("UPDATE `aptitude_sets` SET `times_assigned` = `times_assigned` + 1 WHERE `id` = :sid")->execute([':sid' => $setId]);

        // Fetch 15 questions for this set
        $stmtQ = $db->prepare("SELECT q.id, q.category_id, q.question_text, q.marks, q.negative_marks
            FROM `questions` q
            WHERE q.set_id = :sid AND q.is_active = 1 AND q.is_archived = 0
            ORDER BY q.id ASC");
        $stmtQ->execute([':sid' => $setId]);
        $questions = $stmtQ->fetchAll();

        if (empty($questions)) {
            $stmtQFallback = $db->query("SELECT q.id, q.category_id, q.question_text, q.marks, q.negative_marks
                FROM `questions` q
                WHERE q.is_active = 1 AND q.is_archived = 0
                ORDER BY q.id ASC LIMIT 15");
            $questions = $stmtQFallback->fetchAll();
        }

        $startedAt = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', time() + 600); // 10 minutes server-enforced timer

        // Create test_attempts record referencing guest_test_link_id
        $stmtIns = $db->prepare("INSERT INTO `test_attempts`
            (`test_id`, `set_id`, `user_id`, `guest_test_link_id`, `started_at`, `deadline_at`, `expires_at`, `total_questions`, `status`, `created_at`)
            VALUES (1, :sid, :uid, :gid, :started, :deadline, :exp, :total, 'in_progress', NOW())");
        $stmtIns->execute([
            ':sid' => $setId,
            ':uid' => $userId,
            ':gid' => $link['id'],
            ':started' => $startedAt,
            ':deadline' => $expiresAt,
            ':exp' => $expiresAt,
            ':total' => count($questions)
        ]);
        $attemptId = (int)$db->lastInsertId();

        // Seed attempt_answers
        $stmtInsAns = $db->prepare("INSERT INTO `attempt_answers` (`attempt_id`, `question_id`, `marks_awarded`) VALUES (:aid, :qid, 0.00)");
        foreach ($questions as $q) {
            $stmtInsAns->execute([':aid' => $attemptId, ':qid' => $q['id']]);
        }

        // Update guest_test_links status to in_progress
        $db->prepare("UPDATE `guest_test_links` SET 
            `status` = 'in_progress',
            `set_id` = :sid,
            `first_used_at` = NOW(),
            `attempts_used` = `attempts_used` + 1
            WHERE `id` = :gid")->execute([
            ':sid' => $setId,
            ':gid' => $link['id']
        ]);

        AuditLog::log('guest_test_started', 'test_attempts', $attemptId, null, [
            'guest_test_link_id' => $link['id'],
            'user_id' => $userId,
            'set_id' => $setId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ], $userId);

        redirect('/test/g/' . $token);
    }

    /**
     * Autosave Answer via AJAX (POST /test/g/{token}/answer)
     */
    public function saveGuestAnswer(string $token): void {
        header('X-Robots-Tag: noindex, nofollow');
        $tokenHash = hash('sha256', trim($token));
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM `guest_test_links` WHERE `token_hash` = :hash LIMIT 1");
        $stmt->execute([':hash' => $tokenHash]);
        $link = $stmt->fetch();

        if (!$link || $link['status'] !== 'in_progress') {
            response_json(['success' => false, 'error' => 'Assessment session is not active.'], 403);
            return;
        }

        $stmtAtt = $db->prepare("SELECT * FROM `test_attempts` WHERE `guest_test_link_id` = :gid AND `status` = 'in_progress' ORDER BY id DESC LIMIT 1");
        $stmtAtt->execute([':gid' => $link['id']]);
        $attempt = $stmtAtt->fetch();

        if (!$attempt) {
            response_json(['success' => false, 'error' => 'Attempt not found.'], 404);
            return;
        }

        // Check 10-minute server timer
        if (time() > strtotime($attempt['expires_at'])) {
            Test::finalizeTestAttempt((int)$attempt['id'], 'timed_out');
            $db->prepare("UPDATE `guest_test_links` SET `status` = 'submitted', `submitted_at` = NOW() WHERE `id` = :id")
               ->execute([':id' => $link['id']]);
            response_json(['success' => false, 'expired' => true, 'error' => 'Time limit expired. Assessment submitted.']);
            return;
        }

        $questionId = (int)($_POST['question_id'] ?? 0);
        $optionId = !empty($_POST['option_id']) ? (int)$_POST['option_id'] : null;

        $stmtUpd = $db->prepare("UPDATE `attempt_answers` SET `selected_option_id` = :opt, `updated_at` = NOW() WHERE `attempt_id` = :aid AND `question_id` = :qid");
        $stmtUpd->execute([
            ':opt' => $optionId,
            ':aid' => (int)$attempt['id'],
            ':qid' => $questionId
        ]);

        response_json(['success' => true]);
    }

    /**
     * Submit Guest Assessment (POST /test/g/{token}/submit)
     */
    public function submitGuestTest(string $token): void {
        header('X-Robots-Tag: noindex, nofollow');
        $tokenHash = hash('sha256', trim($token));
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM `guest_test_links` WHERE `token_hash` = :hash LIMIT 1");
        $stmt->execute([':hash' => $tokenHash]);
        $link = $stmt->fetch();

        if (!$link) {
            redirect('/test/g/' . $token);
            return;
        }

        $stmtAtt = $db->prepare("SELECT * FROM `test_attempts` WHERE `guest_test_link_id` = :gid AND `status` = 'in_progress' ORDER BY id DESC LIMIT 1");
        $stmtAtt->execute([':gid' => $link['id']]);
        $attempt = $stmtAtt->fetch();

        if ($attempt) {
            Test::finalizeTestAttempt((int)$attempt['id'], 'completed');
            $db->prepare("UPDATE `guest_test_links` SET `status` = 'submitted', `submitted_at` = NOW() WHERE `id` = :id")
               ->execute([':id' => $link['id']]);

            AuditLog::log('guest_test_submitted', 'test_attempts', $attempt['id'], null, [
                'guest_test_link_id' => $link['id'],
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ], (int)$link['user_id']);
        }

        redirect('/test/g/' . $token);
    }

    private function render404(): void {
        echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='robots' content='noindex, nofollow'><title>404 Link Not Found</title><style>body{background:#F7F5EF;color:#26332D;font-family:'DM Sans',system-ui,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px;}.card{background:#FFFFFF;border:1px solid #E2DDD0;border-radius:20px;padding:36px;max-width:440px;text-align:center;}h1{margin:0 0 10px 0;font-size:22px;}p{color:#6D766F;font-size:14px;margin:0 0 20px 0;}a{display:inline-block;padding:10px 20px;background:#1F5C4A;color:#fff;text-decoration:none;border-radius:999px;font-size:14px;}</style></head><body><div class='card'><h1>Link Not Found</h1><p>The test link you are trying to access does not exist or is invalid.</p><a href='/'>Return to Home</a></div></body></html>";
        exit;
    }
}
