<?php
/**
 * Contact Inquiry API Endpoint
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    $data = $_POST;
}

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$service = trim($data['service'] ?? 'General Inquiry');
$subject = trim($data['subject'] ?? 'Website Inquiry');
$message = trim($data['message'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please provide your Name, Email, and Message.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please provide a valid email address.'
    ]);
    exit;
}

try {
    $pdo = getDBConnection();
    $insertSql = "
        INSERT INTO inquiries (name, email, phone, service, subject, message, status)
        VALUES (:name, :email, :phone, :service, :subject, :message, 'Unread')
    ";

    $stmt = $pdo->prepare($insertSql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':service' => $service,
        ':subject' => $subject,
        ':message' => $message
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Thank you for reaching out! Your message has been sent to our team.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while saving your inquiry: ' . $e->getMessage()
    ]);
}
