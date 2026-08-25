<?php
/**
 * Phase 2 Migration Script for Neon PostgreSQL
 */

require_once __DIR__ . '/includes/db.php';

try {
    $pdo = getDBConnection();
    echo "Connected to Neon PostgreSQL.\n";

    // 1. Performers Table
    $sqlPerformers = "
    CREATE TABLE IF NOT EXISTS performers (
        id SERIAL PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        role VARCHAR(100) NOT NULL, -- Master Puppeteer, Bubble Artist, Magician, Mascot Performer, Sound Tech
        phone VARCHAR(50),
        status VARCHAR(50) DEFAULT 'Available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    $pdo->exec($sqlPerformers);
    echo "Table 'performers' verified.\n";

    // 2. Booking Assignments Table
    $sqlAssignments = "
    CREATE TABLE IF NOT EXISTS booking_assignments (
        id SERIAL PRIMARY KEY,
        booking_id INT REFERENCES bookings(id) ON DELETE CASCADE,
        performer_id INT REFERENCES performers(id) ON DELETE CASCADE,
        assigned_role VARCHAR(100) NOT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_assignments_booking ON booking_assignments(booking_id);
    CREATE INDEX IF NOT EXISTS idx_assignments_performer ON booking_assignments(performer_id);
    ";
    $pdo->exec($sqlAssignments);
    echo "Table 'booking_assignments' verified.\n";

    // 3. Payments & Receipts Table
    $sqlPayments = "
    CREATE TABLE IF NOT EXISTS payments (
        id SERIAL PRIMARY KEY,
        booking_id INT REFERENCES bookings(id) ON DELETE CASCADE,
        reference_code VARCHAR(30) NOT NULL,
        payment_method VARCHAR(50) NOT NULL, -- GCash, Maya, BDO, BPI, Bank Transfer, Cash
        transaction_ref VARCHAR(100),
        amount NUMERIC(10, 2) NOT NULL,
        receipt_file VARCHAR(255),
        status VARCHAR(50) DEFAULT 'Pending Review', -- Pending Review, Verified, Rejected
        admin_notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_payments_booking ON payments(booking_id);
    CREATE INDEX IF NOT EXISTS idx_payments_status ON payments(status);
    ";
    $pdo->exec($sqlPayments);
    echo "Table 'payments' verified.\n";

    // 4. Alter bookings table to add payment_status if not exists
    $sqlAlterBookings = "
    ALTER TABLE bookings ADD COLUMN IF NOT EXISTS payment_status VARCHAR(50) DEFAULT 'Unpaid';
    ALTER TABLE bookings ADD COLUMN IF NOT EXISTS downpayment_amount NUMERIC(10, 2) DEFAULT 0.00;
    ";
    $pdo->exec($sqlAlterBookings);
    echo "Bookings table columns verified.\n";

    // 5. Seed default performers if empty
    $count = $pdo->query("SELECT COUNT(*) FROM performers")->fetchColumn();
    if ($count == 0) {
        $performers = [
            ['Kuya Roberto Santos', 'Master Puppeteer', '0917-111-2233'],
            ['Ate Jasmin Cruz', 'Lead Bubble Artist (Bubble Factory)', '0917-222-3344'],
            ['Ate Bea Alonzo', 'Bubble Factory Performer', '0917-333-4455'],
            ['Ate Nicole Reyes', 'Bubble Factory Performer', '0917-444-5566'],
            ['Magic Marcus', 'Lead Comedy Magician', '0918-555-6677'],
            ['Kuya Dennis Ramos', 'Character Mascot Lead & Choreographer', '0919-666-7788'],
            ['Kuya Mark Rivera', 'Black Theater (Teatro Itim) Specialist', '0920-777-8899'],
            ['Kuya Noel Bautista', 'Sound & Stage Lighting Technician', '0921-888-9900']
        ];

        $ins = $pdo->prepare("INSERT INTO performers (name, role, phone, status) VALUES (?, ?, ?, 'Available')");
        foreach ($performers as $p) {
            $ins->execute($p);
        }
        echo "Seeded 8 default Roppets performers & crew members.\n";
    }

    echo "Phase 2 migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration Error: " . $e->getMessage() . "\n";
    exit(1);
}
