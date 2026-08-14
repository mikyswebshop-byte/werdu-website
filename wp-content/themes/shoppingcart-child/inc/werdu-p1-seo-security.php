<?php
/**
 * WERDU P1 — Security hardening, SEO/AIO schema, Calc asset enqueue
 * Loaded from shoppingcart-child/functions.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ============================================================
   P1.1 SECURITY — Generator, XML-RPC, REST users, asset versions
   ============================================================ */

// Generator meta (idempotent — also removed elsewhere in functions.php)
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

// Disable XML-RPC
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'wp_headers', function ( $headers ) {
    unset( $headers['X-Pingback'] );
    return $headers;
} );

// Block REST API user enumeration for anonymous visitors
add_filter( 'rest_endpoints', function ( $endpoints ) {
    if ( is_user_logged_in() ) {
        return $endpoints;
    }
    unset( $endpoints['/wp/v2/users'] );
    unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    return $endpoints;
} );

// Strip ?ver= from non-core theme/plugin assets (keep WP core versions)
function werdu_strip_noncore_asset_ver( $src ) {
    if ( ! is_string( $src ) || $src === '' ) {
        return $src;
    }
    if ( strpos( $src, 'ver=' ) === false ) {
        return $src;
    }
    // Preserve WordPress core asset versioning
    if ( strpos( $src, '/wp-includes/' ) !== false || strpos( $src, '/wp-admin/' ) !== false ) {
        return $src;
    }
    // CF7 field chrome must cache-bust; query strings are otherwise stripped.
    if ( strpos( $src, 'werdu-cf7-calc-look.css' ) !== false ) {
        return $src;
    }
    return remove_query_arg( 'ver', $src );
}
add_filter( 'style_loader_src', 'werdu_strip_noncore_asset_ver', 9999 );
add_filter( 'script_loader_src', 'werdu_strip_noncore_asset_ver', 9999 );

/* ============================================================
   P1.1b E-MAIL OBFUSCATION (Impressum-compliant HEX entities)
   ============================================================ */

/**
 * Convert plain e-mail to HTML numeric character references.
 * Remains human-readable in the browser; frustrates naive scrapers.
 */
function werdu_email_to_entities( $email ) {
    $email = sanitize_email( $email );
    if ( ! $email || ! is_email( $email ) ) {
        return esc_html( $email );
    }
    $out = '';
    $len = strlen( $email );
    for ( $i = 0; $i < $len; $i++ ) {
        $out .= '&#' . ord( $email[ $i ] ) . ';';
    }
    return $out;
}

/**
 * Build obfuscated mailto anchor.
 */
function werdu_obfuscated_mailto( $email, $label = '' ) {
    $email = sanitize_email( $email );
    if ( ! $email || ! is_email( $email ) ) {
        return '';
    }
    $display = $label !== '' ? esc_html( $label ) : werdu_email_to_entities( $email );
    $href    = 'mailto:' . $email;
    // Obfuscate href via entities too
    $href_ent = '';
    $hlen     = strlen( $href );
    for ( $i = 0; $i < $hlen; $i++ ) {
        $href_ent .= '&#' . ord( $href[ $i ] ) . ';';
    }
    return '<a href="' . $href_ent . '" class="werdu-obf-mail">' . $display . '</a>';
}

/**
 * Replace plain emails / mailto links in HTML content.
 */
function werdu_obfuscate_emails_in_html( $content ) {
    if ( ! is_string( $content ) || $content === '' || is_admin() ) {
        return $content;
    }
    // Skip JSON-LD / scripts to avoid breaking structured data
    if ( stripos( $content, '<script' ) !== false && preg_match( '/application\/ld\+json/i', $content ) ) {
        // Still process outside script blocks via callback split
        $parts = preg_split( '/(<script\b[^>]*>.*?<\/script>)/is', $content, -1, PREG_SPLIT_DELIM_CAPTURE );
        if ( is_array( $parts ) ) {
            foreach ( $parts as $i => $part ) {
                if ( preg_match( '/^<script\b/i', $part ) ) {
                    continue;
                }
                $parts[ $i ] = werdu_obfuscate_emails_in_html_chunk( $part );
            }
            return implode( '', $parts );
        }
    }
    return werdu_obfuscate_emails_in_html_chunk( $content );
}

function werdu_obfuscate_emails_in_html_chunk( $content ) {
    // Existing mailto anchors
    $content = preg_replace_callback(
        '/<a\s+([^>]*?)href=(["\'])mailto:([^"\']+)\2([^>]*)>(.*?)<\/a>/is',
        function ( $m ) {
            $email = sanitize_email( html_entity_decode( $m[3], ENT_QUOTES, 'UTF-8' ) );
            if ( ! $email || ! is_email( $email ) ) {
                return $m[0];
            }
            // Keep visible label text if it is not the raw email
            $inner = trim( wp_strip_all_tags( $m[5] ) );
            $label = ( $inner && strcasecmp( $inner, $email ) !== 0 ) ? $inner : '';
            return werdu_obfuscated_mailto( $email, $label );
        },
        $content
    );

    // Bare emails not already inside tags/attributes
    $content = preg_replace_callback(
        '/(?<!["\'=]|&#)(?<!mailto:)([a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,})(?![^<]*>)/',
        function ( $m ) {
            return werdu_obfuscated_mailto( $m[1] );
        },
        $content
    );

    return $content;
}

add_filter( 'the_content', 'werdu_obfuscate_emails_in_html', 20 );
add_filter( 'widget_text', 'werdu_obfuscate_emails_in_html', 20 );
add_filter( 'widget_text_content', 'werdu_obfuscate_emails_in_html', 20 );

/* ============================================================
   P1.2–P1.4 CALC ASSETS + SEO HEAD (solarbatterie-rechner)
   ============================================================ */

function werdu_is_solarbatterie_rechner_page() {
    if ( is_page( 'solarbatterie-rechner' ) ) {
        return true;
    }
    if ( is_page_template( 'solarbatterie-rechner-v3.php' ) ) {
        return true;
    }
    return false;
}

function werdu_enqueue_calc_assets() {
    if ( ! werdu_is_solarbatterie_rechner_page() ) {
        return;
    }

    $css_candidates = array(
        get_stylesheet_directory() . '/css/werdu-calc.css',
        get_stylesheet_directory() . '/CSS/werdu-calc.css',
    );
    $js_candidates = array(
        get_stylesheet_directory() . '/JS/werdu-calc.js',
        get_stylesheet_directory() . '/js/werdu-calc.js',
    );

    foreach ( $css_candidates as $css_path ) {
        if ( file_exists( $css_path ) ) {
            wp_enqueue_style(
                'werdu-calc',
                str_replace( get_stylesheet_directory(), get_stylesheet_directory_uri(), $css_path ),
                array(),
                filemtime( $css_path )
            );
            break;
        }
    }

    foreach ( $js_candidates as $js_path ) {
        if ( file_exists( $js_path ) ) {
            wp_enqueue_script(
                'werdu-calc',
                str_replace( get_stylesheet_directory(), get_stylesheet_directory_uri(), $js_path ),
                array(),
                filemtime( $js_path ),
                true
            );
            break;
        }
    }
}
add_action( 'wp_enqueue_scripts', 'werdu_enqueue_calc_assets', 25 );

/**
 * Meta + JSON-LD for Calc 1 — strictly in wp_head
 */
function werdu_calc_wp_head_seo() {
    if ( ! werdu_is_solarbatterie_rechner_page() ) {
        return;
    }

    $url   = 'https://werdu.de/solarbatterie-rechner/';
    $title = 'Solarbatterie Rechner 2026 | PV-Speicher Größe kostenlos berechnen';
    $desc  = 'Kostenloser Solarbatterie-Rechner 2026: Berechnen Sie die optimale PV-Speicher-Größe für Ihr Zuhause. Mit regionaler Ertragsberechnung, Amortisations-Analyse & passenden LiFePO4-Produkten.';
    $image = 'https://werdu.de/wp-content/uploads/2026/04/cropped-logo-werdu_143_140-1.webp';

    echo '<link rel="canonical" href="' . esc_url( $url ) . '" />' . "\n";
    echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
    echo '<meta property="og:locale" content="de_DE" />' . "\n";
    echo '<meta property="og:type" content="website" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $desc ) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
    echo '<meta property="og:site_name" content="WERDU.de" />' . "\n";
    echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '" />' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
    echo '<meta name="author" content="WERDU.de" />' . "\n";

    $graph = array(
        '@context' => 'https://schema.org',
        '@graph'   => array(
            array(
                '@type'               => 'SoftwareApplication',
                'name'                => 'WERDU Solarbatterie-Rechner',
                'applicationCategory' => 'UtilityApplication',
                'operatingSystem'     => 'Web Browser',
                'offers'              => array(
                    '@type'         => 'Offer',
                    'price'         => '0',
                    'priceCurrency' => 'EUR',
                ),
                'description'         => $desc,
                'author'              => array( '@id' => 'https://werdu.de/#organization' ),
                'publisher'           => array( '@id' => 'https://werdu.de/#organization' ),
                'inLanguage'          => 'de-DE',
                'url'                 => $url,
            ),
            array(
                '@type'      => 'WebPage',
                '@id'        => $url . '#webpage',
                'url'        => $url,
                'name'       => $title,
                'description'=> $desc,
                'inLanguage' => 'de-DE',
                'isPartOf'   => array( '@id' => 'https://werdu.de/#website' ),
                'about'      => array( '@type' => 'Thing', 'name' => 'Solarbatterie Rechner' ),
                'speakable'  => array(
                    '@type'       => 'SpeakableSpecification',
                    'cssSelector' => array( '.werdu-header h1', '.werdu-header p' ),
                ),
            ),
            array(
                '@type'           => 'BreadcrumbList',
                '@id'             => $url . '#breadcrumb',
                'itemListElement' => array(
                    array(
                        '@type'    => 'ListItem',
                        'position' => 1,
                        'name'     => 'Startseite',
                        'item'     => 'https://werdu.de/',
                    ),
                    array(
                        '@type'    => 'ListItem',
                        'position' => 2,
                        'name'     => 'Solarbatterie-Rechner',
                        'item'     => $url,
                    ),
                ),
            ),
            array(
                '@type'       => 'HowTo',
                'name'        => 'Solarbatterie-Größe berechnen',
                'description' => 'Schritt-für-Schritt-Anleitung zur Berechnung der optimalen Solarbatterie-Größe für Ihre PV-Anlage.',
                'totalTime'   => 'PT5M',
                'step'        => array(
                    array(
                        '@type'    => 'HowToStep',
                        'position' => 1,
                        'name'     => 'Haushaltsdaten eingeben',
                        'text'     => 'Geben Sie Ihre Postleitzahl, Haushaltsgröße, jährlichen Stromverbrauch und aktuellen Strompreis ein.',
                    ),
                    array(
                        '@type'    => 'HowToStep',
                        'position' => 2,
                        'name'     => 'PV-Anlage konfigurieren',
                        'text'     => 'Tragen Sie die Leistung Ihrer PV-Anlage in kWp sowie Dachneigung, Ausrichtung und Verschattung ein.',
                    ),
                    array(
                        '@type'    => 'HowToStep',
                        'position' => 3,
                        'name'     => 'Autarkie-Ziel festlegen',
                        'text'     => 'Wählen Sie Ihr gewünschtes Autarkie-Level und die saisonale Optimierung.',
                    ),
                    array(
                        '@type'    => 'HowToStep',
                        'position' => 4,
                        'name'     => 'Ergebnisse analysieren',
                        'text'     => 'Sehen Sie empfohlene Speichergröße, Autarkie, Ersparnis und passende Produkte.',
                    ),
                ),
            ),
            array(
                '@type'      => 'FAQPage',
                'mainEntity' => array(
                    array(
                        '@type'          => 'Question',
                        'name'           => 'Wie berechne ich die richtige Solarbatterie-Größe?',
                        'acceptedAnswer' => array(
                            '@type' => 'Answer',
                            'text'  => 'Die optimale Speichergröße hängt von Verbrauch, PV-Leistung, Dachausrichtung und Autarkieziel ab. Der WERDU-Rechner berücksichtigt regionale Solarerträge anhand Ihrer PLZ.',
                        ),
                    ),
                    array(
                        '@type'          => 'Question',
                        'name'           => 'Was kostet eine Solarbatterie für ein Einfamilienhaus?',
                        'acceptedAnswer' => array(
                            '@type' => 'Answer',
                            'text'  => 'Für ein typisches Einfamilienhaus mit 3–4 Personen und 8–10 kWp liegen passende LiFePO4-Lösungen bei WERDU.de typischerweise zwischen 1.990 € und 2.899 €.',
                        ),
                    ),
                    array(
                        '@type'          => 'Question',
                        'name'           => 'Lohnt sich ein PV-Speicher im Jahr 2026?',
                        'acceptedAnswer' => array(
                            '@type' => 'Answer',
                            'text'  => 'Ja. Bei Strompreisen von 35–42 ct/kWh amortisieren sich moderne LiFePO4-Speicher oft nach 6–12 Jahren und halten 15–20 Jahre.',
                        ),
                    ),
                ),
            ),
        ),
    );

    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    echo "\n</script>\n";
}
add_action( 'wp_head', 'werdu_calc_wp_head_seo', 5 );

/* ============================================================
   P1.3 GLOBAL ORGANIZATION + Service (GEO) + Beratung schema
   ============================================================ */

function werdu_global_organization_schema() {
    if ( is_admin() ) {
        return;
    }

    $org = array(
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        '@id'      => 'https://werdu.de/#organization',
        'name'     => 'WERDU',
        'alternateName' => array( 'WERDU.de', 'Werdu' ),
        'url'      => 'https://werdu.de/',
        'logo'     => array(
            '@type'  => 'ImageObject',
            'url'    => 'https://werdu.de/wp-content/uploads/2026/04/cropped-logo-werdu_143_140-1.webp',
            'width'  => 143,
            'height' => 140,
        ),
        'email'    => 'service@werdu.de',
        'areaServed' => array(
            '@type' => 'Country',
            'name'  => 'Germany',
        ),
        'address'  => array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'c/o Impressumservice Dein-Impressum, Stettiner Str. 41',
            'addressLocality' => 'Hungen',
            'postalCode'      => '35410',
            'addressCountry'  => 'DE',
        ),
        'vatID'    => 'DE462239894',
        'contactPoint' => array(
            '@type'             => 'ContactPoint',
            'contactType'       => 'customer service',
            'email'             => 'service@werdu.de',
            'availableLanguage' => array( 'German', 'Dutch' ),
            'areaServed'        => 'DE',
        ),
    );

    // Avoid duplicating Organization on every page if Rank Math already emits one —
    // we still output a stable @id reference node used by other graphs.
    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode( $org, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    echo "\n</script>\n";
}
add_action( 'wp_head', 'werdu_global_organization_schema', 4 );

function werdu_beratung_schema() {
    if ( ! is_page( 'beratung-anfragen' ) ) {
        return;
    }

    $url = 'https://werdu.de/beratung-anfragen/';

    $graph = array(
        '@context' => 'https://schema.org',
        '@graph'   => array(
            array(
                '@type'       => 'Service',
                '@id'         => $url . '#service',
                'name'        => 'Kostenlose Heimspeicher-Fachanalyse',
                'serviceType' => 'Energieberatung Heimspeicher',
                'description' => 'Unverbindliche Fachanalyse für Heimspeicher und Solarbatterien inkl. PLZ-basierter Ertragsabschätzung und Produktempfehlung.',
                'provider'    => array( '@id' => 'https://werdu.de/#organization' ),
                'areaServed'  => array(
                    '@type' => 'Country',
                    'name'  => 'Germany',
                ),
                'url'         => $url,
                'offers'      => array(
                    '@type'         => 'Offer',
                    'price'         => '0',
                    'priceCurrency' => 'EUR',
                ),
            ),
            array(
                '@type'      => 'WebPage',
                '@id'        => $url . '#webpage',
                'url'        => $url,
                'name'       => 'Beratung anfragen | WERDU.de Heimspeicher',
                'description'=> 'Kostenlose Fachanalyse für Ihren Heimspeicher. Rechnerdaten werden automatisch übernommen.',
                'isPartOf'   => array( '@id' => 'https://werdu.de/#website' ),
                'about'      => array( '@id' => $url . '#service' ),
                'breadcrumb' => array( '@id' => $url . '#breadcrumb' ),
            ),
            array(
                '@type'           => 'BreadcrumbList',
                '@id'             => $url . '#breadcrumb',
                'itemListElement' => array(
                    array(
                        '@type'    => 'ListItem',
                        'position' => 1,
                        'name'     => 'Startseite',
                        'item'     => 'https://werdu.de/',
                    ),
                    array(
                        '@type'    => 'ListItem',
                        'position' => 2,
                        'name'     => 'Beratung anfragen',
                        'item'     => $url,
                    ),
                ),
            ),
            array(
                '@type'      => 'FAQPage',
                '@id'        => $url . '#faq',
                'mainEntity' => array(
                    array(
                        '@type'          => 'Question',
                        'name'           => 'Was kostet die Analyse?',
                        'acceptedAnswer' => array(
                            '@type' => 'Answer',
                            'text'  => 'Die erste Einschätzung und die Satelliten-Prüfung sind vollkommen unverbindlich und kostenfrei.',
                        ),
                    ),
                    array(
                        '@type'          => 'Question',
                        'name'           => 'Wie lange dauert die Auswertung?',
                        'acceptedAnswer' => array(
                            '@type' => 'Answer',
                            'text'  => 'Ein Experte prüft Ihre Daten. Sie erhalten das Ergebnis in der Regel innerhalb von 24–48 Stunden.',
                        ),
                    ),
                    array(
                        '@type'          => 'Question',
                        'name'           => 'Warum die Postleitzahl?',
                        'acceptedAnswer' => array(
                            '@type' => 'Answer',
                            'text'  => 'Nur so können lokale Sonnenstunden und Ertragsfaktoren exakt für Ihren Wohnort einkalkuliert werden.',
                        ),
                    ),
                    array(
                        '@type'          => 'Question',
                        'name'           => 'Sind die Systeme nachrüstbar?',
                        'acceptedAnswer' => array(
                            '@type' => 'Answer',
                            'text'  => 'Ja. Die LiFePO4-Systeme von WERDU.de sind modular aufgebaut und können später erweitert werden.',
                        ),
                    ),
                ),
            ),
        ),
    );

    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    echo "\n</script>\n";
}
add_action( 'wp_head', 'werdu_beratung_schema', 6 );

/**
 * Remove outdated Elementor JSON-LD that still points to /kostenlose-fachanalyse/
 * (canonical page is /beratung-anfragen/). Theme wp_head schema replaces it.
 */
function werdu_strip_stale_beratung_jsonld( $content ) {
    if ( ! is_string( $content ) || $content === '' ) {
        return $content;
    }
    if ( ! is_page( 'beratung-anfragen' ) ) {
        return $content;
    }
    $content = preg_replace(
        '/<script\b[^>]*type=(["\'])application\/ld\+json\1[^>]*>.*?kostenlose-fachanalyse.*?<\/script>/is',
        '',
        $content
    );
    return $content;
}
add_filter( 'the_content', 'werdu_strip_stale_beratung_jsonld', 5 );
add_filter( 'elementor/frontend/the_content', 'werdu_strip_stale_beratung_jsonld', 5 );

/* ============================================================
   P1.4 Prevent duplicate blocking Google Fonts on calc page
   ============================================================ */
add_action( 'wp_enqueue_scripts', function () {
    if ( ! werdu_is_solarbatterie_rechner_page() ) {
        return;
    }
    // Calc uses system-ui stack — dequeue common GF handles if present
    wp_dequeue_style( 'google-fonts' );
    wp_dequeue_style( 'shoppingcart-google-fonts' );
    wp_dequeue_style( 'shoppingcart-fonts' );
}, 100 );

