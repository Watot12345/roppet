<?php
/**
 * Phase 3 Migration Script for Neon PostgreSQL
 */

require_once __DIR__ . '/includes/db.php';

try {
    $pdo = getDBConnection();
    echo "Connected to Neon PostgreSQL.\n";

    // 1. Services Table
    $sqlServices = "
    CREATE TABLE IF NOT EXISTS services (
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
    );
    ";
    $pdo->exec($sqlServices);
    echo "Table 'services' verified.\n";

    // 2. Packages Table
    $sqlPackages = "
    CREATE TABLE IF NOT EXISTS packages (
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
    );
    ";
    $pdo->exec($sqlPackages);
    echo "Table 'packages' verified.\n";

    // 3. Puppet Stories Table
    $sqlStories = "
    CREATE TABLE IF NOT EXISTS puppet_stories (
        id SERIAL PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        moral_lesson VARCHAR(255) NOT NULL,
        category VARCHAR(100) DEFAULT 'Classic Moral',
        synopsis TEXT,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    $pdo->exec($sqlStories);
    echo "Table 'puppet_stories' verified.\n";

    // 4. Fabrication Quotes Table
    $sqlFabQuotes = "
    CREATE TABLE IF NOT EXISTS fabrication_quotes (
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
    );
    CREATE INDEX IF NOT EXISTS idx_fab_status ON fabrication_quotes(status);
    ";
    $pdo->exec($sqlFabQuotes);
    echo "Table 'fabrication_quotes' verified.\n";

    // Seed Services if empty
    $sCount = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
    if ($sCount == 0) {
        $services = [
            ['Original Hand Puppet Show', 'hand-puppet-show', 'puppetry', 4500, 'Top Seller', 'Features 3 thrilling moral stories, colorful puppet stage backdrop, full sound system, and energetic professional puppeteers.', "100+ Available Classic Stories\n3 Puppet Plays Included\nComplete Sound & Mic Setup", 'assets/images/smurfs-hand-puppet-show-by-roppets-edutainment-production-inc-1-265x265_c.png', 1],
            ['Bubble Show by Bubble Factory', 'bubble-show', 'specialty', 6000, 'Signature Act', '3 all-girl performers in themed costumes performing synchronized bubble art, giant smoke bubbles, and put-a-kid-in-a-bubble experience.', "3 All-Girl Themed Performers\nGiant Smoke Bubble Volcano & Hand Sculptures\nKids Inside a Bubble Interaction", 'assets/images/The-original-bubble-show-by-the-bubble-factory-1-265x265_c.png', 2],
            ['Teatro Itim (Black Theater Play)', 'teatro-itim', 'puppetry', 7500, 'Award Winning', 'A mesmerizing black-light visual puppetry technique where fluorescent characters glide and dance in total darkness to uplifting music.', "UV / Black Light Stage Effects\nGlowing Puppetry & Illusion\nPerfect for Indoor Venues", 'assets/images/teatro-itim-by-black-theater-play-1-265x265_c.png', 3],
            ['Cultural Marionette (Tali Galaw)', 'cultural-marionette', 'puppetry', 5500, 'Cultural Heritage', 'Traditional string puppetry highlighting Philippine cultural folk dances (Tinikling, Singkil, Pandanggo sa Ilaw) with intricately strung puppets.', "Authentic Philippine Folk Dances\nMaster String Puppeteers\nEducational & Cultural Value", 'assets/images/Tali-Galaw-Marionette-Group-1-265x265_c.png', 4],
            ['Character Mascots & Meet-and-Greet', 'character-mascots', 'mascots-magic', 3500, 'Interactive', 'Energetic character mascots to meet your guests, dance during program highlights, lead the cake ceremony, and pose for memorable photos.', "Huge Library of Characters\nMascot Dance Number\nPhoto-Op & Cake Ceremony", 'assets/images/finn_adventure.png', 5],
            ['Fancy Magic & Illusion Show', 'fancy-magic', 'mascots-magic', 4000, 'Hilarious & Fun', 'Kid-friendly comedic magic show packed with laughter, interactive tricks with the celebrant, doves, illusions, and audience participation.', "Professional Comedy Magician\nHigh Audience Participation\nCelebrant Special Magic Trick", 'assets/images/Fancy-magic-show-by-roppets-edutainment-production-inc.-1-265x265_c.png', 6]
        ];

        $insS = $pdo->prepare("INSERT INTO services (title, slug, category, starting_price, badge, description, features, image_url, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($services as $s) {
            $insS->execute($s);
        }
        echo "Seeded 6 default services.\n";
    }

    // Seed Packages if empty
    $pCount = $pdo->query("SELECT COUNT(*) FROM packages")->fetchColumn();
    if ($pCount == 0) {
        $packages = [
            ['Kiddie Fun Package', 'kiddie-fun', 'Ideal for intimate home and clubhouse parties', 6500, "Original Hand Puppet Show (3 Stories)\nPuppet Stage & Sound System\n1 Character Mascot Appearance\nMascot Dance & Cake Cutting\n1 Professional Puppeteer Host", 0, 1],
            ['Classic Combo Package', 'classic-combo', 'Classic puppetry paired with hilarious comedy magic', 8500, "Original Hand Puppet Show (3 Stories)\nFancy Magic & Illusion Show\n1 Character Mascot Appearance & Dance\nFull Sound System Setup with Mics\nParty Games Hosting & Program Assistance", 0, 2],
            ['Supreme Edutainment Package', 'supreme-edutainment', 'Our #1 best-selling complete entertainment combo', 11500, "Original Hand Puppet Show (3 Stories)\nThe Bubble Factory Show (3 Girls)\n1 Character Mascot of your choice\nComplete Theater Backdrop & Sound System\nKid-in-a-Bubble Interactive Experience\nParty Games Coordination", 1, 3],
            ['Grand Roppets Extravaganza', 'grand-extravaganza', 'Full production for large parties, malls & schools', 18500, "Teatro Itim Black Theater OR Puppet Show\nThe Bubble Factory Extravaganza\nFancy Magic & Illusion Show\n2 Character Mascots Meet-and-Greet\nFull Lights & Sound System Setup\n2 Hours Face Painting / Balloon Twisting", 0, 4]
        ];

        $insP = $pdo->prepare("INSERT INTO packages (name, slug, subtitle, price, features, is_popular, sort_order) VALUES (?, ?, ?, ?, ?, ?::boolean, ?)");
        foreach ($packages as $p) {
            $insP->execute($p);
        }
        echo "Seeded 4 default party packages.\n";
    }

    // Seed Puppet Stories if empty
    $stCount = $pdo->query("SELECT COUNT(*) FROM puppet_stories")->fetchColumn();
    if ($stCount == 0) {
        $stories = [
            ['The 3 Little Pigs & The Wise Builder', 'Hard work, perseverance, and planning for the future', 'Fable Classic', 'A hilarious retelling of the three brothers learning why building with strong materials and diligence pays off.'],
            ['Little Red Riding Hood & Safety First', 'Obedience to parents and stranger awareness', 'Fable Classic', 'Red Riding Hood journeys to her grandmother and learns essential safety lessons on not talking to sly wolves.'],
            ['Pinocchio & The Value of Honesty', 'Truthfulness and integrity of heart', 'Moral Tale', 'Pinocchios nose grows whenever he tells a lie until he discovers that honesty is always the best policy.'],
            ['The Boy Who Cried Wolf', 'The consequences of deceit and breaking trust', 'Classic Moral', 'A playful shepherd boy plays pranks until no one believes him when real danger arrives.'],
            ['Si Pagong at si Matsing (The Turtle & Monkey)', 'Fairness, humility, and cleverness over greed', 'Filipino Folklore', 'A classic Philippine folklore showing how greedy Monkey gets outsmarted by patient, honest Turtle.'],
            ['The Lion & The Helpful Mouse', 'Kindness is never wasted; even the smallest can help', 'Aesop Fable', 'A mighty lion spares a tiny mouse, and later the mouse saves the lion from a hunters trap.'],
            ['Ang Alamat ng Pinya (Legend of the Pineapple)', 'Attentiveness, obedience, and using ones eyes to help', 'Filipino Legend', 'The famous Philippine legend of Pina learning to look for things with patience and helpfulness.']
        ];

        $insSt = $pdo->prepare("INSERT INTO puppet_stories (title, moral_lesson, category, synopsis) VALUES (?, ?, ?, ?)");
        foreach ($stories as $st) {
            $insSt->execute($st);
        }
        echo "Seeded 7 classic puppet stories.\n";
    }

    echo "Phase 3 migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration Error: " . $e->getMessage() . "\n";
    exit(1);
}
