<?php
/**
 * Roppets Edutainment Production Inc.
 * Database Connection using PDO PostgreSQL (Neon DB)
 */

define('DB_HOST', getenv('DB_HOST') ?: 'ep-wild-wind-ayk3w0b8.c-5.us-east-2.aws.neon.tech');
define('DB_PORT', getenv('DB_PORT') ?: '5432');
define('DB_NAME', getenv('DB_NAME') ?: 'neondb');
define('DB_USER', getenv('DB_USER') ?: 'neondb_owner');
define('DB_PASS', getenv('DB_PASS') ?: 'npg_38tOXpeWgyQo');
define('DB_SSLMODE', getenv('DB_SSLMODE') ?: 'require');

function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            "pgsql:host=%s;port=%s;dbname=%s;sslmode=%s",
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_SSLMODE
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }
    }
    return $pdo;
}

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

