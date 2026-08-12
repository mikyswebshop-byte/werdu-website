<?php
/**
 * Werdu.de Chat AI Floating Button v3.1
 * Fixes:
 * - Header ALTIJD zichtbaar op mobiel (top offset voor browser chrome)
 * - Venster start op mobiel lager (top: 60px), niet fullscreen
 * - Klikbare links in ALLE antwoorden
 * - Venster sluitbaar via X-knop EN buiten klik
 */

add_action( 'wp_footer', function () {
	if ( is_admin() || wp_doing_ajax() ) return;
	?>
<style>
/* ===== CHAT AI BUTTON ===== */
.wd-ai-float {
    position: fixed;
    bottom: 80px;
    right: 20px;
    z-index: 9999;
}
.wd-ai-btn {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #ff6600;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    font-size: 24px;
    color: #fff;
    padding: 0;
    margin: 0;
}
.wd-ai-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0,0,0,0.4);
}

/* ===== CHAT WINDOW ===== */
.wd-ai-box {
    position: fixed;
    bottom: 150px;
    right: 20px;
    width: 380px;
    height: 500px;
    max-height: calc(100vh - 180px);
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    display: none;
    flex-direction: column;
    z-index: 9998;
    overflow: hidden;
    border: 1px solid #e5e5e5;
}
.wd-ai-box.open { display: flex; }

/* HEADER — ALTIJD zichtbaar */
.wd-ai-header {
    background: #1a1a2e;
    color: #fff;
    padding: 12px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    min-height: 48px;
    box-sizing: border-box;
    position: relative;
    z-index: 10;
}
.wd-ai-header strong {
    font-size: 15px;
    color: #fff;
    font-weight: 600;
}
.wd-ai-close {
    background: none;
    border: none;
    color: #fff;
    font-size: 24px;
    cursor: pointer;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    padding: 0;
    line-height: 1;
    transition: background 0.2s ease;
}
.wd-ai-close:hover { background: rgba(255,255,255,0.15); }

/* BODY — scrollbaar */
.wd-ai-body {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: #f8f9fa;
    min-height: 0;
    -webkit-overflow-scrolling: touch;
}
.wd-ai-body::-webkit-scrollbar { width: 6px; }
.wd-ai-body::-webkit-scrollbar-track { background: transparent; }
.wd-ai-body::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }

/* BERICHTEN */
.wd-ai-msg {
    max-width: 85%;
    padding: 10px 14px;
    border-radius: 14px;
    font-size: 13px;
    line-height: 1.6;
    word-wrap: break-word;
    word-break: break-word;
}
.wd-ai-msg.user {
    align-self: flex-end;
    background: #ff6600;
    color: #fff;
    border-bottom-right-radius: 4px;
}
.wd-ai-msg.bot {
    align-self: flex-start;
    background: #fff;
    color: #333;
    border: 1px solid #e5e5e5;
    border-bottom-left-radius: 4px;
}

/* LINKS in bot berichten */
.wd-ai-msg.bot a {
    color: #ff6600;
    text-decoration: underline;
    font-weight: 600;
    display: inline;
}
.wd-ai-msg.bot a:hover {
    color: #e55a00;
}
.wd-ai-msg.bot a.wd-ai-link-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ff6600;
    color: #fff;
    padding: 8px 14px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.85rem;
    margin-top: 8px;
    transition: all 0.2s ease;
}
.wd-ai-msg.bot a.wd-ai-link-btn:hover {
    background: #e55a00;
    transform: translateY(-1px);
}

/* LIJSTEN in bot berichten */
.wd-ai-msg.bot ul {
    margin: 8px 0;
    padding-left: 18px;
}
.wd-ai-msg.bot li {
    margin-bottom: 4px;
}

/* INPUT */
.wd-ai-input-wrap {
    display: flex;
    padding: 10px 14px;
    background: #fff;
    border-top: 1px solid #e5e5e5;
    gap: 8px;
    flex-shrink: 0;
}
.wd-ai-input {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 18px;
    padding: 8px 14px;
    font-size: 13px;
    outline: none;
    min-width: 0;
    font-family: inherit;
}
.wd-ai-input:focus { border-color: #ff6600; }
.wd-ai-send {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #ff6600;
    color: #fff;
    border: none;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    padding: 0;
    transition: background 0.2s ease;
}
.wd-ai-send:hover { background: #e55a00; }

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .wd-ai-float { bottom: 70px; right: 15px; }
    .wd-ai-btn { width: 50px; height: 50px; font-size: 20px; }
    .wd-ai-box {
        width: calc(100vw - 30px);
        max-width: 400px;
        height: 450px;
        max-height: calc(100vh - 140px);
        bottom: 130px;
        right: 15px;
    }
}

/* MOBIEL: venster start LAGER, niet fullscreen */
@media (max-width: 480px) {
    .wd-ai-float { bottom: 65px; right: 10px; }
    .wd-ai-btn { width: 48px; height: 48px; font-size: 18px; }
    .wd-ai-box {
        /* BELANGRIJK: niet fullscreen, maar met top offset */
        top: 60px;           /* Start 60px vanaf bovenkant = onder browser chrome */
        bottom: 10px;        /* 10px vanaf onderkant */
        right: 10px;
        left: 10px;
        width: auto;
        height: auto;
        max-height: calc(100vh - 70px);  /* Ruimte voor browser chrome */
        border-radius: 16px;
    }
    .wd-ai-header {
        padding: 14px 16px;
        min-height: 52px;
        border-radius: 16px 16px 0 0;
    }
    .wd-ai-header strong { font-size: 16px; }
    .wd-ai-body { padding: 12px; }
    .wd-ai-input-wrap { padding: 10px 12px; }
    .wd-ai-input { font-size: 16px; }
    .wd-ai-msg { font-size: 14px; max-width: 90%; }
}
</style>

<!-- Chat AI Button -->
<div class="wd-ai-float">
    <button class="wd-ai-btn" onclick="wdAiChatOpen()" title="Chat oeffnen" aria-label="Chat oeffnen">&#128172;</button>
</div>

<!-- Chat Window -->
<div class="wd-ai-box" id="wdAiChatBox">
    <div class="wd-ai-header">
        <strong>&#9889; Werdu.de Berater</strong>
        <button class="wd-ai-close" onclick="wdAiChatClose()" aria-label="Chat schliessen">&times;</button>
    </div>
    <div class="wd-ai-body" id="wdAiChatBody">
        <div class="wd-ai-msg bot">Hallo! Ich bin Ihr digitaler Berater fuer Heimspeicher und Solarbatterien. Fragen Sie mich zu Produkten, Preisen, Technik, Versand, Garantie oder Foerderung.</div>
    </div>
    <div class="wd-ai-input-wrap">
        <input type="text" class="wd-ai-input" id="wdAiChatInput" placeholder="Frage eingeben..." onkeydown="if(event.key==='Enter')wdAiChatSend()">
        <button class="wd-ai-send" onclick="wdAiChatSend()" aria-label="Senden">&#10148;</button>
    </div>
</div>

<script data-noptimize="1">
/* <![CDATA[ */
(function(){
    window.wdAiChatOpen = function() {
        var box = document.getElementById('wdAiChatBox');
        if (box) {
            box.classList.add('open');
            document.body.style.overflow = 'hidden';
            var input = document.getElementById('wdAiChatInput');
            if (input) setTimeout(function(){ input.focus(); }, 300);
        }
    };
    window.wdAiChatClose = function() {
        var box = document.getElementById('wdAiChatBox');
        if (box) {
            box.classList.remove('open');
            document.body.style.overflow = '';
        }
    };

    // Sluit bij klik buiten het venster
    document.addEventListener('click', function(e) {
        var box = document.getElementById('wdAiChatBox');
        var btn = document.querySelector('.wd-ai-btn');
        if (box && box.classList.contains('open')) {
            if (!box.contains(e.target) && !btn.contains(e.target)) {
                wdAiChatClose();
            }
        }
    });

    var WD_KB = [
        { keys:"produkt|speicher|batterie|heimspeicher|solarbatterie|angebot|kaufen|bestellen", 
          ans:"Wir bieten mehrere Heimspeicher an:<br><br>&bull; <a href=\"/16-kwh-512v-lifepo4-heimspeicher/\">16 kWh 51,2V</a> - ca. 2.345,-&euro;<br>&bull; <a href=\"/15-kwh-all-in-one-heimspeicher/\">15 kWh All-in-One</a> - 2.899,-&euro;<br>&bull; <a href=\"/30-32-kwh-lifepo4-heimspeicher/\">30-32 kWh</a> - 3.499,-&euro;<br>&bull; <a href=\"/10-kwh-sodium-ion-heimspeicher/\">10 kWh Sodium-Ion</a><br>&bull; <a href=\"/gotion-340ah-lifepo4-zellen/\">GOTION 340Ah Zellen</a><br><br>Alle: 10 Jahre Garantie, 8.000 Zyklen, LiFePO4 Technologie.<br><br><a href=\"/shop/\" class=\"wd-ai-link-btn\">&#128722; Zum Shop</a>" },

        { keys:"preis|kosten|preise|wie viel|guenstig|sparen|billig|teuer", 
          ans:"Unsere Preise (direkt ab Werk):<br><br>&bull; <a href=\"/16-kwh-512v-lifepo4-heimspeicher/\">16 kWh</a> ab ca. 2.345,-&euro;<br>&bull; <a href=\"/15-kwh-all-in-one-heimspeicher/\">All-in-One</a> ab 2.899,-&euro;<br>&bull; <a href=\"/30-32-kwh-lifepo4-heimspeicher/\">30-32 kWh</a> ab 3.499,-&euro;<br><br>Im Vergleich zu Markenherstellern sparen Sie bis zu 1.000 Euro. Mit KfW-Foerderung bis zu 15% Zuschuss.<br><br><a href=\"/shop/\" class=\"wd-ai-link-btn\">&#128722; Produkte ansehen</a>" },

        { keys:"versand|lieferung|liefert|wie lange|delivery|senden|versenden", 
          ans:"Kostenloser Versand fuer alle Heimspeicher!<br><br>&bull; EU-Lager: 48h Versand, 5-10 Werktage Lieferung<br>&bull; China: 10-15 Tage Produktion + 30-45 Tage Seetransport<br><br>Sie sparen bis zu 150 Euro Versandkosten.<br><br><a href=\"/beratung-anfragen/\" class=\"wd-ai-link-btn\">&#9993; Beratung anfragen</a>" },

        { keys:"garantie|gewaehrleistung|reklamation|defekt|kaputt", 
          ans:"10 Jahre Garantie auf alle Produkte.<br><br>LiFePO4 Zellen: mindestens 8.000 Ladezyklen = ueber 20 Jahre Lebensdauer. Bei korrekter Lagerung und Nutzung innerhalb der Spezifikationen ist die Garantie vollstaendig gueltig.<br><br><a href=\"/beratung-anfragen/\" class=\"wd-ai-link-btn\">&#9993; Garantieanfrage</a>" },

        { keys:"installation|installieren|anschluss|plug|play|diy|montage|einbauen|anschliessen|installateur|montagepartner|elektriker|verkabelung|dc-anschluss|can|rs485", 
          ans:"Viele unserer Speicher sind Plug &amp; Play: In der Praxis geht es um den Anschluss von Plus-/Minus-Kabel (DC) und dem Datenkabel zur Kommunikation.<br><br><strong>Technisch:</strong> DC-Verkabelung zum Hybrid-/Batteriewechselrichter sowie BMS-Kommunikation typischerweise via CAN oder RS485.<br><br><strong>Optional:</strong> Auf Wunsch vermitteln wir einen lokalen Montage-Partner fuer den schnellen Anschluss. Richtwert nach Marktcheck (Juli 2026) fuer reine Anschlussarbeiten: ca. 300&ndash;500&nbsp;&euro; &mdash; Abrechnung direkt mit dem Installateur, keine Pflichtgebuehr im Shop.<br><br><a href=\"/beratung-anfragen/?installer=1\" class=\"wd-ai-link-btn\">&#9993; Montagepartner-Vermittlung anfragen</a> <a href=\"/heimspeicher-installation/\" class=\"wd-ai-link-btn\">&#128214; Installationsanleitung</a>" },

        { keys:"montagepartner|lokaler installateur|montageaufwand|300|500|richtwert montage", 
          ans:"Optional vermitteln wir einen lokalen Montage-Partner fuer den DC- &amp; Kommunikationsanschluss (Plus/Minus-Kabel und Datenkabel; technisch: DC-Verkabelung + BMS via CAN/RS485).<br><br>Marktcheck Juli 2026: ca. 300&ndash;500&nbsp;&euro; Richtwert fuer reine Anschlussarbeiten &mdash; unverbindlich, Zahlung direkt an den Installateur.<br><br><a href=\"/beratung-anfragen/?installer=1\" class=\"wd-ai-link-btn\">&#9993; Vermittlung anfragen</a> <a href=\"/kasse/\" class=\"wd-ai-link-btn\">&#128722; Option an der Kasse waehlen</a>" },

        { keys:"technik|lifepo4|zyklen|entladetiefe|dod|bms|sicherheit|technologie|zellen|akkus", 
          ans:"LiFePO4 - sicherste Batterietechnologie:<br><br>&bull; 8.000 Zyklen<br>&bull; 100% Entladetiefe (DoD)<br>&bull; Kein thermisches Durchgehen<br>&bull; Integriertes BMS mit Balancierung<br>&bull; -20 Grad bis +60 Grad<br>&bull; Kein Kobalt - umweltfreundlich<br><br>Unsere Zellen sind Grade-A mit QR-Code zur Echtheitspruefung.<br><br><a href=\"/blog/lifepo4-technologie/\" class=\"wd-ai-link-btn\">&#128214; Mehr zur Technik</a>" },

        { keys:"sodium|natrium|natrium-ion|temperatur", 
          ans:"Unser <a href=\"/10-kwh-sodium-ion-heimspeicher/\">10 kWh Sodium-Ion Speicher</a> ist extrem temperaturstabil (-40 Grad bis +60 Grad) mit 4.000 Zyklen. Inklusive 5 kW Hybrid-Wechselrichter.<br><br><a href=\"/10-kwh-sodium-ion-heimspeicher/\" class=\"wd-ai-link-btn\">&#128722; Produkt ansehen</a>" },

        { keys:"kontakt|email|telefon|erreichen|anfrage|beratung|hilfe|support|service", 
          ans:"Kontakt:<br><br>&bull; E-Mail: <a href=\"mailto:service@werdu.de\">service@werdu.de</a><br>&bull; Beratung: <a href=\"/beratung-anfragen/\">Formular oeffnen</a><br><br>Telefonisch bald erreichbar - wir warten auf Finanzamt-Registrierung.<br><br><a href=\"/beratung-anfragen/\" class=\"wd-ai-link-btn\">&#9993; Beratung anfragen</a>" },

        { keys:"foerderung|kfw|zuschuss|subvention|staatlich|foerderprogramm", 
          ans:"KfW foerdert Heimspeicher mit bis zu 15% Zuschuss.<br><br>Voraussetzungen:<br>&bull; Erstinstallation<br>&bull; Wohngebaeude<br>&bull; Max. 50 kWh Speicherkapazitaet<br>&bull; In Kombination mit PV-Anlage<br><br><a href=\"/beratung-anfragen/\" class=\"wd-ai-link-btn\">&#9993; Foerderung beantragen</a>" },

        { keys:"autarkie|eigenverbrauch|unabhaengig|stromnetz|selbstversorgung", 
          ans:"Mit einem 15-16 kWh Heimspeicher erreichen Sie je nach Verbrauch einen Autarkiegrad von 60-80%.<br><br>Mit 30+ kWh bis zu 90% Autarkie. Die Amortisation liegt typischerweise bei 8-12 Jahren.<br><br><a href=\"/solarbatterie-rechner/\" class=\"wd-ai-link-btn\">&#128200; Kostenloser Rechner</a>" },

        { keys:"rechner|calculator|berechnen|ersparnis|amortisation|jaehrlicher ertrag", 
          ans:"Unser <a href=\"/solarbatterie-rechner/\">kostenloser Rechner</a> berechnet:<br><br>&bull; Jaehrlicher Ertrag<br>&bull; Eigenverbrauch<br>&bull; Autarkiegrad<br>&bull; Amortisation<br>&bull; Ersparnis ueber 20 Jahre<br><br>Basiert auf Ihrer PLZ, PV-Leistung, Dachneigung und Verbrauch.<br><br><a href=\"/solarbatterie-rechner/\" class=\"wd-ai-link-btn\">&#128200; Jetzt berechnen</a>" },

        { keys:"gotion|zellen|340ah|grade a|diy|zellen kaufen", 
          ans:"<a href=\"/gotion-340ah-lifepo4-zellen/\">GOTION 340Ah LiFePO4</a> - Grade A mit QR-Code:<br><br>&bull; 3,2V, 340Ah pro Zelle<br>&bull; 6.000+ Zyklen<br>&bull; Ideal fuer DIY-Speicher<br>&bull; Echtheitspruefung via QR-Code<br><br>Perfekt fuer Eigenbau-Projekte.<br><br><a href=\"/gotion-340ah-lifepo4-zellen/\" class=\"wd-ai-link-btn\">&#128722; Zellen bestellen</a>" },

        { keys:"tewaycell|hersteller|marke|werdu|acc", 
          ans:"Tewaycell ist unser Premium-Hersteller fuer mobile ESS-Systeme. Deutsche Qualitaetskontrolle, 10 Jahre Garantie, 8.000 Zyklen. Werdu.de ist Ihr direkter Vertriebspartner - ohne Zwischenhaendler, faire Preise.<br><br><a href=\"/ueber-uns/\" class=\"wd-ai-link-btn\">&#128100; Ueber uns</a>" },

        { keys:"widerruf|retoure|rueckgabe|ruecksendung|umtausch", 
          ans:"14 Tage Widerrufsrecht ab Erhalt der Ware. Details: <a href=\"/widerrufsbelehrung/\">Widerrufsbelehrung</a><br><br>Bei Defekt innerhalb der Garantiezeit: Kostenloser Austausch oder Reparatur.<br><br><a href=\"/ruecksendung/\" class=\"wd-ai-link-btn\">&#128230; Ruecksendung starten</a>" },

        { keys:"datenschutz|dsgvo|daten|privatsphaere", 
          ans:"Ihre Daten werden nur zur Bearbeitung Ihrer Anfrage verwendet und nicht an Dritte weitergegeben. Rechtsgrundlage ist Ihre Einwilligung (Art. 6 Abs. 1 lit. a DSGVO). <a href=\"/datenschutzerklaerung/\">Datenschutzerklaerung</a>" },

        { keys:"agb|geschaeftsbedingungen|rechtliches", 
          ans:"<a href=\"/agb/\">AGB</a> einsehbar. Bei Fragen: <a href=\"mailto:service@werdu.de\">service@werdu.de</a>" },

        { keys:"impressum|unternehmen|firma|wer betreibt", 
          ans:"Betreiber: <a href=\"/impressum/\">ACC Heimspeicher</a><br>E-Mail: <a href=\"mailto:service@werdu.de\">service@werdu.de</a><br><br><a href=\"/impressum/\" class=\"wd-ai-link-btn\">&#128195; Impressum ansehen</a>" },

        { keys:"entladen|entladetiefe|dod|tiefentladung|100%|tiefe|zyklen|lebensdauer", 
          ans:"Unsere LiFePO4-Speicher sind fuer 100% Entladetiefe (DoD) ausgelegt. Sie koennen den gesamten Speicher nutzen, ohne die Lebensdauer signifikant zu verkuerzen.<br><br>Bei taeglichem vollen Zyklus erreichen Sie 8.000 Zyklen = ueber 20 Jahre. Tipp: Fuer maximale Lebensdauer empfehlen wir einen SOC-Bereich von 10-90%.<br><br><a href=\"/blog/lifepo4-laden-pflege/\" class=\"wd-ai-link-btn\">&#128214; Pflegetipps</a>" },

        { keys:"bms|management|balancierung|ueberwachung|app|steuerung|battery management", 
          ans:"Unsere BMS-Systeme (Battery Management System) schuetzen vor:<br><br>&bull; Ueberladung<br>&bull; Tiefentladung<br>&bull; Ueberhitzung<br>&bull; Kurzschluss<br><br>Sie balancieren automatisch alle Zellen und sind per App ueberwachbar. Alle wichtigen Parameter sind in Echtzeit einsehbar.<br><br><a href=\"/beratung-anfragen/\" class=\"wd-ai-link-btn\">&#9993; Technische Beratung</a>" },

        { keys:"hallo|hi|guten tag|moin|servus|hey", 
          ans:"Hallo! Ich bin Ihr digitaler Berater fuer Heimspeicher und Solarbatterien.<br><br>Fragen Sie mich zu:<br>&bull; <a href=\"/shop/\">Produkten und Preisen</a><br>&bull; <a href=\"/blog/lifepo4-technologie/\">LiFePO4-Technologie</a><br>&bull; <a href=\"/heimspeicher-installation/\">Installation</a><br>&bull; <a href=\"/beratung-anfragen/\">Garantie und KfW-Foerderung</a><br>&bull; <a href=\"/solarbatterie-rechner/\">Autarkiegrad und Ersparnis</a><br><br>Was interessiert Sie?" },

        { keys:"danke|vielen dank|danke schoen|super|top", 
          ans:"Gerne! Haben Sie noch weitere Fragen zu Heimspeichern oder Solarbatterien?<br><br>Fuer eine persoenliche Beratung steht Ihnen auch unser <a href=\"/beratung-anfragen/\">Beratungsformular</a> zur Verfuegung.<br><br><a href=\"/shop/\" class=\"wd-ai-link-btn\">&#128722; Zum Shop</a>" },

        { keys:"bye|tschues|auf wiedersehen|ciao", 
          ans:"Auf Wiedersehen! Bei weiteren Fragen stehe ich Ihnen jederzeit zur Verfuegung.<br><br>Besuchen Sie auch unseren <a href=\"/shop/\">Shop</a> oder den <a href=\"/solarbatterie-rechner/\">kostenlosen Rechner</a>." }
    ];

    window.wdAiChatSend = function() {
        var input = document.getElementById('wdAiChatInput');
        var body = document.getElementById('wdAiChatBody');
        if (!input || !body) return;
        var text = input.value.trim();
        if (!text) return;

        var uDiv = document.createElement('div');
        uDiv.className = 'wd-ai-msg user';
        uDiv.textContent = text;
        body.appendChild(uDiv);
        input.value = '';
        body.scrollTop = body.scrollHeight;

        setTimeout(function() {
            var lower = text.toLowerCase();
            var answer = "Ich bin der Werdu.de Berater. Fragen Sie mich zu <a href=\"/shop/\">Produkten</a>, <a href=\"/solarbatterie-rechner/\">Preisen</a>, <a href=\"/blog/lifepo4-technologie/\">Technik</a>, <a href=\"/beratung-anfragen/\">Versand</a>, <a href=\"/beratung-anfragen/\">Garantie</a>, <a href=\"/beratung-anfragen/\">KfW-Foerderung</a>, <a href=\"/solarbatterie-rechner/\">Autarkiegrad</a> oder <a href=\"/heimspeicher-installation/\">Installation</a>.<br><br><a href=\"/beratung-anfragen/\" class=\"wd-ai-link-btn\">&#9993; Persoenliche Beratung</a>";
            var matched = false;
            for (var i = 0; i < WD_KB.length; i++) {
                var keys = WD_KB[i].keys.split('|');
                for (var k = 0; k < keys.length; k++) {
                    if (lower.indexOf(keys[k]) !== -1) {
                        answer = WD_KB[i].ans;
                        matched = true;
                        break;
                    }
                }
                if (matched) break;
            }
            var bDiv = document.createElement('div');
            bDiv.className = 'wd-ai-msg bot';
            bDiv.innerHTML = answer;
            body.appendChild(bDiv);
            body.scrollTop = body.scrollHeight;
        }, 600);
    };
})();
/* ]]> */
</script>
	<?php
}, 100 );