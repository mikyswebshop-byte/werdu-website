<?php
/**
 * Werdu PVGIS Proxy v2
 * URL: https://werdu.de/pvgis-proxy.php
 */

$PVGIS_BASE = 'https://re.jrc.ec.europa.eu/api/v5_2/PVcalc';
$MAX_TIMEOUT = 20;

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

$lat = $_GET['lat'] ?? null;
$lon = $_GET['lon'] ?? null;

if (!$lat || !$lon) {
    http_response_code(400);
    echo json_encode(['error' => 'lat en lon zijn verplicht']);
    exit;
}

if (!is_numeric($lat) || !is_numeric($lon)) {
    http_response_code(400);
    echo json_encode(['error' => 'Ongeldige coördinaten']);
    exit;
}

$params = [
    'lat' => $lat,
    'lon' => $lon,
    'peakpower' => $_GET['peakpower'] ?? '5.5',
    'loss' => $_GET['loss'] ?? '14',
    'mountingplace' => 'building',
    'angle' => $_GET['angle'] ?? '30',
    'aspect' => $_GET['aspect'] ?? '0',
    'outputformat' => 'json'
];

$allowed_params = ['lat','lon','peakpower','loss','mountingplace','angle','aspect','outputformat'];
$query = [];
foreach ($params as $key => $val) {
    if (in_array($key, $allowed_params)) {
        $query[] = urlencode($key) . '=' . urlencode($val);
    }
}

$url = $PVGIS_BASE . '?' . implode('&', $query);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => $MAX_TIMEOUT,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; WerduApp-Proxy/1.0; +https://werdu.de)',
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
    CURLOPT_ENCODING => ''
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$errno = curl_errno($ch);
curl_close($ch);

header('Content-Type: application/json');

if ($error || $errno) {
    http_response_code(502);
    echo json_encode(['error' => 'PVGIS niet bereikbaar', 'curl_error' => $error, 'curl_errno' => $errno]);
    exit;
}

if ($http_code !== 200) {
    http_response_code(502);
    echo json_encode(['error' => 'PVGIS fout', 'http_code' => $http_code, 'response_preview' => substr($response, 0, 500)]);
    exit;
}

if (empty($response)) {
    http_response_code(502);
    echo json_encode(['error' => 'Leeg antwoord van PVGIS']);
    exit;
}

$json = json_decode($response);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(502);
    echo json_encode(['error' => 'Ongeldig JSON antwoord', 'json_error' => json_last_error_msg(), 'response_preview' => substr($response, 0, 500)]);
    exit;
}

echo $response;