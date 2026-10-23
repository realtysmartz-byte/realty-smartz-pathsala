<?php
namespace App\Helpers;

use App\Config\Database;
use App\Models\User;
use App\Models\Profile;
use App\Models\Setting;
use Exception;
use PDO;

class GoogleSheetsService {

    public const HEADERS = [
        'Candidate ID',
        'Candidate Full Name',
        'Aadhaar Number',
        'Contact Number',
        'Gmail Address',
        'Date of Birth',
        'Gender',
        'Full Address',
        'Emergency Contact Person Name',
        'Emergency Contact Number',
        'Relationship with Emergency Contact',
        'Aptitude Marks',
        'Total Marks',
        'Test Status',
        'Onboarding Status',
        'Registration Date',
        'Test Completion Date'
    ];

    public const INTERVIEW_HEADERS = [
        'Application Ref',
        'Submitted At',
        'HR Name (Selected)',
        'Post Applied For',
        'Applied For Segment (Commercial / Residential)',
        'Candidate Full Name',
        'Contact Number',
        'Email Address',
        'Experience Type',
        'Years in Real Estate',
        'Vehicle Available',
        'Current Location',
        'Higher Education',
        'Any References',
        'Reference Details',
        'Last Company HR and TL',
        'Reason to Leave',
        'Immediate Joiner',
        'Employment History',
        'Decision',
        'Candidate Interested',
        'Position Offered',
        'Offered Salary',
        'Date of Joining',
        'Rejection Reason',
        'Source By',
        'Feedback by TL/SM',
        'Feedback by HR',
        'Feedback Final',
        'Module Discussed',
        'Last Updated At',
        'Last Updated By'
    ];

    /**
     * Synchronize a candidate's record with Google Sheets
     *
     * @param int $userId Candidate User ID
     * @return array Status array ['success' => bool, 'message' => string, 'details' => mixed]
     */
    public static function syncCandidate(int $userId): array {
        try {
            $data = self::buildCandidateRow($userId);
            if (empty($data)) {
                return [
                    'success' => false,
                    'message' => "Candidate ID {$userId} not found in database."
                ];
            }

            return self::upsertRow($data);
        } catch (\Throwable $e) {
            self::log("Error syncing candidate {$userId}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Sync error: " . $e->getMessage()
            ];
        }
    }

    /**
     * Non-blocking asynchronous sync for candidates
     */
    public static function syncCandidateAsync(int $userId): void {
        self::dispatchAsync('candidate', $userId);
    }

    /**
     * Non-blocking asynchronous sync for interview applications
     */
    public static function syncInterviewApplicationAsync(int $applicationId): void {
        self::dispatchAsync('interview', $applicationId);
    }

    /**
     * Dispatch background sync process across different hosting environments
     */
    private static function dispatchAsync(string $type, int $id): void {
        // On cPanel with PHP-FPM, flush response immediately to user then continue in background
        if (function_exists('fastcgi_finish_request')) {
            @fastcgi_finish_request();
            if ($type === 'interview') {
                self::syncInterviewApplication($id);
            } else {
                self::syncCandidate($id);
            }
            return;
        }

        $binScript = ROOT_PATH . '/bin/sync_sheets.php';
        if (file_exists($binScript) && stripos(PHP_OS, 'WIN') === 0) {
            $phpBinary = PHP_BINARY;
            if (!file_exists($phpBinary) || str_ends_with(strtolower($phpBinary), 'php-cgi.exe') || str_ends_with(strtolower($phpBinary), 'httpd.exe')) {
                if (file_exists('C:\\xampp\\php\\php.exe')) {
                    $phpBinary = 'C:\\xampp\\php\\php.exe';
                } else {
                    $phpBinary = 'php';
                }
            }

            $escapedPhp = escapeshellarg($phpBinary);
            $escapedScript = escapeshellarg($binScript);
            $escapedType = escapeshellarg($type);
            $id = (int)$id;

            try {
                $cmd = "{$escapedPhp} {$escapedScript} --type={$escapedType} --id={$id}";
                $descriptors = [
                    0 => ['pipe', 'r'],
                    1 => ['file', 'NUL', 'w'],
                    2 => ['file', 'NUL', 'w']
                ];
                $proc = proc_open($cmd, $descriptors, $pipes, ROOT_PATH, null, ['bypass_shell' => false]);
                if (is_resource($proc)) {
                    fclose($pipes[0]);
                    return;
                }
            } catch (\Throwable $t) {
                // proceed to direct fallback
            }
        }

        // Direct sync fallback (guarantees sync works on all hosting environments)
        if ($type === 'interview') {
            self::syncInterviewApplication($id);
        } else {
            self::syncCandidate($id);
        }
    }

    /**
     * Gather and format the 17-column candidate data record
     *
     * @param int $userId
     * @return array|null 17-element array or null if user not found
     */
    public static function buildCandidateRow(int $userId): ?array {
        $db = Database::getConnection();

        // 1. Core User
        $stmtUser = $db->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
        $stmtUser->execute([':id' => $userId]);
        $user = $stmtUser->fetch();
        if (!$user) {
            return null;
        }

        // 2. Profile
        $stmtProfile = $db->prepare("SELECT * FROM `profiles` WHERE `user_id` = :uid LIMIT 1");
        $stmtProfile->execute([':uid' => $userId]);
        $profile = $stmtProfile->fetch() ?: [];

        // 3. Emergency Contact
        $stmtEmg = $db->prepare("SELECT * FROM `emergency_contacts` WHERE `user_id` = :uid LIMIT 1");
        $stmtEmg->execute([':uid' => $userId]);
        $emergency = $stmtEmg->fetch() ?: [];

        // 4. Latest Aptitude Test Attempt
        $stmtAttempt = $db->prepare("SELECT * FROM `test_attempts` WHERE `user_id` = :uid ORDER BY `id` DESC LIMIT 1");
        $stmtAttempt->execute([':uid' => $userId]);
        $attempt = $stmtAttempt->fetch();

        // Format Candidate ID
        $prefix = ($user['candidate_type'] ?? '') === 'aptitude_only' ? 'TCAND-' : 'CAND-';
        $candidateId = $prefix . str_pad((string)$userId, 5, '0', STR_PAD_LEFT);

        // Format Aadhaar: Treat as sensitive data; mask as XXXX XXXX 1234
        $aadhaarMasked = '';
        if (!empty($profile['aadhaar_last4'])) {
            $aadhaarMasked = 'XXXX XXXX ' . $profile['aadhaar_last4'];
        } elseif (!empty($profile['aadhaar_enc']) || !empty($profile['aadhaar_encrypted'])) {
            $rawEnc = $profile['aadhaar_enc'] ?? $profile['aadhaar_encrypted'];
            $decrypted = CryptoHelper::decrypt($rawEnc);
            $aadhaarMasked = CryptoHelper::maskAadhaar($decrypted);
        }

        // Format Full Address
        $addressParts = array_filter([
            $profile['current_address'] ?? ($profile['address'] ?? ''),
            $profile['city'] ?? '',
            $profile['state'] ?? '',
            $profile['pincode'] ?? ''
        ]);
        $fullAddress = implode(', ', $addressParts);

        // Format Test Marks and Status
        $aptitudeMarks = 'Not Attempted';
        $totalMarks = '15';
        $testStatus = 'Not Attempted';
        $testCompletionDate = '';

        if ($attempt) {
            $totalMarks = (string)($attempt['total_questions'] ?? 15);
            if ($attempt['status'] === 'completed') {
                $aptitudeMarks = (string)round((float)$attempt['score'], 2);
                $passed = !empty($attempt['passed']);
                $testStatus = $passed ? 'Completed - Passed' : 'Completed - Below Benchmark';
                $testCompletionDate = !empty($attempt['completed_at']) ? date('d-m-Y H:i', strtotime($attempt['completed_at'])) : '';
            } elseif ($attempt['status'] === 'timed_out') {
                $aptitudeMarks = (string)round((float)$attempt['score'], 2);
                $testStatus = 'Timed Out';
                $testCompletionDate = !empty($attempt['completed_at']) ? date('d-m-Y H:i', strtotime($attempt['completed_at'])) : '';
            } elseif ($attempt['status'] === 'in_progress') {
                $aptitudeMarks = 'In Progress';
                $testStatus = 'In Progress';
            }
        }

        // Format Onboarding Status
        $rawStatus = $user['status'] ?? 'registered';
        $onboardingStatusMap = [
            'registered'          => 'Registered',
            'email_verified'      => 'Email Verified',
            'profile_completed'   => 'Profile Completed',
            'documents_submitted' => 'Documents Submitted',
            'test_pending'        => 'Test Pending',
            'test_completed'      => 'Test Completed',
            'under_review'        => 'Under Review',
            'approved'            => 'Approved',
            'changes_requested'   => 'Changes Requested',
            'rejected'            => 'Rejected'
        ];
        if (($user['candidate_type'] ?? '') === 'aptitude_only') {
            $onboardingStatus = 'Aptitude Test Only (' . ($onboardingStatusMap[$rawStatus] ?? ucfirst($rawStatus)) . ')';
        } else {
            $onboardingStatus = $onboardingStatusMap[$rawStatus] ?? ucfirst($rawStatus);
        }

        // 17-column structured row
        return [
            $candidateId,                                                                      // 1. Candidate ID
            $user['full_name'] ?? '',                                                          // 2. Candidate Full Name
            $aadhaarMasked,                                                                    // 3. Aadhaar Number (Masked)
            $user['mobile'] ?? '',                                                             // 4. Contact Number
            $user['email'] ?? '',                                                              // 5. Gmail Address
            !empty($profile['dob']) ? date('d-m-Y', strtotime($profile['dob'])) : '',          // 6. Date of Birth
            ucfirst($profile['gender'] ?? ''),                                                 // 7. Gender
            $fullAddress,                                                                      // 8. Full Address
            $emergency['contact_name'] ?? '',                                                  // 9. Emergency Contact Person Name
            $emergency['mobile'] ?? '',                                                        // 10. Emergency Contact Number
            $emergency['relationship'] ?? '',                                                  // 11. Relationship with Emergency Contact
            $aptitudeMarks,                                                                    // 12. Aptitude Marks
            $totalMarks,                                                                       // 13. Total Marks
            $testStatus,                                                                       // 14. Test Status
            $onboardingStatus,                                                                 // 15. Onboarding Status
            !empty($user['created_at']) ? date('d-m-Y H:i', strtotime($user['created_at'])) : '', // 16. Registration Date
            $testCompletionDate                                                                // 17. Test Completion Date
        ];
    }

    /**
     * Synchronize an interview application's record with Google Sheets
     *
     * @param int $applicationId Interview Application ID
     * @return array Status array ['success' => bool, 'message' => string]
     */
    public static function syncInterviewApplication(int $applicationId): array {
        try {
            $data = self::buildInterviewCandidateRow($applicationId);
            if (empty($data)) {
                return [
                    'success' => false,
                    'message' => "Interview Application ID {$applicationId} not found in database."
                ];
            }

            $tabName = config('GOOGLE_SHEETS_INTERVIEW_TAB') ?: Setting::get('google_sheets_interview_tab', 'Interview Candidate');
            return self::upsertRow($data, $tabName, self::INTERVIEW_HEADERS);
        } catch (\Throwable $e) {
            self::log("Error syncing interview application {$applicationId}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Sync error: " . $e->getMessage()
            ];
        }
    }

    /**
     * Gather and format the 31-column interview candidate data record
     *
     * @param int $applicationId
     * @return array|null
     */
    public static function buildInterviewCandidateRow(int $applicationId): ?array {
        $app = \App\Models\InterviewApplication::findById($applicationId);
        if (!$app) return null;

        $refId = 'INT-' . str_pad((string)$app['id'], 5, '0', STR_PAD_LEFT);

        // Summarize employment history
        $empSummary = '';
        if (!empty($app['employment_rows'])) {
            $parts = [];
            foreach ($app['employment_rows'] as $emp) {
                $org = $emp['organisation'] ?? '';
                $desig = $emp['designation'] ?? '';
                $sal = $emp['salary'] ?? '';
                $dur = $emp['duration'] ?? '';
                $hr = $emp['hr_name_number'] ?? '';
                $parts[] = "{$org} ({$desig}, Sal: {$sal}, Dur: {$dur}, HR: {$hr})";
            }
            $empSummary = implode(" | ", $parts);
        }

        $formattedSalary = !empty($app['offered_salary']) ? '₹ ' . number_format((float)$app['offered_salary'], 0) : '';

        return [
            $refId,                                                                                 // 1. Application Ref
            !empty($app['submitted_at']) ? date('d-m-Y H:i', strtotime($app['submitted_at'])) : '', // 2. Submitted At
            $app['hr_name'] ?? '',                                                                  // 3. HR Name (Selected)
            $app['post_applied'] ?? '',                                                             // 4. Post Applied For
            ucfirst($app['applied_for_type'] ?? 'Commercial'),                                      // 5. Applied For Segment
            $app['full_name'] ?? '',                                                                // 6. Candidate Full Name
            $app['mobile'] ?? '',                                                                   // 7. Contact Number
            $app['email'] ?? '',                                                                    // 7. Email Address
            ucfirst($app['experience_type'] ?? ''),                                                 // 8. Experience Type
            ($app['experience_type'] === 'experience') ? (($app['years_in_real_estate'] !== null) ? $app['years_in_real_estate'] . ' Yrs' : '0 Yrs') : 'Fresher', // 9. Years in RE
            ucfirst($app['vehicle_available'] ?? ''),                                               // 10. Vehicle Available
            $app['current_location'] ?? '',                                                         // 11. Current Location
            $app['higher_education'] ?? '',                                                         // 12. Higher Education
            ucfirst($app['has_references'] ?? ''),                                                  // 13. Any References
            $app['reference_details'] ?? '',                                                        // 14. Reference Details
            $app['last_company_hr_tl'] ?? '',                                                       // 15. Last Company HR/TL
            $app['reason_to_leave'] ?? '',                                                          // 16. Reason to Leave
            ucfirst($app['immediate_joiner'] ?? ''),                                                // 17. Immediate Joiner
            $empSummary,                                                                            // 18. Employment History
            ucfirst($app['decision'] ?? 'Pending'),                                                 // 19. Decision
            ucfirst($app['candidate_interested'] ?? ''),                                            // 20. Candidate Interested
            $app['position_offered'] ?? '',                                                         // 21. Position Offered
            $formattedSalary,                                                                       // 22. Offered Salary
            !empty($app['joining_date']) ? date('d-m-Y', strtotime($app['joining_date'])) : '',     // 23. Date of Joining
            $app['rejection_reason'] ?? '',                                                         // 24. Rejection Reason
            $app['source_by'] ?? '',                                                                // 25. Source By
            $app['feedback_tl_sm'] ?? '',                                                           // 26. Feedback by TL/SM
            $app['feedback_hr'] ?? '',                                                              // 27. Feedback by HR
            $app['feedback_final'] ?? '',                                                           // 28. Feedback Final
            $app['module_discussed'] ?? '',                                                         // 29. Module Discussed
            !empty($app['updated_at']) ? date('d-m-Y H:i', strtotime($app['updated_at'])) : '',    // 30. Last Updated At
            $app['updated_by_name'] ?? ($app['updated_by'] ? 'Admin #' . $app['updated_by'] : '')  // 31. Last Updated By
        ];
    }

    /**
     * Upsert candidate / interview record in Google Sheets without creating duplicates
     *
     * @param array $row Row data
     * @param string|null $sheetName Tab name (default 'Candidates')
     * @param array|null $headers Header row definition
     * @return array
     */
    public static function upsertRow(array $row, ?string $sheetName = null, ?array $headers = null): array {
        $candidateId = $row[0] ?? '';
        if (empty($candidateId)) {
            return ['success' => false, 'message' => 'Record ID missing.'];
        }

        $config = self::getCredentialsConfig();
        $targetSheet = !empty($sheetName) ? $sheetName : ($config['sheet_name'] ?? 'Candidates');
        $targetHeaders = !empty($headers) ? $headers : self::HEADERS;

        // 1. Try Webhook URL if configured
        if (!empty($config['webhook_url'])) {
            return self::syncViaWebhook($config['webhook_url'], $candidateId, $row, $targetSheet, $targetHeaders);
        }

        // 2. Try Google Sheets REST API v4 with Service Account
        if (!empty($config['spreadsheet_id']) && !empty($config['service_account'])) {
            return self::syncViaGoogleApi($config, $candidateId, $row, $targetSheet, $targetHeaders);
        }

        // 3. Neither configured yet -> Log gracefully, do not fail
        self::log("Google Sheets sync skipped: credentials not yet configured. Record for '{$candidateId}' saved in DB.");
        return [
            'success'  => true,
            'skipped'  => true,
            'message'  => "Google Sheets credentials not yet configured. Record safely recorded in application database."
        ];
    }

    /**
     * Sync via Google Sheets API v4 using Service Account JWT
     */
    private static function syncViaGoogleApi(array $config, string $candidateId, array $row, string $sheetName, array $headers): array {
        $token = self::getAccessToken($config['service_account']);
        if (!$token) {
            throw new Exception("Unable to obtain Google OAuth2 access token.");
        }

        $spreadsheetId = $config['spreadsheet_id'];
        $colEnd = self::columnLetter(count($headers));

        // Step 1: Ensure tab and headers exist on the sheet
        self::ensureSheetAndHeadersExist($spreadsheetId, $sheetName, $headers, $token);

        // Step 2: Search Column A for existing ID
        $searchUrl = "https://sheets.googleapis.com/v4/spreadsheets/" . rawurlencode($spreadsheetId) . "/values/" . rawurlencode($sheetName) . "!A:A";
        $res = self::httpGet($searchUrl, $token);
        
        $rowIndex = null;
        if (!empty($res['values']) && is_array($res['values'])) {
            foreach ($res['values'] as $idx => $valRow) {
                if (isset($valRow[0]) && trim((string)$valRow[0]) === $candidateId) {
                    $rowIndex = $idx + 1; // 1-indexed for Sheets
                    break;
                }
            }
        }

        if ($rowIndex !== null && $rowIndex > 1) {
            // Update existing row
            $updateRange = "{$sheetName}!A{$rowIndex}:{$colEnd}{$rowIndex}";
            $updateUrl = "https://sheets.googleapis.com/v4/spreadsheets/" . rawurlencode($spreadsheetId) . "/values/" . rawurlencode($updateRange) . "?valueInputOption=USER_ENTERED";
            
            $body = [
                'range'  => $updateRange,
                'majorDimension' => 'ROWS',
                'values' => [$row]
            ];
            
            $putRes = self::httpPut($updateUrl, $body, $token);
            self::log("Updated existing row {$rowIndex} in Google Sheets '{$sheetName}' for {$candidateId}.");
            return [
                'success' => true,
                'action'  => 'updated',
                'row'     => $rowIndex,
                'message' => "Record updated in Google Sheets (Row {$rowIndex})."
            ];
        } else {
            // Append new row
            $appendUrl = "https://sheets.googleapis.com/v4/spreadsheets/" . rawurlencode($spreadsheetId) . "/values/" . rawurlencode($sheetName) . "!A:{$colEnd}:append?valueInputOption=USER_ENTERED&insertDataOption=INSERT_ROWS";
            
            $body = [
                'range'  => "{$sheetName}!A:{$colEnd}",
                'majorDimension' => 'ROWS',
                'values' => [$row]
            ];
            
            $postRes = self::httpPost($appendUrl, $body, $token);
            self::log("Appended new record to Google Sheets '{$sheetName}' for {$candidateId}.");
            return [
                'success' => true,
                'action'  => 'appended',
                'message' => "New record synchronized to Google Sheets."
            ];
        }
    }

    /**
     * Sync via Google Apps Script Webhook endpoint
     */
    private static function syncViaWebhook(string $url, string $candidateId, array $row, string $sheetName = 'Candidates', ?array $headers = null): array {
        $headers = !empty($headers) ? $headers : self::HEADERS;
        $payload = json_encode([
            'action'       => 'upsert',
            'tab'          => $sheetName,
            'sheet_name'   => $sheetName,
            'sheet'        => $sheetName,
            'type'         => ($sheetName === 'Interview Candidates' ? 'interview_candidate' : 'candidate'),
            'candidate_id' => $candidateId,
            'headers'      => $headers,
            'row'          => $row,
            'timestamp'    => date('Y-m-d H:i:s')
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            throw new Exception("Webhook curl error: " . $err);
        }

        // Detect Google Apps Script HTML error or missing doPost
        if (stripos($response, 'Script function not found') !== false || stripos($response, '<title>Error</title>') !== false || stripos($response, 'errorMessage') !== false) {
            $snippet = strip_tags(substr($response, 0, 300));
            throw new Exception("Google Apps Script error: doPost function is missing or not deployed. ({$snippet})");
        }

        $json = json_decode((string)$response, true);
        if ($json && isset($json['status']) && $json['status'] === 'error') {
            throw new Exception("Google Sheets Webhook error: " . ($json['message'] ?? 'Unknown script error'));
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            self::log("Successfully synchronized {$candidateId} to '{$sheetName}' via Webhook.");
            return [
                'success' => true,
                'action'  => 'webhook',
                'message' => "Synchronized to Google Sheets tab '{$sheetName}' via Webhook."
            ];
        }

        throw new Exception("Webhook failed with HTTP code {$httpCode}: {$response}");
    }

    /**
     * Ensure the tab exists in the spreadsheet and row 1 has the column headers
     */
    private static function ensureSheetAndHeadersExist(string $spreadsheetId, string $sheetName, array $headers, string $token): void {
        // 1. Verify if sheet tab exists
        $metaUrl = "https://sheets.googleapis.com/v4/spreadsheets/" . rawurlencode($spreadsheetId) . "?fields=sheets.properties";
        $meta = self::httpGet($metaUrl, $token);
        $sheetExists = false;
        if (!empty($meta['sheets'])) {
            foreach ($meta['sheets'] as $s) {
                if (($s['properties']['title'] ?? '') === $sheetName) {
                    $sheetExists = true;
                    break;
                }
            }
        }

        if (!$sheetExists) {
            // Add tab to spreadsheet
            $batchUrl = "https://sheets.googleapis.com/v4/spreadsheets/" . rawurlencode($spreadsheetId) . ":batchUpdate";
            self::httpPost($batchUrl, [
                'requests' => [
                    ['addSheet' => ['properties' => ['title' => $sheetName]]]
                ]
            ], $token);
            self::log("Created new sheet tab '{$sheetName}' in spreadsheet.");
        }

        // 2. Ensure headers row
        $colEnd = self::columnLetter(count($headers));
        $headersUrl = "https://sheets.googleapis.com/v4/spreadsheets/" . rawurlencode($spreadsheetId) . "/values/" . rawurlencode($sheetName) . "!A1:{$colEnd}1";
        $current = self::httpGet($headersUrl, $token);

        if (empty($current['values']) || empty($current['values'][0])) {
            $body = [
                'range'  => "{$sheetName}!A1:{$colEnd}1",
                'majorDimension' => 'ROWS',
                'values' => [$headers]
            ];
            $putUrl = "https://sheets.googleapis.com/v4/spreadsheets/" . rawurlencode($spreadsheetId) . "/values/" . rawurlencode($sheetName) . "!A1:{$colEnd}1?valueInputOption=USER_ENTERED";
            self::httpPut($putUrl, $body, $token);
            self::log("Initialized " . count($headers) . " column headers in Google Sheet '{$sheetName}'.");
        }
    }

    /**
     * Convert 1-based column index to letter (1=A, 26=Z, 27=AA, etc.)
     */
    public static function columnLetter(int $col): string {
        $letter = '';
        while ($col > 0) {
            $mod = ($col - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $col = (int)(($col - $mod) / 26);
        }
        return $letter ?: 'A';
    }

    /**
     * Get Google Service Account Access Token using RS256 JWT assertion
     */
    private static function getAccessToken(array $sa): ?string {
        $cacheFile = STORAGE_PATH . '/cache/google_sheets_token.json';
        if (file_exists($cacheFile)) {
            $cached = json_decode((string)file_get_contents($cacheFile), true);
            if (!empty($cached['access_token']) && !empty($cached['expires_at']) && $cached['expires_at'] > time() + 60) {
                return $cached['access_token'];
            }
        }

        $now = time();
        $jwtHeader = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $jwtClaim = json_encode([
            'iss'   => $sa['client_email'],
            'scope' => 'https://www.googleapis.com/auth/spreadsheets',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'exp'   => $now + 3600,
            'iat'   => $now
        ]);

        $b64Header = self::base64UrlEncode($jwtHeader);
        $b64Claim = self::base64UrlEncode($jwtClaim);
        $sigInput = $b64Header . '.' . $b64Claim;

        $privateKey = $sa['private_key'] ?? '';
        $binarySignature = '';
        if (!openssl_sign($sigInput, $binarySignature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new Exception("OpenSSL signing failed for Google Service Account private key.");
        }

        $jwt = $sigInput . '.' . self::base64UrlEncode($binarySignature);

        // Exchange JWT for OAuth2 access token
        $postData = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt
        ]);

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT        => 10
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $json = json_decode((string)$response, true);
        if ($httpCode === 200 && !empty($json['access_token'])) {
            @mkdir(dirname($cacheFile), 0755, true);
            file_put_contents($cacheFile, json_encode([
                'access_token' => $json['access_token'],
                'expires_at'   => $now + (int)($json['expires_in'] ?? 3600)
            ]));
            return $json['access_token'];
        }

        throw new Exception("Google OAuth2 token exchange error ({$httpCode}): " . ($json['error_description'] ?? $response));
    }

    /**
     * Retrieve credentials and configuration from environment and settings table
     */
    public static function getCredentialsConfig(): array {
        $spreadsheetId = config('GOOGLE_SHEETS_SPREADSHEET_ID') ?: Setting::get('google_sheets_spreadsheet_id', '');
        $webhookUrl = config('GOOGLE_SHEETS_WEBHOOK_URL') ?: Setting::get('google_sheets_webhook_url', '');
        $sheetName = config('GOOGLE_SHEETS_SHEET_NAME') ?: Setting::get('google_sheets_sheet_name', 'Candidates');

        $saJsonPath = config('GOOGLE_SHEETS_SERVICE_ACCOUNT_JSON') ?: Setting::get('google_sheets_service_account_path', '');
        if (empty($saJsonPath)) {
            $defaultPath = STORAGE_PATH . '/credentials/google-service-account.json';
            if (file_exists($defaultPath)) {
                $saJsonPath = $defaultPath;
            }
        } elseif (!str_starts_with($saJsonPath, '/') && !str_starts_with($saJsonPath, '\\') && !preg_match('/^[a-zA-Z]:/', $saJsonPath)) {
            $saJsonPath = ROOT_PATH . '/' . ltrim($saJsonPath, '/\\');
        }

        $serviceAccount = null;
        if (!empty($saJsonPath) && file_exists($saJsonPath)) {
            $content = file_get_contents($saJsonPath);
            $serviceAccount = json_decode((string)$content, true);
        }

        return [
            'spreadsheet_id'  => trim($spreadsheetId),
            'sheet_name'      => trim($sheetName) ?: 'Candidates',
            'webhook_url'     => trim($webhookUrl),
            'service_account' => $serviceAccount
        ];
    }

    private static function httpGet(string $url, string $token): array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["Authorization: Bearer {$token}"],
            CURLOPT_TIMEOUT        => 12
        ]);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode((string)$res, true) ?: [];
    }

    private static function httpPut(string $url, array $body, string $token): array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'PUT',
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer {$token}",
                "Content-Type: application/json"
            ],
            CURLOPT_TIMEOUT        => 12
        ]);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode((string)$res, true) ?: [];
    }

    private static function httpPost(string $url, array $body, string $token): array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer {$token}",
                "Content-Type: application/json"
            ],
            CURLOPT_TIMEOUT        => 12
        ]);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode((string)$res, true) ?: [];
    }

    private static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Dedicated logging for Google Sheets integration
     */
    public static function log(string $message): void {
        $logDir = STORAGE_PATH . '/logs';
        @mkdir($logDir, 0755, true);
        $file = $logDir . '/google_sheets.log';
        $entry = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        @file_put_contents($file, $entry, FILE_APPEND);
    }
}
