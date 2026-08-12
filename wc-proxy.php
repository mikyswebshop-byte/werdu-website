<?php
/**
 * Werdu WooCommerce Proxy
 * Verbergt API keys en voorkomt CORS problemen
 * URL: https://werdu.de/wc-proxy.php
 */

// === CONFIGURATIE ===
$WC_BASE = 'https://werdu.de/wp-json/wc/v3';
$CONSUMER_KEY = 'ck_b6cace7b70688b43cfcc3c263a5d6235c8a09e0c';
$CONSUMER_SECRET = 'cs_3b62ea5dbe9d22358c5629c5219caf405e1c3186';
$MAX_TIMEOUT = 15;

// === CORS ===
$allowed_origins = [
    'https://werdu.de',
    'https://www.werdu.de',
    'http://werdu.de',
    'http://localhost',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// === VALIDATIE ===
$endpoint = $_GET['endpoint'] ?? '';
$product_id = $_GET['product_id'] ?? '';

if (!$endpoint) {
    http_response_code(400);
    echo json_encode(['error' => 'endpoint is verplicht']);
    exit;
}

// Alleen toegestane endpoints
$allowed_endpoints = ['products'];
$ep = explode('/', $endpoint)[0];
if (!in_array($ep, $allowed_endpoints)) {
    http_response_code(403);
    echo json_encode(['error' => 'Endpoint niet toegestaan']);
    exit;
}

// === URL OPBOUWEN ===
$url = $WC_BASE . '/' . ltrim($endpoint, '/');
if ($product_id) {
    $url .= '/' . intval($product_id);
}

$url .= (strpos($url, '?') === false ? '?' : '&');
$url .= 'consumer_key=' . urlencode($CONSUMER_KEY);
$url .= '&consumer_secret=' . urlencode($CONSUMER_SECRET);

// === CURL ===
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => $MAX_TIMEOUT,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_USERAGENT => 'WerduApp-Proxy/1.0',
    CURLOPT_HTTPHEADER => ['Accept: application/json']
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

header('Content-Type: application/json');

if ($error) {
    http_response_code(502);
    echo json_encode(['error' => 'WooCommerce niet bereikbaar', 'details' => $error]);
    exit;
}

if ($http_code !== 200) {
    http_response_code(502);
    echo json_encode(['error' => 'WooCommerce fout', 'http_code' => $http_code]);
    exit;
}

// JSON valideren
$json = json_decode($response);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(502);
    echo json_encode(['error' => 'Ongeldig JSON antwoord']);
    exit;
}

echo $response;