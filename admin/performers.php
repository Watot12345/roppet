<?php
$adminPageTitle = "Performers & Production Crew";
$adminActiveNav = "performers";
require_once __DIR__ . '/includes/admin-header.php';

$pdo = getDBConnection();
$message = '';
$error = '';

// Handle Add Performer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (!empty($name) && !empty($role)) {
            $stmt = $pdo->prepare("INSERT INTO performers (name, role, phone, status) VALUES (?, ?, ?, 'Available')");
            $stmt->execute([$name, $role, $phone]);
            $message = "Performer / crew member added successfully!";
        } else {
            $error = "Please fill in all required fields.";
        }
    } elseif ($_POST['action'] === 'toggle_status') {
        $id = intval($_POST['performer_id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? 'Available');
        $stmt = $pdo->prepare("UPDATE performers SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
        $message = "Status updated.";
    } elseif ($_POST['action'] === 'delete') {
        $id = intval($_POST['performer_id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM performers WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Performer removed.";
    }
}

$performers = $pdo->query("SELECT * FROM performers ORDER BY role ASC, name ASC")->fetchAll();
?>

<?php if (!empty($message)): ?>
  <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
    <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> <?php echo htmlspecialchars($message); ?>
  </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
  
  <!-- Add New Performer Card -->
  <div class="admin-card">
    <h3 style="font-size: 16px; font-weight: 700; color: var(--admin-text); margin-bottom: 18px;">
      <i class="fa-solid fa-user-plus" style="color: var(--admin-primary); margin-right: 6px;"></i> Add New Crew Member
    </h3>

    <form method="POST" action="performers.php">
      <input type="hidden" name="action" value="add">

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Full Name *</label>
        <input type="text" name="name" required placeholder="e.g. Kuya Alex Santos" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Specialization / Role *</label>
        <select name="role" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
          <option value="Master Puppeteer">Master Puppeteer</option>
          <option value="Bubble Artist (Bubble Factory)">Bubble Artist (Bubble Factory)</option>
          <option value="Black Theater Specialist">Black Theater Specialist</option>
          <option value="Comedy Magician">Comedy Magician</option>
          <option value="Character Mascot Performer">Character Mascot Performer</option>
          <option value="Sound & Light Technician">Sound & Light Technician</option>
          <option value="Event Coordinator & Host">Event Coordinator & Host</option>
        </select>
      </div>

      <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Contact Phone</label>
        <input type="text" name="phone" placeholder="e.g. 0917-123-4567" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
      </div>

      <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; justify-content: center;">
        <i class="fa-solid fa-plus"></i> Save Crew Member
      </button>
    </form>
  </div>

  <!-- Performers List Table -->
  <div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
      <h3 style="font-size: 17px; font-weight: 700; color: var(--admin-text);">Active Talent & Production Roster</h3>
      <span style="font-size: 13px; color: var(--admin-muted);">Total: <?php echo count($performers); ?> staff</span>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Primary Specialization</th>
            <th>Contact</th>
            <th>Availability</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($performers as $p): ?>
            <tr>
              <td>
                <strong><?php echo htmlspecialchars($p['name']); ?></strong>
              </td>
              <td>
                <span class="badge" style="background: rgba(133,46,112,0.1); color: var(--admin-primary);">
                  <?php echo htmlspecialchars($p['role']); ?>
                </span>
              </td>
              <td><?php echo htmlspecialchars($p['phone'] ?: '—'); ?></td>
              <td>
                <form method="POST" action="performers.php" style="display: inline;">
                  <input type="hidden" name="action" value="toggle_status">
                  <input type="hidden" name="performer_id" value="<?php echo $p['id']; ?>">
                  <select name="status" onchange="this.form.submit()" class="form-select-sm" style="font-weight: 600;">
                    <option value="Available" <?php echo ($p['status'] === 'Available') ? 'selected' : ''; ?>>Available</option>
                    <option value="On Leave" <?php echo ($p['status'] === 'On Leave') ? 'selected' : ''; ?>>On Leave</option>
                    <option value="Assigned" <?php echo ($p['status'] === 'Assigned') ? 'selected' : ''; ?>>Assigned</option>
                  </select>
                </form>
              </td>
              <td>
                <form method="POST" action="performers.php" onsubmit="return confirm('Remove this crew member?');" style="display: inline;">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="performer_id" value="<?php echo $p['id']; ?>">
                  <button type="submit" class="btn-admin btn-admin-sm" style="background: #fee2e2; color: #991b1b;">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>
