-- =======================================================
-- Roppets Edutainment Production Inc.
-- MySQL / MariaDB Complete Database Backup Schema & Seed
-- Compatible with XAMPP MySQL, phpMyAdmin, and MySQL 5.7 / 8.0
-- =======================================================

CREATE DATABASE IF NOT EXISTS `roppet_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `roppet_db`;

-- 1. Admin Users
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` VARCHAR(30) DEFAULT 'admin',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admin_users` (`username`, `password_hash`, `full_name`, `role`) VALUES
('admin', '$2y$10$tZ9222cQh3u7fIcx08.x0OSG8q1P/t456/Uv2xL11yK9469eC.E2G', 'Master Administrator', 'super_admin')
ON DUPLICATE KEY UPDATE `full_name`=VALUES(`full_name`);

-- 2. System Settings
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
    `setting_key` VARCHAR(100) PRIMARY KEY,
    `setting_value` TEXT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES
('company_name', 'Roppets Edutainment Production Inc.'),
('company_phone', '(02) 8912-3456 / +63 917 530 0823'),
('company_email', 'edutainment@roppets.com'),
('company_address', 'Unit 314 Parc Condominium, #226 15th Avenue, Cubao, Quezon City, Metro Manila, Philippines'),
('bdo_account', '0012-3456-7890 (Roppets Edutainment Production Inc.)'),
('gcash_account', '0917-530-0823 (Roppets Production Finance)'),
('max_shows_per_day', '5'),
('blackout_dates', '2026-12-25,2027-01-01'),
('smtp_host', 'smtp.gmail.com'),
('smtp_port', '587'),
('smtp_user', 'edutainment@roppets.com'),
('smtp_pass', ''),
('smtp_from_name', 'Roppets Edutainment Production Inc.'),
('smtp_from_email', 'edutainment@roppets.com')
ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);

-- 3. Services Catalog
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(100) UNIQUE NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `starting_price` DECIMAL(10, 2) DEFAULT 0.00,
    `badge` VARCHAR(50),
    `description` TEXT NOT NULL,
    `features` TEXT,
    `image_url` VARCHAR(255),
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `services` (`title`, `slug`, `category`, `starting_price`, `badge`, `description`, `features`, `image_url`, `is_active`, `sort_order`) VALUES
('Original Hand Puppet Show', 'original-hand-puppet-show', 'puppetry', 5000.00, 'Top Seller', 'Features 3 thrilling moral stories, colorful puppet stage backdrop, full sound system, and energetic professional puppeteers.', '3 Moral Play Scripts||Custom Theater Backdrop||Full Audio & Microphone System||Master Puppeteer Cast', 'assets/images/service_puppet.png', 1, 1),
('Bubble Show by Bubble Factory', 'the-bubble-factory-show', 'bubble', 5000.00, 'Crowd Favorite', 'The Philippines pioneer all-girl bubble performers executing giant smoke bubbles, bouncing bubbles, and putting the birthday celebrant inside a giant bubble.', '3 Themed Female Performers||Kid-in-a-Bubble Stunt||LED Glow Bubble Lights||Giant Smoke & Fire Bubbles', 'assets/images/service_bubble.png', 1, 2),
('Teatro Itim (Black Theater Play)', 'teatro-itim-black-theater', 'black-theater', 7500.00, 'Exclusive Art', 'Czech-style black light puppet performance where vibrant fluorescent characters magically float, dance, and glow in complete darkness.', 'Ultra-Violet Blacklight Lamps||Fluorescent Neon Puppets||Uplifting Musical Choreography||Indoor & Hall Compatible', 'assets/images/service_teatro.png', 1, 3),
('Cultural Marionette Show (Tali Galaw)', 'cultural-marionette-show', 'puppetry', 5500.00, 'Philippine Heritage', 'Classical string puppetry showcasing Filipino folk tales, traditional dances, and international marionette craftsmanship.', 'Authentic Handcrafted Marionettes||Folkloric Music & Narratives||Puppeteer String Masterclass', 'assets/images/service_marionette.png', 1, 4),
('Character Mascots & Meet-and-Greet', 'character-mascots', 'mascots', 5000.00, 'Kids Love It', 'Beloved life-sized mascot characters bringing joy, high-fives, and unforgettable photo opportunities for guests.', 'Over 50+ Character Designs||Internal Battery Cooling Fan||High-Energy Mascot Dance Routine', 'assets/images/service_mascots.png', 1, 5),
('Fancy Magic & Illusion Show', 'fancy-magic-show', 'magic', 4000.00, 'Family Fun', 'Interactive comedy magic and visual illusions performed by master illusionists to dazzle children and adults alike.', 'Live Dove Illusions||Interactive Audience Participation||Comedy Magic Routines', 'assets/images/service_magic.png', 1, 6)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- 4. Party Packages
DROP TABLE IF EXISTS `packages`;
CREATE TABLE `packages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(100) UNIQUE NOT NULL,
    `subtitle` VARCHAR(255),
    `price` DECIMAL(10, 2) NOT NULL,
    `features` TEXT NOT NULL,
    `is_popular` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `packages` (`name`, `slug`, `subtitle`, `price`, `features`, `is_popular`, `is_active`, `sort_order`) VALUES
('Kiddie Fun Package', 'kiddie-fun-package', 'Essential Puppetry Celebration', 6500.00, 'Roppets Hand Puppet Show (3 Moral Plays)||Signature Puppet Stage Backdrop||Complete Sound System with Microphones||1 Mascot Character Appearance||Professional Puppeteer Crew', 0, 1, 1),
('Classic Combo Package', 'classic-combo-package', 'Puppets + Comedy Magic Fun', 8500.00, 'Full Roppets Hand Puppet Show (3 Plays)||Fancy Comedy Magic Show with Illusionist||1 Mascot Character Meet-and-Greet||Full Audio Sound System & Operator||Games & Program Hosting Assistance', 0, 1, 2),
('Supreme Edutainment', 'supreme-edutainment-package', 'Puppets + The Bubble Factory (Best Seller)', 11500.00, 'Original Roppets Hand Puppet Show (3 Moral Plays)||The Bubble Factory Show (3 All-Girl Performers)||Celebrant-Inside-a-Bubble Photo Stunt||1 Life-Sized Mascot Character||Full Sound & Lighting System Support', 1, 1, 3),
('Grand Extravaganza', 'grand-extravaganza-package', 'The Ultimate Roppets Production Experience', 18500.00, 'Teatro Itim Black Light Theater OR Hand Puppet Show||The Bubble Factory Giant Bubble Spectacle||Comedy Magic & Grand Illusion Show||2 Character Mascots of Your Choice||Professional Stage Lighting & Full Sound Rig', 0, 1, 4)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 5. Puppet Moral Stories Catalog
DROP TABLE IF EXISTS `puppet_stories`;
CREATE TABLE `puppet_stories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `moral_lesson` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100) DEFAULT 'Classic Moral',
    `synopsis` TEXT,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `puppet_stories` (`title`, `moral_lesson`, `category`, `synopsis`, `is_active`) VALUES
('The 3 Little Pigs & The Wise Builder', 'Hard work, foresight, and building strong foundations in life.', 'Classic Moral', 'Follow the journey of the three piglets as they learn that taking shortcuts leads to danger, while diligence brings lasting security.', 1),
('Little Red Riding Hood & Safety First', 'Obedience to parents and being cautious around strangers.', 'Safety & Values', 'Red Riding Hood learns the importance of listening to her elders and recognizing who she can trust.', 1),
('Pinocchio & The Value of Honesty', 'Honesty, integrity, and the power of telling the truth.', 'Character Building', 'A lively adaptation showing how honesty leads to real transformation and courage.', 1),
('The Boy Who Cried Wolf', 'Truthfulness and the consequences of false alarms.', 'Values Education', 'A young shepherd discovers that people will not believe a liar even when telling the truth.', 1),
('Si Pagong at si Matsing', 'Humility, fairness, and wisdom over selfishness.', 'Filipino Folklore', 'The timeless Filipino fable highlighting how patience and wisdom triumph over boastfulness.', 1),
('The Lion & The Helpful Mouse', 'Kindness, empathy, and helping others regardless of size.', 'Friendship', 'A powerful lion realizes that even the smallest friend can save the day.', 1),
('Ang Alamat ng Pinya', 'Obedience and using your senses and mind responsibly.', 'Philippine Legend', 'A classic moral legend illustrating the importance of being attentive and helping one parents.', 1);

-- 6. Performers Roster
DROP TABLE IF EXISTS `performers`;
CREATE TABLE `performers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `role` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(50),
    `email` VARCHAR(150),
    `status` VARCHAR(50) DEFAULT 'Available',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `performers` (`name`, `role`, `phone`, `email`, `status`) VALUES
('Kuya Roberto Santos', 'Master Puppeteer & Director', '0917-530-0823', 'roberto@roppets.com', 'Available'),
('Ate Camille & The Bubble Girls', 'Lead Bubble Artist', '0918-123-4567', 'bubblefactory@roppets.com', 'Available'),
('Kuya Mark (Magic Master)', 'Comedy Illusionist', '0922-888-9999', 'magic@roppets.com', 'Available'),
('Rico Dela Vega', 'Mascot Performer & Puppeteer', '0919-444-5555', 'rico@roppets.com', 'Available');

-- 7. Bookings
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `reference_code` VARCHAR(50) UNIQUE NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(50) NOT NULL,
    `event_date` DATE NOT NULL,
    `event_type` VARCHAR(100) NOT NULL,
    `service_package` VARCHAR(150) NOT NULL,
    `location` VARCHAR(255),
    `guest_count` INT DEFAULT 0,
    `notes` TEXT,
    `total_price` DECIMAL(10, 2) DEFAULT 0.00,
    `status` VARCHAR(50) DEFAULT 'Pending',
    `admin_notes` TEXT,
    `downpayment_amount` DECIMAL(10, 2) DEFAULT 0.00,
    `payment_status` VARCHAR(50) DEFAULT 'Unpaid',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Booking Assignments
DROP TABLE IF EXISTS `booking_assignments`;
CREATE TABLE `booking_assignments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_id` INT NOT NULL,
    `performer_id` INT NOT NULL,
    `assigned_role` VARCHAR(100) NOT NULL,
    `notes` TEXT,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (`booking_id`),
    INDEX (`performer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Payments
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_id` INT NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `payment_method` VARCHAR(50) NOT NULL,
    `transaction_ref` VARCHAR(100),
    `proof_file` VARCHAR(255),
    `status` VARCHAR(50) DEFAULT 'Pending',
    `admin_notes` TEXT,
    `verified_at` DATETIME,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Reviews
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_id` INT,
    `client_name` VARCHAR(150) NOT NULL,
    `location` VARCHAR(150),
    `event_type` VARCHAR(100) DEFAULT 'Birthday Party',
    `rating` INT DEFAULT 5,
    `comment` TEXT NOT NULL,
    `is_approved` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (`is_approved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `reviews` (`client_name`, `location`, `event_type`, `rating`, `comment`, `is_approved`) VALUES
('Maria Santos', 'Quezon City', 'Birthday Party', 5, 'Roppets made my daughter 7th birthday truly unforgettable! The puppetry had all 80 kids laughing and learning about kindness.', 1),
('Principal Evelyn Reyes', 'Ateneo Grade School, QC', 'School Tour', 5, 'The 3 moral story plays were wonderfully produced. Very educational and kept the entire elementary batch thoroughly engaged.', 1),
('Atty. Fernando Gomez', 'Ayala Alabang, Muntinlupa', 'Birthday Party', 5, 'The Bubble Factory performance was world-class. Putting our celebrant inside a giant bubble was the highlight of the day!', 1);

-- 11. Gallery Items
DROP TABLE IF EXISTS `gallery_items`;
CREATE TABLE `gallery_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `media_type` VARCHAR(20) DEFAULT 'image',
    `media_url` VARCHAR(255) NOT NULL,
    `caption` TEXT,
    `is_featured` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Activity Logs
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `admin_user` VARCHAR(100) NOT NULL,
    `action` VARCHAR(100) NOT NULL,
    `details` TEXT,
    `ip_address` VARCHAR(50),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. Fabrication Quotes
DROP TABLE IF EXISTS `fabrication_quotes`;
CREATE TABLE `fabrication_quotes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `client_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(50) NOT NULL,
    `item_type` VARCHAR(100) NOT NULL,
    `quantity` INT DEFAULT 1,
    `blueprint_file` VARCHAR(255),
    `specifications` TEXT,
    `target_delivery` DATE,
    `status` VARCHAR(50) DEFAULT 'Under Review',
    `estimated_quote` DECIMAL(10, 2),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
