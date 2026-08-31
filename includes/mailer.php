<?php
/**
 * Roppets Edutainment Production Inc.
 * Automated Email & Notification Dispatcher Engine
 * Supports Gmail SMTP (STARTTLS / SSL) with graceful logging fallback.
 */

require_once __DIR__ . '/db.php';

/**
 * Core SMTP / Mail Dispatcher
 */
function sendNotificationEmail($toEmail, $recipientName, $subject, $messageHtml) {
    if (empty($toEmail)) return false;

    $smtpHost = getSystemSetting('smtp_host', 'smtp.gmail.com');
    $smtpPort = intval(getSystemSetting('smtp_port', 587));
    $smtpUser = getSystemSetting('smtp_user', 'edutainment@roppets.com');
    $smtpPass = getSystemSetting('smtp_pass', '');
    $fromEmail = getSystemSetting('smtp_from_email', 'edutainment@roppets.com');
    $fromName = getSystemSetting('smtp_from_name', 'Roppets Edutainment Production Inc.');

    $sent = false;
    $errorMsg = '';

    // If SMTP Password (e.g. Gmail App Password) is provided, use SMTP socket transmission
    if (!empty($smtpPass) && !empty($smtpUser)) {
        try {
            $sent = sendViaSmtpSocket($smtpHost, $smtpPort, $smtpUser, $smtpPass, $fromEmail, $fromName, $toEmail, $recipientName, $subject, $messageHtml);
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            $sent = false;
        }
    }

    // If SMTP wasn't sent or not configured, try native PHP mail
    if (!$sent && function_exists('mail')) {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            "From: {$fromName} <{$fromEmail}>",
            "Reply-To: {$fromEmail}",
            'X-Mailer: PHP/' . phpversion()
        ];
        try {
            $sent = @mail($toEmail, $subject, $messageHtml, implode("\r\n", $headers));
        } catch (Exception $e) {
            $sent = false;
        }
    }

    // Log email event
    $statusText = $sent ? "SENT (SMTP/Mail)" : "LOGGED (Local/Simulation" . ($errorMsg ? " - " . $errorMsg : "") . ")";
    $logEntry = sprintf(
        "[%s] EMAIL TO: %s <%s> | SUBJECT: %s | RESULT: %s\n",
        date('Y-m-d H:i:s'),
        $recipientName,
        $toEmail,
        $subject,
        $statusText
    );

    $logFile = __DIR__ . '/../uploads/mail_logs.txt';
    @file_put_contents($logFile, $logEntry, FILE_APPEND);

    return $sent;
}

/**
 * Lightweight Standalone SMTP Socket Client (Supports Gmail STARTTLS / SSL)
 */
function sendViaSmtpSocket($host, $port, $user, $pass, $fromEmail, $fromName, $toEmail, $toName, $subject, $htmlBody) {
    $timeout = 15;
    $isSsl = ($port == 465);
    $connectHost = ($isSsl ? 'ssl://' : 'tcp://') . $host;

    $socket = @fsockopen($connectHost, $port, $errno, $errstr, $timeout);
    if (!$socket) {
        throw new Exception("Could not connect to SMTP server {$host}:{$port} ({$errstr})");
    }

    $readResponse = function() use ($socket) {
        $response = "";
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == " ") break;
        }
        return $response;
    };

    $sendCommand = function($cmd, $expectedCode) use ($socket, $readResponse) {
        fputs($socket, $cmd . "\r\n");
        $resp = $readResponse();
        $code = substr($resp, 0, 3);
        if ($code != $expectedCode) {
            throw new Exception("SMTP error on '{$cmd}': {$resp}");
        }
        return $resp;
    };

    $greeting = $readResponse();
    $sendCommand("EHLO " . gethostname(), 250);

    // If port 587, initiate STARTTLS
    if ($port == 587) {
        $sendCommand("STARTTLS", 220);
        $cryptoMethod = STREAM_CRYPTO_METHOD_TLS_CLIENT;
        if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
            $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
        }
        if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
            $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
        }
        if (!stream_socket_enable_crypto($socket, true, $cryptoMethod)) {
            throw new Exception("Failed to establish TLS encryption with SMTP server.");
        }
        $sendCommand("EHLO " . gethostname(), 250);
    }

    // Authenticate
    $sendCommand("AUTH LOGIN", 334);
    $sendCommand(base64_encode($user), 334);
    $sendCommand(base64_encode($pass), 235);

    // Sender & Recipient
    $sendCommand("MAIL FROM: <{$fromEmail}>", 250);
    $sendCommand("RCPT TO: <{$toEmail}>", 250);

    // Send Data
    $sendCommand("DATA", 354);

    $boundary = "----=_NextPart_" . md5(uniqid(time()));
    $headers = [
        "MIME-Version: 1.0",
        "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>",
        "To: =?UTF-8?B?" . base64_encode($toName) . "?= <{$toEmail}>",
        "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
        "Date: " . date('r'),
        "Content-Type: text/html; charset=UTF-8",
        "Content-Transfer-Encoding: 8bit",
        "X-Mailer: RoppetsMailer/2.0"
    ];

    $emailData = implode("\r\n", $headers) . "\r\n\r\n" . $htmlBody . "\r\n.";
    $sendCommand($emailData, 250);
    $sendCommand("QUIT", 221);

    fclose($socket);
    return true;
}

/**
 * Dynamic Website Base URL Helper
 */
function getBaseSiteUrl() {
    $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = str_replace('\\', '/', dirname($script));
    $basePath = ($dir === '/' || $dir === '' || $dir === '/api' || $dir === '/admin') ? '' : $dir;
    // Strip trailing /api or /admin if present
    $basePath = preg_replace('#/(api|admin)$#', '', $basePath);
    return rtrim($proto . $host . $basePath, '/');
}

/**
 * 1. Booking Received & Formal Quotation Email (Client)
 */
function sendBookingConfirmationToClient($booking) {
    $ref = $booking['reference_code'];
    $name = htmlspecialchars($booking['name']);
    $date = date('F d, Y (l)', strtotime($booking['event_date']));
    $pkg = htmlspecialchars($booking['service_package']);
    $price = number_format(floatval($booking['total_price'] ?? 0), 2);
    $downpayment = number_format(floatval($booking['total_price'] ?? 0) * 0.5, 2);
    $baseUrl = getBaseSiteUrl();

    $subject = "Booking Quotation [{$ref}] - Roppets Edutainment Production";
    $html = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
        <div style='background: linear-gradient(135deg, #852e70 0%, #a21caf 100%); color: #ffffff; padding: 25px; text-align: center;'>
            <h2 style='margin: 0; font-size: 22px;'>Roppets Edutainment Production Inc.</h2>
            <p style='margin: 6px 0 0 0; font-size: 14px; opacity: 0.9;'>Pioneering Master Puppetry & Family Entertainment Since 1989</p>
        </div>
        <div style='padding: 30px; color: #334155; line-height: 1.6;'>
            <p>Dear <strong>{$name}</strong>,</p>
            <p>Mabuhay! We have received your event reservation request. Below is your official quotation and booking reference code:</p>
            
            <div style='background: #faf5f9; border: 1px dashed #852e70; padding: 18px; border-radius: 8px; margin: 20px 0;'>
                <div style='display: flex; justify-content: space-between; margin-bottom: 8px;'>
                    <strong>Reference Code:</strong> 
                    <span style='font-family: monospace; font-size: 16px; color: #852e70; font-weight: bold;'>{$ref}</span>
                </div>
                <div style='margin-bottom: 6px;'><strong>Event Date:</strong> {$date}</div>
                <div style='margin-bottom: 6px;'><strong>Show / Package:</strong> {$pkg}</div>
                <div style='margin-bottom: 6px;'><strong>Estimated Total:</strong> ₱{$price}</div>
                <div><strong>Required Downpayment (50%):</strong> <span style='color: #166534; font-weight: bold;'>₱{$downpayment}</span></div>
            </div>

            <p>To lock in your schedule and confirm your cast of puppeteers, please view your formal quotation and complete the 50% reservation deposit:</p>
            
            <div style='text-align: center; margin: 25px 0;'>
                <a href='{$baseUrl}/track.php?ref={$ref}' style='display: inline-block; background: #852e70; color: #ffffff; padding: 12px 28px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 14px;'>Track Booking & Pay Downpayment</a>
                &nbsp;
                <a href='{$baseUrl}/invoice.php?ref={$ref}' style='display: inline-block; background: #f1f5f9; color: #334155; padding: 12px 20px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 14px; border: 1px solid #cbd5e1;'>View Official Quotation</a>
            </div>

            <p style='font-size: 13px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 15px; margin-top: 25px;'>
                <strong>Office Hotline:</strong> (02) 8912-3456 | <strong>Mobile/Viber:</strong> +63 917 530 0823<br>
                <strong>Office Address:</strong> Unit 314 Parc Condominium, #226 15th Ave, Cubao, Quezon City
            </p>
        </div>
    </div>
    ";

    return sendNotificationEmail($booking['email'], $booking['name'], $subject, $html);
}

/**
 * 1.5. New Booking Company / Admin Alert Email
 */
function sendAdminBookingAlert($booking) {
    $companyEmail = getSystemSetting('company_email', getenv('SMTP_FROM') ?: 'edutainment@roppets.com');
    $companyName = getSystemSetting('company_name', 'Roppets Production Management');
    
    $ref = $booking['reference_code'];
    $clientName = htmlspecialchars($booking['name']);
    $clientPhone = htmlspecialchars($booking['phone']);
    $clientEmail = htmlspecialchars($booking['email']);
    $date = date('F d, Y (l)', strtotime($booking['event_date']));
    $eventType = htmlspecialchars($booking['event_type'] ?? 'Event');
    $pkg = htmlspecialchars($booking['service_package']);
    $location = htmlspecialchars($booking['location'] ?? 'To be confirmed');
    $guestCount = htmlspecialchars($booking['guest_count'] ?? 'N/A');
    $notes = nl2br(htmlspecialchars($booking['notes'] ?? 'None'));
    $price = number_format(floatval($booking['total_price'] ?? 0), 2);
    $downpayment = number_format(floatval($booking['total_price'] ?? 0) * 0.5, 2);

    $baseUrl = getBaseSiteUrl();
    $adminBookingUrl = "{$baseUrl}/admin/bookings.php";

    $subject = "🚨 NEW BOOKING ALERT [{$ref}] - {$clientName} ({$date})";
    $html = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
        <div style='background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: #ffffff; padding: 22px; text-align: center;'>
            <h3 style='margin: 0; font-size: 20px; color: #f59e0b;'>⭐ New Online Booking Received!</h3>
            <p style='margin: 4px 0 0 0; font-size: 13px; color: #cbd5e1;'>Reference: <strong>{$ref}</strong></p>
        </div>
        <div style='padding: 25px; color: #334155; line-height: 1.6;'>
            <p>A new event booking reservation was just submitted on the website:</p>

            <table style='width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 14px;'>
                <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; width: 35%; font-weight: bold; color: #64748b;'>Reference Code:</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #852e70;'>{$ref}</td></tr>
                <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #64748b;'>Client Name:</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold;'>{$clientName}</td></tr>
                <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #64748b;'>Contact Phone:</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9;'><a href='tel:{$clientPhone}' style='color: #2563eb;'>{$clientPhone}</a></td></tr>
                <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #64748b;'>Contact Email:</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9;'><a href='mailto:{$clientEmail}' style='color: #2563eb;'>{$clientEmail}</a></td></tr>
                <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #64748b;'>Event Date:</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; color: #b45309; font-weight: bold;'>{$date}</td></tr>
                <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #64748b;'>Event Type:</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9;'>{$eventType}</td></tr>
                <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #64748b;'>Show / Package:</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold;'>{$pkg}</td></tr>
                <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #64748b;'>Venue / Location:</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9;'>{$location}</td></tr>
                <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #64748b;'>Guest Count:</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9;'>{$guestCount} guests</td></tr>
                <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #64748b;'>Total Price:</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-size: 15px; font-weight: bold; color: #166534;'>₱{$price}</td></tr>
                <tr><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #64748b;'>50% Downpayment:</td><td style='padding: 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #166534;'>₱{$downpayment}</td></tr>
                <tr><td style='padding: 8px; font-weight: bold; color: #64748b;'>Special Notes:</td><td style='padding: 8px;'>{$notes}</td></tr>
            </table>

            <div style='text-align: center; margin: 25px 0;'>
                <a href='{$adminBookingUrl}' style='display: inline-block; background: #852e70; color: #ffffff; padding: 12px 28px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 14px;'>Open Admin Dashboard to Review</a>
            </div>
        </div>
    </div>
    ";

    return sendNotificationEmail($companyEmail, $companyName, $subject, $html);
}

/**
 * 2. Official Payment Receipt Email
 */
function sendPaymentReceiptToClient($booking, $amountPaid, $paymentMethod, $refCode) {
    $name = htmlspecialchars($booking['name']);
    $date = date('F d, Y', strtotime($booking['event_date']));
    $pkg = htmlspecialchars($booking['service_package']);
    $amountFormatted = number_format(floatval($amountPaid), 2);
    $baseUrl = getBaseSiteUrl();

    $subject = "Official Payment Receipt [{$refCode}] - Roppets Edutainment Production";
    $html = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
        <div style='background: #166534; color: #ffffff; padding: 25px; text-align: center;'>
            <h2 style='margin: 0;'>Payment Verified & Confirmed</h2>
            <p style='margin: 6px 0 0 0; font-size: 14px; opacity: 0.9;'>Official Acknowledgment Receipt</p>
        </div>
        <div style='padding: 30px; color: #334155; line-height: 1.6;'>
            <p>Dear <strong>{$name}</strong>,</p>
            <p>We have successfully verified your payment! Your show schedule is officially locked in with our production team.</p>
            
            <div style='background: #f0fdf4; border: 1px solid #86efac; padding: 18px; border-radius: 8px; margin: 20px 0;'>
                <div><strong>Reference Code:</strong> <code>{$refCode}</code></div>
                <div><strong>Amount Received:</strong> <span style='font-size: 16px; color: #166534; font-weight: bold;'>₱{$amountFormatted}</span></div>
                <div><strong>Payment Method:</strong> {$paymentMethod}</div>
                <div><strong>Event Date:</strong> {$date}</div>
                <div><strong>Show Package:</strong> {$pkg}</div>
            </div>

            <p style='text-align: center; margin: 25px 0;'>
                <a href='{$baseUrl}/invoice.php?ref={$refCode}' style='display: inline-block; background: #166534; color: #ffffff; padding: 12px 28px; border-radius: 25px; text-decoration: none; font-weight: bold;'>Download Official Receipt & Contract</a>
            </p>

            <p>Our production coordinator will contact you 3 days prior to the event for final logistics and performer call time briefing.</p>
            <p>Best regards,<br><strong>Roppets Finance & Production Team</strong></p>
        </div>
    </div>
    ";

    return sendNotificationEmail($booking['email'], $booking['name'], $subject, $html);
}

/**
 * 3. Performer & Crew Dispatch Briefing Email
 */
function sendPerformerDispatchBriefing($booking, $performerEmail, $performerName, $assignedRole) {
    $ref = $booking['reference_code'];
    $date = date('F d, Y (l)', strtotime($booking['event_date']));
    $client = htmlspecialchars($booking['name']);
    $phone = htmlspecialchars($booking['phone']);
    $venue = htmlspecialchars($booking['location'] ?: 'To be confirmed');
    $pkg = htmlspecialchars($booking['service_package']);
    $notes = htmlspecialchars($booking['notes'] ?: 'None');

    $subject = "Show Briefing [{$ref}]: {$date} - {$pkg}";
    $html = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
        <div style='background: #1e293b; color: #ffffff; padding: 20px; text-align: center;'>
            <h3 style='margin: 0;'>Roppets Performer Dispatch Briefing</h3>
            <p style='margin: 4px 0 0 0; font-size: 13px; color: #cbd5e1;'>Assigned Role: <strong>{$assignedRole}</strong></p>
        </div>
        <div style='padding: 25px; color: #334155; line-height: 1.6;'>
            <p>Hello <strong>{$performerName}</strong>,</p>
            <p>You have been assigned to the following Roppets production event:</p>

            <table style='width: 100%; border-collapse: collapse; margin: 15px 0;'>
                <tr><td style='padding: 6px 0; border-bottom: 1px solid #f1f5f9; width: 35%; font-weight: bold;'>Booking Ref:</td><td style='padding: 6px 0; border-bottom: 1px solid #f1f5f9;'>{$ref}</td></tr>
                <tr><td style='padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold;'>Event Date:</td><td style='padding: 6px 0; border-bottom: 1px solid #f1f5f9;'>{$date}</td></tr>
                <tr><td style='padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold;'>Venue / Location:</td><td style='padding: 6px 0; border-bottom: 1px solid #f1f5f9;'>{$venue}</td></tr>
                <tr><td style='padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold;'>Client Contact:</td><td style='padding: 6px 0; border-bottom: 1px solid #f1f5f9;'>{$client} ({$phone})</td></tr>
                <tr><td style='padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold;'>Show Package:</td><td style='padding: 6px 0; border-bottom: 1px solid #f1f5f9;'>{$pkg}</td></tr>
                <tr><td style='padding: 6px 0; font-weight: bold;'>Special Notes:</td><td style='padding: 6px 0;'>{$notes}</td></tr>
            </table>

            <p style='font-size: 13px; color: #64748b;'>Please ensure complete stage backdrops, sound equipment, puppet characters, and bubble equipment are checked out at least 2 hours before the scheduled call time.</p>
        </div>
    </div>
    ";

    return sendNotificationEmail($performerEmail, $performerName, $subject, $html);
}

/**
 * 4. Diagnostics / SMTP Test Function
 */
function sendTestSmtpEmail($toEmail) {
    $subject = "Roppets SMTP Diagnostics Test - " . date('Y-m-d H:i:s');
    $html = "
    <div style='font-family: Arial, sans-serif; padding: 20px;'>
        <h2 style='color: #852e70;'>SMTP Test Successful!</h2>
        <p>This email confirms that your Gmail SMTP credentials configured in Roppets Admin Settings are active and delivering properly.</p>
        <p>Server Time: " . date('Y-m-d H:i:s') . "</p>
    </div>
    ";
    return sendNotificationEmail($toEmail, 'Roppets Admin', $subject, $html);
}

/**
 * Internal Staff Activity Audit Log
 */
function logStaffActivity($adminUser, $action, $details = '') {
    try {
        $pdo = getDBConnection();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt = $pdo->prepare("INSERT INTO activity_logs (admin_user, action, details, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$adminUser, $action, $details, $ip]);
    } catch (Exception $e) {}
}
