<?php
/*
Plugin Name: Custom Validation for CF7
Plugin URI:  https://momomedia.com.au/plugins/custom-validation-for-cf7/
Description: Adds advanced validation to Contact Form 7: blocks URLs, validates phone and email, with admin settings.
Version:     1.10
Author:      MOMO Media
Author URI:  https://momomedia.com.au
License:     GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: custom-validation-for-cf7
Domain Path: /languages
Requires at least: 5.2
Requires PHP: 7.2
Requires Plugins: contact-form-7
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

define( 'CF7VP_VERSION', '1.10' );

// ======================
// Admin Menu & Tabs
// ======================
add_action( 'admin_menu', 'cf7vp_add_admin_menu' );
function cf7vp_add_admin_menu() {
    add_menu_page(
        'Custom Validation for CF7',
        'CF7 Validation',
        'manage_options',
        'custom-validation-for-cf7',
        'cf7vp_dashboard_page',
        'dashicons-shield-alt',
        25
    );
}

// ======================
// Dashboard Page
// ======================
function cf7vp_dashboard_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $tab_nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
    $active_tab = 'settings';

    if ( isset( $_GET['tab'] ) && ! empty( $tab_nonce ) && wp_verify_nonce( $tab_nonce, 'cf7vp_tab_switch' ) ) {
        $active_tab = sanitize_key( $_GET['tab'] );
    }

    $tab_nonce_create = wp_create_nonce( 'cf7vp_tab_switch' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__( 'Custom Validation for CF7', 'custom-validation-for-cf7' ); ?></h1>
        <h2 class="nav-tab-wrapper">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=custom-validation-for-cf7&tab=settings&_wpnonce=' . $tab_nonce_create ) ); ?>" class="nav-tab <?php echo $active_tab === 'settings' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Settings', 'custom-validation-for-cf7' ); ?></a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=custom-validation-for-cf7&tab=credit&_wpnonce=' . $tab_nonce_create ) ); ?>" class="nav-tab <?php echo $active_tab === 'credit' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Credit', 'custom-validation-for-cf7' ); ?></a>
        </h2>
        <?php
        if ( $active_tab === 'settings' ) {
            cf7vp_settings_tab();
        } else {
            cf7vp_credit_tab();
        }
        ?>
    </div>
    <?php
}

// ======================
// Settings Tab
// ======================
function cf7vp_settings_tab() {
    if ( isset($_POST['cf7vp_save']) && check_admin_referer('cf7vp_settings_save', 'cf7vp_nonce') ) {
        if ( ! current_user_can( 'manage_options' ) ) return;

        update_option( 'cf7vp_enable_phone', isset($_POST['cf7vp_enable_phone']) ? 1 : 0 );
        update_option( 'cf7vp_enable_email', isset($_POST['cf7vp_enable_email']) ? 1 : 0 );
        update_option( 'cf7vp_enable_urlblock', isset($_POST['cf7vp_enable_urlblock']) ? 1 : 0 );
        update_option( 'cf7vp_enable_urlblock_all', isset($_POST['cf7vp_enable_urlblock_all']) ? 1 : 0 );

        $digits = isset($_POST['cf7vp_phone_digits']) ? intval($_POST['cf7vp_phone_digits']) : 10;
        $digits = max(4, min(15, $digits));
        update_option( 'cf7vp_phone_digits', $digits );

        $phone_msg = isset($_POST['cf7vp_phone_message']) ? sanitize_text_field( wp_unslash($_POST['cf7vp_phone_message']) ) : '';
        $email_msg = isset($_POST['cf7vp_email_message']) ? sanitize_text_field( wp_unslash($_POST['cf7vp_email_message']) ) : '';
        $url_msg   = isset($_POST['cf7vp_url_message'])   ? sanitize_text_field( wp_unslash($_POST['cf7vp_url_message']) )   : '';

        if ( $phone_msg === '' ) {
            /* translators: %d is the number of digits required for the phone number */
            $phone_msg = sprintf( __('Phone number must be exactly %d digits.', 'custom-validation-for-cf7'), $digits );
        }
        if ( $email_msg === '' ) $email_msg = __('Please enter a valid email address.', 'custom-validation-for-cf7');
        if ( $url_msg   === '' ) $url_msg   = __('URLs are not allowed in this field.', 'custom-validation-for-cf7');

        update_option( 'cf7vp_phone_message', $phone_msg );
        update_option( 'cf7vp_email_message', $email_msg );
        update_option( 'cf7vp_url_message', $url_msg );

        echo '<div class="updated"><p>' . esc_html__( 'Settings saved.', 'custom-validation-for-cf7' ) . '</p></div>';
    }

    $enable_phone   = (int) get_option('cf7vp_enable_phone', 1);
    $enable_email   = (int) get_option('cf7vp_enable_email', 1);
    $enable_url     = (int) get_option('cf7vp_enable_urlblock', 1);
    $enable_url_all = (int) get_option('cf7vp_enable_urlblock_all', 0);

    $phone_digits   = (int) get_option('cf7vp_phone_digits', 10);
    $phone_digits   = ($phone_digits < 4 || $phone_digits > 15) ? 10 : $phone_digits;

$phone_message  = get_option(
    'cf7vp_phone_message',
    sprintf(
/* translators: d: the exact number of digits required for the phone number */
        __('Phone number must be exactly %d digits.', 'custom-validation-for-cf7'),
        $phone_digits
    )
);

    $email_message  = get_option('cf7vp_email_message', __('Please enter a valid email address.', 'custom-validation-for-cf7') );
    $url_message    = get_option('cf7vp_url_message',   __('URLs are not allowed in this field.', 'custom-validation-for-cf7') );
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('cf7vp_settings_save', 'cf7vp_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e('Enable Rules', 'custom-validation-for-cf7'); ?></th>
                <td>
                    <label><input type="checkbox" name="cf7vp_enable_phone" <?php checked(1, $enable_phone); ?> /> <?php esc_html_e('Enable Phone Validation', 'custom-validation-for-cf7'); ?></label><br/>
                    <label><input type="checkbox" name="cf7vp_enable_email" <?php checked(1, $enable_email); ?> /> <?php esc_html_e('Enable Email Validation', 'custom-validation-for-cf7'); ?></label><br/>
                    <label><input type="checkbox" name="cf7vp_enable_urlblock" <?php checked(1, $enable_url); ?> /> <?php esc_html_e('Block URLs in Textareas Only', 'custom-validation-for-cf7'); ?></label><br/>
                    <label><input type="checkbox" name="cf7vp_enable_urlblock_all" <?php checked(1, $enable_url_all); ?> /> <?php esc_html_e('Block URLs in All Fields (Max Security)', 'custom-validation-for-cf7'); ?></label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Phone Number Digits', 'custom-validation-for-cf7'); ?></th>
                <td><input type="number" min="4" max="15" name="cf7vp_phone_digits" value="<?php echo esc_attr($phone_digits); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Phone Validation Message', 'custom-validation-for-cf7'); ?></th>
                <td><input type="text" name="cf7vp_phone_message" value="<?php echo esc_attr($phone_message); ?>" class="regular-text"/></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Email Validation Message', 'custom-validation-for-cf7'); ?></th>
                <td><input type="text" name="cf7vp_email_message" value="<?php echo esc_attr($email_message); ?>" class="regular-text"/></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('URL Block Message', 'custom-validation-for-cf7'); ?></th>
                <td><input type="text" name="cf7vp_url_message" value="<?php echo esc_attr($url_message); ?>" class="regular-text"/></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="cf7vp_save" class="button-primary" value="<?php esc_attr_e('Save Settings', 'custom-validation-for-cf7'); ?>">
        </p>
    </form>
    <?php
}

// ======================
// Credit Tab
// ======================
function cf7vp_credit_tab() {
    ?>
    <h2><?php echo esc_html__( 'Credit', 'custom-validation-for-cf7' ); ?></h2>
    <p><?php echo wp_kses_post( __( 'Developed by <strong>MOMO Media</strong>', 'custom-validation-for-cf7') ); ?></p>
    <p><?php echo esc_html__( 'Contact us for development or support.', 'custom-validation-for-cf7' ); ?></p>
    <?php
}

// ======================
// PHONE Validation
// ======================
add_filter('wpcf7_validate_tel',  'cf7vp_validate_phone', 20, 2);
add_filter('wpcf7_validate_tel*', 'cf7vp_validate_phone', 20, 2);

function cf7vp_validate_phone( $result, $tag ) {
    $enable = (int) get_option('cf7vp_enable_phone', 1);
    if ( ! $enable ) return $result;

    $tag = new WPCF7_FormTag($tag);
    if ( $tag->basetype !== 'tel' ) return $result;

    $name  = $tag->name;
    $value = filter_input(INPUT_POST, $name, FILTER_SANITIZE_STRING);
    $value = $value !== null ? trim($value) : '';

    $allowed_digits = (int) get_option('cf7vp_phone_digits', 10);
    $allowed_digits = ($allowed_digits < 4 || $allowed_digits > 15) ? 10 : $allowed_digits;

    if ( ! preg_match( '/^\d{' . $allowed_digits . '}$/', $value ) ) {
        $message = get_option(
            'cf7vp_phone_message',
            sprintf(
        /* translators: d: the exact number of digits required for the phone number */
                __('Phone number must be exactly %d digits.', 'custom-validation-for-cf7'),
                $allowed_digits
            )
        );
        $result->invalidate( $tag, esc_html( $message ) );
    }
    return $result;
}

// ======================
// EMAIL Validation
// ======================
add_filter('wpcf7_validate_email',  'cf7vp_validate_email', 20, 2);
add_filter('wpcf7_validate_email*', 'cf7vp_validate_email', 20, 2);

function cf7vp_validate_email( $result, $tag ) {
    $enable = (int) get_option('cf7vp_enable_email', 1);
    if ( ! $enable ) return $result;

    $tag = new WPCF7_FormTag($tag);
    if ( $tag->basetype !== 'email' ) return $result;

    $name  = $tag->name;
    $value = filter_input(INPUT_POST, $name, FILTER_SANITIZE_EMAIL);
    $value = $value !== null ? trim($value) : '';

    if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
        $message = get_option('cf7vp_email_message', __('Please enter a valid email address.', 'custom-validation-for-cf7'));
        $result->invalidate( $tag, esc_html( $message ) );
    }
    return $result;
}

// ======================
// URL Validation
// ======================
add_filter('wpcf7_validate', 'cf7vp_block_urls_all', 20, 2);

// Add a hidden nonce field to every CF7 form.
add_action( 'wpcf7_form_hidden_fields', function( $fields ) {
    $fields['cf7vp_nonce'] = wp_create_nonce( 'cf7vp_nonce_action' );
    return $fields;
});

// Validate fields and check nonce
function cf7vp_block_urls_all( $result, $tags ) {

    // Nonce check
    if ( ! isset( $_POST['cf7vp_nonce'] ) ||
         ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cf7vp_nonce'] ) ), 'cf7vp_nonce_action' ) ) {
        return $result;
    }

    $enable_all = (int) get_option( 'cf7vp_enable_urlblock_all', 0 );

    if ( ! $enable_all ) {
        return $result; // Only run when max security is ON
    }

    foreach ( $tags as $tag ) {
        $tag  = new WPCF7_FormTag( $tag );
        $name = $tag->name;

        if ( empty( $name ) ) {
            continue;
        }

        $value = isset( $_POST[$name] ) ? sanitize_text_field( wp_unslash( $_POST[$name] ) ) : '';

        if ( $value === '' ) {
            continue;
        }

        // &#10071; DO NOT block the email field
        if ( $tag->basetype === 'email' ) {
            continue;
        }

        // URL detection
        if ( preg_match( '/https?:\/\/|www\./i', $value ) ) {
            $message = get_option( 'cf7vp_url_message', __( 'URLs are not allowed in this field.', 'custom-validation-for-cf7' ) );
            $result->invalidate( $tag, esc_html( $message ) );
        }
    }

    return $result;
}


// ======================
// Block links & special characters in all text fields
// ======================
add_filter('wpcf7_validate_text',  'cf7vp_block_urls_all_text', 20, 2);
add_filter('wpcf7_validate_text*', 'cf7vp_block_urls_all_text', 20, 2);

function cf7vp_block_urls_all_text( $result, $tag ) {
    $tag = new WPCF7_FormTag($tag);
    $name = $tag->name;

    if ( empty( $name ) ) return $result;

    // Skip email fields
    if ( $tag->basetype === 'email' ) {
        return $result;
    }

    $value = isset($_POST[$name]) ? sanitize_text_field( wp_unslash($_POST[$name]) ) : '';

    if ( $value !== '' ) {
        // Block URLs, www, colon, and other unwanted characters
        if ( preg_match('/https?:\/\/|www\.|[:]/i', $value) ) {
            $message = get_option('cf7vp_url_message', __('No links or special characters allowed.', 'custom-validation-for-cf7'));
            $result->invalidate($tag, esc_html($message));
        }

        // Optional: limit length to 15 characters
        $max_length = 15;
        if ( strlen($value) > $max_length ) {
            $result->invalidate($tag, sprintf(__('Maximum %d characters allowed.', 'custom-validation-for-cf7'), $max_length));
        }
    }

    return $result;
}
