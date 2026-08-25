<?php
$adminPageTitle = "Payment Verification Inbox";
$adminActiveNav = "payments";
require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/../includes/mailer.php';

$pdo = getDBConnection();
$message = '';
$error = '';

// Handle Approve / Reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $paymentId = intval($_POST['payment_id'] ?? 0);
    $bookingId = intval($_POST['booking_id'] ?? 0);
    $action = $_POST['action'];

    if ($action === 'verify') {
        try {
            $pdo->beginTransaction();

            $pStmt = $pdo->prepare("SELECT amount FROM payments WHERE id = ?");
            $pStmt->execute([$paymentId]);
            $amount = floatval($pStmt->fetchColumn() ?: 0);

            // 1. Update Payment status
            $stmt1 = $pdo->prepare("UPDATE payments SET status = 'Verified', verified_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt1->execute([$paymentId]);

            // 2. Update Booking status
            $stmt2 = $pdo->prepare("
                UPDATE bookings
                SET status = 'Downpayment Paid', downpayment_amount = downpayment_amount + :amt
                WHERE id = :bid
            ");
            $stmt2->execute([':amt' => $amount, ':bid' => $bookingId]);

            $pdo->commit();

            logStaffActivity($_SESSION['admin_name'] ?? 'Admin', 'Verified Payment', "Payment ID: $paymentId, Booking ID: $bookingId, Amount: ₱$amount");
            $message = "Payment of ₱" . number_format($amount) . " verified successfully! Booking status set to 'Downpayment Paid'.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error verifying payment: " . $e->getMessage();
        }
    } elseif ($action === 'reject') {
        try {
            $notes = trim($_POST['reject_reason'] ?? 'Invalid proof / mismatch');
            $stmt = $pdo->prepare("UPDATE payments SET status = 'Rejected', admin_notes = ? WHERE id = ?");
            $stmt->execute([$notes, $paymentId]);

            logStaffActivity($_SESSION['admin_name'] ?? 'Admin', 'Rejected Payment', "Payment ID: $paymentId, Reason: $notes");
            $message = "Payment marked as Rejected.";
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Fetch all payment receipts with booking client names
$sql = "
    SELECT p.*, b.reference_code, b.name AS client_name, b.email AS client_email, b.phone AS client_phone, b.event_date, b.total_price
    FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    ORDER BY (p.status = 'Pending Review') DESC, p.created_at DESC
";
$payments = $pdo->query($sql)->fetchAll();
?>

<?php if (!empty($message)): ?>
  <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
    <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> <?php echo htmlspecialchars($message); ?>
  </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
  <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
    <i class="fa-solid fa-circle-exclamation" style="margin-right: 6px;"></i> <?php echo htmlspecialchars($error); ?>
  </div>
<?php endif; ?>

<div class="admin-card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h3 style="font-size: 17px; font-weight: 700; color: var(--admin-text);">Client Proof of Payment Submissions</h3>
    <span style="font-size: 13px; color: var(--admin-muted);">Total Submissions: <?php echo count($payments); ?></span>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Ref Code & Date</th>
          <th>Client Info</th>
          <th>Method</th>
          <th>Amount Paid</th>
          <th>Transaction Ref</th>
          <th>Receipt File</th>
          <th>Status</th>
          <th>Verification Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($payments)): ?>
          <tr>
            <td colspan="8" style="text-align: center; color: var(--admin-muted); padding: 40px;">
              No payment receipts submitted yet.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($payments as $pay): ?>
            <tr>
              <td>
                <strong style="font-family: monospace; color: var(--admin-primary); font-size: 14px;">
                  <?php echo htmlspecialchars($pay['reference_code']); ?>
                </strong><br>
                <small style="color: var(--admin-muted); font-size: 11px;">
                  <?php echo date('M d, Y h:i A', strtotime($pay['created_at'])); ?>
                </small>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($pay['client_name']); ?></strong><br>
                <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($pay['client_phone']); ?></small>
              </td>
              <td>
                <span class="badge" style="background: #f1f5f9; color: #334155;">
                  <?php echo htmlspecialchars($pay['payment_method']); ?>
                </span>
              </td>
              <td>
                <strong style="color: #16a34a; font-size: 15px;">₱<?php echo number_format($pay['amount']); ?></strong><br>
                <small style="color: var(--admin-muted);">Total: ₱<?php echo number_format($pay['total_price']); ?></small>
              </td>
              <td style="font-family: monospace; font-size: 13px;">
                <?php echo htmlspecialchars($pay['transaction_ref'] ?: 'N/A'); ?>
              </td>
              <td>
                <?php if (!empty($pay['receipt_file'])): ?>
                  <a href="../uploads/receipts/<?php echo htmlspecialchars($pay['receipt_file']); ?>" target="_blank" class="btn-admin btn-admin-sm" style="background: rgba(133,46,112,0.1); color: var(--admin-primary);">
                    <i class="fa-solid fa-image"></i> View Receipt
                  </a>
                <?php else: ?>
                  <span style="color: var(--admin-muted); font-size: 12px;">No file</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($pay['status'] === 'Verified'): ?>
                  <span class="badge badge-confirmed"><i class="fa-solid fa-check-double"></i> Verified</span>
                <?php elseif ($pay['status'] === 'Rejected'): ?>
                  <span class="badge badge-cancelled"><i class="fa-solid fa-xmark"></i> Rejected</span>
                <?php else: ?>
                  <span class="badge badge-pending"><i class="fa-solid fa-clock"></i> Pending Review</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($pay['status'] === 'Pending Review'): ?>
                  <div style="display: flex; gap: 6px;">
                    <form method="POST" action="payments.php" onsubmit="return confirm('Confirm receipt verification?');">
                      <input type="hidden" name="action" value="verify">
                      <input type="hidden" name="payment_id" value="<?php echo $pay['id']; ?>">
                      <input type="hidden" name="booking_id" value="<?php echo $pay['booking_id']; ?>">
                      <button type="submit" class="btn-admin btn-admin-sm btn-admin-primary" style="background: #16a34a;">
                        <i class="fa-solid fa-check"></i> Approve
                      </button>
                    </form>

                    <form method="POST" action="payments.php" onsubmit="return confirm('Reject this payment receipt?');">
                      <input type="hidden" name="action" value="reject">
                      <input type="hidden" name="payment_id" value="<?php echo $pay['id']; ?>">
                      <button type="submit" class="btn-admin btn-admin-sm" style="background: #fee2e2; color: #991b1b;">
                        <i class="fa-solid fa-xmark"></i> Reject
                      </button>
                    </form>
                  </div>
                <?php else: ?>
                  <small style="color: var(--admin-muted);">Processed</small>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>
