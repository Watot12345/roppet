<?php
$adminPageTitle = "Event Photo & Video Gallery (CMS)";
$adminActiveNav = "gallery_cms";
require_once __DIR__ . '/includes/admin-header.php';

$pdo = getDBConnection();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_item') {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'puppetry');
        $caption = trim($_POST['caption'] ?? '');
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
        $mediaUrl = '';

        // Handle Image Upload
        if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['media_file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'mp4'];

            if (in_array($ext, $allowed)) {
                $targetName = sprintf("gallery_%s.%s", time(), $ext);
                $targetPath = __DIR__ . '/../uploads/gallery/' . $targetName;
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $mediaUrl = 'uploads/gallery/' . $targetName;
                }
            }
        } elseif (!empty($_POST['custom_url'])) {
            $mediaUrl = trim($_POST['custom_url']);
        }

        if (!empty($title) && !empty($mediaUrl)) {
            $stmt = $pdo->prepare("
                INSERT INTO gallery_items (title, category, media_type, media_url, caption, is_featured)
                VALUES (?, ?, 'image', ?, ?, ?)
            ");
            $stmt->execute([$title, $category, $mediaUrl, $caption, $isFeatured]);
            $message = "New media item added to the gallery!";
        } else {
            $error = "Please provide a title and attach a photo or image URL.";
        }
    } elseif ($_POST['action'] === 'delete') {
        $id = intval($_POST['item_id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM gallery_items WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Gallery item deleted.";
    }
}

$galleryItems = $pdo->query("SELECT * FROM gallery_items ORDER BY id DESC")->fetchAll();
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

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
  
  <!-- Add New Gallery Item -->
  <div class="admin-card">
    <h3 style="font-size: 16px; font-weight: 700; color: var(--admin-text); margin-bottom: 18px;">
      <i class="fa-solid fa-cloud-arrow-up" style="color: var(--admin-primary); margin-right: 6px;"></i> Upload Media Highlight
    </h3>

    <form method="POST" action="manage-gallery.php" enctype="multipart/form-data">
      <input type="hidden" name="action" value="add_item">

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Show / Event Title *</label>
        <input type="text" name="title" required placeholder="e.g. Ayala Mall Black Theater Tour" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Category *</label>
        <select name="category" style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
          <option value="puppetry">Puppet Shows</option>
          <option value="bubble">The Bubble Factory</option>
          <option value="black-theater">Teatro Itim Black Theater</option>
          <option value="mascots">Character Mascots & Magic</option>
          <option value="fabrication">Custom Mascot Fabrication</option>
        </select>
      </div>

      <!-- Drag & Drop Gallery Uploader -->
      <div style="margin-bottom: 16px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Media Photo *</label>
        
        <div id="galleryDropZone" style="border: 2px dashed #cbd5e1; border-radius: 10px; padding: 18px 14px; text-align: center; background: #fafafa; cursor: pointer; transition: all 0.2s ease;">
          <input type="file" id="galleryFileInput" name="media_file" accept="image/*" style="display: none;">
          
          <div id="galleryPrompt" style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(133,46,112,0.1); color: var(--admin-primary); display: flex; align-items: center; justify-content: center; font-size: 18px;">
              <i class="fa-solid fa-cloud-arrow-up"></i>
            </div>
            <div style="font-size: 13px;">
              <span style="font-weight: 600; color: var(--admin-primary);">Drag & drop photo here</span>, or <span style="text-decoration: underline; color: #2563eb;">browse</span>
            </div>
            <small style="color: var(--admin-muted); font-size: 11px;">JPG, PNG, WEBP, MP4 (Max 10MB)</small>
          </div>

          <div id="galleryPreview" style="display: none; align-items: center; gap: 12px; text-align: left; background: #fff; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--admin-border);">
            <img id="galleryPreviewImg" src="" alt="Preview" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
            <div style="flex: 1; min-width: 0;">
              <strong id="galleryFileName" style="display: block; font-size: 12.5px; color: var(--admin-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></strong>
              <small id="galleryFileSize" style="color: var(--admin-muted); font-size: 11px;"></small>
            </div>
            <button type="button" onclick="clearGalleryImage(event)" style="background: #fee2e2; border: none; color: #991b1b; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">&times;</button>
          </div>
        </div>

        <div style="margin-top: 8px;">
          <input type="text" id="galleryCustomUrl" name="custom_url" placeholder="Or enter image path / URL: assets/images/..." style="width: 100%; padding: 7px 12px; font-size: 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: monospace; color: #64748b; background: #fff;">
        </div>
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Caption / Description</label>
        <textarea name="caption" rows="2" placeholder="Brief event description..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;"></textarea>
      </div>

      <div style="margin-bottom: 20px;">
        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
          <input type="checkbox" name="is_featured" value="1">
          <span>Feature this item on top</span>
        </label>
      </div>

      <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; justify-content: center;">
        <i class="fa-solid fa-plus"></i> Add to Gallery
      </button>
    </form>
  </div>

  <!-- Gallery List -->
  <div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
      <h3 style="font-size: 17px; font-weight: 700; color: var(--admin-text);">Media Gallery Items</h3>
      <a href="../gallery.php" target="_blank" class="btn-admin btn-admin-sm" style="background: #f1f5f9; color: #334155;">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> View Gallery
      </a>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Thumbnail</th>
            <th>Title & Caption</th>
            <th>Category</th>
            <th>Featured</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($galleryItems as $item): ?>
            <tr>
              <td style="width: 60px;">
                <img src="../<?php echo htmlspecialchars($item['media_url']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;" onerror="this.src='../assets/images/logo.png'">
              </td>
              <td>
                <strong><?php echo htmlspecialchars($item['title']); ?></strong><br>
                <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($item['caption'] ?: 'No caption'); ?></small>
              </td>
              <td>
                <span class="badge" style="background: rgba(133,46,112,0.1); color: var(--admin-primary);">
                  <?php echo htmlspecialchars($item['category']); ?>
                </span>
              </td>
              <td>
                <?php if ($item['is_featured']): ?>
                  <span class="badge" style="background: #fef3c7; color: #b45309;"><i class="fa-solid fa-star"></i> Yes</span>
                <?php else: ?>
                  <span style="color: var(--admin-muted); font-size: 12px;">No</span>
                <?php endif; ?>
              </td>
              <td>
                <form method="POST" action="manage-gallery.php" onsubmit="return confirm('Delete this media item?');" style="display: inline;">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
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

<script>
  const gDropZone = document.getElementById('galleryDropZone');
  const gFileInput = document.getElementById('galleryFileInput');
  const gPromptEl = document.getElementById('galleryPrompt');
  const gPreviewEl = document.getElementById('galleryPreview');
  const gPreviewImg = document.getElementById('galleryPreviewImg');
  const gFileNameEl = document.getElementById('galleryFileName');
  const gFileSizeEl = document.getElementById('galleryFileSize');
  const gCustomUrlInput = document.getElementById('galleryCustomUrl');

  if (gDropZone && gFileInput) {
    gDropZone.addEventListener('click', (e) => {
      if (e.target.tagName !== 'BUTTON') {
        gFileInput.click();
      }
    });

    ['dragenter', 'dragover'].forEach(name => {
      gDropZone.addEventListener(name, (e) => {
        e.preventDefault();
        e.stopPropagation();
        gDropZone.style.borderColor = 'var(--admin-primary)';
        gDropZone.style.background = 'rgba(133,46,112,0.06)';
      });
    });

    ['dragleave', 'drop'].forEach(name => {
      gDropZone.addEventListener(name, (e) => {
        e.preventDefault();
        e.stopPropagation();
        gDropZone.style.borderColor = '#cbd5e1';
        gDropZone.style.background = '#fafafa';
      });
    });

    gDropZone.addEventListener('drop', (e) => {
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        gFileInput.files = files;
        handleGalleryFile(files[0]);
      }
    });

    gFileInput.addEventListener('change', () => {
      if (gFileInput.files.length > 0) {
        handleGalleryFile(gFileInput.files[0]);
      }
    });
  }

  function handleGalleryFile(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      gPreviewImg.src = e.target.result;
      gFileNameEl.textContent = file.name;
      gFileSizeEl.textContent = (file.size / 1024).toFixed(1) + ' KB (Ready to upload)';
      gPromptEl.style.display = 'none';
      gPreviewEl.style.display = 'flex';
      gCustomUrlInput.value = '';
    };
    reader.readAsDataURL(file);
  }

  function clearGalleryImage(e) {
    if (e) e.stopPropagation();
    gFileInput.value = '';
    gPreviewImg.src = '';
    gPromptEl.style.display = 'flex';
    gPreviewEl.style.display = 'none';
    gCustomUrlInput.value = '';
  }

  if (gCustomUrlInput) {
    gCustomUrlInput.addEventListener('input', () => {
      const val = gCustomUrlInput.value.trim();
      if (val) {
        gPreviewImg.src = (val.startsWith('http') || val.startsWith('/') ? val : '../' + val);
        gFileNameEl.textContent = val;
        gFileSizeEl.textContent = 'Existing Asset / URL';
        gPromptEl.style.display = 'none';
        gPreviewEl.style.display = 'flex';
        gFileInput.value = '';
      } else {
        clearGalleryImage();
      }
    });
  }
</script>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>
