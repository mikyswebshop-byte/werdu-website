<?php
/**
 * Template Name: Werdu Homepage V2
 * Description: Editorial light landing page for Werdu.de (forced on the front page).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$whp_beratung = function_exists( 'werdu_home_seo_beratung_url' ) ? werdu_home_seo_beratung_url() : home_url( '/beratung-anfragen/' );
$whp_rechner  = function_exists( 'werdu_home_seo_rechner_url' ) ? werdu_home_seo_rechner_url() : home_url( '/solarbatterie-rechner/' );
$whp_shop     = home_url( '/shop/' );
$whp_up       = content_url( '/uploads/2026' );
$whp_hero     = $whp_up . '/06/Deutsches-Einfamilienhaus-mit-Solarmodulen-auf-dem-Dach_1046_783.webp';
$whp_hero_768 = $whp_up . '/06/Deutsches-Einfamilienhaus-mit-Solarmodulen-auf-dem-Dach_1046_783-768x575.webp';
$whp_prod_16  = $whp_up . '/07/16kwh-lifepo4-heimspeicher-hero_1024_1024.webp';
$whp_prod_30  = $whp_up . '/03/tewaycell-30kwh-32kwh-lifepo4-stromspeicher-batterie-51-2v-mobile-ess.webp';
$whp_prod_aio = $whp_up . '/04/All-in-One-Gross-1000-1000.webp';
$whp_home     = $whp_up . '/05/Heimspeicher-Zuhause-Plug-Play-1024x683.jpg';
$whp_logo     = $whp_up . '/08';

$whp_price = static function ( $id ) {
	$html = do_shortcode( '[werdu_preis id="' . sanitize_key( $id ) . '"]' );
	if ( '' === $html || '—' === $html ) {
		return '<a href="' . esc_url( home_url( '/shop/' ) ) . '">Preis im Shop anzeigen</a>';
	}
	return $html;
};
?>
<style id="whp-critical">
@font-face{font-display:swap!important}
body.home.boxed-layout #page{max-width:none!important;margin:0!important;box-shadow:none!important}
body.home,#content,.whp-page{background:#fff;color:#1a1a1a}
.whp-hero-lcp{display:block;width:100%;height:min(78vh,820px);object-fit:cover}
.whp-btn--primary{background:#ff6600;color:#fff;border:1px solid #ff6600}
</style>

<main id="whp-main" class="whp-page">

	<section class="whp-hero" id="whp-hero">
		<div class="whp-hero-frame">
			<img
				class="whp-hero-lcp"
				src="<?php echo esc_url( $whp_hero ); ?>"
				srcset="<?php echo esc_url( $whp_hero_768 ); ?> 768w, <?php echo esc_url( $whp_hero ); ?> 1046w"
				sizes="100vw"
				alt="Deutsches Einfamilienhaus mit Solarmodulen auf dem Dach"
				width="1046"
				height="783"
				fetchpriority="high"
				decoding="async"
			/>
			<div class="whp-hero-panel">
				<span class="whp-kicker">Deutschland · 0&nbsp;% MwSt.</span>
				<h1>PV-Speicher kaufen</h1>
				<p>LiFePO4-Solarbatterie mit sichtbarem Festpreis. Eigenverbrauch typischerweise von 20–30&nbsp;% auf 70–85&nbsp;% – ohne PDF-Angebot.</p>
				<div class="whp-actions">
					<a class="whp-btn whp-btn--primary" href="#whp-rechner">Kapazität berechnen</a>
					<a class="whp-btn whp-btn--ghost" href="<?php echo esc_url( $whp_shop ); ?>">Zum Shop</a>
				</div>
			</div>
		</div>
	</section>

	<section class="whp-intro whp-shell" aria-labelledby="whp-intro-title">
		<div>
			<span class="whp-kicker">Werdu</span>
			<h2 class="whp-display" id="whp-intro-title">Energie, die im Haus bleibt</h2>
		</div>
		<p class="whp-lede">Ein passend dimensionierter PV-Speicher verschiebt Mittagsstrom in den Abend. Sie kaufen zum Preis im Shop – Beratung ist optional, kein individuelles Angebot.</p>
	</section>

	<section id="whp-produkte" aria-label="PV-Speicher">
		<article class="whp-chapter">
			<figure class="whp-chapter-media">
				<img src="<?php echo esc_url( $whp_prod_16 ); ?>" alt="16 kWh LiFePO4 PV-Speicher Basen Green" width="1024" height="1024" loading="lazy" decoding="async" />
			</figure>
			<div class="whp-chapter-copy">
				<span class="whp-kicker">16 kWh</span>
				<h2>LiFePO4 PV-Speicher</h2>
				<p class="whp-meta">51,2&nbsp;V / 314&nbsp;Ah · Basen Green · 200&nbsp;A Dauerstrom gemäß Produktdaten</p>
				<p>Für klassische Eigenverbrauchshaushalte. Touchscreen, aktiver Balancer und App laut Herstellerangabe.</p>
				<span class="whp-price"><?php echo wp_kses_post( $whp_price( 'basen16kwh' ) ); ?></span>
				<div class="whp-actions">
					<a class="whp-btn whp-btn--primary" href="<?php echo esc_url( home_url( '/16-kwh-lifepo4-heimspeicher-51-2v-314ah/' ) ); ?>">Erfahren Sie mehr</a>
				</div>
			</div>
		</article>

		<article class="whp-chapter whp-chapter--flip">
			<figure class="whp-chapter-media">
				<img src="<?php echo esc_url( $whp_prod_30 ); ?>" alt="30–32 kWh LiFePO4 Speicher" width="400" height="400" loading="lazy" decoding="async" />
			</figure>
			<div class="whp-chapter-copy">
				<span class="whp-kicker">30–32 kWh</span>
				<h2>Speicher für hohen Bedarf</h2>
				<p class="whp-meta">51,2&nbsp;V · für E-Auto, Wärmepumpe, hohen Verbrauch</p>
				<p>Modulare Erweiterung je nach Modell. LiFePO4 mit hoher nutzbarer Entladetiefe laut Produktdaten.</p>
				<span class="whp-price"><?php echo wp_kses_post( $whp_price( '30kwh' ) ); ?></span>
				<div class="whp-actions">
					<a class="whp-btn whp-btn--primary" href="<?php echo esc_url( home_url( '/30-32-kwh-lifepo4-heimspeicher-560-628ah/' ) ); ?>">Erfahren Sie mehr</a>
				</div>
			</div>
		</article>

		<article class="whp-chapter">
			<figure class="whp-chapter-media">
				<img src="<?php echo esc_url( $whp_prod_aio ); ?>" alt="15 kWh All-in-One Solarbatterie mit 5 kW Hybrid-Wechselrichter" width="1000" height="1000" loading="lazy" decoding="async" />
			</figure>
			<div class="whp-chapter-copy">
				<span class="whp-kicker">15 kWh All-in-One</span>
				<h2>Solarbatterie mit Wechselrichter</h2>
				<p class="whp-meta">inkl. 5&nbsp;kW Hybrid-Wechselrichter</p>
				<p>Batterie und Hybrid-WR in einem System. Sinnvoll, wenn noch kein Hybrid-Wechselrichter vorhanden ist.</p>
				<span class="whp-price"><?php echo wp_kses_post( $whp_price( '15kwh_aio' ) ); ?></span>
				<div class="whp-actions">
					<a class="whp-btn whp-btn--primary" href="<?php echo esc_url( home_url( '/tewaycell-15-kwh-all-in-one-lifepo4-solarbatterie-5-kw-hybrid-wechselrichter/' ) ); ?>">Erfahren Sie mehr</a>
				</div>
			</div>
		</article>
	</section>

	<section class="whp-brands" aria-label="Wechselrichter">
		<div class="whp-shell">
			<p>Wechselrichter – Kombination immer anhand Datenblatt prüfen</p>
			<ul class="whp-logo-row">
				<li><img src="<?php echo esc_url( $whp_logo . '/sma_400_259.webp' ); ?>" alt="SMA" width="400" height="259" loading="lazy" decoding="async" /></li>
				<li><img src="<?php echo esc_url( $whp_logo . '/fornius_400_172.webp' ); ?>" alt="Fronius" width="400" height="172" loading="lazy" decoding="async" /></li>
				<li><img src="<?php echo esc_url( $whp_logo . '/Huawei_400_287.webp' ); ?>" alt="Huawei" width="400" height="287" loading="lazy" decoding="async" /></li>
				<li><img src="<?php echo esc_url( $whp_logo . '/sungrow_400_183.webp' ); ?>" alt="Sungrow" width="400" height="183" loading="lazy" decoding="async" /></li>
				<li><img src="<?php echo esc_url( $whp_logo . '/solaredge_400_136.webp' ); ?>" alt="SolarEdge" width="400" height="136" loading="lazy" decoding="async" /></li>
				<li><img src="<?php echo esc_url( $whp_logo . '/kostal_400_114.webp' ); ?>" alt="Kostal" width="400" height="114" loading="lazy" decoding="async" /></li>
				<li><img src="<?php echo esc_url( $whp_logo . '/enphase_400_87.webp' ); ?>" alt="Enphase" width="400" height="87" loading="lazy" decoding="async" /></li>
				<li><img src="<?php echo esc_url( $whp_logo . '/growatt_400_128.webp' ); ?>" alt="Growatt" width="400" height="128" loading="lazy" decoding="async" /></li>
				<li><img src="<?php echo esc_url( $whp_logo . '/goodwe_400_90.webp' ); ?>" alt="GoodWe" width="400" height="90" loading="lazy" decoding="async" /></li>
			</ul>
		</div>
	</section>

	<section class="whp-shell" id="whp-rechner">
		<div class="whp-calc">
			<div>
				<span class="whp-kicker">Autarkie-Rechner</span>
				<h2 class="whp-display">Welche Kapazität passt zu Ihrem Haus?</h2>
				<p class="whp-lede" style="margin-top:18px;">Das Ergebnis führt zur Beratung – nicht zu einem Preisangebot. Festpreise stehen im <a href="<?php echo esc_url( $whp_shop ); ?>">Shop</a>.</p>
			</div>
			<div id="werdu-calc-isolated">
				<form id="pv-calculator" class="whp-form" action="<?php echo esc_url( $whp_beratung ); ?>" method="get">
					<div class="whp-field">
						<label for="calc_location">Postleitzahl</label>
						<input id="calc_location" name="plz" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="5" placeholder="z. B. 80331" autocomplete="postal-code" />
					</div>
					<div class="whp-field">
						<label for="calc_verbrauch">Jahresverbrauch (kWh)</label>
						<input id="calc_verbrauch" name="verbrauch" type="text" inputmode="numeric" placeholder="z. B. 4500" required />
					</div>
					<div class="whp-field">
						<label for="calc_pv_leistung">PV-Leistung (kWp)</label>
						<input id="calc_pv_leistung" name="pv" type="text" inputmode="decimal" placeholder="z. B. 10" required />
					</div>
					<div class="whp-field">
						<label for="calc_plan">Bevorzugtes System</label>
						<select id="calc_plan" name="plan">
							<option value="16">16 kWh LiFePO4</option>
							<option value="32">30–32 kWh LiFePO4</option>
							<option value="15aio">15 kWh All-in-One</option>
						</select>
					</div>
					<div class="whp-field">
						<label for="calc_dachneigung">Dachneigung</label>
						<select id="calc_dachneigung" name="dach">
							<option value="0.94">flach (ca. 15°)</option>
							<option value="1" selected>optimal (ca. 30–35°)</option>
							<option value="0.96">steil (ca. 45°)</option>
							<option value="0.85">sehr steil (ca. 60°)</option>
						</select>
					</div>
					<div class="whp-field">
						<label for="calc_ausrichtung">Ausrichtung</label>
						<select id="calc_ausrichtung" name="ausrichtung">
							<option value="1" selected>Süd</option>
							<option value="0.92">Süd-Ost / Süd-West</option>
							<option value="0.78">Ost / West</option>
						</select>
					</div>
					<div class="whp-field whp-field--full">
						<label for="calc_email">E-Mail (optional)</label>
						<input id="calc_email" name="email" type="email" autocomplete="email" placeholder="name@beispiel.de" />
					</div>
					<div class="whp-field whp-field--full">
						<button type="submit" id="calc-submit" class="whp-btn whp-btn--primary whp-btn--block werdu-calc-btn">Kapazität berechnen</button>
					</div>
				</form>
				<div id="calc-result" class="whp-calc-result" hidden>
					<h3>Ihre Richtwerte</h3>
					<div class="whp-stats">
						<div><span>Kapazität</span><strong id="whp-res-kwh">–</strong></div>
						<div><span>Autarkie</span><strong id="whp-res-autarkie">–</strong></div>
						<div><span>Ersparnis</span><strong id="whp-res-spar">–</strong></div>
					</div>
					<p>Orientierung, keine verbindliche Auslegung. Als Nächstes übergeben wir die Werte an die Fachberatung.</p>
					<a class="whp-btn whp-btn--primary werdu-calc-cta whp-calc-cta" href="<?php echo esc_url( $whp_beratung ); ?>">Beratung anfordern</a>
				</div>
			</div>
		</div>
	</section>

	<section class="whp-tech" id="whp-vergleich">
		<figure class="whp-tech-media">
			<img src="<?php echo esc_url( $whp_home ); ?>" alt="PV-Speicher im Wohnhaus, Plug-and-Play-Aufstellung" width="1024" height="683" loading="lazy" decoding="async" />
		</figure>
		<div class="whp-tech-copy">
			<span class="whp-kicker">Technologie</span>
			<h2 class="whp-display">LiFePO4 als Standard</h2>
			<p class="whp-lede" style="margin-top:16px;">Sicherheit und Zyklenfestigkeit passen zur Nutzungsdauer einer PV-Anlage. NMC ist energiedichter, aber thermisch empfindlicher. Blei-Säure ist für diesen Einsatz veraltet.</p>
			<ul class="whp-facts">
				<li><span>Typische Zyklen</span><strong>6.000–8.000</strong></li>
				<li><span>Nutzungsdauer</span><strong>15–20 Jahre</strong></li>
				<li><span>Kobalt</span><strong>nein</strong></li>
				<li><span>Steuer</span><strong>0&nbsp;% MwSt.*</strong></li>
			</ul>
			<p class="whp-lede" style="margin-top:22px;font-size:0.9rem;">*Nach § 12 Abs. 3 UStG auf begünstigte PV-Anlagen und dazugehörige Speicher an Wohngebäuden.</p>
		</div>
	</section>

	<section class="whp-wissen whp-shell" id="whp-wissen">
		<div class="whp-wissen-head">
			<span class="whp-kicker">Wissen</span>
			<h2 class="whp-display">Kurz erklärt</h2>
		</div>
		<div class="whp-wissen-list">
			<article id="warum-pv-speicher">
				<h2>Warum ein PV-Speicher</h2>
				<p>Ohne Speicher bleiben oft nur 20–30&nbsp;% des Solarstroms im Haus. Mit passend dimensionierter Solarbatterie sind 70–85&nbsp;% Eigenverbrauch realistisch. Nachrüstung an bestehende PV-Anlagen ist üblich.</p>
			</article>
			<article id="autarkie-eigenverbrauch">
				<h2>Autarkie</h2>
				<p>Ziel ist ein hoher Jahresanteil selbst genutzten Stroms – nicht 100&nbsp;% Inselbetrieb. Fraunhofer ISE beschreibt den Sprung von rund 30&nbsp;% ohne Speicher auf bis etwa 80&nbsp;% mit Batterie. Notstrom braucht eine ausgewiesene Funktion.</p>
			</article>
			<article id="dimensionierung">
				<h2>Kapazität</h2>
				<p>Praxisregel: 1,0–1,5 kWh nutzbarer Speicher je 1.000 kWh Jahresverbrauch. 4.000 kWh liegen oft bei 5–8 kWh; mit E-Auto oder Wärmepumpe eher 12–16 kWh oder 30–32 kWh.</p>
			</article>
			<article id="wechselrichter">
				<h2>Nachrüstung</h2>
				<p>AC-gekoppelt lässt den vorhandenen Wechselrichter unangetastet. Hybrid und All-in-One bündeln PV und Batterie. Anschluss durch eine Elektrofachkraft; Stundensätze setzt der Betrieb. Siehe <a href="<?php echo esc_url( home_url( '/heimspeicher-installation/' ) ); ?>">Installation</a>.</p>
			</article>
			<article id="mwst-kosten">
				<h2>0&nbsp;% MwSt.</h2>
				<p>Seit 2023 gilt 0&nbsp;% auf begünstigte PV-Anlagen und dazugehörige Speicher an Wohngebäuden – Kauf und Installation, nicht jedes Zubehör. Einordnung: <a href="<?php echo esc_url( home_url( '/mwst-befreiung-eigenverbrauch/' ) ); ?>">MwSt-Befreiung</a>.</p>
			</article>
		</div>
	</section>

	<section class="whp-faq-sec whp-shell" id="faq-pv-speicher">
		<span class="whp-kicker">FAQ</span>
		<h2 class="whp-display">Fragen</h2>
		<div class="whp-faq">
			<?php
			if ( function_exists( 'werdu_home_seo_faq_data' ) ) {
				foreach ( werdu_home_seo_faq_data() as $i => $item ) {
					$open = ( 0 === $i ) ? ' open' : '';
					echo '<details' . $open . '>';
					echo '<summary>' . esc_html( $item['q'] ) . '</summary>';
					echo '<div class="whp-faq-a">' . wp_kses_post( $item['a'] ) . '</div>';
					echo '</details>';
				}
			}
			?>
		</div>
	</section>

	<section class="whp-close">
		<div class="whp-shell">
			<p class="whp-lede">Kapazität berechnen oder Festpreise im Shop ansehen.</p>
			<div class="whp-actions" style="margin-top:0;">
				<a class="whp-btn whp-btn--primary" href="#whp-rechner">Rechner</a>
				<a class="whp-btn whp-btn--ghost" href="<?php echo esc_url( $whp_beratung ); ?>">Beratung</a>
			</div>
		</div>
	</section>

</main>
<?php
if ( function_exists( 'werdu_home_seo_faq_json_ld' ) ) {
	echo werdu_home_seo_faq_json_ld(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
if ( function_exists( 'werdu_home_seo_software_json_ld' ) ) {
	echo werdu_home_seo_software_json_ld(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
get_footer();
