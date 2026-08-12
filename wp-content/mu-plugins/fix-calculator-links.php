<?php
/**
 * Plugin Name: WERDU Fix Calculator /kontakt/ Links
 * Description: Tijdelijk vangnet dat elke /kontakt/-referentie (in gerenderde HTML én in opgeslagen Elementor-data) omzet naar home_url('/beratung-anfragen/'). Verwijderen zodra de bron (Elementor-paginacontent) permanent is opgeschoond.
 * Version: 1.0
 * Author: Michael van der Veen
 * Network: false
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ============================================
// 0. HELPERS
// ============================================

/**
 * Doelwaarde voor elke /kontakt/-referentie: altijd de huidige omgeving
 * (test of productie) via home_url(), nooit een hardgecodeerde host.
 */
function werdu_fix_kontakt_beratung_url() {
    static $url = null;
    if ( null === $url ) {
        $url = home_url( '/beratung-anfragen/' );
    }
    return $url;
}

/**
 * Vervangt elke bekende /kontakt/-schrijfwijze in platte tekst (HTML of een
 * losse string) door de Beratung-URL. Wordt zowel door de output-filters
 * (stap 1) als door de Elementor-JSON-cleaner (stap 2) hergebruikt.
 *
 * Volgorde is belangrijk: langere/specifiekere patronen (met trailing slash,
 * met omliggende quotes) staan vóór de kortere generieke varianten, zodat er
 * geen dubbele slash of half vervangen string overblijft.
 */
function werdu_fix_kontakt_apply_replacements( $text, $beratung_url ) {
    if ( ! is_string( $text ) || '' === $text || false === strpos( $text, 'kontakt' ) ) {
        return $text;
    }

    $replacements = array(
        'https://werdu.de/kontakt/'     => $beratung_url,
        'http://werdu.de/kontakt/'      => $beratung_url,
        'https://www.werdu.de/kontakt/' => $beratung_url,
        'https://werdu.de/kontakt'      => $beratung_url,
        'http://werdu.de/kontakt'       => $beratung_url,
        'href="/kontakt/"'               => 'href="' . $beratung_url . '"',
        "href='/kontakt/'"               => "href='" . $beratung_url . "'",
        'action="/kontakt/"'             => 'action="' . $beratung_url . '"',
        "action='/kontakt/'"             => "action='" . $beratung_url . "'",
        "'/kontakt/'"                    => "'" . $beratung_url . "'",
        '"/kontakt/"'                    => '"' . $beratung_url . '"',
        // Bredere prefix-varianten (zonder verplichte sluit-quote direct erna)
        // vangen ook dynamische JS-concatenatie op, bv.
        // elCta.href = '/kontakt/?kwh=' + kwh + '&plz=' + plz;
        "'/kontakt/"                     => "'" . $beratung_url,
        '"/kontakt/'                     => '"' . $beratung_url,
    );

    return str_replace( array_keys( $replacements ), array_values( $replacements ), $text );
}

// ============================================
// 1. OUTPUT-FILTER — direct resultaat op de pagina
// ============================================

function werdu_fix_kontakt_filter_output( $content ) {
    return werdu_fix_kontakt_apply_replacements( $content, werdu_fix_kontakt_beratung_url() );
}
add_filter( 'the_content', 'werdu_fix_kontakt_filter_output', 20 );
add_filter( 'elementor/frontend/the_content', 'werdu_fix_kontakt_filter_output', 20 );

// ============================================
// 2. DATABASE-META FIX — eenmalige Elementor JSON-cleaner
// ============================================

/**
 * Loopt recursief door een gedecodeerde _elementor_data-structuur en past
 * werdu_fix_kontakt_apply_replacements() toe op elke stringwaarde. Werken op
 * de gedecodeerde array (i.p.v. ruwe JSON-tekst met escaped slashes) voorkomt
 * dat de Elementor-paginastructuur corrupt raakt.
 */
function werdu_fix_kontakt_clean_elementor_json( $raw_json, $beratung_url ) {
    $data = json_decode( $raw_json, true );

    if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $data ) ) {
        // Geen (geldige) JSON — val terug op een platte tekstvervanging zodat
        // niet-JSON-geëscapete voorkomens alsnog worden opgevangen.
        return werdu_fix_kontakt_apply_replacements( $raw_json, $beratung_url );
    }

    array_walk_recursive(
        $data,
        function ( &$value ) use ( $beratung_url ) {
            if ( is_string( $value ) && false !== strpos( $value, 'kontakt' ) ) {
                $value = werdu_fix_kontakt_apply_replacements( $value, $beratung_url );
            }
        }
    );

    $encoded = wp_json_encode( $data );
    return ( false === $encoded ) ? $raw_json : $encoded;
}

function werdu_fix_kontakt_clear_elementor_cache( $post_id ) {
    delete_post_meta( $post_id, '_elementor_css' );
    if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
        try {
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        } catch ( \Throwable $e ) {
            // Best-effort: een mislukte cache-clear mag de rest niet blokkeren.
        }
    }
}

function werdu_fix_kontakt_run_db_cleanup() {
    global $wpdb;

    $beratung_url = werdu_fix_kontakt_beratung_url();

    // ---- wp_posts.post_content ------------------------------------------
    $posts = $wpdb->get_results(
        "SELECT ID, post_content FROM {$wpdb->posts}
         WHERE post_type NOT IN ('revision')
           AND post_status != 'trash'
           AND post_content LIKE '%kontakt%'"
    );

    foreach ( $posts as $post ) {
        $updated = werdu_fix_kontakt_apply_replacements( $post->post_content, $beratung_url );
        if ( $updated !== $post->post_content ) {
            $wpdb->update(
                $wpdb->posts,
                array( 'post_content' => $updated ),
                array( 'ID' => $post->ID )
            );
            clean_post_cache( $post->ID );
        }
    }

    // ---- wp_postmeta._elementor_data -------------------------------------
    $metas = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT meta_id, post_id, meta_value FROM {$wpdb->postmeta}
             WHERE meta_key = %s AND meta_value LIKE %s",
            '_elementor_data',
            '%kontakt%'
        )
    );

    foreach ( $metas as $meta ) {
        $updated = werdu_fix_kontakt_clean_elementor_json( $meta->meta_value, $beratung_url );
        if ( $updated !== $meta->meta_value ) {
            $wpdb->update(
                $wpdb->postmeta,
                array( 'meta_value' => $updated ),
                array( 'meta_id' => $meta->meta_id )
            );
            clean_post_cache( $meta->post_id );
            werdu_fix_kontakt_clear_elementor_cache( $meta->post_id );
        }
    }
}

/**
 * Voert werdu_fix_kontakt_run_db_cleanup() precies één keer uit (bewaakt via
 * een option), zodat dit niet op elke pageview opnieuw over wp_posts en
 * wp_postmeta query't. Verhoog WERDU_FIX_KONTAKT_DB_VERSION om de cleanup
 * bewust nogmaals te laten draaien (bv. na nieuwe Elementor-content).
 */
define( 'WERDU_FIX_KONTAKT_DB_VERSION', '1' );

function werdu_fix_kontakt_maybe_run_db_cleanup() {
    if ( get_option( 'werdu_fix_kontakt_db_cleanup_version' ) === WERDU_FIX_KONTAKT_DB_VERSION ) {
        return;
    }
    if ( get_transient( 'werdu_fix_kontakt_db_cleanup_lock' ) ) {
        return;
    }
    set_transient( 'werdu_fix_kontakt_db_cleanup_lock', 1, 60 );

    werdu_fix_kontakt_run_db_cleanup();

    update_option( 'werdu_fix_kontakt_db_cleanup_version', WERDU_FIX_KONTAKT_DB_VERSION, false );
    delete_transient( 'werdu_fix_kontakt_db_cleanup_lock' );
}
add_action( 'init', 'werdu_fix_kontakt_maybe_run_db_cleanup' );
