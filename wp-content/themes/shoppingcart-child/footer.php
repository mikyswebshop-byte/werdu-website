<?php
/**
 * The template for displaying the footer.
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */

$shoppingcart_settings = shoppingcart_get_theme_options(); ?>
</div><!-- end #content -->

<!-- Footer Start ============================================= -->
<footer id="colophon" class="site-footer" role="contentinfo">
<?php
$footer_column = $shoppingcart_settings['shoppingcart_footer_column_section'];
if( is_active_sidebar( 'shoppingcart_footer_1' ) || is_active_sidebar( 'shoppingcart_footer_2' ) || is_active_sidebar( 'shoppingcart_footer_3' ) || is_active_sidebar( 'shoppingcart_footer_4' )) { ?>
	<div class="widget-wrap">
		<div class="wrap">
			<div class="widget-area">
				<?php
				if($footer_column == '1' || $footer_column == '2' ||  $footer_column == '3' || $footer_column == '4'){
					echo '<div class="column-'.absint($footer_column).'">';
					if ( is_active_sidebar( 'shoppingcart_footer_1' ) ) :
						dynamic_sidebar( 'shoppingcart_footer_1' );
					endif;
					echo '</div><!-- end .column'.absint($footer_column). '  -->';
				}
				if($footer_column == '2' ||  $footer_column == '3' || $footer_column == '4'){
					echo '<div class="column-'.absint($footer_column).'">';
					if ( is_active_sidebar( 'shoppingcart_footer_2' ) ) :
						dynamic_sidebar( 'shoppingcart_footer_2' );
					endif;
					echo '</div><!--end .column'.absint($footer_column).'  -->';
				}
				if($footer_column == '3' || $footer_column == '4'){
					echo '<div class="column-'.absint($footer_column).'">';
					if ( is_active_sidebar( 'shoppingcart_footer_3' ) ) :
						dynamic_sidebar( 'shoppingcart_footer_3' );
					endif;
					echo '</div><!--end .column'.absint($footer_column).'  -->';
				}
				if($footer_column == '4'){
					echo '<div class="column-'.absint($footer_column).'">';
					if ( is_active_sidebar( 'shoppingcart_footer_4' ) ) :
						dynamic_sidebar( 'shoppingcart_footer_4' );
					endif;
					echo '</div><!--end .column'.absint($footer_column).'  -->';
				}
				?>
			</div> <!-- end .widget-area -->
		</div><!-- end .wrap -->
	</div> <!-- end .widget-wrap -->
<?php } ?>

<div class="site-info">
	<div class="wrap">
		<?php 
		// Footer menu en social links behouden
		do_action('shoppingcart_footer_menu');
		if($shoppingcart_settings['shoppingcart_buttom_social_icons'] == 0):
			do_action('shoppingcart_social_links');
		endif;

		// Dynamische copyright via Customizer
		if ( function_exists('get_theme_mod') ) {
			$footer_text = get_theme_mod('werdu_footer_text', '&copy; ' . date_i18n('Y') . ' <a href="' . esc_url( home_url('/') ) . '">' . esc_html( get_bloginfo('name') ) . '</a>. Alle Rechte vorbehalten.');
			echo '<div class="copyright">' . $footer_text . '</div>';
		} 
		?>
		<div style="clear:both;"></div>
	</div> <!-- end .wrap -->
</div> <!-- end .site-info -->

<?php
$disable_scroll = $shoppingcart_settings['shoppingcart_scroll'];
if($disable_scroll == 0): ?>
	<button type="button" class="go-to-top">
		<span class="screen-reader-text"><?php esc_html_e('Go to top','shoppingcart'); ?></span>
		<span class="icon-bg"></span>
		<span class="back-to-top-text"><i class="fa-solid fa-angle-up"></i></span>
		<i class="fa-solid fa-angles-up back-to-top-icon"></i>
	</button>
<?php endif; ?>

<div class="page-overlay"></div>
</footer> <!-- end #colophon -->
</div><!-- end .site-content-contain -->
</div><!-- end #page -->
<?php wp_footer(); ?>
</body>
</html>