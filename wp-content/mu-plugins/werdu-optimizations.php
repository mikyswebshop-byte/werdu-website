<?php
/**
 * Plugin Name: WERDU Performance & Optimizations v3.2
 * Description: WooCommerce-Overhead-Reduzierung, Datenbank-Optimierung, Bloat-Entfernung
 * Version: 3.2
 * Author: WERDU
 */

if (!defined('ABSPATH')) exit;

/* 1. HEARTBEAT API */
add_action('init', function() {
    if (!is_admin()) {
        wp_deregister_script('heartbeat');
        return;
    }
    wp_enqueue_script('heartbeat');
    wp_localize_script('heartbeat', 'heartbeatSettings', array('interval' => 120));
}, 1);

/* 2. EMOJIS */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
add_filter('emoji_svg_url', '__return_false');

/* 3. EMBEDS */
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');
remove_action('rest_api_init', 'wp_oembed_register_route');
add_filter('embed_oembed_discover', '__return_false');
remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
add_action('wp_footer', function() {
    wp_dequeue_script('wp-embed');
}, 99);

/* 4. HEADER LINKS */
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('template_redirect', 'rest_output_link_header', 11);
add_filter('the_generator', '__return_empty_string');

/* 5. DASHICONS */
add_action('wp_enqueue_scripts', function() {
    if (!is_user_logged_in()) {
        wp_dequeue_style('dashicons');
    }
}, 100);

/* 6. PREFETCH / PRECONNECT — GEEN Google Fonts */
add_action('wp_head', function() {
    echo '<link rel="preconnect" href="https://api.silentshield.io" crossorigin>' . "
";
    echo '<link rel="dns-prefetch" href="https://www.google-analytics.com">' . "
";
    echo '<link rel="preconnect" href="https://werdu.de" crossorigin>' . "
";
}, 1);

/* 7. QUERY STRINGS */
add_filter('script_loader_src', function($src) {
    return remove_query_arg('ver', $src);
}, 15, 1);

add_filter('style_loader_src', function($src) {
    return remove_query_arg('ver', $src);
}, 15, 1);

/* 8. XML-RPC & PINGBACKS */
add_filter('xmlrpc_enabled', '__return_false');
add_filter('xmlrpc_methods', function($methods) {
    unset($methods['pingback.ping']);
    unset($methods['pingback.extensions.getPingbacks']);
    return $methods;
});
add_action('pre_ping', function(&$links) {
    $home = get_option('home');
    foreach ($links as $l => $link) {
        if (strpos($link, $home) === 0) {
            unset($links[$l]);
        }
    }
});

/* 9. FEED LINKS */
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);

/* 10. ATTACHMENT REDIRECT */
add_action('template_redirect', function() {
    if (is_attachment()) {
        global $post;
        if ($post && $post->post_parent) {
            wp_redirect(get_permalink($post->post_parent), 301);
            exit;
        }
    }
});

/* 11. POST REVISIONS */
if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 3);
}

/* 12. AUTOSAVE INTERVAL */
if (!defined('AUTOSAVE_INTERVAL')) {
    define('AUTOSAVE_INTERVAL', 300);
}

/* 13. TRASH LEERUNG */
if (!defined('EMPTY_TRASH_DAYS')) {
    define('EMPTY_TRASH_DAYS', 7);
}

/* 14. TRANSIENTS BEREINIGEN */
add_action('wp', function() {
    $last_clean = get_transient('werdu_transient_cleaned');
    if (false === $last_clean) {
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_%' AND option_value < " . time());
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%' AND option_name NOT LIKE '_transient_timeout_%' AND option_name NOT IN (SELECT REPLACE(option_name, '_transient_timeout_', '_transient_') FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_%')");
        set_transient('werdu_transient_cleaned', time(), HOUR_IN_SECONDS);
    }
});

/* 15. WOOCOMMERCE SESSIONS */
add_action('wp', function() {
    $last_wc_clean = get_transient('werdu_wc_cleaned');
    if (false === $last_wc_clean && function_exists('WC')) {
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->prefix}woocommerce_sessions WHERE session_expiry < " . (time() - 172800));
        set_transient('werdu_wc_cleaned', time(), HOUR_IN_SECONDS);
    }
});

/* 16. WOOCOMMERCE CART FRAGMENTS (non-shop) */
add_action('wp_enqueue_scripts', function() {
    if (function_exists('is_woocommerce') && !is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page()) {
        wp_dequeue_script('wc-cart-fragments');
    }
}, 99);

/* 17. WOOCOMMERCE STYLES/SCRIPTS (non-shop) */
add_action('wp_enqueue_scripts', function() {
    if (function_exists('is_woocommerce') && !is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page()) {
        wp_dequeue_style('woocommerce-general');
        wp_dequeue_style('woocommerce-layout');
        wp_dequeue_style('woocommerce-smallscreen');
        wp_dequeue_style('woocommerce_frontend_styles');
        wp_dequeue_style('wc-block-style');
        wp_dequeue_style('wc-blocks-style');
        wp_dequeue_script('wc-add-to-cart');
        wp_dequeue_script('woocommerce');
        wp_dequeue_script('wc-single-product');
        wp_dequeue_script('wc-checkout');
        wp_dequeue_script('wc-cart');
        wp_dequeue_script('wc-price-slider');
        wp_dequeue_script('wc-cart-fragments');
        wp_dequeue_script('wc-add-to-cart-variation');
        wp_dequeue_script('wc-country-select');
        wp_dequeue_script('wc-address-i18n');
        wp_dequeue_script('wc-blocks-checkout');
        wp_dequeue_script('wc-blocks-registry');
        wp_dequeue_script('wc-settings');
        wp_dequeue_script('wc-shared-settings');
    }
}, 99);

/* 18. WOOCOMMERCE WIDGETS (non-shop) */
add_action('widgets_init', function() {
    if (function_exists('is_woocommerce') && !is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page()) {
        unregister_widget('WC_Widget_Products');
        unregister_widget('WC_Widget_Cart');
        unregister_widget('WC_Widget_Layered_Nav');
        unregister_widget('WC_Widget_Layered_Nav_Filters');
        unregister_widget('WC_Widget_Price_Filter');
        unregister_widget('WC_Widget_Product_Categories');
        unregister_widget('WC_Widget_Product_Search');
        unregister_widget('WC_Widget_Product_Tag_Cloud');
        unregister_widget('WC_Widget_Recent_Reviews');
        unregister_widget('WC_Widget_Top_Rated_Products');
        unregister_widget('WC_Widget_Recently_Viewed');
    }
}, 20);

/* 19. WOOCOMMERCE SESSION START (non-shop) */
add_action('init', function() {
    if (function_exists('is_woocommerce') && !is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page()) {
        if (has_filter('woocommerce_start_session')) {
            remove_filter('woocommerce_start_session', 'woocommerce_start_session', 10);
        }
    }
}, 1);

/* 20. WOOCOMMERCE GEOLOCATION */
add_filter('woocommerce_default_customer_address', function() {
    return 'base';
});

/* 21. WOOCOMMERCE MARKETING & REVIEWS */
remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
add_filter('woocommerce_product_tabs', function($tabs) {
    unset($tabs['reviews']);
    return $tabs;
});

/* 22. ADMIN BAR (nicht-Admin) */
add_action('after_setup_theme', function() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
});

/* 23. RANKMATH SCHEMA CACHE */
add_filter('rank_math/json_ld', function($data, $jsonld) {
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    $cached = $data;
    return $data;
}, 999, 2);

/* 24. ELEMENTOR ASSETS (nicht-Elementor Posts) */
add_action('wp_enqueue_scripts', function() {
    if (is_singular('post') && !get_post_meta(get_the_ID(), '_elementor_edit_mode', true)) {
        wp_dequeue_style('elementor-frontend');
        wp_dequeue_style('elementor-post-' . get_the_ID());
    }
}, 99);