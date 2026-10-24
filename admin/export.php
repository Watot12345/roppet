<?php
/**
 * Export Bookings to CSV Spreadsheet
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = getDBConnection();

$filename = sprintf("roppets_bookings_%s.csv", date('Y-m-d_His'));

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header Row
fputcsv($output, [
    'Reference Code',
    'Client Name',
    'Email Address',
    'Contact Number',
    'Event Date',
    'Event Type',
    'Service / Package',
    'Venue Location',
    'Estimated Guests',
    'Total Price (PHP)',
    'Downpayment Amount (PHP)',
    'Booking Status',
    'Payment Status',
    'Client Special Notes',
    'Internal Admin Notes',
    'Date Booked'
]);

$stmt = $pdo->query("SELECT * FROM bookings ORDER BY event_date ASC, created_at DESC");
while ($row = $stmt->fetch()) {
    fputcsv($output, [
        $row['reference_code'],
        $row['name'],
        $row['email'],
        $row['phone'],
        $row['event_date'],
        $row['event_type'],
        $row['service_package'],
        $row['location'],
        $row['guest_count'],
        $row['total_price'],
        $row['downpayment_amount'],
        $row['status'],
        $row['payment_status'],
        $row['notes'],
        $row['admin_notes'],
        $row['created_at']
    ]);
}

fclose($output);
exit;
