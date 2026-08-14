<?php
/**
 * Theme Name: Shoppingcart Child
 * Template: shoppingcart
 * Version: 7.7.5
 * Author: Werdu.de
 */

/* ============================================================
   P1 SEO / SECURITY / CALC ASSETS
   ============================================================ */
$werdu_p1 = get_stylesheet_directory() . '/inc/werdu-p1-seo-security.php';
if ( file_exists( $werdu_p1 ) ) {
    require_once $werdu_p1;
}

$werdu_installer = get_stylesheet_directory() . '/werdu-installer-option.php';
if ( file_exists( $werdu_installer ) ) {
    require_once $werdu_installer;
}

$werdu_partner = get_stylesheet_directory() . '/inc/werdu-partner.php';
if ( file_exists( $werdu_partner ) ) {
    require_once $werdu_partner;
}

/* ============================================================
   0. STYLES ENQUEUE (Child + Parent)
   ============================================================ */
if ( ! function_exists( 'werdu_child_styles' ) ) {
    function werdu_child_styles() {
        wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array(), '1.2.7' );
        wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( 'parent-style' ), '7.7.5' );
    }
    add_action( 'wp_enqueue_scripts', 'werdu_child_styles', 10 );
}

/* ============================================================
   1. VERTALING: Delivery time -> Lieferzeit
   ============================================================ */
add_filter( 'gettext', function( $translated_text, $text, $domain ) {
    if ( $text === 'Delivery time:' || $text === 'Delivery time' ) {
        return 'Lieferzeit';
    }
    return $translated_text;
}, 10, 3 );

/* ============================================================
   2. FIX: IE-conditional script overschrijven (defer)
   ============================================================ */
add_action( 'wp_enqueue_scripts', function () {
    wp_dequeue_script( 'shoppingcart-script' );
    wp_enqueue_script(
        'shoppingcart-script',
        get_template_directory_uri() . '/assets/js/shoppingcart.js',
        array( 'jquery' ),
        null,
        true
    );
}, 25 );

/* ============================================================
   3. RANK MATH CANONICAL FIX
   ============================================================ */
add_filter( 'rank_math/frontend/canonical', function( $canonical ) {
    if ( is_product() ) {
        $product = wc_get_product( get_the_ID() );
        if ( $product ) {
            $permalink = get_permalink( $product->get_id() );
            $canonical = str_replace( '/shop/', '/', $permalink );
            return $canonical;
        }
    }
    if ( is_product_category() ) {
        $term = get_queried_object();
        if ( $term && ! is_wp_error( $term ) ) {
            return get_term_link( $term );
        }
    }
    return $canonical;
}, 20 );

/* ============================================================
   3b. HOMEPAGE SEO — never emit noindex on the front page
   ============================================================ */
add_filter( 'wp_robots', 'werdu_homepage_allow_indexing', 999 );
function werdu_homepage_allow_indexing( $robots ) {
    if ( is_admin() || ! is_front_page() ) {
        return $robots;
    }
    $robots['index']  = true;
    $robots['follow'] = true;
    unset( $robots['noindex'], $robots['nofollow'] );
    return $robots;
}

add_filter( 'rank_math/frontend/robots', 'werdu_homepage_rankmath_robots', 999 );
function werdu_homepage_rankmath_robots( $robots ) {
    if ( ! is_front_page() ) {
        return $robots;
    }
    if ( ! is_array( $robots ) ) {
        $robots = array();
    }
    $robots['index']  = 'index';
    $robots['follow'] = 'follow';
    unset( $robots['nofollow'], $robots['noindex'] );
    return $robots;
}

/* ============================================================
   4. 301 REDIRECT: Oude /shop/ product URLs
   ============================================================ */
add_action( 'template_redirect', function() {
    if ( ! is_404() ) return;
    $request_uri = $_SERVER['REQUEST_URI'];
    if ( strpos( $request_uri, '/shop/' ) === 0 ) {
        $new_url = str_replace( '/shop/', '/', $request_uri );
        wp_redirect( home_url( $new_url ), 301 );
        exit;
    }
}, 1 );

/* ============================================================
   5. WERDU COMPACT CSS
   ============================================================ */
function werdu_compact_css() {
    $css_path = get_stylesheet_directory() . '/werdu-compact.css';
    if ( file_exists( $css_path ) ) {
        wp_enqueue_style(
            'werdu-compact',
            get_stylesheet_directory_uri() . '/werdu-compact.css',
            array(),
            '1.0.0'
        );
    }
}
add_action( 'wp_enqueue_scripts', 'werdu_compact_css' );

/* ============================================================
   6. WHATSAPP + CHATBOT INCLUDES
   ============================================================ */
$wa_file = get_stylesheet_directory() . '/werdu-whatsapp.php';
$chat_file = get_stylesheet_directory() . '/werdu-chatbot.php';
if ( file_exists( $wa_file ) ) {
    require_once $wa_file;
}
if ( file_exists( $chat_file ) ) {
    require_once $chat_file;
}

/* ============================================================
   7. FORMALE E-MAIL ANREDE (WooCommerce)
   ============================================================ */
add_filter( 'woocommerce_email_subject', 'werdu_formalize_email_subject', 10, 3 );
add_filter( 'woocommerce_email_heading', 'werdu_formalize_email_heading', 10, 3 );
add_filter( 'woocommerce_email_content', 'werdu_formalize_email_content', 10, 2 );

function werdu_formalize_email_subject( $subject, $order, $email ) {
    $replacements = array(
        'deine'  => 'Ihre', 'Deine'  => 'Ihre',
        'dein'   => 'Ihr',  'Dein'   => 'Ihr',
        'deiner' => 'Ihrer','Deiner' => 'Ihrer',
        'deines' => 'Ihres','Deines' => 'Ihres',
        'deinem' => 'Ihrem','Deinem' => 'Ihrem',
        'deinen' => 'Ihren','Deinen' => 'Ihren',
    );
    return strtr( $subject, $replacements );
}

function werdu_formalize_email_heading( $heading, $order, $email ) {
    $replacements = array(
        'deine'  => 'Ihre', 'Deine'  => 'Ihre',
        'dein'   => 'Ihr',  'Dein'   => 'Ihr',
        'deiner' => 'Ihrer','Deiner' => 'Ihrer',
        'deines' => 'Ihres','Deines' => 'Ihres',
        'deinem' => 'Ihrem','Deinem' => 'Ihrem',
        'deinen' => 'Ihren','Deinen' => 'Ihren',
    );
    return strtr( $heading, $replacements );
}

function werdu_formalize_email_content( $content, $email ) {
    $replacements = array(
        'deine Bestellung'     => 'Ihre Bestellung', 'Deine Bestellung'     => 'Ihre Bestellung',
        'deiner Bestellung'    => 'Ihrer Bestellung','Deiner Bestellung'    => 'Ihrer Bestellung',
        'deines Bestellung'    => 'Ihres Bestellung','Deines Bestellung'    => 'Ihres Bestellung',
        'deinem Bestellung'    => 'Ihrem Bestellung','Deinem Bestellung'    => 'Ihrem Bestellung',
        'deinen Bestellung'    => 'Ihren Bestellung','Deinen Bestellung'    => 'Ihren Bestellung',
        'dein Konto'           => 'Ihr Konto',       'Dein Konto'           => 'Ihr Konto',
        'dein Widerruf'        => 'Ihr Widerruf',    'Dein Widerruf'        => 'Ihr Widerruf',
        'dein Ruecksendeantrag' => 'Ihr Ruecksendeantrag', 'Dein Ruecksendeantrag' => 'Ihr Ruecksendeantrag',
        'deine Ruecksendung'    => 'Ihre Ruecksendung',    'Deine Ruecksendung'    => 'Ihre Ruecksendung',
        'deine Zahlung'        => 'Ihre Zahlung',    'Deine Zahlung'        => 'Ihre Zahlung',
        'dein Passwort'        => 'Ihr Passwort',    'Dein Passwort'        => 'Ihr Passwort',
        'deine Anfrage'        => 'Ihre Anfrage',    'Deine Anfrage'        => 'Ihre Anfrage',
        'dein Beleg'           => 'Ihr Beleg',       'Dein Beleg'           => 'Ihr Beleg',
        'deine Sendung'        => 'Ihre Sendung',    'Deine Sendung'        => 'Ihre Sendung',
        'deine Retoure'        => 'Ihre Retoure',    'Deine Retoure'        => 'Ihre Retoure',
        'dein Widerrufsantrag' => 'Ihr Widerrufsantrag', 'Dein Widerrufsantrag' => 'Ihr Widerrufsantrag',
        'kannst du'            => 'koennen Sie',     'Kannst du'            => 'Koennen Sie',
        'du der'               => 'Sie der',         'du die'               => 'Sie die',
        'du das'               => 'Sie das',         'du kannst'            => 'Sie koennen',
        'Du kannst'            => 'Sie koennen',     'hast du'              => 'haben Sie',
        'Hast du'              => 'Haben Sie',       'wirst du'             => 'werden Sie',
        'Wirst du'             => 'Werden Sie',      'solltest du'          => 'sollten Sie',
        'Solltest du'          => 'Sollten Sie',     'musst du'             => 'muessen Sie',
        'Musst du'             => 'Muessen Sie',     'dein'                 => 'Ihr',
        'Dein'                 => 'Ihr',             'deine'                => 'Ihre',
        'Deine'                => 'Ihre',            'deiner'               => 'Ihrer',
        'Deiner'               => 'Ihrer',           'deines'               => 'Ihres',
        'Deines'               => 'Ihres',           'deinem'               => 'Ihrem',
        'Deinem'               => 'Ihrem',           'deinen'               => 'Ihren',
        'Deinen'               => 'Ihren',
    );
    return strtr( $content, $replacements );
}

/* ============================================================
   8. BACK TO TOP FIX v4
   ============================================================ */
add_action( 'wp_footer', 'werdu_fix_back_to_top', 5 );
function werdu_fix_back_to_top() {
    if ( is_admin() || wp_doing_ajax() ) return;
    ?>
<style>
.go-to-top:not(#werdu-btt),
#scroll-top,
.back-to-top,
.scroll-top,
.topbutton,
.scrollup,
.to-top,
[class*="scroll-top"]:not(#werdu-btt),
[class*="back-to-top"]:not(#werdu-btt),
[class*="topbutton"]:not(#werdu-btt) {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
    overflow: hidden !important;
    position: absolute !important;
    left: -9999px !important;
    pointer-events: none !important;
}
#werdu-btt {
    position: fixed !important;
    bottom: 20px !important;
    right: 20px !important;
    z-index: 10000 !important;
    background-color: transparent !important;
    border: none !important;
    cursor: pointer !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}
#werdu-btt.werdu-btt-visible {
    opacity: 1;
    visibility: visible;
}
#werdu-btt .icon-bg {
    background-color: #ff6600 !important;
    box-shadow: 0 2px 3px 0 rgba(0, 0, 0, 0.08) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    height: 40px !important;
    width: 40px !important;
    position: relative !important;
    transition: all 0.3s ease-out 0s !important;
}
#werdu-btt .icon-bg svg {
    width: 20px !important;
    height: 20px !important;
    fill: #fff !important;
    display: block !important;
}
@media (max-width: 768px) {
    #werdu-btt { bottom: 15px !important; right: 15px !important; }
    #werdu-btt .icon-bg { height: 36px !important; width: 36px !important; }
    #werdu-btt .icon-bg svg { width: 18px !important; height: 18px !important; }
}
@media (max-width: 480px) {
    #werdu-btt { bottom: 10px !important; right: 10px !important; }
    #werdu-btt .icon-bg { height: 32px !important; width: 32px !important; }
    #werdu-btt .icon-bg svg { width: 16px !important; height: 16px !important; }
}
</style>
<script data-noptimize="1">
(function(){
    var selectors = [
        '.go-to-top:not(#werdu-btt)',
        '#scroll-top',
        '.back-to-top',
        '.scroll-top',
        '.topbutton',
        '.scrollup',
        '.to-top'
    ];
    selectors.forEach(function(sel) {
        var els = document.querySelectorAll(sel);
        els.forEach(function(el) {
            el.style.display = 'none';
            el.style.visibility = 'hidden';
            el.style.opacity = '0';
            el.style.width = '0';
            el.style.height = '0';
            el.style.overflow = 'hidden';
            el.style.position = 'absolute';
            el.style.left = '-9999px';
            el.style.pointerEvents = 'none';
        });
    });
    var btt = document.createElement('button');
    btt.id = 'werdu-btt';
    btt.className = 'go-to-top';
    btt.setAttribute('aria-label', 'Zurueck zum Seitenanfang');
    btt.setAttribute('title', 'Zurueck zum Seitenanfang');
    btt.innerHTML = '<span class="icon-bg"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 4l-8 8h5v8h6v-8h5z"/></svg></span>';
    document.body.appendChild(btt);
    btt.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    var scrollThreshold = 300;
    function toggleBtt() {
        if (window.pageYOffset > scrollThreshold) {
            btt.classList.add('werdu-btt-visible');
        } else {
            btt.classList.remove('werdu-btt-visible');
        }
    }
    window.addEventListener('scroll', toggleBtt, { passive: true });
    setTimeout(toggleBtt, 100);
})();
</script>
    <?php
}

/* ============================================================
   9. SPEED OPTIMIZATION
   ============================================================ */

// 9.1 jQuery Migrate uitschakelen
add_action( 'wp_default_scripts', function( $scripts ) {
    if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
        $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps,
            array( 'jquery-migrate' )
        );
    }
} );

// 9.2 Dashicons uitschakelen voor niet-ingelogde bezoekers
add_action( 'wp_enqueue_scripts', function() {
    if ( ! is_user_logged_in() ) {
        wp_dequeue_style( 'dashicons' );
    }
}, 999 );

// 9.3 Emoji's uitschakelen
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

// 9.4 WordPress versienummer verwijderen
remove_action( 'wp_head', 'wp_generator' );

/* ============================================================
   10. CLS FIX — font-display: swap
   ============================================================ */
add_action( 'wp_head', function() {
    if ( is_admin() ) return;
    echo '<style>@font-face{font-display:swap!important}</style>' . "
";
}, 2 );

/* ============================================================
   11. AI BOT MANAGEMENT
   ============================================================ */
add_action( 'send_headers', 'werdu_ai_bot_headers' );
function werdu_ai_bot_headers() {
    if ( ! is_admin() ) {
        header( 'X-Robots-Tag: index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' );
    }
}

/* ============================================================
   12. WooCommerce scripts ALLEEN op shop pagina's
   ============================================================ */
add_action( 'wp_enqueue_scripts', function() {
    if ( is_admin() ) return;
    $is_woo_page = is_woocommerce() || is_cart() || is_checkout() || is_account_page() || is_shop() || is_product_category() || is_product_tag();
    if ( ! $is_woo_page ) {
        wp_dequeue_script( 'wc-cart-fragments' );
        wp_dequeue_script( 'wc-add-to-cart' );
        wp_dequeue_script( 'woocommerce' );
        wp_dequeue_script( 'wc-single-product' );
        wp_dequeue_script( 'wc-add-to-cart-variation' );
        wp_dequeue_script( 'wc-checkout' );
        wp_dequeue_style( 'woocommerce-general' );
        wp_dequeue_style( 'woocommerce-layout' );
        wp_dequeue_style( 'woocommerce-smallscreen' );
        wp_dequeue_style( 'woocommerce-gzd-layout' );
        wp_dequeue_style( 'woocommerce-gzd-single-product' );
    }
}, 999 );

/* ============================================================
   13. Onnodige scripts/styles uitschakelen op startpagina
   ============================================================ */
function werdu_page_has_form() {
    if ( is_admin() ) return false;
    $form_pages = array( 'kontakt', 'beratung', 'beratung-anfragen', 'contact', 'anfrage' );
    if ( is_page( $form_pages ) ) return true;
    $post = get_post();
    if ( $post && isset( $post->post_content ) ) {
        $content = $post->post_content;
        if ( strpos( $content, '[contact-form-7' ) !== false ) return true;
        if ( strpos( $content, '[fluentform' ) !== false ) return true;
    }
    return false;
}

add_action( 'wp_enqueue_scripts', function() {
    if ( ! is_front_page() || is_admin() ) return;

    wp_dequeue_script( 'megamenu' );
    wp_dequeue_script( 'megamenu-pro' );
    wp_dequeue_script( 'maxmegamenu' );
    wp_dequeue_script( 'maxmegamenu-js' );
    wp_dequeue_script( 'max-mega-menu' );
    wp_dequeue_style( 'megamenu' );
    wp_dequeue_style( 'megamenu-pro' );
    wp_dequeue_style( 'maxmegamenu' );
    wp_dequeue_style( 'maxmegamenu-style' );
    wp_dequeue_style( 'maxmegamenu-css' );
    wp_dequeue_style( 'max-mega-menu' );
    wp_dequeue_style( 'maxmegamenu-style-css' );

    if ( ! is_page( array( 'kontakt', 'beratung', 'beratung-anfragen' ) ) ) {
        wp_dequeue_script( 'contact-form-7' );
        wp_dequeue_style( 'contact-form-7' );
    }

    if ( ! werdu_page_has_form() ) {
        wp_dequeue_script( 'fluentform-submission' );
        wp_dequeue_style( 'fluentform-public-default' );
    }

    wp_dequeue_style( 'woocommerce-gzd-layout' );
    wp_dequeue_style( 'woocommerce-gzd-single-product' );
    wp_dequeue_script( 'shortpixel' );
    wp_dequeue_style( 'shortpixel' );
    wp_dequeue_script( 'caos' );
    wp_dequeue_style( 'caos' );
    wp_dequeue_script( 'duplicator' );
    wp_dequeue_style( 'duplicator' );

}, 999 );

/* ============================================================
   13b. HOMEPAGE LIGHTHOUSE — dequeue unused, defer the rest
   ============================================================ */
add_action( 'wp_enqueue_scripts', 'werdu_homepage_speed_dequeue', 1001 );
function werdu_homepage_speed_dequeue() {
    if ( is_admin() || ! is_front_page() ) {
        return;
    }

    $style_handles = array(
        'font-icons',
        'shoppingcart-google-fonts',
        'elementor-frontend',
        'elementor-icons',
        'elementor-animations',
        'elementor-gf-local-roboto',
        'elementor-global',
        'fluent-forms-elementor-widget',
        'fluentform-elementor-widget',
        'wc-blocks-style',
        'wc-blocks-vendors-style',
        'wp-block-library',
        'wp-block-library-theme',
        'global-styles',
        'classic-theme-styles',
        'werdu-compact',
        'woocommerce-general',
        'woocommerce-layout',
        'woocommerce-smallscreen',
    );
    foreach ( $style_handles as $handle ) {
        wp_dequeue_style( $handle );
        wp_deregister_style( $handle );
    }

    if ( function_exists( 'wp_styles' ) && wp_styles() ) {
        foreach ( wp_styles()->registered as $handle => $obj ) {
            if ( 0 === strpos( $handle, 'elementor' ) || false !== strpos( $handle, 'fluent-forms-elementor' ) ) {
                wp_dequeue_style( $handle );
                wp_deregister_style( $handle );
            }
        }
    }

    $script_handles = array(
        'jquery-flexslider',
        'shoppingcart-slider',
        'shoppingcart-skip-link-focus-fix',
        'html5',
        'elementor-frontend',
        'elementor-webpack-runtime',
        'elementor-frontend-modules',
        'elementor-pro-frontend',
        'fluent-forms-elementor-widget',
        'wp-embed',
        'comment-reply',
        'jquery-migrate',
        'jquery-sticky',
        'shoppingcart-sticky-settings',
        'fluent-forms-elementor-widget',
        'fluentform-elementor-widget',
    );
    foreach ( $script_handles as $handle ) {
        wp_dequeue_script( $handle );
        wp_deregister_script( $handle );
    }

    if ( function_exists( 'wp_scripts' ) && wp_scripts() ) {
        foreach ( wp_scripts()->registered as $handle => $obj ) {
            if ( 0 === strpos( $handle, 'caos' ) || 'google_gtagjs' === $handle ) {
                wp_dequeue_script( $handle );
                wp_deregister_script( $handle );
                continue;
            }
            if ( false !== strpos( $handle, 'fluent-forms-elementor' ) || false !== strpos( $handle, 'sticky' ) ) {
                wp_dequeue_script( $handle );
                wp_deregister_script( $handle );
                continue;
            }
            if ( in_array( $handle, array( 'jquery', 'jquery-core', 'jquery-migrate' ), true ) ) {
                wp_scripts()->add_data( $handle, 'group', 1 );
            }
        }
    }
}

add_filter( 'caos_exclude_from_tracking', 'werdu_homepage_exclude_caos' );
function werdu_homepage_exclude_caos( $exclude ) {
    if ( is_front_page() ) {
        return true;
    }
    return $exclude;
}

add_filter( 'style_loader_tag', 'werdu_homepage_defer_noncritical_css', 20, 4 );
function werdu_homepage_defer_noncritical_css( $html, $handle, $href, $media ) {
    if ( is_admin() || ! is_front_page() ) {
        return $html;
    }
    if ( ! is_string( $href ) || '' === $href ) {
        return $html;
    }
    $keep_blocking = array(
        'parent-style',
        'child-style',
        'shoppingcart-style',
        'shoppingcart-responsive',
    );
    if ( in_array( $handle, $keep_blocking, true ) ) {
        return $html;
    }
    if ( $media && $media !== 'all' && $media !== 'screen' && $media !== 'print' ) {
        return $html;
    }
    $href_esc = esc_url( $href );
    return '<link rel="stylesheet" href="' . $href_esc . '" media="print" onload="this.media=\'all\'">'
        . '<noscript><link rel="stylesheet" href="' . $href_esc . '"></noscript>';
}

add_filter( 'script_loader_tag', 'werdu_homepage_defer_noncritical_js', 20, 2 );
function werdu_homepage_defer_noncritical_js( $tag, $handle ) {
    if ( is_admin() || ! is_front_page() ) {
        return $tag;
    }
    if ( false !== strpos( $tag, ' defer' ) || false !== strpos( $tag, ' async' ) ) {
        return $tag;
    }
    if ( 0 === strpos( $handle, 'caos' ) ) {
        return str_replace( ' src', ' async src', $tag );
    }
    $defer_handles = array(
        'jquery-core',
        'jquery',
        'jquery-migrate',
        'elementor-frontend',
        'elementor-webpack-runtime',
        'elementor-frontend-modules',
        'elementor-pro-frontend',
        'wp-embed',
        'comment-reply',
        'complianz',
        'complianz-gdpr',
        'cmplz-cookiebanner',
    );
    if ( in_array( $handle, $defer_handles, true ) || 0 === strpos( $handle, 'cmplz' ) ) {
        return str_replace( ' src', ' defer src', $tag );
    }
    return $tag;
}

add_action( 'wp_head', 'werdu_homepage_inline_lp_css', 2 );
function werdu_homepage_inline_lp_css() {
    if ( is_admin() || ! is_front_page() ) {
        return;
    }
    $css_path = get_stylesheet_directory() . '/css/werdu-startseite-v3.css';
    if ( ! file_exists( $css_path ) ) {
        return;
    }
    $css = file_get_contents( $css_path );
    if ( ! is_string( $css ) || '' === $css ) {
        return;
    }
    echo '<style id="whp-css">' . $css . '</style>' . "\n";
}

add_action( 'wp_head', 'werdu_print_a11y_css', 4 );
function werdu_print_a11y_css() {
    if ( is_admin() ) {
        return;
    }
    echo '<style id="werdu-a11y">'
        . ':root{--cmplz_button_accept_background_color:#c2410c;--cmplz_button_accept_text_color:#ffffff;--cmplz_button_font_size:18px;--cmplz_text_font_size:14px;--cmplz_link_font_size:14px;--cmplz_hyperlink_color:#0b57d0}'
        . '.cmplz-cookiebanner .cmplz-buttons .cmplz-btn.cmplz-accept,.cmplz-btn.cmplz-accept{background:#c2410c!important;color:#fff!important;font-weight:700!important;font-size:18px!important;min-height:48px!important}'
        . '#site-title a,.main-navigation a:hover,.main-navigation ul li.current-menu-item a,.main-navigation ul li.current_page_item a,.main-navigation ul li:hover>a{color:#c2410c!important}'
        . '.header-right .wcmenucart-contents,.header-right .wishlist-btn{color:#334155!important}'
        . '.header-right .cart-value,.wl-counter{background:#c2410c!important;color:#fff!important}'
        . '#colophon .widget-wrap a,#colophon .widget a,#colophon .textwidget a,#colophon .copyright a,#colophon .site-info a{color:#c2410c!important;text-decoration:underline!important;display:inline-block;padding:10px 6px;margin:2px 4px 2px 0;min-height:44px;line-height:1.35;font-size:15px;box-sizing:border-box}'
        . '#colophon .copyright,#colophon .site-info{color:#444!important;font-size:14px!important}'
        . '#colophon [style*="color:#999"],#colophon [style*="color: #999"]{color:#444!important}'
        . '#colophon [style*="color:#ff6600"],#colophon [style*="color: #ff6600"],#colophon [style*="color:#FF6600"]{color:#c2410c!important;text-decoration:underline!important}'
        . '#colophon [style*="153, 255"],#colophon [style*="#0099FF"],#colophon [style*="#0099ff"]{color:#0b57d0!important;text-decoration:underline!important}'
        . '.whp-page a:not(.whp-btn){color:#c2410c;text-decoration:underline}'
        . '.whp-page .whp-btn--primary,.whp-page a.whp-btn--primary,button.whp-btn--primary,.whp-btn--primary{background:#c2410c!important;color:#fff!important;font-size:1.125rem!important;font-weight:700!important}'
        . '</style>' . "\n";
}

add_filter( 'widget_text', 'werdu_a11y_fix_widget_html', 99 );
add_filter( 'widget_text_content', 'werdu_a11y_fix_widget_html', 99 );
add_filter( 'widget_custom_html_content', 'werdu_a11y_fix_widget_html', 99 );
add_filter( 'widget_block_content', 'werdu_a11y_fix_widget_html', 99 );
function werdu_a11y_fix_widget_html( $html ) {
    if ( ! is_string( $html ) || '' === $html ) {
        return $html;
    }
    if ( false !== strpos( $html, 'contact-list' ) ) {
        $html = preg_replace_callback(
            '/(<ul[^>]*class="[^"]*contact-list[^"]*"[^>]*>)(.*?)(<\/ul>)/is',
            static function ( $m ) {
                $inner = $m[2];
                $inner = preg_replace( '/<p\b[^>]*>/i', '<li>', $inner );
                $inner = preg_replace( '/<\/p>/i', '</li>', $inner );
                return $m[1] . $inner . $m[3];
            },
            $html
        );
    }
    $html = str_ireplace( array( 'color:#ff6600', 'color: #ff6600' ), 'color:#c2410c', $html );
    $html = str_ireplace( 'text-decoration:none', 'text-decoration:underline', $html );
    $html = str_ireplace( array( 'color:#999', 'color: #999' ), 'color:#444', $html );
    $html = str_ireplace( 'font-size:10px', 'font-size:14px', $html );
    $html = str_ireplace( array( 'color:#0099FF', 'color: #0099FF', 'color: rgb(0, 153, 255)' ), 'color:#0b57d0', $html );
    return $html;
}

add_action( 'dynamic_sidebar_before', 'werdu_a11y_sidebar_ob_start', 0 );
function werdu_a11y_sidebar_ob_start() {
    ob_start( 'werdu_a11y_fix_widget_html' );
}
add_action( 'dynamic_sidebar_after', 'werdu_a11y_sidebar_ob_end', 999 );
function werdu_a11y_sidebar_ob_end() {
    if ( ob_get_level() > 0 ) {
        ob_end_flush();
    }
}

add_action( 'wp_head', 'werdu_homepage_preload_lcp_image', 1 );
function werdu_homepage_preload_lcp_image() {
    if ( is_admin() || ! is_front_page() ) {
        return;
    }
    $hero = get_stylesheet_directory_uri() . '/images/whp-hero-640.webp';
    echo '<link rel="preload" as="image" href="' . esc_url( $hero ) . '" fetchpriority="high">' . "\n";
}

/* ============================================================
   14. Database query cache hints
   ============================================================ */
add_filter( 'wp_cache_themes_persistently', '__return_true', 100 );

/* ============================================================
   15. Elementor: Google Fonts uitschakelen
   ============================================================ */
add_action( 'elementor/init', function() {
    add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );
} );

/* ============================================================
   16. WordPress heartbeat vertragen
   ============================================================ */
add_filter( 'heartbeat_settings', function( $settings ) {
    $settings['interval'] = 60;
    return $settings;
} );

/* ============================================================
   17. wp-cron timeout
   ============================================================ */
add_filter( 'cron_request', function( $cron_request ) {
    $cron_request['args']['timeout'] = 2;
    return $cron_request;
} );

/* ============================================================
   18. PREMIUM PAGES ASSETS
   ============================================================ */
function werdu_enqueue_premium_assets() {
    if ( is_front_page() ) return;
    $css_file = get_stylesheet_directory() . '/css/werdu-pages.css';
    $js_file  = get_stylesheet_directory() . '/js/werdu-pages.js';
    $css_ver = file_exists($css_file) ? filemtime($css_file) : '7.0';
    $js_ver  = file_exists($js_file)  ? filemtime($js_file)  : '7.0';
    wp_enqueue_style( 'werdu-pages', get_stylesheet_directory_uri() . '/css/werdu-pages.css', array(), $css_ver, 'all' );
    wp_enqueue_script( 'werdu-pages', get_stylesheet_directory_uri() . '/js/werdu-pages.js', array(), $js_ver, true );
}
add_action('wp_enqueue_scripts', 'werdu_enqueue_premium_assets', 20);

/* ============================================================
   18b. BERATUNG PREFILL (Calc → /beratung-anfragen/)
   ============================================================ */
function werdu_theme_js_uri( $filename ) {
    $candidates = array(
        get_stylesheet_directory() . '/JS/' . $filename,
        get_stylesheet_directory() . '/js/' . $filename,
    );
    foreach ( $candidates as $path ) {
        if ( file_exists( $path ) ) {
            return array(
                'path' => $path,
                'uri'  => str_replace(
                    get_stylesheet_directory(),
                    get_stylesheet_directory_uri(),
                    $path
                ),
            );
        }
    }
    return null;
}

function werdu_enqueue_beratung_prefill() {
    if ( is_admin() ) return;
    if ( ! is_page( 'beratung-anfragen' ) && ! is_page( 'beratung' ) ) return;

    $js = werdu_theme_js_uri( 'werdu-beratung-prefill.js' );
    if ( ! $js ) return;

    wp_enqueue_script(
        'werdu-beratung-prefill',
        $js['uri'],
        array(),
        filemtime( $js['path'] ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'werdu_enqueue_beratung_prefill', 25 );

/* ============================================================
   18c. CALC → Beratung handoff (shared + per-calculator bridges)
   ============================================================ */
function werdu_enqueue_calc_handoff_assets() {
    if ( is_admin() ) return;

    $is_home_calc = is_front_page() || is_home();
    $is_big_calc  = function_exists( 'werdu_is_solarbatterie_rechner_page' )
        ? werdu_is_solarbatterie_rechner_page()
        : ( is_page( 'solarbatterie-rechner' ) || is_page_template( 'solarbatterie-rechner-v3.php' ) );
    $is_blog_calc = is_page( 'gratis-heimspeicher-rechner-online' )
        || is_page_template( 'gratis-solarbatterie-rechner-v3.php' )
        || is_page_template( 'page-werdu-rechner.php' );

    if ( ! $is_home_calc && ! $is_big_calc && ! $is_blog_calc ) {
        return;
    }

    $handoff = werdu_theme_js_uri( 'werdu-calc-handoff.js' );
    if ( $handoff ) {
        wp_enqueue_script(
            'werdu-calc-handoff',
            $handoff['uri'],
            array(),
            filemtime( $handoff['path'] ),
            true
        );
        wp_localize_script( 'werdu-calc-handoff', 'werduCalcConfig', array(
            'beratungUrl' => home_url( '/beratung-anfragen/' ),
            'homeUrl'     => home_url( '/' ),
        ) );
    }

    if ( $is_home_calc ) {
        $home = werdu_theme_js_uri( 'werdu-homepage-calc-bridge.js' );
        if ( $home ) {
            wp_enqueue_script(
                'werdu-homepage-calc-bridge',
                $home['uri'],
                array( 'werdu-calc-handoff' ),
                filemtime( $home['path'] ),
                true
            );
        }
    }

    if ( $is_big_calc ) {
        $big = werdu_theme_js_uri( 'werdu-big-calc-bridge.js' );
        if ( $big ) {
            wp_enqueue_script(
                'werdu-big-calc-bridge',
                $big['uri'],
                array( 'werdu-calc-handoff' ),
                filemtime( $big['path'] ),
                true
            );
        }
    }

    if ( $is_blog_calc ) {
        $blog = werdu_theme_js_uri( 'werdu-blog-calc-bridge.js' );
        if ( $blog ) {
            wp_enqueue_script(
                'werdu-blog-calc-bridge',
                $blog['uri'],
                array( 'werdu-calc-handoff' ),
                filemtime( $blog['path'] ),
                true
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'werdu_enqueue_calc_handoff_assets', 30 );

/* ============================================================
   18d. Werdu Homepage V2 template — losstaand, review-baar
   ============================================================ */
/**
 * Laadt de CSS/JS voor het "Werdu Homepage V2"-template uitsluitend wanneer
 * dat exacte template actief is (template-werdu-v2.php, root van het child
 * theme). Bewust hier in functions.php i.p.v. bovenin het templatebestand
 * zelf: het templatebestand moet EXACT beginnen met de Template Name-header
 * gevolgd door get_header(), zodat WordPress het altijd correct herkent en
 * in de Sjabloon-dropdown toont, ongeacht hook-timing.
 */
function werdu_enqueue_homepage_v2_assets() {
    if ( ! is_front_page() && ! is_page_template( 'template-werdu-v2.php' ) ) {
        return;
    }

    if ( ! is_front_page() ) {
        $css_path = get_stylesheet_directory() . '/css/werdu-startseite-v3.css';
        if ( file_exists( $css_path ) ) {
            wp_enqueue_style(
                'werdu-startseite-v3',
                get_stylesheet_directory_uri() . '/css/werdu-startseite-v3.css',
                array(),
                filemtime( $css_path )
            );
        }
    }

    $handoff_deps = array();
    $handoff = werdu_theme_js_uri( 'werdu-calc-handoff.js' );
    if ( $handoff ) {
        wp_enqueue_script( 'werdu-calc-handoff', $handoff['uri'], array(), filemtime( $handoff['path'] ), true );
        wp_localize_script( 'werdu-calc-handoff', 'werduCalcConfig', array(
            'beratungUrl' => function_exists( 'werdu_home_seo_beratung_url' ) ? werdu_home_seo_beratung_url() : home_url( '/beratung-anfragen/' ),
            'homeUrl'     => home_url( '/' ),
        ) );
        $handoff_deps[] = 'werdu-calc-handoff';
    }

    $js_path = get_stylesheet_directory() . '/JS/werdu-startseite-v3.js';
    if ( file_exists( $js_path ) ) {
        wp_enqueue_script(
            'werdu-startseite-v3',
            get_stylesheet_directory_uri() . '/JS/werdu-startseite-v3.js',
            $handoff_deps,
            filemtime( $js_path ),
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'werdu_enqueue_homepage_v2_assets', 30 );

/**
 * Force the light-theme landing template on the WordPress front page so
 * Elementor canvas / leftover widget HTML cannot paint the old layout.
 */
function werdu_force_homepage_v2_template( $template ) {
    if ( is_admin() || ! is_front_page() ) {
        return $template;
    }
    $lp = get_stylesheet_directory() . '/template-werdu-v2.php';
    if ( file_exists( $lp ) ) {
        return $lp;
    }
    return $template;
}
add_filter( 'template_include', 'werdu_force_homepage_v2_template', 99999 );

function werdu_dequeue_elementor_on_homepage() {
    if ( is_admin() || ! is_front_page() ) {
        return;
    }
    wp_dequeue_style( 'elementor-frontend' );
    wp_dequeue_style( 'elementor-icons' );
    wp_dequeue_style( 'elementor-animations' );
    wp_dequeue_style( 'elementor-gf-local-roboto' );
    wp_dequeue_script( 'elementor-frontend' );
    wp_dequeue_script( 'elementor-webpack-runtime' );
    wp_dequeue_script( 'elementor-frontend-modules' );
}
add_action( 'wp_enqueue_scripts', 'werdu_dequeue_elementor_on_homepage', 1000 );

/**
 * Eenmalige page-template cache flush: WordPress cachet de lijst met
 * beschikbare page templates (Seiten-Attribute -> Template dropdown) in de
 * 'themes'-cache-group. Na het toevoegen/hernoemen van template-werdu-v2.php
 * moet die cache geleegd worden zodat het nieuwe template direct in de
 * dropdown verschijnt, zonder te wachten op een automatische expiry.
 * Zichzelf-beperkend via een optie-vlag: draait maar één keer per
 * daadwerkelijke wijziging van dit bestand (filemtime-versie), niet op
 * elke pageload.
 */
function werdu_flush_page_template_cache_once() {
    $version_flag = 'werdu_v2_tpl_cache_flushed';
    $current_ver  = (string) @filemtime( __FILE__ );
    if ( get_option( $version_flag ) === $current_ver ) {
        return;
    }

    wp_clean_themes_cache( false );

    if ( function_exists( 'wp_cache_flush_group' ) ) {
        wp_cache_flush_group( 'themes' );
    }

    update_option( $version_flag, $current_ver, false );
}
add_action( 'admin_init', 'werdu_flush_page_template_cache_once' );

/**
 * Late footer fallback: inject bridges only if helpers are missing (optimizer-safe).
 */
function werdu_print_calc_bridge_footer_fallback() {
    if ( is_admin() ) return;

    $need_home = is_front_page() || is_home();
    $need_blog = is_page( 'gratis-heimspeicher-rechner-online' );
    $need_big  = is_page( 'solarbatterie-rechner' ) || is_page_template( 'solarbatterie-rechner-v3.php' );
    if ( ! $need_home && ! $need_blog && ! $need_big ) return;

    $urls = array();
    $handoff = werdu_theme_js_uri( 'werdu-calc-handoff.js' );
    if ( $handoff ) {
        $urls['handoff'] = $handoff['uri'] . '?v=' . filemtime( $handoff['path'] );
    }
    if ( $need_home ) {
        $f = werdu_theme_js_uri( 'werdu-homepage-calc-bridge.js' );
        if ( $f ) $urls['home'] = $f['uri'] . '?v=' . filemtime( $f['path'] );
    }
    if ( $need_blog ) {
        $f = werdu_theme_js_uri( 'werdu-blog-calc-bridge.js' );
        if ( $f ) $urls['blog'] = $f['uri'] . '?v=' . filemtime( $f['path'] );
    }
    if ( $need_big ) {
        $f = werdu_theme_js_uri( 'werdu-big-calc-bridge.js' );
        if ( $f ) $urls['big'] = $f['uri'] . '?v=' . filemtime( $f['path'] );
        $c = werdu_theme_js_uri( 'werdu-calc.js' );
        if ( $c ) $urls['calc'] = $c['uri'] . '?v=' . filemtime( $c['path'] );
    }
    if ( empty( $urls ) ) return;

    $json   = wp_json_encode( $urls );
    $config = wp_json_encode( array(
        'beratungUrl' => home_url( '/beratung-anfragen/' ),
        'homeUrl'     => home_url( '/' ),
    ) );
    echo "<script id=\"werdu-calc-bridge-fallback\">\n";
    echo "(function(){\n";
    echo "  if (!window.werduCalcConfig) window.werduCalcConfig = {$config};\n";
    echo "  var urls = {$json};\n";
    echo "  function load(src, test){\n";
    echo "    if (!src) return;\n";
    echo "    try { if (test && test()) return; } catch (e) {}\n";
    echo "    var bare = src.split('?')[0];\n";
    echo "    if (document.querySelector('script[src*=\"'+bare.split('/').pop()+'\"]')) return;\n";
    echo "    var s=document.createElement('script'); s.src=src; s.async=false; document.body.appendChild(s);\n";
    echo "  }\n";
    echo "  function run(){\n";
    echo "    load(urls.handoff, function(){ return !!window.werduCalcHandoff; });\n";
    if ( $need_home ) {
        echo "    setTimeout(function(){ load(urls.home, function(){ return !!(window.werduCalc && window.werduCalc.__werduBridged); }); }, 50);\n";
    }
    if ( $need_blog ) {
        echo "    setTimeout(function(){ load(urls.blog, function(){ return !!(window.calculateWerdu && window.calculateWerdu.__werduBridged); }); }, 50);\n";
    }
    if ( $need_big ) {
        echo "    setTimeout(function(){ load(urls.big, function(){ return !!window.__werduBigBridge; }); }, 50);\n";
        echo "    setTimeout(function(){ load(urls.calc, function(){ return typeof window.wCalculate === 'function'; }); }, 50);\n";
    }
    echo "  }\n";
    echo "  if (document.readyState === 'complete') setTimeout(run, 300);\n";
    echo "  else window.addEventListener('load', function(){ setTimeout(run, 300); });\n";
    echo "})();\n";
    echo "</script>\n";
}
add_action( 'wp_footer', 'werdu_print_calc_bridge_footer_fallback', 99 );

/* ============================================================
   19. Font Awesome font-display: swap
   ============================================================ */
add_action( 'wp_head', function() {
    if ( is_admin() ) return;
    echo '<style>@font-face { font-display: swap !important; }</style>' . "
";
}, 0 );

/* ============================================================
   20. CLS FIX — Reserveer ruimte
   ============================================================ */
add_action( 'wp_head', function() {
    if ( is_admin() ) return;
    echo '<style id="werdu-cls-lock">'
        . '#site-content-contain{position:relative}'
        . '.custom-logo-link{display:inline-block;width:61px;height:60px;overflow:hidden}'
        . '.custom-logo,.custom-logo-link img{width:61px!important;height:60px!important;max-width:61px!important;max-height:60px!important;aspect-ratio:143/140}'
        . '#sticky-header #site-branding{display:none!important}'
        . '#sticky-header{min-height:56px;box-sizing:border-box}'
        . '#site-detail{min-height:48px}'
        . '.custom-logo{transition:none!important}'
        . 'img[width][height]:not(.custom-logo):not(.whp-hero-lcp){height:auto}'
        . '</style>' . "\n";
}, 3 );

/* ============================================================
   22. LOGO DIMENSIONS FIX
   ============================================================ */
add_filter( 'get_custom_logo', function( $html ) {
    if ( ! is_string( $html ) || '' === $html ) {
        return $html;
    }
    $html = preg_replace( '/\swidth="\d+"/', ' width="61"', $html );
    $html = preg_replace( '/\sheight="\d+"/', ' height="60"', $html );
    if ( false === strpos( $html, 'width=' ) ) {
        $html = str_replace( '<img', '<img width="61" height="60"', $html );
    }
    return $html;
}, 30 );

/* ============================================================
   23. HERO FETCHPRIORITY
   ============================================================ */
add_filter( 'wp_get_attachment_image_attributes', function( $attr, $attachment ) {
    if ( ! is_front_page() ) {
        return $attr;
    }
    $class = isset( $attr['class'] ) ? $attr['class'] : '';
    $is_hero = ( false !== strpos( $class, 'attachment-large' ) )
        || ( false !== strpos( $class, 'werdu-hero-lcp' ) )
        || ( ! empty( $attr['fetchpriority'] ) && 'high' === $attr['fetchpriority'] );
    if ( $is_hero ) {
        $attr['fetchpriority'] = 'high';
        $attr['decoding']      = 'async';
        unset( $attr['loading'] );
    }
    return $attr;
}, 10, 2 );

/* ============================================================
   24. PRECONNECT CLEANUP
   ============================================================ */
add_filter( 'wp_resource_hints', function( $hints, $relation_type ) {
    if ( is_admin() ) return $hints;
    if ( $relation_type === 'preconnect' || $relation_type === 'dns-prefetch' ) {
        $clean = array();
        foreach ( $hints as $hint ) {
            $url = is_array( $hint ) ? ( isset( $hint['href'] ) ? $hint['href'] : '' ) : $hint;
            if ( is_string( $url ) && (
                strpos( $url, 'fonts.gstatic.com' ) !== false ||
                strpos( $url, 'google-analytics.com' ) !== false ||
                strpos( $url, 'fonts.googleapis.com' ) !== false ||
                strpos( $url, '://werdu.de' ) !== false
            ) ) {
                continue;
            }
            $clean[] = $hint;
        }
        return $clean;
    }
    return $hints;
}, 999, 2 );

/* ============================================================
   25. WERDU PREIS MANAGER
   ============================================================ */
$werdu_preis_file = get_stylesheet_directory() . '/werdu-preis-manager.php';
if ( file_exists( $werdu_preis_file ) ) {
    require_once $werdu_preis_file;
} else {
    add_action( 'admin_notices', function() {
        echo '<div class="notice notice-error"><p><strong>Werdu Preis Manager:</strong> Het bestand <code>werdu-preis-manager.php</code> ontbreekt.</p></div>';
    });
}

/* ============================================================
   26. BARRIEREFREIHEIT — Dynamische Sitemap Shortcode
   ============================================================ */
if (!function_exists('werdu_bf_dynamic_sitemap')) {
    function werdu_bf_dynamic_sitemap($atts) {
        $exclude_slugs = array('cart','checkout','my-account','wishlist','compare','winkelmand');
        $output = '';

        $pages = get_pages(array(
            'sort_column' => 'post_title',
            'sort_order'  => 'ASC',
            'post_status' => 'publish',
        ));

        $page_rows = '';
        if (!empty($pages)) {
            foreach ($pages as $page) {
                $slug = $page->post_name;
                if (in_array($slug, $exclude_slugs)) continue;
                $url = get_permalink($page);
                $title = esc_html(get_the_title($page));
                $path = esc_html(str_replace(home_url(), '', $url));
                if (empty($path)) $path = '/';
                $page_rows .= '<tr>';
                $page_rows .= '<th scope="row" style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;font-weight:600;">' . $title . '</th>';
                $page_rows .= '<td style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;"><a href="' . esc_url($url) . '" style="color:#0099FF;text-decoration:underline;">' . $path . '</a></td>';
                $page_rows .= '<td style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;"><span style="display:inline-flex;align-items:center;gap:0.5rem;background:#fff3e0;color:#d35400;padding:0.35rem 0.85rem;border-radius:999px;font-weight:600;font-size:0.95rem;border:1px solid #ffcc80;">Teilweise</span></td>';
                $page_rows .= '</tr>';
            }
        }

        $posts = get_posts(array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ));

        $post_rows = '';
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $url = get_permalink($post);
                $title = esc_html(get_the_title($post));
                $path = esc_html(str_replace(home_url(), '', $url));
                $post_rows .= '<tr>';
                $post_rows .= '<th scope="row" style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;font-weight:600;">' . $title . '</th>';
                $post_rows .= '<td style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;"><a href="' . esc_url($url) . '" style="color:#0099FF;text-decoration:underline;">' . $path . '</a></td>';
                $post_rows .= '<td style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;"><span style="display:inline-flex;align-items:center;gap:0.5rem;background:#fff3e0;color:#d35400;padding:0.35rem 0.85rem;border-radius:999px;font-weight:600;font-size:0.95rem;border:1px solid #ffcc80;">Teilweise</span></td>';
                $post_rows .= '</tr>';
            }
        }

        $product_rows = '';
        if (class_exists('WooCommerce') && function_exists('wc_get_products')) {
            $products = wc_get_products(array(
                'status'  => 'publish',
                'limit'   => -1,
                'orderby' => 'title',
                'order'   => 'ASC',
            ));
            if (!empty($products)) {
                foreach ($products as $product) {
                    $url = $product->get_permalink();
                    $title = esc_html($product->get_name());
                    $path = esc_html(str_replace(home_url(), '', $url));
                    $product_rows .= '<tr>';
                    $product_rows .= '<th scope="row" style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;font-weight:600;">' . $title . '</th>';
                    $product_rows .= '<td style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;"><a href="' . esc_url($url) . '" style="color:#0099FF;text-decoration:underline;">' . $path . '</a></td>';
                    $product_rows .= '<td style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;"><span style="display:inline-flex;align-items:center;gap:0.5rem;background:#fff3e0;color:#d35400;padding:0.35rem 0.85rem;border-radius:999px;font-weight:600;font-size:0.95rem;border:1px solid #ffcc80;">Teilweise</span></td>';
                    $product_rows .= '</tr>';
                }
            }
        }

        $output .= '<h2 id="sitemap" style="font-size:clamp(1.35rem,3vw,1.75rem);margin-top:2.5rem;color:#1a1a1a;font-weight:700;line-height:1.3;">12. Uebersicht aller Seiten auf werdu.de</h2>';
        $output .= '<div style="padding:1.5rem;margin:1.5rem 0;border-radius:8px;border-left:5px solid #0099FF;background:#e6f4ff;box-shadow:0 2px 8px rgba(0,0,0,0.08);">';
        $output .= '<p style="margin:0 0 1rem 0;line-height:1.7;">Nachfolgend finden Sie eine <strong>automatisch generierte Uebersicht</strong> aller oeffentlich zugaenglichen Seiten, Blog-Artikel und Produkte. Diese Liste aktualisiert sich automatisch, sobald neue Inhalte veroeffentlicht werden.</p>';
        $output .= '</div>';

        if ($page_rows) {
            $output .= '<h3 style="font-size:clamp(1.1rem,2.5vw,1.35rem);margin-top:1.5rem;color:#1a1a1a;font-weight:700;line-height:1.3;">12.1 Seiten</h3>';
            $output .= '<div style="overflow-x:auto;margin:1.5rem 0;">';
            $output .= '<table style="width:100%;border-collapse:collapse;font-size:1rem;min-width:500px;">';
            $output .= '<caption style="text-align:left;font-weight:700;margin-bottom:0.5rem;color:#1a1a1a;">Status der Seiten</caption>';
            $output .= '<thead style="background:#ff6600;color:#fff;"><tr>';
            $output .= '<th scope="col" style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;">Seite</th>';
            $output .= '<th scope="col" style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;">URL</th>';
            $output .= '<th scope="col" style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;">Status</th>';
            $output .= '</tr></thead><tbody>';
            $output .= $page_rows;
            $output .= '</tbody></table></div>';
        }

        if ($post_rows) {
            $output .= '<h3 style="font-size:clamp(1.1rem,2.5vw,1.35rem);margin-top:1.5rem;color:#1a1a1a;font-weight:700;line-height:1.3;">12.2 Blog-Artikel</h3>';
            $output .= '<div style="overflow-x:auto;margin:1.5rem 0;">';
            $output .= '<table style="width:100%;border-collapse:collapse;font-size:1rem;min-width:500px;">';
            $output .= '<caption style="text-align:left;font-weight:700;margin-bottom:0.5rem;color:#1a1a1a;">Status der Blog-Artikel</caption>';
            $output .= '<thead style="background:#ff6600;color:#fff;"><tr>';
            $output .= '<th scope="col" style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;">Artikel</th>';
            $output .= '<th scope="col" style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;">URL</th>';
            $output .= '<th scope="col" style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;">Status</th>';
            $output .= '</tr></thead><tbody>';
            $output .= $post_rows;
            $output .= '</tbody></table></div>';
        }

        if ($product_rows) {
            $output .= '<h3 style="font-size:clamp(1.1rem,2.5vw,1.35rem);margin-top:1.5rem;color:#1a1a1a;font-weight:700;line-height:1.3;">12.3 Produkte</h3>';
            $output .= '<div style="overflow-x:auto;margin:1.5rem 0;">';
            $output .= '<table style="width:100%;border-collapse:collapse;font-size:1rem;min-width:500px;">';
            $output .= '<caption style="text-align:left;font-weight:700;margin-bottom:0.5rem;color:#1a1a1a;">Status der Produkte</caption>';
            $output .= '<thead style="background:#ff6600;color:#fff;"><tr>';
            $output .= '<th scope="col" style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;">Produkt</th>';
            $output .= '<th scope="col" style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;">URL</th>';
            $output .= '<th scope="col" style="padding:0.75rem 1rem;text-align:left;border:1px solid #d1d5db;">Status</th>';
            $output .= '</tr></thead><tbody>';
            $output .= $product_rows;
            $output .= '</tbody></table></div>';
        }

        $output .= '<div style="padding:1.5rem;margin:1.5rem 0;border-radius:8px;border-left:5px solid #1AFF00;background:#e8f5e9;box-shadow:0 2px 8px rgba(0,0,0,0.08);">';
        $output .= '<p style="margin:0;"><span style="display:inline-flex;align-items:center;gap:0.5rem;background:#e8f5e9;color:#2e7d32;padding:0.35rem 0.85rem;border-radius:999px;font-weight:600;font-size:0.95rem;border:1px solid #a5d6a7;">Barrierefrei</span></p>';
        $output .= '<p style="margin:1rem 0 0 0;line-height:1.7;">Diese Barrierefreiheitserklaerung selbst wurde gemaess WCAG 2.1 Level AA und EN 301 549 erstellt.</p>';
        $output .= '</div>';

        return $output;
    }
    add_shortcode('werdu_bf_sitemap', 'werdu_bf_dynamic_sitemap');
}