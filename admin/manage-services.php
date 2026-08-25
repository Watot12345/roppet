<?php
$adminPageTitle = "Manage Entertainment Services (CMS)";
$adminActiveNav = "services_cms";
require_once __DIR__ . '/includes/admin-header.php';

$pdo = getDBConnection();
$message = '';
$error = '';

// Handle Add / Edit / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_service') {
        $id = intval($_POST['service_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title)));
        $category = trim($_POST['category'] ?? 'puppetry');
        $price = floatval($_POST['starting_price'] ?? 0);
        $badge = trim($_POST['badge'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $features = trim($_POST['features'] ?? '');
        $imageUrl = trim($_POST['image_url'] ?? 'assets/images/smurfs-hand-puppet-show-by-roppets-edutainment-production-inc-1-265x265_c.png');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id > 0) {
            $stmt = $pdo->prepare("
                UPDATE services
                SET title = ?, slug = ?, category = ?, starting_price = ?, badge = ?, description = ?, features = ?, image_url = ?, is_active = ?::boolean
                WHERE id = ?
            ");
            $stmt->execute([$title, $slug, $category, $price, $badge, $description, $features, $imageUrl, $isActive, $id]);
            $message = "Service updated successfully!";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO services (title, slug, category, starting_price, badge, description, features, image_url, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?::boolean)
            ");
            $stmt->execute([$title, $slug, $category, $price, $badge, $description, $features, $imageUrl, $isActive]);
            $message = "New service added!";
        }
    } elseif ($_POST['action'] === 'delete') {
        $id = intval($_POST['service_id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Service removed.";
    }
}

$services = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC, id ASC")->fetchAll();
?>

<?php if (!empty($message)): ?>
  <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
    <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> <?php echo htmlspecialchars($message); ?>
  </div>
<?php endif; ?>

<div class="admin-card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h3 style="font-size: 17px; font-weight: 700; color: var(--admin-text);">Entertainment Shows & Services Catalog</h3>
    <button type="button" onclick="openServiceModal()" class="btn-admin btn-admin-primary">
      <i class="fa-solid fa-plus"></i> Add New Service
    </button>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Image</th>
          <th>Service Title</th>
          <th>Category</th>
          <th>Starting Price</th>
          <th>Badge</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($services as $s): ?>
          <tr>
            <td style="width: 60px;">
              <img src="../<?php echo htmlspecialchars($s['image_url']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;" onerror="this.src='../assets/images/logo.png'">
            </td>
            <td>
              <strong><?php echo htmlspecialchars($s['title']); ?></strong><br>
              <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($s['slug']); ?></small>
            </td>
            <td>
              <span class="badge" style="background: #f1f5f9; color: #475569;">
                <?php echo htmlspecialchars($s['category']); ?>
              </span>
            </td>
            <td>
              <strong style="color: var(--admin-primary); font-size: 15px;">₱<?php echo number_format($s['starting_price']); ?></strong>
            </td>
            <td>
              <?php if (!empty($s['badge'])): ?>
                <span class="badge" style="background: rgba(133,46,112,0.1); color: var(--admin-primary);">
                  <?php echo htmlspecialchars($s['badge']); ?>
                </span>
              <?php else: ?>
                <span style="color: var(--admin-muted); font-size: 12px;">—</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge <?php echo $s['is_active'] ? 'badge-confirmed' : 'badge-cancelled'; ?>">
                <?php echo $s['is_active'] ? 'Active' : 'Hidden'; ?>
              </span>
            </td>
            <td>
              <div style="display: flex; gap: 6px;">
                <button type="button" class="btn-admin btn-admin-sm" style="background: #f1f5f9; color: #334155;" onclick='editService(<?php echo json_encode($s, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                  <i class="fa-solid fa-pen"></i> Edit
                </button>
                <form method="POST" action="manage-services.php" onsubmit="return confirm('Delete this service?');" style="display: inline;">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="service_id" value="<?php echo $s['id']; ?>">
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

<!-- Modal Add / Edit Service -->
<div id="serviceModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
  <div style="background: #fff; max-width: 600px; width: 100%; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
    <div style="background: var(--admin-primary); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
      <h3 id="serviceModalTitle" style="font-size: 17px; margin: 0;">Add / Edit Service</h3>
      <button type="button" onclick="closeServiceModal()" style="background: none; border: none; color: #fff; font-size: 20px; cursor: pointer;">&times;</button>
    </div>

    <form method="POST" action="manage-services.php" style="padding: 24px; max-height: 80vh; overflow-y: auto;">
      <input type="hidden" name="action" value="save_service">
      <input type="hidden" id="editServiceId" name="service_id" value="0">

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Service Title *</label>
          <input type="text" id="editTitle" name="title" required placeholder="e.g. Puppet Show" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
        </div>
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Category *</label>
          <select id="editCategory" name="category" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
            <option value="puppetry">Puppet Shows (puppetry)</option>
            <option value="specialty">Specialty Shows (specialty)</option>
            <option value="mascots-magic">Mascots & Magic (mascots-magic)</option>
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Starting Price (₱) *</label>
          <input type="number" id="editPrice" name="starting_price" step="100" required placeholder="4500" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
        </div>
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Badge (Optional)</label>
          <input type="text" id="editBadge" name="badge" placeholder="e.g. Top Seller, Signature Act" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
        </div>
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Image Path / URL</label>
        <input type="text" id="editImage" name="image_url" placeholder="assets/images/..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Short Description</label>
        <textarea id="editDesc" name="description" rows="2" placeholder="Brief summary of the show..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;"></textarea>
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Key Features (One per line)</label>
        <textarea id="editFeatures" name="features" rows="3" placeholder="3 Puppet Plays Included&#10;Complete Sound System" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;"></textarea>
      </div>

      <div style="margin-bottom: 20px;">
        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
          <input type="checkbox" id="editActive" name="is_active" value="1" checked>
          <span>Visible on Website (Active)</span>
        </label>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeServiceModal()" class="btn-admin" style="background: #f1f5f9; color: #334155;">Cancel</button>
        <button type="submit" class="btn-admin btn-admin-primary">Save Service</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openServiceModal() {
    document.getElementById('serviceModalTitle').textContent = 'Add New Service';
    document.getElementById('editServiceId').value = 0;
    document.getElementById('editTitle').value = '';
    document.getElementById('editCategory').value = 'puppetry';
    document.getElementById('editPrice').value = '';
    document.getElementById('editBadge').value = '';
    document.getElementById('editImage').value = 'assets/images/smurfs-hand-puppet-show-by-roppets-edutainment-production-inc-1-265x265_c.png';
    document.getElementById('editDesc').value = '';
    document.getElementById('editFeatures').value = '';
    document.getElementById('editActive').checked = true;
    document.getElementById('serviceModal').style.display = 'flex';
  }

  function editService(s) {
    document.getElementById('serviceModalTitle').textContent = 'Edit Service: ' + s.title;
    document.getElementById('editServiceId').value = s.id;
    document.getElementById('editTitle').value = s.title;
    document.getElementById('editCategory').value = s.category;
    document.getElementById('editPrice').value = s.starting_price;
    document.getElementById('editBadge').value = s.badge || '';
    document.getElementById('editImage').value = s.image_url || '';
    document.getElementById('editDesc').value = s.description;
    document.getElementById('editFeatures').value = s.features || '';
    document.getElementById('editActive').checked = (s.is_active == 1 || s.is_active === true);
    document.getElementById('serviceModal').style.display = 'flex';
  }

  function closeServiceModal() {
    document.getElementById('serviceModal').style.display = 'none';
  }
</script>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>
