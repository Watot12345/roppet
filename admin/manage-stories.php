<?php
$adminPageTitle = "Puppet Moral Stories Catalog (CMS)";
$adminActiveNav = "stories_cms";
require_once __DIR__ . '/includes/admin-header.php';

$pdo = getDBConnection();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_story') {
        $id = intval($_POST['story_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $moral = trim($_POST['moral_lesson'] ?? '');
        $category = trim($_POST['category'] ?? 'Classic Moral');
        $synopsis = trim($_POST['synopsis'] ?? '');

        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE puppet_stories SET title = ?, moral_lesson = ?, category = ?, synopsis = ? WHERE id = ?");
            $stmt->execute([$title, $moral, $category, $synopsis, $id]);
            $message = "Story updated!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO puppet_stories (title, moral_lesson, category, synopsis) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $moral, $category, $synopsis]);
            $message = "New puppet play added to catalog!";
        }
    } elseif ($_POST['action'] === 'delete') {
        $id = intval($_POST['story_id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM puppet_stories WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Story removed.";
    }
}

$stories = $pdo->query("SELECT * FROM puppet_stories ORDER BY id ASC")->fetchAll();
?>

<?php if (!empty($message)): ?>
  <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
    <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> <?php echo htmlspecialchars($message); ?>
  </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
  
  <!-- Add New Story -->
  <div class="admin-card">
    <h3 style="font-size: 16px; font-weight: 700; color: var(--admin-text); margin-bottom: 18px;">
      <i class="fa-solid fa-book-sparkles" style="color: var(--admin-primary); margin-right: 6px;"></i> Add Puppet Play
    </h3>

    <form method="POST" action="manage-stories.php">
      <input type="hidden" name="action" value="save_story">
      <input type="hidden" name="story_id" value="0">

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Story Title *</label>
        <input type="text" name="title" required placeholder="e.g. The Honest Woodcutter" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Moral Lesson / Value *</label>
        <input type="text" name="moral_lesson" required placeholder="e.g. Honesty, respect for elders" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Story Genre / Category</label>
        <select name="category" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
          <option value="Classic Moral">Classic Moral</option>
          <option value="Fable Classic">Fable Classic</option>
          <option value="Filipino Folklore">Filipino Folklore</option>
          <option value="Environmental Awareness">Environmental Awareness</option>
          <option value="Christian Values">Christian Values</option>
        </select>
      </div>

      <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Synopsis / Plot Summary</label>
        <textarea name="synopsis" rows="3" placeholder="Brief plot of the puppet show..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;"></textarea>
      </div>

      <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; justify-content: center;">
        <i class="fa-solid fa-plus"></i> Save Story
      </button>
    </form>
  </div>

  <!-- Stories Catalog Table -->
  <div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
      <h3 style="font-size: 17px; font-weight: 700; color: var(--admin-text);">Moral Puppet Story Catalog</h3>
      <span style="font-size: 13px; color: var(--admin-muted);">Catalog: <?php echo count($stories); ?> plays</span>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Story Title</th>
            <th>Core Moral Lesson</th>
            <th>Category</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($stories as $st): ?>
            <tr>
              <td>
                <strong style="color: var(--admin-primary);"><?php echo htmlspecialchars($st['title']); ?></strong>
                <?php if (!empty($st['synopsis'])): ?>
                  <br><small style="color: var(--admin-muted);"><?php echo htmlspecialchars($st['synopsis']); ?></small>
                <?php endif; ?>
              </td>
              <td style="font-size: 13px; color: #334155;">
                <i class="fa-solid fa-heart" style="color: #ef4444; font-size: 11px; margin-right: 4px;"></i>
                <?php echo htmlspecialchars($st['moral_lesson']); ?>
              </td>
              <td>
                <span class="badge" style="background: #f1f5f9; color: #475569;">
                  <?php echo htmlspecialchars($st['category']); ?>
                </span>
              </td>
              <td>
                <form method="POST" action="manage-stories.php" onsubmit="return confirm('Delete this story play?');" style="display: inline;">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="story_id" value="<?php echo $st['id']; ?>">
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
