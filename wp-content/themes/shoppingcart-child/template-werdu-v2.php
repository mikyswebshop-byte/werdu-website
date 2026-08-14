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
$whp_hero     = home_url( '/wp-content/uploads/2026/08/pv-speicher-kaufen-modernes-deutsches-wohnhaus-alpen_1024_572.webp' );
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
.whp-hero-lcp{display:block;width:100%;height:auto;aspect-ratio:1024/572;object-fit:cover}
.whp-btn--primary{background:#ff6600;color:#fff;box-shadow:0 4px 14px rgba(224,85,0,.25);border:0;border-radius:10px;padding:15px 28px;font-weight:700;text-decoration:none;display:inline-flex}
@media(max-width:900px){.whp-hero-grid{grid-template-columns:1fr}}
</style>

<main id="whp-main" class="whp-page">

	<section class="whp-hero" id="whp-hero">
		<div class="whp-wrap whp-hero-grid">
			<div>
				<span class="whp-kicker">Deutschland · 0&nbsp;% MwSt. nach § 12 Abs. 3 UStG</span>
				<h1>Heimspeicher kaufen: LiFePO4-Stromspeicher mit transparenten Festpreisen</h1>
				<p class="whp-hero-text">Ein passend dimensionierter Heimspeicher hebt den Solar-Eigenverbrauch typischerweise von 20–30&nbsp;% auf 70–85&nbsp;%. Berechnen Sie Ihre Kapazität – und kaufen Sie zum sichtbaren Shop-Preis, ohne individuelles PDF-Angebot.</p>
				<ul class="whp-usps">
					<li><span class="whp-check" aria-hidden="true">✓</span> 0&nbsp;% MwSt. auf begünstigte PV-Speicher</li>
					<li><span class="whp-check" aria-hidden="true">✓</span> LiFePO4 mit 6.000–8.000 typischen Zyklen</li>
					<li><span class="whp-check" aria-hidden="true">✓</span> Kompatibel mit gängigen Hybrid-Wechselrichtern</li>
					<li><span class="whp-check" aria-hidden="true">✓</span> Optionaler Versand an einen Fachbetrieb vor Ort</li>
				</ul>
				<div class="whp-actions">
					<a class="whp-btn whp-btn--primary" href="#whp-rechner">Jetzt Heimspeicher berechnen</a>
					<a class="whp-btn whp-btn--ghost" href="<?php echo esc_url( $whp_beratung ); ?>">Kostenlose Beratung</a>
				</div>
			</div>
			<figure class="whp-hero-media">
				<img
					class="whp-hero-lcp"
					src="<?php echo esc_url( $whp_hero ); ?>"
					alt="Modernes Wohnhaus mit Photovoltaik und LiFePO4-Heimspeicher in Deutschland"
					width="1024"
					height="572"
					sizes="(max-width: 900px) 100vw, 540px"
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
						<input id="calc_location" name="plz" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="5" placeholder=" " title="z. B. 80331" autocomplete="postal-code" />
					</div>
					<div class="whp-field">
						<label for="calc_verbrauch">Jahresverbrauch (kWh)</label>
						<input id="calc_verbrauch" name="verbrauch" type="text" inputmode="numeric" placeholder=" " title="z. B. 4500" required />
					</div>
					<div class="whp-field">
						<label for="calc_pv_leistung">PV-Leistung (kWp)</label>
						<input id="calc_pv_leistung" name="pv" type="text" inputmode="decimal" placeholder=" " title="z. B. 10" required />
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
						<input id="calc_email" name="email" type="email" autocomplete="email" placeholder=" " title="name@beispiel.de" />
					</div>
					<div class="whp-field whp-field--full">
						<button type="submit" id="calc-submit" class="whp-btn whp-btn--primary whp-btn--block werdu-calc-btn">Jetzt Heimspeicher berechnen</button>
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
				<h2 class="whp-h2">Heimspeicher im direkten Vergleich</h2>
				<p class="whp-lead">Transparente Festpreise aus dem Preis-Manager. Keine versteckten Angebotsrunden.</p>
			</div>
			<div class="whp-grid">
				<article class="whp-card whp-card--hit">
					<span class="whp-badge">Bestseller</span>
					<div class="whp-card-img">
						<img src="<?php echo esc_url( $whp_prod_16 ); ?>" alt="16 kWh Basen Green LiFePO4 Heimspeicher" width="420" height="420" sizes="(max-width: 700px) 90vw, 315px" loading="lazy" decoding="async" />
					</div>
					<h3>16 kWh LiFePO4 Heimspeicher</h3>
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
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/tewaycell-30kwh-32kwh-lifepo4-stromspeicher-batterie-51-2v-mobile-ess.webp' ) ); ?>" alt="30-32 kWh LiFePO4 Heimspeicher" width="400" height="400" loading="lazy" decoding="async" />
					</div>
					<h3>30–32 kWh LiFePO4 Heimspeicher</h3>
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
				<article class="whp-card">
					<div class="whp-card-img">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/Tewaycell_48V_300Ah_15Kwh_Mobile_Haus_Solarspeicher_System_1.webp' ) ); ?>" alt="16 kWh LiFePO4 Solarbatterie" width="400" height="400" loading="lazy" decoding="async" />
					</div>
					<h3>16 kWh LiFePO4 Solarbatterie</h3>
					<p class="whp-sub">Grade-A Zellen · 8.000 Zyklen</p>
					<ul class="whp-specs">
						<li><span class="whp-check" aria-hidden="true">✓</span> LiFePO4, 48–51,2&nbsp;V-Klasse</li>
						<li><span class="whp-check" aria-hidden="true">✓</span> 8.000 Zyklen laut Produktdaten</li>
						<li><span class="whp-check" aria-hidden="true">✓</span> Für klassische Eigenverbrauchshaushalte</li>
					</ul>
					<div class="whp-card-cta">
						<span class="whp-price"><?php echo wp_kses_post( $whp_price( '16kwh' ) ); ?></span>
						<a class="whp-btn whp-btn--primary whp-btn--block" href="<?php echo esc_url( home_url( '/16-kwh-heimspeicher-lifepo4-solarbatterie/' ) ); ?>">Produkt ansehen</a>
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
							<th scope="row">Einsatz Heimspeicher</th>
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
				<h2>Inhaltsverzeichnis</h2>
				<ol>
					<li><a href="#warum-heimspeicher">Warum sich ein Heimspeicher lohnt</a></li>
					<li><a href="#autarkie-eigenverbrauch">Autarkie und Eigenverbrauch</a></li>
					<li><a href="#dimensionierung">Die richtige Kapazität</a></li>
					<li><a href="#wechselrichter">Wechselrichter und Nachrüstung</a></li>
					<li><a href="#mwst-kosten">0&nbsp;% MwSt., Kosten, Installation</a></li>
					<li><a href="#faq-heimspeicher">Häufige Fragen</a></li>
				</ol>
			</nav>

			<h2 id="warum-heimspeicher">Warum sich ein Heimspeicher 2026 lohnt</h2>
			<p>Ohne Speicher nutzen Eigenheimbesitzer typischerweise nur 20 bis 30 Prozent ihres Solarstroms selbst. Der Rest geht zu einer deutlich niedrigeren Einspeisevergütung ins Netz, während abends teurer Haushaltsstrom zurückgekauft wird. Ein LiFePO4-Heimspeicher verschiebt Mittagsüberschüsse in die Morgen- und Abendspitze und hebt den Eigenverbrauch auf 70 bis 85 Prozent – abhängig von Lastprofil, PV-Größe, Wärmepumpe und E-Auto.</p>
			<p>WERDU verkauft Stromspeicher zu transparenten Festpreisen im <a href="<?php echo esc_url( $whp_shop ); ?>">Shop</a>. Es gibt kein automatisches PDF-Angebot. Wer unsicher bei Zählerplatz, Phasenlage oder Notstrom ist, nutzt die <a href="<?php echo esc_url( $whp_beratung ); ?>">kostenlose Fachberatung</a> oder den ausführlichen <a href="<?php echo esc_url( $whp_rechner ); ?>">Autarkie-Rechner</a>. Vertiefung: <a href="<?php echo esc_url( home_url( '/solarbatterie-kaufen/' ) ); ?>">Solarbatterie kaufen</a>, <a href="<?php echo esc_url( home_url( '/solarbatterie-preise-transparente-kosten/' ) ); ?>">transparente Preise</a> und <a href="<?php echo esc_url( home_url( '/heimspeicher-kaufen-transparente-solarbatterie-preise/' ) ); ?>">Heimspeicher kaufen</a>.</p>
			<p>Die Nachrüstung an eine bestehende Photovoltaikanlage ist üblich. AC-gekoppelte Speicher lassen den vorhandenen Wechselrichter unangetastet. Hybrid-Systeme bündeln PV und Batterie auf der DC-Seite und brauchen einen kompatiblen Hybrid-Wechselrichter. All-in-One-Geräte bringen Batterie und Hybrid-WR in einem Gehäuse – sinnvoll, wenn noch kein Hybrid-Gerät vorhanden ist. Einen Überblick der Bauformen finden Sie unter <a href="<?php echo esc_url( home_url( '/heimspeicher-systeme/' ) ); ?>">Heimspeicher-Systeme</a> und <a href="<?php echo esc_url( home_url( '/all-in-one-heimspeicher-off-grid/' ) ); ?>">All-in-One / Off-Grid</a>.</p>

			<h2 id="autarkie-eigenverbrauch">Autarkie und Eigenverbrauch</h2>
			<p>Autarkie heißt nicht 100 Prozent Inselbetrieb. In Mitteleuropa bleibt ein Netzanschluss für Winterlücken sinnvoll. Ziel ist ein hoher Jahresanteil selbst genutzten Solarstroms. Das <a href="https://www.ise.fraunhofer.de" target="_blank" rel="noopener">Fraunhofer-Institut für Solare Energiesysteme (ISE)</a> hat den Eigenverbrauchs-Effekt von Speichern in Wohngebäuden mehrfach beschrieben: von rund 30 Prozent ohne Speicher auf bis zu etwa 80 Prozent mit passend dimensionierter Batterie.</p>
			<p>Gespeicherter Strom kann abends die Wallbox oder die Wärmepumpe speisen. Standard-Netzwechselrichter schalten bei Netzausfall ab (Inselnetzverhinderung). Notstrom oder Ersatzstrom braucht ausgewiesene Funktion und geeigneten Wechselrichter – das ist nicht dasselbe wie Off-Grid. Ratgeber: <a href="<?php echo esc_url( home_url( '/notstrom-heimspeicher-ersatzstrom-blackout/' ) ); ?>">Notstrom und Ersatzstrom</a>, <a href="<?php echo esc_url( home_url( '/energieautarkie-erreichen-unabhaengig-vom-stromnetz-2026-werdu-de/' ) ); ?>">Energieautarkie</a>, <a href="<?php echo esc_url( home_url( '/energieunabhaengigkeit/' ) ); ?>">Energieunabhängigkeit</a>.</p>
			<blockquote class="whp-quote">Marktdaten zu Photovoltaik und Speichern veröffentlicht die <a href="https://www.bundesnetzagentur.de" target="_blank" rel="noopener">Bundesnetzagentur</a>. Verbraucherschutz-Hinweise zur Photovoltaik bietet die <a href="https://www.verbraucherzentrale.de" target="_blank" rel="noopener">Verbraucherzentrale</a>.</blockquote>

			<h2 id="dimensionierung">Die richtige Kapazität</h2>
			<p>Als Praxisregel für Einfamilienhäuser gilt: pro 1.000 kWh Jahresverbrauch etwa 1,0 bis 1,5 kWh nutzbare Speicherkapazität, abgestimmt auf die kWp-Leistung der PV-Anlage. 4.000 kWh liegen häufig bei 5–8 kWh nutzbar; mit E-Auto oder Wärmepumpe eher bei 12–16 kWh oder der 30–32-kWh-Klasse. Zu klein bedeutet weiterhin Abendstrom vom Netz. Zu groß bedeutet unnötige Investition bei geringer Winterauslastung.</p>
			<p>Der Rechner auf dieser Seite und der <a href="<?php echo esc_url( $whp_rechner ); ?>">große Autarkie-Rechner</a> sowie die <a href="<?php echo esc_url( home_url( '/gratis-heimspeicher-rechner-online/' ) ); ?>">Gratis-Online-Variante</a> liefern die Größenordnung. Wirtschaftlichkeit: <a href="<?php echo esc_url( home_url( '/heimspeicher-kosten-pro-kwh/' ) ); ?>">Kosten pro kWh</a>, <a href="<?php echo esc_url( home_url( '/was-ein-lifepo4-heimspeicher-wirklich-kostet/' ) ); ?>">was ein LiFePO4-Heimspeicher kostet</a>. Entladetiefe: <a href="<?php echo esc_url( home_url( '/entladetiefe-tabelle/' ) ); ?>">DoD-Tabelle</a> und <a href="<?php echo esc_url( home_url( '/solarbatterie-wie-weit-entladen/' ) ); ?>">wie weit entladen</a>.</p>

			<h2 id="wechselrichter">Wechselrichter, Kompatibilität, Nachrüstung</h2>
			<p>Ob SMA, Victron, Deye, Growatt, GoodWe oder Solis: Die Kopplung hängt von Kommunikation (CAN/RS485), Spannungslage (typisch 48–51,2 V) und der Freigabe im Datenblatt des Wechselrichters ab. Wir listen gängige Hersteller als Orientierung – verbindlich ist immer die Kombination aus Batterie-BMS und WR-Firmware. All-in-One-Systeme mit integriertem Hybrid-WR umgehen diese Frage für den Batteriepfad, ersetzen aber nicht die fachgerechte Netzbindung.</p>
			<p>Installation ist Arbeit für eine Elektrofachkraft. Optional versenden wir an einen zertifizierten lokalen Installateur; Stundensätze setzt der Betrieb selbst. Ablauf: <a href="<?php echo esc_url( home_url( '/heimspeicher-installation/' ) ); ?>">Installation</a>, <a href="<?php echo esc_url( home_url( '/heimspeicher-installation-plug-play-anleitung-2026-werdu-de/' ) ); ?>">Plug-and-Play-Anleitung</a>, <a href="<?php echo esc_url( home_url( '/zahlung-und-lieferung/' ) ); ?>">Zahlung und Lieferung</a>, <a href="<?php echo esc_url( home_url( '/heimspeicher-versand-lieferbedingungen/' ) ); ?>">Versandbedingungen</a>. Auswahl auch an der <a href="<?php echo esc_url( home_url( '/kasse/' ) ); ?>">Kasse</a>.</p>

			<h2 id="mwst-kosten">0&nbsp;% MwSt., Sicherheit, Pflichten</h2>
			<p>Seit 2023 gilt nach § 12 Abs. 3 UStG 0&nbsp;% Umsatzsteuer auf begünstigte PV-Anlagen und dazugehörige Stromspeicher an Wohngebäuden – Kauf und Installation. Das ist keine pauschale Steuerfreiheit für jedes Zubehör. Einordnung: <a href="<?php echo esc_url( home_url( '/mwst-befreiung-eigenverbrauch/' ) ); ?>">MwSt-Befreiung Eigenverbrauch</a>. Förderprogramme ändern sich; Hinweise im Beitrag zur <a href="<?php echo esc_url( home_url( '/kfw-foerderung-heimspeicher-2026-bis-zu-15-zuschuss-werdu-de/' ) ); ?>">KfW-Förderung</a> – verbindlich sind die Regeln des jeweiligen Programms.</p>
			<p>LiFePO4 gilt als thermisch stabile Zellchemie. BMS überwacht Spannung, Strom, Temperatur und Ausgleich. Apps: <a href="<?php echo esc_url( home_url( '/kostenlose-apps-fuer-ihre-solarbatterie/' ) ); ?>">kostenlose Apps</a>, <a href="<?php echo esc_url( home_url( '/intelligente-energieoptimierung-fuer-solarbatterien-ems/' ) ); ?>">EMS</a>, <a href="<?php echo esc_url( home_url( '/intelligente-energiesteuerung-bms-app-technologie-2026-werdu-de/' ) ); ?>">BMS-Technologie</a>. Sicherheit und Zyklen: <a href="<?php echo esc_url( home_url( '/lifepo4-sicherheit-die-sicherste-solarbatterie-technologie-2026/' ) ); ?>">LiFePO4-Sicherheit</a>, <a href="<?php echo esc_url( home_url( '/wie-viele-zyklen-schafft-eine-solarbatterie-von-werdu-de/' ) ); ?>">Zyklen</a>, <a href="<?php echo esc_url( home_url( '/garantie/' ) ); ?>">Garantie</a>, <a href="<?php echo esc_url( home_url( '/achtung-garantie-richtige-lagerung-ihrer-lifepo4-batterie/' ) ); ?>">Lagerung</a>. Recht: <a href="<?php echo esc_url( home_url( '/batteriegesetz/' ) ); ?>">Batteriegesetz</a>, <a href="<?php echo esc_url( home_url( '/elektrog/' ) ); ?>">ElektroG</a>, <a href="<?php echo esc_url( home_url( '/entsorgung/' ) ); ?>">Entsorgung</a>, <a href="<?php echo esc_url( home_url( '/dachbrand-pv-anlage/' ) ); ?>">PV und Brandschutz</a>.</p>
			<p>Weitere Seiten: <a href="<?php echo esc_url( home_url( '/solarbatterien/' ) ); ?>">Solarbatterien</a>, <a href="<?php echo esc_url( home_url( '/solaranlage-mit-speicher-2026-pv-batterie-komplettsysteme/' ) ); ?>">Solaranlage mit Speicher</a>, <a href="<?php echo esc_url( home_url( '/faq-heimspeicher/' ) ); ?>">FAQ</a>, <a href="<?php echo esc_url( home_url( '/ueber-uns/' ) ); ?>">Über uns</a>, <a href="<?php echo esc_url( home_url( '/impressum/' ) ); ?>">Impressum</a>, <a href="<?php echo esc_url( home_url( '/agb/' ) ); ?>">AGB</a>, <a href="<?php echo esc_url( home_url( '/datenschutzerklaerung/' ) ); ?>">Datenschutz</a>.</p>
		</div>
	</section>

	<section class="whp-section" id="faq-heimspeicher">
		<div class="whp-wrap">
			<div class="whp-center">
				<span class="whp-kicker">FAQ</span>
				<h2 class="whp-h2">Häufig gestellte Fragen zum Heimspeicher</h2>
				<p class="whp-lead">Ausführliche Antworten für Google, ChatGPT Search und Perplexity – und für Ihre Kaufentscheidung.</p>
			</div>
			<div class="whp-faq">
				<?php
				if ( function_exists( 'werdu_home_seo_faq_data' ) ) {
					foreach ( werdu_home_seo_faq_data() as $i => $item ) {
						$open = ( 0 === $i ) ? ' open' : '';
						echo '<details' . $open . '>';
						echo '<summary>' . esc_html( $item['q'] ) . '</summary>';
						echo '<div class="whp-faq-a"><p>' . wp_kses_post( $item['a'] ) . '</p></div>';
						echo '</details>';
					}
				}
				?>
			</div>
			<div class="whp-cta-band" style="margin-top:40px;">
				<h2 class="whp-h2">Bereit für Ihren Heimspeicher?</h2>
				<p class="whp-lead">Berechnen Sie die Kapazität und lassen Sie sich kostenlos beraten. Die Festpreise sehen Sie jederzeit im Shop.</p>
				<div class="whp-actions" style="justify-content:center;">
					<a class="whp-btn whp-btn--primary" href="#whp-rechner">Jetzt Heimspeicher berechnen</a>
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
