<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\InterviewApplication;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\User;
use App\Helpers\QrCodeHelper;
use App\Helpers\PdfHelper;
use App\Helpers\Mailer;
use Exception;
use Throwable;

class InterviewController {

    /**
     * =========================================================================
     * PART A: PUBLIC CANDIDATE-FACING FORM (/q/{slug})
     * =========================================================================
     */

    /**
     * Display the public candidate form via direct clean URL (/interview-candidate)
     */
    public function showPublicFormDirect(): void {
        $qr = InterviewApplication::getQrSettings();
        $slug = $qr['slug'] ?? 'interview-candidate';
        $this->showPublicForm($slug, true);
    }

    /**
     * Handle public form submission via direct clean URL (/interview-candidate)
     */
    public function submitPublicFormDirect(): void {
        $qr = InterviewApplication::getQrSettings();
        $slug = $qr['slug'] ?? 'interview-candidate';
        $this->submitPublicForm($slug, true);
    }

    /**
     * Display success page via direct clean URL (/interview-candidate/success)
     */
    public function showSuccessDirect(): void {
        $qr = InterviewApplication::getQrSettings();
        $slug = $qr['slug'] ?? 'interview-candidate';
        $this->showSuccess($slug, true);
    }

    /**
     * Display the public candidate form
     */
    public function showPublicForm(string $slug, bool $isDirect = false): void {
        header('X-Robots-Tag: noindex, nofollow');

        $slug = trim($slug);
        $qr = $this->verifySlug($slug);
        if (!$qr) {
            $this->render404();
            return;
        }

        // Check accepting_responses
        if (empty($qr['accepting_responses'])) {
            require APP_PATH . '/views/public/interview_not_accepting.php';
            exit;
        }

        // Generate idempotency token if not already present
        if (empty($_SESSION['interview_idempotency'])) {
            $_SESSION['interview_idempotency'] = bin2hex(random_bytes(16));
        }
        $idempotencyToken = $_SESSION['interview_idempotency'];

        // Load HR Names
        $hrConfigFile = APP_PATH . '/config/hr_names.php';
        $hrNames = file_exists($hrConfigFile) ? require $hrConfigFile : [];
        if (!is_array($hrNames)) $hrNames = [];

        $csrfToken = csrf_token();
        $_SESSION['_csrf_token'] = $csrfToken;
        $_SESSION['csrf_token'] = $csrfToken;
        $signedToken = hash_hmac('sha256', 'interview_' . $slug, (string)config('APP_KEY', 'realty_smartz_pathsala_secret_2026'));

        require APP_PATH . '/views/public/interview_form.php';
    }

    /**
     * Handle public candidate form submission (POST /q/{slug} or /interview-candidate)
     */
    public function submitPublicForm(string $slug, bool $isDirect = false): void {
        header('X-Robots-Tag: noindex, nofollow');

        $slug = trim($slug);
        $qr = $this->verifySlug($slug);
        if (!$qr) {
            $this->render404();
            return;
        }

        if (empty($qr['accepting_responses'])) {
            require APP_PATH . '/views/public/interview_not_accepting.php';
            exit;
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Rate limit verification (10 submissions per hour per IP)
        if (!InterviewApplication::checkRateLimit($ip)) {
            http_response_code(429);
            $errorMessage = "Too many submissions from this connection. Please try again after an hour.";
            require APP_PATH . '/views/public/interview_error.php';
            exit;
        }

        // Honeypot check: field named "website_url" or "hp_field" must be empty
        if (!empty($_POST['website_url']) || !empty($_POST['hp_field'])) {
            // Silently redirect to success for bot traps without saving
            redirect($isDirect ? '/interview-candidate/success' : '/q/' . urlencode($slug) . '/success');
            return;
        }

        // CSRF verification (supports session CSRF + mobile webview HMAC fallback)
        $postedCsrf = trim((string)($_POST['csrf_token'] ?? $_POST['_csrf_token'] ?? ''));
        $postedSigned = trim((string)($_POST['signed_token'] ?? ''));
        $sessionCsrf = trim((string)($_SESSION['_csrf_token'] ?? $_SESSION['csrf_token'] ?? ''));
        $expectedSigned = hash_hmac('sha256', 'interview_' . $slug, (string)config('APP_KEY', 'realty_smartz_pathsala_secret_2026'));

        $isCsrfValid = (!empty($postedCsrf) && !empty($sessionCsrf) && hash_equals($sessionCsrf, $postedCsrf));
        $isSignedValid = (!empty($postedSigned) && hash_equals($expectedSigned, $postedSigned));

        if (!$isCsrfValid && !$isSignedValid) {
            http_response_code(403);
            $errorMessage = "Invalid or expired security token. Please refresh the page and try again.";
            require APP_PATH . '/views/public/interview_error.php';
            exit;
        }

        // Idempotency check: if token was already used, redirect directly to success
        $postIdempotency = trim($_POST['idempotency_token'] ?? '');
        if (!empty($_SESSION['interview_submitted_token']) && $_SESSION['interview_submitted_token'] === $postIdempotency) {
            redirect($isDirect ? '/interview-candidate/success' : '/q/' . urlencode($slug) . '/success');
            return;
        }

        // Load HR Names for strict validation
        $hrConfigFile = APP_PATH . '/config/hr_names.php';
        $allowedHrNames = file_exists($hrConfigFile) ? require $hrConfigFile : [];
        if (!is_array($allowedHrNames)) $allowedHrNames = [];

        // STRICT SERVER-SIDE VALIDATION & SANITIZATION
        $errors = [];

        // 1. HR Name
        $hrName = trim(strip_tags((string)($_POST['hr_name'] ?? '')));
        if (empty($hrName) || !in_array($hrName, $allowedHrNames, true)) {
            $errors['hr_name'] = "Please select a valid HR Name from the list.";
        }

        // 2. Post Applied For
        $postApplied = trim(strip_tags((string)($_POST['post_applied'] ?? '')));
        if (empty($postApplied)) {
            $errors['post_applied'] = "Post Applied For is required.";
        } elseif (mb_strlen($postApplied) > 255) {
            $errors['post_applied'] = "Post Applied For cannot exceed 255 characters.";
        }

        // 2b. Applied For Segment (Commercial / Residential / Both)
        $rawAppliedForType = strtolower(trim((string)($_POST['applied_for_type'] ?? 'commercial')));
        if (!in_array($rawAppliedForType, ['commercial', 'residential', 'both'], true)) {
            $errors['applied_for_type'] = "Please select whether you applied for Commercial or Residential.";
            $appliedForType = 'Commercial';
        } else {
            $appliedForType = ucfirst($rawAppliedForType);
        }

        // 3. Full Name
        $fullName = trim(strip_tags((string)($_POST['full_name'] ?? '')));
        if (empty($fullName)) {
            $errors['full_name'] = "Full Name is required.";
        } elseif (mb_strlen($fullName) > 100) {
            $errors['full_name'] = "Full Name cannot exceed 100 characters.";
        }

        // 4. Mail ID
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Please enter a valid Mail ID.";
        } elseif (mb_strlen($email) > 255) {
            $errors['email'] = "Mail ID cannot exceed 255 characters.";
        }

        // 5. Contact Number (10-digit Indian mobile starting 6-9, numeric keypad, +91/spaces stripped)
        $rawMobile = (string)($_POST['mobile'] ?? '');
        $cleanMobile = preg_replace('/[^\d]/', '', $rawMobile);
        if (str_starts_with($cleanMobile, '91') && strlen($cleanMobile) === 12) {
            $cleanMobile = substr($cleanMobile, 2);
        }
        if (strlen($cleanMobile) !== 10 || !preg_match('/^[6-9]\d{9}$/', $cleanMobile)) {
            $errors['mobile'] = "Enter a valid 10-digit Indian mobile number starting with 6, 7, 8, or 9.";
        }

        // 6. Experience / Fresher (Radio)
        $experienceType = strtolower(trim((string)($_POST['experience_type'] ?? '')));
        if (!in_array($experienceType, ['experience', 'fresher'], true)) {
            $errors['experience_type'] = "Please select either Experience or Fresher.";
        }

        // 7. Years in Real Estate (number, step 0.5, min 0; shown and required only when Experience is selected)
        $yearsInRealEstate = null;
        if ($experienceType === 'experience') {
            $rawYears = trim((string)($_POST['years_in_real_estate'] ?? ''));
            if ($rawYears === '' || !is_numeric($rawYears) || (float)$rawYears < 0) {
                $errors['years_in_real_estate'] = "Years in Real Estate is required (min 0, step 0.5).";
            } else {
                $yearsInRealEstate = round((float)$rawYears, 1);
            }
        }

        // 8. Employment history (only when Experience is selected)
        $employmentRows = [];
        if ($experienceType === 'experience') {
            $postedRows = $_POST['employment_rows'] ?? [];
            if (!is_array($postedRows)) $postedRows = [];

            $sortOrder = 1;
            foreach ($postedRows as $r) {
                if (!is_array($r)) continue;
                $org = trim(strip_tags((string)($r['organisation'] ?? '')));
                $desig = trim(strip_tags((string)($r['designation'] ?? '')));
                
                $rawSal = trim(strip_tags((string)($r['salary'] ?? $r['salary_amount'] ?? '')));
                $salPeriod = trim(strip_tags((string)($r['salary_period'] ?? $r['salary_type'] ?? 'Per Month')));
                if (!in_array($salPeriod, ['Per Month', 'LPA'], true)) {
                    $salPeriod = 'Per Month';
                }

                $sal = '';
                if (!empty($rawSal)) {
                    $cleanAmount = preg_replace('/^(₹|\s)+/u', '', $rawSal);
                    $cleanAmount = trim((string)$cleanAmount);
                    if (preg_match('/\b(lpa|lac|lakh|per annum|pa)\b/i', $cleanAmount)) {
                        $sal = '₹ ' . $cleanAmount;
                    } elseif (preg_match('/\b(per month|month|\/mo|\/m|pm)\b/i', $cleanAmount)) {
                        $sal = '₹ ' . $cleanAmount;
                    } else {
                        $sal = '₹ ' . $cleanAmount . ' ' . $salPeriod;
                    }
                }

                $hrNum = trim(strip_tags((string)($r['hr_name_number'] ?? '')));
                $dur = trim(strip_tags((string)($r['duration'] ?? '')));

                if (!empty($org) || !empty($desig) || !empty($sal) || !empty($hrNum) || !empty($dur)) {
                    $employmentRows[] = [
                        'sort_order'     => $sortOrder++,
                        'organisation'   => mb_substr($org, 0, 255),
                        'designation'    => mb_substr($desig, 0, 255),
                        'salary'         => mb_substr($sal, 0, 100),
                        'hr_name_number' => mb_substr($hrNum, 0, 255),
                        'duration'       => mb_substr($dur, 0, 100),
                    ];
                }
                if ($sortOrder > 6) break;
            }

            // Company / Organisation name is strictly mandatory when Experience is selected
            if (empty($employmentRows) || empty($employmentRows[0]['organisation'])) {
                $errors['employment_history'] = "Company / Organisation name is mandatory when Experience is selected.";
            }
        }

        // 9. Vehicle Available (Radio: Yes / No)
        $vehicleAvailable = strtolower(trim((string)($_POST['vehicle_available'] ?? '')));
        if (!in_array($vehicleAvailable, ['yes', 'no'], true)) {
            $errors['vehicle_available'] = "Please select Vehicle Available (Yes / No).";
        }

        // 10. Current Location (text, required)
        $currentLocation = trim(strip_tags((string)($_POST['current_location'] ?? '')));
        if (empty($currentLocation)) {
            $errors['current_location'] = "Current Location is required.";
        } elseif (mb_strlen($currentLocation) > 255) {
            $errors['current_location'] = "Current Location cannot exceed 255 characters.";
        }

        // 11. Higher Education (text, required)
        $higherEducation = trim(strip_tags((string)($_POST['higher_education'] ?? '')));
        if (empty($higherEducation)) {
            $errors['higher_education'] = "Higher Education is required.";
        } elseif (mb_strlen($higherEducation) > 255) {
            $errors['higher_education'] = "Higher Education cannot exceed 255 characters.";
        }

        // 12. Any References (Radio: Yes / No) + Reference details (textarea when Yes)
        $hasReferences = strtolower(trim((string)($_POST['has_references'] ?? '')));
        if (!in_array($hasReferences, ['yes', 'no'], true)) {
            $errors['has_references'] = "Please specify Any References (Yes / No).";
        }

        $referenceDetails = null;
        if ($hasReferences === 'yes') {
            $referenceDetails = trim(strip_tags((string)($_POST['reference_details'] ?? '')));
            if (empty($referenceDetails)) {
                $errors['reference_details'] = "Reference details are required when References is Yes.";
            } elseif (mb_strlen($referenceDetails) > 1000) {
                $errors['reference_details'] = "Reference details cannot exceed 1000 characters.";
            }
        }

        // 13. Last Company HR and TL Name (text, only when Experience)
        $lastCompanyHrTl = null;
        if ($experienceType === 'experience') {
            $lastCompanyHrTl = trim(strip_tags((string)($_POST['last_company_hr_tl'] ?? '')));
            if (mb_strlen($lastCompanyHrTl) > 255) {
                $errors['last_company_hr_tl'] = "Last Company HR and TL Name cannot exceed 255 characters.";
            }
        }

        // 14. Reason to Leave (text, only when Experience)
        $reasonToLeave = null;
        if ($experienceType === 'experience') {
            $reasonToLeave = trim(strip_tags((string)($_POST['reason_to_leave'] ?? '')));
            if (mb_strlen($reasonToLeave) > 255) {
                $errors['reason_to_leave'] = "Reason to Leave cannot exceed 255 characters.";
            }
        }

        // 15. Immediate Joiner (Radio: Yes / No)
        $immediateJoiner = strtolower(trim((string)($_POST['immediate_joiner'] ?? '')));
        if (!in_array($immediateJoiner, ['yes', 'no'], true)) {
            $errors['immediate_joiner'] = "Please specify Immediate Joiner (Yes / No).";
        }

        // 16. Consent Checkbox (required)
        $consent = !empty($_POST['consent']);
        if (!$consent) {
            $errors['consent'] = "You must agree to the storage of your details for recruitment.";
        }

        // HARD RULE: The public form NEVER saves posted HR fields:
        // Source By, feedback, decision, offered salary, date of joining, position offered, module discussed.
        // Any such posted values are strictly ignored.

        if (!empty($errors)) {
            $oldInput = $_POST;
            $hrNames = $allowedHrNames;
            $csrfToken = csrf_token();
            $idempotencyToken = $postIdempotency ?: bin2hex(random_bytes(16));
            require APP_PATH . '/views/public/interview_form.php';
            return;
        }

        // Transactional creation
        try {
            $appData = [
                'hr_name'              => $hrName,
                'post_applied'         => $postApplied,
                'applied_for_type'     => $appliedForType,
                'full_name'            => $fullName,
                'email'                => $email,
                'mobile'               => $cleanMobile,
                'experience_type'      => $experienceType,
                'years_in_real_estate' => $yearsInRealEstate,
                'vehicle_available'    => $vehicleAvailable,
                'current_location'     => $currentLocation,
                'higher_education'     => $higherEducation,
                'has_references'       => $hasReferences,
                'reference_details'    => $referenceDetails,
                'last_company_hr_tl'   => $lastCompanyHrTl,
                'reason_to_leave'      => $reasonToLeave,
                'immediate_joiner'     => $immediateJoiner,
                'ip_address'           => $ip,
                'user_agent'           => mb_substr($userAgent, 0, 500)
            ];

            $appId = InterviewApplication::create($appData, $employmentRows);

            // Realtime Sync to Google Sheets (Tab: Interview Candidate)
            try {
                \App\Helpers\GoogleSheetsService::syncInterviewApplication($appId);
            } catch (\Throwable $e) {
                \App\Helpers\GoogleSheetsService::log("Google Sheets sync failed on interview submit ({$appId}): " . $e->getMessage());
            }

            // Realtime Telegram Alert to Group
            try {
                \App\Helpers\TelegramService::sendInterviewCandidateAlert($appId);
            } catch (\Throwable $e) {
                \App\Helpers\TelegramService::log("Telegram notification failed on interview submit ({$appId}): " . $e->getMessage());
            }

            // Record idempotency token in session
            $_SESSION['interview_submitted_token'] = $postIdempotency;
            unset($_SESSION['interview_idempotency']);

            // Post-Redirect-Get to avoid duplicate submissions on refresh
            redirect($isDirect ? '/interview-candidate/success' : '/q/' . urlencode($slug) . '/success');
            return;

        } catch (Throwable $e) {
            // Log error silently without exposing stack traces to public
            $logDir = STORAGE_PATH . '/logs';
            if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
            @file_put_contents($logDir . '/interview_errors.log', "[" . date('Y-m-d H:i:s') . "] Submission Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n", FILE_APPEND);

            http_response_code(500);
            $errorMessage = "We encountered a temporary server issue while submitting your form. Please try again in a few moments.";
            require APP_PATH . '/views/public/interview_error.php';
            exit;
        }
    }

    /**
     * Show success page (/q/{slug}/success or /interview-candidate/success)
     */
    public function showSuccess(string $slug, bool $isDirect = false): void {
        header('X-Robots-Tag: noindex, nofollow');

        $slug = trim($slug);
        $qr = $this->verifySlug($slug);
        if (!$qr) {
            $this->render404();
            return;
        }

        require APP_PATH . '/views/public/interview_success.php';
    }

    /**
     * Helper to verify slug from database
     */
    private function verifySlug(string $slug): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `qr_settings` WHERE `type` = 'interview_form' AND `slug` = :slug LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    private function render404(): void {
        http_response_code(404);
        if (file_exists(APP_PATH . '/views/public/404.php')) {
            require APP_PATH . '/views/public/404.php';
        } else {
            echo "404 Not Found";
        }
        exit;
    }

    /**
     * =========================================================================
     * PART B: ADMIN SECTION "INTERVIEW CANDIDATES"
     * =========================================================================
     */

    /**
     * List page (/admin/interview-candidates)
     */
    public function index(): void {
        $user = auth();
        if (!$user || !in_array($user['role'], ['super_admin', 'hr_manager', 'reviewer'], true)) {
            redirect('/admin/login');
            return;
        }

        $filters = [
            'decision'         => $_GET['decision'] ?? '',
            'search'           => $_GET['search'] ?? '',
            'hr_name'          => $_GET['hr_name'] ?? '',
            'experience_type'  => $_GET['experience_type'] ?? '',
            'immediate_joiner' => $_GET['immediate_joiner'] ?? '',
            'date_range'       => $_GET['date_range'] ?? '',
            'feedback_status'  => $_GET['feedback_status'] ?? '',
            'archived'         => !empty($_GET['archived']) ? 1 : 0
        ];

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;

        $result = InterviewApplication::list($filters, $page, $perPage);
        $counts = InterviewApplication::getCountsByDecision();

        // Load HR names for filter dropdown
        $hrConfigFile = APP_PATH . '/config/hr_names.php';
        $hrNames = file_exists($hrConfigFile) ? require $hrConfigFile : [];

        $pageTitle = "Interview Candidates | Realty Smartz Pathshala";
        $viewTitle = "Interview Candidates";

        require APP_PATH . '/views/admin/interview/list.php';
    }

    /**
     * Detail page (/admin/interview-candidates/{id})
     */
    public function detail(int $id): void {
        $user = auth();
        if (!$user || !in_array($user['role'], ['super_admin', 'hr_manager', 'reviewer'], true)) {
            redirect('/admin/login');
            return;
        }

        $app = InterviewApplication::findById($id);
        if (!$app) {
            flash('error', 'Interview application not found.');
            redirect('/admin/interview-candidates');
            return;
        }

        // Mark viewed automatically if not already viewed
        if (empty($app['first_viewed_at'])) {
            InterviewApplication::markViewed($id, (int)$user['id']);
            $app['first_viewed_at'] = date('Y-m-d H:i:s');
            $app['first_viewed_by'] = (int)$user['id'];
            $app['first_viewed_by_name'] = $user['full_name'];
        }

        // Find repeat applicants (same mobile or email)
        $repeatApplicants = InterviewApplication::findRepeatApplicants($app['mobile'], $app['email'], $id);

        $isReadOnly = ($user['role'] === 'reviewer');

        $pageTitle = e($app['full_name']) . " | Interview Details";
        $viewTitle = "Interview Candidate Details";

        require APP_PATH . '/views/admin/interview/detail.php';
    }

    /**
     * Save all HR feedback fields manually, and dispatch candidate email when requested
     * (/admin/interview-candidates/{id}/save-feedback)
     */
    public function saveFeedback(int $id): void {
        $user = auth();
        if (!$user || !in_array($user['role'], ['super_admin', 'hr_manager', 'reviewer'], true)) {
            if ($this->isJsonRequest()) {
                response_json(['success' => false, 'error' => 'Unauthorized'], 401);
                return;
            }
            redirect('/admin/login');
            return;
        }

        if ($user['role'] === 'reviewer') {
            if ($this->isJsonRequest()) {
                response_json(['success' => false, 'error' => 'Reviewer accounts are read-only.'], 403);
                return;
            }
            flash('error', 'Reviewer accounts are read-only.');
            redirect('/admin/interview-candidates/' . $id);
            return;
        }

        $input = $_POST;
        if (empty($input) && $this->isJsonRequest()) {
            $rawJson = file_get_contents('php://input');
            $input = !empty($rawJson) ? (json_decode($rawJson, true) ?? []) : [];
        }

        $res = InterviewApplication::saveHrFeedback($id, $input, (int)$user['id']);

        if ($this->isJsonRequest()) {
            response_json($res, $res['success'] ? 200 : 422);
            return;
        }

        if ($res['success']) {
            flash('success', $res['message']);
        } else {
            flash('error', $res['error'] ?? 'Failed to save feedback.');
        }
        redirect('/admin/interview-candidates/' . $id);
    }

    private function isJsonRequest(): bool {
        $header = $_SERVER['HTTP_ACCEPT'] ?? '';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return (stripos($header, 'application/json') !== false || stripos($contentType, 'application/json') !== false);
    }

    /**
     * Autosave API PATCH endpoint (/api/interview/{id})
     */
    public function patchField(int $id, array $input): void {
        $user = auth();
        if (!$user || !in_array($user['role'], ['super_admin', 'hr_manager', 'reviewer'], true)) {
            response_json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        if ($user['role'] === 'reviewer') {
            response_json(['success' => false, 'error' => 'Reviewer accounts are read-only.'], 403);
            return;
        }

        $field = trim((string)($input['field'] ?? ''));
        $value = $input['value'] ?? null;
        $clientVersion = (int)($input['version'] ?? 0);

        if (empty($field)) {
            response_json(['success' => false, 'error' => 'Missing field name'], 422);
            return;
        }

        $res = InterviewApplication::updateField($id, $field, $value, $clientVersion, (int)$user['id']);

        if (!empty($res['conflict'])) {
            response_json($res, 409);
            return;
        }

        if (empty($res['success'])) {
            response_json($res, 422);
            return;
        }

        response_json($res, 200);
    }

    /**
     * Live polling API updates endpoint (/api/interview/updates?since={cursor})
     */
    public function apiUpdates(): void {
        $user = auth();
        if (!$user || !in_array($user['role'], ['super_admin', 'hr_manager', 'reviewer'], true)) {
            response_json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $since = trim((string)($_GET['since'] ?? ''));
        if (empty($since) || !strtotime($since)) {
            $since = date('Y-m-d H:i:s', strtotime('-1 hour'));
        }

        $updates = InterviewApplication::getUpdatesSince($since);
        response_json(['success' => true, 'data' => $updates], 200);
    }

    /**
     * Archive application (/admin/interview-candidates/{id}/archive)
     */
    public function archive(int $id): void {
        $user = auth();
        if (!$user || !in_array($user['role'], ['super_admin', 'hr_manager'], true)) {
            flash('error', 'Unauthorized action.');
            redirect('/admin/interview-candidates');
            return;
        }

        InterviewApplication::archive($id, (int)$user['id']);
        flash('success', 'Candidate application archived successfully.');
        redirect('/admin/interview-candidates/' . $id);
    }

    /**
     * Restore application (/admin/interview-candidates/{id}/restore)
     */
    public function restore(int $id): void {
        $user = auth();
        if (!$user || !in_array($user['role'], ['super_admin', 'hr_manager'], true)) {
            flash('error', 'Unauthorized action.');
            redirect('/admin/interview-candidates');
            return;
        }

        InterviewApplication::restore($id, (int)$user['id']);
        flash('success', 'Candidate application restored successfully.');
        redirect('/admin/interview-candidates/' . $id);
    }

    /**
     * Toggle QR accepting responses (/admin/interview/toggle-accepting)
     */
    public function toggleAccepting(): void {
        $user = auth();
        if (!$user || !in_array($user['role'], ['super_admin', 'hr_manager'], true)) {
            response_json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $accepting = !empty($_POST['accepting']);
        InterviewApplication::updateQrSettings($accepting);

        AuditLog::log('qr_toggle_accepting', 'qr_settings', 1, null, ['accepting' => $accepting], (int)$user['id']);

        response_json(['success' => true, 'accepting' => $accepting]);
    }

    /**
     * Download print-ready A4 PDF mirroring the paper Interview Application Form
     * (/admin/interview-candidates/{id}/pdf)
     */
    public function downloadPdf(int $id): void {
        $user = auth();
        if (!$user || !in_array($user['role'], ['super_admin', 'hr_manager', 'reviewer'], true)) {
            flash('error', 'Unauthorized.');
            redirect('/admin/login');
            return;
        }

        $app = InterviewApplication::findById($id);
        if (!$app) {
            flash('error', 'Application record not found.');
            redirect('/admin/interview-candidates');
            return;
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        $pdfContent = PdfHelper::generateInterviewFormPdf($app);

        $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $app['full_name']);
        $filename = "Interview_Form_{$cleanName}_{$id}.pdf";

        AuditLog::log('download_interview_pdf', 'interview_applications', $id, null, [
            'filename' => $filename,
            'ip'       => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ], (int)$user['id']);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdfContent));
        header('Cache-Control: private, max-age=0, must-revalidate');
        echo $pdfContent;
        exit;
    }

    /**
     * Export Excel/CSV from list respecting current filters
     * (/admin/interview-candidates/export)
     */
    public function export(): void {
        $user = auth();
        if (!$user || !in_array($user['role'], ['super_admin', 'hr_manager', 'reviewer'], true)) {
            flash('error', 'Unauthorized.');
            redirect('/admin/login');
            return;
        }

        $filters = [
            'decision'         => $_GET['decision'] ?? '',
            'search'           => $_GET['search'] ?? '',
            'hr_name'          => $_GET['hr_name'] ?? '',
            'experience_type'  => $_GET['experience_type'] ?? '',
            'immediate_joiner' => $_GET['immediate_joiner'] ?? '',
            'date_range'       => $_GET['date_range'] ?? '',
            'feedback_status'  => $_GET['feedback_status'] ?? '',
            'archived'         => !empty($_GET['archived']) ? 1 : 0
        ];

        // Fetch up to 5000 records
        $result = InterviewApplication::list($filters, 1, 5000);
        $items = $result['items'] ?? [];

        while (ob_get_level()) {
            ob_end_clean();
        }

        $format = strtolower(trim((string)($_GET['format'] ?? 'csv')));
        $filename = 'Interview_Candidates_' . date('Y_m_d_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // UTF-8 BOM for Excel compatibility
        fputs($out, "\xEF\xBB\xBF");

        // Headers
        fputcsv($out, [
            'ID',
            'Full Name',
            'Post Applied For',
            'Mobile',
            'Mail ID',
            'HR Name',
            'Experience / Fresher',
            'Submitted Date',
            'Decision',
            'Offered Salary',
            'Joining Date',
            'Feedback by HR',
            'Feedback Final'
        ]);

        foreach ($items as $row) {
            fputcsv($out, [
                $row['id'],
                $row['full_name'],
                $row['post_applied'],
                $row['mobile'],
                $row['email'],
                $row['hr_name'],
                ucfirst($row['experience_type']),
                date('d M Y, h:i A', strtotime($row['submitted_at'])),
                ucfirst($row['decision']),
                $row['offered_salary'] ? '₹' . number_format((float)$row['offered_salary'], 2) : 'N/A',
                $row['joining_date'] ? date('d M Y', strtotime($row['joining_date'])) : 'N/A',
                $row['feedback_hr'] ?: '',
                $row['feedback_final'] ?: ''
            ]);
        }

        fclose($out);
        exit;
    }

    /**
     * Download QR code for Interview Form (/admin/qr-codes/download/interview)
     */
    public function downloadQrCode(string $format = 'png'): void {
        $user = auth();
        if (!$user || !in_array($user['role'], ['super_admin', 'hr_manager', 'reviewer'], true)) {
            flash('error', 'Unauthorized.');
            redirect('/admin/login');
            return;
        }

        $qr = InterviewApplication::getQrSettings();
        $targetUrl = QrCodeHelper::getBaseUrl() . '/interview-candidate';

        while (ob_get_level()) {
            ob_end_clean();
        }

        if ($format === 'svg') {
            $svg = QrCodeHelper::getSvg($targetUrl);
            header('Content-Type: image/svg+xml');
            header('Content-Disposition: attachment; filename="Realty_Smartz_Interview_Form_QR.svg"');
            header('Content-Length: ' . strlen($svg));
            echo $svg;
            exit;
        }

        // High resolution PNG with solid white background and 4-module quiet zone (scannable by all devices)
        $png = QrCodeHelper::getPng($targetUrl);
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="Realty_Smartz_Interview_Form_QR.png"');
        header('Content-Length: ' . strlen($png));
        echo $png;
        exit;
    }

    /**
     * Save automation settings & test email (/admin/interview/settings)
     */
    public function saveSettings(): void {
        $user = auth();
        if (!$user || !in_array($user['role'], ['super_admin', 'hr_manager'], true)) {
            response_json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        // Handle Test Email
        if (!empty($_POST['action']) && $_POST['action'] === 'send_test_email') {
            $testType = trim((string)($_POST['test_type'] ?? 'hr_new_form'));
            $testEmail = trim((string)($_POST['test_email'] ?? ''));

            if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
                response_json(['success' => false, 'error' => 'Please provide a valid recipient email address for testing.'], 422);
                return;
            }

            $subject = "Test Automation Email ({$testType}) | Realty Smartz Pathshala";
            $bodyHtml = "<p>This is a test notification for <strong>" . e($testType) . "</strong> sent from the Realty Smartz Pathshala Admin Portal.</p>";
            
            try {
                $sent = Mailer::sendRawHtml($testEmail, $subject, $bodyHtml);
                if ($sent) {
                    response_json(['success' => true, 'message' => "Test email dispatched successfully to {$testEmail}."]);
                } else {
                    response_json(['success' => false, 'error' => "Failed to deliver test email. Please check SMTP configuration."]);
                }
            } catch (Throwable $e) {
                response_json(['success' => false, 'error' => $e->getMessage()]);
            }
            return;
        }

        // Handle Test Telegram Bot Connection
        if (!empty($_POST['action']) && $_POST['action'] === 'send_test_telegram') {
            $testToken = trim((string)($_POST['telegram_bot_token'] ?? ''));
            $testChatId = trim((string)($_POST['telegram_chat_id'] ?? ''));

            try {
                $res = \App\Helpers\TelegramService::testConnection($testToken, $testChatId);
                response_json($res);
            } catch (Throwable $e) {
                response_json(['success' => false, 'error' => $e->getMessage()]);
            }
            return;
        }

        // Handle Auto-Detect Group Chats
        if (!empty($_POST['action']) && $_POST['action'] === 'detect_telegram_chats') {
            $testToken = trim((string)($_POST['telegram_bot_token'] ?? ''));

            try {
                $chats = \App\Helpers\TelegramService::getRecentChats($testToken);
                response_json(['success' => true, 'chats' => $chats]);
            } catch (Throwable $e) {
                response_json(['success' => false, 'error' => $e->getMessage()]);
            }
            return;
        }

        // Save Automation switches and templates
        Setting::set('interview_email_hr_new_form', !empty($_POST['interview_email_hr_new_form']) ? 1 : 0);
        Setting::set('interview_email_hr_recipients', trim((string)($_POST['interview_email_hr_recipients'] ?? 'hr@realtysmartzpathshala.in')));
        Setting::set('interview_email_candidate_selected', !empty($_POST['interview_email_candidate_selected']) ? 1 : 0);
        Setting::set('interview_email_selected_subject', trim((string)($_POST['interview_email_selected_subject'] ?? 'Offer of Employment | Realty Smartz Pathshala')));
        Setting::set('interview_email_selected_body', trim((string)($_POST['interview_email_selected_body'] ?? '')));
        Setting::set('interview_email_candidate_rejected', !empty($_POST['interview_email_candidate_rejected']) ? 1 : 0);
        Setting::set('interview_email_rejected_subject', trim((string)($_POST['interview_email_rejected_subject'] ?? 'Application Update | Realty Smartz Pathshala')));
        Setting::set('interview_email_rejected_body', trim((string)($_POST['interview_email_rejected_body'] ?? '')));

        // Telegram Bot Automation settings
        Setting::set('telegram_notifications_enabled', !empty($_POST['telegram_notifications_enabled']) ? 1 : 0);
        if (isset($_POST['telegram_bot_token'])) {
            Setting::set('telegram_bot_token', trim((string)($_POST['telegram_bot_token'] ?? '')));
        }
        if (isset($_POST['telegram_chat_id'])) {
            Setting::set('telegram_chat_id', trim((string)($_POST['telegram_chat_id'] ?? '')));
        }

        AuditLog::log('update_interview_automation_settings', 'settings', 0, null, [], (int)$user['id']);

        flash('success', 'Interview & Telegram automation settings updated successfully.');
        redirect('/admin/interview-candidates?tab=settings');
    }
}
