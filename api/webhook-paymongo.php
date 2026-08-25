<?php
/**
 * PayMongo Webhook Listener
 * Automatically verifies payments and updates PostgreSQL booking status
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/mailer.php';

$rawPayload = file_get_contents('php://input');
$event = json_decode($rawPayload, true);

if (!$event || !isset($event['data']['attributes']['type'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid webhook payload']);
    exit;
}

$eventType = $event['data']['attributes']['type'];

if ($eventType === 'checkout_session.payment.paid') {
    $sessionData = $event['data']['attributes']['data']['attributes'] ?? [];
    $refCode = $sessionData['reference_number'] ?? '';
    
    // Amount in PHP (converted from cents)
    $amountPaid = 0;
    $paymentsList = $sessionData['payments'] ?? [];
    if (!empty($paymentsList)) {
        $amountPaid = ($paymentsList[0]['attributes']['amount'] ?? 0) / 100;
        $paymentSource = $paymentsList[0]['attributes']['source']['type'] ?? 'PayMongo Gateway';
        $paymongoId = $paymentsList[0]['id'] ?? 'pay_auto';
    } else {
        $paymentSource = 'PayMongo Automated Gateway';
        $paymongoId = 'pm_' . bin2hex(random_bytes(4));
    }

    if (!empty($refCode)) {
        try {
            $pdo = getDBConnection();
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT id, name, email, event_date, service_package FROM bookings WHERE reference_code = ?");
            $stmt->execute([$refCode]);
            $booking = $stmt->fetch();

            if ($booking) {
                $bookingId = $booking['id'];

                // 1. Insert verified payment record
                $insPay = $pdo->prepare("
                    INSERT INTO payments (
                        booking_id, amount, payment_method, transaction_ref, status, admin_notes, verified_at
                    ) VALUES (
                        ?, ?, ?, ?, 'Verified', 'Automated PayMongo Webhook Reconciliation', CURRENT_TIMESTAMP
                    )
                ");
                $insPay->execute([$bookingId, $amountPaid, ucfirst($paymentSource), $paymongoId]);

                // 2. Update booking status
                $updBook = $pdo->prepare("
                    UPDATE bookings
                    SET status = 'Downpayment Paid', downpayment_amount = downpayment_amount + ?
                    WHERE id = ?
                ");
                $updBook->execute([$amountPaid, $bookingId]);

                $pdo->commit();

                // 3. Send Official Receipt Email to Client (Phase 4)
                sendPaymentReceiptToClient($booking, $amountPaid, ucfirst($paymentSource), $refCode);

                // 4. Log staff activity
                logStaffActivity('System / PayMongo Webhook', 'Auto Verified Payment', "Ref: {$refCode}, Amount: ₱{$amountPaid} via {$paymentSource}");
            } else {
                $pdo->rollBack();
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
        }
    }
}

http_response_code(200);
echo json_encode(['status' => 'success']);
