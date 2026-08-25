<?php
$adminPageTitle = "Contact Inquiries Inbox";
$adminActiveNav = "inquiries";
require_once __DIR__ . '/includes/admin-header.php';

$pdo = getDBConnection();
$message = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $inqId = intval($_POST['inquiry_id'] ?? 0);
    if ($_POST['action'] === 'mark_read') {
        $stmt = $pdo->prepare("UPDATE inquiries SET status = 'Read' WHERE id = :id");
        $stmt->execute([':id' => $inqId]);
        $message = "Inquiry marked as read.";
    } elseif ($_POST['action'] === 'mark_replied') {
        $stmt = $pdo->prepare("UPDATE inquiries SET status = 'Replied' WHERE id = :id");
        $stmt->execute([':id' => $inqId]);
        $message = "Inquiry marked as replied.";
    } elseif ($_POST['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM inquiries WHERE id = :id");
        $stmt->execute([':id' => $inqId]);
        $message = "Inquiry deleted.";
    }
}

$inquiries = $pdo->query("SELECT * FROM inquiries ORDER BY created_at DESC")->fetchAll();
?>

<?php if (!empty($message)): ?>
  <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
    <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> <?php echo htmlspecialchars($message); ?>
  </div>
<?php endif; ?>

<div class="admin-card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h3 style="font-size: 17px; font-weight: 700; color: var(--admin-text);">All Messages & Inquiries</h3>
    <span style="color: var(--admin-muted); font-size: 13px;">Total: <?php echo count($inquiries); ?> messages</span>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Date Received</th>
          <th>Sender Info</th>
          <th>Subject / Service</th>
          <th>Message Details</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($inquiries)): ?>
          <tr>
            <td colspan="6" style="text-align: center; color: var(--admin-muted); padding: 35px;">
              No contact messages in your inbox.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($inquiries as $inq): ?>
            <tr>
              <td style="font-size: 12px; color: var(--admin-muted); white-space: nowrap;">
                <?php echo date('M d, Y h:i A', strtotime($inq['created_at'])); ?>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($inq['name']); ?></strong><br>
                <a href="mailto:<?php echo htmlspecialchars($inq['email']); ?>" style="color: #0284c7; font-size: 12.5px;">
                  <?php echo htmlspecialchars($inq['email']); ?>
                </a><br>
                <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($inq['phone'] ?: 'No phone provided'); ?></small>
              </td>
              <td>
                <strong style="color: var(--admin-text);"><?php echo htmlspecialchars($inq['subject']); ?></strong><br>
                <span class="badge" style="background: rgba(133,46,112,0.1); color: var(--admin-primary); font-size: 11px; margin-top: 4px;">
                  <?php echo htmlspecialchars($inq['service']); ?>
                </span>
              </td>
              <td style="max-width: 320px; font-size: 13.5px; color: #334155;">
                <?php echo nl2br(htmlspecialchars($inq['message'])); ?>
              </td>
              <td>
                <?php
                  $st = $inq['status'];
                  $badgeClass = 'badge-read';
                  if ($st === 'Unread') $badgeClass = 'badge-unread';
                  elseif ($st === 'Replied') $badgeClass = 'badge-confirmed';
                ?>
                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($st); ?></span>
              </td>
              <td style="white-space: nowrap;">
                <div style="display: flex; gap: 6px;">
                  <a href="mailto:<?php echo htmlspecialchars($inq['email']); ?>?subject=Re: <?php echo urlencode($inq['subject']); ?>" class="btn-admin btn-admin-sm btn-admin-primary" title="Reply via Email">
                    <i class="fa-solid fa-reply"></i> Reply
                  </a>

                  <?php if ($inq['status'] === 'Unread'): ?>
                    <form method="POST" action="inquiries.php" style="display: inline;">
                      <input type="hidden" name="action" value="mark_read">
                      <input type="hidden" name="inquiry_id" value="<?php echo $inq['id']; ?>">
                      <button type="submit" class="btn-admin btn-admin-sm" style="background: #f1f5f9; color: #334155;" title="Mark as Read">
                        <i class="fa-solid fa-envelope-open"></i>
                      </button>
                    </form>
                  <?php endif; ?>

                  <form method="POST" action="inquiries.php" onsubmit="return confirm('Are you sure you want to delete this message?');" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="inquiry_id" value="<?php echo $inq['id']; ?>">
                    <button type="submit" class="btn-admin btn-admin-sm" style="background: #fee2e2; color: #991b1b;" title="Delete">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>
                </div>
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
