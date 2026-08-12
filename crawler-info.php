<?php
/**
 * WERDU.DE Crawler Info Presenteerblad
 * 
 * Dit script toont SEO-vriendelijke informatie over werdu.de aan goede crawlers
 * (Googlebot, Bingbot, OpenSEO, Ahrefs, Semrush, etc.)
 * Slechte crawlers (scanners, spammers, scrapers) krijgen een lege 403.
 * 
 * PLAATS DIT BESTAND IN: /var/www/vhosts/werdu.de/httpdocs/crawler-info.php
 * 
 * GEBRUIK: https://werdu.de/crawler-info.php
 */

// === GOEDE CRAWLERS WHITELIST (user agents) ===
$good_crawlers = [
    'googlebot',
    'googlebot-image',
    'googlebot-news',
    'googlebot-video',
    'bingbot',
    'bingpreview',
    'slurp',              // Yahoo
    'duckduckbot',
    'baiduspider',
    'yandexbot',
    'facebookexternalhit',
    'twitterbot',
    'linkedinbot',
    'whatsapp',
    'applebot',
    'openseo',
    'openseo-audit',
    'ahrefsbot',
    'ahrefssiteaudit',
    'semrushbot',
    'mj12bot',            // Majestic
    'dotbot',             // Moz
    'rogerbot',           // Moz
    'screaming frog',
    'sitebulb',
    'deepcrawl',
    'onpage',
    'seokicks',
    'blexbot',
    'germcrawler',
    'lighthouse',
    'pagespeed',
    'gtmetrix',
    'pingdom',
    'uptimerobot',
    'cloudflare-alwaysonline',
    'archive.org_bot',
    'ia_archiver',
];

// === SLECHTE CRAWLERS BLACKLIST (bekende scanners/spammers) ===
$bad_crawlers = [
    'sqlmap',
    'nikto',
    'nmap',
    'masscan',
    'zgrab',
    'gobuster',
    'dirbuster',
    'wfuzz',
    'burpsuite',
    'metasploit',
    'acunetix',
    'nessus',
    'openvas',
    'qualys',
    'detectify',
    'pentest',
    'scanner',
    'crawler4j',
    'scrapy',
    'python-requests',
    'curl',
    'wget',
    'libwww',
    'java',
    'php',
    'ruby',
    'node-fetch',
    'axios',
    'httpclient',
    'indy library',
    'okhttp',
    'winhttp',
    'http_request2',
    'mechanize',
    'urllib',
    'httpx',
    'aiohttp',
    'guzzle',
    'faraday',
    'zmeu',
    'morfeus',
    'surveybot',
    'webzip',
    'webreaper',
    'teleport',
    'webcopier',
    'wgetscraper',
    'emailcollector',
    'emailsiphon',
    'emailwolf',
    'extractorpro',
    'datacha0s',
    'libweb',
    'microsoft url control',
    'indy library',
    'disco',
    'zao',
    'mozillacrawl',
    'webbandit',
    'ecatch',
    'eyenetie',
    'nicerspro',
    'grub-client',
    'looksmart',
    'webmirror',
    'webfetch',
    'webgo',
    'webauto',
    'webstripper',
    'webauto',
    'webmirror',
    'webfetch',
    'webbandit',
    'webreaper',
    'webzip',
    'teleport',
    'webcopier',
    'wgetscraper',
    'emailcollector',
    'emailsiphon',
    'emailwolf',
    'extractorpro',
    'datacha0s',
    'libweb',
    'microsoft url control',
    'indy library',
    'disco',
    'zao',
    'mozillacrawl',
    'webbandit',
    'ecatch',
    'eyenetie',
    'nicerspro',
    'grub-client',
    'looksmart',
    'webmirror',
    'webfetch',
    'webgo',
    'webauto',
    'webstripper',
];

// === DETECTEER CRAWLER ===
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
$remote_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$is_good = false;
$is_bad = false;
$matched_crawler = 'onbekend';

// Check goede crawlers
foreach ($good_crawlers as $crawler) {
    if (strpos($user_agent, $crawler) !== false) {
        $is_good = true;
        $matched_crawler = $crawler;
        break;
    }
}

// Check slechte crawlers (alleen als niet al goed)
if (!$is_good) {
    foreach ($bad_crawlers as $crawler) {
        if (strpos($user_agent, $crawler) !== false) {
            $is_bad = true;
            $matched_crawler = $crawler;
            break;
        }
    }
}

// === SLECHTE CRAWLER → 403 + NIETS ===
if ($is_bad) {
    http_response_code(403);
    header('Content-Type: text/plain');
    die(''); // Volledig leeg — geen info, geen HTML, geen tekst
}

// === ONBEKENDE CRAWLER → 403 + NIETS (paranoia mode) ===
// Alleen goede crawlers krijgen toegang. Alles anders = 403.
if (!$is_good) {
    http_response_code(403);
    header('Content-Type: text/plain');
    die(''); // Volledig leeg
}

// === GOEDE CRAWLER → PRESENTEERBLAD ===
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <title>WERDU.DE — Crawler Info & SEO Presenteerblad</title>
    <meta name="description" content="Offizielle Crawler-Informationen für WERDU.DE. Domain-Übersicht, Sitemap, Robots.txt, Social Profiles und technische Spezifikationen.">

    <!-- Open Graph -->
    <meta property="og:title" content="WERDU.DE — Crawler Info & SEO Presenteerblad">
    <meta property="og:description" content="Offizielle Crawler-Informationen für WERDU.DE. Domain-Übersicht, Sitemap, Robots.txt und technische Spezifikationen.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://werdu.de/crawler-info.php">
    <meta property="og:site_name" content="WERDU.DE">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="WERDU.DE — Crawler Info & SEO Presenteerblad">
    <meta name="twitter:description" content="Offizielle Crawler-Informationen für WERDU.DE.">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0a2540 0%, #1e3a5f 100%);
            min-height: 100vh;
            padding: 24px;
            color: #1e293b;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            color: #fff;
            margin-bottom: 28px;
        }
        .header h1 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 6px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.85;
        }
        .header .crawler-badge {
            display: inline-block;
            background: #00d4aa;
            color: #0a2540;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-top: 10px;
            text-transform: uppercase;
        }
        .card {
            background: #fff;
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .card h2 {
            font-size: 16px;
            color: #0a2540;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card h2 .icon { font-size: 20px; }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .info-item {
            background: #f8fafc;
            border-radius: 10px;
            padding: 14px;
            border: 1px solid #e2e8f0;
        }
        .info-item.full { grid-column: 1 / -1; }
        .info-item .label {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .info-item .value {
            font-size: 14px;
            color: #0a2540;
            font-weight: 700;
            word-break: break-all;
        }
        .info-item .value a {
            color: #00d4aa;
            text-decoration: none;
        }
        .info-item .value a:hover { text-decoration: underline; }
        .info-item .value.small {
            font-size: 12px;
            font-weight: 400;
            color: #475569;
        }
        .schema-box {
            background: #0f172a;
            border-radius: 10px;
            padding: 16px;
            color: #e2e8f0;
            font-family: 'SF Mono', Monaco, monospace;
            font-size: 12px;
            line-height: 1.6;
            overflow-x: auto;
        }
        .schema-box .comment { color: #64748b; }
        .schema-box .key { color: #7dd3fc; }
        .schema-box .string { color: #86efac; }
        .schema-box .number { color: #fbbf24; }
        .footer {
            text-align: center;
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            margin-top: 20px;
        }
        .footer a { color: #00d4aa; text-decoration: none; }
        @media (max-width: 640px) {
            .info-grid { grid-template-columns: 1fr; }
            .header h1 { font-size: 22px; }
            body { padding: 16px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🤖 WERDU.DE Crawler Info</h1>
            <p>Offizielles SEO-Presenteerblad für Suchmaschinen und Audit-Tools</p>
            <div class="crawler-badge">✓ Erkannter Crawler: <?php echo htmlspecialchars(ucfirst($matched_crawler)); ?></div>
        </div>

        <!-- DOMAIN ÜBERSICHT -->
        <div class="card">
            <h2><span class="icon">🌐</span> Domain-Übersicht</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Domain</div>
                    <div class="value">werdu.de</div>
                </div>
                <div class="info-item">
                    <div class="label">Protokoll</div>
                    <div class="value">HTTPS (TLS 1.3)</div>
                </div>
                <div class="info-item">
                    <div class="label">CMS</div>
                    <div class="value">WordPress 6.x + WooCommerce</div>
                </div>
                <div class="info-item">
                    <div class="label">Theme</div>
                    <div class="value">Shoppingcart Child + Elementor</div>
                </div>
                <div class="info-item full">
                    <div class="label">Beschreibung</div>
                    <div class="value small">WERDU.DE ist ein deutscher Online-Shop für LiFePO4 Heimspeicher, Solarbatterien und Energiespeicher-Systeme. Direktimport aus China mit transparenten Preisen — ohne Markenaufschlag. Produkte: 16kWh TewayCell, 16kWh Basen Green, 30-32kWh modulare Systeme. Zielgruppe: Privatanwender mit PV-Anlage (0% MwSt.) und B2B.</div>
                </div>
            </div>
        </div>

        <!-- SITEMAP & ROBOTS -->
        <div class="card">
            <h2><span class="icon">🗺️</span> Sitemap & Robots.txt</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Sitemap XML</div>
                    <div class="value"><a href="https://werdu.de/sitemap.xml" target="_blank">https://werdu.de/sitemap.xml</a></div>
                </div>
                <div class="info-item">
                    <div class="label">Robots.txt</div>
                    <div class="value"><a href="https://werdu.de/robots.txt" target="_blank">https://werdu.de/robots.txt</a></div>
                </div>
                <div class="info-item">
                    <div class="label">RSS Feed</div>
                    <div class="value"><a href="https://werdu.de/feed/" target="_blank">https://werdu.de/feed/</a></div>
                </div>
                <div class="info-item">
                    <div class="label">Blog</div>
                    <div class="value"><a href="https://werdu.de/blog/" target="_blank">https://werdu.de/blog/</a></div>
                </div>
            </div>
        </div>

        <!-- KONTAKT -->
        <div class="card">
            <h2><span class="icon">📞</span> Kontakt & Impressum</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Telefon</div>
                    <div class="value">+49 151 20229842</div>
                </div>
                <div class="info-item">
                    <div class="label">Kontaktseite</div>
                    <div class="value"><a href="https://werdu.de/kontakt/" target="_blank">https://werdu.de/kontakt/</a></div>
                </div>
                <div class="info-item full">
                    <div class="label">Impressum</div>
                    <div class="value"><a href="https://werdu.de/impressum/" target="_blank">https://werdu.de/impressum/</a></div>
                </div>
            </div>
        </div>

        <!-- SOCIAL PROFILES -->
        <div class="card">
            <h2><span class="icon">🔗</span> Social Profiles & Externe Links</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">LinkedIn</div>
                    <div class="value"><a href="https://www.linkedin.com/company/werdu-de/" target="_blank">linkedin.com/company/werdu-de</a></div>
                </div>
                <div class="info-item">
                    <div class="label">YouTube</div>
                    <div class="value"><a href="https://www.youtube.com/@werdu-de" target="_blank">youtube.com/@werdu-de</a></div>
                </div>
                <div class="info-item">
                    <div class="label">Instagram</div>
                    <div class="value"><a href="https://www.instagram.com/werdu.de/" target="_blank">instagram.com/werdu.de</a></div>
                </div>
                <div class="info-item">
                    <div class="label">Facebook</div>
                    <div class="value"><a href="https://www.facebook.com/werdu.de/" target="_blank">facebook.com/werdu.de</a></div>
                </div>
            </div>
        </div>

        <!-- HAUPTSEITEN -->
        <div class="card">
            <h2><span class="icon">📄</span> Wichtige Seiten</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Startseite</div>
                    <div class="value"><a href="https://werdu.de/" target="_blank">https://werdu.de/</a></div>
                </div>
                <div class="info-item">
                    <div class="label">Shop</div>
                    <div class="value"><a href="/shop/" target="_blank">https://werdu.de/shop/</a></div>
                </div>
                <div class="info-item">
                    <div class="label">Über uns</div>
                    <div class="value"><a href="https://werdu.de/ueber-uns/" target="_blank">https://werdu.de/ueber-uns/</a></div>
                </div>
                <div class="info-item">
                    <div class="label">Installation</div>
                    <div class="value"><a href="https://werdu.de/heimspeicher-installation/" target="_blank">https://werdu.de/heimspeicher-installation/</a></div>
                </div>
                <div class="info-item">
                    <div class="label">Rechner</div>
                    <div class="value"><a href="https://werdu.de/gratis-heimspeicher-rechner-online/" target="_blank">Gratis Heimspeicher Rechner</a></div>
                </div>
                <div class="info-item">
                    <div class="label">Sicherheit</div>
                    <div class="value"><a href="https://werdu.de/lifepo4-sicherheit-die-sicherste-solarbatterie-technologie-2026/" target="_blank">LiFePO4 Sicherheit</a></div>
                </div>
            </div>
        </div>

        <!-- PRODUKTE -->
        <div class="card">
            <h2><span class="icon">🔋</span> Hauptprodukte</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">16kWh TewayCell 48V 300Ah</div>
                    <div class="value"><a href="https://werdu.de/16-kwh-heimspeicher-lifepo4-solarbatterie/" target="_blank">Produktseite</a></div>
                </div>
                <div class="info-item">
                    <div class="label">16kWh Basen Green 51,2V 314Ah</div>
                    <div class="value"><a href="https://werdu.de/16-kwh-lifepo4-heimspeicher-51-2v-314ah/" target="_blank">Produktseite</a></div>
                </div>
                <div class="info-item">
                    <div class="label">30-32kWh 51,2V modular</div>
                    <div class="value"><a href="https://werdu.de/30-32-kwh-lifepo4-heimspeicher-560-628ah/" target="_blank">Produktseite</a></div>
                </div>
                <div class="info-item">
                    <div class="label">Gotion 340Ah Zellen</div>
                    <div class="value"><a href="https://werdu.de/gotion-340ah-lifepo4-batteriezellen/" target="_blank">Produktseite</a></div>
                </div>
            </div>
        </div>

        <!-- TECHNISCHE SPEZIFIKATIONEN -->
        <div class="card">
            <h2><span class="icon">⚙️</span> Technische Spezifikationen</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Server</div>
                    <div class="value">Apache (Shared Hosting)</div>
                </div>
                <div class="info-item">
                    <div class="label">PHP</div>
                    <div class="value">8.3.x</div>
                </div>
                <div class="info-item">
                    <div class="label">Datenbank</div>
                    <div class="value">MySQL / MariaDB</div>
                </div>
                <div class="info-item">
                    <div class="label">Cache</div>
                    <div class="value">WP Super Cache + Autoptimize</div>
                </div>
                <div class="info-item">
                    <div class="label">SEO Plugin</div>
                    <div class="value">Rank Math SEO</div>
                </div>
                <div class="info-item">
                    <div class="label">Analytics</div>
                    <div class="value">Google Analytics 4 + Search Console</div>
                </div>
                <div class="info-item full">
                    <div class="label">Sicherheitsheader</div>
                    <div class="value small">Content-Security-Policy, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, Strict-Transport-Security, X-Powered-By removed, Server header removed</div>
                </div>
            </div>
        </div>

        <!-- JSON-LD SCHEMA -->
        <div class="card">
            <h2><span class="icon">📋</span> JSON-LD Schema (Organization)</h2>
            <div class="schema-box">
{
  <span class="key">"@context"</span>: <span class="string">"https://schema.org"</span>,
  <span class="key">"@type"</span>: <span class="string">"Organization"</span>,
  <span class="key">"name"</span>: <span class="string">"WERDU.DE"</span>,
  <span class="key">"url"</span>: <span class="string">"https://werdu.de"</span>,
  <span class="key">"logo"</span>: <span class="string">"https://werdu.de/wp-content/uploads/werdu-logo.png"</span>,
  <span class="key">"description"</span>: <span class="string">"Deutscher Online-Shop für LiFePO4 Heimspeicher und Solarbatterien. Direktimport aus China mit transparenten Preisen."</span>,
  <span class="key">"telephone"</span>: <span class="string">"+49-151-20229842"</span>,
  <span class="key">"sameAs"</span>: [
    <span class="string">"https://www.linkedin.com/company/werdu-de/"</span>,
    <span class="string">"https://www.youtube.com/@werdu-de"</span>,
    <span class="string">"https://www.instagram.com/werdu.de/"</span>,
    <span class="string">"https://www.facebook.com/werdu.de/"</span>
  ],
  <span class="key">"contactPoint"</span>: {
    <span class="key">"@type"</span>: <span class="string">"ContactPoint"</span>,
    <span class="key">"telephone"</span>: <span class="string">"+49-151-20229842"</span>,
    <span class="key">"contactType"</span>: <span class="string">"customer service"</span>,
    <span class="key">"availableLanguage"</span>: <span class="string">"German"</span>
  }
}
            </div>
        </div>

        <!-- CRAWLER STATUS -->
        <div class="card">
            <h2><span class="icon">🔒</span> Crawler-Zugriffsregeln</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Ihr Status</div>
                    <div class="value" style="color: #22c55e;">✓ AUTORISIERT</div>
                </div>
                <div class="info-item">
                    <div class="label">User-Agent</div>
                    <div class="value small"><?php echo htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'Nicht gesetzt'); ?></div>
                </div>
                <div class="info-item">
                    <div class="label">IP-Adresse</div>
                    <div class="value"><?php echo htmlspecialchars($remote_ip); ?></div>
                </div>
                <div class="info-item">
                    <div class="label">Zeitstempel</div>
                    <div class="value"><?php echo date('d.m.Y H:i:s T'); ?></div>
                </div>
                <div class="info-item full">
                    <div class="label">Erlaubte Crawler</div>
                    <div class="value small">Googlebot, Bingbot, DuckDuckBot, Baiduspider, YandexBot, Facebook, Twitter, LinkedIn, WhatsApp, AppleBot, OpenSEO, AhrefsBot, SEMrushBot, MJ12Bot, DotBot, Screaming Frog, Sitebulb, DeepCrawl, OnPage, SEOkicks, BLEXBot, Lighthouse, PageSpeed, GTmetrix, Pingdom, UptimeRobot, Cloudflare, Archive.org</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Diese Seite ist ausschließlich für autorisierte Crawler bestimmt.</p>
            <p>Nicht-autorisierte Zugriffe werden mit 403 und leerer Antwort blockiert.</p>
            <p style="margin-top:8px;">© <?php echo date('Y'); ?> WERDU.DE — <a href="https://werdu.de/">Zurück zur Startseite</a></p>
        </div>
    </div>
</body>
</html>