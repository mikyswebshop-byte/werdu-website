<?php
// test-php.php — Diagnostiek voor auto-cache-pro.php
// PLAATS DIT IN: /var/www/vhosts/werdu.de/httpdocs/test-php.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>PHP Diagnostiek</h1>";
echo "<p>PHP Versie: " . phpversion() . "</p>";
echo "<p>Test 1: Basis syntax check...</p>";

// Test 1: Simpele echo
echo "<p style='color:green'>✓ Basis PHP werkt</p>";

// Test 2: Check of curl beschikbaar is
echo "<p>Test 2: cURL beschikbaar...</p>";
if (function_exists('curl_init')) {
    echo "<p style='color:green'>✓ cURL is beschikbaar</p>";
} else {
    echo "<p style='color:red'>✗ cURL ontbreekt!</p>";
}

// Test 3: Check of gzencode beschikbaar is
echo "<p>Test 3: gzencode beschikbaar...</p>";
if (function_exists('gzencode')) {
    echo "<p style='color:green'>✓ gzencode is beschikbaar</p>";
} else {
    echo "<p style='color:red'>✗ gzencode ontbreekt!</p>";
}

// Test 4: Check schrijfrechten cache map
echo "<p>Test 4: Cache map schrijfbaar...</p>";
$cache_dir = dirname(__FILE__) . '/wp-content/cache/werdu-auto-cache-pro/';
if (is_dir($cache_dir)) {
    if (is_writable($cache_dir)) {
        echo "<p style='color:green'>✓ Cache map bestaat en is schrijfbaar: " . $cache_dir . "</p>";
    } else {
        echo "<p style='color:red'>✗ Cache map bestaat maar is NIET schrijfbaar!</p>";
    }
} else {
    echo "<p style='color:red'>✗ Cache map bestaat NIET: " . $cache_dir . "</p>";
}

// Test 5: Probeer auto-cache-pro.php te parsen
echo "<p>Test 5: Syntax check auto-cache-pro.php...</p>";
$cache_file = dirname(__FILE__) . '/auto-cache-pro.php';
if (file_exists($cache_file)) {
    echo "<p>Bestand gevonden: " . $cache_file . "</p>";
    echo "<p>Grootte: " . filesize($cache_file) . " bytes</p>";
    
    // Check op veelvoorkomende syntax-problemen
    $content = file_get_contents($cache_file);
    
    // Check voor verkeerde quotes (curly quotes van Word/tekstverwerker)
    $bad_quotes = array('"', '"', ''', ''');
    $found_bad = false;
    foreach ($bad_quotes as $quote) {
        if (strpos($content, $quote) !== false) {
            echo "<p style='color:red'>✗ GEVONDEN: Verkeerd quote-teken in bestand: " . htmlspecialchars($quote) . "</p>";
            $found_bad = true;
        }
    }
    if (!$found_bad) {
        echo "<p style='color:green'>✓ Geen verkeerde quotes gevonden</p>";
    }
    
    // Check voor BOM (Byte Order Mark)
    if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
        echo "<p style='color:red'>✗ GEVONDEN: UTF-8 BOM aan begin van bestand!</p>";
    } else {
        echo "<p style='color:green'>✓ Geen BOM gevonden</p>";
    }
    
    // Check of bestand eindigt met ?>
    if (substr(trim($content), -2) !== '?>') {
        echo "<p style='color:orange'>⚠ Bestand eindigt niet met ?> (kan OK zijn)</p>";
    } else {
        echo "<p style='color:green'>✓ Bestand eindigt correct met ?></p>";
    }
    
    // Check aantal opening en closing PHP tags
    $open_tags = substr_count($content, '<?php');
    $close_tags = substr_count($content, '?>');
    echo "<p><?php tags: " . $open_tags . " | ?> tags: " . $close_tags . "</p>";
    
} else {
    echo "<p style='color:red'>✗ auto-cache-pro.php NIET gevonden!</p>";
}

echo "<hr><p><strong>Test 6: Probeer auto-cache-pro.php te includen (dit toont de exacte fout)...</strong></p>";

// Vang fouten op
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo "<p style='color:red'><strong>FOUT:</strong> " . htmlspecialchars($errstr) . " in " . htmlspecialchars($errfile) . " regel " . $errline . "</p>";
    return true;
});

try {
    include dirname(__FILE__) . '/auto-cache-pro.php';
} catch (Throwable $e) {
    echo "<p style='color:red'><strong>FATALE FOUT:</strong> " . htmlspecialchars($e->getMessage()) . " in " . htmlspecialchars($e->getFile()) . " regel " . $e->getLine() . "</p>";
}

restore_error_handler();

echo "<hr><p>Diagnostiek voltooid.</p>";
?>