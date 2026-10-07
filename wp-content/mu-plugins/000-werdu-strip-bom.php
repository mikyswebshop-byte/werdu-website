<?php
/**
 * Plugin Name: Werdu Strip UTF-8 BOM
 * Description: Verhindert, dass ein UTF-8-BOM aus früh geladenen PHP-Dateien (z. B. wp-config.php) den HTML-Body beschädigt — zusammen mit Content-Encoding:gzip führt das in Safari iOS zu „kan raw-gegevens niet decoderen“.
 * Version: 1.0.0
 * Author: Werdu
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Strip a leading UTF-8 BOM from buffered HTML/text responses only.
 * Binary downloads are left untouched via content-type check when headers exist.
 */
function werdu_strip_utf8_bom_buffer($buffer) {
    if (!is_string($buffer) || $buffer === '') {
        return $buffer;
    }

    if (strncmp($buffer, "\xEF\xBB\xBF", 3) !== 0) {
        return $buffer;
    }

    if (function_exists('headers_list')) {
        foreach (headers_list() as $header) {
            if (stripos($header, 'Content-Type:') === 0) {
                $type = strtolower(substr($header, 13));
                if (strpos($type, 'text/') === false
                    && strpos($type, 'json') === false
                    && strpos($type, 'xml') === false
                    && strpos($type, 'javascript') === false
                ) {
                    return $buffer;
                }
                break;
            }
        }
    }

    return substr($buffer, 3);
}

if (PHP_SAPI !== 'cli' && !headers_sent()) {
    ob_start('werdu_strip_utf8_bom_buffer');
}
