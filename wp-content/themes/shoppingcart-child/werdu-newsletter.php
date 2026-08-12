<?php
/**
 * Plugin Name: Werdu Newsletter
 * Plugin URI:  https://werdu.de
 * Description: Simpler Newsletter-Abonnement-Speicher für werdu.de. Slaat alleen e-mail en optioneel naam op. DSGVO-konform.
 * Version:     1.0.0
 * Author:      ACC Heimspeicher
 * Author URI:  https://werdu.de
 * License:     GPL v2
 * Text Domain: werdu-newsletter
 */

// Voorkom directe toegang
if (!defined('ABSPATH')) {
    exit;
}

class Werdu_Newsletter {
    
    private $table_name;
    private $db_version = '1.0';
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'werdu_newsletter';
        
        // Hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        add_action('wp_ajax_werdu_newsletter_subscribe', array($this, 'ajax_subscribe'));
        add_action('wp_ajax_nopriv_werdu_newsletter_subscribe', array($this, 'ajax_subscribe'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_export'));
    }
    
    /**
     * Tabel aanmaken bij activatie
     */
    public function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            email varchar(100) NOT NULL,
            name varchar(100) DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            source varchar(50) DEFAULT 'website',
            subscribed_at datetime DEFAULT CURRENT_TIMESTAMP,
            confirmed_at datetime DEFAULT NULL,
            unsubscribed_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            KEY status (status)
        ) {$charset_collate};";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        update_option('werdu_newsletter_db_version', $this->db_version);
    }
    
    /**
     * AJAX: Nieuwe inschrijving
     */
    public function ajax_subscribe() {
        // Verificatie
        if (!isset($_POST['email']) || empty($_POST['email'])) {
            wp_send_json_error(array('message' => 'E-Mail ist erforderlich.'));
        }
        
        $email = sanitize_email($_POST['email']);
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        
        // Email validatie
        if (!is_email($email)) {
            wp_send_json_error(array('message' => 'Ungültige E-Mail-Adresse.'));
        }
        
        global $wpdb;
        
        // Check of email al bestaat
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$this->table_name} WHERE email = %s",
            $email
        ));
        
        if ($existing) {
            wp_send_json_success(array(
                'message' => 'Diese E-Mail ist bereits angemeldet.',
                'existing' => true
            ));
        }
        
        // IP en User Agent (voor beveiliging, niet voor marketing)
        $ip = $this->get_client_ip();
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '';
        $source = isset($_POST['source']) ? sanitize_text_field($_POST['source']) : 'website';
        
        // Insert
        $result = $wpdb->insert(
            $this->table_name,
            array(
                'email' => $email,
                'name' => $name ?: null,
                'status' => 'subscribed', // Of 'pending' voor double-opt-in
                'ip_address' => $ip,
                'user_agent' => $user_agent,
                'source' => $source,
                'subscribed_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            wp_send_json_error(array('message' => 'Datenbankfehler. Bitte später erneut versuchen.'));
        }
        
        // Optioneel: Bevestigingsmail sturen (double-opt-in)
        // $this->send_confirmation_email($email, $name);
        
        wp_send_json_success(array(
            'message' => 'Erfolgreich angemeldet.',
            'id' => $wpdb->insert_id
        ));
    }
    
    /**
     * Admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'Werdu Newsletter',
            'Newsletter',
            'manage_options',
            'werdu-newsletter',
            array($this, 'admin_page'),
            'dashicons-email-alt',
            30
        );
    }
    
    /**
     * Admin overzichtspagina
     */
    public function admin_page() {
        global $wpdb;
        
        // Stats
        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
        $subscribed = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE status = 'subscribed'");
        $pending = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE status = 'pending'");
        
        // Laatste 50 inschrijvingen
        $subscribers = $wpdb->get_results(
            "SELECT * FROM {$this->table_name} 
             ORDER BY subscribed_at DESC 
             LIMIT 50"
        );
        ?>
        <div class="wrap">
            <h1>📬 Werdu Newsletter Abonnenten</h1>
            
            <div style="background:#fff;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                <h2>Statistiken</h2>
                <p>
                    <strong>Gesamt:</strong> <?php echo esc_html($total); ?> | 
                    <strong>Aktiv:</strong> <?php echo esc_html($subscribed); ?> | 
                    <strong>Ausstehend:</strong> <?php echo esc_html($pending); ?>
                </p>
                <p>
                    <a href="<?php echo admin_url('admin.php?page=werdu-newsletter&export=csv'); ?>" class="button button-primary">
                        📥 CSV Exportieren
                    </a>
                </p>
            </div>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>E-Mail</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Quelle</th>
                        <th>Datum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subscribers as $sub): ?>
                    <tr>
                        <td><?php echo esc_html($sub->id); ?></td>
                        <td><?php echo esc_html($sub->email); ?></td>
                        <td><?php echo esc_html($sub->name ?: '—'); ?></td>
                        <td>
                            <span style="display:inline-block;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;
                                <?php echo $sub->status === 'subscribed' ? 'background:#E8F5E9;color:#27AE60;' : 'background:#FFF3E6;color:#FF6600;'; ?>">
                                <?php echo esc_html($sub->status); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html($sub->source); ?></td>
                        <td><?php echo esc_html(date('d.m.Y H:i', strtotime($sub->subscribed_at))); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    /**
     * CSV Export
     */
    public function handle_export() {
        if (!isset($_GET['export']) || $_GET['export'] !== 'csv' || !isset($_GET['page']) || $_GET['page'] !== 'werdu-newsletter') {
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Keine Berechtigung.');
        }
        
        global $wpdb;
        $subscribers = $wpdb->get_results("SELECT email, name, status, source, subscribed_at FROM {$this->table_name} ORDER BY subscribed_at DESC", ARRAY_A);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="werdu-newsletter-' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
        
        fputcsv($output, array('E-Mail', 'Name', 'Status', 'Quelle', 'Datum'));
        
        foreach ($subscribers as $sub) {
            fputcsv($output, $sub);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Client IP (achter load balancer)
     */
    private function get_client_ip() {
        $headers = array(
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }
}

// Initialize
new Werdu_Newsletter();