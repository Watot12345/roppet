<?php
$adminPageTitle = "Manage Party Packages (CMS)";
$adminActiveNav = "packages_cms";
require_once __DIR__ . '/includes/admin-header.php';

$pdo = getDBConnection();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_package') {
        $id = intval($_POST['package_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name)));
        $subtitle = trim($_POST['subtitle'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $features = trim($_POST['features'] ?? '');
        $isPopular = isset($_POST['is_popular']) ? 1 : 0;
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id > 0) {
            $stmt = $pdo->prepare("
                UPDATE packages
                SET name = ?, slug = ?, subtitle = ?, price = ?, features = ?, is_popular = ?::boolean, is_active = ?::boolean
                WHERE id = ?
            ");
            $stmt->execute([$name, $slug, $subtitle, $price, $features, $isPopular, $isActive, $id]);
            $message = "Package updated successfully!";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO packages (name, slug, subtitle, price, features, is_popular, is_active)
                VALUES (?, ?, ?, ?, ?, ?::boolean, ?::boolean)
            ");
            $stmt->execute([$name, $slug, $subtitle, $price, $features, $isPopular, $isActive]);
            $message = "New package created!";
        }
    } elseif ($_POST['action'] === 'delete') {
        $id = intval($_POST['package_id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM packages WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Package removed.";
    }
}

$packages = $pdo->query("SELECT * FROM packages ORDER BY sort_order ASC, price ASC")->fetchAll();
?>

<?php if (!empty($message)): ?>
  <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
    <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> <?php echo htmlspecialchars($message); ?>
  </div>
<?php endif; ?>

<div class="admin-card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h3 style="font-size: 17px; font-weight: 700; color: var(--admin-text);">Party Packages & Pricing Tiers</h3>
    <button type="button" onclick="openPackageModal()" class="btn-admin btn-admin-primary">
      <i class="fa-solid fa-plus"></i> Add New Package
    </button>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Package Name</th>
          <th>Subtitle</th>
          <th>Price</th>
          <th>Popular Badge</th>
          <th>Inclusions Preview</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($packages as $p): ?>
          <tr>
            <td>
              <strong><?php echo htmlspecialchars($p['name']); ?></strong><br>
              <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($p['slug']); ?></small>
            </td>
            <td style="color: var(--admin-muted); font-size: 13px;">
              <?php echo htmlspecialchars($p['subtitle'] ?: '—'); ?>
            </td>
            <td>
              <strong style="color: var(--admin-primary); font-size: 16px;">₱<?php echo number_format($p['price']); ?></strong>
            </td>
            <td>
              <?php if ($p['is_popular']): ?>
                <span class="badge" style="background: #fef3c7; color: #b45309;">
                  <i class="fa-solid fa-star"></i> Most Popular
                </span>
              <?php else: ?>
                <span style="color: var(--admin-muted); font-size: 12px;">Standard</span>
              <?php endif; ?>
            </td>
            <td style="max-width: 220px; font-size: 12.5px; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
              <?php echo htmlspecialchars($p['features']); ?>
            </td>
            <td>
              <span class="badge <?php echo $p['is_active'] ? 'badge-confirmed' : 'badge-cancelled'; ?>">
                <?php echo $p['is_active'] ? 'Active' : 'Hidden'; ?>
              </span>
            </td>
            <td>
              <div style="display: flex; gap: 6px;">
                <button type="button" class="btn-admin btn-admin-sm" style="background: #f1f5f9; color: #334155;" onclick='editPackage(<?php echo json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                  <i class="fa-solid fa-pen"></i> Edit
                </button>
                <form method="POST" action="manage-packages.php" onsubmit="return confirm('Delete this package?');" style="display: inline;">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="package_id" value="<?php echo $p['id']; ?>">
                  <button type="submit" class="btn-admin btn-admin-sm" style="background: #fee2e2; color: #991b1b;">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Add / Edit Package -->
<div id="packageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
  <div style="background: #fff; max-width: 600px; width: 100%; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
    <div style="background: var(--admin-primary); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
      <h3 id="packageModalTitle" style="font-size: 17px; margin: 0;">Add / Edit Package</h3>
      <button type="button" onclick="closePackageModal()" style="background: none; border: none; color: #fff; font-size: 20px; cursor: pointer;">&times;</button>
    </div>

    <form method="POST" action="manage-packages.php" style="padding: 24px; max-height: 80vh; overflow-y: auto;">
      <input type="hidden" name="action" value="save_package">
      <input type="hidden" id="editPkgId" name="package_id" value="0">

      <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 14px; margin-bottom: 14px;">
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Package Name *</label>
          <input type="text" id="editPkgName" name="name" required placeholder="e.g. Supreme Package" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
        </div>
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Package Price (₱) *</label>
          <input type="number" id="editPkgPrice" name="price" step="100" required placeholder="11500" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
        </div>
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Subtitle / Tagline</label>
        <input type="text" id="editPkgSubtitle" name="subtitle" placeholder="e.g. Ideal for clubhouse parties" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Package Inclusions (One per line) *</label>
        <textarea id="editPkgFeatures" name="features" rows="5" required placeholder="Original Hand Puppet Show (3 Stories)&#10;The Bubble Factory Show (3 Girls)&#10;1 Mascot of choice" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;"></textarea>
      </div>

      <div style="display: flex; gap: 20px; margin-bottom: 20px;">
        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
          <input type="checkbox" id="editPkgPopular" name="is_popular" value="1">
          <span>Mark as "Most Popular"</span>
        </label>

        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
          <input type="checkbox" id="editPkgActive" name="is_active" value="1" checked>
          <span>Visible (Active)</span>
        </label>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closePackageModal()" class="btn-admin" style="background: #f1f5f9; color: #334155;">Cancel</button>
        <button type="submit" class="btn-admin btn-admin-primary">Save Package</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openPackageModal() {
    document.getElementById('packageModalTitle').textContent = 'Add New Package';
    document.getElementById('editPkgId').value = 0;
    document.getElementById('editPkgName').value = '';
    document.getElementById('editPkgPrice').value = '';
    document.getElementById('editPkgSubtitle').value = '';
    document.getElementById('editPkgFeatures').value = '';
    document.getElementById('editPkgPopular').checked = false;
    document.getElementById('editPkgActive').checked = true;
    document.getElementById('packageModal').style.display = 'flex';
  }

  function editPackage(p) {
    document.getElementById('packageModalTitle').textContent = 'Edit Package: ' + p.name;
    document.getElementById('editPkgId').value = p.id;
    document.getElementById('editPkgName').value = p.name;
    document.getElementById('editPkgPrice').value = p.price;
    document.getElementById('editPkgSubtitle').value = p.subtitle || '';
    document.getElementById('editPkgFeatures').value = p.features;
    document.getElementById('editPkgPopular').checked = (p.is_popular == 1 || p.is_popular === true);
    document.getElementById('editPkgActive').checked = (p.is_active == 1 || p.is_active === true);
    document.getElementById('packageModal').style.display = 'flex';
  }

  function closePackageModal() {
    document.getElementById('packageModal').style.display = 'none';
  }
</script>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>
