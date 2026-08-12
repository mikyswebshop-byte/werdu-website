<?php
/**
 * WERDU Partner werden — assets, Major-City IP geo, form handler, SEO/schema
 *
 * @package Shoppingcart_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether current request is the partner landing page.
 */
function werdu_is_partner_page() {
	if ( is_page( 'partner-werden' ) ) {
		return true;
	}
	if ( is_page_template( 'page-partner-werden.php' ) || is_page_template( 'template-partner-werden.php' ) ) {
		return true;
	}
	return false;
}

/**
 * Client IP (respect common proxy headers).
 */
function werdu_partner_client_ip() {
	$keys = array(
		'HTTP_CF_CONNECTING_IP',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_X_REAL_IP',
		'REMOTE_ADDR',
	);
	foreach ( $keys as $key ) {
		if ( empty( $_SERVER[ $key ] ) ) {
			continue;
		}
		$raw = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
		if ( strpos( $raw, ',' ) !== false ) {
			$parts = explode( ',', $raw );
			$raw   = trim( $parts[0] );
		}
		if ( filter_var( $raw, FILTER_VALIDATE_IP ) ) {
			return $raw;
		}
	}
	return '127.0.0.1';
}

/**
 * Major German regional hubs (Metropolregionen) — canonical PLZ + coords.
 *
 * @return array<string, array{plz:string,city:string,lat:float,lon:float}>
 */
function werdu_partner_major_hubs() {
	return array(
		'Berlin'      => array( 'plz' => '10117', 'city' => 'Berlin', 'lat' => 52.5200, 'lon' => 13.4050 ),
		'Hamburg'     => array( 'plz' => '20095', 'city' => 'Hamburg', 'lat' => 53.5511, 'lon' => 9.9937 ),
		'München'     => array( 'plz' => '80331', 'city' => 'München', 'lat' => 48.1351, 'lon' => 11.5820 ),
		'Köln'        => array( 'plz' => '50667', 'city' => 'Köln', 'lat' => 50.9375, 'lon' => 6.9603 ),
		'Frankfurt'   => array( 'plz' => '60311', 'city' => 'Frankfurt', 'lat' => 50.1109, 'lon' => 8.6821 ),
		'Stuttgart'   => array( 'plz' => '70173', 'city' => 'Stuttgart', 'lat' => 48.7758, 'lon' => 9.1829 ),
		'Düsseldorf'  => array( 'plz' => '40213', 'city' => 'Düsseldorf', 'lat' => 51.2277, 'lon' => 6.7735 ),
		'Leipzig'     => array( 'plz' => '04109', 'city' => 'Leipzig', 'lat' => 51.3397, 'lon' => 12.3731 ),
		'Hannover'    => array( 'plz' => '30159', 'city' => 'Hannover', 'lat' => 52.3759, 'lon' => 9.7320 ),
		'Bremen'      => array( 'plz' => '28195', 'city' => 'Bremen', 'lat' => 53.0793, 'lon' => 8.8017 ),
		'Nürnberg'    => array( 'plz' => '90402', 'city' => 'Nürnberg', 'lat' => 49.4521, 'lon' => 11.0767 ),
		'Dortmund'    => array( 'plz' => '44135', 'city' => 'Dortmund', 'lat' => 51.5136, 'lon' => 7.4653 ),
	);
}

/**
 * Alias map: English / alternate city names → hub key.
 *
 * @return array<string, string>
 */
function werdu_partner_city_aliases() {
	return array(
		'Berlin'            => 'Berlin',
		'Hamburg'           => 'Hamburg',
		'München'           => 'München',
		'Munich'            => 'München',
		'Köln'              => 'Köln',
		'Cologne'           => 'Köln',
		'Frankfurt'         => 'Frankfurt',
		'Frankfurt am Main' => 'Frankfurt',
		'Frankfurt/Main'    => 'Frankfurt',
		'Stuttgart'         => 'Stuttgart',
		'Düsseldorf'        => 'Düsseldorf',
		'Dusseldorf'        => 'Düsseldorf',
		'Leipzig'           => 'Leipzig',
		'Hannover'          => 'Hannover',
		'Hanover'           => 'Hannover',
		'Bremen'            => 'Bremen',
		'Nürnberg'          => 'Nürnberg',
		'Nuremberg'         => 'Nürnberg',
		'Dortmund'          => 'Dortmund',
	);
}

/**
 * PLZ 2-digit prefix → nearest major hub (Major City Fallback Matrix).
 *
 * @return array<string, string> prefix => hub key
 */
function werdu_partner_plz_prefix_matrix() {
	return array(
		'01' => 'Leipzig',
		'02' => 'Leipzig',
		'03' => 'Berlin',
		'04' => 'Leipzig',
		'06' => 'Leipzig',
		'07' => 'Leipzig',
		'08' => 'Leipzig',
		'09' => 'Leipzig',
		'10' => 'Berlin',
		'11' => 'Berlin',
		'12' => 'Berlin',
		'13' => 'Berlin',
		'14' => 'Berlin',
		'15' => 'Berlin',
		'16' => 'Berlin',
		'17' => 'Hamburg',
		'18' => 'Hamburg',
		'19' => 'Hamburg',
		'20' => 'Hamburg',
		'21' => 'Hamburg',
		'22' => 'Hamburg',
		'23' => 'Hamburg',
		'24' => 'Hamburg',
		'25' => 'Hamburg',
		'26' => 'Bremen',
		'27' => 'Bremen',
		'28' => 'Bremen',
		'29' => 'Hannover',
		'30' => 'Hannover',
		'31' => 'Hannover',
		'32' => 'Hannover',
		'33' => 'Dortmund',
		'34' => 'Frankfurt',
		'35' => 'Frankfurt',
		'36' => 'Frankfurt',
		'37' => 'Hannover',
		'38' => 'Hannover',
		'39' => 'Hannover',
		'40' => 'Düsseldorf',
		'41' => 'Düsseldorf',
		'42' => 'Düsseldorf',
		'44' => 'Dortmund',
		'45' => 'Dortmund',
		'46' => 'Düsseldorf',
		'47' => 'Düsseldorf',
		'48' => 'Dortmund',
		'49' => 'Hannover',
		'50' => 'Köln',
		'51' => 'Köln',
		'52' => 'Köln',
		'53' => 'Köln',
		'54' => 'Köln',
		'55' => 'Frankfurt',
		'56' => 'Köln',
		'57' => 'Köln',
		'58' => 'Dortmund',
		'59' => 'Dortmund',
		'60' => 'Frankfurt',
		'61' => 'Frankfurt',
		'63' => 'Frankfurt',
		'64' => 'Frankfurt',
		'65' => 'Frankfurt',
		'66' => 'Frankfurt',
		'67' => 'Frankfurt',
		'68' => 'Frankfurt',
		'69' => 'Frankfurt',
		'70' => 'Stuttgart',
		'71' => 'Stuttgart',
		'72' => 'Stuttgart',
		'73' => 'Stuttgart',
		'74' => 'Stuttgart',
		'75' => 'Stuttgart',
		'76' => 'Stuttgart',
		'77' => 'Stuttgart',
		'78' => 'Stuttgart',
		'79' => 'Stuttgart',
		'80' => 'München',
		'81' => 'München',
		'82' => 'München',
		'83' => 'München',
		'84' => 'München',
		'85' => 'München',
		'86' => 'München',
		'87' => 'München',
		'88' => 'Stuttgart',
		'89' => 'Stuttgart',
		'90' => 'Nürnberg',
		'91' => 'Nürnberg',
		'92' => 'Nürnberg',
		'93' => 'Nürnberg',
		'94' => 'Nürnberg',
		'95' => 'Nürnberg',
		'96' => 'Nürnberg',
		'97' => 'Nürnberg',
		'98' => 'Leipzig',
		'99' => 'Leipzig',
	);
}

/**
 * Haversine distance in km.
 *
 * @param float $lat1 Lat A.
 * @param float $lon1 Lon A.
 * @param float $lat2 Lat B.
 * @param float $lon2 Lon B.
 * @return float
 */
function werdu_partner_haversine_km( $lat1, $lon1, $lat2, $lon2 ) {
	$earth = 6371.0;
	$d_lat = deg2rad( $lat2 - $lat1 );
	$d_lon = deg2rad( $lon2 - $lon1 );
	$a     = sin( $d_lat / 2 ) * sin( $d_lat / 2 )
		+ cos( deg2rad( $lat1 ) ) * cos( deg2rad( $lat2 ) )
		* sin( $d_lon / 2 ) * sin( $d_lon / 2 );
	return $earth * ( 2 * atan2( sqrt( $a ), sqrt( 1 - $a ) ) );
}

/**
 * Resolve hub entry by key.
 *
 * @param string $key Hub key.
 * @return array{plz:string,city:string}
 */
function werdu_partner_hub_result( $key ) {
	$hubs = werdu_partner_major_hubs();
	if ( isset( $hubs[ $key ] ) ) {
		return array(
			'plz'  => $hubs[ $key ]['plz'],
			'city' => $hubs[ $key ]['city'],
		);
	}
	return array(
		'plz'  => '10117',
		'city' => 'Berlin',
	);
}

/**
 * Nearest major hub by coordinates.
 *
 * @param float $lat Latitude.
 * @param float $lon Longitude.
 * @return array{plz:string,city:string}
 */
function werdu_partner_nearest_hub_by_coords( $lat, $lon ) {
	$hubs    = werdu_partner_major_hubs();
	$best    = 'Berlin';
	$best_km = PHP_FLOAT_MAX;
	foreach ( $hubs as $key => $hub ) {
		$km = werdu_partner_haversine_km( $lat, $lon, $hub['lat'], $hub['lon'] );
		if ( $km < $best_km ) {
			$best_km = $km;
			$best    = $key;
		}
	}
	return werdu_partner_hub_result( $best );
}

/**
 * Map PLZ to major hub via prefix matrix.
 *
 * @param string $plz 5-digit PLZ.
 * @return array{plz:string,city:string}|null
 */
function werdu_partner_hub_by_plz( $plz ) {
	$plz = preg_replace( '/\D/', '', (string) $plz );
	if ( strlen( $plz ) < 2 ) {
		return null;
	}
	$prefix  = substr( $plz, 0, 2 );
	$matrix  = werdu_partner_plz_prefix_matrix();
	if ( isset( $matrix[ $prefix ] ) ) {
		return werdu_partner_hub_result( $matrix[ $prefix ] );
	}
	return null;
}

/**
 * Match city string to a major hub (exact / alias / substring).
 *
 * @param string $city City name from geo provider.
 * @return array{plz:string,city:string}|null
 */
function werdu_partner_hub_by_city_name( $city ) {
	$city = trim( (string) $city );
	if ( $city === '' ) {
		return null;
	}
	$aliases = werdu_partner_city_aliases();
	$hubs    = werdu_partner_major_hubs();

	if ( isset( $aliases[ $city ] ) ) {
		return werdu_partner_hub_result( $aliases[ $city ] );
	}

	foreach ( $aliases as $alias => $key ) {
		if ( stripos( $city, $alias ) !== false || stripos( $alias, $city ) !== false ) {
			return werdu_partner_hub_result( $key );
		}
	}

	foreach ( $hubs as $key => $hub ) {
		if ( stripos( $city, $hub['city'] ) !== false ) {
			return werdu_partner_hub_result( $key );
		}
	}

	return null;
}

/**
 * Normalize any geo result to a major DE Metropolregion (never leave small towns).
 *
 * @param array $raw Raw geo fields (city, plz, country, lat, lon).
 * @return array{plz:string,city:string}
 */
function werdu_partner_normalize_geo( $raw ) {
	$city    = isset( $raw['city'] ) ? trim( (string) $raw['city'] ) : '';
	$plz     = isset( $raw['plz'] ) ? preg_replace( '/\D/', '', (string) $raw['plz'] ) : '';
	$plz     = substr( (string) $plz, 0, 5 );
	$country = isset( $raw['country'] ) ? strtoupper( (string) $raw['country'] ) : 'DE';
	$lat     = isset( $raw['lat'] ) ? (float) $raw['lat'] : null;
	$lon     = isset( $raw['lon'] ) ? (float) $raw['lon'] : null;

	if ( $country && $country !== 'DE' && $lat === null ) {
		return werdu_partner_hub_result( 'Berlin' );
	}

	// 1) Exact / alias major city match.
	$by_city = werdu_partner_hub_by_city_name( $city );
	if ( $by_city ) {
		return $by_city;
	}

	// 2) Coordinates → nearest major hub (best for villages / Kleinorte).
	if ( $lat !== null && $lon !== null && $lat !== 0.0 && $lon !== 0.0 ) {
		return werdu_partner_nearest_hub_by_coords( $lat, $lon );
	}

	// 3) PLZ prefix matrix.
	if ( strlen( $plz ) === 5 ) {
		$by_plz = werdu_partner_hub_by_plz( $plz );
		if ( $by_plz ) {
			return $by_plz;
		}
	}

	// 4) Non-DE or unknown → Berlin default.
	return werdu_partner_hub_result( 'Berlin' );
}

/**
 * Resolve geo via ip-api.com with transient cache → always a major hub.
 *
 * @return array{plz:string,city:string}
 */
function werdu_partner_resolve_geo() {
	$ip        = werdu_partner_client_ip();
	$cache_key = 'werdu_partner_geo_v2_' . md5( $ip );
	$cached    = get_transient( $cache_key );
	if ( is_array( $cached ) && ! empty( $cached['plz'] ) && ! empty( $cached['city'] ) ) {
		return $cached;
	}

	// Private / local IPs → Berlin default (dev / localhost).
	if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
		$result = werdu_partner_hub_result( 'Berlin' );
		set_transient( $cache_key, $result, DAY_IN_SECONDS );
		return $result;
	}

	$result = werdu_partner_hub_result( 'Berlin' );
	$url    = 'http://ip-api.com/json/' . rawurlencode( $ip ) . '?fields=status,countryCode,city,zip,lat,lon&lang=de';

	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 3,
			'headers' => array( 'Accept' => 'application/json' ),
		)
	);

	if ( ! is_wp_error( $response ) && (int) wp_remote_retrieve_response_code( $response ) === 200 ) {
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( is_array( $body ) && ( $body['status'] ?? '' ) === 'success' ) {
			$result = werdu_partner_normalize_geo(
				array(
					'city'    => $body['city'] ?? '',
					'plz'     => $body['zip'] ?? '',
					'country' => $body['countryCode'] ?? 'DE',
					'lat'     => $body['lat'] ?? null,
					'lon'     => $body['lon'] ?? null,
				)
			);
		}
	}

	set_transient( $cache_key, $result, 12 * HOUR_IN_SECONDS );
	return $result;
}

/**
 * AJAX: return detected major-city PLZ/city.
 */
function werdu_partner_geo_ajax() {
	check_ajax_referer( 'werdu_partner', 'nonce' );
	$geo = werdu_partner_resolve_geo();
	wp_send_json_success( $geo );
}
add_action( 'wp_ajax_werdu_partner_geo', 'werdu_partner_geo_ajax' );
add_action( 'wp_ajax_nopriv_werdu_partner_geo', 'werdu_partner_geo_ajax' );

/**
 * AJAX: partner application (fallback when CF7 not assigned).
 */
function werdu_partner_apply_ajax() {
	check_ajax_referer( 'werdu_partner', 'nonce' );

	$firma   = sanitize_text_field( wp_unslash( $_POST['partner-firma'] ?? '' ) );
	$person  = sanitize_text_field( wp_unslash( $_POST['partner-name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['partner-email'] ?? '' ) );
	$phone   = sanitize_text_field( wp_unslash( $_POST['partner-telefon'] ?? '' ) );
	$plz     = sanitize_text_field( wp_unslash( $_POST['partner-plz'] ?? '' ) );
	$radius  = sanitize_text_field( wp_unslash( $_POST['partner-radius'] ?? '' ) );
	$type    = sanitize_text_field( wp_unslash( $_POST['partner-typ'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['partner-nachricht'] ?? '' ) );

	if ( $firma === '' || $person === '' || ! is_email( $email ) || $plz === '' || $type === '' ) {
		wp_send_json_error(
			array( 'message' => 'Bitte füllen Sie alle Pflichtfelder korrekt aus.' ),
			400
		);
	}

	$allowed_radius = array( '20', '30', '50', '100' );
	if ( ! in_array( $radius, $allowed_radius, true ) ) {
		$radius = '50';
	}
	$allowed_type = array( 'montage', 'b2b', 'beides' );
	if ( ! in_array( $type, $allowed_type, true ) ) {
		$type = 'montage';
	}

	$type_labels = array(
		'montage' => 'Montage-Partner',
		'b2b'     => 'Fachpartner & Händler',
		'beides'  => 'Montage + Fachpartner',
	);

	$to      = apply_filters( 'werdu_partner_notify_email', 'service@werdu.de' );
	$subject = '[WERDU Partner] ' . $type_labels[ $type ] . ' — ' . $firma;
	$body    = "Neue Partner-Anfrage über /partner-werden/\n\n"
		. "Firmenname: {$firma}\n"
		. "Ansprechpartner: {$person}\n"
		. "E-Mail: {$email}\n"
		. "Telefon: {$phone}\n"
		. "PLZ: {$plz}\n"
		. "Radius: {$radius} km\n"
		. 'Partnerschafts-Typ: ' . $type_labels[ $type ] . "\n"
		. "Nachricht:\n{$message}\n";

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $person . ' <' . $email . '>',
	);

	$sent = wp_mail( $to, $subject, $body, $headers );
	if ( ! $sent ) {
		wp_send_json_error(
			array( 'message' => 'E-Mail konnte nicht gesendet werden. Bitte schreiben Sie an service@werdu.de.' ),
			500
		);
	}

	wp_send_json_success(
		array( 'message' => 'Vielen Dank — Ihre Partner-Anfrage ist eingegangen. Wir melden uns zeitnah.' )
	);
}
add_action( 'wp_ajax_werdu_partner_apply', 'werdu_partner_apply_ajax' );
add_action( 'wp_ajax_nopriv_werdu_partner_apply', 'werdu_partner_apply_ajax' );

/**
 * Enqueue partner assets.
 */
function werdu_partner_assets() {
	if ( ! werdu_is_partner_page() ) {
		return;
	}
	$ver = '1.2.0';
	$dir = get_stylesheet_directory_uri();

	wp_enqueue_style(
		'werdu-partner',
		$dir . '/css/werdu-partner.css',
		array(),
		$ver
	);
	wp_enqueue_script(
		'werdu-partner-geo',
		$dir . '/JS/werdu-partner-geo.js',
		array(),
		$ver,
		true
	);

	$hubs_public = array();
	foreach ( werdu_partner_major_hubs() as $hub ) {
		$hubs_public[] = array(
			'plz'  => $hub['plz'],
			'city' => $hub['city'],
			'lat'  => $hub['lat'],
			'lon'  => $hub['lon'],
		);
	}

	wp_localize_script(
		'werdu-partner-geo',
		'werduPartner',
		array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'werdu_partner' ),
			'majorHubs'  => $hubs_public,
			'plzMatrix'  => werdu_partner_plz_prefix_matrix(),
			'defaultGeo' => array( 'plz' => '10117', 'city' => 'Berlin' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'werdu_partner_assets', 25 );

/**
 * Meta title / description + JSON-LD (Service + BreadcrumbList + FAQPage).
 */
function werdu_partner_seo_head() {
	if ( ! werdu_is_partner_page() ) {
		return;
	}

	$title = 'PV Speicher Partner werden | Montage & B2B-Händler | WERDU.de';
	$desc  = 'PV Speicher Partner werden bei ACC Heimspeicher / WERDU.de: bis zu 35 % Marge, deutsches Lager (oft 48h), 10 Jahre LiFePO4-Garantie, deutschsprachiger B2B-Support und regionale Leads über PLZ-Rechner.';
	$url   = home_url( '/partner-werden/' );

	echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
	echo '<link rel="canonical" href="' . esc_url( $url ) . '" />' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $desc ) . '" />' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
	echo '<meta property="og:type" content="website" />' . "\n";
	echo '<meta property="og:image" content="https://werdu.de/wp-content/uploads/2026/07/16kwh-lifepo4-heimspeicher-hero_1024_1024.webp" />' . "\n";

	$service = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Service',
		'name'        => 'Montage-Partner & B2B-Händlernetzwerk WERDU.de',
		'description' => $desc,
		'url'         => $url,
		'provider'    => array(
			'@type' => 'Organization',
			'name'  => 'WERDU.de',
			'url'   => home_url( '/' ),
		),
		'areaServed'  => array(
			'@type' => 'Country',
			'name'  => 'Germany',
		),
		'serviceType' => array(
			'PV Speicher Partner werden',
			'Montage-Partner-Netzwerk',
			'B2B-Fachpartner / Händler',
		),
		'audience'    => array(
			'@type'        => 'BusinessAudience',
			'audienceType' => 'Elektriker, Installateure, Fachhändler',
		),
	);

	$breadcrumb = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => 'Startseite',
				'item'     => home_url( '/' ),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => 'PV Speicher Partner werden',
				'item'     => $url,
			),
		),
	);

	$faq = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => array(
			array(
				'@type'          => 'Question',
				'name'           => 'Was bedeutet es konkret, PV Speicher Partner werden zu wollen?',
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => 'Sie treten dem B2B-Netzwerk von ACC Heimspeicher / WERDU.de bei — entweder als Montage-Partner für den Anschluss vor Ort oder als Fachpartner & Händler für den Wiederverkauf. Konditionen, Region und Radius werden individuell geprüft.',
				),
			),
			array(
				'@type'          => 'Question',
				'name'           => 'Welche Margen sind möglich, wenn ich PV Speicher Partner werden will?',
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => 'Im Händler-Modell sind je nach Volumen bis zu 35 % Marge möglich (B2B ab 3 Einheiten). Montage-Partner kalkulieren zusätzlich die lokale Anschlussleistung (Richtwert 300–500 €).',
				),
			),
			array(
				'@type'          => 'Question',
				'name'           => 'Wie schnell liefern Sie aus dem deutschen Lager?',
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => 'Viele lagernde Positionen können innerhalb von 48 Stunden in den Versand gehen. Exakte Verfügbarkeit hängt von Modell und aktueller Bestandsmenge ab.',
				),
			),
			array(
				'@type'          => 'Question',
				'name'           => 'Gibt es Schulungen, bevor ich PV Speicher Partner werden kann?',
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => 'Ja. Im Onboarding erhalten Sie Montageleitfäden, Produktdaten und Support-Kontakte für DC-Anschluss und BMS-Kommunikation.',
				),
			),
			array(
				'@type'          => 'Question',
				'name'           => 'Wie kommen regionale Leads zustande?',
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => 'Über PLZ-basierte Rechner auf der Startseite und dem Solarbatterie-Rechner. Interessenten hinterlassen Bedarfskennzahlen; passende Partner in der Region können eingebunden werden.',
				),
			),
			array(
				'@type'          => 'Question',
				'name'           => 'Muss ich Exklusivität in meiner PLZ-Region akzeptieren?',
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => 'Exklusivität ist nicht pauschal garantiert. Wir steuern Partnerdichte so, dass Qualität und Erreichbarkeit stimmen. Details klären wir im Freischaltungsgespräch.',
				),
			),
			array(
				'@type'          => 'Question',
				'name'           => 'Welche Garantie erhalten Endkunden?',
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => 'Auf die LiFePO4-Heimspeicher geben wir 10 Jahre Garantie gemäß unseren Garantiebedingungen.',
				),
			),
			array(
				'@type'          => 'Question',
				'name'           => 'Warum sollte mein Betrieb ausgerechnet bei WERDU.de PV Speicher Partner werden?',
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => 'Weil Transparenz, Lieferfähigkeit und deutschsprachiger Support zusammenkommen — ergänzt um digitale Vorqualifizierung über PLZ-Rechner.',
				),
			),
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $service, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	echo '<script type="application/ld+json">' . wp_json_encode( $faq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'werdu_partner_seo_head', 5 );

/**
 * Document title for partner page.
 *
 * @param array $parts Title parts.
 * @return array
 */
function werdu_partner_document_title( $parts ) {
	if ( werdu_is_partner_page() ) {
		$parts['title'] = 'PV Speicher Partner werden | Montage & B2B-Händler';
	}
	return $parts;
}
add_filter( 'document_title_parts', 'werdu_partner_document_title', 20 );

/**
 * Dedicated Fluent Forms "Partner-Anfrage" form ID, built specifically for /partner-werden/.
 */
if ( ! defined( 'WERDU_PARTNER_FLUENTFORM_ID' ) ) {
	define( 'WERDU_PARTNER_FLUENTFORM_ID', 10 );
}

/**
 * Resolve the Fluent Forms ID to render on the Partner werden page.
 *
 * Defaults to the dedicated Partner form (ID 10). A `werdu_partner_fluentform_id`
 * filter/option remains available for a future manual override, but no
 * admin/database configuration is required — ID 10 is used out of the box.
 *
 * @return int Fluent Form ID, or 0 when Fluent Forms is not active.
 */
function werdu_partner_resolve_fluentform_id() {
	if ( ! shortcode_exists( 'fluentform' ) ) {
		return 0;
	}

	$default  = (int) WERDU_PARTNER_FLUENTFORM_ID;
	$resolved = (int) apply_filters( 'werdu_partner_fluentform_id', (int) get_option( 'werdu_partner_fluentform_id', $default ) );

	return $resolved > 0 ? $resolved : $default;
}

/**
 * Render the Partner-Formular: Fluent Forms (ID 10 by default) → CF7 → built-in AJAX form.
 */
function werdu_partner_render_form() {
	$ff_id = werdu_partner_resolve_fluentform_id();
	if ( $ff_id > 0 && shortcode_exists( 'fluentform' ) ) {
		$ff_output = do_shortcode( '[fluentform id="' . absint( $ff_id ) . '"]' );
		if ( '' !== trim( (string) $ff_output ) ) {
			// Fluent Forms sanitizes/escapes its own markup; nothing further to do here.
			echo $ff_output;
			return;
		}
		// Shortcode produced no markup (e.g. form unpublished/deleted) — fall through instead of leaving a gap.
	}

	$form_id = (int) apply_filters( 'werdu_partner_cf7_id', (int) get_option( 'werdu_partner_cf7_id', 0 ) );

	if ( ! $form_id && function_exists( 'wpcf7_contact_form' ) && class_exists( 'WPCF7_ContactForm' ) ) {
		$posts = get_posts(
			array(
				'post_type'      => 'wpcf7_contact_form',
				'post_status'    => 'publish',
				'posts_per_page' => 20,
				's'              => 'Partner',
			)
		);
		foreach ( $posts as $p ) {
			if ( stripos( $p->post_title, 'Partner' ) !== false ) {
				$form_id = (int) $p->ID;
				break;
			}
		}
	}

	if ( $form_id && shortcode_exists( 'contact-form-7' ) ) {
		echo do_shortcode( '[contact-form-7 id="' . absint( $form_id ) . '" title="WERDU Partner werden"]' );
		return;
	}

	?>
	<form id="werdu-partner-form" class="wp-form-grid" method="post" novalidate>
		<div class="wp-field">
			<label for="partner-firma">Firmenname <span class="req">*</span></label>
			<input type="text" id="partner-firma" name="partner-firma" required autocomplete="organization" />
		</div>
		<div class="wp-field">
			<label for="partner-name">Ansprechpartner <span class="req">*</span></label>
			<input type="text" id="partner-name" name="partner-name" required autocomplete="name" />
		</div>
		<div class="wp-field">
			<label for="partner-email">E-Mail <span class="req">*</span></label>
			<input type="email" id="partner-email" name="partner-email" required autocomplete="email" />
		</div>
		<div class="wp-field">
			<label for="partner-telefon">Telefon</label>
			<input type="tel" id="partner-telefon" name="partner-telefon" autocomplete="tel" />
		</div>
		<div class="wp-field">
			<label for="partner-plz">PLZ <span class="req">*</span></label>
			<input type="text" id="partner-plz" name="partner-plz" inputmode="numeric" pattern="[0-9]{5}" maxlength="5" required autocomplete="postal-code" />
		</div>
		<div class="wp-field">
			<label for="partner-radius">Radius <span class="req">*</span></label>
			<select id="partner-radius" name="partner-radius" required>
				<option value="20">20 km</option>
				<option value="30">30 km</option>
				<option value="50" selected>50 km</option>
				<option value="100">100 km</option>
			</select>
		</div>
		<div class="wp-field wp-field--full">
			<label>Partnerschafts-Typ <span class="req">*</span></label>
			<div class="wp-partner-type">
				<label class="wp-type-option is-active">
					<input type="radio" name="partner-typ" value="montage" checked />
					<strong>Montage-Partner</strong>
					<small>300 € – 500 € pro Anschluss · Installation vor Ort</small>
				</label>
				<label class="wp-type-option">
					<input type="radio" name="partner-typ" value="b2b" />
					<strong>Fachpartner &amp; Händler</strong>
					<small>B2B-Einkauf ab 3 Einheiten</small>
				</label>
			</div>
		</div>
		<div class="wp-field wp-field--full">
			<label for="partner-nachricht">Kurzbeschreibung / Kapazität</label>
			<textarea id="partner-nachricht" name="partner-nachricht" rows="4" placeholder="z. B. Meisterbetrieb, Einsatzgebiet, bisherige Speicher-Erfahrung…"></textarea>
		</div>
		<div class="wp-submit-wrap">
			<button type="submit" class="wp-btn wp-btn-primary">Partnerschaft anfragen</button>
			<div class="wp-form-msg" data-wp-form-msg role="status" aria-live="polite"></div>
		</div>
	</form>
	<?php
}

/**
 * Ensure WP page exists with correct template (idempotent).
 */
function werdu_partner_ensure_page() {
	if ( get_option( 'werdu_partner_page_ready' ) === '1.1.0' ) {
		return;
	}
	$existing = get_page_by_path( 'partner-werden' );
	if ( ! $existing ) {
		$id = wp_insert_post(
			array(
				'post_title'   => 'Partner werden',
				'post_name'    => 'partner-werden',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
				'post_author'  => 1,
			),
			true
		);
		if ( ! is_wp_error( $id ) && $id ) {
			update_post_meta( $id, '_wp_page_template', 'page-partner-werden.php' );
		}
	} else {
		update_post_meta( $existing->ID, '_wp_page_template', 'page-partner-werden.php' );
	}
	update_option( 'werdu_partner_page_ready', '1.1.0', false );
}
add_action( 'init', 'werdu_partner_ensure_page', 30 );
