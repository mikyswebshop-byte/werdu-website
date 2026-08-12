<?php
/**
 * WERDU PREIS MANAGER v3.0
 * Plaats in: /wp-content/themes/shoppingcart-child/werdu-preis-manager.php
 * Voeg toe aan functions.php: require_once get_stylesheet_directory() . '/werdu-preis-manager.php';
 * Datum: 02-07-2026
 * 
 * FEATURES:
 * - 4 producten (3 bestaand + 1 nieuw Basen Green 16 kWh)
 * - Volledig beheersbaar via admin: naam, URL, afbeelding, specs, badge, prijs, UVP, volgorde
 * - AUTO-SYNC: Producten kunnen automatisch uit WooCommerce worden overgenomen
 * - Shortcodes werken in Elementor HTML-widgets (frontend + editor preview)
 * - Automatisch JSON-LD schema op startpagina
 * - Admin pagina met overzicht, volgorde-wijziging, en productbeheer
 * - html_entity_decode fix voor &quot; problemen in Elementor
 */

// ============================================================
// 0. SHORTCODES REGISTRIEREN (vroeg, zodat ze ALTIJD beschikbaar zijn)
// ============================================================

add_shortcode('werdu_preis', 'werdu_sc_preis');
add_shortcode('werdu_preis_raw', 'werdu_sc_preis_raw');
add_shortcode('werdu_uvp', 'werdu_sc_uvp');
add_shortcode('werdu_savings', 'werdu_sc_savings');
add_shortcode('werdu_preis_datum', 'werdu_sc_preis_datum');

function werdu_sc_preis($atts) {
    $atts = shortcode_atts(['id' => ''], $atts, 'werdu_preis');
    return werdu_format_preis(get_option('werdu_preis_' . sanitize_key($atts['id']), ''));
}
function werdu_sc_preis_raw($atts) {
    $atts = shortcode_atts(['id' => ''], $atts, 'werdu_preis_raw');
    return werdu_format_preis_raw(get_option('werdu_preis_' . sanitize_key($atts['id']), ''));
}
function werdu_sc_uvp($atts) {
    $atts = shortcode_atts(['id' => ''], $atts, 'werdu_uvp');
    return '<s>' . werdu_format_preis(get_option('werdu_uvp_' . sanitize_key($atts['id']), '')) . '</s>';
}
function werdu_sc_savings($atts) {
    $atts = shortcode_atts(['id' => ''], $atts, 'werdu_savings');
    $savings = werdu_get_savings(sanitize_key($atts['id']));
    return $savings ? 'Sie sparen ' . $savings : '';
}
function werdu_sc_preis_datum() {
    $datum = get_option('werdu_preis_gueltig_bis', '');
    if (empty($datum)) return '';
    $d = DateTime::createFromFormat('Y-m-d', $datum);
    return $d ? 'gültig bis ' . $d->format('d.m.Y') : '';
}

// ============================================================
// 1. FRONTEND: SHORTCODES IN ALLE ELEMENTOR WIDGETS
// ============================================================

/**
 * Methode A: Filter op gerenderde widget content (alle widgets)
 * Prioriteit 999 = laatste filter, overschrijft alles
 */
add_filter('elementor/widget/render_content', function($content, $widget) {
    $decoded = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
    $processed = do_shortcode($decoded);
    return ($processed !== $decoded) ? $processed : $content;
}, 999, 2);

/**
 * Methode B: Widget settings aanpassen VOOR rendering
 */
add_action('elementor/frontend/widget/before_render', function($widget) {
    $settings = $widget->get_settings_for_display();
    if (empty($settings['html'])) return;
    $html = html_entity_decode($settings['html'], ENT_QUOTES, 'UTF-8');
    $html = do_shortcode($html);
    $widget->set_settings('html', $html);
});

/**
 * Methode C: Filter op volledige Elementor content
 */
add_filter('elementor/frontend/the_content', function($content) {
    return do_shortcode($content);
}, 999);

// ============================================================
// 2. EDITOR PREVIEW: JAVASCRIPT FALLBACK
// ============================================================

add_action('wp_enqueue_scripts', function() {
    // Alleen laden in Elementor editor of preview
    if (!\Elementor\Plugin::$instance->editor->is_edit_mode() && !\Elementor\Plugin::$instance->preview->is_preview_mode()) {
        return;
    }

    $produkte = werdu_get_produkte();
    $price_data = [];
    foreach ($produkte as $id => $info) {
        $preis = werdu_format_preis(get_option('werdu_preis_' . $id, ''));
        $uvp   = werdu_format_preis(get_option('werdu_uvp_' . $id, ''));
        $savings = werdu_get_savings($id);
        if (!empty($preis) && $preis !== '—') {
            $price_data[$id] = [
                'preis' => $preis,
                'uvp'   => $uvp,
                'savings' => $savings,
            ];
        }
    }

    $json = wp_json_encode($price_data);
    $datum = esc_js(werdu_sc_preis_datum());

    $script = "
    (function() {
        var prices = {$json};
        var datum = '{$datum}';

        function replaceShortcodes() {
            var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
            var nodes = [];
            var node;
            while (node = walker.nextNode()) {
                if (node.textContent.indexOf('[werdu_') !== -1) {
                    nodes.push(node);
                }
            }
            nodes.forEach(function(textNode) {
                var text = textNode.textContent;
                var parent = textNode.parentNode;

                text = text.replace(/\\[werdu_preis\\s+id=([\"'])([^\"']+)\\1\\]/g, function(match, q, id) {
                    return prices[id] && prices[id].preis ? prices[id].preis : match;
                });
                text = text.replace(/\\[werdu_uvp\\s+id=([\"'])([^\"']+)\\1\\]/g, function(match, q, id) {
                    return prices[id] && prices[id].uvp ? '<s>' + prices[id].uvp + '</s>' : match;
                });
                text = text.replace(/\\[werdu_savings\\s+id=([\"'])([^\"']+)\\1\\]/g, function(match, q, id) {
                    return prices[id] && prices[id].savings ? 'Sie sparen ' + prices[id].savings : match;
                });
                text = text.replace(/\\[werdu_preis_datum\\]/g, datum || match);

                if (text !== textNode.textContent) {
                    var wrapper = document.createElement('span');
                    wrapper.innerHTML = text;
                    parent.replaceChild(wrapper, textNode);
                }
            });
        }

        replaceShortcodes();
        var observer = new MutationObserver(function(mutations) {
            replaceShortcodes();
        });
        observer.observe(document.body, { childList: true, subtree: true });

        if (window.elementor) {
            window.elementor.on('preview:loaded', replaceShortcodes);
        }
    })();
    ";

    wp_add_inline_script('elementor-frontend', $script);
}, 999);

// ============================================================
// 3. ADMIN MENU
// ============================================================

add_action('admin_menu', function() {
    add_menu_page(
        'Werdu Preise',
        'Werdu Preise',
        'manage_options',
        'werdu-preise',
        'werdu_preise_admin_page',
        'dashicons-update',
        81
    );
});

// ============================================================
// 4. PRODUKTLIJST — 4 PRODUCTEN + 9 LEGE PLEKKEN
// ============================================================

function werdu_get_produkte() {
    $defaults = [
        // === PRODUCT 1 ===
        '16kwh' => [
            'label' => '16 kWh TewayCell (Mobile ESS, 512V, 314 Ah)',
            'url' => 'https://werdu.de/tewaycell-16-kwh-512-v-lifepo4-solarbatterie-314-ah-mobile-ess-kostenloser-versand/',
            'image' => 'https://werdu.de/wp-content/uploads/2026/03/Tewaycell_48V_300Ah_15Kwh_Mobile_Haus_Solarspeicher_System_1.webp',
            'desc' => 'LiFePO4 Solarbatterie mit 8.000 Zyklen, 10 Jahren Garantie und Grade-A Zellen. Ideal fuer Eigenverbrauch.',
            'specs' => 'LiFePO4 &bull; 8.000 Zyklen &bull; 10 Jahre Garantie &bull; Grade-A Zellen &bull; 48V-51,2V, 300Ah',
            'badge' => 'Bestseller',
            'order' => 1,
        ],
        // === PRODUCT 2: BASEN GREEN 16 kWh ===
        'basen16kwh' => [
            'label' => '16 kWh Basen Green LiFePO4 Heimspeicher',
            'url' => 'https://werdu.de/16-kwh-lifepo4-heimspeicher-51-2v-314ah/',
            'image' => 'https://werdu.de/wp-content/uploads/2026/07/16kwh-lifepo4-heimspeicher-hero_1024_1024.webp',
            'desc' => '16 kWh LiFePO4 Heimspeicher mit 51,2V 314Ah, 200A Dauerstrom, 10.000 Zyklen, Touchscreen und 5A aktivem Balancer.',
            'specs' => '51,2V 314Ah &bull; 200A Dauerstrom &bull; 10.000 Zyklen &bull; Touchscreen &bull; 5A aktiver Balancer &bull; App-Steuerung',
            'badge' => 'Bestseller',
            'order' => 2,
        ],
        // === PRODUCT 3 ===
        '30kwh' => [
            'label' => '30-32 kWh LiFePO4 (51,2V Mobile ESS)',
            'url' => 'https://werdu.de/tewaycell-30-32-kwh-lifepo4-batterie-512v-560-628ah-mobile-ess-300ah-bms/',
            'image' => 'https://werdu.de/wp-content/uploads/2026/03/tewaycell-30kwh-32kwh-lifepo4-stromspeicher-batterie-51-2v-mobile-ess.webp',
            'desc' => 'Modulare LiFePO4 Batterie fuer maximale Autarkie. 100% Entladetiefe, 51,2V System.',
            'specs' => 'Maximale Autarkie &bull; Modulare Erweiterung &bull; 100% Entladetiefe &bull; 51,2V Systemspannung',
            'badge' => 'Profi',
            'order' => 3,
        ],
        // === PRODUCT 4: 15 kWh All-in-One (verplaatst) ===
        '15kwh_aio' => [
            'label' => '15 kWh All-in-One (inkl. 5 kW Hybrid-WR)',
            'url' => 'https://werdu.de/tewaycell-15-kwh-all-in-one-lifepo4-solarbatterie-5-kw-hybrid-wechselrichter/',
            'image' => 'https://werdu.de/wp-content/uploads/2026/04/All-in-One-Gross-1000-1000.webp',
            'desc' => 'All-in-One Solarbatterie mit integriertem 5 kW Hybrid-Wechselrichter und App-Steuerung.',
            'specs' => 'Inkl. 5 kW Hybrid-Wechselrichter &bull; App-Steuerung &bull; Sofort einsatzbereit &bull; Plug & Play',
            'badge' => 'Bestseller',
            'order' => 4,
        ],

        // === NIEUWE PRODUCTEN PLEKKEN (9) ===
        'produkt_05' => [
            'label' => 'Produkt 5 — Noch nicht konfiguriert',
            'url' => '',
            'image' => '',
            'desc' => '',
            'specs' => '',
            'badge' => '',
            'order' => 5,
        ],
        'produkt_06' => [
            'label' => 'Produkt 6 — Noch nicht konfiguriert',
            'url' => '',
            'image' => '',
            'desc' => '',
            'specs' => '',
            'badge' => '',
            'order' => 6,
        ],
        'produkt_07' => [
            'label' => 'Produkt 7 — Noch nicht konfiguriert',
            'url' => '',
            'image' => '',
            'desc' => '',
            'specs' => '',
            'badge' => '',
            'order' => 7,
        ],
        'produkt_08' => [
            'label' => 'Produkt 8 — Noch nicht konfiguriert',
            'url' => '',
            'image' => '',
            'desc' => '',
            'specs' => '',
            'badge' => '',
            'order' => 8,
        ],
        'produkt_09' => [
            'label' => 'Produkt 9 — Noch nicht konfiguriert',
            'url' => '',
            'image' => '',
            'desc' => '',
            'specs' => '',
            'badge' => '',
            'order' => 9,
        ],
        'produkt_10' => [
            'label' => 'Produkt 10 — Noch nicht konfiguriert',
            'url' => '',
            'image' => '',
            'desc' => '',
            'specs' => '',
            'badge' => '',
            'order' => 10,
        ],
        'produkt_11' => [
            'label' => 'Produkt 11 — Noch nicht konfiguriert',
            'url' => '',
            'image' => '',
            'desc' => '',
            'specs' => '',
            'badge' => '',
            'order' => 11,
        ],
        'produkt_12' => [
            'label' => 'Produkt 12 — Noch nicht konfiguriert',
            'url' => '',
            'image' => '',
            'desc' => '',
            'specs' => '',
            'badge' => '',
            'order' => 12,
        ],
        'produkt_13' => [
            'label' => 'Produkt 13 — Noch nicht konfiguriert',
            'url' => '',
            'image' => '',
            'desc' => '',
            'specs' => '',
            'badge' => '',
            'order' => 13,
        ],
    ];

    // Laad opgeslagen productgegevens (naam, URL, afbeelding, specs, badge, volgorde)
    $saved = get_option('werdu_produkte_data', []);
    if (!empty($saved) && is_array($saved)) {
        foreach ($defaults as $id => &$data) {
            if (isset($saved[$id])) {
                foreach (['label', 'url', 'image', 'desc', 'specs', 'badge', 'order'] as $field) {
                    if (isset($saved[$id][$field])) {
                        $data[$field] = sanitize_text_field($saved[$id][$field]);
                    }
                }
            }
        }
    }

    // Sorteer op 'order'
    uasort($defaults, function($a, $b) {
        return ($a['order'] ?? 99) <=> ($b['order'] ?? 99);
    });

    return $defaults;
}

// ============================================================
// 5. WOO COMMERCE AUTO-SYNC FUNCTIES
// ============================================================

/**
 * Haal WooCommerce producten op en toon ze als keuzelijst
 */
function werdu_get_woocommerce_products() {
    if (!class_exists('WooCommerce')) {
        return [];
    }

    $args = [
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
    ];

    $query = new WP_Query($args);
    $products = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $product_id = get_the_ID();
            $product = wc_get_product($product_id);
            if (!$product) continue;

            $products[] = [
                'id' => $product_id,
                'name' => $product->get_name(),
                'url' => get_permalink($product_id),
                'image' => wp_get_attachment_url($product->get_image_id()),
                'regular_price' => $product->get_regular_price(),
                'sale_price' => $product->get_sale_price(),
                'price' => $product->get_price(),
                'sku' => $product->get_sku(),
                'short_description' => $product->get_short_description(),
            ];
        }
        wp_reset_postdata();
    }

    return $products;
}

/**
 * Synchroniseer een WooCommerce product naar een Werdu product-slot
 */
function werdu_sync_from_woocommerce($wc_product_id, $slot_id) {
    if (!class_exists('WooCommerce')) {
        return new WP_Error('no_woocommerce', 'WooCommerce ist nicht aktiv.');
    }

    $product = wc_get_product($wc_product_id);
    if (!$product) {
        return new WP_Error('product_not_found', 'WooCommerce Produkt nicht gefunden.');
    }

    // Haal productgegevens op
    $name = $product->get_name();
    $url = get_permalink($wc_product_id);
    $image = wp_get_attachment_url($product->get_image_id());
    $regular_price = $product->get_regular_price();
    $sale_price = $product->get_sale_price();
    $price = $product->get_price();
    $short_desc = $product->get_short_description();

    // Gebruik sale_price als actuele prijs, anders regular_price
    $current_price = !empty($sale_price) ? $sale_price : $price;
    $uvp = !empty($regular_price) ? $regular_price : $price;

    // Sla productgegevens op in het Preis Manager systeem
    $saved = get_option('werdu_produkte_data', []);
    $saved[$slot_id] = [
        'label' => $name,
        'url' => $url,
        'image' => $image ?: '',
        'desc' => $short_desc ?: '',
        'specs' => '', // Handmatig invullen na sync
        'badge' => '',
        'order' => intval($saved[$slot_id]['order'] ?? 99),
    ];
    update_option('werdu_produkte_data', $saved);

    // Sla prijzen op
    if (!empty($current_price)) {
        update_option('werdu_preis_' . $slot_id, $current_price);
    }
    if (!empty($uvp) && floatval($uvp) > floatval($current_price)) {
        update_option('werdu_uvp_' . $slot_id, $uvp);
    }

    return [
        'success' => true,
        'message' => 'Produkt "' . $name . '" erfolgreich synchronisiert.',
        'slot_id' => $slot_id,
        'preis' => $current_price,
        'uvp' => $uvp,
    ];
}

// ============================================================
// 6. ADMIN PAGINA
// ============================================================

function werdu_preise_admin_page() {
    $produkte = werdu_get_produkte();

    // === OPSLAAN: PRIJZEN ===
    if (isset($_POST['werdu_preise_save']) && check_admin_referer('werdu_preise_nonce')) {
        foreach ($produkte as $id => $info) {
            update_option('werdu_preis_' . $id, sanitize_text_field($_POST['preis_' . $id] ?? ''));
            update_option('werdu_uvp_' . $id, sanitize_text_field($_POST['uvp_' . $id] ?? ''));
        }
        update_option('werdu_preis_gueltig_bis', sanitize_text_field($_POST['preis_gueltig_bis'] ?? ''));
        echo '<div class="notice notice-success"><p><strong>Preise gespeichert!</strong> Alle Shortcodes, Schema und Produktboxen sind jetzt aktualisiert.</p></div>';
        $produkte = werdu_get_produkte(); // Herlaad
    }

    // === OPSLAAN: PRODUCTGEGEVENS (naam, URL, afbeelding, specs, badge, volgorde) ===
    if (isset($_POST['werdu_produkte_save']) && check_admin_referer('werdu_produkte_nonce')) {
        $saved = get_option('werdu_produkte_data', []);
        foreach ($produkte as $id => $info) {
            $saved[$id] = [
                'label' => sanitize_text_field($_POST['prod_label_' . $id] ?? $info['label']),
                'url' => esc_url_raw($_POST['prod_url_' . $id] ?? $info['url']),
                'image' => esc_url_raw($_POST['prod_image_' . $id] ?? $info['image']),
                'desc' => sanitize_textarea_field($_POST['prod_desc_' . $id] ?? $info['desc']),
                'specs' => sanitize_text_field($_POST['prod_specs_' . $id] ?? $info['specs']),
                'badge' => sanitize_text_field($_POST['prod_badge_' . $id] ?? $info['badge']),
                'order' => intval($_POST['prod_order_' . $id] ?? ($info['order'] ?? 99)),
            ];
        }
        update_option('werdu_produkte_data', $saved);
        echo '<div class="notice notice-success"><p><strong>Produktdaten gespeichert!</strong> Namen, URLs, Bilder, Specs, Badges und Reihenfolge wurden aktualisiert.</p></div>';
        $produkte = werdu_get_produkte(); // Herlaad
    }

    // === WOO COMMERCE SYNC ===
    $sync_message = '';
    $sync_error = '';
    if (isset($_POST['werdu_wc_sync']) && check_admin_referer('werdu_wc_sync_nonce')) {
        $wc_product_id = intval($_POST['wc_product_id'] ?? 0);
        $slot_id = sanitize_key($_POST['wc_slot_id'] ?? '');

        if ($wc_product_id > 0 && !empty($slot_id)) {
            $result = werdu_sync_from_woocommerce($wc_product_id, $slot_id);
            if (is_wp_error($result)) {
                $sync_error = $result->get_error_message();
            } else {
                $sync_message = $result['message'] . ' Preis: ' . werdu_format_preis($result['preis']) . ', UVP: ' . werdu_format_preis($result['uvp']);
            }
            $produkte = werdu_get_produkte(); // Herlaad
        } else {
            $sync_error = 'Bitte wählen Sie ein WooCommerce-Produkt und einen Slot aus.';
        }
    }

    // Haal WooCommerce producten op voor de dropdown
    $wc_products = werdu_get_woocommerce_products();
    $wc_available = class_exists('WooCommerce') && !empty($wc_products);

    ?>
    <div class="wrap">
        <h1 style="color:#ff6600;">&#9889; Werdu Preise verwalten</h1>
        <p style="font-size:15px;max-width:700px;">
            <strong>Preise hier einmal ändern – überall auf der Seite aktualisiert.</strong><br>
            Geben Sie die Preise ohne Punkt/Komma als Tausender ein (z.B. 2345). Das System formatiert automatisch.<br>
            <span style="color:#666;">Produkte 5-13 sind leer – füllen Sie Preis und UVP ein, um sie zu aktivieren.</span>
        </p>
        <hr style="margin:20px 0;">

        <?php if ($sync_message): ?>
        <div class="notice notice-success is-dismissible"><p><strong>&#10004; Sync erfolgreich:</strong> <?php echo esc_html($sync_message); ?></p></div>
        <?php endif; ?>
        <?php if ($sync_error): ?>
        <div class="notice notice-error is-dismissible"><p><strong>&#10008; Fehler:</strong> <?php echo esc_html($sync_error); ?></p></div>
        <?php endif; ?>

        <!-- === WOO COMMERCE AUTO-SYNC === -->
        <?php if ($wc_available): ?>
        <h2 style="margin-top:25px;">&#128260; WooCommerce Auto-Sync</h2>
        <p style="font-size:14px;max-width:700px;color:#666;">
            Wählen Sie ein WooCommerce-Produkt und einen leeren Slot. Name, Bild, URL, Preis und UVP werden automatisch übernommen.<br>
            <strong>Hinweis:</strong> Specs und Badge müssen danach manuell ergänzt werden.
        </p>
        <form method="post" action="" style="background:#f0f6fc;padding:20px;border-radius:8px;max-width:900px;margin-bottom:25px;">
            <?php wp_nonce_field('werdu_wc_sync_nonce'); ?>
            <table style="width:100%;">
                <tr>
                    <td style="padding-right:15px;">
                        <label style="font-weight:600;display:block;margin-bottom:5px;">WooCommerce Produkt:</label>
                        <select name="wc_product_id" style="width:100%;font-size:14px;padding:8px;">
                            <option value="">-- Produkt wählen --</option>
                            <?php foreach ($wc_products as $wc_prod): ?>
                            <option value="<?php echo esc_attr($wc_prod['id']); ?>">
                                <?php echo esc_html($wc_prod['name']); ?> 
                                (<?php echo esc_html($wc_prod['regular_price'] ? $wc_prod['regular_price'] . ' €' : 'kein Preis'); ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td style="padding-right:15px;">
                        <label style="font-weight:600;display:block;margin-bottom:5px;">Ziel-Slot:</label>
                        <select name="wc_slot_id" style="width:100%;font-size:14px;padding:8px;">
                            <option value="">-- Slot wählen --</option>
                            <?php foreach ($produkte as $id => $info): 
                                $is_empty = strpos($info['label'], 'Noch nicht konfiguriert') !== false;
                                $preis = get_option('werdu_preis_' . $id, '');
                            ?>
                            <option value="<?php echo esc_attr($id); ?>">
                                <?php echo esc_html($info['label']); ?> 
                                <?php echo $is_empty ? '(leer)' : '(' . werdu_format_preis($preis) . ')'; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td style="vertical-align:bottom;">
                        <input type="submit" name="werdu_wc_sync" 
                               class="button button-primary" value="&#128260; Sync" 
                               style="font-size:14px;padding:8px 20px;height:auto;background:#27ae60;border-color:#27ae60;">
                    </td>
                </tr>
            </table>
        </form>
        <?php else: ?>
        <div class="notice notice-warning" style="max-width:700px;">
            <p><strong>WooCommerce nicht gefunden oder keine Produkte.</strong><br>
            WooCommerce Auto-Sync ist deaktiviert. Installieren Sie WooCommerce und fügen Sie Produkte hinzu, um diese Funktion zu nutzen.</p>
        </div>
        <?php endif; ?>

        <hr style="margin:20px 0;">

        <!-- === TAB: PREISE === -->
        <h2 style="margin-top:25px;">&#128176; Preise & UVP</h2>
        <form method="post" action="">
            <?php wp_nonce_field('werdu_preise_nonce'); ?>

            <table class="widefat" style="max-width:1000px;">
                <thead>
                    <tr>
                        <th style="width:5%;">#</th>
                        <th style="width:30%;">Produkt</th>
                        <th style="width:20%;">Aktueller Preis (EUR)</th>
                        <th style="width:20%;">UVP (EUR)</th>
                        <th style="width:25%;">Ersparnis</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $row_num = 1;
                foreach ($produkte as $id => $info): 
                    $preis = get_option('werdu_preis_' . $id, '');
                    $uvp = get_option('werdu_uvp_' . $id, '');
                    $savings = '';
                    if ($preis && $uvp) {
                        $savings = number_format(floatval($uvp) - floatval($preis), 0, ',', '.') . ',- €';
                    }
                    $is_active = !empty($preis);
                    $row_style = $is_active ? '' : 'background:#f9f9f9;';
                    $label_style = strpos($info['label'], 'Noch nicht konfiguriert') !== false ? 'color:#999;' : 'font-weight:600;';
                ?>
                    <tr style="<?php echo esc_attr($row_style); ?>">
                        <td style="text-align:center;font-weight:700;color:#999;"><?php echo $row_num++; ?></td>
                        <td>
                            <span style="<?php echo esc_attr($label_style); ?>"><?php echo esc_html($info['label']); ?></span><br>
                            <code style="font-size:11px;color:#666;">[werdu_preis id="<?php echo esc_attr($id); ?>"]</code>
                        </td>
                        <td>
                            <input type="number" name="preis_<?php echo esc_attr($id); ?>" 
                                   value="<?php echo esc_attr($preis); ?>" 
                                   step="1" min="0" placeholder="2345"
                                   style="width:110px;font-size:14px;padding:5px;">
                            <?php if ($preis): ?>
                                <span style="color:#27ae60;font-weight:600;margin-left:6px;font-size:13px;">
                                    <?php echo werdu_format_preis($preis); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <input type="number" name="uvp_<?php echo esc_attr($id); ?>" 
                                   value="<?php echo esc_attr($uvp); ?>" 
                                   step="1" min="0" placeholder="3199"
                                   style="width:110px;font-size:14px;padding:5px;">
                            <?php if ($uvp): ?>
                                <span style="color:#999;text-decoration:line-through;margin-left:6px;font-size:13px;">
                                    <?php echo werdu_format_preis($uvp); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="color:#ff6600;font-weight:700;">
                            <?php echo $savings ? 'Sie sparen ' . esc_html($savings) : '<span style="color:#ccc;font-weight:400;">—</span>'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <table class="form-table" style="max-width:600px;margin-top:25px;">
                <tr>
                    <th scope="row">Preis gültig bis</th>
                    <td>
                        <input type="date" name="preis_gueltig_bis" 
                               value="<?php echo esc_attr(get_option('werdu_preis_gueltig_bis', '')); ?>"
                               style="width:180px;font-size:15px;padding:6px;">
                        <p class="description">Wird automatisch im Schema (JSON-LD) verwendet.</p>
                    </td>
                </tr>
            </table>

            <p style="margin-top:20px;">
                <input type="submit" name="werdu_preise_save" 
                       class="button button-primary" value="Preise speichern" 
                       style="font-size:16px;padding:8px 25px;height:auto;background:#ff6600;border-color:#ff6600;">
            </p>
        </form>

        <hr style="margin:30px 0;">

        <!-- === TAB: PRODUCTGEGEVENS === -->
        <h2>&#128295; Produktdaten bearbeiten</h2>
        <p style="font-size:14px;max-width:700px;color:#666;">
            Hier kannst du Produktnamen, URLs, Bilder, Specs, Badges und die Reihenfolge ändern. 
            Die Änderungen wirken sich auf das Schema (JSON-LD) und die Produktboxen aus.<br>
            <strong>Reihenfolge:</strong> Niedrigere Zahl = weiter oben in der Liste.
        </p>

        <form method="post" action="">
            <?php wp_nonce_field('werdu_produkte_nonce'); ?>

            <table class="widefat" style="max-width:1200px;">
                <thead>
                    <tr>
                        <th style="width:6%;">Reihen-<br>folge</th>
                        <th style="width:22%;">Produktname</th>
                        <th style="width:22%;">Produkt-URL</th>
                        <th style="width:22%;">Bild-URL</th>
                        <th style="width:14%;">Specs</th>
                        <th style="width:10%;">Badge</th>
                        <th style="width:4%;">ID</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($produkte as $id => $info): 
                    $is_empty = strpos($info['label'], 'Noch nicht konfiguriert') !== false;
                    $row_style = $is_empty ? 'background:#f9f9f9;' : '';
                ?>
                    <tr style="<?php echo esc_attr($row_style); ?>">
                        <td style="text-align:center;">
                            <input type="number" name="prod_order_<?php echo esc_attr($id); ?>" 
                                   value="<?php echo esc_attr($info['order'] ?? 99); ?>"
                                   style="width:55px;font-size:14px;padding:4px;text-align:center;">
                        </td>
                        <td>
                            <input type="text" name="prod_label_<?php echo esc_attr($id); ?>" 
                                   value="<?php echo esc_attr($info['label']); ?>"
                                   style="width:100%;font-size:13px;padding:5px;"
                                   placeholder="Produktname">
                        </td>
                        <td>
                            <input type="url" name="prod_url_<?php echo esc_attr($id); ?>" 
                                   value="<?php echo esc_attr($info['url']); ?>"
                                   style="width:100%;font-size:12px;padding:5px;"
                                   placeholder="https://werdu.de/produkt/">
                        </td>
                        <td>
                            <input type="url" name="prod_image_<?php echo esc_attr($id); ?>" 
                                   value="<?php echo esc_attr($info['image']); ?>"
                                   style="width:100%;font-size:12px;padding:5px;"
                                   placeholder="https://werdu.de/wp-content/uploads/...">
                            <?php if (!empty($info['image'])): ?>
                                <img src="<?php echo esc_url($info['image']); ?>" style="max-width:60px;max-height:40px;margin-top:4px;border:1px solid #ddd;border-radius:4px;" alt="">
                            <?php endif; ?>
                        </td>
                        <td>
                            <input type="text" name="prod_specs_<?php echo esc_attr($id); ?>" 
                                   value="<?php echo esc_attr($info['specs']); ?>"
                                   style="width:100%;font-size:12px;padding:5px;"
                                   placeholder="Specs...">
                        </td>
                        <td>
                            <input type="text" name="prod_badge_<?php echo esc_attr($id); ?>" 
                                   value="<?php echo esc_attr($info['badge']); ?>"
                                   style="width:100%;font-size:12px;padding:5px;"
                                   placeholder="Bestseller">
                        </td>
                        <td style="text-align:center;font-size:11px;color:#999;">
                            <code><?php echo esc_html($id); ?></code>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <p style="margin-top:20px;">
                <input type="submit" name="werdu_produkte_save" 
                       class="button button-primary" value="Produktdaten speichern" 
                       style="font-size:16px;padding:8px 25px;height:auto;background:#0099FF;border-color:#0099FF;">
            </p>
        </form>

        <hr style="margin:30px 0;">
        <h2>Shortcodes für Elementor</h2>
        <p>Kopieren Sie diese Shortcodes in Ihre Elementor HTML-Widgets:</p>

        <h3 style="margin-top:20px;">Aktive Produkte</h3>
        <table class="widefat" style="max-width:700px;">
            <thead><tr><th>Shortcode</th><th>Ergebnis</th></tr></thead>
            <tbody>
                <?php foreach ($produkte as $id => $info): 
                    $preis = werdu_format_preis(get_option('werdu_preis_' . $id, ''));
                    if ($preis === '—') continue;
                ?>
                <tr>
                    <td><code>[werdu_preis id="<?php echo esc_attr($id); ?>"]</code></td>
                    <td><?php echo esc_html($preis); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr><td><code>[werdu_preis_datum]</code></td><td><?php echo werdu_sc_preis_datum(); ?></td></tr>
            </tbody>
        </table>

        <h3 style="margin-top:20px;">Leere Produkte (Produkt 5–13)</h3>
        <table class="widefat" style="max-width:700px;">
            <thead><tr><th>Shortcode</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach ($produkte as $id => $info): 
                    $preis = werdu_format_preis(get_option('werdu_preis_' . $id, ''));
                    if ($preis !== '—') continue;
                ?>
                <tr>
                    <td><code>[werdu_preis id="<?php echo esc_attr($id); ?>"]</code></td>
                    <td>Leer – Preis eintragen zum Aktivieren</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2 style="margin-top:30px;">Automatisches Schema</h2>
        <p>Das JSON-LD Schema auf der Startseite wird <strong>automatisch</strong> mit allen Preisen und Produktdaten aktualisiert. Sie müssen das Schema nicht mehr anfassen.</p>
    </div>
    <?php
}

// ============================================================
// 7. HULPFUNCTIES
// ============================================================

function werdu_format_preis($preis) {
    if (empty($preis)) return '—';
    return number_format(floatval($preis), 0, ',', '.') . ',- €';
}
function werdu_format_preis_raw($preis) {
    if (empty($preis)) return '0.00';
    return number_format(floatval($preis), 2, '.', '');
}
function werdu_get_savings($id) {
    $preis = floatval(get_option('werdu_preis_' . $id, 0));
    $uvp = floatval(get_option('werdu_uvp_' . $id, 0));
    if ($preis > 0 && $uvp > 0 && $uvp > $preis) {
        return number_format($uvp - $preis, 0, ',', '.') . ',- €';
    }
    return '';
}

// ============================================================
// 8. DYNAMISCH SCHEMA — STARTSEITE
// ============================================================

add_action('wp_head', function() {
    if (!is_front_page() && !is_home()) return;

    $produkte = werdu_get_produkte();
    $gueltig = get_option('werdu_preis_gueltig_bis', date('Y-m-d', strtotime('+1 month')));

    $itemList = [];
    $position = 1;
    foreach ($produkte as $id => $info) {
        $preis = get_option('werdu_preis_' . $id, '');
        if (empty($preis)) continue;
        $itemList[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'item' => [
                '@type' => 'Product',
                'name' => $info['label'],
                'image' => $info['image'],
                'description' => $info['desc'],
                'brand' => ['@type' => 'Brand', 'name' => 'Werdu'],
                'offers' => [
                    '@type' => 'Offer',
                    'url' => $info['url'],
                    'price' => werdu_format_preis_raw($preis),
                    'priceCurrency' => 'EUR',
                    'availability' => 'https://schema.org/InStock',
                    'priceValidUntil' => $gueltig
                ]
            ]
        ];
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebSite',
                '@id' => 'https://werdu.de/#website',
                'url' => 'https://werdu.de/',
                'name' => 'WERDU.de - Heimspeicher & Solarbatterien',
                'publisher' => ['@id' => 'https://werdu.de/#organization'],
                'inLanguage' => 'de-DE',
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => ['@type' => 'EntryPoint', 'urlTemplate' => 'https://werdu.de/?s={search_term_string}'],
                    'query-input' => 'required name=search_term_string'
                ]
            ],
            [
                '@type' => 'WebPage',
                '@id' => 'https://werdu.de/#webpage',
                'url' => 'https://werdu.de/',
                'name' => 'Heimspeicher & Solarbatterien kaufen | WERDU.de',
                'description' => 'Heimspeicher und Solarbatterien mit LiFePO4 Technologie. 16-32 kWh, kostenloser Versand, 10 Jahre Garantie. Jetzt Energieunabhängigkeit starten!',
                'isPartOf' => ['@id' => 'https://werdu.de/#website'],
                'publisher' => ['@id' => 'https://werdu.de/#organization'],
                'breadcrumb' => ['@id' => 'https://werdu.de/#breadcrumb'],
                'datePublished' => '2026-01-15T08:00:00+01:00',
                'dateModified' => date('Y-m-d') . 'T08:00:00+02:00'
            ],
            [
                '@type' => 'Organization',
                '@id' => 'https://werdu.de/#organization',
                'name' => 'WERDU',
                'alternateName' => ['WERDU.de', 'Werdu'],
                'url' => 'https://werdu.de/',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => 'https://werdu.de/wp-content/uploads/2026/04/cropped-logo-werdu_143_140-1.webp',
                    'width' => 143,
                    'height' => 140
                ],
                'areaServed' => [
                    '@type' => 'Country',
                    'name' => 'Germany'
                ],
                'contactPoint' => [
                    '@type' => 'ContactPoint',
                    'contactType' => 'customer service',
                    'email' => 'service@werdu.de',
                    'availableLanguage' => ['German', 'Dutch'],
                    'areaServed' => 'DE'
                ],
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => 'c/o Impressumservice Dein-Impressum, Stettiner Str. 41',
                    'addressLocality' => 'Hungen',
                    'postalCode' => '35410',
                    'addressCountry' => 'DE'
                ],
                'vatID' => 'DE462239894'
            ],
            [
                '@type' => 'Service',
                '@id' => 'https://werdu.de/#service-heimspeicher',
                'name' => 'Heimspeicher & Solarbatterien',
                'serviceType' => 'Verkauf und Beratung von LiFePO4-Heimspeichern',
                'provider' => ['@id' => 'https://werdu.de/#organization'],
                'areaServed' => [
                    '@type' => 'Country',
                    'name' => 'Germany'
                ],
                'url' => 'https://werdu.de/'
            ],
            [
                '@type' => 'BreadcrumbList',
                '@id' => 'https://werdu.de/#breadcrumb',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Startseite', 'item' => 'https://werdu.de/']
                ]
            ],
            [
                '@type' => 'ItemList',
                '@id' => 'https://werdu.de/#productlist',
                'itemListElement' => $itemList
            ]
        ]
    ];

    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    echo "\n</script>\n";
}, 5);