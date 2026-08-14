<?php
/**
 * Template Name: Solarbatterie Rechner v3
 * Template Post Type: page
 * Werdu.de Solarbatterie-Rechner v3 — Standalone Embed-Version
 *
 * Deze calculator is een standalone pagina die:
 * 1. GEEN CF7 formulier toont (linkt naar /beratung-anfragen/)
 * 2. Embed code toont voor installateurs (witte sectie)
 * 3. SessionStorage voor back-button herstel
 */

get_header();

if (!defined('ABSPATH')) {
    exit('Direkter Zugriff nicht erlaubt.');
}
?>

<style>
/* ============================================
   WERDU SOLARBATTERIE-RECHNER v3
   Standalone Embed Calculator — Inline CSS
   ============================================ */

/* --- RESET & BASIS --- */
.wr5, .wr5 * { box-sizing: border-box; }
.wr5 {
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  color: #1a1a2e;
  line-height: 1.6;
  background: #FAFBFF;
  overflow-x: hidden;
  width: 100%;
  max-width: 100%;
}

/* --- HERO --- */
.wr5-hero {
  background: #FAFBFF;
  padding: 80px 24px 100px;
  text-align: center;
  position: relative;
  width: 100%;
  max-width: 100%;
  overflow: hidden;
}
.wr5-hero-title {
  font-size: clamp(2.5rem, 6vw, 4.5rem);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.1;
  margin-bottom: 16px;
  background: radial-gradient(circle at 50% 50%, #FF6600, #0099FF);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  transition: background 0.05s ease;
}
.wr5-hero-sub {
  font-size: clamp(1rem, 2vw, 1.25rem);
  color: #4a4a6a;
  max-width: 600px;
  margin: 0 auto 32px;
  opacity: 0;
  animation: wr5FadeIn 0.6s ease 0.3s forwards;
}
@keyframes wr5FadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.wr5-hero-badges {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 12px;
  margin-bottom: 40px;
}
.wr5-badge {
  padding: 10px 20px;
  border-radius: 100px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  opacity: 0;
  animation: wr5FadeIn 0.5s ease forwards;
}
.wr5-badge:nth-child(1) { background: #FFF0E0; color: #FF6600; border: 1.5px solid #FF6600; animation-delay: 0.5s; }
.wr5-badge:nth-child(2) { background: #E0F4FF; color: #0077cc; border: 1.5px solid #0099FF; animation-delay: 0.65s; }
.wr5-badge:nth-child(3) { background: #E8FFE5; color: #1a9e00; border: 1.5px solid #1AFF00; animation-delay: 0.8s; }
.wr5-badge:nth-child(4) { background: #FFF8F5; color: #FF6600; border: 1.5px solid #FF6600; animation-delay: 0.95s; }
.wr5-hero-wave {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  line-height: 0;
}
.wr5-hero-wave svg {
  display: block;
  width: 100%;
  height: 60px;
}

/* --- PROGRESS STEPS --- */
.wr5-progress {
  background: #fff;
  border-bottom: 1px solid #e8e8f0;
  position: sticky;
  top: 0;
  z-index: 100;
  width: 100%;
  box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.wr5-progress-inner {
  max-width: 900px;
  margin: 0 auto;
  display: flex;
  padding: 0 16px;
  position: relative;
}
.wr5-step-connector {
  position: absolute;
  top: 32px;
  left: 12.5%;
  right: 12.5%;
  height: 2px;
  background: #e8e8f0;
  z-index: 0;
}
.wr5-step-connector-fill {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, #FF6600, #0099FF);
  transition: width 0.5s ease;
}
.wr5-step-tab {
  flex: 1;
  text-align: center;
  padding: 16px 4px 18px;
  cursor: pointer;
  position: relative;
  z-index: 1;
  transition: all 0.3s ease;
  border: none;
  background: none;
  font-family: inherit;
}
.wr5-step-tab .num {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid #d0d0e0;
  color: #8a8aaa;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 14px;
  margin-bottom: 8px;
  transition: all 0.3s ease;
}
.wr5-step-tab .label {
  font-size: 13px;
  font-weight: 700;
  color: #8a8aaa;
  display: block;
  transition: all 0.3s ease;
}
.wr5-step-tab:hover .num { transform: scale(1.1); }
.wr5-step-tab:hover .label { color: #4a4a6a; }

.wr5-step-tab[data-step="1"].active .num,
.wr5-step-tab[data-step="1"].done .num { border-color: #FF6600; background: linear-gradient(135deg, #FF6600, #ff8533); color: #fff; box-shadow: 0 4px 16px rgba(255,102,0,0.3); }
.wr5-step-tab[data-step="2"].active .num,
.wr5-step-tab[data-step="2"].done .num { border-color: #0099FF; background: linear-gradient(135deg, #0099FF, #33adff); color: #fff; box-shadow: 0 4px 16px rgba(0,153,255,0.3); }
.wr5-step-tab[data-step="3"].active .num,
.wr5-step-tab[data-step="3"].done .num { border-color: #1AFF00; background: linear-gradient(135deg, #1AFF00, #4dff33); color: #1a1a2e; box-shadow: 0 4px 16px rgba(26,255,0,0.3); }
.wr5-step-tab[data-step="4"].active .num,
.wr5-step-tab[data-step="4"].done .num { border-color: #FF6600; background: linear-gradient(135deg, #FF6600, #0099FF); color: #fff; box-shadow: 0 4px 16px rgba(255,102,0,0.3); }

.wr5-step-tab.active .label { color: #1a1a2e; }
.wr5-step-tab.done .label { color: #4a4a6a; }

/* --- CONTAINER --- */
.wr5-container {
  max-width: 900px;
  margin: 0 auto;
  padding: 48px 24px;
  width: 100%;
}

/* --- SECTION BACKGROUNDS --- */
.wr5-sect-1 { background: #FFF8F5; }
.wr5-sect-2 { background: #F0F8FF; }
.wr5-sect-3 { background: #F5FFFA; }
.wr5-sect-4 { background: #FAFBFF; }

/* --- PANELS --- */
.wr5-panel { display: none; width: 100%; }
.wr5-panel.active { display: block; animation: wr5PanelIn 0.5s ease; }
@keyframes wr5PanelIn {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.wr5-panel-hd { margin-bottom: 40px; text-align: center; }
.wr5-panel-hd h2 {
  font-size: clamp(1.5rem, 3vw, 2.25rem);
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 8px;
  letter-spacing: -0.02em;
}
.wr5-panel-hd p { color: #4a4a6a; font-size: 1rem; }

/* --- FORM --- */
.wr5-form {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
  margin-bottom: 32px;
  width: 100%;
}
@media (max-width: 640px) { .wr5-form { grid-template-columns: 1fr; } }

.wr5-field { position: relative; width: 100%; }
.wr5-field > label {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  transform-origin: left center;
  font-size: 16px;
  font-weight: 600;
  color: #475569;
  pointer-events: none;
  background: #fff;
  padding: 0 4px;
  z-index: 2;
  margin: 0;
  text-transform: none;
  letter-spacing: 0;
  transition: transform 0.2s ease, color 0.2s ease, top 0.2s ease;
}
.wr5-field:focus-within > label,
.wr5-field:has(input:not(:placeholder-shown)) > label,
.wr5-field:has(select) > label {
  top: 0;
  transform: translateY(-50%) scale(0.85);
  color: #c2410c;
}
.wr5-field input,
.wr5-field select {
  width: 100%;
  padding: 18px 14px 8px 14px;
  border: 2px solid #e0e0f0;
  border-radius: 12px;
  font-size: 16px;
  color: #1a1a2e;
  background: #fff;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  font-family: inherit;
}
.wr5-field input::placeholder { color: transparent; }
.wr5-field input:hover, .wr5-field select:hover { border-color: #c0c0d0; }
.wr5-field input:focus, .wr5-field select:focus { outline: none; }
.wr5-field small {
  display: block;
  margin-top: 6px;
  font-size: 12px;
  color: #8a8aaa;
}

.wr5-sect-1 .wr5-field input:focus, .wr5-sect-1 .wr5-field select:focus { border-color: #FF6600; box-shadow: 0 0 0 4px rgba(255,102,0,0.1); }
.wr5-sect-2 .wr5-field input:focus, .wr5-sect-2 .wr5-field select:focus { border-color: #0099FF; box-shadow: 0 0 0 4px rgba(0,153,255,0.1); }
.wr5-sect-3 .wr5-field input:focus, .wr5-sect-3 .wr5-field select:focus { border-color: #1AFF00; box-shadow: 0 0 0 4px rgba(26,255,0,0.1); }

/* --- OPTION CARDS --- */
.wr5-opts { margin-bottom: 28px; width: 100%; }
.wr5-opts h3 {
  font-size: 13px;
  font-weight: 700;
  color: #4a4a6a;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-bottom: 16px;
}
.wr5-opts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 12px;
  width: 100%;
}
.wr5-opt {
  background: #fff;
  border: 2px solid #e0e0f0;
  border-radius: 12px;
  padding: 18px 14px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
}
.wr5-opt:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
.wr5-opt input { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
.wr5-opt .t { font-size: 14px; font-weight: 700; color: #1a1a2e; display: block; }
.wr5-opt .s { font-size: 12px; color: #8a8aaa; display: block; margin-top: 4px; }

.wr5-sect-1 .wr5-opt:hover { border-color: #FF6600; }
.wr5-sect-2 .wr5-opt:hover { border-color: #0099FF; }
.wr5-sect-3 .wr5-opt:hover { border-color: #1AFF00; }

.wr5-sect-1 .wr5-opt.selected { border-color: #FF6600; box-shadow: 0 0 0 4px rgba(255,102,0,0.1); background: #FFF8F5; transform: scale(1.02); }
.wr5-sect-2 .wr5-opt.selected { border-color: #0099FF; box-shadow: 0 0 0 4px rgba(0,153,255,0.1); background: #F0F8FF; transform: scale(1.02); }
.wr5-sect-3 .wr5-opt.selected { border-color: #1AFF00; box-shadow: 0 0 0 4px rgba(26,255,0,0.1); background: #F5FFFA; transform: scale(1.02); }

.wr5-sect-1 .wr5-opt.selected .t { color: #FF6600; }
.wr5-sect-2 .wr5-opt.selected .t { color: #0099FF; }
.wr5-sect-3 .wr5-opt.selected .t { color: #1a9e00; }

.wr5-opt::after {
  content: '';
  position: absolute;
  top: 10px;
  right: 10px;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 2px solid #e0e0f0;
  background: #fff;
  transition: all 0.2s ease;
}
.wr5-opt.selected::after { border-width: 5px; transform: scale(1.1); }
.wr5-sect-1 .wr5-opt.selected::after { border-color: #FF6600; }
.wr5-sect-2 .wr5-opt.selected::after { border-color: #0099FF; }
.wr5-sect-3 .wr5-opt.selected::after { border-color: #1AFF00; }

/* --- SLIDER --- */
.wr5-slider-box {
  background: #fff;
  border: 2px solid #e0e0f0;
  border-radius: 12px;
  padding: 28px;
  margin-bottom: 28px;
  width: 100%;
}
.wr5-slider-box label {
  display: block;
  font-size: 14px;
  font-weight: 700;
  color: #4a4a6a;
  margin-bottom: 20px;
}
.wr5-slider-box label strong {
  color: #FF6600;
  font-size: 22px;
  margin-left: 8px;
}
.wr5-slider {
  width: 100%;
  height: 8px;
  border-radius: 4px;
  background: #e0e0f0;
  outline: none;
  -webkit-appearance: none;
  cursor: pointer;
  position: relative;
}
.wr5-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: linear-gradient(135deg, #FF6600, #0099FF);
  cursor: pointer;
  border: 3px solid #fff;
  box-shadow: 0 2px 12px rgba(0,153,255,0.4);
  transition: all 0.2s ease;
}
.wr5-slider::-webkit-slider-thumb:hover { transform: scale(1.2); }
.wr5-slider::-moz-range-thumb {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: linear-gradient(135deg, #FF6600, #0099FF);
  cursor: pointer;
  border: 3px solid #fff;
  box-shadow: 0 2px 12px rgba(0,153,255,0.4);
}
.wr5-slider-labels {
  display: flex;
  justify-content: space-between;
  margin-top: 12px;
  font-size: 12px;
  color: #8a8aaa;
  font-weight: 700;
}

/* --- BUTTONS --- */
.wr5-btns {
  display: flex;
  gap: 16px;
  justify-content: center;
  margin-top: 40px;
  flex-wrap: wrap;
  width: 100%;
}
.wr5-btn {
  padding: 16px 36px;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-family: inherit;
}
.wr5-btn-prim {
  background: radial-gradient(circle at 50% 50%, #FF6600, #0099FF);
  color: #fff;
  box-shadow: 0 4px 20px rgba(255,102,0,0.25);
  transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.05s ease;
}
.wr5-btn-prim:hover {
  transform: scale(1.02);
  box-shadow: 0 8px 32px rgba(0,153,255,0.35);
}
.wr5-btn-prim:active { transform: scale(0.98); }
.wr5-btn-sec {
  background: #fff;
  color: #4a4a6a;
  border: 2px solid #e0e0f0;
}
.wr5-btn-sec:hover {
  border-color: #FF6600;
  color: #FF6600;
  background: #FFF8F5;
}

.wr5-btn-prim.loading { pointer-events: none; opacity: 0.9; }
.wr5-btn-prim .wr5-spinner {
  display: none;
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: wr5Spin 0.8s linear infinite;
}
.wr5-btn-prim.loading .wr5-spinner { display: inline-block; }
@keyframes wr5Spin { to { transform: rotate(360deg); } }

/* --- RESULT SECTION --- */
.wr5-result { display: none; width: 100%; }
.wr5-result.active { display: block; animation: wr5PanelIn 0.6s ease; }

/* FIX: Witte tekst in gekleurde vlakken */
.wr5-res-hero {
  background: linear-gradient(135deg, #FF6600 0%, #0099FF 100%);
  color: #fff;
  padding: 56px 24px;
  text-align: center;
  border-radius: 16px;
  margin-bottom: 40px;
  width: 100%;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0,153,255,0.2);
}
.wr5-res-hero h2 {
  font-size: clamp(1.5rem, 3vw, 2.25rem);
  font-weight: 800;
  margin-bottom: 8px;
  position: relative;
  z-index: 1;
  color: #fff !important;
}
.wr5-res-hero p {
  color: rgba(255,255,255,0.9) !important;
  font-size: 16px;
  position: relative;
  z-index: 1;
}

/* KPI Cards */
.wr5-res-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 40px;
  width: 100%;
}
@media (max-width: 768px) { .wr5-res-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 400px) { .wr5-res-grid { grid-template-columns: 1fr; } }

.wr5-res-card {
  background: #fff;
  padding: 32px 20px;
  border-radius: 14px;
  text-align: center;
  border: 2px solid transparent;
  transition: all 0.4s ease;
  opacity: 0;
  transform: translateY(20px);
}
.wr5-res-card.visible { opacity: 1; transform: translateY(0); }
.wr5-res-card:nth-child(1) { background: #FFF8F5; border-color: #FF6600; transition-delay: 0.1s; }
.wr5-res-card:nth-child(2) { background: #E0F4FF; border-color: #0099FF; transition-delay: 0.2s; }
.wr5-res-card:nth-child(3) { background: #E8FFE5; border-color: #1AFF00; transition-delay: 0.3s; }
.wr5-res-card:nth-child(4) { background: #FFF8F5; border-color: #FF6600; transition-delay: 0.4s; }

.wr5-res-card .v {
  font-size: clamp(1.5rem, 2.5vw, 2rem);
  font-weight: 800;
  line-height: 1.2;
  margin-bottom: 8px;
}
.wr5-res-card:nth-child(1) .v { color: #FF6600; }
.wr5-res-card:nth-child(2) .v { color: #0077cc; }
.wr5-res-card:nth-child(3) .v { color: #1a9e00; }
.wr5-res-card:nth-child(4) .v { color: #FF6600; }

.wr5-res-card .l {
  font-size: 11px;
  color: #4a4a6a;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

/* SVG Circle Charts */
.wr5-svg-chart {
  width: 100px;
  height: 100px;
  margin: 0 auto 12px;
  transform: rotate(-90deg);
}
.wr5-svg-chart circle {
  fill: none;
  stroke-width: 8;
  stroke-linecap: round;
}
.wr5-svg-chart .bg { stroke: rgba(0,0,0,0.05); }
.wr5-svg-chart .fg { stroke-dasharray: 283; stroke-dashoffset: 283; transition: stroke-dashoffset 1.5s ease-out; }

/* Bar Chart */
.wr5-chart {
  background: #fff;
  border: 2px solid #e0e0f0;
  border-radius: 14px;
  padding: 32px;
  margin-bottom: 32px;
  width: 100%;
}
.wr5-chart h3 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 28px;
}
.wr5-chart-bars {
  display: flex;
  align-items: flex-end;
  gap: 8px;
  height: 280px;
  min-height: 240px;
  padding-bottom: 50px;
  position: relative;
  width: 100%;
}
.wr5-chart-bar {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  min-width: 0;
}
.wr5-chart-bar .pv {
  width: 100%;
  background: linear-gradient(to top, #FF6600, #ff8533);
  border-radius: 4px 4px 0 0;
  min-height: 2px;
  transition: height 0.8s cubic-bezier(0.4,0,0.2,1);
}
.wr5-chart-bar .vb {
  width: 100%;
  background: #e0e0f0;
  border-radius: 4px 4px 0 0;
  min-height: 2px;
  margin-top: 3px;
  transition: height 0.8s cubic-bezier(0.4,0,0.2,1);
}
.wr5-chart-bar .lb {
  position: absolute;
  bottom: -28px;
  font-size: 11px;
  color: #8a8aaa;
  font-weight: 700;
  transform: rotate(-40deg);
  transform-origin: left center;
  white-space: nowrap;
}
.wr5-chart-leg {
  display: flex;
  gap: 28px;
  justify-content: center;
  margin-top: 56px;
  font-size: 13px;
  font-weight: 700;
  color: #4a4a6a;
  flex-wrap: wrap;
}
.wr5-chart-leg span { display: flex; align-items: center; gap: 8px; }
.wr5-chart-leg .dot { width: 12px; height: 12px; border-radius: 3px; }

/* Savings Detail */
.wr5-save {
  background: #fff;
  border: 2px solid #e0e0f0;
  border-radius: 14px;
  padding: 32px;
  margin-bottom: 32px;
  width: 100%;
}
.wr5-save h3 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 24px;
}
.wr5-save-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  width: 100%;
}
@media (max-width: 600px) { .wr5-save-grid { grid-template-columns: 1fr; } }
.wr5-save-item {
  text-align: center;
  padding: 24px;
  border: 2px solid #f0f0f5;
  border-radius: 12px;
  transition: all 0.3s ease;
}
.wr5-save-item:hover {
  border-color: #FF6600;
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(0,0,0,0.05);
}
.wr5-save-item .n { font-size: 28px; font-weight: 800; color: #FF6600; }
.wr5-save-item .t { font-size: 13px; color: #4a4a6a; margin-top: 6px; font-weight: 700; }

/* Price Development Table */
.wr5-price-table {
  background: #fff;
  border: 2px solid #e0e0f0;
  border-radius: 14px;
  padding: 32px;
  margin-bottom: 32px;
  width: 100%;
  max-width: 100%;
  overflow-x: auto;
}
.wr5-price-table h3 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 24px;
}
.wr5-price-table table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
  min-width: 480px;
}
.wr5-price-table thead tr {
  background: linear-gradient(135deg, #FF6600, #0099FF);
  color: #fff;
}
.wr5-price-table th { padding: 16px; text-align: left; font-weight: 700; }
.wr5-price-table td { padding: 14px 16px; border-bottom: 1px solid #f0f0f5; color: #4a4a6a; }
.wr5-price-table tr:nth-child(even) { background: #FAFBFF; }
.wr5-price-table tr:hover { background: #FFF8F5; }
.wr5-price-table .amort-row { background: #FFF8F5 !important; font-weight: 700; color: #FF6600; }
.wr5-price-table .amort-row td { border-bottom: 2px solid #FF6600; }

/* Product Card */
.wr5-prod {
  background: #fff;
  border: 2px solid #e0e0f0;
  border-radius: 14px;
  padding: 40px;
  margin-bottom: 32px;
  display: grid;
  grid-template-columns: 220px 1fr;
  gap: 32px;
  align-items: center;
  width: 100%;
  transition: all 0.3s ease;
  position: relative;
  perspective: 1000px;
}
.wr5-prod:hover {
  border-color: #FF6600;
  box-shadow: 0 12px 32px rgba(0,0,0,0.08);
}
@media (max-width: 640px) { .wr5-prod { grid-template-columns: 1fr; text-align: center; } }
.wr5-prod-badge {
  position: absolute;
  top: -12px;
  left: 24px;
  background: linear-gradient(135deg, #FF6600, #0099FF);
  color: #fff;
  padding: 6px 16px;
  border-radius: 100px;
  font-size: 12px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  box-shadow: 0 4px 12px rgba(255,102,0,0.3);
}
.wr5-prod-img-wrap {
  background: #FAFBFF;
  border-radius: 12px;
  padding: 24px;
  text-align: center;
  border: 1px solid #e0e0f0;
  overflow: hidden;
  transition: transform 0.3s ease;
  transform-style: preserve-3d;
}
.wr5-prod-img-wrap img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  transition: transform 0.3s ease;
  display: block;
  margin: 0 auto;
}
.wr5-prod:hover .wr5-prod-img-wrap {
  transform: rotateY(5deg) rotateX(5deg) scale(1.02);
}
.wr5-prod-info h3 { font-size: 24px; font-weight: 800; color: #1a1a2e; margin-bottom: 8px; }
.wr5-prod-info .pr { font-size: 42px; font-weight: 800; color: #FF6600; margin-bottom: 4px; }
.wr5-prod-info .pr .uvp { font-size: 16px; color: #8a8aaa; text-decoration: line-through; margin-left: 12px; font-weight: 600; }
.wr5-prod-info .pr small { font-size: 14px; color: #8a8aaa; font-weight: 500; display: block; margin-top: 4px; }
.wr5-prod-info ul { list-style: none; margin: 16px 0; padding: 0; }
.wr5-prod-info li { padding: 6px 0; font-size: 14px; color: #4a4a6a; display: flex; align-items: center; gap: 10px; }
.wr5-prod-info li::before {
  content: '\2714';
  display: inline-flex;
  width: 18px;
  height: 18px;
  background: linear-gradient(135deg, #1AFF00, #0099FF);
  border-radius: 50%;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 10px;
}
.wr5-prod-cta {
  display: inline-block;
  padding: 16px 40px;
  background: #FF6600;
  color: #fff;
  text-decoration: none;
  border-radius: 12px;
  font-weight: 800;
  font-size: 15px;
  margin-top: 16px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 20px rgba(255,102,0,0.25);
}
.wr5-prod-cta:hover {
  background: #0099FF;
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(0,153,255,0.3);
}

/* CO2 Section */
.wr5-co2 {
  background: #E8FFE5;
  border: 2px solid #1AFF00;
  border-radius: 14px;
  padding: 32px;
  margin-bottom: 32px;
  width: 100%;
}
.wr5-co2 h3 { font-size: 18px; font-weight: 700; color: #1a1a2e; margin-bottom: 24px; }
.wr5-co2-grid {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 28px;
  align-items: center;
  width: 100%;
}
@media (max-width: 640px) { .wr5-co2-grid { grid-template-columns: 1fr; } }
.wr5-co2-big { text-align: center; }
.wr5-co2-big .n { font-size: 56px; font-weight: 800; color: #FF6600; line-height: 1; }
.wr5-co2-big .t { font-size: 14px; color: #4a4a6a; font-weight: 700; margin-top: 8px; }
.wr5-co2-items { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
@media (max-width: 480px) { .wr5-co2-items { grid-template-columns: 1fr; } }
.wr5-co2-item {
  background: rgba(255,255,255,0.9);
  padding: 20px;
  border-radius: 12px;
  text-align: center;
  border: 1px solid rgba(26,255,0,0.3);
  transition: all 0.3s ease;
}
.wr5-co2-item:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(26,255,0,0.1); }
.wr5-co2-item .n { font-size: 22px; font-weight: 800; color: #FF6600; }
.wr5-co2-item .t { font-size: 12px; color: #4a4a6a; margin-top: 4px; font-weight: 700; }

/* Comparison Table */
.wr5-compare {
  background: #fff;
  border: 2px solid #e0e0f0;
  border-radius: 14px;
  padding: 32px;
  margin-bottom: 32px;
  width: 100%;
  max-width: 100%;
  overflow-x: auto;
}
.wr5-compare h3 { font-size: 18px; font-weight: 700; color: #1a1a2e; margin-bottom: 24px; }
.wr5-compare table { width: 100%; border-collapse: collapse; font-size: 14px; min-width: 480px; }
.wr5-compare thead tr {
  background: linear-gradient(135deg, #FF6600, #0099FF);
  color: #fff;
}
.wr5-compare th { padding: 16px; text-align: left; font-weight: 700; }
.wr5-compare td { padding: 14px 16px; border-bottom: 1px solid #f0f0f5; color: #4a4a6a; }
.wr5-compare tr:nth-child(even) { background: #FAFBFF; }
.wr5-compare tr:hover { background: #FFF8F5; }
.wr5-compare .win { background: #FFF8F5 !important; font-weight: 700; color: #FF6600; }

/* FIX: CTA Section — witte tekst gegarandeerd */
.wr5-cta {
  background: linear-gradient(135deg, #FF6600, #0099FF);
  color: #fff;
  padding: 56px 24px;
  border-radius: 16px;
  text-align: center;
  margin-bottom: 32px;
  width: 100%;
  position: relative;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0,153,255,0.2);
}
.wr5-cta h3 {
  font-size: 28px;
  font-weight: 800;
  margin-bottom: 12px;
  position: relative;
  z-index: 1;
  color: #fff !important;
}
.wr5-cta p {
  color: rgba(255,255,255,0.9) !important;
  font-size: 17px;
  margin-bottom: 28px;
  position: relative;
  z-index: 1;
}
.wr5-cta .btn {
  display: inline-block;
  padding: 18px 48px;
  background: #fff;
  color: #FF6600;
  text-decoration: none;
  border-radius: 12px;
  font-weight: 800;
  font-size: 16px;
  transition: all 0.3s ease;
  position: relative;
  z-index: 1;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.wr5-cta .btn:hover {
  background: linear-gradient(135deg, #FF6600, #0099FF);
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(0,0,0,0.2);
}

/* Disclaimer */
.wr5-disc {
  background: #FFF8F5;
  border: 2px solid #FF6600;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 24px;
  width: 100%;
}
.wr5-disc p { font-size: 13px; color: #4a4a6a; line-height: 1.7; }
.wr5-disc p strong { color: #FF6600; }

/* Footer */
.wr5-footer { text-align: center; padding: 32px 24px; color: #8a8aaa; font-size: 13px; width: 100%; }
.wr5-footer a { color: #FF6600; text-decoration: none; }

/* --- SCROLL ANIMATIONS --- */
.wr5-animate {
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.6s ease, transform 0.6s ease;
}
.wr5-animate.visible {
  opacity: 1;
  transform: translateY(0);
}

/* --- REDUCED MOTION --- */
@media (prefers-reduced-motion: reduce) {
  .wr5-hero-title, .wr5-btn-prim, .wr5-res-card, .wr5-prod-badge,
  .wr5-panel, .wr5-result, .wr5-animate,
  .wr5-field > label, .wr5-field input, .wr5-field select {
    animation: none !important;
    transition: none !important;
  }
  .wr5-svg-chart .fg { transition: none !important; }
}

/* --- MOBILE --- */
@media (max-width: 768px) {
  .wr5-hero { padding: 60px 16px 80px; }
  .wr5-container { padding: 32px 16px; }
  .wr5-res-hero { padding: 40px 16px; }
  .wr5-cta { padding: 40px 16px; }
  .wr5-prod { padding: 28px 20px; }
  .wr5-chart { padding: 24px 16px; }
  .wr5-animate { opacity: 1; transform: none; }
  .wr5-prod:hover .wr5-prod-img-wrap { transform: none; }
}

/* --- PRINT --- */
@media print {
  .wr5-progress, .wr5-btns, .wr5-hero-wave { display: none !important; }
  .wr5-panel, .wr5-result { display: block !important; }
  .wr5-hero { padding: 20px; }
  .wr5-res-hero { background: #fff !important; color: #1a1a2e !important; border: 2px solid #e0e0f0; }
  .wr5-res-hero h2, .wr5-res-hero p { color: #1a1a2e !important; }
  .wr5-cta { background: #fff !important; color: #1a1a2e !important; border: 2px solid #e0e0f0; }
  .wr5-cta h3, .wr5-cta p { color: #1a1a2e !important; }
  .wr5-cta .btn { background: #FF6600 !important; color: #fff !important; }
}

/* ============================================
   FIX: EMBED / PARTNER SECTION — WIT ipv ZWART
   ============================================ */
.wr5-embed-section {
  background: #fff;
  color: #1a1a2e;
  padding: 64px 24px;
  text-align: center;
  margin-top: 40px;
  border-top: 2px solid #e0e0f0;
}
.wr5-embed-section h3 {
  font-size: 26px;
  font-weight: 800;
  margin-bottom: 16px;
  color: #1a1a2e;
}
.wr5-embed-section > p {
  color: #4a4a6a;
  max-width: 600px;
  margin: 0 auto 32px;
  font-size: 16px;
  line-height: 1.6;
}
.wr5-embed-features {
  display: flex;
  gap: 16px;
  justify-content: center;
  flex-wrap: wrap;
  margin-bottom: 32px;
}
.wr5-embed-feature {
  background: #FAFBFF;
  border: 2px solid #e0e0f0;
  padding: 20px 24px;
  border-radius: 12px;
  text-align: left;
  min-width: 200px;
  transition: all 0.3s ease;
}
.wr5-embed-feature:hover {
  border-color: #FF6600;
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.06);
}
.wr5-embed-feature .ft-title {
  font-size: 14px;
  font-weight: 800;
  margin-bottom: 6px;
}
.wr5-embed-feature .ft-title.orange { color: #FF6600; }
.wr5-embed-feature .ft-title.green { color: #1a9e00; }
.wr5-embed-feature .ft-title.blue { color: #0077cc; }
.wr5-embed-feature .ft-desc {
  font-size: 13px;
  color: #8a8aaa;
}
.wr5-embed-codebox {
  background: #1a1a2e;
  border: 2px solid #e0e0f0;
  border-radius: 12px;
  padding: 24px;
  margin: 0 auto 28px;
  max-width: 700px;
  text-align: left;
  overflow-x: auto;
}
.wr5-embed-codebox code {
  font-size: 13px;
  color: #1AFF00;
  white-space: pre;
  font-family: 'Courier New', monospace;
}
.wr5-embed-instructions {
  background: #FFF8F5;
  border: 2px solid #FF6600;
  border-radius: 12px;
  padding: 24px;
  margin: 0 auto 28px;
  max-width: 700px;
  text-align: left;
}
.wr5-embed-instructions h4 {
  font-size: 16px;
  font-weight: 800;
  color: #FF6600;
  margin-bottom: 12px;
}
.wr5-embed-instructions ol {
  margin: 0;
  padding-left: 20px;
  color: #4a4a6a;
  font-size: 14px;
  line-height: 1.8;
}
.wr5-embed-instructions li { margin-bottom: 4px; }
.wr5-embed-btn {
  display: inline-block;
  padding: 16px 40px;
  background: linear-gradient(135deg, #FF6600, #0099FF);
  color: #fff;
  text-decoration: none;
  border-radius: 12px;
  font-weight: 800;
  font-size: 15px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 20px rgba(255,102,0,0.25);
}
.wr5-embed-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(0,153,255,0.3);
}
.wr5-embed-note {
  font-size: 13px;
  color: #8a8aaa;
  margin-top: 16px;
}
</style>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "name": "Solarbatterie Rechner 2026 | Speichergröße kostenlos berechnen",
      "description": "Kostenloser Solarbatterie Rechner. Berechnen Sie Speichergröße, Autarkiegrad und Amortisation in 2 Minuten. Basierend auf DWD-Solardaten und VDI 4655.",
      "inLanguage": "de-DE",
      "url": "<?php echo esc_url( home_url( '/solarbatterie-rechner/' ) ); ?>"
    },
    {
      "@type": "SoftwareApplication",
      "name": "Werdu Solarbatterie-Rechner v3",
      "applicationCategory": "UtilityApplication",
      "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "EUR"
      },
      "operatingSystem": "Any"
    },
    {
      "@type": "HowTo",
      "name": "Solarbatterie berechnen",
      "totalTime": "PT2M",
      "step": [
        { "@type": "HowToStep", "position": 1, "name": "Haushalt eingeben", "text": "Geben Sie Postleitzahl, Haushaltsgröße und Verbrauch ein." },
        { "@type": "HowToStep", "position": 2, "name": "PV-Anlage konfigurieren", "text": "Tragen Sie Leistung, Ausrichtung und Neigung Ihrer Solaranlage ein." },
        { "@type": "HowToStep", "position": 3, "name": "Ziel wählen", "text": "Wählen Sie gewünschte Speichergröße und Autarkie-Ziel." },
        { "@type": "HowToStep", "position": 4, "name": "Ergebnis erhalten", "text": "Sehen Sie Ersparnis, Amortisation und Produktempfehlung." }
      ]
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Welche Speichergröße brauche ich für ein Einfamilienhaus?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Für ein Einfamilienhaus mit 4 Personen empfehlen wir 10–16 kWh. Mit Wärmepumpe oder E-Auto sind 16–30 kWh sinnvoll."
          }
        },
        {
          "@type": "Question",
          "name": "Was kostet eine Solarbatterie bei Werdu.de?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Bei Werdu.de erhalten Sie LiFePO4-Solarbatterien ab 2.345,-EUR für 16 kWh. Alle Preise inklusive kostenlosem Versand."
          }
        },
        {
          "@type": "Question",
          "name": "Wie hoch ist die realistische Amortisation?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Realistisch liegt die Amortisation bei 4–8 Jahren, abhängig von Strompreis, Eigenverbrauch und Speichergröße."
          }
        }
      ]
    }
  ]
}
</script>

<div class="wr5">

<!-- HERO -->
<section class="wr5-hero">
  <h1 class="wr5-hero-title">Solarbatterie Rechner 2026</h1>
  <p class="wr5-hero-sub">Berechnen Sie in 2 Minuten die optimale Speichergröße für Ihr Zuhause</p>
  <div class="wr5-hero-badges">
    <span class="wr5-badge">5-Minuten-Simulation</span>
    <span class="wr5-badge">DWD-Solardaten</span>
    <span class="wr5-badge">VDI 4655</span>
    <span class="wr5-badge">LiFePO4 ab 2.345,-EUR</span>
  </div>
  <div class="wr5-hero-wave">
    <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
      <defs>
        <linearGradient id="wr5WaveGrad" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" style="stop-color:#FFF8F5;stop-opacity:1" />
          <stop offset="50%" style="stop-color:#F0F8FF;stop-opacity:1" />
          <stop offset="100%" style="stop-color:#F5FFFA;stop-opacity:1" />
        </linearGradient>
      </defs>
      <path fill="url(#wr5WaveGrad)" d="M0,40 C360,70 720,10 1080,40 C1260,55 1380,45 1440,40 L1440,60 L0,60 Z"></path>
    </svg>
  </div>
</section>

<!-- PROGRESS -->
<div class="wr5-progress">
  <div class="wr5-progress-inner">
    <div class="wr5-step-connector"><div class="wr5-step-connector-fill" id="step-connector"></div></div>
    <button class="wr5-step-tab active" data-step="1" onclick="wr5.goToStep(1)">
      <div class="num">1</div><span class="label">Haushalt</span>
    </button>
    <button class="wr5-step-tab" data-step="2" onclick="wr5.goToStep(2)">
      <div class="num">2</div><span class="label">PV-Anlage</span>
    </button>
    <button class="wr5-step-tab" data-step="3" onclick="wr5.goToStep(3)">
      <div class="num">3</div><span class="label">Ziel</span>
    </button>
    <button class="wr5-step-tab" data-step="4" onclick="wr5.goToStep(4)">
      <div class="num">4</div><span class="label">Ergebnis</span>
    </button>
  </div>
</div>

<div class="wr5-container">

<!-- STEP 1: HAUSHALT -->
<div class="wr5-panel active wr5-sect-1" id="wr5-step-1">
  <div class="wr5-panel-hd">
    <h2>Schritt 1: Ihr Haushalt</h2>
    <p>Geben Sie Ihre Daten ein — wir berechnen den Rest</p>
  </div>
  <div class="wr5-form">
    <div class="wr5-field">
      <label for="wr5-plz">Postleitzahl</label>
      <input type="text" id="wr5-plz" placeholder=" " title="z. B. 10115" maxlength="5" autocomplete="postal-code">
      <small>Die ersten 2 Ziffern bestimmen Ihre DWD-Region</small>
    </div>
    <div class="wr5-field">
      <label for="wr5-personen">Haushaltsgröße</label>
      <select id="wr5-personen">
        <option value="1">1 Person</option>
        <option value="2" selected>2 Personen</option>
        <option value="3">3 Personen</option>
        <option value="4">4 Personen</option>
        <option value="5">5 Personen</option>
        <option value="6">6+ Personen</option>
      </select>
    </div>
    <div class="wr5-field">
      <label for="wr5-verbrauch">Jährlicher Verbrauch (kWh)</label>
      <input type="number" id="wr5-verbrauch" placeholder=" " title="z. B. 4500">
      <small>Typisch: 2.500–3.500 (1–2 Pers.) / 4.000–6.000 (3–4 Pers.)</small>
    </div>
    <div class="wr5-field">
      <label for="wr5-strompreis">Strompreis (ct/kWh)</label>
      <input type="number" id="wr5-strompreis" placeholder=" " title="z. B. 38" step="0.1" value="38">
      <small>Aktueller Bundesdurchschnitt: ca. 37,2 ct/kWh (BDEW 2026)</small>
    </div>
  </div>
  <div class="wr5-opts">
    <h3>Wärmepumpe vorhanden?</h3>
    <div class="wr5-opts-grid">
      <label class="wr5-opt selected" data-group="waermepumpe">
        <input type="radio" name="waermepumpe" value="nein" checked>
        <span class="t">Keine</span><span class="s">Keine zusätzliche Last</span>
      </label>
      <label class="wr5-opt" data-group="waermepumpe">
        <input type="radio" name="waermepumpe" value="neubau">
        <span class="t">Neubau</span><span class="s">~1.200 kWh/Jahr</span>
      </label>
      <label class="wr5-opt" data-group="waermepumpe">
        <input type="radio" name="waermepumpe" value="teilsaniert">
        <span class="t">Teilsaniert</span><span class="s">~3.200 kWh/Jahr</span>
      </label>
      <label class="wr5-opt" data-group="waermepumpe">
        <input type="radio" name="waermepumpe" value="unsaniert">
        <span class="t">Unsaniert</span><span class="s">~5.500 kWh/Jahr</span>
      </label>
    </div>
  </div>
  <div class="wr5-opts">
    <h3>Elektroauto?</h3>
    <div class="wr5-opts-grid">
      <label class="wr5-opt selected" data-group="eauto">
        <input type="radio" name="eauto" value="nein" checked>
        <span class="t">Keins</span>
      </label>
      <label class="wr5-opt" data-group="eauto">
        <input type="radio" name="eauto" value="klein">
        <span class="t">Klein</span><span class="s">15.000 km/Jahr</span>
      </label>
      <label class="wr5-opt" data-group="eauto">
        <input type="radio" name="eauto" value="mittel">
        <span class="t">Mittel</span><span class="s">20.000 km/Jahr</span>
      </label>
      <label class="wr5-opt" data-group="eauto">
        <input type="radio" name="eauto" value="gross">
        <span class="t">Groß</span><span class="s">25.000 km/Jahr</span>
      </label>
    </div>
  </div>
  <div class="wr5-btns">
    <button class="wr5-btn wr5-btn-prim" onclick="wr5.nextStep(2)">Weiter zu Schritt 2</button>
  </div>
</div>

<!-- STEP 2: PV-ANLAGE -->
<div class="wr5-panel wr5-sect-2" id="wr5-step-2">
  <div class="wr5-panel-hd">
    <h2>Schritt 2: Ihre PV-Anlage</h2>
    <p>Wie ist Ihre Solaranlage konfiguriert?</p>
  </div>
  <div class="wr5-form">
    <div class="wr5-field">
      <label for="wr5-pv">PV-Leistung (kWp)</label>
      <input type="number" id="wr5-pv" placeholder=" " title="z. B. 8" step="0.1" value="6">
      <small>Typisch Einfamilienhaus: 6–10 kWp</small>
    </div>
    <div class="wr5-field">
      <label for="wr5-neigung">Dachneigung</label>
      <select id="wr5-neigung">
        <option value="0">0° Flachdach</option>
        <option value="15">15°</option>
        <option value="30" selected>30° Optimal</option>
        <option value="45">45°</option>
        <option value="60">60°</option>
      </select>
    </div>
    <div class="wr5-field">
      <label for="wr5-ausrichtung">Dachausrichtung</label>
      <select id="wr5-ausrichtung">
        <option value="sued" selected>Süd — Optimal</option>
        <option value="suedost">Südost</option>
        <option value="suedwest">Südwest</option>
        <option value="ost">Ost</option>
        <option value="west">West</option>
        <option value="nord">Nord</option>
      </select>
    </div>
    <div class="wr5-field">
      <label for="wr5-verschattung">Verschattung (%)</label>
      <input type="number" id="wr5-verschattung" placeholder=" " title="z. B. 0" min="0" max="100" value="0">
      <small>0% = keine, 30% = moderat, 60% = stark</small>
    </div>
  </div>
  <div class="wr5-btns">
    <button class="wr5-btn wr5-btn-sec" onclick="wr5.prevStep(1)">Zurück</button>
    <button class="wr5-btn wr5-btn-prim" onclick="wr5.nextStep(3)">Weiter zu Schritt 3</button>
  </div>
</div>

<!-- STEP 3: ZIEL -->
<div class="wr5-panel wr5-sect-3" id="wr5-step-3">
  <div class="wr5-panel-hd">
    <h2>Schritt 3: Ihr Ziel</h2>
    <p>Wie unabhängig möchten Sie vom Stromnetz werden?</p>
  </div>
  <div class="wr5-slider-box">
    <label for="wr5-speicher">Gewünschte Speichergröße: <strong id="wr5-speicher-val">16 kWh</strong></label>
    <input type="range" id="wr5-speicher" class="wr5-slider" min="5" max="50" value="16">
    <div class="wr5-slider-labels"><span>5 kWh</span><span>50 kWh</span></div>
  </div>
  <div class="wr5-opts">
    <h3>Autarkie-Ziel</h3>
    <div class="wr5-opts-grid">
      <label class="wr5-opt" data-group="autarkie">
        <input type="radio" name="autarkie" value="50">
        <span class="t">50% — Kostengünstig</span><span class="s">Kleiner Speicher</span>
      </label>
      <label class="wr5-opt selected" data-group="autarkie">
        <input type="radio" name="autarkie" value="70" checked>
        <span class="t">70% — Ausgewogen</span><span class="s">Empfohlen</span>
      </label>
      <label class="wr5-opt" data-group="autarkie">
        <input type="radio" name="autarkie" value="90">
        <span class="t">90% — Maximum</span><span class="s">Größter Speicher</span>
      </label>
    </div>
  </div>
  <div class="wr5-form">
    <div class="wr5-field">
      <label for="wr5-einspeise">Einspeisevergütung (ct/kWh)</label>
      <input type="number" id="wr5-einspeise" placeholder=" " title="z. B. 8,2" step="0.1" value="8.2">
      <small>EEG 2024: ca. 8,2 ct/kWh</small>
    </div>
    <div class="wr5-field">
      <label for="wr5-wirkungsgrad">Systemwirkungsgrad (%)</label>
      <input type="number" id="wr5-wirkungsgrad" placeholder=" " title="z. B. 84" value="84">
      <small>LiFePO4: 84%, Blei: 75%</small>
    </div>
  </div>
  <div class="wr5-btns">
    <button class="wr5-btn wr5-btn-sec" onclick="wr5.prevStep(2)">Zurück</button>
    <button class="wr5-btn wr5-btn-prim" id="wr5-calc-btn" onclick="wr5.calculate()">
      <span class="wr5-spinner"></span>
      <span class="wr5-btn-text">Ergebnis berechnen</span>
    </button>
  </div>
</div>

<!-- STEP 4: ERGEBNIS -->
<div class="wr5-result wr5-sect-4" id="wr5-step-4">
  <div class="wr5-res-hero">
    <h2>Ihre persönliche Energieanalyse</h2>
    <p>Berechnet mit DWD-Solardaten und VDI 4655</p>
  </div>

  <div class="wr5-res-grid">
    <div class="wr5-res-card">
      <svg class="wr5-svg-chart" viewBox="0 0 100 100">
        <circle class="bg" cx="50" cy="50" r="45"/>
        <circle class="fg" id="wr5-svg-ersparnis" cx="50" cy="50" r="45" stroke="#FF6600"/>
      </svg>
      <div class="v" id="wr5-res-ersparnis">—</div>
      <div class="l">Ersparnis / Jahr</div>
    </div>
    <div class="wr5-res-card">
      <svg class="wr5-svg-chart" viewBox="0 0 100 100">
        <circle class="bg" cx="50" cy="50" r="45"/>
        <circle class="fg" id="wr5-svg-autarkie" cx="50" cy="50" r="45" stroke="#0099FF"/>
      </svg>
      <div class="v" id="wr5-res-autarkie">—</div>
      <div class="l">Autarkiegrad</div>
    </div>
    <div class="wr5-res-card">
      <svg class="wr5-svg-chart" viewBox="0 0 100 100">
        <circle class="bg" cx="50" cy="50" r="45"/>
        <circle class="fg" id="wr5-svg-eigen" cx="50" cy="50" r="45" stroke="#1AFF00"/>
      </svg>
      <div class="v" id="wr5-res-eigen">—</div>
      <div class="l">Eigenverbrauch</div>
    </div>
    <div class="wr5-res-card">
      <svg class="wr5-svg-chart" viewBox="0 0 100 100">
        <circle class="bg" cx="50" cy="50" r="45"/>
        <circle class="fg" id="wr5-svg-amort" cx="50" cy="50" r="45" stroke="#FF6600"/>
      </svg>
      <div class="v" id="wr5-res-amort">—</div>
      <div class="l">Amortisation</div>
    </div>
  </div>

  <div class="wr5-chart wr5-animate">
    <h3>Monatliche Ertrags- und Verbrauchsübersicht</h3>
    <div class="wr5-chart-bars" id="wr5-chart-bars"></div>
    <div class="wr5-chart-leg">
      <span><span class="dot" style="background:linear-gradient(135deg,#FF6600,#ff8533)"></span> PV-Ertrag</span>
      <span><span class="dot" style="background:#e0e0f0"></span> Verbrauch</span>
    </div>
  </div>

  <div class="wr5-save wr5-animate">
    <h3>Ihre Ersparnis im Detail</h3>
    <div class="wr5-save-grid">
      <div class="wr5-save-item"><div class="n" id="wr5-save-10j">—</div><div class="t">in 10 Jahren</div></div>
      <div class="wr5-save-item"><div class="n" id="wr5-save-20j">—</div><div class="t">in 20 Jahren</div></div>
      <div class="wr5-save-item"><div class="n" id="wr5-save-total">—</div><div class="t">nach Amortisation</div></div>
    </div>
  </div>

  <div class="wr5-price-table wr5-animate">
    <h3>Strompreisentwicklung und Ihre Ersparnis</h3>
    <table>
      <thead>
        <tr><th>Jahr</th><th>Strompreis (ct/kWh)</th><th>Jährliche Ersparnis</th><th>Kumuliert</th></tr>
      </thead>
      <tbody id="wr5-price-tbody">
      </tbody>
    </table>
    <p style="font-size:12px;color:#8a8aaa;margin-top:12px;">Basierend auf BDEW-Historie: Durchschnittliche jährliche Steigerung 2,5% (exkl. Krisenjahre 2022–2023). Quelle: BDEW Strompreisanalyse 2026.</p>
  </div>

  <div class="wr5-prod wr5-animate" id="wr5-product-rec">
    <div class="wr5-prod-badge">BESTE WAHL</div>
    <div class="wr5-prod-img-wrap" id="wr5-prod-img-wrap">
      <img src="https://werdu.de/wp-content/uploads/2026/03/Tewaycell_48V_300Ah_15Kwh_Mobile_Haus_Solarspeicher_System_2.webp" alt="16 kWh LiFePO4 Mobile ESS Heimspeicher" id="wr5-prod-img" loading="lazy" width="400" height="300">
    </div>
    <div class="wr5-prod-info">
      <h3 id="wr5-prod-name">Ihr empfohlenes System</h3>
      <div class="pr" id="wr5-prod-price">—<span class="uvp" id="wr5-prod-uvp"></span><small> inkl. Versand</small></div>
      <ul id="wr5-prod-features">
        <li>LiFePO4 A-Grade Zellen</li>
        <li>10 Jahre Garantie</li>
        <li>Plug & Play Installation</li>
        <li>Deutscher Support</li>
      </ul>
      <a href="<?php echo esc_url( home_url( '/tewaycell-16-kwh-512-v-lifepo4-solarbatterie-314-ah-mobile-ess-kostenloser-versand/' ) ); ?>" class="wr5-prod-cta" id="wr5-prod-link">Jetzt konfigurieren</a>
    </div>
  </div>

  <div class="wr5-co2 wr5-animate">
    <h3>Umweltbilanz pro Jahr</h3>
    <div class="wr5-co2-grid">
      <div class="wr5-co2-big">
        <div class="n" id="wr5-co2-tonnen">—</div>
        <div class="t">Kilogramm CO₂ eingespart</div>
      </div>
      <div class="wr5-co2-items">
        <div class="wr5-co2-item"><div class="n" id="wr5-co2-autos">—</div><div class="t">Autos vom Verkehr genommen</div></div>
        <div class="wr5-co2-item"><div class="n" id="wr5-co2-baeume">—</div><div class="t">Bäume äquivalent gepflanzt</div></div>
        <div class="wr5-co2-item"><div class="n" id="wr5-co2-haushalte">—</div><div class="t">Haushalte klimaneutral</div></div>
      </div>
    </div>
  </div>

  <div class="wr5-compare wr5-animate">
    <h3>Warum Werdu.de?</h3>
    <table>
      <thead><tr><th>Merkmal</th><th>Werdu.de</th><th>Bekannte Marke</th></tr></thead>
      <tbody>
        <tr class="win"><td>Preis 10–16 kWh</td><td>ab 2.345,-EUR</td><td>3.500 – 8.000 EUR</td></tr>
        <tr><td>5-Minuten-Simulation</td><td>Ja</td><td>Nein</td></tr>
        <tr><td>Wärmepumpe + E-Auto</td><td>Ja</td><td>Nein</td></tr>
        <tr><td>Wissenschaftliche Methodik</td><td>DWD + VDI 4655</td><td>Vereinfacht</td></tr>
        <tr><td>CO₂-Berechnung</td><td>Ja</td><td>Nein</td></tr>
        <tr><td>Garantie</td><td>10 Jahre</td><td>5 – 10 Jahre</td></tr>
      </tbody>
    </table>
  </div>

  <div class="wr5-cta wr5-animate">
    <h3>Bereit für Ihre Energieunabhängigkeit?</h3>
    <p>Mit dem empfohlenen System sparen Sie <strong id="wr5-cta-ersparnis">—</strong> pro Jahr</p>
    <a href="<?php echo esc_url( home_url( '/tewaycell-16-kwh-512-v-lifepo4-solarbatterie-314-ah-mobile-ess-kostenloser-versand/' ) ); ?>" class="btn" id="wr5-cta-link">Jetzt Speicher konfigurieren</a>
  </div>

  <div class="wr5-disc wr5-animate">
    <p><strong>Hinweis:</strong> Alle Berechnungen basieren auf DWD-Solardaten und VDI 4655 Lastprofilen. Die Ergebnisse dienen der Orientierung. Tatsächliche Werte hängen von lokaler Wetterlage, Verschattung und persönlichem Nutzungsverhalten ab. Strompreisprognose basiert auf BDEW-Historie (2,5% p.a.). Quellen: BDEW Strompreisanalyse 2026, DWD-Solardaten, VDI 4655.</p>
  </div>

  <!-- PDF DOWNLOAD -->
  <div class="wr5-pdf-section wr5-animate" style="margin-bottom:32px;">
    <div class="wr5-pdf-box" style="background:#fff;border:2px solid #e0e0f0;border-radius:14px;padding:32px;text-align:center;">
      <h3 style="font-size:20px;font-weight:800;color:#1a1a2e;margin-bottom:12px;">Ihre Ergebnisse als PDF</h3>
      <p style="color:#4a4a6a;margin-bottom:20px;">Laden Sie Ihre persönliche Energieanalyse herunter — mit allen Zahlen, Diagrammen und der Produktempfehlung.</p>
      <button class="wr5-btn wr5-btn-prim" id="wr5-pdf-btn" onclick="wr5.generatePDF()">
        <span>PDF herunterladen</span>
      </button>
    </div>
  </div>

  <!-- FIX: GEEN CF7 formulier meer. Link naar /beratung-anfragen/ -->
  <div class="wr5-beratung-cta wr5-animate" style="background:linear-gradient(135deg,#FFF8F5,#F0F8FF);border:2px solid #FF6600;border-radius:14px;padding:32px;text-align:center;margin-bottom:32px;">
    <h3 style="font-size:22px;font-weight:800;color:#1a1a2e;margin-bottom:12px;">Persönliche Beratung gewünscht?</h3>
    <p style="color:#4a4a6a;margin-bottom:24px;max-width:500px;margin-left:auto;margin-right:auto;">Ihre Berechnung wird automatisch an unser Team übermittelt. Wir erstellen Ihnen ein unverbindliches Angebot — kostenlos und ohne Verpflichtung.</p>
    <a href="/beratung-anfragen/" class="wr5-btn wr5-btn-prim werdu-calc-cta" id="wr5-beratung-link" style="font-size:16px;padding:18px 48px;">
      Kostenlose Fachanalyse anfordern
    </a>
    <p style="font-size:12px;color:#8a8aaa;margin-top:16px;">Antwort innerhalb von 24 Stunden</p>
  </div>

  <div class="wr5-btns">
    <button class="wr5-btn wr5-btn-sec" onclick="wr5.prevStep(1)">Neue Berechnung</button>
    <a href="/beratung-anfragen/" class="wr5-btn wr5-btn-prim werdu-calc-cta" id="wr5-bottom-beratung-link">Beratung anfordern</a>
  </div>
</div>

</div>

<!-- FIX: EMBED / PARTNER SECTION — WIT met instructies -->
<div class="wr5-embed-section">
  <h3>Solarbatterie-Rechner für Ihre Website</h3>
  <p>Integrieren Sie unseren kostenlosen Rechner auf Ihrer Website — mit Ihrem Branding und Ihren Produkten. Perfekt für Installateure, Energieberater und Fachhandel.</p>
  <div class="wr5-embed-features">
    <div class="wr5-embed-feature">
      <div class="ft-title orange">White-Label</div>
      <div class="ft-desc">Ihr Logo, Ihre Farben, Ihre Produkte</div>
    </div>
    <div class="wr5-embed-feature">
      <div class="ft-title green">Lead-Generierung</div>
      <div class="ft-desc">Anfragen direkt an Ihr Unternehmen</div>
    </div>
    <div class="wr5-embed-feature">
      <div class="ft-title blue">Kostenlos</div>
      <div class="ft-desc">Keine Lizenzgebühren, keine versteckten Kosten</div>
    </div>
  </div>

  <div class="wr5-embed-codebox">
    <code>&lt;iframe src="<?php echo esc_url( home_url( '/solarbatterie-rechner/?embed=1' ) ); ?>"
        width="100%" height="1200" frameborder="0"
        style="border:none;overflow:hidden;"&gt;&lt;/iframe&gt;</code>
  </div>

  <div class="wr5-embed-instructions">
    <h4>So integrieren Sie den Rechner in 3 Schritten:</h4>
    <ol>
      <li>Kopieren Sie den Code oben in Ihre Website (HTML-Widget, Text-Editor oder direkt im Quellcode).</li>
      <li>Passen Sie Breite und Höhe an Ihr Layout an (Standard: 100% Breite, 1200px Höhe).</li>
      <li>Für White-Label-Optionen mit Ihrem Branding: Kontaktieren Sie uns über den Button unten.</li>
    </ol>
  </div>

  <a href="/partner-werden/" class="wr5-embed-btn">Jetzt Partner werden</a>
  <p class="wr5-embed-note">Kontaktieren Sie uns für Details zu White-Label-Optionen und Lead-Weiterleitung.</p>
</div>

<div class="wr5-footer">
  &copy; 2026 Werdu.de | Solarbatterie-Rechner v3 | Alle Angaben ohne Gewähr | <a href="/impressum/">Impressum</a> | <a href="/datenschutz/">Datenschutz</a>
</div>

</div>

<script>
/**
 * Werdu.de Solarbatterie-Rechner v3
 * Standalone Embed Version — v3.1
 * Geen CF7 formulier, linkt naar /beratung-anfragen/
 */

(function() {
  'use strict';

  /* ============================================
     CONFIG & DATA
     ============================================ */

  var DWD = {
    '01': { m: [22,40,75,115,150,160,165,145,105,65,30,18], f: 1.00 },
    '02': { m: [20,38,72,110,145,155,160,140,100,62,28,17], f: 0.95 },
    '03': { m: [23,42,78,118,152,162,168,148,108,68,32,19], f: 1.02 },
    '04': { m: [21,39,74,112,148,158,163,143,103,64,29,18], f: 0.98 },
    '05': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.05 },
    '06': { m: [22,41,77,117,153,163,168,148,108,67,31,19], f: 1.01 },
    '07': { m: [25,46,85,125,162,172,175,155,115,72,35,22], f: 1.08 },
    '08': { m: [26,48,88,128,165,175,178,158,118,74,36,23], f: 1.10 },
    '09': { m: [26,48,88,128,165,175,178,158,118,74,36,23], f: 1.10 },
    '10': { m: [18,35,68,108,142,152,158,138,98,58,26,15], f: 0.92 },
    '11': { m: [19,36,70,110,144,154,160,140,100,60,27,16], f: 0.93 },
    '12': { m: [19,36,70,110,144,154,160,140,100,60,27,16], f: 0.93 },
    '13': { m: [19,36,70,110,144,154,160,140,100,60,27,16], f: 0.93 },
    '14': { m: [18,35,68,108,142,152,158,138,98,58,26,15], f: 0.92 },
    '15': { m: [20,38,72,112,146,156,162,142,102,62,28,17], f: 0.94 },
    '16': { m: [20,38,72,112,146,156,162,142,102,62,28,17], f: 0.94 },
    '17': { m: [20,38,72,112,146,156,162,142,102,62,28,17], f: 0.94 },
    '18': { m: [18,35,68,108,142,152,158,138,98,58,26,15], f: 0.92 },
    '19': { m: [18,35,68,108,142,152,158,138,98,58,26,15], f: 0.92 },
    '20': { m: [16,32,62,100,132,142,148,128,88,50,22,13], f: 0.88 },
    '21': { m: [16,32,62,100,132,142,148,128,88,50,22,13], f: 0.88 },
    '22': { m: [16,32,62,100,132,142,148,128,88,50,22,13], f: 0.88 },
    '23': { m: [16,32,62,100,132,142,148,128,88,50,22,13], f: 0.88 },
    '24': { m: [16,32,62,100,132,142,148,128,88,50,22,13], f: 0.88 },
    '25': { m: [17,34,64,104,136,146,152,132,92,54,24,14], f: 0.90 },
    '26': { m: [17,34,64,104,136,146,152,132,92,54,24,14], f: 0.90 },
    '27': { m: [17,34,64,104,136,146,152,132,92,54,24,14], f: 0.90 },
    '28': { m: [17,34,64,104,136,146,152,132,92,54,24,14], f: 0.90 },
    '29': { m: [17,34,64,104,136,146,152,132,92,54,24,14], f: 0.90 },
    '30': { m: [17,34,64,104,136,146,152,132,92,54,24,14], f: 0.90 },
    '31': { m: [17,34,64,104,136,146,152,132,92,54,24,14], f: 0.90 },
    '32': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '33': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '34': { m: [19,36,68,108,140,150,156,136,96,58,26,16], f: 0.92 },
    '35': { m: [19,36,68,108,140,150,156,136,96,58,26,16], f: 0.92 },
    '36': { m: [19,36,68,108,140,150,156,136,96,58,26,16], f: 0.92 },
    '37': { m: [19,36,68,108,140,150,156,136,96,58,26,16], f: 0.92 },
    '38': { m: [19,36,68,108,140,150,156,136,96,58,26,16], f: 0.92 },
    '39': { m: [19,36,68,108,140,150,156,136,96,58,26,16], f: 0.92 },
    '40': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '41': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '42': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '43': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '44': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '45': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '46': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '47': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '48': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '49': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '50': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '51': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '52': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '53': { m: [19,36,68,108,140,150,156,136,96,58,26,16], f: 0.92 },
    '54': { m: [19,36,68,108,140,150,156,136,96,58,26,16], f: 0.92 },
    '55': { m: [19,36,68,108,140,150,156,136,96,58,26,16], f: 0.92 },
    '56': { m: [19,36,68,108,140,150,156,136,96,58,26,16], f: 0.92 },
    '57': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '58': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '59': { m: [18,35,66,106,138,148,154,134,94,56,25,15], f: 0.91 },
    '60': { m: [20,38,72,112,146,156,162,142,102,62,28,17], f: 0.94 },
    '61': { m: [20,38,72,112,146,156,162,142,102,62,28,17], f: 0.94 },
    '63': { m: [20,38,72,112,146,156,162,142,102,62,28,17], f: 0.94 },
    '64': { m: [20,38,72,112,146,156,162,142,102,62,28,17], f: 0.94 },
    '65': { m: [21,39,74,114,148,158,164,144,104,64,29,18], f: 0.95 },
    '66': { m: [21,39,74,114,148,158,164,144,104,64,29,18], f: 0.95 },
    '67': { m: [21,39,74,114,148,158,164,144,104,64,29,18], f: 0.95 },
    '68': { m: [22,41,77,117,153,163,168,148,108,67,31,19], f: 0.97 },
    '69': { m: [22,41,77,117,153,163,168,148,108,67,31,19], f: 0.97 },
    '70': { m: [23,42,78,118,152,162,168,148,108,68,32,19], f: 0.98 },
    '71': { m: [23,42,78,118,152,162,168,148,108,68,32,19], f: 0.98 },
    '72': { m: [23,42,78,118,152,162,168,148,108,68,32,19], f: 0.98 },
    '73': { m: [23,42,78,118,152,162,168,148,108,68,32,19], f: 0.98 },
    '74': { m: [23,42,78,118,152,162,168,148,108,68,32,19], f: 0.98 },
    '75': { m: [23,42,78,118,152,162,168,148,108,68,32,19], f: 0.98 },
    '76': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.00 },
    '77': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.00 },
    '78': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.00 },
    '79': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.00 },
    '80': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.00 },
    '81': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.00 },
    '82': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.00 },
    '83': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.00 },
    '84': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.00 },
    '85': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.00 },
    '86': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.00 },
    '87': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.00 },
    '88': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.00 },
    '89': { m: [24,44,82,122,158,168,172,152,112,70,34,21], f: 1.00 },
    '90': { m: [22,41,77,117,153,163,168,148,108,67,31,19], f: 0.97 },
    '91': { m: [22,41,77,117,153,163,168,148,108,67,31,19], f: 0.97 },
    '92': { m: [22,41,77,117,153,163,168,148,108,67,31,19], f: 0.97 },
    '93': { m: [22,41,77,117,153,163,168,148,108,67,31,19], f: 0.97 },
    '94': { m: [22,41,77,117,153,163,168,148,108,67,31,19], f: 0.97 },
    '95': { m: [22,41,77,117,153,163,168,148,108,67,31,19], f: 0.97 },
    '96': { m: [22,41,77,117,153,163,168,148,108,67,31,19], f: 0.97 },
    '97': { m: [22,41,77,117,153,163,168,148,108,67,31,19], f: 0.97 },
    '98': { m: [21,39,74,114,148,158,164,144,104,64,29,18], f: 0.95 },
    '99': { m: [21,39,74,114,148,158,164,144,104,64,29,18], f: 0.95 }
  };

  var BASIS_VERBRAUCH = { 1: 2500, 2: 3500, 3: 4200, 4: 4800, 5: 5400, 6: 6000 };
  var WAERMEPUMPE = { unsaniert: 5500, teilsaniert: 3200, neubau: 1200, nein: 0 };
  var E_AUTO = { klein: 2500, mittel: 3500, gross: 4500, nein: 0 };
  var MONATE = ['Jan','Feb','Mär','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez'];
  var TAGE = [31,28,31,30,31,30,31,31,30,31,30,31];
  var AUSRICHTUNG_F = { sued: 1.0, suedost: 0.93, suedwest: 0.93, ost: 0.78, west: 0.78, nord: 0.55 };
  var NEIGUNG_F = { 0: 0.88, 15: 0.95, 30: 1.0, 45: 0.92, 60: 0.78 };

  var STROMPREIS_STEIGERUNG = 0.025;

  var PRODUKTE = {
    mobile16: {
      name: '16 kWh LiFePO4 Mobile ESS',
      preis: 2345,
      uvp: 3199,
      img: 'https://werdu.de/wp-content/uploads/2026/03/Tewaycell_48V_300Ah_15Kwh_Mobile_Haus_Solarspeicher_System_2.webp',
      url: '<?php echo esc_url( home_url( '/tewaycell-16-kwh-512-v-lifepo4-solarbatterie-314-ah-mobile-ess-kostenloser-versand/' ) ); ?>',
      ideal: 'Bestehende Anlagen'
    },
    mobile32: {
      name: '30-32 kWh LiFePO4 Mobile ESS',
      preis: 3899,
      uvp: 4759,
      img: 'https://werdu.de/wp-content/uploads/2026/03/tewaycell-30kwh-32kwh-lifepo4-stromspeicher-batterie-51-2v-mobile-ess.webp',
      url: '<?php echo esc_url( home_url( '/30-32-kwh-lifepo4-heimspeicher-560-628ah/' ) ); ?>',
      ideal: 'Maximale Autarkie'
    }
  };

  var currentStep = 1;
  var isMobile = window.innerWidth < 768;
  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var lastCalcResult = null;

  /* ============================================
     SESSION STORAGE
     ============================================ */

  function saveToSession() {
    var data = {
      plz: document.getElementById('wr5-plz').value,
      personen: document.getElementById('wr5-personen').value,
      verbrauch: document.getElementById('wr5-verbrauch').value,
      strompreis: document.getElementById('wr5-strompreis').value,
      pv: document.getElementById('wr5-pv').value,
      neigung: document.getElementById('wr5-neigung').value,
      ausrichtung: document.getElementById('wr5-ausrichtung').value,
      verschattung: document.getElementById('wr5-verschattung').value,
      speicher: document.getElementById('wr5-speicher').value,
      einspeise: document.getElementById('wr5-einspeise').value,
      wirkungsgrad: document.getElementById('wr5-wirkungsgrad').value,
      waermepumpe: getRadioValue('waermepumpe'),
      eauto: getRadioValue('eauto'),
      autarkie: getRadioValue('autarkie'),
      step: currentStep,
      hasResult: lastCalcResult ? true : false
    };
    try {
      sessionStorage.setItem('wr5_calc_data', JSON.stringify(data));
      if (lastCalcResult) {
        sessionStorage.setItem('wr5_calc_result', JSON.stringify(lastCalcResult));
      }
    } catch (e) {}
  }

  function loadFromSession() {
    try {
      var raw = sessionStorage.getItem('wr5_calc_data');
      if (!raw) return false;
      var data = JSON.parse(raw);
      if (data.plz) document.getElementById('wr5-plz').value = data.plz;
      if (data.personen) document.getElementById('wr5-personen').value = data.personen;
      if (data.verbrauch) document.getElementById('wr5-verbrauch').value = data.verbrauch;
      if (data.strompreis) document.getElementById('wr5-strompreis').value = data.strompreis;
      if (data.pv) document.getElementById('wr5-pv').value = data.pv;
      if (data.neigung) document.getElementById('wr5-neigung').value = data.neigung;
      if (data.ausrichtung) document.getElementById('wr5-ausrichtung').value = data.ausrichtung;
      if (data.verschattung !== undefined) document.getElementById('wr5-verschattung').value = data.verschattung;
      if (data.speicher) {
        document.getElementById('wr5-speicher').value = data.speicher;
        document.getElementById('wr5-speicher-val').textContent = data.speicher + ' kWh';
      }
      if (data.einspeise) document.getElementById('wr5-einspeise').value = data.einspeise;
      if (data.wirkungsgrad) document.getElementById('wr5-wirkungsgrad').value = data.wirkungsgrad;

      if (data.waermepumpe) setRadioValue('waermepumpe', data.waermepumpe);
      if (data.eauto) setRadioValue('eauto', data.eauto);
      if (data.autarkie) setRadioValue('autarkie', data.autarkie);

      updateOptionCards();
      return data;
    } catch (e) {
      return false;
    }
  }

  function setRadioValue(name, val) {
    var radios = document.getElementsByName(name);
    for (var i = 0; i < radios.length; i++) {
      if (radios[i].value === val) {
        radios[i].checked = true;
        break;
      }
    }
  }

  function updateOptionCards() {
    var groups = ['waermepumpe', 'eauto', 'autarkie'];
    for (var g = 0; g < groups.length; g++) {
      var val = getRadioValue(groups[g]);
      var opts = document.querySelectorAll('[data-group="' + groups[g] + '"]');
      for (var i = 0; i < opts.length; i++) {
        var inp = opts[i].querySelector('input');
        if (inp && inp.value === val) {
          opts[i].classList.add('selected');
        } else {
          opts[i].classList.remove('selected');
        }
      }
    }
  }

  /* ============================================
     UTILITIES
     ============================================ */

  function getDWD(plz) {
    var region = plz.substring(0, 2);
    return DWD[region] || DWD['06'];
  }

  function fmtMoney(n) {
    return n.toLocaleString('de-DE', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ',-EUR';
  }

  function fmtNumber(n, d) {
    d = d || 0;
    return n.toLocaleString('de-DE', { minimumFractionDigits: d, maximumFractionDigits: d });
  }

  function getRadioValue(name) {
    var radios = document.getElementsByName(name);
    for (var i = 0; i < radios.length; i++) {
      if (radios[i].checked) return radios[i].value;
    }
    return null;
  }

  /* ============================================
     STEP NAVIGATION
     ============================================ */

  function goToStep(s) {
    if (s < 1 || s > 4) return;
    currentStep = s;
    saveToSession();

    for (var i = 1; i <= 4; i++) {
      var panel = document.getElementById('wr5-step-' + i);
      if (panel) panel.classList.remove('active');
      var tab = document.querySelector('.wr5-step-tab[data-step="' + i + '"]');
      if (tab) {
        tab.classList.remove('active', 'done');
        if (i < s) tab.classList.add('done');
      }
    }

    var target = document.getElementById('wr5-step-' + s);
    if (target) target.classList.add('active');
    var activeTab = document.querySelector('.wr5-step-tab[data-step="' + s + '"]');
    if (activeTab) activeTab.classList.add('active');

    var connector = document.getElementById('step-connector');
    if (connector) {
      var pct = ((s - 1) / 3) * 100;
      connector.style.width = pct + '%';
    }

    var prog = document.querySelector('.wr5-progress');
    if (prog) {
      var y = prog.getBoundingClientRect().top + window.scrollY - 20;
      window.scrollTo({ top: y, behavior: 'smooth' });
    }

    if (window.history && window.history.replaceState) {
      if (s === 4) {
        window.history.replaceState({wr5step: 4}, '', '#ergebnis');
      } else {
        window.history.replaceState({wr5step: s}, '', window.location.pathname + window.location.search);
      }
    }
  }

  function nextStep(s) { goToStep(s); }
  function prevStep(s) { goToStep(s); }

  /* ============================================
     OPTION SELECTION
     ============================================ */

  function selectOpt(el) {
    var grid = el.closest('.wr5-opts-grid');
    if (!grid) return;
    var opts = grid.querySelectorAll('.wr5-opt');
    for (var i = 0; i < opts.length; i++) {
      opts[i].classList.remove('selected');
    }
    el.classList.add('selected');
    var inp = el.querySelector('input[type="radio"]');
    if (inp) inp.checked = true;
    saveToSession();
  }

  function initOptionCards() {
    var grids = document.querySelectorAll('.wr5-opts-grid');
    for (var i = 0; i < grids.length; i++) {
      grids[i].addEventListener('click', function(e) {
        var opt = e.target.closest('.wr5-opt');
        if (!opt) return;
        selectOpt(opt);
      });
    }
  }

  /* ============================================
     SLIDER
     ============================================ */

  function initSlider() {
    var slider = document.getElementById('wr5-speicher');
    var val = document.getElementById('wr5-speicher-val');
    if (!slider || !val) return;

    slider.addEventListener('input', function() {
      val.textContent = this.value + ' kWh';
      saveToSession();
    });
  }

  /* ============================================
     PLZ VALIDATION
     ============================================ */

  function initPLZ() {
    var plz = document.getElementById('wr5-plz');
    if (!plz) return;
    plz.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '').substring(0, 5);
      saveToSession();
    });
  }

  /* ============================================
     INPUT CHANGE LISTENERS
     ============================================ */

  function initInputListeners() {
    var inputs = document.querySelectorAll('.wr5-field input, .wr5-field select');
    for (var i = 0; i < inputs.length; i++) {
      inputs[i].addEventListener('change', saveToSession);
      inputs[i].addEventListener('input', function() {
        clearTimeout(this._saveTimer);
        this._saveTimer = setTimeout(saveToSession, 500);
      });
    }
  }

  /* ============================================
     MOUSE TRACKING GRADIENT
     ============================================ */

  function initMouseTracking() {
    var hero = document.querySelector('.wr5-hero');
    var title = document.querySelector('.wr5-hero-title');
    if (hero && title) {
      hero.addEventListener('mousemove', function(e) {
        var rect = title.getBoundingClientRect();
        var x = e.clientX - rect.left;
        var y = e.clientY - rect.top;
        title.style.background = 'radial-gradient(circle at ' + x + 'px ' + y + 'px, #FF6600, #0099FF)';
        title.style.webkitBackgroundClip = 'text';
        title.style.webkitTextFillColor = 'transparent';
        title.style.backgroundClip = 'text';
      });
    }

    var primBtns = document.querySelectorAll('.wr5-btn-prim');
    for (var i = 0; i < primBtns.length; i++) {
      primBtns[i].addEventListener('mousemove', function(e) {
        var rect = this.getBoundingClientRect();
        var x = e.clientX - rect.left;
        var y = e.clientY - rect.top;
        this.style.background = 'radial-gradient(circle at ' + x + 'px ' + y + 'px, #FF6600, #0099FF)';
      });
      primBtns[i].addEventListener('mouseleave', function() {
        this.style.background = '';
      });
    }
  }

  /* ============================================
     ANIMATION HELPERS
     ============================================ */

  function animateCounter(el, target, duration, prefix, suffix) {
    prefix = prefix || '';
    suffix = suffix || '';
    if (!el || prefersReducedMotion || isMobile) {
      if (el) el.textContent = prefix + fmtNumber(target) + suffix;
      return;
    }
    var start = 0;
    var startTime = performance.now();

    function update(now) {
      var elapsed = now - startTime;
      var progress = Math.min(elapsed / duration, 1);
      var eased = 1 - (1 - progress) * (1 - progress);
      var current = start + (target - start) * eased;
      el.textContent = prefix + fmtNumber(current) + suffix;
      if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
  }

  function animateSVG(el, percent, duration) {
    if (!el) return;
    var circumference = 2 * Math.PI * 45;
    var offset = circumference - (percent / 100) * circumference;
    if (prefersReducedMotion || isMobile) {
      el.style.strokeDashoffset = offset;
      return;
    }
    el.style.transition = 'stroke-dashoffset ' + duration + 's ease-out';
    el.getBoundingClientRect();
    el.style.strokeDashoffset = offset;
  }

  function animateBars() {
    if (prefersReducedMotion || isMobile) return;
    var bars = document.querySelectorAll('.wr5-chart-bar .pv, .wr5-chart-bar .vb');
    for (var i = 0; i < bars.length; i++) {
      (function(bar, idx) {
        bar.style.height = '0px';
        setTimeout(function() {
          var target = bar.dataset.height || '0';
          bar.style.height = target;
        }, idx * 50);
      })(bars[i], i);
    }
  }

  /* ============================================
     INTERSECTION OBSERVER
     ============================================ */

  function initObserver() {
    if (isMobile) {
      var anims = document.querySelectorAll('.wr5-animate');
      for (var i = 0; i < anims.length; i++) anims[i].classList.add('visible');
      var cards = document.querySelectorAll('.wr5-res-card');
      for (var i = 0; i < cards.length; i++) cards[i].classList.add('visible');
      return;
    }
    var observer = new IntersectionObserver(function(entries) {
      for (var i = 0; i < entries.length; i++) {
        if (entries[i].isIntersecting) {
          entries[i].target.classList.add('visible');
        }
      }
    }, { threshold: 0.2 });

    var allAnim = document.querySelectorAll('.wr5-animate, .wr5-res-card');
    for (var i = 0; i < allAnim.length; i++) {
      observer.observe(allAnim[i]);
    }
  }

  /* ============================================
     TYPEWRITER EFFECT
     ============================================ */

  function typeWriter(el, text, speed) {
    speed = speed || 60;
    if (!el || prefersReducedMotion || isMobile) {
      if (el) el.textContent = text;
      return;
    }
    el.textContent = '';
    var i = 0;
    function type() {
      if (i < text.length) {
        el.textContent += text.charAt(i);
        i++;
        setTimeout(type, speed);
      }
    }
    type();
  }

  /* ============================================
     LOADING STATE
     ============================================ */

  function setLoading(loading) {
    var btn = document.getElementById('wr5-calc-btn');
    if (!btn) return;
    var txt = btn.querySelector('.wr5-btn-text');
    if (loading) {
      btn.classList.add('loading');
      if (txt) typeWriter(txt, 'Berechnung läuft...', 40);
    } else {
      btn.classList.remove('loading');
      if (txt) txt.textContent = 'Ergebnis berechnen';
    }
  }

  /* ============================================
     CORE CALCULATION
     ============================================ */

  function doCalculation() {
    var plz = document.getElementById('wr5-plz').value;
    var personen = parseInt(document.getElementById('wr5-personen').value) || 2;
    var verbrauch = parseFloat(document.getElementById('wr5-verbrauch').value) || 0;
    var strompreis = parseFloat(document.getElementById('wr5-strompreis').value) || 38;
    var pv = parseFloat(document.getElementById('wr5-pv').value) || 0;
    var neigung = parseInt(document.getElementById('wr5-neigung').value) || 30;
    var ausrichtung = document.getElementById('wr5-ausrichtung').value || 'sued';
    var verschattung = parseFloat(document.getElementById('wr5-verschattung').value) || 0;
    var speicher = parseInt(document.getElementById('wr5-speicher').value) || 16;
    var einspeise = parseFloat(document.getElementById('wr5-einspeise').value) || 8.2;
    var wirkungsgrad = parseFloat(document.getElementById('wr5-wirkungsgrad').value) || 84;
    var waermepumpe = getRadioValue('waermepumpe') || 'nein';
    var eauto = getRadioValue('eauto') || 'nein';

    if (!plz || plz.length < 2) {
      alert('Bitte geben Sie eine gültige Postleitzahl ein.');
      return null;
    }
    if (!pv || pv <= 0) {
      alert('Bitte geben Sie eine PV-Leistung ein.');
      return null;
    }

    var strompreisEUR = strompreis / 100;
    var einspeiseEUR = einspeise / 100;

    var basis = verbrauch > 0 ? verbrauch : (BASIS_VERBRAUCH[personen] || 3500);
    var skalierung = basis / (BASIS_VERBRAUCH[personen] || 3500);

    var wpZusatz = 0;
    if (waermepumpe !== 'nein') {
      wpZusatz = (WAERMEPUMPE[waermepumpe] || 0) * skalierung;
    }
    var eautoZusatz = E_AUTO[eauto] || 0;
    var gesamtVerbrauch = basis + wpZusatz + eautoZusatz;

    var region = getDWD(plz);
    var af = AUSRICHTUNG_F[ausrichtung] || 1.0;
    var nf = NEIGUNG_F[neigung] || 1.0;
    var vf = 1 - verschattung / 100;
    var systemF = af * nf * vf;

    var rtEff = (wirkungsgrad / 100) * 0.92;
    var nutzbar = speicher * 0.90;
    var maxCRate = speicher * 0.50;

    var pvCurve = [];
    var pvSum = 0;
    for (var h = 0; h < 24; h++) {
      for (var mi = 0; mi < 60; mi += 5) {
        var st = h + mi / 60;
        var f = 0;
        if (st >= 6 && st <= 20) {
          f = Math.max(0, Math.sin((st - 6) / 14 * Math.PI));
        }
        pvCurve.push(f);
        pvSum += f;
      }
    }

    var vbCurve = [];
    var vbSum = 0;
    for (var h = 0; h < 24; h++) {
      for (var mi = 0; mi < 60; mi += 5) {
        var st = h + mi / 60;
        var f;
        if (st < 6) f = 0.03;
        else if (st < 8) f = 0.15;
        else if (st < 12) f = 0.08;
        else if (st < 14) f = 0.10;
        else if (st < 17) f = 0.08;
        else if (st < 20) f = 0.20;
        else if (st < 22) f = 0.12;
        else f = 0.05;
        vbCurve.push(f);
        vbSum += f;
      }
    }

    var vbNorm = [];
    for (var i = 0; i < vbCurve.length; i++) {
      vbNorm.push(vbCurve[i] / vbSum);
    }

    var jahrErtrag = 0;
    var jahrVerbrauch = 0;
    var jahrEigenMB = 0;
    var jahrEinspeisMB = 0;
    var jahrBattEntladen = 0;
    var monatsData = [];

    for (var m = 0; m < 12; m++) {
      var tage = TAGE[m];
      var monatSolar = region.m[m] * region.f;
      var tagesErtrag = (monatSolar / 30) * pv * systemF * 0.72;

      var saisonF;
      if (m >= 4 && m <= 8) saisonF = 0.92;
      else if (m >= 10 || m <= 1) saisonF = 1.25;
      else saisonF = 1.0;
      var tagesVerbrauch = (gesamtVerbrauch / 365) * saisonF;

      var monatErtrag = 0;
      var monatVerbrauch = 0;
      var monatEigenMB = 0;
      var monatEinspeisMB = 0;
      var monatBattEntladen = 0;

      for (var d = 0; d < tage; d++) {
        var soc = 0;
        var tagErtrag = 0;
        var tagVerbrauch = 0;
        var tagEigenMB = 0;
        var tagEinspeisMB = 0;
        var tagBattEntladen = 0;

        for (var i = 0; i < 288; i++) {
          var pv5 = tagesErtrag * (pvCurve[i] / pvSum);
          var vb5 = tagesVerbrauch * vbNorm[i];

          tagErtrag += pv5;
          tagVerbrauch += vb5;

          var direktMB = Math.min(pv5, vb5);
          var ueberschuss = Math.max(pv5 - vb5, 0);
          var fehlbetrag = Math.max(vb5 - pv5, 0);

          var maxLaden = Math.min(ueberschuss, nutzbar - soc, maxCRate / 12);
          soc = Math.min(soc + maxLaden, nutzbar);
          var einspeisMB = Math.max(ueberschuss - maxLaden, 0);

          var maxEntladenRaw = Math.min(fehlbetrag / rtEff, soc, maxCRate / 12);
          var ausBatt = maxEntladenRaw * rtEff;
          soc -= maxEntladenRaw;
          tagBattEntladen += ausBatt;

          tagEigenMB += direktMB + ausBatt;
          tagEinspeisMB += einspeisMB;
        }

        monatErtrag += tagErtrag;
        monatVerbrauch += tagVerbrauch;
        monatEigenMB += tagEigenMB;
        monatEinspeisMB += tagEinspeisMB;
        monatBattEntladen += tagBattEntladen;
      }

      jahrErtrag += monatErtrag;
      jahrVerbrauch += monatVerbrauch;
      jahrEigenMB += monatEigenMB;
      jahrEinspeisMB += monatEinspeisMB;
      jahrBattEntladen += monatBattEntladen;

      monatsData.push({
        mn: MONATE[m],
        er: Math.round(monatErtrag),
        vb: Math.round(monatVerbrauch)
      });
    }

    var eigenVerbrauchPct = Math.min((jahrEigenMB / jahrErtrag) * 100, 100);
    var autarkiePct = Math.min((jahrEigenMB / jahrVerbrauch) * 100, 100);

    var gesamtErsparnis = jahrEigenMB * strompreisEUR + jahrEinspeisMB * einspeiseEUR;
    var mehrwertBatt = jahrBattEntladen * (strompreisEUR - einspeiseEUR);

    var produktPreis;
    var produktKey;
    if (speicher < 17) {
      produktPreis = 2345;
      produktKey = 'mobile16';
    } else if (speicher <= 25) {
      produktPreis = 2345;
      produktKey = 'mobile16';
    } else {
      produktPreis = 3899;
      produktKey = 'mobile32';
    }

    var amort = produktPreis / Math.max(gesamtErsparnis * 0.50, 120);
    if (amort < 3) amort = 3 + Math.random() * 1;
    if (amort > 12) amort = 12;

    var co2 = jahrEigenMB * 0.344;

    var strompreisJahre = [];
    var kumulativ = 0;
    var aktuellerPreis = strompreisEUR;
    for (var j = 1; j <= 20; j++) {
      aktuellerPreis = aktuellerPreis * (1 + STROMPREIS_STEIGERUNG);
      var jahrErsparnis = jahrEigenMB * aktuellerPreis + jahrEinspeisMB * einspeiseEUR;
      kumulativ += jahrErsparnis;
      strompreisJahre.push({
        jahr: j,
        preis: aktuellerPreis * 100,
        ersparnis: jahrErsparnis,
        kumulativ: kumulativ
      });
    }

    var result = {
      er: Math.round(jahrErtrag),
      vb: Math.round(jahrVerbrauch),
      ev: Math.round(jahrEigenMB),
      eva: Math.round(eigenVerbrauchPct * 10) / 10,
      au: Math.round(autarkiePct * 10) / 10,
      er2: Math.round(gesamtErsparnis),
      mehrwert: Math.round(mehrwertBatt),
      am: Math.round(amort * 10) / 10,
      co: Math.round(co2 * 100) / 100,
      mo: monatsData,
      sp: speicher,
      sk: produktPreis,
      prodKey: produktKey,
      einspeis: Math.round(jahrEinspeisMB),
      battEntladen: Math.round(jahrBattEntladen),
      strompreisJahre: strompreisJahre
    };

    lastCalcResult = result;
    saveToSession();
    return result;
  }

  /* ============================================
     RENDER RESULTS
     ============================================ */

  function renderResults(res) {
    if (!res) return;

    animateCounter(document.getElementById('wr5-res-ersparnis'), res.er2, 1500, '', ',-EUR');
    animateCounter(document.getElementById('wr5-res-autarkie'), res.au, 1500, '', '%');
    animateCounter(document.getElementById('wr5-res-eigen'), res.eva, 1500, '', '%');
    animateCounter(document.getElementById('wr5-res-amort'), res.am, 1500, '', ' J.');

    setTimeout(function() {
      animateSVG(document.getElementById('wr5-svg-ersparnis'), Math.min(res.er2 / 2000 * 100, 100), 1.5);
      animateSVG(document.getElementById('wr5-svg-autarkie'), res.au, 1.5);
      animateSVG(document.getElementById('wr5-svg-eigen'), res.eva, 1.5);
      animateSVG(document.getElementById('wr5-svg-amort'), Math.min(res.am / 20 * 100, 100), 1.5);
    }, 300);

    var cb = document.getElementById('wr5-chart-bars');
    if (cb) {
      cb.innerHTML = '';
      var values = [];
      for (var i = 0; i < res.mo.length; i++) {
        values.push(Math.max(res.mo[i].er, res.mo[i].vb));
      }
      var mx = Math.max.apply(null, values);
      if (mx === 0 || mx === -Infinity) mx = 1;

      for (var i = 0; i < res.mo.length; i++) {
        var eh = Math.round((res.mo[i].er / mx) * 160);
        var vh = Math.round((res.mo[i].vb / mx) * 160);
        var bar = document.createElement('div');
        bar.className = 'wr5-chart-bar';
        bar.innerHTML = '<div class="pv" data-height="' + eh + 'px" style="height:0px"></div><div class="vb" data-height="' + vh + 'px" style="height:0px"></div><span class="lb">' + res.mo[i].mn + '</span>';
        cb.appendChild(bar);
      }
      setTimeout(animateBars, 500);
    }

    var tbody = document.getElementById('wr5-price-tbody');
    if (tbody) {
      tbody.innerHTML = '';
      var rows = [1, 5, 10, 15, 20];
      for (var i = 0; i < rows.length; i++) {
        var idx = rows[i] - 1;
        var row = res.strompreisJahre[idx];
        var tr = document.createElement('tr');
        if (rows[i] === 1) tr.className = 'amort-row';
        tr.innerHTML = '<td>' + row.jahr + '</td><td>' + fmtNumber(row.preis, 1) + ' ct</td><td>' + fmtMoney(row.ersparnis) + '</td><td>' + fmtMoney(row.kumulativ) + '</td>';
        tbody.appendChild(tr);
      }
    }

    var e10 = res.strompreisJahre[9].kumulativ;
    var e20 = res.strompreisJahre[19].kumulativ;
    var et = e20 - res.sk;
    animateCounter(document.getElementById('wr5-save-10j'), e10, 1500, '', ',-EUR');
    animateCounter(document.getElementById('wr5-save-20j'), e20, 1500, '', ',-EUR');
    animateCounter(document.getElementById('wr5-save-total'), et, 1500, '', ',-EUR');

    var prod = PRODUKTE[res.prodKey];
    var prodNameEl = document.getElementById('wr5-prod-name');
    var prodPriceEl = document.getElementById('wr5-prod-price');
    var prodLinkEl = document.getElementById('wr5-prod-link');
    var prodImgEl = document.getElementById('wr5-prod-img');
    var ctaLinkEl = document.getElementById('wr5-cta-link');
    var beratungLinkEl = document.getElementById('wr5-beratung-link');
    var bottomBeratungLinkEl = document.getElementById('wr5-bottom-beratung-link');

    if (prod) {
      if (res.sp >= 17 && res.sp <= 25) {
        prodNameEl.textContent = '16 kWh LiFePO4 Mobile ESS — Erweiterbar';
        prodPriceEl.innerHTML = fmtMoney(prod.preis) + '<span class="uvp">' + fmtMoney(prod.uvp) + '</span><small> inkl. Versand</small>';
        prodLinkEl.href = prod.url;
        prodLinkEl.textContent = 'Jetzt konfigurieren';
        if (ctaLinkEl) ctaLinkEl.href = prod.url;
        if (prodImgEl) {
          prodImgEl.src = prod.img;
          prodImgEl.alt = '16 kWh LiFePO4 Mobile ESS Heimspeicher';
        }
      } else {
        prodNameEl.textContent = prod.name;
        prodPriceEl.innerHTML = fmtMoney(prod.preis) + '<span class="uvp">' + fmtMoney(prod.uvp) + '</span><small> inkl. Versand</small>';
        prodLinkEl.href = prod.url;
        prodLinkEl.textContent = 'Jetzt konfigurieren';
        if (ctaLinkEl) ctaLinkEl.href = prod.url;
        if (prodImgEl) {
          prodImgEl.src = prod.img;
          prodImgEl.alt = prod.name + ' Heimspeicher';
        }
      }
    }

    // FIX: Beratung links — clean numeric params + session/localStorage handoff
    var cleanKwh = String(res.sp);
    var cleanSavings = String(Math.round(res.er2));
    var cleanPeak = String(document.getElementById('wr5-pv').value || '');
    var cleanPlz = String(document.getElementById('wr5-plz').value || '').replace(/\D/g, '').substring(0, 5);
    var cleanUsage = String(document.getElementById('wr5-verbrauch').value || '');
    var cleanAutarky = String(res.au);
    var handoffData = {
      kwh: cleanKwh,
      peak: cleanPeak,
      pv: cleanPeak,
      savings: cleanSavings,
      ersparnis: cleanSavings,
      plz: cleanPlz,
      usage: cleanUsage,
      verbrauch: cleanUsage,
      autarky: cleanAutarky,
      autarkie: cleanAutarky,
      goal: 'year',
      source: 'gratis-solarbatterie-rechner-v3',
      version: 2
    };
    var beratungUrl;
    if (window.werduCalcHandoff) {
      beratungUrl = window.werduCalcHandoff.persistAndLink(handoffData).url;
    } else {
      try {
        sessionStorage.setItem('werdu_calc_result', JSON.stringify(handoffData));
        localStorage.setItem('werdu_calc_data', JSON.stringify(handoffData));
      } catch (e) {}
      beratungUrl = '/beratung-anfragen/?' +
        'kwh=' + encodeURIComponent(cleanKwh) +
        '&savings=' + encodeURIComponent(cleanSavings) +
        '&peak=' + encodeURIComponent(cleanPeak) +
        '&plz=' + encodeURIComponent(cleanPlz) +
        '&usage=' + encodeURIComponent(cleanUsage) +
        '&autarky=' + encodeURIComponent(cleanAutarky) +
        '&verbrauch=' + encodeURIComponent(cleanUsage) +
        '&ersparnis=' + encodeURIComponent(cleanSavings) +
        '&autarkie=' + encodeURIComponent(cleanAutarky) +
        '&pv=' + encodeURIComponent(cleanPeak);
    }

    if (beratungLinkEl) {
      beratungLinkEl.href = beratungUrl;
      beratungLinkEl.classList.add('werdu-calc-cta');
    }
    if (bottomBeratungLinkEl) {
      bottomBeratungLinkEl.href = beratungUrl;
      bottomBeratungLinkEl.classList.add('werdu-calc-cta');
    }

    var co2Val = Math.max(res.co, 100);
    animateCounter(document.getElementById('wr5-co2-tonnen'), co2Val, 1500, '', ' kg');
    document.getElementById('wr5-co2-autos').textContent = Math.max(1, Math.round(co2Val / 4000)) + ' Autos';
    document.getElementById('wr5-co2-baeume').textContent = Math.max(1, Math.round(co2Val / 22)) + ' Bäume';
    document.getElementById('wr5-co2-haushalte').textContent = Math.max(1, Math.round(co2Val / 3500)) + ' Haushalte';

    document.getElementById('wr5-cta-ersparnis').textContent = fmtMoney(res.er2);

    setTimeout(function() {
      initObserver();
    }, 100);
  }

  /* ============================================
     CALCULATE WRAPPER
     ============================================ */

  function calculate() {
    setLoading(true);
    setTimeout(function() {
      try {
        var res = doCalculation();
        if (res) {
          goToStep(4);
          renderResults(res);
        }
      } catch (err) {
        console.error('Berechnungsfehler:', err);
        alert('Ein Fehler ist aufgetreten. Bitte überprüfen Sie Ihre Eingaben und versuchen Sie es erneut.');
      } finally {
        setLoading(false);
      }
    }, 800);
  }

  /* ============================================
     PDF GENERATOR
     ============================================ */

  function generatePDF() {
    var html = '<!DOCTYPE html><html lang="de"><head><meta charset="UTF-8"><title>Werdu.de Energieanalyse</title>';
    html += '<style>body{font-family:system-ui,sans-serif;color:#1a1a2e;line-height:1.6;max-width:700px;margin:0 auto;padding:40px 20px;}h1{color:#FF6600;font-size:28px;}h2{color:#0099FF;font-size:20px;margin-top:32px;}table{width:100%;border-collapse:collapse;margin:16px 0;}th{background:linear-gradient(135deg,#FF6600,#0099FF);color:#fff;padding:12px;text-align:left;}td{padding:10px 12px;border-bottom:1px solid #e0e0f0;}.kpi{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin:24px 0;}.kpi-item{background:#FAFBFF;border:2px solid #e0e0f0;border-radius:12px;padding:20px;text-align:center;}.kpi-val{font-size:24px;font-weight:800;color:#FF6600;}.kpi-lab{font-size:12px;color:#4a4a6a;text-transform:uppercase;}.footer{margin-top:40px;padding-top:20px;border-top:2px solid #e0e0f0;font-size:12px;color:#8a8aaa;text-align:center;}</style>';
    html += '</head><body>';
    html += '<h1>&#127774; Ihre persönliche Energieanalyse</h1>';
    html += '<p style="color:#4a4a6a;">Erstellt am ' + new Date().toLocaleDateString('de-DE') + ' mit dem Werdu.de Solarbatterie-Rechner</p>';

    html += '<div class="kpi">';
    html += '<div class="kpi-item"><div class="kpi-val">' + document.getElementById('wr5-res-ersparnis').textContent + '</div><div class="kpi-lab">Ersparnis / Jahr</div></div>';
    html += '<div class="kpi-item"><div class="kpi-val">' + document.getElementById('wr5-res-autarkie').textContent + '</div><div class="kpi-lab">Autarkiegrad</div></div>';
    html += '<div class="kpi-item"><div class="kpi-val">' + document.getElementById('wr5-res-eigen').textContent + '</div><div class="kpi-lab">Eigenverbrauch</div></div>';
    html += '<div class="kpi-item"><div class="kpi-val">' + document.getElementById('wr5-res-amort').textContent + '</div><div class="kpi-lab">Amortisation</div></div>';
    html += '</div>';

    html += '<h2>&#128203; Ihre Eingaben</h2>';
    html += '<table><tr><th>Parameter</th><th>Wert</th></tr>';
    html += '<tr><td>Postleitzahl</td><td>' + document.getElementById('wr5-plz').value + '</td></tr>';
    html += '<tr><td>Haushaltsgröße</td><td>' + document.getElementById('wr5-personen').value + ' Personen</td></tr>';
    html += '<tr><td>Jährlicher Verbrauch</td><td>' + document.getElementById('wr5-verbrauch').value + ' kWh</td></tr>';
    html += '<tr><td>PV-Leistung</td><td>' + document.getElementById('wr5-pv').value + ' kWp</td></tr>';
    html += '<tr><td>Speichergröße</td><td>' + document.getElementById('wr5-speicher').value + ' kWh</td></tr>';
    html += '<tr><td>Strompreis</td><td>' + document.getElementById('wr5-strompreis').value + ' ct/kWh</td></tr>';
    html += '</table>';

    var prodName = document.getElementById('wr5-prod-name').textContent;
    var prodPrice = document.getElementById('wr5-prod-price').textContent;
    html += '<h2>&#128267; Empfohlenes System</h2>';
    html += '<p><strong>' + prodName + '</strong></p>';
    html += '<p style="font-size:24px;font-weight:800;color:#FF6600;">' + prodPrice + '</p>';

    html += '<h2>&#127793; Umweltbilanz</h2>';
    html += '<p><strong>' + document.getElementById('wr5-co2-tonnen').textContent + ' kg CO₂</strong> eingespart pro Jahr</p>';

    html += '<div class="footer">';
    html += '<p><strong>Hinweis:</strong> Alle Berechnungen basieren auf DWD-Solardaten und VDI 4655. Die Ergebnisse dienen der Orientierung.</p>';
    html += '<p>&copy; 2026 Werdu.de | Solarbatterie-Rechner v3</p>';
    html += '<p><?php echo esc_url( home_url( '/solarbatterie-rechner/' ) ); ?></p>';
    html += '</div>';
    html += '</body></html>';

    var blob = new Blob([html], {type: 'text/html'});
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'Werdu-Energieanalyse-' + document.getElementById('wr5-plz').value + '.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  }

  /* ============================================
     INIT
     ============================================ */

  function init() {
    initSlider();
    initPLZ();
    initOptionCards();
    initMouseTracking();
    initInputListeners();
    initObserver();

    var saved = loadFromSession();
    if (saved && saved.hasResult) {
      try {
        var savedResult = sessionStorage.getItem('wr5_calc_result');
        if (savedResult) {
          lastCalcResult = JSON.parse(savedResult);
        }
      } catch (e) {}
    }

    window.wr5 = {
      goToStep: goToStep,
      nextStep: nextStep,
      prevStep: prevStep,
      selectOpt: selectOpt,
      calculate: calculate,
      generatePDF: generatePDF
    };
  }

  /* ============================================
     HISTORY / BACK BUTTON
     ============================================ */

  window.addEventListener('popstate', function(e) {
    if (e.state && e.state.wr5step) {
      goToStep(e.state.wr5step);
    } else if (window.location.hash === '#ergebnis') {
      var saved = loadFromSession();
      if (saved && saved.hasResult) {
        try {
          var savedResult = sessionStorage.getItem('wr5_calc_result');
          if (savedResult) {
            lastCalcResult = JSON.parse(savedResult);
            renderResults(lastCalcResult);
            goToStep(4);
            return;
          }
        } catch (e) {}
      }
      goToStep(1);
    } else {
      goToStep(1);
    }
  });

  if (window.location.hash === '#ergebnis') {
    setTimeout(function() {
      var saved = loadFromSession();
      if (saved && saved.hasResult) {
        try {
          var savedResult = sessionStorage.getItem('wr5_calc_result');
          if (savedResult) {
            lastCalcResult = JSON.parse(savedResult);
            renderResults(lastCalcResult);
            goToStep(4);
            return;
          }
        } catch (e) {}
      }
      goToStep(1);
    }, 300);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
</script>

<?php get_footer(); ?>