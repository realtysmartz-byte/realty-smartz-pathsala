<?php
namespace App\Models;

use App\Config\Database;
use Throwable;

class AuditLog {
    /**
     * Record an audit log entry
     */
    public static function log(string $action, string $entityType, ?int $entityId = null, ?array $oldValues = null, ?array $newValues = null, ?int $userId = null): void {
        try {
            $db = Database::getConnection();
            $currentUserId = $userId ?? ($_SESSION['user']['id'] ?? null);
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250);

            $stmt = $db->prepare("INSERT INTO `audit_logs`
                (`user_id`, `action`, `entity_type`, `entity_id`, `ip_address`, `user_agent`, `old_values`, `new_values`, `created_at`)
                VALUES (:uid, :act, :etype, :eid, :ip, :ua, :old, :new, NOW())");

            $stmt->execute([
                ':uid'   => $currentUserId,
                ':act'   => $action,
                ':etype' => $entityType,
                ':eid'   => $entityId,
                ':ip'    => $ip,
                ':ua'    => $ua,
                ':old'   => $oldValues ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
                ':new'   => $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null
            ]);
        } catch (Throwable $e) {
            // Fail silently so auditing never disrupts business operations
            @file_put_contents(STORAGE_PATH . '/logs/audit_error.log', date('Y-m-d H:i:s') . ' ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
        }
    }
}
