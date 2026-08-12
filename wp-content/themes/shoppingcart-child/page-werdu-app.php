<?php
/**
 * Template Name: Werdu App
 * Description: Fullscreen Werdu Energy App - geen WordPress header/footer
 */
?><!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Werdu App - Energiesteuerung v4</title>
<?php wp_head(); ?>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: #0a0e1a;
    color: #fff;
    min-height: 100vh;
}
.app-container {
    max-width: 420px;
    margin: 0 auto;
    background: #0f1629;
    min-height: 100vh;
    position: relative;
    overflow: hidden;
}
.app-header {
    background: linear-gradient(135deg, #1a5276 0%, #0d2137 100%);
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255,102,0,0.3);
}
.app-logo {
    width: 50px; height: 50px;
    background: #ff6600;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 20px;
    margin-bottom: 8px;
}
.app-title { font-size: 18px; font-weight: 700; color: #fff; }
.app-subtitle { font-size: 11px; color: #8b9dc3; margin-top: 2px; }

.loading-overlay {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: #0f1629; display: flex; flex-direction: column;
    align-items: center; justify-content: center; z-index: 1000;
    transition: opacity 0.5s;
}
.loading-overlay.hidden { opacity: 0; pointer-events: none; }
.spinner {
    width: 50px; height: 50px;
    border: 3px solid rgba(255,102,0,0.2);
    border-top-color: #ff6600;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.loading-text { margin-top: 15px; font-size: 13px; color: #8b9dc3; }
.loading-sub { margin-top: 8px; font-size: 10px; color: #5a6a8a; }

.debug-console {
    margin: 10px 15px; padding: 10px;
    background: rgba(0,0,0,0.5); border-radius: 8px;
    font-family: monospace; font-size: 10px; color: #8b9dc3;
    max-height: 100px; overflow-y: auto;
    border: 1px solid rgba(255,102,0,0.2);
}
.debug-console .ok { color: #27ae60; }
.debug-console .err { color: #e74c3c; }
.debug-console .warn { color: #f1c40f; }

.plant-selector {
    margin: 15px;
    background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%);
    border-radius: 16px; padding: 15px;
    border: 1px solid rgba(255,102,0,0.2);
}
.plant-selector-title {
    font-size: 11px; color: #8b9dc3;
    text-transform: uppercase; letter-spacing: 1px;
    margin-bottom: 10px;
}
.plant-list { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px; }
.plant-chip {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 14px; background: rgba(255,255,255,0.05);
    border-radius: 20px; border: 2px solid transparent;
    cursor: pointer; white-space: nowrap;
    transition: all 0.3s; font-size: 12px;
}
.plant-chip.active { background: rgba(255,102,0,0.2); border-color: #ff6600; }
.plant-chip-status { width: 8px; height: 8px; border-radius: 50%; background: #27ae60; }
.plant-chip-status.offline { background: #e74c3c; }

.status-card {
    margin: 15px;
    background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%);
    border-radius: 16px; padding: 20px;
    border: 1px solid rgba(255,102,0,0.2);
}
.status-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
.status-label { font-size: 12px; color: #8b9dc3; text-transform: uppercase; letter-spacing: 1px; }
.status-live { display: flex; align-items: center; gap: 6px; font-size: 11px; color: #27ae60; }
.live-dot { width: 8px; height: 8px; background: #27ae60; border-radius: 50%; animation: pulse 2s infinite; }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

.battery-display { text-align: center; margin: 20px 0; }
.battery-circle { width: 160px; height: 160px; margin: 0 auto; position: relative; }
.battery-circle svg { transform: rotate(-90deg); }
.battery-bg { fill: none; stroke: rgba(255,255,255,0.1); stroke-width: 8; }
.battery-fill {
    fill: none; stroke: url(#batteryGradient); stroke-width: 8;
    stroke-linecap: round; stroke-dasharray: 440; stroke-dashoffset: 96;
    transition: stroke-dashoffset 1s ease;
}
.battery-text { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; }
.battery-percent { font-size: 36px; font-weight: 800; color: #fff; }
.battery-label { font-size: 11px; color: #8b9dc3; }

.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 15px; }
.stat-box { background: rgba(255,255,255,0.05); border-radius: 10px; padding: 12px 8px; text-align: center; }
.stat-icon { font-size: 18px; margin-bottom: 4px; }
.stat-value { font-size: 16px; font-weight: 700; color: #fff; }
.stat-unit { font-size: 10px; color: #8b9dc3; }
.stat-label { font-size: 9px; color: #5a6a8a; margin-top: 2px; }

.product-card {
    margin: 15px;
    background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%);
    border-radius: 16px; padding: 20px;
    border: 1px solid rgba(255,102,0,0.2);
}
.product-header { display: flex; gap: 15px; margin-bottom: 15px; }
.product-image {
    width: 80px; height: 80px; background: rgba(255,255,255,0.05);
    border-radius: 10px; display: flex; align-items: center;
    justify-content: center; font-size: 30px; flex-shrink: 0;
}
.product-info { flex: 1; }
.product-name { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 4px; }
.product-price { font-size: 22px; font-weight: 800; color: #ff6600; }
.product-stock {
    display: inline-flex; align-items: center; gap: 5px;
    margin-top: 8px; padding: 4px 10px;
    background: rgba(39,174,96,0.2); border-radius: 20px;
    font-size: 11px; color: #27ae60;
}
.product-stock.low { background: rgba(231,76,60,0.2); color: #e74c3c; }
.product-stock.out { background: rgba(149,165,166,0.2); color: #95a5a6; }

.power-flow {
    margin: 15px;
    background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%);
    border-radius: 16px; padding: 20px;
    border: 1px solid rgba(255,102,0,0.2);
}
.flow-title { font-size: 13px; font-weight: 600; margin-bottom: 15px; color: #fff; }
.flow-visual { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; }
.flow-node { text-align: center; flex: 1; }
.flow-icon {
    width: 50px; height: 50px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; margin: 0 auto 8px;
}
.flow-sun { background: rgba(255,193,7,0.2); }
.flow-battery { background: rgba(255,102,0,0.2); }
.flow-home { background: rgba(39,174,96,0.2); }
.flow-grid { background: rgba(155,89,182,0.2); }
.flow-name { font-size: 10px; color: #8b9dc3; }
.flow-value { font-size: 12px; font-weight: 700; color: #fff; margin-top: 2px; }
.flow-arrow { color: #27ae60; font-size: 18px; animation: flowMove 1.5s infinite; }
.flow-arrow.reverse { color: #e74c3c; animation: flowMoveReverse 1.5s infinite; }
@keyframes flowMove { 0%, 100% { transform: translateX(0); opacity: 1; } 50% { transform: translateX(5px); opacity: 0.5; } }
@keyframes flowMoveReverse { 0%, 100% { transform: translateX(0); opacity: 1; } 50% { transform: translateX(-5px); opacity: 0.5; } }

.savings-card {
    margin: 15px;
    background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
    border-radius: 16px; padding: 20px;
    position: relative; overflow: hidden;
}
.savings-card::before {
    content: '€'; position: absolute;
    right: -10px; top: -20px;
    font-size: 100px; opacity: 0.1; font-weight: 900;
}
.savings-title { font-size: 12px; opacity: 0.9; margin-bottom: 8px; }
.savings-amount { font-size: 32px; font-weight: 800; }
.savings-period { font-size: 11px; opacity: 0.8; margin-top: 4px; }
.savings-detail {
    display: flex; gap: 15px; margin-top: 15px;
    padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.2);
}
.savings-item { flex: 1; }
.savings-item-value { font-size: 16px; font-weight: 700; }
.savings-item-label { font-size: 9px; opacity: 0.8; }

.chart-section {
    margin: 15px;
    background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%);
    border-radius: 16px; padding: 20px;
    border: 1px solid rgba(255,102,0,0.2);
}
.chart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
.chart-tabs { display: flex; gap: 5px; }
.chart-tab {
    padding: 5px 12px; border-radius: 20px;
    font-size: 10px; font-weight: 600; cursor: pointer;
    background: rgba(255,255,255,0.05); color: #8b9dc3; border: none;
}
.chart-tab.active { background: #ff6600; color: #fff; }
.chart-canvas { height: 150px; position: relative; }
.chart-bars { display: flex; align-items: flex-end; justify-content: space-between; height: 120px; gap: 6px; }
.chart-bar {
    flex: 1; background: linear-gradient(to top, #ff6600, #ff9933);
    border-radius: 4px 4px 0 0; min-height: 10px;
    position: relative; transition: height 0.5s ease;
}
.chart-bar-grid { background: linear-gradient(to top, #9b59b6, #bb8fce); }
.chart-bar-label {
    position: absolute; bottom: -18px; left: 50%;
    transform: translateX(-50%); font-size: 8px;
    color: #5a6a8a; white-space: nowrap;
}
.chart-legend { display: flex; gap: 15px; margin-top: 25px; justify-content: center; }
.legend-item { display: flex; align-items: center; gap: 5px; font-size: 10px; color: #8b9dc3; }
.legend-dot { width: 8px; height: 8px; border-radius: 2px; }
.legend-dot.solar { background: #ff6600; }
.legend-dot.grid { background: #9b59b6; }

.ai-section {
    margin: 15px;
    background: linear-gradient(135deg, #2d1b4e 0%, #1a0f2e 100%);
    border-radius: 16px; padding: 20px;
    border: 1px solid rgba(155,89,182,0.3);
    position: relative; overflow: hidden;
}
.ai-section::before {
    content: ''; position: absolute;
    top: -50%; right: -50%; width: 200%; height: 200%;
    background: radial-gradient(circle, rgba(155,89,182,0.1) 0%, transparent 70%);
    animation: aiGlow 4s ease-in-out infinite;
}
@keyframes aiGlow {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.8; }
}
.ai-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(155,89,182,0.3); padding: 4px 12px;
    border-radius: 20px; font-size: 10px; font-weight: 700;
    color: #d4a5ff; margin-bottom: 12px;
    position: relative; z-index: 1;
}
.ai-title { font-size: 16px; font-weight: 700; margin-bottom: 8px; position: relative; z-index: 1; }
.ai-text { font-size: 12px; color: #b8a5d1; line-height: 1.5; position: relative; z-index: 1; }
.ai-scenario {
    margin-top: 15px; padding: 12px;
    background: rgba(0,0,0,0.3); border-radius: 10px;
    position: relative; z-index: 1;
}
.ai-scenario-title { font-size: 11px; font-weight: 700; color: #ff6600; margin-bottom: 8px; }
.ai-scenario-row {
    display: flex; justify-content: space-between;
    padding: 6px 0; font-size: 11px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.ai-scenario-row:last-child { border-bottom: none; font-weight: 700; color: #27ae60; }

.notif-section {
    margin: 15px;
    background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%);
    border-radius: 16px; padding: 20px;
    border: 1px solid rgba(255,102,0,0.2);
}
.notif-title { font-size: 14px; font-weight: 700; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
.notif-row {
    display: flex; justify-content: space-between;
    align-items: center; padding: 12px 0;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.notif-row:last-child { border-bottom: none; }
.notif-label { font-size: 12px; color: #b8c5d9; }
.notif-desc { font-size: 10px; color: #5a6a8a; margin-top: 2px; }
.toggle-switch {
    position: relative; width: 44px; height: 24px;
    background: rgba(255,255,255,0.1); border-radius: 12px;
    cursor: pointer; transition: background 0.3s; flex-shrink: 0;
}
.toggle-switch.active { background: #27ae60; }
.toggle-knob {
    position: absolute; top: 2px; left: 2px;
    width: 20px; height: 20px; background: #fff;
    border-radius: 50%; transition: transform 0.3s;
}
.toggle-switch.active .toggle-knob { transform: translateX(20px); }

.bottom-nav {
    position: fixed; bottom: 0; left: 50%;
    transform: translateX(-50%); width: 100%;
    max-width: 420px; background: rgba(15,22,41,0.95);
    backdrop-filter: blur(20px);
    border-top: 1px solid rgba(255,102,0,0.2);
    display: flex; justify-content: space-around;
    padding: 10px 0 20px; z-index: 100;
}
.nav-item {
    display: flex; flex-direction: column;
    align-items: center; gap: 3px;
    cursor: pointer; padding: 5px 15px;
}
.nav-item.active .nav-icon { color: #ff6600; }
.nav-item.active .nav-label { color: #ff6600; }
.nav-icon { font-size: 20px; color: #5a6a8a; transition: color 0.3s; }
.nav-label { font-size: 9px; color: #5a6a8a; transition: color 0.3s; }

.content-padding { padding-bottom: 80px; }
.error-message {
    margin: 15px; padding: 15px;
    background: rgba(231,76,60,0.2);
    border: 1px solid rgba(231,76,60,0.3);
    border-radius: 10px; font-size: 12px;
    color: #e74c3c; text-align: center;
}
.page-section { display: none; }
.page-section.active { display: block; }

@media (min-width: 421px) {
    body { background: #1a1a2e; padding: 20px; }
    .app-container { border-radius: 20px; min-height: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.5); }
    .bottom-nav { position: relative; border-radius: 0 0 20px 20px; transform: none; left: auto; }
}
</style>
</head>
<body>

<div class="app-container">
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <div class="loading-text">Daten werden geladen...</div>
        <div class="loading-sub" id="loadingSub">Initialisiere...</div>
    </div>

    <svg width="0" height="0">
        <defs>
            <linearGradient id="batteryGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" style="stop-color:#27ae60"/>
                <stop offset="50%" style="stop-color:#f1c40f"/>
                <stop offset="100%" style="stop-color:#ff6600"/>
            </linearGradient>
        </defs>
    </svg>

    <div class="app-header">
        <div class="app-logo">W</div>
        <div class="app-title">Werdu Energy App</div>
        <div class="app-subtitle" id="headerSubtitle">Verbindung wird hergestellt...</div>
    </div>

    <div class="debug-console" id="debugConsole" style="display:none;"></div>

    <div class="content-padding page-section active" id="pageHome">
        <div class="plant-selector">
            <div class="plant-selector-title">Ihre Anlagen</div>
            <div class="plant-list" id="plantList"></div>
        </div>

        <div class="product-card" id="productCard" style="display:none;">
            <div class="product-header">
                <div class="product-image" id="productImage">🔋</div>
                <div class="product-info">
                    <div class="product-name" id="productName">Lädt...</div>
                    <div class="product-price" id="productPrice">--</div>
                    <div class="product-stock" id="productStock">
                        <span>●</span>
                        <span id="stockText">Prüfe Verfügbarkeit...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="status-card">
            <div class="status-header">
                <span class="status-label">Aktueller Status</span>
                <div class="status-live">
                    <span class="live-dot"></span>
                    LIVE
                </div>
            </div>
            <div class="battery-display">
                <div class="battery-circle">
                    <svg width="160" height="160" viewBox="0 0 160 160">
                        <circle class="battery-bg" cx="80" cy="80" r="70"/>
                        <circle class="battery-fill" cx="80" cy="80" r="70" id="batteryCircle"/>
                    </svg>
                    <div class="battery-text">
                        <div class="battery-percent" id="batteryPercent">--%</div>
                        <div class="battery-label">SOC</div>
                    </div>
                </div>
            </div>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-icon">⚡</div>
                    <div class="stat-value" id="powerValue">--</div>
                    <div class="stat-unit">kW</div>
                    <div class="stat-label">Leistung</div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon">🔋</div>
                    <div class="stat-value" id="todayValue">--</div>
                    <div class="stat-unit">kWh</div>
                    <div class="stat-label">Heute gel.</div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon">🌡️</div>
                    <div class="stat-value" id="tempValue">--</div>
                    <div class="stat-unit">°C</div>
                    <div class="stat-label">Zellen</div>
                </div>
            </div>
        </div>

        <div class="power-flow">
            <div class="flow-title">Energiefluss in Echtzeit</div>
            <div class="flow-visual">
                <div class="flow-node">
                    <div class="flow-icon flow-sun">☀️</div>
                    <div class="flow-name">Solar</div>
                    <div class="flow-value" id="solarValue">-- kW</div>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-node">
                    <div class="flow-icon flow-battery">🔋</div>
                    <div class="flow-name">Speicher</div>
                    <div class="flow-value" id="batteryFlowValue">-- kW</div>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-node">
                    <div class="flow-icon flow-home">🏠</div>
                    <div class="flow-name">Haus</div>
                    <div class="flow-value" id="homeValue">-- kW</div>
                </div>
            </div>
        </div>

        <div class="savings-card">
            <div class="savings-title">💰 Ihre Einsparungen</div>
            <div class="savings-amount" id="savingsAmount">-- €</div>
            <div class="savings-period">Heute gespart</div>
            <div class="savings-detail">
                <div class="savings-item">
                    <div class="savings-item-value" id="monthSavings">-- €</div>
                    <div class="savings-item-label">Dieser Monat</div>
                </div>
                <div class="savings-item">
                    <div class="savings-item-value" id="yearSavings">-- €</div>
                    <div class="savings-item-label">Dieses Jahr</div>
                </div>
                <div class="savings-item">
                    <div class="savings-item-value" id="co2Saved">-- t</div>
                    <div class="savings-item-label">CO₂ vermieden</div>
                </div>
            </div>
        </div>

        <div class="ai-section">
            <div class="ai-badge"><span>✨</span>KI-GESTÜTZT</div>
            <div class="ai-title">Intelligente Energieoptimierung</div>
            <div class="ai-text">
                Unsere KI lernt Ihre Gewohnheiten und optimiert automatisch den Energieverbrauch. 
                Mit 95% Genauigkeit für den nächsten Tag.
            </div>
            <div class="ai-scenario">
                <div class="ai-scenario-title">🧠 Beispiel: Intelligente Ladesteuerung</div>
                <div class="ai-scenario-row"><span>Szenario</span><span>Morgens bewölkt, Nachmittag Sonne</span></div>
                <div class="ai-scenario-row"><span>Klassisch</span><span>Lädt morgens teuren Netzstrom</span></div>
                <div class="ai-scenario-row"><span>KI-gesteuert</span><span>Wartet auf kostenlosen Solarstrom</span></div>
                <div class="ai-scenario-row"><span>Ersparnis</span><span>2,50 €/Tag = 912 €/Jahr</span></div>
            </div>
        </div>
    </div>

    <div class="content-padding page-section" id="pageStats">
        <div class="chart-section">
            <div class="chart-header">
                <span class="status-label">Historische Daten</span>
                <div class="chart-tabs">
                    <button class="chart-tab active" data-period="day">Tag</button>
                    <button class="chart-tab" data-period="week">Woche</button>
                    <button class="chart-tab" data-period="month">Monat</button>
                    <button class="chart-tab" data-period="year">Jahr</button>
                </div>
            </div>
            <div class="chart-canvas">
                <div class="chart-bars" id="historyChartBars"></div>
            </div>
            <div class="chart-legend">
                <div class="legend-item"><div class="legend-dot solar"></div><span>Solar erzeugt</span></div>
                <div class="legend-item"><div class="legend-dot grid"></div><span>Netzbezug</span></div>
            </div>
        </div>
        <div class="status-card">
            <div class="status-header"><span class="status-label">Statistik Übersicht</span></div>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-icon">☀️</div>
                    <div class="stat-value" id="statTotalSolar">--</div>
                    <div class="stat-unit">kWh</div>
                    <div class="stat-label">Gesamt Solar</div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon">🔌</div>
                    <div class="stat-value" id="statTotalGrid">--</div>
                    <div class="stat-unit">kWh</div>
                    <div class="stat-label">Netzbezug</div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon">💰</div>
                    <div class="stat-value" id="statTotalSavings">--</div>
                    <div class="stat-unit">€</div>
                    <div class="stat-label">Ersparnis</div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-padding page-section" id="pageControl">
        <div class="notif-section">
            <div class="notif-title">🔔 Push-Benachrichtigungen</div>
            <div class="notif-row">
                <div><div class="notif-label">Niedriger Batteriestand</div><div class="notif-desc">Warnung bei SOC unter 20%</div></div>
                <div class="toggle-switch active" data-key="lowBattery"><div class="toggle-knob"></div></div>
            </div>
            <div class="notif-row">
                <div><div class="notif-label">Hoher Solarertrag</div><div class="notif-desc">Benachrichtigung bei >5 kW</div></div>
                <div class="toggle-switch active" data-key="highSolar"><div class="toggle-knob"></div></div>
            </div>
            <div class="notif-row">
                <div><div class="notif-label">Tägliche Zusammenfassung</div><div class="notif-desc">Um 20:00 Uhr</div></div>
                <div class="toggle-switch" data-key="dailySummary"><div class="toggle-knob"></div></div>
            </div>
            <div class="notif-row">
                <div><div class="notif-label">Fehlermeldungen</div><div class="notif-desc">Systemfehler & Warnungen</div></div>
                <div class="toggle-switch active" data-key="errors"><div class="toggle-knob"></div></div>
            </div>
            <div class="notif-row">
                <div><div class="notif-label">Preisalarm Strom</div><div class="notif-desc">Bei negativen Strompreisen</div></div>
                <div class="toggle-switch" data-key="priceAlert"><div class="toggle-knob"></div></div>
            </div>
        </div>
        <div class="notif-section">
            <div class="notif-title">⚙️ Anlagensteuerung</div>
            <div class="notif-row">
                <div><div class="notif-label">KI-Optimierung</div><div class="notif-desc">Automatische Ladesteuerung</div></div>
                <div class="toggle-switch active" data-key="aiOptimize"><div class="toggle-knob"></div></div>
            </div>
            <div class="notif-row">
                <div><div class="notif-label">Notstrom-Reserve</div><div class="notif-desc">Immer 10% SOC behalten</div></div>
                <div class="toggle-switch active" data-key="emergencyReserve"><div class="toggle-knob"></div></div>
            </div>
            <div class="notif-row">
                <div><div class="notif-label">Nachtladung</div><div class="notif-desc">Günstigen Nachtstrom nutzen</div></div>
                <div class="toggle-switch" data-key="nightCharge"><div class="toggle-knob"></div></div>
            </div>
        </div>
    </div>

    <div class="content-padding page-section" id="pageSettings">
        <div class="notif-section">
            <div class="notif-title">🏭 Multi-Anlage Verwaltung</div>
            <div id="plantSettingsList"></div>
        </div>
        <div class="notif-section">
            <div class="notif-title">📡 API Konfiguration</div>
            <div class="notif-row">
                <div><div class="notif-label">Simulationsmodus</div><div class="notif-desc">Keine echte Hardware nötig</div></div>
                <div class="toggle-switch active" data-key="simMode"><div class="toggle-knob"></div></div>
            </div>
            <div class="notif-row">
                <div><div class="notif-label">Auto-Refresh</div><div class="notif-desc">Alle 30 Sekunden aktualisieren</div></div>
                <div class="toggle-switch active" data-key="autoRefresh"><div class="toggle-knob"></div></div>
            </div>
        </div>
    </div>

    <div class="bottom-nav">
        <div class="nav-item active" data-page="home">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">Home</span>
        </div>
        <div class="nav-item" data-page="stats">
            <span class="nav-icon">📊</span>
            <span class="nav-label">Statistik</span>
        </div>
        <div class="nav-item" data-page="control">
            <span class="nav-icon">⚡</span>
            <span class="nav-label">Steuerung</span>
        </div>
        <div class="nav-item" data-page="settings">
            <span class="nav-icon">⚙️</span>
            <span class="nav-label">Einstell.</span>
        </div>
    </div>
</div>

<script>
const CONFIG = {
    wcProxyUrl: '<?php echo esc_url(home_url('/wc-proxy.php')); ?>',
    pvgisProxyUrl: '<?php echo esc_url(home_url('/pvgis-proxy.php')); ?>',
    productId: 3371,
    simulationMode: true,
    location: { lat: 52.52, lon: 13.405, peakpower: 5.5, angle: 30, aspect: 0 }
};

const PLANTS = [
    { id: 1, name: 'Hauptanlage', location: 'Berlin', status: 'online', lat: 52.52, lon: 13.405, peakpower: 5.5 },
    { id: 2, name: 'Ferienhaus', location: 'München', status: 'online', lat: 48.14, lon: 11.58, peakpower: 3.2 },
    { id: 3, name: 'Gewerbe', location: 'Hamburg', status: 'offline', lat: 53.55, lon: 9.99, peakpower: 8.0 }
];

let activePlantId = 1;
let debugMode = true;

function log(msg, type='info') {
    console.log('[Werdu] ' + msg);
    if (!debugMode) return;
    const consoleEl = document.getElementById('debugConsole');
    if (!consoleEl) return;
    consoleEl.style.display = 'block';
    const line = document.createElement('div');
    line.className = type;
    line.textContent = '[' + new Date().toLocaleTimeString() + '] ' + msg;
    consoleEl.appendChild(line);
    consoleEl.scrollTop = consoleEl.scrollHeight;
}

async function fetchWithTimeout(url, options={}, timeout=5000) {
    const controller = new AbortController();
    const id = setTimeout(function() { controller.abort(); }, timeout);
    try {
        const response = await fetch(url, Object.assign({}, options, { signal: controller.signal }));
        clearTimeout(id);
        return response;
    } catch (error) {
        clearTimeout(id);
        throw error;
    }
}

class WerduDataManager {
    constructor() {
        this.productData = null;
        this.solarData = null;
        this.historyData = this.generateHistoryData();
    }

    async fetchProductData() {
        try {
            log('Fetching product via proxy...');
            var url = CONFIG.wcProxyUrl + '?endpoint=products&product_id=' + CONFIG.productId;
            const response = await fetchWithTimeout(url, {}, 5000);
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const data = await response.json();
            log('Product geladen: ' + (data.name || 'OK'), 'ok');
            this.productData = data;
            return data;
        } catch (error) {
            log('Product proxy error: ' + error.message, 'err');
            return null;
        }
    }

    async fetchSolarData() {
        try {
            log('Fetching solar via proxy...');
            const plant = PLANTS.find(function(p) { return p.id === activePlantId; });
            var params = new URLSearchParams({
                lat: plant.lat, lon: plant.lon, peakpower: plant.peakpower,
                loss: 14, mountingplace: 'building', angle: 30, aspect: 0, outputformat: 'json'
            });
            var url = CONFIG.pvgisProxyUrl + '?' + params.toString();
            const response = await fetchWithTimeout(url, {}, 8000);
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const data = await response.json();
            log('Solar data geladen', 'ok');
            this.solarData = data;
            return data;
        } catch (error) {
            log('Solar proxy error: ' + error.message, 'err');
            return null;
        }
    }

    generateBatteryData() {
        var hour = new Date().getHours();
        var isDaytime = hour >= 6 && hour <= 20;
        var baseSoc = 78;
        var socVariation = Math.sin((hour / 24) * Math.PI * 2) * 15;
        var currentSoc = Math.max(10, Math.min(100, Math.round(baseSoc + socVariation)));
        var solarPower = isDaytime ? (2.5 + Math.random() * 2.5) : 0;
        var homeConsumption = (1.2 + Math.random() * 1.5);
        var batteryPower = solarPower - homeConsumption;
        var todayKwh = (12 + Math.random() * 8);
        var savings = (todayKwh * 0.40);
        return {
            soc: currentSoc,
            power: Math.abs(batteryPower).toFixed(1),
            solar: solarPower.toFixed(1),
            home: homeConsumption.toFixed(1),
            grid: Math.max(0, -batteryPower).toFixed(1),
            todayKwh: todayKwh.toFixed(1),
            savings: savings.toFixed(2),
            temperature: (20 + Math.random() * 8).toFixed(1),
            monthSavings: (142 + Math.random() * 20).toFixed(0),
            yearSavings: (1247 + Math.random() * 100).toFixed(0),
            co2Saved: (8.5 + Math.random() * 0.5).toFixed(1)
        };
    }

    generateHistoryData() {
        var data = { day: [], week: [], month: [], year: [] };
        for (var i = 0; i < 24; i++) {
            var isDay = i >= 6 && i <= 20;
            data.day.push({
                label: i + ':00',
                solar: isDay ? Math.round((Math.random() * 4 + 0.5) * 10) / 10 : 0,
                grid: Math.round((Math.random() * 2 + 0.3) * 10) / 10
            });
        }
        var days = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
        for (var i = 0; i < 7; i++) {
            data.week.push({
                label: days[i],
                solar: Math.round((Math.random() * 25 + 5) * 10) / 10,
                grid: Math.round((Math.random() * 8 + 2) * 10) / 10
            });
        }
        for (var i = 1; i <= 30; i++) {
            data.month.push({
                label: i + '.',
                solar: Math.round((Math.random() * 30 + 3) * 10) / 10,
                grid: Math.round((Math.random() * 10 + 1) * 10) / 10
            });
        }
        var months = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];
        for (var i = 0; i < 12; i++) {
            data.year.push({
                label: months[i],
                solar: Math.round((Math.random() * 400 + 100) * 10) / 10,
                grid: Math.round((Math.random() * 150 + 50) * 10) / 10
            });
        }
        return data;
    }

    async getAllData() {
        log('Starting data fetch...');
        var productData = null;
        var solarData = null;
        try { productData = await this.fetchProductData(); } catch (e) { log('Product fetch exception: ' + e.message, 'err'); }
        try { solarData = await this.fetchSolarData(); } catch (e) { log('Solar fetch exception: ' + e.message, 'err'); }
        var batteryData = this.generateBatteryData();
        log('Battery simulation ready', 'ok');
        return { product: productData, solar: solarData, battery: batteryData };
    }
}

class WerduUIManager {
    constructor(dataManager) {
        this.dataManager = dataManager;
        this.currentPeriod = 'day';
    }

    renderPlantSelector() {
        var list = document.getElementById('plantList');
        if (!list) { log('plantList not found', 'err'); return; }
        list.innerHTML = '';
        var self = this;
        PLANTS.forEach(function(plant) {
            var chip = document.createElement('div');
            chip.className = 'plant-chip ' + (plant.id === activePlantId ? 'active' : '');
            chip.innerHTML = '<span class="plant-chip-status ' + plant.status + '"></span><span>' + plant.name + '</span>';
            chip.addEventListener('click', function() {
                activePlantId = plant.id;
                self.renderPlantSelector();
                self.refreshData();
            });
            list.appendChild(chip);
        });
        log('Plant selector rendered', 'ok');
    }

    renderPlantSettings() {
        var list = document.getElementById('plantSettingsList');
        if (!list) { log('plantSettingsList not found', 'err'); return; }
        list.innerHTML = '';
        PLANTS.forEach(function(plant) {
            var row = document.createElement('div');
            row.className = 'notif-row';
            row.innerHTML = '<div><div class="notif-label">' + plant.name + ' (' + plant.location + ')</div><div class="notif-desc">' + plant.peakpower + ' kWp • ' + (plant.status === 'online' ? 'Online' : 'Offline') + '</div></div><div class="toggle-switch ' + (plant.status === 'online' ? 'active' : '') + '" data-plant="' + plant.id + '"><div class="toggle-knob"></div></div>';
            list.appendChild(row);
        });
    }

    updateProductCard(product) {
        var card = document.getElementById('productCard');
        if (!card) return;
        if (!product) { card.style.display = 'none'; return; }
        card.style.display = 'block';
        document.getElementById('productName').textContent = product.name || 'Unbekannt';
        document.getElementById('productPrice').textContent = product.price ? product.price + ' €' : 'Preis auf Anfrage';
        var stockEl = document.getElementById('productStock');
        var stockText = document.getElementById('stockText');
        if (product.stock_status === 'instock') {
            stockEl.className = 'product-stock';
            stockText.textContent = 'Auf Lager';
        } else if (product.stock_status === 'outofstock') {
            stockEl.className = 'product-stock out';
            stockText.textContent = 'Nicht verfügbar';
        } else {
            stockEl.className = 'product-stock low';
            stockText.textContent = 'Wenige verfügbar';
        }
    }

    updateBatteryDisplay(battery) {
        if (!battery || typeof battery.soc !== 'number') { log('Invalid battery data', 'err'); return; }
        var circumference = 440;
        var offset = circumference - (battery.soc / 100) * circumference;
        var circle = document.getElementById('batteryCircle');
        if (circle) circle.style.strokeDashoffset = offset;
        var pct = document.getElementById('batteryPercent');
        if (pct) pct.textContent = battery.soc + '%';
        var pw = document.getElementById('powerValue');
        if (pw) pw.textContent = battery.power;
        var tv = document.getElementById('todayValue');
        if (tv) tv.textContent = battery.todayKwh;
        var tmp = document.getElementById('tempValue');
        if (tmp) tmp.textContent = battery.temperature;
    }

    updatePowerFlow(battery) {
        if (!battery) return;
        var sv = document.getElementById('solarValue');
        if (sv) sv.textContent = battery.solar + ' kW';
        var bfv = document.getElementById('batteryFlowValue');
        if (bfv) {
            var batteryFlow = parseFloat(battery.solar) - parseFloat(battery.home);
            bfv.textContent = (batteryFlow > 0 ? '+' : '') + batteryFlow.toFixed(1) + ' kW';
        }
        var hv = document.getElementById('homeValue');
        if (hv) hv.textContent = battery.home + ' kW';
    }

    updateSavings(battery) {
        if (!battery) return;
        var sa = document.getElementById('savingsAmount');
        if (sa) sa.textContent = battery.savings + ' €';
        var ms = document.getElementById('monthSavings');
        if (ms) ms.textContent = battery.monthSavings + ' €';
        var ys = document.getElementById('yearSavings');
        if (ys) ys.textContent = battery.yearSavings + ' €';
        var co2 = document.getElementById('co2Saved');
        if (co2) co2.textContent = battery.co2Saved + ' t';
    }

    updateHistoryChart(period) {
        this.currentPeriod = period;
        var data = this.dataManager.historyData[period];
        var container = document.getElementById('historyChartBars');
        if (!container) { log('historyChartBars not found', 'err'); return; }
        container.innerHTML = '';
        var maxSolar = 1;
        var maxGrid = 1;
        data.forEach(function(d) { if (d.solar > maxSolar) maxSolar = d.solar; if (d.grid > maxGrid) maxGrid = d.grid; });
        var maxVal = Math.max(maxSolar, maxGrid);
        data.forEach(function(item) {
            var barContainer = document.createElement('div');
            barContainer.style.cssText = 'flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;height:100%;justify-content:flex-end;';
            var solarHeight = (item.solar / maxVal) * 100;
            var gridHeight = (item.grid / maxVal) * 100;
            barContainer.innerHTML = '<div style="width:100%;display:flex;gap:1px;align-items:flex-end;justify-content:center;height:100%;"><div style="width:50%;background:linear-gradient(to top,#ff6600,#ff9933);border-radius:3px 3px 0 0;height:' + solarHeight + '%;min-height:2px;"></div><div style="width:50%;background:linear-gradient(to top,#9b59b6,#bb8fce);border-radius:3px 3px 0 0;height:' + gridHeight + '%;min-height:2px;"></div></div><div style="font-size:8px;color:#5a6a8a;white-space:nowrap;">' + item.label + '</div>';
            container.appendChild(barContainer);
        });
        var totalSolar = data.reduce(function(s, d) { return s + d.solar; }, 0).toFixed(1);
        var totalGrid = data.reduce(function(s, d) { return s + d.grid; }, 0).toFixed(1);
        var savings = (totalSolar * 0.40).toFixed(2);
        var ts = document.getElementById('statTotalSolar');
        if (ts) ts.textContent = totalSolar;
        var tg = document.getElementById('statTotalGrid');
        if (tg) tg.textContent = totalGrid;
        var tsv = document.getElementById('statTotalSavings');
        if (tsv) tsv.textContent = savings;
    }

    updateHeader(status) {
        var el = document.getElementById('headerSubtitle');
        if (el) el.textContent = status;
    }

    hideLoading() {
        var el = document.getElementById('loadingOverlay');
        if (el) el.classList.add('hidden');
        log('Loading hidden', 'ok');
    }

    showError(message) {
        var content = document.getElementById('pageHome');
        if (!content) return;
        var err = document.createElement('div');
        err.className = 'error-message';
        err.textContent = message;
        content.insertBefore(err, content.firstChild);
    }

    updateAll(data) {
        log('Updating UI...');
        this.updateProductCard(data.product);
        this.updateBatteryDisplay(data.battery);
        this.updatePowerFlow(data.battery);
        this.updateSavings(data.battery);
        var plant = PLANTS.find(function(p) { return p.id === activePlantId; });
        this.updateHeader(plant ? 'Verbunden mit ' + plant.name : 'Bereit');
        this.hideLoading();
    }

    async refreshData() {
        var overlay = document.getElementById('loadingOverlay');
        if (overlay) overlay.classList.remove('hidden');
        try {
            var data = await dataManager.getAllData();
            this.updateAll(data);
        } catch (e) {
            log('Refresh error: ' + e.message, 'err');
            this.hideLoading();
        }
    }
}

class NotificationManager {
    constructor() {
        try {
            this.settings = JSON.parse(localStorage.getItem('werdu_notif_settings')) || {
                lowBattery: true, highSolar: true, dailySummary: false,
                errors: true, priceAlert: false, aiOptimize: true,
                emergencyReserve: true, nightCharge: false,
                simMode: true, autoRefresh: true
            };
        } catch (e) {
            this.settings = { lowBattery: true, highSolar: true, dailySummary: false, errors: true, priceAlert: false, aiOptimize: true, emergencyReserve: true, nightCharge: false, simMode: true, autoRefresh: true };
        }
        this.applySettings();
    }

    applySettings() {
        var self = this;
        document.querySelectorAll('.toggle-switch').forEach(function(toggle) {
            var key = toggle.dataset.key || toggle.dataset.plant;
            if (key && self.settings[key] !== undefined) {
                toggle.classList.toggle('active', self.settings[key]);
            }
        });
    }

    toggle(key) {
        this.settings[key] = !this.settings[key];
        try {
            localStorage.setItem('werdu_notif_settings', JSON.stringify(this.settings));
        } catch (e) { log('localStorage error: ' + e.message, 'err'); }
        this.applySettings();
        this.showToast(key + ': ' + (this.settings[key] ? 'EIN' : 'AUS'));
    }

    showToast(message) {
        var toast = document.createElement('div');
        toast.style.cssText = 'position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: #ff6600; color: #fff; padding: 10px 20px; border-radius: 20px; font-size: 12px; font-weight: 600; z-index: 2000; animation: fadeInOut 2s ease forwards;';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(function() { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 2000);
    }

    async requestPermission() {
        if ('Notification' in window) {
            try {
                var permission = await Notification.requestPermission();
                log('Notification permission: ' + permission, 'ok');
                return permission === 'granted';
            } catch (e) {
                log('Notification permission error: ' + e.message, 'err');
                return false;
            }
        }
        log('Notifications not supported', 'warn');
        return false;
    }
}

class NavigationManager {
    constructor(uiManager) {
        this.uiManager = uiManager;
        this.init();
    }

    init() {
        var self = this;
        document.querySelectorAll('.nav-item').forEach(function(item) {
            item.addEventListener('click', function() {
                var page = item.dataset.page;
                self.switchPage(page);
                document.querySelectorAll('.nav-item').forEach(function(i) { i.classList.remove('active'); });
                item.classList.add('active');
            });
        });
        document.querySelectorAll('.chart-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.chart-tab').forEach(function(t) { t.classList.remove('active'); });
                tab.classList.add('active');
                self.uiManager.updateHistoryChart(tab.dataset.period);
            });
        });
    }

    switchPage(page) {
        document.querySelectorAll('.page-section').forEach(function(p) { p.classList.add('active'); });
        document.querySelectorAll('.page-section').forEach(function(p) {
            if (p.id !== 'page' + page.charAt(0).toUpperCase() + page.slice(1)) {
                p.classList.remove('active');
            }
        });
        if (page === 'stats') {
            this.uiManager.updateHistoryChart('day');
        }
    }
}

var dataManager = new WerduDataManager();
var uiManager = new WerduUIManager(dataManager);
var notifManager = new NotificationManager();
var navManager = new NavigationManager(uiManager);

var loaderHidden = false;
function safeHideLoader() {
    if (loaderHidden) return;
    loaderHidden = true;
    uiManager.hideLoading();
    log('Loader force-hidden (safety timeout)', 'warn');
}
setTimeout(safeHideLoader, 3000);

document.addEventListener('DOMContentLoaded', async function() {
    log('DOM loaded, initializing...');
    try {
        uiManager.updateHeader('Daten werden geladen...');
        uiManager.renderPlantSelector();
        uiManager.renderPlantSettings();

        log('Fetching all data...');
        var data = await dataManager.getAllData();
        log('Data received, updating UI...');

        if (!data.product && !data.battery) {
            uiManager.showError('Verbindungsfehler. Simulationsmodus wird verwendet.');
        }

        uiManager.updateAll(data);
        uiManager.updateHistoryChart('day');

        document.querySelectorAll('.toggle-switch').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                var key = toggle.dataset.key || toggle.dataset.plant;
                if (key) {
                    notifManager.toggle(key);
                    toggle.classList.toggle('active');
                }
            });
        });

        setInterval(async function() {
            if (notifManager.settings.autoRefresh) {
                try {
                    var freshData = await dataManager.getAllData();
                    uiManager.updateAll(freshData);
                } catch (e) { log('Auto-refresh error: ' + e.message, 'err'); }
            }
        }, 30000);

        notifManager.requestPermission();

    } catch (error) {
        log('CRITICAL ERROR: ' + error.message, 'err');
        console.error(error);
        uiManager.showError('Fehler beim Laden: ' + error.message);
        uiManager.hideLoading();
    }
});
</script>

<style>
@keyframes fadeInOut {
    0% { opacity: 0; transform: translateX(-50%) translateY(-10px); }
    20% { opacity: 1; transform: translateX(-50%) translateY(0); }
    80% { opacity: 1; transform: translateX(-50%) translateY(0); }
    100% { opacity: 0; transform: translateX(-50%) translateY(-10px); }
}
</style>

<?php wp_footer(); ?>
</body>
</html>