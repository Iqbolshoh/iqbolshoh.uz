<?php

// Qat'iy ruxsat etilgan domenlar ro'yxati (Whitelist)
$allowedOrigins = [
    'https://iqbolshoh.uz',
    'http://localhost:5173',
];

// So'rov manbasini aniqlash
$httpOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
$httpReferer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

$isAllowed = false;
$matchedOrigin = '';

// 1-qadam: HTTP_ORIGIN ni tekshirish (Browserlar asosan buni yuboradi)
if (!empty($httpOrigin) && in_array($httpOrigin, $allowedOrigins)) {
    $isAllowed = true;
    $matchedOrigin = $httpOrigin;
}
// 2-qadam: Agar Origin bo'lmasa, HTTP_REFERER ni tekshirish
elseif (!empty($httpReferer)) {
    foreach ($allowedOrigins as $origin) {
        if (strpos($httpReferer, $origin) === 0) {
            $isAllowed = true;
            $matchedOrigin = $origin;
            break;
        }
    }
}

// Ruxsatsiz manbalarni darhol bloklash
if (!$isAllowed) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Forbidden: Invalid origin or unauthorized access.']);
    exit();
}

// Xavfsiz CORS sarlavhalarini (headers) o'rnatish
header("Access-Control-Allow-Origin: $matchedOrigin");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Brauzerning pre-flight (OPTIONS) so'roviga javob berish
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Faqat POST so'rovlarga ruxsat berish
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed. Only POST is accepted.']);
    exit();
}

// Qo'shimcha botlarga qarshi tekshiruv (Application JSON ekanligini tasdiqlash)
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if (strpos($contentType, 'application/json') !== 0) {
    http_response_code(406);
    echo json_encode(['success' => false, 'error' => 'Not Acceptable: Content-Type must be application/json.']);
    exit();
}

// Telegram API sozlamalari
$telegramToken = "YOUR_TELEGRAM_BOT_TOKEN";
$telegramChatId = "YOUR_TELEGRAM_CHAT_ID";

// ... qolgan kodlar o'zgarishsiz qoladi

if (empty($telegramToken) || empty($telegramChatId)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Internal Server Error: Missing Telegram credentials.']);
    exit();
}

// Read and decode the incoming JSON payload
$rawInput = file_get_contents('php://input');
$requestData = json_decode($rawInput, true);

// Validate the request format
if (!$requestData || !isset($requestData['type'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Bad Request: Invalid JSON format.']);
    exit();
}

$requestType = $requestData['type'];
$payloadData = isset($requestData['data']) ? $requestData['data'] : [];
$currentTime = date('Y-m-d H:i:s');
$telegramMessage = "";

// Sanitize inputs to prevent XSS and HTML injection
$sanitizedData = [];
foreach ($payloadData as $key => $value) {
    $sanitizedData[$key] = htmlspecialchars(strip_tags((string) $value));
}

// Format message based on request type
if ($requestType === 'contact') {
    $clientName = isset($sanitizedData['name']) ? $sanitizedData['name'] : 'Unknown';
    $clientEmail = isset($sanitizedData['email']) ? $sanitizedData['email'] : 'Unknown';
    $messageSubject = !empty($sanitizedData['subject']) ? $sanitizedData['subject'] : 'No subject provided';
    $messageText = !empty($sanitizedData['message']) ? $sanitizedData['message'] : 'No message provided';

    $telegramMessage = "📨 <b>NEW CONTACT MESSAGE</b>\n"
        . "#IQBOLSHOH_UZ #CONTACT\n\n"
        . "👤 <b>Name:</b> $clientName\n"
        . "📧 <b>Email:</b> $clientEmail\n"
        . "📝 <b>Subject:</b> $messageSubject\n\n"
        . "💬 <b>Message:</b>\n$messageText\n\n"
        . "🕒 <b>Time:</b> $currentTime";
} elseif ($requestType === 'service') {
    $serviceName = isset($sanitizedData['serviceName']) ? $sanitizedData['serviceName'] : 'Unknown Service';
    $servicePrice = isset($sanitizedData['servicePrice']) ? $sanitizedData['servicePrice'] : '-';
    $clientName = isset($sanitizedData['name']) ? $sanitizedData['name'] : 'Unknown';
    $clientEmail = isset($sanitizedData['email']) ? $sanitizedData['email'] : 'Unknown';
    $clientPhone = isset($sanitizedData['phone']) ? $sanitizedData['phone'] : 'Unknown';
    $messageText = !empty($sanitizedData['message']) ? $sanitizedData['message'] : '<i>No additional message</i>';

    $telegramMessage = "🚀 <b>NEW SERVICE REQUEST</b>\n"
        . "#IQBOLSHOH_UZ #SERVICE\n\n"
        . "🧩 <b>Service:</b> $serviceName\n"
        . "💵 <b>Price:</b> $servicePrice\n\n"
        . "👤 <b>Client:</b> $clientName\n"
        . "📧 <b>Email:</b> $clientEmail\n"
        . "📱 <b>Phone:</b> $clientPhone\n\n"
        . "💬 <b>Message:</b>\n$messageText\n\n"
        . "🕒 <b>Time:</b> $currentTime";
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Bad Request: Unknown request type.']);
    exit();
}

// Prepare cURL request to Telegram API
$telegramApiUrl = "https://api.telegram.org/bot{$telegramToken}/sendMessage";
$postParameters = [
    'chat_id' => $telegramChatId,
    'text' => $telegramMessage,
    'parse_mode' => 'HTML',
    'disable_web_page_preview' => true
];

// Execute cURL request
$curlHandle = curl_init();
curl_setopt($curlHandle, CURLOPT_URL, $telegramApiUrl);
curl_setopt($curlHandle, CURLOPT_POST, true);
curl_setopt($curlHandle, CURLOPT_POSTFIELDS, http_build_query($postParameters));
curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10);

$curlResponse = curl_exec($curlHandle);
$httpStatusCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
$curlError = curl_error($curlHandle);
curl_close($curlHandle);

// Check Telegram API response
if ($httpStatusCode === 200) {
    echo json_encode(['success' => true, 'message' => 'Message successfully sent.']);
} else {
    http_response_code(502); // Bad Gateway
    echo json_encode([
        'success' => false,
        'error' => 'Failed to communicate with Telegram API.',
        'details' => $curlError ? $curlError : 'HTTP Status ' . $httpStatusCode
    ]);
}
