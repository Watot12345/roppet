<?php
/**
 * Create PayMongo Checkout Session API
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/paymongo.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!$data) $data = $_POST;

$refCode = trim($data['reference_code'] ?? '');
if (empty($refCode)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing reference code.']);
    exit;
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE reference_code = :ref");
    $stmt->execute([':ref' => $refCode]);
    $booking = $stmt->fetch();

    if (!$booking) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Booking not found.']);
        exit;
    }

    $totalPrice = floatval($booking['total_price']);
    $downpayment = $totalPrice > 0 ? ($totalPrice * 0.5) : 3000.00;

    $requestedAmount = floatval($data['amount'] ?? $downpayment);
    if ($requestedAmount <= 0) $requestedAmount = $downpayment;

    $desc = "50% Downpayment for Roppets {$booking['service_package']} ({$refCode})";
    $result = PayMongoGateway::createCheckoutSession($booking, $requestedAmount, $desc);

    echo json_encode($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gateway error: ' . $e->getMessage()]);
}
