<?php
/**
 * Roppets AI Chatbot Assistant API
 * Supports Google Gemini API (gemini-1.5-flash, gemini-2.0-flash, etc.)
 * with clean professional output (no emojis/emoticons).
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
if (!$data) $data = $_POST;

$userMessage = trim($data['message'] ?? '');
$history = $data['history'] ?? [];

if (empty($userMessage)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide a message.']);
    exit;
}

$pdo = getDBConnection();

// 1. Direct Reference Code Booking Lookup (e.g. ROP-2026-9414)
if (preg_match('/(ROP-\d{4}-\d{4}|ROP-[A-Z0-9-]+)/i', $userMessage, $match)) {
    $searchRef = strtoupper(trim($match[1]));
    try {
        $stmt = $pdo->prepare("
            SELECT b.*, 
                   COALESCE((SELECT SUM(amount) FROM payments WHERE booking_id = b.id AND status = 'Verified'), 0) AS total_paid
            FROM bookings b
            WHERE UPPER(b.reference_code) = :ref
            LIMIT 1
        ");
        $stmt->execute([':ref' => $searchRef]);
        $booking = $stmt->fetch();

        if ($booking) {
            $totalPrice = floatval($booking['total_price'] ?? 0);
            $totalPaid = floatval($booking['total_paid'] ?? 0);
            $balance = max(0, $totalPrice - $totalPaid);
            $eventDateFormatted = date('F d, Y (l)', strtotime($booking['event_date']));

            // Query assigned performers
            $perfStmt = $pdo->prepare("
                SELECT p.name, p.role, ba.assigned_role 
                FROM booking_assignments ba
                JOIN performers p ON ba.performer_id = p.id
                WHERE ba.booking_id = :bid
            ");
            $perfStmt->execute([':bid' => $booking['id']]);
            $performers = $perfStmt->fetchAll();
            $crewList = [];
            foreach ($performers as $p) {
                $roleLabel = $p['assigned_role'] ?: $p['role'];
                $crewList[] = "{$p['name']} ({$roleLabel})";
            }
            $crewStr = !empty($crewList) ? implode(', ', $crewList) : "Production assignment in progress (finalized 3 days before show)";

            $reply = "**Booking Details for {$booking['reference_code']}**\n\n" .
                     "• **Client Name:** {$booking['name']}\n" .
                     "• **Event Date:** {$eventDateFormatted}\n" .
                     "• **Show Package:** {$booking['service_package']}\n" .
                     "• **Venue / City:** " . ($booking['location'] ?: 'Metro Manila') . "\n" .
                     "• **Current Status:** **{$booking['status']}**\n" .
                     "• **Total Investment:** ₱" . number_format($totalPrice, 2) . "\n" .
                     "• **Amount Paid:** ₱" . number_format($totalPaid, 2) . "\n" .
                     "• **Remaining Balance:** ₱" . number_format($balance, 2) . "\n" .
                     "• **Production Crew:** {$crewStr}\n\n" .
                     "**Self-Service Actions:**\n" .
                     "• [View Formal Quotation / Invoice](invoice.php?ref={$booking['reference_code']})\n" .
                     "• [Open Online Payment & Tracking Portal](track.php?ref={$booking['reference_code']})";

            echo json_encode([
                'success' => true,
                'reply' => $reply,
                'source' => 'live-booking-lookup',
                'booking_ref' => $booking['reference_code']
            ]);
            exit;
        } else {
            $reply = "**Booking Status Lookup:**\n\n" .
                     "No active event reservation found matching reference code **{$searchRef}**.\n\n" .
                     "Please double check the reference code from your booking confirmation (e.g. `ROP-2026-9414`) or visit our [Self-Service Tracking Portal](track.php).";

            echo json_encode([
                'success' => true,
                'reply' => $reply,
                'source' => 'booking-not-found'
            ]);
            exit;
        }
    } catch (Exception $e) {}
}

// 2. Fetch Gemini API Key and Model from Database
$geminiApiKey = getenv('GEMINI_API_KEY') ?: '';
$geminiModel = getenv('GEMINI_MODEL') ?: 'gemini-1.5-flash';
$liveServices = [];
$livePackages = [];

try {
    $stmt = $pdo->prepare("SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ('gemini_api_key', 'gemini_model')");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    foreach ($rows as $r) {
        if ($r['setting_key'] === 'gemini_api_key' && !empty($r['setting_value'])) {
            $geminiApiKey = trim($r['setting_value']);
        }
        if ($r['setting_key'] === 'gemini_model' && !empty($r['setting_value'])) {
            $geminiModel = trim($r['setting_value']);
        }
    }
    $liveServices = $pdo->query("SELECT title, starting_price FROM services WHERE is_active = TRUE ORDER BY sort_order ASC, id ASC")->fetchAll();
    $livePackages = $pdo->query("SELECT name, price, subtitle FROM packages WHERE is_active = TRUE ORDER BY sort_order ASC, price ASC")->fetchAll();
} catch (Exception $e) {}

$showsListStr = '';
foreach ($liveServices as $ls) {
    $showsListStr .= "- {$ls['title']}: ₱" . number_format($ls['starting_price'], 0) . "\n";
}
$pkgListStr = '';
foreach ($livePackages as $lp) {
    $pkgListStr .= "- {$lp['name']}: ₱" . number_format($lp['price'], 0) . " ({$lp['subtitle']})\n";
}

// 3. If Gemini API Key exists, call Google Gemini REST API
if (!empty($geminiApiKey)) {
    $systemInstruction = "You are 'PuppetBot', the professional, warm, and expert AI Assistant for Roppets Edutainment Production Inc. (Philippines' pioneer in master puppetry since 1989).
Important guideline: Do NOT use any emojis or emoticons in your responses. Use clear, elegant, and professional formatting with markdown bolding and bullet points.
Key Information & Live Pricing:
Shows:
{$showsListStr}
Packages:
{$pkgListStr}
- Custom Mascot & Plush Fabrication: We make 2D brand sketches into wearable mascots with internal cooling fans, plush toys, and giant 10ft puppets.
- Contact: Phone (02) 8912-3456, Mobile +63 917 530 0823, Email edutainment@roppets.com, Office in Cubao, Quezon City.
- Booking & Tracking: Clients can book via the website or track existing bookings with their Reference Code (ROP-2026-XXXX).";

    $contents = [];
    // Convert history format if present
    foreach ($history as $h) {
        $role = ($h['sender'] === 'user') ? 'user' : 'model';
        $contents[] = [
            'role' => $role,
            'parts' => [['text' => $h['text']]]
        ];
    }
    $contents[] = [
        'role' => 'user',
        'parts' => [['text' => $userMessage]]
    ];

    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiApiKey}";

    $payload = [
        'contents' => $contents,
        'systemInstruction' => [
            'parts' => [['text' => $systemInstruction]]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 500
        ]
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $apiResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $resObj = json_decode($apiResponse, true);
        $reply = $resObj['candidates'][0]['content']['parts'][0]['text'] ?? '';
        if (!empty($reply)) {
            // Strip any unintended emoji from external model response
            $reply = preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F900}-\x{1F9FF}\x{1F018}-\x{1F270}]/u', '', $reply);
            echo json_encode([
                'success' => true,
                'reply' => trim($reply),
                'source' => 'gemini-api',
                'model' => $geminiModel
            ]);
            exit;
        }
    }
}

// 3. Clean Local Fallback Engine (No Emojis, Pure Professional Formatting)
$msg = strtolower($userMessage);
$reply = "";

if (strpos($msg, 'package') !== false || strpos($msg, 'rate') !== false || strpos($msg, 'price') !== false || strpos($msg, 'cost') !== false || strpos($msg, 'how much') !== false) {
    $reply = "**Popular Roppets Party Packages:**\n\n" .
             "1. **Kiddie Fun Package (₱6,500):** Hand Puppet Show (3 plays), Puppet Stage, Sound System, 1 Mascot appearance.\n" .
             "2. **Classic Combo Package (₱8,500):** Puppet Show + Comedy Magic Show + 1 Mascot + Full Sound System.\n" .
             "3. **Supreme Edutainment (₱11,500 - Best Seller):** Puppet Show + The Bubble Factory Show (3 Girls) + 1 Mascot + Kid-in-a-Bubble experience.\n" .
             "4. **Grand Extravaganza (₱18,500):** Teatro Itim Black Light OR Puppet Show + Bubble Show + Magic Show + 2 Mascots + Lights & Sound.\n\n" .
             "Click **Party Packages** above or tap **Book a Show** to reserve your date.";
} elseif (strpos($msg, 'bubble') !== false) {
    $reply = "**The Original Bubble Show by The Bubble Factory (₱6,000):**\n\n" .
             "Features 3 all-girl performers in colorful themed costumes performing synchronized bubble art, giant smoke bubbles, bouncing bubbles, and putting the birthday celebrant inside a giant soap bubble!\n\n" .
             "Would you like to check date availability for your party?";
} elseif (strpos($msg, 'puppet') !== false || strpos($msg, 'story') !== false || strpos($msg, 'stories') !== false) {
    $reply = "**Original Roppets Hand Puppet Show (₱4,500):**\n\n" .
             "Includes 3 thrilling moral story plays (such as *The 3 Little Pigs*, *Pinocchio*, *Red Riding Hood*, or *Si Pagong at si Matsing*), full portable stage, sound system, and professional puppeteers.\n\n" .
             "Over 100+ classic values-forming scripts available.";
} elseif (strpos($msg, 'teatro') !== false || strpos($msg, 'black') !== false || strpos($msg, 'glow') !== false) {
    $reply = "**Teatro Itim Black Theater Play (₱7,500):**\n\n" .
             "A mesmerizing Czech-style black-light puppetry performance where fluorescent characters float and dance in total darkness to uplifting music. Ideal for indoor halls, auditoriums, and mall events.";
} elseif (strpos($msg, 'mascot') !== false || strpos($msg, 'fabricat') !== false || strpos($msg, 'plush') !== false || strpos($msg, 'custom') !== false || strpos($msg, 'stuffed') !== false) {
    $reply = "**Custom Mascot & Plush Fabrication Studio:**\n\n" .
             "We transform your 2D sketches and company logos into high-quality wearable mascots (with built-in battery cooling fans), stuffed plush toys, or giant puppets.\n\n" .
             "Visit our **Fabrication Studio** page to upload your 2D character blueprint for a quotation within 24 hours.";
} elseif (strpos($msg, 'track') !== false || strpos($msg, 'ref') !== false || strpos($msg, 'status') !== false || strpos($msg, 'code') !== false) {
    $reply = "**Tracking Your Event Reservation:**\n\n" .
             "You can check your status, print your formal quotation, or pay your downpayment via GCash/Card anytime at our **Track Booking** portal (`track.php`). Just enter your Reference Code (e.g. `ROP-2026-9414`).";
} elseif (strpos($msg, 'contact') !== false || strpos($msg, 'phone') !== false || strpos($msg, 'number') !== false || strpos($msg, 'call') !== false || strpos($msg, 'office') !== false || strpos($msg, 'location') !== false) {
    $reply = "**Contact Roppets Edutainment Production Inc.:**\n\n" .
             "• **Landline:** (02) 8912-3456\n" .
             "• **Mobile / Viber:** +63 917 530 0823\n" .
             "• **Email:** edutainment@roppets.com\n" .
             "• **Office:** Unit 314 Parc Condominium, #226 15th Avenue, Cubao, Quezon City, Metro Manila\n\n" .
             "Our office is open Monday to Saturday, 9:00 AM to 6:00 PM.";
} elseif (strpos($msg, 'book') !== false || strpos($msg, 'reserve') !== false || strpos($msg, 'schedule') !== false) {
    $reply = "**Ready to Book?**\n\n" .
             "Simply click the **'Book a Show'** button at the top of the page, fill out your date and venue, and our production coordinators will send you a confirmation immediately.";
} else {
    $reply = "Mabuhay! I am here to help you plan an unforgettable event with Roppets.\n\n" .
             "You can ask me about:\n" .
             "• **Party Packages & Pricing**\n" .
             "• **Hand Puppet Shows & Moral Stories**\n" .
             "• **The Bubble Factory Show**\n" .
             "• **Custom Mascot & Plush Fabrication**\n" .
             "• **Tracking an Existing Booking**\n\n" .
             "How can we assist your event today?";
}

echo json_encode([
    'success' => true,
    'reply' => $reply,
    'source' => 'roppets-knowledge-engine'
]);
