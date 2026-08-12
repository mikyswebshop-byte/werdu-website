<?php
/**
 * Plugin Name: Werdu Simple Cache
 * Description: HTML page cache — veilig, snel, stabiel. Automatisch 1× pro Tag legen + opwarmen.
 * Version:     4.4.0
 * Author:      Werdu
 */

if (!defined('ABSPATH')) exit;

class Werdu_Simple_Cache {
    private $dir;
    private $ttl = 604800;        // ← 7 Tage statt 24 Stunden
    private $max_files = 300;
    private $cron_hook = 'werdu_cache_daily_event';
    private $cron_schedule = 'werdu_daily';

    public function __construct() {
        $this->dir = WP_CONTENT_DIR . '/cache/werdu-simple/';
        add_action('init', [$this, 'init']);
    }

    public function init() {
        if (!is_dir($this->dir)) {
            wp_mkdir_p($this->dir);
        }

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

        add_filter('cron_schedules', [$this, 'add_schedules']);
        add_action('init', [$this, 'maybe_schedule_cron'], 30);
        add_action($this->cron_hook, [$this, 'clear_and_warm']);
    }

    public function add_schedules($schedules) {
        $schedules[$this->cron_schedule] = [
            'interval' => 86400,   // ← 24 Stunden statt 1 Stunde
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

        $file = $this->get_cache_file();
        $gz_file = $file . '.gz';

        if (file_exists($gz_file)) {
            $age = time() - filemtime($gz_file);
            if ($age < $this->ttl) {
                $html = file_get_contents($gz_file);
                if ($html && strlen($html) > 100) {
                    $this->send_headers('HIT', true);
                    echo $html;
                    exit;
                }
            }
        }

        if (file_exists($file)) {
            $age = time() - filemtime($file);
            if ($age < $this->ttl) {
                $html = file_get_contents($file);
                if ($html && strlen($html) > 500) {
                    $this->send_headers('HIT', false);
                    echo $html;
                    exit;
                }
            }
        }

        $this->send_headers('MISS', false);
        ob_start([$this, 'save_output']);
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
        return $this->dir . md5($url) . '.html';
    }

    private function send_headers($status, $is_gzip = false) {
        if (headers_sent()) return;
        header('X-Cache: ' . $status);
        if ($status === 'HIT') {
            header('Cache-Control: public, max-age=604800');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT');
            if ($is_gzip) {
                header('Content-Encoding: gzip');
                header('Vary: Accept-Encoding');
            }
        }
    }

    public function save_output($buffer) {
        if (empty($buffer) || strlen($buffer) < 500) {
            return $buffer;
        }

        $file = $this->get_cache_file();
        file_put_contents($file, $buffer, LOCK_EX);

        if (function_exists('gzencode')) {
            $gz = gzencode($buffer, 6);
            if ($gz) {
                file_put_contents($file . '.gz', $gz, LOCK_EX);
            }
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
        $ttl_days = round($this->ttl / 86400);
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
        $hash = md5($scheme . '://' . $host . $path);
        @unlink($this->dir . $hash . '.html');
        @unlink($this->dir . $hash . '.html.gz');
    }

    public function clear_all() {
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