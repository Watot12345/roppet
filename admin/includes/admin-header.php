<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if (!isset($adminPageTitle)) {
    $adminPageTitle = "Admin Dashboard";
}
if (!isset($adminActiveNav)) {
    $adminActiveNav = "dashboard";
}

$pdoHeader = getDBConnection();
$pendingPaymentsCount = $pdoHeader->query("SELECT COUNT(*) FROM payments WHERE status = 'Pending Review'")->fetchColumn();
$unreadInquiriesCount = $pdoHeader->query("SELECT COUNT(*) FROM inquiries WHERE status = 'Unread'")->fetchColumn();
$pendingFabQuotesCount = $pdoHeader->query("SELECT COUNT(*) FROM fabrication_quotes WHERE status = 'Under Review'")->fetchColumn();
$pendingReviewsCount = $pdoHeader->query("SELECT COUNT(*) FROM reviews WHERE is_approved = FALSE")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($adminPageTitle); ?> - Roppets Admin</title>
  
  <link rel="icon" type="image/png" href="../assets/images/logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
  <div class="admin-wrapper">
    
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="sidebar-header">
        <img src="../assets/images/logo.png" alt="Roppets">
        <div>
          <strong style="color: #fff; display: block; font-size: 15px;">ROPPETS</strong>
          <span style="font-size: 11px; color: #94a3b8;">Production Portal</span>
        </div>
      </div>

      <ul class="sidebar-menu">
        <li class="sidebar-item <?php echo ($adminActiveNav === 'dashboard') ? 'active' : ''; ?>">
          <a href="index.php" class="sidebar-link">
            <i class="fa-solid fa-gauge-high"></i> Dashboard
          </a>
        </li>

        <li class="sidebar-item <?php echo ($adminActiveNav === 'bookings') ? 'active' : ''; ?>">
          <a href="bookings.php" class="sidebar-link">
            <i class="fa-solid fa-calendar-check"></i> Bookings & Quotes
          </a>
        </li>

        <li class="sidebar-item <?php echo ($adminActiveNav === 'payments') ? 'active' : ''; ?>">
          <a href="payments.php" class="sidebar-link" style="display: flex; justify-content: space-between; align-items: center;">
            <div><i class="fa-solid fa-receipt"></i> Payment Approvals</div>
            <?php if ($pendingPaymentsCount > 0): ?>
              <span class="badge" style="background: #f59e0b; color: #fff; font-size: 10px; padding: 2px 6px;">
                <?php echo $pendingPaymentsCount; ?>
              </span>
            <?php endif; ?>
          </a>
        </li>

        <li class="sidebar-item <?php echo ($adminActiveNav === 'performers') ? 'active' : ''; ?>">
          <a href="performers.php" class="sidebar-link">
            <i class="fa-solid fa-masks-theater"></i> Performers & Crew
          </a>
        </li>

        <li class="sidebar-item <?php echo ($adminActiveNav === 'calendar') ? 'active' : ''; ?>">
          <a href="calendar.php" class="sidebar-link">
            <i class="fa-solid fa-calendar-days"></i> Event Calendar
          </a>
        </li>

        <!-- CMS Submenu Header -->
        <li style="padding: 14px 16px 6px 16px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">
          Content Management (CMS)
        </li>

        <li class="sidebar-item <?php echo ($adminActiveNav === 'services_cms') ? 'active' : ''; ?>">
          <a href="manage-services.php" class="sidebar-link">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Manage Services
          </a>
        </li>

        <li class="sidebar-item <?php echo ($adminActiveNav === 'packages_cms') ? 'active' : ''; ?>">
          <a href="manage-packages.php" class="sidebar-link">
            <i class="fa-solid fa-box-open"></i> Party Packages
          </a>
        </li>

        <li class="sidebar-item <?php echo ($adminActiveNav === 'stories_cms') ? 'active' : ''; ?>">
          <a href="manage-stories.php" class="sidebar-link">
            <i class="fa-solid fa-book-open"></i> Puppet Stories
          </a>
        </li>

        <li class="sidebar-item <?php echo ($adminActiveNav === 'gallery_cms') ? 'active' : ''; ?>">
          <a href="manage-gallery.php" class="sidebar-link">
            <i class="fa-solid fa-images"></i> Media Gallery
          </a>
        </li>

        <li class="sidebar-item <?php echo ($adminActiveNav === 'reviews_cms') ? 'active' : ''; ?>">
          <a href="manage-reviews.php" class="sidebar-link" style="display: flex; justify-content: space-between; align-items: center;">
            <div><i class="fa-solid fa-star"></i> Client Reviews</div>
            <?php if ($pendingReviewsCount > 0): ?>
              <span class="badge" style="background: #10b981; color: #fff; font-size: 10px; padding: 2px 6px;">
                <?php echo $pendingReviewsCount; ?>
              </span>
            <?php endif; ?>
          </a>
        </li>

        <li class="sidebar-item <?php echo ($adminActiveNav === 'fabrication_cms') ? 'active' : ''; ?>">
          <a href="fabrication-quotes.php" class="sidebar-link" style="display: flex; justify-content: space-between; align-items: center;">
            <div><i class="fa-solid fa-paw"></i> Mascot Blueprints</div>
            <?php if ($pendingFabQuotesCount > 0): ?>
              <span class="badge" style="background: #0ea5e9; color: #fff; font-size: 10px; padding: 2px 6px;">
                <?php echo $pendingFabQuotesCount; ?>
              </span>
            <?php endif; ?>
          </a>
        </li>

        <li class="sidebar-item <?php echo ($adminActiveNav === 'inquiries') ? 'active' : ''; ?>">
          <a href="inquiries.php" class="sidebar-link" style="display: flex; justify-content: space-between; align-items: center;">
            <div><i class="fa-solid fa-inbox"></i> Inquiries Inbox</div>
            <?php if ($unreadInquiriesCount > 0): ?>
              <span class="badge" style="background: #ef4444; color: #fff; font-size: 10px; padding: 2px 6px;">
                <?php echo $unreadInquiriesCount; ?>
              </span>
            <?php endif; ?>
          </a>
        </li>

        <!-- System Settings & Logs -->
        <li style="padding: 14px 16px 6px 16px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">
          System
        </li>

        <li class="sidebar-item <?php echo ($adminActiveNav === 'settings') ? 'active' : ''; ?>">
          <a href="settings.php" class="sidebar-link">
            <i class="fa-solid fa-gear"></i> System Settings
          </a>
        </li>

        <li class="sidebar-item <?php echo ($adminActiveNav === 'logs') ? 'active' : ''; ?>">
          <a href="activity-logs.php" class="sidebar-link">
            <i class="fa-solid fa-shield-halved"></i> Activity Logs
          </a>
        </li>

        <li class="sidebar-item" style="margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 10px;">
          <a href="export.php" class="sidebar-link" style="color: #cbd5e1;">
            <i class="fa-solid fa-file-excel" style="color: #10b981;"></i> Export to CSV / Excel
          </a>
        </li>
      </ul>

      <div class="sidebar-footer">
        <a href="../index.php" target="_blank" style="color: #cbd5e1; text-decoration: none; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> View Public Site
        </a>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="admin-main">
      <header class="admin-topbar">
        <div>
          <h2 style="font-size: 20px; font-weight: 700; color: var(--admin-text);"><?php echo htmlspecialchars($adminPageTitle); ?></h2>
        </div>
        <div style="display: flex; align-items: center; gap: 20px;">
          <span style="color: var(--admin-muted); font-size: 13.5px;">
            <i class="fa-solid fa-user-circle" style="color: var(--admin-primary); margin-right: 4px;"></i>
            Logged in as: <strong><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></strong>
          </span>
          <a href="logout.php" class="btn-admin" style="background: #fee2e2; color: #991b1b;">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
          </a>
        </div>
      </header>

      <div class="admin-content">
