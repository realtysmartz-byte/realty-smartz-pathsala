<?php
namespace App\Helpers;

use App\Config\Database;
use App\Models\Profile;
use App\Models\Setting;
use PDO;
use Throwable;

/**
 * Realty Smartz HRMS Synchronization Service
 * 
 * Automatically sends newly onboarded candidate details to the Realty Smartz HRMS Portal
 * (https://rshrms.in/api/integrations/onboard-candidate) so official employee accounts,
 * codes (RS-xxxx), and portal permissions are provisioned in real time.
 */
class HrmsService {

    public const DEFAULT_API_URL = 'https://rshrms.in/api/integrations/onboard-candidate';
    public const DEFAULT_API_KEY = 'rs_onboarding_secret_2026';

    /**
     * Get configured HRMS Endpoint URL
     */
    public static function getApiUrl(): string {
        $url = trim((string) Setting::get('hrms_api_url', ''));
        if (empty($url) && function_exists('config')) {
            $url = trim((string) config('HRMS_API_URL', ''));
        }
        return !empty($url) ? $url : self::DEFAULT_API_URL;
    }

    /**
     * Get configured HRMS Secret Key
     */
    public static function getApiKey(): string {
        $key = trim((string) Setting::get('hrms_api_key', ''));
        if (empty($key) && function_exists('config')) {
            $key = trim((string) config('HRMS_API_KEY', ''));
        }
        return !empty($key) ? $key : self::DEFAULT_API_KEY;
    }

    /**
     * Synchronize an onboarded candidate to Realty Smartz HRMS
     *
     * @param int $userId Candidate User ID
     * @return array ['success' => bool, 'employee' => array|null, 'error' => string|null, 'conflict' => bool]
     */
    public static function syncCandidate(int $userId): array {
        $db = Database::getConnection();

        // 1. Fetch complete candidate profile data
        $candidate = Profile::getFullProfile($userId);
        if (empty($candidate)) {
            return ['success' => false, 'error' => "Candidate ID #{$userId} not found."];
        }

        $profile   = $candidate['profile'] ?? [];
        $emergency = $candidate['emergency'] ?? [];

        // 2. Parse First Name & Last Name
        $rawName = trim((string)($candidate['full_name'] ?? ''));
        $nameParts = preg_split('/\s+/', $rawName);
        $firstName = $nameParts[0] ?? 'Candidate';
        $lastName  = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

        // If lastName is blank, attempt father's surname or provide fallback
        if ($lastName === '') {
            $fatherName = trim((string)($profile['father_name'] ?? ''));
            $fatherParts = preg_split('/\s+/', $fatherName);
            $lastName = count($fatherParts) > 1 ? end($fatherParts) : '-';
        }

        // 3. Email & Phone
        $email = strtolower(trim((string)($candidate['email'] ?? '')));
        $rawMobile = $candidate['mobile'] ?? '';
        $phoneDigits = preg_replace('/\D/', '', (string)$rawMobile);
        $phone = (strlen($phoneDigits) >= 10) ? substr($phoneDigits, -10) : ($phoneDigits ?: null);

        // 4. Department & Designation Lookup (from linked interview record if available)
        $department  = 'Sales';
        $designation = 'Sales Executive';
        $joiningDate = null;

        try {
            $stmtInt = $db->prepare("SELECT `post_applied`, `position_offered`, `applied_segment`, `joining_date` 
                FROM `interview_applications` 
                WHERE (`email` = :em AND `email` != '') OR (`mobile` = :mob AND `mobile` != '')
                ORDER BY `id` DESC LIMIT 1");
            $stmtInt->execute([':em' => $email, ':mob' => $phone ?? '']);
            $interview = $stmtInt->fetch(PDO::FETCH_ASSOC);

            if ($interview) {
                if (!empty($interview['position_offered'])) {
                    $designation = trim($interview['position_offered']);
                } elseif (!empty($interview['post_applied'])) {
                    $designation = trim($interview['post_applied']);
                }

                if (!empty($interview['applied_segment'])) {
                    $department = 'Sales (' . trim($interview['applied_segment']) . ')';
                }

                if (!empty($interview['joining_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $interview['joining_date'])) {
                    $joiningDate = $interview['joining_date'];
                }
            }
        } catch (Throwable $e) {
            error_log("HRMS department lookup error: " . $e->getMessage());
        }

        // Check experience designation if still default
        if ($designation === 'Sales Executive' && !empty($candidate['experience'])) {
            foreach ($candidate['experience'] as $exp) {
                if (!empty($exp['designation'])) {
                    $designation = trim($exp['designation']);
                    break;
                }
            }
        }

        // Date of joining fallback
        if (empty($joiningDate)) {
            if (!empty($profile['joining_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $profile['joining_date'])) {
                $joiningDate = $profile['joining_date'];
            } else {
                $joiningDate = date('Y-m-d');
            }
        }

        // 5. Date of Birth & Gender
        $dob = (!empty($profile['dob']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $profile['dob'])) ? $profile['dob'] : null;

        $rawGender = strtolower(trim((string)($profile['gender'] ?? '')));
        $gender = match($rawGender) {
            'male'   => 'Male',
            'female' => 'Female',
            'other'  => 'Other',
            default  => null
        };

        // 6. Blood Group
        $bloodGroup = !empty($profile['blood_group']) ? trim((string)$profile['blood_group']) : null;

        // 7. Full Formatted Address
        $addressParts = array_filter([
            $profile['address'] ?? ($profile['current_address'] ?? ($profile['permanent_address'] ?? '')),
            $profile['city'] ?? '',
            $profile['state'] ?? '',
            $profile['pincode'] ?? ''
        ]);
        $address = !empty($addressParts) ? implode(', ', $addressParts) : null;

        // 8. Emergency Contact
        $emergencyContactName  = !empty($emergency['contact_name']) ? trim((string)$emergency['contact_name']) : null;
        $rawEmgPhone           = $emergency['mobile'] ?? ($emergency['alternate_mobile'] ?? '');
        $emgPhoneDigits        = preg_replace('/\D/', '', (string)$rawEmgPhone);
        $emergencyContactPhone = (strlen($emgPhoneDigits) >= 10) ? substr($emgPhoneDigits, -10) : ($emgPhoneDigits ?: null);

        // 9. Profile Photo URL
        $profilePhotoUrl = null;
        if (!empty($candidate['docs_by_type']['profile_photo']['id'])) {
            $docId = (int) $candidate['docs_by_type']['profile_photo']['id'];
            $profilePhotoUrl = url('/document/download/' . $docId);
        } elseif (!empty($profile['photo_path'])) {
            $profilePhotoUrl = url($profile['photo_path']);
        }

        // 10. Construct HRMS Request Payload
        $payload = [
            'firstName'             => $firstName,
            'lastName'              => $lastName,
            'email'                 => $email,
            'phone'                 => $phone,
            'department'            => $department,
            'designation'           => $designation,
            'dateOfJoining'         => $joiningDate,
            'dateOfBirth'           => $dob,
            'gender'                => $gender,
            'bloodGroup'            => $bloodGroup,
            'address'               => $address,
            'emergencyContactName'  => $emergencyContactName,
            'emergencyContactPhone' => $emergencyContactPhone,
            'password'              => null, // HRMS auto-generates secure initial password
            'profilePhotoUrl'       => $profilePhotoUrl,
        ];

        // 11. Send HTTP POST to HRMS API
        $endpoint = self::getApiUrl();
        $apiKey   = self::getApiKey();

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-hrms-api-key: ' . $apiKey,
                'User-Agent: RealtySmartzPathshala/2.0'
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => true
        ]);

        $responseBody = curl_exec($ch);
        $httpCode     = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError    = curl_error($ch);
        curl_close($ch);

        $resData = is_string($responseBody) ? json_decode($responseBody, true) : null;

        // 12. Evaluate API Response
        // Case A: Success (200 OK)
        if ($httpCode === 200 && !empty($resData['success'])) {
            $employee     = $resData['employee'] ?? [];
            $employeeCode = $employee['employeeCode'] ?? ($employee['code'] ?? null);
            $employeeId   = $employee['id'] ?? null;

            try {
                $stmtUpd = $db->prepare("UPDATE `profiles` SET 
                    `hrms_employee_code` = :code,
                    `hrms_employee_id`   = :empid,
                    `hrms_synced_at`     = NOW(),
                    `hrms_sync_status`   = 'synced',
                    `hrms_sync_error`    = NULL
                    WHERE `user_id` = :uid");
                $stmtUpd->execute([
                    ':code'  => $employeeCode,
                    ':empid' => $employeeId,
                    ':uid'   => $userId
                ]);
            } catch (Throwable $e) {
                error_log("Failed updating candidate profiles table with HRMS employee code: " . $e->getMessage());
            }

            self::logSync($userId, $endpoint, $payload, $httpCode, $responseBody, 'success', $employeeCode);

            return [
                'success'       => true,
                'employee'      => $employee,
                'employee_code' => $employeeCode,
                'message'       => $resData['message'] ?? 'Candidate onboarded successfully into Realty Smartz HRMS'
            ];
        }

        // Case B: 409 Conflict (Candidate email already registered in HRMS)
        if ($httpCode === 409) {
            $errorMsg = $resData['error'] ?? 'An employee with this email already exists in HRMS';

            try {
                $db->prepare("UPDATE `profiles` SET 
                    `hrms_sync_status` = 'conflict',
                    `hrms_sync_error`  = :err,
                    `hrms_synced_at`   = NOW()
                    WHERE `user_id` = :uid")->execute([
                    ':err' => $errorMsg,
                    ':uid' => $userId
                ]);
            } catch (Throwable $e) {}

            self::logSync($userId, $endpoint, $payload, $httpCode, $responseBody, 'conflict', null, $errorMsg);

            return [
                'success'  => false,
                'conflict' => true,
                'error'    => $errorMsg
            ];
        }

        // Case C: Error or Endpoint Pending
        $errorMsg = !empty($resData['error']) 
            ? $resData['error'] 
            : ("HRMS API error (HTTP " . ($httpCode ?: 'Connection Failed') . ")" . ($curlError ? ": {$curlError}" : ''));

        try {
            $db->prepare("UPDATE `profiles` SET 
                `hrms_sync_status` = 'failed',
                `hrms_sync_error`  = :err
                WHERE `user_id` = :uid")->execute([
                ':err' => substr($errorMsg, 0, 500),
                ':uid' => $userId
            ]);
        } catch (Throwable $e) {}

        self::logSync($userId, $endpoint, $payload, $httpCode, $responseBody ?: $curlError, 'failed', null, $errorMsg);

        return [
            'success' => false,
            'error'   => $errorMsg
        ];
    }

    /**
     * Record sync audit in `hrms_sync_log`
     */
    private static function logSync(
        int $userId, 
        string $endpoint, 
        array $payload, 
        int $httpCode, 
        ?string $responseBody, 
        string $status, 
        ?string $employeeCode = null, 
        ?string $errorMessage = null
    ): void {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO `hrms_sync_log` 
                (`user_id`, `endpoint`, `request_payload`, `response_code`, `response_body`, `status`, `employee_code`, `error_message`, `created_at`) 
                VALUES (:uid, :ep, :req, :code, :resp, :st, :empcode, :err, NOW())");
            $stmt->execute([
                ':uid'     => $userId,
                ':ep'      => $endpoint,
                ':req'     => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ':code'    => $httpCode,
                ':resp'    => $responseBody ? substr($responseBody, 0, 1500) : null,
                ':st'      => $status,
                ':empcode' => $employeeCode,
                ':err'     => $errorMessage ? substr($errorMessage, 0, 255) : null
            ]);
        } catch (Throwable $e) {
            error_log("Failed writing to hrms_sync_log: " . $e->getMessage());
        }
    }
}
