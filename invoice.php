<?php
/**
 * Official Printable Quotation & Event Production Agreement
 * Roppets Edutainment Production Inc.
 */

require_once __DIR__ . '/includes/db.php';

$refCode = trim($_GET['ref'] ?? '');

if (empty($refCode)) {
    die("Please provide a valid reference code. e.g. invoice.php?ref=ROP-2026-9414");
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE UPPER(reference_code) = UPPER(:ref)");
$stmt->execute([':ref' => $refCode]);
$b = $stmt->fetch();

if (!$b) {
    die("Booking not found for reference code: " . htmlspecialchars($refCode));
}

$totalPrice = floatval($b['total_price']);
$downpayment = $totalPrice * 0.5;
$balance = $totalPrice - $downpayment;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quotation & Agreement - <?php echo htmlspecialchars($b['reference_code']); ?> - Roppets</title>
  
  <link rel="icon" type="image/png" href="assets/images/logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background: #f1f5f9;
      color: #1e293b;
      padding: 40px 20px;
      font-size: 13.5px;
      line-height: 1.6;
    }
    .invoice-container {
      max-width: 850px;
      margin: 0 auto;
      background: #ffffff;
      padding: 50px;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      position: relative;
    }
    .invoice-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 2px solid #852e70;
      padding-bottom: 25px;
      margin-bottom: 30px;
    }
    .invoice-header img {
      height: 60px;
      margin-bottom: 8px;
    }
    .company-details {
      font-size: 12px;
      color: #64748b;
    }
    .invoice-title-block {
      text-align: right;
    }
    .invoice-title {
      font-family: 'Outfit', sans-serif;
      font-size: 26px;
      font-weight: 800;
      color: #852e70;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .ref-badge {
      display: inline-block;
      background: #faf5f9;
      border: 1px solid #852e70;
      color: #852e70;
      font-family: monospace;
      font-size: 15px;
      font-weight: 700;
      padding: 4px 12px;
      border-radius: 6px;
      margin-top: 6px;
    }
    .info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
      margin-bottom: 30px;
    }
    .info-card {
      background: #fafafa;
      border-radius: 8px;
      padding: 18px;
      border: 1px solid #e2e8f0;
    }
    .info-card h4 {
      font-family: 'Outfit', sans-serif;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #852e70;
      margin-bottom: 10px;
      border-bottom: 1px solid #e2e8f0;
      padding-bottom: 6px;
    }
    .info-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 6px;
    }
    .info-row span {
      color: #64748b;
    }
    .info-row strong {
      color: #1e293b;
    }
    .spec-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    .spec-table th {
      background: #852e70;
      color: #fff;
      padding: 12px 16px;
      text-align: left;
      font-weight: 600;
      font-size: 13px;
      text-transform: uppercase;
    }
    .spec-table td {
      padding: 14px 16px;
      border-bottom: 1px solid #e2e8f0;
    }
    .total-summary {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 30px;
    }
    .summary-box {
      width: 320px;
      background: #faf5f9;
      border-radius: 8px;
      padding: 18px;
      border: 1px solid rgba(133,46,112,0.2);
    }
    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      font-size: 13.5px;
    }
    .summary-row.grand-total {
      border-top: 2px solid #852e70;
      padding-top: 8px;
      margin-top: 8px;
      font-size: 16px;
      font-weight: 800;
      color: #852e70;
    }
    .terms-block {
      background: #f8fafc;
      border-radius: 8px;
      padding: 20px;
      border-left: 4px solid #852e70;
      margin-bottom: 35px;
      font-size: 12px;
      color: #475569;
    }
    .terms-block h5 {
      font-size: 13px;
      color: #1e293b;
      margin-bottom: 6px;
      text-transform: uppercase;
    }
    .terms-block ol {
      padding-left: 18px;
    }
    .terms-block li {
      margin-bottom: 4px;
    }
    .signature-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 50px;
      margin-top: 40px;
      padding-top: 20px;
    }
    .signature-line {
      border-top: 1px solid #94a3b8;
      padding-top: 8px;
      text-align: center;
      font-weight: 600;
      font-size: 13px;
    }
    .signature-line small {
      display: block;
      color: #64748b;
      font-weight: 400;
      font-size: 11.5px;
    }
    .print-actions {
      max-width: 850px;
      margin: 0 auto 20px auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      border: none;
    }
    .btn-print {
      background: #852e70;
      color: #fff;
    }
    .btn-back {
      background: #e2e8f0;
      color: #334155;
    }

    @media print {
      body {
        background: #fff;
        padding: 0;
      }
      .invoice-container {
        box-shadow: none;
        padding: 20px 0;
      }
      .print-actions {
        display: none !important;
      }
    }
  </style>
</head>
<body>

  <div class="print-actions">
    <a href="track.php?ref=<?php echo urlencode($b['reference_code']); ?>" class="btn btn-back">
      <i class="fa-solid fa-arrow-left"></i> Back to Tracking
    </a>
    <button onclick="window.print()" class="btn btn-print">
      <i class="fa-solid fa-print"></i> Print / Save as PDF
    </button>
  </div>

  <div class="invoice-container">
    
    <!-- Invoice Header -->
    <div class="invoice-header">
      <div>
        <img src="assets/images/logo.png" alt="Roppets Logo">
        <div class="company-details">
          <strong>Roppets Edutainment Production Inc.</strong><br>
          Unit 314 Parc Condominium, #226 15th Ave, Cubao, QC<br>
          Phone: (02) 8912-3456 / +63 917 530 0823<br>
          Email: edutainment@roppets.com | Website: www.roppets.com
        </div>
      </div>
      <div class="invoice-title-block">
        <div class="invoice-title">Event Quotation</div>
        <div class="ref-badge"><?php echo htmlspecialchars($b['reference_code']); ?></div>
        <div style="font-size: 12px; color: #64748b; margin-top: 6px;">
          Date Issued: <?php echo date('F d, Y'); ?>
        </div>
      </div>
    </div>

    <!-- Client & Event Info Grid -->
    <div class="info-grid">
      <div class="info-card">
        <h4>Client Information</h4>
        <div class="info-row">
          <span>Client Name:</span>
          <strong><?php echo htmlspecialchars($b['name']); ?></strong>
        </div>
        <div class="info-row">
          <span>Contact Number:</span>
          <strong><?php echo htmlspecialchars($b['phone']); ?></strong>
        </div>
        <div class="info-row">
          <span>Email Address:</span>
          <strong><?php echo htmlspecialchars($b['email']); ?></strong>
        </div>
      </div>

      <div class="info-card">
        <h4>Event Details</h4>
        <div class="info-row">
          <span>Event Date:</span>
          <strong style="color: #852e70;"><?php echo date('F d, Y', strtotime($b['event_date'])); ?></strong>
        </div>
        <div class="info-row">
          <span>Occasion / Type:</span>
          <strong><?php echo htmlspecialchars($b['event_type']); ?></strong>
        </div>
        <div class="info-row">
          <span>Venue / Location:</span>
          <strong><?php echo htmlspecialchars($b['location'] ?: 'To be confirmed'); ?></strong>
        </div>
        <div class="info-row">
          <span>Guest Estimate:</span>
          <strong><?php echo intval($b['guest_count']); ?> Pax</strong>
        </div>
      </div>
    </div>

    <!-- Production Specs Table -->
    <table class="spec-table">
      <thead>
        <tr>
          <th>Description / Production Package Inclusions</th>
          <th style="text-align: right; width: 140px;">Amount</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <strong style="font-size: 15px; color: #1e293b;"><?php echo htmlspecialchars($b['service_package']); ?></strong>
            <p style="color: #64748b; font-size: 12.5px; margin-top: 4px;">
              Includes full theater staging, professional performing cast, high-powered sound audio system with wireless mics, and program coordination.
            </p>
            <?php if (!empty($b['notes'])): ?>
              <div style="font-size: 12px; color: #852e70; margin-top: 6px; font-style: italic;">
                Special Request / Preferred Story: <?php echo htmlspecialchars($b['notes']); ?>
              </div>
            <?php endif; ?>
          </td>
          <td style="text-align: right; font-weight: 700; font-size: 15px;">
            ₱<?php echo number_format($totalPrice, 2); ?>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Totals & Payment Schedule -->
    <div class="total-summary">
      <div class="summary-box">
        <div class="summary-row">
          <span>Package Subtotal:</span>
          <strong>₱<?php echo number_format($totalPrice, 2); ?></strong>
        </div>
        <div class="summary-row">
          <span>50% Downpayment:</span>
          <strong style="color: #d97706;">₱<?php echo number_format($downpayment, 2); ?></strong>
        </div>
        <div class="summary-row">
          <span>Balance upon Arrival:</span>
          <strong>₱<?php echo number_format($balance, 2); ?></strong>
        </div>
        <div class="summary-row grand-total">
          <span>Total Contract Price:</span>
          <span>₱<?php echo number_format($totalPrice, 2); ?></span>
        </div>
      </div>
    </div>

    <!-- Payment Channels & Bank Details -->
    <div style="background: #faf5f9; border: 1px solid rgba(133,46,112,0.15); border-radius: 8px; padding: 18px; margin-bottom: 25px;">
      <h5 style="color: #852e70; font-size: 13px; font-weight: 700; margin-bottom: 8px; text-transform: uppercase;">
        Official Payment Channels:
      </h5>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 12.5px;">
        <div>
          <strong>BDO Current Account:</strong><br>
          Account Name: Roppets Edutainment Production Inc.<br>
          Account No: 0012-3456-7890
        </div>
        <div>
          <strong>GCash / Maya (Finance Head):</strong><br>
          Account Name: Roppets Production<br>
          Mobile No: 0917-530-0823
        </div>
      </div>
    </div>

    <!-- Production Terms -->
    <div class="terms-block">
      <h5>Terms & Agreement:</h5>
      <ol>
        <li>A <strong>50% downpayment</strong> is required upon confirmation to block the cast, puppeteers, and mascots for the scheduled event date.</li>
        <li>The remaining 50% balance must be settled on the day of the event immediately after performance completion.</li>
        <li>Roppets crew will arrive at the venue approximately 1 to 1.5 hours prior to the program for stage setup and sound check.</li>
        <li>For outdoor venues, a covered stage or shelter must be provided to protect audio and visual equipment from weather conditions.</li>
      </ol>
    </div>

    <!-- Signature Lines -->
    <div class="signature-grid">
      <div class="signature-line">
        <strong>Roppets Edutainment Production Inc.</strong>
        <small>Authorized Production Officer</small>
      </div>
      <div class="signature-line">
        <strong><?php echo htmlspecialchars($b['name']); ?></strong>
        <small>Client / Conforme Signature</small>
      </div>
    </div>

  </div>

</body>
</html>
