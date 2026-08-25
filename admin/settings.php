<?php
$adminPageTitle = "System, Gateway & AI Settings";
$adminActiveNav = "settings";
require_once __DIR__ . '/includes/admin-header.php';

$pdo = getDBConnection();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_settings') {
        $fields = [
            'company_name' => trim($_POST['company_name'] ?? ''),
            'company_phone' => trim($_POST['company_phone'] ?? ''),
            'company_email' => trim($_POST['company_email'] ?? ''),
            'company_address' => trim($_POST['company_address'] ?? ''),
            'bdo_account' => trim($_POST['bdo_account'] ?? ''),
            'gcash_account' => trim($_POST['gcash_account'] ?? ''),
            'paymongo_secret_key' => trim($_POST['paymongo_secret_key'] ?? ''),
            'paymongo_public_key' => trim($_POST['paymongo_public_key'] ?? ''),
            'gemini_api_key' => trim($_POST['gemini_api_key'] ?? ''),
            'gemini_model' => trim($_POST['gemini_model'] ?? 'gemini-1.5-flash'),
            'max_shows_per_day' => trim($_POST['max_shows_per_day'] ?? '5')
        ];

        try {
            $stmt = $pdo->prepare("
                INSERT INTO system_settings (setting_key, setting_value)
                VALUES (?, ?)
                ON CONFLICT (setting_key) DO UPDATE SET setting_value = EXCLUDED.setting_value, updated_at = CURRENT_TIMESTAMP
            ");
            foreach ($fields as $k => $v) {
                $stmt->execute([$k, $v]);
            }
            $message = "System settings, Payment Gateway, and Gemini AI configurations saved successfully!";
        } catch (Exception $e) {
            $error = "Error saving settings: " . $e->getMessage();
        }
    }
}

// Fetch all settings
$settingsRows = $pdo->query("SELECT * FROM system_settings")->fetchAll();
$settings = [];
foreach ($settingsRows as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<?php if (!empty($message)): ?>
  <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
    <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> <?php echo htmlspecialchars($message); ?>
  </div>
<?php endif; ?>

<div class="admin-card" style="max-width: 800px;">
  <div style="border-bottom: 1px solid var(--admin-border); padding-bottom: 16px; margin-bottom: 24px;">
    <h3 style="font-size: 18px; font-weight: 700; color: var(--admin-text);">Global Company, Gateway & AI Settings</h3>
    <p style="color: var(--admin-muted); font-size: 13px;">Configure phone lines, invoice details, PayMongo gateway keys, and Gemini AI Chatbot model.</p>
  </div>

  <form method="POST" action="settings.php">
    <input type="hidden" name="action" value="save_settings">

    <!-- Company Contact Info -->
    <div style="margin-bottom: 16px;">
      <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Company Name</label>
      <input type="text" name="company_name" value="<?php echo htmlspecialchars($settings['company_name'] ?? 'Roppets Edutainment Production Inc.'); ?>" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
      <div>
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Official Phone Numbers</label>
        <input type="text" name="company_phone" value="<?php echo htmlspecialchars($settings['company_phone'] ?? '(02) 8912-3456 / +63 917 530 0823'); ?>" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
      </div>
      <div>
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Official Email</label>
        <input type="email" name="company_email" value="<?php echo htmlspecialchars($settings['company_email'] ?? 'edutainment@roppets.com'); ?>" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
      </div>
    </div>

    <div style="margin-bottom: 16px;">
      <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Office Address (for Invoices & Quotations)</label>
      <textarea name="company_address" rows="2" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;"><?php echo htmlspecialchars($settings['company_address'] ?? 'Unit 314 Parc Condominium, #226 15th Avenue, Cubao, Quezon City, Metro Manila, Philippines'); ?></textarea>
    </div>

    <!-- Gemini AI Chatbot Settings -->
    <div style="border-top: 1px solid var(--admin-border); padding-top: 20px; margin-top: 20px;">
      <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
        <i class="fa-solid fa-robot" style="color: #6366f1; font-size: 18px;"></i>
        <h4 style="font-size: 15px; font-weight: 700; color: #4338ca; margin: 0;">Google Gemini AI Chatbot Integration</h4>
      </div>
      <p style="color: var(--admin-muted); font-size: 12.5px; margin-bottom: 14px;">Powers the floating PuppetBot on the website. Enter your Google Gemini API key to activate real AI reasoning.</p>

      <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 16px; margin-bottom: 16px;">
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Gemini API Key</label>
          <input type="password" name="gemini_api_key" value="<?php echo htmlspecialchars($settings['gemini_api_key'] ?? ''); ?>" placeholder="AIzaSy..." style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: monospace;">
        </div>
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">AI Model</label>
          <select name="gemini_model" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
            <option value="gemini-1.5-flash" <?php echo (($settings['gemini_model'] ?? '') === 'gemini-1.5-flash') ? 'selected' : ''; ?>>Gemini 1.5 Flash (Fast & Recommended)</option>
            <option value="gemini-2.0-flash" <?php echo (($settings['gemini_model'] ?? '') === 'gemini-2.0-flash') ? 'selected' : ''; ?>>Gemini 2.0 Flash</option>
            <option value="gemini-1.5-pro" <?php echo (($settings['gemini_model'] ?? '') === 'gemini-1.5-pro') ? 'selected' : ''; ?>>Gemini 1.5 Pro (Deep Reasoning)</option>
          </select>
        </div>
      </div>
    </div>

    <!-- PayMongo Online Gateway Keys -->
    <div style="border-top: 1px solid var(--admin-border); padding-top: 20px; margin-top: 20px;">
      <h4 style="font-size: 15px; font-weight: 700; color: #16a34a; margin-bottom: 4px;">PayMongo Payment Gateway (GCash, Maya & Cards)</h4>
      <p style="color: var(--admin-muted); font-size: 12.5px; margin-bottom: 14px;">Leave blank for sandbox demo mode, or paste your PayMongo secret key.</p>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Secret Key (<code>sk_test_...</code> / <code>sk_live_...</code>)</label>
          <input type="password" name="paymongo_secret_key" value="<?php echo htmlspecialchars($settings['paymongo_secret_key'] ?? ''); ?>" placeholder="sk_test_..." style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: monospace;">
        </div>
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Public Key (<code>pk_test_...</code> / <code>pk_live_...</code>)</label>
          <input type="text" name="paymongo_public_key" value="<?php echo htmlspecialchars($settings['paymongo_public_key'] ?? ''); ?>" placeholder="pk_test_..." style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: monospace;">
        </div>
      </div>
    </div>

    <!-- Manual Bank Accounts -->
    <div style="border-top: 1px solid var(--admin-border); padding-top: 20px; margin-top: 20px;">
      <h4 style="font-size: 15px; font-weight: 700; color: var(--admin-primary); margin-bottom: 12px;">Manual Bank Deposit Details</h4>
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">BDO Account Information</label>
          <input type="text" name="bdo_account" value="<?php echo htmlspecialchars($settings['bdo_account'] ?? '0012-3456-7890 (Roppets Edutainment Production Inc.)'); ?>" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
        </div>
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">GCash / Maya Account Number</label>
          <input type="text" name="gcash_account" value="<?php echo htmlspecialchars($settings['gcash_account'] ?? '0917-530-0823 (Roppets Finance)'); ?>" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
        </div>
      </div>
    </div>

    <div style="margin-bottom: 24px; border-top: 1px solid var(--admin-border); padding-top: 20px;">
      <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Max Simultaneous Shows Per Day</label>
      <input type="number" name="max_shows_per_day" value="<?php echo htmlspecialchars($settings['max_shows_per_day'] ?? '5'); ?>" min="1" max="20" style="width: 120px; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
      <small style="color: var(--admin-muted); display: block; margin-top: 4px;">Controls the date availability checker trigger threshold.</small>
    </div>

    <button type="submit" class="btn-admin btn-admin-primary btn-admin-lg">
      <i class="fa-solid fa-floppy-disk"></i> Save All Settings & Keys
    </button>
  </form>
</div>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>
