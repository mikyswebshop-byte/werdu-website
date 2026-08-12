<?php
/**
 * Proxy voor WooCommerce REST API (alleen-lezen)
 * Plaats dit bestand in de root van je website, bijv. https://werdu.de/wc-api-proxy.php
 */

// Pas deze gegevens aan met jouw Consumer Key en Secret
$consumer_key    = 'ck_945031f5e7c47ab5fdf27b01092127f352e894bc';
$consumer_secret = 'cs_b5de6aba79f3c09d3a76f8417175273bf134d96f';

// Stel het juiste domein in (jouw eigen site)
$store_url = 'https://werdu.de';

// Endpoint voor alle producten (max 100 stuks)
$endpoint = '/wp-json/wc/v3/products?per_page=100&status=publish';

$url = $store_url . $endpoint;

$args = [
    'headers' => [
        'Authorization' => 'Basic ' . base64_encode( $consumer_key . ':' . $consumer_secret )
    ],
    'timeout' => 15,
];

$response = wp_remote_get( $url, $args );

if ( is_wp_remote_error( $response ) ) {
    http_response_code(500);
    echo json_encode(['error' => 'API request failed']);
    exit;
}

$body = wp_remote_retrieve_body( $response );
$data = json_decode( $body, true );

if ( ! $data ) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Geef alleen benodigde velden terug: naam, prijs, permalink, variaties?
$products = [];
foreach ( $data as $product ) {
    // Alleen simpele producten of variabele producten met een minimale prijs
    if ( $product['type'] === 'simple' ) {
        $price = floatval( $product['price'] );
        if ( $price > 0 ) {
            $products[] = [
                'id'    => $product['id'],
                'name'  => $product['name'],
                'price' => $price,
                'link'  => $product['permalink'],
            ];
        }
    } elseif ( $product['type'] === 'variable' ) {
        // Voor variabele producten gebruiken we de laagste prijs (of een bereik, maar voor calculator volstaat minimum)
        if ( ! empty( $product['variations'] ) ) {
            $min_price = null;
            foreach ( $product['variations'] as $variation_id ) {
                // Aparte API-call voor variaties is te zwaar; we doen een aparte endpoint
                // Simpeler: gebruik de ingebouwde price_html niet, we halen later op via aparte call
            }
            // Voor nu: voeg toe met prijs = 0, die halen we later op
            $products[] = [
                'id'    => $product['id'],
                'name'  => $product['name'],
                'price' => 0, // wordt later opgehaald
                'link'  => $product['permalink'],
                'variable' => true,
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode( $products );