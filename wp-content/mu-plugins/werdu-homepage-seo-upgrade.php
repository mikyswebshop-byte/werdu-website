<?php
/**
 * Plugin Name: WERDU Homepage SEO & AIO Upgrade
 * Description: Injecteert de Hero H1/intro en het uitgebreide SEO/AIO-contentblok (ToC, vergelijkingstabel, FAQ, JSON-LD) op de homepage — direct onder de Heimspeicher-rechner sectie — met een clean, high-end designsysteem (CSS-variabelen, borderless tabel, subtiele focus-states) zonder de bestaande Elementor-content, calculator-logica of styling te veranderen.
 * Version: 2.0
 * Author: Michael van der Veen
 * Network: false
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
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
  --werdu-orange-hover: #E65C00;
  --werdu-border: #E2E8F0;
  --werdu-radius: 16px;
  --werdu-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05);
  /* Lichte, natuurlijke sfeer (Design System 3.0 — geen donkere thema's) */
  --werdu-sky-top: #EAF5FF;
  --werdu-sky-bottom: #FFFFFF;
  --werdu-glass-bg: rgba(255, 255, 255, 0.68);
  --werdu-glass-border: rgba(255, 255, 255, 0.6);
  --werdu-glass-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.12);
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
  padding: 36px 24px;
  /* Glassmorphism: licht transparant vlak op de hemelsblauwe atmosfeer */
  background: linear-gradient(160deg, rgba(255, 255, 255, 0.82) 0%, rgba(234, 245, 255, 0.55) 100%);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border: 1px solid var(--werdu-glass-border);
  border-radius: var(--werdu-radius);
  box-shadow: var(--werdu-glass-shadow);
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
  background: linear-gradient(160deg, rgba(255, 247, 237, 0.9) 0%, rgba(255, 255, 255, 0.85) 100%);
  border: 1px solid rgba(255, 102, 0, 0.25);
  color: var(--werdu-text);
  padding: 32px;
  border-radius: var(--werdu-radius);
  margin: 36px 0;
  text-align: center;
  box-shadow: var(--werdu-glass-shadow);
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
   DESIGN SYSTEM 3.0 — Fris, licht & premium (geen donkere thema's)
   ============================================
   Extra tokens naast de bestaande --werdu-* variabelen (niets wordt
   hernoemd of verwijderd). De selectors hieronder zijn geverifieerd tegen
   de daadwerkelijke, live gerenderde homepage-DOM (test.werdu.de) — géén
   giswerk en géén ":contains()"-constructies (die zijn ongeldige CSS en
   maken de hele regel onbruikbaar). Tekst-gebaseerde targeting (bv. een
   CTA-link zonder eigen class herkennen op zijn tekst) gebeurt via de
   auto-tag-JS in werdu_home_seo_print_interactions_js(), die daar een
   class aan toevoegt waarop hieronder wordt gestyled. Primaire accentkleur
   is oranje (#FF6600); hemelsblauw wordt uitsluitend decoratief gebruikt
   voor de lichte achtergrond-atmosfeer. */
:root {
  --werdu-primary: #0F172A;
  --werdu-accent: #0284C7;
  --werdu-accent-glow: rgba(2, 132, 199, 0.15);
  --werdu-brand-orange: #FF6600;
  --werdu-brand-orange-dark: #E65C00;
  --werdu-shadow-lg: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
  --werdu-shadow-glow: 0 0 35px rgba(255, 102, 0, 0.18);
}

/* Wechselrichter-vertrouwensbanner (echte class: .werdu-compat-marquee) —
   lichte glass-strip i.p.v. het vorige donkere thema, met glazen
   pill-badges per merk die oranje oplichten bij hover. */
.werdu-compat-marquee {
  background: linear-gradient(135deg, rgba(234, 245, 255, 0.85) 0%, rgba(255, 255, 255, 0.92) 100%) !important;
  border: 1px solid var(--werdu-glass-border) !important;
  backdrop-filter: blur(12px) !important;
  -webkit-backdrop-filter: blur(12px) !important;
  border-radius: 20px !important;
  box-shadow: var(--werdu-glass-shadow) !important;
}

.werdu-compat-brand {
  background: rgba(255, 255, 255, 0.75);
  border: 1px solid rgba(15, 23, 42, 0.08);
  color: var(--werdu-text);
  font-weight: 600;
  border-radius: 30px;
  padding: 6px 16px;
  backdrop-filter: blur(8px);
  transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease, color 0.3s ease;
  display: inline-block;
}

.werdu-compat-brand:hover {
  background: var(--werdu-brand-orange);
  border-color: var(--werdu-brand-orange);
  color: #FFFFFF;
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(255, 102, 0, 0.35);
}

.werdu-compat-sep {
  color: rgba(15, 23, 42, 0.25);
}

/* Autarkie-Rechner velden (echte classes: .werdu-calc-container met
   .werdu-calc-input / .werdu-calc-select) — lichte achtergrond, wordt
   bij focus overschreven door de oranje glow verderop in dit bestand. */
.werdu-calc-container input[type="text"],
.werdu-calc-container input[type="number"],
.werdu-calc-container input[type="email"],
.werdu-calc-container select {
  background: #F8FAFC;
  border: 1.5px solid #CBD5E1;
  border-radius: 10px;
  transition: border-color 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}

/* Rekenmodule-container krijgt een glazen kaart + hover-lift, net als de
   product- en FAQ-kaarten (consistente 3D glassmorphism-taal). */
.werdu-calc-container {
  background: var(--werdu-glass-bg) !important;
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border: 1px solid var(--werdu-glass-border) !important;
  border-radius: var(--werdu-radius) !important;
  box-shadow: var(--werdu-glass-shadow) !important;
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.werdu-calc-container:hover,
.werdu-calc-container:focus-within {
  transform: translateY(-4px);
  box-shadow: 0 28px 48px -14px rgba(15, 23, 42, 0.16), var(--werdu-shadow-glow) !important;
}

/* Bestseller-productkaarten (echte class: .werdu-product-card) — witte
   glass-kaart met zachte hover-lift, schaduw en oranje glow op de rand. */
.werdu-product-card {
  background: var(--werdu-glass-bg);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid var(--werdu-glass-border);
  border-radius: 16px;
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease;
}

.werdu-product-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--werdu-shadow-lg), 0 0 0 3px rgba(255, 102, 0, 0.12) !important;
  border-color: var(--werdu-brand-orange);
}

/* High-conversion CTA-gradient in de primaire accentkleur (#FF6600) — op
   de bevestigde CTA-classes én op elke link die de auto-tag-JS als CTA
   herkent (.werdu-cta-auto). Inclusief periodiek shimmer/glans-effect. */
.werdu-calc-cta,
.werdu-seo-cta,
.btn-3d,
.werdu-cta-auto {
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, var(--werdu-brand-orange) 0%, var(--werdu-brand-orange-dark) 100%) !important;
  color: #FFFFFF !important;
  font-weight: 700 !important;
  border-radius: 12px !important;
  box-shadow: 0 10px 20px -5px rgba(255, 102, 0, 0.4) !important;
  transition: transform 0.3s ease, box-shadow 0.3s ease, filter 0.3s ease !important;
}

.werdu-calc-cta::after,
.werdu-seo-cta::after,
.btn-3d::after,
.werdu-cta-auto::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(115deg, transparent 20%, rgba(255, 255, 255, 0.55) 50%, transparent 80%);
  transform: translateX(-120%);
  animation: werdu-cta-shimmer 3.4s ease-in-out infinite;
  pointer-events: none;
}

@keyframes werdu-cta-shimmer {
  0%, 45% { transform: translateX(-120%); }
  65%, 100% { transform: translateX(120%); }
}

.werdu-calc-cta:hover,
.werdu-seo-cta:hover,
.btn-3d:hover,
.werdu-cta-auto:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 25px -5px rgba(255, 102, 0, 0.5) !important;
  filter: brightness(1.05);
}

/* Bestaande (native) FAQ-blok "Wie lange hält…" deelt de .werdu-faq-item
   class met ons eigen accordion-blok verderop op de pagina. Dit voegt
   glassmorphism + hover-lift toe aan beide varianten, ongeacht of het de
   platte h3/p-variant of de eigen accordion-structuur betreft. */
.werdu-faq-item {
  background: var(--werdu-glass-bg);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease;
}

.werdu-faq-item:hover {
  transform: translateY(-4px);
  box-shadow: var(--werdu-glass-shadow);
  border-color: rgba(255, 102, 0, 0.35);
}

.werdu-faq-item > .werdu-faq-question,
.werdu-faq-item > .werdu-faq-answer {
  padding-left: 4px;
  padding-right: 4px;
}

/* Reel: Scroll & Reveal — kaarten/secties vliegen vloeiend in bij scroll.
   .werdu-reveal wordt alleen door JS toegevoegd (progressive enhancement:
   zonder JS blijft alles gewoon zichtbaar), .is-visible triggert de
   IntersectionObserver in werdu_home_seo_print_interactions_js(). */
.werdu-reveal {
  opacity: 0;
  transform: translateY(28px);
  transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.werdu-reveal.is-visible {
  opacity: 1;
  transform: translateY(0);
}

@media (prefers-reduced-motion: reduce) {
  .werdu-compat-brand,
  .werdu-product-card,
  .werdu-calc-container,
  .werdu-faq-item,
  .werdu-calc-cta,
  .werdu-seo-cta,
  .btn-3d,
  .werdu-cta-auto,
  .werdu-reveal {
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
  .werdu-calc-cta::after,
  .werdu-seo-cta::after,
  .btn-3d::after,
  .werdu-cta-auto::after {
    animation: none !important;
    display: none !important;
  }
  .werdu-reveal {
    opacity: 1;
    transform: none;
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

  // ---- Design System 3.0: Scroll & Reveal (fade-in-slide-up) ----
  // Progressive enhancement: .werdu-reveal wordt hier pas toegevoegd, dus
  // zonder JS (of bij falende IntersectionObserver-support) blijft alles
  // gewoon standaard zichtbaar. Bij prefers-reduced-motion slaan we het
  // hele effect over (CSS toont de elementen dan sowieso direct).
  function bindScrollReveal() {
    if ( prefersReducedMotion || ! ( 'IntersectionObserver' in window ) ) {
      return;
    }

    var targets = document.querySelectorAll(
      '.werdu-product-card, .werdu-faq-item, .werdu-highlight-card, .werdu-card-soft, .werdu-toc-box, .werdu-table-wrapper, .werdu-variant-card'
    );
    if ( ! targets.length ) {
      return;
    }

    var observer = new IntersectionObserver( function ( entries, obs ) {
      entries.forEach( function ( entry ) {
        if ( entry.isIntersecting ) {
          entry.target.classList.add( 'is-visible' );
          obs.unobserve( entry.target );
        }
      } );
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' } );

    targets.forEach( function ( el, i ) {
      if ( el.dataset.werduRevealBound === '1' ) {
        return;
      }
      el.dataset.werduRevealBound = '1';
      el.classList.add( 'werdu-reveal' );
      el.style.transitionDelay = Math.min( i % 4, 3 ) * 0.08 + 's';
      observer.observe( el );
    } );
  }

  function init() {
    bindFaqAccordion();
    bindCtaButtons();
    bindCalcButton();
    bindCtaAutoTag();
    bindCalcInputMicroInteractions();
    bindScrollReveal();
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
    return <<<'HTML'
<div class="werdu-seo-container" style="padding-bottom:0;">
    <div class="werdu-hero-intro">
        <h1>PV-Speicher für Ihr Zuhause: Autarkie-Rechner &amp; Testsieger 2026</h1>
        <p>Sie möchten Ihre Stromkosten spürbar senken und sich unabhängiger von steigenden Netzpreisen machen? Ein moderner PV-Speicher speichert Ihren selbst erzeugten Solarstrom und stellt ihn genau dann bereit, wenn Sie ihn wirklich brauchen – auch abends und nachts. Nutzen Sie unseren kostenlosen Autarkie-Rechner, um die passende Kapazität für Ihr Zuhause zu ermitteln, und sichern Sie sich anschließend Ihr individuelles Angebot.</p>
    </div>
</div>
HTML;
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

        <div class="werdu-table-wrapper">
            <table class="werdu-table">
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
    if ( false !== strpos( $content, 'werdu-hero-intro' ) ) {
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
