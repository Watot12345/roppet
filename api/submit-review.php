<?php
/**
 * Public Review Submission API
 * Roppets Edutainment Production Inc.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Read JSON or POST body
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$clientName = trim($input['client_name'] ?? '');
$location = trim($input['location'] ?? 'Metro Manila');
$eventType = trim($input['event_type'] ?? 'Birthday Party');
$rating = intval($input['rating'] ?? 5);
$comment = trim($input['comment'] ?? '');
$refCode = trim($input['reference_code'] ?? '');

if (empty($clientName)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide your name.']);
    exit;
}

if (empty($comment)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please write your feedback/review.']);
    exit;
}

if ($rating < 1 || $rating > 5) {
    $rating = 5;
}

try {
    $pdo = getDBConnection();

    // Check optional booking reference
    $bookingId = null;
    if (!empty($refCode)) {
        $stmt = $pdo->prepare("SELECT id FROM bookings WHERE reference_code = ? LIMIT 1");
        $stmt->execute([$refCode]);
        $bookingId = $stmt->fetchColumn() ?: null;
    }

    $insStmt = $pdo->prepare("
        INSERT INTO reviews (booking_id, client_name, location, event_type, rating, comment, is_approved)
        VALUES (?, ?, ?, ?, ?, ?, FALSE)
    ");
    $insStmt->execute([$bookingId, $clientName, $location, $eventType, $rating, $comment]);

    // Optional activity log if table exists
    try {
        $logStmt = $pdo->prepare("
            INSERT INTO activity_logs (admin_user, action, details, ip_address)
            VALUES ('Public Customer', 'Submitted Review', ?, ?)
        ");
        $logStmt->execute([
            "{$clientName} submitted a {$rating}-star review for {$eventType}",
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);
    } catch (Exception $e) {}

    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your review! Your feedback has been received and will appear on our website once verified by our team.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
