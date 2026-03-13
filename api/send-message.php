<?php

// Security: Allow requests only from your domain
$allowedOrigin = 'https://iqbolshoh.uz';

// Check if the request comes from the allowed origin (or allow all for local development)
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowedOrigin) {
    header("Access-Control-Allow-Origin: $allowedOrigin");
} else {
    // For local testing, you can temporarily use '*'. In production, stick to the specific domain.
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Handle pre-flight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Only POST requests are allowed.']);
    exit();
}

// Function to load variables from a .env file
function loadEnv($path)
{
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
    return true;
}

// Load the .env file located in the same directory
loadEnv(__DIR__ . '/.env');

$token = $_ENV['TELEGRAM_BOT_TOKEN'] ?? null;
$chatId = $_ENV['TELEGRAM_CHAT_ID'] ?? null;

// Validate server configuration
if (!$token || !$chatId) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server configuration error. Missing Telegram credentials.']);
    exit();
}

// Read incoming JSON payload
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['type'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request format.']);
    exit();
}

$type = $input['type'];
$data = $input['data'] ?? [];
$time = date('Y-m-d H:i:s');
$message = "";

// Sanitize inputs to prevent XSS and HTML injection in Telegram
foreach ($data as $key => $value) {
    $data[$key] = htmlspecialchars(strip_tags($value));
}

// 1. Format for Contact Form
if ($type === 'contact') {
    $name = $data['name'] ?? 'Unknown';
    $email = $data['email'] ?? 'Unknown';
    $subject = !empty($data['subject']) ? $data['subject'] : 'No subject provided';
    $msgText = !empty($data['message']) ? $data['message'] : 'No message provided';

    $message = "📨 <b>NEW CONTACT MESSAGE</b>\n"
        . "#IQBOLSHOH_UZ #CONTACT\n\n"
        . "👤 <b>Name:</b> $name\n"
        . "📧 <b>Email:</b> $email\n"
        . "📝 <b>Subject:</b> $subject\n\n"
        . "💬 <b>Message:</b>\n$msgText\n\n"
        . "🕒 <b>Time:</b> $time";
}
// 2. Format for Services Form
elseif ($type === 'service') {
    $serviceName = $data['serviceName'] ?? 'Unknown Service';
    $servicePrice = $data['servicePrice'] ?? '-';
    $name = $data['name'] ?? 'Unknown';
    $email = $data['email'] ?? 'Unknown';
    $phone = $data['phone'] ?? 'Unknown';
    $msgText = !empty($data['message']) ? $data['message'] : '<i>No additional message</i>';

    $message = "🚀 <b>NEW SERVICE REQUEST</b>\n"
        . "#IQBOLSHOH_UZ #SERVICE\n\n"
        . "🧩 <b>Service:</b> $serviceName\n"
        . "💵 <b>Price:</b> $servicePrice\n\n"
        . "👤 <b>Client:</b> $name\n"
        . "📧 <b>Email:</b> $email\n"
        . "📱 <b>Phone:</b> $phone\n\n"
        . "💬 <b>Message:</b>\n$msgText\n\n"
        . "🕒 <b>Time:</b> $time";
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Unknown request type.']);
    exit();
}

// Send request to Telegram using cURL for security and speed
$url = "https://api.telegram.org/bot{$token}/sendMessage";
$postData = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'HTML',
    'disable_web_page_preview' => true
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo json_encode(['success' => true, 'message' => 'Message successfully sent to Telegram.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Telegram server error.', 'details' => $response]);
}
?>