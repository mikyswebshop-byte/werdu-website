<?php
/**
 * Werdu E-Mail Handler v2
 * URL: https://werdu.de/email-handler.php
 */

header('Content-Type: application/json');

$allowed_origins = [
    'https://werdu.de',
    'https://www.werdu.de',
    'http://werdu.de',
    'http://localhost',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Nur POST erlaubt']);
    exit;
}

// --- Bot-Schutz: Honeypot ---
if (!empty($_POST['website']) || !empty($input['website'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Ungültige Anfrage']);
    exit;
}

// --- Bot-Schutz: Rate Limiting (IP-basiert) ---
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateLimitFile = sys_get_temp_dir() . '/werdu_rate_' . md5($ip) . '.txt';
$maxRequests = 5;
$timeWindow = 3600; // 1 Stunde

if (file_exists($rateLimitFile)) {
    $requests = json_decode(file_get_contents($rateLimitFile), true);
    $requests = array_filter($requests, function($time) use ($timeWindow) {
        return (time() - $time) < $timeWindow;
    });
    if (count($requests) >= $maxRequests) {
        http_response_code(429);
        echo json_encode(['error' => 'Zu viele Anfragen. Bitte versuchen Sie es später erneut.']);
        exit;
    }
    $requests[] = time();
} else {
    $requests = [time()];
}
file_put_contents($rateLimitFile, json_encode($requests));

$input = json_decode(file_get_contents('php://input'), true);

$email = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);
$plz = htmlspecialchars($input['plz'] ?? '');
$pvLeistung = floatval($input['pvLeistung'] ?? 0);
$dachneigung = htmlspecialchars($input['dachneigung'] ?? '');
$ausrichtung = htmlspecialchars($input['ausrichtung'] ?? '');
$verbrauch = floatval($input['verbrauch'] ?? 0);
$produktName = htmlspecialchars($input['produktName'] ?? '');
$produktLink = filter_var($input['produktLink'] ?? '', FILTER_VALIDATE_URL);
$produktPreis = floatval($input['produktPreis'] ?? 0);
$jahresErtrag = floatval($input['jahresErtrag'] ?? 0);
$ersparnis = floatval($input['ersparnis'] ?? 0);
$amortisation = floatval($input['amortisation'] ?? 0);
$autarkiegrad = intval($input['autarkiegrad'] ?? 0);

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Ungültige E-Mail Adresse']);
    exit;
}

$absender = 'service@werdu.de';
$antwortAn = 'service@werdu.de';

// --- E-Mail an Kunde ---
$betreffKunde = 'Ihre PV-Ertragsberechnung – Werdu.de';
$nachrichtKunde = '<!DOCTYPE html>
<html lang="de">
<head><meta charset="UTF-8"><title>Ihre Berechnung</title></head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px;">
<table width="100%" cellpadding="0" cellspacing="0"><tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e0e0e0;">
<tr><td style="background:#0a0e1a;padding:25px;text-align:center;">
<h1 style="color:#ff6600;margin:0;font-size:24px;">WERDU</h1>
<p style="color:#94a3b8;margin:5px 0 0 0;font-size:12px;">Solarbatterien & Energiespeicher</p>
</td></tr>
<tr><td style="padding:30px;">
<h2 style="color:#1a5276;margin-top:0;">Ihre persönliche PV-Berechnung</h2>
<p>Hallo,</p>
<p>vielen Dank für Ihre Anfrage auf <strong>werdu.de</strong>. Hier sind Ihre Ergebnisse:</p>
<table width="100%" cellpadding="10" cellspacing="0" style="background:#f9f9f9;border-radius:4px;margin:15px 0;">
<tr><td style="border-bottom:1px solid #e0e0e0;color:#555;"><strong>Standort:</strong></td><td style="border-bottom:1px solid #e0e0e0;text-align:right;">PLZ ' . $plz . '</td></tr>
<tr><td style="border-bottom:1px solid #e0e0e0;color:#555;"><strong>PV-Leistung:</strong></td><td style="border-bottom:1px solid #e0e0e0;text-align:right;">' . $pvLeistung . ' kWp</td></tr>
<tr><td style="border-bottom:1px solid #e0e0e0;color:#555;"><strong>Jahresertrag:</strong></td><td style="border-bottom:1px solid #e0e0e0;text-align:right;">' . round($jahresErtrag) . ' kWh</td></tr>
<tr><td style="border-bottom:1px solid #e0e0e0;color:#555;"><strong>Jährliche Ersparnis:</strong></td><td style="border-bottom:1px solid #e0e0e0;text-align:right;color:#27ae60;font-weight:bold;">€' . round($ersparnis) . '</td></tr>
<tr><td style="border-bottom:1px solid #e0e0e0;color:#555;"><strong>Amortisation:</strong></td><td style="border-bottom:1px solid #e0e0e0;text-align:right;">' . number_format($amortisation, 1) . ' Jahre</td></tr>
<tr><td style="color:#555;"><strong>Autarkiegrad:</strong></td><td style="text-align:right;font-weight:bold;">' . $autarkiegrad . '%</td></tr>
</table>
<h3 style="color:#1a5276;">Empfohlenes System für Sie</h3>
<p style="font-size:16px;margin-bottom:5px;"><strong>' . $produktName . '</strong></p>
<p style="color:#ff6600;font-size:18px;font-weight:bold;margin-top:0;">ab €' . number_format($produktPreis, 0, ',', '.') . '</p>
<p style="text-align:center;margin:25px 0;">
<a href="' . $produktLink . '" style="display:inline-block;background:#ff6600;color:#ffffff;padding:14px 30px;text-decoration:none;border-radius:4px;font-weight:bold;font-size:15px;">Jetzt ansehen →</a>
</p>
<p style="font-size:12px;color:#777;margin-top:30px;border-top:1px solid #e0e0e0;padding-top:15px;">
<strong>Hinweis:</strong> Diese Berechnung dient zur Orientierung und stellt kein verbindliches Angebot dar.<br><br>
Werdu.de – Ihr Spezialist für Solarbatterien und Energiespeicher<br>
E-Mail: <a href="mailto:service@werdu.de">service@werdu.de</a>
</p>
</td></tr>
<tr><td style="background:#0a0e1a;padding:15px;text-align:center;">
<p style="color:#64748b;font-size:11px;margin:0;">© ' . date('Y') . ' Werdu.de – Alle Rechte vorbehalten</p>
</td></tr>
</table>
</td></tr></table>
</body></html>';

$headerKunde = 'MIME-Version: 1.0' . "\r\n";
$headerKunde .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
$headerKunde .= 'From: Werdu.de <' . $absender . '>' . "\r\n";
$headerKunde .= 'Reply-To: ' . $antwortAn . "\r\n";

// --- E-Mail an Werdu (intern) ---
$betreffIntern = 'Neue PV-Berechnung – ' . $email;
$nachrichtIntern = '<!DOCTYPE html>
<html lang="de">
<head><meta charset="UTF-8"><title>Neue Berechnung</title></head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px;">
<table width="100%" cellpadding="0" cellspacing="0"><tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e0e0e0;">
<tr><td style="background:#ff6600;padding:15px;text-align:center;color:#fff;font-weight:bold;font-size:16px;">
Neue Calculator-Anfrage
</td></tr>
<tr><td style="padding:25px;">
<h3 style="color:#1a5276;margin-top:0;">Kundendaten</h3>
<table width="100%" cellpadding="8" cellspacing="0">
<tr><td style="border-bottom:1px solid #e0e0e0;"><strong>E-Mail:</strong></td><td style="border-bottom:1px solid #e0e0e0;"><a href="mailto:' . $email . '">' . $email . '</a></td></tr>
<tr><td style="border-bottom:1px solid #e0e0e0;"><strong>PLZ:</strong></td><td style="border-bottom:1px solid #e0e0e0;">' . $plz . '</td></tr>
<tr><td style="border-bottom:1px solid #e0e0e0;"><strong>PV-Leistung:</strong></td><td style="border-bottom:1px solid #e0e0e0;">' . $pvLeistung . ' kWp</td></tr>
<tr><td style="border-bottom:1px solid #e0e0e0;"><strong>Dachneigung:</strong></td><td style="border-bottom:1px solid #e0e0e0;">' . $dachneigung . '</td></tr>
<tr><td style="border-bottom:1px solid #e0e0e0;"><strong>Ausrichtung:</strong></td><td style="border-bottom:1px solid #e0e0e0;">' . $ausrichtung . '</td></tr>
<tr><td><strong>Verbrauch:</strong></td><td>' . $verbrauch . ' kWh/Jahr</td></tr>
</table>

<h3 style="color:#1a5276;margin-top:20px;">Empfohlenes Produkt</h3>
<table width="100%" cellpadding="8" cellspacing="0" style="background:#f9f9f9;border-radius:4px;">
<tr><td style="border-bottom:1px solid #e0e0e0;"><strong>Name:</strong></td><td style="border-bottom:1px solid #e0e0e0;">' . $produktName . '</td></tr>
<tr><td style="border-bottom:1px solid #e0e0e0;"><strong>Link:</strong></td><td style="border-bottom:1px solid #e0e0e0;"><a href="' . $produktLink . '">' . $produktLink . '</a></td></tr>
<tr><td><strong>Preis:</strong></td><td>€' . number_format($produktPreis, 2, ',', '.') . '</td></tr>
</table>

<h3 style="color:#1a5276;margin-top:20px;">Berechnungsergebnisse</h3>
<table width="100%" cellpadding="8" cellspacing="0" style="background:#f9f9f9;border-radius:4px;">
<tr><td style="border-bottom:1px solid #e0e0e0;"><strong>Jahresertrag:</strong></td><td style="border-bottom:1px solid #e0e0e0;">' . round($jahresErtrag) . ' kWh</td></tr>
<tr><td style="border-bottom:1px solid #e0e0e0;"><strong>Ersparnis:</strong></td><td style="border-bottom:1px solid #e0e0e0;">€' . round($ersparnis) . '/Jahr</td></tr>
<tr><td style="border-bottom:1px solid #e0e0e0;"><strong>Amortisation:</strong></td><td style="border-bottom:1px solid #e0e0e0;">' . number_format($amortisation, 1) . ' Jahre</td></tr>
<tr><td><strong>Autarkiegrad:</strong></td><td>' . $autarkiegrad . '%</td></tr>
</table>

<p style="margin-top:25px;text-align:center;">
<a href="mailto:' . $email . '?subject=Ihre%20PV-Berechnung%20-%20Werdu.de&body=Hallo,%0A%0Avielen%20Dank%20für%20Ihre%20Anfrage.%0A%0AMit%20freundlichen%20Grüßen%0AWerdu.de" style="display:inline-block;background:#1a5276;color:#ffffff;padding:12px 25px;text-decoration:none;border-radius:4px;font-weight:bold;">Kunde sofort antworten</a>
</p>
</td></tr>
</table>
</td></tr></table>
</body></html>';

$headerIntern = 'MIME-Version: 1.0' . "\r\n";
$headerIntern .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
$headerIntern .= 'From: Werdu.de Rechner <' . $absender . '>' . "\r\n";

// --- E-Mails versenden ---
$mailKunde = mail($email, $betreffKunde, $nachrichtKunde, $headerKunde);
$mailIntern = mail($antwortAn, $betreffIntern, $nachrichtIntern, $headerIntern);

if ($mailKunde && $mailIntern) {
    echo json_encode(['success' => true, 'message' => 'E-Mails versendet']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'E-Mail Versand fehlgeschlagen', 'mail_kunde' => $mailKunde, 'mail_intern' => $mailIntern]);
}