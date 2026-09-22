<?php
/**
 * Homepage SEO helpers — FAQ copy, URLs, JSON-LD for template-werdu-v2.php
 *
 * @package ShoppingCartChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return string
 */
function werdu_home_seo_beratung_url() {
	return home_url( '/beratung-anfragen/' );
}

/**
 * @return string
 */
function werdu_home_seo_rechner_url() {
	return home_url( '/solarbatterie-rechner/' );
}

/**
 * FAQ entries for the homepage (longer sentences for readable avg. length).
 *
 * @return array<int, array{q:string,a:string}>
 */
function werdu_home_seo_faq_data() {
	$shop     = esc_url( home_url( '/shop/' ) );
	$rechner  = esc_url( werdu_home_seo_rechner_url() );
	$beratung = esc_url( werdu_home_seo_beratung_url() );
	$mwst     = esc_url( home_url( '/mwst-befreiung-eigenverbrauch/' ) );
	$install  = esc_url( home_url( '/heimspeicher-installation/' ) );

	return array(
		array(
			'q' => 'Was kostet ein PV-Speicher bei Werdu?',
			'a' => 'Die Festpreise stehen direkt im <a href="' . $shop . '">Shop</a> und ändern sich nicht durch ein PDF-Angebot. Orientierung liefern der <a href="' . $rechner . '">Solarbatterie-Rechner</a> und optional die <a href="' . $beratung . '">Fachberatung</a>, ohne dass daraus ein individueller Angebotsprozess wird.',
		),
		array(
			'q' => 'Wie groß sollte mein Heimspeicher sein?',
			'a' => 'Als Praxisregel rechnen viele Haushalte mit etwa 1,0 bis 1,5 kWh nutzbarer Kapazität je 1.000 kWh Jahresverbrauch. Bei 4.000 kWh liegen typische Auslegungen oft zwischen 5 und 8 kWh; mit Wärmepumpe oder E-Auto steigen die Werte häufig auf 12–16 kWh oder auf 30–32 kWh.',
		),
		array(
			'q' => 'Wie hoch wird der Eigenverbrauch mit Speicher?',
			'a' => 'Ohne Batterie bleiben oft nur 20–30 % des Solarstroms im Haus, während ein passend dimensionierter LiFePO4-Speicher 70–85 % Eigenverbrauch realistisch macht. Fraunhofer ISE beschreibt ähnliche Sprünge; 100 % Inselbetrieb ist kein Standardziel für Netzanschluss-Haushalte.',
		),
		array(
			'q' => 'Gilt 0 % MwSt. auf den Speicher?',
			'a' => 'Nach § 12 Abs. 3 UStG kann auf begünstigte PV-Anlagen und dazugehörige Speicher an Wohngebäuden 0 % MwSt. gelten. Die Regelung betrifft Kauf und Installation der begünstigten Systeme, nicht jedes Zubehör; Details stehen unter <a href="' . $mwst . '">MwSt-Befreiung Eigenverbrauch</a>.',
		),
		array(
			'q' => 'Kann ich an eine bestehende PV-Anlage nachrüsten?',
			'a' => 'Ja: AC-gekoppelte Speicher lassen den vorhandenen Wechselrichter oft unangetastet, während Hybrid- und All-in-One-Systeme PV und Batterie bündeln. Den elektrischen Anschluss übernimmt eine Elektrofachkraft; Stundensätze setzt der Betrieb selbst. Mehr unter <a href="' . $install . '">Heimspeicher-Installation</a>.',
		),
		array(
			'q' => 'Warum LiFePO4 statt anderer Zellchemie?',
			'a' => 'LiFePO4 gilt als thermisch robust und zyklenfest und liegt typischerweise bei 6.000–8.000 Zyklen sowie einer Nutzungsdauer von 15–20 Jahren laut gängigen Produktdaten. NMC ist energiedichter, aber thermisch empfindlicher; Blei-Säure ist für diesen Dauerbetrieb weitgehend veraltet.',
		),
		array(
			'q' => 'Bekomme ich ein automatisches PDF-Angebot aus dem Rechner?',
			'a' => 'Nein. Der Rechner liefert Richtwerte zu Kapazität und Autarkie und übergibt die Parameter an die Beratungseite; verbindliche Festpreise bleiben im Shop sichtbar. So vermeiden wir Angebotsrunden und halten die Preislogik transparent.',
		),
		array(
			'q' => 'Gibt es eine optionale Installateur-Lieferung?',
			'a' => 'Optional kann der Speicher an einen zertifizierten lokalen Installateur geliefert werden, der den Anschluss vor Ort übernimmt. Die Stundensätze legt der jeweilige Betrieb fest; wir erfinden keinen pauschalen Montageton. Die Auswahl läuft über Beratung oder Kasse.',
		),
		array(
			'q' => 'Wie lange dauert Lieferung und Inbetriebnahme?',
			'a' => 'Lieferzeiten hängen vom gewählten Produkt und Lagerbestand ab und stehen produktbezogen im Shop. Die physische Aufstellung können Sie vorbereiten; Plus-/Minus- und Kommunikationsanschluss bleiben Aufgabe der Elektrofachkraft nach den Angaben im Datenblatt.',
		),
		array(
			'q' => 'Welche Garantie gilt für die Solarbatterie?',
			'a' => 'Die Garantiebedingungen stehen produktbezogen auf den jeweiligen Produktseiten und unter der Garantie-Seite; typisch sind lange Laufzeiten bei korrekter Lagerung und Nutzung. Für eine Einordnung Ihrer Anlage reicht die <a href="' . $beratung . '">Beratung mit Rechnerwerten</a>, ohne dass daraus ein neues Preisangebot entsteht.',
		),
	);
}

/**
 * FAQPage JSON-LD for the homepage.
 *
 * @return string
 */
function werdu_home_seo_faq_json_ld() {
	$entities = array();
	foreach ( werdu_home_seo_faq_data() as $item ) {
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( $item['q'] ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( $item['a'] ),
			),
		);
	}

	$data = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $entities,
	);

	return '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

/**
 * SoftwareApplication JSON-LD for the homepage calculator.
 *
 * @return string
 */
function werdu_home_seo_software_json_ld() {
	$data = array(
		'@context'            => 'https://schema.org',
		'@type'               => 'SoftwareApplication',
		'name'                => 'Werdu Solarbatterie-Rechner',
		'applicationCategory' => 'UtilitiesApplication',
		'operatingSystem'     => 'Web',
		'url'                 => home_url( '/' ),
		'offers'              => array(
			'@type'         => 'Offer',
			'price'         => '0',
			'priceCurrency' => 'EUR',
		),
		'description'         => 'Kostenloser Online-Rechner für Kapazität, Autarkie und Orientierungsersparnis eines PV-Speichers; Ergebnis führt zur Beratung, Preise stehen im Shop.',
	);

	return '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
