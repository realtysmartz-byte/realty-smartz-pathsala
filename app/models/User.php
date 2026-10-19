<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class User {
    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `users` WHERE `email` = :email LIMIT 1");
        $stmt->execute([':email' => strtolower(trim($email))]);
        return $stmt->fetch() ?: null;
    }

    public static function findByMobile(string $mobile): ?array {
        $db = Database::getConnection();
        $clean = preg_replace('/\D/', '', $mobile);
        $stmt = $db->prepare("SELECT * FROM `users` WHERE `mobile` = :mobile LIMIT 1");
        $stmt->execute([':mobile' => $clean]);
        return $stmt->fetch() ?: null;
    }

    public static function createCandidate(array $data): int {
        $db = Database::getConnection();

        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 11]);
        $cleanMobile = preg_replace('/\D/', '', $data['mobile']);
        $fatherName = trim($data['father_name'] ?? '');

        $stmt = $db->prepare("INSERT INTO `users`
            (`full_name`, `father_name`, `email`, `mobile`, `password_hash`, `role`, `status`, `created_at`)
            VALUES (:name, :father, :email, :mobile, :hash, 'candidate', 'registered', NOW())");

        $stmt->execute([
            ':name'   => trim($data['full_name']),
            ':father' => $fatherName ?: null,
            ':email'  => strtolower(trim($data['email'])),
            ':mobile' => $cleanMobile,
            ':hash'   => $hash
        ]);

        $userId = (int) $db->lastInsertId();

        // Create profile record with father_name
        $stmtProfile = $db->prepare("INSERT INTO `profiles` (`user_id`, `father_name`, `created_at`) VALUES (:uid, :father, NOW())");
        $stmtProfile->execute([
            ':uid'    => $userId,
            ':father' => $fatherName ?: null
        ]);

        AuditLog::log('candidate_registered', 'users', $userId, null, [
            'email'  => $data['email'],
            'name'   => $data['full_name'],
            'mobile' => $cleanMobile
        ], $userId);

        return $userId;
    }

    public static function createVerification(int $userId): array {
        $db = Database::getConnection();

        // Expire any existing pending verifications for this user
        $db->prepare("DELETE FROM `email_verifications` WHERE `user_id` = :uid")->execute([':uid' => $userId]);

        $otp = sprintf('%06d', random_int(100000, 999999));
        $token = bin2hex(random_bytes(32));

        $stmt = $db->prepare("INSERT INTO `email_verifications`
            (`user_id`, `otp`, `token`, `attempts`, `expires_at`, `created_at`)
            VALUES (:uid, :otp, :tok, 0, DATE_ADD(NOW(), INTERVAL 15 MINUTE), NOW())");

        $stmt->execute([
            ':uid' => $userId,
            ':otp' => $otp,
            ':tok' => $token
        ]);

        return [
            'otp' => $otp,
            'token' => $token,
            'expires_at' => date('Y-m-d H:i:s', time() + 900)
        ];
    }

    public static function verifyWithOtp(int $userId, string $otp): array {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM `email_verifications` WHERE `user_id` = :uid AND `expires_at` > NOW() ORDER BY `id` DESC LIMIT 1");
        $stmt->execute([':uid' => $userId]);
        $row = $stmt->fetch();

        if (!$row) {
            return ['success' => false, 'error' => 'Verification code has expired or does not exist. Please request a new OTP.'];
        }

        if ((int)$row['attempts'] >= 5) {
            return ['success' => false, 'error' => 'Maximum verification attempts exceeded. Please request a fresh OTP.'];
        }

        // Increment attempts
        $db->prepare("UPDATE `email_verifications` SET `attempts` = `attempts` + 1 WHERE `id` = :id")->execute([':id' => $row['id']]);

        if (trim($row['otp']) !== trim($otp)) {
            $left = 5 - ((int)$row['attempts'] + 1);
            return ['success' => false, 'error' => "Incorrect OTP. {$left} attempts remaining."];
        }

        // Success: Mark user as verified
        $db->prepare("UPDATE `users` SET `email_verified_at` = NOW(), `status` = CASE WHEN `status` = 'registered' THEN 'email_verified' ELSE `status` END WHERE `id` = :uid")->execute([':uid' => $userId]);
        $db->prepare("DELETE FROM `email_verifications` WHERE `user_id` = :uid")->execute([':uid' => $userId]);

        AuditLog::log('email_verified', 'users', $userId, null, ['method' => 'otp'], $userId);

        return ['success' => true];
    }

    public static function verifyWithToken(string $token): array {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM `email_verifications` WHERE `token` = :tok AND `expires_at` > NOW() LIMIT 1");
        $stmt->execute([':tok' => $token]);
        $row = $stmt->fetch();

        if (!$row) {
            return ['success' => false, 'error' => 'Verification link has expired or is invalid. Please request a new verification email.'];
        }

        $userId = (int)$row['user_id'];
        $db->prepare("UPDATE `users` SET `email_verified_at` = NOW(), `status` = CASE WHEN `status` = 'registered' THEN 'email_verified' ELSE `status` END WHERE `id` = :uid")->execute([':uid' => $userId]);
        $db->prepare("DELETE FROM `email_verifications` WHERE `user_id` = :uid")->execute([':uid' => $userId]);

        AuditLog::log('email_verified', 'users', $userId, null, ['method' => 'token'], $userId);

        return ['success' => true, 'user_id' => $userId];
    }

    public static function updateStatus(int $userId, string $newStatus, ?string $comments = null, ?int $changedBy = null): void {
        $db = Database::getConnection();

        $oldUser = self::findById($userId);
        $oldStatus = $oldUser['status'] ?? 'registered';

        $stmt = $db->prepare("UPDATE `users` SET `status` = :st WHERE `id` = :uid");
        $stmt->execute([':st' => $newStatus, ':uid' => $userId]);

        // Insert into status_history
        $stmtHistory = $db->prepare("INSERT INTO `status_history` (`user_id`, `old_status`, `new_status`, `comments`, `changed_by`, `created_at`) VALUES (:uid, :old, :new, :comm, :by, NOW())");
        $stmtHistory->execute([
            ':uid'  => $userId,
            ':old'  => $oldStatus,
            ':new'  => $newStatus,
            ':comm' => $comments,
            ':by'   => $changedBy
        ]);

        if (!empty($comments)) {
            $stmtComm = $db->prepare("INSERT INTO `admin_comments` (`user_id`, `admin_id`, `comment_type`, `comment_text`, `created_at`) VALUES (:uid, :aid, 'public_to_candidate', :txt, NOW())");
            $stmtComm->execute([
                ':uid' => $userId,
                ':aid' => $changedBy ?? 1,
                ':txt' => $comments
            ]);
        }

        AuditLog::log('status_updated', 'users', $userId, ['status' => $oldStatus], ['status' => $newStatus, 'comments' => $comments], $changedBy);
    }

    public static function softDelete(int $userId, ?int $archivedBy = null): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE `users` SET `is_archived` = 1, `archived_at` = NOW(), `archived_by` = :by WHERE `id` = :uid");
        $stmt->execute([':by' => $archivedBy, ':uid' => $userId]);

        AuditLog::log('candidate_archived', 'users', $userId, null, ['archived' => 1], $archivedBy);
    }

    public static function restore(int $userId, ?int $restoredBy = null): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE `users` SET `is_archived` = 0, `archived_at` = NULL, `archived_by` = NULL WHERE `id` = :uid");
        $stmt->execute([':uid' => $userId]);

        AuditLog::log('candidate_restored', 'users', $userId, null, ['archived' => 0], $restoredBy);
    }
}
