<?php
namespace App\Models;

use App\Config\Database;
use App\Helpers\Mailer;
use App\Models\AuditLog;
use App\Models\Setting;
use PDO;
use Exception;

class InterviewApplication {

    /**
     * Create a new interview application transactionally
     */
    public static function create(array $data, array $employmentRows = []): int {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $sql = "INSERT INTO `interview_applications` (
                `hr_name`, `post_applied`, `full_name`, `email`, `mobile`,
                `experience_type`, `years_in_real_estate`, `vehicle_available`,
                `current_location`, `higher_education`, `has_references`,
                `reference_details`, `last_company_hr_tl`, `reason_to_leave`,
                `immediate_joiner`, `consent_at`, `submitted_at`, `ip_address`,
                `user_agent`, `decision`, `version`, `is_archived`
            ) VALUES (
                :hr_name, :post_applied, :full_name, :email, :mobile,
                :experience_type, :years_in_real_estate, :vehicle_available,
                :current_location, :higher_education, :has_references,
                :reference_details, :last_company_hr_tl, :reason_to_leave,
                :immediate_joiner, NOW(), NOW(), :ip_address,
                :user_agent, 'pending', 1, 0
            )";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':hr_name'              => $data['hr_name'],
                ':post_applied'         => $data['post_applied'],
                ':full_name'            => $data['full_name'],
                ':email'                => strtolower($data['email']),
                ':mobile'               => $data['mobile'],
                ':experience_type'      => $data['experience_type'],
                ':years_in_real_estate' => ($data['experience_type'] === 'experience') ? ($data['years_in_real_estate'] ?? null) : null,
                ':vehicle_available'    => $data['vehicle_available'] ?? 'no',
                ':current_location'     => $data['current_location'],
                ':higher_education'     => $data['higher_education'],
                ':has_references'       => $data['has_references'] ?? 'no',
                ':reference_details'    => ($data['has_references'] === 'yes') ? ($data['reference_details'] ?? null) : null,
                ':last_company_hr_tl'   => ($data['experience_type'] === 'experience') ? ($data['last_company_hr_tl'] ?? null) : null,
                ':reason_to_leave'      => ($data['experience_type'] === 'experience') ? ($data['reason_to_leave'] ?? null) : null,
                ':immediate_joiner'     => $data['immediate_joiner'] ?? 'yes',
                ':ip_address'           => $data['ip_address'] ?? null,
                ':user_agent'           => $data['user_agent'] ?? null,
            ]);

            $applicationId = (int) $db->lastInsertId();

            // Insert employment rows if experience
            if ($data['experience_type'] === 'experience' && !empty($employmentRows)) {
                $sqlRow = "INSERT INTO `interview_employment_rows` (
                    `application_id`, `sort_order`, `organisation`, `designation`,
                    `salary`, `hr_name_number`, `duration`
                ) VALUES (
                    :aid, :sort_order, :organisation, :designation,
                    :salary, :hr_name_number, :duration
                )";
                $stmtRow = $db->prepare($sqlRow);

                $sortOrder = 1;
                foreach ($employmentRows as $row) {
                    if (empty(trim($row['organisation'] ?? '')) && empty(trim($row['designation'] ?? ''))) {
                        continue;
                    }
                    $stmtRow->execute([
                        ':aid'            => $applicationId,
                        ':sort_order'     => $sortOrder++,
                        ':organisation'   => trim($row['organisation'] ?? ''),
                        ':designation'    => trim($row['designation'] ?? ''),
                        ':salary'         => trim($row['salary'] ?? ''),
                        ':hr_name_number' => trim($row['hr_name_number'] ?? ''),
                        ':duration'       => trim($row['duration'] ?? '')
                    ]);
                    if ($sortOrder > 6) break;
                }
            }

            $db->commit();

            // Check & dispatch HR notification email
            try {
                self::sendAutomationEmails($applicationId, 'new_form', $data);
            } catch (\Throwable $e) {
                error_log("Interview HR notification email failed: " . $e->getMessage());
            }

            return $applicationId;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Find single interview application with employment rows, versions, and email logs
     */
    public static function findById(int $id): ?array {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT ia.*, 
            u_first.full_name as first_viewed_by_name,
            u_dec.full_name as decision_by_name,
            u_upd.full_name as updated_by_name
            FROM `interview_applications` ia
            LEFT JOIN `users` u_first ON ia.first_viewed_by = u_first.id
            LEFT JOIN `users` u_dec ON ia.decision_by = u_dec.id
            LEFT JOIN `users` u_upd ON ia.updated_by = u_upd.id
            WHERE ia.id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$app) return null;

        // Employment rows
        $stmtRows = $db->prepare("SELECT * FROM `interview_employment_rows` WHERE `application_id` = :aid ORDER BY `sort_order` ASC, `id` ASC");
        $stmtRows->execute([':aid' => $id]);
        $app['employment_rows'] = $stmtRows->fetchAll(PDO::FETCH_ASSOC);

        // Audit/Field versions
        $stmtVers = $db->prepare("SELECT ifv.*, u.full_name as admin_name 
            FROM `interview_field_versions` ifv
            LEFT JOIN `users` u ON ifv.updated_by = u.id
            WHERE ifv.application_id = :aid
            ORDER BY ifv.id DESC LIMIT 50");
        $stmtVers->execute([':aid' => $id]);
        $app['field_versions'] = $stmtVers->fetchAll(PDO::FETCH_ASSOC);

        // Email logs
        $stmtLogs = $db->prepare("SELECT * FROM `interview_email_log` WHERE `application_id` = :aid ORDER BY `id` DESC LIMIT 20");
        $stmtLogs->execute([':aid' => $id]);
        $app['email_logs'] = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

        return $app;
    }

    /**
     * Paginated list of interview applications
     */
    public static function list(array $filters, int $page = 1, int $perPage = 20): array {
        $db = Database::getConnection();

        $where = ["ia.is_archived = :archived"];
        $params = [':archived' => !empty($filters['archived']) ? 1 : 0];

        // Decision filter
        if (!empty($filters['decision']) && in_array($filters['decision'], ['pending', 'selected', 'rejected'], true)) {
            $where[] = "ia.decision = :decision";
            $params[':decision'] = $filters['decision'];
        }

        // Search filter
        if (!empty($filters['search'])) {
            $where[] = "(ia.full_name LIKE :search OR ia.mobile LIKE :search OR ia.email LIKE :search OR ia.post_applied LIKE :search)";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        // HR Name filter
        if (!empty($filters['hr_name'])) {
            $where[] = "ia.hr_name = :hr_name";
            $params[':hr_name'] = $filters['hr_name'];
        }

        // Experience type filter
        if (!empty($filters['experience_type']) && in_array($filters['experience_type'], ['experience', 'fresher'], true)) {
            $where[] = "ia.experience_type = :exp_type";
            $params[':exp_type'] = $filters['experience_type'];
        }

        // Immediate joiner
        if (!empty($filters['immediate_joiner']) && in_array($filters['immediate_joiner'], ['yes', 'no'], true)) {
            $where[] = "ia.immediate_joiner = :imm_join";
            $params[':imm_join'] = $filters['immediate_joiner'];
        }

        // Date range
        if (!empty($filters['date_range'])) {
            if ($filters['date_range'] === 'today') {
                $where[] = "DATE(ia.submitted_at) = CURDATE()";
            } elseif ($filters['date_range'] === '7days') {
                $where[] = "ia.submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            } elseif ($filters['date_range'] === '30days') {
                $where[] = "ia.submitted_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            }
        }

        // Feedback status filter
        if (!empty($filters['feedback_status'])) {
            if ($filters['feedback_status'] === 'pending') {
                $where[] = "(ia.feedback_hr IS NULL OR TRIM(ia.feedback_hr) = '') AND (ia.feedback_final IS NULL OR TRIM(ia.feedback_final) = '')";
            } elseif ($filters['feedback_status'] === 'filled') {
                $where[] = "((ia.feedback_hr IS NOT NULL AND TRIM(ia.feedback_hr) != '') OR (ia.feedback_final IS NOT NULL AND TRIM(ia.feedback_final) != ''))";
            }
        }

        $whereSql = implode(' AND ', $where);

        // Count total
        $countStmt = $db->prepare("SELECT COUNT(*) FROM `interview_applications` ia WHERE {$whereSql}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $totalPages = max(1, (int) ceil($total / $perPage));
        $offset = max(0, ($page - 1) * $perPage);

        // Select items
        $sql = "SELECT ia.id, ia.hr_name, ia.post_applied, ia.full_name, ia.email, ia.mobile,
                       ia.experience_type, ia.submitted_at, ia.decision, ia.offered_salary,
                       ia.joining_date, ia.feedback_hr, ia.feedback_final, ia.first_viewed_at,
                       ia.version, ia.is_archived
                FROM `interview_applications` ia
                WHERE {$whereSql}
                ORDER BY ia.submitted_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages
        ];
    }

    /**
     * Get live counts for filter chips
     */
    public static function getCountsByDecision(): array {
        $db = Database::getConnection();

        $row = $db->query("SELECT 
            COUNT(*) as total_all,
            SUM(CASE WHEN decision = 'pending' THEN 1 ELSE 0 END) as total_pending,
            SUM(CASE WHEN decision = 'selected' THEN 1 ELSE 0 END) as total_selected,
            SUM(CASE WHEN decision = 'rejected' THEN 1 ELSE 0 END) as total_rejected,
            SUM(CASE WHEN (feedback_hr IS NULL OR TRIM(feedback_hr) = '') AND (feedback_final IS NULL OR TRIM(feedback_final) = '') THEN 1 ELSE 0 END) as total_feedback_pending,
            SUM(CASE WHEN first_viewed_at IS NULL THEN 1 ELSE 0 END) as total_unopened
            FROM `interview_applications`
            WHERE is_archived = 0")->fetch(PDO::FETCH_ASSOC);

        return [
            'all'              => (int)($row['total_all'] ?? 0),
            'pending'          => (int)($row['total_pending'] ?? 0),
            'selected'         => (int)($row['total_selected'] ?? 0),
            'rejected'         => (int)($row['total_rejected'] ?? 0),
            'feedback_pending' => (int)($row['total_feedback_pending'] ?? 0),
            'unopened'         => (int)($row['total_unopened'] ?? 0)
        ];
    }

    /**
     * Get total unopened count for sidebar badge
     */
    public static function getUnopenedCount(): int {
        $db = Database::getConnection();
        return (int) $db->query("SELECT COUNT(*) FROM `interview_applications` WHERE first_viewed_at IS NULL AND is_archived = 0")->fetchColumn();
    }

    /**
     * Get updates since a specific ISO/SQL timestamp for live polling
     */
    public static function getUpdatesSince(string $sinceTimestamp): array {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT ia.id, ia.hr_name, ia.post_applied, ia.full_name, ia.email, ia.mobile,
                ia.experience_type, ia.submitted_at, ia.decision, ia.offered_salary,
                ia.joining_date, ia.feedback_hr, ia.feedback_final, ia.first_viewed_at,
                ia.version, ia.updated_at
            FROM `interview_applications` ia
            WHERE ia.is_archived = 0 
              AND (ia.updated_at > :since1 OR ia.submitted_at > :since2)
            ORDER BY ia.submitted_at DESC
            LIMIT 50");

        $stmt->execute([
            ':since1' => $sinceTimestamp,
            ':since2' => $sinceTimestamp
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $counts = self::getCountsByDecision();

        return [
            'rows'             => $rows,
            'counts'           => $counts,
            'unopened'         => $counts['unopened'],
            'server_timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Field-level autosave merge with concurrency control
     */
    public static function updateField(int $id, string $field, mixed $value, int $clientVersion, int $adminId): array {
        $db = Database::getConnection();

        $allowedFields = [
            'source_by', 'feedback_tl_sm', 'feedback_hr', 'feedback_final',
            'module_discussed', 'position_offered', 'decision',
            'candidate_interested', 'offered_salary', 'joining_date', 'rejection_reason'
        ];

        if (!in_array($field, $allowedFields, true)) {
            return ['success' => false, 'error' => "Invalid field update: {$field}"];
        }

        // Fetch current record
        $stmt = $db->prepare("SELECT ia.id, ia.version, ia.updated_by, ia.updated_at, ia.{$field},
                u.full_name as updated_by_name
            FROM `interview_applications` ia
            LEFT JOIN `users` u ON ia.updated_by = u.id
            WHERE ia.id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$current) {
            return ['success' => false, 'error' => 'Application record not found'];
        }

        $oldValue = $current[$field];

        // Format and clean values based on field type
        if ($field === 'offered_salary') {
            if ($value === '' || $value === null) {
                $value = null;
            } else {
                $cleanNum = preg_replace('/[^\d.]/', '', (string)$value);
                $value = is_numeric($cleanNum) ? max(0.00, round((float)$cleanNum, 2)) : null;
            }
        } elseif ($field === 'joining_date') {
            $value = (!empty($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($value))) ? trim($value) : null;
        } elseif ($field === 'candidate_interested') {
            $value = in_array(strtolower((string)$value), ['yes', 'no'], true) ? strtolower((string)$value) : null;
        } elseif ($field === 'decision') {
            $value = in_array(strtolower((string)$value), ['pending', 'selected', 'rejected'], true) ? strtolower((string)$value) : 'pending';
        } else {
            $value = is_string($value) ? trim($value) : (string)$value;
        }

        // Check if value didn't actually change
        if ((string)$oldValue === (string)$value) {
            return [
                'success'           => true,
                'no_change'         => true,
                'version'           => (int)$current['version'],
                'last_updated_text' => 'Saved'
            ];
        }

        // Concurrency check: If client's version is older than database version
        if ($clientVersion > 0 && $clientVersion < (int)$current['version']) {
            // Check if this specific field was modified since client's version
            return [
                'conflict'        => true,
                'updated_by_name' => $current['updated_by_name'] ?: 'another administrator',
                'server_value'    => $oldValue,
                'version'         => (int)$current['version']
            ];
        }

        $newVersion = (int)$current['version'] + 1;

        // Perform atomic update
        $db->beginTransaction();
        try {
            $extraSet = "";
            $extraParams = [];

            if ($field === 'decision') {
                $extraSet = ", `decision_at` = NOW(), `decision_by` = :aid_dec";
                $extraParams[':aid_dec'] = $adminId;
            }

            $updSql = "UPDATE `interview_applications` SET 
                `{$field}` = :val,
                `version` = :new_ver,
                `updated_by` = :aid,
                `updated_at` = NOW()
                {$extraSet}
                WHERE `id` = :id";

            $stmtUpd = $db->prepare($updSql);
            $stmtUpd->bindValue(':val', $value);
            $stmtUpd->bindValue(':new_ver', $newVersion, PDO::PARAM_INT);
            $stmtUpd->bindValue(':aid', $adminId, PDO::PARAM_INT);
            $stmtUpd->bindValue(':id', $id, PDO::PARAM_INT);
            foreach ($extraParams as $pk => $pv) {
                $stmtUpd->bindValue($pk, $pv);
            }
            $stmtUpd->execute();

            // Record in interview_field_versions table
            $stmtVer = $db->prepare("INSERT INTO `interview_field_versions` 
                (`application_id`, `field`, `value`, `updated_at`, `updated_by`)
                VALUES (:aid, :field, :val, NOW(), :admin_id)");
            $stmtVer->execute([
                ':aid'      => $id,
                ':field'    => $field,
                ':val'      => (string)$value,
                ':admin_id' => $adminId
            ]);

            // Audit Log
            AuditLog::log('interview_field_update', 'interview_applications', $id, $oldValue, [
                'field'     => $field,
                'new_value' => $value,
                'version'   => $newVersion
            ], $adminId);

            $db->commit();

            // Check automation emails if decision changed
            if ($field === 'decision') {
                $fullApp = self::findById($id);
                if ($value === 'selected') {
                    self::sendAutomationEmails($id, 'selected', $fullApp);
                } elseif ($value === 'rejected') {
                    self::sendAutomationEmails($id, 'rejected', $fullApp);
                }
            }

            // Get admin name
            $adminUser = User::findById($adminId);
            $adminName = $adminUser['full_name'] ?? 'Admin';
            $lastUpdatedText = "Last updated by " . e($adminName) . " on " . date('d M Y, h:i A');

            return [
                'success'           => true,
                'version'           => $newVersion,
                'last_updated_text' => $lastUpdatedText
            ];

        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Mark an application as viewed (clears "New" state)
     */
    public static function markViewed(int $id, int $adminId): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE `interview_applications` 
            SET `first_viewed_at` = NOW(), `first_viewed_by` = :aid 
            WHERE `id` = :id AND `first_viewed_at` IS NULL");
        $stmt->execute([':id' => $id, ':aid' => $adminId]);
    }

    /**
     * Soft-delete application (archive)
     */
    public static function archive(int $id, int $adminId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE `interview_applications` 
            SET `is_archived` = 1, `archived_at` = NOW(), `updated_by` = :aid 
            WHERE `id` = :id");
        $res = $stmt->execute([':id' => $id, ':aid' => $adminId]);

        AuditLog::log('interview_application_archived', 'interview_applications', $id, null, [], $adminId);
        return $res;
    }

    /**
     * Restore soft-deleted application
     */
    public static function restore(int $id, int $adminId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE `interview_applications` 
            SET `is_archived` = 0, `archived_at` = NULL, `updated_by` = :aid 
            WHERE `id` = :id");
        $res = $stmt->execute([':id' => $id, ':aid' => $adminId]);

        AuditLog::log('interview_application_restored', 'interview_applications', $id, null, [], $adminId);
        return $res;
    }

    /**
     * Find earlier applications submitted with the same mobile or email
     */
    public static function findRepeatApplicants(string $mobile, string $email, int $excludeId = 0): array {
        if (empty($mobile) && empty($email)) return [];

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, full_name, post_applied, mobile, email, submitted_at, decision 
            FROM `interview_applications` 
            WHERE (`mobile` = :mob OR `email` = :em) AND `id` != :ex 
            ORDER BY `submitted_at` DESC");
        $stmt->execute([
            ':mob' => $mobile,
            ':em'  => strtolower($email),
            ':ex'  => $excludeId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve QR settings for the interview form
     */
    public static function getQrSettings(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM `qr_settings` WHERE `type` = 'interview_form' LIMIT 1");
        $res = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;

        if (!$res) {
            // Seed fallback
            $slug = 'rsp_interview_' . bin2hex(random_bytes(6));
            $db->prepare("INSERT INTO `qr_settings` (`type`, `slug`, `accepting_responses`, `created_at`) 
                VALUES ('interview_form', :slug, 1, NOW())")->execute([':slug' => $slug]);
            return ['type' => 'interview_form', 'slug' => $slug, 'accepting_responses' => 1];
        }

        return $res;
    }

    /**
     * Toggle or update accepting_responses for the interview form
     */
    public static function updateQrSettings(bool $accepting): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE `qr_settings` SET `accepting_responses` = :acc WHERE `type` = 'interview_form'");
        $stmt->execute([':acc' => $accepting ? 1 : 0]);
    }

    /**
     * Rate limit verification: max 10 submissions per hour per IP
     */
    public static function checkRateLimit(string $ip): bool {
        if (empty($ip)) return true;
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM `interview_applications` 
            WHERE `ip_address` = :ip AND `submitted_at` >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
        $stmt->execute([':ip' => $ip]);
        $count = (int) $stmt->fetchColumn();
        return $count < 10;
    }

    /**
     * Automated transactional email sender with 24-hour deduplication
     */
    public static function sendAutomationEmails(int $applicationId, string $triggerType, array $appData): void {
        $db = Database::getConnection();

        // 1. New Form HR Alert
        if ($triggerType === 'new_form') {
            $enabled = (int) Setting::get('interview_email_hr_new_form', 1);
            if (!$enabled) return;

            $recipientsRaw = Setting::get('interview_email_hr_recipients', 'hr@realtysmartzpathshala.in');
            $recipients = array_filter(array_map('trim', explode(',', $recipientsRaw)));
            if (empty($recipients)) return;

            $candidateName = $appData['full_name'] ?? 'Candidate';
            $postApplied = $appData['post_applied'] ?? 'N/A';
            $mobile = $appData['mobile'] ?? 'N/A';
            $detailUrl = url('/admin/interview-candidates/' . $applicationId);

            $subject = "New Interview Application: {$candidateName} - {$postApplied}";
            $bodyHtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: 'DM Sans', Arial, sans-serif; background: #F7F5EF; margin:0; padding:20px; color:#26332D; }
.card { background: #FFFFFF; border-radius: 12px; border: 1px solid #E2DDD0; max-width: 540px; margin: 0 auto; padding: 28px; }
.btn { display: inline-block; background: #1F5C4A; color: #FFFFFF !important; text-decoration: none; padding: 12px 24px; border-radius: 999px; font-weight: 600; margin-top: 18px; }
</style>
</head>
<body>
<div class="card">
    <h2 style="margin-top:0; color:#1F5C4A;">New Interview Application Received</h2>
    <p>A new candidate has submitted the Interview Application Form via QR code.</p>
    <table style="width:100%; border-collapse:collapse; margin-top:16px;">
        <tr><td style="padding:6px 0; color:#6D766F;">Candidate Name:</td><td><strong>{$candidateName}</strong></td></tr>
        <tr><td style="padding:6px 0; color:#6D766F;">Post Applied For:</td><td><strong>{$postApplied}</strong></td></tr>
        <tr><td style="padding:6px 0; color:#6D766F;">Mobile:</td><td><strong>{$mobile}</strong></td></tr>
        <tr><td style="padding:6px 0; color:#6D766F;">HR Selected:</td><td><strong>{$appData['hr_name']}</strong></td></tr>
    </table>
    <a href="{$detailUrl}" class="btn">View Application Details</a>
</div>
</body>
</html>
HTML;

            foreach ($recipients as $toEmail) {
                if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) continue;

                $logStmt = $db->prepare("INSERT INTO `interview_email_log` (`application_id`, `type`, `to_email`, `status`, `created_at`) 
                    VALUES (:aid, 'hr_new_form', :em, 'pending', NOW())");
                $logStmt->execute([':aid' => $applicationId, ':em' => $toEmail]);
                $logId = (int) $db->lastInsertId();

                try {
                    $sent = Mailer::sendRawHtml($toEmail, $subject, $bodyHtml);
                    $status = $sent ? 'sent' : 'failed';
                    $db->prepare("UPDATE `interview_email_log` SET `status` = :st, `sent_at` = NOW() WHERE `id` = :id")
                       ->execute([':st' => $status, ':id' => $logId]);
                } catch (\Throwable $e) {
                    $db->prepare("UPDATE `interview_email_log` SET `status` = 'failed', `error` = :err WHERE `id` = :id")
                       ->execute([':err' => $e->getMessage(), ':id' => $logId]);
                }
            }
            return;
        }

        // 2. Candidate Decision: Selected
        if ($triggerType === 'selected') {
            $enabled = (int) Setting::get('interview_email_candidate_selected', 0);
            if (!$enabled) return;

            // 24-Hour Deduplication
            $dupStmt = $db->prepare("SELECT COUNT(*) FROM `interview_email_log` 
                WHERE `application_id` = :aid AND `type` = 'candidate_selected' AND `status` = 'sent' AND `created_at` >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
            $dupStmt->execute([':aid' => $applicationId]);
            if ((int)$dupStmt->fetchColumn() > 0) {
                return;
            }

            $candidateEmail = $appData['email'] ?? '';
            if (empty($candidateEmail) || !filter_var($candidateEmail, FILTER_VALIDATE_EMAIL)) return;

            $candidateName = $appData['full_name'] ?? 'Candidate';
            $position = $appData['position_offered'] ?: ($appData['post_applied'] ?? 'Selected Role');
            $joiningDate = !empty($appData['joining_date']) ? date('d M Y', strtotime($appData['joining_date'])) : 'To be confirmed';

            $subject = Setting::get('interview_email_selected_subject', 'Offer of Employment | Realty Smartz Pathshala');
            $bodyRaw = Setting::get('interview_email_selected_body', "Dear {name},\n\nWe are pleased to inform you that you have been selected for the position of {position} at Realty Smartz Pathshala.\n\nDate of Joining: {joining_date}.");

            $bodyText = str_replace(['{name}', '{position}', '{joining_date}'], [$candidateName, $position, $joiningDate], $bodyRaw);
            $bodyHtml = nl2br(e($bodyText));

            $logStmt = $db->prepare("INSERT INTO `interview_email_log` (`application_id`, `type`, `to_email`, `status`, `created_at`) 
                VALUES (:aid, 'candidate_selected', :em, 'pending', NOW())");
            $logStmt->execute([':aid' => $applicationId, ':em' => $candidateEmail]);
            $logId = (int) $db->lastInsertId();

            try {
                $sent = Mailer::sendRawHtml($candidateEmail, $subject, $bodyHtml);
                $status = $sent ? 'sent' : 'failed';
                $db->prepare("UPDATE `interview_email_log` SET `status` = :st, `sent_at` = NOW() WHERE `id` = :id")
                   ->execute([':st' => $status, ':id' => $logId]);
            } catch (\Throwable $e) {
                $db->prepare("UPDATE `interview_email_log` SET `status` = 'failed', `error` = :err WHERE `id` = :id")
                   ->execute([':err' => $e->getMessage(), ':id' => $logId]);
            }
            return;
        }

        // 3. Candidate Decision: Rejected
        if ($triggerType === 'rejected') {
            $enabled = (int) Setting::get('interview_email_candidate_rejected', 0);
            if (!$enabled) return;

            // 24-Hour Deduplication
            $dupStmt = $db->prepare("SELECT COUNT(*) FROM `interview_email_log` 
                WHERE `application_id` = :aid AND `type` = 'candidate_rejected' AND `status` = 'sent' AND `created_at` >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
            $dupStmt->execute([':aid' => $applicationId]);
            if ((int)$dupStmt->fetchColumn() > 0) {
                return;
            }

            $candidateEmail = $appData['email'] ?? '';
            if (empty($candidateEmail) || !filter_var($candidateEmail, FILTER_VALIDATE_EMAIL)) return;

            $candidateName = $appData['full_name'] ?? 'Candidate';
            $position = $appData['post_applied'] ?? 'Applicant';

            $subject = Setting::get('interview_email_rejected_subject', 'Application Update | Realty Smartz Pathshala');
            $bodyRaw = Setting::get('interview_email_rejected_body', "Dear {name},\n\nThank you for attending the interview at Realty Smartz Pathshala.");

            $bodyText = str_replace(['{name}', '{position}', '{joining_date}'], [$candidateName, $position, ''], $bodyRaw);
            $bodyHtml = nl2br(e($bodyText));

            $logStmt = $db->prepare("INSERT INTO `interview_email_log` (`application_id`, `type`, `to_email`, `status`, `created_at`) 
                VALUES (:aid, 'candidate_rejected', :em, 'pending', NOW())");
            $logStmt->execute([':aid' => $applicationId, ':em' => $candidateEmail]);
            $logId = (int) $db->lastInsertId();

            try {
                $sent = Mailer::sendRawHtml($candidateEmail, $subject, $bodyHtml);
                $status = $sent ? 'sent' : 'failed';
                $db->prepare("UPDATE `interview_email_log` SET `status` = :st, `sent_at` = NOW() WHERE `id` = :id")
                   ->execute([':st' => $status, ':id' => $logId]);
            } catch (\Throwable $e) {
                $db->prepare("UPDATE `interview_email_log` SET `status` = 'failed', `error` = :err WHERE `id` = :id")
                   ->execute([':err' => $e->getMessage(), ':id' => $logId]);
            }
            return;
        }
    }
}
