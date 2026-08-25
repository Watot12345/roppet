<?php
/**
 * Booking Status Tracking API Endpoint
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../includes/db.php';

$refCode = trim($_GET['ref'] ?? $_POST['ref'] ?? '');

if (empty($refCode)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid Reference Code.']);
    exit;
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT reference_code, name, event_date, event_type, service_package, location, total_price, status, created_at
        FROM bookings
        WHERE UPPER(reference_code) = UPPER(:ref)
    ");
    $stmt->execute([':ref' => $refCode]);
    $booking = $stmt->fetch();

    if ($booking) {
        echo json_encode([
            'success' => true,
            'booking' => $booking
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'No booking found matching reference code: ' . htmlspecialchars($refCode)
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error querying tracking database: ' . $e->getMessage()
    ]);
}
