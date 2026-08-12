/**
 * Werdu PV Calculator (legacy #pv-calculator form)
 * Also writes enriched werdu_calc_result + Beratung handoff CTA.
 */
document.addEventListener('DOMContentLoaded', function() {

    var MONTH_FACTORS = [0.03, 0.05, 0.09, 0.13, 0.15, 0.15, 0.15, 0.13, 0.10, 0.07, 0.04, 0.02];
    var MONTH_NAMES = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];

    var form = document.getElementById('pv-calculator');
    var submitBtn = document.getElementById('calc-submit');
    var resultDiv = document.getElementById('calc_result');

    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        var location = document.getElementById('calc_location').value.trim();
        var pvLeistung = parseFloat(document.getElementById('calc_pv_leistung').value);
        var verbrauch = parseFloat(document.getElementById('calc_verbrauch').value);
        var email = document.getElementById('calc_email').value.trim();
        var planSelect = document.getElementById('calc_plan');

        if (!location || !pvLeistung || !verbrauch || !email || !planSelect.value) {
            alert('Bitte füllen Sie alle Pflichtfelder aus.');
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            var errMsg = document.querySelector('.error-message');
            if (errMsg) errMsg.hidden = false;
            return;
        }

        submitBtn.disabled = true;
        var btnText = submitBtn.querySelector('.btn-text');
        var btnLoading = submitBtn.querySelector('.btn-loading');
        if (btnText) btnText.hidden = true;
        if (btnLoading) btnLoading.hidden = false;

        getCoords(location).then(function(coords) {
            if (!coords) throw new Error('Adresse nicht gefunden');

            return fetchPVGIS(
                coords.lat,
                coords.lon,
                pvLeistung,
                document.getElementById('calc_dachneigung').value,
                document.getElementById('calc_ausrichtung').value
            );
        }).then(function(pvData) {
            var jahresErtrag = pvData.outputs.totals.fixed.E_y;
            var strompreis = 0.35;
            var ersparnis = jahresErtrag * strompreis * 0.6;
            var systemPreis = parseFloat(planSelect.value);
            var amortisation = systemPreis / ersparnis;
            var autarkie = Math.min(100, Math.round((jahresErtrag / verbrauch) * 100));
            var selfConsumption = Math.min(95, Math.round((verbrauch / Math.max(jahresErtrag, 1)) * 100 * 1.2));
            if (jahresErtrag < verbrauch) {
                selfConsumption = Math.min(95, Math.round((jahresErtrag / verbrauch) * 100 * 1.2));
            }

            var planLabel = (planSelect.options[planSelect.selectedIndex].text || '') + ' ' + planSelect.value;
            var kwh = 16;
            if (/30|32/.test(planLabel)) kwh = 32;
            else if (/\b15\b/.test(planLabel)) kwh = 15;
            else if (/\b10\b/.test(planLabel)) kwh = 10;

            var plz = String(location).replace(/\D/g, '').substring(0, 5);
            if (plz.length !== 5) plz = String(location).trim().substring(0, 5);

            var monthly = MONTH_FACTORS.map(function(factor, idx) {
                var gen = Math.round(jahresErtrag * factor);
                var cons = Math.round(verbrauch / 12);
                var stored = Math.round(Math.min(cons * (autarkie / 100), gen * 0.55));
                return { month: MONTH_NAMES[idx], generation: gen, consumption: cons, battery: stored };
            });

            var calcResult = {
                kwh: String(kwh),
                peak: String(pvLeistung),
                savings: String(Math.round(ersparnis)),
                plz: plz,
                goal: 'year',
                usage: String(Math.round(verbrauch)),
                autarky: String(autarkie),
                selfConsumption: String(selfConsumption),
                annualYield: String(Math.round(jahresErtrag)),
                monthly: monthly,
                source: 'homepage-calculator-js',
                version: 2
            };

            var beratungUrl = '/beratung-anfragen/?' +
                'kwh=' + encodeURIComponent(calcResult.kwh) +
                '&peak=' + encodeURIComponent(calcResult.peak) +
                '&savings=' + encodeURIComponent(calcResult.savings) +
                '&plz=' + encodeURIComponent(calcResult.plz) +
                '&goal=' + encodeURIComponent(calcResult.goal) +
                '&usage=' + encodeURIComponent(calcResult.usage) +
                '&autarky=' + encodeURIComponent(calcResult.autarky) +
                '&self=' + encodeURIComponent(calcResult.selfConsumption) +
                '&verbrauch=' + encodeURIComponent(calcResult.usage) +
                '&ersparnis=' + encodeURIComponent(calcResult.savings) +
                '&autarkie=' + encodeURIComponent(calcResult.autarky) +
                '&pv=' + encodeURIComponent(calcResult.peak);

            if (window.werduCalcHandoff) {
                beratungUrl = window.werduCalcHandoff.persistAndLink(calcResult).url;
            } else {
                try {
                    sessionStorage.setItem('werdu_calc_result', JSON.stringify(calcResult));
                    localStorage.setItem('werdu_calc_data', JSON.stringify(calcResult));
                } catch (err) {}
            }

            resultDiv.querySelector('.result-content').innerHTML =
                '<h3>Ihre persönliche Ertragsprognose</h3>' +
                '<dl class="result-stats">' +
                    '<dt>Erwarteter PV-Jahresertrag:</dt><dd>' + Math.round(jahresErtrag) + ' kWh</dd>' +
                    '<dt>Empfohlene Speicherkapazität:</dt><dd>' + kwh + ' kWh</dd>' +
                    '<dt>Jährliche Ersparnis:</dt><dd>ca. ' + Math.round(ersparnis).toLocaleString('de-DE') + ' €</dd>' +
                    '<dt>Amortisationszeit:</dt><dd>' + amortisation.toFixed(1) + ' Jahre</dd>' +
                    '<dt>Erreichter Autarkiegrad:</dt><dd>' + autarkie + '%</dd>' +
                '</dl>' +
                '<p class="result-note">💡 Typische Kunden sparen 600 € – 900 € pro Jahr</p>' +
                '<p style="margin-top:16px;"><a class="werdu-calc-cta" href="' + beratungUrl + '" style="display:inline-block;background:#FF6600;color:#fff;font-weight:800;padding:14px 22px;border-radius:12px;text-decoration:none;">Kostenlose Fachanalyse anfordern →</a></p>';

            resultDiv.hidden = false;
            resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        }).catch(function(err) {
            console.error('Calculator error:', err);
            alert('Bei der Berechnung ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.');
        }).finally(function() {
            submitBtn.disabled = false;
            if (btnText) btnText.hidden = false;
            if (btnLoading) btnLoading.hidden = true;
        });
    });

    document.querySelectorAll('.werdu-faq-item').forEach(function(item) {
        item.addEventListener('toggle', function() {
            var icon = this.querySelector('.faq-icon');
            if (icon) icon.style.transform = this.open ? 'rotate(180deg)' : 'rotate(0deg)';
        });
    });

    var newsForm = document.getElementById('newsletter-form');
    if (newsForm) {
        newsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var email = document.getElementById('newsletter-email').value;
            var success = document.getElementById('newsletter-success');
            var error = document.getElementById('newsletter-error');

            if (!email || !email.includes('@')) {
                if (error) error.hidden = false;
                if (success) success.hidden = true;
                return;
            }

            if (error) error.hidden = true;
            if (success) success.hidden = false;
            this.reset();
        });
    }

    var chatBtn = document.getElementById('chat-toggle');
    if (chatBtn) {
        chatBtn.addEventListener('click', function() {
            if (window.tidioChatApi) {
                window.tidioChatApi.open();
            } else {
                alert('Der Live-Chat wird geladen. Bitte haben Sie einen Moment Geduld.');
            }
        });
    }

    function getCoords(location) {
        var url = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(location) + '&countrycodes=de&limit=1';
        return fetch(url, { headers: { 'Accept-Language': 'de' } }).then(function(res) {
            return res.json();
        }).then(function(data) {
            return data.length ? { lat: parseFloat(data[0].lat), lon: parseFloat(data[0].lon) } : null;
        });
    }

    function fetchPVGIS(lat, lon, peakpower, angle, aspect) {
        var url = 'https://re.jrc.ec.europa.eu/api/v5_2/PVcalc?lat=' + lat + '&lon=' + lon + '&peakpower=' + peakpower + '&loss=14&mountingplace=building&angle=' + angle + '&aspect=' + aspect + '&outputformat=json';
        return fetch(url).then(function(res) {
            if (!res.ok) throw new Error('PVGIS API Fehler');
            return res.json();
        });
    }
});
