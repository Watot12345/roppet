<?php
$adminPageTitle = "System, Capacity & Gmail SMTP Settings";
$adminActiveNav = "settings";
require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/../includes/mailer.php';

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
            'max_shows_per_day' => trim($_POST['max_shows_per_day'] ?? '5'),
            'blackout_dates' => trim($_POST['blackout_dates'] ?? ''),
            'smtp_host' => trim($_POST['smtp_host'] ?? 'smtp.gmail.com'),
            'smtp_port' => trim($_POST['smtp_port'] ?? '587'),
            'smtp_user' => trim($_POST['smtp_user'] ?? ''),
            'smtp_pass' => trim($_POST['smtp_pass'] ?? ''),
            'smtp_from_email' => trim($_POST['smtp_from_email'] ?? ''),
            'smtp_from_name' => trim($_POST['smtp_from_name'] ?? 'Roppets Edutainment Production Inc.')
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
            $message = "System settings, Capacity limits, and Gmail SMTP configurations saved successfully!";
            logStaffActivity('Admin', 'Updated System Settings', 'Modified global settings, capacity limits & SMTP');
        } catch (Exception $e) {
            $error = "Error saving settings: " . $e->getMessage();
        }
    } elseif ($_POST['action'] === 'send_test_email') {
        $testRecipient = trim($_POST['test_email'] ?? '');
        if (!empty($testRecipient)) {
            $sent = sendTestSmtpEmail($testRecipient);
            if ($sent) {
                $message = "Test email successfully sent to {$testRecipient} via SMTP!";
            } else {
                $message = "Test email logged (check uploads/mail_logs.txt for details). If using Gmail, make sure you generated a 16-character App Password.";
            }
        } else {
            $error = "Please provide an email address for the test dispatch.";
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

<?php if (!empty($error)): ?>
  <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;">
    <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> <?php echo htmlspecialchars($error); ?>
  </div>
<?php endif; ?>

<div class="admin-card" style="max-width: 850px;">
  <div style="border-bottom: 1px solid var(--admin-border); padding-bottom: 16px; margin-bottom: 24px;">
    <h3 style="font-size: 18px; font-weight: 700; color: var(--admin-text);">Global Company, Capacity & Gmail SMTP Settings</h3>
    <p style="color: var(--admin-muted); font-size: 13px;">Manage company contacts, production capacity limiters, Gmail SMTP credentials, and PayMongo payment keys.</p>
  </div>

  <form method="POST" action="settings.php">
    <input type="hidden" name="action" value="save_settings">

    <!-- Section 1: Company Contact Details -->
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
      <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Office Address (for Quotations & Invoices)</label>
      <textarea name="company_address" rows="2" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;"><?php echo htmlspecialchars($settings['company_address'] ?? 'Unit 314 Parc Condominium, #226 15th Avenue, Cubao, Quezon City, Metro Manila, Philippines'); ?></textarea>
    </div>

    <!-- Section 2: Phase 3 Capacity & Double-Booking Limiter -->
    <div style="border-top: 1px solid var(--admin-border); padding-top: 20px; margin-top: 20px;">
      <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
        <i class="fa-solid fa-calendar-check" style="color: #0284c7; font-size: 18px;"></i>
        <h4 style="font-size: 15px; font-weight: 700; color: #0369a1; margin: 0;">Show Capacity & Double-Booking Guard (Phase 3)</h4>
      </div>
      <p style="color: var(--admin-muted); font-size: 12.5px; margin-bottom: 14px;">Controls the maximum allowable events per day and blocks bookings on theater holiday breaks.</p>

      <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px; margin-bottom: 16px;">
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Max Shows Per Day</label>
          <input type="number" name="max_shows_per_day" value="<?php echo htmlspecialchars($settings['max_shows_per_day'] ?? '5'); ?>" min="1" max="20" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
          <small style="color: var(--admin-muted); font-size: 11px;">Triggers 'Fully Booked' when reached.</small>
        </div>
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Blackout / Closed Holiday Dates</label>
          <input type="text" name="blackout_dates" value="<?php echo htmlspecialchars($settings['blackout_dates'] ?? '2026-12-25,2027-01-01'); ?>" placeholder="YYYY-MM-DD, YYYY-MM-DD" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: monospace;">
          <small style="color: var(--admin-muted); font-size: 11px;">Comma-separated dates where booking is disabled.</small>
        </div>
      </div>
    </div>

    <!-- Section 3: Phase 4 Automated Gmail SMTP Settings -->
    <div style="border-top: 1px solid var(--admin-border); padding-top: 20px; margin-top: 20px;">
      <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
        <i class="fa-solid fa-envelope-circle-check" style="color: #dc2626; font-size: 18px;"></i>
        <h4 style="font-size: 15px; font-weight: 700; color: #b91c1c; margin: 0;">Automated Gmail / SMTP Email Notifications (Phase 4)</h4>
      </div>
      <p style="color: var(--admin-muted); font-size: 12.5px; margin-bottom: 14px;">Automatically sends instant formal quotations to clients upon booking, official receipts on payment, and performer briefings.</p>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">SMTP Host</label>
          <input type="text" name="smtp_host" value="<?php echo htmlspecialchars($settings['smtp_host'] ?? 'smtp.gmail.com'); ?>" placeholder="smtp.gmail.com" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: monospace;">
        </div>
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">SMTP Port</label>
          <input type="number" name="smtp_port" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>" placeholder="587" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: monospace;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Gmail / SMTP Username (Email)</label>
          <input type="email" name="smtp_user" value="<?php echo htmlspecialchars($settings['smtp_user'] ?? 'edutainment@roppets.com'); ?>" placeholder="your-email@gmail.com" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: monospace;">
        </div>
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Gmail App Password (16-character)</label>
          <input type="password" name="smtp_pass" value="<?php echo htmlspecialchars($settings['smtp_pass'] ?? ''); ?>" placeholder="xxxx xxxx xxxx xxxx" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: monospace;">
          <small style="color: var(--admin-muted); font-size: 11px;">Generate at Google Account > Security > App Passwords.</small>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Sender From Email</label>
          <input type="email" name="smtp_from_email" value="<?php echo htmlspecialchars($settings['smtp_from_email'] ?? 'edutainment@roppets.com'); ?>" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
        </div>
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Sender Display Name</label>
          <input type="text" name="smtp_from_name" value="<?php echo htmlspecialchars($settings['smtp_from_name'] ?? 'Roppets Edutainment Production Inc.'); ?>" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
        </div>
      </div>
    </div>

    <!-- Section 4: Gemini AI & Payment Gateways -->
    <div style="border-top: 1px solid var(--admin-border); padding-top: 20px; margin-top: 20px;">
      <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
        <i class="fa-solid fa-robot" style="color: #6366f1; font-size: 18px;"></i>
        <h4 style="font-size: 15px; font-weight: 700; color: #4338ca; margin: 0;">Google Gemini AI Chatbot Integration</h4>
      </div>
      <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 16px; margin-bottom: 16px; margin-top: 10px;">
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Gemini API Key</label>
          <input type="password" name="gemini_api_key" value="<?php echo htmlspecialchars($settings['gemini_api_key'] ?? ''); ?>" placeholder="AIzaSy..." style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: monospace;">
        </div>
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">AI Model</label>
          <select name="gemini_model" style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit;">
            <option value="gemini-1.5-flash" <?php echo (($settings['gemini_model'] ?? '') === 'gemini-1.5-flash') ? 'selected' : ''; ?>>Gemini 1.5 Flash (Recommended)</option>
            <option value="gemini-2.0-flash" <?php echo (($settings['gemini_model'] ?? '') === 'gemini-2.0-flash') ? 'selected' : ''; ?>>Gemini 2.0 Flash</option>
            <option value="gemini-1.5-pro" <?php echo (($settings['gemini_model'] ?? '') === 'gemini-1.5-pro') ? 'selected' : ''; ?>>Gemini 1.5 Pro</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Section 5: PayMongo Online Gateway Keys -->
    <div style="border-top: 1px solid var(--admin-border); padding-top: 20px; margin-top: 20px;">
      <h4 style="font-size: 15px; font-weight: 700; color: #16a34a; margin-bottom: 4px;">PayMongo Payment Gateway (GCash, Maya & Cards)</h4>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; margin-top: 10px;">
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Secret Key</label>
          <input type="password" name="paymongo_secret_key" value="<?php echo htmlspecialchars($settings['paymongo_secret_key'] ?? ''); ?>" placeholder="sk_test_..." style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: monospace;">
        </div>
        <div>
          <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Public Key</label>
          <input type="text" name="paymongo_public_key" value="<?php echo htmlspecialchars($settings['paymongo_public_key'] ?? ''); ?>" placeholder="pk_test_..." style="width: 100%; padding: 9px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: monospace;">
        </div>
      </div>
    </div>

    <!-- Section 6: Manual Bank Details -->
    <div style="border-top: 1px solid var(--admin-border); padding-top: 20px; margin-top: 20px; margin-bottom: 24px;">
      <h4 style="font-size: 15px; font-weight: 700; color: var(--admin-primary); margin-bottom: 12px;">Manual Bank Deposit Channels</h4>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
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

    <button type="submit" class="btn-admin btn-admin-primary btn-admin-lg">
      <i class="fa-solid fa-floppy-disk"></i> Save All Global Settings
    </button>
  </form>

  <!-- Diagnostics: Send Test Email Tool -->
  <div style="border-top: 2px dashed var(--admin-border); padding-top: 20px; margin-top: 30px;">
    <h4 style="font-size: 14px; font-weight: 700; color: #475569; margin-bottom: 6px;">Test Gmail SMTP Dispatch</h4>
    <p style="font-size: 12.5px; color: var(--admin-muted); margin-bottom: 12px;">Send a test email to verify your SMTP host, port, and credentials.</p>
    <form method="POST" action="settings.php" style="display: flex; gap: 10px; max-width: 500px;">
      <input type="hidden" name="action" value="send_test_email">
      <input type="email" name="test_email" placeholder="recipient@example.com" required style="flex: 1; padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 13px;">
      <button type="submit" class="btn-admin btn-admin-secondary">
        <i class="fa-solid fa-paper-plane"></i> Send Test
      </button>
    </form>
  </div>
</div>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>
