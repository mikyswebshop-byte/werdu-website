<?php
/**
 * Haalt alle WooCommerce-producten op en geeft ze als JSON terug
 * Bestand: /werdu-products.php
 */

// Laad WordPress
require_once('wp-load.php');

// Controleer of WooCommerce actief is
if (!class_exists('WooCommerce')) {
    http_response_code(500);
    echo json_encode(['error' => 'WooCommerce not active']);
    exit;
}

$products = [];
$args = [
    'limit' => -1,
    'status' => 'publish',
    'type' => ['simple', 'variable'], // alleen simpele en variabele producten
];

$all_products = wc_get_products($args);

foreach ($all_products as $product) {
    $price = 0;
    if ($product->is_type('variable')) {
        $price = $product->get_variation_price('min'); // laagste variatieprijs
    } else {
        $price = $product->get_price();
    }
    
    // Alleen producten met een prijs > 0 tonen
    if ($price <= 0) continue;
    
    $products[] = [
        'id'    => $product->get_id(),
        'name'  => $product->get_name(),
        'price' => floatval($price),
        'link'  => get_permalink($product->get_id()),
    ];
}

header('Content-Type: application/json');
echo json_encode($products);