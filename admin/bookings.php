<?php
$adminPageTitle = "Bookings & Event Reservations";
$adminActiveNav = "bookings";
require_once __DIR__ . '/includes/admin-header.php';

$pdo = getDBConnection();
$message = '';
$error = '';

// Handle Status Update / Notes update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $bookingId = intval($_POST['booking_id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? 'Pending');
        $adminNotes = trim($_POST['admin_notes'] ?? '');
        $totalPrice = floatval($_POST['total_price'] ?? 0);

        try {
            $stmt = $pdo->prepare("
                UPDATE bookings
                SET status = :status, admin_notes = :notes, total_price = :price
                WHERE id = :id
            ");
            $stmt->execute([
                ':status' => $newStatus,
                ':notes' => $adminNotes,
                ':price' => $totalPrice,
                ':id' => $bookingId
            ]);
            $message = "Booking status updated successfully!";
        } catch (Exception $e) {
            $error = "Update error: " . $e->getMessage();
        }
    }
}

// Filters & Search
$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? 'all');

$sql = "SELECT * FROM bookings WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (LOWER(name) LIKE LOWER(:s) OR LOWER(email) LIKE LOWER(:s) OR LOWER(phone) LIKE LOWER(:s) OR LOWER(reference_code) LIKE LOWER(:s))";
    $params[':s'] = "%$search%";
}

if ($statusFilter !== 'all' && !empty($statusFilter)) {
    $sql .= " AND status = :st";
    $params[':st'] = $statusFilter;
}

$sql .= " ORDER BY event_date ASC, created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();
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

<!-- Filter & Search Toolbar -->
<div class="admin-card" style="padding: 18px 24px;">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <form method="GET" action="bookings.php" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; flex: 1;">
      <div style="flex: 1; min-width: 220px;">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by Client, Ref Code, Phone..." style="width: 100%; padding: 9px 14px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
      </div>
      <div>
        <select name="status" style="padding: 9px 14px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
          <option value="all" <?php echo ($statusFilter === 'all') ? 'selected' : ''; ?>>All Statuses</option>
          <option value="Pending" <?php echo ($statusFilter === 'Pending') ? 'selected' : ''; ?>>Pending Only</option>
          <option value="Confirmed" <?php echo ($statusFilter === 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
          <option value="Downpayment Paid" <?php echo ($statusFilter === 'Downpayment Paid') ? 'selected' : ''; ?>>Downpayment Paid</option>
          <option value="Completed" <?php echo ($statusFilter === 'Completed') ? 'selected' : ''; ?>>Completed</option>
          <option value="Cancelled" <?php echo ($statusFilter === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
        </select>
      </div>
      <button type="submit" class="btn-admin btn-admin-primary">
        <i class="fa-solid fa-filter"></i> Filter
      </button>
      <?php if (!empty($search) || $statusFilter !== 'all'): ?>
        <a href="bookings.php" class="btn-admin" style="background: #f1f5f9; color: #475569;">Reset</a>
      <?php endif; ?>
    </form>

    <div>
      <a href="export.php" class="btn-admin" style="background: #dcfce7; color: #166534;">
        <i class="fa-solid fa-file-csv"></i> Export CSV
      </a>
    </div>
  </div>
</div>

<!-- Bookings List Table -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Ref Code</th>
          <th>Client & Contact</th>
          <th>Event Date</th>
          <th>Show / Package</th>
          <th>Venue Location</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Operations & Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($bookings)): ?>
          <tr>
            <td colspan="8" style="text-align: center; color: var(--admin-muted); padding: 40px;">
              No bookings matched your filter criteria.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($bookings as $b): ?>
            <tr>
              <td>
                <strong style="font-family: monospace; color: var(--admin-primary); font-size: 14px;">
                  <?php echo htmlspecialchars($b['reference_code']); ?>
                </strong><br>
                <small style="color: var(--admin-muted); font-size: 11px;">
                  <?php echo date('M d, Y', strtotime($b['created_at'])); ?>
                </small>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($b['name']); ?></strong><br>
                <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($b['phone']); ?></small><br>
                <small style="color: #0284c7;"><?php echo htmlspecialchars($b['email']); ?></small>
              </td>
              <td>
                <strong style="color: #1e293b;"><?php echo htmlspecialchars($b['event_date']); ?></strong><br>
                <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($b['event_type']); ?></small>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($b['service_package']); ?></strong><br>
                <small style="color: var(--admin-muted);">Guests: <?php echo intval($b['guest_count']); ?></small>
              </td>
              <td style="max-width: 170px; font-size: 13px;">
                <?php echo htmlspecialchars($b['location'] ?: 'Not specified'); ?>
              </td>
              <td>
                <strong style="font-size: 14px; color: var(--admin-primary);">₱<?php echo number_format($b['total_price']); ?></strong>
                <?php if ($b['downpayment_amount'] > 0): ?>
                  <br><small style="color: #16a34a; font-weight: 600;">Paid: ₱<?php echo number_format($b['downpayment_amount']); ?></small>
                <?php endif; ?>
              </td>
              <td>
                <form method="POST" action="bookings.php" style="display: flex; flex-direction: column; gap: 4px;">
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                  <input type="hidden" name="total_price" value="<?php echo $b['total_price']; ?>">
                  <input type="hidden" name="admin_notes" value="<?php echo htmlspecialchars($b['admin_notes'] ?? ''); ?>">
                  <select name="status" onchange="this.form.submit()" class="form-select-sm" style="font-weight: 600;">
                    <option value="Pending" <?php echo ($b['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Confirmed" <?php echo ($b['status'] === 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="Downpayment Paid" <?php echo ($b['status'] === 'Downpayment Paid') ? 'selected' : ''; ?>>Downpayment Paid</option>
                    <option value="Completed" <?php echo ($b['status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="Cancelled" <?php echo ($b['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                  </select>
                </form>
              </td>
              <td style="white-space: nowrap;">
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                  <a href="assign.php?id=<?php echo $b['id']; ?>" class="btn-admin btn-admin-sm" style="background: rgba(133,46,112,0.1); color: var(--admin-primary);" title="Assign Performers & Crew">
                    <i class="fa-solid fa-users"></i> Dispatch Crew
                  </a>
                  <a href="../invoice.php?ref=<?php echo urlencode($b['reference_code']); ?>" target="_blank" class="btn-admin btn-admin-sm" style="background: #f1f5f9; color: #334155;" title="Print Quotation">
                    <i class="fa-solid fa-print"></i> Quote
                  </a>
                  <button type="button" class="btn-admin btn-admin-sm" style="background: #f1f5f9; color: #334155;" onclick="openDetailsModal(<?php echo htmlspecialchars(json_encode($b)); ?>)">
                    <i class="fa-solid fa-pen-to-square"></i> Notes
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Details & Edit Modal -->
<div id="bookingDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
  <div style="background: #fff; max-width: 600px; width: 100%; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
    <div style="background: var(--admin-primary); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 17px; margin: 0;">Event Reservation Details</h3>
      <button type="button" onclick="closeDetailsModal()" style="background: none; border: none; color: #fff; font-size: 20px; cursor: pointer;">&times;</button>
    </div>
    
    <div style="padding: 24px; max-height: 80vh; overflow-y: auto;">
      <form method="POST" action="bookings.php">
        <input type="hidden" name="action" value="update_status">
        <input type="hidden" id="modalBookingId" name="booking_id" value="">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
          <div>
            <small style="color: var(--admin-muted); text-transform: uppercase;">Ref Code:</small>
            <div id="modalRefCode" style="font-family: monospace; font-weight: 700; font-size: 16px; color: var(--admin-primary);">-</div>
          </div>
          <div>
            <small style="color: var(--admin-muted); text-transform: uppercase;">Event Date:</small>
            <div id="modalEventDate" style="font-weight: 600;">-</div>
          </div>
        </div>

        <div style="margin-bottom: 14px;">
          <small style="color: var(--admin-muted); text-transform: uppercase;">Client Name & Phone:</small>
          <div id="modalClientInfo" style="font-weight: 600;">-</div>
        </div>

        <div style="margin-bottom: 14px;">
          <small style="color: var(--admin-muted); text-transform: uppercase;">Client Special Notes / Request:</small>
          <div id="modalClientNotes" style="background: #f8fafc; padding: 10px; border-radius: 6px; font-style: italic; color: #475569; margin-top: 4px;">-</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
          <div>
            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Status</label>
            <select id="modalStatus" name="status" style="width: 100%; padding: 8px; border: 1px solid var(--admin-border); border-radius: 6px;">
              <option value="Pending">Pending</option>
              <option value="Confirmed">Confirmed</option>
              <option value="Downpayment Paid">Downpayment Paid</option>
              <option value="Completed">Completed</option>
              <option value="Cancelled">Cancelled</option>
            </select>
          </div>
          <div>
            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Agreed Total Price (₱)</label>
            <input type="number" id="modalPrice" name="total_price" step="100" style="width: 100%; padding: 8px; border: 1px solid var(--admin-border); border-radius: 6px;">
          </div>
        </div>

        <div style="margin-bottom: 20px;">
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Admin & Production Notes (Internal Staff Only)</label>
          <textarea id="modalAdminNotes" name="admin_notes" rows="3" placeholder="e.g. Assigned Team A, Puppeteers: John & Mike, Mascots prepped..." style="width: 100%; padding: 8px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;"></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
          <button type="button" onclick="closeDetailsModal()" class="btn-admin" style="background: #f1f5f9; color: #334155;">Cancel</button>
          <button type="submit" class="btn-admin btn-admin-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function openDetailsModal(b) {
    document.getElementById('modalBookingId').value = b.id;
    document.getElementById('modalRefCode').textContent = b.reference_code;
    document.getElementById('modalEventDate').textContent = b.event_date + ' (' + b.event_type + ')';
    document.getElementById('modalClientInfo').textContent = b.name + ' — ' + b.phone + ' (' + b.email + ')';
    document.getElementById('modalClientNotes').textContent = b.notes || 'No special requests submitted.';
    document.getElementById('modalStatus').value = b.status;
    document.getElementById('modalPrice').value = b.total_price;
    document.getElementById('modalAdminNotes').value = b.admin_notes || '';

    const modal = document.getElementById('bookingDetailsModal');
    modal.style.display = 'flex';
  }

  function closeDetailsModal() {
    document.getElementById('bookingDetailsModal').style.display = 'none';
  }
</script>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>
