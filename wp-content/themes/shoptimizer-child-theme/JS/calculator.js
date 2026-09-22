/**
 * Werdu PV Calculator
 */

document.addEventListener('DOMContentLoaded', function() {
    
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
            document.querySelector('.error-message').hidden = false;
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.querySelector('.btn-text').hidden = true;
        submitBtn.querySelector('.btn-loading').hidden = false;
        
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
            
            resultDiv.querySelector('.result-content').innerHTML = 
                '<h3>Ihre persönliche Ertragsprognose</h3>' +
                '<dl class="result-stats">' +
                    '<dt>Erwarteter PV-Jahresertrag:</dt><dd>' + Math.round(jahresErtrag) + ' kWh</dd>' +
                    '<dt>Jährliche Ersparnis:</dt><dd>ca. ' + Math.round(ersparnis).toLocaleString('de-DE') + ' €</dd>' +
                    '<dt>Amortisationszeit:</dt><dd>' + amortisation.toFixed(1) + ' Jahre</dd>' +
                    '<dt>Erreichter Autarkiegrad:</dt><dd>' + autarkie + '%</dd>' +
                '</dl>' +
                '<p class="result-note">💡 Typische Kunden sparen 600 € – 900 € pro Jahr</p>';
            
            resultDiv.hidden = false;
            resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            
        }).catch(function(err) {
            console.error('Calculator error:', err);
            alert('Bei der Berechnung ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.');
        }).finally(function() {
            submitBtn.disabled = false;
            submitBtn.querySelector('.btn-text').hidden = false;
            submitBtn.querySelector('.btn-loading').hidden = true;
        });
    });
    
    // FAQ toggle
    document.querySelectorAll('.werdu-faq-item').forEach(function(item) {
        item.addEventListener('toggle', function() {
            var icon = this.querySelector('.faq-icon');
            if (icon) icon.style.transform = this.open ? 'rotate(180deg)' : 'rotate(0deg)';
        });
    });
    
    // Newsletter form
    var newsForm = document.getElementById('newsletter-form');
    if (newsForm) {
        newsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var email = document.getElementById('newsletter-email').value;
            var success = document.getElementById('newsletter-success');
            var error = document.getElementById('newsletter-error');
            
            if (!email || !email.includes('@')) {
                error.hidden = false;
                success.hidden = true;
                return;
            }
            
            error.hidden = true;
            success.hidden = false;
            this.reset();
        });
    }
    
    // Chat button
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