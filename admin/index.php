<?php
$adminPageTitle = "Dashboard Overview";
$adminActiveNav = "dashboard";
require_once __DIR__ . '/includes/admin-header.php';

$pdo = getDBConnection();

// Fetch summary metrics
$totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pendingBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'Pending'")->fetchColumn();
$confirmedBookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status IN ('Confirmed', 'Downpayment Paid')")->fetchColumn();
$totalInquiries = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'Unread'")->fetchColumn();
$pipelineRevenue = $pdo->query("SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE status NOT IN ('Cancelled')")->fetchColumn();

// Fetch recent bookings
$recentBookings = $pdo->query("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 6")->fetchAll();

// Fetch recent inquiries
$recentInquiries = $pdo->query("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<!-- Metrics KPI Cards -->
<div class="kpi-grid">
  <div class="kpi-card">
    <div>
      <div class="kpi-label">Pending Inquiries</div>
      <div class="kpi-number" style="color: #d97706;"><?php echo number_format($pendingBookings); ?></div>
    </div>
    <div class="kpi-icon" style="background: #fef3c7; color: #d97706;">
      <i class="fa-solid fa-clock"></i>
    </div>
  </div>

  <div class="kpi-card">
    <div>
      <div class="kpi-label">Confirmed / Paid Events</div>
      <div class="kpi-number" style="color: #16a34a;"><?php echo number_format($confirmedBookings); ?></div>
    </div>
    <div class="kpi-icon" style="background: #dcfce7; color: #16a34a;">
      <i class="fa-solid fa-calendar-check"></i>
    </div>
  </div>

  <div class="kpi-card">
    <div>
      <div class="kpi-label">Total Bookings</div>
      <div class="kpi-number"><?php echo number_format($totalBookings); ?></div>
    </div>
    <div class="kpi-icon" style="background: rgba(133, 46, 112, 0.1); color: var(--admin-primary);">
      <i class="fa-solid fa-masks-theater"></i>
    </div>
  </div>

  <div class="kpi-card">
    <div>
      <div class="kpi-label">Estimated Pipeline</div>
      <div class="kpi-number" style="font-size: 26px;">₱<?php echo number_format($pipelineRevenue); ?></div>
    </div>
    <div class="kpi-icon" style="background: #e0f2fe; color: #0284c7;">
      <i class="fa-solid fa-peso-sign"></i>
    </div>
  </div>
</div>

<!-- Recent Bookings Table -->
<div class="admin-card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
    <h3 style="font-size: 17px; font-weight: 700; color: var(--admin-text);">Recent Event Reservations</h3>
    <a href="bookings.php" class="btn-admin btn-admin-primary btn-admin-sm">
      View All Bookings <i class="fa-solid fa-arrow-right"></i>
    </a>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Ref Code</th>
          <th>Client Name</th>
          <th>Event Date</th>
          <th>Service / Package</th>
          <th>Location</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recentBookings)): ?>
          <tr>
            <td colspan="8" style="text-align: center; color: var(--admin-muted); padding: 30px;">
              No bookings recorded yet.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($recentBookings as $b): ?>
            <tr>
              <td>
                <strong style="font-family: monospace; color: var(--admin-primary); font-size: 13px;">
                  <?php echo htmlspecialchars($b['reference_code']); ?>
                </strong>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($b['name']); ?></strong><br>
                <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($b['phone']); ?></small>
              </td>
              <td><?php echo htmlspecialchars($b['event_date']); ?></td>
              <td><?php echo htmlspecialchars($b['service_package']); ?></td>
              <td><?php echo htmlspecialchars($b['location'] ?: '—'); ?></td>
              <td>
                <strong>₱<?php echo number_format($b['total_price']); ?></strong>
              </td>
              <td>
                <?php
                  $st = $b['status'];
                  $badgeClass = 'badge-pending';
                  if ($st === 'Confirmed') $badgeClass = 'badge-confirmed';
                  elseif ($st === 'Downpayment Paid') $badgeClass = 'badge-paid';
                  elseif ($st === 'Completed') $badgeClass = 'badge-completed';
                  elseif ($st === 'Cancelled') $badgeClass = 'badge-cancelled';
                ?>
                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($st); ?></span>
              </td>
              <td>
                <a href="bookings.php?search=<?php echo urlencode($b['reference_code']); ?>" class="btn-admin btn-admin-sm" style="background: #f1f5f9; color: #334155;">
                  Manage
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Recent Inquiries -->
<div class="admin-card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
    <h3 style="font-size: 17px; font-weight: 700; color: var(--admin-text);">Recent Contact Inquiries</h3>
    <a href="inquiries.php" class="btn-admin btn-admin-sm" style="background: #f1f5f9; color: #334155;">
      Go to Inbox <i class="fa-solid fa-inbox"></i>
    </a>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Sender</th>
          <th>Subject / Service</th>
          <th>Message Preview</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recentInquiries)): ?>
          <tr>
            <td colspan="6" style="text-align: center; color: var(--admin-muted); padding: 25px;">
              No contact messages recorded yet.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($recentInquiries as $inq): ?>
            <tr>
              <td style="font-size: 12px; color: var(--admin-muted);">
                <?php echo date('M d, Y', strtotime($inq['created_at'])); ?>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($inq['name']); ?></strong><br>
                <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($inq['email']); ?></small>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($inq['subject']); ?></strong><br>
                <small style="color: var(--admin-primary);"><?php echo htmlspecialchars($inq['service']); ?></small>
              </td>
              <td style="max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--admin-muted);">
                <?php echo htmlspecialchars($inq['message']); ?>
              </td>
              <td>
                <span class="badge <?php echo ($inq['status'] === 'Unread') ? 'badge-unread' : 'badge-read'; ?>">
                  <?php echo htmlspecialchars($inq['status']); ?>
                </span>
              </td>
              <td>
                <a href="inquiries.php" class="btn-admin btn-admin-sm" style="background: #f1f5f9; color: #334155;">
                  View
                </a>
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
