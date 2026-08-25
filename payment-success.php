<?php
$pageTitle = "Payment Successful | Roppets Edutainment Production Inc.";
$currentNav = "track";
include_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/mailer.php';

$refCode = trim($_GET['ref'] ?? '');
$amount = floatval($_GET['amount'] ?? 0);
$method = trim($_GET['method'] ?? 'GCash / Online Banking');
$isSimulated = isset($_GET['simulated']);

$booking = null;
if (!empty($refCode)) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE reference_code = :ref");
        $stmt->execute([':ref' => $refCode]);
        $booking = $stmt->fetch();

        // If simulated or successful checkout return, auto-record payment in DB if not already paid
        if ($booking && ($isSimulated || $amount > 0)) {
            $checkPay = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE booking_id = ? AND status = 'Verified'");
            $checkPay->execute([$booking['id']]);
            $verifiedCount = $checkPay->fetchColumn();

            if ($verifiedCount == 0 && $amount > 0) {
                $pdo->beginTransaction();
                $transRef = 'PM-' . strtoupper(bin2hex(random_bytes(4)));

                $ins = $pdo->prepare("
                    INSERT INTO payments (booking_id, amount, payment_method, transaction_ref, status, admin_notes, verified_at)
                    VALUES (?, ?, ?, ?, 'Verified', 'Instant PayMongo Gateway Payment', CURRENT_TIMESTAMP)
                ");
                $ins->execute([$booking['id'], $amount, $method, $transRef]);

                $upd = $pdo->prepare("
                    UPDATE bookings
                    SET status = 'Downpayment Paid', downpayment_amount = downpayment_amount + ?
                    WHERE id = ?
                ");
                $upd->execute([$amount, $booking['id']]);
                $pdo->commit();

                logStaffActivity('PayMongo Gateway', 'Auto-Verified Downpayment', "Ref: {$refCode}, Amount: ₱{$amount} via {$method}");
            }
        }
    } catch (Exception $e) {}
}
?>

  <!-- Success Hero Banner -->
  <section class="section-padding" style="background: #ffffff; min-height: 600px; display: flex; align-items: center;">
    <div class="container" style="max-width: 680px; text-align: center;">
      
      <div style="background: #faf5f9; border-radius: var(--border-radius-lg); border: 2px solid #86efac; padding: 45px 35px; box-shadow: var(--shadow-lg);">
        
        <div style="width: 85px; height: 85px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 42px; margin: 0 auto 20px auto; box-shadow: 0 10px 25px rgba(22, 163, 74, 0.2);">
          <i class="fa-solid fa-check"></i>
        </div>

        <span class="section-badge" style="background: #dcfce7; color: #166534; font-size: 13px; margin-bottom: 8px;">Payment Verified & Recorded</span>
        <h1 style="font-size: 32px; color: var(--dark-text); margin: 6px 0 12px 0;">Downpayment Received!</h1>
        <p style="color: var(--muted-text); font-size: 15.5px; line-height: 1.6; margin-bottom: 25px;">
          Thank you! Your downpayment for <strong><?php echo htmlspecialchars($booking['service_package'] ?? 'Roppets Show'); ?></strong> has been processed. Your event schedule is officially secured.
        </p>

        <!-- Receipt Summary Box -->
        <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--border-radius-md); padding: 20px; text-align: left; margin-bottom: 30px;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
            <span style="color: var(--muted-text); font-size: 13.5px;">Reference Code:</span>
            <strong style="font-family: monospace; color: var(--primary); font-size: 16px;"><?php echo htmlspecialchars($refCode); ?></strong>
          </div>

          <div style="display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
            <span style="color: var(--muted-text); font-size: 13.5px;">Client Name:</span>
            <strong><?php echo htmlspecialchars($booking['name'] ?? 'Roppets Client'); ?></strong>
          </div>

          <div style="display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
            <span style="color: var(--muted-text); font-size: 13.5px;">Event Date:</span>
            <strong><?php echo htmlspecialchars($booking['event_date'] ?? 'Upcoming'); ?></strong>
          </div>

          <div style="display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
            <span style="color: var(--muted-text); font-size: 13.5px;">Payment Channel:</span>
            <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 600;"><?php echo htmlspecialchars($method); ?></span>
          </div>

          <div style="display: flex; justify-content: space-between; padding-top: 4px;">
            <span style="color: var(--muted-text); font-size: 14px; font-weight: 700;">Amount Credited:</span>
            <strong style="font-size: 18px; color: #16a34a;">₱<?php echo number_format($amount > 0 ? $amount : 3500); ?></strong>
          </div>
        </div>

        <!-- Action Links -->
        <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
          <a href="track.php?ref=<?php echo urlencode($refCode); ?>" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-magnifying-glass-location"></i> View Reservation Status
          </a>
          <a href="invoice.php?ref=<?php echo urlencode($refCode); ?>" target="_blank" class="btn btn-outline btn-lg">
            <i class="fa-solid fa-print"></i> Official Invoice Agreement
          </a>
        </div>

      </div>

    </div>
  </section>

<?php
include_once __DIR__ . '/includes/footer.php';
?>
