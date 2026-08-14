<?php
/**
 * Footer Column 1–4 content for the Shoppingcart widget areas.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function werdu_footer_column_title( $n ) {
	$titles = array(
		1 => 'PV-Speicher',
		2 => 'Service',
		3 => 'Rechtliches',
		4 => 'Wissen',
	);
	return isset( $titles[ $n ] ) ? $titles[ $n ] : '';
}

function werdu_footer_column_markup( $n ) {
	$n = (int) $n;
	ob_start();
	if ( 1 === $n ) :
		?>
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/16-kwh-lifepo4-heimspeicher-51-2v-314ah/' ) ); ?>">16 kWh LiFePO4 PV-Speicher</a></li>
			<li><a href="<?php echo esc_url( home_url( '/30-32-kwh-lifepo4-heimspeicher-560-628ah/' ) ); ?>">30–32 kWh LiFePO4 Speicher</a></li>
			<li><a href="<?php echo esc_url( home_url( '/tewaycell-15-kwh-all-in-one-lifepo4-solarbatterie-5-kw-hybrid-wechselrichter/' ) ); ?>">15 kWh All-in-One Solarbatterie</a></li>
			<li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Solarbatterien im Shop</a></li>
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
			<li><a href="<?php echo esc_url( home_url( '/wie-viele-zyklen-schafft-eine-solarbatterie-von-werdu-de/' ) ); ?>">Zyklen einer Solarbatterie</a></li>
		</ul>
		<?php
	endif;
	return (string) ob_get_clean();
}

function werdu_ensure_footer_widgets() {
	$ver = '20260814-ft6';
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
