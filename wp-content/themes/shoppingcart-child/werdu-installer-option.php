<?php
/**
 * WERDU — Optionale Montagepartner-Vermittlung (P2)
 * Non-binding market guidance (Juli 2026): ca. 300–500 €
 * No cart fee — selection stored as order meta only.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WERDU_INSTALLER_META_KEY', '_werdu_installer_requested' );
define(
    'WERDU_INSTALLER_LABEL',
    'Auf Wunsch: Vermittlung eines lokalen Montage-Partners für den schnellen DC- & Kommunikationsanschluss (Plus/Minus-Kabel & Datenkabel). Richtwert nach Marktcheck (Juli 2026): ca. 300 € - 500 € Montageaufwand direkt mit dem Installateur.'
);

/**
 * Session helpers
 */
function werdu_installer_get_session_flag() {
    if ( ! function_exists( 'WC' ) || ! WC()->session ) {
        return 'no';
    }
    $val = WC()->session->get( 'werdu_installer_requested' );
    return ( $val === 'yes' ) ? 'yes' : 'no';
}

function werdu_installer_set_session_flag( $yes ) {
    if ( ! function_exists( 'WC' ) || ! WC()->session ) {
        return;
    }
    WC()->session->set( 'werdu_installer_requested', $yes ? 'yes' : 'no' );
}

/**
 * Shared checkbox markup
 */
function werdu_installer_render_checkbox( $context = 'checkout', $checked = false ) {
    $id = ( $context === 'product' ) ? 'werdu_installer_product' : 'werdu_installer_checkout';
    $name = ( $context === 'product' ) ? 'werdu_installer_product' : 'werdu_installer_checkout';
    ?>
    <div class="werdu-installer-option werdu-installer-option--<?php echo esc_attr( $context ); ?>">
        <label class="werdu-installer-option__label" for="<?php echo esc_attr( $id ); ?>">
            <input
                type="checkbox"
                class="werdu-installer-option__input"
                name="<?php echo esc_attr( $name ); ?>"
                id="<?php echo esc_attr( $id ); ?>"
                value="1"
                <?php checked( $checked ); ?>
            />
            <span class="werdu-installer-option__text"><?php echo esc_html( WERDU_INSTALLER_LABEL ); ?></span>
        </label>
        <p class="werdu-installer-option__note">
            Unverbindlicher Richtwert — die Abrechnung erfolgt direkt mit dem Installateur. Keine Zusatzgebühr im Warenkorb.
        </p>
    </div>
    <?php
}

/**
 * Minimal CSS for product + checkout
 */
function werdu_installer_enqueue_assets() {
    if ( is_admin() ) {
        return;
    }
    if ( ! ( is_product() || is_checkout() ) ) {
        return;
    }

    $css = '
.werdu-installer-option{margin:1.25rem 0;padding:1rem 1.1rem;border:2px solid #FF6600;border-radius:12px;background:linear-gradient(135deg,#FFF8F5 0%,#F0F8FF 100%);box-sizing:border-box}
.werdu-installer-option__label{display:flex;gap:12px;align-items:flex-start;cursor:pointer;margin:0;font-weight:600;color:#1a1a2e;line-height:1.45}
.werdu-installer-option__input{margin-top:4px;flex-shrink:0;width:18px;height:18px;accent-color:#FF6600}
.werdu-installer-option__text{font-size:0.95rem}
.werdu-installer-option__note{margin:0.65rem 0 0 30px;font-size:0.82rem;color:#64748b;line-height:1.4}
@media(max-width:600px){.werdu-installer-option__note{margin-left:0}}
';
    wp_register_style( 'werdu-installer-option', false, array(), null );
    wp_enqueue_style( 'werdu-installer-option' );
    wp_add_inline_style( 'werdu-installer-option', $css );
}
add_action( 'wp_enqueue_scripts', 'werdu_installer_enqueue_assets', 30 );

/* ============================================================
   PRODUCT PAGE
   ============================================================ */
function werdu_installer_product_checkbox() {
    if ( ! is_product() ) {
        return;
    }
    werdu_installer_render_checkbox( 'product', werdu_installer_get_session_flag() === 'yes' );
}
add_action( 'woocommerce_before_add_to_cart_button', 'werdu_installer_product_checkbox', 15 );

/**
 * Persist product-page choice into cart item + session
 */
function werdu_installer_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
    $requested = isset( $_POST['werdu_installer_product'] ) && $_POST['werdu_installer_product'] === '1';
    werdu_installer_set_session_flag( $requested );
    // Session is authoritative for checkout; cart item stores the choice for reference only.
    $cart_item_data['werdu_installer_requested'] = $requested ? 'yes' : 'no';
    return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'werdu_installer_add_cart_item_data', 10, 3 );

function werdu_installer_get_cart_item_from_session( $cart_item, $values ) {
    if ( isset( $values['werdu_installer_requested'] ) ) {
        $cart_item['werdu_installer_requested'] = $values['werdu_installer_requested'];
        if ( $values['werdu_installer_requested'] === 'yes' ) {
            werdu_installer_set_session_flag( true );
        }
    }
    return $cart_item;
}
add_filter( 'woocommerce_get_cart_item_from_session', 'werdu_installer_get_cart_item_from_session', 10, 2 );

/* ============================================================
   CHECKOUT
   ============================================================ */
function werdu_installer_checkout_field() {
    static $rendered = false;

    if ( $rendered || is_admin() ) {
        return;
    }
    if ( ! is_checkout() || is_wc_endpoint_url( 'order-received' ) ) {
        return;
    }

    $rendered = true;
    $checked  = werdu_installer_get_session_flag() === 'yes';

    // If any cart item already requested installer, pre-check
    if ( ! $checked && function_exists( 'WC' ) && WC()->cart ) {
        foreach ( WC()->cart->get_cart() as $item ) {
            if ( ! empty( $item['werdu_installer_requested'] ) && $item['werdu_installer_requested'] === 'yes' ) {
                $checked = true;
                break;
            }
        }
    }

    echo '<div id="werdu_installer_checkout_wrap">';
    werdu_installer_render_checkbox( 'checkout', $checked );
    echo '</div>';
}
add_action( 'woocommerce_review_order_before_submit', 'werdu_installer_checkout_field', 15 );
add_action( 'woocommerce_after_order_notes', 'werdu_installer_checkout_field', 5 );

/**
 * Save order meta — no fee added
 */
function werdu_installer_save_order_meta( $order_id ) {
    if ( ! $order_id ) {
        return;
    }

    $requested = ( isset( $_POST['werdu_installer_checkout'] ) && $_POST['werdu_installer_checkout'] === '1' );
    $value     = $requested ? 'yes' : 'no';

    update_post_meta( $order_id, WERDU_INSTALLER_META_KEY, $value );

    if ( function_exists( 'wc_get_order' ) ) {
        $order = wc_get_order( $order_id );
        if ( $order ) {
            $order->update_meta_data( WERDU_INSTALLER_META_KEY, $value );
            $order->save();
        }
    }

    werdu_installer_set_session_flag( $requested );
}
add_action( 'woocommerce_checkout_update_order_meta', 'werdu_installer_save_order_meta', 20 );

/* HPOS-compatible checkout order object hook */
function werdu_installer_save_order_meta_hpos( $order ) {
    if ( ! $order || ! is_a( $order, 'WC_Order' ) ) {
        return;
    }
    $requested = ( isset( $_POST['werdu_installer_checkout'] ) && $_POST['werdu_installer_checkout'] === '1' );
    $value     = $requested ? 'yes' : 'no';
    $order->update_meta_data( WERDU_INSTALLER_META_KEY, $value );
}
add_action( 'woocommerce_checkout_create_order', 'werdu_installer_save_order_meta_hpos', 20 );

/**
 * Admin order display
 */
function werdu_installer_admin_order_meta( $order ) {
    if ( ! $order ) {
        return;
    }
    $val = $order->get_meta( WERDU_INSTALLER_META_KEY );
    if ( $val === '' ) {
        $val = get_post_meta( $order->get_id(), WERDU_INSTALLER_META_KEY, true );
    }
    if ( $val === '' ) {
        return;
    }
    $label = ( $val === 'yes' )
        ? 'Ja — Montagepartner-Vermittlung gewünscht (Richtwert 300–500 €)'
        : 'Nein — reine Gerätelieferung';
    echo '<p><strong>Montagepartner-Vermittlung:</strong> ' . esc_html( $label ) . '</p>';
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'werdu_installer_admin_order_meta', 20 );

/**
 * Customer / admin emails
 */
function werdu_installer_email_order_meta( $order, $sent_to_admin = false, $plain_text = false, $email = null ) {
    if ( ! $order ) {
        return;
    }
    $val = $order->get_meta( WERDU_INSTALLER_META_KEY );
    if ( $val === '' ) {
        $val = get_post_meta( $order->get_id(), WERDU_INSTALLER_META_KEY, true );
    }
    if ( $val === '' ) {
        return;
    }

    $label = ( $val === 'yes' )
        ? 'Ja — Montagepartner-Vermittlung gewünscht (Richtwert Juli 2026: ca. 300–500 €, Abrechnung direkt mit Installateur)'
        : 'Nein — reine Gerätelieferung';

    if ( $plain_text ) {
        echo "\n" . 'Montagepartner-Vermittlung: ' . $label . "\n";
        return;
    }

    echo '<p style="margin:16px 0;padding:12px 14px;border-left:4px solid #FF6600;background:#FFF8F5;"><strong>Montagepartner-Vermittlung:</strong> ' . esc_html( $label ) . '</p>';
}
add_action( 'woocommerce_email_after_order_table', 'werdu_installer_email_order_meta', 20, 4 );

/**
 * Thank-you / order details (frontend)
 */
function werdu_installer_order_details( $order ) {
    werdu_installer_admin_order_meta( $order );
}
add_action( 'woocommerce_order_details_after_order_table', 'werdu_installer_order_details', 20 );
