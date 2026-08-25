<?php
$pageTitle = "Checkout Cancelled | Roppets Edutainment Production Inc.";
$currentNav = "track";
include_once __DIR__ . '/includes/header.php';

$refCode = trim($_GET['ref'] ?? '');
?>

  <section class="section-padding" style="background: #ffffff; min-height: 500px; display: flex; align-items: center;">
    <div class="container" style="max-width: 600px; text-align: center;">
      <div style="background: #fff; border: 1px solid var(--border-color); border-radius: var(--border-radius-lg); padding: 40px 30px; box-shadow: var(--shadow-sm);">
        <div style="width: 70px; height: 70px; border-radius: 50%; background: #fee2e2; color: #991b1b; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 15px auto;">
          <i class="fa-solid fa-xmark"></i>
        </div>
        <h2 style="font-size: 24px; color: var(--dark-text); margin-bottom: 8px;">Payment Session Cancelled</h2>
        <p style="color: var(--muted-text); font-size: 14.5px; margin-bottom: 25px;">
          Your online checkout session was cancelled. No charges were made. You can try again or use direct bank transfer anytime.
        </p>

        <a href="track.php<?php echo !empty($refCode) ? '?ref=' . urlencode($refCode) : ''; ?>" class="btn btn-primary">
          <i class="fa-solid fa-arrow-left"></i> Return to Tracking Portal
        </a>
      </div>
    </div>
  </section>

<?php
include_once __DIR__ . '/includes/footer.php';
?>
