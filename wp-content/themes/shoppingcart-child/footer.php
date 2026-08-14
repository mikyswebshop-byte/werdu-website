<?php
/**
 * The template for displaying the footer.
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 * @since ShoppingCart 1.0
 */

$shoppingcart_settings = shoppingcart_get_theme_options();
$wd_logo = content_url( '/uploads/2026/04/Logo-ACC-Heimspeicher_475_190.webp' );
?>
</div><!-- end #content -->

<style id="wd-footer-compact">
#colophon .wd-ft-brand{display:flex;flex-wrap:wrap;gap:1rem 1.25rem;align-items:center;max-width:1200px;margin:0 auto;padding:2rem 1.25rem 1.25rem;border-bottom:1px solid #e2e8f0}
#colophon .wd-ft-brand img{width:220px;max-width:100%;height:auto;display:block}
#colophon .wd-ft-tagline{margin:0 0 0.4rem;font-weight:800;font-size:1.05rem;color:#0f172a;line-height:1.35}
#colophon .wd-ft-lead{margin:0 0 0.4rem;color:#475569;font-size:0.95rem;line-height:1.4}
#colophon .wd-ft-close{margin:0;font-weight:700;color:#0f172a;font-size:0.95rem;line-height:1.4}
#colophon .wd-ft-trust{display:flex;flex-wrap:wrap;gap:1.25rem 2rem;max-width:1200px;margin:0 auto;padding:1.25rem;border-top:1px solid #e2e8f0}
#colophon .wd-ft-trust > div{flex:1 1 200px}
#colophon .wd-ft-trust h3{margin:0 0 0.75rem;font-size:0.95rem;font-weight:800;text-transform:uppercase;letter-spacing:.02em}
#colophon .wd-ft-trust p,#colophon .wd-ft-trust li{margin:0 0 0.4rem;line-height:1.4;color:#475569;font-size:0.92rem}
#colophon .wd-ft-trust ul{list-style:none;margin:0;padding:0}
#colophon .wd-ft-trust li{padding-left:1.1rem;position:relative}
#colophon .wd-ft-trust li::before{content:"\2713";position:absolute;left:0;color:#c2410c;font-weight:800;pointer-events:none}
@media (max-width:768px){
  #colophon .wd-ft-brand,#colophon .widget-wrap,#colophon .wd-ft-trust{padding-top:1.5rem;padding-bottom:1.5rem}
}
@media (prefers-reduced-motion:reduce){
  #colophon a{transition:none}
}
</style>

<footer id="colophon" class="site-footer" role="contentinfo">
	<div class="wd-ft-brand">
		<img src="<?php echo esc_url( $wd_logo ); ?>" alt="ACC Heimspeicher" width="475" height="190">
		<div>
			<p class="wd-ft-tagline">Intelligent Energie speichern. Maximal sparen.</p>
			<p class="wd-ft-lead">Mit hochwertigen Solarbatterien direkt vom Hersteller nutzt du Solarenergie effizienter und senkst deine Stromkosten dauerhaft.</p>
			<p class="wd-ft-close">Mehr Unabhängigkeit. Mehr Kontrolle. Mehr Ersparnis.</p>
		</div>
	</div>

	<nav aria-label="Footer">
	<div class="widget-wrap">
		<div class="wrap">
			<div class="widget-area">
				<?php
				for ( $i = 1; $i <= 4; $i++ ) {
					echo '<div class="column-4">';
					$sidebar_id = 'shoppingcart_footer_' . $i;
					if ( is_active_sidebar( $sidebar_id ) ) {
						dynamic_sidebar( $sidebar_id );
					} elseif ( function_exists( 'werdu_footer_column_markup' ) ) {
						echo '<aside class="widget"><h3 class="widget-title">' . esc_html( werdu_footer_column_title( $i ) ) . '</h3>';
						echo werdu_footer_column_markup( $i ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '</aside>';
					}
					echo '</div>';
				}
				?>
			</div>
		</div>
	</div>
	</nav>

	<div class="wd-ft-trust">
		<div>
			<h3>ACC Heimspeicher</h3>
			<p>35410 Hungen, Deutschland</p>
			<p>Telefon: <a href="tel:+4915120229842">+49 151 20229842</a></p>
			<p>E-Mail: <a href="mailto:service@werdu.de">service@werdu.de</a></p>
		</div>
		<div>
			<h3>Sicher einkaufen</h3>
			<ul>
				<li>Geprüfte Bestellabwicklung</li>
				<li>Transparente Kontakt</li>
				<li>Zuverlässige Beratung</li>
				<li>Ihre Zufriedenheit steht bei uns im Mittelpunkt</li>
			</ul>
		</div>
		<div>
			<h3>Unsere Stärke</h3>
			<ul>
				<li>Moderne LiFePO4 Technologie</li>
				<li>Hohe Qualität und Lebensdauer</li>
				<li>Effiziente Energiespeicherung</li>
			</ul>
		</div>
		<div>
			<h3>Wir akzeptieren</h3>
			<p>Visa, Mastercard, American Express, PayPal</p>
		</div>
	</div>

	<div class="site-info">
		<div class="wrap">
			<div class="copyright">
				<p>© 2026 ACC Heimspeicher - werdu.de</p>
				<p>Leistung und Zuverlässigkeit für Ihre Energieversorgung.</p>
				<p>EU-Streitbeilegung: Wir sind nicht verpflichtet, an Streitbeilegungsverfahren teilzunehmen.</p>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>

<?php
$disable_scroll = $shoppingcart_settings['shoppingcart_scroll'];
if ( $disable_scroll == 0 ) : ?>
	<button type="button" class="go-to-top">
		<span class="screen-reader-text"><?php esc_html_e( 'Go to top', 'shoppingcart' ); ?></span>
		<span class="icon-bg"></span>
		<span class="back-to-top-text"><i class="fa-solid fa-angle-up"></i></span>
		<i class="fa-solid fa-angles-up back-to-top-icon"></i>
	</button>
<?php endif; ?>

	<div class="page-overlay"></div>
	<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "Organization",
		"name": "ACC Heimspeicher",
		"url": "https://werdu.de",
		"logo": "<?php echo esc_url( $wd_logo ); ?>",
		"contactPoint": {
			"@type": "ContactPoint",
			"telephone": "+49-151-20229842",
			"contactType": "customer service",
			"availableLanguage": ["German"]
		},
		"address": {
			"@type": "PostalAddress",
			"streetAddress": "ACC Heimspeicher",
			"addressLocality": "Hungen",
			"postalCode": "35410",
			"addressCountry": "DE"
		}
	}
	</script>
</footer>
</div><!-- end .site-content-contain -->
</div><!-- end #page -->
<?php wp_footer(); ?>
</body>
</html>
