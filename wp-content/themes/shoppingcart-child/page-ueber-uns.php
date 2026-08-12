<?php
/**
 * Template Name: Über uns
 * Description: Custom template for the Über uns page — inline CSS, geen externe bestanden nodig
 */

get_header();
?>

<style>
/* === WD ÜBER UNS INLINE CSS === */
.wd-ueber-wrap {
    max-width: 920px;
    margin: 0 auto;
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #333;
    line-height: 1.75;
    font-size: 16px;
}
.wd-ueber-wrap * { box-sizing: border-box; }

.wd-ueber-hero {
    background: linear-gradient(135deg, #1a5276 0%, #16a085 100%);
    color: #fff;
    padding: 65px 35px 55px;
    border-radius: 0 0 28px 28px;
    text-align: center;
    margin-bottom: 35px;
    position: relative;
    overflow: hidden;
}
.wd-ueber-hero::before {
    content: "";
    position: absolute;
    top: -40%; left: -40%;
    width: 180%; height: 180%;
    background: radial-gradient(circle, rgba(255,255,255,0.06) 1px, transparent 1px);
    background-size: 22px 22px;
    pointer-events: none;
}
.wd-ueber-hero h1 {
    font-size: 2.3em;
    margin: 0 0 12px;
    font-weight: 700;
    position: relative;
    z-index: 1;
    line-height: 1.2;
}
.wd-ueber-hero .wd-ueber-sub {
    font-size: 1.15em;
    opacity: 0.92;
    max-width: 620px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
    line-height: 1.6;
}
.wd-ueber-photo {
    width: 170px; height: 170px;
    border-radius: 50%;
    margin: 0 auto 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 4px solid rgba(255,255,255,0.35);
    box-shadow: 0 6px 25px rgba(0,0,0,0.25);
    overflow: hidden;
    position: relative;
    z-index: 1;
    background: linear-gradient(135deg, rgba(255,255,255,0.15), rgba(255,255,255,0.05));
}
.wd-ueber-photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
.wd-ueber-photo span { font-size: 13px; color: rgba(255,255,255,0.65); text-align: center; line-height: 1.4; }

.wd-ueber-trust {
    display: flex;
    justify-content: center;
    gap: 25px;
    flex-wrap: wrap;
    margin: 30px 0;
    padding: 22px 18px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 1px solid #e9ecef;
}
.wd-ueber-trust-item {
    text-align: center;
    font-size: 14px;
    color: #555;
    min-width: 95px;
}
.wd-ueber-trust-item strong {
    display: block;
    font-size: 26px;
    color: #ff6600;
    margin-bottom: 4px;
    font-weight: 700;
}

.wd-ueber-section { padding: 25px 0; }

.wd-ueber-box {
    padding: 28px 32px;
    margin-bottom: 28px;
    border-left: 5px solid #ff6600;
    background: #fafafa;
    border-radius: 0 12px 12px 0;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    position: relative;
}
.wd-ueber-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(0,0,0,0.07);
}
.wd-ueber-box-blue   { border-left-color: #1a5276; background: #e8f4f8; }
.wd-ueber-box-green  { border-left-color: #16a085; background: #e8f8f5; }
.wd-ueber-box-orange { border-left-color: #ff6600; background: #fff5eb; }

.wd-ueber-box h2 {
    color: #1a5276;
    font-size: 1.55em;
    margin: 0 0 16px;
    font-weight: 700;
    line-height: 1.3;
}
.wd-ueber-box h3 {
    color: #ff6600;
    font-size: 1.15em;
    margin: 20px 0 10px;
    font-weight: 600;
}
.wd-ueber-box p {
    font-size: 16px;
    margin: 0 0 14px;
    line-height: 1.75;
}
.wd-ueber-box p:last-child { margin-bottom: 0; }
.wd-ueber-box strong { color: #1a5276; }

.wd-ueber-fact {
    background: linear-gradient(135deg, #fff8e1, #fff3cd);
    border: 1px solid #ffe082;
    border-radius: 10px;
    padding: 18px 22px;
    margin: 18px 0;
    font-size: 15px;
    line-height: 1.7;
}
.wd-ueber-fact strong { color: #856404; }

.wd-ueber-timeline {
    position: relative;
    padding-left: 28px;
    margin: 18px 0;
}
.wd-ueber-timeline::before {
    content: "";
    position: absolute;
    left: 7px; top: 0; bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, #1a5276, #16a085, #ff6600);
    border-radius: 3px;
}
.wd-ueber-timeline-item {
    position: relative;
    padding: 16px 0 16px 22px;
    border-bottom: 1px dashed #ddd;
}
.wd-ueber-timeline-item:last-child { border-bottom: none; }
.wd-ueber-timeline-item::before {
    content: "";
    position: absolute;
    left: -24px; top: 20px;
    width: 12px; height: 12px;
    background: #ff6600;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 2px 6px rgba(255,102,0,0.25);
}
.wd-ueber-timeline-item strong {
    color: #1a5276;
    display: block;
    margin-bottom: 3px;
    font-size: 1.03em;
}
.wd-ueber-timeline-item span {
    color: #666;
    font-size: 14px;
    line-height: 1.6;
    display: block;
}

.wd-ueber-quote {
    font-style: italic;
    font-size: 1.3em;
    color: #1a5276;
    text-align: center;
    padding: 32px 38px;
    margin: 32px 0;
    border-top: 2px solid #1abc9c;
    border-bottom: 2px solid #1abc9c;
    background: linear-gradient(135deg, #f8fffe, #f0f9ff);
    border-radius: 8px;
    position: relative;
    line-height: 1.6;
}
.wd-ueber-quote::before {
    content: '"';
    font-size: 55px;
    color: #1abc9c;
    position: absolute;
    top: 8px; left: 18px;
    line-height: 1;
    font-family: Georgia, serif;
    opacity: 0.25;
}

.wd-ueber-promises {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin: 22px 0;
}
.wd-ueber-promise-card {
    background: #fff;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 22px 18px;
    text-align: center;
    transition: all 0.3s ease;
}
.wd-ueber-promise-card:hover {
    border-color: #ff6600;
    transform: translateY(-3px);
    box-shadow: 0 8px 18px rgba(0,0,0,0.07);
}
.wd-ueber-promise-icon {
    font-size: 34px;
    margin-bottom: 10px;
    line-height: 1;
}
.wd-ueber-promise-card h4 {
    color: #1a5276;
    font-size: 1.05em;
    margin: 0 0 8px;
    font-weight: 700;
}
.wd-ueber-promise-card p {
    font-size: 14px;
    color: #555;
    margin: 0;
    line-height: 1.6;
}

.wd-ueber-faq { margin: 25px 0; }
.wd-ueber-faq-item {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    margin-bottom: 10px;
    overflow: hidden;
    background: #fff;
}
.wd-ueber-faq-q {
    width: 100%;
    padding: 16px 20px;
    background: #f8f9fa;
    border: none;
    text-align: left;
    font-size: 15px;
    font-weight: 600;
    color: #1a5276;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.2s ease;
    font-family: inherit;
}
.wd-ueber-faq-q:hover { background: #e8f4f8; }
.wd-ueber-faq-q .wd-ueber-faq-icon {
    font-size: 20px;
    color: #ff6600;
    transition: transform 0.3s ease;
    font-weight: 700;
    flex-shrink: 0;
    margin-left: 12px;
}
.wd-ueber-faq-q.active .wd-ueber-faq-icon { transform: rotate(45deg); }
.wd-ueber-faq-a {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.35s ease, padding 0.35s ease;
    padding: 0 20px;
}
.wd-ueber-faq-a.active {
    max-height: 600px;
    padding: 16px 20px;
}
.wd-ueber-faq-a p {
    margin: 0;
    font-size: 15px;
    line-height: 1.7;
    color: #555;
}

.wd-ueber-cta {
    background: linear-gradient(135deg, #ff6600, #ff8533);
    color: #fff;
    padding: 42px 35px;
    border-radius: 16px;
    text-align: center;
    margin: 35px 0;
    position: relative;
    overflow: hidden;
}
.wd-ueber-cta::before {
    content: "";
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}
.wd-ueber-cta h2 {
    color: #fff;
    margin: 0 0 14px;
    font-size: 1.7em;
    font-weight: 700;
    position: relative;
    z-index: 1;
    line-height: 1.3;
}
.wd-ueber-cta p {
    font-size: 1.05em;
    margin: 0 0 22px;
    opacity: 0.95;
    position: relative;
    z-index: 1;
    max-width: 640px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}
.wd-ueber-cta-btns {
    display: flex;
    gap: 14px;
    justify-content: center;
    flex-wrap: wrap;
    position: relative;
    z-index: 1;
}
.wd-ueber-cta a {
    display: inline-block;
    background: #fff;
    color: #ff6600;
    padding: 13px 30px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 700;
    font-size: 15px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}
.wd-ueber-cta a:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.2);
}
.wd-ueber-cta a.wd-ueber-cta-sec {
    background: transparent;
    color: #fff;
    border: 2px solid rgba(255,255,255,0.55);
}
.wd-ueber-cta a.wd-ueber-cta-sec:hover {
    background: rgba(255,255,255,0.12);
    border-color: #fff;
}

.wd-ueber-hl {
    background: #fff3cd;
    padding: 2px 7px;
    border-radius: 4px;
    font-weight: 600;
    color: #856404;
}

.wd-ueber-box a {
    color: #ff6600;
    font-weight: 600;
    text-decoration: none;
    border-bottom: 1px solid transparent;
    transition: border-color 0.2s ease;
}
.wd-ueber-box a:hover { border-bottom-color: #ff6600; }

@media (max-width: 768px) {
    .wd-ueber-hero { padding: 40px 18px 35px; }
    .wd-ueber-hero h1 { font-size: 1.7em; }
    .wd-ueber-box { padding: 20px 18px; }
    .wd-ueber-trust { gap: 14px; padding: 18px 12px; }
    .wd-ueber-promises { grid-template-columns: 1fr; }
    .wd-ueber-timeline { padding-left: 18px; }
    .wd-ueber-cta-btns { flex-direction: column; }
    .wd-ueber-cta a { width: 100%; max-width: 280px; margin: 0 auto; }
    .wd-ueber-quote { font-size: 1.1em; padding: 24px 20px; }
}
</style>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Person",
      "name": "Gründer Werdu.de",
      "jobTitle": "Einkäufer & Gründer",
      "description": "Niederländer, 60 Jahre, 40+ Jahre Erfahrung in Einkauf, Qualitätsprüfung und Prototypentwicklung für Automobil- und Energiebranche.",
      "worksFor": {
        "@type": "Organization",
        "name": "Werdu.de",
        "url": "https://werdu.de",
        "email": "service@werdu.de"
      },
      "knowsAbout": ["Heimspeicher", "LiFePO4 Batterien", "Solarbatterien", "Energiespeicher", "PV Speicher"]
    },
    {
      "@type": "Organization",
      "name": "Werdu.de",
      "url": "https://werdu.de",
      "email": "service@werdu.de",
      "description": "Fairer Heimspeicher-Shop mit direkter Beratung. Kein Konzern — ein Mensch mit 40+ Jahren Erfahrung.",
      "sameAs": ["https://werdu.de"]
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Warum sind Ihre Batterien so viel günstiger als Marken wie deutschen Premium-Marken?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Die LiFePO4-A-Grade-Zellen in unseren Batterien stammen aus denselben Produktionslinien wie die in teuren Markenbatterien. Der Preisunterschied entsteht durch Vertriebsstrukturen, Marketingbudgets und mehrere Zwischenhändler — nicht durch bessere Zellen. Ich verkaufe direkt, ohne Showroom, ohne Vertriebsleiter mit Firmenwagen."
          }
        },
        {
          "@type": "Question",
          "name": "Wie kann ich sicher sein, dass die Qualität stimmt?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Ich habe die Fabriken in China persönlich besucht, die BMS-Systeme verglichen und die Zellproduktion geprüft. Ich verkaufe nur Produkte, die ich selbst an mein Haus anschließen würde. Bei Problemen schreiben Sie mir direkt — kein Callcenter, kein Chatbot."
          }
        },
        {
          "@type": "Question",
          "name": "Was ist mit der Garantie?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Unsere Produkte kommen mit Herstellergarantie. Da ich direkt importiere und keine teuren Zwischenhändler habe, kann ich diese Garantie an Sie weitergeben — ohne Aufschlag. Details finden Sie auf unserer Garantie-Seite."
          }
        },
        {
          "@type": "Question",
          "name": "Sind Sie ein Einzelunternehmer oder ein großes Unternehmen?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Ich bin ein Einzelunternehmer — ein Niederländer, 60 Jahre alt, der seit 2016 in Deutschland lebt. Ich habe keine Millionenfinanzierung, keine Anwaltsabteilung und keinen Showroom. Aber ich habe 40 Jahre Erfahrung im Einkauf und die Zeit, jeden Kunden persönlich zu betreuen."
          }
        },
        {
          "@type": "Question",
          "name": "Haben Sie selbst eine Solarbatterie?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Ja. Ich habe zwei Batterien auf meinem Haus in den Niederlanden betrieben — auf einer bestehenden 8-kW-Peak-PV-Anlage. Ich kenne das Gefühl der Unabhängigkeit und auch die Frustration über überteuerte Markenprodukte. Deshalb habe ich Werdu.de gegründet."
          }
        },
        {
          "@type": "Question",
          "name": "Wie funktioniert die Beratung?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Sie schreiben mir eine E-Mail an service@werdu.de oder nutzen das Beratungsformular. Ich antworte persönlich — meist innerhalb von 24 Stunden. Ich habe kein Callcenter, deshalb kann ich Ihnen auch am Wochenende antworten, wenn nötig."
          }
        }
      ]
    }
  ]
}
</script>

<div class="wd-ueber-wrap">

<!-- HERO -->
<div class="wd-ueber-hero">
    <div class="wd-ueber-photo">
        <span>Ihr Foto<br>170×170px</span>
    </div>
    <h1>Das bin ich — und das ist Werdu.de</h1>
    <p class="wd-ueber-sub">Ich bin kein Konzern. Ich bin kein Startup mit Millionenfinanzierung. Ich bin ein Mann mit 60 Jahren Lebenserfahrung, der weiß, was Qualität wert ist — und was sie kosten darf.</p>
</div>

<!-- TRUST BAR -->
<div class="wd-ueber-trust">
    <div class="wd-ueber-trust-item">
        <strong>40+</strong>
        Jahre Erfahrung
    </div>
    <div class="wd-ueber-trust-item">
        <strong>8</strong>
        Branchen
    </div>
    <div class="wd-ueber-trust-item">
        <strong>3</strong>
        Kontinente
    </div>
    <div class="wd-ueber-trust-item">
        <strong>1</strong>
        Ziel: Fairness
    </div>
</div>

<!-- SECTIONS -->
<div class="wd-ueber-section">

<!-- WARUM HEIMSPEICHER -->
<div class="wd-ueber-box wd-ueber-box-blue">
    <h2>Warum ich Heimspeicher verkaufe</h2>
    <p>Ich habe selbst zwei Batterien auf meinem Haus in den Niederlanden betrieben. 8 kW Peak-Leistung, eine bestehende PV-Anlage, und ich wollte endlich unabhängig sein vom Stromnetz. Ich weiß also ganz genau, wie sich das anfühlt — dieses Gefühl, wenn man zum ersten Mal sieht, wie der eigene Strom den Kühlschrank am Laufen hält, während draußen die Sonne scheint.</p>
    <p>Aber ich habe auch gesehen, was die Marktriesen verlangen. <span class="wd-ueber-hl">15.000 Euro für eine Batterie, die im Grunde aus denselben Zellen besteht wie eine für 3.000 Euro.</span> Das hat mich wütend gemacht. Und neugierig.</p>
    <p>Ich habe die Fabriken in China von innen gesehen. Ich habe geschaut, wie die Kabel verlegt werden. Ich habe die BMS-Systeme verglichen. Und ich habe verstanden: <strong>Das Herz jeder Batterie — die LiFePO4-Zellen — sind praktisch identisch.</strong> A-Grade ist A-Grade. Ob da ein deutsches Logo drauf steht oder nicht, ändert nichts an der Chemie.</p>
    <p><a href="/solarbatterien/">→ Unsere Solarbatterien im Überblick</a></p>
</div>

<!-- WAS ICH GELERNT HABE -->
<div class="wd-ueber-box wd-ueber-box-green">
    <h2>Was ich in meinem Leben gelernt habe</h2>
    <p>Ich bin kein Theoretiker. Ich bin Einkäufer von Beruf. Und ich habe in meinem Leben in Branchen gearbeitet, in denen man schnell verstehen muss, wo der wahre Wert liegt — und wo nur Marketing ist.</p>

    <div class="wd-ueber-timeline">
        <div class="wd-ueber-timeline-item">
            <strong>Lijm & Chemieindustrie (Niederlande)</strong>
            <span>Der teuerste Kleber ist nicht immer der beste. Qualität erkennt man am Ergebnis, nicht am Preisschild.</span>
        </div>
        <div class="wd-ueber-timeline-item">
            <strong>Solarmodule & Installation (Niederlande)</strong>
            <span>Ich arbeitete bei einem niederländischen Unternehmen, das Zonnepanelen-Systeme installierte. Ich sah, wie viel Aufschlag auf die Hardware gelegt wird, bevor sie beim Kunden ankommt.</span>
        </div>
        <div class="wd-ueber-timeline-item">
            <strong>Prototypen-Qualität bei Weltautomobilhersteller (Deutschland, seit 2016)</strong>
            <span>Ich war Mediator zwischen Ingenieuren und OEM-Zulieferern. Die Ingenieure wollten perfekte Qualität — die OEMs verstanden nicht immer, was technisch nötig war. Ich erklärte alles in klare Worte mit digitalen 3D-CAD-Zeichnungen und Materiallisten für Autositze, das Armaturenbrett, Kraftstoffleitungen. Welches Material darf über die Unterboden schleifen, ohne zu versagen? Das war mein Alltag.</span>
        </div>
        <div class="wd-ueber-timeline-item">
            <strong>EV-Hersteller Shanghai</strong>
            <span>Ich arbeitete für einen bekannten chinesischen EV-Hersteller in Shanghai. Dort habe ich verstanden, dass "Made in China" heute etwas anderes bedeutet als vor 20 Jahren.</span>
        </div>
        <div class="wd-ueber-timeline-item">
            <strong>Elektrische Komponenten Landmaschinen</strong>
            <span>Robustheit schlägt das schönste Datenblatt. Ein Produkt muss funktionieren — nicht nur gut aussehen.</span>
        </div>
    </div>

    <p>Und überall habe ich dasselbe gesehen: <strong>Der Preis sagt nichts über die Qualität.</strong> Der Preis sagt etwas über die Marke, über das Marketing, über die Vertriebsstruktur. Aber nicht über das Produkt selbst.</p>
</div>

<!-- CHINA FEITEN -->
<div class="wd-ueber-box wd-ueber-box-orange">
    <h2>Was ich in China gesehen habe — und was die Zahlen beweisen</h2>
    <p>Ich habe nicht extra nach China geflogen, um Batteriefabriken zu besuchen. Ich war dort, weil ich für einen bekannten chinesischen EV-Hersteller in <strong>Shanghai</strong> arbeitete. Und in meinen freien Tagen — am Wochenende, in der Abenddämmerung — bin ich in die Fabriken gefahren, die Heimspeicher bauen. Nicht als Tourist. Als Einkäufer. Mit dem Blick eines Mannes, der 40 Jahre lang gelernt hat, Qualität zu erkennen, bevor der Verkäufer sie ihm erklärt.</p>

    <div class="wd-ueber-fact">
        <strong>Fakt:</strong> China produziert über <strong>80 %</strong> aller Lithium-Ionen-Batteriezellen weltweit. Die IEA bestätigt: China hält über 80 % der globalen Produktionskapazität. Korea und Japan folgen mit weit geringeren Anteilen. Das bedeutet: Die Zellen in Ihrer teuren Markenbatterie kommen höchstwahrscheinlich aus China — egal welches Logo auf dem Gehäuse steht.
    </div>

    <p>Ich habe die Zellproduktion gesehen. Die Prüfstände. Die Laboratorien. Ich habe gefragt, ich habe nachgehakt, ich habe mir die BMS-Platinen angeschaut. Und ich habe gelernt:</p>
    <p><strong>Die A-Grade-Zellen mit QR-Code-Zertifizierung, die in eine 15.000-Euro-Batterie von deutschen Premium-Marken gehen, stammen aus denselben Produktionslinien wie die Zellen, die in eine 3.000-Euro-Batterie von Werdu.de gehen.</strong></p>

    <div class="wd-ueber-fact">
        <strong>Fakt:</strong> Die größten chinesischen Zellhersteller allein kontrollieren zusammen rund <strong>55 %</strong> des globalen EV-Batteriemarkts. Die Top-6 chinesischen Hersteller kommen auf über 70 %. Wenn Sie eine A-Grade-Zelle kaufen, kaufen Sie im Grunde chinesische Qualität — ob das Ihnen die Marke sagt oder nicht.
    </div>

    <p>Der Unterschied? Das Gehäuse. Die Software. Das Logo. Der Vertriebsaufschlag. Die Garantieabwicklung über drei Ebenen.</p>
    <p>Die Zellen aber — das Herzstück — sind identisch. Und sie sind das bei Weitem teuerste Element jeder ESS. Wenn die Zellen gleich sind, warum sollten dann die Preise um das Fünffache auseinanderliegen?</p>
    <p><a href="/lifepo4-sicherheit-die-sicherste-solarbatterie-technologie-2026/">→ Mehr über LiFePO4-Sicherheit</a></p>
</div>

<!-- WARUM ANDERS -->
<div class="wd-ueber-box wd-ueber-box-blue">
    <h2>Warum Werdu.de anders ist</h2>
    <p>Ich bin ein Anfänger in diesem Markt. Das gebe ich offen zu. Ich habe keine 10.000 verkaufte Batterien hinter mir. Ich habe keine Millionenumsätze. Ich habe keine Anwaltsabteilung.</p>
    <p>Aber ich habe etwas, was die Großen nicht haben: <strong>Den Drang, es richtig zu machen.</strong> Den Stolz, jedem Kunden persönlich zu antworten. Die Zeit, jedes Produkt selbst zu prüfen. Und die Erfahrung aus vier Jahrzehnten Einkauf, um zu wissen, wo der wahre Wert liegt.</p>
    <p>Bei Werdu.de zahlst du nicht für ein schickes Showroom-Erlebnis. Du zahlst nicht für den Vertriebsleiter mit dem Firmenwagen. Du zahlst nicht für die Werbekampagne im Fernsehen.</p>
    <p><strong>Du zahlst für die Batterie. Für die Zellen. Für das BMS. Für das, was wirklich zählt.</strong></p>
    <p>Und wenn etwas nicht stimmt? Dann schreibst du mir eine E-Mail. Nicht an ein Callcenter. Nicht an einen Chatbot. An mich. Persönlich. Weil ich noch klein genug bin, um mich um jeden einzelnen Kunden zu kümmern.</p>
    <p><a href="/garantie/">→ Unsere Garantiebedingungen</a></p>
</div>

<!-- QUOTE -->
<div class="wd-ueber-quote">
    Ich verkaufe keine Batterien. Ich verkaufe Unabhängigkeit — zu einem Preis, bei dem man nicht erst 15 Jahre sparen muss, um ihn zu amortisieren.
</div>

<!-- PROMISES -->
<div class="wd-ueber-box wd-ueber-box-green">
    <h2>Was ich dir verspreche</h2>
    <p>Ich bin 60 Jahre alt. Ich bin Niederländer. Ich lebe in Deutschland seit 2016. Ich habe gesehen, wie Industrie funktioniert — und wie sie Kunden übers Ohr haut.</p>

    <div class="wd-ueber-promises">
        <div class="wd-ueber-promise-card">
            <div class="wd-ueber-promise-icon">🤝</div>
            <h4>Ehrlichkeit</h4>
            <p>Ich sage dir, was die Batterie kann — und was sie nicht kann. Keine übertriebenen Lebensdauerversprechen. Keine unrealistischen Ertragsprognosen.</p>
        </div>
        <div class="wd-ueber-promise-card">
            <div class="wd-ueber-promise-icon">⚡</div>
            <h4>Qualität</h4>
            <p>Ich verkaufe nur Produkte, die ich selbst kaufen würde. Die ich an mein eigenes Haus anschließen würde. Die ich meinem Nachbarn empfehlen würde.</p>
        </div>
        <div class="wd-ueber-promise-card">
            <div class="wd-ueber-promise-icon">💶</div>
            <h4>Fairness</h4>
            <p>Ein fairer Preis für ein faires Produkt. Keine versteckten Kosten. Keine überraschenden Zusatzgebühren. Kein "Premium" ohne Grund.</p>
        </div>
    </div>

    <p style="text-align:center; margin-top:22px;"><a href="/solarbatterie-rechner/">→ Kostenloser Solarbatterie-Rechner</a></p>
</div>

<!-- FAQ -->
<div class="wd-ueber-box wd-ueber-box-orange">
    <h2>Häufige Fragen — ehrlich beantwortet</h2>
    <div class="wd-ueber-faq">
        <div class="wd-ueber-faq-item">
            <button class="wd-ueber-faq-q" onclick="this.classList.toggle('active'); this.nextElementSibling.classList.toggle('active');">
                Warum sind Ihre Batterien so viel günstiger als Marken wie deutschen Premium-Marken?
                <span class="wd-ueber-faq-icon">+</span>
            </button>
            <div class="wd-ueber-faq-a">
                <p>Die LiFePO4-A-Grade-Zellen in unseren Batterien stammen aus denselben Produktionslinien wie die in teuren Markenbatterien. Der Preisunterschied entsteht durch Vertriebsstrukturen, Marketingbudgets und mehrere Zwischenhändler — nicht durch bessere Zellen. Ich verkaufe direkt, ohne Showroom, ohne Vertriebsleiter mit Firmenwagen.</p>
            </div>
        </div>
        <div class="wd-ueber-faq-item">
            <button class="wd-ueber-faq-q" onclick="this.classList.toggle('active'); this.nextElementSibling.classList.toggle('active');">
                Wie kann ich sicher sein, dass die Qualität stimmt?
                <span class="wd-ueber-faq-icon">+</span>
            </button>
            <div class="wd-ueber-faq-a">
                <p>Ich habe die Fabriken in China persönlich besucht, die BMS-Systeme verglichen und die Zellproduktion geprüft. Ich verkaufe nur Produkte, die ich selbst an mein Haus anschließen würde. Bei Problemen schreiben Sie mir direkt — kein Callcenter, kein Chatbot.</p>
            </div>
        </div>
        <div class="wd-ueber-faq-item">
            <button class="wd-ueber-faq-q" onclick="this.classList.toggle('active'); this.nextElementSibling.classList.toggle('active');">
                Was ist mit der Garantie?
                <span class="wd-ueber-faq-icon">+</span>
            </button>
            <div class="wd-ueber-faq-a">
                <p>Unsere Produkte kommen mit Herstellergarantie. Da ich direkt importiere und keine teuren Zwischenhändler habe, kan ich diese Garantie an Sie weitergeben — ohne Aufschlag. Details finden Sie auf unserer Garantie-Seite.</p>
            </div>
        </div>
        <div class="wd-ueber-faq-item">
            <button class="wd-ueber-faq-q" onclick="this.classList.toggle('active'); this.nextElementSibling.classList.toggle('active');">
                Sind Sie ein Einzelunternehmer oder ein großes Unternehmen?
                <span class="wd-ueber-faq-icon">+</span>
            </button>
            <div class="wd-ueber-faq-a">
                <p>Ich bin ein Einzelunternehmer — ein Niederländer, 60 Jahre alt, der seit 2016 in Deutschland lebt. Ich habe keine Millionenfinanzierung, keine Anwaltsabteilung und keinen Showroom. Aber ich habe 40 Jahre Erfahrung im Einkauf und die Zeit, jeden Kunden persönlich zu betreuen.</p>
            </div>
        </div>
        <div class="wd-ueber-faq-item">
            <button class="wd-ueber-faq-q" onclick="this.classList.toggle('active'); this.nextElementSibling.classList.toggle('active');">
                Haben Sie selbst eine Solarbatterie?
                <span class="wd-ueber-faq-icon">+</span>
            </button>
            <div class="wd-ueber-faq-a">
                <p>Ja. Ich habe zwei Batterien auf meinem Haus in den Niederlanden betrieben — auf einer bestehenden 8-kW-Peak-PV-Anlage. Ich kenne das Gefühl der Unabhängigkeit und auch die Frustration über überteuerte Markenprodukte. Deshalb habe ich Werdu.de gegründet.</p>
            </div>
        </div>
        <div class="wd-ueber-faq-item">
            <button class="wd-ueber-faq-q" onclick="this.classList.toggle('active'); this.nextElementSibling.classList.toggle('active');">
                Wie funktioniert die Beratung?
                <span class="wd-ueber-faq-icon">+</span>
            </button>
            <div class="wd-ueber-faq-a">
                <p>Sie schreiben mir eine E-Mail an service@werdu.de oder nutzen das Beratungsformular. Ich antworte persönlich — meist innerhalb von 24 Stunden. Ich habe kein Callcenter, deshalb kann ich Ihnen auch am Wochenende antworten, wenn nötig.</p>
            </div>
        </div>
    </div>
</div>

</div>

<!-- CTA -->
<div class="wd-ueber-cta">
    <h2>Noch unsicher? Frag mich.</h2>
    <p>Schreib mir eine E-Mail. Oder nutze das Beratungsformular. Ich antworte persönlich — nicht ein Algorithmus, nicht ein Praktikant, nicht ein Chatbot der nur Keywords erkennt.</p>
    <p>Ich bin ein Mensch, der Batterien verkauft, weil er selbst welche haben wollte und festgestellt hat, dass der Markt nicht fair ist. Vielleicht ist das genau der Grund, warum du bei mir kaufen solltest.</p>
    <div class="wd-ueber-cta-btns">
        <a href="/beratung-anfragen/">Jetzt Beratung anfragen</a>
        <a href="/shop/" class="wd-ueber-cta-sec">Produkte ansehen</a>
    </div>
</div>

</div>

<?php
get_footer();