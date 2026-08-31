<?php
/**
 * Roppets Edutainment Production Inc.
 * Dual-Engine Database Connector (PostgreSQL / Neon & MySQL / MariaDB)
 */

// 1. Simple Environment Variables (.env) Loader
(function() {
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '#') continue;
            if (strpos($line, '=') !== false) {
                list($key, $val) = explode('=', $line, 2);
                $key = trim($key);
                $val = trim($val, " \t\n\r\0\x0B\"'");
                if (!isset($_ENV[$key]) && !getenv($key)) {
                    putenv("{$key}={$val}");
                    $_ENV[$key] = $val;
                }
            }
        }
    }
})();

// 2. Active Driver Determination ('pgsql' or 'mysql')
define('DB_DRIVER', getenv('DB_DRIVER') ?: 'pgsql');

function getDBDriver() {
    return strtolower(DB_DRIVER);
}

function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        $driver = getDBDriver();
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 3,
        ];

        $attemptConnect = function($drv) use ($options) {
            if ($drv === 'mysql') {
                $host = getenv('MYSQL_HOST') ?: (getenv('DB_HOST') ?: '127.0.0.1');
                $port = getenv('MYSQL_PORT') ?: '3306';
                $name = getenv('MYSQL_NAME') ?: 'roppet_db';
                $user = getenv('MYSQL_USER') ?: 'root';
                $pass = getenv('MYSQL_PASS') ?: '';
                
                $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
                return new PDO($dsn, $user, $pass, $options);
            } else {
                $host = getenv('DB_HOST') ?: 'ep-wild-wind-ayk3w0b8.c-5.us-east-2.aws.neon.tech';
                $port = getenv('DB_PORT') ?: '5432';
                $name = getenv('DB_NAME') ?: 'neondb';
                $user = getenv('DB_USER') ?: 'neondb_owner';
                $pass = getenv('DB_PASS') ?: '';
                $sslmode = getenv('DB_SSLMODE') ?: 'require';

                $dsn = "pgsql:host={$host};port={$port};dbname={$name};sslmode={$sslmode}";
                return new PDO($dsn, $user, $pass, $options);
            }
        };

        try {
            $pdo = $attemptConnect($driver);
        } catch (PDOException $e) {
            error_log("Primary database connection error ({$driver}): " . $e->getMessage());
            
            // Try alternate driver failover
            $altDriver = ($driver === 'mysql') ? 'pgsql' : 'mysql';
            try {
                $pdo = $attemptConnect($altDriver);
                error_log("Successfully connected to alternate database engine ({$altDriver}).");
            } catch (PDOException $e2) {
                error_log("Alternate database connection error ({$altDriver}): " . $e2->getMessage());
                throw new Exception("Database connection failed ({$driver} & {$altDriver}). Please check your database service and .env credentials.");
            }
        }
    }
    return $pdo;
}

/**
 * Global System Settings Helper
 */
function getSystemSetting($key, $default = '') {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ? LIMIT 1");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return ($val !== false && $val !== null) ? $val : $default;
    } catch (Exception $e) {
        return $default;
    }
}
