<?php
/**
 * Plugin Name: WERDU Homepage SEO & AIO Upgrade
 * Description: Injecteert de Hero H1/intro en het uitgebreide SEO/AIO-contentblok (ToC, vergelijkingstabel, FAQ, JSON-LD) op de homepage — direct onder de Heimspeicher-rechner sectie — met een clean, high-end designsysteem (CSS-variabelen, borderless tabel, subtiele focus-states) zonder de bestaande Elementor-content, calculator-logica of styling te veranderen.
 * Version: 3.3
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
    return <<<'CSS'
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

.werdu-faq-item.is-open .werdu-faq-icon {
  transform: rotate(45deg); /* Turns + into x */
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

function werdu_home_seo_print_css() {
    if ( is_admin() || ! is_front_page() ) {
        return;
    }
    echo '<style id="werdu-home-seo-css">' . werdu_home_seo_base_css() . '</style>' . "\n";
}
// Priority 99999: print zo laat mogelijk in <head>, na alle Elementor- en
// theme-stylesheets, zodat de !important-regels hierboven altijd winnen
// bij gelijke specificiteit (cascade-volgorde), zonder specificiteit-oorlog.
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
    ?>
<script id="werdu-home-seo-interactions">
(function () {
  'use strict';

  var prefersReducedMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

  var CHECK_SVG = '<svg class="werdu-btn-check" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>';
  var SPINNER_HTML = '<span class="werdu-btn-spinner" aria-hidden="true"></span>';

  // ---- Reel 3: FAQ-accordion (CSS Grid transition, geen JS hoogteberekening) ----
  function bindFaqAccordion() {
    document.querySelectorAll( '.werdu-faq-item' ).forEach( function ( item ) {
      var header = item.querySelector( '.werdu-faq-header' );
      if ( ! header || header.dataset.werduBound === '1' ) {
        return;
      }
      header.dataset.werduBound = '1';
      header.setAttribute( 'role', 'button' );
      header.setAttribute( 'tabindex', '0' );
      header.setAttribute( 'aria-expanded', item.classList.contains( 'is-open' ) ? 'true' : 'false' );

      function toggle() {
        var open = item.classList.toggle( 'is-open' );
        header.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
      }

      header.addEventListener( 'click', toggle );
      header.addEventListener( 'keydown', function ( e ) {
        if ( e.key === 'Enter' || e.key === ' ' ) {
          e.preventDefault();
          toggle();
        }
      } );
    } );
  }

  // ---- Reel 21: 3-staps knop-feedback (Default -> Loading -> Success) ----

  // CTA-links (.werdu-btn-primary): korte, niet-blokkerende loading/success
  // animatie vóór de navigatie, puur voor visuele feedback op de klik.
  function bindCtaButtons() {
    document.querySelectorAll( 'a.werdu-btn-primary' ).forEach( function ( btn ) {
      if ( btn.dataset.werduFeedbackBound === '1' ) {
        return;
      }
      btn.dataset.werduFeedbackBound = '1';

      var href = btn.getAttribute( 'href' );
      if ( ! href || btn.target === '_blank' ) {
        return;
      }

      btn.addEventListener( 'click', function ( e ) {
        if ( btn.classList.contains( 'is-loading' ) || btn.classList.contains( 'is-success' ) ) {
          return;
        }
        e.preventDefault();

        if ( prefersReducedMotion ) {
          window.location.href = href;
          return;
        }

        var originalHTML = btn.innerHTML;
        btn.classList.add( 'is-loading' );
        btn.innerHTML = SPINNER_HTML + '<span>Einen Moment...</span>';

        setTimeout( function () {
          btn.classList.remove( 'is-loading' );
          btn.classList.add( 'is-success' );
          btn.innerHTML = CHECK_SVG + '<span>Weiter...</span>';

          setTimeout( function () {
            window.location.href = href;
          }, 450 );
        }, 500 );
      } );
    } );
  }

  // Calculator-submitknop (.werdu-calc-btn): loading zodra geklikt, success
  // zodra het resultaat-element daadwerkelijk verandert (geen vaste timer).
  function bindCalcButton() {
    var btn = document.querySelector( '.werdu-calc-btn' );
    if ( ! btn || btn.dataset.werduFeedbackBound === '1' ) {
      return;
    }
    var resultEl = document.getElementById( 'calc-result' ) || document.querySelector( '.werdu-calc-result' );
    if ( ! resultEl ) {
      return;
    }
    btn.dataset.werduFeedbackBound = '1';

    var originalHTML = btn.innerHTML;
    var revertTimer = null;
    var safetyTimer = null;

    function showSuccess() {
      clearTimeout( safetyTimer );
      btn.classList.remove( 'is-loading' );
      btn.classList.add( 'is-success' );
      btn.innerHTML = CHECK_SVG + '<span>Berechnet!</span>';
      revertTimer = setTimeout( function () {
        btn.classList.remove( 'is-success' );
        btn.innerHTML = originalHTML;
      }, 1500 );
    }

    var observer = new MutationObserver( function () {
      if ( btn.classList.contains( 'is-loading' ) ) {
        showSuccess();
      }
    } );
    observer.observe( resultEl, { childList: true, subtree: true, characterData: true } );

    btn.addEventListener( 'click', function () {
      clearTimeout( revertTimer );
      btn.classList.remove( 'is-success' );

      if ( prefersReducedMotion ) {
        return;
      }

      btn.classList.add( 'is-loading' );
      btn.innerHTML = SPINNER_HTML + '<span>Berechne...</span>';

      // Vangnet: als er onverhoopt geen resultaat-mutatie volgt, niet
      // eindeloos in de loading-state blijven hangen.
      safetyTimer = setTimeout( function () {
        if ( btn.classList.contains( 'is-loading' ) ) {
          btn.classList.remove( 'is-loading' );
          btn.innerHTML = originalHTML;
        }
      }, 6000 );
    } );
  }

  // ---- Design System 2.0: auto-tag CTA-links zonder eigen class ----
  // Sommige CTA-links in de Elementor-database content hebben geen eigen
  // class (bv. de kale <a href="...">Beratung anfragen</a> in de
  // rechtvaardige-toelichting-tekst). In plaats van een ongeldige CSS
  // ":contains()"-selector te gebruiken, herkennen we deze hier op hun
  // zichtbare tekst en voegen we .werdu-cta-auto toe — de CSS hierboven
  // stylet die class met dezelfde high-conversion gradient.
  var CTA_TEXT_PATTERN = /beratung anfragen|beratung anfordern|angebot anfordern|kostenlose\s+(beratung|analyse|fachanalyse)/i;
  function bindCtaAutoTag() {
    var content = document.getElementById( 'content' ) || document.body;
    content.querySelectorAll( 'a' ).forEach( function ( link ) {
      if ( link.dataset.werduCtaChecked === '1' ) {
        return;
      }
      link.dataset.werduCtaChecked = '1';

      if ( link.classList.contains( 'werdu-calc-cta' )
        || link.classList.contains( 'werdu-seo-cta' )
        || link.classList.contains( 'btn-3d' )
        || link.classList.contains( 'werdu-btn-primary' ) ) {
        return;
      }

      var text = ( link.textContent || '' ).trim();
      if ( text && CTA_TEXT_PATTERN.test( text ) ) {
        link.classList.add( 'werdu-cta-auto' );
      }
    } );
  }

  // ---- Design System 2.0: subtiele "lift" op het actieve rechner-veld ----
  function bindCalcInputMicroInteractions() {
    if ( prefersReducedMotion ) {
      return;
    }
    var container = document.querySelector( '.werdu-calc-container' );
    if ( ! container || container.dataset.werduLiftBound === '1' ) {
      return;
    }
    container.dataset.werduLiftBound = '1';

    container.querySelectorAll( 'input, select' ).forEach( function ( field ) {
      var row = field.closest( '.calc-row' ) || field.parentElement;
      if ( ! row ) {
        return;
      }
      row.style.transition = 'transform 0.2s ease';
      field.addEventListener( 'focus', function () {
        row.style.transform = 'scale(1.01)';
      } );
      field.addEventListener( 'blur', function () {
        row.style.transform = 'scale(1)';
      } );
    } );
  }

  // Scroll & Reveal (opacity/translateY-onthulling bij scroll) is bewust
  // verwijderd in Design System 3.1: content moet direct zichtbaar/leesbaar
  // zijn (performance + geen "flash of invisible content" voor SEO/AIO),
  // in plaats van pas na een IntersectionObserver-trigger te verschijnen.

  function init() {
    bindFaqAccordion();
    bindCtaButtons();
    bindCalcButton();
    bindCtaAutoTag();
    bindCalcInputMicroInteractions();
  }

  if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', init );
  } else {
    init();
  }

  // De calculator- en FAQ-markup kunnen iets vertraagd renderen (Elementor);
  // kort blijven proberen zodat nieuw gerenderde elementen ook gebonden worden.
  var attempts = 0;
  var timer = setInterval( function () {
    attempts++;
    init();
    if ( attempts > 20 ) {
      clearInterval( timer );
    }
  }, 300 );
})();
</script>
    <?php
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
    $rechner = werdu_home_seo_rechner_url();

    $template = <<<'HTML'
<div class="werdu-seo-container" style="padding-bottom:0;">
    <div class="werdu-hero-container">
        <div>
            <span class="werdu-badge-de">Für den deutschen Markt · 0&nbsp;% MwSt.</span>
            <h1>PV-Speicher für Ihr Zuhause: Autarkie-Rechner &amp; Testsieger 2026</h1>
            <p class="werdu-hero-p">Sie möchten Ihre Stromkosten spürbar senken und sich unabhängiger von steigenden Netzpreisen machen? Ein moderner PV-Speicher speichert Ihren selbst erzeugten Solarstrom und stellt ihn genau dann bereit, wenn Sie ihn wirklich brauchen – auch abends, nachts und bei einem Stromausfall.</p>
            <div class="werdu-usps-grid">
                <div class="werdu-usp-tag"><span class="werdu-usp-check">✓</span> 0&nbsp;% MwSt. auf PV-Speicher</div>
                <div class="werdu-usp-tag"><span class="werdu-usp-check">✓</span> LiFePO4-Technologie</div>
                <div class="werdu-usp-tag"><span class="werdu-usp-check">✓</span> Optionale Notstromfunktion</div>
                <div class="werdu-usp-tag"><span class="werdu-usp-check">✓</span> Bis zu 85&nbsp;% Autarkiegrad</div>
            </div>
            <a href="___RECHNER_URL___" class="btn-werdu-primary">Jetzt Autarkie-Rechner starten</a>
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
HTML;

    return str_replace( '___RECHNER_URL___', esc_url( $rechner ), $template );
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

    $template = <<<'HTML'
<div class="werdu-seo-container">

    <!-- Table of Contents -->
    <div class="werdu-toc-box">
        <h2>Inhaltsverzeichnis: Ihr Ratgeber rund um den PV-Speicher</h2>
        <ul>
            <li><a href="#warum-pv-speicher-kaufen">1. Warum sich ein PV-Speicher 2026 lohnt</a></li>
            <li><a href="#autarkie-vorteile">2. Mehr Autarkie, weniger Stromkosten</a></li>
            <li><a href="#dimensionierung-kapazitaet">3. Die richtige Kapazität für Ihren Speicher</a></li>
            <li><a href="#technologie-vergleich">4. LiFePO4 vs. NMC: Technologien im Vergleich</a></li>
            <li><a href="#kosten-wirtschaftlichkeit">5. Kosten, Förderung &amp; Amortisation im Überblick</a></li>
            <li><a href="#faq-bereich">6. Häufig gestellte Fragen zum PV-Speicher</a></li>
        </ul>
    </div>

    <!-- Schnellauswahl: 3 Szenario-Karten (Welcher Speicher passt zu mir?) -->
    <div class="werdu-section-head" id="schnellauswahl">
        <h2>Welcher PV-Speicher passt zu Ihnen?</h2>
        <p>Drei typische Haushaltsprofile – finden Sie in Sekunden Ihre passende Kategorie und bestätigen Sie die genaue Größe anschließend mit dem kostenlosen Autarkie-Rechner.</p>
    </div>
    <div class="werdu-card-container">
        <div class="werdu-scenario-card">
            <span class="werdu-scenario-badge">Beliebteste Wahl</span>
            <h3>Standard-Haushalt</h3>
            <p>2–4 Personen, ca. 3.000–4.500&nbsp;kWh Jahresverbrauch, klassische PV-Anlage ohne E-Auto.</p>
            <ul>
                <li>Empfohlene Größe: 5–8&nbsp;kWh</li>
                <li>Autarkiegrad: ca. 70–78&nbsp;%</li>
                <li>0&nbsp;% MwSt. inklusive</li>
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
            <p>Sie möchten größtmögliche Unabhängigkeit und bei einem Stromausfall abgesichert sein.</p>
            <ul>
                <li>Empfohlene Größe: 16–32&nbsp;kWh</li>
                <li>Optionale Notstromfunktion</li>
                <li>All-in-One Off-Grid-fähig</li>
            </ul>
            <a href="___BERATUNG_URL___" class="btn-werdu-primary">Beratung anfragen</a>
        </div>
    </div>

    <!-- Main Article Body -->
    <section class="werdu-seo-body">

        <h2 id="warum-pv-speicher-kaufen">1. Warum sich ein PV-Speicher 2026 lohnt</h2>
        <p>
            Die Einspeisevergütung für Solarstrom liegt auf einem historischen Tiefstand, während die Strompreise für deutsche Haushalte weiterhin hoch bleiben. Wer eine Photovoltaikanlage ohne leistungsstarken Speicher betreibt, verschenkt Tag für Tag bares Geld: Ohne eigene Speichermöglichkeit nutzen Eigenheimbesitzer im Durchschnitt lediglich 20&nbsp;% bis 30&nbsp;% ihres selbst erzeugten Solarstroms. Der große Rest fließt für wenige Cent ins öffentliche Netz – nur um abends teuren Netzstrom zurückzukaufen.
        </p>
        <p>
            Ein modernes Speichersystem hebt Ihren Eigenverbrauch sofort auf 70&nbsp;% bis über 85&nbsp;%. Es speichert die ungenutzte Sonnenenergie der Mittagsstunden und stellt sie genau dann zur Verfügung, wenn der Verbrauch im Haushalt am höchsten ist: morgens und abends. So werden Sie spürbar unabhängiger von fossilen Energieträgern und den Preissteigerungen der Stromkonzerne. Auch bei einer bestehenden Solaranlage lohnt sich die Nachrüstung eines Batteriespeichers – sie sichert den langfristigen Wert Ihrer Immobilie und bringt Sie Ihrer persönlichen Energiewende einen großen Schritt näher.
        </p>

        <div class="werdu-highlight-card">
            <h3>Welche Speichergröße passt zu Ihnen?</h3>
            <p>Ermitteln Sie mit unserem präzisen Online-Rechner in unter 2 Minuten die optimale Kapazität und Ihre jährliche Ersparnis.</p>
            <a href="___RECHNER_URL___" class="werdu-btn-primary">Jetzt Autarkie &amp; Speichergröße berechnen</a>
        </div>

        <h2 id="autarkie-vorteile">2. Mehr Autarkie, weniger Stromkosten</h2>
        <p>
            Die Entscheidung für eine hochwertige Solarbatterie bringt weit mehr als nur finanzielle Ersparnisse. Es geht um Autonomie, Versorgungssicherheit und maximale Unabhängigkeit im eigenen Zuhause.
        </p>
        <ul>
            <li><strong>Spürbar niedrigere Abschlagszahlungen:</strong> Jede Kilowattstunde aus Ihrem eigenen Speicher müssen Sie nicht mehr teuer vom Netzbetreiber beziehen.</li>
            <li><strong>Schutz vor Strompreissteigerungen:</strong> Steigen die Netzstrompreise, bleiben Ihre Erzeugungskosten konstant bei nahezu 0&nbsp;Cent pro Kilowattstunde.</li>
            <li><strong>Optionale Notstromversorgung:</strong> Moderne Heimspeicher sichern Ihr Zuhause bei Netzausfällen ab und halten Kühlschrank, Heizung und Licht unterbrechungsfrei am Laufen.</li>
            <li><strong>Optimal für E-Auto und Wärmepumpe:</strong> Nutzen Sie gespeicherten Solarstrom, um Ihr Elektrofahrzeug abends kostengünstig zu laden oder Ihre Wärmepumpe zu betreiben.</li>
        </ul>

        <blockquote>
            "Eine wissenschaftliche Analyse des <a href="https://www.ise.fraunhofer.de" target="_blank" rel="noopener dofollow">Fraunhofer-Instituts für Solare Energiesysteme (ISE)</a> bestätigt: Durch den gezielten Einsatz eines optimal dimensionierten Batteriespeichers lässt sich der Eigenverbrauchsanteil einer Wohngebäude-Photovoltaikanlage von rund 30&nbsp;% auf bis zu 80&nbsp;% steigern."
        </blockquote>

        <h2 id="dimensionierung-kapazitaet">3. Die richtige Kapazität für Ihren Speicher</h2>
        <p>
            Eine der wichtigsten Entscheidungen rund um Ihren PV-Speicher ist die passende Dimensionierung. Ist der Speicher zu klein, kaufen Sie abends weiterhin teuren Netzstrom zu. Ist er stark überdimensioniert, steigen die Anschaffungskosten unnötig, ohne dass die Batterie in den ertragsarmen Wintermonaten voll geladen werden kann.
        </p>
        <p>
            Als bewährte Praxisregel für Einfamilienhäuser gilt: Pro 1.000&nbsp;kWh jährlichem Stromverbrauch sollte die Nutzkapazität etwa 1 bis 1,5&nbsp;kWh betragen – abgestimmt auf die Spitzenleistung (kWp) Ihrer Photovoltaikanlage.
        </p>

        <div class="werdu-table-wrapper werdu-matrix-wrapper">
            <table class="werdu-table werdu-matrix-table">
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
            Zur Orientierung: So schneiden unsere drei meistgewählten Speichergrößen im direkten Vergleich ab. Wählen Sie eine Karte aus, um die Kennzahlen hervorzuheben, und bestätigen Sie die für Sie passende Größe anschließend mit dem Autarkie-Rechner.
        </p>

        <div class="werdu-variant-group" role="radiogroup" aria-label="Speichergröße vergleichen">
            <label class="werdu-variant-label">
                <input type="radio" name="werdu-variant-picker" value="16-basen-green" checked>
                <div class="werdu-variant-card">
                    <h3>16 kWh Basen Green</h3>
                    <p>LiFePO4 • 51,2V, 314Ah • 200A Dauerstrom • 10.000 Zyklen</p>
                    <strong>ab 1.990,- €</strong>
                </div>
            </label>
            <label class="werdu-variant-label">
                <input type="radio" name="werdu-variant-picker" value="16-tewaycell">
                <div class="werdu-variant-card">
                    <h3>16 kWh TewayCell</h3>
                    <p>LiFePO4 • 48-51,2V, 300Ah • 8.000 Zyklen • Grade-A Zellen</p>
                    <strong>ab 2.345,- €</strong>
                </div>
            </label>
            <label class="werdu-variant-label">
                <input type="radio" name="werdu-variant-picker" value="30-32-tewaycell">
                <div class="werdu-variant-card">
                    <h3>30-32 kWh TewayCell</h3>
                    <p>LiFePO4 • Maximale Autarkie • Modulare Erweiterung</p>
                    <strong>ab 3.899,- €</strong>
                </div>
            </label>
        </div>

        <div style="text-align:center;">
            <a href="___RECHNER_URL___" class="werdu-btn-primary">Passende Größe mit dem Rechner bestätigen</a>
        </div>

        <h2 id="technologie-vergleich">4. LiFePO4 vs. NMC: Technologien im Vergleich</h2>
        <p>
            Moderne Speicherlösungen unterscheiden sich vor allem in der Zellchemie. Die sicherste und langlebigste Technologie für den stationären Einsatz im Eigenheim ist die Lithium-Eisenphosphat-Zelle (LiFePO4).
        </p>
        <p>
            Im Vergleich zu älteren NMC-Akkus (Lithium-Nickel-Mangan-Kobaltoxid) überzeugt LiFePO4 durch eine unübertroffene thermische und chemische Stabilität – ein thermisches Durchgehen ist bauartbedingt nahezu ausgeschlossen. Hochwertige LiFePO4-Systeme erreichen zudem 6.000 bis 8.000 vollständige Ladezyklen, was einer realistischen Lebensdauer von 15 bis 20 Jahren entspricht. Und: Diese Technologie verzichtet vollständig auf das umstrittene Schwermetall Kobalt.
        </p>

        <h2 id="kosten-wirtschaftlichkeit">5. Kosten, Förderung &amp; Amortisation im Überblick</h2>
        <p>
            Dank technologischem Fortschritt und skalierender Produktion sind die Preise für Batteriespeicher in den vergangenen Jahren spürbar gesunken. Zusätzlich profitieren Sie in Deutschland von staatlichen Vergünstigungen: Seit 2023 gilt gemäß § 12 Abs. 3 UStG ein Steuersatz von <strong>0&nbsp;% Umsatzsteuer</strong> auf Kauf und Installation von PV-Anlagen und deren Stromspeichern auf Wohngebäuden – Sie sparen also direkt 19&nbsp;% bei der Anschaffung.
        </p>
        <p>
            Unter Berücksichtigung der eingesparten Netzstromkosten amortisiert sich ein hochwertiger Speicher heute in der Regel bereits nach 7 bis 9 Jahren. Da moderne LiFePO4-Systeme 15 bis 20 Jahre halten, erwirtschaftet Ihr Speicher über seine restliche Lebensdauer einen erheblichen finanziellen Nettogewinn.
        </p>

        <div class="werdu-card-soft">
            <h3>Lassen Sie sich individuell beraten</h3>
            <p>Jedes Gebäude und jedes Verbrauchsprofil ist anders. Wir helfen Ihnen, die passende Lösung für Ihr Zuhause zu finden.</p>
            <a href="___BERATUNG_URL___" class="werdu-btn-primary">Kostenlose Beratung anfragen</a>
        </div>

        <h2 id="faq-bereich">6. Häufig gestellte Fragen zum PV-Speicher</h2>
        <div class="werdu-faq-box">
        <div class="werdu-faq-container">
            <div class="werdu-faq-item is-open">
                <div class="werdu-faq-header">
                    <span>Kann ich einen Speicher nachträglich einbauen?</span>
                    <span class="werdu-faq-icon">+</span>
                </div>
                <div class="werdu-faq-answer-wrapper">
                    <div class="werdu-faq-answer-inner">
                        <p>Ja, ein PV-Speicher lässt sich an nahezu jede bestehende Photovoltaikanlage nachrüsten. Je nach vorhandener Technik kommen AC-gekoppelte Systeme (ideal für die Nachrüstung) oder ein hybrider DC-Wechselrichter zum Einsatz.</p>
                    </div>
                </div>
            </div>
            <div class="werdu-faq-item">
                <div class="werdu-faq-header">
                    <span>Wie lange hält eine moderne Solarbatterie?</span>
                    <span class="werdu-faq-icon">+</span>
                </div>
                <div class="werdu-faq-answer-wrapper">
                    <div class="werdu-faq-answer-inner">
                        <p>Hochwertige LiFePO4-Speicher erreichen eine Lebensdauer von 15 bis 20 Jahren und bewältigen mühelos 6.000 bis 8.000 Ladezyklen. Selbst danach verfügen sie meist noch über eine Restkapazität von mehr als 80&nbsp;%.</p>
                    </div>
                </div>
            </div>
            <div class="werdu-faq-item">
                <div class="werdu-faq-header">
                    <span>Funktioniert der Speicher auch bei einem Stromausfall?</span>
                    <span class="werdu-faq-icon">+</span>
                </div>
                <div class="werdu-faq-answer-wrapper">
                    <div class="werdu-faq-answer-inner">
                        <p>Standard-Netzeinspeisesysteme schalten bei einem Stromausfall aus Sicherheitsgründen ab. Verfügt Ihr System über eine Notstrom- oder Ersatzstromfunktion, versorgt die Batterie Ihr Zuhause im Ernstfall automatisch weiter.</p>
                    </div>
                </div>
            </div>
            <div class="werdu-faq-item">
                <div class="werdu-faq-header">
                    <span>Lohnt sich ein Speicher auch im Winter?</span>
                    <span class="werdu-faq-icon">+</span>
                </div>
                <div class="werdu-faq-answer-wrapper">
                    <div class="werdu-faq-answer-inner">
                        <p>Ja. Auch im ertragsärmeren Winter fängt der Speicher kurzzeitige Sonnenphasen ab. Über das gesamte Jahr betrachtet sorgt das Zusammenspiel aus PV-Anlage und Speicher für die höchstmögliche Gesamtrendite Ihrer Investition.</p>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <div class="werdu-highlight-card" style="margin-top:56px;">
            <h3 style="font-size:1.7rem;">Starten Sie jetzt in Ihre energetische Unabhängigkeit</h3>
            <p style="max-width:650px;margin-left:auto;margin-right:auto;">Sichern Sie sich die besten Konditionen für Ihren neuen PV-Speicher. Unsere Fachberater analysieren Ihren Bedarf und erstellen ein unverbindliches, maßgeschneidertes Angebot.</p>
            <a href="___BERATUNG_URL___" class="werdu-btn-primary">Jetzt unverbindliches Angebot anfordern</a>
        </div>

    </section>
</div>

<!-- JSON-LD FAQ Schema for Google AIO Search Features -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Kann ich einen Speicher nachträglich einbauen?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Ja, ein PV-Speicher lässt sich an nahezu jede bestehende Photovoltaikanlage nachrüsten. Je nach vorhandener Technik kommen AC-gekoppelte Systeme oder ein hybrider DC-Wechselrichter zum Einsatz."
      }
    },
    {
      "@type": "Question",
      "name": "Wie lange hält eine moderne Solarbatterie?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Hochwertige LiFePO4-Speicher erreichen eine Lebensdauer von 15 bis 20 Jahren und bewältigen mühelos 6.000 bis 8.000 Ladezyklen."
      }
    },
    {
      "@type": "Question",
      "name": "Funktioniert der Speicher auch bei einem Stromausfall?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Verfügt das System über eine Notstrom- oder Ersatzstromfunktion, versorgt die Batterie das Zuhause bei einem Netzausfall automatisch weiter."
      }
    },
    {
      "@type": "Question",
      "name": "Lohnt sich ein Speicher auch im Winter?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Ja. Auch im Winter fängt der Speicher kurzzeitige Sonnenphasen ab und reduziert so den Zukauf von Netzstrom."
      }
    }
  ]
}
</script>
HTML;

    return str_replace(
        array( '___BERATUNG_URL___', '___RECHNER_URL___' ),
        array( esc_url( $beratung ), esc_url( $rechner ) ),
        $template
    );
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
    if ( ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }

    // Idempotentie: nooit tweemaal injecteren binnen dezelfde request.
    if ( false !== strpos( $content, 'werdu-hero-container' ) ) {
        return $content;
    }

    $hero = werdu_home_seo_hero_html();
    $body = werdu_home_seo_body_html();

    // Bestaande Hero-sectie, productkaarten en calculator blijven volledig
    // ongewijzigd — het Hero-blok wordt er alleen vóór geplaatst.
    $content = $hero . $content;

    // Direct onder de calculator-sectie plaatsen: vlak vóór de eerstvolgende
    // <section class="werdu-seo-section"> die daar al op volgt.
    if ( false !== strpos( $content, WERDU_HOME_CALC_SECTION_MARKER )
        && false !== strpos( $content, WERDU_HOME_AFTER_CALC_MARKER ) ) {
        $content = str_replace(
            WERDU_HOME_AFTER_CALC_MARKER,
            $body . WERDU_HOME_AFTER_CALC_MARKER,
            $content
        );
    } else {
        // Vangnet: als de calculator-markers onverhoopt niet worden gevonden
        // (bv. na een toekomstige Elementor-wijziging), toch niets verliezen
        // en het blok gewoon aan het einde van de content toevoegen.
        $content .= $body;
    }

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
