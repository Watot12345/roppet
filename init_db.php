<?php
/**
 * Database Migration Script for Neon PostgreSQL
 */

require_once __DIR__ . '/includes/db.php';

try {
    $pdo = getDBConnection();
    echo "Connected to Neon PostgreSQL successfully.\n";

    // 1. Create bookings table
    $sqlBookings = "
    CREATE TABLE IF NOT EXISTS bookings (
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
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_bookings_ref ON bookings(reference_code);
    CREATE INDEX IF NOT EXISTS idx_bookings_date ON bookings(event_date);
    CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status);
    ";
    $pdo->exec($sqlBookings);
    echo "Table 'bookings' verified.\n";

    // 2. Create inquiries table
    $sqlInquiries = "
    CREATE TABLE IF NOT EXISTS inquiries (
        id SERIAL PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL,
        phone VARCHAR(50),
        service VARCHAR(150),
        subject VARCHAR(255),
        message TEXT NOT NULL,
        status VARCHAR(50) DEFAULT 'Unread',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_inquiries_status ON inquiries(status);
    ";
    $pdo->exec($sqlInquiries);
    echo "Table 'inquiries' verified.\n";

    // 3. Create admin_users table
    $sqlAdminUsers = "
    CREATE TABLE IF NOT EXISTS admin_users (
        id SERIAL PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        full_name VARCHAR(150) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        role VARCHAR(50) DEFAULT 'admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    $pdo->exec($sqlAdminUsers);
    echo "Table 'admin_users' verified.\n";

    // 4. Seed default admin user if not exists
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = :username");
    $stmt->execute([':username' => 'admin']);
    if (!$stmt->fetch()) {
        $defaultPassword = password_hash('roppets2026!', PASSWORD_DEFAULT);
        $insertAdmin = $pdo->prepare("
            INSERT INTO admin_users (username, password_hash, full_name, email, role)
            VALUES (:username, :hash, :full_name, :email, :role)
        ");
        $insertAdmin->execute([
            ':username' => 'admin',
            ':hash' => $defaultPassword,
            ':full_name' => 'Roppets Master Admin',
            ':email' => 'edutainment@roppets.com',
            ':role' => 'superadmin'
        ]);
        echo "Default admin user 'admin' created with initial password.\n";
    } else {
        echo "Admin user already exists.\n";
    }

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Error during migration: " . $e->getMessage() . "\n";
    exit(1);
}
