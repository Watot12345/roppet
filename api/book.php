<?php
/**
 * Quick Booking & Quotation Request API Endpoint
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/mailer.php';

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// Get JSON input
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    $data = $_POST;
}

// Sanitize & validate fields
$name = trim($data['name'] ?? '');
$phone = trim($data['phone'] ?? '');
$email = trim($data['email'] ?? '');
$eventDate = trim($data['event_date'] ?? '');
$eventType = trim($data['event_type'] ?? 'Birthday Party');
$servicePackage = trim($data['service_package'] ?? 'General Inquiry');
$location = trim($data['location'] ?? '');
$guestCount = intval($data['guest_count'] ?? 0);
$notes = trim($data['notes'] ?? '');

if (empty($name) || empty($phone) || empty($email) || empty($eventDate)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please provide full name, contact phone, email, and event date.'
    ]);
    exit;
}

// Calculate base price estimate according to chosen service
$estimatedPrice = 4500.00;
$pkgLower = strtolower($servicePackage);
if (strpos($pkgLower, 'bubble') !== false) {
    $estimatedPrice = 6000.00;
} elseif (strpos($pkgLower, 'teatro') !== false) {
    $estimatedPrice = 7500.00;
} elseif (strpos($pkgLower, 'marionette') !== false) {
    $estimatedPrice = 5500.00;
} elseif (strpos($pkgLower, 'kiddie fun') !== false) {
    $estimatedPrice = 6500.00;
} elseif (strpos($pkgLower, 'classic combo') !== false) {
    $estimatedPrice = 8500.00;
} elseif (strpos($pkgLower, 'supreme') !== false) {
    $estimatedPrice = 11500.00;
} elseif (strpos($pkgLower, 'grand') !== false) {
    $estimatedPrice = 18500.00;
} elseif (strpos($pkgLower, 'magic') !== false) {
    $estimatedPrice = 4000.00;
} elseif (strpos($pkgLower, 'mascot') !== false) {
    $estimatedPrice = 3500.00;
}

try {
    $pdo = getDBConnection();

    // 1. Capacity & Blackout Date Guard (Phase 3)
    $maxShowsPerDay = intval(getSystemSetting('max_shows_per_day', 5));
    if ($maxShowsPerDay <= 0) $maxShowsPerDay = 5;

    $blackoutStr = getSystemSetting('blackout_dates', '2026-12-25,2027-01-01');
    $blackoutDates = array_filter(array_map('trim', explode(',', $blackoutStr)));

    if (in_array($eventDate, $blackoutDates)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Our theater and performers are on holiday break on this date. Please choose an alternative date.'
        ]);
        exit;
    }

    $stmtCap = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE event_date = :dt AND status NOT IN ('Cancelled')");
    $stmtCap->execute([':dt' => $eventDate]);
    $activeOnDate = (int)$stmtCap->fetchColumn();

    if ($activeOnDate >= $maxShowsPerDay) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "This date has reached maximum production capacity ({$maxShowsPerDay} shows). Please select another date or contact our office."
        ]);
        exit;
    }

    // Generate unique reference code: ROP-YYYY-XXXX (e.g. ROP-2026-8912)
    $year = date('Y');
    $isUnique = false;
    $refCode = '';
    $attempts = 0;

    while (!$isUnique && $attempts < 10) {
        $attempts++;
        $randNum = mt_rand(1000, 9999);
        $refCode = "ROP-{$year}-{$randNum}";

        $stmtCheck = $pdo->prepare("SELECT id FROM bookings WHERE reference_code = :ref");
        $stmtCheck->execute([':ref' => $refCode]);
        if (!$stmtCheck->fetch()) {
            $isUnique = true;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO bookings (
            reference_code, name, phone, email, event_date, 
            event_type, service_package, location, guest_count, 
            notes, total_price, status
        ) VALUES (
            :ref, :name, :phone, :email, :event_date,
            :event_type, :service_package, :location, :guest_count,
            :notes, :price, 'Pending'
        ) RETURNING id
    ");

    $stmt->execute([
        ':ref' => $refCode,
        ':name' => $name,
        ':phone' => $phone,
        ':email' => $email,
        ':event_date' => $eventDate,
        ':event_type' => $eventType,
        ':service_package' => $servicePackage,
        ':location' => $location,
        ':guest_count' => $guestCount,
        ':notes' => $notes,
        ':price' => $estimatedPrice
    ]);

    $bookingId = $stmt->fetchColumn();

    // Trigger Client Email Notification
    sendBookingConfirmationToClient([
        'reference_code' => $refCode,
        'name' => $name,
        'email' => $email,
        'event_date' => $eventDate,
        'service_package' => $servicePackage,
        'total_price' => $estimatedPrice
    ]);

    echo json_encode([
        'success' => true,
        'booking_id' => $bookingId,
        'reference_code' => $refCode,
        'message' => 'Your booking request has been successfully recorded in our system!'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
