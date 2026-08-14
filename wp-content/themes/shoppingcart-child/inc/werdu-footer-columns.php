<?php
/**
 * Footer Column 1–4 content for the Shoppingcart widget areas.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function werdu_footer_column_title( $n ) {
	$titles = array(
		1 => 'Produkte',
		2 => 'Service',
		3 => 'Rechtliches',
		4 => 'Über uns',
	);
	return isset( $titles[ $n ] ) ? $titles[ $n ] : '';
}

function werdu_footer_column_markup( $n ) {
	$n = (int) $n;
	ob_start();
	if ( 1 === $n ) :
		?>
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/16-kwh-lifepo4-heimspeicher-51-2v-314ah/' ) ); ?>">16 kWh Basen Green LiFePO4 PV-Speicher</a></li>
			<li><a href="<?php echo esc_url( home_url( '/16-kwh-heimspeicher-lifepo4-solarbatterie/' ) ); ?>">16 kWh TewayCell LiFePO4 Solarbatterie</a></li>
			<li><a href="<?php echo esc_url( home_url( '/30-32-kwh-lifepo4-heimspeicher-560-628ah/' ) ); ?>">30-32 kWh TewayCell LiFePO4 Speicher</a></li>
			<li><a href="<?php echo esc_url( home_url( '/32-kwh-lifepo4-heimspeicher-628ah/' ) ); ?>">32 kWh Basen Green LiFePO4 PV-Speicher</a></li>
			<li><a href="<?php echo esc_url( home_url( '/tewaycell-15-kwh-all-in-one-lifepo4-solarbatterie-5-kw-hybrid-wechselrichter/' ) ); ?>">15 kWh All-in-One LiFePO4 mit Wechselrichter</a></li>
			<li><a href="<?php echo esc_url( home_url( '/tewaycell-30-kwh-all-in-one-solarspeicher-mit-12-kw-hybrid-wechselrichter-3-phasig/' ) ); ?>">30 kWh All-in-One Solarspeicher 3-phasig</a></li>
			<li><a href="<?php echo esc_url( home_url( '/sodium-ion-solarspeicher-10-kwh-mit-5-kw-wechselrichter/' ) ); ?>">10 kWh Sodium-Ion Solarspeicher</a></li>
			<li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Solarbatterien Übersicht</a></li>
			<li><a href="<?php echo esc_url( home_url( '/heimspeicher-systeme/' ) ); ?>">LiFePO4 Batterien und PV-Speicher</a></li>
			<li><a href="<?php echo esc_url( home_url( '/solarbatterie-rechner/' ) ); ?>">PV-Speicher Rechner</a></li>
		</ul>
		<?php
	elseif ( 2 === $n ) :
		?>
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/zahlung-lieferung/' ) ); ?>">Zahlung und Lieferung</a></li>
			<li><a href="<?php echo esc_url( home_url( '/mwst-befreiung-eigenverbrauch/' ) ); ?>">MwSt. Befreiung für Eigenverbrauch</a></li>
			<li><a href="<?php echo esc_url( home_url( '/widerrufsbelehrung/' ) ); ?>">Widerrufsbelehrung</a></li>
			<li><a href="<?php echo esc_url( home_url( '/garantie/' ) ); ?>">Garantie</a></li>
			<li><a href="<?php echo esc_url( home_url( '/ruecksendung/' ) ); ?>">Rücksendung</a></li>
		</ul>
		<?php
	elseif ( 3 === $n ) :
		?>
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/agb-e-mail-version/' ) ); ?>">AGB</a></li>
			<li><a href="<?php echo esc_url( home_url( '/impressum/' ) ); ?>">Impressum</a></li>
			<li><a href="<?php echo esc_url( home_url( '/cookie-richtlinie/' ) ); ?>">Cookie Richtlinien (EU)</a></li>
			<li><a href="<?php echo esc_url( home_url( '/datenschutzerklaerung/' ) ); ?>">Datenschutzerklärung</a></li>
			<li><a href="<?php echo esc_url( home_url( '/barrierefreiheitserklaerung/' ) ); ?>">Barrierefreiheitserklärung</a></li>
		</ul>
		<?php
	elseif ( 4 === $n ) :
		?>
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/ueber-uns/' ) ); ?>">Über uns</a></li>
			<li><a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>">Kontakt</a></li>
			<li><a href="<?php echo esc_url( home_url( '/heimspeicher-installation/' ) ); ?>">PV-Speicher Installation</a></li>
			<li><a href="<?php echo esc_url( home_url( '/lohnt-sich-ein-heimspeicher/' ) ); ?>">Lohnt sich ein PV-Speicher?</a></li>
			<li><a href="<?php echo esc_url( home_url( '/heimspeicher-kosten-2026-preisfallen-vermeiden/' ) ); ?>">7 versteckte Preisfallen beim Speicher</a></li>
			<li><a href="<?php echo esc_url( home_url( '/wie-viele-zyklen-schafft-eine-solarbatterie-von-werdu-de/' ) ); ?>">Wie viele Zyklen schafft eine Solarbatterie?</a></li>
			<li><a href="<?php echo esc_url( home_url( '/batteriegesetz/' ) ); ?>">Batteriegesetz</a></li>
			<li><a href="<?php echo esc_url( home_url( '/elektrog/' ) ); ?>">ElektroG</a></li>
		</ul>
		<?php
	endif;
	return (string) ob_get_clean();
}

function werdu_ensure_footer_widgets() {
	$ver = '20260814-ft5';
	if ( get_option( 'werdu_footer_cols_ver' ) === $ver ) {
		return;
	}

	$html_widgets = get_option( 'widget_custom_html', array() );
	if ( ! is_array( $html_widgets ) ) {
		$html_widgets = array();
	}

	$sidebars = get_option( 'sidebars_widgets', array() );
	if ( ! is_array( $sidebars ) ) {
		$sidebars = array();
	}

	for ( $i = 1; $i <= 4; $i++ ) {
		$id = 810 + $i;
		$html_widgets[ $id ] = array(
			'title'   => werdu_footer_column_title( $i ),
			'content' => werdu_footer_column_markup( $i ),
		);
		$sidebars[ 'shoppingcart_footer_' . $i ] = array( 'custom_html-' . $id );
	}
	$html_widgets['_multiwidget'] = 1;

	update_option( 'widget_custom_html', $html_widgets, false );
	update_option( 'sidebars_widgets', $sidebars, false );
	update_option( 'werdu_footer_cols_ver', $ver, false );
}
add_action( 'init', 'werdu_ensure_footer_widgets', 30 );
