<?php
/**
 * Plugin Name: WERDU Homepage SEO & AIO Upgrade
 * Description: Injecteert de Hero H1/intro en het uitgebreide SEO/AIO-contentblok (ToC, vergelijkingstabel, FAQ, JSON-LD) op de homepage — direct onder de Heimspeicher-rechner sectie — met een clean, high-end designsysteem (CSS-variabelen, borderless tabel, subtiele focus-states) zonder de bestaande Elementor-content, calculator-logica of styling te veranderen.
 * Version: 4.0
 * Author: Michael van der Veen
 * Network: false
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Deterministische asset-/cache-versie op basis van de laatste wijzigingsdatum
 * van dit bestand (filemtime), NIET time(). Een live time()-stempel zou bij
 * elke pageload een "nieuwe versie" opleveren en zo alle caching permanent
 * uitschakelen; filemtime() verandert alleen wanneer dit bestand daadwerkelijk
 * wordt aangepast — precies wat je wilt voor cache-busting.
 */
if ( ! defined( 'WERDU_ASSET_VER' ) ) {
    define( 'WERDU_ASSET_VER', (string) @filemtime( __FILE__ ) );
}

/**
 * Extra vangnet bovenop de content_version()-cachebuster in
 * werdu-simple-cache.php: zodra WERDU_ASSET_VER wijzigt (dus zodra dit
 * bestand opnieuw wordt gedeployed), sturen we éénmalig een
 * "X-LiteSpeed-Purge: *"-header mee. Dat is een native LiteSpeed Web
 * Server-feature die werkt ongeacht of het LSCache-plugin actief is; op elke
 * andere hostingstack wordt de header simpelweg genegeerd (geen risico).
 */
add_action( 'init', 'werdu_home_seo_maybe_purge_edge_cache', 1 );
function werdu_home_seo_maybe_purge_edge_cache() {
    // Handmatige purge-trigger: ?purge_werdu_cache aan een URL toevoegen
    // forceert direct een cache-purge, los van de automatische check hieronder.
    if ( isset( $_GET['purge_werdu_cache'] ) ) {
        werdu_home_seo_purge_edge_cache();
        return;
    }

    // Automatische purge: zodra WERDU_ASSET_VER wijzigt (dus zodra dit
    // bestand opnieuw wordt gedeployed), één keer purgen.
    $last_ver = get_option( 'werdu_home_seo_asset_ver' );
    if ( $last_ver === WERDU_ASSET_VER ) {
        return;
    }
    update_option( 'werdu_home_seo_asset_ver', WERDU_ASSET_VER, false );
    werdu_home_seo_purge_edge_cache();
}

function werdu_home_seo_purge_edge_cache() {
    if ( ! headers_sent() ) {
        header( 'X-LiteSpeed-Purge: *' );
    }
    if ( function_exists( 'litespeed_purge_all' ) ) {
        litespeed_purge_all();
    }
    do_action( 'litespeed_purge_all' );
}

/**
 * LET OP: op de live homepage bestaat geen element met class "werdu-calc-wrap".
 * De Heimspeicher-rechner staat in <section class="werdu-calc-section" id="solarbatterie-rechner">,
 * direct gevolgd door <section class="werdu-seo-section">. Deze twee markers worden
 * daarom gebruikt om het nieuwe contentblok exact op de gevraagde plek (direct onder
 * de rekenmodule) te injecteren.
 */
define( 'WERDU_HOME_CALC_SECTION_MARKER', '<section class="werdu-calc-section"' );
define( 'WERDU_HOME_AFTER_CALC_MARKER', '<section class="werdu-seo-section">' );

/**
 * Nooit een hardgecodeerde host gebruiken: altijd de huidige omgeving
 * (test of productie) via home_url().
 */
function werdu_home_seo_beratung_url() {
    return home_url( '/beratung-anfragen/' );
}

function werdu_home_seo_rechner_url() {
    return home_url( '/solarbatterie-rechner/' );
}

// ============================================
// BASE CSS & INTERACTION SYSTEM
// ============================================

/**
 * High-end designsysteem: CSS-variabelen, borderless tabel, pill-vormige
 * primary button, reduced-motion support en subtiele focus-glow op de
 * bestaande (Elementor-gerenderde) calculator-velden. Puur additieve CSS —
 * er wordt geen bestaande class verwijderd of overschreven buiten deze
 * nieuwe/eigen selectors.
 */
function werdu_home_seo_base_css() {
    return 'body.home .werdu-btn-glow,body.home .btn-3d,body.home .btn-werdu-primary,body.home .werdu-btn-primary{box-shadow:0 4px 14px rgba(224,85,0,.25)!important;text-shadow:none!important;filter:none!important}';
    return <<<'CSS'
body.home,
body.home #page,
body.home .site-content-contain,
body.home #content,
body.home .site-content {
  background-color: #f8fafc !important;
  color: #0f172a;
}

body.home a.skip-link,
body.home .skip-link {
  position: absolute !important;
  width: 1px !important;
  height: 1px !important;
  overflow: hidden !important;
  clip: rect(0, 0, 0, 0) !important;
}
body.home a.skip-link:focus,
body.home .skip-link:focus,
body.home .screen-reader-text:focus {
  display: inline-block !important;
  position: absolute !important;
  left: 8px !important;
  top: 8px !important;
  z-index: 100001 !important;
  width: auto !important;
  height: auto !important;
  clip: auto !important;
  background: #0f172a !important;
  color: #ffffff !important;
  padding: 8px 14px !important;
}

:root {
  --werdu-bg: #FFFFFF;
  --werdu-bg-subtle: #F8FAFC;
  --werdu-text: #0F172A;
  --werdu-muted: #475569;
  --werdu-orange: #FF6600;
  --werdu-orange-hover: #E05500;
  --werdu-border: #E2E8F0;
  --werdu-radius: 16px;
  --werdu-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05);
  /* Lichte, natuurlijke sfeer (Design System 3.1 — performance-first, geen
     blur-filters meer; alleen lichte, goedkope gradients/borders/shadows). */
  --werdu-sky-top: #EAF5FF;
  --werdu-sky-bottom: #FFFFFF;
  --werdu-sky-bg: linear-gradient(180deg, #EDF5FF 0%, #FFFFFF 100%);
  --werdu-card-border: #E2E8F0;
  /* Design System 3.3 — "High-Visual": gloeiende knoppen/kaarten. Statische
     box-shadow + een eenmalige hover-transitie kosten niets tijdens scroll
     (geen backdrop-filter, geen infinite animatie) en botsen dus niet met
     de performance-fix uit 3.1. */
  --werdu-orange-glow: rgba(255, 102, 0, 0.45);
  --werdu-glow-shadow: 0 8px 25px var(--werdu-orange-glow), 0 0 15px rgba(255, 102, 0, 0.3);
}

.werdu-seo-container {
  max-width: 1140px;
  margin: 0 auto;
  padding: 40px 20px;
  font-family: system-ui, -apple-system, sans-serif;
  color: var(--werdu-text);
  line-height: 1.75;
  /* Frisse, lichte atmosfeer: zacht hemelsblauw vervloeiend naar wit */
  background: linear-gradient(180deg, var(--werdu-sky-top) 0%, var(--werdu-sky-bottom) 480px);
  border-radius: 28px;
}

.werdu-seo-container h2 {
  font-size: 1.8rem;
  font-weight: 800;
  letter-spacing: -0.01em;
  color: var(--werdu-text);
  margin-top: 48px;
}

.werdu-seo-container h3 {
  color: var(--werdu-text);
  font-weight: 700;
}

.werdu-seo-container p {
  color: var(--werdu-muted);
}

.werdu-hero-intro {
  text-align: center;
  max-width: 800px;
  margin: 0 auto 48px auto;
  padding: 40px 24px;
  /* Zacht hemelsblauw-naar-wit verloop, GEEN backdrop-filter/blur (performance) */
  background: var(--werdu-sky-bg);
  border: 1px solid #E0EEFF;
  border-radius: var(--werdu-radius);
  box-shadow: 0 10px 25px -5px rgba(0, 102, 204, 0.05);
}

.werdu-hero-intro h1 {
  font-size: 2.25rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  margin-bottom: 12px;
  color: var(--werdu-text);
}

.werdu-hero-intro p {
  color: var(--werdu-muted);
  font-size: 1.05rem;
  margin: 0;
}

.werdu-toc-box {
  background: var(--werdu-bg-subtle);
  border: 1px solid var(--werdu-border);
  border-radius: var(--werdu-radius);
  padding: 28px;
  margin: 40px 0;
}

.werdu-toc-box h2 {
  margin-top: 0;
  font-size: 1.2rem;
}

.werdu-toc-box ul {
  list-style: none;
  margin: 0;
  padding: 0;
  display: grid;
  gap: 10px;
}

.werdu-toc-box a {
  color: var(--werdu-text);
  font-weight: 600;
  text-decoration: none;
  border-bottom: 1px solid transparent;
  transition: border-color 0.2s ease, color 0.2s ease;
}

.werdu-toc-box a:hover {
  color: var(--werdu-orange);
  border-bottom-color: var(--werdu-orange);
}

.werdu-btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 14px 28px;
  background-color: var(--werdu-orange);
  color: #FFFFFF !important;
  font-weight: 600;
  border-radius: 9999px;
  text-decoration: none !important;
  transition: all 0.2s ease;
  box-shadow: 0 4px 14px rgba(255, 102, 0, 0.3);
  border: none;
}

.werdu-btn-primary:hover {
  background-color: var(--werdu-orange-hover);
  transform: translateY(-2px);
}

.werdu-highlight-card {
  background: #FFFFFF;
  border: 2px solid var(--werdu-orange);
  color: var(--werdu-text);
  padding: 32px;
  border-radius: var(--werdu-radius);
  margin: 36px 0;
  text-align: center;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
}

.werdu-highlight-card * {
  color: var(--werdu-text);
}

.werdu-highlight-card h3 {
  color: var(--werdu-text);
  margin-top: 0;
  font-size: 1.4rem;
}

.werdu-highlight-card p {
  color: var(--werdu-muted);
  margin-bottom: 20px;
}

.werdu-card-soft {
  background: var(--werdu-bg-subtle);
  border: 1px solid var(--werdu-border);
  padding: 32px;
  border-radius: var(--werdu-radius);
  text-align: center;
  margin: 40px 0;
}

.werdu-card-soft h3 {
  margin-top: 0;
  font-size: 1.4rem;
}

.werdu-card-soft p {
  margin-bottom: 20px;
}

.werdu-seo-container blockquote {
  background: var(--werdu-bg-subtle);
  border-left: 4px solid var(--werdu-orange);
  border-radius: 0 12px 12px 0;
  margin: 32px 0;
  padding: 22px 24px;
  font-style: italic;
  color: var(--werdu-text);
}

.werdu-seo-container blockquote a {
  color: var(--werdu-orange);
  font-weight: 600;
}

/* Borderless comparison table */
.werdu-table-wrapper {
  overflow-x: auto;
  background: var(--werdu-bg);
  border-radius: var(--werdu-radius);
  border: 1px solid var(--werdu-border);
  box-shadow: var(--werdu-shadow);
  margin: 32px 0;
}

.werdu-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
}

.werdu-table th {
  background: var(--werdu-bg-subtle);
  padding: 16px 20px;
  border-bottom: 2px solid var(--werdu-border);
  font-weight: 700;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.05em;
  color: var(--werdu-text);
  text-align: left;
}

.werdu-table td {
  padding: 16px 20px;
  border-bottom: 1px solid var(--werdu-border);
  color: var(--werdu-muted);
}

.werdu-table tr:last-child td {
  border-bottom: none;
}

.werdu-table td strong {
  color: var(--werdu-text);
}

/* Reel 20: Accessible, Large Click-Target Cards */
.werdu-variant-group {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin: 28px 0;
}

.werdu-variant-label {
  display: block;
  cursor: pointer;
  margin: 0;
}

.werdu-variant-label input[type="radio"] {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
  pointer-events: none;
}

.werdu-variant-card {
  position: relative;
  border: 2px solid var(--werdu-border);
  border-radius: var(--werdu-radius);
  padding: 24px;
  background: #FFFFFF;
  transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.werdu-variant-card:hover {
  border-color: #CBD5E1;
}

.werdu-variant-label input[type="radio"]:checked + .werdu-variant-card {
  border-color: var(--werdu-orange);
  background-color: #FFF7ED;
  box-shadow: 0 0 0 4px rgba(255, 102, 0, 0.15);
}

.werdu-variant-card h3 {
  margin: 0 0 6px;
  font-size: 1.1rem;
}

.werdu-variant-card p {
  margin: 0 0 12px;
  font-size: 0.9rem;
}

.werdu-variant-card strong {
  color: var(--werdu-orange);
  font-size: 1.05rem;
}

/* Reel 3: CSS Grid Accordion Transition */
.werdu-faq-item {
  border: 1px solid var(--werdu-border);
  border-radius: 12px;
  margin-bottom: 12px;
  background: #FFFFFF;
  overflow: hidden;
}

.werdu-faq-item > summary.werdu-faq-header {
  list-style: none;
}

.werdu-faq-item > summary.werdu-faq-header::-webkit-details-marker {
  display: none;
}

.werdu-faq-header {
  padding: 20px;
  font-weight: 700;
  font-size: 1.05rem;
  color: var(--werdu-text);
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  user-select: none;
  gap: 16px;
}

.werdu-faq-icon {
  font-size: 1.2rem;
  transition: transform 0.3s ease;
  color: var(--werdu-orange);
  flex-shrink: 0;
}

.werdu-faq-item.is-open .werdu-faq-icon,
.werdu-faq-item[open] .werdu-faq-icon {
  transform: rotate(45deg);
}

.werdu-faq-answer-wrapper {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.werdu-faq-item.is-open .werdu-faq-answer-wrapper {
  grid-template-rows: 1fr;
}

.werdu-faq-answer-inner {
  overflow: hidden;
  padding: 0 20px 20px 20px;
  color: var(--werdu-muted);
  line-height: 1.6;
}

.werdu-faq-answer-inner p {
  margin: 0;
}

/* Reel 21: Button Loading & Success States */
.werdu-btn-primary,
.werdu-calc-btn {
  gap: 10px;
}

.werdu-btn-primary.is-loading,
.werdu-calc-btn.is-loading {
  pointer-events: none;
  opacity: 0.85;
}

.werdu-btn-primary.is-success,
.werdu-calc-btn.is-success {
  background-color: #10B981 !important;
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3) !important;
}

.werdu-btn-spinner {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.4);
  border-top-color: #fff;
  animation: werdu-spin 0.7s linear infinite;
  flex-shrink: 0;
}

.werdu-btn-check {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
}

@keyframes werdu-spin {
  to { transform: rotate(360deg); }
}

/* Accessibility: respect reduced-motion preference — fade instead of movement */
@media (prefers-reduced-motion: reduce) {
  .werdu-btn-primary,
  .werdu-calc-btn,
  .werdu-variant-card,
  .werdu-faq-icon,
  .werdu-faq-answer-wrapper {
    transition: opacity 0.2s ease;
  }
  .werdu-btn-primary:hover,
  .werdu-calc-btn:hover {
    transform: none;
    opacity: 0.9;
  }
  .werdu-btn-spinner {
    animation-duration: 1.4s;
  }
}

/* Opvallende oranje focus-glow op de invoervelden van de rekenmodule
   (Elementor-markup) — exacte glow-specificatie uit het actieplan. */
.werdu-calc-container .calc-row:focus-within,
.werdu-calc-container label:focus-within {
  box-shadow: 0 0 0 3px rgba(255, 102, 0, 0.3);
  border-radius: 10px;
}

.werdu-calc-input:focus,
.werdu-calc-select:focus,
.werdu-calc-container input:focus,
.werdu-calc-container select:focus {
  outline: none;
  border-color: var(--werdu-orange) !important;
  box-shadow: 0 0 0 3px rgba(255, 102, 0, 0.3) !important;
}

/* Calculator submit button gets the same high-end primary style */
.werdu-calc-btn {
  background-color: var(--werdu-orange) !important;
  border-radius: 9999px !important;
  border: none !important;
  font-weight: 600 !important;
  box-shadow: 0 4px 14px rgba(255, 102, 0, 0.3) !important;
  transition: all 0.2s ease;
}

.werdu-calc-btn:hover {
  background-color: var(--werdu-orange-hover) !important;
  transform: translateY(-2px);
}

/* ============================================
   DESIGN SYSTEM 3.1 — Performance-first & hoog contrast (bugfix-release)
   ============================================
   Ten opzichte van 3.0: alle backdrop-filter/blur() en de infinite
   shimmer-animatie zijn verwijderd (zwaar voor lagere-eind devices en
   overbodig voor leesbaarheid/snelheid). De vertrouwensbanner is
   hersteld naar een expliciete hoog-contrast fix: alle tekst/logo's
   binnen .werdu-compat-marquee worden geforceerd donker, ongeacht wat
   Elementor daar zelf ooit als (voor de donkere 2.0-versie bedoelde)
   witte tekstkleur heeft ingesteld — dit voorkomt de "wit-op-wit"
   contrastbug. Extra tokens naast de bestaande --werdu-* variabelen. */
:root {
  --werdu-primary: #0F172A;
  --werdu-accent: #0284C7;
  --werdu-brand-orange: #FF6600;
  --werdu-brand-orange-dark: #E05500;
  --werdu-shadow-lg: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
}

/* Wechselrichter-vertrouwensbanner (echte class: .werdu-compat-marquee) —
   effen lichte achtergrond (geen blur), met geforceerd donker, vetgedrukt
   tekstcontrast op ALLE kind-elementen (logo's/labels/spans). */
.werdu-compat-marquee {
  background: #F1F5F9 !important;
  border: 1px solid var(--werdu-card-border) !important;
  border-radius: 12px !important;
  padding: 16px 20px !important;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04) !important;
}

.werdu-compat-marquee *,
.werdu-compat-brand,
.werdu-compat-brand * {
  color: #334155 !important;
  font-weight: 700 !important;
  opacity: 1 !important;
  text-shadow: none !important;
  -webkit-text-fill-color: #334155 !important;
}

.werdu-compat-brand {
  background: #FFFFFF;
  border: 1px solid var(--werdu-card-border);
  border-radius: 30px;
  padding: 6px 16px;
  transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
  display: inline-block;
}

.werdu-compat-brand:hover,
.werdu-compat-brand:hover * {
  background: var(--werdu-brand-orange);
  border-color: var(--werdu-brand-orange);
  color: #FFFFFF !important;
  -webkit-text-fill-color: #FFFFFF !important;
}

.werdu-compat-sep {
  color: #94A3B8 !important;
}

/* Autarkie-Rechner velden (echte classes: .werdu-calc-container met
   .werdu-calc-input / .werdu-calc-select). */
.werdu-calc-container input[type="text"],
.werdu-calc-container input[type="number"],
.werdu-calc-container input[type="email"],
.werdu-calc-container select {
  background: #F8FAFC;
  border: 1.5px solid #CBD5E1;
  border-radius: 10px;
  transition: border-color 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
}

/* Rekenmodule-container, productkaarten en FAQ-items: effen wit, lichte
   rand en zachte schaduw — geen backdrop-filter/blur meer (performance). */
.werdu-calc-container,
.werdu-product-card,
.werdu-faq-item {
  background: #FFFFFF !important;
  border: 1px solid var(--werdu-card-border) !important;
  border-radius: 12px !important;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04) !important;
  transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}

.werdu-calc-container:hover,
.werdu-calc-container:focus-within,
.werdu-product-card:hover,
.werdu-faq-item:hover {
  border-color: var(--werdu-brand-orange) !important;
  box-shadow: 0 6px 20px rgba(255, 102, 0, 0.12) !important;
}

.werdu-product-card:hover {
  transform: translateY(-4px);
}

/* High-conversion CTA's in effen oranje (#FF6600) — op de bevestigde
   CTA-classes én op elke link die de auto-tag-JS als CTA herkent
   (.werdu-cta-auto). Bewust GEEN gradient/shimmer meer: één vlakke kleur
   + een korte, goedkope hover-lift is sneller en even opvallend. */
.werdu-calc-cta,
.werdu-seo-cta,
.btn-3d,
.werdu-cta-auto {
  background-color: var(--werdu-brand-orange) !important;
  background-image: none !important;
  color: #FFFFFF !important;
  font-weight: 700 !important;
  border-radius: 8px !important;
  border: none !important;
  box-shadow: 0 4px 12px rgba(255, 102, 0, 0.25) !important;
  transition: transform 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease !important;
}

.werdu-calc-cta:hover,
.werdu-seo-cta:hover,
.btn-3d:hover,
.werdu-cta-auto:hover {
  background-color: var(--werdu-brand-orange-dark) !important;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(255, 102, 0, 0.32) !important;
}

/* Bestaande (native) FAQ-blok "Wie lange hält…" deelt de .werdu-faq-item
   class met ons eigen accordion-blok verderop op de pagina. */
.werdu-faq-item > .werdu-faq-question,
.werdu-faq-item > .werdu-faq-answer {
  padding-left: 4px;
  padding-right: 4px;
}

@media (prefers-reduced-motion: reduce) {
  .werdu-compat-brand,
  .werdu-product-card,
  .werdu-calc-container,
  .werdu-faq-item,
  .werdu-calc-cta,
  .werdu-seo-cta,
  .btn-3d,
  .werdu-cta-auto {
    transition: opacity 0.2s ease !important;
    transform: none !important;
  }
  .werdu-compat-brand:hover,
  .werdu-product-card:hover,
  .werdu-calc-container:hover,
  .werdu-faq-item:hover,
  .werdu-calc-cta:hover,
  .werdu-seo-cta:hover,
  .btn-3d:hover,
  .werdu-cta-auto:hover {
    transform: none !important;
  }
}

/* ============================================
   DESIGN SYSTEM 3.2 — Deutsche Landingpage-Struktur
   ============================================
   Layout-laag voor de Duitse markt: 2-koloms hero met badge/USP's,
   3 Szenario-Karten en een Vergleichsmatrix. Puur additief bovenop 3.1
   (geen bestaande class hernoemd/verwijderd); zelfde performance-principes:
   geen backdrop-filter, geen infinite animaties, effen kleuren. */
.werdu-hero-container {
  background: var(--werdu-sky-bg);
  border: 1px solid #DBEAFE;
  border-radius: 20px;
  padding: 45px 35px;
  margin: 20px 0 35px 0;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
  display: grid;
  grid-template-columns: 1.4fr 1fr;
  gap: 40px;
  align-items: center;
  text-align: left;
}

.werdu-badge-de {
  background: #DBEAFE;
  color: #1E40AF;
  font-size: 0.85rem;
  font-weight: 700;
  padding: 6px 14px;
  border-radius: 20px;
  display: inline-block;
  margin-bottom: 15px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.werdu-hero-container h1 {
  color: var(--werdu-text) !important;
  font-size: 2.4rem !important;
  font-weight: 800 !important;
  line-height: 1.25 !important;
  margin-bottom: 15px !important;
  text-align: left;
}

.werdu-hero-p {
  font-size: 1.1rem;
  color: var(--werdu-muted);
  line-height: 1.6;
  margin-bottom: 25px;
}

.werdu-usps-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 12px;
  margin-bottom: 30px;
}

.werdu-usp-tag {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
  color: var(--werdu-text);
  font-size: 0.95rem;
}

.werdu-usp-check {
  color: var(--werdu-orange);
  font-size: 1.2rem;
  font-weight: 900;
  flex-shrink: 0;
}

.btn-werdu-primary {
  background-color: var(--werdu-orange) !important;
  color: #FFFFFF !important;
  font-weight: 700 !important;
  padding: 16px 36px !important;
  border-radius: 10px !important;
  text-decoration: none !important;
  display: inline-block !important;
  font-size: 1.1rem !important;
  box-shadow: 0 6px 20px rgba(255, 102, 0, 0.3) !important;
  border: none !important;
  cursor: pointer !important;
  transition: transform 0.15s ease, background-color 0.15s ease !important;
}

.btn-werdu-primary:hover {
  background-color: var(--werdu-orange-hover) !important;
  transform: translateY(-2px);
}

.werdu-hero-media {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.werdu-hero-lcp-wrap {
  margin: 0;
  border-radius: 16px;
  overflow: hidden;
  background: #e2e8f0;
  border: 1px solid var(--werdu-card-border);
}

.werdu-hero-lcp {
  display: block;
  width: 100%;
  height: auto;
  aspect-ratio: 1024 / 572;
  object-fit: cover;
}

.werdu-hero-fact-card {
  background: #FFFFFF;
  border: 1px solid var(--werdu-card-border);
  border-radius: 16px;
  padding: 28px;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
}

.werdu-hero-fact-card h3 {
  font-size: 1.05rem;
  color: var(--werdu-text);
  margin: 0 0 16px;
}

.werdu-hero-fact-row {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  padding: 10px 0;
  border-bottom: 1px solid var(--werdu-card-border);
}

.werdu-hero-fact-row:last-child {
  border-bottom: none;
}

.werdu-hero-fact-row span:first-child {
  color: var(--werdu-muted);
  font-size: 0.9rem;
}

.werdu-hero-fact-row strong {
  color: var(--werdu-orange);
  font-size: 1.15rem;
}

.werdu-section-head {
  text-align: center;
  margin: 40px 0 25px 0;
}

.werdu-section-head h2 {
  font-size: 2rem !important;
  font-weight: 800 !important;
  color: var(--werdu-text) !important;
  margin-top: 0 !important;
}

.werdu-section-head p {
  color: var(--werdu-muted);
  max-width: 640px;
  margin: 8px auto 0;
}

.werdu-card-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 25px;
  margin-bottom: 45px;
}

.werdu-scenario-card {
  background: #FFFFFF;
  border: 1px solid var(--werdu-card-border);
  border-radius: 16px;
  padding: 30px;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
  position: relative;
  transition: transform 0.2s ease, border-color 0.2s ease;
}

.werdu-scenario-card:hover {
  transform: translateY(-4px);
  border-color: var(--werdu-orange);
}

.werdu-scenario-badge {
  position: absolute;
  top: -12px;
  right: 20px;
  background: var(--werdu-orange);
  color: #FFFFFF;
  font-size: 0.75rem;
  font-weight: 800;
  padding: 4px 12px;
  border-radius: 12px;
  text-transform: uppercase;
}

.werdu-scenario-card h3 {
  color: var(--werdu-text) !important;
  font-size: 1.4rem !important;
  margin-bottom: 10px !important;
  margin-top: 0 !important;
}

.werdu-scenario-card p {
  color: var(--werdu-muted);
  font-size: 0.95rem;
  line-height: 1.5;
  margin-bottom: 20px;
}

.werdu-scenario-card ul {
  list-style: none;
  margin: 0 0 20px;
  padding: 0;
  display: grid;
  gap: 8px;
}

.werdu-scenario-card ul li {
  font-size: 0.9rem;
  color: var(--werdu-muted);
  padding-left: 22px;
  position: relative;
}

.werdu-scenario-card ul li::before {
  content: "✓";
  position: absolute;
  left: 0;
  color: var(--werdu-orange);
  font-weight: 900;
}

.werdu-matrix-wrapper {
  background: #FFFFFF;
  border: 1px solid var(--werdu-card-border);
  border-radius: 16px;
  padding: 25px;
  margin-bottom: 45px;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
  overflow-x: auto;
}

.werdu-matrix-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.werdu-matrix-table th {
  background: var(--werdu-bg-subtle);
  color: var(--werdu-text);
  padding: 14px;
  font-weight: 700;
  border-bottom: 2px solid var(--werdu-card-border);
}

.werdu-matrix-table td {
  padding: 14px;
  border-bottom: 1px solid var(--werdu-card-border);
  color: var(--werdu-muted);
  font-size: 0.95rem;
}

.werdu-matrix-table tr:last-child td {
  border-bottom: none;
}

.werdu-faq-box {
  background: #FFFFFF;
  border: 1px solid var(--werdu-card-border);
  border-radius: 16px;
  padding: 30px;
  margin-bottom: 40px;
}

.werdu-faq-item .werdu-faq-answer-inner {
  padding: 0 20px 20px 20px;
}

.werdu-product-grid {
  display: grid !important;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important;
  gap: 25px !important;
  align-items: stretch !important;
  margin: 32px 0 40px;
}

.werdu-product-card-item {
  display: flex !important;
  flex-direction: column !important;
  justify-content: space-between !important;
  height: 100% !important;
  background: #ffffff !important;
  border-radius: 12px !important;
  padding: 24px !important;
  box-sizing: border-box !important;
  border: 1px solid #e2e8f0 !important;
}

.werdu-product-card-item.is-bestseller {
  border: 2px solid #ff6600 !important;
  box-shadow: 0 10px 25px rgba(255, 102, 0, 0.15) !important;
}

.werdu-product-card-item h3 {
  min-height: 56px !important;
  margin-bottom: 10px !important;
  font-size: 1.1rem !important;
  font-weight: 700 !important;
}

.werdu-product-card-item p {
  flex-grow: 1 !important;
  margin-bottom: 15px !important;
  font-size: 0.9rem !important;
  color: #4a5568 !important;
}

.werdu-product-card-cta {
  margin-top: auto !important;
  padding-top: 15px !important;
  border-top: 1px solid #edf2f7 !important;
}

.werdu-product-card-cta strong {
  display: block;
  color: var(--werdu-orange);
  font-size: 1.2rem;
  margin-bottom: 12px;
}

@media (max-width: 782px) {
  .werdu-hero-container {
    grid-template-columns: 1fr;
    padding: 30px 22px;
  }
  .werdu-hero-container h1 {
    font-size: 1.7rem !important;
  }
}

/* ============================================
   DESIGN SYSTEM 3.3 — High-Visual (gloeiende knoppen, kaarten, badges)
   ============================================
   Toegepast op de ECHTE, al bestaande CTA/kaart/tabel-classes (zodat het
   direct zichtbaar is), plus dezelfde regels ook onder de expliciet
   gevraagde class-namen (.werdu-btn-glow, .btn-primary-orange,
   .werdu-hero-card, .werdu-usp-item, .werdu-bullet-list/.werdu-bullet-icon,
   .werdu-styled-table, .werdu-card-grid, .werdu-visual-card,
   .werdu-badge-orange) — voor het geval toekomstige content die direct
   gebruikt. Er wordt niets herschreven dat al bestond; dit is een extra
   visuele laag boven op 3.1/3.2. */
.werdu-calc-cta,
.werdu-seo-cta,
.btn-3d,
.werdu-cta-auto,
.btn-werdu-primary,
.werdu-btn-primary,
.werdu-btn-glow,
.btn-primary-orange {
  background: linear-gradient(135deg, var(--werdu-orange) 0%, var(--werdu-orange-hover) 100%) !important;
  border: 2px solid #FF8533 !important;
  box-shadow: var(--werdu-glow-shadow) !important;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2) !important;
  transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s ease !important;
}

.werdu-calc-cta:hover,
.werdu-seo-cta:hover,
.btn-3d:hover,
.werdu-cta-auto:hover,
.btn-werdu-primary:hover,
.werdu-btn-primary:hover,
.werdu-btn-glow:hover,
.btn-primary-orange:hover {
  transform: translateY(-3px) scale(1.02) !important;
  box-shadow: 0 12px 35px var(--werdu-orange-glow), 0 0 25px rgba(255, 102, 0, 0.6) !important;
  border-color: #FFFFFF !important;
}

/* Hero-kaart: dezelfde look als .werdu-hero-container, ook onder de
   expliciet gevraagde .werdu-hero-card class. */
.werdu-hero-container,
.werdu-hero-card {
  border-width: 2px !important;
  position: relative;
  overflow: hidden;
}

/* USP-vinkjes: ronde oranje bullet-badge in plaats van platte tekst-check. */
.werdu-usp-tag,
.werdu-usp-item,
.werdu-bullet-list li {
  list-style: none !important;
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
}

.werdu-usp-check,
.werdu-bullet-icon {
  background: var(--werdu-orange) !important;
  color: #FFFFFF !important;
  width: 24px !important;
  height: 24px !important;
  border-radius: 50% !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 13px !important;
  font-weight: 900 !important;
  box-shadow: 0 4px 10px rgba(255, 102, 0, 0.3) !important;
  flex-shrink: 0;
}

/* Tabellen: donkere kop, zebra-striping en zachte schaduw — zowel op de
   bestaande .werdu-table/.werdu-matrix-table als op .werdu-styled-table. */
.werdu-table-wrapper,
.werdu-matrix-wrapper {
  border-radius: 16px !important;
  box-shadow: var(--werdu-shadow-lg, var(--werdu-shadow)) !important;
}

.werdu-table thead th,
.werdu-matrix-table th,
.werdu-styled-table th {
  background: var(--werdu-text) !important;
  color: #FFFFFF !important;
  border-bottom: none !important;
}

.werdu-table tbody tr:nth-child(even) td,
.werdu-matrix-table tr:nth-child(even) td,
.werdu-styled-table tr:nth-child(even) td {
  background-color: var(--werdu-bg-subtle);
}

.werdu-table tbody tr:hover td,
.werdu-matrix-table tr:hover td,
.werdu-styled-table tr:hover td {
  background-color: #F1F5F9;
}

.werdu-styled-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: var(--werdu-shadow-lg, var(--werdu-shadow));
  border: 1px solid var(--werdu-card-border);
  background: #FFFFFF;
}

.werdu-styled-table td {
  padding: 16px 20px;
  border-bottom: 1px solid var(--werdu-card-border);
  color: var(--werdu-muted);
  font-size: 0.95rem;
}

/* Scenario-/productkaarten: sterkere schaduw + oranje glow bij hover. */
.werdu-scenario-card,
.werdu-product-card,
.werdu-visual-card {
  border-width: 2px !important;
  box-shadow: var(--werdu-shadow-lg, var(--werdu-shadow)) !important;
}

.werdu-scenario-card:hover,
.werdu-product-card:hover,
.werdu-visual-card:hover {
  box-shadow: 0 20px 40px -10px rgba(255, 102, 0, 0.2) !important;
}

.werdu-card-container,
.werdu-card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 25px;
  margin: 35px 0;
}

/* Badges: gradient-variant naast de bestaande vlakke .werdu-scenario-badge. */
.werdu-badge-orange {
  background: linear-gradient(135deg, var(--werdu-orange), #FF8533) !important;
  color: #FFFFFF !important;
  font-weight: 800 !important;
  font-size: 0.8rem !important;
  padding: 6px 14px !important;
  border-radius: 20px !important;
  text-transform: uppercase !important;
  display: inline-block !important;
  box-shadow: 0 4px 12px rgba(255, 102, 0, 0.25) !important;
}

@media (prefers-reduced-motion: reduce) {
  .werdu-calc-cta,
  .werdu-seo-cta,
  .btn-3d,
  .werdu-cta-auto,
  .btn-werdu-primary,
  .werdu-btn-primary,
  .werdu-btn-glow,
  .btn-primary-orange,
  .werdu-scenario-card,
  .werdu-product-card,
  .werdu-visual-card {
    transition: opacity 0.2s ease !important;
  }
  .werdu-calc-cta:hover,
  .werdu-seo-cta:hover,
  .btn-3d:hover,
  .werdu-cta-auto:hover,
  .btn-werdu-primary:hover,
  .werdu-btn-primary:hover,
  .werdu-btn-glow:hover,
  .btn-primary-orange:hover,
  .werdu-scenario-card:hover,
  .werdu-product-card:hover,
  .werdu-visual-card:hover {
    transform: none !important;
  }
}
CSS;
}

function werdu_home_seo_critical_css() {
    if ( is_admin() || ! is_front_page() ) {
        return;
    }
    echo '<style id="werdu-home-critical-css">'
        . '@font-face{font-display:swap!important}'
        . 'body.home,body.home #page,body.home #content,.whp-page{background:#f8fafc!important;color:#0f172a}'
        . 'a.skip-link,.skip-link{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0)}'
        . '.whp-hero-grid{display:grid;grid-template-columns:1.1fr 1fr;gap:48px;align-items:center}'
        . '.whp-hero h1{font-size:clamp(1.9rem,4vw,2.75rem);font-weight:800;line-height:1.15;margin:0 0 16px;color:#0f172a}'
        . '.whp-hero-lcp{display:block;width:100%;height:auto;aspect-ratio:1024/572;object-fit:cover}'
        . '.whp-btn--primary{background:#ff6600;color:#fff;box-shadow:0 4px 14px rgba(224,85,0,.25)}'
        . '@media(max-width:900px){.whp-hero-grid{grid-template-columns:1fr}}'
        . '</style>' . "\n";
}
add_action( 'wp_head', 'werdu_home_seo_critical_css', 1 );

function werdu_home_seo_print_css() {
    if ( is_admin() || ! is_front_page() ) {
        return;
    }
    echo '<style id="werdu-home-seo-css">'
        . 'body.home .werdu-calc-cta,body.home .werdu-seo-cta,body.home .btn-3d,body.home .werdu-cta-auto,body.home .btn-werdu-primary,body.home .werdu-btn-primary,body.home .werdu-btn-glow,body.home .btn-primary-orange{'
        . 'background:#ff6600!important;border:none!important;box-shadow:0 4px 14px rgba(224,85,0,.25)!important;text-shadow:none!important;filter:none!important}'
        . 'body.home .werdu-calc-cta:hover,body.home .btn-werdu-primary:hover,body.home .werdu-btn-primary:hover{'
        . 'background:#e05500!important;transform:translateY(-2px)!important;box-shadow:0 4px 14px rgba(224,85,0,.25)!important}'
        . '</style>' . "\n";
}
add_action( 'wp_head', 'werdu_home_seo_print_css', 99999 );

// ============================================
// FORM UX FIX — inputmode="numeric" op PLZ/telefoon-achtige velden
// ============================================

/**
 * De calculator-velden zijn Elementor-content (niet in een bestand te
 * bewerken), dus deze fix wordt client-side toegepast: elk veld dat een
 * postcode/PLZ of telefoonnummer lijkt te zijn, krijgt inputmode="numeric"
 * i.p.v. type="number" (voorkomt spinner-pijltjes en toont op mobiel het
 * juiste numerieke toetsenbord zonder het HTML-inputtype te wijzigen).
 */
function werdu_home_seo_print_inputmode_js() {
    if ( is_admin() || ! is_front_page() ) {
        return;
    }
    ?>
<script id="werdu-home-seo-inputmode">
(function () {
  'use strict';

  var SELECTORS = [
    'input#plz',
    'input[name="plz"]',
    'input[id*="plz" i]',
    'input[name*="plz" i]',
    'input[id*="zip" i]',
    'input[name*="zip" i]',
    'input[id*="postleitzahl" i]',
    'input[type="tel"]',
    'input[id*="phone" i]',
    'input[name*="phone" i]',
    'input[id*="telefon" i]',
    'input[name*="telefon" i]'
  ];

  function applyInputmode() {
    document.querySelectorAll( SELECTORS.join( ',' ) ).forEach( function ( el ) {
      if ( el.getAttribute( 'inputmode' ) !== 'numeric' ) {
        el.setAttribute( 'inputmode', 'numeric' );
      }
      if ( el.getAttribute( 'type' ) === 'number' ) {
        el.setAttribute( 'type', 'text' );
      }
      if ( ! el.getAttribute( 'pattern' ) ) {
        el.setAttribute( 'pattern', '[0-9]*' );
      }
    } );
  }

  if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', applyInputmode );
  } else {
    applyInputmode();
  }

  // De calculator-markup kan iets vertraagd renderen; kort blijven proberen.
  var attempts = 0;
  var timer = setInterval( function () {
    attempts++;
    applyInputmode();
    if ( attempts > 20 ) {
      clearInterval( timer );
    }
  }, 300 );
})();
</script>
    <?php
}
add_action( 'wp_footer', 'werdu_home_seo_print_inputmode_js', 20 );

// ============================================
// INTERACTIVE UX — FAQ-accordion + 3-staps knop-feedback
// ============================================

/**
 * Reel 3 (FAQ-accordion) en Reel 21 (Default -> Loading -> Success knop-
 * feedback) worden hier client-side bedraad. De CSS-transitions (grid-
 * template-rows, spinner-animatie) staan al in werdu_home_seo_base_css();
 * dit script schakelt alleen de juiste classes ("is-open", "is-loading",
 * "is-success") op de juiste momenten.
 */
function werdu_home_seo_print_interactions_js() {
    if ( is_admin() || ! is_front_page() ) {
        return;
    }
    return;
}
add_action( 'wp_footer', 'werdu_home_seo_print_interactions_js', 21 );

// ============================================
// CONTENT — Hero + SEO/AIO-artikel
// ============================================

/**
 * Nieuw Hero-blok (H1 + intro) — wordt vóór de bestaande content geplaatst.
 * De bestaande Hero/aankondigingsbalk, productkaarten en calculator blijven
 * volledig ongewijzigd; dit is een extra, op zichzelf staand blok.
 */
function werdu_home_seo_hero_html() {
    $rechner  = werdu_home_seo_rechner_url();
    $beratung = werdu_home_seo_beratung_url();
    $hero_src = home_url( '/wp-content/uploads/2026/08/pv-speicher-kaufen-modernes-deutsches-wohnhaus-alpen_1024_572.webp' );

    $template = <<<'HTML'
<div class="werdu-seo-container" style="padding-bottom:0;">
    <div class="werdu-hero-container">
        <div>
            <span class="werdu-badge-de">Für den deutschen Markt · 0&nbsp;% MwSt. nach § 12 Abs. 3 UStG</span>
            <h1>PV-Speicher kaufen: LiFePO4-Heimspeicher mit transparenten Preisen</h1>
            <p class="werdu-hero-p">Ein richtig dimensionierter PV-Speicher hebt Ihren Solar-Eigenverbrauch typischerweise von rund 20–30&nbsp;% auf 70–85&nbsp;%. Berechnen Sie in wenigen Minuten Ihre Kapazität – und kaufen Sie anschließend zum transparenten Festpreis im Shop, ohne individuelles Angebot.</p>
            <div class="werdu-usps-grid">
                <div class="werdu-usp-tag"><span class="werdu-usp-check">✓</span> 0&nbsp;% MwSt. auf PV-Speicher</div>
                <div class="werdu-usp-tag"><span class="werdu-usp-check">✓</span> LiFePO4 mit 6.000–8.000 Zyklen</div>
                <div class="werdu-usp-tag"><span class="werdu-usp-check">✓</span> Optionale Notstromfunktion</div>
                <div class="werdu-usp-tag"><span class="werdu-usp-check">✓</span> Optionaler Fachbetrieb vor Ort</div>
            </div>
            <a href="___RECHNER_URL___" class="btn-werdu-primary">Jetzt Autarkie-Rechner starten</a>
            <a href="___BERATUNG_URL___" class="werdu-btn-primary" style="margin-left:12px;">Kostenlose Beratung</a>
        </div>
        <div class="werdu-hero-media">
            <div class="werdu-hero-lcp-wrap">
                <img
                    class="werdu-hero-lcp"
                    src="___HERO_SRC___"
                    alt="Modernes Wohnhaus mit Photovoltaik und LiFePO4-Heimspeicher in Deutschland"
                    width="1024"
                    height="572"
                    fetchpriority="high"
                    decoding="async"
                />
            </div>
            <div class="werdu-hero-fact-card">
                <h3>Ihr PV-Speicher auf einen Blick</h3>
                <div class="werdu-hero-fact-row"><span>Steuervorteil</span><strong>0&nbsp;% MwSt.</strong></div>
                <div class="werdu-hero-fact-row"><span>Zellchemie</span><strong>LiFePO4</strong></div>
                <div class="werdu-hero-fact-row"><span>Max. Autarkiegrad</span><strong>bis 85&nbsp;%</strong></div>
                <div class="werdu-hero-fact-row"><span>Notstrom-Option</span><strong>Verfügbar</strong></div>
            </div>
        </div>
    </div>
</div>
HTML;

    return str_replace(
        array( '___RECHNER_URL___', '___BERATUNG_URL___', '___HERO_SRC___' ),
        array( esc_url( $rechner ), esc_url( $beratung ), esc_url( $hero_src ) ),
        $template
    );
}

/**
 * Enige waarheidsbron voor de FAQ-vragen/antwoorden: dezelfde array voedt
 * zowel de zichtbare accordion-HTML als de JSON-LD FAQPage-schema. Dit
 * voorkomt contentdrift tussen wat bezoekers zien en wat crawlers/AI-search
 * (Google AIO, Perplexity, ChatGPT Search) als structured data binnenkrijgen.
 */
function werdu_home_seo_faq_data() {
    $rechner     = esc_url( werdu_home_seo_rechner_url() );
    $beratung    = esc_url( werdu_home_seo_beratung_url() );
    $shop        = esc_url( home_url( '/shop/' ) );
    $mwst        = esc_url( home_url( '/mwst-befreiung-eigenverbrauch/' ) );
    $installation = esc_url( home_url( '/heimspeicher-installation/' ) );
    $notstrom    = esc_url( home_url( '/notstrom-heimspeicher-ersatzstrom-blackout/' ) );
    $kosten      = esc_url( home_url( '/heimspeicher-kosten-pro-kwh/' ) );
    $garantie    = esc_url( home_url( '/garantie/' ) );
    $systeme     = esc_url( home_url( '/heimspeicher-systeme/' ) );

    return array(
        array(
            'q' => 'Kann ich einen Speicher nachträglich an meine PV-Anlage anbauen?',
            'a' => 'Ja. Ein Heimspeicher lässt sich an nahezu jede bestehende Photovoltaikanlage nachrüsten. Für die Nachrüstung sind AC-gekoppelte Systeme oft die praktischste Lösung, weil sie den vorhandenen Wechselrichter unangetastet lassen. Alternativ kann ein Hybrid-Wechselrichter die PV-Seite und die Batterie auf der DC-Seite zusammenführen. Welche Variante zu Ihrem Zählerplatz, Ihrer Netzform und Ihrem Verbrauch passt, klären Sie am schnellsten mit dem <a href="' . $rechner . '">Autarkie-Rechner</a> und einer <a href="' . $beratung . '">kostenlosen Fachberatung</a>. Ablauf und Hinweise stehen unter <a href="' . $installation . '">Heimspeicher-Installation</a>.',
        ),
        array(
            'q' => 'Wie lange hält eine moderne LiFePO4-Solarbatterie?',
            'a' => 'Hochwertige Lithium-Eisenphosphat-Speicher (LiFePO4) sind für den stationären Einsatz im Eigenheim ausgelegt. Typisch sind 6.000 bis 8.000 Vollzyklen und eine kalendarische Nutzungsdauer von 15 bis 20 Jahren. Nach dieser Zykluszahl liegt die Restkapazität häufig noch über 80 Prozent. Einzelne Systeme im Shop sind mit höheren Zyklenangaben spezifiziert – maßgeblich sind immer die Angaben auf der jeweiligen Produktseite. Zur Einordnung der Lebensdauer siehe die <a href="' . $garantie . '">Garantie-Informationen</a>.',
        ),
        array(
            'q' => 'Funktioniert der Speicher auch bei einem Stromausfall?',
            'a' => 'Nicht automatisch. Netzgekoppelte Wechselrichter müssen bei einem Ausfall des öffentlichen Netzes aus Sicherheitsgründen abschalten (Inselnetzverhinderung). Erst wenn Ihr System eine ausgewiesene Notstrom- oder Ersatzstromfunktion besitzt und der Wechselrichter dafür geeignet ist, versorgt die Batterie definierte Stromkreise weiter. Ersatzstrom ist nicht dasselbe wie eine vollständige Off-Grid-Versorgung. Lesen Sie dazu den Ratgeber <a href="' . $notstrom . '">Notstrom und Ersatzstrom</a>.',
        ),
        array(
            'q' => 'Lohnt sich ein PV-Speicher auch im Winter?',
            'a' => 'Ja, aber mit realistischer Erwartung. Im Winter erzeugt die Photovoltaikanlage weniger Energie, und der Speicher kann nicht an jedem Tag voll werden. Dennoch speichert er sonnige Phasen und verschiebt den Strom in die Abendstunden. Die Wirtschaftlichkeit bewertet man über das volle Jahr: Im Sommer deckt der Speicher einen großen Teil des Haushaltsstroms, im Winter reduziert er Netzbezug an ertragsstarken Tagen. Die Jahresautarkie – nicht ein einzelner Januar-Tag – ist die richtige Kennzahl. Dimensionieren Sie daher anhand des Jahresverbrauchs, nicht anhand eines Winter-Worst-Case.',
        ),
        array(
            'q' => 'Gilt wirklich 0 % MwSt. nach § 12 Abs. 3 UStG?',
            'a' => 'Für begünstigte Photovoltaikanlagen und die dazugehörigen Stromspeicher gilt in Deutschland seit 2023 der Steuersatz von 0&nbsp;% nach § 12 Abs. 3 UStG, wenn die Anlage auf oder in der Nähe von Wohnungen bzw. bestimmten öffentlichen oder gemeinnützig genutzten Gebäuden installiert wird. Der Vorteil gilt für den Kauf und die Installation, nicht pauschal für jedes beliebige Zubehör. Eine verständliche Einordnung finden Sie auf der Seite zur <a href="' . $mwst . '">MwSt-Befreiung für den Eigenverbrauch</a>. Dies ist keine Steuerberatung; im Zweifel entscheidet Ihr Steuerberater oder das zuständige Finanzamt.',
        ),
        array(
            'q' => 'Welche Speicherkapazität brauche ich für mein Einfamilienhaus?',
            'a' => 'Als Praxisregel für Einfamilienhäuser gilt: Pro 1.000 kWh Jahresstromverbrauch etwa 1,0 bis 1,5 kWh nutzbare Speicherkapazität, abgestimmt auf die kWp-Leistung Ihrer PV-Anlage. Ein Haushalt mit 4.000 kWh liegt damit häufig im Bereich 5–8 kWh, ein Haushalt mit E-Auto oder Wärmepumpe eher bei 12–16 kWh oder mehr. Der kostenlose <a href="' . $rechner . '">Autarkie-Rechner</a> ermittelt die Größenordnung aus Verbrauch und PV-Leistung. Anschließend wählen Sie das passende System im <a href="' . $shop . '">Shop</a>.',
        ),
        array(
            'q' => 'Warum LiFePO4 statt NMC oder Blei-Säure?',
            'a' => 'LiFePO4 ist für Heimspeicher die etablierte Standardchemie: hohe thermische Stabilität, kein Kobalt in der Kathode und eine Zyklenfestigkeit, die zur Nutzungsdauer einer PV-Anlage passt. NMC-Zellen sind energiedichter, aber thermisch empfindlicher. Blei-Säure erreicht nur wenige hundert bis rund 1.500 Zyklen. Natrium-Ionen-Systeme sind kobalt- und lithiumfrei mit guter Kälteleistung; die Zyklenzahlen liegen typischerweise unter LiFePO4. Einen Überblick der Bauformen finden Sie unter <a href="' . $systeme . '">Heimspeicher-Systeme</a>.',
        ),
        array(
            'q' => 'Muss ich den Speicher selbst installieren oder gibt es einen Fachbetrieb?',
            'a' => 'Sie können das Gerät als Endkunde kaufen und durch einen qualifizierten Elektriker anschließen lassen. Optional bieten wir den Versand an einen zertifizierten lokalen Installateur: Der Speicher wird direkt zum Fachbetrieb geliefert, der die Montage bei Ihnen vor Ort übernimmt. Die Stundensätze setzt jeder Betrieb selbst – es gibt keinen einheitlichen Pauschalpreis. Ablauf und Hinweise stehen auf der <a href="' . $installation . '">Installationsseite</a>. Die optionale Auswahl erscheint auch in der <a href="' . $beratung . '">Beratung</a>.',
        ),
        array(
            'q' => 'Wie hoch ist der typische Autarkiegrad mit Speicher?',
            'a' => 'Ohne Speicher nutzen Eigenheime mit Photovoltaik häufig nur etwa 20 bis 30 Prozent des selbst erzeugten Stroms direkt. Mit einem passend dimensionierten LiFePO4-Heimspeicher sind 70 bis 85 Prozent Autarkie realistisch, abhängig von Lastprofil, PV-Größe, Wärmepumpe und E-Auto. 100 Prozent Netzunabhängigkeit ist im mitteleuropäischen Winter ohne sehr große PV- und Speicherreserven oder ein Notstromaggregat in der Regel nicht das Ziel. Entscheidend ist, den Speicher an Verbrauch und Erzeugung zu koppeln statt ihn maximal zu überdimensionieren. Fraunhofer ISE hat den Eigenverbrauchs-Effekt von Speichern in Wohngebäuden mehrfach quantifiziert.',
        ),
        array(
            'q' => 'Was kostet ein Heimspeicher – und wo sehe ich den aktuellen Preis?',
            'a' => 'Die Kosten hängen von nutzbarer Kapazität, BMS, Gehäuse und davon ab, ob ein Hybrid-Wechselrichter bereits integriert ist. Transparente Festpreise stehen im <a href="' . $shop . '">Shop</a> und auf den Produktseiten; sie werden hier nicht festgeschrieben, weil sich Listenpreise ändern. Zusätzlich gilt für begünstigte Anlagen 0&nbsp;% MwSt. Eine Einordnung finden Sie unter <a href="' . $kosten . '">Heimspeicher-Kosten pro kWh</a>. Der Rechner liefert die Größenordnung, die Beratung klärt Anschluss und optionale Montage – es wird kein automatisches PDF-Angebot erzeugt.',
        ),
    );
}

/**
 * Rendert de zichtbare FAQ-accordion als native <details>/<summary>.
 */
function werdu_home_seo_faq_html() {
    $allowed = array(
        'a'      => array( 'href' => array(), 'target' => array(), 'rel' => array() ),
        'strong' => array(),
        'em'     => array(),
        'br'     => array(),
    );
    $html = '';
    foreach ( werdu_home_seo_faq_data() as $i => $item ) {
        $open = ( 0 === $i ) ? ' open' : '';
        $html .= '<details class="werdu-faq-item"' . $open . '>'
            . '<summary class="werdu-faq-header"><span>' . esc_html( $item['q'] ) . '</span><span class="werdu-faq-icon" aria-hidden="true">+</span></summary>'
            . '<div class="werdu-faq-answer-inner"><p>' . wp_kses( $item['a'], $allowed ) . '</p></div>'
            . '</details>';
    }
    return $html;
}

/**
 * FAQPage JSON-LD vanuit werdu_home_seo_faq_data().
 */
function werdu_home_seo_faq_json_ld() {
    $questions = array();
    foreach ( werdu_home_seo_faq_data() as $item ) {
        $questions[] = array(
            '@type'          => 'Question',
            'name'           => wp_strip_all_tags( $item['q'] ),
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => wp_strip_all_tags( $item['a'] ),
            ),
        );
    }

    $schema = array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $questions,
    );

    return '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
        . '</script>';
}

/**
 * SoftwareApplication JSON-LD für den Autarkie-Rechner.
 */
function werdu_home_seo_software_json_ld() {
    $schema = array(
        '@context'            => 'https://schema.org',
        '@type'               => 'SoftwareApplication',
        'name'                => 'WERDU Autarkie-Rechner',
        'applicationCategory' => 'BusinessApplication',
        'operatingSystem'     => 'Web',
        'inLanguage'          => 'de-DE',
        'url'                 => werdu_home_seo_rechner_url(),
        'description'         => 'Kostenloser Online-Rechner zur Dimensionierung von LiFePO4-Heimspeichern anhand von Jahresverbrauch und PV-Leistung. Ergebnisse führen zur Beratung, nicht zu einem automatischen Preisangebot.',
        'offers'              => array(
            '@type'         => 'Offer',
            'price'         => '0',
            'priceCurrency' => 'EUR',
        ),
        'publisher'           => array(
            '@type' => 'Organization',
            'name'  => 'WERDU',
            'url'   => home_url( '/' ),
        ),
    );

    return '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
        . '</script>';
}

/**
 * Groot SEO/AIO-contentblok (ToC, artikel, vergelijkingstabel, FAQ, JSON-LD).
 * Wordt direct onder de calculator-sectie geplaatst. Alle CTA's verwijzen naar
 * home_url('/beratung-anfragen/') resp. home_url('/solarbatterie-rechner/') —
 * nooit naar /kontakt/ en nooit naar een hardgecodeerde host. De copy is
 * bewust gevarieerd (PV-Speicher, Batteriespeicher, Heimspeicher, LiFePO4,
 * Autarkie) om onnatuurlijke keyword-stuffing te vermijden.
 */
function werdu_home_seo_body_html() {
    $beratung = werdu_home_seo_beratung_url();
    $rechner  = werdu_home_seo_rechner_url();
    $shop     = home_url( '/shop/' );
    $preis_basen = function_exists( 'do_shortcode' ) ? do_shortcode( '[werdu_preis id="basen16kwh"]' ) : '';
    $preis_16    = function_exists( 'do_shortcode' ) ? do_shortcode( '[werdu_preis id="16kwh"]' ) : '';
    $preis_30    = function_exists( 'do_shortcode' ) ? do_shortcode( '[werdu_preis id="30kwh"]' ) : '';
    $shop_link   = '<a href="' . esc_url( $shop ) . '">Preis im Shop anzeigen</a>';
    if ( $preis_basen === '' || $preis_basen === '—' ) {
        $preis_basen = $shop_link;
    }
    if ( $preis_16 === '' || $preis_16 === '—' ) {
        $preis_16 = $shop_link;
    }
    if ( $preis_30 === '' || $preis_30 === '—' ) {
        $preis_30 = $shop_link;
    }

    $template = <<<'HTML'
<div class="werdu-seo-container">

    <div class="werdu-toc-box">
        <h2>Inhaltsverzeichnis: Ihr Ratgeber rund um den PV-Speicher</h2>
        <ul>
            <li><a href="#warum-pv-speicher-kaufen">1. Warum sich ein PV-Speicher 2026 lohnt</a></li>
            <li><a href="#autarkie-vorteile">2. Mehr Autarkie, weniger Stromkosten</a></li>
            <li><a href="#dimensionierung-kapazitaet">3. Die richtige Kapazität für Ihren Speicher</a></li>
            <li><a href="#technologie-vergleich">4. LiFePO4, Natrium-Ionen und NMC im Vergleich</a></li>
            <li><a href="#kosten-wirtschaftlichkeit">5. Kosten, 0&nbsp;% MwSt. &amp; Amortisation</a></li>
            <li><a href="#installation-fachbetrieb">6. Installation und optionaler Fachbetrieb</a></li>
            <li><a href="#sicherheit-recht">7. Sicherheit, Apps und rechtliche Pflichten</a></li>
            <li><a href="#faq-bereich">8. Häufig gestellte Fragen zum PV-Speicher</a></li>
        </ul>
    </div>

    <div class="werdu-section-head" id="schnellauswahl">
        <h2>Welcher PV-Speicher passt zu Ihnen?</h2>
        <p>Drei typische Haushaltsprofile – finden Sie in Sekunden Ihre Kategorie und bestätigen Sie die Größe anschließend mit dem kostenlosen Autarkie-Rechner. Es entsteht kein automatisches Preisangebot; die Festpreise stehen im Shop.</p>
    </div>
    <div class="werdu-card-container">
        <div class="werdu-scenario-card">
            <span class="werdu-scenario-badge">Beliebteste Wahl</span>
            <h3>Standard-Haushalt</h3>
            <p>2–4 Personen, ca. 3.000–4.500&nbsp;kWh Jahresverbrauch, klassische PV-Anlage ohne E-Auto.</p>
            <ul>
                <li>Empfohlene Größe: 5–8&nbsp;kWh</li>
                <li>Autarkiegrad: ca. 70–78&nbsp;%</li>
                <li>0&nbsp;% MwSt. bei begünstigter Anlage</li>
            </ul>
            <a href="___RECHNER_URL___" class="btn-werdu-primary">Größe berechnen</a>
        </div>
        <div class="werdu-scenario-card">
            <span class="werdu-scenario-badge">Für Vielverbraucher</span>
            <h3>Haushalt mit E-Auto &amp; Wärmepumpe</h3>
            <p>Höherer Verbrauch durch Elektromobilität und/oder Wärmepumpe, ca. 6.000–9.000&nbsp;kWh im Jahr.</p>
            <ul>
                <li>Empfohlene Größe: 12–16&nbsp;kWh</li>
                <li>Autarkiegrad: ca. 80–88&nbsp;%</li>
                <li>LiFePO4-Technologie</li>
            </ul>
            <a href="___RECHNER_URL___" class="btn-werdu-primary">Größe berechnen</a>
        </div>
        <div class="werdu-scenario-card">
            <span class="werdu-scenario-badge">Volle Unabhängigkeit</span>
            <h3>Maximale Autarkie &amp; Notstrom</h3>
            <p>Sie möchten größtmögliche Unabhängigkeit und bei einem Stromausfall abgesichert sein – mit passendem Hybrid-Wechselrichter.</p>
            <ul>
                <li>Empfohlene Größe: 16–32&nbsp;kWh</li>
                <li>Optionale Notstromfunktion</li>
                <li>All-in-One möglich</li>
            </ul>
            <a href="___BERATUNG_URL___" class="btn-werdu-primary">Beratung anfragen</a>
        </div>
    </div>

    <section class="werdu-seo-body">

        <h2 id="warum-pv-speicher-kaufen">1. Warum sich ein PV-Speicher 2026 lohnt</h2>
        <p>
            Ohne Speicher nutzen Eigenheimbesitzer typischerweise nur 20&nbsp;% bis 30&nbsp;% ihres Solarstroms selbst; der Rest geht zu einer deutlich niedrigeren Einspeisevergütung ins Netz, während abends teurer Haushaltsstrom zurückgekauft wird.
        </p>
        <p>
            Die Einspeisevergütung für neuen Solarstrom liegt weit unter dem Haushaltsstrompreis. Wer eine Photovoltaikanlage ohne leistungsstarken Speicher betreibt, verschenkt deshalb Tag für Tag nutzbare Kilowattstunden. Ein modernes LiFePO4-Speichersystem hebt den Eigenverbrauch auf 70&nbsp;% bis über 85&nbsp;%, indem es Mittagsüberschüsse in die Morgen- und Abendspitze verschiebt. Das senkt den Netzbezug, entlastet das lokale Netz in der Mittagsspitze und macht Haushalte unabhängiger von Preisbewegungen der Versorger.
        </p>
        <p>
            Auch die Nachrüstung an eine bestehende Anlage ist üblich: AC-gekoppelte Speicher arbeiten mit dem vorhandenen Wechselrichter, Hybrid-Systeme bündeln PV und Batterie auf der DC-Seite. Ob Nachrüstung oder Neubau – die transparente Preisliste im <a href="___SHOP_URL___">Shop</a> ersetzt individuelle PDF-Angebote. Wer unsicher bei Zählerplatz, Phasenlage oder Notstrom ist, nutzt die <a href="___BERATUNG_URL___">kostenlose Fachberatung</a>. Vertiefende Einordnung liefern die Ratgeber <a href="___KAUFEN_URL___">Solarbatterie kaufen</a> und <a href="___PREISE_URL___">transparente Solarbatterie-Preise</a>.
        </p>

        <div class="werdu-highlight-card">
            <h3>Welche Speichergröße passt zu Ihnen?</h3>
            <p>Ermitteln Sie mit dem Online-Rechner in wenigen Minuten die passende Kapazität. Das Ergebnis führt zur Beratung – nicht zu einem automatischen Preisangebot.</p>
            <a href="___RECHNER_URL___" class="werdu-btn-primary">Jetzt Autarkie &amp; Speichergröße berechnen</a>
        </div>

        <h2 id="autarkie-vorteile">2. Mehr Autarkie, weniger Stromkosten</h2>
        <p>
            Ein passend dimensionierter Heimspeicher erhöht den Eigenverbrauch, senkt den Netzbezug und kann – mit geeigneter Wechselrichtertechnik – ausgewählte Stromkreise bei Netzausfall weiterversorgen.
        </p>
        <ul>
            <li><strong>Niedrigere Abschläge:</strong> Jede Kilowattstunde aus dem eigenen Speicher muss nicht vom Versorger bezogen werden.</li>
            <li><strong>Stabilere Energiekosten:</strong> Steigen die Netzpreise, bleiben die Erzeugungskosten Ihrer PV-Kilowattstunde nahezu konstant.</li>
            <li><strong>Optionale Notstromversorgung:</strong> Nur Systeme mit ausgewiesener Notstrom- oder Ersatzstromfunktion überbrücken einen Ausfall; Standard-Netzwechselrichter schalten ab. Details im Ratgeber <a href="___NOTSTROM_URL___">Notstrom und Ersatzstrom</a>.</li>
            <li><strong>E-Auto und Wärmepumpe:</strong> Gespeicherter Solarstrom kann abends die Wallbox oder die Wärmepumpe speisen, statt teuren Nachtstrom zu kaufen.</li>
        </ul>
        <p>
            Autarkie bedeutet nicht zwangsläufig 100&nbsp;% Inselbetrieb. In Mitteleuropa bleibt ein Netzanschluss für Winterlücken und Leistungsspitzen sinnvoll. Ziel ist ein hoher Jahresanteil selbst genutzten Solarstroms – das ist der Hebel, den Fraunhofer ISE für Wohngebäude quantifiziert hat. Weiterführend: <a href="___AUTARKIE_URL___">Energieautarkie erreichen</a> und <a href="___UNAB_URL___">Energieunabhängigkeit</a>.
        </p>

        <blockquote>
            Eine Analyse des <a href="https://www.ise.fraunhofer.de" target="_blank" rel="noopener">Fraunhofer-Instituts für Solare Energiesysteme (ISE)</a> zeigt: Mit einem passend dimensionierten Batteriespeicher lässt sich der Eigenverbrauchsanteil einer Wohngebäude-Photovoltaikanlage von rund 30&nbsp;% auf bis zu 80&nbsp;% steigern.
        </blockquote>

        <h2 id="dimensionierung-kapazitaet">3. Die richtige Kapazität für Ihren Speicher</h2>
        <p>
            Pro 1.000&nbsp;kWh Jahresverbrauch sind etwa 1,0 bis 1,5&nbsp;kWh nutzbare Speicherkapazität eine bewährte Ausgangsgröße – immer abgestimmt auf die kWp-Leistung der Photovoltaikanlage.
        </p>
        <p>
            Ist der Speicher zu klein, kaufen Sie abends weiterhin Netzstrom. Ist er stark überdimensioniert, steigen Anschaffungskosten, ohne dass die Batterie in ertragsarmen Winterwochen zuverlässig voll wird. Deshalb dimensioniert der <a href="___RECHNER_URL___">Autarkie-Rechner</a> nach Verbrauch und PV-Größe, nicht nach einem Marketing-Maximum. Eine zweite Meinung liefert die <a href="___GRATIS_URL___">Gratis-Online-Variante</a> desselben Rechenwegs.
        </p>

        <div class="werdu-table-wrapper werdu-matrix-wrapper">
            <table class="werdu-table werdu-matrix-table">
                <caption class="screen-reader-text">Richtwerte für PV-Leistung, Speicherkapazität und Autarkiegrad nach Jahresverbrauch</caption>
                <thead>
                    <tr>
                        <th>Jährlicher Verbrauch</th>
                        <th>Empfohlene PV-Leistung</th>
                        <th>Empfohlene Speichergröße</th>
                        <th>Erreichbare Autarkie</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>3.000&nbsp;–&nbsp;4.000&nbsp;kWh</td>
                        <td>4&nbsp;–&nbsp;6&nbsp;kWp</td>
                        <td><strong>5&nbsp;–&nbsp;7&nbsp;kWh</strong></td>
                        <td>ca. 70&nbsp;–&nbsp;78&nbsp;%</td>
                    </tr>
                    <tr>
                        <td>4.500&nbsp;–&nbsp;6.000&nbsp;kWh</td>
                        <td>7&nbsp;–&nbsp;10&nbsp;kWp</td>
                        <td><strong>8&nbsp;–&nbsp;10&nbsp;kWh</strong></td>
                        <td>ca. 75&nbsp;–&nbsp;83&nbsp;%</td>
                    </tr>
                    <tr>
                        <td>6.000&nbsp;–&nbsp;9.000&nbsp;kWh (E-Auto / Wärmepumpe)</td>
                        <td>10&nbsp;–&nbsp;15&nbsp;kWp</td>
                        <td><strong>12&nbsp;–&nbsp;16&nbsp;kWh</strong></td>
                        <td>ca. 80&nbsp;–&nbsp;88&nbsp;%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p>
            Die folgenden Systeme sind reale Shop-Produkte. Die Preise kommen dynamisch aus dem Preis-Manager, nicht aus diesem Text. Produktseiten: <a href="___P_BASEN_URL___">16 kWh Basen Green</a>, <a href="___P_16_URL___">16 kWh LiFePO4</a>, <a href="___P_30_URL___">30–32 kWh LiFePO4</a>.
        </p>

        <div class="werdu-product-grid" role="list">
            <article class="werdu-product-card-item is-bestseller" role="listitem">
                <h3>16 kWh Basen Green LiFePO4</h3>
                <p>51,2&nbsp;V / 314&nbsp;Ah, 200&nbsp;A Dauerstrom, 10.000 Zyklen gemäß Produktdatenblatt, Touchscreen und aktivem Balancer.</p>
                <div class="werdu-product-card-cta">
                    <strong>___PREIS_BASEN___</strong>
                    <a href="___P_BASEN_URL___" class="werdu-btn-primary">Zum Produkt</a>
                </div>
            </article>
            <article class="werdu-product-card-item" role="listitem">
                <h3>16 kWh LiFePO4 Heimspeicher</h3>
                <p>Klassische 16-kWh-Klasse für Eigenverbrauch. Aktuelle Spezifikation und Lieferumfang stehen auf der Produktseite.</p>
                <div class="werdu-product-card-cta">
                    <strong>___PREIS_16___</strong>
                    <a href="___P_16_URL___" class="werdu-btn-primary">Zum Produkt</a>
                </div>
            </article>
            <article class="werdu-product-card-item" role="listitem">
                <h3>30–32 kWh LiFePO4 Heimspeicher</h3>
                <p>Für hohe Lasten, E-Mobilität und den Wunsch nach maximaler Autarkie. Modular erweiterbare Bauform je nach Modell.</p>
                <div class="werdu-product-card-cta">
                    <strong>___PREIS_30___</strong>
                    <a href="___P_30_URL___" class="werdu-btn-primary">Zum Produkt</a>
                </div>
            </article>
        </div>

        <div style="text-align:center;">
            <a href="___RECHNER_URL___" class="werdu-btn-primary">Passende Größe mit dem Rechner bestätigen</a>
        </div>

        <h2 id="technologie-vergleich">4. LiFePO4, Natrium-Ionen und NMC im Vergleich</h2>
        <p>
            Für den stationären Heimspeicher ist LiFePO4 (Lithium-Eisenphosphat) die Standardchemie: hohe thermische Stabilität, 6.000–8.000 typische Vollzyklen und 15–20 Jahre kalendarische Nutzungsdauer.
        </p>
        <p>
            NMC-Zellen (Nickel-Mangan-Kobalt) sind energiedichter und in der Elektromobilität verbreitet, für Keller und Technikraum aber thermisch empfindlicher. Blei-Säure bleibt eine Referenz der Vergangenheit: wenige hundert bis rund 1.500 Zyklen und geringe nutzbare Entladetiefe. Natrium-Ionen-Speicher verzichten auf Lithium und Kobalt und zeigen oft gutes Kälteverhalten; ihre Zyklenfestigkeit liegt typischerweise unter LiFePO4. Im Sortiment finden Sie neben LiFePO4 auch ein <a href="___SODIUM_URL___">Natrium-Ionen-System mit 10 kWh und 5 kW Wechselrichter</a> sowie All-in-One-Geräte mit integriertem Hybrid-Wechselrichter (<a href="___AIO15_URL___">15 kWh / 5 kW</a>, <a href="___AIO30_URL___">30 kWh / 12 kW dreiphasig</a>).
        </p>
        <p>
            Sicherheit entsteht nicht nur durch die Zellchemie, sondern durch BMS, Gehäuse, Installation und korrekte Lagerung. Vertiefung: <a href="___SICHER_URL___">LiFePO4-Sicherheit</a>, <a href="___ZYKLEN_URL___">Zyklenfestigkeit</a>, <a href="___DOD_URL___">Entladetiefe</a> und <a href="___LAGER_URL___">richtige Lagerung</a>. Einen Systemüberblick gibt <a href="___SYSTEME_URL___">Heimspeicher-Systeme</a> sowie <a href="___AIO_URL___">All-in-One und Off-Grid</a>.
        </p>

        <h2 id="kosten-wirtschaftlichkeit">5. Kosten, 0&nbsp;% MwSt. &amp; Amortisation</h2>
        <p>
            Seit 2023 gilt nach § 12 Abs. 3 UStG 0&nbsp;% Umsatzsteuer auf begünstigte PV-Anlagen und die dazugehörigen Stromspeicher an Wohngebäuden – Kauf und Installation. Das ist keine pauschale Steuerfreiheit für jedes Zubehör.
        </p>
        <p>
            Die Listenpreise stehen tagesaktuell im Shop; dieser Text enthält bewusst keine festgeschriebenen Euro-Beträge. Eine Einordnung der Wirtschaftlichkeit finden Sie unter <a href="___KOSTEN_URL___">Heimspeicher-Kosten pro kWh</a> und <a href="___WAS_KOSTET_URL___">Was ein LiFePO4-Heimspeicher wirklich kostet</a>. Ob sich ein Speicher rechnet, hängt von Verbrauch, PV-Ertrag, Strompreis und Nutzungsdauer ab. Bei 15–20 Jahren Lebensdauer und 6.000–8.000 Zyklen bleibt nach der Amortisation in vielen Haushalten ein langer Nutzungszeitraum. Förderprogramme ändern sich; aktuelle Hinweise stehen im Beitrag zur <a href="___KFW_URL___">KfW-Förderung für Heimspeicher</a>. Verbindlich sind immer die Regeln des jeweiligen Programms, nicht eine Zusammenfassung auf dieser Seite. Steuerliche Einordnung: <a href="___MWST_URL___">MwSt-Befreiung Eigenverbrauch</a>.
        </p>
        <p>
            Die <a href="https://www.bundesnetzagentur.de" target="_blank" rel="noopener">Bundesnetzagentur</a> veröffentlicht Marktdaten zu Photovoltaik und Speichern. Verbraucherschutz-Hinweise zur Photovoltaik bietet die <a href="https://www.verbraucherzentrale.de" target="_blank" rel="noopener">Verbraucherzentrale</a>. WERDU verkauft zu transparenten Festpreisen – ohne versteckte Angebotsrunden.
        </p>

        <div class="werdu-card-soft">
            <h3>Lassen Sie sich fachlich beraten</h3>
            <p>Jedes Gebäude und jedes Lastprofil ist anders. Die Beratung ist kostenlos und unverbindlich; Preise sehen Sie vorher im Shop.</p>
            <a href="___BERATUNG_URL___" class="werdu-btn-primary">Kostenlose Beratung anfragen</a>
        </div>

        <h2 id="installation-fachbetrieb">6. Installation und optionaler Fachbetrieb</h2>
        <p>
            Der elektrische Anschluss eines Heimspeichers ist Arbeit für eine qualifizierte Elektrofachkraft. Optional kann der Speicher direkt an einen zertifizierten lokalen Installateur geliefert werden, der die Montage bei Ihnen vor Ort übernimmt.
        </p>
        <p>
            WERDU erstellt keine komplexen Individualangebote für die Montage. Jeder Fachbetrieb kalkuliert nach Aufwand, Zählerschrank, Leitungswegen und Stundensatz – es gibt keinen einheitlichen Installationsfestpreis. Die optionale Installateur-Auswahl ist in der Beratung, auf Produktseiten und an der <a href="___KASSE_URL___">Kasse</a> vorgesehen. Technischer Ablauf: <a href="___INSTALL_URL___">Heimspeicher-Installation</a> und die <a href="___PLUG_URL___">Plug-&amp;-Play-Anleitung</a>. Versandbedingungen: <a href="___VERSAND_URL___">Zahlung und Lieferung</a> sowie <a href="___LIEFER_URL___">Versand und Lieferbedingungen</a>.
        </p>
        <p>
            Netzbetreiber-Anmeldung, Zählerkonzept und Absicherung bleiben Aufgabe der Elektrofachkraft. All-in-One-Systeme reduzieren den Verkabelungsaufwand zwischen Batterie und Hybrid-Wechselrichter, ersetzen aber nicht die fachgerechte Netzbindung.
        </p>

        <h2 id="sicherheit-recht">7. Sicherheit, Apps und rechtliche Pflichten</h2>
        <p>
            LiFePO4 gilt als thermisch stabile Zellchemie für Heimspeicher. Rechtlich relevant bleiben Batteriegesetz, ElektroG und die fachgerechte Entsorgung am Lebensende – unabhängig von der Marke.
        </p>
        <p>
            Das Batterie-Management-System (BMS) überwacht Spannung, Strom, Temperatur und Zellausgleich. Viele Systeme lassen sich per App auslesen; kostenlose Anwendungen sind auf der Seite <a href="___APPS_URL___">kostenlose Apps für Ihre Solarbatterie</a> beschrieben, Energiemanagement unter <a href="___EMS_URL___">intelligente Energieoptimierung (EMS)</a> und <a href="___BMS_URL___">BMS- und App-Technologie</a>. Rechtliche Pflichtseiten: <a href="___BATTG_URL___">Batteriegesetz</a>, <a href="___ELEKTROG_URL___">ElektroG</a>, <a href="___ENTSORG_URL___">Entsorgung</a>. Brandschutz rund um PV: <a href="___DACH_URL___">Dachbrand und PV-Anlage</a>.
        </p>
        <p>
            Weitere Orientierung: <a href="___FAQ_URL___">FAQ Heimspeicher</a>, <a href="___SOLAR_URL___">Solarbatterien</a>, <a href="___KOMPLETT_URL___">Solaranlage mit Speicher 2026</a>, <a href="___NETZ_URL___">Netzengpässe überbrücken</a>. Unternehmen: <a href="___UEBER_URL___">Über uns</a>. Rechtliches: <a href="___IMP_URL___">Impressum</a>, <a href="___AGB_URL___">AGB</a>, <a href="___DS_URL___">Datenschutzerklärung</a>.
        </p>

        <h2 id="faq-bereich">8. Häufig gestellte Fragen zum PV-Speicher</h2>
        <p>Die folgenden Antworten sind bewusst ausführlich formuliert, damit Suchmaschinen und KI-Assistenten sie direkt zitieren können. Ergänzend steht die thematische <a href="___FAQ_URL___">FAQ-Übersicht</a> bereit.</p>
        <div class="werdu-faq-box">
        <div class="werdu-faq-container">___FAQ_ACCORDION___</div>
        </div>

        <div class="werdu-highlight-card" style="margin-top:56px;">
            <h3 style="font-size:1.7rem;">Starten Sie in Ihre energetische Unabhängigkeit</h3>
            <p style="max-width:650px;margin-left:auto;margin-right:auto;">Berechnen Sie Ihre Kapazität und lassen Sie sich kostenlos beraten. Die Festpreise sehen Sie jederzeit im Shop – ohne automatisches PDF-Angebot.</p>
            <a href="___BERATUNG_URL___" class="werdu-btn-primary">Jetzt kostenlose Beratung anfragen</a>
        </div>

    </section>
</div>

___FAQ_JSONLD___
___SOFTWARE_JSONLD___
HTML;

    $replacements = array(
        '___BERATUNG_URL___'   => esc_url( $beratung ),
        '___RECHNER_URL___'    => esc_url( $rechner ),
        '___SHOP_URL___'       => esc_url( $shop ),
        '___FAQ_ACCORDION___'  => werdu_home_seo_faq_html(),
        '___FAQ_JSONLD___'     => werdu_home_seo_faq_json_ld(),
        '___SOFTWARE_JSONLD___'=> werdu_home_seo_software_json_ld(),
        '___PREIS_BASEN___'    => wp_kses_post( $preis_basen ),
        '___PREIS_16___'       => wp_kses_post( $preis_16 ),
        '___PREIS_30___'       => wp_kses_post( $preis_30 ),
        '___P_BASEN_URL___'    => esc_url( home_url( '/16-kwh-lifepo4-heimspeicher-51-2v-314ah/' ) ),
        '___P_16_URL___'       => esc_url( home_url( '/16-kwh-heimspeicher-lifepo4-solarbatterie/' ) ),
        '___P_30_URL___'       => esc_url( home_url( '/30-32-kwh-lifepo4-heimspeicher-560-628ah/' ) ),
        '___SODIUM_URL___'     => esc_url( home_url( '/sodium-ion-solarspeicher-10-kwh-mit-5-kw-wechselrichter/' ) ),
        '___AIO15_URL___'      => esc_url( home_url( '/tewaycell-15-kwh-all-in-one-lifepo4-solarbatterie-5-kw-hybrid-wechselrichter/' ) ),
        '___AIO30_URL___'      => esc_url( home_url( '/tewaycell-30-kwh-all-in-one-solarspeicher-mit-12-kw-hybrid-wechselrichter-3-phasig/' ) ),
        '___KAUFEN_URL___'     => esc_url( home_url( '/solarbatterie-kaufen/' ) ),
        '___PREISE_URL___'     => esc_url( home_url( '/solarbatterie-preise-transparente-kosten/' ) ),
        '___NOTSTROM_URL___'   => esc_url( home_url( '/notstrom-heimspeicher-ersatzstrom-blackout/' ) ),
        '___AUTARKIE_URL___'   => esc_url( home_url( '/energieautarkie-erreichen-unabhaengig-vom-stromnetz-2026-werdu-de/' ) ),
        '___UNAB_URL___'       => esc_url( home_url( '/energieunabhaengigkeit/' ) ),
        '___GRATIS_URL___'     => esc_url( home_url( '/gratis-heimspeicher-rechner-online/' ) ),
        '___SICHER_URL___'     => esc_url( home_url( '/lifepo4-sicherheit-die-sicherste-solarbatterie-technologie-2026/' ) ),
        '___ZYKLEN_URL___'     => esc_url( home_url( '/wie-viele-zyklen-schafft-eine-solarbatterie-von-werdu-de/' ) ),
        '___DOD_URL___'        => esc_url( home_url( '/entladetiefe-tabelle/' ) ),
        '___LAGER_URL___'      => esc_url( home_url( '/achtung-garantie-richtige-lagerung-ihrer-lifepo4-batterie/' ) ),
        '___SYSTEME_URL___'    => esc_url( home_url( '/heimspeicher-systeme/' ) ),
        '___AIO_URL___'        => esc_url( home_url( '/all-in-one-heimspeicher-off-grid/' ) ),
        '___KOSTEN_URL___'     => esc_url( home_url( '/heimspeicher-kosten-pro-kwh/' ) ),
        '___WAS_KOSTET_URL___' => esc_url( home_url( '/was-ein-lifepo4-heimspeicher-wirklich-kostet/' ) ),
        '___KFW_URL___'        => esc_url( home_url( '/kfw-foerderung-heimspeicher-2026-bis-zu-15-zuschuss-werdu-de/' ) ),
        '___MWST_URL___'       => esc_url( home_url( '/mwst-befreiung-eigenverbrauch/' ) ),
        '___KASSE_URL___'      => esc_url( home_url( '/kasse/' ) ),
        '___INSTALL_URL___'    => esc_url( home_url( '/heimspeicher-installation/' ) ),
        '___PLUG_URL___'       => esc_url( home_url( '/heimspeicher-installation-plug-play-anleitung-2026-werdu-de/' ) ),
        '___VERSAND_URL___'    => esc_url( home_url( '/zahlung-und-lieferung/' ) ),
        '___LIEFER_URL___'     => esc_url( home_url( '/heimspeicher-versand-lieferbedingungen/' ) ),
        '___APPS_URL___'       => esc_url( home_url( '/kostenlose-apps-fuer-ihre-solarbatterie/' ) ),
        '___EMS_URL___'        => esc_url( home_url( '/intelligente-energieoptimierung-fuer-solarbatterien-ems/' ) ),
        '___BMS_URL___'        => esc_url( home_url( '/intelligente-energiesteuerung-bms-app-technologie-2026-werdu-de/' ) ),
        '___BATTG_URL___'      => esc_url( home_url( '/batteriegesetz/' ) ),
        '___ELEKTROG_URL___'   => esc_url( home_url( '/elektrog/' ) ),
        '___ENTSORG_URL___'    => esc_url( home_url( '/entsorgung/' ) ),
        '___DACH_URL___'       => esc_url( home_url( '/dachbrand-pv-anlage/' ) ),
        '___FAQ_URL___'        => esc_url( home_url( '/faq-heimspeicher/' ) ),
        '___SOLAR_URL___'      => esc_url( home_url( '/solarbatterien/' ) ),
        '___KOMPLETT_URL___'   => esc_url( home_url( '/solaranlage-mit-speicher-2026-pv-batterie-komplettsysteme/' ) ),
        '___NETZ_URL___'       => esc_url( home_url( '/netzengpaesse-ueberbruecken-7-bewaehrte-strategien/' ) ),
        '___UEBER_URL___'      => esc_url( home_url( '/ueber-uns/' ) ),
        '___IMP_URL___'        => esc_url( home_url( '/impressum/' ) ),
        '___AGB_URL___'        => esc_url( home_url( '/agb/' ) ),
        '___DS_URL___'         => esc_url( home_url( '/datenschutzerklaerung/' ) ),
    );

    return str_replace( array_keys( $replacements ), array_values( $replacements ), $template );
}

/**
 * Injecteert Hero-blok + SEO-body op de homepage. Werkt uitsluitend op de
 * front page, uitsluitend binnen de hoofd-loop (niet in widgets/excerpts/
 * feeds), en is idempotent (dubbele injectie bij herhaalde the_content-
 * aanroepen binnen dezelfde request wordt voorkomen via een marker-check).
 */
function werdu_home_seo_inject( $content ) {
    if ( is_admin() || is_feed() || ! is_front_page() ) {
        return $content;
    }
    // The front page is fully rendered by template-werdu-v2.php.
    return $content;
}
add_filter( 'the_content', 'werdu_home_seo_inject', 15 );

// ============================================
// GLOBAL SANITIZER — Elementor HTML-widget output (ALLE pagina's)
// ============================================

/**
 * Sitebrede sanitizer (niet beperkt tot de homepage):
 *
 * A. Verwijdert hardgecodeerde inline style="...background/color/font-family..."
 *    uit Elementor HTML-widget-output. Dit voorkomt dark-on-dark contrast-
 *    problemen en inline styling die ons designsysteem doorbreekt. Alleen
 *    style-attributen die een van deze drie eigenschappen bevatten worden
 *    verwijderd — overige inline styles (bv. spacing/layout) blijven intact.
 *
 * B. Normaliseert een vaste set keyword-gestufte koppen (letterlijk
 *    "Heimspeicher kaufen ..." herhaald in titels) naar natuurlijkere
 *    varianten, sitebreed.
 *
 * LET OP: dit is een regex-gebaseerde best-effort sanitizer op de
 * uiteindelijke HTML-tekst (net als de eerdere /kontakt/-fix in
 * fix-calculator-links.php) — geen volwaardige HTML-parser. Style-
 * attributen worden verwijderd op basis van "quote...quote" matching
 * (niet backreference-based), wat in de praktijk voor Elementor-content
 * betrouwbaar is omdat style-attributen vrijwel altijd met dubbele quotes
 * worden geschreven.
 */
function werdu_sanitize_elementor_html_widget_output( $content ) {
    if ( is_admin() || is_feed() || ! is_string( $content ) || '' === $content ) {
        return $content;
    }

    // A. Hardgecodeerde inline background/color/font-family styles verwijderen.
    $content = preg_replace(
        '/style=["\'][^"\']*(?:background|color|font-family)[^"\']*["\']/i',
        '',
        $content
    );

    // B. Keyword-gestufte koppen normaliseren (\x{2013} = en dash "–").
    $title_replacements = array(
        '/Heimspeicher kaufen\s*[:\x{2013}\-]\s*Unsere Bestseller/iu'         => 'Unsere Bestseller Batteriespeicher',
        '/Heimspeicher kaufen\s*[:\x{2013}\-]\s*Vergleich der Top-Systeme/iu' => 'Vergleich der Top-Speichersysteme',
        '/Häufig gestellte Fragen zum Heimspeicher Kauf/iu'                   => 'Häufig gestellte Fragen (FAQ)',
        '/Vor dem Heimspeicher Kauf:\s*Das sollten Sie wissen/iu'             => 'Wichtige Informationen vor dem Kauf',
        '/Keine Neuigkeiten zum Heimspeicher Kauf verpassen/iu'               => 'Bleiben Sie auf dem Laufenden',
        '/Heimspeicher kaufen für maximale Energieunabhängigkeit/iu'          => 'Maximale Energieunabhängigkeit',
        '/PV Speicher kaufen 2026:\s*Testsieger/iu'                          => 'PV-Speicher & Autarkie-Rechner',
    );

    foreach ( $title_replacements as $pattern => $replacement ) {
        $result = preg_replace( $pattern, $replacement, $content );
        if ( null !== $result ) {
            $content = $result;
        }
    }

    // C. Homepage LCP: never lazy-load the designated hero image.
    if ( is_front_page() ) {
        $content = preg_replace(
            '/(<img\b[^>]*class="[^"]*werdu-hero-lcp[^"]*"[^>]*)\sloading=(["\'])lazy\2/i',
            '$1',
            $content
        );
    }

    return $content;
}
add_filter( 'the_content', 'werdu_sanitize_elementor_html_widget_output', 999 );

/**
 * Extra vangnet op Elementor's eigen data-hook: het "html"-veld van elke
 * HTML-widget wordt al vóór het renderen door dezelfde sanitizer gehaald.
 * Dit vult de the_content-filter aan voor contexten waarin die filter niet
 * (volledig) wordt doorlopen, zoals Template Library-content of globale
 * widgets (zie Elementor changelog: "builder_content_data" is bedoeld voor
 * precies dit soort hergebruikte content).
 */
function werdu_sanitize_elementor_builder_data( $data ) {
    if ( is_admin() || ! is_array( $data ) ) {
        return $data;
    }

    foreach ( $data as &$element ) {
        if ( ! is_array( $element ) ) {
            continue;
        }

        if ( isset( $element['widgetType'], $element['settings']['html'] )
            && 'html' === $element['widgetType']
            && is_string( $element['settings']['html'] ) ) {
            $element['settings']['html'] = werdu_sanitize_elementor_html_widget_output( $element['settings']['html'] );
        }

        if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
            $element['elements'] = werdu_sanitize_elementor_builder_data( $element['elements'] );
        }
    }
    unset( $element );

    return $data;
}
add_filter( 'elementor/frontend/builder_content_data', 'werdu_sanitize_elementor_builder_data' );
