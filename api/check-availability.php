<?php
/**
 * Date Availability & Capacity Checker API Endpoint
 * Roppets Edutainment Production Inc.
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../includes/db.php';

$date = trim($_GET['date'] ?? '');

if (empty($date)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide an event date.']);
    exit;
}

// Validate date format YYYY-MM-DD
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid date format (expected YYYY-MM-DD).']);
    exit;
}

try {
    $pdo = getDBConnection();

    // 1. Fetch capacity limit & blackout dates
    $maxShowsPerDay = intval(getSystemSetting('max_shows_per_day', 5));
    if ($maxShowsPerDay <= 0) $maxShowsPerDay = 5;

    $blackoutStr = getSystemSetting('blackout_dates', '2026-12-25,2027-01-01');
    $blackoutDates = array_filter(array_map('trim', explode(',', $blackoutStr)));

    // 2. Check if date is in blackout/holiday list
    if (in_array($date, $blackoutDates)) {
        echo json_encode([
            'success' => true,
            'date' => $date,
            'booked_count' => 0,
            'max_capacity' => $maxShowsPerDay,
            'remaining_slots' => 0,
            'status' => 'Closed / Holiday',
            'color' => '#ef4444',
            'message' => 'Our theater and performers are on holiday break on this date. Please choose an alternative date.',
            'can_book' => false,
            'available' => false
        ]);
        exit;
    }

    // 3. Count active bookings for this date
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM bookings
        WHERE event_date = :dt AND status NOT IN ('Cancelled')
    ");
    $stmt->execute([':dt' => $date]);
    $count = (int)$stmt->fetchColumn();

    $remainingSlots = max(0, $maxShowsPerDay - $count);

    if ($count >= $maxShowsPerDay) {
        $status = 'Fully Booked';
        $color = '#ef4444';
        $message = "All {$maxShowsPerDay} performer teams are booked on this date. Please select another date or contact our office.";
        $canBook = false;
    } elseif ($remainingSlots <= 2) {
        $status = "High Demand ({$remainingSlots} slot" . ($remainingSlots === 1 ? '' : 's') . " left)";
        $color = '#f59e0b';
        $message = "Only {$remainingSlots} production slot" . ($remainingSlots === 1 ? ' is' : 's are') . " remaining for this date. Fast reservation recommended.";
        $canBook = true;
    } else {
        $status = "Available ({$remainingSlots} slots left)";
        $color = '#10b981';
        $message = "Performer cast, bubble artists, and stage technicians are available on this date.";
        $canBook = true;
    }

    echo json_encode([
        'success' => true,
        'date' => $date,
        'booked_count' => $count,
        'max_capacity' => $maxShowsPerDay,
        'remaining_slots' => $remainingSlots,
        'status' => $status,
        'color' => $color,
        'message' => $message,
        'can_book' => $canBook,
        'available' => $canBook
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
