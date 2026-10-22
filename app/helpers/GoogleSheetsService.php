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
     * Upsert candidate record in Google Sheets without creating duplicates
     *
     * @param array $row 17-element array
     * @return array
     */
    public static function upsertRow(array $row): array {
        $candidateId = $row[0] ?? '';
        if (empty($candidateId)) {
            return ['success' => false, 'message' => 'Candidate ID missing.'];
        }

        $config = self::getCredentialsConfig();

        // 1. Try Webhook URL if configured
        if (!empty($config['webhook_url'])) {
            return self::syncViaWebhook($config['webhook_url'], $candidateId, $row);
        }

        // 2. Try Google Sheets REST API v4 with Service Account
        if (!empty($config['spreadsheet_id']) && !empty($config['service_account'])) {
            return self::syncViaGoogleApi($config, $candidateId, $row);
        }

        // 3. Neither configured yet -> Log gracefully, do not fail
        self::log("Google Sheets sync skipped: credentials not yet configured. Record for '{$candidateId}' saved in DB.");
        return [
            'success'  => true,
            'skipped'  => true,
            'message'  => "Google Sheets credentials not yet configured. Candidate data safely recorded in application database."
        ];
    }

    /**
     * Sync via Google Sheets API v4 using Service Account JWT
     */
    private static function syncViaGoogleApi(array $config, string $candidateId, array $row): array {
        $token = self::getAccessToken($config['service_account']);
        if (!$token) {
            throw new Exception("Unable to obtain Google OAuth2 access token.");
        }

        $spreadsheetId = $config['spreadsheet_id'];
        $sheetName = $config['sheet_name'] ?? 'Candidates';

        // Step 1: Ensure headers exist on the sheet
        self::ensureHeadersExist($spreadsheetId, $sheetName, $token);

        // Step 2: Search Column A for existing Candidate ID
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
            $updateRange = "{$sheetName}!A{$rowIndex}:Q{$rowIndex}";
            $updateUrl = "https://sheets.googleapis.com/v4/spreadsheets/" . rawurlencode($spreadsheetId) . "/values/" . rawurlencode($updateRange) . "?valueInputOption=USER_ENTERED";
            
            $body = [
                'range'  => $updateRange,
                'majorDimension' => 'ROWS',
                'values' => [$row]
            ];
            
            $putRes = self::httpPut($updateUrl, $body, $token);
            self::log("Updated existing candidate row {$rowIndex} in Google Sheets for {$candidateId}.");
            return [
                'success' => true,
                'action'  => 'updated',
                'row'     => $rowIndex,
                'message' => "Record updated in Google Sheets (Row {$rowIndex})."
            ];
        } else {
            // Append new row
            $appendUrl = "https://sheets.googleapis.com/v4/spreadsheets/" . rawurlencode($spreadsheetId) . "/values/" . rawurlencode($sheetName) . "!A:Q:append?valueInputOption=USER_ENTERED&insertDataOption=INSERT_ROWS";
            
            $body = [
                'range'  => "{$sheetName}!A:Q",
                'majorDimension' => 'ROWS',
                'values' => [$row]
            ];
            
            $postRes = self::httpPost($appendUrl, $body, $token);
            self::log("Appended new candidate record to Google Sheets for {$candidateId}.");
            return [
                'success' => true,
                'action'  => 'appended',
                'message' => "New candidate record synchronized to Google Sheets."
            ];
        }
    }

    /**
     * Sync via Google Apps Script Webhook endpoint
     */
    private static function syncViaWebhook(string $url, string $candidateId, array $row): array {
        $payload = json_encode([
            'action'       => 'upsert',
            'candidate_id' => $candidateId,
            'headers'      => self::HEADERS,
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
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            throw new Exception("Webhook curl error: " . $err);
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            self::log("Successfully synchronized {$candidateId} via Webhook.");
            return [
                'success' => true,
                'action'  => 'webhook',
                'message' => "Synchronized to Google Sheets via Webhook."
            ];
        }

        throw new Exception("Webhook failed with HTTP code {$httpCode}: {$response}");
    }

    /**
     * Ensure row 1 of the spreadsheet has all 17 column headers
     */
    private static function ensureHeadersExist(string $spreadsheetId, string $sheetName, string $token): void {
        $headersUrl = "https://sheets.googleapis.com/v4/spreadsheets/" . rawurlencode($spreadsheetId) . "/values/" . rawurlencode($sheetName) . "!A1:Q1";
        $current = self::httpGet($headersUrl, $token);

        if (empty($current['values']) || empty($current['values'][0])) {
            $body = [
                'range'  => "{$sheetName}!A1:Q1",
                'majorDimension' => 'ROWS',
                'values' => [self::HEADERS]
            ];
            $putUrl = "https://sheets.googleapis.com/v4/spreadsheets/" . rawurlencode($spreadsheetId) . "/values/" . rawurlencode($sheetName) . "!A1:Q1?valueInputOption=USER_ENTERED";
            self::httpPut($putUrl, $body, $token);
            self::log("Initialized 17 column headers in Google Sheet '{$sheetName}'.");
        }
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
