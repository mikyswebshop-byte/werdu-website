<?php
/**
 * Plugin Name: WERDU Homepage SEO & AIO Upgrade
 * Description: Injecteert de nieuwe H1/Hero-copy en het uitgebreide SEO/AIO-contentblok (ToC, vergelijkingstabel, FAQ, JSON-LD) op de homepage, direct onder de Heimspeicher-rechner sectie, zonder de bestaande Elementor-content, calculator of styling aan te passen.
 * Version: 1.0
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

/**
 * Nieuw Hero-blok (H1 + intro) — wordt vóór de bestaande content geplaatst.
 * De bestaande Hero/aankondigingsbalk, productkaarten en calculator blijven
 * volledig ongewijzigd; dit is een extra, op zichzelf staand blok.
 */
function werdu_home_seo_hero_html() {
    return <<<'HTML'
<div class="werdu-hero-seo-block" style="max-width:1300px;margin:0 auto;padding:30px 20px 10px;">
    <h1 style="font-size:2.1rem;line-height:1.25;color:#1a1a1a;margin:0 0 14px;">PV Speicher kaufen 2026: Testsieger &amp; Autarkie-Rechner für Ihr Zuhause</h1>
    <p style="font-size:1.1rem;line-height:1.6;color:#333;max-width:900px;margin:0;">Möchten Sie einen hochqualitativen PV Speicher kaufen, um Ihre Stromkosten drastisch zu senken und sich unabhängig von steigenden Netzpreisen zu machen? Mit einem modernen PV Speicher nutzen Sie Ihren erzeugten Solarstrom genau dann, wenn Sie ihn wirklich brauchen – auch abends und in der Nacht. Nutzen Sie unseren kostenlosen Autarkie-Rechner, um die ideale Kapazität für Ihren Heimspeicher zu berechnen und sichern Sie sich Ihr individuelles Angebot für eine nachhaltige Solarbatterie.</p>
</div>
HTML;
}

/**
 * Groot SEO/AIO-contentblok (ToC, artikel, vergelijkingstabel, FAQ, JSON-LD).
 * Wordt direct onder de calculator-sectie geplaatst. Alle CTA's verwijzen naar
 * home_url('/beratung-anfragen/') resp. home_url('/solarbatterie-rechner/') —
 * nooit naar /kontakt/ en nooit naar een hardgecodeerde host.
 */
function werdu_home_seo_body_html() {
    $beratung = werdu_home_seo_beratung_url();
    $rechner  = werdu_home_seo_rechner_url();

    $template = <<<'HTML'
<!-- Rank Math Compliant Table of Contents Block -->
<div class="werdu-toc-box" style="background:#f4f7fa; border:1px solid #dcdfe3; border-left:5px solid #0056b3; padding:25px; border-radius:8px; margin:40px 0;">
    <h2 style="font-size:1.3rem; margin-top:0; color:#1a1a1a;">Inhaltsverzeichnis: Ihr umfassender PV Speicher Ratgeber</h2>
    <ul style="line-height:1.8; margin-bottom:0; padding-left:20px;">
        <li><a href="#warum-pv-speicher-kaufen" style="color:#0056b3; text-decoration:none; font-weight:600;">1. Warum Sie 2026 einen PV Speicher kaufen sollten</a></li>
        <li><a href="#autarkie-vorteile" style="color:#0056b3; text-decoration:none; font-weight:600;">2. Maximaler Nutzen: Autarkie steigern &amp; Stromkosten nachhaltig senken</a></li>
        <li><a href="#dimensionierung-kapazitaet" style="color:#0056b3; text-decoration:none; font-weight:600;">3. Die richtige Größe: Wie viel kWh PV Speicher brauchen Sie wirklich?</a></li>
        <li><a href="#technologie-vergleich" style="color:#0056b3; text-decoration:none; font-weight:600;">4. Technologien im Vergleich: LFP (Lithium-Eisenphosphat) vs. NMC</a></li>
        <li><a href="#kosten-wirtschaftlichkeit" style="color:#0056b3; text-decoration:none; font-weight:600;">5. PV Speicher Kosten, Förderung &amp; Amortisation im Überblick</a></li>
        <li><a href="#faq-bereich" style="color:#0056b3; text-decoration:none; font-weight:600;">6. Häufig gestellte Fragen (FAQ zum PV Speicher)</a></li>
    </ul>
</div>

<!-- Main SEO & High-Conversion Article Body -->
<section class="werdu-seo-body" style="line-height:1.75; color:#2c3e50; font-size:1.05rem;">

    <h2 id="warum-pv-speicher-kaufen" style="font-size:1.8rem; color:#1a1a1a; margin-top:40px;">1. Warum Sie 2026 einen PV Speicher kaufen sollten</h2>
    <p>
        Die Einspeisevergütung für Solarstrom liegt auf einem historischen Tiefstand, während die Strompreise für deutsche Haushalte weiterhin auf hohem Niveau verharren. Wer heute eine Photovoltaikanlage ohne einen leistungsstarken <strong>PV Speicher</strong> betreibt, verschenkt Tag für Tag bares Geld. Ohne eigene Speichermöglichkeit nutzen Eigenheimbesitzer im Durchschnitt lediglich 20 % bis 30 % ihres selbst erzeugten Solarstroms. Der große Rest fließt für wenige Cent ins öffentliche Netz – nur um abends teuren Netzstrom zurückzukaufen.
    </p>
    <p>
        Indem Sie einen modernen <strong>PV Speicher kaufen</strong>, heben Sie Ihren Eigenverbrauch sofort auf 70 % bis über 85 %. Sie speichern die ungenutzte Sonnenenergie der Mittagsstunden ab und stellen sie genau dann zur Verfügung, wenn der Stromverbrauch im Haushalt am höchsten ist: morgens und in den Abendstunden. Das macht Sie von fossilen Energieträgern und den Preissteigerungen der Stromkonzerne nachhaltig unabhängig. Auch für bestehende Solaranlagen lohnt sich die Nachrüstung: Einen passenden <strong>Heimspeicher kaufen</strong> sichert den langfristigen Werterhalt Ihrer Immobilie und maximiert Ihre persönliche Energiewende.
    </p>

    <!-- Mid-Content High Conversion Box -->
    <div style="background:linear-gradient(135deg, #0056b3 0%, #003d80 100%); color:#fff; padding:30px; border-radius:10px; margin:35px 0; text-align:center;">
        <h3 style="color:#fff; margin-top:0; font-size:1.5rem;">Wissen Sie schon, welche Speichergröße Sie benötigen?</h3>
        <p style="margin-bottom:20px; font-size:1.1rem;">Ermitteln Sie mit unserem präzisen Online-Rechner in unter 2 Minuten die optimale Kapazität und Ihre jährliche Ersparnis.</p>
        <a href="___RECHNER_URL___" style="display:inline-block; background:#ff9900; color:#1a1a1a; padding:14px 30px; border-radius:6px; font-weight:bold; text-decoration:none; font-size:1.1rem;">Jetzt Autarkie &amp; Speichergröße berechnen</a>
    </div>

    <h2 id="autarkie-vorteile" style="font-size:1.8rem; color:#1a1a1a; margin-top:40px;">2. Maximaler Nutzen: Autarkie steigern &amp; Stromkosten nachhaltig senken</h2>
    <p>
        Der Entschluss, eine hochwertige <strong>Solarbatterie zu kaufen</strong>, bringt Ihnen weit mehr als nur finanzielle Ersparnisse. Es geht um Autonomie, Versorgungssicherheit und maximale Unabhängigkeit im eigenen Zuhause.
    </p>
    <ul style="padding-left:20px;">
        <li style="margin-bottom:10px;"><strong>Massive Reduktion der monatlichen Abschlagszahlungen:</strong> Jede Kilowattstunde, die Sie aus Ihrem eigenen <strong>PV Speicher</strong> entnehmen, müssen Sie nicht mehr teuer von Ihrem Stromversorger beziehen.</li>
        <li style="margin-bottom:10px;"><strong>Schutz vor Strompreissteigerungen:</strong> Wenn die Netzstrompreise steigen, bleiben Ihre Erzeugungskosten konstant bei nahezu 0 Cent pro Kilowattstunde.</li>
        <li style="margin-bottom:10px;"><strong>Optionale Notstrom- und Ersatzstromversorgung:</strong> Moderne Heimspeicher sichern Ihr Gebäude bei Netzausfällen ab und halten wichtige Verbraucher wie Kühlschrank, Heizung und Licht unterbrechungsfrei am Laufen.</li>
        <li style="margin-bottom:10px;"><strong>Optimale Einbindung von E-Auto und Wärmepumpe:</strong> Nutzen Sie den gespeicherten Solarstrom gezielt, um Ihr Elektrofahrzeug abends sauber und kostengünstig aufzuladen oder Ihre Wärmepumpe zu betreiben.</li>
    </ul>

    <!-- Dofollow External Authority Link for Rank Math -->
    <blockquote style="background:#f9f9f9; border-left:4px solid #0056b3; margin:30px 0; padding:20px; font-style:italic;">
        "Eine wissenschaftliche Analyse des <a href="https://www.ise.fraunhofer.de" target="_blank" rel="noopener dofollow" style="color:#0056b3; text-decoration:underline; font-weight:bold;">Fraunhofer-Instituts für Solare Energiesysteme (ISE)</a> bestätigt: Durch den gezielten Einsatz eines optimal dimensionierten PV Speichers lässt sich der Eigenverbrauchsanteil einer Wohngebäude-Photovoltaikanlage von rund 30 % auf bis zu 80 % steigern."
    </blockquote>

    <h2 id="dimensionierung-kapazitaet" style="font-size:1.8rem; color:#1a1a1a; margin-top:40px;">3. Die richtige Größe: Wie viel kWh PV Speicher brauchen Sie wirklich?</h2>
    <p>
        Eine der kritischsten Entscheidungen beim Thema <strong>PV Speicher kaufen</strong> ist die richtige Auslegung der Kapazität. Ist der Speicher zu klein bemessen, müssen Sie in den Abendstunden weiterhin teuren Netzstrom hinzukaufen. Ist der <strong>Heimspeicher</strong> hingegen stark überdimensioniert, steigen die Anschaffungskosten unnötig, ohne dass die Batterie in den ertragsarmen Wintermonaten voll geladen werden kann.
    </p>
    <p>
        Als bewährte Praxisregel für Einfamilienhäuser gilt: Pro 1.000 kWh jährlichem Stromverbrauch sollte die Nutzkapazität des PV Speichers ca. 1 bis 1,5 kWh betragen. Gleichzeitig sollte die Speicherkapazität harmonisch auf die Spitzenleistung (kWp) Ihrer Photovoltaikanlage abgestimmt sein.
    </p>

    <!-- AIO Clean Data Table -->
    <div style="overflow-x:auto; margin:30px 0;">
        <table style="width:100%; border-collapse:collapse; text-align:left; border:1px solid #e0e0e0; font-size:1rem;">
            <thead>
                <tr style="background:#0056b3; color:#fff;">
                    <th style="padding:14px; border:1px solid #ddd;">Jährlicher Stromverbrauch</th>
                    <th style="padding:14px; border:1px solid #ddd;">Empfohlene PV-Leistung</th>
                    <th style="padding:14px; border:1px solid #ddd;">Empfohlene PV Speicher Größe</th>
                    <th style="padding:14px; border:1px solid #ddd;">Erreichbare Autarkie</th>
                </tr>
            </thead>
            <tbody>
                <tr style="background:#ffffff;">
                    <td style="padding:12px; border:1px solid #ddd;">3.000 - 4.000 kWh</td>
                    <td style="padding:12px; border:1px solid #ddd;">4 - 6 kWp</td>
                    <td style="padding:12px; border:1px solid #ddd;"><strong>5 - 7 kWh</strong></td>
                    <td style="padding:12px; border:1px solid #ddd;">ca. 70 % - 78 %</td>
                </tr>
                <tr style="background:#f9f9f9;">
                    <td style="padding:12px; border:1px solid #ddd;">4.500 - 6.000 kWh</td>
                    <td style="padding:12px; border:1px solid #ddd;">7 - 10 kWp</td>
                    <td style="padding:12px; border:1px solid #ddd;"><strong>8 - 10 kWh</strong></td>
                    <td style="padding:12px; border:1px solid #ddd;">ca. 75 % - 83 %</td>
                </tr>
                <tr style="background:#ffffff;">
                    <td style="padding:12px; border:1px solid #ddd;">6.000 - 9.000 kWh (mit E-Auto / Wärmepumpe)</td>
                    <td style="padding:12px; border:1px solid #ddd;">10 - 15 kWp</td>
                    <td style="padding:12px; border:1px solid #ddd;"><strong>12 - 16 kWh</strong></td>
                    <td style="padding:12px; border:1px solid #ddd;">ca. 80 % - 88 %</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h2 id="technologie-vergleich" style="font-size:1.8rem; color:#1a1a1a; margin-top:40px;">4. Technologien im Vergleich: LFP (Lithium-Eisenphosphat) vs. NMC</h2>
    <p>
        Wenn Sie heute eine moderne <strong>Solarbatterie kaufen</strong>, treffen Sie auf unterschiedliche chemische Zelltechnologien. Die modernste und sicherste Technologie für den stationären Einsatz im Eigenheim ist die Lithium-Eisenphosphat-Zelle (LiFePO4 bzw. LFP).
    </p>
    <p>
        Im direkten Vergleich zu älteren NMC-Akkus (Lithium-Nickel-Mangan-Kobaltoxid) zeichnen sich LFP-PV-Speicher durch eine unübertroffene thermische und chemische Stabilität aus. Ein thermisches Durchgehen ist bei LFP-Zellen bauartbedingt nahezu ausgeschlossen. Darüber hinaus bieten hochwertige LFP-Systeme eine extrem hohe Zyklusfestigkeit von über 6.000 bis 8.000 vollständigen Ladezyklen. Das entspricht einer realistischen Lebensdauer von 15 bis 20 Jahren. Zudem verzichtet die LFP-Technologie vollständig auf das umstrittene Schwermetall Kobalt.
    </p>

    <h2 id="kosten-wirtschaftlichkeit" style="font-size:1.8rem; color:#1a1a1a; margin-top:40px;">5. PV Speicher Kosten, Förderung &amp; Amortisation im Überblick</h2>
    <p>
        Durch den rasanten technologischen Fortschritt und skalierende Produktionskapazitäten sind die Preise für <strong>PV Speicher</strong> in den vergangenen Jahren kontinuierlich gesunken. Gleichzeitig profitieren Immobilienbesitzer in Deutschland von staatlichen Vergünstigungen: Seit 2023 gilt gemäß § 12 Abs. 3 UStG ein Steuersatz von <strong>0 % Umsatzsteuer</strong> auf den Kauf und die Installation von PV-Anlagen und deren Stromspeichern auf Wohngebäuden. Sie sparen somit direkt 19 % bei der Anschaffung.
    </p>
    <p>
        Unter Berücksichtigung der eingesparten Netzstromkosten amortisiert sich ein hochwertiger <strong>Heimspeicher</strong> heute in der Regel bereits nach 7 bis 9 Jahren. Da moderne LFP-Speicher eine Nutzungsdauer von 15 bis 20 Jahren aufweisen, erwirtschaftet das System über seine verbleibende Lebensdauer hinweg erhebliche finanzielle Netto-Gewinne für Ihren Haushalt.
    </p>

    <!-- Secondary Call-to-Action Card -->
    <div style="background:#e8f4ff; border:2px solid #0056b3; padding:30px; border-radius:10px; text-align:center; margin:40px 0;">
        <h3 style="margin-top:0; color:#0056b3; font-size:1.5rem;">Lassen Sie sich von unseren Experten individuell beraten</h3>
        <p style="margin-bottom:20px; color:#333;">Jedes Gebäude und jedes Verbrauchsprofil verlangt nach einer maßgeschneiderten Lösung. Wir helfen Ihnen, den perfekt passenden PV Speicher zu finden.</p>
        <a href="___BERATUNG_URL___" style="display:inline-block; background:#28a745; color:#fff; padding:14px 32px; border-radius:6px; font-weight:bold; text-decoration:none; font-size:1.1rem;">Kostenlose Beratung anfragen</a>
    </div>

    <h2 id="faq-bereich" style="font-size:1.8rem; color:#1a1a1a; margin-top:40px;">6. Häufig gestellte Fragen (FAQ zum PV Speicher)</h2>
    <div class="werdu-faq-container" style="margin-top:20px;">
        <div style="margin-bottom:25px;">
            <h3 style="font-size:1.2rem; color:#0056b3; margin-bottom:8px;">Kann ich einen PV Speicher nachträglich einbauen?</h3>
            <p>Ja, ein <strong>PV Speicher</strong> kann an nahezu jeder bestehenden Photovoltaikanlage problemlos nachgerüstet werden. Je nach vorhandener Anlagentechnik nutzt man hierfür AC-gekoppelte Systeme (ideal für die Nachrüstung) oder tauscht den vorhandenen Wechselrichter gegen einen hybriden DC-Wechselrichter aus.</p>
        </div>
        <div style="margin-bottom:25px;">
            <h3 style="font-size:1.2rem; color:#0056b3; margin-bottom:8px;">Wie lange hält eine moderne Solarbatterie?</h3>
            <p>Hochwertige LFP-<strong>Solarbatterien</strong> erreichen eine Lebensdauer von 15 bis 20 Jahren und bewältigen mühelos 6.000 bis 8.000 Ladezyklen. Selbst nach dieser langen Nutzungsdauer verfügen die Speicher meist noch über eine Restkapazität von mehr als 80 %.</p>
        </div>
        <div style="margin-bottom:25px;">
            <h3 style="font-size:1.2rem; color:#0056b3; margin-bottom:8px;">Funktioniert der PV Speicher auch bei einem Stromausfall?</h3>
            <p>Standardmäßige Netzeinspeise-Systeme schalten bei einem Stromausfall aus Sicherheitsgründen ab. Wenn Sie jedoch einen <strong>Heimspeicher kaufen</strong>, der über eine Notstrom- oder Ersatzstromfunktion verfügt, versorgt die Batterie Ihr Gebäude im Ernstfall vollautomatisch weiter.</p>
        </div>
        <div style="margin-bottom:25px;">
            <h3 style="font-size:1.2rem; color:#0056b3; margin-bottom:8px;">Lohnt sich das PV Speicher Kaufen auch in den Wintermonaten?</h3>
            <p>Ja. Auch im Ertragsarmem Winter nutzt der PV Speicher jeden Sonnenstrahl und fängt tagsüber Ertragsspitzen ab. Über das gesamte Kalenderjahr hinweg sorgt das Zusammenspiel aus PV-Anlage und Speicher für die höchstmögliche Gesamtrendite Ihrer Investition.</p>
        </div>
    </div>

    <!-- Final High-Converting Bottom Banner -->
    <div style="background:#1a1a1a; color:#fff; padding:40px 25px; border-radius:10px; text-align:center; margin-top:50px;">
        <h2 style="color:#fff; margin-top:0; font-size:2rem;">Starten Sie jetzt in Ihre energetische Unabhängigkeit</h2>
        <p style="font-size:1.15rem; max-width:750px; margin:0 auto 25px auto; color:#dcdfe3;">Sichern Sie sich die besten Konditionen für Ihren neuen PV Speicher. Unsere Fachberater analysieren Ihren Bedarf und erstellen ein maßgeschneidertes, unverbindliches Angebot.</p>
        <a href="___BERATUNG_URL___" style="display:inline-block; background:#ff9900; color:#1a1a1a; padding:16px 36px; border-radius:6px; text-decoration:none; font-weight:bold; font-size:1.2rem;">Jetzt unverbindliches Angebot anfordern</a>
    </div>

</section>

<!-- JSON-LD FAQ Schema for Google AIO Search Features -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Kann ich einen PV Speicher nachträglich einbauen?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Ja, ein PV Speicher kann an nahezu jeder bestehenden Photovoltaikanlage problemlos nachgerüstet werden. Je nach Vorraussetzung nutzt man AC- oder DC-gekoppelte Systeme."
      }
    },
    {
      "@type": "Question",
      "name": "Wie lange hält eine moderne Solarbatterie?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Hochwertige LFP-Solarbatterien erreichen eine Lebensdauer von 15 bis 20 Jahren und bewältigen über 6.000 bis 8.000 Ladezyklen."
      }
    },
    {
      "@type": "Question",
      "name": "Funktioniert der PV Speicher auch bei einem Stromausfall?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Ja, sofern Sie einen Heimspeicher mit integrierter Notstrom- oder Ersatzstromfunktion wählen, übernimmt die Batterie bei Netzausfall die unterbrechungsfreie Versorgung."
      }
    },
    {
      "@type": "Question",
      "name": "Lohnt sich das PV Speicher Kaufen auch in den Wintermonaten?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Ja. Auch im Winter nutzt der Speicher kurzzeitige Sonnenphasen, um Ertragsspitzen abzufangen und den Netzeinkauf zu reduzieren."
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
    if ( false !== strpos( $content, 'werdu-hero-seo-block' ) ) {
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
