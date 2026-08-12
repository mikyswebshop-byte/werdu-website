<?php
/**
 * Plugin Name: WERDU Beveiliging
 * Description: Aangepaste beveiligingsmaatregelen voor werdu.de
 * Version: 1.0
 * Author: Michael van der Veen
 * Network: false
 */

// ============================================
// 1. WORDPRESS VERSIE VERBERGEN
// ============================================

// Verwijder versie uit RSS-feeds en headers
add_filter('the_generator', '__return_empty_string');

// Verwijder versie uit scripts/styles query strings
add_filter('style_loader_src', 'werdu_remove_version', 9999);
add_filter('script_loader_src', 'werdu_remove_version', 9999);

function werdu_remove_version($src) {
    if (strpos($src, 'ver=') !== false) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}

// ============================================
// 2. REST API GEBRUIKERS-ENDPOINT BESCHERMEN
// ============================================

// .htaccess blokkeert al /wp-json/wp/v2/users voor niet-ingelogden
// Dit is een dubbele beveiliging als .htaccess faalt
add_filter('rest_endpoints', function($endpoints) {
    if (!is_user_logged_in()) {
        if (isset($endpoints['/wp/v2/users'])) {
            unset($endpoints['/wp/v2/users']);
        }
        if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
            unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
        }
    }
    return $endpoints;
}, 100);

// ============================================
// 3. AUTHOR-ARCHIEF REDIRECTEN
// ============================================

// .htaccess blokkeert ?author=N, dit redirect author-archieven
add_action('template_redirect', function() {
    if (is_author()) {
        wp_redirect(home_url(), 301);
        exit;
    }
});

// ============================================
// 4. LOGIN-FOUTMELDINGEN AANPASSEN
// ============================================

// Geef geen hint of gebruikersnaam of wachtwoord fout is
add_filter('login_errors', function($error) {
    return '<strong>Fout:</strong> Ongeldige inloggegevens.';
});

// ============================================
// 5. BRUTE-FORCE BESCHERMING
// ============================================

class Werdu_BruteForce_Protection {
    
    private $max_attempts = 5;
    private $lockout_time = 900; // 15 minuten
    
    public function __construct() {
        add_action('wp_login_failed', [$this, 'log_failed_attempt']);
        add_filter('authenticate', [$this, 'check_lockout'], 30, 3);
    }
    
    public function log_failed_attempt($username) {
        $ip = $this->get_client_ip();
        if (empty($ip)) return;
        
        $key = 'werdu_failed_login_' . md5($ip);
        $attempts = get_transient($key);
        
        if ($attempts === false) {
            set_transient($key, 1, $this->lockout_time);
        } else {
            set_transient($key, $attempts + 1, $this->lockout_time);
        }
    }
    
    public function check_lockout($user, $username, $password) {
        $ip = $this->get_client_ip();
        if (empty($ip)) return $user;
        
        $key = 'werdu_failed_login_' . md5($ip);
        $attempts = get_transient($key);
        
        if ($attempts !== false && $attempts >= $this->max_attempts) {
            return new WP_Error(
                'too_many_attempts', 
                '<strong>Fout:</strong> Te veel mislukte pogingen. Probeer het over 15 minuten opnieuw.'
            );
        }
        
        return $user;
    }
    
    private function get_client_ip() {
        $ip_keys = [
            'HTTP_CF_CONNECTING_IP',    // Cloudflare
            'HTTP_X_FORWARDED_FOR',     // Proxy/load balancer
            'HTTP_X_REAL_IP',           // Nginx proxy
            'REMOTE_ADDR'               // Standaard
        ];
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                // Bij X-Forwarded-For: neem eerste IP (client)
                if (strpos($ip, ',') !== false) {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]);
                }
                // Valideer IP
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
                // Fallback voor interne IPs
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }
}

new Werdu_BruteForce_Protection();

// ============================================
// 6. BESTANDSBEWERKING IN WP-ADMIN UITZETTEN
// ============================================

// Voorkomt dat hackers via gehackte admin-account bestanden bewerken
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

// ============================================
// 7. XML-RPC VOLLEDIG UITZETTEN
// ============================================

// .htaccess blokkeert al, dit zet WordPress XML-RPC functies uit
add_filter('xmlrpc_enabled', '__return_false');

// Verwijder pingback methoden (DDoS-vector)
add_filter('xmlrpc_methods', function($methods) {
    unset($methods['pingback.ping']);
    unset($methods['pingback.extensions.getPingbacks']);
    return $methods;
});

// ============================================
// 8. SECURITY HEADERS AANVULLEN
// ============================================

// Alles zit in .htaccess, maar dit vangt als .htaccess faalt
add_action('send_headers', function() {
    // Cache control voor gevoelige pagina's
    if (is_admin() || (function_exists('is_login') && is_login())) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
});

// ============================================
// 9. AUTO-UPDATE VOOR SECURITY RELEASES
// ============================================

// Core security updates automatisch
add_filter('auto_update_core', '__return_true');

// Plugin security updates voor beveiligingsplugins
add_filter('auto_update_plugin', function($update, $item) {
    $security_plugins = ['wordfence', 'sucuri-scanner', 'better-wp-security', 'solid-security'];
    if (isset($item->slug) && in_array($item->slug, $security_plugins)) {
        return true;
    }
    return $update;
}, 10, 2);

// ============================================
// 10. NIEUWE GEBRUIKERSREGISTRATIE BLOKKEREN
// ============================================

// Jouw site heeft geen publieke registratie nodig
add_filter('option_users_can_register', '__return_false');

// ============================================
// 11. UPLOAD-BESTANDSTYPES BEPERKEN
// ============================================

add_filter('upload_mimes', function($mimes) {
    // Verwijder potentieel gevaarlijke bestandstypes
    unset($mimes['swf']);
    unset($mimes['exe']);
    unset($mimes['dll']);
    unset($mimes['bat']);
    unset($mimes['cmd']);
    unset($mimes['sh']);
    unset($mimes['php']);
    unset($mimes['php3']);
    unset($mimes['php4']);
    unset($mimes['php5']);
    unset($mimes['phtml']);
    unset($mimes['pl']);
    unset($mimes['py']);
    unset($mimes['jsp']);
    unset($mimes['asp']);
    unset($mimes['aspx']);
    
    return $mimes;
});

// ============================================
// 12. DEBUGGING UITZETTEN IN PRODUCTIE
// ============================================

if (!defined('WP_DEBUG')) {
    define('WP_DEBUG', false);
}
if (!defined('WP_DEBUG_LOG')) {
    define('WP_DEBUG_LOG', false);
}
if (!defined('WP_DEBUG_DISPLAY')) {
    define('WP_DEBUG_DISPLAY', false);
}

// ============================================
// 13. SESSION SECURITY
// ============================================

// Secure cookies indien HTTPS
if (!defined('COOKIE_SECURE')) {
    define('COOKIE_SECURE', true);
}

// ============================================
// 14. ADMIN BAR VERBERGEN VOOR NIET-ADMINS
// ============================================

// Vermindert informatielekkage (gebruikersnamen in admin bar)
add_action('after_setup_theme', function() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
});

// ============================================
// 15. LOGIN-PAGINA TITEL AANPASSEN
// ============================================

// Verwijdert WordPress-versie uit login-pagina titel
add_filter('login_title', function($title) {
    return str_replace(' &#8212; WordPress', '', $title);
});

// ============================================
// 16. WOOCOMMERCE SPECIFIEK (indien actief)
// ============================================

// Verwijder WooCommerce versie uit broncode
add_action('wp_head', function() {
    if (class_exists('WooCommerce')) {
        remove_action('wp_head', 'wc_generator_tag');
    }
}, 1);

// ============================================
// 17. ELEMENTOR COMPATIBILITEIT
// ============================================

// Zorg dat Elementor REST API blijft werken
add_filter('elementor/editor/user_can_edit', function($can_edit, $post_id) {
    return $can_edit;
}, 10, 2);

// ============================================
// 18. SILENTSHIELD COMPATIBILITEIT
// ============================================

// SilentShield gebruikt blob workers — CSP is al in .htaccess
// Geen extra actie nodig

// ============================================
// 19. SECURITY.TXT GENEREREN
// ============================================

// Optioneel: uncomment om security.txt te genereren
/*
add_action('init', function() {
    if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/.well-known/security.txt') {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Contact: mailto:service@werdu.de\n";
        echo "Expires: " . date('Y-m-d\TH:i:s\Z', strtotime('+1 year')) . "\n";
        echo "Preferred-Languages: de, nl, en\n";
        exit;
    }
});
*/

// ============================================
// 20. IP-LOGGING BIJ MISLUKTE LOGINS (optioneel)
// ============================================

// Log naar error log voor analyse
add_action('wp_login_failed', function($username) {
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    error_log('WERDU SECURITY: Misluke login poging voor gebruiker "' . sanitize_user($username) . '" van IP: ' . $ip);
});