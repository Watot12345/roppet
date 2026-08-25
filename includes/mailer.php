<?php
/**
 * Roppets Edutainment Production Inc.
 * Automated Email & Notification Dispatcher Engine
 */

require_once __DIR__ . '/db.php';

function sendNotificationEmail($toEmail, $recipientName, $subject, $messageHtml) {
    $fromEmail = getenv('SMTP_FROM') ?: 'edutainment@roppets.com';
    $fromName = 'Roppets Edutainment Production Inc.';

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        "From: {$fromName} <{$fromEmail}>",
        "Reply-To: {$fromEmail}",
        'X-Mailer: PHP/' . phpversion()
    ];

    $logEntry = sprintf(
        "[%s] EMAIL TO: %s <%s> | SUBJECT: %s | RESULT: ",
        date('Y-m-d H:i:s'),
        $recipientName,
        $toEmail,
        $subject
    );

    // Attempt PHP native mail if mail server configured
    $sent = false;
    try {
        if (function_exists('mail') && !empty($toEmail)) {
            $sent = @mail($toEmail, $subject, $messageHtml, implode("\r\n", $headers));
        }
    } catch (Exception $e) {
        $sent = false;
    }

    $logEntry .= $sent ? "SENT\n" : "LOGGED (Simulation/Local)\n";

    // Write to audit log file
    $logFile = __DIR__ . '/../uploads/mail_logs.txt';
    @file_put_contents($logFile, $logEntry, FILE_APPEND);

    return true;
}

/**
 * Send Booking Received Notification to Client
 */
function sendBookingConfirmationToClient($booking) {
    $ref = $booking['reference_code'];
    $name = htmlspecialchars($booking['name']);
    $date = htmlspecialchars($booking['event_date']);
    $pkg = htmlspecialchars($booking['service_package']);

    $subject = "Booking Received [{$ref}] - Roppets Edutainment Production";
    $html = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
        <div style='background: #852e70; color: #ffffff; padding: 25px; text-align: center;'>
            <h2 style='margin: 0;'>Roppets Edutainment Production</h2>
            <p style='margin: 5px 0 0 0; font-size: 14px;'>Booking Reservation Confirmation</p>
        </div>
        <div style='padding: 30px; color: #334155; line-height: 1.6;'>
            <p>Dear <strong>{$name}</strong>,</p>
            <p>Thank you for choosing Roppets Edutainment Production Inc. for your upcoming event! We have received your reservation request.</p>
            
            <div style='background: #faf5f9; border: 1px dashed #852e70; padding: 15px; border-radius: 6px; margin: 20px 0;'>
                <strong>Your Booking Reference Code:</strong> <span style='font-family: monospace; font-size: 18px; color: #852e70; font-weight: bold;'>{$ref}</span><br>
                <strong>Event Date:</strong> {$date}<br>
                <strong>Package:</strong> {$pkg}
            </div>

            <p>You can track your event status, view your official quotation, and upload downpayment receipts at our Client Portal:</p>
            <p style='text-align: center;'>
                <a href='http://localhost/roppet/track.php?ref={$ref}' style='display: inline-block; background: #852e70; color: #ffffff; padding: 12px 25px; border-radius: 25px; text-decoration: none; font-weight: bold;'>Track Event Reservation</a>
            </p>
            
            <p>For urgent inquiries, call us at (02) 8912-3456 or mobile +63 917 530 0823.</p>
            <p>Best regards,<br><strong>Roppets Production Team</strong></p>
        </div>
    </div>
    ";

    return sendNotificationEmail($booking['email'], $booking['name'], $subject, $html);
}

/**
 * Log Internal Staff Activity
 */
function logStaffActivity($adminUser, $action, $details = '') {
    try {
        $pdo = getDBConnection();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt = $pdo->prepare("INSERT INTO activity_logs (admin_user, action, details, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$adminUser, $action, $details, $ip]);
    } catch (Exception $e) {}
}
