<?php
$adminPageTitle = "Client Reviews & Ratings (CMS)";
$adminActiveNav = "reviews_cms";
require_once __DIR__ . '/includes/admin-header.php';

$pdo = getDBConnection();
$message = '';

// Handle review actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $revId = intval($_POST['review_id'] ?? 0);
    if ($_POST['action'] === 'toggle_approval') {
        $status = intval($_POST['is_approved'] ?? 0);
        $stmt = $pdo->prepare("UPDATE reviews SET is_approved = ?::boolean WHERE id = ?");
        $stmt->execute([$status, $revId]);
        $message = "Review approval status updated.";
    } elseif ($_POST['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$revId]);
        $message = "Review deleted.";
    }
}

$reviews = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC")->fetchAll();
?>

<?php if (!empty($message)): ?>
  <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
    <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> <?php echo htmlspecialchars($message); ?>
  </div>
<?php endif; ?>

<div class="admin-card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h3 style="font-size: 17px; font-weight: 700; color: var(--admin-text);">Client Feedback & Testimonials</h3>
    <span style="font-size: 13px; color: var(--admin-muted);">Total Reviews: <?php echo count($reviews); ?></span>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Client Name</th>
          <th>Rating</th>
          <th>Review Feedback</th>
          <th>Location / Event</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($reviews)): ?>
          <tr>
            <td colspan="6" style="text-align: center; color: var(--admin-muted); padding: 35px;">
              No client reviews submitted yet.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($reviews as $r): ?>
            <tr>
              <td>
                <strong><?php echo htmlspecialchars($r['client_name']); ?></strong><br>
                <small style="color: var(--admin-muted);"><?php echo date('M d, Y', strtotime($r['created_at'])); ?></small>
              </td>
              <td>
                <div style="color: #f59e0b; font-size: 13px;">
                  <?php for ($i = 0; $i < $r['rating']; $i++): ?><i class="fa-solid fa-star"></i><?php endfor; ?>
                </div>
              </td>
              <td style="max-width: 320px; font-size: 13px; color: #334155;">
                "<?php echo htmlspecialchars($r['comment']); ?>"
              </td>
              <td>
                <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($r['location'] ?: 'Philippines'); ?> (<?php echo htmlspecialchars($r['event_type']); ?>)</small>
              </td>
              <td>
                <span class="badge <?php echo $r['is_approved'] ? 'badge-confirmed' : 'badge-pending'; ?>">
                  <?php echo $r['is_approved'] ? 'Approved' : 'Pending'; ?>
                </span>
              </td>
              <td>
                <div style="display: flex; gap: 6px;">
                  <form method="POST" action="manage-reviews.php" style="display: inline;">
                    <input type="hidden" name="action" value="toggle_approval">
                    <input type="hidden" name="review_id" value="<?php echo $r['id']; ?>">
                    <input type="hidden" name="is_approved" value="<?php echo $r['is_approved'] ? 0 : 1; ?>">
                    <button type="submit" class="btn-admin btn-admin-sm" style="background: <?php echo $r['is_approved'] ? '#fee2e2; color: #991b1b;' : '#dcfce7; color: #166534;'; ?>">
                      <?php echo $r['is_approved'] ? '<i class="fa-solid fa-eye-slash"></i> Hide' : '<i class="fa-solid fa-check"></i> Approve'; ?>
                    </button>
                  </form>

                  <form method="POST" action="manage-reviews.php" onsubmit="return confirm('Delete this review?');" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="review_id" value="<?php echo $r['id']; ?>">
                    <button type="submit" class="btn-admin btn-admin-sm" style="background: #fee2e2; color: #991b1b;">
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
