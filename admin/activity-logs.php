<?php
$adminPageTitle = "Staff Activity Audit Logs";
$adminActiveNav = "logs";
require_once __DIR__ . '/includes/admin-header.php';

$pdo = getDBConnection();
$logs = $pdo->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 100")->fetchAll();
?>

<div class="admin-card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h3 style="font-size: 17px; font-weight: 700; color: var(--admin-text);">System Operation Trail & Security Logs</h3>
    <span style="font-size: 13px; color: var(--admin-muted);">Recent 100 operations</span>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Timestamp</th>
          <th>Staff User</th>
          <th>Action</th>
          <th>Details</th>
          <th>IP Address</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($logs)): ?>
          <tr>
            <td colspan="5" style="text-align: center; color: var(--admin-muted); padding: 35px;">
              No activity logs recorded yet.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($logs as $l): ?>
            <tr>
              <td style="font-size: 12px; color: var(--admin-muted); white-space: nowrap;">
                <?php echo date('M d, Y h:i:s A', strtotime($l['created_at'])); ?>
              </td>
              <td>
                <i class="fa-solid fa-user-shield" style="color: var(--admin-primary); margin-right: 4px;"></i>
                <strong><?php echo htmlspecialchars($l['admin_user']); ?></strong>
              </td>
              <td>
                <span class="badge" style="background: #f1f5f9; color: #334155;">
                  <?php echo htmlspecialchars($l['action']); ?>
                </span>
              </td>
              <td style="font-size: 13px; color: #475569;">
                <?php echo htmlspecialchars($l['details']); ?>
              </td>
              <td style="font-family: monospace; font-size: 12px; color: var(--admin-muted);">
                <?php echo htmlspecialchars($l['ip_address'] ?: '127.0.0.1'); ?>
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
