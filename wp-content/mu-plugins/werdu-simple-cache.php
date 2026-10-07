<?php
/**
 * Plugin Name: Werdu Simple Cache
 * Description: HTML page cache — veilig, snel, stabiel. Automatisch 1× pro Tag legen + opwarmen.
 * Version:     4.5.0
 * Author:      Werdu
 */

if (!defined('ABSPATH')) exit;

class Werdu_Simple_Cache {
    private $dir;
    private $ttl = 604800;        // 7 Tage
    private $max_files = 300;
    private $cron_hook = 'werdu_cache_daily_event';
    private $cron_schedule = 'werdu_daily';
    private $plugin_ver = '4.5.0';

    public function __construct() {
        $this->dir = WP_CONTENT_DIR . '/cache/werdu-simple/';
        add_action('init', [$this, 'init']);
    }

    public function init() {
        if (!is_dir($this->dir)) {
            wp_mkdir_p($this->dir);
        }

        // v4.5.0: corrupt gzip HIT bodies (UTF-8 BOM + Content-Encoding: gzip)
        // braken Safari iOS ("kan raw-gegevens niet decoderen"). Purge once on upgrade.
        $this->maybe_upgrade_clear();

        if (current_user_can('manage_options')) {
            add_action('admin_bar_menu', [$this, 'admin_bar'], 100);
            add_action('wp_ajax_werdu_simple_cache_clear', [$this, 'ajax_clear']);
            add_action('wp_ajax_werdu_warmer_run', [$this, 'ajax_warm_run']);
        }

        add_action('template_redirect', [$this, 'serve'], PHP_INT_MAX);
        add_action('shutdown', [$this, 'shutdown_flush'], 1);
        add_action('save_post', [$this, 'clear_single'], 20);
        add_action('edit_post', [$this, 'clear_single'], 20);
        add_action('deleted_post', [$this, 'clear_all'], 20);

        // Edge/manual purge hooks from werdu-homepage-seo-upgrade.php
        add_action('litespeed_purge_all', [$this, 'clear_all'], 5);

        add_filter('cron_schedules', [$this, 'add_schedules']);
        add_action('init', [$this, 'maybe_schedule_cron'], 30);
        add_action($this->cron_hook, [$this, 'clear_and_warm']);
    }

    /**
     * One-shot purge when this mu-plugin version changes after deploy.
     */
    private function maybe_upgrade_clear() {
        $stored = get_option('werdu_simple_cache_ver');
        if ($stored === $this->plugin_ver) {
            return;
        }
        $this->clear_all();
        update_option('werdu_simple_cache_ver', $this->plugin_ver, false);
    }

    public function add_schedules($schedules) {
        $schedules[$this->cron_schedule] = [
            'interval' => 86400,
            'display'  => 'Täglich um Mitternacht'
        ];
        return $schedules;
    }

    public function maybe_schedule_cron() {
        // Alte stündliche Cron-Events entfernen (Migration von v4.3.0)
        $old_hook = 'werdu_cache_hourly_event';
        $old_timestamp = wp_next_scheduled($old_hook);
        if ($old_timestamp) {
            wp_unschedule_event($old_timestamp, $old_hook);
        }

        if (!wp_next_scheduled($this->cron_hook)) {
            wp_schedule_event(strtotime('tomorrow 03:00:00'), $this->cron_schedule, $this->cron_hook);
        }
    }

    public function clear_and_warm() {
        $this->clear_all();
        $this->warm_all();
    }

    public function serve() {
        if ($this->should_skip()) return;

        // Emergency: wipe ANY legacy .gz companions site-wide once per request
        // when still present (corrupt HIT bodies took werdu.de offline for Safari).
        $this->purge_legacy_gz_files();

        $file = $this->get_cache_file();

        // Serve plain HTML only. Never emit Content-Encoding: gzip from PHP.
        // Pre-compressed .gz HIT bodies conflicted with early UTF-8 BOM output and/or
        // Apache mod_deflate, so Safari iOS received Content-Encoding:gzip with a
        // non-gzip body (often only the BOM) → "kan raw-gegevens niet decoderen".
        if (file_exists($file)) {
            $age = time() - filemtime($file);
            if ($age < $this->ttl) {
                $html = file_get_contents($file);
                if ($html && strlen($html) > 500 && $this->is_sane_html_cache($html)) {
                    $this->discard_output_buffers();
                    $html = $this->strip_utf8_bom($html);
                    // Refuse to serve tiny/corrupt snapshots (BOM-only etc.).
                    if (strlen($html) < 500) {
                        @unlink($file);
                    } else {
                        $this->send_headers('HIT');
                        echo $html;
                        exit;
                    }
                } else {
                    @unlink($file);
                    @unlink($file . '.gz');
                }
            }
        }

        // Drop stale/corrupt companion .gz for this URL.
        $gz_file = $file . '.gz';
        if (file_exists($gz_file)) {
            @unlink($gz_file);
        }

        $this->send_headers('MISS');
        ob_start([$this, 'save_output']);
    }

    /**
     * Delete all *.html.gz leftovers from older plugin versions (one short scan).
     */
    private function purge_legacy_gz_files() {
        static $done = false;
        if ($done || !is_dir($this->dir)) {
            return;
        }
        $done = true;
        foreach (glob($this->dir . '*.gz') as $gz) {
            @unlink($gz);
        }
    }

    /**
     * Reject cache payloads that are clearly not a full HTML document.
     */
    private function is_sane_html_cache($html) {
        $probe = $this->strip_utf8_bom($html);
        $head = strtolower(substr($probe, 0, 64));
        return (strpos($head, '<!doctype') !== false || strpos($head, '<html') !== false);
    }

    private function strip_utf8_bom($value) {
        if (strncmp($value, "\xEF\xBB\xBF", 3) === 0) {
            return substr($value, 3);
        }
        return $value;
    }

    /**
     * Remove any buffered early output (classic cause: a PHP file saved with UTF-8 BOM)
     * so a cache HIT cannot prepend non-gzip bytes in front of the document.
     */
    private function discard_output_buffers() {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    private function should_skip() {
        if (is_admin()) return true;
        if (is_user_logged_in()) return true;
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') return true;
        if (!empty($_GET)) return true;
        if (defined('DOING_AJAX') && DOING_AJAX) return true;
        if (defined('REST_REQUEST') && REST_REQUEST) return true;
        if (is_customize_preview()) return true;

        $uri = $_SERVER['REQUEST_URI'];
        $skip = ['cart', 'checkout', 'my-account', 'wp-login', 'wp-admin', 'wc-', 'account', 'order', 'beratung', 'kontakt'];
        foreach ($skip as $s) {
            if (stripos($uri, $s) !== false) return true;
        }
        return false;
    }

    private function get_cache_file() {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $url = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $this->dir . md5($url . '|v=' . $this->content_version()) . '.html';
    }

    /**
     * Fingerprint gebaseerd op de laatste wijzigingsdatum van de mu-plugins
     * die de gerenderde output beïnvloeden (content-filters, /kontakt/-fix,
     * SEO-injectie). Zodra een van deze bestanden wordt aangepast/gedeployed
     * verandert deze fingerprint automatisch, waardoor de cache-KEY wijzigt
     * en oude, verouderde HTML-snapshots stilzwijgend worden genegeerd — een
     * MISS genereert direct een verse cache met de nieuwste output. Geen
     * handmatige "Cache legen"-klik meer nodig na elke deploy.
     */
    private function content_version() {
        static $version = null;
        if (null !== $version) return $version;

        $files = [
            WP_CONTENT_DIR . '/mu-plugins/werdu-homepage-seo-upgrade.php',
            WP_CONTENT_DIR . '/mu-plugins/fix-calculator-links.php',
            __FILE__,
        ];

        $stamp = 0;
        foreach ($files as $f) {
            if (file_exists($f)) {
                $stamp = max($stamp, filemtime($f));
            }
        }

        $version = (string) $stamp . '|c=' . $this->plugin_ver;
        return $version;
    }

    private function send_headers($status) {
        if (headers_sent()) return;
        header('X-Cache: ' . $status);
        // Never set Content-Encoding here — Apache/mod_deflate (or the host)
        // must be the only layer that compresses HTML responses.
        if ($status === 'HIT') {
            header('Cache-Control: public, max-age=604800');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT');
            header('Vary: Accept-Encoding');
        }
    }

    public function save_output($buffer) {
        if (empty($buffer) || strlen($buffer) < 500) {
            return $buffer;
        }

        $buffer = $this->strip_utf8_bom($buffer);
        if (!$this->is_sane_html_cache($buffer)) {
            return $buffer;
        }

        $file = $this->get_cache_file();
        file_put_contents($file, $buffer, LOCK_EX);

        // Remove legacy precompressed companions; PHP must not serve them.
        if (file_exists($file . '.gz')) {
            @unlink($file . '.gz');
        }

        return $buffer;
    }

    public function shutdown_flush() {
        if (ob_get_level() > 0) {
            ob_end_flush();
        }
    }

    public function admin_bar($bar) {
        $files = count(glob($this->dir . '*.html'));
        $bar->add_node([
            'id'    => 'werdu-cache-clear',
            'title' => 'Cache legen (' . $files . ')',
            'href'  => wp_nonce_url(admin_url('admin-ajax.php?action=werdu_simple_cache_clear'), 'wsc'),
            'meta'  => ['onclick' => 'fetch(this.href).then(r=>r.text()).then(t=>{alert(t);location.reload();});return false;']
        ]);
        $bar->add_node([
            'id'    => 'werdu-cache-warm',
            'title' => 'Cache opwarmen',
            'href'  => wp_nonce_url(admin_url('admin-ajax.php?action=werdu_warmer_run'), 'wwr'),
            'meta'  => ['onclick' => 'fetch(this.href).then(r=>r.text()).then(t=>alert(t));return false;']
        ]);
    }

    public function ajax_clear() {
        if (!current_user_can('manage_options')) wp_die('No access');
        if (!wp_verify_nonce($_REQUEST['_wpnonce'] ?? '', 'wsc')) wp_die('Invalid nonce');
        $before = count(glob($this->dir . '*'));
        $this->clear_all();
        $after = count(glob($this->dir . '*'));
        wp_die('Cache geleegd: ' . ($before - $after) . ' bestanden verwijderd');
    }

    public function ajax_warm_run() {
        if (!current_user_can('manage_options')) wp_die('No access');
        if (!wp_verify_nonce($_REQUEST['_wpnonce'] ?? '', 'wwr')) wp_die('Invalid nonce');
        $count = $this->warm_all();
        wp_die($count);
    }

    public function clear_single($post_id) {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        $post = get_post($post_id);
        if (!$post || !in_array($post->post_status, ['publish', 'private'])) return;

        $url = get_permalink($post_id);
        $scheme = parse_url($url, PHP_URL_SCHEME) ?: 'https';
        $host = parse_url($url, PHP_URL_HOST);
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $hash = md5($scheme . '://' . $host . $path . '|v=' . $this->content_version());
        @unlink($this->dir . $hash . '.html');
        @unlink($this->dir . $hash . '.html.gz');
    }

    public function clear_all() {
        if (!is_dir($this->dir)) {
            return;
        }
        foreach (glob($this->dir . '*') as $f) {
            @unlink($f);
        }
    }

    public function warm_all() {
        $urls = [home_url('/')];

        $post_types = get_post_types(['public' => true], 'names');
        unset($post_types['attachment']);

        foreach ($post_types as $pt) {
            $items = get_posts([
                'post_type'      => $pt,
                'post_status'    => 'publish',
                'posts_per_page' => 999,
                'fields'         => 'ids',
            ]);
            foreach ($items as $id) {
                $url = get_permalink($id);
                if ($url && $url !== home_url('/')) {
                    $urls[] = $url;
                }
            }
        }

        $taxonomies = get_taxonomies(['public' => true], 'names');
        foreach ($taxonomies as $tax) {
            $terms = get_terms(['taxonomy' => $tax, 'hide_empty' => true, 'fields' => 'ids']);
            if (!is_wp_error($terms)) {
                foreach ($terms as $term_id) {
                    $url = get_term_link($term_id, $tax);
                    if (!is_wp_error($url)) {
                        $urls[] = $url;
                    }
                }
            }
        }

        $urls = array_unique($urls);
        $total = count($urls);
        $success = 0;

        foreach ($urls as $url) {
            wp_remote_get($url, [
                'timeout'   => 3,
                'sslverify' => false,
                'blocking'  => false,
            ]);
            $success++;
            usleep(50000);
        }

        return $success . ' von ' . $total . ' Seiten aufgewaermt';
    }
}

new Werdu_Simple_Cache();
