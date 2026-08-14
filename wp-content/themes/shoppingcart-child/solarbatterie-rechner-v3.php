<?php
/**
 * Template Name: Solarbatterie Rechner
 * Description: Solarbatterie-Rechner mit WordPress header/footer. SEO via wp_head hooks; assets enqueued externally.
 * Version: 3.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<div id="werdu-calc-isolated">
<div class="werdu-calc-wrapper">
    <noscript>
        <div style="background:#fef3c7; border:1px solid #f59e0b; border-radius:12px; padding:20px; margin-bottom:20px; text-align:center;">
            <p style="margin:0; color:#92400e; font-weight:600;">&#9888; JavaScript ist erforderlich für den Solarbatterie-Rechner.</p>
            <p style="margin:8px 0 0; color:#92400e; font-size:0.9em;">Bitte aktivieren Sie JavaScript in Ihrem Browser oder besuchen Sie unseren <a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" style="color:#ea580c; text-decoration:underline; font-weight:600;">Shop</a> für direkte Produktempfehlungen.</p>
        </div>
    </noscript>

    <!-- HEADER -->
    <div class="werdu-header" role="banner">
        <h1 itemprop="headline">Solarbatterie-Rechner 2026</h1>
        <p itemprop="description">Berechnen Sie <strong>kostenlos</strong> die optimale PV-Speicher-Größe für Ihr Zuhause. Basierend auf Ihrem Verbrauch, Ihrer PV-Anlage, Dachausrichtung und regionalem Solarertrag. Inklusive Amortisations-Analyse & Produktempfehlung.</p>
    </div>

    <!-- PROGRESS -->
    <nav aria-label="Berechnungsfortschritt">
        <ul class="werdu-progress" role="tablist">
            <li class="werdu-step active" data-step="1" role="tab" aria-selected="true" aria-controls="step1" tabindex="0">
                <div class="werdu-step-num" aria-label="Schritt 1">1</div>
                <div class="werdu-step-label">Haushalt</div>
            </li>
            <li class="werdu-step" data-step="2" role="tab" aria-selected="false" aria-controls="step2" tabindex="0">
                <div class="werdu-step-num" aria-label="Schritt 2">2</div>
                <div class="werdu-step-label">PV-Anlage</div>
            </li>
            <li class="werdu-step" data-step="3" role="tab" aria-selected="false" aria-controls="step3" tabindex="0">
                <div class="werdu-step-num" aria-label="Schritt 3">3</div>
                <div class="werdu-step-label">Ziel & System</div>
            </li>
            <li class="werdu-step" data-step="4" role="tab" aria-selected="false" aria-controls="step4" tabindex="0">
                <div class="werdu-step-num" aria-label="Schritt 4">4</div>
                <div class="werdu-step-label">Ergebnis</div>
            </li>
        </ul>
    </nav>

    <!-- ===== BLOK 1: STEP 1 - HAUSHALT ===== -->
    <div class="werdu-section active" data-section="1" id="step1">
        <h2 class="werdu-section-title"><span class="icon">&#127968;</span> Schritt 1: Ihr Haushalt & Verbrauch</h2>

        <div class="werdu-grid">
            <div class="werdu-card">
                <span class="werdu-tooltip" data-tip="Ihre PLZ bestimmt den regionalen Solarertrag. Süddeutschland (8,9) = höherer Ertrag, Norddeutschland (2,3) = etwas niedriger.">?</span>
                <div class="werdu-float-field">
                    <label class="werdu-label" for="w-plz">Postleitzahl</label>
                    <input type="text" class="werdu-input" id="w-plz" placeholder=" " title="z. B. 80331" maxlength="5" pattern="[0-9]{5}" inputmode="numeric" autocomplete="postal-code">
                </div>
                <div class="werdu-hint">Für regionale Ertragsberechnung</div>
            </div>

            <div class="werdu-card">
                <label class="werdu-label">
                    Haushaltsgröße
                    <span class="werdu-tooltip" data-tip="Anzahl der Personen im Haushalt. Dies beeinflusst den Basisstromverbrauch.">?</span>
                </label>
                <div class="werdu-options" id="w-persons">
                    <button class="werdu-option" data-value="1">1 Person</button>
                    <button class="werdu-option" data-value="2">2 Personen</button>
                    <button class="werdu-option selected" data-value="3">3 Personen</button>
                    <button class="werdu-option" data-value="4">4 Personen</button>
                    <button class="werdu-option" data-value="5">5+ Personen</button>
                </div>
            </div>

            <div class="werdu-card">
                <span class="werdu-tooltip" data-tip="Finden Sie auf Ihrer Stromrechnung. Durchschnitt: 1P=2.300kWh, 2P=3.500kWh, 3P=4.500kWh, 4P=5.500kWh, 5P=7.000kWh">?</span>
                <div class="werdu-float-field">
                    <label class="werdu-label" for="w-consumption">Jahresverbrauch (kWh)</label>
                    <input type="number" class="werdu-input" id="w-consumption" placeholder=" " title="z. B. 4500" min="1000" max="15000" step="100" value="4500">
                </div>
                <div class="werdu-range-value" id="w-consumption-val">4.500 kWh/Jahr</div>
            </div>

            <div class="werdu-card">
                <span class="werdu-tooltip" data-tip="Aktueller Arbeitspreis aus Ihrem Stromvertrag. Deutschland Durchschnitt 2026: 35-42 ct/kWh">?</span>
                <div class="werdu-float-field">
                    <label class="werdu-label" for="w-price">Strompreis (ct/kWh)</label>
                    <input type="number" class="werdu-input" id="w-price" placeholder=" " title="z. B. 40" min="25" max="60" step="1" value="40">
                </div>
                <div class="werdu-range-value" id="w-price-val">40 ct/kWh</div>
            </div>

            <div class="werdu-card">
                <label class="werdu-label">
                    E-Auto vorhanden?
                    <span class="werdu-tooltip" data-tip="E-Autos erhöhen den Strombedarf um ca. 2.500-4.000 kWh/Jahr pro Fahrzeug">?</span>
                </label>
                <div class="werdu-options" id="w-ev">
                    <button class="werdu-option selected" data-value="0">Nein</button>
                    <button class="werdu-option" data-value="1">1 Auto</button>
                    <button class="werdu-option" data-value="2">2 Autos</button>
                </div>
            </div>

            <div class="werdu-card">
                <label class="werdu-label">
                    Wärmepumpe vorhanden?
                    <span class="werdu-tooltip" data-tip="Wärmepumpen verbrauchen zusätzlich ca. 3.000-7.000 kWh/Jahr je nach Größe und Isolierung">?</span>
                </label>
                <div class="werdu-options" id="w-hp">
                    <button class="werdu-option selected" data-value="0">Nein</button>
                    <button class="werdu-option" data-value="3000">Ja, klein (~3.000)</button>
                    <button class="werdu-option" data-value="5000">Ja, mittel (~5.000)</button>
                    <button class="werdu-option" data-value="7000">Ja, groß (~7.000)</button>
                </div>
            </div>
        </div>

        <div class="werdu-nav">
            <button class="werdu-btn werdu-btn-next" onclick="wNext()">Weiter zu PV-Anlage &#8594;</button>
        </div>
    </div>

    <!-- ===== BLOK 2: STEP 2 - PV-ANLAGE ===== -->
    <div class="werdu-section" data-section="2" id="step2">
        <h2 class="werdu-section-title"><span class="icon">&#9728;&#65039;</span> Schritt 2: Ihre PV-Anlage</h2>

        <div class="werdu-grid">
            <div class="werdu-card">
                <span class="werdu-tooltip" data-tip="Leistung Ihrer Solaranlage. 1 kWp ca. 3-4 Module. Typisch: Einfamilienhaus 5-10 kWp, großes Haus 10-20 kWp">?</span>
                <div class="werdu-float-field">
                    <label class="werdu-label" for="w-pv">PV-Leistung (kWp)</label>
                    <input type="number" class="werdu-input" id="w-pv" placeholder=" " title="z. B. 8" min="3" max="30" step="0.5" value="8">
                </div>
                <div class="werdu-range-value" id="w-pv-val">8 kWp</div>
            </div>

            <div class="werdu-card">
                <label class="werdu-label">
                    Dachneigung
                    <span class="werdu-tooltip" data-tip="Optimale Neigung in Deutschland: 30-35°. Flachdach: 10-15°, Steildach: 45-60°">?</span>
                </label>
                <div class="werdu-options" id="w-tilt">
                    <button class="werdu-option" data-value="15">15° (Flach)</button>
                    <button class="werdu-option selected" data-value="30">30° (Optimal)</button>
                    <button class="werdu-option" data-value="45">45° (Steil)</button>
                    <button class="werdu-option" data-value="60">60° (Sehr steil)</button>
                </div>
            </div>

            <div class="werdu-card">
                <label class="werdu-label">
                    Dachausrichtung
                    <span class="werdu-tooltip" data-tip="Süd = 100% Ertrag, Südost/Südwest = 85-92%, Ost/West = 75-80%, Nord = nicht empfohlen">?</span>
                </label>
                <div class="werdu-options" id="w-orientation">
                    <button class="werdu-option" data-value="90">Ost</button>
                    <button class="werdu-option" data-value="45">Südost</button>
                    <button class="werdu-option selected" data-value="0">Süd</button>
                    <button class="werdu-option" data-value="-45">Südwest</button>
                    <button class="werdu-option" data-value="-90">West</button>
                </div>
            </div>

            <div class="werdu-card">
                <label class="werdu-label">
                    Verschattung
                    <span class="werdu-tooltip" data-tip="Bäume, Gebäude oder Schornsteine reduzieren den Ertrag. Starke Verschattung sollte durch Optimierer ausgeglichen werden.">?</span>
                </label>
                <div class="werdu-options" id="w-shading">
                    <button class="werdu-option selected" data-value="1.0">Keine</button>
                    <button class="werdu-option" data-value="0.9">Leicht</button>
                    <button class="werdu-option" data-value="0.8">Mittel</button>
                    <button class="werdu-option" data-value="0.65">Stark</button>
                </div>
            </div>
        </div>

        <div class="werdu-nav">
            <button class="werdu-btn werdu-btn-prev" onclick="wPrev()">&#8592; Zurück</button>
            <button class="werdu-btn werdu-btn-next" onclick="wNext()">Weiter zu Ziel & System &#8594;</button>
        </div>
    </div>

    <!-- ===== BLOK 3: STEP 3 - ZIEL & SYSTEM ===== -->
    <div class="werdu-section" data-section="3" id="step3">
        <h2 class="werdu-section-title"><span class="icon">&#127919;</span> Schritt 3: Ihr Autarkie-Ziel & System</h2>

        <div class="werdu-grid">
            <div class="werdu-card full">
                <span class="werdu-tooltip" data-tip="Wie viel Prozent Ihres Stroms möchten Sie selbst produzieren? 100% sind in Deutschland aufgrund saisonaler Schwankungen (Winter/Sommer) nicht realistisch. 70-80% ist ein gutes Ziel.">?</span>
                <div class="werdu-float-field">
                    <label class="werdu-label" for="w-autarky">Gewünschte Autarkie (%)</label>
                    <input type="number" class="werdu-input" id="w-autarky" placeholder=" " title="z. B. 70" min="50" max="90" step="5" value="70">
                </div>
                <div style="display:flex; justify-content:space-between; margin-top:10px; font-size:0.85em; color:#64748b;">
                    <span>50% (Basis)</span>
                    <span class="werdu-range-value" id="w-autarky-val" style="margin:0; font-size:1.3em;">70%</span>
                    <span>90% (Hoch)</span>
                </div>
            </div>

            <div class="werdu-card">
                <label class="werdu-label">
                    Saisonale Optimierung
                    <span class="werdu-tooltip" data-tip="Winter-Autarkie erfordert deutlich größere Speicher (ca. 2x mehr Kapazität). Sommer-Autarkie ist leichter zu erreichen.">?</span>
                </label>
                <div class="werdu-options" id="w-season">
                    <button class="werdu-option selected" data-value="summer">Sommer-Autarkie</button>
                    <button class="werdu-option" data-value="year">Jahres-Autarkie</button>
                    <button class="werdu-option" data-value="winter">Winter-Autarkie</button>
                </div>
            </div>

            <div class="werdu-card">
                <span class="werdu-tooltip" data-tip="Verluste durch Wechselrichter, Kabel, Temperatur. LiFePO4 Systeme: typisch 8-12%. Höher bei längeren Kabeln oder älteren Wechselrichtern.">?</span>
                <div class="werdu-float-field">
                    <label class="werdu-label" for="w-loss">Systemverlust (%)</label>
                    <input type="number" class="werdu-input" id="w-loss" placeholder=" " title="z. B. 10" min="5" max="25" step="1" value="10">
                </div>
                <div class="werdu-range-value" id="w-loss-val">10%</div>
            </div>

            <div class="werdu-card">
                <label class="werdu-label">
                    Wechselrichter vorhanden?
                    <span class="werdu-tooltip" data-tip="Wenn Sie bereits einen Hybrid-Wechselrichter haben, empfehlen wir eine reine Batterie. Sonst ein All-in-One System mit integriertem Wechselrichter.">?</span>
                </label>
                <div class="werdu-options" id="w-inverter">
                    <button class="werdu-option selected" data-value="yes">Ja, Hybrid-WR vorhanden</button>
                    <button class="werdu-option" data-value="no">Nein, benötige Komplettlösung</button>
                </div>
            </div>
        </div>

        <div class="werdu-nav">
            <button class="werdu-btn werdu-btn-prev" onclick="wPrev()">&#8592; Zurück</button>
            <button class="werdu-btn werdu-btn-calc" onclick="wCalculate()">&#128640; Berechnung starten</button>
        </div>
    </div>

    <!-- ===== BLOK 4: STEP 4 - ERGEBNISSE ===== -->
    <div class="werdu-section" data-section="4" id="step4">

        <!-- Hero Stats -->
        <div class="werdu-result-hero" itemscope itemtype="https://schema.org/Thing">
            <h3>&#128202; Ihre optimale Energiespeicher-Lösung</h3>
            <meta itemprop="name" content="Solarbatterie Berechnungsergebnis">
            <div class="werdu-hero-stats">
                <div class="werdu-hero-stat" data-stat="capacity">
                    <span class="value" id="res-battery" data-value="">-</span>
                    <div class="label">Empfohlene Kapazität</div>
                </div>
                <div class="werdu-hero-stat" data-stat="autarky">
                    <span class="value" id="res-autarky" data-value="">-</span>
                    <div class="label">Realistische Autarkie</div>
                </div>
                <div class="werdu-hero-stat" data-stat="savings">
                    <span class="value" id="res-savings" data-value="">-</span>
                    <div class="label">Jährliche Ersparnis</div>
                </div>
                <div class="werdu-hero-stat" data-stat="roi">
                    <span class="value" id="res-roi" data-value="">-</span>
                    <div class="label">Amortisation</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="werdu-charts-row">
            <div class="werdu-chart-card" style="text-align:center;">
                <h4>&#128260; Eigenverbrauchsanteil</h4>
                <svg class="werdu-donut" viewBox="0 0 200 200">
                    <circle class="werdu-donut-bg" cx="100" cy="100" r="80"/>
                    <circle class="werdu-donut-fill" id="chart-self" cx="100" cy="100" r="80" 
                        stroke-dasharray="0 502" stroke-dashoffset="0" transform="rotate(-90 100 100)" stroke="#f97316"/>
                    <text class="werdu-donut-text" id="chart-self-text" x="100" y="92" dominant-baseline="central" text-anchor="middle">0%</text>
                    <text class="werdu-donut-label" x="100" y="115" dominant-baseline="central" text-anchor="middle">Eigenverbrauch</text>
                </svg>
            </div>

            <div class="werdu-chart-card" style="text-align:center;">
                <h4>&#9889; Autarkiegrad</h4>
                <svg class="werdu-donut" viewBox="0 0 200 200">
                    <circle class="werdu-donut-bg" cx="100" cy="100" r="80"/>
                    <circle class="werdu-donut-fill" id="chart-auto" cx="100" cy="100" r="80" 
                        stroke-dasharray="0 502" stroke-dashoffset="0" transform="rotate(-90 100 100)" stroke="#22c55e"/>
                    <text class="werdu-donut-text" id="chart-auto-text" x="100" y="92" dominant-baseline="central" text-anchor="middle">0%</text>
                    <text class="werdu-donut-label" x="100" y="115" dominant-baseline="central" text-anchor="middle">Unabhängigkeit</text>
                </svg>
            </div>

            <div class="werdu-chart-card">
                <h4>&#128202; Täglicher Energiefluss</h4>
                <div class="werdu-bar-chart">
                    <div class="werdu-bar-item">
                        <div class="werdu-bar-label">Solar</div>
                        <div class="werdu-bar-track">
                            <div class="werdu-bar-fill solar" id="bar-solar" style="width:0%">-</div>
                        </div>
                    </div>
                    <div class="werdu-bar-item">
                        <div class="werdu-bar-label">Speicher</div>
                        <div class="werdu-bar-track">
                            <div class="werdu-bar-fill battery" id="bar-battery" style="width:0%">-</div>
                        </div>
                    </div>
                    <div class="werdu-bar-item">
                        <div class="werdu-bar-label">Netzbezug</div>
                        <div class="werdu-bar-track">
                            <div class="werdu-bar-fill grid" id="bar-grid" style="width:0%">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Savings Cards -->
        <div class="werdu-savings-grid">
            <div class="werdu-savings-card">
                <div class="icon">&#128176;</div>
                <div class="value" id="save-year">-</div>
                <div class="label">Ersparnis pro Jahr</div>
            </div>
            <div class="werdu-savings-card">
                <div class="icon">&#128200;</div>
                <div class="value" id="save-10">-</div>
                <div class="label">Ersparnis 10 Jahre</div>
            </div>
            <div class="werdu-savings-card">
                <div class="icon">&#127793;</div>
                <div class="value" id="save-co2">-</div>
                <div class="label">CO₂-Ersparnis/Jahr</div>
            </div>
            <div class="werdu-savings-card">
                <div class="icon">&#128663;</div>
                <div class="value" id="save-km">-</div>
                <div class="label">Solare km (E-Auto)</div>
            </div>
        </div>

        <!-- Monthly Chart -->
        <div class="werdu-chart-card" style="margin-bottom:24px;">
            <h4>&#128197; Monatliche Solarerträge vs. Verbrauch</h4>
            <div class="werdu-monthly-chart" id="monthly-chart"></div>
            <div class="werdu-month-labels">
                <span>Jan</span><span>Feb</span><span>Mär</span><span>Apr</span><span>Mai</span><span>Jun</span>
                <span>Jul</span><span>Aug</span><span>Sep</span><span>Okt</span><span>Nov</span><span>Dez</span>
            </div>
        </div>

        <!-- ROI Timeline -->
        <div class="werdu-chart-card" style="margin-bottom:24px;">
            <h4>&#9201; Amortisations-Timeline</h4>
            <div class="werdu-roi-timeline">
                <div class="werdu-roi-point active">Kauf</div>
                <div class="werdu-roi-line">
                    <div class="werdu-roi-fill" id="roi-line" style="width:0%"></div>
                </div>
                <div class="werdu-roi-point" id="roi-point">J<span id="roi-year">-</span></div>
                <div class="werdu-roi-line">
                    <div class="werdu-roi-fill" style="width:0%"></div>
                </div>
                <div class="werdu-roi-point active">20J</div>
            </div>
            <div class="werdu-roi-info">
                Bei einer Nutzungsdauer von über <strong>6.000 Zyklen</strong> und einer Lebensdauer von 15-20 Jahren amortisiert sich Ihr Speicher nach <strong id="roi-text" style="color:#ea580c;">-</strong>. Danach produzieren Sie praktisch <strong>kostenlosen Strom</strong>.
            </div>
        </div>

        <!-- Product Recommendation -->
        <div class="werdu-product-rec">
            <h3>Passende Produkte für Ihren Bedarf</h3>
            <div class="werdu-product-grid" id="w-products">

                <a href="<?php echo esc_url( home_url( '/tewaycell-10-kwh-all-in-one-sodium-ion-solarspeicher-5-kw-hybrid-wechselrichter/' ) ); ?>" class="werdu-product-item" id="prod-10">
                    <div class="werdu-product-type">All-in-One System</div>
                    <div class="werdu-product-kwh">10 kWh</div>
                    <div class="werdu-product-price">2.799 €</div>
                    <div class="werdu-product-features">Sodium-Ion + 5 kW Hybrid-WR<br>Ideal für Singles & kleine Haushalte<br>Plug & Play</div>
                    <div class="werdu-product-cta">Zum Produkt &#8594;</div>
                </a>

                <!-- BASEN GREEN 16 kWh - VERVANGT Tewaycell 15 kWh -->
                <a href="<?php echo esc_url( home_url( '/16-kwh-heimspeicher-lifepo4-314ah/' ) ); ?>" class="werdu-product-item" id="prod-16green">
                    <div class="werdu-product-type">Batterie Only</div>
                    <div class="werdu-product-kwh">16 kWh</div>
                    <div class="werdu-product-price">1.990 €</div>
                    <div class="werdu-product-features">Basen Green LiFePO₄ Grade A<br>51,2V 314Ah • 200A Dauerstrom<br>Touchscreen • 5A aktiver Balancer<br>10.000 Zyklen • App-Steuerung</div>
                    <div class="werdu-product-cta">Zum Produkt &#8594;</div>
                </a>

                <a href="<?php echo esc_url( home_url( '/tewaycell-16-kwh-512-v-lifepo4-solarbatterie-314-ah-mobile-ess-kostenloser-versand/' ) ); ?>" class="werdu-product-item" id="prod-16">
                    <div class="werdu-product-type">Batterie Only</div>
                    <div class="werdu-product-kwh">16 kWh</div>
                    <div class="werdu-product-price">2.345 €</div>
                    <div class="werdu-product-features">LiFePO₄ 314Ah Mobile ESS<br>Max. 200A Entladestrom<br>Kompatibel mit Growatt, Victron uvm.</div>
                    <div class="werdu-product-cta">Zum Produkt &#8594;</div>
                </a>

                <a href="<?php echo esc_url( home_url( '/tewaycell-15-kwh-all-in-one-lifepo4-solarbatterie-5-kw-hybrid-wechselrichter/' ) ); ?>" class="werdu-product-item" id="prod-15aio">
                    <div class="werdu-product-type">All-in-One System</div>
                    <div class="werdu-product-kwh">15 kWh</div>
                    <div class="werdu-product-price">2.899 €</div>
                    <div class="werdu-product-features">LiFePO₄ + 5 kW Hybrid-WR<br>Plug & Play, mobil einsetzbar<br>MPPT integriert</div>
                    <div class="werdu-product-cta">Zum Produkt &#8594;</div>
                </a>

                <a href="<?php echo esc_url( home_url( '/tewaycell-30-32-kwh-lifepo4-batterie-512v-560-628ah-mobile-ess-300ah-bms/' ) ); ?>" class="werdu-product-item" id="prod-30">
                    <div class="werdu-product-type">Batterie Only</div>
                    <div class="werdu-product-kwh">30-32 kWh</div>
                    <div class="werdu-product-price">3.499 €</div>
                    <div class="werdu-product-features">LiFePO₄ 560-628Ah<br>Max. 15.000W Ausgangsleistung<br>Für große Haushalte & E-Auto</div>
                    <div class="werdu-product-cta">Zum Produkt &#8594;</div>
                </a>

                <a href="<?php echo esc_url( home_url( '/tewaycell-30-kwh-all-in-one-solarspeicher-mit-12-kw-hybrid-wechselrichter-3-phasig/' ) ); ?>" class="werdu-product-item" id="prod-30aio">
                    <div class="werdu-product-type">All-in-One 3-phasig</div>
                    <div class="werdu-product-kwh">30 kWh</div>
                    <div class="werdu-product-price">4.839 €</div>
                    <div class="werdu-product-features">LiFePO₄ + 12 kW 3-phasig WR<br>Dual MPPT, USV-Funktion<br>Für Gewerbe & maximale Autarkie</div>
                    <div class="werdu-product-cta">Zum Produkt &#8594;</div>
                </a>
            </div>
        </div>

        <!-- Comparison Table -->
        <h3 style="color:#1e293b; margin:28px 0 16px 0; font-size:1.15em; font-weight:700;">&#128220; Marktübersicht: Was Sie bei Ihrer Wahl beachten sollten</h3>
        <div class="werdu-compare-wrap" itemscope itemtype="https://schema.org/Table">
            <meta itemprop="about" content="Vergleich Solarbatterien Werdu.de vs. Markt">
            <table class="werdu-compare-table">
                <caption style="position:absolute;left:-9999px;">Vergleichstabelle: Werdu.de Tewaycell Solarbatterien gegen Marktübliche Produkte</caption>
                <thead>
                    <tr><th scope="col">Merkmal</th><th scope="col">Werdu.de Tewaycell</th><th scope="col">Marktüblich (Vergleichswerte)</th></tr>
                </thead>
                <tbody>
                    <tr><th scope="row" style="font-weight:600;">Preis 10-16 kWh</th><td class="price-werdu">ab 2.345 €</td><td class="price-market">3.500 – 5.000 €</td></tr>
                    <tr><th scope="row" style="font-weight:600;">Preis 30+ kWh</th><td class="price-werdu">ab 3.499 €</td><td class="price-market">7.000 – 11.000 €</td></tr>
                    <tr><th scope="row" style="font-weight:600;">Zyklenlebensdauer</th><td>6.000 – 8.000 Zyklen (80% DoD)</td><td class="price-market">4.000 – 10.000 Zyklen</td></tr>
                    <tr><th scope="row" style="font-weight:600;">Technologie</th><td>LiFePO₄ (A-Grade) & Sodium-Ion</td><td class="price-market">Meist LiFePO₄</td></tr>
                    <tr><th scope="row" style="font-weight:600;">Garantie</th><td>10 Jahre</td><td class="price-market">5 – 10 Jahre</td></tr>
                    <tr><th scope="row" style="font-weight:600;">Lieferzeit</th><td>45 – 66 Tage (China) / EU schneller</td><td class="price-market">3 – 8 Wochen</td></tr>
                    <tr><th scope="row" style="font-weight:600;">Erweiterbarkeit</th><td>Bis 15 Einheiten parallel</td><td class="price-market">Oft modular, teils fest</td></tr>
                    <tr><th scope="row" style="font-weight:600;">Sicherheitsfeatures</th><td>BMS, Active Balancer, Feuerlöschsystem</td><td class="price-market">BMS standard, sonst variabel</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Final CTA → Beratung mit Rechner-Parametern -->
        <div class="werdu-final-cta">
            <h3>&#128640; Bereit für Ihre Energieunabhängigkeit?</h3>
            <p>Mit der empfohlenen Lösung sparen Sie bis zu <strong id="cta-savings" style="color:#ea580c;">-</strong> pro Jahr.</p>
            <a href="<?php echo esc_url( home_url( '/beratung-anfragen/' ) ); ?>" class="btn werdu-calc-cta" id="werdu-beratung-cta">Kostenlose Fachanalyse anfordern &#8594;</a>
            <div class="sub">Kostenloser Versand &#8226; 10 Jahre Garantie &#8226; 14 Tage Rückgabe &#8226; Deutscher Support</div>
        </div>

        <div style="text-align:center; margin-top:20px;">
            <button class="werdu-btn werdu-btn-prev" onclick="wReset()" style="margin:0 auto;">&#8634; Neue Berechnung starten</button>
        </div>

    </div>

    <!-- FOOTER -->
    <footer class="werdu-footer" itemscope itemtype="https://schema.org/WPFooter">
        <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
            <span itemprop="name">WERDU.de</span> &ndash; 
            <span itemprop="url"><?php echo esc_url( home_url( '/' ) ); ?></span>
        </div>
        <p>&copy; 2026 WERDU.de | Solarbatterie-Rechner v3.2 | Alle Angaben ohne Gewähr. Die tatsächliche Ersparnis hängt von individuellen Faktoren ab.</p>
        <p style="font-size:0.75em; margin-top:8px;">
            <a href="<?php echo esc_url( home_url( '/datenschutz' ) ); ?>" style="color:#94a3b8; text-decoration:underline;">Datenschutz</a> &bull; 
            <a href="<?php echo esc_url( home_url( '/impressum' ) ); ?>" style="color:#94a3b8; text-decoration:underline;">Impressum</a> &bull; 
            <a href="<?php echo esc_url( home_url( '/agb' ) ); ?>" style="color:#94a3b8; text-decoration:underline;">AGB</a>
        </p>
    </footer>

</div>
</div>

<?php get_footer(); ?>
