<?php
/**
 * Custom Mascot & Plush Fabrication Quotation API Endpoint
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$clientName = trim($_POST['client_name'] ?? '');
$companyName = trim($_POST['company_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$fabType = trim($_POST['fabrication_type'] ?? 'Corporate Mascot');
$quantity = intval($_POST['quantity'] ?? 1);
$targetDelivery = trim($_POST['target_delivery'] ?? null);
$coolingFan = isset($_POST['cooling_fan']) && $_POST['cooling_fan'] ? true : false;
$fabricPreference = trim($_POST['fabric_preference'] ?? 'High-Density Foam & Soft Velour');
$description = trim($_POST['description'] ?? '');

if (empty($clientName) || empty($email) || empty($phone) || empty($description)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide your name, contact details, and project description.']);
    exit;
}

$blueprintFile = '';

// Handle Blueprint Upload
if (isset($_FILES['blueprint_file']) && $_FILES['blueprint_file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['blueprint_file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'ai', 'psd'];

    if (!in_array($ext, $allowed)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid blueprint file format. Allowed: JPG, PNG, WEBP, PDF, AI, PSD']);
        exit;
    }

    if ($file['size'] > 15 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File size exceeds 15MB limit.']);
        exit;
    }

    $uploadDir = __DIR__ . '/../uploads/blueprints/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $blueprintFile = sprintf("blueprint_%s_%s.%s", time(), bin2hex(random_bytes(3)), $ext);
    $targetPath = $uploadDir . $blueprintFile;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save blueprint file.']);
        exit;
    }
}

try {
    $pdo = getDBConnection();
    $year = date('Y');
    $randHex = strtoupper(bin2hex(random_bytes(2)));
    $quoteRef = sprintf("FAB-%s-%s", $year, $randHex);

    $stmt = $pdo->prepare("
        INSERT INTO fabrication_quotes (
            quote_ref, client_name, company_name, email, phone,
            fabrication_type, quantity, target_delivery, cooling_fan,
            fabric_preference, description, blueprint_file, status
        ) VALUES (
            :ref, :name, :company, :email, :phone,
            :ftype, :qty, :deliv, :fan,
            :pref, :desc, :file, 'Under Review'
        )
    ");

    $stmt->execute([
        ':ref' => $quoteRef,
        ':name' => $clientName,
        ':company' => $companyName,
        ':email' => $email,
        ':phone' => $phone,
        ':ftype' => $fabType,
        ':qty' => $quantity,
        ':deliv' => !empty($targetDelivery) ? $targetDelivery : null,
        ':fan' => $coolingFan ? 'true' : 'false',
        ':pref' => $fabricPreference,
        ':desc' => $description,
        ':file' => $blueprintFile
    ]);

    echo json_encode([
        'success' => true,
        'quote_ref' => $quoteRef,
        'message' => 'Your custom mascot / plush fabrication specifications have been submitted successfully! Our workshop head will send a formal quote within 24 hours.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
