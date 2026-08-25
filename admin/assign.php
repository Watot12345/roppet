<?php
$adminPageTitle = "Event Crew Dispatch & Assignment";
$adminActiveNav = "bookings";
require_once __DIR__ . '/includes/admin-header.php';

$pdo = getDBConnection();
$bookingId = intval($_GET['id'] ?? 0);
$message = '';
$error = '';

if ($bookingId <= 0) {
    echo "<div class='admin-card'><p>Invalid booking ID specified. <a href='bookings.php'>Back to Bookings</a></p></div>";
    require_once __DIR__ . '/includes/admin-footer.php';
    exit;
}

// Fetch booking info
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$bookingId]);
$b = $stmt->fetch();

if (!$b) {
    echo "<div class='admin-card'><p>Booking not found. <a href='bookings.php'>Back to Bookings</a></p></div>";
    require_once __DIR__ . '/includes/admin-footer.php';
    exit;
}

// Handle Add Assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'assign_crew') {
        $performerId = intval($_POST['performer_id'] ?? 0);
        $role = trim($_POST['assigned_role'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if ($performerId > 0 && !empty($role)) {
            $ins = $pdo->prepare("
                INSERT INTO booking_assignments (booking_id, performer_id, assigned_role, notes)
                VALUES (?, ?, ?, ?)
            ");
            $ins->execute([$bookingId, $performerId, $role, $notes]);

            // Dispatch briefing email to performer if email exists
            require_once __DIR__ . '/../includes/mailer.php';
            $pStmt = $pdo->prepare("SELECT name, email FROM performers WHERE id = ?");
            $pStmt->execute([$performerId]);
            $perfData = $pStmt->fetch();
            if ($perfData && !empty($perfData['email'])) {
                sendPerformerDispatchBriefing($b, $perfData['email'], $perfData['name'], $role);
            }
            logStaffActivity('Admin', 'Assigned Performer', "Assigned {$perfData['name']} as {$role} to Ref: {$b['reference_code']}");

            $message = "Crew member assigned and show briefing notification sent!";
        } else {
            $error = "Please select a performer and role.";
        }
    } elseif ($_POST['action'] === 'remove_crew') {
        $assignId = intval($_POST['assignment_id'] ?? 0);
        $del = $pdo->prepare("DELETE FROM booking_assignments WHERE id = ? AND booking_id = ?");
        $del->execute([$assignId, $bookingId]);
        $message = "Assignment removed.";
    }
}

// Fetch current assignments
$stmtAssigns = $pdo->prepare("
    SELECT ba.*, p.name AS performer_name, p.phone AS performer_phone, p.role AS default_role
    FROM booking_assignments ba
    JOIN performers p ON ba.performer_id = p.id
    WHERE ba.booking_id = ?
    ORDER BY ba.created_at ASC
");
$stmtAssigns->execute([$bookingId]);
$assignments = $stmtAssigns->fetchAll();

// Fetch available performers
$allPerformers = $pdo->query("SELECT * FROM performers ORDER BY name ASC")->fetchAll();
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

<!-- Event Summary Top Box -->
<div class="admin-card" style="background: linear-gradient(135deg, #faf5f9 0%, #ffffff 100%); border-left: 4px solid var(--admin-primary);">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
      <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--admin-muted);">Dispatching for:</span>
      <h2 style="font-size: 22px; color: var(--admin-primary); margin: 2px 0;"><?php echo htmlspecialchars($b['service_package']); ?></h2>
      <div style="font-size: 13.5px; color: #475569;">
        <strong>Client:</strong> <?php echo htmlspecialchars($b['name']); ?> (<?php echo htmlspecialchars($b['phone']); ?>) | 
        <strong>Date:</strong> <?php echo htmlspecialchars($b['event_date']); ?> | 
        <strong>Venue:</strong> <?php echo htmlspecialchars($b['location'] ?: 'TBD'); ?>
      </div>
    </div>
    <div>
      <a href="bookings.php" class="btn-admin" style="background: #f1f5f9; color: #334155;">
        <i class="fa-solid fa-arrow-left"></i> Back to Bookings
      </a>
      <a href="../invoice.php?ref=<?php echo urlencode($b['reference_code']); ?>" target="_blank" class="btn-admin btn-admin-primary">
        <i class="fa-solid fa-print"></i> View Quotation
      </a>
    </div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 1.2fr 2fr; gap: 30px; align-items: start;">
  
  <!-- Assign Performer Form -->
  <div class="admin-card">
    <h3 style="font-size: 16px; font-weight: 700; color: var(--admin-text); margin-bottom: 18px;">
      <i class="fa-solid fa-user-check" style="color: var(--admin-primary); margin-right: 6px;"></i> Assign Talent / Crew
    </h3>

    <form method="POST" action="assign.php?id=<?php echo $bookingId; ?>">
      <input type="hidden" name="action" value="assign_crew">

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Select Performer *</label>
        <select name="performer_id" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
          <option value="">-- Choose from Roster --</option>
          <?php foreach ($allPerformers as $p): ?>
            <option value="<?php echo $p['id']; ?>">
              <?php echo htmlspecialchars($p['name']); ?> (<?php echo htmlspecialchars($p['role']); ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Assigned Role for This Show *</label>
        <input type="text" name="assigned_role" required placeholder="e.g. Lead Puppeteer (3 Little Pigs) / Mascot Performer" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Call Time & Production Notes</label>
        <textarea name="notes" rows="2" placeholder="e.g. Call time: 1:30 PM, bring stage backdrop & sound mic..." style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;"></textarea>
      </div>

      <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; justify-content: center;">
        <i class="fa-solid fa-plus"></i> Confirm Assignment
      </button>
    </form>
  </div>

  <!-- Assigned Crew List -->
  <div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
      <h3 style="font-size: 17px; font-weight: 700; color: var(--admin-text);">Assigned Show Cast & Crew</h3>
      <span style="font-size: 13px; color: var(--admin-muted);">Assigned: <?php echo count($assignments); ?> crew</span>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Crew Member</th>
            <th>Show Assignment</th>
            <th>Notes & Call Time</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($assignments)): ?>
            <tr>
              <td colspan="4" style="text-align: center; color: var(--admin-muted); padding: 30px;">
                No performers assigned to this event yet. Use the form on the left to dispatch crew.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($assignments as $a): ?>
              <tr>
                <td>
                  <strong><?php echo htmlspecialchars($a['performer_name']); ?></strong><br>
                  <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($a['performer_phone'] ?: 'No phone'); ?></small>
                </td>
                <td>
                  <span class="badge" style="background: rgba(133,46,112,0.1); color: var(--admin-primary);">
                    <?php echo htmlspecialchars($a['assigned_role']); ?>
                  </span>
                </td>
                <td style="font-size: 13px; color: #475569;">
                  <?php echo htmlspecialchars($a['notes'] ?: 'No specific call time notes'); ?>
                </td>
                <td>
                  <form method="POST" action="assign.php?id=<?php echo $bookingId; ?>" onsubmit="return confirm('Remove crew assignment?');" style="display: inline;">
                    <input type="hidden" name="action" value="remove_crew">
                    <input type="hidden" name="assignment_id" value="<?php echo $a['id']; ?>">
                    <button type="submit" class="btn-admin btn-admin-sm" style="background: #fee2e2; color: #991b1b;">
                      <i class="fa-solid fa-xmark"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>
