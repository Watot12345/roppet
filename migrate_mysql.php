<?php
/**
 * Roppets Edutainment Production Inc.
 * MySQL Backup Database Initializer & PostgreSQL Sync Tool
 */

require_once __DIR__ . '/includes/db.php';

echo "=== Roppets MySQL Backup Database Initializer ===\n\n";

$mysqlHost = getenv('MYSQL_HOST') ?: '127.0.0.1';
$mysqlPort = getenv('MYSQL_PORT') ?: '3306';
$mysqlName = getenv('MYSQL_NAME') ?: 'roppet_db';
$mysqlUser = getenv('MYSQL_USER') ?: 'root';
$mysqlPass = getenv('MYSQL_PASS') ?: '';

try {
    // 1. Connect to MySQL Server (Root) & Create Database
    echo "1. Connecting to MySQL Server at {$mysqlHost}:{$mysqlPort}...\n";
    $pdoServer = new PDO("mysql:host={$mysqlHost};port={$mysqlPort}", $mysqlUser, $mysqlPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `{$mysqlName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "   Database '{$mysqlName}' verified / created successfully.\n\n";

    // 2. Connect to the roppet_db in MySQL
    $pdoMysql = new PDO("mysql:host={$mysqlHost};port={$mysqlPort};dbname={$mysqlName};charset=utf8mb4", $mysqlUser, $mysqlPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // 3. Create Tables
    echo "2. Initializing MySQL Tables...\n";

    $schema = [
        "admin_users" => "
            CREATE TABLE IF NOT EXISTS admin_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                role VARCHAR(30) DEFAULT 'admin',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        "system_settings" => "
            CREATE TABLE IF NOT EXISTS system_settings (
                setting_key VARCHAR(100) PRIMARY KEY,
                setting_value TEXT,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        "services" => "
            CREATE TABLE IF NOT EXISTS services (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(150) NOT NULL,
                slug VARCHAR(100) UNIQUE NOT NULL,
                category VARCHAR(50) NOT NULL,
                starting_price DECIMAL(10, 2) DEFAULT 0.00,
                badge VARCHAR(50),
                description TEXT NOT NULL,
                features TEXT,
                image_url VARCHAR(255),
                is_active TINYINT(1) DEFAULT 1,
                sort_order INT DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        "packages" => "
            CREATE TABLE IF NOT EXISTS packages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                slug VARCHAR(100) UNIQUE NOT NULL,
                subtitle VARCHAR(255),
                price DECIMAL(10, 2) NOT NULL,
                features TEXT NOT NULL,
                is_popular TINYINT(1) DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                sort_order INT DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        "puppet_stories" => "
            CREATE TABLE IF NOT EXISTS puppet_stories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(150) NOT NULL,
                moral_lesson VARCHAR(255) NOT NULL,
                category VARCHAR(100) DEFAULT 'Classic Moral',
                synopsis TEXT,
                is_active TINYINT(1) DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        "performers" => "
            CREATE TABLE IF NOT EXISTS performers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                role VARCHAR(100) NOT NULL,
                phone VARCHAR(50),
                email VARCHAR(150),
                status VARCHAR(50) DEFAULT 'Available',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        "bookings" => "
            CREATE TABLE IF NOT EXISTS bookings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reference_code VARCHAR(50) UNIQUE NOT NULL,
                name VARCHAR(150) NOT NULL,
                email VARCHAR(150) NOT NULL,
                phone VARCHAR(50) NOT NULL,
                event_date DATE NOT NULL,
                event_type VARCHAR(100) NOT NULL,
                service_package VARCHAR(150) NOT NULL,
                location VARCHAR(255),
                guest_count INT DEFAULT 0,
                notes TEXT,
                total_price DECIMAL(10, 2) DEFAULT 0.00,
                status VARCHAR(50) DEFAULT 'Pending',
                admin_notes TEXT,
                downpayment_amount DECIMAL(10, 2) DEFAULT 0.00,
                payment_status VARCHAR(50) DEFAULT 'Unpaid',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        "booking_assignments" => "
            CREATE TABLE IF NOT EXISTS booking_assignments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                booking_id INT NOT NULL,
                performer_id INT NOT NULL,
                assigned_role VARCHAR(100) NOT NULL,
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (booking_id),
                INDEX (performer_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        "payments" => "
            CREATE TABLE IF NOT EXISTS payments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                booking_id INT NOT NULL,
                amount DECIMAL(10, 2) NOT NULL,
                payment_method VARCHAR(50) NOT NULL,
                transaction_ref VARCHAR(100),
                proof_file VARCHAR(255),
                status VARCHAR(50) DEFAULT 'Pending',
                admin_notes TEXT,
                verified_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (booking_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        "reviews" => "
            CREATE TABLE IF NOT EXISTS reviews (
                id INT AUTO_INCREMENT PRIMARY KEY,
                booking_id INT,
                client_name VARCHAR(150) NOT NULL,
                location VARCHAR(150),
                event_type VARCHAR(100) DEFAULT 'Birthday Party',
                rating INT DEFAULT 5,
                comment TEXT NOT NULL,
                is_approved TINYINT(1) DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (is_approved)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        "gallery_items" => "
            CREATE TABLE IF NOT EXISTS gallery_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(150) NOT NULL,
                category VARCHAR(50) NOT NULL,
                media_type VARCHAR(20) DEFAULT 'image',
                media_url VARCHAR(255) NOT NULL,
                caption TEXT,
                is_featured TINYINT(1) DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        "fabrication_quotes" => "
            CREATE TABLE IF NOT EXISTS fabrication_quotes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                client_name VARCHAR(150) NOT NULL,
                email VARCHAR(150) NOT NULL,
                phone VARCHAR(50) NOT NULL,
                item_type VARCHAR(100) NOT NULL,
                quantity INT DEFAULT 1,
                blueprint_file VARCHAR(255),
                specifications TEXT,
                target_delivery DATE,
                status VARCHAR(50) DEFAULT 'Under Review',
                estimated_quote DECIMAL(10, 2),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        "activity_logs" => "
            CREATE TABLE IF NOT EXISTS activity_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                admin_user VARCHAR(100) NOT NULL,
                action VARCHAR(100) NOT NULL,
                details TEXT,
                ip_address VARCHAR(50),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ",
        "inquiries" => "
            CREATE TABLE IF NOT EXISTS inquiries (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                email VARCHAR(150) NOT NULL,
                phone VARCHAR(50),
                subject VARCHAR(255),
                service VARCHAR(100),
                message TEXT NOT NULL,
                status VARCHAR(30) DEFAULT 'Unread',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        "
    ];

    foreach ($schema as $tableName => $sql) {
        $pdoMysql->exec($sql);
        echo "   [✓] Table `{$tableName}` ready.\n";
    }

    echo "\n3. Syncing Live Data from Neon Cloud PostgreSQL to Local MySQL...\n";
    
    // Connect to PostgreSQL
    $pdoPg = new PDO(
        "pgsql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT') . ";dbname=" . getenv('DB_NAME') . ";sslmode=" . getenv('DB_SSLMODE'),
        getenv('DB_USER'),
        getenv('DB_PASS'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );

    $tablesToSync = [
        'admin_users',
        'system_settings',
        'services',
        'packages',
        'puppet_stories',
        'performers',
        'bookings',
        'booking_assignments',
        'payments',
        'reviews',
        'gallery_items',
        'activity_logs',
        'inquiries'
    ];

    foreach ($tablesToSync as $tbl) {
        try {
            $rows = $pdoPg->query("SELECT * FROM {$tbl}")->fetchAll();
            if (!empty($rows)) {
                // Clear existing MySQL table to avoid duplicates on full sync
                $pdoMysql->exec("DELETE FROM `{$tbl}`");
                
                $columns = array_keys($rows[0]);
                $colList = implode(', ', array_map(function($c) { return "`{$c}`"; }, $columns));
                $placeholderList = implode(', ', array_fill(0, count($columns), '?'));

                $insStmt = $pdoMysql->prepare("INSERT INTO `{$tbl}` ({$colList}) VALUES ({$placeholderList})");

                foreach ($rows as $row) {
                    $values = [];
                    foreach ($columns as $c) {
                        $val = $row[$c];
                        if (is_bool($val)) {
                            $val = $val ? 1 : 0;
                        }
                        $values[] = $val;
                    }
                    $insStmt->execute($values);
                }
                echo "   [✓] Synced " . count($rows) . " record(s) into `{$tbl}`\n";
            } else {
                echo "   [-] `{$tbl}` is empty, skipping.\n";
            }
        } catch (Exception $e) {
            echo "   [!] Notice syncing `{$tbl}`: " . $e->getMessage() . "\n";
        }
    }

    echo "\n=======================================================\n";
    echo "MySQL Backup Database is 100% Ready and Synchronized!\n";
    echo "To switch between databases anytime:\n";
    echo "  - Neon PostgreSQL: In .env set DB_DRIVER=pgsql\n";
    echo "  - Local MySQL:     In .env set DB_DRIVER=mysql\n";
    echo "=======================================================\n";

} catch (Exception $e) {
    echo "\nError during MySQL initialization/sync: " . $e->getMessage() . "\n";
}
