<?php
/**
 * Template Name: Werdu Homepage V2
 * Description: Light-theme B2C landing page for Werdu.de (forced on the front page).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$whp_beratung = function_exists( 'werdu_home_seo_beratung_url' ) ? werdu_home_seo_beratung_url() : home_url( '/beratung-anfragen/' );
$whp_rechner  = function_exists( 'werdu_home_seo_rechner_url' ) ? werdu_home_seo_rechner_url() : home_url( '/solarbatterie-rechner/' );
$whp_shop     = home_url( '/shop/' );
$whp_hero     = get_stylesheet_directory_uri() . '/images/whp-hero-640.webp';
$whp_prod_16  = home_url( '/wp-content/uploads/2026/07/16kwh-lifepo4-heimspeicher-hero_1024_1024-420x420.webp' );

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
body.home,#content,.whp-page{background:#f8fafc;color:#0f172a}
.whp-hero-grid{display:grid;grid-template-columns:1.1fr 1fr;gap:48px;align-items:center}
.whp-hero h1{font-size:clamp(1.9rem,4vw,2.75rem);font-weight:800;line-height:1.15;margin:0 0 16px;color:#0f172a}
.whp-hero-lcp{display:block;width:100%;height:auto;aspect-ratio:640/358;object-fit:cover}
.whp-btn--primary{background:#ff6600;color:#fff;box-shadow:0 4px 14px rgba(224,85,0,.25);border:0;border-radius:10px;padding:15px 28px;font-weight:700;text-decoration:none;display:inline-flex}
@media(max-width:900px){.whp-hero-grid{grid-template-columns:1fr}}
</style>

<main id="whp-main" class="whp-page">

	<section class="whp-hero" id="whp-hero">
		<div class="whp-wrap whp-hero-grid">
			<div>
				<span class="whp-kicker">Deutschland · 0&nbsp;% MwSt. nach § 12 Abs. 3 UStG</span>
				<h1>PV-Speicher kaufen: LiFePO4-Solarbatterie mit transparenten Festpreisen</h1>
				<p class="whp-hero-text">Ein passend dimensionierter PV-Speicher hebt den Solar-Eigenverbrauch typischerweise von 20–30&nbsp;% auf 70–85&nbsp;%. Berechnen Sie Ihre Kapazität – und kaufen Sie zum sichtbaren Shop-Preis, ohne individuelles PDF-Angebot.</p>
				<ul class="whp-usps">
					<li><span class="whp-check" aria-hidden="true">✓</span> 0&nbsp;% MwSt. auf begünstigte PV-Speicher</li>
					<li><span class="whp-check" aria-hidden="true">✓</span> LiFePO4 mit 6.000–8.000 typischen Zyklen</li>
					<li><span class="whp-check" aria-hidden="true">✓</span> Kompatibel mit gängigen Hybrid-Wechselrichtern</li>
					<li><span class="whp-check" aria-hidden="true">✓</span> Optionaler Versand an einen Fachbetrieb vor Ort</li>
				</ul>
				<div class="whp-actions">
					<a class="whp-btn whp-btn--primary" href="#whp-rechner">Jetzt PV-Speicher berechnen</a>
					<a class="whp-btn whp-btn--ghost" href="<?php echo esc_url( $whp_beratung ); ?>">Kostenlose Beratung</a>
				</div>
			</div>
			<figure class="whp-hero-media">
				<img
					class="whp-hero-lcp"
					src="<?php echo esc_url( $whp_hero ); ?>"
					alt="Modernes Wohnhaus mit Photovoltaik und LiFePO4-PV-Speicher in Deutschland"
					width="640"
					height="358"
					fetchpriority="high"
					decoding="async"
				/>
				<div class="whp-hero-facts">
					<div><span>Steuervorteil</span><strong>0&nbsp;% MwSt.</strong></div>
					<div><span>Zellchemie</span><strong>LiFePO4</strong></div>
					<div><span>Autarkie</span><strong>bis 85&nbsp;%</strong></div>
					<div><span>Notstrom</span><strong>optional</strong></div>
				</div>
			</figure>
		</div>
	</section>

	<section class="whp-trust whp-section--tight" aria-label="Wechselrichter-Kompatibilität">
		<div class="whp-wrap">
			<p class="whp-trust-label">Unterstützte Hybrid-Wechselrichter (herstellerabhängig, bitte Datenblatt prüfen)</p>
			<ul class="whp-brands">
				<li>SMA</li>
				<li>Victron</li>
				<li>Deye</li>
				<li>Growatt</li>
				<li>GoodWe</li>
				<li>Solis</li>
			</ul>
		</div>
	</section>

	<section class="whp-section" id="whp-rechner">
		<div class="whp-wrap">
			<div class="whp-center">
				<span class="whp-kicker">Autarkie-Rechner</span>
				<h2 class="whp-h2">Welche Speicherkapazität passt zu Ihrem Haus?</h2>
				<p class="whp-lead">Geben Sie Verbrauch und PV-Leistung ein. Das Ergebnis führt zur kostenlosen Beratung – nicht zu einem automatischen Preisangebot. Festpreise stehen im <a href="<?php echo esc_url( $whp_shop ); ?>">Shop</a>.</p>
			</div>
			<div class="whp-calc-card" id="werdu-calc-isolated">
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
						<label for="calc_dachneigung">Dachneigung (Faktor)</label>
						<select id="calc_dachneigung" name="dach">
							<option value="0.94">flach (ca. 15°)</option>
							<option value="1" selected>optimal (ca. 30–35°)</option>
							<option value="0.96">steil (ca. 45°)</option>
							<option value="0.85">sehr steil (ca. 60°)</option>
						</select>
					</div>
					<div class="whp-field">
						<label for="calc_ausrichtung">Ausrichtung (Faktor)</label>
						<select id="calc_ausrichtung" name="ausrichtung">
							<option value="1" selected>Süd</option>
							<option value="0.92">Süd-Ost / Süd-West</option>
							<option value="0.78">Ost / West</option>
						</select>
					</div>
					<div class="whp-field whp-field--full">
						<label for="calc_email">E-Mail (optional, für die Beratung)</label>
						<input id="calc_email" name="email" type="email" autocomplete="email" placeholder="name@beispiel.de" />
					</div>
					<div class="whp-field whp-field--full">
						<button type="submit" id="calc-submit" class="whp-btn whp-btn--primary whp-btn--block werdu-calc-btn">Jetzt PV-Speicher berechnen</button>
					</div>
				</form>
				<div id="calc-result" class="whp-calc-result" hidden>
					<h3>Ihre Richtwerte</h3>
					<div class="whp-stats">
						<div><span>Empfohlene Kapazität</span><strong id="whp-res-kwh">–</strong></div>
						<div><span>Autarkiegrad (Richtwert)</span><strong id="whp-res-autarkie">–</strong></div>
						<div><span>Jährliche Ersparnis (Richtwert)</span><strong id="whp-res-spar">–</strong></div>
					</div>
					<p>Das ist eine Orientierung, keine verbindliche Auslegung. Im nächsten Schritt übergeben wir die Werte an die Fachberatung.</p>
					<a class="whp-btn whp-btn--primary werdu-calc-cta whp-calc-cta" href="<?php echo esc_url( $whp_beratung ); ?>">Kostenlose Beratung anfordern</a>
				</div>
			</div>
		</div>
	</section>

	<section class="whp-section" id="whp-produkte" style="padding-top:0;">
		<div class="whp-wrap">
			<div class="whp-center">
				<span class="whp-kicker">Shop</span>
				<h2 class="whp-h2">PV-Speicher im direkten Vergleich</h2>
				<p class="whp-lead">Transparente Festpreise aus dem Preis-Manager. Keine versteckten Angebotsrunden.</p>
			</div>
			<div class="whp-grid">
				<article class="whp-card whp-card--hit">
					<span class="whp-badge">Bestseller</span>
					<div class="whp-card-img">
						<img src="<?php echo esc_url( $whp_prod_16 ); ?>" alt="16 kWh Basen Green LiFePO4 PV-Speicher" width="420" height="420" sizes="(max-width: 700px) 90vw, 315px" loading="lazy" decoding="async" />
					</div>
					<h3>16 kWh LiFePO4 PV-Speicher</h3>
					<p class="whp-sub">51,2&nbsp;V / 314&nbsp;Ah · Basen Green</p>
					<ul class="whp-specs">
						<li><span class="whp-check" aria-hidden="true">✓</span> 200&nbsp;A Dauerstrom gemäß Produktdaten</li>
						<li><span class="whp-check" aria-hidden="true">✓</span> 10.000 Zyklen laut Herstellerangabe</li>
						<li><span class="whp-check" aria-hidden="true">✓</span> Touchscreen, aktiver Balancer, App</li>
					</ul>
					<div class="whp-card-cta">
						<span class="whp-price"><?php echo wp_kses_post( $whp_price( 'basen16kwh' ) ); ?></span>
						<a class="whp-btn whp-btn--primary whp-btn--block" href="<?php echo esc_url( home_url( '/16-kwh-lifepo4-heimspeicher-51-2v-314ah/' ) ); ?>">Produkt ansehen</a>
					</div>
				</article>
				<article class="whp-card whp-card--hit">
					<span class="whp-badge">Bestseller</span>
					<div class="whp-card-img">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/tewaycell-30kwh-32kwh-lifepo4-stromspeicher-batterie-51-2v-mobile-ess.webp' ) ); ?>" alt="30-32 kWh LiFePO4 PV-Speicher" width="400" height="400" loading="lazy" decoding="async" />
					</div>
					<h3>30–32 kWh LiFePO4 Speicher</h3>
					<p class="whp-sub">Maximale Autarkie · 51,2&nbsp;V</p>
					<ul class="whp-specs">
						<li><span class="whp-check" aria-hidden="true">✓</span> Für E-Auto, Wärmepumpe, hohen Verbrauch</li>
						<li><span class="whp-check" aria-hidden="true">✓</span> Modulare Erweiterung je nach Modell</li>
						<li><span class="whp-check" aria-hidden="true">✓</span> LiFePO4, hohe nutzbare Entladetiefe</li>
					</ul>
					<div class="whp-card-cta">
						<span class="whp-price"><?php echo wp_kses_post( $whp_price( '30kwh' ) ); ?></span>
						<a class="whp-btn whp-btn--primary whp-btn--block" href="<?php echo esc_url( home_url( '/30-32-kwh-lifepo4-heimspeicher-560-628ah/' ) ); ?>">Produkt ansehen</a>
					</div>
				</article>
				<article class="whp-card">
					<div class="whp-card-img">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/04/All-in-One-Gross-1000-1000.webp' ) ); ?>" alt="15 kWh All-in-One LiFePO4 mit 5 kW Hybrid-Wechselrichter" width="400" height="400" loading="lazy" decoding="async" />
					</div>
					<h3>15 kWh All-in-One</h3>
					<p class="whp-sub">inkl. 5&nbsp;kW Hybrid-Wechselrichter</p>
					<ul class="whp-specs">
						<li><span class="whp-check" aria-hidden="true">✓</span> Batterie und Hybrid-WR in einem System</li>
						<li><span class="whp-check" aria-hidden="true">✓</span> App-Steuerung, sofort einsatzbereit</li>
						<li><span class="whp-check" aria-hidden="true">✓</span> Ideal, wenn noch kein Hybrid-WR vorhanden ist</li>
					</ul>
					<div class="whp-card-cta">
						<span class="whp-price"><?php echo wp_kses_post( $whp_price( '15kwh_aio' ) ); ?></span>
						<a class="whp-btn whp-btn--primary whp-btn--block" href="<?php echo esc_url( home_url( '/tewaycell-15-kwh-all-in-one-lifepo4-solarbatterie-5-kw-hybrid-wechselrichter/' ) ); ?>">Produkt ansehen</a>
					</div>
				</article>
			</div>
		</div>
	</section>

	<section class="whp-section" id="whp-vergleich" style="padding-top:0;">
		<div class="whp-wrap">
			<div class="whp-center">
				<span class="whp-kicker">Vergleichsmatrix</span>
				<h2 class="whp-h2">LiFePO4 im Vergleich zu älteren Speichertechnologien</h2>
				<p class="whp-lead">Für den stationären Einsatz im Eigenheim ist Lithium-Eisenphosphat die etablierte Standardchemie – nicht weil sie die höchste Energiedichte hat, sondern weil Sicherheit und Zyklenfestigkeit zur Nutzungsdauer einer PV-Anlage passen.</p>
			</div>
			<div class="whp-table-wrap">
				<table class="whp-table">
					<caption>Vergleich LiFePO4, Natrium-Ionen, NMC und Blei-Säure</caption>
					<thead>
						<tr>
							<th scope="col">Kriterium</th>
							<th scope="col">LiFePO4</th>
							<th scope="col">Natrium-Ionen</th>
							<th scope="col">NMC</th>
							<th scope="col">Blei-Säure</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th scope="row">Typische Zyklen</th>
							<td><strong>6.000–8.000</strong></td>
							<td>3.000–5.000</td>
							<td>niedriger als LFP</td>
							<td>500–1.500</td>
						</tr>
						<tr>
							<th scope="row">Kalendarische Nutzung</th>
							<td><strong>15–20 Jahre</strong></td>
							<td>10–15 Jahre</td>
							<td>variabel</td>
							<td>5–8 Jahre</td>
						</tr>
						<tr>
							<th scope="row">Thermische Stabilität</th>
							<td><strong>hoch</strong></td>
							<td>gut</td>
							<td>empfindlicher</td>
							<td>mittel</td>
						</tr>
						<tr>
							<th scope="row">Kobalt in der Kathode</th>
							<td><strong>nein</strong></td>
							<td>nein</td>
							<td>ja</td>
							<td>nein</td>
						</tr>
						<tr>
							<th scope="row">Einsatz PV-Speicher</th>
							<td><strong>Standard</strong></td>
							<td>Alternative</td>
							<td>eher Mobil</td>
							<td>veraltet</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</section>

	<section class="whp-section" id="whp-wissen">
		<div class="whp-wrap whp-prose">
			<nav class="whp-toc" aria-label="Inhaltsverzeichnis">
				<h2>Kurzüberblick</h2>
				<ol>
					<li><a href="#warum-pv-speicher">Warum sich ein PV-Speicher lohnt</a></li>
					<li><a href="#autarkie-eigenverbrauch">Autarkie und Eigenverbrauch</a></li>
					<li><a href="#dimensionierung">Die richtige Kapazität</a></li>
					<li><a href="#wechselrichter">Wechselrichter und Nachrüstung</a></li>
					<li><a href="#mwst-kosten">0&nbsp;% MwSt. und Installation</a></li>
					<li><a href="#faq-pv-speicher">Häufige Fragen</a></li>
				</ol>
			</nav>

			<div class="whp-info-grid">
				<article class="whp-info-card" id="warum-pv-speicher">
					<h2>Warum sich ein PV-Speicher 2026 lohnt</h2>
					<p>Ohne Speicher bleiben oft nur 20–30&nbsp;% des Solarstroms im Haus. Mit passend dimensionierter Solarbatterie sind 70–85&nbsp;% Eigenverbrauch realistisch.</p>
					<ul>
						<li>Mittagsspitze wird in den Abend verschoben</li>
						<li>Festpreise im <a href="<?php echo esc_url( $whp_shop ); ?>">Shop</a> – kein PDF-Angebot</li>
						<li>Nachrüstung an bestehende PV-Anlagen ist üblich</li>
					</ul>
					<p><a href="<?php echo esc_url( $whp_rechner ); ?>">Kapazität berechnen</a> oder <a href="<?php echo esc_url( home_url( '/solarbatterie-kaufen/' ) ); ?>">Solarbatterie-Überblick</a>.</p>
				</article>

				<article class="whp-info-card" id="autarkie-eigenverbrauch">
					<h2>Autarkie und Eigenverbrauch</h2>
					<p>Autarkie heißt hoher Jahresanteil selbst genutzten Stroms – nicht 100&nbsp;% Inselbetrieb. Ein Netzanschluss bleibt in Mitteleuropa sinnvoll.</p>
					<ul>
						<li>Fraunhofer ISE: ohne Speicher rund 30&nbsp;%, mit Speicher bis etwa 80&nbsp;%</li>
						<li>Gespeicherter Strom für Haushalt, Wallbox oder Wärmepumpe</li>
						<li>Notstrom nur mit ausgewiesener Funktion und geeignetem Wechselrichter</li>
					</ul>
					<p><a href="<?php echo esc_url( home_url( '/notstrom-heimspeicher-ersatzstrom-blackout/' ) ); ?>">Notstrom und Ersatzstrom</a></p>
				</article>

				<article class="whp-info-card" id="dimensionierung">
					<h2>Die richtige Kapazität</h2>
					<p>Praxisregel: pro 1.000 kWh Jahresverbrauch etwa 1,0–1,5 kWh nutzbarer Speicher, abgestimmt auf die kWp-Leistung.</p>
					<ul>
						<li>4.000 kWh: häufig 5–8 kWh nutzbar</li>
						<li>E-Auto oder Wärmepumpe: eher 12–16 kWh oder 30–32 kWh</li>
						<li>Zu klein = Abendstrom vom Netz, zu groß = unnötige Winter-Reserve</li>
					</ul>
					<p><a href="<?php echo esc_url( $whp_rechner ); ?>">Autarkie-Rechner</a> · <a href="<?php echo esc_url( home_url( '/heimspeicher-kosten-pro-kwh/' ) ); ?>">Kosten pro kWh</a></p>
				</article>

				<article class="whp-info-card" id="wechselrichter">
					<h2>Wechselrichter und Nachrüstung</h2>
					<p>SMA, Victron, Deye, Growatt, GoodWe oder Solis: verbindlich sind Datenblatt, CAN/RS485 und 48–51,2&nbsp;V-Klasse – nicht der Markenname allein.</p>
					<ul>
						<li>AC-gekoppelt: vorhandener Wechselrichter bleibt</li>
						<li>Hybrid/All-in-One: Batterie und WR auf einem Pfad</li>
						<li>Anschluss durch Elektrofachkraft; Stundensatz setzt der Betrieb</li>
					</ul>
					<p><a href="<?php echo esc_url( home_url( '/heimspeicher-installation/' ) ); ?>">Installation</a> · <a href="<?php echo esc_url( home_url( '/all-in-one-heimspeicher-off-grid/' ) ); ?>">All-in-One / Off-Grid</a></p>
				</article>

				<article class="whp-info-card" id="mwst-kosten">
					<h2>0&nbsp;% MwSt., Sicherheit, Pflichten</h2>
					<p>Nach § 12 Abs. 3 UStG gilt 0&nbsp;% auf begünstigte PV-Anlagen und dazugehörige Speicher an Wohngebäuden – Kauf und Installation, nicht jedes Zubehör.</p>
					<ul>
						<li>LiFePO4: thermisch stabile Zellchemie, BMS überwacht den Betrieb</li>
						<li>Typisch 6.000–8.000 Zyklen und 15–20 Jahre Nutzungsdauer</li>
						<li>Batteriegesetz, ElektroG und fachgerechte Entsorgung gelten</li>
					</ul>
					<p><a href="<?php echo esc_url( home_url( '/mwst-befreiung-eigenverbrauch/' ) ); ?>">MwSt-Befreiung</a> · <a href="<?php echo esc_url( home_url( '/garantie/' ) ); ?>">Garantie</a></p>
				</article>

				<article class="whp-info-card">
					<h2>Nächste Schritte</h2>
					<p>Drei klare Wege – ohne Angebotsrunde und ohne versteckte Preise.</p>
					<ul>
						<li><a href="<?php echo esc_url( $whp_shop ); ?>">Festpreise im Shop</a></li>
						<li><a href="<?php echo esc_url( $whp_beratung ); ?>">Kostenlose Fachberatung</a></li>
						<li><a href="<?php echo esc_url( home_url( '/kostenlose-apps-fuer-ihre-solarbatterie/' ) ); ?>">Kostenlose Apps zur Solarbatterie</a></li>
					</ul>
				</article>
			</div>

			<blockquote class="whp-quote">Marktdaten zu Photovoltaik und Speichern veröffentlicht die <a href="https://www.bundesnetzagentur.de" target="_blank" rel="noopener">Bundesnetzagentur</a>. Verbraucherschutz-Hinweise zur Photovoltaik bietet die <a href="https://www.verbraucherzentrale.de" target="_blank" rel="noopener">Verbraucherzentrale</a>. Eigenverbrauchs-Effekte beschreibt das <a href="https://www.ise.fraunhofer.de" target="_blank" rel="noopener">Fraunhofer ISE</a>.</blockquote>
		</div>
	</section>

	<section class="whp-section" id="faq-pv-speicher">
		<div class="whp-wrap">
			<div class="whp-center">
				<span class="whp-kicker">FAQ</span>
				<h2 class="whp-h2">Häufige Fragen zum PV-Speicher</h2>
				<p class="whp-lead">Kurze Antworten für die Kaufentscheidung – Details klären Rechner und Beratung.</p>
			</div>
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
			<div class="whp-cta-band" style="margin-top:40px;">
				<h2 class="whp-h2">Bereit für Ihren PV-Speicher?</h2>
				<p class="whp-lead">Kapazität berechnen, kostenlos beraten lassen. Die Festpreise stehen jederzeit im Shop.</p>
				<div class="whp-actions" style="justify-content:center;">
					<a class="whp-btn whp-btn--primary" href="#whp-rechner">Jetzt PV-Speicher berechnen</a>
					<a class="whp-btn whp-btn--ghost" href="<?php echo esc_url( $whp_beratung ); ?>">Beratung anfragen</a>
				</div>
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
