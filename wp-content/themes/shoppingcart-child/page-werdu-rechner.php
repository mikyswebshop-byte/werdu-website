<?php
/**
 * Template Name: Werdu Speicher-Rechner
 * Description: Interaktiver Heimspeicher-Rechner mit PVGIS-Integration
 */
?><!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Heimspeicher-Rechner | Werdu.de</title>
<?php wp_head(); ?>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: #0a0e1a;
    color: #fff;
    min-height: 100vh;
    line-height: 1.5;
}
.app-container {
    max-width: 480px;
    margin: 0 auto;
    background: #0f1629;
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}

/* Header */
.app-header {
    background: linear-gradient(135deg, #1a5276 0%, #0d2137 100%);
    padding: 25px 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255,102,0,0.3);
}
.app-logo {
    width: 55px; height: 55px;
    background: #ff6600;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 22px;
    margin-bottom: 10px;
    box-shadow: 0 4px 15px rgba(255,102,0,0.3);
}
.app-title { font-size: 20px; font-weight: 700; color: #fff; }
.app-subtitle { font-size: 12px; color: #8b9dc3; margin-top: 4px; }

/* Sections */
.section {
    margin: 15px;
    background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%);
    border-radius: 16px;
    padding: 20px;
    border: 1px solid rgba(255,102,0,0.15);
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}
.section:nth-child(2) { animation-delay: 0.1s; }
.section:nth-child(3) { animation-delay: 0.2s; }
.section:nth-child(4) { animation-delay: 0.3s; }
.section:nth-child(5) { animation-delay: 0.4s; }
.section:nth-child(6) { animation-delay: 0.5s; }

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.section-title {
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.section-title .icon { font-size: 18px; }

/* Input Groups */
.input-group { margin-bottom: 18px; }
.input-group:last-child { margin-bottom: 0; }
.input-label {
    font-size: 12px;
    color: #8b9dc3;
    margin-bottom: 8px;
    display: flex;
    justify-content: space-between;
}
.input-label .value {
    color: #ff6600;
    font-weight: 700;
}

/* Text Input */
.text-input {
    width: 100%;
    padding: 12px 15px;
    background: rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    color: #fff;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.3s;
}
.text-input:focus {
    outline: none;
    border-color: #ff6600;
    box-shadow: 0 0 0 3px rgba(255,102,0,0.15);
}
.text-input::placeholder { color: #5a6a8a; }

/* Slider */
.slider-container { position: relative; }
input[type="range"] {
    -webkit-appearance: none;
    width: 100%;
    height: 6px;
    border-radius: 3px;
    background: rgba(255,255,255,0.1);
    outline: none;
    margin: 10px 0;
}
input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #ff6600;
    cursor: pointer;
    box-shadow: 0 2px 10px rgba(255,102,0,0.4);
    transition: transform 0.2s;
}
input[type="range"]::-webkit-slider-thumb:hover { transform: scale(1.2); }
input[type="range"]::-moz-range-thumb {
    width: 22px; height: 22px;
    border-radius: 50%;
    background: #ff6600;
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 10px rgba(255,102,0,0.4);
}
.slider-labels {
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    color: #5a6a8a;
    margin-top: 4px;
}

/* Toggle */
.toggle-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
}
.toggle-row .label { font-size: 13px; color: #b8c5d9; }
.toggle-row .desc { font-size: 10px; color: #5a6a8a; }
.toggle-switch {
    position: relative;
    width: 50px; height: 28px;
    background: rgba(255,255,255,0.1);
    border-radius: 14px;
    cursor: pointer;
    transition: background 0.3s;
    flex-shrink: 0;
}
.toggle-switch.active { background: #27ae60; }
.toggle-knob {
    position: absolute;
    top: 3px; left: 3px;
    width: 22px; height: 22px;
    background: #fff;
    border-radius: 50%;
    transition: transform 0.3s;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
.toggle-switch.active .toggle-knob { transform: translateX(22px); }

/* Results Card */
.results-card {
    margin: 15px;
    background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
    border-radius: 16px;
    padding: 20px;
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.6s ease 0.4s forwards;
    opacity: 0;
}
.results-card::before {
    content: '€';
    position: absolute;
    right: -15px; top: -30px;
    font-size: 120px;
    opacity: 0.08;
    font-weight: 900;
}
.results-title {
    font-size: 12px;
    opacity: 0.9;
    margin-bottom: 5px;
    position: relative;
}
.results-big {
    font-size: 36px;
    font-weight: 800;
    position: relative;
}
.results-sub {
    font-size: 12px;
    opacity: 0.85;
    margin-top: 4px;
    position: relative;
}
.results-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid rgba(255,255,255,0.2);
    position: relative;
}
.results-item { text-align: center; }
.results-item-value { font-size: 18px; font-weight: 700; }
.results-item-label { font-size: 9px; opacity: 0.8; margin-top: 2px; }

/* Progress Bars */
.progress-section { margin-top: 15px; }
.progress-row { margin-bottom: 12px; }
.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: #b8c5d9;
    margin-bottom: 5px;
}
.progress-bar-bg {
    height: 8px;
    background: rgba(0,0,0,0.3);
    border-radius: 4px;
    overflow: hidden;
}
.progress-bar-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 1s ease;
    background: linear-gradient(90deg, #ff6600, #ff9933);
}
.progress-bar-fill.green { background: linear-gradient(90deg, #27ae60, #2ecc71); }
.progress-bar-fill.purple { background: linear-gradient(90deg, #9b59b6, #bb8fce); }

/* Product Cards */
.product-option {
    margin-bottom: 12px;
    padding: 15px;
    background: rgba(255,255,255,0.05);
    border-radius: 12px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
}
.product-option:hover { background: rgba(255,255,255,0.08); }
.product-option.selected {
    border-color: #ff6600;
    background: rgba(255,102,0,0.1);
}
.product-option .badge {
    position: absolute;
    top: -8px; right: 10px;
    background: #ff6600;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 10px;
}
.product-option-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.product-option-name { font-size: 14px; font-weight: 700; }
.product-option-price { font-size: 18px; font-weight: 800; color: #ff6600; }
.product-option-desc { font-size: 11px; color: #8b9dc3; }
.product-option-specs {
    display: flex;
    gap: 12px;
    margin-top: 10px;
    font-size: 10px;
    color: #5a6a8a;
}

/* CTA Button */
.cta-button {
    display: block;
    width: calc(100% - 30px);
    margin: 0 15px 15px;
    padding: 16px;
    background: linear-gradient(135deg, #ff6600 0%, #ff8533 100%);
    color: #fff;
    border: none;
    border-radius: 14px;
    font-size: 16px;
    font-weight: 700;
    text-align: center;
    cursor: pointer;
    text-decoration: none;
    box-shadow: 0 4px 20px rgba(255,102,0,0.3);
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
}
.cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(255,102,0,0.4);
}
.cta-button:active { transform: translateY(0); }
.cta-button .sub {
    display: block;
    font-size: 11px;
    font-weight: 400;
    opacity: 0.9;
    margin-top: 3px;
}
.cta-button.pulse {
    animation: ctaPulse 2s infinite;
}
@keyframes ctaPulse {
    0%, 100% { box-shadow: 0 4px 20px rgba(255,102,0,0.3); }
    50% { box-shadow: 0 4px 30px rgba(255,102,0,0.6); }
}

/* Comparison */
.compare-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
    margin-top: 10px;
}
.compare-table th {
    text-align: left;
    padding: 8px 5px;
    color: #8b9dc3;
    font-weight: 600;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.compare-table td {
    padding: 8px 5px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    color: #b8c5d9;
}
.compare-table td:last-child { color: #27ae60; font-weight: 700; }
.compare-table tr:last-child td { border-bottom: none; }

/* Loading */
.loading-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15,22,41,0.95);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    transition: opacity 0.3s;
}
.loading-overlay.hidden { opacity: 0; pointer-events: none; }
.spinner {
    width: 40px; height: 40px;
    border: 3px solid rgba(255,102,0,0.2);
    border-top-color: #ff6600;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.loading-text { margin-top: 12px; font-size: 13px; color: #8b9dc3; }

/* Error/Success messages */
.msg {
    margin: 0 15px 15px;
    padding: 12px 15px;
    border-radius: 10px;
    font-size: 12px;
    display: none;
}
.msg.show { display: block; }
.msg.error { background: rgba(231,76,60,0.2); border: 1px solid rgba(231,76,60,0.3); color: #e74c3c; }
.msg.success { background: rgba(39,174,96,0.2); border: 1px solid rgba(39,174,96,0.3); color: #27ae60; }

/* Footer */
.footer-note {
    text-align: center;
    padding: 20px;
    font-size: 10px;
    color: #5a6a8a;
}

@media (min-width: 481px) {
    body { background: #1a1a2e; padding: 20px; }
    .app-container { border-radius: 20px; min-height: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.5); }
}
</style>
</head>
<body>

<div class="app-container">
    <!-- Loading -->
    <div class="loading-overlay hidden" id="loadingOverlay">
        <div class="spinner"></div>
        <div class="loading-text">Berechne Ertragsdaten...</div>
    </div>

    <!-- Header -->
    <div class="app-header">
        <div class="app-logo">⚡</div>
        <div class="app-title">Ihr persönlicher Speicher-Rechner</div>
        <div class="app-subtitle">Berechnen Sie Ihre Einsparung in 60 Sekunden</div>
    </div>

    <div id="msgBox" class="msg"></div>

    <!-- Step 1: Location -->
    <div class="section">
        <div class="section-title"><span class="icon">📍</span> Ihr Standort</div>
        <div class="input-group">
            <div class="input-label">Postleitzahl <span class="value" id="plzValue"></span></div>
            <input type="text" class="text-input" id="plzInput" placeholder="z.B. 10115" maxlength="5" value="10115">
        </div>
        <div class="input-group">
            <div class="input-label">Jährlicher Stromverbrauch <span class="value" id="verbrauchValue">4.000 kWh</span></div>
            <div class="slider-container">
                <input type="range" id="verbrauchSlider" min="1500" max="15000" step="100" value="4000">
                <div class="slider-labels"><span>1.500</span><span>15.000 kWh</span></div>
            </div>
        </div>
        <div class="input-group">
            <div class="input-label">Aktueller Strompreis <span class="value" id="preisValue">35 ct/kWh</span></div>
            <div class="slider-container">
                <input type="range" id="preisSlider" min="20" max="60" step="1" value="35">
                <div class="slider-labels"><span>20 ct</span><span>60 ct/kWh</span></div>
            </div>
        </div>
    </div>

    <!-- Step 2: PV -->
    <div class="section">
        <div class="section-title"><span class="icon">☀️</span> Ihre PV-Anlage</div>
        <div class="toggle-row">
            <div>
                <div class="label">Haben Sie bereits Solaranlage?</div>
                <div class="desc">Ja = Erweiterung | Nein = Komplettsystem</div>
            </div>
            <div class="toggle-switch" id="hatPVSwitch">
                <div class="toggle-knob"></div>
            </div>
        </div>
        <div class="input-group" id="pvGroup" style="margin-top:15px;">
            <div class="input-label">PV-Leistung <span class="value" id="pvValue">8 kWp</span></div>
            <div class="slider-container">
                <input type="range" id="pvSlider" min="3" max="30" step="0.5" value="8">
                <div class="slider-labels"><span>3 kWp</span><span>30 kWp</span></div>
            </div>
        </div>
    </div>

    <!-- Step 3: Results Preview -->
    <div class="results-card" id="resultsCard" style="display:none;">
        <div class="results-title">💰 Ihre jährliche Einsparung</div>
        <div class="results-big" id="jahresErsparnis">-- €</div>
        <div class="results-sub">mit optimalem Heimspeicher</div>
        <div class="results-grid">
            <div class="results-item">
                <div class="results-item-value" id="eigenverbrauchRate">--%</div>
                <div class="results-item-label">Eigenverbrauch</div>
            </div>
            <div class="results-item">
                <div class="results-item-value" id="autarkieRate">--%</div>
                <div class="results-item-label">Autarkiegrad</div>
            </div>
            <div class="results-item">
                <div class="results-item-value" id="amortisation">-- Jahre</div>
                <div class="results-item-label">Amortisation</div>
            </div>
        </div>
    </div>

    <!-- Progress Visualization -->
    <div class="section" id="vizSection" style="display:none;">
        <div class="section-title"><span class="icon">📊</span> Ihre Energiebilanz</div>
        <div class="progress-section">
            <div class="progress-row">
                <div class="progress-label"><span>Ohne Speicher (Eigenverbrauch)</span><span id="ohneSpeicherPct">30%</span></div>
                <div class="progress-bar-bg"><div class="progress-bar-fill" id="ohneSpeicherBar" style="width:30%"></div></div>
            </div>
            <div class="progress-row">
                <div class="progress-label"><span>Mit Werdu Speicher (Eigenverbrauch)</span><span id="mitSpeicherPct">75%</span></div>
                <div class="progress-bar-bg"><div class="progress-bar-fill green" id="mitSpeicherBar" style="width:75%"></div></div>
            </div>
            <div class="progress-row">
                <div class="progress-label"><span>Autarkiegrad</span><span id="autarkiePct">65%</span></div>
                <div class="progress-bar-bg"><div class="progress-bar-fill purple" id="autarkieBar" style="width:65%"></div></div>
            </div>
        </div>
        <div style="margin-top:15px; padding-top:15px; border-top:1px solid rgba(255,255,255,0.1);">
            <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:6px;">
                <span style="color:#8b9dc3">Jährliche PV-Erzeugung:</span>
                <span style="font-weight:700" id="pvErzeugung">-- kWh</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:6px;">
                <span style="color:#8b9dc3">Direktverbrauch + Speicher:</span>
                <span style="font-weight:700; color:#27ae60" id="nutzungKwh">-- kWh</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:12px;">
                <span style="color:#8b9dc3">Eingesparte CO₂:</span>
                <span style="font-weight:700; color:#27ae60" id="co2Saved">-- kg</span>
            </div>
        </div>
    </div>

    <!-- Step 4: Product Recommendation -->
    <div class="section" id="productSection" style="display:none;">
        <div class="section-title"><span class="icon">🔋</span> Empfohlene Speicher</div>
        <div id="productList"></div>
    </div>

    <!-- Comparison -->
    <div class="section">
        <div class="section-title"><span class="icon">⚖️</span> Vergleich: Mit vs. Ohne Speicher</div>
        <table class="compare-table">
            <tr><th></th><th>Ohne Speicher</th><th>Mit Werdu</th></tr>
            <tr><td>Eigenverbrauch</td><td>~30%</td><td>bis 85%</td></tr>
            <tr><td>Netzbezug</td><td>Hoher Bedarf</td><td>Minimal</td></tr>
            <tr><td>Stromkosten/Jahr</td><td id="kostenOhne">-- €</td><td id="kostenMit" style="color:#27ae60">-- €</td></tr>
            <tr><td>10-Jahres-Ersparnis</td><td>--</td><td id="ersparnis10J">-- €</td></tr>
        </table>
    </div>

    <!-- CTA -->
    <a href="#" class="cta-button pulse" id="ctaButton" style="display:none;">
        Jetzt Speicher konfigurieren
        <span class="sub">Kostenloser Versand · 10 Jahre Garantie</span>
    </a>

    <div class="footer-note">
        Berechnung basiert auf PVGIS-Daten der EU-Kommission.<br>
        Ergebnisse sind Schätzungen. Individuelle Abweichungen möglich.
    </div>
</div>

<script>
// ============================================
// WERDU SPEICHER-RECHNER
// ============================================

const CONFIG = {
    pvgisProxy: '<?php echo esc_url(home_url("/pvgis-proxy.php")); ?>',
    products: [
        {
            id: 1,
            name: 'Tewaycell 16 kWh',
            subtitle: 'Für bestehende Anlagen',
            price: 2599,
            kwh: 16,
            peakpower: 5,
            url: '<?php echo esc_url(home_url("/produkt/tewaycell-16kwh/")); ?>',
            ideal: 'Bis 5.000 kWh Verbrauch'
        },
        {
            id: 2,
            name: 'Tewaycell 15 kWh All-in-One',
            subtitle: 'Inkl. 5 kW Wechselrichter',
            price: 2899,
            kwh: 15,
            peakpower: 5,
            url: '<?php echo esc_url(home_url("/produkt/tewaycell-15kwh-all-in-one/")); ?>',
            ideal: 'Neueinsteiger & Komplettsystem',
            badge: 'Bestseller'
        },
        {
            id: 3,
            name: 'Tewaycell 30-32 kWh',
            subtitle: 'Maximale Autarkie',
            price: 3499,
            kwh: 32,
            peakpower: 10,
            url: '<?php echo esc_url(home_url("/produkt/tewaycell-30kwh/")); ?>',
            ideal: 'Ab 6.000 kWh Verbrauch'
        }
    ]
};

// State
let state = {
    plz: '10115',
    verbrauch: 4000,
    preis: 35,
    hatPV: false,
    pvKwp: 8,
    pvgisData: null,
    selectedProduct: null
};

// PVGIS cache per PLZ
const pvgisCache = {};

// ============================================
// DOM ELEMENTS
// ============================================
const els = {
    plzInput: document.getElementById('plzInput'),
    verbrauchSlider: document.getElementById('verbrauchSlider'),
    verbrauchValue: document.getElementById('verbrauchValue'),
    preisSlider: document.getElementById('preisSlider'),
    preisValue: document.getElementById('preisValue'),
    hatPVSwitch: document.getElementById('hatPVSwitch'),
    pvSlider: document.getElementById('pvSlider'),
    pvValue: document.getElementById('pvValue'),
    pvGroup: document.getElementById('pvGroup'),
    resultsCard: document.getElementById('resultsCard'),
    vizSection: document.getElementById('vizSection'),
    productSection: document.getElementById('productSection'),
    productList: document.getElementById('productList'),
    ctaButton: document.getElementById('ctaButton'),
    loading: document.getElementById('loadingOverlay'),
    msgBox: document.getElementById('msgBox'),
    // Result fields
    jahresErsparnis: document.getElementById('jahresErsparnis'),
    eigenverbrauchRate: document.getElementById('eigenverbrauchRate'),
    autarkieRate: document.getElementById('autarkieRate'),
    amortisation: document.getElementById('amortisation'),
    ohneSpeicherPct: document.getElementById('ohneSpeicherPct'),
    ohneSpeicherBar: document.getElementById('ohneSpeicherBar'),
    mitSpeicherPct: document.getElementById('mitSpeicherPct'),
    mitSpeicherBar: document.getElementById('mitSpeicherBar'),
    autarkiePct: document.getElementById('autarkiePct'),
    autarkieBar: document.getElementById('autarkieBar'),
    pvErzeugung: document.getElementById('pvErzeugung'),
    nutzungKwh: document.getElementById('nutzungKwh'),
    co2Saved: document.getElementById('co2Saved'),
    kostenOhne: document.getElementById('kostenOhne'),
    kostenMit: document.getElementById('kostenMit'),
    ersparnis10J: document.getElementById('ersparnis10J')
};

// ============================================
// HELPERS
// ============================================
function formatMoney(n) {
    return n.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' €';
}
function showMsg(text, type) {
    els.msgBox.textContent = text;
    els.msgBox.className = 'msg ' + type + ' show';
    setTimeout(function() { els.msgBox.classList.remove('show'); }, 4000);
}
function setLoading(show) {
    els.loading.classList.toggle('hidden', !show);
}

// ============================================
// PVGIS FETCH
// ============================================
async function fetchPVGIS(plz) {
    if (pvgisCache[plz]) return pvgisCache[plz];

    // PLZ naar coördinaten (vereenvoudigde mapping voor Duitsland)
    var coords = plzToCoords(plz);
    if (!coords) {
        showMsg('PLZ nicht erkannt. Verwende Berlin als Standard.', 'error');
        coords = { lat: 52.52, lon: 13.405 };
    }

    try {
        setLoading(true);
        var params = new URLSearchParams({
            lat: coords.lat, lon: coords.lon,
            peakpower: 1, loss: 14,
            mountingplace: 'building', angle: 30, aspect: 0,
            outputformat: 'json'
        });
        var response = await fetch(CONFIG.pvgisProxy + '?' + params.toString(), { timeout: 8000 });
        if (!response.ok) throw new Error('HTTP ' + response.status);
        var data = await response.json();
        pvgisCache[plz] = data;
        setLoading(false);
        return data;
    } catch (e) {
        setLoading(false);
        console.error('PVGIS error:', e);
        // Fallback: standaard Duitsland gemiddelde ~950 kWh/kWp/jaar
        return { outputs: { totals: { fixed: { E_y: 950 } } } };
    }
}

function plzToCoords(plz) {
    // Vereenvoudigde mapping voor grote Duitse steden
    var map = {
        '1': { lat: 52.52, lon: 13.405 }, // Berlin
        '2': { lat: 53.55, lon: 9.99 },   // Hamburg
        '3': { lat: 52.37, lon: 9.73 },   // Hannover
        '4': { lat: 51.23, lon: 6.78 },   // Düsseldorf
        '5': { lat: 50.94, lon: 6.96 },   // Köln
        '6': { lat: 50.11, lon: 8.68 },   // Frankfurt
        '7': { lat: 48.78, lon: 9.18 },   // Stuttgart
        '8': { lat: 48.14, lon: 11.58 },  // München
        '9': { lat: 49.45, lon: 11.08 }   // Nürnberg
    };
    var first = plz.charAt(0);
    return map[first] || { lat: 51.3, lon: 9.5 }; // Centrum Duitsland fallback
}

// ============================================
// BEREKENING
// ============================================
function bereken() {
    var s = state;

    // PV opbrengst (kWh/jaar)
    var ertragPerKwp = (s.pvgisData && s.pvgisData.outputs && s.pvgisData.outputs.totals && s.pvgisData.outputs.totals.fixed)
        ? s.pvgisData.outputs.totals.fixed.E_y : 950;
    var pvErzeugung = s.pvKwp * ertragPerKwp;

    // Eigenverbruik zonder batterij: ~30% van opbrengst
    var eigenverbrauchOhne = Math.min(pvErzeugung * 0.30, s.verbrauch);
    var einspeisungOhne = pvErzeugung - eigenverbrauchOhne;
    var netzbezugOhne = s.verbrauch - eigenverbrauchOhne;

    // Met batterij: afhankelijk van batterijgrootte
    var empfohleneKwh = s.verbrauch <= 4500 ? 16 : (s.verbrauch <= 7000 ? 15 : 32);
    var speicherKwh = empfohleneKwh;

    // Dagelijkse cyclus: batterij vult overdag, leegt 's avonds
    // Max eigenverbruik = min(verbruik, pvErzeuging * factor)
    var speicherNutzung = Math.min(speicherKwh * 300, pvErzeugung * 0.50); // 300 cycli/jaar
    var eigenverbrauchMit = Math.min(eigenverbrauchOhne + speicherNutzung, s.verbrauch * 0.85);
    var evRate = Math.min((eigenverbrauchMit / pvErzeugung) * 100, 95);
    var autarkie = Math.min((eigenverbrauchMit / s.verbrauch) * 100, 95);

    // Kosten
    var kostenOhne = netzbezugOhne * (s.preis / 100);
    var netzbezugMit = s.verbrauch - eigenverbrauchMit;
    var kostenMit = netzbezugMit * (s.preis / 100);
    var ersparnis = kostenOhne - kostenMit;
    var ersparnis10J = ersparnis * 10;

    // Amortisatie
    var produkt = CONFIG.products.find(function(p) {
        if (s.verbrauch <= 4500) return p.kwh === 16;
        if (s.verbrauch <= 7000) return p.kwh === 15;
        return p.kwh === 32;
    }) || CONFIG.products[1];
    var amort = produkt.price / ersparnis;

    // CO2
    var co2 = eigenverbrauchMit * 0.4; // ~400g CO2/kWh vermeden

    return {
        pvErzeugung: pvErzeugung,
        eigenverbrauchOhne: eigenverbrauchOhne,
        eigenverbrauchMit: eigenverbrauchMit,
        evRate: evRate,
        autarkie: autarkie,
        ersparnis: ersparnis,
        ersparnis10J: ersparnis10J,
        kostenOhne: kostenOhne,
        kostenMit: kostenMit,
        amortisation: amort,
        co2: co2,
        produkt: produkt,
        speicherKwh: speicherKwh
    };
}

// ============================================
// UI UPDATE
// ============================================
function updateUI() {
    var r = bereken();

    // Results card
    els.resultsCard.style.display = 'block';
    els.jahresErsparnis.textContent = formatMoney(r.ersparnis);
    els.eigenverbrauchRate.textContent = r.evRate.toFixed(0) + '%';
    els.autarkieRate.textContent = r.autarkie.toFixed(0) + '%';
    els.amortisation.textContent = (r.amortisation < 20 ? r.amortisation.toFixed(1) : '>20') + ' J.';

    // Viz
    els.vizSection.style.display = 'block';
    els.ohneSpeicherPct.textContent = '30%';
    els.ohneSpeicherBar.style.width = '30%';
    els.mitSpeicherPct.textContent = r.evRate.toFixed(0) + '%';
    els.mitSpeicherBar.style.width = Math.min(r.evRate, 100) + '%';
    els.autarkiePct.textContent = r.autarkie.toFixed(0) + '%';
    els.autarkieBar.style.width = Math.min(r.autarkie, 100) + '%';
    els.pvErzeugung.textContent = r.pvErzeugung.toFixed(0) + ' kWh';
    els.nutzungKwh.textContent = r.eigenverbrauchMit.toFixed(0) + ' kWh';
    els.co2Saved.textContent = r.co2.toFixed(0) + ' kg';

    // Comparison
    els.kostenOhne.textContent = formatMoney(r.kostenOhne);
    els.kostenMit.textContent = formatMoney(r.kostenMit);
    els.ersparnis10J.textContent = formatMoney(r.ersparnis10J);

    // Products
    renderProducts(r);

    // CTA
    els.ctaButton.style.display = 'block';
    els.ctaButton.href = r.produkt.url;
}

function renderProducts(r) {
    els.productSection.style.display = 'block';
    els.productList.innerHTML = '';

    CONFIG.products.forEach(function(produkt, idx) {
        var isRecommended = produkt.id === r.produkt.id;
        var div = document.createElement('div');
        div.className = 'product-option' + (isRecommended ? ' selected' : '');
        div.innerHTML = 
            (isRecommended ? '<div class="badge">EMPFOHLEN</div>' : '') +
            '<div class="product-option-header">' +
            '<div class="product-option-name">' + produkt.name + '</div>' +
            '<div class="product-option-price">' + produkt.price.toLocaleString('de-DE') + ' €</div>' +
            '</div>' +
            '<div class="product-option-desc">' + produkt.subtitle + ' · Ideal: ' + produkt.ideal + '</div>' +
            '<div class="product-option-specs">' +
            '<span>🔋 ' + produkt.kwh + ' kWh</span>' +
            '<span>⚡ bis ' + produkt.peakpower + ' kW</span>' +
            '<span>🚚 Kostenloser Versand</span>' +
            '</div>';

        div.addEventListener('click', function() {
            document.querySelectorAll('.product-option').forEach(function(p) { p.classList.remove('selected'); });
            div.classList.add('selected');
            state.selectedProduct = produkt;
            els.ctaButton.href = produkt.url;
        });

        els.productList.appendChild(div);
    });

    state.selectedProduct = r.produkt;
}

// ============================================
// EVENT LISTENERS
// ============================================
function init() {
    // PLZ
    els.plzInput.addEventListener('change', function() {
        state.plz = this.value;
        fetchPVGIS(state.plz).then(function(data) {
            state.pvgisData = data;
            updateUI();
        });
    });

    // Verbrauch
    els.verbrauchSlider.addEventListener('input', function() {
        state.verbrauch = parseInt(this.value);
        els.verbrauchValue.textContent = state.verbrauch.toLocaleString('de-DE') + ' kWh';
        updateUI();
    });

    // Preis
    els.preisSlider.addEventListener('input', function() {
        state.preis = parseInt(this.value);
        els.preisValue.textContent = state.preis + ' ct/kWh';
        updateUI();
    });

    // PV Toggle
    els.hatPVSwitch.addEventListener('click', function() {
        state.hatPV = !state.hatPV;
        this.classList.toggle('active', state.hatPV);
        els.pvGroup.style.display = state.hatPV ? 'block' : 'none';
        if (!state.hatPV) {
            // Auto PV sizing: ~1 kWp per 1000 kWh verbruik
            state.pvKwp = Math.max(5, Math.round(state.verbrauch / 1000));
            els.pvSlider.value = state.pvKwp;
            els.pvValue.textContent = state.pvKwp + ' kWp';
        }
        updateUI();
    });

    // PV Slider
    els.pvSlider.addEventListener('input', function() {
        state.pvKwp = parseFloat(this.value);
        els.pvValue.textContent = state.pvKwp + ' kWp';
        updateUI();
    });

    // Init
    fetchPVGIS(state.plz).then(function(data) {
        state.pvgisData = data;
        updateUI();
    });
}

// Start
document.addEventListener('DOMContentLoaded', init);
</script>

<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<?php wp_footer(); ?>
</body>
</html>