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
  .custom-logo, .custom-logo-link img { aspect-ratio: 143 / 140; height: auto; max-height: 60px; width: auto; }
  @font-face { font-display: swap !important; }
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
<?php esc_html_e('Skip to content','shoppingcart'); ?>
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
                            <button class="top-menu-toggle" type="button">
                                <span class="screen-reader-text">Menu</span>
                                <i class="fa-solid fa-bars"></i>
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
    <?php if ( is_front_page() || is_home() ) : ?>
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

<button class="menu-toggle">
<span class="line-bar"></span>
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