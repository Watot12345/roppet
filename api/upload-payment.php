<?php
/**
 * Payment Proof Upload API Endpoint
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$refCode = trim($_POST['reference_code'] ?? '');
$paymentMethod = trim($_POST['payment_method'] ?? 'GCash');
$transactionRef = trim($_POST['transaction_ref'] ?? '');
$amount = floatval($_POST['amount'] ?? 0);

if (empty($refCode) || $amount <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a valid reference code and payment amount.']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Find booking
    $stmt = $pdo->prepare("SELECT id, name, status FROM bookings WHERE UPPER(reference_code) = UPPER(:ref)");
    $stmt->execute([':ref' => $refCode]);
    $booking = $stmt->fetch();

    if (!$booking) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Booking not found for reference: ' . htmlspecialchars($refCode)]);
        exit;
    }

    $receiptFileName = '';

    // Handle File Upload
    if (isset($_FILES['receipt_file']) && $_FILES['receipt_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['receipt_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

        if (!in_array($ext, $allowed)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid file format. Allowed: JPG, PNG, WEBP, PDF']);
            exit;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit.']);
            exit;
        }

        $uploadDir = __DIR__ . '/../uploads/receipts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $receiptFileName = sprintf("receipt_%s_%s.%s", $booking['id'], time(), $ext);
        $targetPath = $uploadDir . $receiptFileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file.']);
            exit;
        }
    }

    // Insert payment record
    $insPayment = $pdo->prepare("
        INSERT INTO payments (
            booking_id, reference_code, payment_method,
            transaction_ref, amount, receipt_file, status
        ) VALUES (
            :bid, :ref, :method, :tref, :amt, :file, 'Pending Review'
        )
    ");
    $insPayment->execute([
        ':bid' => $booking['id'],
        ':ref' => $refCode,
        ':method' => $paymentMethod,
        ':tref' => $transactionRef,
        ':amt' => $amount,
        ':file' => $receiptFileName
    ]);

    // Update booking payment status
    $upBooking = $pdo->prepare("
        UPDATE bookings
        SET payment_status = 'Pending Review', downpayment_amount = :amt
        WHERE id = :id
    ");
    $upBooking->execute([':amt' => $amount, ':id' => $booking['id']]);

    echo json_encode([
        'success' => true,
        'message' => 'Proof of payment uploaded successfully! Our staff will verify and confirm your downpayment.',
        'payment' => [
            'reference_code' => $refCode,
            'amount' => $amount,
            'status' => 'Pending Review'
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
