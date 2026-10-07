<?php
/**
 * Plugin Name: Werdu SEO Hardening
 * Description: Sitemap zonder 301-bronnen, blog-slug herstel, geen WC-session-cookies voor bots, nette titles/H1. Aanvulling op cache/gzip-fix.
 * Version: 1.0.0
 * Author: Werdu
 *
 * NA DEPLOY (eenmalig door beheerder):
 * 1) https://werdu.de/?purge_werdu_cache=1
 * 2) Op de server: wp-config.php opslaan als UTF-8 ZONDER BOM
 * 3) Google Search Console → URL-inspectie voor /, /shop/, /pv-speicher-rechner/
 * 4) Rank Math → Sitemap cache legen (Links per sitemap +1 opslaan)
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1) Rank Math sitemap: skip entries that are redirection sources (301 in sitemap).
 */
add_filter('rank_math/sitemap/entry', function ($url, $type, $object) {
    if (!is_array($url) || empty($url['loc'])) {
        return $url;
    }

    $path = wp_parse_url($url['loc'], PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        return $url;
    }
    $path = trailingslashit($path);

    // Hard fallback list (bekende oude slugs) + live Rank Math match.
    static $legacy = null;
    if (null === $legacy) {
        $legacy = array_flip([
            '/garantie/',
            '/heimspeicher-versand-lieferbedingungen/',
            '/batteriespeicher-kaufen-maximale-autarkie-ab-16-kwh/',
            '/kfw-foerderung-heimspeicher-2026-bis-zu-15-zuschuss-werdu-de/',
            '/heimspeicher-installation-plug-play-anleitung-2026-werdu-de/',
            '/solarbatterien/',
                            '/solarbatterie-kaufen-heimspeicher-vergleich-kaufberatung/',
            '/solaranlage-mit-speicher-2026-pv-batterie-komplettsysteme/',
            '/solarbatterie-kaufen-2/',
            '/eigenverbrauchsquote-erhoehen-7-strategien-2026-werdu/',
            '/lifepo4-batterien-technologie/',
            '/transparente-solarbatterie-preise/',
            '/energieunabhaengigkeit/',
            '/entladetiefe-tabelle/',
            '/intelligente-energiesteuerung-bms-app-technologie-2026-werdu-de/',
            '/gewerbe-industrie/1-phasig/',
            '/tewaycell-solarspeicher/15kwh/',
            '/tewaycell-all-in-one-51-2v-energiespeichersystem/',
            '/tewaycell-solarspeicher/10kwh/',
            '/gewerbe-industrie/3-phasig/',
            '/tewaycell-solarspeicher/30kwh/',
            '/grade-a-zellen/340-ah/',
            '/basen-green-heimspeicher/16kwh-basen-green-heimspeicher/',
            '/solarbatterie-rechner/',
            '/faq-heimspeicher/',
            '/gratis-heimspeicher-rechner-online/',
            '/blog-2/', // na rename mag blog-2 niet in sitemap als die alleen redirect is
            '/heimspeicher-kaufen-transparente-solarbatterie-preise/',
        ]);
    }

    if (isset($legacy[$path])) {
        return false;
    }

    if (class_exists('\RankMath\Redirections\DB') && method_exists('\RankMath\Redirections\DB', 'match_redirections')) {
        $uri = ltrim($path, '/');
        $hit = \RankMath\Redirections\DB::match_redirections($uri);
        if (!empty($hit)) {
            return false;
        }
    }

    return $url;
}, 20, 3);

/**
 * 2) Eenmalig: /blog-2/ → /blog/ (slug), Rank Math redirect omdraaien indien nodig.
 */
add_action('init', function () {
    if (get_option('werdu_blog_slug_fixed_v1') === '1') {
        return;
    }
    if (!function_exists('wp_update_post')) {
        return;
    }

    $posts = get_posts([
        'name'           => 'blog-2',
        'post_type'      => ['page', 'post'],
        'post_status'    => ['publish', 'private'],
        'posts_per_page' => 1,
        'fields'         => 'all',
    ]);

    if (empty($posts)) {
        update_option('werdu_blog_slug_fixed_v1', '1', false);
        return;
    }

    $blog = $posts[0];
    $conflict = get_posts([
        'name'           => 'blog',
        'post_type'      => $blog->post_type,
        'post_status'    => ['publish', 'private', 'draft'],
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ]);

    if (!empty($conflict) && (int) $conflict[0] !== (int) $blog->ID) {
        // Conflict: niet forceren; handmatig in WP-admin.
        return;
    }

    $result = wp_update_post([
        'ID'        => $blog->ID,
        'post_name' => 'blog',
    ], true);

    if (is_wp_error($result)) {
        return;
    }

    // Rank Math: verwijder redirect van /blog/ → elders; zorg dat /blog-2/ → /blog/ bestaat.
    if (class_exists('\RankMath\Redirections\DB')) {
        try {
            $from_blog = \RankMath\Redirections\DB::match_redirections('blog');
            if (!empty($from_blog['id'])) {
                \RankMath\Redirections\DB::delete($from_blog['id']);
            }
        } catch (\Throwable $e) {
            // stil: redirect-opschoning is best-effort
        }
    }

    update_option('werdu_blog_slug_fixed_v1', '1', false);

    if (function_exists('flush_rewrite_rules')) {
        flush_rewrite_rules(false);
    }
}, 30);

/**
 * 3) Geen WooCommerce-session-cookie voor crawlers (publieke GET) → betere cache/indexatie.
 */
add_action('init', function () {
    if (is_admin() || (defined('WP_CLI') && WP_CLI)) {
        return;
    }
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if ($ua === '') {
        return;
    }
    if (!preg_match('/Googlebot|Google-InspectionTool|bingbot|Applebot|YandexBot|DuckDuckBot|Baiduspider|Slurp|facebookexternalhit|Bytespider|GPTBot|ChatGPT|Perplexity|ClaudeBot/i', $ua)) {
        return;
    }
    add_filter('woocommerce_set_cookie_enabled', '__return_false', 99);
}, 0);

/**
 * 4) Titles: clickbait / gebroken SEO-titles vervangen (Rank Math + fallback).
 */
function werdu_seo_title_map() {
    return [
        'faq-pv-speicher' => 'FAQ PV-Speicher 2026: Heimspeicher & Solarbatterie | WERDU',
        'heimspeicher-kosten-pro-kwh' => 'Heimspeicher Kosten pro kWh 2026 | Transparante Preise | WERDU',
        '30-32-kwh-lifepo4-heimspeicher-560-628ah' => '30–32 kWh LiFePO4 Heimspeicher | Preis & Specs | WERDU',
        'blog' => 'Heimspeicher Ratgeber & Blog 2026 | WERDU',
        'blog-2' => 'Heimspeicher Ratgeber & Blog 2026 | WERDU',
    ];
}

add_filter('rank_math/frontend/title', function ($title) {
    if (!is_singular()) {
        return $title;
    }
    $post = get_queried_object();
    if (!$post || empty($post->post_name)) {
        return $title;
    }
    $map = werdu_seo_title_map();
    if (isset($map[$post->post_name])) {
        return $map[$post->post_name];
    }
    return $title;
}, 20);

add_filter('pre_get_document_title', function ($title) {
    if (!is_singular()) {
        return $title;
    }
    $post = get_queried_object();
    if (!$post || empty($post->post_name)) {
        return $title;
    }
    $map = werdu_seo_title_map();
    return isset($map[$post->post_name]) ? $map[$post->post_name] : $title;
}, 20);

/**
 * 5) H1 / post_title op bekende problematische singuliere pagina's (the_title in de loop).
 */
add_filter('the_title', function ($title, $post_id) {
    if (is_admin() || !is_singular() || !in_the_loop() || !is_main_query()) {
        return $title;
    }
    if ((int) $post_id !== (int) get_queried_object_id()) {
        return $title;
    }
    $post = get_post($post_id);
    if (!$post) {
        return $title;
    }
    $map = [
        'faq-pv-speicher' => 'FAQ PV-Speicher: die wichtigsten Antworten',
        'heimspeicher-kosten-pro-kwh' => 'Heimspeicher Kosten pro kWh 2026',
        '30-32-kwh-lifepo4-heimspeicher-560-628ah' => '30–32 kWh LiFePO4 Heimspeicher',
        'blog' => 'Heimspeicher Ratgeber',
        'blog-2' => 'Heimspeicher Ratgeber',
        'solarbatterie-kaufen' => 'Solarbatterie kaufen: Vergleich und Kaufberatung',
    ];
    return isset($map[$post->post_name]) ? $map[$post->post_name] : $title;
}, 20, 2);

/**
 * 6) Admin-bar reminder tot BOM/server-check gedaan is (alleen admins).
 */
add_action('admin_notices', function () {
    if (!current_user_can('manage_options')) {
        return;
    }
    if (get_option('werdu_bom_server_ack') === '1') {
        return;
    }
    echo '<div class="notice notice-warning"><p><strong>WERDU:</strong> Controleer op de server of <code>wp-config.php</code> als UTF-8 <em>zonder BOM</em> is opgeslagen. Daarna in wp-admin: <code>update_option(\'werdu_bom_server_ack\', \'1\');</code> via WP-CLI of zet optie <code>werdu_bom_server_ack=1</code>. Na deploy: <a href="' . esc_url(home_url('/?purge_werdu_cache=1')) . '">cache legen</a> + GSC URL-inspectie.</p></div>';
});
