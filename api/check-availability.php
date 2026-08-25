<?php
/**
 * Date Availability Checker API Endpoint
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../includes/db.php';

$date = trim($_GET['date'] ?? '');

if (empty($date)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide an event date.']);
    exit;
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM bookings
        WHERE event_date = :dt AND status NOT IN ('Cancelled')
    ");
    $stmt->execute([':dt' => $date]);
    $count = (int)$stmt->fetchColumn();

    $maxShowsPerDay = 5; // Roppets has multiple teams

    if ($count >= $maxShowsPerDay) {
        $status = 'Fully Booked';
        $color = '#ef4444';
        $message = 'Date has reached maximum crew capacity. Please choose another date or contact office.';
        $available = false;
    } elseif ($count >= 2) {
        $status = 'High Demand';
        $color = '#f59e0b';
        $message = "Date has {$count} scheduled events. Fast booking recommended.";
        $available = true;
    } else {
        $status = 'Available';
        $color = '#10b981';
        $message = 'Cast and production crew are available for this date.';
        $available = true;
    }

    echo json_encode([
        'success' => true,
        'date' => $date,
        'booked_count' => $count,
        'status' => $status,
        'color' => $color,
        'message' => $message,
        'available' => $available
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
