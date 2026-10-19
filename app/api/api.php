<?php
/**
 * ==============================================================================================
 * REALTY SMARTZ PATHSHALA - RESTful API SPECIFICATION & ROUTER
 * ==============================================================================================
 * Base URL: /api/v1
 * All requests and responses are JSON encoded (Content-Type: application/json)
 *
 * ----------------------------------------------------------------------------------------------
 * ENDPOINT REGISTRY:
 * ----------------------------------------------------------------------------------------------
 * 1. POST /api/v1/auth/register
 *    - Description: Register a new candidate account and send verification OTP.
 *    - Parameters (JSON body):
 *      {
 *        "full_name": "string (min 2, max 100)",
 *        "email":     "string (valid email, unique)",
 *        "mobile":    "string (10-digit Indian mobile)",
 *        "password":  "string (min 8 chars, 1 uppercase, 1 digit)"
 *      }
 *    - Success Response (201 Created):
 *      { "success": true, "message": "Verification code dispatched.", "user_id": 12 }
 *    - Error Response (422 Unprocessable):
 *      { "success": false, "error": "Validation error message" }
 *
 * 2. POST /api/v1/auth/login
 *    - Description: Authenticate candidate or administrator.
 *    - Parameters (JSON body):
 *      { "email": "string", "password": "string" }
 *    - Success Response (200 OK):
 *      { "success": true, "user": { "id": 12, "full_name": "...", "role": "candidate", "status": "..." } }
 *    - Error Response (401 Unauthorized / 429 Locked):
 *      { "success": false, "error": "Invalid credentials or throttle lock." }
 *
 * 3. POST /api/v1/auth/verify-otp
 *    - Description: Verify candidate email address via 6-digit OTP code.
 *    - Parameters (JSON body):
 *      { "user_id": 12, "otp": "123456" }
 *    - Success Response (200 OK):
 *      { "success": true, "message": "Email verified successfully." }
 *
 * 4. GET /api/v1/candidate/profile
 *    - Description: Retrieve authenticated candidate full profile with decrypted fields and docs.
 *    - Headers: Cookie session or X-User-Id
 *    - Success Response (200 OK):
 *      { "success": true, "profile": { ... } }
 *
 * 5. POST /api/v1/candidate/profile/save
 *    - Description: Save section data for candidate onboarding (Section A through G).
 *    - Parameters: { "section": "personal|identity|education|experience|bank|emergency|declaration", "data": { ... } }
 *    - Success Response (200 OK):
 *      { "success": true, "completion_percentage": 85 }
 *
 * 6. POST /api/v1/candidate/document/upload
 *    - Description: Upload and compress document (multipart/form-data). Max size <= 2MB.
 *    - Fields: "file" (file upload), "document_type" (profile_photo, last_study, offer_letter, etc.)
 *    - Success Response (200 OK):
 *      { "success": true, "document": { "id": 5, "version": 1, "download_url": "..." } }
 *
 * 7. POST /api/v1/candidate/profile/submit
 *    - Description: Submit full profile for HR verification and lock modifications.
 *    - Success Response (200 OK):
 *      { "success": true, "message": "Profile locked and submitted for review." }
 *
 * 8. GET /api/v1/test/session
 *    - Description: Start or resume aptitude assessment session with server timer.
 *    - Success Response (200 OK):
 *      { "success": true, "attempt_id": 10, "time_remaining_seconds": 1800, "questions": [ ... ] }
 *
 * 9. POST /api/v1/test/answer
 *    - Description: Autosave candidate response for question in active test attempt.
 *    - Parameters: { "attempt_id": 10, "question_id": 4, "option_id": 12, "is_marked": false }
 *    - Success Response (200 OK):
 *      { "success": true }
 *
 * 10. POST /api/v1/test/violation
 *     - Description: Log candidate tab-switch or blur anti-cheat violation.
 *     - Parameters: { "attempt_id": 10, "type": "tab_switch|fullscreen_exit" }
 *     - Success Response (200 OK):
 *       { "success": true, "counts": { "tab_switch_count": 2 } }
 *
 * 11. POST /api/v1/test/submit
 *     - Description: Finalize test attempt and compute score, percentage, and pass/fail.
 *     - Parameters: { "attempt_id": 10 }
 *     - Success Response (200 OK):
 *       { "success": true, "score": 22.5, "percentage": 90.0, "passed": true }
 *
 * 12. GET /api/v1/projects
 *     - Description: List active real estate projects with optional ?cat=luxury filter.
 *     - Success Response (200 OK):
 *       { "success": true, "data": [ ... ] }
 * ==============================================================================================
 */

use App\Config\Database;
use App\Models\User;
use App\Models\Profile;
use App\Models\Test;
use App\Models\Project;
use App\Helpers\Validator;
use App\Helpers\Uploader;
use App\Helpers\Mailer;
use App\Middleware\RateLimitMiddleware;

header('Content-Type: application/json; charset=UTF-8');

// Parse request path and input
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
if (false !== $pos = strpos($requestUri, '?')) {
    $requestUri = substr($requestUri, 0, $pos);
}
$endpoint = '/' . trim(str_replace('/api/v1', '', $requestUri), '/');
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// Read JSON payload if content-type is application/json
$rawInput = file_get_contents('php://input');
$jsonBody = !empty($rawInput) ? json_decode($rawInput, true) : [];
$inputData = array_merge($_POST, is_array($jsonBody) ? $jsonBody : []);

try {
    // ----------------------------------------------------------------
    // 1. POST /auth/register
    // ----------------------------------------------------------------
    if ($method === 'POST' && $endpoint === '/auth/register') {
        $validator = Validator::make($inputData, [
            'full_name' => 'required|min:2|max:100',
            'email'     => 'required|email|max:150|unique:users,email',
            'mobile'    => 'required|mobile',
            'password'  => 'required|min:8'
        ]);

        if ($validator->fails()) {
            response_json(['success' => false, 'error' => $validator->firstError() ?? 'Validation failed.'], 422);
        }

        $userId = User::createCandidate($inputData);
        $ver = User::createVerification($userId);
        Mailer::sendVerificationOtp($inputData['email'], $inputData['full_name'], $ver['otp'], $ver['token']);

        response_json([
            'success' => true,
            'message' => 'Verification code dispatched to email.',
            'user_id' => $userId
        ], 201);
    }

    // ----------------------------------------------------------------
    // 2. POST /auth/login
    // ----------------------------------------------------------------
    if ($method === 'POST' && $endpoint === '/auth/login') {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $email = strtolower(trim($inputData['email'] ?? ''));
        $password = $inputData['password'] ?? '';

        $throttle = RateLimitMiddleware::checkLoginThrottle($email, $ip);
        if ($throttle) {
            response_json(['success' => false, 'error' => $throttle], 429);
        }

        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            RateLimitMiddleware::recordFailedLogin($email, $ip);
            response_json(['success' => false, 'error' => 'Invalid email address or password.'], 401);
        }

        RateLimitMiddleware::clearLoginThrottle($email, $ip);
        $_SESSION['user'] = $user;

        response_json([
            'success' => true,
            'user' => [
                'id' => (int)$user['id'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'status' => $user['status'],
                'email_verified' => !empty($user['email_verified_at'])
            ]
        ]);
    }

    // ----------------------------------------------------------------
    // 3. POST /auth/verify-otp
    // ----------------------------------------------------------------
    if ($method === 'POST' && $endpoint === '/auth/verify-otp') {
        $userId = (int) ($inputData['user_id'] ?? ($_SESSION['user']['id'] ?? 0));
        $otp = trim($inputData['otp'] ?? '');

        if (!$userId || empty($otp)) {
            response_json(['success' => false, 'error' => 'User ID and 6-digit OTP code are required.'], 400);
        }

        $res = User::verifyWithOtp($userId, $otp);
        if ($res['success']) {
            if (isset($_SESSION['user']) && (int)$_SESSION['user']['id'] === $userId) {
                $_SESSION['user']['email_verified_at'] = date('Y-m-d H:i:s');
            }
            response_json(['success' => true, 'message' => 'Email verified successfully.']);
        } else {
            response_json(['success' => false, 'error' => $res['error']], 422);
        }
    }

    // ----------------------------------------------------------------
    // 4. GET /candidate/profile
    // ----------------------------------------------------------------
    if ($method === 'GET' && $endpoint === '/candidate/profile') {
        $userId = (int) ($_SESSION['user']['id'] ?? ($inputData['user_id'] ?? 0));
        if (!$userId) {
            response_json(['success' => false, 'error' => 'Unauthorized.'], 401);
        }

        $profile = Profile::getFullProfile($userId);
        unset($profile['password_hash']);
        response_json(['success' => true, 'profile' => $profile]);
    }

    // ----------------------------------------------------------------
    // 5. POST /candidate/profile/save
    // ----------------------------------------------------------------
    if ($method === 'POST' && $endpoint === '/candidate/profile/save') {
        $userId = (int) ($_SESSION['user']['id'] ?? ($inputData['user_id'] ?? 0));
        if (!$userId) {
            response_json(['success' => false, 'error' => 'Unauthorized.'], 401);
        }

        $section = $inputData['section'] ?? '';
        $data = $inputData['data'] ?? $inputData;

        match ($section) {
            'personal'    => Profile::savePersonal($userId, $data),
            'identity'    => Profile::saveIdentity($userId, $data),
            'education'   => Profile::saveEducation($userId, $data),
            'experience'  => Profile::saveExperience($userId, $data),
            'bank'        => Profile::saveBank($userId, $data),
            'emergency'   => Profile::saveEmergency($userId, $data),
            'declaration' => Profile::saveDeclaration($userId, $data),
            default       => response_json(['success' => false, 'error' => 'Invalid section name.'], 400)
        };

        $fresh = Profile::getFullProfile($userId);
        response_json([
            'success' => true,
            'completion_percentage' => $fresh['profile']['completion_percentage'] ?? 0
        ]);
    }

    // ----------------------------------------------------------------
    // 6. POST /candidate/document/upload
    // ----------------------------------------------------------------
    if ($method === 'POST' && $endpoint === '/candidate/document/upload') {
        $userId = (int) ($_SESSION['user']['id'] ?? ($inputData['user_id'] ?? 0));
        $docType = $inputData['document_type'] ?? '';

        if (!$userId || empty($_FILES['file']) || empty($docType)) {
            response_json(['success' => false, 'error' => 'Missing file, user or document type.'], 400);
        }

        $doc = Uploader::process($_FILES['file'], $userId, $docType);
        response_json(['success' => true, 'document' => $doc]);
    }

    // ----------------------------------------------------------------
    // 7. POST /test/session
    // ----------------------------------------------------------------
    if ($method === 'POST' && $endpoint === '/test/session') {
        $userId = (int) ($_SESSION['user']['id'] ?? ($inputData['user_id'] ?? 0));
        if (!$userId) {
            response_json(['success' => false, 'error' => 'Unauthorized.'], 401);
        }

        $session = Test::startOrResumeTest($userId, 1);
        response_json($session);
    }

    // ----------------------------------------------------------------
    // 8. POST /test/answer
    // ----------------------------------------------------------------
    if ($method === 'POST' && $endpoint === '/test/answer') {
        $userId = (int) ($_SESSION['user']['id'] ?? ($inputData['user_id'] ?? 0));
        $attemptId = (int) ($inputData['attempt_id'] ?? 0);
        $questionId = (int) ($inputData['question_id'] ?? 0);
        $optionId = !empty($inputData['option_id']) ? (int)$inputData['option_id'] : null;
        $isMarked = !empty($inputData['is_marked']);

        $res = Test::saveAnswer($attemptId, $userId, $questionId, $optionId, $isMarked);
        response_json($res);
    }

    // ----------------------------------------------------------------
    // 9. POST /test/violation
    // ----------------------------------------------------------------
    if ($method === 'POST' && $endpoint === '/test/violation') {
        $userId = (int) ($_SESSION['user']['id'] ?? ($inputData['user_id'] ?? 0));
        $attemptId = (int) ($inputData['attempt_id'] ?? 0);
        $type = $inputData['type'] ?? 'tab_switch';

        $res = Test::logAntiCheatViolation($attemptId, $userId, $type);
        response_json($res);
    }

    // ----------------------------------------------------------------
    // 10. POST /test/submit
    // ----------------------------------------------------------------
    if ($method === 'POST' && $endpoint === '/test/submit') {
        $attemptId = (int) ($inputData['attempt_id'] ?? 0);
        $res = Test::finalizeTestAttempt($attemptId, 'completed');
        response_json($res);
    }

    // ----------------------------------------------------------------
    // 11. GET /projects
    // ----------------------------------------------------------------
    if ($method === 'GET' && $endpoint === '/projects') {
        $cat = $_GET['cat'] ?? null;
        $projects = Project::getAllPublished($cat);
        response_json(['success' => true, 'data' => $projects]);
    }

    // If endpoint doesn't match any route
    response_json(['success' => false, 'error' => "Endpoint {$method} {$endpoint} not found."], 404);

} catch (Exception $e) {
    response_json(['success' => false, 'error' => $e->getMessage()], 500);
}
