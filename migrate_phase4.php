<?php
/**
 * Phase 4 Migration Script for Neon PostgreSQL
 */

require_once __DIR__ . '/includes/db.php';

try {
    $pdo = getDBConnection();
    echo "Connected to Neon PostgreSQL.\n";

    // 1. Reviews Table
    $sqlReviews = "
    CREATE TABLE IF NOT EXISTS reviews (
        id SERIAL PRIMARY KEY,
        booking_id INT,
        client_name VARCHAR(150) NOT NULL,
        location VARCHAR(150),
        event_type VARCHAR(100) DEFAULT 'Birthday Party',
        rating INT DEFAULT 5,
        comment TEXT NOT NULL,
        is_approved BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_reviews_approved ON reviews(is_approved);
    ";
    $pdo->exec($sqlReviews);
    echo "Table 'reviews' verified.\n";

    // 2. Gallery Items Table
    $sqlGallery = "
    CREATE TABLE IF NOT EXISTS gallery_items (
        id SERIAL PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        category VARCHAR(50) NOT NULL, -- puppetry, bubble, black-theater, mascots, fabrication
        media_type VARCHAR(20) DEFAULT 'image', -- image, video
        media_url VARCHAR(255) NOT NULL,
        caption TEXT,
        is_featured BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    $pdo->exec($sqlGallery);
    echo "Table 'gallery_items' verified.\n";

    // 3. Activity Logs Table
    $sqlLogs = "
    CREATE TABLE IF NOT EXISTS activity_logs (
        id SERIAL PRIMARY KEY,
        admin_user VARCHAR(100) NOT NULL,
        action VARCHAR(100) NOT NULL,
        details TEXT,
        ip_address VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_logs_created ON activity_logs(created_at);
    ";
    $pdo->exec($sqlLogs);
    echo "Table 'activity_logs' verified.\n";

    // 4. System Settings Table
    $sqlSettings = "
    CREATE TABLE IF NOT EXISTS system_settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    $pdo->exec($sqlSettings);
    echo "Table 'system_settings' verified.\n";

    // Seed default reviews if empty
    $rCount = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
    if ($rCount == 0) {
        $reviews = [
            ['Christine Mendoza', 'Quezon City', 'Birthday Party', 5, 'The Roppets puppet show and Bubble Factory were the highlight of my sons 5th birthday! All the kids and parents were mesmerized when they put the celebrant inside a giant bubble. Highly recommended!', 1],
            ['Sir Ronald Tan', 'Makati City', 'School Family Day', 5, 'We booked Teatro Itim (Black Theater) for our schools family day. The UV visual effects and moral storytelling were world-class! The students talked about it for weeks. Super professional team.', 1],
            ['Anna Valencia', 'Bonifacio Global City', 'Corporate Launch', 5, 'Roppets fabricated our corporate mascot from our 2D illustrations. The craftsmanship, lightweight ventilation, and durable stitching exceeded our expectations. Truly the master puppet makers in PH!', 1],
            ['Dr. Grace Del Rosario', 'Alabang', 'Kiddie Party', 5, 'Kuya Roberto and the puppeteers were so engaging. The moral lesson was very clear, and the kids were laughing throughout the entire show.', 1]
        ];

        $insR = $pdo->prepare("INSERT INTO reviews (client_name, location, event_type, rating, comment, is_approved) VALUES (?, ?, ?, ?, ?, ?::boolean)");
        foreach ($reviews as $r) {
            $insR->execute($r);
        }
        echo "Seeded 4 default approved reviews.\n";
    }

    // Seed default gallery items if empty
    $gCount = $pdo->query("SELECT COUNT(*) FROM gallery_items")->fetchColumn();
    if ($gCount == 0) {
        $gallery = [
            ['The Bubble Factory Live Show', 'bubble', 'image', 'assets/images/hero_bubble.jpg', '3 All-Girl Bubble Performers in giant bubble choreography', 1],
            ['Original 3 Little Pigs Puppet Stage', 'puppetry', 'image', 'assets/images/puppet_3pigs.jpg', 'Live hand puppet stage with sound and lighting setup', 1],
            ['Teatro Itim UV Glowing Puppets', 'black-theater', 'image', 'assets/images/black_theater.jpg', 'Ultraviolet glow-in-the-dark puppetry performance', 1],
            ['Tali Galaw Cultural Marionette Show', 'puppetry', 'image', 'assets/images/Tali-Galaw-Marionette-Group-4-265x265_c.png', 'Traditional string puppets performing Filipino folk dances', 0],
            ['Finn Adventure Time Mascot Fabrication', 'mascots', 'image', 'assets/images/finn_adventure.png', 'Custom fabricated wearable mascot costume with cooling kit', 1],
            ['Doc McStuffins Puppet Construction', 'fabrication', 'image', 'assets/images/doc_mcstuffins.jpg', 'Detailed custom hand puppet with movable mouth and costume accessories', 0]
        ];

        $insG = $pdo->prepare("INSERT INTO gallery_items (title, category, media_type, media_url, caption, is_featured) VALUES (?, ?, ?, ?, ?, ?::boolean)");
        foreach ($gallery as $g) {
            $insG->execute($g);
        }
        echo "Seeded 6 default gallery items.\n";
    }

    // Seed default system settings if empty
    $sCount = $pdo->query("SELECT COUNT(*) FROM system_settings")->fetchColumn();
    if ($sCount == 0) {
        $settings = [
            'company_name' => 'Roppets Edutainment Production Inc.',
            'company_phone' => '(02) 8912-3456 / +63 917 530 0823',
            'company_email' => 'edutainment@roppets.com',
            'company_address' => 'Unit 314 Parc Condominium, #226 15th Avenue, Cubao, Quezon City, Metro Manila, Philippines',
            'bdo_account' => '0012-3456-7890 (Roppets Edutainment Production Inc.)',
            'gcash_account' => '0917-530-0823 (Roppets Production Finance)',
            'max_shows_per_day' => '5'
        ];

        $insSet = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON CONFLICT (setting_key) DO UPDATE SET setting_value = EXCLUDED.setting_value");
        foreach ($settings as $k => $v) {
            $insSet->execute([$k, $v]);
        }
        echo "Seeded default system settings.\n";
    }

    echo "Phase 4 migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration Error: " . $e->getMessage() . "\n";
    exit(1);
}
