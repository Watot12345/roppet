<?php
/**
 * Roppets Edutainment Production Inc.
 * Migration & Transfer Script: Neon PostgreSQL -> Supabase PostgreSQL
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Source: Neon PostgreSQL
$sourceHost = 'ep-wild-wind-ayk3w0b8.c-5.us-east-2.aws.neon.tech';
$sourcePort = '5432';
$sourceDb   = 'neondb';
$sourceUser = 'neondb_owner';
$sourcePass = 'npg_38tOXpeWgyQo';

// Target: Supabase PostgreSQL (via Supavisor pooler for dual-stack IPv4/IPv6 support)
$targetHost = 'aws-0-ap-south-1.pooler.supabase.com';
$targetPort = '5432';
$targetDb   = 'postgres';
$targetUser = 'postgres.olrpqobxouiyymvujxkw';
$targetPass = '0P1wihBtE2MFfEc4';

echo "====================================================\n";
echo "Starting Migration & Transfer to Supabase...\n";
echo "====================================================\n";

try {
    // 1. Establish connections
    echo "[1/5] Connecting to Source (Neon PostgreSQL)... ";
    $sourcePdo = new PDO(
        "pgsql:host={$sourceHost};port={$sourcePort};dbname={$sourceDb};sslmode=require",
        $sourceUser,
        $sourcePass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 10
        ]
    );
    echo "CONNECTED!\n";

    echo "[2/5] Connecting to Target (Supabase PostgreSQL)... ";
    $targetPdo = new PDO(
        "pgsql:host={$targetHost};port={$targetPort};dbname={$targetDb};sslmode=require",
        $targetUser,
        $targetPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 10
        ]
    );
    echo "CONNECTED!\n";

    // 2. Define Table Schemas on Target (Supabase)
    echo "[3/5] Creating Tables and Indexes in Supabase...\n";

    $ddlStatements = [
        // 1. admin_users
        "CREATE TABLE IF NOT EXISTS admin_users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(150) NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            role VARCHAR(50) DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        // 2. bookings
        "CREATE TABLE IF NOT EXISTS bookings (
            id SERIAL PRIMARY KEY,
            reference_code VARCHAR(30) UNIQUE NOT NULL,
            name VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            event_date DATE NOT NULL,
            event_type VARCHAR(100) DEFAULT 'Birthday Party',
            service_package VARCHAR(150) NOT NULL,
            location VARCHAR(255),
            guest_count INT DEFAULT 0,
            notes TEXT,
            total_price NUMERIC(10, 2) DEFAULT 0.00,
            status VARCHAR(50) DEFAULT 'Pending',
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            payment_status VARCHAR(50) DEFAULT 'Unpaid',
            downpayment_amount NUMERIC(10, 2) DEFAULT 0.00
        )",
        "CREATE INDEX IF NOT EXISTS idx_bookings_ref ON bookings(reference_code)",
        "CREATE INDEX IF NOT EXISTS idx_bookings_date ON bookings(event_date)",
        "CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status)",

        // 3. inquiries
        "CREATE TABLE IF NOT EXISTS inquiries (
            id SERIAL PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL,
            phone VARCHAR(50),
            service VARCHAR(150),
            subject VARCHAR(255),
            message TEXT NOT NULL,
            status VARCHAR(50) DEFAULT 'Unread',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE INDEX IF NOT EXISTS idx_inquiries_status ON inquiries(status)",

        // 4. performers
        "CREATE TABLE IF NOT EXISTS performers (
            id SERIAL PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            role VARCHAR(100) NOT NULL,
            phone VARCHAR(50),
            status VARCHAR(50) DEFAULT 'Available',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            email VARCHAR(150)
        )",

        // 5. booking_assignments (depends on bookings and performers)
        "CREATE TABLE IF NOT EXISTS booking_assignments (
            id SERIAL PRIMARY KEY,
            booking_id INT REFERENCES bookings(id) ON DELETE CASCADE,
            performer_id INT REFERENCES performers(id) ON DELETE CASCADE,
            assigned_role VARCHAR(100) NOT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE INDEX IF NOT EXISTS idx_assignments_booking ON booking_assignments(booking_id)",
        "CREATE INDEX IF NOT EXISTS idx_assignments_performer ON booking_assignments(performer_id)",

        // 6. payments (depends on bookings)
        "CREATE TABLE IF NOT EXISTS payments (
            id SERIAL PRIMARY KEY,
            booking_id INT REFERENCES bookings(id) ON DELETE CASCADE,
            reference_code VARCHAR(30) NOT NULL,
            payment_method VARCHAR(50) NOT NULL,
            transaction_ref VARCHAR(100),
            amount NUMERIC(10, 2) NOT NULL,
            receipt_file VARCHAR(255),
            status VARCHAR(50) DEFAULT 'Pending Review',
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE INDEX IF NOT EXISTS idx_payments_booking ON payments(booking_id)",
        "CREATE INDEX IF NOT EXISTS idx_payments_status ON payments(status)",

        // 7. services
        "CREATE TABLE IF NOT EXISTS services (
            id SERIAL PRIMARY KEY,
            title VARCHAR(150) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            category VARCHAR(50) NOT NULL,
            starting_price NUMERIC(10, 2) DEFAULT 0.00,
            badge VARCHAR(50),
            description TEXT NOT NULL,
            features TEXT,
            image_url VARCHAR(255),
            is_active BOOLEAN DEFAULT TRUE,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        // 8. packages
        "CREATE TABLE IF NOT EXISTS packages (
            id SERIAL PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            subtitle VARCHAR(255),
            price NUMERIC(10, 2) NOT NULL,
            features TEXT NOT NULL,
            is_popular BOOLEAN DEFAULT FALSE,
            is_active BOOLEAN DEFAULT TRUE,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        // 9. puppet_stories
        "CREATE TABLE IF NOT EXISTS puppet_stories (
            id SERIAL PRIMARY KEY,
            title VARCHAR(150) NOT NULL,
            moral_lesson VARCHAR(255) NOT NULL,
            category VARCHAR(100) DEFAULT 'Classic Moral',
            synopsis TEXT,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        // 10. fabrication_quotes
        "CREATE TABLE IF NOT EXISTS fabrication_quotes (
            id SERIAL PRIMARY KEY,
            quote_ref VARCHAR(30) UNIQUE NOT NULL,
            client_name VARCHAR(150) NOT NULL,
            company_name VARCHAR(150),
            email VARCHAR(150) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            fabrication_type VARCHAR(100) NOT NULL,
            quantity INT DEFAULT 1,
            target_delivery DATE,
            cooling_fan BOOLEAN DEFAULT FALSE,
            fabric_preference VARCHAR(100),
            description TEXT NOT NULL,
            blueprint_file VARCHAR(255),
            estimated_price NUMERIC(10, 2) DEFAULT 0.00,
            status VARCHAR(50) DEFAULT 'Under Review',
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE INDEX IF NOT EXISTS idx_fab_status ON fabrication_quotes(status)",

        // 11. reviews
        "CREATE TABLE IF NOT EXISTS reviews (
            id SERIAL PRIMARY KEY,
            booking_id INT,
            client_name VARCHAR(150) NOT NULL,
            location VARCHAR(150),
            event_type VARCHAR(100) DEFAULT 'Birthday Party',
            rating INT DEFAULT 5,
            comment TEXT NOT NULL,
            is_approved BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE INDEX IF NOT EXISTS idx_reviews_approved ON reviews(is_approved)",

        // 12. gallery_items
        "CREATE TABLE IF NOT EXISTS gallery_items (
            id SERIAL PRIMARY KEY,
            title VARCHAR(150) NOT NULL,
            category VARCHAR(50) NOT NULL,
            media_type VARCHAR(20) DEFAULT 'image',
            media_url VARCHAR(255) NOT NULL,
            caption TEXT,
            is_featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        // 13. activity_logs
        "CREATE TABLE IF NOT EXISTS activity_logs (
            id SERIAL PRIMARY KEY,
            admin_user VARCHAR(100) NOT NULL,
            action VARCHAR(100) NOT NULL,
            details TEXT,
            ip_address VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE INDEX IF NOT EXISTS idx_logs_created ON activity_logs(created_at)",

        // 14. system_settings
        "CREATE TABLE IF NOT EXISTS system_settings (
            setting_key VARCHAR(100) PRIMARY KEY,
            setting_value TEXT NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ];

    foreach ($ddlStatements as $stmt) {
        $targetPdo->exec($stmt);
    }
    echo "  All 14 tables and indexes created successfully in Supabase!\n";

    // 3. Migrate data table by table in dependency order
    echo "[4/5] Migrating Data from Neon to Supabase...\n";

    // Tables in dependency order
    $tablesToMigrate = [
        'system_settings',
        'admin_users',
        'performers',
        'puppet_stories',
        'services',
        'packages',
        'bookings',
        'inquiries',
        'fabrication_quotes',
        'reviews',
        'gallery_items',
        'activity_logs',
        'booking_assignments',
        'payments'
    ];

    foreach ($tablesToMigrate as $table) {
        echo "  - Migrating table '{$table}'... ";
        
        // Fetch rows from source
        $rows = $sourcePdo->query("SELECT * FROM \"{$table}\"")->fetchAll();
        $rowCount = count($rows);

        if ($rowCount === 0) {
            echo "0 rows (empty table).\n";
            continue;
        }

        // Clean target table first in case re-running
        $targetPdo->exec("DELETE FROM \"{$table}\"");

        // Insert into target
        $columns = array_keys($rows[0]);
        $colNamesList = implode(', ', array_map(function($c) { return "\"{$c}\""; }, $columns));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        $insertSql = "INSERT INTO \"{$table}\" ({$colNamesList}) VALUES ({$placeholders})";
        $insertStmt = $targetPdo->prepare($insertSql);

        $inserted = 0;
        foreach ($rows as $row) {
            $values = [];
            foreach ($columns as $c) {
                $val = $row[$c];
                // In PostgreSQL PDO, booleans need explicit cast or 't'/'f' string if boolean type
                if (is_bool($val)) {
                    $val = $val ? 't' : 'f';
                }
                $values[] = $val;
            }
            $insertStmt->execute($values);
            $inserted++;
        }

        // Reset serial sequence if table has 'id' column
        if (in_array('id', $columns)) {
            $seqStmt = $targetPdo->query("SELECT setval(pg_get_serial_sequence('\"{$table}\"', 'id'), COALESCE(MAX(id), 1), MAX(id) IS NOT NULL) FROM \"{$table}\"");
            $seqStmt->fetch();
        }

        echo "Done ({$inserted}/{$rowCount} rows transferred, sequence synced).\n";
    }

    // 4. Verification Check
    echo "[5/5] Verifying Data Parity between Neon and Supabase...\n";
    $allMatched = true;
    foreach ($tablesToMigrate as $table) {
        $sourceCnt = (int)$sourcePdo->query("SELECT COUNT(*) FROM \"{$table}\"")->fetchColumn();
        $targetCnt = (int)$targetPdo->query("SELECT COUNT(*) FROM \"{$table}\"")->fetchColumn();

        $status = ($sourceCnt === $targetCnt) ? "MATCH [OK]" : "MISMATCH [ERROR]";
        if ($sourceCnt !== $targetCnt) {
            $allMatched = false;
        }
        echo sprintf("  %-20s | Source: %2d | Supabase: %2d | %s\n", $table, $sourceCnt, $targetCnt, $status);
    }

    if ($allMatched) {
        echo "\nSUCCESS! All data was perfectly transferred to Supabase without any loss.\n";
    } else {
        echo "\nWARNING: Some counts do not match! Please check logs above.\n";
    }

} catch (Exception $e) {
    echo "\nCRITICAL ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
