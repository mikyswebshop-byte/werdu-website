<?php
/**
 * Header template voor werdu.de — v4 (H1-fix: alleen H1 op startpagina)
 * Geen output buffering. Geen CSP. Inline CSS voor CLS-fix.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="profile" href="http://gmpg.org/xfn/11" />

<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php endif; ?>

<style>
  /* CLS-FIX: Reserveer ruimte voor header */
  #masthead { min-height: 120px; }
  #sticky-header { min-height: 70px; box-sizing: border-box; }
  .site-content-contain { min-height: 100vh; }
  .custom-logo-link { display: inline-block; width: 61px; height: 60px; overflow: hidden; }
  .custom-logo, .custom-logo-link img {
    width: 61px !important;
    height: 60px !important;
    max-height: 60px !important;
    max-width: 61px !important;
    aspect-ratio: 143 / 140;
  }
  #sticky-header #site-branding { display: none !important; }
  #site-detail { min-height: 48px; }
  @font-face { font-display: swap !important; }
  .menu-toggle, .top-menu-toggle {
    background: transparent;
    border: 0;
    cursor: pointer;
    width: 40px;
    height: 40px;
    position: relative;
    padding: 0;
  }
  .menu-toggle .line-bar,
  .top-menu-toggle .line-bar,
  .menu-toggle .line-bar:before,
  .menu-toggle .line-bar:after,
  .top-menu-toggle .line-bar:before,
  .top-menu-toggle .line-bar:after {
    display: block;
    width: 22px;
    height: 2px;
    background: #0f172a;
    border-radius: 2px;
    position: absolute;
    left: 9px;
    content: "";
  }
  .menu-toggle .line-bar,
  .top-menu-toggle .line-bar { top: 19px; }
  .menu-toggle .line-bar:before,
  .top-menu-toggle .line-bar:before { top: -7px; }
  .menu-toggle .line-bar:after,
  .top-menu-toggle .line-bar:after { top: 7px; }
  a.skip-link, .skip-link {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    clip-path: inset(50%) !important;
    white-space: nowrap !important;
    border: 0 !important;
  }
  a.skip-link:focus, .skip-link:focus, .screen-reader-text:focus {
    display: inline-block !important;
    position: absolute !important;
    left: 8px !important;
    top: 8px !important;
    z-index: 100001 !important;
    width: auto !important;
    height: auto !important;
    margin: 0 !important;
    padding: 8px 14px !important;
    overflow: visible !important;
    clip: auto !important;
    clip-path: none !important;
    white-space: normal !important;
    background: #0f172a !important;
    color: #ffffff !important;
    font-size: 0.9rem !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;
    text-decoration: underline !important;
    border-radius: 6px !important;
  }
  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; }
  }
</style>

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php 
if ( function_exists( 'wp_body_open' ) ) {
    wp_body_open();
} else {
    do_action( 'wp_body_open' );
} 
?>

<div id="page" class="site">
<a class="skip-link screen-reader-text" href="#site-content-contain">
<?php echo esc_html( 'Zum Inhalt springen' ); ?>
</a>

<header id="masthead" class="site-header" role="banner">
<div class="header-wrap">

<?php the_custom_header_markup(); ?>

<div class="top-header">
<?php 
$shoppingcart_settings = shoppingcart_get_theme_options();

if ($shoppingcart_settings['shoppingcart_disable_top_bar'] == 0 ){
    if(is_active_sidebar('shoppingcart_header_info') || has_nav_menu('top-menu') || has_nav_menu('social-link')): ?>

        <div class="top-bar">
            <div class="wrap">

                <?php if(is_active_sidebar('shoppingcart_header_info')) {
                    dynamic_sidebar('shoppingcart_header_info');
                } ?>

                <div class="right-top-bar">

                    <?php if($shoppingcart_settings['shoppingcart_top_social_icons'] == 0){
                        do_action('shoppingcart_social_links');
                    } ?>

                    <?php if(has_nav_menu('top-menu')){ ?>
                        <nav class="top-bar-menu">
                            <button class="top-menu-toggle" type="button" aria-label="Menü öffnen" aria-expanded="false">
                                <span class="screen-reader-text">Menü öffnen</span>
                                <span class="line-bar" aria-hidden="true"></span>
                            </button>

                            <?php
                            wp_nav_menu(array(
                                'container' => '',
                                'theme_location' => 'top-menu',
                                'depth' => 1,
                                'items_wrap' => '<ul class="top-menu">%3$s</ul>',
                            ));
                            ?>
                        </nav>
                    <?php } ?>

                </div>
            </div>
        </div>

    <?php endif;
} ?>

<div id="site-branding">
<div class="wrap">

<?php shoppingcart_the_custom_logo(); ?>
<div id="site-detail">
    <?php if ( is_front_page() ) : ?>
        <p id="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
    <?php elseif ( is_home() ) : ?>
        <h1 id="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
    <?php else : ?>
        <div id="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></div>
    <?php endif; ?>
    <?php $description = get_bloginfo( 'description', 'display' ); if ( $description || is_customize_preview() ) : ?>
        <div id="site-description"><?php echo $description; ?></div>
    <?php endif; ?>
</div>

<div class="header-right">

<?php if (1 != $shoppingcart_settings['shoppingcart_search_custom_header']) { ?>
<div id="search-box">
<?php 
if (!class_exists('woocommerce')) {
    get_search_form();
} else {
    the_widget('WC_Widget_Product_Search', 'title=');
}
?>
</div>
<?php } ?>

<?php do_action('shoppingcart_cart_wishlist_icon_display'); ?>

</div>
</div>
</div>

<div id="sticky-header">
<div class="wrap">
<div class="main-header">

<div id="site-branding">
<?php shoppingcart_the_custom_logo(); ?>
</div>

<?php if($shoppingcart_settings['shoppingcart_disable_main_menu']==0){ ?>
<nav id="site-navigation" class="main-navigation">

<button class="menu-toggle" type="button" aria-label="Hauptmenü öffnen" aria-expanded="false" aria-controls="primary-menu">
<span class="line-bar" aria-hidden="true"></span>
</button>

<?php
wp_nav_menu(array(
    'theme_location' => 'primary',
    'container' => '',
    'items_wrap' => '<ul id="primary-menu">%3$s</ul>',
));
?>

</nav>
<?php } ?>

</div>
</div>
</div>

</div>
</header>

<div id="site-content-contain" class="site-content-contain">
<div id="content" class="site-content">