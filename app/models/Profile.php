<?php
namespace App\Models;

use App\Config\Database;
use App\Helpers\CryptoHelper;
use PDO;

class Profile {
    /**
     * Get complete candidate profile with related records
     */
    public static function getFullProfile(int $userId): array {
        $db = Database::getConnection();

        // 1. User core
        $stmtUser = $db->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
        $stmtUser->execute([':id' => $userId]);
        $user = $stmtUser->fetch();
        if (!$user) return [];

        // 2. Profile table
        $stmtProfile = $db->prepare("SELECT * FROM `profiles` WHERE `user_id` = :uid LIMIT 1");
        $stmtProfile->execute([':uid' => $userId]);
        $profile = $stmtProfile->fetch() ?: [];

        // Decrypt Aadhaar and format masked preview
        $rawAadhaarEnc = $profile['aadhaar_enc'] ?? ($profile['aadhaar_encrypted'] ?? null);
        if (!empty($rawAadhaarEnc)) {
            $profile['aadhaar_decrypted'] = CryptoHelper::decrypt($rawAadhaarEnc);
            $profile['aadhaar_masked'] = CryptoHelper::maskAadhaar($profile['aadhaar_decrypted']);
        } else {
            $profile['aadhaar_decrypted'] = '';
            $profile['aadhaar_masked'] = !empty($profile['aadhaar_last4']) ? ('XXXX XXXX ' . $profile['aadhaar_last4']) : '';
        }

        // Decrypt PAN and format masked preview
        $rawPanEnc = $profile['pan_enc'] ?? null;
        if (!empty($rawPanEnc)) {
            $profile['pan_decrypted'] = CryptoHelper::decrypt($rawPanEnc);
            $profile['pan_masked'] = CryptoHelper::maskPan($profile['pan_decrypted']);
        } elseif (!empty($profile['pan_number'])) {
            $profile['pan_decrypted'] = $profile['pan_number'];
            $profile['pan_masked'] = CryptoHelper::maskPan($profile['pan_number']);
        } else {
            $profile['pan_decrypted'] = '';
            $profile['pan_masked'] = !empty($profile['pan_last4']) ? ('XXXXX' . $profile['pan_last4']) : '';
        }

        // Decode Social Links JSON
        if (!empty($profile['social_links'])) {
            $profile['social'] = is_string($profile['social_links']) ? (json_decode($profile['social_links'], true) ?: []) : $profile['social_links'];
        } else {
            $profile['social'] = [];
        }

        // 3. Education
        $stmtEdu = $db->prepare("SELECT * FROM `education` WHERE `user_id` = :uid ORDER BY `year_of_passing` DESC");
        $stmtEdu->execute([':uid' => $userId]);
        $education = $stmtEdu->fetchAll();

        // 4. Experience
        $stmtExp = $db->prepare("SELECT * FROM `experience` WHERE `user_id` = :uid ORDER BY `id` DESC");
        $stmtExp->execute([':uid' => $userId]);
        $experience = $stmtExp->fetchAll();

        // 5. Bank
        $stmtBank = $db->prepare("SELECT * FROM `bank_details` WHERE `user_id` = :uid LIMIT 1");
        $stmtBank->execute([':uid' => $userId]);
        $bank = $stmtBank->fetch() ?: [];
        if (!empty($bank['account_number_encrypted'])) {
            $bank['account_number_decrypted'] = CryptoHelper::decrypt($bank['account_number_encrypted']);
        } else {
            $bank['account_number_decrypted'] = '';
        }

        // 6. Emergency Contact
        $stmtEmg = $db->prepare("SELECT * FROM `emergency_contacts` WHERE `user_id` = :uid LIMIT 1");
        $stmtEmg->execute([':uid' => $userId]);
        $emergency = $stmtEmg->fetch() ?: [];

        // 7. Active Documents with Full Version History
        $stmtDocs = $db->prepare("SELECT * FROM `documents` WHERE `user_id` = :uid AND `is_archived` = 0 ORDER BY `uploaded_at` DESC");
        $stmtDocs->execute([':uid' => $userId]);
        $documents = $stmtDocs->fetchAll();

        // Fetch past versions
        $stmtVers = $db->prepare("SELECT * FROM `document_versions` WHERE `user_id` = :uid ORDER BY `version` DESC");
        $stmtVers->execute([':uid' => $userId]);
        $allVers = $stmtVers->fetchAll();
        $versByDocId = [];
        foreach ($allVers as $v) {
            $versByDocId[$v['document_id']][] = $v;
        }

        // Index documents by type and attach versions
        $docsByType = [];
        foreach ($documents as &$doc) {
            $doc['versions'] = $versByDocId[$doc['id']] ?? [];
            $docsByType[$doc['document_type']] = $doc;
        }
        unset($doc);

        // 8. Aptitude test attempt
        $stmtAttempt = $db->prepare("SELECT a.*, s.set_code, s.name as set_name 
            FROM `test_attempts` a 
            LEFT JOIN `aptitude_sets` s ON a.set_id = s.id 
            WHERE a.user_id = :uid 
            ORDER BY a.id DESC LIMIT 1");
        $stmtAttempt->execute([':uid' => $userId]);
        $testAttempt = $stmtAttempt->fetch() ?: null;

        if ($testAttempt) {
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
            $stmtCat->execute([':aid' => $testAttempt['id']]);
            $testAttempt['categories'] = $stmtCat->fetchAll();
        }

        return array_merge($user, [
            'profile'      => $profile,
            'education'    => $education,
            'experience'   => $experience,
            'bank'         => $bank,
            'emergency'    => $emergency,
            'documents'    => $documents,
            'docs_by_type' => $docsByType,
            'test_attempt' => $testAttempt
        ]);
    }

    /**
     * Save Personal Information (Section A)
     */
    public static function savePersonal(int $userId, array $data): void {
        $db = Database::getConnection();

        // Update full name in users table if provided
        if (!empty($data['full_name'])) {
            $stmtU = $db->prepare("UPDATE `users` SET `full_name` = :name WHERE `id` = :id");
            $stmtU->execute([':name' => trim($data['full_name']), ':id' => $userId]);
        }

        $sameAsPerm = !empty($data['same_as_permanent']) ? 1 : 0;
        $currAddr = $sameAsPerm ? ($data['permanent_address'] ?? '') : ($data['current_address'] ?? '');

        $sql = "UPDATE `profiles` SET
            `father_name` = :father,
            `mother_name` = :mother,
            `dob` = :dob,
            `gender` = :gender,
            `marital_status` = :marital,
            `blood_group` = :blood,
            `nationality` = :nat,
            `permanent_address` = :perm,
            `current_address` = :curr,
            `same_as_permanent` = :same,
            `city` = :city,
            `state` = :state,
            `pincode` = :pin,
            `updated_at` = NOW()
            WHERE `user_id` = :uid";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':father'  => $data['father_name'] ?? null,
            ':mother'  => $data['mother_name'] ?? null,
            ':dob'     => !empty($data['dob']) ? $data['dob'] : null,
            ':gender'  => $data['gender'] ?? null,
            ':marital' => $data['marital_status'] ?? null,
            ':blood'   => $data['blood_group'] ?? null,
            ':nat'     => $data['nationality'] ?? 'Indian',
            ':perm'    => $data['permanent_address'] ?? null,
            ':curr'    => $currAddr,
            ':same'    => $sameAsPerm,
            ':city'    => $data['city'] ?? null,
            ':state'   => $data['state'] ?? null,
            ':pin'     => $data['pincode'] ?? null,
            ':uid'     => $userId
        ]);

        self::recalculateCompletion($userId);
    }

    /**
     * Save Identity Information (Section B)
     */
    public static function saveIdentity(int $userId, array $data): void {
        $db = Database::getConnection();

        $aadhaarRaw = preg_replace('/\D/', '', $data['aadhaar_number'] ?? '');
        $aadhaarEncrypted = null;
        $aadhaarLast4 = null;
        if (!empty($aadhaarRaw)) {
            $aadhaarEncrypted = CryptoHelper::encrypt($aadhaarRaw);
            $aadhaarLast4 = substr($aadhaarRaw, -4);
        }

        $pan = !empty($data['pan_number']) ? strtoupper(trim($data['pan_number'])) : null;
        $passport = !empty($data['passport_number']) ? strtoupper(trim($data['passport_number'])) : null;

        $sql = "UPDATE `profiles` SET
            `pan_number` = :pan,
            `passport_number` = :pass";

        $params = [
            ':pan'  => $pan,
            ':pass' => $passport,
            ':uid'  => $userId
        ];

        if ($aadhaarEncrypted !== null) {
            $sql .= ", `aadhaar_encrypted` = :a_enc, `aadhaar_last4` = :a_l4";
            $params[':a_enc'] = $aadhaarEncrypted;
            $params[':a_l4']  = $aadhaarLast4;
        }

        $sql .= ", `updated_at` = NOW() WHERE `user_id` = :uid";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        self::recalculateCompletion($userId);
    }

    /**
     * Save Education Record (Section C)
     */
    public static function saveEducation(int $userId, array $data): void {
        $db = Database::getConnection();

        // Check if row already exists for user
        $stmtCheck = $db->prepare("SELECT id FROM `education` WHERE `user_id` = :uid LIMIT 1");
        $stmtCheck->execute([':uid' => $userId]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            $stmt = $db->prepare("UPDATE `education` SET
                `highest_qualification` = :qual,
                `institute` = :inst,
                `year_of_passing` = :year,
                `percentage_cgpa` = :score,
                `updated_at` = NOW()
                WHERE `id` = :id");
            $stmt->execute([
                ':qual'  => $data['highest_qualification'],
                ':inst'  => $data['institute'],
                ':year'  => (int)$data['year_of_passing'],
                ':score' => $data['percentage_cgpa'],
                ':id'    => $existing['id']
            ]);
        } else {
            $stmt = $db->prepare("INSERT INTO `education`
                (`user_id`, `highest_qualification`, `institute`, `year_of_passing`, `percentage_cgpa`, `created_at`)
                VALUES (:uid, :qual, :inst, :year, :score, NOW())");
            $stmt->execute([
                ':uid'   => $userId,
                ':qual'  => $data['highest_qualification'],
                ':inst'  => $data['institute'],
                ':year'  => (int)$data['year_of_passing'],
                ':score' => $data['percentage_cgpa']
            ]);
        }

        self::recalculateCompletion($userId);
    }

    /**
     * Save Work Experience Record (Section D)
     */
    public static function saveExperience(int $userId, array $data): void {
        $db = Database::getConnection();

        $isFresher = !empty($data['is_fresher']) ? 1 : 0;

        $stmtCheck = $db->prepare("SELECT id FROM `experience` WHERE `user_id` = :uid LIMIT 1");
        $stmtCheck->execute([':uid' => $userId]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            $stmt = $db->prepare("UPDATE `experience` SET
                `is_fresher` = :fresher,
                `company` = :comp,
                `designation` = :desig,
                `joining_date` = :join,
                `last_working_date` = :last,
                `last_salary` = :sal,
                `updated_at` = NOW()
                WHERE `id` = :id");
            $stmt->execute([
                ':fresher' => $isFresher,
                ':comp'    => $isFresher ? null : ($data['company'] ?? null),
                ':desig'   => $isFresher ? null : ($data['designation'] ?? null),
                ':join'    => ($isFresher || empty($data['joining_date'])) ? null : $data['joining_date'],
                ':last'    => ($isFresher || empty($data['last_working_date'])) ? null : $data['last_working_date'],
                ':sal'     => $isFresher ? null : ($data['last_salary'] ?? null),
                ':id'      => $existing['id']
            ]);
        } else {
            $stmt = $db->prepare("INSERT INTO `experience`
                (`user_id`, `is_fresher`, `company`, `designation`, `joining_date`, `last_working_date`, `last_salary`, `created_at`)
                VALUES (:uid, :fresher, :comp, :desig, :join, :last, :sal, NOW())");
            $stmt->execute([
                ':uid'     => $userId,
                ':fresher' => $isFresher,
                ':comp'    => $isFresher ? null : ($data['company'] ?? null),
                ':desig'   => $isFresher ? null : ($data['designation'] ?? null),
                ':join'    => ($isFresher || empty($data['joining_date'])) ? null : $data['joining_date'],
                ':last'    => ($isFresher || empty($data['last_working_date'])) ? null : $data['last_working_date'],
                ':sal'     => $isFresher ? null : ($data['last_salary'] ?? null)
            ]);
        }

        self::recalculateCompletion($userId);
    }

    /**
     * Save Bank Details (Section E)
     */
    public static function saveBank(int $userId, array $data): void {
        $db = Database::getConnection();

        $accountRaw = preg_replace('/\D/', '', $data['account_number'] ?? '');
        $accEncrypted = CryptoHelper::encrypt($accountRaw);
        $accLast4 = substr($accountRaw, -4);
        $ifsc = strtoupper(trim($data['ifsc_code'] ?? ''));

        $stmtCheck = $db->prepare("SELECT id FROM `bank_details` WHERE `user_id` = :uid LIMIT 1");
        $stmtCheck->execute([':uid' => $userId]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            $stmt = $db->prepare("UPDATE `bank_details` SET
                `account_holder_name` = :holder,
                `bank_name` = :bname,
                `account_number_encrypted` = :acc_enc,
                `account_number_last4` = :acc_l4,
                `ifsc_code` = :ifsc,
                `updated_at` = NOW()
                WHERE `id` = :id");
            $stmt->execute([
                ':holder'  => trim($data['account_holder_name'] ?? ''),
                ':bname'   => trim($data['bank_name'] ?? ''),
                ':acc_enc' => $accEncrypted,
                ':acc_l4'  => $accLast4,
                ':ifsc'    => $ifsc,
                ':id'      => $existing['id']
            ]);
        } else {
            $stmt = $db->prepare("INSERT INTO `bank_details`
                (`user_id`, `account_holder_name`, `bank_name`, `account_number_encrypted`, `account_number_last4`, `ifsc_code`, `created_at`)
                VALUES (:uid, :holder, :bname, :acc_enc, :acc_l4, :ifsc, NOW())");
            $stmt->execute([
                ':uid'     => $userId,
                ':holder'  => trim($data['account_holder_name'] ?? ''),
                ':bname'   => trim($data['bank_name'] ?? ''),
                ':acc_enc' => $accEncrypted,
                ':acc_l4'  => $accLast4,
                ':ifsc'    => $ifsc
            ]);
        }

        self::recalculateCompletion($userId);
    }

    /**
     * Save Emergency Contact (Section F)
     */
    public static function saveEmergency(int $userId, array $data): void {
        $db = Database::getConnection();

        $stmtCheck = $db->prepare("SELECT id FROM `emergency_contacts` WHERE `user_id` = :uid LIMIT 1");
        $stmtCheck->execute([':uid' => $userId]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            $stmt = $db->prepare("UPDATE `emergency_contacts` SET
                `contact_name` = :cname,
                `relationship` = :rel,
                `mobile` = :mob,
                `alternate_mobile` = :alt,
                `address` = :addr,
                `updated_at` = NOW()
                WHERE `id` = :id");
            $stmt->execute([
                ':cname' => trim($data['contact_name'] ?? ''),
                ':rel'   => trim($data['relationship'] ?? ''),
                ':mob'   => preg_replace('/\D/', '', $data['mobile'] ?? ''),
                ':alt'   => preg_replace('/\D/', '', $data['alternate_mobile'] ?? ''),
                ':addr'  => trim($data['address'] ?? ''),
                ':id'    => $existing['id']
            ]);
        } else {
            $stmt = $db->prepare("INSERT INTO `emergency_contacts`
                (`user_id`, `contact_name`, `relationship`, `mobile`, `alternate_mobile`, `address`, `created_at`)
                VALUES (:uid, :cname, :rel, :mob, :alt, :addr, NOW())");
            $stmt->execute([
                ':uid'   => $userId,
                ':cname' => trim($data['contact_name'] ?? ''),
                ':rel'   => trim($data['relationship'] ?? ''),
                ':mob'   => preg_replace('/\D/', '', $data['mobile'] ?? ''),
                ':alt'   => preg_replace('/\D/', '', $data['alternate_mobile'] ?? ''),
                ':addr'  => trim($data['address'] ?? '')
            ]);
        }

        self::recalculateCompletion($userId);
    }

    /**
     * Save Declaration & Signature (Section G)
     */
    public static function saveDeclaration(int $userId, array $data): void {
        $db = Database::getConnection();

        $accepted = !empty($data['declaration_accepted']) ? 1 : 0;
        $sigType = $data['signature_type'] ?? 'typed';
        $sigData = $data['signature_data'] ?? null;

        $stmt = $db->prepare("UPDATE `profiles` SET
            `declaration_accepted` = :acc,
            `declaration_date` = CURDATE(),
            `signature_type` = :stype,
            `signature_data` = :sdata,
            `updated_at` = NOW()
            WHERE `user_id` = :uid");

        $stmt->execute([
            ':acc'   => $accepted,
            ':stype' => $sigType,
            ':sdata' => $sigData,
            ':uid'   => $userId
        ]);

        self::recalculateCompletion($userId);
    }

    /**
     * Lock and submit profile for review
     */
    public static function submitForReview(int $userId): array {
        $data = self::getFullProfile($userId);
        $completion = $data['profile']['completion_percentage'] ?? 0;

        if ($completion < 90) {
            return ['success' => false, 'error' => "Please complete all mandatory sections before submitting (currently {$completion}% complete)."];
        }

        $db = Database::getConnection();
        // Lock profile
        $db->prepare("UPDATE `profiles` SET `is_locked` = 1, `updated_at` = NOW() WHERE `user_id` = :uid")->execute([':uid' => $userId]);

        // Update user status
        User::updateStatus($userId, 'under_review', 'Candidate submitted profile and all documents for review.', $userId);

        // Notify HR
        $hrEmail = config('MAIL_USERNAME', 'hr@realtysmartzpathshala.in');
        \App\Helpers\Mailer::sendHrNewSubmission($hrEmail, $data['full_name'], $userId);

        return ['success' => true];
    }

    /**
     * Recalculate completion percentage across all sections
     */
    public static function recalculateCompletion(int $userId): int {
        $data = self::getFullProfile($userId);
        if (empty($data)) return 0;

        $p = $data['profile'] ?? [];
        $points = 0;
        $total = 4; // 4 Core Steps: A (Basic), B (Social), C (Docs & Identity), D (Declaration)

        // Step A: Basic Details
        if (!empty($p['father_name']) && !empty($p['dob']) && !empty($p['gender']) && (!empty($p['address']) || !empty($p['current_address']))) {
            $points++;
        }

        // Step B: Social Links (LinkedIn or Instagram is required)
        $socialLinks = !empty($p['social_links']) ? (is_string($p['social_links']) ? json_decode($p['social_links'], true) : $p['social_links']) : [];
        if (!empty($socialLinks['linkedin']) || !empty($socialLinks['instagram'])) {
            $points++;
        }

        // Step C: Documents & Identity (Aadhaar + PAN)
        $hasAadhaar = !empty($p['aadhaar_last4']) || !empty($p['aadhaar_enc']);
        $hasPan = !empty($p['pan_last4']) || !empty($p['pan_enc']) || !empty($p['pan_number']);
        if ($hasAadhaar && $hasPan) {
            $points++;
        }

        // Step D: Declaration & Signature
        if (!empty($p['declaration_accepted']) && !empty($p['signature_data'])) {
            $points++;
        }

        $percentage = (int) round(($points / $total) * 100);

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE `profiles` SET `completion_percentage` = :pct WHERE `user_id` = :uid");
        $stmt->execute([':pct' => $percentage, ':uid' => $userId]);

        return $percentage;
    }

    /**
     * Get precise status and lock state for the 4-step onboarding pipeline
     */
    public static function getOnboardingStepStatus(int $userId): array {
        $data = self::getFullProfile($userId);
        $p = $data['profile'] ?? [];
        $docs = $data['docs_by_type'] ?? [];
        $emg = $data['emergency'] ?? [];

        // Step 1: Basic Details (Personal & Identity)
        $hasBasic = !empty($data['full_name']) 
            && !empty($p['father_name']) 
            && !empty($p['dob']) 
            && !empty($p['gender']) 
            && (!empty($p['address']) || !empty($p['current_address']))
            && !empty($p['city']) 
            && !empty($p['state']) 
            && !empty($p['pincode']);

        // Step 2: Social Links & Professional Profiles (LinkedIn or Instagram is required)
        $socialData = $p['social'] ?? [];
        if (empty($socialData) && !empty($p['social_links'])) {
            $socialData = is_string($p['social_links']) ? json_decode($p['social_links'], true) : $p['social_links'];
        }
        $hasSocialLink = !empty(trim((string)($socialData['linkedin'] ?? ''))) || !empty(trim((string)($socialData['instagram'] ?? '')));
        $hasSocial = $hasBasic && $hasSocialLink;

        // Step 3: Documents & KYC (Aadhaar & PAN numbers + uploaded proofs + emergency contact)
        $hasAadhaar = !empty($p['aadhaar_last4']) || !empty($p['aadhaar_enc']) || !empty($p['aadhaar_encrypted']);
        $hasPan = !empty($p['pan_last4']) || !empty($p['pan_enc']) || !empty($p['pan_number']);
        $hasAadhaarDoc = !empty($docs['aadhaar']) || !empty($docs['aadhaar_front']);
        $hasPanDoc = !empty($docs['pan_card']);
        $hasEmergency = !empty($emg['contact_name']) && !empty($emg['mobile']);
        $hasDocs = $hasBasic && $hasAadhaar && $hasPan && $hasAadhaarDoc && $hasPanDoc && $hasEmergency;

        // Step 4: Final Declaration & Signature
        $hasSubmitted = !empty($p['declaration_accepted']) || !empty($p['is_locked']);

        // Sequential step locking: determine max allowed step
        $maxAllowed = 'step_a';
        if ($hasBasic) {
            $maxAllowed = 'step_b';
            if ($hasSocial) {
                $maxAllowed = 'step_c';
            }
            if ($hasDocs) {
                $maxAllowed = 'step_d';
            }
        }

        return [
            'step_a_completed' => (bool)$hasBasic,
            'step_b_completed' => (bool)$hasSocial,
            'step_c_completed' => (bool)$hasDocs,
            'step_d_completed' => (bool)$hasSubmitted,
            'max_allowed_step' => $maxAllowed
        ];
    }

    /**
     * 4-Step Onboarding Wizard - Step A (Basic Details)
     */
    public static function saveStepA(int $userId, array $data): void {
        $db = Database::getConnection();

        $fullName = trim($data['full_name'] ?? '');
        $fatherName = trim($data['father_name'] ?? '');
        $whatsapp = preg_replace('/\D/', '', $data['whatsapp'] ?? '');
        $dob = !empty($data['dob']) ? trim($data['dob']) : null;
        $gender = strtolower(trim($data['gender'] ?? ''));
        $address = trim($data['address'] ?? ($data['current_address'] ?? ''));
        $city = trim($data['city'] ?? '');
        $state = trim($data['state'] ?? '');
        $pincode = trim($data['pincode'] ?? '');

        // Update users table
        $stmtU = $db->prepare("UPDATE `users` SET 
            `full_name` = CASE WHEN :name != '' THEN :name2 ELSE `full_name` END,
            `father_name` = CASE WHEN :father != '' THEN :father2 ELSE `father_name` END,
            `whatsapp` = :wa
            WHERE `id` = :uid");
        $stmtU->execute([
            ':name'    => $fullName,
            ':name2'   => $fullName,
            ':father'  => $fatherName,
            ':father2' => $fatherName,
            ':wa'      => $whatsapp ?: null,
            ':uid'     => $userId
        ]);

        // Update profiles table
        $stmtP = $db->prepare("UPDATE `profiles` SET
            `father_name` = CASE WHEN :father != '' THEN :father2 ELSE `father_name` END,
            `whatsapp` = :wa,
            `dob` = :dob,
            `gender` = :gender,
            `address` = :addr,
            `current_address` = :curr,
            `permanent_address` = :perm,
            `city` = :city,
            `state` = :state,
            `pincode` = :pin,
            `updated_at` = NOW()
            WHERE `user_id` = :uid");
        $stmtP->execute([
            ':father'  => $fatherName,
            ':father2' => $fatherName,
            ':wa'      => $whatsapp ?: null,
            ':dob'     => $dob,
            ':gender'  => in_array($gender, ['male', 'female', 'other'], true) ? $gender : null,
            ':addr'    => $address ?: null,
            ':curr'    => $address ?: null,
            ':perm'    => $address ?: null,
            ':city'    => $city ?: null,
            ':state'   => $state ?: null,
            ':pin'     => $pincode ?: null,
            ':uid'     => $userId
        ]);

        self::recalculateCompletion($userId);
    }

    /**
     * 4-Step Onboarding Wizard - Step B (Social Links)
     */
    public static function saveStepB(int $userId, array $data): void {
        $db = Database::getConnection();

        $linkedin = trim($data['linkedin'] ?? '');
        $instagram = trim($data['instagram'] ?? '');

        if (empty($linkedin) && empty($instagram)) {
            throw new Exception("Please provide at least one social profile link (LinkedIn or Instagram).");
        }

        if (!empty($linkedin) && !preg_match('#^(https?:\/\/)?([a-zA-Z0-9_\-]+\.)?linkedin\.com\/#i', $linkedin)) {
            throw new Exception("Please provide a valid LinkedIn profile URL (e.g. https://www.linkedin.com/in/username).");
        }

        $social = [
            'linkedin'  => $linkedin,
            'instagram' => $instagram,
            'facebook'  => trim($data['facebook'] ?? ''),
            'twitter'   => trim($data['twitter'] ?? ($data['x'] ?? '')),
            'youtube'   => trim($data['youtube'] ?? ''),
            'portfolio' => trim($data['portfolio'] ?? '')
        ];

        $stmt = $db->prepare("UPDATE `profiles` SET `social_links` = :links, `updated_at` = NOW() WHERE `user_id` = :uid");
        $stmt->execute([
            ':links' => json_encode($social, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ':uid'   => $userId
        ]);

        self::recalculateCompletion($userId);
    }

    /**
     * 4-Step Onboarding Wizard - Step C (Documents & Identity)
     */
    public static function saveStepC(int $userId, array $data): void {
        $db = Database::getConnection();

        $rawAadhaar = preg_replace('/\D/', '', $data['aadhaar_number'] ?? '');
        $rawPan = strtoupper(trim($data['pan_number'] ?? ''));

        $sql = "UPDATE `profiles` SET `updated_at` = NOW()";
        $params = [':uid' => $userId];

        if (!empty($rawAadhaar) && strlen($rawAadhaar) === 12) {
            $aadhaarEnc = CryptoHelper::encrypt($rawAadhaar);
            $aadhaarLast4 = substr($rawAadhaar, -4);
            $sql .= ", `aadhaar_enc` = :a_enc, `aadhaar_encrypted` = :a_enc2, `aadhaar_last4` = :a_l4";
            $params[':a_enc'] = $aadhaarEnc;
            $params[':a_enc2'] = $aadhaarEnc;
            $params[':a_l4'] = $aadhaarLast4;
        }

        if (!empty($rawPan) && preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $rawPan)) {
            $panEnc = CryptoHelper::encrypt($rawPan);
            $panLast4 = substr($rawPan, -4);
            $sql .= ", `pan_enc` = :p_enc, `pan_number` = :p_raw, `pan_last4` = :p_l4";
            $params[':p_enc'] = $panEnc;
            $params[':p_raw'] = $rawPan;
            $params[':p_l4'] = $panLast4;
        }

        $sql .= " WHERE `user_id` = :uid";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        // Emergency Contacts
        $c1Name = trim($data['emergency_name_1'] ?? ($data['emergency_contact_name'] ?? ''));
        $c1Rel = trim($data['emergency_rel_1'] ?? ($data['emergency_relationship'] ?? ''));
        $c1Phone = trim($data['emergency_phone_1'] ?? ($data['emergency_mobile'] ?? ''));
        $c2Name = trim($data['emergency_name_2'] ?? '');
        $c2Rel = trim($data['emergency_rel_2'] ?? '');
        $c2Phone = trim($data['emergency_phone_2'] ?? '');

        if (!empty($c1Name) && !empty($c1Phone)) {
            $c2Combined = (!empty($c2Name) && !empty($c2Phone)) ? "{$c2Phone} ({$c2Name} - {$c2Rel})" : null;
            $stmtEmg = $db->prepare("INSERT INTO `emergency_contacts` 
                (`user_id`, `contact_name`, `relationship`, `mobile`, `alternate_mobile`, `created_at`, `updated_at`)
                VALUES (:uid, :c1name, :c1rel, :c1phone, :c2phone, NOW(), NOW())
                ON DUPLICATE KEY UPDATE 
                `contact_name` = :c1name2,
                `relationship` = :c1rel2,
                `mobile` = :c1phone2,
                `alternate_mobile` = :c2phone2,
                `updated_at` = NOW()");
            $stmtEmg->execute([
                ':uid'      => $userId,
                ':c1name'   => $c1Name,
                ':c1rel'    => $c1Rel,
                ':c1phone'  => $c1Phone,
                ':c2phone'  => $c2Combined,
                ':c1name2'  => $c1Name,
                ':c1rel2'   => $c1Rel,
                ':c1phone2' => $c1Phone,
                ':c2phone2' => $c2Combined
            ]);
        }

        self::recalculateCompletion($userId);
    }

    /**
     * 4-Step Onboarding Wizard - Step D (Submit & Lock)
     */
    public static function submitStepD(int $userId, array $data): array {
        $db = Database::getConnection();
        $full = self::getFullProfile($userId);
        $p = $full['profile'] ?? [];
        $docs = $full['docs_by_type'] ?? [];

        // Check required documents: Aadhaar and PAN are required
        $hasAadhaar = !empty($p['aadhaar_last4']) || !empty($p['aadhaar_enc']);
        $hasPan = !empty($p['pan_last4']) || !empty($p['pan_enc']) || !empty($p['pan_number']);

        if (!$hasAadhaar) {
            return ['success' => false, 'error' => 'Please provide a valid 12-digit Aadhaar number.'];
        }
        if (!$hasPan) {
            return ['success' => false, 'error' => 'Please provide a valid 10-character PAN number.'];
        }

        // Check document uploads: Aadhaar and PAN uploads required
        $hasAadhaarDoc = !empty($docs['aadhaar']) || !empty($docs['aadhaar_front']);
        $hasPanDoc = !empty($docs['pan_card']);

        if (!$hasAadhaarDoc) {
            return ['success' => false, 'error' => 'Please upload your Aadhaar Card (front side or combined document).'];
        }
        if (!$hasPanDoc) {
            return ['success' => false, 'error' => 'Please upload your PAN Card document.'];
        }

        if (empty($data['declaration_accepted'])) {
            return ['success' => false, 'error' => 'You must accept the DPDP data declaration and consent statement to proceed.'];
        }

        $sigType = in_array($data['signature_type'] ?? '', ['typed', 'drawn'], true) ? $data['signature_type'] : 'typed';
        $sigData = trim($data['signature_data'] ?? '');
        if (empty($sigData)) {
            return ['success' => false, 'error' => 'Please provide your digital signature (typed name or drawn signature).'];
        }

        // Lock profile and set completion to 100%
        $stmtP = $db->prepare("UPDATE `profiles` SET
            `declaration_accepted` = 1,
            `declaration_date` = NOW(),
            `signature_type` = :stype,
            `signature_data` = :sdata,
            `completion_percentage` = 100,
            `is_locked` = 1,
            `updated_at` = NOW()
            WHERE `user_id` = :uid");
        $stmtP->execute([
            ':stype' => $sigType,
            ':sdata' => $sigData,
            ':uid'   => $userId
        ]);

        // Update status to 'documents_submitted'
        User::updateStatus($userId, 'documents_submitted', 'Candidate completed 4-step onboarding wizard and submitted documents.', $userId);

        AuditLog::log('onboarding_submitted', 'profiles', $p['id'] ?? null, null, [
            'signature_type' => $sigType,
            'timestamp'      => date('Y-m-d H:i:s')
        ], $userId);

        return ['success' => true];
    }
}
