<?php
/**
 * TEMPORARY ONE-OFF SCRIPT — fix-db-calc-urls.php
 * ------------------------------------------------
 * Scans wp_posts.post_content for hardcoded links/redirects to /kontakt/
 * (relative or absolute, incl. the ones calculateWerdu() writes into
 * #cta-link at runtime) and rewrites them to /beratung-anfragen/ directly
 * in the database.
 *
 * USAGE
 *   Dry run (default, makes NO changes):
 *     https://your-site.tld/fix-db-calc-urls.php?token=YOUR_TOKEN
 *     php fix-db-calc-urls.php
 *
 *   Apply changes for real (updates the database, then deletes itself):
 *     https://your-site.tld/fix-db-calc-urls.php?token=YOUR_TOKEN&apply=1
 *     php fix-db-calc-urls.php --apply
 *
 * SECURITY
 *   Requires TOKEN below (browser) or CLI execution. Delete this file from
 *   the server immediately after use — it self-deletes automatically after
 *   a successful --apply run, but verify it is gone.
 */

define( 'WERDU_FIX_TOKEN', 'werdu-fix-2026-kontakt-cleanup' );

$is_cli   = ( php_sapi_name() === 'cli' );
$is_apply = $is_cli
    ? in_array( '--apply', isset( $argv ) ? $argv : array(), true )
    : ( isset( $_GET['apply'] ) && $_GET['apply'] == '1' );

if ( ! $is_cli ) {
    $token = isset( $_GET['token'] ) ? $_GET['token'] : '';
    if ( ! hash_equals( WERDU_FIX_TOKEN, (string) $token ) ) {
        http_response_code( 403 );
        die( "Forbidden. Add ?token=YOUR_TOKEN to the URL (see source of this file).\n" );
    }
    header( 'Content-Type: text/plain; charset=utf-8' );
}

require_once __DIR__ . '/wp-load.php';

global $wpdb;

echo "=== werdu.de /kontakt/ -> /beratung-anfragen/ database cleanup ===\n";
echo $is_apply ? "MODE: APPLY (writing changes)\n\n" : "MODE: DRY RUN (no changes will be written; add apply=1 / --apply to write)\n\n";

// Ordered replacements: longest / most specific patterns first so shorter
// generic patterns below don't clobber content that was already rewritten.
$replacements = array(
    'https://werdu.de/kontakt/'  => '/beratung-anfragen/',
    'http://werdu.de/kontakt/'   => '/beratung-anfragen/',
    'https://www.werdu.de/kontakt/' => '/beratung-anfragen/',
    'https://werdu.de/kontakt'   => '/beratung-anfragen',
    'http://werdu.de/kontakt'    => '/beratung-anfragen',
    'href="/kontakt/"'           => 'href="/beratung-anfragen/"',
    "href='/kontakt/'"           => "href='/beratung-anfragen/'",
    'action="/kontakt/"'         => 'action="/beratung-anfragen/"',
    "action='/kontakt/'"         => "action='/beratung-anfragen/'",
    // Generic JS/HTML string-literal base paths, e.g. the dynamic
    // calculateWerdu() concatenation: '/kontakt/?kwh=' + kwh + ...
    "'/kontakt/"                 => "'/beratung-anfragen/",
    '"/kontakt/'                 => '"/beratung-anfragen/',
);

$posts = $wpdb->get_results(
    "SELECT ID, post_title, post_type, post_status, post_content
     FROM {$wpdb->posts}
     WHERE post_type NOT IN ('revision')
       AND post_status != 'trash'
       AND (
             post_content LIKE '%werdu.de/kontakt%'
          OR post_content LIKE '%/kontakt/%'
          OR post_content LIKE '%cta-link%'
       )"
);

if ( empty( $posts ) ) {
    echo "No posts found containing /kontakt/, werdu.de/kontakt, or cta-link. Nothing to do.\n";
    exit;
}

echo 'Found ' . count( $posts ) . " candidate post(s):\n\n";

$changed_count = 0;

foreach ( $posts as $post ) {
    $original = $post->post_content;
    $updated  = $original;
    $hits     = array();

    foreach ( $replacements as $search => $replace ) {
        $count = substr_count( $updated, $search );
        if ( $count > 0 ) {
            $updated = str_replace( $search, $replace, $updated );
            $hits[]  = "{$search} -> {$replace} (x{$count})";
        }
    }

    $label = "#{$post->ID} [{$post->post_type}/{$post->post_status}] \"{$post->post_title}\"";

    if ( $updated === $original ) {
        echo "- {$label}: matched search LIKE clause but no exact pattern replaced (needs manual check)\n";
        continue;
    }

    echo "- {$label}\n";
    foreach ( $hits as $hit ) {
        echo "    * {$hit}\n";
    }

    if ( $is_apply ) {
        $result = $wpdb->update(
            $wpdb->posts,
            array( 'post_content' => $updated ),
            array( 'ID' => $post->ID )
        );
        if ( $result !== false ) {
            clean_post_cache( $post->ID );
            echo "    -> SAVED\n";
            $changed_count++;
        } else {
            echo "    -> FAILED TO SAVE: " . $wpdb->last_error . "\n";
        }
    } else {
        $changed_count++;
    }
}

echo "\n=== Summary ===\n";
echo $is_apply
    ? "{$changed_count} post(s) updated in the database.\n"
    : "{$changed_count} post(s) WOULD be updated. Re-run with apply=1 (browser) or --apply (CLI) to write changes.\n";

if ( $is_apply && $changed_count > 0 ) {
    // Best-effort cache flush so visitors see the fix immediately.
    if ( function_exists( 'wp_cache_flush' ) ) {
        wp_cache_flush();
    }
}

if ( $is_apply ) {
    echo "\nSelf-deleting this script now for security...\n";
    $self = __FILE__;
    if ( @unlink( $self ) ) {
        echo "Deleted {$self}. Done.\n";
    } else {
        echo "WARNING: could not auto-delete {$self} — delete it manually right now.\n";
    }
}
