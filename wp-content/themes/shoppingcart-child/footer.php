<?php
/**
 * Site footer — one layout for every page.
 *
 * @package Theme Freesia
 * @subpackage ShoppingCart
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shoppingcart_settings = shoppingcart_get_theme_options();
$wd_logo = content_url( '/uploads/2026/04/Logo-ACC-Heimspeicher_475_190.webp' );
$wd_home = home_url( '/' );
?>
</div><!-- end #content -->

<style id="wd-footer-css">
#colophon.wd-footer{background:#f8fafc;border-top:1px solid #e2e8f0;color:#0f172a;font-size:0.95rem;line-height:1.45}
#colophon.wd-footer .wd-ft-inner{max-width:1160px;margin:0 auto;padding:0 1.25rem}
#colophon.wd-footer .wd-ft-top{display:flex;align-items:center;justify-content:space-between;gap:1.25rem 2rem;flex-wrap:wrap;padding:2rem 0 1.5rem;border-bottom:1px solid #e2e8f0}
#colophon.wd-footer .wd-ft-brand{display:flex;align-items:center;gap:1.1rem;min-width:0}
#colophon.wd-footer .wd-ft-brand img{width:168px;max-width:42vw;height:auto;display:block}
#colophon.wd-footer .wd-ft-brand p{margin:0;color:#475569;font-size:0.95rem;max-width:36rem}
#colophon.wd-footer .wd-ft-cta{background:#ff6600;color:#fff!important;text-decoration:none!important;font-weight:700;border-radius:10px;padding:12px 22px;display:inline-flex;align-items:center;min-height:44px;white-space:nowrap}
#colophon.wd-footer .wd-ft-cta:hover{background:#e05500;color:#fff!important}
#colophon.wd-footer .wd-ft-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1.5rem 2rem;padding:1.75rem 0}
#colophon.wd-footer .wd-ft-grid h3{margin:0 0 0.85rem;font-size:0.8rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase;color:#0f172a}
#colophon.wd-footer .wd-ft-grid ul{list-style:none;margin:0;padding:0}
#colophon.wd-footer .wd-ft-grid li{margin:0 0 0.55rem}
#colophon.wd-footer .wd-ft-grid a{color:#334155;text-decoration:none;font-size:0.92rem}
#colophon.wd-footer .wd-ft-grid a:hover{color:#ff6600;text-decoration:underline}
#colophon.wd-footer .wd-ft-bottom{display:grid;grid-template-columns:1.2fr 1fr 1.3fr;gap:1rem 1.5rem;padding:1.25rem 0 1.5rem;border-top:1px solid #e2e8f0;color:#475569;font-size:0.85rem}
#colophon.wd-footer .wd-ft-bottom a{color:#ff6600;text-decoration:none}
#colophon.wd-footer .wd-ft-bottom a:hover{text-decoration:underline}
#colophon.wd-footer .wd-ft-bottom p{margin:0 0 0.35rem}
#colophon.wd-footer .wd-ft-bottom p:last-child{margin-bottom:0}
.werdu-footer,.wr5-footer,body footer:not(#colophon){display:none!important}
@media (max-width:900px){
  #colophon.wd-footer .wd-ft-grid,#colophon.wd-footer .wd-ft-bottom{grid-template-columns:1fr 1fr}
}
@media (max-width:640px){
  #colophon.wd-footer .wd-ft-top{align-items:flex-start}
  #colophon.wd-footer .wd-ft-grid,#colophon.wd-footer .wd-ft-bottom{grid-template-columns:1fr;padding-top:1.25rem}
}
@media (prefers-reduced-motion:reduce){
  #colophon.wd-footer a{transition:none}
}
</style>

<footer id="colophon" class="site-footer wd-footer" role="contentinfo">
	<div class="wd-ft-inner">
		<div class="wd-ft-top">
			<div class="wd-ft-brand">
				<a href="<?php echo esc_url( $wd_home ); ?>">
					<img src="<?php echo esc_url( $wd_logo ); ?>" alt="ACC Heimspeicher" width="475" height="190">
				</a>
				<p>LiFePO4 PV-Speicher mit transparenten Festpreisen – direkt im Shop, ohne Angebotsrunde.</p>
			</div>
			<a class="wd-ft-cta" href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Zum Shop</a>
		</div>

		<nav class="wd-ft-grid" aria-label="Fußzeile">
			<div>
				<h3>PV-Speicher</h3>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/16-kwh-lifepo4-heimspeicher-51-2v-314ah/' ) ); ?>">16 kWh LiFePO4 PV-Speicher</a></li>
					<li><a href="<?php echo esc_url( home_url( '/30-32-kwh-lifepo4-heimspeicher-560-628ah/' ) ); ?>">30–32 kWh LiFePO4 Speicher</a></li>
					<li><a href="<?php echo esc_url( home_url( '/tewaycell-15-kwh-all-in-one-lifepo4-solarbatterie-5-kw-hybrid-wechselrichter/' ) ); ?>">15 kWh All-in-One Solarbatterie</a></li>
					<li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Solarbatterien im Shop</a></li>
					<li><a href="<?php echo esc_url( home_url( '/solarbatterie-rechner/' ) ); ?>">PV-Speicher Rechner</a></li>
				</ul>
			</div>
			<div>
				<h3>Service</h3>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/zahlung-lieferung/' ) ); ?>">Zahlung und Lieferung</a></li>
					<li><a href="<?php echo esc_url( home_url( '/mwst-befreiung-eigenverbrauch/' ) ); ?>">0 % MwSt. Eigenverbrauch</a></li>
					<li><a href="<?php echo esc_url( home_url( '/garantie/' ) ); ?>">Garantie</a></li>
					<li><a href="<?php echo esc_url( home_url( '/widerrufsbelehrung/' ) ); ?>">Widerrufsbelehrung</a></li>
					<li><a href="<?php echo esc_url( home_url( '/ruecksendung/' ) ); ?>">Rücksendung</a></li>
				</ul>
			</div>
			<div>
				<h3>Wissen</h3>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/ueber-uns/' ) ); ?>">Über uns</a></li>
					<li><a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">Kontakt</a></li>
					<li><a href="<?php echo esc_url( home_url( '/heimspeicher-installation/' ) ); ?>">PV-Speicher Installation</a></li>
					<li><a href="<?php echo esc_url( home_url( '/lohnt-sich-ein-heimspeicher/' ) ); ?>">Lohnt sich ein PV-Speicher?</a></li>
					<li><a href="<?php echo esc_url( home_url( '/wie-viele-zyklen-schafft-eine-solarbatterie-von-werdu-de/' ) ); ?>">Zyklen einer Solarbatterie</a></li>
				</ul>
			</div>
			<div>
				<h3>Rechtliches</h3>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/agb-e-mail-version/' ) ); ?>">AGB</a></li>
					<li><a href="<?php echo esc_url( home_url( '/impressum/' ) ); ?>">Impressum</a></li>
					<li><a href="<?php echo esc_url( home_url( '/datenschutzerklaerung/' ) ); ?>">Datenschutzerklärung</a></li>
					<li><a href="<?php echo esc_url( home_url( '/cookie-richtlinie/' ) ); ?>">Cookie-Richtlinie</a></li>
					<li><a href="<?php echo esc_url( home_url( '/barrierefreiheitserklaerung/' ) ); ?>">Barrierefreiheit</a></li>
				</ul>
			</div>
		</nav>

		<div class="wd-ft-bottom">
			<div>
				<p><strong>ACC Heimspeicher</strong> · 35410 Hungen</p>
				<p>Tel. <a href="tel:+4915120229842">+49 151 20229842</a></p>
				<p><a href="mailto:service@werdu.de">service@werdu.de</a></p>
			</div>
			<div>
				<p><strong>Zahlung</strong></p>
				<p>Visa, Mastercard, American Express, PayPal</p>
			</div>
			<div>
				<p>© 2026 ACC Heimspeicher · werdu.de</p>
				<p>EU-Streitbeilegung: Wir sind nicht verpflichtet, an Streitbeilegungsverfahren teilzunehmen.</p>
			</div>
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
