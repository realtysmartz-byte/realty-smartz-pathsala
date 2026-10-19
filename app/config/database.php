<?php
namespace App\Config;

use PDO;
use PDOException;
use RuntimeException;

class Database {
    private static ?PDO $instance = null;

    /**
     * Get singleton PDO connection
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = config('DB_HOST', '127.0.0.1');
            $port = config('DB_PORT', '3306');
            $db   = config('DB_NAME', 'realtysmartz_pathshala');
            $user = config('DB_USER', 'root');
            $pass = config('DB_PASS', '');
            $charset = config('DB_CHARSET', 'utf8mb4');

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE utf8mb4_unicode_ci"
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // If database does not exist or connection failed
                throw new RuntimeException("Database Connection Error: " . $e->getMessage(), (int)$e->getCode(), $e);
            }
        }

        return self::$instance;
    }

    /**
     * Reset connection (useful for testing or reconnecting)
     */
    public static function reset(): void {
        self::$instance = null;
    }
}
