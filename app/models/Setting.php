<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Setting {
    private static array $cache = [];

    public static function get(string $key, $default = null) {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT `setting_value` FROM `settings` WHERE `setting_key` = :key LIMIT 1");
            $stmt->execute([':key' => $key]);
            $val = $stmt->fetchColumn();

            if ($val !== false && $val !== null) {
                self::$cache[$key] = $val;
                return $val;
            }
        } catch (\Throwable $e) {
            return $default;
        }

        return $default;
    }

    public static function set(string $key, $value, string $group = 'general'): void {
        self::$cache[$key] = $value;
        $db = Database::getConnection();

        $stmt = $db->prepare("INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `updated_at`)
            VALUES (:key, :val, :grp, NOW())
            ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`), `updated_at` = NOW()");

        $stmt->execute([
            ':key' => $key,
            ':val' => (string) $value,
            ':grp' => $group
        ]);
    }

    public static function getAll(): array {
        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT * FROM `settings` ORDER BY `setting_group` ASC, `setting_key` ASC");
            $rows = $stmt->fetchAll();
            $settings = [];
            foreach ($rows as $r) {
                $settings[$r['setting_key']] = $r['setting_value'];
            }
            return $settings;
        } catch (\Throwable $e) {
            return [
                'site_name' => 'Realty Smartz Pathshala',
                'site_tagline' => 'Master the Art of Real Estate',
                'stat_professionals_trained' => '500+',
                'stat_live_projects' => '50+',
                'stat_success_rate' => '4.9',
                'stat_placement_rate' => '98%',
                'stat_years_active' => '10+ Years',
                'stat_avg_deal' => '2.4 Cr',
                'contact_phone' => '+91 99999 99999',
                'contact_email' => 'info@realtysmartzpathshala.in',
                'preloader_text' => 'Preparing Your Success Path...'
            ];
        }
    }
}
