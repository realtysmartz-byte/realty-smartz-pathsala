<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\User;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Blog;
use App\Models\Setting;
use App\Models\AuditLog;
use App\Helpers\PdfHelper;
use App\Helpers\DocxHelper;
use App\Helpers\ExportHelper;
use App\Helpers\Mailer;
use PDO;
use Exception;

class AdminController {
    /**
     * Admin Dashboard Overview (/admin/dashboard)
     */
    public function dashboard(): void {
        $db = Database::getConnection();

        // 1. Stat Strip Metrics (Real Data Only)
        $totalCandidates = (int) $db->query("SELECT COUNT(*) FROM `users` WHERE `role` = 'candidate' AND `is_archived` = 0")->fetchColumn();
        
        $onboardingSubmitted = (int) $db->query("SELECT COUNT(DISTINCT u.id) FROM `users` u 
            WHERE u.role = 'candidate' AND u.is_archived = 0 
            AND (u.status IN ('documents_submitted', 'profile_completed', 'test_pending', 'test_completed', 'under_review', 'approved') 
                 OR (SELECT COUNT(*) FROM documents d WHERE d.user_id = u.id AND d.is_archived = 0) >= 2)")->fetchColumn();

        $testsCompleted = (int) $db->query("SELECT COUNT(*) FROM `test_attempts` WHERE `status` = 'completed'")->fetchColumn();

        $avgScoreVal = $db->query("SELECT AVG(percentage) FROM `test_attempts` WHERE `status` = 'completed'")->fetchColumn();
        $averageScore = ($testsCompleted > 0 && $avgScoreVal !== null && $avgScoreVal !== false)
            ? round((float)$avgScoreVal) . '%'
            : '-';

        // 2. Main Table: Recent Candidates (10 rows)
        $stmtRecent = $db->query("SELECT u.id, u.full_name, u.mobile, u.email, u.created_at,
            u.whatsapp,
            COALESCE(u.father_name, p.father_name) as father_name,
            COALESCE(p.joining_date, u.joining_date) as joining_date,
            p.dob, p.gender, p.address, p.city, p.state, p.pincode,
            (SELECT COUNT(*) FROM documents d WHERE d.user_id = u.id AND d.is_archived = 0) as doc_count,
            ta.score as test_score, ta.total_questions as test_total
            FROM `users` u
            LEFT JOIN `profiles` p ON u.id = p.user_id
            LEFT JOIN (
                SELECT ta1.user_id, ta1.score, ta1.total_questions
                FROM test_attempts ta1
                INNER JOIN (SELECT user_id, MAX(id) as max_id FROM test_attempts WHERE status = 'completed' GROUP BY user_id) ta2 ON ta1.id = ta2.max_id
            ) ta ON u.id = ta.user_id
            WHERE u.role = 'candidate' AND u.is_archived = 0
            ORDER BY u.created_at DESC LIMIT 10");
        $recentCandidates = $stmtRecent ? $stmtRecent->fetchAll() : [];

        // 3. Right Column: Recent Test Results (Name, Set, Score, date)
        $stmtTests = $db->query("SELECT ta.id, ta.score, ta.total_questions, ta.created_at, ta.passed,
            u.id as user_id, u.full_name as candidate_name,
            COALESCE(s.set_code, s.name, 'Set A') as set_name
            FROM `test_attempts` ta
            JOIN `users` u ON ta.user_id = u.id
            LEFT JOIN `aptitude_sets` s ON ta.set_id = s.id
            WHERE ta.status = 'completed'
            ORDER BY ta.created_at DESC LIMIT 10");
        $recentTests = $stmtTests ? $stmtTests->fetchAll() : [];

        $viewTitle = 'Dashboard';
        $pageTitle = 'Dashboard | Realty Smartz Pathshala';

        require APP_PATH . '/views/admin/dashboard.php';
    }

    /**
     * Candidates Listing & Filter (/admin/candidates)
     */
    public function candidates(): void {
        $db = Database::getConnection();

        $search = trim($_GET['q'] ?? '');
        $testStatus = $_GET['test_status'] ?? '';
        $docsStatus = $_GET['docs_status'] ?? '';
        $dateRange = $_GET['date_range'] ?? '';
        $sort = $_GET['sort'] ?? 'created_desc';
        $archived = !empty($_GET['archived']) ? 1 : 0;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $where = ["u.role = 'candidate'", "u.is_archived = :archived"];
        $params = [':archived' => $archived];

        if (!empty($search)) {
            $where[] = "(u.full_name LIKE :search OR u.father_name LIKE :search OR p.father_name LIKE :search OR u.email LIKE :search OR u.mobile LIKE :search OR u.whatsapp LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($dateRange)) {
            if ($dateRange === 'today') {
                $where[] = "DATE(u.created_at) = CURDATE()";
            } elseif ($dateRange === '7days') {
                $where[] = "u.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            } elseif ($dateRange === '30days') {
                $where[] = "u.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            }
        }

        if ($docsStatus === 'uploaded') {
            $where[] = "(SELECT COUNT(*) FROM documents d WHERE d.user_id = u.id AND d.is_archived = 0) > 0";
        } elseif ($docsStatus === 'missing') {
            $where[] = "(SELECT COUNT(*) FROM documents d WHERE d.user_id = u.id AND d.is_archived = 0) = 0";
        }

        if ($testStatus === 'taken') {
            $where[] = "ta.score IS NOT NULL";
        } elseif ($testStatus === 'not_taken') {
            $where[] = "ta.score IS NULL";
        }

        $orderBy = match ($sort) {
            'created_asc' => 'u.created_at ASC',
            'name_asc' => 'u.full_name ASC',
            'score_desc' => 'ta.score DESC, u.created_at DESC',
            default => 'u.created_at DESC'
        };

        $whereClause = implode(' AND ', $where);

        $countSql = "SELECT COUNT(DISTINCT u.id) 
                     FROM `users` u 
                     LEFT JOIN `profiles` p ON u.id = p.user_id 
                     LEFT JOIN (
                         SELECT ta1.user_id, ta1.score
                         FROM test_attempts ta1
                         INNER JOIN (SELECT user_id, MAX(id) as max_id FROM test_attempts WHERE status = 'completed' GROUP BY user_id) ta2 ON ta1.id = ta2.max_id
                     ) ta ON u.id = ta.user_id
                     WHERE {$whereClause}";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT u.id, u.full_name, u.email, u.mobile, u.created_at, u.is_archived, u.is_guest,
                       u.whatsapp,
                       COALESCE(p.joining_date, u.joining_date) as joining_date,
                       p.dob, p.gender, p.address, p.city, p.state, p.pincode,
                       COALESCE(u.father_name, p.father_name) as father_name,
                       (SELECT COUNT(*) FROM documents d WHERE d.user_id = u.id AND d.is_archived = 0) as total_docs,
                       ta.score as test_score, ta.total_questions as test_total, ta.percentage as test_pct, ta.passed as test_passed
                FROM `users` u
                LEFT JOIN `profiles` p ON u.id = p.user_id
                LEFT JOIN (
                    SELECT ta1.user_id, ta1.score, ta1.total_questions, ta1.percentage, ta1.passed
                    FROM test_attempts ta1
                    INNER JOIN (SELECT user_id, MAX(id) as max_id FROM test_attempts WHERE status = 'completed' GROUP BY user_id) ta2 ON ta1.id = ta2.max_id
                ) ta ON u.id = ta.user_id
                WHERE {$whereClause}
                ORDER BY {$orderBy}
                LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $candidates = $stmt->fetchAll();

        $viewTitle = 'Candidates';
        $pageTitle = 'Candidates | Realty Smartz Pathshala';

        require APP_PATH . '/views/admin/candidates.php';
    }

    /**
     * Dedicated Documents Overview (/admin/documents)
     */
    /**
     * Dedicated Documents Overview (/admin/documents)
     */
    public function documents(): void {
        $db = Database::getConnection();

        $search = trim($_GET['q'] ?? '');
        $type = trim($_GET['type'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $dateFrom = trim($_GET['date_from'] ?? '');
        $dateTo = trim($_GET['date_to'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $where = ["d.is_archived = 0"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(u.full_name LIKE :search OR u.email LIKE :search OR u.mobile LIKE :search OR d.original_name LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($type)) {
            $where[] = "d.document_type = :type";
            $params[':type'] = $type;
        }

        if (!empty($status)) {
            $where[] = "d.status = :status";
            $params[':status'] = $status;
        }

        if (!empty($dateFrom)) {
            $where[] = "DATE(d.uploaded_at) >= :date_from";
            $params[':date_from'] = $dateFrom;
        }

        if (!empty($dateTo)) {
            $where[] = "DATE(d.uploaded_at) <= :date_to";
            $params[':date_to'] = $dateTo;
        }

        $whereClause = implode(' AND ', $where);

        // Total count
        $countStmt = $db->prepare("SELECT COUNT(*) FROM `documents` d JOIN `users` u ON d.user_id = u.id WHERE {$whereClause}");
        $countStmt->execute($params);
        $totalDocs = (int) $countStmt->fetchColumn();
        $totalPages = max(1, (int)ceil($totalDocs / $perPage));

        // Documents list
        $sql = "SELECT d.*, u.full_name as candidate_name, u.email as candidate_email, u.mobile as candidate_mobile,
                (SELECT COUNT(*) FROM `document_versions` dv WHERE dv.document_id = d.id) as version_count
            FROM `documents` d 
            JOIN `users` u ON d.user_id = u.id 
            WHERE {$whereClause} 
            ORDER BY d.uploaded_at DESC 
            LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $docs = $stmt->fetchAll();

        // Summary counts
        $statTotal = (int) $db->query("SELECT COUNT(*) FROM `documents` WHERE `is_archived` = 0")->fetchColumn();
        $statPending = (int) $db->query("SELECT COUNT(*) FROM `documents` WHERE `status` = 'pending' AND `is_archived` = 0")->fetchColumn();
        $statApproved = (int) $db->query("SELECT COUNT(*) FROM `documents` WHERE `status` = 'approved' AND `is_archived` = 0")->fetchColumn();
        $statReupload = (int) $db->query("SELECT COUNT(*) FROM `documents` WHERE `status` = 'reupload_requested' AND `is_archived` = 0")->fetchColumn();

        // Distinct document types in DB for filter dropdown
        $availableTypes = $db->query("SELECT DISTINCT `document_type` FROM `documents` WHERE `is_archived` = 0 ORDER BY `document_type` ASC")->fetchAll(PDO::FETCH_COLUMN);

        $viewTitle = 'Documents Manager';
        $appVersion = Setting::get('app_version', '1.0.0');
        $lastDeployed = Setting::get('last_deployed_at', date('Y-m-d H:i:s'));

        require APP_PATH . '/views/admin/documents.php';
    }

    /**
     * Update Individual Document Status & Request Re-upload (POST /admin/documents/{id}/status)
     */
    public function updateDocumentStatus(int $id): void {
        $db = Database::getConnection();
        $newStatus = trim($_POST['status'] ?? '');
        $rejectionReason = trim($_POST['rejection_reason'] ?? ($_POST['comments'] ?? ''));
        $adminId = (int) auth()['id'];

        $stmt = $db->prepare("SELECT d.*, u.full_name as candidate_name, u.email as candidate_email 
            FROM `documents` d 
            JOIN `users` u ON d.user_id = u.id 
            WHERE d.id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $doc = $stmt->fetch();

        if (!$doc) {
            if ($this->isAjax()) {
                response_json(['success' => false, 'error' => 'Document not found.'], 404);
                return;
            }
            flash('error', 'Document not found.');
            redirect('/admin/documents');
            return;
        }

        $validStatuses = ['pending', 'approved', 'rejected', 'reupload_requested'];
        if (!in_array($newStatus, $validStatuses, true)) {
            if ($this->isAjax()) {
                response_json(['success' => false, 'error' => 'Invalid document status.'], 422);
                return;
            }
            flash('error', 'Invalid document status.');
            redirect('/admin/documents');
            return;
        }

        $oldStatus = $doc['status'];
        $updStmt = $db->prepare("UPDATE `documents` SET `status` = :st, `rejection_reason` = :rr WHERE `id` = :id");
        $updStmt->execute([
            ':st' => $newStatus,
            ':rr' => ($newStatus === 'reupload_requested' || $newStatus === 'rejected') ? $rejectionReason : null,
            ':id' => $id
        ]);

        // If re-upload requested, also mark candidate user status as changes_requested
        if ($newStatus === 'reupload_requested') {
            $db->prepare("UPDATE `users` SET `status` = 'changes_requested' WHERE `id` = :uid")
               ->execute([':uid' => $doc['user_id']]);

            // Add status history
            $stmtHist = $db->prepare("INSERT INTO `status_history` (`user_id`, `changed_by`, `old_status`, `new_status`, `comments`, `created_at`)
                VALUES (:uid, :admin, :old, 'changes_requested', :comm, NOW())");
            $docLabel = ucwords(str_replace('_', ' ', $doc['document_type']));
            $stmtHist->execute([
                ':uid'   => $doc['user_id'],
                ':admin' => $adminId,
                ':old'   => 'under_review',
                ':comm'  => "Re-upload requested for {$docLabel}: {$rejectionReason}"
            ]);

            // Dispatch notification email to candidate
            Mailer::sendStatusNotification(
                $doc['candidate_email'],
                $doc['candidate_name'],
                'changes_requested',
                "Action Required: Re-upload requested for your {$docLabel}. Reason: {$rejectionReason}. Please login to your onboarding portal to upload a clear replacement document."
            );
        }

        AuditLog::log('document_status_updated', 'documents', $id, ['status' => $oldStatus], [
            'status' => $newStatus,
            'reason' => $rejectionReason,
            'candidate_id' => $doc['user_id']
        ], $adminId);

        $msg = ($newStatus === 'approved')
            ? 'Document approved successfully.'
            : (($newStatus === 'reupload_requested') ? 'Re-upload request dispatched to candidate.' : 'Document status updated.');

        if ($this->isAjax()) {
            response_json(['success' => true, 'message' => $msg, 'status' => $newStatus]);
            return;
        }

        flash('success', $msg);
        $redirectUrl = !empty($_POST['redirect']) ? $_POST['redirect'] : '/admin/documents';
        redirect($redirectUrl);
    }

    /**
     * Authorized Reveal of Masked Aadhaar or PAN (POST /admin/candidates/{id}/reveal-identity)
     */
    public function revealIdentity(int $id): void {
        $currentUser = auth();
        if (!in_array($currentUser['role'] ?? '', ['super_admin', 'hr_manager'], true)) {
            response_json(['success' => false, 'error' => 'Permission denied. Only HR Managers and Super Admins can reveal sensitive identity numbers.'], 403);
            return;
        }

        $field = trim($_POST['field'] ?? ($_POST['type'] ?? ''));
        if (!in_array($field, ['aadhaar', 'pan'], true)) {
            response_json(['success' => false, 'error' => 'Invalid identity field.'], 422);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT `aadhaar_enc`, `pan_enc`, `aadhaar_last4`, `pan_last4` FROM `profiles` WHERE `user_id` = :uid LIMIT 1");
        $stmt->execute([':uid' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            response_json(['success' => false, 'error' => 'Candidate profile not found.'], 404);
            return;
        }

        $decrypted = '';
        if ($field === 'aadhaar') {
            if (!empty($row['aadhaar_enc'])) {
                $decrypted = \App\Helpers\CryptoHelper::decrypt($row['aadhaar_enc']);
            }
            if (empty($decrypted) && !empty($row['aadhaar_last4'])) {
                $decrypted = 'XXXX XXXX ' . $row['aadhaar_last4'];
            }
        } elseif ($field === 'pan') {
            if (!empty($row['pan_enc'])) {
                $decrypted = \App\Helpers\CryptoHelper::decrypt($row['pan_enc']);
            }
            if (empty($decrypted) && !empty($row['pan_last4'])) {
                $decrypted = 'XXXXX' . $row['pan_last4'];
            }
        }

        if (empty($decrypted)) {
            response_json(['success' => false, 'error' => 'No record exists for this field.'], 404);
            return;
        }

        // Audit Log Entry (Mandatory for DPDP Act compliance)
        AuditLog::log('identity_revealed', 'users', $id, null, [
            'field'       => $field,
            'revealed_by' => $currentUser['email'],
            'ip'          => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ], (int)$currentUser['id']);

        response_json([
            'success'  => true,
            'field'    => $field,
            'revealed' => $decrypted,
            'value'    => $decrypted
        ]);
    }

    /**
     * Download Filtered Documents as ZIP (/admin/documents/export-zip)
     */
    public function exportDocumentsZip(): void {
        $db = Database::getConnection();
        $type = trim($_GET['type'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $search = trim($_GET['q'] ?? '');

        $where = ["d.is_archived = 0"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(u.full_name LIKE :search OR u.email LIKE :search OR u.mobile LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        if (!empty($type)) {
            $where[] = "d.document_type = :type";
            $params[':type'] = $type;
        }
        if (!empty($status)) {
            $where[] = "d.status = :status";
            $params[':status'] = $status;
        }

        $whereClause = implode(' AND ', $where);
        $stmt = $db->prepare("SELECT d.*, u.full_name as candidate_name FROM `documents` d JOIN `users` u ON d.user_id = u.id WHERE {$whereClause} LIMIT 200");
        $stmt->execute($params);
        $docs = $stmt->fetchAll();

        if (empty($docs)) {
            flash('error', 'No documents found matching the filter criteria.');
            redirect('/admin/documents');
            return;
        }

        $zipPath = STORAGE_PATH . '/cache/docs_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            flash('error', 'Could not initialize ZIP archive.');
            redirect('/admin/documents');
            return;
        }

        foreach ($docs as $d) {
            if (file_exists($d['file_path'])) {
                $cleanName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $d['candidate_name'] . '_' . $d['document_type'] . '_' . $d['original_name']);
                $zip->addFile($d['file_path'], $cleanName);
            }
        }
        $zip->close();

        AuditLog::log('documents_bulk_zip_exported', 'documents', null, null, ['count' => count($docs)], (int)auth()['id']);

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="Candidate_Documents_' . date('Y-m-d') . '.zip"');
        header('Content-Length: ' . filesize($zipPath));
        readfile($zipPath);
        @unlink($zipPath);
        exit;
    }

    /**
     * Bulk Action on Selected Documents (POST /admin/documents/bulk-action)
     */
    public function bulkDocumentAction(): void {
        $action = trim($_POST['bulk_action'] ?? '');
        $docIds = $_POST['doc_ids'] ?? [];

        if (empty($docIds) || !is_array($docIds)) {
            flash('error', 'No documents selected for bulk action.');
            redirect('/admin/documents');
            return;
        }

        $db = Database::getConnection();
        $adminId = (int) auth()['id'];

        if ($action === 'approve') {
            $inClause = implode(',', array_map('intval', $docIds));
            $db->exec("UPDATE `documents` SET `status` = 'approved' WHERE `id` IN ({$inClause})");
            AuditLog::log('documents_bulk_approved', 'documents', null, null, ['count' => count($docIds), 'ids' => $docIds], $adminId);
            flash('success', count($docIds) . ' documents approved successfully.');
        } elseif ($action === 'zip') {
            $inClause = implode(',', array_map('intval', $docIds));
            $stmt = $db->query("SELECT d.*, u.full_name as candidate_name FROM `documents` d JOIN `users` u ON d.user_id = u.id WHERE d.id IN ({$inClause})");
            $docs = $stmt->fetchAll();

            $zipPath = STORAGE_PATH . '/cache/bulk_' . date('Ymd_His') . '.zip';
            $zip = new \ZipArchive();
            $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            foreach ($docs as $d) {
                if (file_exists($d['file_path'])) {
                    $cleanName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $d['candidate_name'] . '_' . $d['document_type'] . '_' . $d['original_name']);
                    $zip->addFile($d['file_path'], $cleanName);
                }
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="Selected_Documents_' . date('Y-m-d') . '.zip"');
            header('Content-Length: ' . filesize($zipPath));
            readfile($zipPath);
            @unlink($zipPath);
            exit;
        } else {
            flash('error', 'Invalid bulk action.');
        }

        redirect('/admin/documents');
    }

    /**
     * Dedicated Assessments Overview (/admin/assessments)
     */
    public function assessments(): void {
        $db = Database::getConnection();
        $sets = $db->query("SELECT s.*, 
            (SELECT COUNT(*) FROM questions q WHERE q.set_id = s.id AND q.is_archived = 0) as question_count
            FROM aptitude_sets s ORDER BY s.id ASC")->fetchAll();

        $recentAttempts = $db->query("SELECT ta.*, u.full_name, u.email, s.set_code 
            FROM test_attempts ta 
            JOIN users u ON ta.user_id = u.id 
            LEFT JOIN aptitude_sets s ON ta.set_id = s.id 
            ORDER BY ta.created_at DESC LIMIT 20")->fetchAll();

        $candidates = $db->query("SELECT id, full_name, email, mobile FROM users WHERE role = 'candidate' AND is_archived = 0 ORDER BY full_name ASC")->fetchAll();

        // Guest test links with person name, set, status, created, expires, score
        $guestLinks = $db->query("SELECT gtl.*, 
            COALESCE(u.full_name, gtl.guest_name, 'Open Guest') as person_name,
            COALESCE(u.email, gtl.guest_email, '') as person_email,
            COALESCE(u.mobile, gtl.guest_mobile, '') as person_mobile,
            COALESCE(s.set_code, 'Dynamic') as set_name,
            ta.score as attempt_score,
            ta.total_questions as attempt_total,
            ta.percentage as attempt_percentage,
            ta.passed as attempt_passed
            FROM guest_test_links gtl
            LEFT JOIN users u ON gtl.user_id = u.id
            LEFT JOIN aptitude_sets s ON gtl.set_id = s.id
            LEFT JOIN test_attempts ta ON ta.guest_test_link_id = gtl.id
            ORDER BY gtl.created_at DESC")->fetchAll();

        $cacheFile = STORAGE_PATH . '/cache/guest_tokens.json';
        $tokenCache = file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : [];
        if (!is_array($tokenCache)) $tokenCache = [];

        foreach ($guestLinks as &$link) {
            $link['token'] = $tokenCache[$link['id']] ?? '';
            $link['url'] = !empty($link['token']) ? url('/test/g/' . $link['token']) : '';
            if (in_array($link['status'], ['active', 'in_progress'], true) && time() > strtotime($link['expires_at'])) {
                $link['status'] = 'expired';
            }
        }
        unset($link);

        $viewTitle = 'Aptitude Assessments';
        $appVersion = Setting::get('app_version', '1.0.0');
        $lastDeployed = Setting::get('last_deployed_at', date('Y-m-d H:i:s'));

        require APP_PATH . '/views/admin/assessments.php';
    }

    /**
     * Single Candidate Comprehensive Detail (/admin/candidates/{id})
     */
    public function candidateDetail(int $id): void {
        $candidate = Profile::getFullProfile($id);
        if (!$candidate) {
            flash('error', 'Candidate record not found.');
            redirect('/admin/candidates');
            return;
        }

        $db = Database::getConnection();

        // 1. Fetch detailed question answers if candidate took aptitude test
        $testAnswers = [];
        if (!empty($candidate['test_attempt']['id'])) {
            $stmtAns = $db->prepare("SELECT aa.*, q.question_text,
                    sel.option_text as candidate_answer,
                    corr.option_text as correct_answer
                FROM `attempt_answers` aa
                JOIN `questions` q ON aa.question_id = q.id
                LEFT JOIN `options` sel ON aa.selected_option_id = sel.id
                LEFT JOIN `options` corr ON (corr.question_id = q.id AND corr.is_correct = 1)
                WHERE aa.attempt_id = :aid
                ORDER BY aa.id ASC");
            $stmtAns->execute([':aid' => $candidate['test_attempt']['id']]);
            $testAnswers = $stmtAns->fetchAll();
        }

        // 2. Fetch guest test links associated with this candidate
        $stmtGuest = $db->prepare("SELECT gtl.*, COALESCE(s.set_code, s.name, 'Set A') as set_name
            FROM `guest_test_links` gtl
            LEFT JOIN `aptitude_sets` s ON gtl.set_id = s.id
            WHERE gtl.user_id = :uid
            ORDER BY gtl.id DESC");
        $stmtGuest->execute([':uid' => $id]);
        $guestLinks = $stmtGuest->fetchAll();

        $cacheFile = STORAGE_PATH . '/cache/guest_tokens.json';
        $tokenCache = file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : [];
        if (!is_array($tokenCache)) $tokenCache = [];

        foreach ($guestLinks as &$gLink) {
            $gLink['token'] = $tokenCache[$gLink['id']] ?? '';
            $gLink['url'] = !empty($gLink['token']) ? url('/test/g/' . $gLink['token']) : '';
            if (in_array($gLink['status'], ['active', 'in_progress'], true) && time() > strtotime($gLink['expires_at'])) {
                $gLink['status'] = 'expired';
            }
        }
        unset($gLink);

        // Top bar page title is the candidate's name per prompt specification
        $viewTitle = $candidate['full_name'];
        $pageTitle = e($candidate['full_name']) . " | Candidate Profile";

        require APP_PATH . '/views/admin/candidate_detail.php';
    }

    /**
     * Update Candidate Details (POST /admin/candidates/{id}/update)
     */
    public function updateCandidate(int $id): void {
        $db = Database::getConnection();
        $adminId = (int)(auth()['id'] ?? 0);

        $stmtU = $db->prepare("SELECT * FROM `users` WHERE `id` = :id AND `role` = 'candidate' LIMIT 1");
        $stmtU->execute([':id' => $id]);
        $user = $stmtU->fetch();

        if (!$user) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))) {
                response_json(['success' => false, 'error' => 'Candidate not found.'], 404);
                return;
            }
            flash('error', 'Candidate not found.');
            redirect('/admin/candidates');
            return;
        }

        $fullName = trim($_POST['full_name'] ?? '');
        $fatherName = trim($_POST['father_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $mobile = trim($_POST['mobile'] ?? '');
        $whatsapp = trim($_POST['whatsapp'] ?? '');
        $joiningDate = !empty($_POST['joining_date']) ? trim($_POST['joining_date']) : null;
        $dob = !empty($_POST['dob']) ? trim($_POST['dob']) : null;
        $gender = !empty($_POST['gender']) ? trim($_POST['gender']) : null;
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $state = trim($_POST['state'] ?? '');
        $pincode = trim($_POST['pincode'] ?? '');

        if (empty($fullName)) {
            $msg = 'Full name is required.';
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))) {
                response_json(['success' => false, 'error' => $msg], 422);
                return;
            }
            flash('error', $msg);
            redirect($_SERVER['HTTP_REFERER'] ?? ('/admin/candidates/' . $id));
            return;
        }

        // Validate email uniqueness if changed
        if (!empty($email) && $email !== strtolower($user['email'])) {
            $checkEmail = $db->prepare("SELECT id FROM `users` WHERE `email` = :em AND `id` != :id LIMIT 1");
            $checkEmail->execute([':em' => $email, ':id' => $id]);
            if ($checkEmail->fetch()) {
                $msg = 'Email address is already in use by another account.';
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))) {
                    response_json(['success' => false, 'error' => $msg], 422);
                    return;
                }
                flash('error', $msg);
                redirect($_SERVER['HTTP_REFERER'] ?? ('/admin/candidates/' . $id));
                return;
            }
        }

        // 1. Update users table
        $stmtUpdUser = $db->prepare("UPDATE `users` SET
            `full_name` = :fname,
            `father_name` = :fatname,
            `email` = :email,
            `mobile` = :mobile,
            `whatsapp` = :wa,
            `joining_date` = :jdate,
            `updated_at` = NOW()
            WHERE `id` = :id");
        $stmtUpdUser->execute([
            ':fname' => $fullName,
            ':fatname' => $fatherName ?: null,
            ':email' => $email ?: $user['email'],
            ':mobile' => $mobile ?: null,
            ':wa' => $whatsapp ?: null,
            ':jdate' => $joiningDate,
            ':id' => $id
        ]);

        // 2. Update profiles table
        $stmtCheckP = $db->prepare("SELECT id FROM `profiles` WHERE `user_id` = :uid LIMIT 1");
        $stmtCheckP->execute([':uid' => $id]);
        $hasProfile = $stmtCheckP->fetch();

        if ($hasProfile) {
            $stmtUpdProf = $db->prepare("UPDATE `profiles` SET
                `father_name` = :fatname,
                `whatsapp` = :wa,
                `joining_date` = :jdate,
                `dob` = :dob,
                `gender` = :gender,
                `address` = :addr,
                `city` = :city,
                `state` = :state,
                `pincode` = :pin,
                `updated_at` = NOW()
                WHERE `user_id` = :uid");
            $stmtUpdProf->execute([
                ':fatname' => $fatherName ?: null,
                ':wa' => $whatsapp ?: null,
                ':jdate' => $joiningDate,
                ':dob' => $dob,
                ':gender' => $gender,
                ':addr' => $address ?: null,
                ':city' => $city ?: null,
                ':state' => $state ?: null,
                ':pin' => $pincode ?: null,
                ':uid' => $id
            ]);
        } else {
            $stmtInsProf = $db->prepare("INSERT INTO `profiles`
                (`user_id`, `father_name`, `whatsapp`, `joining_date`, `dob`, `gender`, `address`, `city`, `state`, `pincode`, `completion_percentage`, `created_at`)
                VALUES (:uid, :fatname, :wa, :jdate, :dob, :gender, :addr, :city, :state, :pin, 50, NOW())");
            $stmtInsProf->execute([
                ':uid' => $id,
                ':fatname' => $fatherName ?: null,
                ':wa' => $whatsapp ?: null,
                ':jdate' => $joiningDate,
                ':dob' => $dob,
                ':gender' => $gender,
                ':addr' => $address ?: null,
                ':city' => $city ?: null,
                ':state' => $state ?: null,
                ':pin' => $pincode ?: null
            ]);
        }

        AuditLog::log('update_candidate', 'users', $id, null, [
            'full_name' => $fullName,
            'joining_date' => $joiningDate,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ], $adminId);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))) {
            response_json([
                'success' => true,
                'message' => 'Candidate details updated successfully.',
                'candidate' => [
                    'id' => $id,
                    'full_name' => $fullName,
                    'father_name' => $fatherName,
                    'email' => $email ?: $user['email'],
                    'mobile' => $mobile,
                    'whatsapp' => $whatsapp,
                    'joining_date' => $joiningDate,
                    'joining_date_formatted' => $joiningDate ? date('d M Y', strtotime($joiningDate)) : 'Not set'
                ]
            ]);
            return;
        }

        flash('success', 'Candidate details updated successfully.');
        $referer = $_SERVER['HTTP_REFERER'] ?? ('/admin/candidates/' . $id);
        redirect($referer);
    }

    /**
     * Archive/Delete Candidate (POST /admin/candidates/{id}/archive, /admin/candidates/{id}/delete)
     */
    public function archiveCandidate(int $id): void {
        User::softDelete($id, (int)auth()['id']);
        AuditLog::log('archive_candidate', 'users', $id, null, ['ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'], (int)auth()['id']);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))) {
            response_json(['success' => true, 'message' => 'Candidate has been deleted.']);
            return;
        }

        flash('success', 'Candidate profile has been deleted.');
        $referer = $_SERVER['HTTP_REFERER'] ?? '/admin/candidates';
        redirect($referer);
    }

    /**
     * Restore Candidate (POST /admin/candidates/{id}/restore)
     */
    public function restoreCandidate(int $id): void {
        User::restore($id, (int)auth()['id']);
        AuditLog::log('restore_candidate', 'users', $id, null, ['ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'], (int)auth()['id']);
        flash('success', 'Candidate profile has been restored.');
        redirect('/admin/candidates/' . $id);
    }

    /**
     * Download Full File PDF: Cover + Table of Contents + All Uploaded Documents
     * (/admin/candidates/{id}/export-full-pdf)
     */
    public function exportFullPdf(int $id): void {
        $candidate = Profile::getFullProfile($id);
        if (!$candidate) {
            flash('error', 'Candidate not found.');
            redirect('/admin/candidates');
            return;
        }

        $pdfContent = PdfHelper::generateFullCandidateFile($candidate);

        $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $candidate['full_name']);
        $filename = "{$cleanName}-{$id}-file.pdf";

        AuditLog::log('download_full_file_pdf', 'users', $id, null, [
            'filename' => $filename,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ], auth()['id'] ?? null);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdfContent));
        echo $pdfContent;
        exit;
    }

    /**
     * Export Profile Only PDF (/admin/candidates/{id}/export-pdf)
     */
    public function exportPdf(int $id): void {
        if (!empty($_GET['full'])) {
            $this->exportFullPdf($id);
            return;
        }

        $candidate = Profile::getFullProfile($id);
        if (!$candidate) {
            flash('error', 'Candidate not found.');
            redirect('/admin/candidates');
            return;
        }

        $pdfContent = PdfHelper::generateCandidateProfile($candidate);
        $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $candidate['full_name']);
        $filename = "{$cleanName}-{$id}-profile.pdf";

        AuditLog::log('download_profile_pdf', 'users', $id, null, [
            'filename' => $filename,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ], auth()['id'] ?? null);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdfContent));
        echo $pdfContent;
        exit;
    }

    /**
     * Export Profile DOCX (/admin/candidates/{id}/export-docx)
     */
    public function exportDocx(int $id): void {
        $candidate = Profile::getFullProfile($id);
        $docxContent = DocxHelper::generateCandidateDocx($candidate);
        $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $candidate['full_name']);
        $filename = "{$cleanName}-{$id}-profile.docx";

        AuditLog::log('download_profile_docx', 'users', $id, null, [
            'filename' => $filename,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ], auth()['id'] ?? null);

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($docxContent));
        echo $docxContent;
        exit;
    }

    /**
     * Download Original Documents in ZIP (/admin/candidates/{id}/export-zip)
     */
    public function exportZip(int $id): void {
        $candidate = Profile::getFullProfile($id);
        $zipContent = ExportHelper::createCandidateDocumentsZip($candidate['documents'], $candidate['full_name']);
        $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $candidate['full_name']);
        $filename = "{$cleanName}-{$id}-documents.zip";

        AuditLog::log('download_documents_zip', 'users', $id, null, [
            'filename' => $filename,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ], auth()['id'] ?? null);

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($zipContent));
        echo $zipContent;
        exit;
    }

    /**
     * Bulk Download Selected Candidates Full PDFs as a single ZIP
     * (/admin/candidates/bulk-pdf-zip)
     */
    public function bulkPdfZip(): void {
        $rawIds = $_GET['ids'] ?? ($_POST['candidate_ids'] ?? '');
        $ids = array_filter(array_map('intval', explode(',', $rawIds)));

        if (empty($ids)) {
            flash('error', 'No candidates selected.');
            redirect('/admin/candidates');
            return;
        }

        $cacheDir = STORAGE_PATH . '/cache';
        @mkdir($cacheDir, 0755, true);
        $zipFile = $cacheDir . '/bulk_candidate_pdfs_' . date('Ymd_His') . '.zip';

        $zip = new \ZipArchive();
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            flash('error', 'Unable to create zip archive.');
            redirect('/admin/candidates');
            return;
        }

        $adminId = auth()['id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        foreach ($ids as $cid) {
            $cand = Profile::getFullProfile($cid);
            if (!$cand) continue;

            $pdfBytes = PdfHelper::generateFullCandidateFile($cand);
            $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $cand['full_name']);
            $pdfFilename = "{$cleanName}-{$cid}-file.pdf";
            $zip->addFromString($pdfFilename, $pdfBytes);

            AuditLog::log('bulk_download_pdf_item', 'users', $cid, null, [
                'file' => $pdfFilename,
                'ip' => $ip
            ], $adminId);

            unset($pdfBytes);
            if (function_exists('gc_collect_cycles')) gc_collect_cycles();
        }

        $zip->close();

        AuditLog::log('bulk_download_pdfs_zip', 'users', null, null, [
            'candidate_count' => count($ids),
            'candidate_ids' => $ids,
            'ip' => $ip
        ], $adminId);

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="Candidates_Full_PDFs_' . date('Y-m-d') . '.zip"');
        header('Content-Length: ' . filesize($zipFile));
        readfile($zipFile);
        @unlink($zipFile);
        exit;
    }

    /**
     * Export Candidates to Excel/CSV (/admin/candidates/export-excel)
     */
    public function exportExcel(): void {
        $db = Database::getConnection();
        $rawIds = $_GET['ids'] ?? '';
        $adminId = auth()['id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (!empty($rawIds)) {
            $ids = array_filter(array_map('intval', explode(',', $rawIds)));
            $inClause = implode(',', $ids);
            $stmt = $db->query("SELECT u.* FROM users u WHERE u.id IN ({$inClause}) ORDER BY u.created_at DESC");
        } else {
            $stmt = $db->query("SELECT u.* FROM users u WHERE u.role = 'candidate' AND u.is_archived = 0 ORDER BY u.created_at DESC");
        }

        $users = $stmt ? $stmt->fetchAll() : [];
        $candidates = [];
        foreach ($users as $u) {
            $candidates[] = Profile::getFullProfile((int)$u['id']);
        }

        $csvContent = ExportHelper::candidatesToCsv($candidates);

        AuditLog::log('export_candidates_excel', 'users', null, null, [
            'count' => count($candidates),
            'ip' => $ip
        ], $adminId);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="Candidates_Export_' . date('Y-m-d') . '.csv"');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel
        echo $csvContent;
        exit;
    }

    /**
     * Create Guest Test Link (POST /admin/guest-links/create)
     */
    public function createGuestLink(): void {
        $userId = !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null;
        $guestName = trim($_POST['guest_name'] ?? '');
        $guestMobile = trim($_POST['guest_mobile'] ?? '');
        $guestEmail = trim($_POST['guest_email'] ?? '');
        $setId = !empty($_POST['set_id']) ? (int)$_POST['set_id'] : null;
        $expiryHours = max(1, (int)($_POST['expiry_hours'] ?? 48));

        // Generate cryptographically secure random token (at least 32 bytes = 64 hex characters)
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + ($expiryHours * 3600));
        $adminId = (int) (auth()['id'] ?? 0);

        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO `guest_test_links` 
            (`token_hash`, `user_id`, `guest_name`, `guest_mobile`, `guest_email`, `set_id`, `expires_at`, `max_attempts`, `attempts_used`, `status`, `created_by`, `created_at`)
            VALUES (:hash, :uid, :gname, :gmobile, :gemail, :setid, :expires, 1, 0, 'active', :aid, NOW())");
        $stmt->execute([
            ':hash' => $tokenHash,
            ':uid' => $userId,
            ':gname' => $guestName ?: null,
            ':gmobile' => $guestMobile ?: null,
            ':gemail' => $guestEmail ?: null,
            ':setid' => $setId ?: null,
            ':expires' => $expiresAt,
            ':aid' => $adminId ?: null
        ]);

        $linkId = (int) $db->lastInsertId();
        $guestUrl = url('/test/g/' . $token);

        // Save token to token cache so admin table can copy link & share on WhatsApp
        $cacheFile = STORAGE_PATH . '/cache/guest_tokens.json';
        @mkdir(dirname($cacheFile), 0755, true);
        $tokenCache = file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : [];
        if (!is_array($tokenCache)) $tokenCache = [];
        $tokenCache[$linkId] = $token;
        @file_put_contents($cacheFile, json_encode($tokenCache));

        AuditLog::log('create_guest_test_link', 'guest_test_links', $linkId, null, [
            'user_id' => $userId,
            'guest_email' => $guestEmail,
            'expires_at' => $expiresAt,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ], $adminId);

        // If AJAX request, return JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))) {
            response_json([
                'success' => true,
                'link_id' => $linkId,
                'url' => $guestUrl,
                'token' => $token,
                'expires_at' => $expiresAt
            ]);
            return;
        }

        flash('success', "Guest test link created: " . $guestUrl);
        if ($userId) {
            redirect('/admin/candidates/' . $userId);
        } else {
            redirect('/admin/assessments');
        }
    }

    /**
     * Revoke Guest Test Link (POST /admin/guest-links/{id}/revoke)
     */
    public function revokeGuestLink(int $id): void {
        $db = Database::getConnection();
        $adminId = (int)(auth()['id'] ?? 0);

        $stmt = $db->prepare("UPDATE `guest_test_links` SET `status` = 'revoked', `revoked_at` = NOW() WHERE `id` = :id");
        $stmt->execute([':id' => $id]);

        AuditLog::log('revoke_guest_test_link', 'guest_test_links', $id, null, [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ], $adminId);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            response_json(['success' => true, 'message' => 'Link revoked.']);
            return;
        }

        flash('success', 'Guest test link has been revoked.');
        $referer = $_SERVER['HTTP_REFERER'] ?? '/admin/assessments';
        redirect($referer);
    }

    /**
     * Allow another attempt for guest test link (creates new link with a different set)
     */
    public function reattemptGuestLink(int $id): void {
        $db = Database::getConnection();
        $adminId = (int)(auth()['id'] ?? 0);

        $stmtOld = $db->prepare("SELECT * FROM `guest_test_links` WHERE `id` = :id LIMIT 1");
        $stmtOld->execute([':id' => $id]);
        $oldLink = $stmtOld->fetch();

        if (!$oldLink) {
            flash('error', 'Original link not found.');
            redirect('/admin/assessments');
            return;
        }

        // Pick a different set if possible
        $oldSetId = $oldLink['set_id'];
        $stmtSet = $db->prepare("SELECT id FROM `aptitude_sets` WHERE `is_active` = 1 AND `id` != :old_sid ORDER BY RAND() LIMIT 1");
        $stmtSet->execute([':old_sid' => (int)$oldSetId]);
        $newSet = $stmtSet->fetch();
        $newSetId = $newSet ? (int)$newSet['id'] : null;

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + (48 * 3600));

        $stmt = $db->prepare("INSERT INTO `guest_test_links` 
            (`token_hash`, `user_id`, `guest_name`, `guest_mobile`, `guest_email`, `set_id`, `expires_at`, `max_attempts`, `attempts_used`, `status`, `created_by`, `created_at`)
            VALUES (:hash, :uid, :gname, :gmobile, :gemail, :setid, :expires, 1, 0, 'active', :aid, NOW())");
        $stmt->execute([
            ':hash' => $tokenHash,
            ':uid' => $oldLink['user_id'],
            ':gname' => $oldLink['guest_name'],
            ':gmobile' => $oldLink['guest_mobile'],
            ':gemail' => $oldLink['guest_email'],
            ':setid' => $newSetId,
            ':expires' => $expiresAt,
            ':aid' => $adminId
        ]);

        $newLinkId = (int) $db->lastInsertId();
        $newUrl = url('/test/g/' . $token);

        // Save token to token cache
        $cacheFile = STORAGE_PATH . '/cache/guest_tokens.json';
        @mkdir(dirname($cacheFile), 0755, true);
        $tokenCache = file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : [];
        if (!is_array($tokenCache)) $tokenCache = [];
        $tokenCache[$newLinkId] = $token;
        @file_put_contents($cacheFile, json_encode($tokenCache));

        AuditLog::log('reattempt_guest_link', 'guest_test_links', $newLinkId, null, [
            'original_link_id' => $id,
            'new_set_id' => $newSetId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ], $adminId);

        flash('success', "New attempt link created with a different set: " . $newUrl);
        $referer = $_SERVER['HTTP_REFERER'] ?? '/admin/assessments';
        redirect($referer);
    }

    /**
     * Aptitude Question Bank (/admin/questions)
     */
    public function questions(): void {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT q.*, c.name as category_name, COUNT(o.id) as option_count
            FROM `questions` q
            JOIN `test_categories` c ON q.category_id = c.id
            LEFT JOIN `options` o ON q.id = o.question_id
            WHERE q.is_archived = 0
            GROUP BY q.id
            ORDER BY q.category_id ASC, q.id ASC");
        $questions = $stmt->fetchAll();

        $categories = $db->query("SELECT * FROM `test_categories` WHERE `is_active` = 1")->fetchAll();
        $testConfig = $db->query("SELECT * FROM `tests` WHERE `id` = 1")->fetch();

        $appVersion = Setting::get('app_version', '1.0.0');
        $lastDeployed = Setting::get('last_deployed_at', date('Y-m-d H:i:s'));

        require APP_PATH . '/views/admin/questions.php';
    }

    /**
     * Save Question (POST /admin/questions/save)
     */
    public function saveQuestion(): void {
        $db = Database::getConnection();
        $qId = !empty($_POST['question_id']) ? (int)$_POST['question_id'] : null;
        $categoryId = (int) $_POST['category_id'];
        $text = trim($_POST['question_text']);
        $marks = (int)($_POST['marks'] ?? 1);
        $negative = (float)($_POST['negative_marks'] ?? 0.25);
        $explanation = trim($_POST['explanation'] ?? '');
        $options = $_POST['options'] ?? [];
        $correctIndex = (int)($_POST['correct_option'] ?? 0);

        if (empty($text) || count($options) < 2) {
            flash('error', 'Question text and at least 2 options are required.');
            redirect('/admin/questions');
            return;
        }

        if ($qId) {
            // Update question
            $stmt = $db->prepare("UPDATE `questions` SET `category_id` = :cat, `question_text` = :q, `explanation` = :exp, `marks` = :m, `negative_marks` = :nm WHERE `id` = :id");
            $stmt->execute([':cat' => $categoryId, ':q' => $text, ':exp' => $explanation, ':m' => $marks, ':nm' => $negative, ':id' => $qId]);
            $db->prepare("DELETE FROM `options` WHERE `question_id` = :qid")->execute([':qid' => $qId]);
        } else {
            // Insert question
            $stmt = $db->prepare("INSERT INTO `questions` (`category_id`, `question_text`, `explanation`, `marks`, `negative_marks`, `is_active`, `is_archived`, `created_at`)
                VALUES (:cat, :q, :exp, :m, :nm, 1, 0, NOW())");
            $stmt->execute([':cat' => $categoryId, ':q' => $text, ':exp' => $explanation, ':m' => $marks, ':nm' => $negative]);
            $qId = (int)$db->lastInsertId();
        }

        // Insert options
        $stmtOpt = $db->prepare("INSERT INTO `options` (`question_id`, `option_text`, `is_correct`, `option_order`) VALUES (:qid, :txt, :corr, :ord)");
        foreach ($options as $idx => $optText) {
            $isCorrect = ($idx === $correctIndex) ? 1 : 0;
            $stmtOpt->execute([
                ':qid'  => $qId,
                ':txt'  => trim($optText),
                ':corr' => $isCorrect,
                ':ord'  => $idx + 1
            ]);
        }

        flash('success', 'Question saved successfully.');
        redirect('/admin/questions');
    }

    /**
     * Site Content Manager (/admin/content)
     */
    public function siteContent(): void {
        $db = Database::getConnection();
        $projects = $db->query("SELECT * FROM `projects` WHERE `is_archived` = 0 ORDER BY `order_num` ASC")->fetchAll();
        $blogs = $db->query("SELECT * FROM `blog_posts` WHERE `is_archived` = 0 ORDER BY `id` DESC")->fetchAll();
        $videos = $db->query("SELECT * FROM `videos` ORDER BY `order_num` ASC")->fetchAll();
        $enquiries = $db->query("SELECT * FROM `enquiries` ORDER BY `created_at` DESC LIMIT 20")->fetchAll();
        $faqs = $db->query("SELECT * FROM `faqs` ORDER BY `order_num` ASC")->fetchAll();
        $settings = Setting::getAll();

        $appVersion = Setting::get('app_version', '1.0.0');
        $lastDeployed = Setting::get('last_deployed_at', date('Y-m-d H:i:s'));

        require APP_PATH . '/views/admin/content.php';
    }

    /**
     * Save Site Settings (POST /admin/content/settings)
     */
    public function saveSettings(): void {
        $settings = $_POST['settings'] ?? [];
        if (is_array($settings)) {
            foreach ($settings as $k => $v) {
                Setting::set($k, trim((string)$v));
            }
        }
        AuditLog::log('settings_updated', 'settings', null, null, ['keys' => array_keys($settings)], (int)auth()['id']);
        flash('success', 'Site content and configuration settings saved successfully!');
        redirect('/admin/content');
    }

    /**
     * Send Test Email via SMTP (POST /admin/email/test)
     */
    public function sendTestEmail(): void {
        $toEmail = trim($_POST['test_email'] ?? '');
        if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            $toEmail = auth()['email'] ?? 'admin@realtysmartzpathshala.in';
        }

        $res = Mailer::sendTestEmail($toEmail, auth()['full_name'] ?? 'Administrator');
        if ($res['success']) {
            AuditLog::log('test_email_sent', 'smtp', null, null, ['to' => $toEmail], (int)auth()['id']);
            flash('success', "Test email successfully dispatched to {$toEmail}. Check your inbox or /storage/logs/mail.log.");
        } else {
            $errMsg = $res['error'] ?? 'SMTP delivery failed';
            AuditLog::log('test_email_failed', 'smtp', null, null, ['to' => $toEmail, 'error' => $errMsg], (int)auth()['id']);
            flash('error', "Failed to send test email: {$errMsg}. Check /storage/logs/mail.log for detailed connection logs.");
        }

        redirect('/admin/content');
    }

    /**
     * Audit Log Viewer (/admin/audit-logs)
     */
    public function auditLogs(): void {
        $db = Database::getConnection();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;

        $stmt = $db->prepare("SELECT a.*, u.full_name as user_name, u.email as user_email
            FROM `audit_logs` a
            LEFT JOIN `users` u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $logs = $stmt->fetchAll();

        $total = (int) $db->query("SELECT COUNT(*) FROM `audit_logs`")->fetchColumn();

        $appVersion = Setting::get('app_version', '1.0.0');
        $lastDeployed = Setting::get('last_deployed_at', date('Y-m-d H:i:s'));

        require APP_PATH . '/views/admin/audit_logs.php';
    }

    /**
     * Database Backups (/admin/backups)
     */
    public function backups(): void {
        $backupDir = STORAGE_PATH . '/backups';
        $files = glob($backupDir . '/*.sql*');
        $backups = [];
        foreach ($files as $f) {
            $backups[] = [
                'filename' => basename($f),
                'size' => round(filesize($f) / 1024, 2) . ' KB',
                'created_at' => date('Y-m-d H:i:s', filemtime($f))
            ];
        }

        $appVersion = Setting::get('app_version', '1.0.0');
        $lastDeployed = Setting::get('last_deployed_at', date('Y-m-d H:i:s'));

        require APP_PATH . '/views/admin/backups.php';
    }

    /**
     * Create Database Backup (POST /admin/backups/create)
     */
    public function createBackup(): void {
        $db = Database::getConnection();
        $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

        $dump = "-- Realty Smartz Pathshala Database Backup\n";
        $dump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $dump .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $t) {
            $createTable = $db->query("SHOW CREATE TABLE `{$t}`")->fetch();
            $dump .= "DROP TABLE IF EXISTS `{$t}`;\n" . $createTable['Create Table'] . ";\n\n";

            $rows = $db->query("SELECT * FROM `{$t}`")->fetchAll();
            if (!empty($rows)) {
                $cols = array_keys($rows[0]);
                $escapedCols = array_map(fn($c) => "`{$c}`", $cols);
                $dump .= "INSERT INTO `{$t}` (" . implode(', ', $escapedCols) . ") VALUES\n";

                $valRows = [];
                foreach ($rows as $r) {
                    $vals = array_map(function($v) use ($db) {
                        return ($v === null) ? "NULL" : $db->quote($v);
                    }, array_values($r));
                    $valRows[] = "(" . implode(', ', $vals) . ")";
                }
                $dump .= implode(",\n", $valRows) . ";\n\n";
            }
        }
        $dump .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $path = STORAGE_PATH . '/backups/' . $filename;
        file_put_contents($path, $dump);

        // Compress with gz if extension available
        if (function_exists('gzencode')) {
            $gzData = gzencode($dump, 9);
            file_put_contents($path . '.gz', $gzData);
            @unlink($path);
        }

        flash('success', 'Database backup successfully created!');
        redirect('/admin/backups');
    }

    /**
     * Download Backup File (/admin/backups/download/{file})
     */
    public function downloadBackup(string $file): void {
        $safeFile = basename($file);
        $filePath = STORAGE_PATH . '/backups/' . $safeFile;

        if (!file_exists($filePath)) {
            flash('error', 'Backup archive not found.');
            redirect('/admin/backups');
            return;
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $safeFile . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    /**
     * Run Pending Migrations (/admin/migrate)
     */
    public function runMigrations(): void {
        // Super Admin only
        if (auth()['role'] !== 'super_admin') {
            flash('error', 'Only Super Admin can execute database migrations.');
            redirect('/admin/dashboard');
            return;
        }

        $db = Database::getConnection();
        $migrationFiles = glob(ROOT_PATH . '/database/migrations/*.sql');
        sort($migrationFiles);

        $executedMigrations = $db->query("SELECT `migration_name` FROM `migrations`")->fetchAll(PDO::FETCH_COLUMN);

        $runCount = 0;
        foreach ($migrationFiles as $file) {
            $baseName = basename($file);
            if (!in_array($baseName, $executedMigrations, true)) {
                $sql = file_get_contents($file);
                if (!empty(trim($sql))) {
                    $db->exec($sql);
                }
                $stmt = $db->prepare("INSERT INTO `migrations` (`migration_name`, `executed_at`) VALUES (:name, NOW())");
                $stmt->execute([':name' => $baseName]);
                $runCount++;
            }
        }

        // Update last deployed setting
        Setting::set('last_deployed_at', date('Y-m-d H:i:s'), 'system');

        flash('success', "Database migration runner completed. {$runCount} new migration(s) executed.");
        redirect('/admin/dashboard');
    }

    /**
     * Aptitude Test Candidates Directory (/admin/aptitude-candidates)
     */
    public function aptitudeCandidates(): void {
        $db = Database::getConnection();

        $search = trim($_GET['q'] ?? '');
        $testStatus = $_GET['test_status'] ?? '';
        $dateRange = $_GET['date_range'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $where = ["u.role = 'candidate'", "u.is_archived = 0", "u.candidate_type = 'aptitude_only'"];
        $params = [];

        if (!empty($search)) {
            $where[] = "(u.full_name LIKE :search OR u.email LIKE :search OR u.mobile LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($dateRange)) {
            if ($dateRange === 'today') {
                $where[] = "DATE(u.created_at) = CURDATE()";
            } elseif ($dateRange === '7days') {
                $where[] = "u.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            } elseif ($dateRange === '30days') {
                $where[] = "u.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            }
        }

        if ($testStatus === 'taken') {
            $where[] = "ta.score IS NOT NULL";
        } elseif ($testStatus === 'not_taken') {
            $where[] = "ta.score IS NULL";
        }

        $whereClause = implode(' AND ', $where);

        $countSql = "SELECT COUNT(DISTINCT u.id) 
                     FROM `users` u 
                     LEFT JOIN (
                         SELECT ta1.user_id, ta1.score
                         FROM test_attempts ta1
                         INNER JOIN (SELECT user_id, MAX(id) as max_id FROM test_attempts WHERE status = 'completed' GROUP BY user_id) ta2 ON ta1.id = ta2.max_id
                     ) ta ON u.id = ta.user_id
                     WHERE {$whereClause}";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $totalCandidates = (int) $countStmt->fetchColumn();
        $totalPages = max(1, (int)ceil($totalCandidates / $perPage));

        $sql = "SELECT u.id, u.full_name, u.email, u.mobile, u.whatsapp, u.created_at, u.candidate_type, u.source,
                ta.id as attempt_id, ta.score as test_score, ta.total_questions as test_total, ta.percentage as test_percentage,
                ta.passed as test_passed, ta.set_name
                FROM `users` u
                LEFT JOIN (
                    SELECT ta1.id, ta1.user_id, ta1.score, ta1.total_questions, ta1.percentage, ta1.passed,
                           COALESCE(s.set_code, s.name, 'Set A') as set_name
                    FROM test_attempts ta1
                    LEFT JOIN aptitude_sets s ON ta1.set_id = s.id
                    INNER JOIN (SELECT user_id, MAX(id) as max_id FROM test_attempts WHERE status = 'completed' GROUP BY user_id) ta2 ON ta1.id = ta2.max_id
                ) ta ON u.id = ta.user_id
                WHERE {$whereClause}
                ORDER BY u.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $candidates = $stmt->fetchAll();

        // Stat metrics
        $statTotal = (int) $db->query("SELECT COUNT(*) FROM `users` WHERE `role` = 'candidate' AND `candidate_type` = 'aptitude_only' AND `is_archived` = 0")->fetchColumn();
        $statTaken = (int) $db->query("SELECT COUNT(DISTINCT u.id) FROM `users` u JOIN `test_attempts` ta ON u.id = ta.user_id WHERE u.role = 'candidate' AND u.candidate_type = 'aptitude_only' AND ta.status = 'completed' AND u.is_archived = 0")->fetchColumn();

        $viewTitle = "Aptitude Candidates";
        $pageTitle = "Aptitude Test Candidates | Realty Smartz Pathshala";

        require APP_PATH . '/views/admin/aptitude_candidates.php';
    }

    /**
     * Convert Aptitude Candidate to Full Onboarding (POST /admin/aptitude-candidates/{id}/convert)
     */
    public function initiateOnboarding(int $id): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE `users` SET `candidate_type` = 'onboarding', `updated_at` = NOW() WHERE `id` = :id");
        $stmt->execute([':id' => $id]);

        AuditLog::log('converted_to_onboarding', 'users', $id, null, [
            'admin_id' => auth()['id'],
            'converted_at' => date('Y-m-d H:i:s')
        ], auth()['id']);

        // Sync with Google Sheets
        try {
            \App\Helpers\GoogleSheetsService::syncCandidate($id);
        } catch (\Throwable $e) {
            error_log("Google Sheets sync failed on convert: " . $e->getMessage());
        }

        flash('success', 'Candidate successfully upgraded to full onboarding profile!');
        redirect('/admin/candidates/' . $id);
    }

    /**
     * QR Code Management Section (/admin/qr-codes)
     */
    public function qrCodes(): void {
        $onboardingUrl = \App\Helpers\QrCodeHelper::getOnboardingUrl();
        $aptitudeUrl = \App\Helpers\QrCodeHelper::getAptitudeUrl();

        $onboardingSvg = \App\Helpers\QrCodeHelper::getSvg($onboardingUrl, 6, 6, '#1F5C4A');
        $aptitudeSvg = \App\Helpers\QrCodeHelper::getSvg($aptitudeUrl, 6, 6, '#1F5C4A');

        $viewTitle = "QR Codes";
        $pageTitle = "QR Code Management | Realty Smartz Pathshala";

        require APP_PATH . '/views/admin/qr_codes.php';
    }

    /**
     * Download QR Code as PNG (/admin/qr-codes/download/{type})
     */
    public function downloadQrCode(string $type): void {
        $targetUrl = ($type === 'aptitude') 
            ? \App\Helpers\QrCodeHelper::getAptitudeUrl() 
            : \App\Helpers\QrCodeHelper::getOnboardingUrl();

        $filename = ($type === 'aptitude') ? 'Realty_Smartz_Aptitude_QR.png' : 'Realty_Smartz_Onboarding_QR.png';

        $pngData = \App\Helpers\QrCodeHelper::getPng($targetUrl, 10, 10, '#1F5C4A');

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pngData));
        header('Cache-Control: public, max-age=86400');
        echo $pngData;
        exit;
    }

    private function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               (!empty($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json')) ||
               (isset($_POST['is_ajax']) && $_POST['is_ajax'] === '1');
    }
}

