<?php
/**
 * Template Name: Partner werden
 * Description: B2B Landing — PV Speicher Partner werden | Montage & Fachhändler | Rank Math SEO
 *
 * @package Shoppingcart_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$home_url = home_url( '/' );
$calc_url = home_url( '/solarbatterie-rechner/' );
$shop_url = home_url( '/shop/' );
$img_alt  = 'PV Speicher Partner werden - ACC Heimspeicher B2B';
$img_1    = 'https://werdu.de/wp-content/uploads/2026/07/16kwh-lifepo4-heimspeicher-hero_1024_1024.webp';
$img_2    = 'https://werdu.de/wp-content/uploads/2026/03/Tewaycell_48V_300Ah_15Kwh_Mobile_Haus_Solarspeicher_System_2.webp';
$img_3    = 'https://werdu.de/wp-content/uploads/2026/03/tewaycell-30kwh-32kwh-lifepo4-stromspeicher-batterie-51-2v-mobile-ess.webp';
?>

<main id="werdu-partner" class="wp-partner-seo" itemscope itemtype="https://schema.org/WebPage">
	<header class="wp-hero">
		<div class="wp-wrap">
			<div class="wp-badge">B2B Partnernetzwerk · Deutschlandweit · 2026</div>
			<h1 itemprop="headline">PV Speicher Partner werden bei ACC Heimspeicher / WERDU.de</h1>
			<p class="wp-hero-lead" itemprop="description">
				Sie möchten PV Speicher Partner werden und mit transparenten LiFePO4-Heimspeichern skalieren?
				WERDU.de bietet Elektrikern, Montagebetrieben und Fachhändlern klare Konditionen, regionale Leads
				über unsere PLZ-Rechner sowie Lieferung aus deutschem Lager — ohne undurchsichtige Projektpreise.
			</p>
			<div class="wp-hero-cta">
				<a class="wp-btn wp-btn-primary" href="#anmeldung">Jetzt Partner werden</a>
				<a class="wp-btn wp-btn-secondary" href="<?php echo esc_url( $calc_url ); ?>">Solarbatterie-Rechner öffnen</a>
			</div>
		</div>
	</header>

	<div class="wp-wrap">
		<nav class="partner-toc" aria-label="Inhaltsverzeichnis">
			<h3>📑 Inhaltsverzeichnis</h3>
			<ul>
				<li><a href="#vorteile">Ihre Vorteile als PV Speicher Partner</a></li>
				<li><a href="#vergleich">Vergleich: ACC vs. Andere Hersteller</a></li>
				<li><a href="#schritte">In 3 Schritten Partner werden</a></li>
				<li><a href="#anmeldung">Partner-Formular</a></li>
				<li><a href="#faq">Häufig gestellte Fragen (FAQ)</a></li>
			</ul>
		</nav>

		<section class="wp-prose" aria-labelledby="intro-title">
			<h2 id="intro-title">Warum Installateure 2026 PV Speicher Partner werden</h2>
			<p>
				Der Markt für Heimspeicher wächst weiter — und wer jetzt PV Speicher Partner werden möchte,
				braucht mehr als nur ein Produktblatt. Entscheidend sind Marge, Lieferfähigkeit, technische Klarheit
				und planbare Aufträge in der eigenen Region. Genau darauf ist das Partnernetzwerk von ACC Heimspeicher
				auf <a href="<?php echo esc_url( $home_url ); ?>">werdu.de</a> ausgelegt.
			</p>
			<p>
				Statt undurchsichtiger Projektkalkulationen sehen Endkunden transparente Preise im Shop.
				Sie als Betrieb konzentrieren sich auf fachgerechte Montage, Beratung und Service —
				während wir Verfügbarkeit, Versandlogistik und digitale Lead-Qualifizierung übernehmen.
			</p>
			<p>
				Ob Sie Montage-Partner (typisch 300 € – 500 € pro Anschluss) oder Fachpartner &amp; Händler
				(B2B-Einkauf ab 3 Einheiten) werden: Die Konditionen sind klar formuliert und auf den deutschen
				Alltag von Meisterbetrieben zugeschnitten.
			</p>
		</section>

		<section class="wp-cards" aria-label="Partner-Modelle" id="partner-modelle">
			<article class="wp-card wp-card--montage" data-wp-select-type="montage" tabindex="0" role="button" aria-label="Montage-Partner auswählen">
				<span class="wp-card-tag">Installation vor Ort</span>
				<h2>Montage-Partner</h2>
				<p class="wp-card-price">300 € – 500 € pro Anschluss</p>
				<p>
					Als zertifizierter Installateur übernehmen Sie die fachgerechte Montage beim Endkunden —
					inkl. DC-Anbindung und Datenkabel (CAN/RS485). Lieferung kann direkt an Ihren Betrieb erfolgen.
				</p>
				<ul class="wp-list">
					<li>Regionale Aufträge im gewünschten Radius</li>
					<li>Technische Support-Linie &amp; Montageleitfäden</li>
					<li>Transparente Produktpreise für Ihre Kundenberatung</li>
					<li>Richtwert 300 € – 500 € Montageaufwand direkt mit dem Endkunden</li>
				</ul>
			</article>

			<article class="wp-card wp-card--b2b" data-wp-select-type="b2b" tabindex="0" role="button" aria-label="Fachpartner &amp; Händler auswählen">
				<span class="wp-card-tag">Handel &amp; Wiederverkauf</span>
				<h2>Fachpartner &amp; Händler</h2>
				<p class="wp-card-price">B2B-Einkauf ab 3 Einheiten</p>
				<p>
					Als Fachhändler oder Systemintegrator beziehen Sie WERDU-Heimspeicher zu Partnerkonditionen
					und verkaufen sie mit eigenen Services an Ihre Kundschaft weiter.
				</p>
				<ul class="wp-list">
					<li>Händlerkonditionen ab 3 Einheiten</li>
					<li>Schnelle Verfügbarkeit &amp; Direktversand</li>
					<li>Marketing-Material &amp; Produkt-Datenblätter</li>
					<li>Direkter Ansprechpartner im Partner-Support</li>
				</ul>
			</article>
		</section>

		<section class="wp-prose" id="marktanalyse" aria-labelledby="markt-title">
			<h2 id="markt-title">Marktanalyse 2026: Warum jetzt PV Speicher Partner werden sinnvoll ist</h2>
			<p>
				Haushalte investieren verstärkt in Eigenverbrauch, Wärmepumpe und E-Mobilität.
				Damit steigt der Bedarf an zuverlässigen LiFePO4-Speichern — und an Betrieben, die Montage,
				Inbetriebnahme und After-Sales lokal abdecken. Wer PV Speicher Partner werden will,
				positioniert sich genau in diesem Engpass.
			</p>
			<p>
				Gleichzeitig sind Endkunden preissensibler geworden. Undurchsichtige „Komplettpakete“ ohne
				nachvollziehbare Speicherpreise verlieren Vertrauen. Transparente Shop-Preise plus klare
				Montageleistungen vor Ort sind 2026 der überzeugendere Weg.
			</p>
			<p>
				Für Installateure bedeutet das: weniger Zeit in Preisverteidigung, mehr Zeit in fachliche Beratung.
				Mit ACC Heimspeicher / WERDU.de erhalten Sie Produkte, die sich im <a href="<?php echo esc_url( $shop_url ); ?>">Shop</a>
				offen kalkulieren lassen — und Leads, die bereits über unseren
				<a href="<?php echo esc_url( $calc_url ); ?>">Solarbatterie-Rechner</a> vorqualifiziert wurden.
			</p>
			<p>
				Branchenseitig bleibt die politische und verbandliche Einordnung wichtig.
				Aktuelle Marktdaten und Rahmenbedingungen finden Sie beim
				<a href="https://www.bsw-solar.de/" target="_blank" rel="noopener dofollow">Bundesverband Solarwirtschaft (BSW)</a>.
				Dort erkennen Sie, warum Speicher und Sektorenkopplung strukturell wachsen — und warum
				regionale Partnernetzwerke entscheidend bleiben.
			</p>

			<figure class="wp-media">
				<img
					src="<?php echo esc_url( $img_1 ); ?>"
					alt="<?php echo esc_attr( $img_alt ); ?>"
					width="800"
					height="800"
					loading="lazy"
					decoding="async"
				/>
				<figcaption>16 kWh LiFePO4 Heimspeicher — Partnerprodukt für Montage &amp; B2B-Handel</figcaption>
			</figure>
		</section>

		<section class="wp-prose" id="vorteile" aria-labelledby="vorteile-title">
			<h2 id="vorteile-title">Ihre Vorteile als PV Speicher Partner</h2>
			<p>
				Viele Hersteller versprechen Partnerschaft — wenige liefern Betriebstauglichkeit.
				Wenn Sie PV Speicher Partner werden, zählen konkrete Vorteile im Alltag:
				Marge, Lieferzeit, Garantie und erreichbarer Support.
			</p>

			<h3>Bis zu 35 % Marge im B2B-Modell</h3>
			<p>
				Fachpartner &amp; Händler profitieren von Einkaufskonditionen ab 3 Einheiten.
				Je nach Produktlinie und Volumen sind Margen von bis zu 35 % realistisch —
				ohne versteckte Listungsgebühren oder undurchsichtige Bonusmodelle.
			</p>
			<p>
				Montage-Partner verdienen zusätzlich über die lokale Anschlussleistung.
				Der Markt-Richtwert (Juli 2026) für DC- &amp; Kommunikationsanschluss liegt bei ca. 300 € – 500 € —
				Abrechnung direkt mit dem Endkunden, klar kommunizierbar.
			</p>

			<h3>100 % deutsches Lager &amp; Lieferung innerhalb von 48 Stunden</h3>
			<p>
				Verfügbarkeit entscheidet über Abschluss. Deshalb setzen wir auf Lagerkapazität in Deutschland
				und schnelle Versandwege. Viele Positionen sind so kurzfristig lieferbar, dass Sie Projekte
				nicht wochenlang „auf Halde“ planen müssen.
			</p>
			<p>
				Für Sie heißt das: weniger Storno-Risiko, bessere Terminplanung und eine glaubwürdige Aussage
				gegenüber dem Kunden — „lieferbar“ statt „irgendwann aus Übersee“.
			</p>

			<h3>10 Jahre Garantie auf LiFePO4-Technologie</h3>
			<p>
				LiFePO4 steht für thermische Stabilität und hohe Zyklenfestigkeit.
				Unsere Systeme sind auf Langzeitbetrieb ausgelegt; die 10-jährige Garantie gibt Endkunden
				und Installateuren Planungssicherheit. Das reduziert Reklamationsaufwand und stärkt Ihre Beratung.
			</p>

			<h3>24/7 deutschsprachige B2B-Support-Linie</h3>
			<p>
				Technikfragen entstehen nicht nur von 9 bis 17 Uhr. Partner erhalten Zugang zu deutschsprachigem
				B2B-Support — inklusive Montagehinweisen zu Plus/Minus-Verkabelung und BMS-Kommunikation
				(typischerweise CAN oder RS485).
			</p>

			<h3>Exklusive regionale Leads über PLZ-Rechner</h3>
			<p>
				Unsere Online-Rechner auf der <a href="<?php echo esc_url( $home_url ); ?>">Startseite</a>
				und unter <a href="<?php echo esc_url( $calc_url ); ?>">/solarbatterie-rechner/</a>
				qualifizieren Interessenten nach PLZ, Verbrauch und PV-Leistung.
				Als Partner können Sie von regionalen Anfragen profitieren — statt kalt zu akquirieren.
			</p>

			<figure class="wp-media">
				<img
					src="<?php echo esc_url( $img_2 ); ?>"
					alt="<?php echo esc_attr( $img_alt ); ?>"
					width="800"
					height="800"
					loading="lazy"
					decoding="async"
				/>
				<figcaption>Mobile ESS Heimspeicher — geeignet für bestehende PV-Anlagen und Erweiterungen</figcaption>
			</figure>
		</section>

		<section class="wp-prose" id="vergleich" aria-labelledby="vergleich-title">
			<h2 id="vergleich-title">Vergleich: ACC vs. Andere Hersteller</h2>
			<p>
				Bevor Sie PV Speicher Partner werden, lohnt ein nüchterner Vergleich.
				Nicht der lauteste Marketingclaim zählt — sondern Konditionen, die im Betrieb funktionieren.
			</p>

			<div class="wp-table-wrap" role="region" aria-label="Partner-Vergleichstabelle" tabindex="0">
				<table class="wp-compare-table">
					<thead>
						<tr>
							<th scope="col">Kriterium</th>
							<th scope="col">ACC Heimspeicher / WERDU.de</th>
							<th scope="col">Typischer Markt / Andere Hersteller</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th scope="row">Preistransparenz</th>
							<td>Shop-Preise öffentlich &amp; nachvollziehbar</td>
							<td>Oft nur Projektangebote / undurchsichtige Pakete</td>
						</tr>
						<tr>
							<th scope="row">Partner-Marge</th>
							<td>Bis zu 35 % im B2B-Modell (ab 3 Einheiten)</td>
							<td>Häufig intransparent, volumenabhängig ohne Klarheit</td>
						</tr>
						<tr>
							<th scope="row">Lager &amp; Lieferzeit</th>
							<td>Deutsches Lager, häufig Versand binnen 48 h</td>
							<td>Lange Seewege / unklare ETA</td>
						</tr>
						<tr>
							<th scope="row">Garantie</th>
							<td>10 Jahre auf LiFePO4-Systeme</td>
							<td>5–10 Jahre, teils mit engen Ausschlüssen</td>
						</tr>
						<tr>
							<th scope="row">Support</th>
							<td>Deutschsprachiger B2B-Support</td>
							<td>Ticket-Systeme, Sprachbarrieren, lange Wartezeiten</td>
						</tr>
						<tr>
							<th scope="row">Lead-Qualität</th>
							<td>PLZ-Rechner &amp; vorqualifizierte Beratung</td>
							<td>Kaltakquise oder unqualifizierte Anfragen</td>
						</tr>
						<tr>
							<th scope="row">Montage-Modell</th>
							<td>Optional lokal, Richtwert 300–500 € Anschluss</td>
							<td>Oft Zwangspakete mit hoher Montagepauschale</td>
						</tr>
					</tbody>
				</table>
			</div>

			<p>
				Kurz gesagt: Wer PV Speicher Partner werden möchte und gleichzeitig glaubwürdige Preise
				gegenüber Endkunden vertreten will, braucht Transparenz. Genau das ist unser Differenzierungsmerkmal.
			</p>
		</section>

		<section class="wp-prose" id="psychologie" aria-labelledby="psycho-title">
			<h2 id="psycho-title">Warum Sie jetzt PV Speicher Partner werden sollten</h2>
			<p>
				Gute Partnerschaften entstehen nicht aus Druck — sondern aus nachvollziehbarem Nutzen.
				Wenn Ihr Betrieb freie Montagekapazität hat oder B2B-Lagerumschlag sucht, ist der Zeitpunkt 2026
				besonders günstig: Nachfrage ist da, Kunden vergleichen aggressiv, und wer liefern kann, gewinnt.
			</p>
			<p>
				Psychologisch entscheidend ist Sicherheit. Sie wollen wissen, dass Nachbestellungen ankommen,
				Garantiefragen geklärt werden und Sie im Störungsfall nicht allein dastehen.
				Deshalb kommunizieren wir Konditionen schriftlich klar — und halten Support erreichbar.
			</p>
			<p>
				Ebenso wichtig: Kontrolle. Sie behalten die Kundenbeziehung vor Ort.
				Wir liefern Produkt, Logistik und digitale Vorqualifizierung.
				So bleibt Ihr Betrieb das Gesicht vor dem Kunden — mit Technik, die hinter den Versprechen steht.
			</p>

			<figure class="wp-media">
				<img
					src="<?php echo esc_url( $img_3 ); ?>"
					alt="<?php echo esc_attr( $img_alt ); ?>"
					width="800"
					height="800"
					loading="lazy"
					decoding="async"
				/>
				<figcaption>30–32 kWh LiFePO4 Speicher — für hohe Autarkieziele und Gewerbe-nahe Anwendungen</figcaption>
			</figure>
		</section>

		<section class="wp-prose" id="technik" aria-labelledby="technik-title">
			<h2 id="technik-title">Technik-Klarheit für Betriebe, die PV Speicher Partner werden</h2>
			<p>
				Installateure scheitern selten am Willen — häufiger an unklarer Dokumentation.
				Deshalb liefern wir praxisnahe Hinweise zu DC-Verkabelung, BMS-Kommunikation und Inbetriebnahme.
				Plug-&amp;-Play bedeutet nicht „beliebig“, sondern „klar strukturiert“.
			</p>
			<p>
				Typischer Ablauf vor Ort: Positionierung, Plus/Minus-Anschluss an Hybrid- oder Batteriewechselrichter,
				Datenkabel setzen, App/EMS prüfen, Schutz- und Betriebsparameter dokumentieren.
				Für viele Haushalte ist das in einem kompakten Zeitfenster realistisch — wenn Vorbereitung stimmt.
			</p>
			<p>
				Für Fachpartner im Handel gilt: Sie brauchen Datenblätter, die Einkauf und Verkauf tragen.
				UVP, technische Specs und Garantiebedingungen müssen ohne Rückfragen erklärbar sein.
				Genau dafür stellen wir Partner-Material bereit.
			</p>
		</section>

		<section class="wp-process" id="schritte" aria-labelledby="schritte-title">
			<h2 id="schritte-title">In 3 Schritten Partner werden</h2>
			<p class="wp-section-lead">
				Der Weg, um PV Speicher Partner werden zu können, ist bewusst kurz gehalten —
				ohne Bewerbungsmarathon, aber mit sauberer Regions- und Kapazitätsprüfung.
			</p>
			<div class="wp-steps">
				<div class="wp-step">
					<div class="wp-step-num">1</div>
					<h3>Anfrage senden</h3>
					<p>Firma, PLZ, Radius und Partnerschafts-Typ — in unter zwei Minuten erledigt.</p>
				</div>
				<div class="wp-step">
					<div class="wp-step-num">2</div>
					<h3>Region &amp; Kapazität prüfen</h3>
					<p>Wir prüfen Abdeckung, Radius und passende Auftragsarten in Ihrem Einsatzgebiet.</p>
				</div>
				<div class="wp-step">
					<div class="wp-step-num">3</div>
					<h3>Freischaltung &amp; Start</h3>
					<p>Onboarding mit Konditionen, Support und — bei Montage — regionaler Auftragszuweisung.</p>
				</div>
			</div>
		</section>

		<section class="wp-prose" id="konditionen" aria-labelledby="konditionen-title">
			<h2 id="konditionen-title">Klare Konditionen: So arbeiten PV-Speicher-Partner mit WERDU.de</h2>
			<p>
				Partnerschaft funktioniert nur mit Regeln, die beide Seiten respektieren.
				Wer PV Speicher Partner werden will, sollte wissen: Wir erwarten fachliche Sorgfalt,
				saubere Kundenkommunikation und Einhaltung geltender Sicherheitsstandards.
			</p>
			<p>
				Im Gegenzug erhalten Sie planbare Einkaufskonditionen, dokumentierte Produkte und
				Zugang zu digitalen Tools, die Beratung beschleunigen — vom Rechner bis zur Beratungsseite.
			</p>
			<p>
				Wichtig für Montage-Partner: Die Installationspauschale ist kein Shop-Zwangszuschlag.
				Sie vereinbaren Leistung und Preis direkt mit dem Endkunden.
				Das hält Verantwortlichkeiten klar und verhindert Missverständnisse in der Abrechnung.
			</p>
			<p>
				Für Händler: Ab 3 Einheiten starten B2B-Konditionen. Größere Abrufe lassen sich staffeln.
				Ziel ist ein gesunder Lagerumschlag — nicht Überbevorratung ohne Nachfrage.
			</p>
		</section>

		<section class="wp-form-section" id="anmeldung" aria-labelledby="partner-form-title">
			<h2 id="partner-form-title">Partner-Formular: Jetzt PV Speicher Partner werden</h2>
			<p class="wp-form-intro">
				Kurz ausfüllen — wir prüfen Verfügbarkeit und Konditionen in Ihrer Region und melden uns zeitnah.
				Mit dem Absenden starten Sie den Prozess, um PV Speicher Partner werden zu können.
			</p>

			<div class="wp-geo" data-wp-geo aria-hidden="true" role="status">
				<span class="wp-geo-icon" aria-hidden="true">PLZ</span>
				<div>
					Verfügbarkeit prüfen für PLZ-Bereich
					<span data-wp-plz>—</span>
					(<span data-wp-city>—</span> &amp; Großraum)
				</div>
			</div>

			<?php
			if ( function_exists( 'werdu_partner_render_form' ) ) {
				werdu_partner_render_form();
			}
			?>
		</section>

		<section class="wp-prose" id="onboarding" aria-labelledby="onboarding-title">
			<h2 id="onboarding-title">Onboarding, Compliance und Alltag im Partnernetzwerk</h2>
			<p>
				Nach der Freischaltung beginnt die eigentliche Arbeit: Prozesse, die im Betrieb wiederholbar sind.
				Dazu gehören Checklisten für Anlieferung, Sichtprüfung, Anschlussreihenfolge und Dokumentation.
				Je standardisierter Ihr Team arbeitet, desto weniger Rückfragen entstehen im Support.
			</p>
			<p>
				Compliance ist kein Selbstzweck. Elektrofachliche Regeln, Produktfreigaben und korrekte Lagerung
				von LiFePO4-Systemen schützen Ihren Betrieb genauso wie den Endkunden.
				Wir stellen die relevanten Hinweise bereit — die Ausführung vor Ort bleibt in Ihrer Verantwortung.
			</p>
			<p>
				Im Alltag zählt Erreichbarkeit. Wenn ein BMS nicht kommuniziert oder ein Wechselrichter
				eine Einstellung erwartet, brauchen Sie innerhalb kurzer Zeit eine klare Antwort.
				Genau deshalb ist deutschsprachiger B2B-Support Teil des Partnerversprechens.
			</p>
			<p>
				Erfolgreiche Partner berichten außerdem: Transparente Shop-Preise verkürzen Verkaufsgespräche.
				Kunden vergleichen online, kommen mit konkreten Fragen — und Sie können sofort auf Specs,
				Garantie und Lieferfenster eingehen, statt erst ein undurchsichtiges Angebot zu „bauen“.
			</p>
			<p>
				Für wachsende Betriebe empfiehlt sich eine klare interne Rollenverteilung:
				Wer qualifiziert Anfragen? Wer plant Montagefenster? Wer pflegt Nachbestellungen?
				Mit dieser Struktur skalieren Sie vom Einzelprojekt zum wiederholbaren Speicher-Geschäft.
			</p>
		</section>

		<section class="wp-prose" id="erfolgsfaktoren" aria-labelledby="erfolg-title">
			<h2 id="erfolg-title">Erfolgsfaktoren für Installateure und Fachhändler</h2>
			<p>
				Technik allein verkauft selten. Vertrauen entsteht durch Verfügbarkeit, saubere Kommunikation
				und nachvollziehbare Zahlen. Zeigen Sie dem Kunden Jahresverbrauch, Autarkieziel und Speichergröße —
				idealerweise vorbereitet über den Solarbatterie-Rechner — und leiten Sie daraus eine Empfehlung ab.
			</p>
			<p>
				Achten Sie auf realistische Terminversprechen. Ein lagerndes Gerät mit 48-Stunden-Versand
				hilft nur, wenn Ihr Montagekalender dazu passt. Planen Sie Puffer für Inbetriebnahme und Kundeneinweisung.
			</p>
			<p>
				Dokumentieren Sie Anschluss und Parameter. Das reduziert Rückläufer, erleichtert Garantiefälle
				und macht Ihr Team unabhängiger von Einzelwissen. Gute Dokumentation ist ein Wettbewerbsvorteil.
			</p>
			<p>
				Nutzen Sie Partnerkonditionen strategisch: Nicht jedes Projekt braucht Maximalmarge sofort.
				Manchmal sichert ein fairer Einstiegspreis Folgeaufträge — Erweiterung, zweites Fahrzeug, Wärmepumpe.
				Langfristige Kundenbeziehungen tragen oft mehr als der einzelne Abschluss.
			</p>
			<p>
				Zuletzt: Bleiben Sie fachlich aktuell. Speicher, EMS und Netzanforderungen entwickeln sich weiter.
				Betriebe, die regelmäßig spezifizieren statt nur „mitliefern“, werden vom Kunden als Berater wahrgenommen —
				und genau diese Position ist im Markt 2026 besonders wertvoll.
			</p>
		</section>

		<section class="wp-prose" id="regionen" aria-labelledby="regionen-title">
			<h2 id="regionen-title">Regionale Abdeckung: PV Speicher Partner werden in Metropolregionen</h2>
			<p>
				Deutschland ist kein einheitlicher Installationsmarkt. Dichte, Gewerbegebiete und PV-Durchdringung
				unterscheiden sich stark. Deshalb arbeiten wir mit einem Major-City-Ansatz:
				Berlin, Hamburg, München, Köln, Frankfurt, Stuttgart, Düsseldorf, Leipzig, Hannover, Bremen,
				Nürnberg und Dortmund bilden die regionalen Knoten.
			</p>
			<p>
				Wenn Ihre PLZ in einem kleineren Ort liegt, ordnen wir die Prüfung dem nächsten Großraum zu.
				So bleibt die Partnerdichte steuerbar — und Leads werden nicht „ins Leere“ verteilt.
			</p>
			<p>
				Für Betriebe, die PV Speicher Partner werden möchten, ist das ein Vorteil:
				Sie bekommen realistische Einzugsgebiete statt theoretischer Deutschland-Fläche ohne Nachfrage.
			</p>
		</section>

		<section class="wp-faq" id="faq" aria-labelledby="faq-title">
			<h2 id="faq-title">Häufig gestellte Fragen (FAQ)</h2>

			<div class="wp-faq-item">
				<h3>Was bedeutet es konkret, PV Speicher Partner werden zu wollen?</h3>
				<p>
					Sie treten dem B2B-Netzwerk von ACC Heimspeicher / WERDU.de bei — entweder als Montage-Partner
					für den Anschluss vor Ort oder als Fachpartner &amp; Händler für den Wiederverkauf.
					Konditionen, Region und Radius werden individuell geprüft.
				</p>
			</div>

			<div class="wp-faq-item">
				<h3>Welche Margen sind möglich, wenn ich PV Speicher Partner werden will?</h3>
				<p>
					Im Händler-Modell sind je nach Volumen bis zu 35 % Marge möglich (B2B ab 3 Einheiten).
					Montage-Partner kalkulieren zusätzlich die lokale Anschlussleistung (Richtwert 300–500 €).
				</p>
			</div>

			<div class="wp-faq-item">
				<h3>Wie schnell liefern Sie aus dem deutschen Lager?</h3>
				<p>
					Viele lagernde Positionen können innerhalb von 48 Stunden in den Versand gehen.
					Exakte Verfügbarkeit hängt von Modell und aktueller Bestandsmenge ab — Partner erhalten
					transparente Angaben vor Auftragsbestätigung.
				</p>
			</div>

			<div class="wp-faq-item">
				<h3>Gibt es Schulungen, bevor ich PV Speicher Partner werden kann?</h3>
				<p>
					Ja. Im Onboarding erhalten Sie Montageleitfäden, Produktdaten und Support-Kontakte.
					Ziel ist, dass Ihr Team DC-Anschluss und Kommunikation sicher und wiederholbar ausführt.
				</p>
			</div>

			<div class="wp-faq-item">
				<h3>Wie kommen regionale Leads zustande?</h3>
				<p>
					Über unsere PLZ-basierten Rechner auf der Startseite und dem Solarbatterie-Rechner.
					Interessenten hinterlassen Bedarfskennzahlen; passende Partner in der Region können
					im nächsten Schritt eingebunden werden.
				</p>
			</div>

			<div class="wp-faq-item">
				<h3>Muss ich Exklusivität in meiner PLZ-Region akzeptieren?</h3>
				<p>
					Exklusivität ist nicht pauschal garantiert. Wir steuern Partnerdichte so, dass Qualität
					und Erreichbarkeit stimmen. Details klären wir im Freischaltungsgespräch.
				</p>
			</div>

			<div class="wp-faq-item">
				<h3>Welche Garantie erhalten Endkunden?</h3>
				<p>
					Auf die LiFePO4-Heimspeicher geben wir 10 Jahre Garantie gemäß unseren Garantiebedingungen.
					Das erleichtert Ihre Beratung und reduziert Unsicherheit beim Kaufentscheid.
				</p>
			</div>

			<div class="wp-faq-item">
				<h3>Warum sollte mein Betrieb ausgerechnet bei WERDU.de PV Speicher Partner werden?</h3>
				<p>
					Weil Transparenz, Lieferfähigkeit und deutschsprachiger Support zusammenkommen —
					ergänzt um digitale Vorqualifizierung. Das spart Akquiseaufwand und schützt Ihre Marge.
				</p>
			</div>
		</section>

		<section class="wp-prose wp-closing" aria-labelledby="closing-title">
			<h2 id="closing-title">Nächster Schritt: PV Speicher Partner werden — Anfrage senden</h2>
			<p>
				Wenn Ihr Betrieb bereit ist, PV Speicher Partner werden zu wollen, reicht ein klarer erster Schritt:
				Formular absenden, Region prüfen lassen, Konditionen erhalten.
				Je früher Sie starten, desto schneller können Sie 2026 von Nachfrage und Lieferfähigkeit profitieren.
			</p>
			<p>
				Nutzen Sie den <a href="<?php echo esc_url( $calc_url ); ?>">Solarbatterie-Rechner</a>,
				um das Kundengespräch vorzubereiten, und kehren Sie zur
				<a href="<?php echo esc_url( $home_url ); ?>">Startseite</a> zurück, um Produktlinien und Preise
				im Gesamtbild zu sehen. Für Marktdaten empfehlen wir erneut den
				<a href="https://www.bsw-solar.de/" target="_blank" rel="noopener dofollow">Bundesverband Solarwirtschaft</a>.
			</p>
			<p class="wp-closing-cta-wrap">
				<a class="wp-btn wp-btn-primary" href="#anmeldung">Zum Partner-Formular</a>
			</p>
		</section>

		<p class="wp-footnote">
			WERDU.de · ACC Heimspeicher · LiFePO4 mit transparenten Preisen ·
			<a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">Kontakt</a> ·
			<a href="<?php echo esc_url( home_url( '/datenschutzerklaerung/' ) ); ?>">Datenschutz</a> ·
			<a href="<?php echo esc_url( home_url( '/partner-werden/' ) ); ?>">PV Speicher Partner werden</a>
		</p>
	</div>
</main>

<?php
get_footer();
