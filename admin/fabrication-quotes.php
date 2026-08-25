<?php
$adminPageTitle = "Custom Mascot & Plush Fabrication Requests";
$adminActiveNav = "fabrication_cms";
require_once __DIR__ . '/includes/admin-header.php';

$pdo = getDBConnection();
$message = '';
$error = '';

// Handle Status & Price Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_fab_quote') {
        $id = intval($_POST['quote_id'] ?? 0);
        $status = trim($_POST['status'] ?? 'Under Review');
        $price = floatval($_POST['estimated_price'] ?? 0);
        $notes = trim($_POST['admin_notes'] ?? '');

        try {
            $stmt = $pdo->prepare("
                UPDATE fabrication_quotes
                SET status = :st, estimated_price = :price, admin_notes = :notes
                WHERE id = :id
            ");
            $stmt->execute([':st' => $status, ':price' => $price, ':notes' => $notes, ':id' => $id]);
            $message = "Fabrication quote updated successfully!";
        } catch (Exception $e) {
            $error = "Update error: " . $e->getMessage();
        }
    }
}

$quotes = $pdo->query("SELECT * FROM fabrication_quotes ORDER BY created_at DESC")->fetchAll();
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
    <h3 style="font-size: 17px; font-weight: 700; color: var(--admin-text);">Custom Mascot & Plush Fabrication Blueprint Requests</h3>
    <span style="font-size: 13px; color: var(--admin-muted);">Total Inquiries: <?php echo count($quotes); ?></span>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Quote Ref</th>
          <th>Client & Company</th>
          <th>Type & Qty</th>
          <th>Target Delivery</th>
          <th>Blueprint / Sketch</th>
          <th>Estimated Quote</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($quotes)): ?>
          <tr>
            <td colspan="8" style="text-align: center; color: var(--admin-muted); padding: 35px;">
              No custom fabrication inquiries submitted yet.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($quotes as $q): ?>
            <tr>
              <td>
                <strong style="font-family: monospace; color: var(--admin-primary); font-size: 14px;">
                  <?php echo htmlspecialchars($q['quote_ref']); ?>
                </strong><br>
                <small style="color: var(--admin-muted); font-size: 11px;"><?php echo date('M d, Y', strtotime($q['created_at'])); ?></small>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($q['client_name']); ?></strong><br>
                <?php if (!empty($q['company_name'])): ?>
                  <small style="color: var(--admin-primary); font-weight: 600;"><?php echo htmlspecialchars($q['company_name']); ?></small><br>
                <?php endif; ?>
                <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($q['phone']); ?></small>
              </td>
              <td>
                <strong style="color: var(--admin-text);"><?php echo htmlspecialchars($q['fabrication_type']); ?></strong><br>
                <small style="color: var(--admin-muted);">Qty: <?php echo intval($q['quantity']); ?> pcs</small>
                <?php if ($q['cooling_fan']): ?>
                  <br><span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 10px;">+ Built-in Cooling Fan</span>
                <?php endif; ?>
              </td>
              <td>
                <?php echo !empty($q['target_delivery']) ? date('M d, Y', strtotime($q['target_delivery'])) : 'Flexible'; ?>
              </td>
              <td>
                <?php if (!empty($q['blueprint_file'])): ?>
                  <a href="../uploads/blueprints/<?php echo htmlspecialchars($q['blueprint_file']); ?>" target="_blank" class="btn-admin btn-admin-sm" style="background: rgba(133,46,112,0.1); color: var(--admin-primary);">
                    <i class="fa-solid fa-image"></i> View Blueprint
                  </a>
                <?php else: ?>
                  <span style="color: var(--admin-muted); font-size: 12px;">No upload</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($q['estimated_price'] > 0): ?>
                  <strong style="color: #16a34a; font-size: 15px;">₱<?php echo number_format($q['estimated_price']); ?></strong>
                <?php else: ?>
                  <span style="color: #d97706; font-size: 12px; font-weight: 600;">Pending Quote</span>
                <?php endif; ?>
              </td>
              <td>
                <?php
                  $st = $q['status'];
                  $badgeClass = 'badge-pending';
                  if ($st === 'Quoted') $badgeClass = 'badge-paid';
                  elseif ($st === 'In Production') $badgeClass = 'badge-confirmed';
                  elseif ($st === 'Completed') $badgeClass = 'badge-completed';
                  elseif ($st === 'Declined') $badgeClass = 'badge-cancelled';
                ?>
                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($st); ?></span>
              </td>
              <td>
                <button type="button" class="btn-admin btn-admin-sm" style="background: #f1f5f9; color: #334155;" onclick='openFabModal(<?php echo json_encode($q, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                  <i class="fa-solid fa-calculator"></i> Quote / Edit
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Edit Quote -->
<div id="fabQuoteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
  <div style="background: #fff; max-width: 620px; width: 100%; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
    <div style="background: var(--admin-primary); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 17px; margin: 0;">Mascot / Plush Fabrication Quote Manager</h3>
      <button type="button" onclick="closeFabModal()" style="background: none; border: none; color: #fff; font-size: 20px; cursor: pointer;">&times;</button>
    </div>

    <form method="POST" action="fabrication-quotes.php" style="padding: 24px; max-height: 80vh; overflow-y: auto;">
      <input type="hidden" name="action" value="update_fab_quote">
      <input type="hidden" id="editFabId" name="quote_id" value="0">

      <div style="margin-bottom: 14px;">
        <small style="color: var(--admin-muted); text-transform: uppercase;">Client Specs & Description:</small>
        <div id="fabClientDesc" style="background: #f8fafc; padding: 12px; border-radius: 6px; font-size: 13.5px; color: #334155; margin-top: 4px;">-</div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Quotation Status</label>
          <select id="editFabStatus" name="status" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px;">
            <option value="Under Review">Under Review</option>
            <option value="Quoted">Quoted (Sent to Client)</option>
            <option value="In Production">In Production (Approved)</option>
            <option value="Completed">Completed & Delivered</option>
            <option value="Declined">Declined</option>
          </select>
        </div>
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Quoted Price (₱)</label>
          <input type="number" id="editFabPrice" name="estimated_price" step="500" placeholder="e.g. 35000" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px;">
        </div>
      </div>

      <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Workshop & Costing Notes (Internal)</label>
        <textarea id="editFabNotes" name="admin_notes" rows="3" placeholder="Materials breakdown: high-density foam, velour fabric, internal cooling fan battery specs..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;"></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeFabModal()" class="btn-admin" style="background: #f1f5f9; color: #334155;">Cancel</button>
        <button type="submit" class="btn-admin btn-admin-primary">Save Quote</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openFabModal(q) {
    document.getElementById('editFabId').value = q.id;
    document.getElementById('fabClientDesc').innerHTML = '<strong>' + q.fabrication_type + '</strong> (Qty: ' + q.quantity + ' pcs)<br>' +
      '<em>Fabric Preference:</em> ' + (q.fabric_preference || 'Standard') + '<br>' +
      '<em>Target Delivery:</em> ' + (q.target_delivery || 'Flexible') + '<br><br>' +
      '<strong>Project Description:</strong><br>' + q.description;
    document.getElementById('editFabStatus').value = q.status;
    document.getElementById('editFabPrice').value = q.estimated_price;
    document.getElementById('editFabNotes').value = q.admin_notes || '';
    document.getElementById('fabQuoteModal').style.display = 'flex';
  }

  function closeFabModal() {
    document.getElementById('fabQuoteModal').style.display = 'none';
  }
</script>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>
